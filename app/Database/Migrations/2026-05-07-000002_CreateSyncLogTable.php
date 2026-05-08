<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSyncLogTable extends Migration
{
    public function up(): void
    {
        if ($this->db->tableExists('sync_log')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'table_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'direction' => [
                'type'       => 'ENUM',
                'constraint' => ['cloud_to_local', 'local_to_cloud', 'bidirectional'],
            ],
            'records_pulled' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'records_pushed' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'synced_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['table_name', 'synced_at']);
        $this->forge->createTable('sync_log');
    }

    public function down(): void
    {
        $this->forge->dropTable('sync_log', true);
    }
}
