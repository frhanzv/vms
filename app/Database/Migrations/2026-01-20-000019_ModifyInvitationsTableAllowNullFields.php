<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyInvitationsTableAllowNullFields extends Migration
{
    public function up()
    {
        // Modify ic_passport to allow null values since visitor fills this later
        $this->forge->modifyColumn('invitations', [
            'ic_passport' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'vehicle_registration' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ]
        ]);
    }

    public function down()
    {
        // Revert changes
        $this->forge->modifyColumn('invitations', [
            'ic_passport' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
            ],
            'vehicle_registration' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => false,
            ]
        ]);
    }
}