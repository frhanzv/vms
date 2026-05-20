<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDoorCheckinCheckoutToVisitorCardLogsAction extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE visitor_card_logs MODIFY COLUMN action ENUM('checkin','checkout','door_access','door_checkin','door_checkout') NOT NULL");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE visitor_card_logs MODIFY COLUMN action ENUM('checkin','checkout','door_access') NOT NULL");
    }
}
