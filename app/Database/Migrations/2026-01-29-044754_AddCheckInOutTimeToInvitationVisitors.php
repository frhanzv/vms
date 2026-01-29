<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCheckInOutTimeToInvitationVisitors extends Migration
{
    public function up()
    {
        // Add check_in_time and check_out_time columns to invitation_visitors table
        $fields = [
            'check_in_time' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'visitor_card_id'
            ],
            'check_out_time' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'check_in_time'
            ]
        ];
        
        $this->forge->addColumn('invitation_visitors', $fields);
    }

    public function down()
    {
        // Drop columns
        $this->forge->dropColumn('invitation_visitors', ['check_in_time', 'check_out_time']);
    }
}
