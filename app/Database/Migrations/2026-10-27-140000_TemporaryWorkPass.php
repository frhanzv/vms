<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * KPK Temporary Work Pass (TWP): a company requests passes for a group of workers for a
 * time window; on approval every worker gets a pass, a TWP card is issued at the counter
 * and the gate records twp_visits. Card pool = KPK "Temporary Work Pass Card" config.
 */
class TemporaryWorkPass extends Migration
{
    public function up()
    {
        $id = ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true];
        $fk = ['type' => 'INT', 'unsigned' => true, 'null' => true];
        $s  = static fn(int $n, bool $null = true) => ['type' => 'VARCHAR', 'constraint' => $n, 'null' => $null];
        $dt = ['type' => 'DATETIME', 'null' => true];

        if (! $this->db->tableExists('twp_requests')) {
            $this->forge->addField([
                'id' => $id, 'company_id' => $fk, 'app_no' => $s(80), 'receipt_no' => $s(80), 'app_date' => $dt, 'requested_by' => $fk,
                'company_name' => $s(200), 'company_reg_id' => $s(80), 'resident' => $s(20), 'ic_no' => $s(40), 'passport_no' => $s(40),
                'full_name' => $s(200), 'contact_no' => $s(40), 'email' => $s(150), 'sub_type' => $s(100), 'type_of_work' => $s(150),
                'location_access' => ['type' => 'TEXT', 'null' => true], 'date_from' => $dt, 'date_to' => $dt,
                'payment_type' => $s(30), 'amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0], 'payment_status' => $s(20), 'payment_ref' => $s(100),
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Pending'], 'remark' => ['type' => 'TEXT', 'null' => true],
                'reject_reason' => $s(255), 'reviewed_by' => $fk, 'reviewed_at' => $dt, 'created_at' => $dt, 'updated_at' => $dt,
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('company_id');
            $this->forge->addKey('status');
            $this->forge->createTable('twp_requests');
        }

        if (! $this->db->tableExists('twp_workers')) {
            $this->forge->addField([
                'id' => $id, 'request_id' => ['type' => 'INT', 'unsigned' => true], 'name' => $s(200, false), 'resident' => $s(20), 'ic_no' => $s(40),
                'passport_no' => $s(40), 'contact_no' => $s(40), 'vehicle_no' => $s(40), 'pass_no' => $s(80), 'qr_string' => $s(64),
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Pending'], 'card_id' => $fk,
                'card_status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Not Issued'], 'issued_at' => $dt, 'issued_by' => $fk,
                'returned_at' => $dt, 'created_at' => $dt, 'updated_at' => $dt,
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('request_id');
            $this->forge->addKey('card_id');
            $this->forge->createTable('twp_workers');
        }

        if (! $this->db->tableExists('twp_cards')) {
            $this->forge->addField([
                'id' => $id, 'company_id' => $fk, 'card_id' => $s(100, false), 'card_no' => $s(80, false),
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'], 'created_by' => $fk, 'created_at' => $dt, 'updated_at' => $dt,
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('card_id');
            $this->forge->createTable('twp_cards');
        }

        if (! $this->db->tableExists('twp_visits')) {
            $this->forge->addField([
                'id' => $id, 'worker_id' => ['type' => 'INT', 'unsigned' => true], 'card_id' => $fk, 'check_in_time' => $dt, 'check_out_time' => $dt,
                'check_in_lane_id' => $fk, 'check_out_lane_id' => $fk, 'created_at' => $dt, 'updated_at' => $dt,
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['worker_id', 'check_out_time']);
            $this->forge->createTable('twp_visits');
        }

        if (! $this->db->tableExists('twp_card_logs')) {
            $this->forge->addField([
                'id' => $id, 'worker_id' => $fk, 'card_id' => $fk, 'action' => $s(20, false), 'lane_id' => $fk, 'message' => $s(255), 'scanned_at' => $dt,
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('action');
            $this->forge->createTable('twp_card_logs');
        }
    }

    public function down()
    {
        foreach (['twp_card_logs', 'twp_visits', 'twp_cards', 'twp_workers', 'twp_requests'] as $t) {
            $this->forge->dropTable($t, true);
        }
    }
}
