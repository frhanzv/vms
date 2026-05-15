<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReceiveEmailNotificationsToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'receive_email_notifications' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
                'after'      => 'is_active',
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'receive_email_notifications');
    }
}
