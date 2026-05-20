<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInboundApiTokensTable extends Migration
{
    public function up()
    {
        // Table may already exist if the dynamic fallback in InboundApi ran first
        if ($this->db->tableExists('inbound_api_tokens')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'client_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
                'null'       => false,
            ],
            'last_used_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('inbound_api_tokens');
    }

    public function down()
    {
        $this->forge->dropTable('inbound_api_tokens', true);
    }
}
