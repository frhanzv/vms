<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisitorDetailsToInvitations extends Migration
{
    public function up()
    {
        $fields = [
            'date_of_birth' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'visitor_email'
            ],
            'sex' => [
                'type' => 'ENUM',
                'constraint' => ['Male', 'Female'],
                'null' => true,
                'after' => 'date_of_birth'
            ],
            'resident' => [
                'type' => 'ENUM',
                'constraint' => ['Malaysian', 'Non-Malaysian'],
                'null' => true,
                'after' => 'sex'
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'resident'
            ],
            'postcode' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
                'after' => 'address'
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'postcode'
            ],
            'state' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'city'
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'state'
            ],
            'registration_no' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'after' => 'company'
            ],
        ];

        $this->forge->addColumn('invitations', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('invitations', [
            'date_of_birth',
            'sex',
            'resident',
            'address',
            'postcode',
            'city',
            'state',
            'country',
            'registration_no'
        ]);
    }
}
