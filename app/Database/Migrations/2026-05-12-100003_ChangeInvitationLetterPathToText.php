<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeInvitationLetterPathToText extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('invitations', [
            'invitation_letter_path' => [
                'name'       => 'invitation_letter_path',
                'type'       => 'TEXT',
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('invitations', [
            'invitation_letter_path' => [
                'name'       => 'invitation_letter_path',
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ]);
    }
}
