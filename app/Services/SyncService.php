<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

class SyncService
{
    protected BaseConnection $local;
    protected BaseConnection $cloud;
    protected array $messages = [];
    protected bool $fullSync = false;

    /** Tables excluded from row sync (schema/audit only). */
    protected array $excludedTables = [
        'migrations',
        'sync_log',
        'sync_deletions',
    ];

    /** Match peer rows by stable business keys when sync_uid differs (seeded defaults). */
    protected array $naturalKeys = [
        'countries'                   => ['code'],
        'companies'                   => ['name'],
        'roles'                       => ['name'],
        'settings'                    => ['setting_key'],
        'users'                       => ['username'],
        'visitor_types'               => ['name'],
        'email_templates'             => ['code'],
        'security_alert_priorities'   => ['alert_name'],
        'email_template_form_fields'  => ['field_key'],
        'visit_reasons'               => ['reason'],
        'blacklistreason'             => ['reason'],
        'businesstype'                => ['name'],
        'registration_types'          => ['name'],
        'reject_reasons'              => ['reason'],
    ];

    /** FK columns not declared in MySQL but required for correct row sync. */
    protected array $logicalForeignKeys = [
        'invitations'         => ['company_id' => 'companies', 'parent_invitation_id' => 'invitations'],
        'users'               => ['company_id' => 'companies'],
        'staff'               => ['company_id' => 'companies'],
        'staff_pass'          => ['company_id' => 'companies'],
        'staff_pass_requests' => ['company_id' => 'companies'],
        'security_alerts'     => ['company_id' => 'companies', 'invitation_id' => 'invitations'],
        'client_features'     => ['company_id' => 'companies'],
        'client_form_fields'  => ['company_id' => 'companies'],
        'client_notification_settings' => ['company_id' => 'companies'],
        'client_messaging_credentials' => ['company_id' => 'companies'],
        'mobile_kiosk_settings' => ['client_id' => 'companies'],
    ];

    protected ?array $syncableTables = null;
    protected ?array $insertOrder     = null;
    protected ?array $deleteOrder    = null;
    protected ?array $fkMaps         = null;
    protected ?array $targetFieldCache = null;

    public function __construct(bool $fullSync = false)
    {
        $this->local    = db_connect('default');
        $this->cloud    = db_connect('cloud');
        $this->fullSync = $fullSync;
    }

