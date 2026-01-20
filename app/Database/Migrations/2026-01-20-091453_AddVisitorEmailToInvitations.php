<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisitorEmailToInvitations extends Migration
{
    public function up()
    {
        $this->forge->addColumn('invitations', [
            'visitor_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'contact'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('invitations', 'visitor_email');
    }
}
