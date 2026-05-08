<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSyncUidToTables extends Migration
{
    protected array $tables = [
        'invitations',
        'invitation_visitors',
        'invitation_schedules',
        'security_alerts',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (!$this->db->tableExists($table)) {
                continue;
            }

            if (!$this->db->fieldExists('sync_uid', $table)) {
                $this->forge->addColumn($table, [
                    'sync_uid' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 36,
                        'null'       => true,
                        'after'      => 'id',
                    ],
                ]);

                // Backfill existing rows with a UUID
                $rows = $this->db->table($table)->select('id')->get()->getResultArray();
                foreach ($rows as $row) {
                    $this->db->table($table)
                        ->where('id', $row['id'])
                        ->update(['sync_uid' => $this->generateUuid()]);
                }

                // Add unique index via raw SQL (forge cannot add unique after-the-fact cleanly)
                $this->db->query("ALTER TABLE `{$table}` ADD UNIQUE KEY `uq_{$table}_sync_uid` (`sync_uid`)");
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if ($this->db->tableExists($table) && $this->db->fieldExists('sync_uid', $table)) {
                $this->forge->dropColumn($table, 'sync_uid');
            }
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