    public function sync(): array
    {
        $this->messages = [];
        $mode = $this->fullSync ? 'FULL' : 'incremental';
        $this->info('=== Sync started at ' . date('Y-m-d H:i:s') . " ({$mode}) ===");

        if (! self::isPeerConfigured()) {
            $this->info('ERROR: database.cloud.* (peer) is not configured — set peer DB host on this server.');
            $this->info('  Laragon/dev PCs: leave unset; do not run sync:run.');
            return $this->messages;
        }

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

    /** True when database.cloud.* points at the other server (Jetson↔cloud). */
    public static function isPeerConfigured(): bool
    {
        $cfg = config('Database')->cloud;

        return ! empty($cfg['hostname']) && ! empty($cfg['database']);
    }

    /** @deprecated Use isPeerConfigured() */
    public static function isCloudConfigured(): bool
    {
        return self::isPeerConfigured();
    }

    // -------------------------------------------------------------------------
    // Deletion propagation
    // -------------------------------------------------------------------------

    protected function syncDeletions(): void
    {
        $lastSync = $this->fullSync ? '1970-01-01 00:00:00' : $this->getLastSync('_deletions', 'bidirectional');
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

        if (!$this->local->fieldExists('sync_uid', $table) || !$this->cloud->fieldExists('sync_uid', $table)) {
            $this->info("  [{$table}] skipped — no sync_uid column on both sides");
            return;
        }

        $lastSync = $this->fullSync ? '1970-01-01 00:00:00' : $this->getLastSync($table, 'bidirectional');
        $fkMap    = $this->getForeignKeys($table);

        $pulledIn = $pushedOut = $skipped = $errors = 0;

        try {
            $cloudRecs = $this->fetchRecordsForSync($this->cloud, $this->local, $table, $lastSync);
            $localRecs = $this->fetchRecordsForSync($this->local, $this->cloud, $table, $lastSync);

            foreach ($cloudRecs as $rec) {
                try {
                    $result = $this->upsertRecord($this->local, $this->cloud, $table, $rec, $fkMap);
                    if ($result === 'inserted' || $result === 'updated') {
                        $pulledIn++;
                    } elseif ($result === 'skipped') {
                        $skipped++;
                    }
                } catch (\Throwable $e) {
                    $errors++;
                    $uid = $rec['sync_uid'] ?? '?';
                    $this->info("  [{$table}] pull {$uid} ERROR: " . $e->getMessage());
                    log_message('error', "[Sync] {$table} pull {$uid}: " . $e->getMessage());
                }
            }

            foreach ($localRecs as $rec) {
                try {
                    $result = $this->upsertRecord($this->cloud, $this->local, $table, $rec, $fkMap);
                    if ($result === 'inserted' || $result === 'updated') {
                        $pushedOut++;
                    } elseif ($result === 'skipped') {
                        $skipped++;
                    }
                } catch (\Throwable $e) {
                    $errors++;
                    $uid = $rec['sync_uid'] ?? '?';
                    $this->info("  [{$table}] push {$uid} ERROR: " . $e->getMessage());
                    log_message('error', "[Sync] {$table} push {$uid}: " . $e->getMessage());
                }
            }

            $status = $errors > 0 ? "partial ({$errors} row errors)" : 'success';
            $this->writeSyncLog($table, 'bidirectional', $pulledIn, $pushedOut, $status);
            $suffix = $errors > 0 ? ", {$errors} errors" : '';
            $this->info("  [{$table}] ↕ pulled {$pulledIn}, pushed {$pushedOut}, skipped {$skipped}{$suffix}");
        } catch (\Throwable $e) {
            $this->writeSyncLog($table, 'bidirectional', 0, 0, 'error: ' . $e->getMessage());
            $this->info("  [{$table}] ERROR: " . $e->getMessage());
            log_message('error', "[Sync] {$table}: " . $e->getMessage());
        }
    }

    /**
     * Changed rows plus any source rows whose sync_uid is missing on the peer.
     *
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

        $rec = $this->filterRecordColumns($targetDb, $table, $rec);

        $existing = $targetDb->table($table)->where('sync_uid', $syncUid)->get()->getRowArray();

        if (!$existing) {
            $existing = $this->findExistingByNaturalKey($targetDb, $table, $rec);
        }

        if (!$existing) {
            $insert = $rec;
            unset($insert['id']);

            try {
                $targetDb->table($table)->insert($insert);
            } catch (\Throwable $e) {
                if (! $this->isDuplicateKeyError($e)) {
                    throw $e;
                }

                $existing = $this->findExistingByNaturalKey($targetDb, $table, $rec);
                if (! $existing) {
                    throw $e;
                }

                return $this->updateExistingRow($targetDb, $table, $syncUid, $rec, $existing);
            }

            $this->clearTombstone($targetDb, $table, $syncUid);

            return 'inserted';
        }

        return $this->updateExistingRow($targetDb, $table, $syncUid, $rec, $existing);
    }

    protected function updateExistingRow(
        BaseConnection $targetDb,
        string $table,
        string $syncUid,
        array $rec,
        array $existing
    ): string {
        if ($this->isNewer($rec, $existing)) {
            $update = $rec;
            unset($update['id']);
            $targetDb->table($table)->where('id', $existing['id'])->update($update);
            $this->clearTombstone($targetDb, $table, $syncUid);

            return 'updated';
        }

        if (($existing['sync_uid'] ?? '') !== $syncUid) {
            $targetDb->table($table)->where('id', $existing['id'])->update(['sync_uid' => $syncUid]);
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

        $dbName      = $this->local->getDatabase();
        $syncable    = array_flip($this->getSyncableTables());
        $foreignKeys = $this->logicalForeignKeys[$table] ?? [];

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

    /**
     * Union of changed rows and rows missing on the peer (by sync_uid).
     */
    protected function fetchRecordsForSync(
        BaseConnection $sourceDb,
        BaseConnection $targetDb,
        string $table,
        string $lastSync
    ): array {
        $records = $this->fetchChanged($sourceDb, $table, $lastSync);

        if ($this->fullSync) {
            $records = $sourceDb->table($table)->get()->getResultArray();
        } else {
            $targetUids = [];
            foreach ($targetDb->table($table)->select('sync_uid')->get()->getResultArray() as $row) {
                if (! empty($row['sync_uid'])) {
                    $targetUids[$row['sync_uid']] = true;
                }
            }

            foreach ($sourceDb->table($table)->select('sync_uid')->get()->getResultArray() as $row) {
                $uid = $row['sync_uid'] ?? null;
                if ($uid && ! isset($targetUids[$uid])) {
                    $full = $sourceDb->table($table)->where('sync_uid', $uid)->get()->getRowArray();
                    if ($full) {
                        $records[] = $full;
                    }
                }
            }
        }

        $byUid = [];
        foreach ($records as $rec) {
            if (! empty($rec['sync_uid'])) {
                $byUid[$rec['sync_uid']] = $rec;
            }
        }

        return array_values($byUid);
    }

    protected function filterRecordColumns(BaseConnection $targetDb, string $table, array $rec): array
    {
        $cacheKey = spl_object_hash($targetDb) . ':' . $table;
        if (! isset($this->targetFieldCache[$cacheKey])) {
            $this->targetFieldCache[$cacheKey] = array_flip(
                array_map('strtolower', $targetDb->getFieldNames($table))
            );
        }

        $allowed = $this->targetFieldCache[$cacheKey];
        $filtered = [];
        foreach ($rec as $key => $value) {
            if (isset($allowed[strtolower((string) $key)])) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    protected function remapFks(array $rec, array $fkMap, BaseConnection $srcDb, BaseConnection $dstDb): ?array
    {
        foreach ($fkMap as $fkColumn => $fkTable) {
            $srcId = $rec[$fkColumn] ?? null;
            if ($srcId === null || $srcId === '' || (int) $srcId === 0) {
                continue;
            }

            $srcRow = $srcDb->table($fkTable)->where('id', (int) $srcId)->get()->getRowArray();
            if (! $srcRow) {
                return null;
            }

            $dstId = $this->resolvePeerRowId($srcDb, $dstDb, $fkTable, $srcRow);
            if ($dstId === null) {
                return null;
            }

            $rec[$fkColumn] = $dstId;
        }

        return $rec;
    }

    protected function resolvePeerRowId(
        BaseConnection $srcDb,
        BaseConnection $dstDb,
        string $table,
        array $srcRow
    ): ?int {
        $syncUid = $srcRow['sync_uid'] ?? null;
        if ($syncUid) {
            $dstRow = $dstDb->table($table)->select('id')->where('sync_uid', $syncUid)->get()->getRowArray();
            if ($dstRow) {
                return (int) $dstRow['id'];
            }
        }

        $match = $this->findExistingByNaturalKey($dstDb, $table, $srcRow);
        if ($match) {
            return (int) $match['id'];
        }

        return null;
    }

    protected function findExistingByNaturalKey(BaseConnection $db, string $table, array $rec): ?array
    {
        $keys = $this->naturalKeys[$table] ?? null;
        if (! $keys) {
            return null;
        }

        $builder = $db->table($table);
        $hasMatch = false;
        foreach ($keys as $column) {
            if (! array_key_exists($column, $rec) || $rec[$column] === null || $rec[$column] === '') {
                return null;
            }
            $builder->where($column, $rec[$column]);
            $hasMatch = true;
        }

        if (! $hasMatch) {
            return null;
        }

        $row = $builder->get()->getRowArray();

        return $row ?: null;
    }

    protected function isDuplicateKeyError(\Throwable $e): bool
    {
        $msg = $e->getMessage();

        return str_contains($msg, 'Duplicate entry') || str_contains($msg, '1062');
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
