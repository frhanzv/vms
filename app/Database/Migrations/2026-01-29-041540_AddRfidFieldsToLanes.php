<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRfidFieldsToLanes extends Migration
{
    public function up()
    {
        // Add RFID reader fields to lanes table
        $fields = [
            'rfid_reader_ip' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'status'
            ],
            'rfid_reader_port' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 49152,
                'null' => true,
                'after' => 'rfid_reader_ip'
            ],
            'rfid_enabled' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'null' => false,
                'after' => 'rfid_reader_port'
            ]
        ];

        $this->forge->addColumn('lanes', $fields);

        // Create visitor_card_logs table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'visitor_card_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'invitation_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'action' => [
                'type' => 'ENUM',
                'constraint' => ['checkin', 'checkout'],
                'null' => false,
            ],
            'lane_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'scanned_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('visitor_card_id');
        $this->forge->addKey('invitation_id');
        $this->forge->addKey('scanned_at');
        
        $this->forge->addForeignKey('visitor_card_id', 'visitor_cards', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('invitation_id', 'invitations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('lane_id', 'lanes', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('visitor_card_logs');
    }

    public function down()
    {
        // Drop visitor_card_logs table
        $this->forge->dropTable('visitor_card_logs', true);

        // Remove RFID fields from lanes table
        $this->forge->dropColumn('lanes', ['rfid_reader_ip', 'rfid_reader_port', 'rfid_enabled']);
    }
}
