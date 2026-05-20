<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubLocationIdToVisitorCardLogs extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Guard: column may already exist if it was added outside migrations
        $fields = $db->getFieldNames('visitor_card_logs');
        if (in_array('sub_location_id', $fields)) {
            return;
        }

        $this->forge->addColumn('visitor_card_logs', [
            'sub_location_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'lane_id',
            ],
        ]);

        $db->query('ALTER TABLE visitor_card_logs
            ADD CONSTRAINT visitor_card_logs_sub_location_id_foreign
            FOREIGN KEY (sub_location_id) REFERENCES sub_locations(id)
            ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->query('ALTER TABLE visitor_card_logs DROP FOREIGN KEY visitor_card_logs_sub_location_id_foreign');
        $this->forge->dropColumn('visitor_card_logs', 'sub_location_id');
    }
}
