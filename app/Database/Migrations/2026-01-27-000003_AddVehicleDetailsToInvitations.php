<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVehicleDetailsToInvitations extends Migration
{
    public function up()
    {
        $fields = [
            'vehicle_category' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'after' => 'vehicle_registration'
            ],
            'vehicle_type' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'after' => 'vehicle_category'
            ],
        ];

        $this->forge->addColumn('invitations', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('invitations', ['vehicle_category', 'vehicle_type']);
    }
}
