<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

class SyncService
{
    protected BaseConnection $local;
    protected BaseConnection $cloud;
    protected array $messages = [];

    /**
     * Reference/config tables: cloud is authoritative, overwrite local.
     * Ordered so parent tables are synced before child tables.
     */
    protected array $cloudToLocal = [
        ['table' => 'roles',                     'order' => 10],
        ['table' => 'countries',                 'order' => 10],
        ['table' => 'companies',                 'order' => 20],
        ['table' => 'states',                    'order' => 20],
        ['table' => 'sub_companies',             'order' => 30],
        ['table' => 'cities',                    'order' => 30],
        ['table' => 'departments',               'order' => 30],
        ['table' => 'designations',              'order' => 30],
        ['table' => 'visitor_types',             'order' => 30],
        ['table' => 'visit_reasons',             'order' => 30],
        ['table' => 'reject_reasons',            'order' => 30],
        ['table' => 'blacklist_reason',          'order' => 30],
        ['table' => 'security_alert_priorities', 'order' => 30],
        ['table' => 'videos',                    'order' => 30],
        ['table' => 'locations',                 'order' => 40],
        ['table' => 'lanes',                     'order' => 50],
        ['table' => 'pathways',                  'order' => 50],
        ['table' => 'users',                     'order' => 50],
        ['table' => 'visitor_cards',             'order' => 60],
        ['table' => 'settings',                  'order' => 60],
        ['table' => 'device_assignments',        'order' => 60],
        ['table' => 'location_visited',          'order' => 60],
        ['table' => 'workflows',                 'order' => 60],
        ['table' => 'client_features',           'order' => 60],
        ['table' => 'client_form_fields',        'order' => 60],
        ['table' => 'email_templates',           'order' => 60],
        ['table' => 'whatsapp_templates',        'order' => 60],
        ['table' => 'blacklist_closed',          'order' => 70],
        ['table' => 'staff_pass_list',           'order' => 70],
        // invitation_schedules pulled from cloud (admins schedule from cloud)
        // FK invitation_id is remapped via sync_uid
        ['table' => 'invitation_schedules', 'order' => 90,
         'fkMap' => ['invitation_id' => 'invitations']],
    ];

    /**
     * Bidirectional tables: merged via sync_uid, last-write-wins.
     */
    protected array $bidirectional = [
        ['table' => 'invitations', 'order' => 80],
    ];

    /**
     * Jetson-originated events: pushed to cloud only.
     * FK columns are remapped via the referenced table's sync_uid.
     */
    protected array $localToCloud = [
        ['table' => 'invitation_visitors', 'order' => 95,
         'fkMap' => ['invitation_id' => 'invitations']],
        ['table' => 'security_alerts', 'order' => 100,
         'fkMap' => ['invitation_id' => 'invitations']],
    ];

    public function __construct()
    {
        $this->local = db_connect('default');
        $this->cloud = db_connect('cloud');
    }

    public function sync(): array
    {
        $this->messages = [];
        $this->info('=== Sync started at ' . date('Y-m-d H:i:s') . ' ===');

        $this->info('--- Cloud → Jetson ---');
        foreach ($this->cloudToLocal as $cfg) {
            if ($this->local->tableExists($cfg['table']) && $this->cloud->tableExists($cfg['table'])) {
                $this->doCloudToLocal($cfg);
            }
        }

        $this->info('--- Bidirectional ---');
        foreach ($this->bidirectional as $cfg) {
            if ($this->local->tableExists($cfg['table']) && $this->cloud->tableExists($cfg['table'])) {
                $this->doBidirectional($cfg);
            }
        }

        $this->info('--- Jetson → Cloud ---');
        foreach ($this->localToCloud as $cfg) {
            if ($this->local->tableExists($cfg['table']) && $this->cloud->tableExists($cfg['table'])) {
                $this->doLocalToCloud($cfg);
            }
        }

        $this->info('=== Sync finished at ' . date('Y-m-d H:i:s') . ' ===');
        return $this->messages;
    }

    // -------------------------------------------------------------------------
    // Direction handlers
    // -------------------------------------------------------------------------

    protected function doCloudToLocal(array $cfg): void
    {
        $table   = $cfg['table'];
        $lastSync = $this->getLastSync($table, 'cloud_to_local');
        $fkMap   = $cfg['fkMap'] ?? [];

        try {
            $records = $this->fetchChanged($this->cloud, $table, $lastSync);
            $inserted = $updated = $skipped = 0;

            foreach ($records as $rec) {
                if ($fkMap) {
                    $rec = $this->remapFks($rec, $fkMap, $this->cloud, $this->local);
                    if ($rec === null) { $skipped++; continue; }
                }

                $existing = $this->local->table($table)->where('id', $rec['id'])->get()->getRowArray();

                if (!$existing) {
                    $this->local->table($table)->insert($rec);
                    $inserted++;
                } else {
                    // Cloud is authoritative for config tables
                    $this->local->table($table)->where('id', $rec['id'])->update($rec);
                    $updated++;
                }
            }

            $this->writeSyncLog($table, 'cloud_to_local', count($records), 0, 'success');
            $this->info("  [{$table}] ↓ {$inserted} inserted, {$updated} updated, {$skipped} skipped");
        } catch (\Throwable $e) {
            $this->writeSyncLog($table, 'cloud_to_local', 0, 0, 'error: ' . $e->getMessage());
            $this->info("  [{$table}] ERROR: " . $e->getMessage());
            log_message('error', "[Sync] {$table} cloud→local: " . $e->getMessage());
        }
    }

