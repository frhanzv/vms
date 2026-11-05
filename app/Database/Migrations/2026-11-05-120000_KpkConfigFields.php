<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * KPK Config fields VMS did not have yet:
 *   companies     Branch (KSB / KPK / BOTH), Account Type (CASH / LEDGER / CREDIT), Haulier Code,
 *                 Mother Company (ROC No), Gov Agent, Created By. Ledger Code is the existing ledger_acc_no.
 *   sub_companies Prefix
 *   users         Type (USER / STAFF), Country, Passport No, Work Permit (PLKS) Expiry, Branch, Remark
 * Role Management: new boxes Dashboard > Temporary Work Pass and Temporary Work Pass > Request List,
 * Pass List, TWP Card, Report. Roles that already have an access list get them ticked, so every role
 * keeps seeing TWP as before (the twp/* routes still allow superadmin, client superadmin, admin, officer only).
 * Nothing is seeded.
 */
class KpkConfigFields extends Migration
{
    private function add(string $table, array $fields): void
    {
        if (! $this->db->tableExists($table)) {
            return;
        }
        $new = array_filter($fields, fn($k) => ! $this->db->fieldExists($k, $table), ARRAY_FILTER_USE_KEY);
        if ($new) {
            $this->forge->addColumn($table, $new);
        }
    }

    public function up()
    {
        $this->add('companies', [
            'branch'             => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'ledger_acc_no'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'account_type'       => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'haulier_code'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'mother_company_roc' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'gov_agent'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_by'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
        ]);
        $this->add('sub_companies', [
            'prefix' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
        ]);
        $this->add('users', [
            'user_type'   => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'country'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'passport_no' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'plks_expiry' => ['type' => 'DATE', 'null' => true],
            'branch'      => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'remark'      => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
        ]);

        if (! $this->db->tableExists('roles') || ! $this->db->fieldExists('access', 'roles')) {
            return;
        }
        $keys = ['dashboard' => ['twp'], 'twp' => ['request_list', 'pass_list', 'card', 'report']];
        foreach ($this->db->table('roles')->select('id, access')->get()->getResultArray() as $role) {
            $access = json_decode((string) ($role['access'] ?? ''), true);
            if (! is_array($access) || empty($access)) {
                continue;
            }
            $changed = false;
            foreach ($keys as $module => $actions) {
                foreach ($actions as $a) {
                    if (! isset($access[$module][$a])) {
                        $access[$module][$a] = true;
                        $changed = true;
                    }
                }
            }
            if ($changed) {
                $this->db->table('roles')->where('id', $role['id'])->update(['access' => json_encode($access)]);
            }
        }
    }

    public function down()
    {
        foreach (['companies' => ['branch', 'account_type', 'haulier_code', 'mother_company_roc', 'gov_agent', 'created_by'],
            'sub_companies' => ['prefix'], 'users' => ['user_type', 'country', 'passport_no', 'plks_expiry', 'branch', 'remark']] as $t => $cols) {
            foreach ($cols as $c) {
                if ($this->db->tableExists($t) && $this->db->fieldExists($c, $t)) {
                    $this->forge->dropColumn($t, $c);
                }
            }
        }
        // Role access keys are left in place: removing them would take access away.
    }
}
