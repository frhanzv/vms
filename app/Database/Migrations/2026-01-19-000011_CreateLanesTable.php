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
    }

    public function down()
    {
        $this->forge->dropTable('lanes');
    }
}