    protected function doBidirectional(array $cfg): void
    {
        $table    = $cfg['table'];
        $lastSync = $this->getLastSync($table, 'bidirectional');

        try {
            $cloudRecs = $this->fetchChanged($this->cloud, $table, $lastSync);
            $localRecs = $this->fetchChanged($this->local, $table, $lastSync);

            $pulledIn = $pushedOut = $conflicts = 0;

            // Cloud → Local pass
            foreach ($cloudRecs as $rec) {
                $syncUid = $rec['sync_uid'] ?? null;
                if (!$syncUid) continue;

                $existing = $this->local->table($table)->where('sync_uid', $syncUid)->get()->getRowArray();

                if (!$existing) {
                    $insert = $rec;
                    unset($insert['id']); // let local auto-assign
                    $this->local->table($table)->insert($insert);
                    $pulledIn++;
                } elseif ($this->isNewer($rec, $existing)) {
                    $update = $rec;
                    unset($update['id']);
                    $this->local->table($table)->where('sync_uid', $syncUid)->update($update);
                    $conflicts++;
                }
            }

            // Local → Cloud pass
            foreach ($localRecs as $rec) {
                $syncUid = $rec['sync_uid'] ?? null;
                if (!$syncUid) continue;

                $existing = $this->cloud->table($table)->where('sync_uid', $syncUid)->get()->getRowArray();

                if (!$existing) {
                    $insert = $rec;
                    unset($insert['id']);
                    $this->cloud->table($table)->insert($insert);
                    $pushedOut++;
                } elseif ($this->isNewer($rec, $existing)) {
                    $update = $rec;
                    unset($update['id']);
                    $this->cloud->table($table)->where('sync_uid', $syncUid)->update($update);
                    $pushedOut++;
                }
            }

            $this->writeSyncLog($table, 'bidirectional', $pulledIn, $pushedOut, 'success');
            $this->info("  [{$table}] ↕ pulled {$pulledIn}, pushed {$pushedOut}, conflicts resolved {$conflicts}");
        } catch (\Throwable $e) {
            $this->writeSyncLog($table, 'bidirectional', 0, 0, 'error: ' . $e->getMessage());
            $this->info("  [{$table}] ERROR: " . $e->getMessage());
            log_message('error', "[Sync] {$table} bidirectional: " . $e->getMessage());
        }
    }

    protected function doLocalToCloud(array $cfg): void
    {
        $table    = $cfg['table'];
        $lastSync = $this->getLastSync($table, 'local_to_cloud');
        $fkMap    = $cfg['fkMap'] ?? [];

        try {
            $records = $this->fetchChanged($this->local, $table, $lastSync);
            $inserted = $updated = $skipped = 0;

            foreach ($records as $rec) {
                if ($fkMap) {
                    $rec = $this->remapFks($rec, $fkMap, $this->local, $this->cloud);
                    if ($rec === null) { $skipped++; continue; }
                }

                $syncUid  = $rec['sync_uid'] ?? null;
                $existing = $syncUid
                    ? $this->cloud->table($table)->where('sync_uid', $syncUid)->get()->getRowArray()
                    : $this->cloud->table($table)->where('id', $rec['id'])->get()->getRowArray();

                if (!$existing) {
                    $insert = $rec;
                    unset($insert['id']);
                    $this->cloud->table($table)->insert($insert);
                    $inserted++;
                } elseif ($this->isNewer($rec, $existing)) {
                    $update = $rec;
                    unset($update['id']);
                    $key = $syncUid ? ['sync_uid' => $syncUid] : ['id' => $existing['id']];
                    $this->cloud->table($table)->where($key)->update($update);
                    $updated++;
                }
            }

            $this->writeSyncLog($table, 'local_to_cloud', 0, count($records), 'success');
            $this->info("  [{$table}] ↑ {$inserted} inserted, {$updated} updated, {$skipped} skipped");
        } catch (\Throwable $e) {
            $this->writeSyncLog($table, 'local_to_cloud', 0, 0, 'error: ' . $e->getMessage());
            $this->info("  [{$table}] ERROR: " . $e->getMessage());
            log_message('error', "[Sync] {$table} local→cloud: " . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** Fetch all records changed since $lastSync (checks both created_at and updated_at). */
    protected function fetchChanged(BaseConnection $db, string $table, string $lastSync): array
    {
        $fields   = $db->getFieldNames($table);
        $hasUpdAt = in_array('updated_at', $fields);

        $builder = $db->table($table);

        if ($hasUpdAt) {
            $builder->groupStart()
                ->where('updated_at >', $lastSync)
                ->orWhere('created_at >', $lastSync)
                ->groupEnd();
        } else {
            $builder->where('created_at >', $lastSync);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Remap FK columns so they point to the correct row in the destination DB.
     * Uses sync_uid as the cross-system join key.
     * Returns null if a required FK cannot be resolved (skip the record).
     */
    protected function remapFks(array $rec, array $fkMap, BaseConnection $srcDb, BaseConnection $dstDb): ?array
    {
        foreach ($fkMap as $fkColumn => $fkTable) {
            $srcId = $rec[$fkColumn] ?? null;
            if ($srcId === null) continue;

            $srcRow = $srcDb->table($fkTable)->select('sync_uid')->where('id', $srcId)->get()->getRowArray();
            if (!$srcRow || empty($srcRow['sync_uid'])) {
                // Parent not synced yet — skip this child record
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

    /** Returns true if $a's timestamp is newer than $b's. */
    protected function isNewer(array $a, array $b): bool
    {
        $tsA = $a['updated_at'] ?? $a['created_at'] ?? '1970-01-01 00:00:00';
        $tsB = $b['updated_at'] ?? $b['created_at'] ?? '1970-01-01 00:00:00';
        return $tsA > $tsB;
    }

    /** Get the timestamp of the last successful sync for a table+direction pair. */
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
