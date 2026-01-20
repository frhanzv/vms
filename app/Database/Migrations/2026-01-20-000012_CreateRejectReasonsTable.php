<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRejectReasonsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'reason' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'mobile_app' => [
                'type' => 'ENUM',
                'constraint' => ['enabled', 'disabled'],
                'default' => 'enabled',
            ],
            'commercial' => [
                'type' => 'ENUM',
                'constraint' => ['yes', 'no'],
                'default' => 'no',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
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
        $this->forge->addKey('status');
        $this->forge->createTable('reject_reasons');

        // Insert sample data from hardcoded view
        $data = [
            [
                'reason' => 'Incomplete Documentation',
                'mobile_app' => 'enabled',
                'commercial' => 'yes',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'reason' => 'Invalid Information',
                'mobile_app' => 'enabled',
                'commercial' => 'yes',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'reason' => 'Security Concerns',
                'mobile_app' => 'enabled',
                'commercial' => 'no',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'reason' => 'Unauthorized Access',
                'mobile_app' => 'disabled',
                'commercial' => 'no',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'reason' => 'Blacklisted Visitor',
                'mobile_app' => 'enabled',
                'commercial' => 'yes',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'reason' => 'Host Not Available',
                'mobile_app' => 'enabled',
                'commercial' => 'no',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'reason' => 'No Show',
                'mobile_app' => 'enabled',
                'commercial' => 'yes',
                'status' => 'inactive',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('reject_reasons')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('reject_reasons');
    }
}
