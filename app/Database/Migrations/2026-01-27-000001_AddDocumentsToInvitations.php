<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDocumentsToInvitations extends Migration
{
    public function up()
    {
        $fields = [
            'government_id_path' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'country'
            ],
            'invitation_letter_path' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'government_id_path'
            ],
            'profile_photo_path' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'invitation_letter_path'
            ],
        ];

        $this->forge->addColumn('invitations', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('invitations', [
            'government_id_path',
            'invitation_letter_path',
            'profile_photo_path'
        ]);
    }
}
