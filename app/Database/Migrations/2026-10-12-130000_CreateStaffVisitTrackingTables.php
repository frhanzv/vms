<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

/**
 * Staff gate tracking — the last part of the KPK staff module.
 *
 * KPK's Staff Report, Staff In-Premise and Staff Out-of-Window reports all
 * read gate-in / gate-out times for staff passes. VMS had the staff pass
 * pipeline (2026-10-09 migration) but recorded no gate movements for staff,
 * so those reports had nothing to read. This adds the same pair of tables
 * vendors use (vendor_visits / vendor_card_logs):
 *
 *   staff_visits     — one row per gate-in / gate-out cycle.
 *   staff_card_logs  — every tap, including denied ones (KPK "Rejected").
 *
 * Written by App\Libraries\StaffGate, which RFID::scan / RFID::scanLane fall
 * back to when a card is not a visitor's, and by GET api/rfid/staff-scan.
 *
 * Also grants the new report.staff_report permission to every role that can
 * already view the Staff Pass List (roles with an empty access JSON already
 * have full access and are left alone).
 */
class CreateStaffVisitTrackingTables extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('staff_visits')) {
            $this->forge->addField([
                'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'staff_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'card_id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'check_in_time'     => ['type' => 'DATETIME', 'null' => true],
                'check_out_time'    => ['type' => 'DATETIME', 'null' => true],
                'check_in_lane_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'check_out_lane_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'version'           => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
                'created_at'        => ['type' => 'DATETIME', 'default' => new RawSql('CURRENT_TIMESTAMP')],
                'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('staff_id');
            $this->forge->addKey('card_id');
            $this->forge->addKey('check_in_time');
            $this->forge->addKey(['check_out_time', 'check_in_time']);
            $this->forge->createTable('staff_visits');
        }

        if (! $this->db->tableExists('staff_card_logs')) {
            $this->forge->addField([
                'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'staff_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'card_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'action'     => ['type' => 'VARCHAR', 'constraint' => 20], // checkin | checkout | denied
                'lane_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'message'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'scanned_at' => ['type' => 'DATETIME'],
                'created_at' => ['type' => 'DATETIME', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('staff_id');
            $this->forge->addKey(['action', 'scanned_at']);
            $this->forge->createTable('staff_card_logs');
        }

        // ---- report.staff_report permission ----
        if (! $this->db->tableExists('roles') || ! $this->db->fieldExists('access', 'roles')) {
            return;
        }
        $roles = $this->db->table('roles')->select('id, access')->get()->getResultArray();
        foreach ($roles as $role) {
            $access = json_decode((string) ($role['access'] ?? ''), true);
            if (! is_array($access) || empty($access)) {
                continue;
            }
            if (empty($access['staff_pass_list']['view'])) {
                continue;
            }
            $report = is_array($access['report'] ?? null) ? $access['report'] : [];
            if (array_key_exists('staff_report', $report)) {
                continue;
            }
            $report['staff_report'] = true;
            $access['report']       = $report;
            $this->db->table('roles')->where('id', $role['id'])->update(['access' => json_encode($access)]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('staff_card_logs', true);
        $this->forge->dropTable('staff_visits', true);
        // The report.staff_report permission key is left in roles.access; it is harmless without the page.
    }
}
