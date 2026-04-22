<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStaffCardsTable extends Migration
{
    public function up()
    {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS `staff_cards` (
                `id` int NOT NULL AUTO_INCREMENT,
                `staff_id` int NOT NULL,
                `status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'active',
                `expiry_date` date DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `fk_staff_cards_staff` (`staff_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
        );
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `staff_cards`');
    }
}
