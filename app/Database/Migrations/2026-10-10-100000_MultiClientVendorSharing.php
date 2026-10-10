<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * One product, several clients in the same building (e.g. KSB and KPK).
 *
 *  - clients.code            every client gets a unique code -> its own link  /c/{code}
 *  - clients.site_group      clients with the same group share the building
 *                            (a vendor sees the locations of all of them)
 *  - vendor_locations.client_id   the ONE client that owns a location
 *  - vendor_client_approvals      one row per vendor pass per client whose
 *                                 location was selected: that client approves
 *                                 its own locations, and sees the pass.
 */
class MultiClientVendorSharing extends Migration
{
    public function up()
    {
        // ---- clients.site_group + unique codes -----------------------------
        if (! $this->db->fieldExists('site_group', 'clients')) {
            $this->forge->addColumn('clients', [
                'site_group' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            ]);
        }
        $used = [];
        foreach ($this->db->table('clients')->select('id, name, code')->get()->getResultArray() as $c) {
            $code = strtoupper(trim((string) ($c['code'] ?? '')));
            if ($code === '' || isset($used[$code])) {
                $base = strtoupper(preg_replace('/[^A-Za-z0-9]+/', '', (string) $c['name'])) ?: 'CLIENT';
                $code = substr($base, 0, 12);
                if (isset($used[$code])) {
                    $code = substr($base, 0, 8) . $c['id'];
                }
            }
            $used[$code] = true;
            if ($code !== (string) ($c['code'] ?? '')) {
                $this->db->table('clients')->where('id', $c['id'])->update(['code' => $code]);
            }
        }

        // ---- vendor_locations.client_id ------------------------------------
        if ($this->db->tableExists('vendor_locations') && ! $this->db->fieldExists('client_id', 'vendor_locations')) {
            $this->forge->addColumn('vendor_locations', [
                'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            ]);
        }

        // ---- per-client approval of a vendor pass --------------------------
        if (! $this->db->tableExists('vendor_client_approvals')) {
            $this->forge->addField([
                'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'vendor_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'status'    => ['type' => 'VARCHAR', 'constraint' => 12, 'default' => 'Pending'], // Pending | Approved | Rejected
                'acted_by'  => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'acted_at'  => ['type' => 'DATETIME', 'null' => true],
                'remark'    => ['type' => 'TEXT', 'null' => true],
                'created_at'=> ['type' => 'DATETIME', 'null' => true],
                'updated_at'=> ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['vendor_id', 'client_id']);
            $this->forge->addKey('client_id');
            $this->forge->createTable('vendor_client_approvals');
        }
    }

    public function down()
    {
        $this->forge->dropTable('vendor_client_approvals', true);
        if ($this->db->fieldExists('client_id', 'vendor_locations')) {
            $this->forge->dropColumn('vendor_locations', 'client_id');
        }
        if ($this->db->fieldExists('site_group', 'clients')) {
            $this->forge->dropColumn('clients', 'site_group');
        }
    }
}
