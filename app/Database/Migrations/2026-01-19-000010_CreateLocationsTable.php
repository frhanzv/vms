<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLocationsTable extends Migration
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
            'branch' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'location_access' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'adam_ip' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'adam_password' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'mobile_app' => [
                'type' => 'ENUM',
                'constraint' => ['enabled', 'disabled'],
                'default' => 'enabled',
            ],
            'is_hold_area' => [
                'type' => 'ENUM',
                'constraint' => ['yes', 'no'],
                'default' => 'no',
            ],
            'visitor_pass_print' => [
                'type' => 'ENUM',
                'constraint' => ['enabled', 'disabled'],
                'default' => 'enabled',
            ],
            'turnstile' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
            ],
            'in_out_bound' => [
                'type' => 'ENUM',
                'constraint' => ['inbound', 'outbound', 'both'],
                'default' => 'both',
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
        $this->forge->addKey('branch');
        $this->forge->addKey('status');
        $this->forge->createTable('locations');

        // Insert sample locations (from hardcoded data)
        $data = [
            [
                'branch' => 'Main Office',
                'location_access' => 'Reception Area',
                'adam_ip' => '192.168.1.100',
                'adam_password' => password_hash('admin123', PASSWORD_DEFAULT),
                'mobile_app' => 'enabled',
                'is_hold_area' => 'yes',
                'visitor_pass_print' => 'enabled',
                'turnstile' => 'active',
                'in_out_bound' => 'inbound',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'branch' => 'Main Office',
                'location_access' => 'Security Gate',
                'adam_ip' => '192.168.1.101',
                'adam_password' => password_hash('admin123', PASSWORD_DEFAULT),
                'mobile_app' => 'enabled',
                'is_hold_area' => 'no',
                'visitor_pass_print' => 'enabled',
                'turnstile' => 'active',
                'in_out_bound' => 'both',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'branch' => 'North Branch',
                'location_access' => 'Lobby Entrance',
                'adam_ip' => '192.168.2.100',
                'adam_password' => password_hash('admin123', PASSWORD_DEFAULT),
                'mobile_app' => 'enabled',
                'is_hold_area' => 'yes',
                'visitor_pass_print' => 'enabled',
                'turnstile' => 'inactive',
                'in_out_bound' => 'inbound',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'branch' => 'South Branch',
                'location_access' => 'Parking Entry',
                'adam_ip' => '192.168.3.100',
                'adam_password' => password_hash('admin123', PASSWORD_DEFAULT),
                'mobile_app' => 'disabled',
                'is_hold_area' => 'no',
                'visitor_pass_print' => 'disabled',
                'turnstile' => 'active',
                'in_out_bound' => 'outbound',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'branch' => 'East Branch',
                'location_access' => 'Main Gate',
                'adam_ip' => '192.168.4.100',
                'adam_password' => password_hash('admin123', PASSWORD_DEFAULT),
                'mobile_app' => 'enabled',
                'is_hold_area' => 'no',
                'visitor_pass_print' => 'enabled',
                'turnstile' => 'active',
                'in_out_bound' => 'both',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'branch' => 'West Branch',
                'location_access' => 'Service Entrance',
                'adam_ip' => '192.168.5.100',
                'adam_password' => password_hash('admin123', PASSWORD_DEFAULT),
                'mobile_app' => 'disabled',
                'is_hold_area' => 'yes',
                'visitor_pass_print' => 'enabled',
                'turnstile' => 'inactive',
                'in_out_bound' => 'inbound',
                'status' => 'inactive',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('locations')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('locations');
    }
}
