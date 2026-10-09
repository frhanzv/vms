<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

/**
 * KPK staff pass pipeline — ported from Safe-G KPK (VendorPass with
 * visitorOrVip = STAFF) onto VMS's existing `staff` table.
 *
 * KPK keeps two independent things on a staff pass, and so does this:
 *   - status     — the pass workflow: Draft -> Pending -> Approved / Rejected
 *                  (and Suspended). Same values the vendor pipeline uses.
 *   - is_active  — whether the person is still an active employee
 *                  (KPK VendorPass.IS_ACTIVE, which drives the separate
 *                  "Inactive Staff" list). Kiosk host lookup reads this.
 *
 * Before this migration `staff.status` held 'Active' / 'Inactive' /
 * 'Suspended' as a loose employment flag. Existing rows are mapped once:
 *   Active    -> status Approved,  is_active 1
 *   Inactive  -> status Approved,  is_active 0
 *   Suspended -> status Suspended, is_active 1
 *   empty     -> status Pending,   is_active 1   (old form never set one)
 *
 * Vehicle-related parts of the KPK staff module (staff vehicle pass,
 * vehicle request) are intentionally NOT ported.
 */
class AddKpkStaffPipelineFields extends Migration
{
    public function up()
    {
        $this->db->resetDataCache();

        $columns = [
            'company_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'id'],
            'access_branch'         => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true, 'after' => 'status'],
            'reject_reason'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'remark'],
            'is_active'             => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1, 'null' => false, 'after' => 'reject_reason'],
            'photo'                 => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'receipt_no'            => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'app_no'],
            'card_id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'collector_name'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'collector_ic_passport' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'issued_by'             => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'issued_at'             => ['type' => 'DATETIME', 'null' => true],
            'terminated_at'         => ['type' => 'DATETIME', 'null' => true],
            'terminated_by'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'suspended_reason'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'renewed_at'            => ['type' => 'DATETIME', 'null' => true],
            'updated_at'            => ['type' => 'DATETIME', 'null' => true],
        ];
        foreach (array_keys($columns) as $field) {
            if ($this->db->fieldExists($field, 'staff')) {
                unset($columns[$field]);
            }
        }
        if (! empty($columns)) {
            $this->forge->addColumn('staff', $columns);
        }

        // The old form allowed a long free-text location list in a TEXT
        // column already, and app_no/status are wide enough — nothing to widen.

        // ---- Map existing rows onto the KPK workflow (see class doc) ----
        $this->db->resetDataCache();
        $this->db->query("UPDATE staff SET is_active = 0, status = 'Approved' WHERE LOWER(status) = 'inactive'");
        $this->db->query("UPDATE staff SET is_active = 1, status = 'Approved' WHERE LOWER(status) = 'active'");
        $this->db->query("UPDATE staff SET status = 'Suspended' WHERE LOWER(status) = 'suspended'");
        $this->db->query("UPDATE staff SET status = 'Pending' WHERE status IS NULL OR status = '' OR status = '-'");
        // 'Review suspension' / 'Renew pass' / '-' were display text, not workflow steps.
        $this->db->query("UPDATE staff SET next_action = NULL WHERE next_action IS NOT NULL AND next_action NOT IN ('ksb_approve', 'kpk_approve')");

        // Card state used to live in staff_cards (latest row per staff).
        // Copy it onto the staff row, which is what the pipeline reads now.
        if ($this->db->tableExists('staff_cards')) {
            $this->db->query(
                "UPDATE staff s
                 JOIN (SELECT sc.staff_id, sc.status, sc.expiry_date
                         FROM staff_cards sc
                         JOIN (SELECT staff_id, MAX(id) AS max_id FROM staff_cards GROUP BY staff_id) m
                           ON m.max_id = sc.id) c ON c.staff_id = s.id
                 SET s.card_status = CASE LOWER(c.status) WHEN 'active' THEN 'Active' WHEN 'terminated' THEN 'Terminated' ELSE 'Inactive' END,
                     s.card_expiry = COALESCE(s.card_expiry, c.expiry_date)
                 WHERE s.card_status IS NULL OR s.card_status = '' OR s.card_status = '-'"
            );
        }
        $this->db->query("UPDATE staff SET card_status = 'Inactive' WHERE card_status IS NULL OR card_status = '' OR card_status = '-'");
        $this->db->query("UPDATE staff SET card_status = 'Active' WHERE LOWER(card_status) = 'active'");
        $this->db->query("UPDATE staff SET card_status = 'Terminated' WHERE LOWER(card_status) = 'terminated'");
        $this->db->query("UPDATE staff SET card_status = 'Inactive' WHERE LOWER(card_status) NOT IN ('active', 'terminated')");

        // ---- Supporting tables (same shape as the vendor_* ones) ----
        if (! $this->db->tableExists('staff_status_logs')) {
            $this->forge->addField([
                'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'staff_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'action'        => ['type' => 'VARCHAR', 'constraint' => 30],
                'from_status'   => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'to_status'     => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'next_action'   => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'remark'        => ['type' => 'TEXT', 'null' => true],
                'reject_reason' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'acted_by'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'created_at'    => ['type' => 'DATETIME', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('staff_id');
            $this->forge->createTable('staff_status_logs');
        }

        if (! $this->db->tableExists('staff_card_print_logs')) {
            $this->forge->addField([
                'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'staff_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'receipt_no' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'is_reprint' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'reason'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'printed_by' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'printed_at' => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('staff_id');
            $this->forge->createTable('staff_card_print_logs');
        }

        if (! $this->db->tableExists('staff_driving_licenses')) {
            $this->forge->addField([
                'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'staff_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'license_class'  => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'license_expiry' => ['type' => 'DATE', 'null' => true],
                'created_at'     => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('staff_id');
            $this->forge->createTable('staff_driving_licenses');

            // Carry over the single license the old import wrote onto the staff row.
            if ($this->db->fieldExists('license_class', 'staff')) {
                $this->db->query(
                    "INSERT INTO staff_driving_licenses (staff_id, license_class, license_expiry, created_at)
                     SELECT id, license_class, license_expiry, NOW() FROM staff
                     WHERE (license_class IS NOT NULL AND license_class <> '') OR license_expiry IS NOT NULL"
                );
            }
        }

        // ---- Role permissions ----
        // Roles that could already edit staff passes get the new approve /
        // reject actions too, so nobody loses the ability to move a pass on.
        // Roles with an empty access JSON already have full access.
        $roles = $this->db->table('roles')->select('id, access')->get()->getResultArray();
        foreach ($roles as $role) {
            $access = json_decode((string) ($role['access'] ?? ''), true);
            if (! is_array($access) || empty($access)) {
                continue;
            }
            $staff = $access['staff_pass_list'] ?? null;
            if (! is_array($staff) || empty($staff['edit'])) {
                continue;
            }
            foreach (['approve', 'reject', 'manage_status'] as $action) {
                if (! array_key_exists($action, $staff)) {
                    $staff[$action] = true;
                }
            }
            $access['staff_pass_list'] = $staff;
            $this->db->table('roles')->where('id', $role['id'])->update(['access' => json_encode($access)]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('staff_status_logs', true);
        $this->forge->dropTable('staff_card_print_logs', true);
        $this->forge->dropTable('staff_driving_licenses', true);

        $this->db->resetDataCache();
        $drop = [];
        foreach ([
            'company_id', 'access_branch', 'reject_reason', 'is_active', 'photo', 'receipt_no', 'card_id',
            'collector_name', 'collector_ic_passport', 'issued_by', 'issued_at', 'terminated_at',
            'terminated_by', 'suspended_reason', 'renewed_at', 'updated_at',
        ] as $field) {
            if ($this->db->fieldExists($field, 'staff')) {
                $drop[] = $field;
            }
        }
        if ($drop) {
            $this->forge->dropColumn('staff', $drop);
        }
        // Workflow status values are left as they are; they read fine as text.
    }
}
