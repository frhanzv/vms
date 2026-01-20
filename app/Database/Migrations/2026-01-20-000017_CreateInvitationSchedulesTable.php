<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInvitationSchedulesTable extends Migration
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
            'date_from' => [
                'type' => 'DATETIME',
            ],
            'date_to' => [
                'type' => 'DATETIME',
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
        $this->forge->addForeignKey('invitation_id', 'invitations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('invitation_schedules');
    }

    public function down()
    {
        $this->forge->dropTable('invitation_schedules');
    }
}