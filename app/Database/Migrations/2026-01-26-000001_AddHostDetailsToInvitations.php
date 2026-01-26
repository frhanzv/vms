<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHostDetailsToInvitations extends Migration
{
    public function up()
    {
        $fields = [
            'staff_id' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'after' => 'invited_by'
            ],
            'company_visited' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'staff_id'
            ],
            'host_contact' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'company_visited'
            ],
        ];

        $this->forge->addColumn('invitations', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('invitations', ['staff_id', 'company_visited', 'host_contact']);
    }
}
