<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Brings the vehicle module in line with the KPK screens:
 *  - sticker year / collection date / date of issuance / collector IC on a pass
 *  - Cargo | Non-Cargo: vehicle operation, trailer weight, trailer type; staff: department and HOD
 *  - vehicle types: operation + the two new "extra detail" flags
 *  - reissue history (Damage / Lost / Stolen)
 *  - daily pass: staff or individual, vehicle details, several visit dates
 *  - companies.ledger_acc_no (a company with a ledger account may pay by LEDGER)
 * Everything is added only if it is not there yet.
 */
class VehicleKpkAlignment extends Migration
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
            'vehicle_operation' => $v(40), 'trailer_weight' => $v(30), 'trailer_type' => $v(6),
            'staff_no' => $v(50), 'department' => $v(100), 'hod' => $v(100),
            'collect_date' => ['type' => 'DATE', 'null' => true],
            'issue_date'   => ['type' => 'DATE', 'null' => true],
            'collector_ic' => $v(50),
            'terminated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->addIf('vehicle_types', [
            'operation'          => $v(40),
            'trailer_weight_req' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'trailer_type_req'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ]);
        $this->addIf('vehicle_daily_passes', [
            'app_for' => ['type' => 'VARCHAR', 'constraint' => 12, 'default' => 'Individual'],
            'staff_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'staff_no' => $v(50), 'department' => $v(100), 'hod' => $v(100),
            'year_made' => $v(6), 'colour' => $v(40), 'brand' => $v(60),
            'road_tax_expiry' => ['type' => 'DATE', 'null' => true], 'insurance_expiry' => ['type' => 'DATE', 'null' => true],
            'visit_dates' => ['type' => 'TEXT', 'null' => true], // JSON [{"from":"Y-m-d","to":"Y-m-d"}, ...]
            'remark' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->addIf('companies', ['ledger_acc_no' => $v(40)]);
        $this->addIf('vehicle_transfers', [
            'from_company_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'to_company_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
        ]);

        if (! $this->db->tableExists('vehicle_reissues')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'vehicle_id'  => ['type' => 'INT', 'unsigned' => true],
                'reason'      => ['type' => 'VARCHAR', 'constraint' => 12],
                'old_serial'  => $v(40),
                'new_serial'  => $v(40),
                'issue_date'  => ['type' => 'DATE', 'null' => true],
                'collector_ic' => $v(50),
                'payment_type' => $v(30),
                'receipt_no'  => $v(60),
                'remark'      => ['type' => 'TEXT', 'null' => true],
                'done_by'     => $v(100),
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('vehicle_id');
            $this->forge->createTable('vehicle_reissues');
        }
    }

    public function down()
    {
        $this->forge->dropTable('vehicle_reissues', true);
    }
}
