<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSyncUidToAllTables extends Migration
{
    /** Tables that must never receive sync_uid or participate in row sync. */
    protected array $excluded = [
        'migrations',
        'sync_log',
        'sync_deletions',
    ];

    public function up(): void
    {
        foreach ($this->syncableTables() as $table) {
            if (!$this->db->tableExists($table)) {
                continue;
            }

            if ($this->db->fieldExists('sync_uid', $table)) {
                $this->backfillMissingSyncUids($table);
                continue;
            }

            $after = $this->db->fieldExists('id', $table) ? 'id' : null;

            $column = [
                'sync_uid' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 36,
                    'null'       => true,
                ],
            ];

            if ($after !== null) {
                $column['sync_uid']['after'] = $after;
            }

            $this->forge->addColumn($table, $column);
            $this->backfillMissingSyncUids($table);

            $index = 'uq_' . substr($table, 0, 50) . '_sync_uid';
            $this->db->query("ALTER TABLE `{$table}` ADD UNIQUE KEY `{$index}` (`sync_uid`)");
        }
    }

    public function down(): void
    {
        foreach ($this->syncableTables() as $table) {
            if ($this->db->tableExists($table) && $this->db->fieldExists('sync_uid', $table)) {
                $this->forge->dropColumn($table, 'sync_uid');
            }
        }
    }

    protected function syncableTables(): array
    {
        $tables = array_diff($this->db->listTables(), $this->excluded);
        sort($tables);

        return array_values($tables);
    }

    protected function backfillMissingSyncUids(string $table): void
    {
        if (!$this->db->fieldExists('id', $table)) {
            return;
        }

        $rows = $this->db->table($table)
            ->select('id')
            ->groupStart()
                ->where('sync_uid', null)
                ->orWhere('sync_uid', '')
            ->groupEnd()
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $this->db->table($table)
                ->where('id', $row['id'])
                ->update(['sync_uid' => $this->generateUuid()]);
        }
    }

    private function generateUuid(): string
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
