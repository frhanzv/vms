<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRegistrationSourceToInvitations extends Migration
{
    public function up()
    {
        $this->forge->addColumn('invitations', [
            'registration_source' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Walk-in',
                'null' => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('invitations', 'registration_source');
    }
}
