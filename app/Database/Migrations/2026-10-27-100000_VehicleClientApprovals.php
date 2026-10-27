<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Vehicle passes are shared and approved per client, like vendor and staff passes:
 * every client that owns one of a pass's Location Access gates (locations.client_id)
 * gets a row here, sees the pass and approves / rejects its own part.
 *
 * Fills the table for existing passes (drafts get no rows, same as vendor / staff):
 * Pending -> Pending, Approved / Issued / Terminated -> Approved, Rejected -> Rejected.
 */
class VehicleClientApprovals extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('vehicle_client_approvals')) {
            $this->forge->addField([
                'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'vehicle_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'client_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'status'     => ['type' => 'VARCHAR', 'constraint' => 12, 'default' => 'Pending'], // Pending | Approved | Rejected
                'acted_by'   => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'acted_at'   => ['type' => 'DATETIME', 'null' => true],
                'remark'     => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['vehicle_id', 'client_id']);
            $this->forge->addKey('client_id');
            $this->forge->createTable('vehicle_client_approvals');
        }

        if (! $this->db->tableExists('vehicles') || ! $this->db->tableExists('locations') || ! $this->db->fieldExists('client_id', 'locations')) {
            return;
        }

        // location code (slug of locations.location_access, as a pass stores it) => owning client
        $owner = [];
        foreach ($this->db->table('locations')->select('location_access, client_id')->where('client_id IS NOT NULL', null, false)->get()->getResultArray() as $l) {
            $owner[\App\Models\VendorLocationModel::slugifyCode((string) $l['location_access'])] = (int) $l['client_id'];
        }
        if ($owner === []) {
            return;
        }

        $map = ['Pending' => 'Pending', 'Approved' => 'Approved', 'Issued' => 'Approved', 'Terminated' => 'Approved', 'Rejected' => 'Rejected'];
        $now = date('Y-m-d H:i:s');
        $vehicles = $this->db->table('vehicles')->select('id, status, location_access')
            ->where("TRIM(COALESCE(location_access, '')) <> ''", null, false)->whereIn('status', array_keys($map))->get()->getResultArray();
        foreach ($vehicles as $v) {
            $clients = [];
            foreach (array_filter(array_map('trim', explode(',', (string) $v['location_access']))) as $code) {
                if (isset($owner[$code])) {
                    $clients[$owner[$code]] = true;
                }
            }
            foreach (array_keys($clients) as $cid) {
                $exists = $this->db->table('vehicle_client_approvals')->where(['vehicle_id' => (int) $v['id'], 'client_id' => $cid])->countAllResults() > 0;
                if (! $exists) {
                    $this->db->table('vehicle_client_approvals')->insert([
                        'vehicle_id' => (int) $v['id'], 'client_id' => $cid, 'status' => $map[$v['status']],
                        'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('vehicle_client_approvals', true);
    }
}
