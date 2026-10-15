<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Role Management gets a "License Class" box under Config. Roles that already manage Vehicle Types,
 * Page Settings or Dynamic Form Fields are given it so nobody loses access they could use before.
 * A box already saved either way is left alone; roles with an empty access JSON (full access) are untouched.
 */
class AddLicenseClassRolePermission extends Migration
{
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
            if (array_key_exists('license_class', $config)) {
                continue;
            }
            if (! empty($config['vehicle_type']) || ! empty($config['page_settings']) || ! empty($config['dynamic_form_fields'])) {
                $access['config']['license_class'] = true;
                $this->db->table('roles')->where('id', $role['id'])->update(['access' => json_encode($access)]);
            }
        }
    }

    public function down()
    {
        // Left in place: removing the key would take access away from roles.
    }
}
