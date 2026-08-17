<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmapTransitionsToVisitorCardLogs extends Migration
{
    public function up(): void
    {
        $fields = $this->db->getFieldNames('visitor_card_logs');
        $add = [];
        if (!in_array('emap_asset_id', $fields, true)) {
            $add['emap_asset_id'] = ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'sub_location_id'];
        }
        if (!in_array('from_sub_location_id', $fields, true)) {
            $add['from_sub_location_id'] = ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'emap_asset_id'];
        }
        if (!in_array('to_sub_location_id', $fields, true)) {
            $add['to_sub_location_id'] = ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'from_sub_location_id'];
        }
        if ($add) {
            $this->forge->addColumn('visitor_card_logs', $add);
        }
    }

    public function down(): void
    {
        foreach (['to_sub_location_id', 'from_sub_location_id', 'emap_asset_id'] as $column) {
            if ($this->db->fieldExists($column, 'visitor_card_logs')) {
                $this->forge->dropColumn('visitor_card_logs', $column);
            }
        }
    }
}
