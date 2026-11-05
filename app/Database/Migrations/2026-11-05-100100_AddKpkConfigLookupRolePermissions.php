<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Role Management gets boxes under Config for the new KPK lists (Payment Type, Branch Office,
 * Release Reason, Operating Hours, Bank Name, Sub Business Type). Roles that already manage
 * License Class, Vehicle Types or Reject Reason get them so nobody loses access they could use before.
 * A box already saved either way is left alone; roles with an empty access JSON (full access) are untouched.
 */
class AddKpkConfigLookupRolePermissions extends Migration
{
    private const KEYS = ['payment_type', 'branch_office', 'release_reason', 'operating_hours', 'bank_name', 'sub_business_type'];

    public function up()
    {
        if (! $this->db->tableExists('roles') || ! $this->db->fieldExists('access', 'roles')) {
            return;
        }
        foreach ($this->db->table('roles')->select('id, access')->get()->getResultArray() as $role) {
            $access = json_decode((string) ($role['access'] ?? ''), true);
            if (! is_array($access) || empty($access)) {
                continue;
            }
            $config = (array) ($access['config'] ?? []);
            if (empty($config['license_class']) && empty($config['vehicle_type']) && empty($config['reject_reason'])) {
                continue;
            }
            $changed = false;
            foreach (self::KEYS as $k) {
                if (! array_key_exists($k, $config)) {
                    $access['config'][$k] = true;
                    $changed = true;
                }
            }
            if ($changed) {
                $this->db->table('roles')->where('id', $role['id'])->update(['access' => json_encode($access)]);
            }
        }
    }

    public function down()
    {
        // Left in place: removing the keys would take access away from roles.
    }
}
