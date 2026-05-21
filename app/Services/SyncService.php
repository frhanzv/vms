<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

class SyncService
{
    protected BaseConnection $local;
    protected BaseConnection $cloud;
    protected array $messages = [];

    /** Tables excluded from row sync (schema/audit only). */
    protected array $excludedTables = [
        'migrations',
        'sync_log',
        'sync_deletions',
    ];

    protected ?array $syncableTables = null;
    protected ?array $insertOrder     = null;
    protected ?array $deleteOrder    = null;
    protected ?array $fkMaps         = null;

    public function __construct()
    {
        $this->local = db_connect('default');
        $this->cloud = db_connect('cloud');
    }

    public function sync(): array
    {
        $this->messages = [];
        $this->info('=== Sync started at ' . date('Y-m-d H:i:s') . ' ===');

        $localHasDeletions = $this->local->tableExists('sync_deletions');
        $cloudHasDeletions = $this->cloud->tableExists('sync_deletions');

        if (!$localHasDeletions || !$cloudHasDeletions) {
            if (!$localHasDeletions) {
                $this->info('ERROR: sync_deletions missing on LOCAL (vms) — run: php spark migrate');
            }
            if (!$cloudHasDeletions) {
                $this->info('ERROR: sync_deletions missing on CLOUD (vms_cloud) — run php spark migrate on the cloud server too.');
            }
            if ($this->local->getDatabase() && !$cloudHasDeletions) {
                $cloudDb = $this->cloud->getDatabase();
                $cloudHost = config('Database')->cloud['hostname'] ?? '?';
                $this->info("  Cloud connection: {$cloudHost} / {$cloudDb} — verify database.cloud.* in .env");
            }
            return $this->messages;
        }

        $tables = $this->getSyncableTables();
        $this->info('Tables in sync: ' . count($tables));

        $this->info('--- Phase 1: Deletions (both directions) ---');
        $this->syncDeletions();

        $this->info('--- Phase 2: Upserts (bidirectional, all tables) ---');
        foreach ($this->getInsertOrder() as $table) {
            $this->syncTableBidirectional($table);
        }

        $this->info('=== Sync finished at ' . date('Y-m-d H:i:s') . ' ===');

        return $this->messages;
    }

    // -------------------------------------------------------------------------
    // Deletion propagation
    // -------------------------------------------------------------------------

    protected function syncDeletions(): void
    {
        $lastSync = $this->getLastSync('_deletions', 'bidirectional');
        $applied  = 0;

        try {
            $cloudTombstones = $this->fetchTombstones($this->cloud, $lastSync);
            $localTombstones = $this->fetchTombstones($this->local, $lastSync);

            foreach ($this->getDeleteOrder() as $table) {
                foreach ($cloudTombstones as $tombstone) {
                    if ($tombstone['table_name'] !== $table) {
                        continue;
                    }
                    if ($this->applyRemoteDeletion($this->local, $tombstone)) {
                        $applied++;
                    }
                }
            }

            foreach ($this->getDeleteOrder() as $table) {
                foreach ($localTombstones as $tombstone) {
                    if ($tombstone['table_name'] !== $table) {
                        continue;
                    }
                    if ($this->applyRemoteDeletion($this->cloud, $tombstone)) {
                        $applied++;
                    }
                }
            }

            $this->writeSyncLog('_deletions', 'bidirectional', $applied, $applied, 'success');
            $this->info("  [deletions] ↕ {$applied} rows deleted across sides");
        } catch (\Throwable $e) {
            $this->writeSyncLog('_deletions', 'bidirectional', 0, 0, 'error: ' . $e->getMessage());
            $this->info('  [deletions] ERROR: ' . $e->getMessage());
            log_message('error', '[Sync] deletions: ' . $e->getMessage());
        }
    }

    protected function fetchTombstones(BaseConnection $db, string $lastSync): array
    {
        return $db->table('sync_deletions')
            ->where('deleted_at >', $lastSync)
            ->orderBy('deleted_at', 'ASC')
            ->get()
            ->getResultArray();
    }

    protected function applyRemoteDeletion(BaseConnection $targetDb, array $tombstone): bool
    {
        $table   = $tombstone['table_name'];
        $syncUid = $tombstone['sync_uid'];

        if (!in_array($table, $this->getSyncableTables(), true) || !$targetDb->tableExists($table)) {
            return false;
        }

        if (!$targetDb->fieldExists('sync_uid', $table)) {
            return false;
        }

        $existing = $targetDb->table($table)->where('sync_uid', $syncUid)->get()->getRowArray();

        if ($existing) {
            $rowTs = $existing['updated_at'] ?? $existing['created_at'] ?? '1970-01-01 00:00:00';
            if ($rowTs > $tombstone['deleted_at']) {
                return false;
            }

            $this->deleteWithoutLog($targetDb, $table, $syncUid);
        }

        $this->ensureTombstone($targetDb, $tombstone);

        return (bool) $existing;
    }

