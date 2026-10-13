<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Vehicle module, part 2:
 *  - Public vs Cargo|Non-Cargo group on every pass and vehicle type
 *  - who the pass is for (vendor or staff), the vendor company, who created it, and the pass it renews
 *  - Transfer Ownership requests
 *  - Daily Pass tables (the feature itself is switched off by default, see VEHICLE_DAILY_PASS in .env)
 */
class VehicleGroupsTransferDaily extends Migration
{
    private function addIf(string $table, array $cols): void
    {
        $add = [];
        foreach ($cols as $name => $def) {
            if (! $this->db->fieldExists($name, $table)) {
                $add[$name] = $def;
            }
        }
        if ($add) {
            $this->forge->addColumn($table, $add);
        }
    }

    public function up()
    {
        if ($this->db->tableExists('vehicles')) {
            $this->addIf('vehicles', [
                'vehicle_group'     => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'public'],   // public | cnc (Cargo / Non-Cargo)
                'pass_for'          => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'vendor'],   // vendor | staff
                'vendor_company_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'created_by_user'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'renewed_from'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            ]);
        }
        if ($this->db->tableExists('vehicle_types')) {
            $this->addIf('vehicle_types', [
                'vehicle_group' => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'both'],         // public | cnc | both
            ]);
        }

        if (! $this->db->tableExists('vehicle_transfers')) {
            $this->forge->addField([
                'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'client_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'vehicle_id'      => ['type' => 'INT', 'unsigned' => true],
                'transfer_no'     => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'from_owner_type' => ['type' => 'VARCHAR', 'constraint' => 12, 'null' => true],
                'from_owner_name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'from_owner_ic'   => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'from_company'    => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'to_owner_type'   => ['type' => 'VARCHAR', 'constraint' => 12],
                'to_owner_name'   => ['type' => 'VARCHAR', 'constraint' => 150],
                'to_owner_ic'     => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'to_company'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'to_contact_no'   => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'reason'          => ['type' => 'TEXT', 'null' => true],
                'status'          => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'Pending'], // Pending, Approved, Rejected
                'reject_reason'   => ['type' => 'TEXT', 'null' => true],
                'requested_by'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'decided_by'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'decided_at'      => ['type' => 'DATETIME', 'null' => true],
                'created_at'      => ['type' => 'DATETIME', 'null' => true],
                'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('vehicle_id');
            $this->forge->addKey('client_id');
            $this->forge->createTable('vehicle_transfers');
        }

        if (! $this->db->tableExists('vehicle_daily_passes')) {
            $this->forge->addField([
                'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'client_id'         => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'app_no'            => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'app_date'          => ['type' => 'DATE', 'null' => true],
                'vendor_company_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'company_name'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'driver_name'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'driver_ic'         => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'contact_no'        => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'vehicle_type_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'plate_no'          => ['type' => 'VARCHAR', 'constraint' => 20],
                'purpose'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'visit_from'        => ['type' => 'DATE', 'null' => true],
                'visit_to'          => ['type' => 'DATE', 'null' => true],
                'location_access'   => ['type' => 'TEXT', 'null' => true],
                'status'            => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'Draft'], // Draft, Pending, Approved, Rejected
                'reject_reason'     => ['type' => 'TEXT', 'null' => true],
                'requested_by'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'created_by_user'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'approved_by'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'approved_at'       => ['type' => 'DATETIME', 'null' => true],
                'created_at'        => ['type' => 'DATETIME', 'null' => true],
                'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('client_id');
            $this->forge->addKey('status');
            $this->forge->createTable('vehicle_daily_passes');
        }
    }

    public function down()
    {
        foreach (['vehicle_daily_passes', 'vehicle_transfers'] as $t) {
            $this->forge->dropTable($t, true);
        }
        foreach (['vehicle_group', 'pass_for', 'vendor_company_id', 'created_by_user', 'renewed_from'] as $c) {
            if ($this->db->fieldExists($c, 'vehicles')) {
                $this->forge->dropColumn('vehicles', $c);
            }
        }
        if ($this->db->fieldExists('vehicle_group', 'vehicle_types')) {
            $this->forge->dropColumn('vehicle_types', 'vehicle_group');
        }
    }
}
