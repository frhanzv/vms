<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInvitationVisitorsTable extends Migration
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
            'invitation_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addKey('invitation_id');
        $this->forge->addKey('ic_passport');
        $this->forge->addForeignKey('invitation_id', 'invitations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('invitation_visitors');
    }

    public function down()
    {
        $this->forge->dropTable('invitation_visitors');
    }
}