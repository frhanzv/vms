<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAssignedToVisitorCardLogsEnums extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE visitor_card_logs
            MODIFY COLUMN action ENUM('checkin','checkout','door_access','door_checkin','door_checkout','assigned') NOT NULL");

        $this->db->query("ALTER TABLE visitor_card_logs
            MODIFY COLUMN scan_source ENUM('rfid','qr_code','admin') NOT NULL DEFAULT 'rfid'");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE visitor_card_logs
            MODIFY COLUMN action ENUM('checkin','checkout','door_access','door_checkin','door_checkout') NOT NULL");

        $this->db->query("ALTER TABLE visitor_card_logs
            MODIFY COLUMN scan_source ENUM('rfid','qr_code') NOT NULL DEFAULT 'rfid'");
    }
}
