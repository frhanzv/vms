<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLanesTable extends Migration
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
            'lane' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'location_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'barrier_no' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'weight_id' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'slip_print' => [
                'type' => 'ENUM',
                'constraint' => ['enabled', 'disabled'],
                'default' => 'enabled',
            ],
            'antena_ip' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => true,
            ],
            'kiosk_ip' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => true,
            ],
            'cam_id_1' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'cam_id_2' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'cam_id_3' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'cam_photo_ip_1' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => true,
            ],
            'cam_photo_ip_2' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => true,
            ],
            'in_bound' => [
                'type' => 'ENUM',
                'constraint' => ['yes', 'no'],
                'default' => 'no',
            ],
            'out_bound' => [
                'type' => 'ENUM',
                'constraint' => ['yes', 'no'],
                'default' => 'no',
            ],
            'last_logged_in_by' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'last_logged_in_datetime' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_changed_on_printer_paper' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey('location_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('location_id', 'locations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('lanes');

        // Insert sample data
        $data = [
            [
                'lane' => 'Lane 1',
                'location_id' => 1, // Main Office Reception
                'barrier_no' => 'BR-001',
                'weight_id' => 'WG-101',
                'slip_print' => 'enabled',
                'antena_ip' => '192.168.1.50',
                'kiosk_ip' => '192.168.1.51',
                'cam_id_1' => 'CAM-001',
                'cam_id_2' => 'CAM-002',
                'cam_id_3' => 'CAM-003',
                'cam_photo_ip_1' => '192.168.1.60',
                'cam_photo_ip_2' => '192.168.1.61',
                'in_bound' => 'yes',
                'out_bound' => 'no',
                'last_logged_in_by' => 'admin',
                'last_logged_in_datetime' => '2026-01-15 14:30:00',
                'last_changed_on_printer_paper' => '2026-01-10 09:00:00',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lane' => 'Lane 2',
                'location_id' => 2, // Main Office Security
                'barrier_no' => 'BR-002',
                'weight_id' => 'WG-102',
                'slip_print' => 'enabled',
                'antena_ip' => '192.168.1.52',
                'kiosk_ip' => '192.168.1.53',
                'cam_id_1' => 'CAM-004',
                'cam_id_2' => 'CAM-005',
                'cam_id_3' => 'CAM-006',
                'cam_photo_ip_1' => '192.168.1.62',
                'cam_photo_ip_2' => '192.168.1.63',
                'in_bound' => 'no',
                'out_bound' => 'yes',
                'last_logged_in_by' => 'security1',
                'last_logged_in_datetime' => '2026-01-16 08:15:00',
                'last_changed_on_printer_paper' => '2026-01-12 11:30:00',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lane' => 'Lane 3',
                'location_id' => 3, // North Branch
                'barrier_no' => 'BR-003',
                'weight_id' => 'WG-103',
                'slip_print' => 'disabled',
                'antena_ip' => '192.168.2.50',
                'kiosk_ip' => '192.168.2.51',
                'cam_id_1' => 'CAM-007',
                'cam_id_2' => 'CAM-008',
                'cam_id_3' => null,
                'cam_photo_ip_1' => '192.168.2.60',
                'cam_photo_ip_2' => null,
                'in_bound' => 'yes',
                'out_bound' => 'no',
                'last_logged_in_by' => 'operator1',
                'last_logged_in_datetime' => '2026-01-14 16:45:00',
                'last_changed_on_printer_paper' => '2026-01-08 13:20:00',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lane' => 'Lane 4',
                'location_id' => 4, // South Branch
                'barrier_no' => 'BR-004',
                'weight_id' => 'WG-104',
                'slip_print' => 'enabled',
                'antena_ip' => '192.168.3.50',
                'kiosk_ip' => '192.168.3.51',
                'cam_id_1' => 'CAM-009',
                'cam_id_2' => 'CAM-010',
                'cam_id_3' => 'CAM-011',
                'cam_photo_ip_1' => '192.168.3.60',
                'cam_photo_ip_2' => '192.168.3.61',
                'in_bound' => 'no',
                'out_bound' => 'yes',
                'last_logged_in_by' => 'parking_staff',
                'last_logged_in_datetime' => '2026-01-15 10:20:00',
                'last_changed_on_printer_paper' => '2026-01-11 14:15:00',
                'status' => 'inactive',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('lanes')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('lanes');
    }
}
