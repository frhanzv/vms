<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVisitorCardsTable extends Migration
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
            'card_id' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'serial_no' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'in_use', 'lost', 'inactive'],
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
        $this->forge->addKey('card_id');
        $this->forge->addKey('status');
        $this->forge->createTable('visitor_cards');

        // Insert sample data from hardcoded view
        $data = [
            [
                'card_id' => 'VC-001',
                'serial_no' => 'SN-2024-001',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'card_id' => 'VC-002',
                'serial_no' => 'SN-2024-002',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'card_id' => 'VC-003',
                'serial_no' => 'SN-2024-003',
                'status' => 'in_use',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'card_id' => 'VC-004',
                'serial_no' => 'SN-2024-004',
                'status' => 'in_use',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'card_id' => 'VC-005',
                'serial_no' => 'SN-2024-005',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'card_id' => 'VC-006',
                'serial_no' => 'SN-2024-006',
                'status' => 'lost',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'card_id' => 'VC-007',
                'serial_no' => 'SN-2024-007',
                'status' => 'inactive',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'card_id' => 'VC-008',
                'serial_no' => 'SN-2024-008',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('visitor_cards')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('visitor_cards');
    }
}
