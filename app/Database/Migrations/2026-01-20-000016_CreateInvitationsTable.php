<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInvitationsTable extends Migration
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
            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'ic_passport' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'contact' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'company' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'vehicle_registration' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'location' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'invited_by' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'reason' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'other_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'link_expiry' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Pending', 'Approved', 'Rejected'],
                'default' => 'Pending',
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
        $this->forge->addKey(['status', 'created_at']);
        $this->forge->addKey('ic_passport');
        $this->forge->createTable('invitations');
    }

    public function down()
    {
        $this->forge->dropTable('invitations');
    }
}