<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeInvitationLinkExpiryToDateTime extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('invitations', [
            'link_expiry' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('invitations', [
            'link_expiry' => [
                'type' => 'DATE',
                'null' => true,
            ],
        ]);
    }
}
