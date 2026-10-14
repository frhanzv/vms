<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Second KPK alignment round for the vehicle module:
 *  - soft delete (soft_delete / deleted_at) on vehicles
 *  - vehicle_drivers: the vendor passes (drivers) linked to a vehicle, with the staff HOD per driver
 *  - vehicle_types.req_pass: the type needs a driver pass
 *  - vehicle_stickers: sticker history (active / inactive / expired)
 *  - companies.vehicle_max_car / _motorbike / _van: per-company vehicle allocation
 * Everything is added only if it is not there yet, so it is safe to run more than once.
 */
class VehicleKpkGaps extends Migration
{
    private function addIf(string $table, array $cols): void
    {
        if (! $this->db->tableExists($table)) {
            return;
        }
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
        $v = static fn(int $n) => ['type' => 'VARCHAR', 'constraint' => $n, 'null' => true];

        $this->addIf('vehicles', [
            'soft_delete' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->addIf('vehicle_types', ['req_pass' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0]]);
        $this->addIf('companies', [
            'vehicle_max_car'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'vehicle_max_motorbike' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'vehicle_max_van'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
        ]);

        if (! $this->db->tableExists('vehicle_drivers')) {
            $this->forge->addField([
                'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'vehicle_id' => ['type' => 'INT', 'unsigned' => true],
                'vendor_id'  => ['type' => 'INT', 'unsigned' => true],
                'hod'        => $v(100),
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('vehicle_id');
            $this->forge->addKey('vendor_id');
            $this->forge->createTable('vehicle_drivers');
        }

        if (! $this->db->tableExists('vehicle_stickers')) {
            $this->forge->addField([
                'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'vehicle_id'      => ['type' => 'INT', 'unsigned' => true],
                'sticker_year'    => $v(6),
                'serial_no'       => $v(40),
                'receipt_no'      => $v(60),
                'payment_type'    => $v(30),
                'issue_date'      => ['type' => 'DATE', 'null' => true],
                'pass_expiry'     => ['type' => 'DATE', 'null' => true],
                'collector_ic'    => $v(50),
                'collector_name'  => $v(100),
                'road_tax_expiry' => ['type' => 'DATE', 'null' => true],
                'insurance_expiry' => ['type' => 'DATE', 'null' => true],
                'status'          => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'active'], // active, inactive, expired
                'issued_by'       => $v(100),
                'created_at'      => ['type' => 'DATETIME', 'null' => true],
                'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('vehicle_id');
            $this->forge->addKey('serial_no');
            $this->forge->createTable('vehicle_stickers');

            // history for passes that were issued before this table existed
            if ($this->db->tableExists('vehicles') && $this->db->fieldExists('sticker_serial', 'vehicles')) {
                $now  = date('Y-m-d H:i:s');
                $rows = $this->db->table('vehicles')->whereIn('status', ['Issued', 'Terminated'])->where('sticker_serial IS NOT NULL', null, false)->get()->getResultArray();
                foreach ($rows as $r) {
                    $this->db->table('vehicle_stickers')->insert([
                        'vehicle_id' => $r['id'], 'sticker_year' => $r['sticker_year'] ?? null, 'serial_no' => $r['sticker_serial'], 'receipt_no' => $r['receipt_no'] ?? null,
                        'payment_type' => $r['payment_type'] ?? null, 'issue_date' => $r['issue_date'] ?? null, 'pass_expiry' => $r['pass_expiry'] ?? null,
                        'collector_ic' => $r['collector_ic'] ?? null, 'collector_name' => $r['collector_name'] ?? null,
                        'road_tax_expiry' => $r['road_tax_expiry'] ?? null, 'insurance_expiry' => $r['insurance_expiry'] ?? null,
                        'status' => ($r['status'] === 'Issued' && (empty($r['pass_expiry']) || $r['pass_expiry'] >= date('Y-m-d'))) ? 'active' : ($r['status'] === 'Issued' ? 'expired' : 'inactive'),
                        'issued_by' => $r['issued_by'] ?? null, 'created_at' => $r['issued_at'] ?? $now, 'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('vehicle_stickers', true);
        $this->forge->dropTable('vehicle_drivers', true);
        foreach ([['vehicles', ['soft_delete', 'deleted_at']], ['vehicle_types', ['req_pass']], ['companies', ['vehicle_max_car', 'vehicle_max_motorbike', 'vehicle_max_van']]] as [$t, $cols]) {
            foreach ($cols as $c) {
                if ($this->db->tableExists($t) && $this->db->fieldExists($c, $t)) {
                    $this->forge->dropColumn($t, $c);
                }
            }
        }
    }
}
