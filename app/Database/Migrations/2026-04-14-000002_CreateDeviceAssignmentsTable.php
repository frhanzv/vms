<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDeviceAssignmentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'device_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Online', 'Offline'],
                'default'    => 'Offline',
            ],
            'registration_status' => [
                'type'       => 'ENUM',
                'constraint' => ['Registered', 'Unregistered'],
                'default'    => 'Registered',
            ],
            'location_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['Check-In', 'Check-Out'],
                'default'    => 'Check-In',
            ],
            'last_heartbeat' => [
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
        $this->forge->addForeignKey('location_id', 'locations', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('device_assignments');
    }

    public function down()
    {
        $this->forge->dropTable('device_assignments');
    }
}
