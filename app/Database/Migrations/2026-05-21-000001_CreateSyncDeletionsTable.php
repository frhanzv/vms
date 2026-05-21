<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSyncDeletionsTable extends Migration
{
    public function up(): void
    {
        if ($this->db->tableExists('sync_deletions')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sync_uid' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'table_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['table_name', 'sync_uid']);
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('sync_deletions');
    }

    public function down(): void
    {
        $this->forge->dropTable('sync_deletions', true);
    }
}
