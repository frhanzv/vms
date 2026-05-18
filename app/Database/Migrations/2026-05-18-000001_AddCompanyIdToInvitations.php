<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompanyIdToInvitations extends Migration
{
    public function up()
    {
        $this->forge->addColumn('invitations', [
            'company_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('invitations', 'company_id');
    }
}