    protected function deleteWithoutLog(BaseConnection $db, string $table, string $syncUid): void
    {
        $db->query('SET @vms_sync_skip_delete_log = 1');
        $db->table($table)->where('sync_uid', $syncUid)->delete();
        $db->query('SET @vms_sync_skip_delete_log = 0');
    }

    protected function ensureTombstone(BaseConnection $db, array $tombstone): void
    {
        $exists = $db->table('sync_deletions')
            ->where('table_name', $tombstone['table_name'])
            ->where('sync_uid', $tombstone['sync_uid'])
            ->countAllResults();

        if ($exists) {
            return;
        }

        $db->table('sync_deletions')->insert([
            'sync_uid'   => $tombstone['sync_uid'],
            'table_name' => $tombstone['table_name'],
            'deleted_at' => $tombstone['deleted_at'],
        ]);
    }

    protected function clearTombstone(BaseConnection $db, string $table, string $syncUid): void
    {
        $db->query('SET @vms_sync_skip_delete_log = 1');
        $db->table('sync_deletions')
            ->where('table_name', $table)
            ->where('sync_uid', $syncUid)
            ->delete();
        $db->query('SET @vms_sync_skip_delete_log = 0');
    }

    // -------------------------------------------------------------------------
    // Bidirectional upserts
    // -------------------------------------------------------------------------

    protected function syncTableBidirectional(string $table): void
    {
        if (!$this->local->tableExists($table) || !$this->cloud->tableExists($table)) {
            return;
        }

        if (!$this->local->fieldExists('sync_uid', $table)) {
            $this->info("  [{$table}] skipped — no sync_uid column");
            return;
        }

        $lastSync = $this->getLastSync($table, 'bidirectional');
        $fkMap    = $this->getForeignKeys($table);

        try {
            $cloudRecs = $this->fetchChanged($this->cloud, $table, $lastSync);
            $localRecs = $this->fetchChanged($this->local, $table, $lastSync);

            $pulledIn = $pushedOut = $skipped = 0;

            foreach ($cloudRecs as $rec) {
                $result = $this->upsertRecord($this->local, $this->cloud, $table, $rec, $fkMap);
                if ($result === 'inserted' || $result === 'updated') {
                    $pulledIn++;
                } elseif ($result === 'skipped') {
                    $skipped++;
                }
            }

            foreach ($localRecs as $rec) {
                $result = $this->upsertRecord($this->cloud, $this->local, $table, $rec, $fkMap);
                if ($result === 'inserted' || $result === 'updated') {
                    $pushedOut++;
                } elseif ($result === 'skipped') {
                    $skipped++;
                }
            }

            $this->writeSyncLog($table, 'bidirectional', $pulledIn, $pushedOut, 'success');
            $this->info("  [{$table}] ↕ pulled {$pulledIn}, pushed {$pushedOut}, skipped {$skipped}");
        } catch (\Throwable $e) {
            $this->writeSyncLog($table, 'bidirectional', 0, 0, 'error: ' . $e->getMessage());
            $this->info("  [{$table}] ERROR: " . $e->getMessage());
            log_message('error', "[Sync] {$table}: " . $e->getMessage());
        }
    }

    /**
     * @return 'inserted'|'updated'|'skipped'|'unchanged'
     */
    protected function upsertRecord(
        BaseConnection $targetDb,
        BaseConnection $sourceDb,
        string $table,
        array $rec,
        array $fkMap
    ): string {
        $syncUid = $rec['sync_uid'] ?? null;
        if (!$syncUid) {
            return 'skipped';
        }

        if ($fkMap) {
            $rec = $this->remapFks($rec, $fkMap, $sourceDb, $targetDb);
            if ($rec === null) {
                return 'skipped';
            }
        }

        $existing = $targetDb->table($table)->where('sync_uid', $syncUid)->get()->getRowArray();

        if (!$existing) {
            $insert = $rec;
            unset($insert['id']);
            $targetDb->table($table)->insert($insert);
            $this->clearTombstone($targetDb, $table, $syncUid);

            return 'inserted';
        }

        if ($this->isNewer($rec, $existing)) {
            $update = $rec;
            unset($update['id']);
            $targetDb->table($table)->where('sync_uid', $syncUid)->update($update);
            $this->clearTombstone($targetDb, $table, $syncUid);

            return 'updated';
        }

        return 'unchanged';
    }

    // -------------------------------------------------------------------------
    // Schema helpers
    // -------------------------------------------------------------------------

    protected function getSyncableTables(): array
    {
        if ($this->syncableTables !== null) {
            return $this->syncableTables;
        }

        $local  = $this->local->listTables();
        $cloud  = $this->cloud->listTables();
        $common = array_intersect($local, $cloud);
        $tables = array_values(array_diff($common, $this->excludedTables));
        sort($tables);

        $this->syncableTables = $tables;

        return $tables;
    }

    protected function getInsertOrder(): array
    {
        if ($this->insertOrder !== null) {
            return $this->insertOrder;
        }

        $this->insertOrder = $this->topologicalSort($this->getSyncableTables());
        $this->deleteOrder = array_reverse($this->insertOrder);

        return $this->insertOrder;
    }

