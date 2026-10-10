<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Multi-client sharing for the WHOLE product (Staff + Visitor, on top of the
 * Vendor part in 2026-10-10-100000):
 *
 *  - locations.client_id  the ONE client that owns a gate / "Location Access"
 *                         row (used by Staff passes and Visitor invitations)
 *  - staff_client_approvals  per client approval of a staff pass (like vendors)
 *
 * Existing rows stay NULL = "not assigned yet" and keep behaving as before
 * (visible as they are today) until an owner is set.
 */
class MultiClientWholeProduct extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('locations') && ! $this->db->fieldExists('client_id', 'locations')) {
            $this->forge->addColumn('locations', [
                'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            ]);
        }
        // Staff passes are approved per client, exactly like vendor passes.
        if (! $this->db->tableExists('staff_client_approvals')) {
            $this->forge->addField([
                'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'staff_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'status'    => ['type' => 'VARCHAR', 'constraint' => 12, 'default' => 'Pending'], // Pending | Approved | Rejected
                'acted_by'  => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'acted_at'  => ['type' => 'DATETIME', 'null' => true],
                'remark'    => ['type' => 'TEXT', 'null' => true],
                'created_at'=> ['type' => 'DATETIME', 'null' => true],
                'updated_at'=> ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['staff_id', 'client_id']);
            $this->forge->addKey('client_id');
            $this->forge->createTable('staff_client_approvals');
        }
    }

    public function down()
    {
        $this->forge->dropTable('staff_client_approvals', true);
        if ($this->db->fieldExists('client_id', 'locations')) {
            $this->forge->dropColumn('locations', 'client_id');
        }
    }
}
