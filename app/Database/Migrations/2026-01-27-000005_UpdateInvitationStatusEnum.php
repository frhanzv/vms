<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateInvitationStatusEnum extends Migration
{
    public function up()
    {
        // Modify status column to include 'Submitted'
        $sql = "ALTER TABLE invitations MODIFY COLUMN status ENUM('Pending', 'Submitted', 'Approved', 'Rejected') DEFAULT 'Pending'";
        $this->db->query($sql);
    }

    public function down()
    {
        // Revert to original ENUM values
        $sql = "ALTER TABLE invitations MODIFY COLUMN status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending'";
        $this->db->query($sql);
    }
}
