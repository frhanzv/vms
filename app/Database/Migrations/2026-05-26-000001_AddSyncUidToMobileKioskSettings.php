<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSyncUidToMobileKioskSettings extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('mobile_kiosk_settings')) {
            return;
        }

        if ($this->db->fieldExists('sync_uid', 'mobile_kiosk_settings')) {
            $this->backfillMissingSyncUids('mobile_kiosk_settings');
            return;
        }

        $column = [
            'sync_uid' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'after'      => 'id',
            ],
        ];

        $this->forge->addColumn('mobile_kiosk_settings', $column);
        $this->backfillMissingSyncUids('mobile_kiosk_settings');

        $this->db->query(
            'ALTER TABLE `mobile_kiosk_settings` ADD UNIQUE KEY `uq_mobile_kiosk_settings_sync_uid` (`sync_uid`)'
        );
    }

    public function down(): void
    {
        if ($this->db->tableExists('mobile_kiosk_settings')
            && $this->db->fieldExists('sync_uid', 'mobile_kiosk_settings')) {
            $this->forge->dropColumn('mobile_kiosk_settings', 'sync_uid');
        }
    }

    protected function backfillMissingSyncUids(string $table): void
    {
        if (! $this->db->fieldExists('id', $table)) {
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
