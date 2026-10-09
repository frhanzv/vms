<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Cards can now be re-ordered (drag / arrows in the Customize drawer). */
class AddSortOrderToDashboardCardSettings extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('dashboard_card_settings') && ! $this->db->fieldExists('sort_order', 'dashboard_card_settings')) {
            $this->forge->addColumn('dashboard_card_settings', [
                'sort_order' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('sort_order', 'dashboard_card_settings')) {
            $this->forge->dropColumn('dashboard_card_settings', 'sort_order');
        }
    }
}
