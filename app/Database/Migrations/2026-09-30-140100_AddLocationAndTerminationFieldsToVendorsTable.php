<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Real KPK's Card Info panel (reached from Closed List / Process Detail)
 * has an "Edit Location Access" button (checkboxes: Annexe Building, KPK
 * Gate, KSB Phase 2 Gate, Phase 1 in the reference screenshots) and a
 * "Card Terminate" button. Neither had backing columns yet.
 */
class AddLocationAndTerminationFieldsToVendorsTable extends Migration
{
    public function up()
    {
        // Guarded per-column — see AddApprovalWorkflowFieldsToVendorsTable
        // for why.
        $this->db->resetDataCache();
        $columns = [
            'location_access' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'card_status',
                'comment'    => 'Comma-separated location codes, see VendorCardInfo::LOCATION_OPTIONS',
            ],
            'terminated_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'location_access',
            ],
            'terminated_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'terminated_at',
            ],
        ];
        foreach (array_keys($columns) as $field) {
            if ($this->db->fieldExists($field, 'vendors')) {
                unset($columns[$field]);
            }
        }
        if (! empty($columns)) {
            $this->forge->addColumn('vendors', $columns);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('vendors', ['location_access', 'terminated_at', 'terminated_by']);
    }
}