    protected function getDeleteOrder(): array
    {
        if ($this->deleteOrder === null) {
            $this->getInsertOrder();
        }

        return $this->deleteOrder;
    }

    protected function topologicalSort(array $tables): array
    {
        $tableSet = array_flip($tables);
        $deps     = [];
        $inDegree = array_fill_keys($tables, 0);

        foreach ($tables as $table) {
            $deps[$table] = [];
            foreach ($this->getForeignKeys($table) as $column => $refTable) {
                if (!isset($tableSet[$refTable]) || $refTable === $table) {
                    continue;
                }
                $deps[$table][] = $refTable;
                $inDegree[$table]++;
            }
        }

        $queue = [];
        foreach ($tables as $table) {
            if ($inDegree[$table] === 0) {
                $queue[] = $table;
            }
        }

        $sorted = [];
        while ($queue) {
            $current = array_shift($queue);
            $sorted[] = $current;

            foreach ($tables as $child) {
                if (in_array($current, $deps[$child], true)) {
                    $inDegree[$child]--;
                    if ($inDegree[$child] === 0) {
                        $queue[] = $child;
                    }
                }
            }
        }

        // Append any cyclic / unresolved tables at the end.
        foreach ($tables as $table) {
            if (!in_array($table, $sorted, true)) {
                $sorted[] = $table;
            }
        }

        return $sorted;
    }

    protected function getForeignKeys(string $table): array
    {
        if ($this->fkMaps === null) {
            $this->fkMaps = [];
        }

        if (isset($this->fkMaps[$table])) {
            return $this->fkMaps[$table];
        }

        $dbName     = $this->local->getDatabase();
        $syncable   = array_flip($this->getSyncableTables());
        $foreignKeys = [];

        $rows = $this->local->query(
            'SELECT COLUMN_NAME, REFERENCED_TABLE_NAME
             FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$dbName, $table]
        )->getResultArray();

        foreach ($rows as $row) {
            $ref = $row['REFERENCED_TABLE_NAME'];
            if (isset($syncable[$ref])) {
                $foreignKeys[$row['COLUMN_NAME']] = $ref;
            }
        }

        $this->fkMaps[$table] = $foreignKeys;

        return $foreignKeys;
    }

    // -------------------------------------------------------------------------
    // Shared helpers
    // -------------------------------------------------------------------------

    protected function fetchChanged(BaseConnection $db, string $table, string $lastSync): array
    {
        $fields     = $db->getFieldNames($table);
        $hasCreated = in_array('created_at', $fields, true);
        $hasUpdated = in_array('updated_at', $fields, true);

        $builder = $db->table($table);

        if ($hasUpdated || $hasCreated) {
            $builder->groupStart();
            if ($hasUpdated) {
                $builder->where('updated_at >', $lastSync);
            }
            if ($hasCreated) {
                $hasUpdated ? $builder->orWhere('created_at >', $lastSync) : $builder->where('created_at >', $lastSync);
            }
            $builder->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    protected function remapFks(array $rec, array $fkMap, BaseConnection $srcDb, BaseConnection $dstDb): ?array
    {
        foreach ($fkMap as $fkColumn => $fkTable) {
            $srcId = $rec[$fkColumn] ?? null;
            if ($srcId === null) {
                continue;
            }

            $srcRow = $srcDb->table($fkTable)->select('sync_uid')->where('id', $srcId)->get()->getRowArray();
            if (!$srcRow || empty($srcRow['sync_uid'])) {
                return null;
            }

            $dstRow = $dstDb->table($fkTable)->select('id')->where('sync_uid', $srcRow['sync_uid'])->get()->getRowArray();
            if (!$dstRow) {
                return null;
            }

            $rec[$fkColumn] = $dstRow['id'];
        }

        return $rec;
    }

    protected function isNewer(array $a, array $b): bool
    {
        $tsA = $a['updated_at'] ?? $a['created_at'] ?? '1970-01-01 00:00:00';
        $tsB = $b['updated_at'] ?? $b['created_at'] ?? '1970-01-01 00:00:00';

        return $tsA > $tsB;
    }

    protected function getLastSync(string $table, string $direction): string
    {
        if (!$this->local->tableExists('sync_log')) {
            return '1970-01-01 00:00:00';
        }

        $row = $this->local->table('sync_log')
            ->where('table_name', $table)
            ->where('direction', $direction)
            ->where('status', 'success')
            ->orderBy('synced_at', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        return $row ? $row['synced_at'] : '1970-01-01 00:00:00';
    }

    protected function writeSyncLog(string $table, string $direction, int $pulled, int $pushed, string $status): void
    {
        if (!$this->local->tableExists('sync_log')) {
            return;
        }

        $this->local->table('sync_log')->insert([
            'table_name'     => $table,
            'direction'      => $direction,
            'records_pulled' => $pulled,
            'records_pushed' => $pushed,
            'status'         => $status,
            'synced_at'      => date('Y-m-d H:i:s'),
        ]);
    }

    protected function info(string $msg): void
    {
        $this->messages[] = $msg;
        log_message('info', '[Sync] ' . $msg);
    }

    public static function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
