<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Replaces the hardcoded LOCATION_OPTIONS const that used to be duplicated
 * across VendorPassRequest, VendorCardInfo (and read from the former by
 * VendorList/VendorProcessDetail) with an admin-editable list, per request:
 * "create one more page where user can input the new location and the new
 * location will appear at the page that has connection to it."
 *
 * Global, not per-client — these are physical locations/gates at the port
 * shared by every vendor company, same as the hardcoded list was.
 */
class CreateVendorLocationsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('vendor_locations')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'label' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('vendor_locations');

        // Seed with the same 4 codes the old hardcoded const used, so
        // existing vendors.location_access / visitor_cards data (which
        // stores these codes as comma-separated strings) keeps resolving
        // to the same labels after the switch to this table.
        $now = date('Y-m-d H:i:s');
        $this->db->table('vendor_locations')->insertBatch([
            ['code' => 'annexe_building', 'label' => 'Annexe Building',   'sort_order' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'kpk_gate',        'label' => 'KPK Gate',          'sort_order' => 2, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'ksb_phase2_gate', 'label' => 'KSB Phase 2 Gate',  'sort_order' => 3, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'phase1',          'label' => 'Phase 1',           'sort_order' => 4, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down()
    {
        if ($this->db->tableExists('vendor_locations')) {
            $this->forge->dropTable('vendor_locations');
        }
    }
}
