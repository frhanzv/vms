<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Vendor Workflow: saved order (Edit Sequence), custom steps and who does a custom step. */
class AddSequenceToVendorWorkflow extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('vendor_workflow_settings')) {
            return;
        }
        $add = [];
        if (! $this->db->fieldExists('sort_order', 'vendor_workflow_settings')) {
            $add['sort_order'] = ['type' => 'INT', 'null' => true];
        }
        if (! $this->db->fieldExists('is_custom', 'vendor_workflow_settings')) {
            $add['is_custom'] = ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0];
        }
        if (! $this->db->fieldExists('owner', 'vendor_workflow_settings')) {
            $add['owner'] = ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true];
        }
        if ($add) {
            $this->forge->addColumn('vendor_workflow_settings', $add);
        }
    }

    public function down()
    {
        foreach (['sort_order', 'is_custom', 'owner'] as $c) {
            if ($this->db->fieldExists($c, 'vendor_workflow_settings')) {
                $this->forge->dropColumn('vendor_workflow_settings', $c);
            }
        }
    }
}
