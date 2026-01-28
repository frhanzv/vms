<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVisitorAssetsAndLicenses extends Migration
{
    public function up()
    {
        // Create visitor_licenses table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'invitation_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'license_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'license_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'expiry_date' => [
                'type' => 'DATE',
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
        $this->forge->addForeignKey('invitation_id', 'invitations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('visitor_licenses');

        // Create visitor_equipment table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'invitation_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'equipment_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'brand_model' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'serial_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
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
        $this->forge->addForeignKey('invitation_id', 'invitations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('visitor_equipment');
    }

    public function down()
    {
        $this->forge->dropTable('visitor_licenses');
        $this->forge->dropTable('visitor_equipment');
    }
}
