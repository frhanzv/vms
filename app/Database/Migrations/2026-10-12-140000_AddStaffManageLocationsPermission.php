<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Role Management > Staff Pass List now has the same checkboxes as Vendor Pass
 * List (View, Edit, Delete, Approve, Reject, Manage Locations) plus Print.
 *
 * "Manage Locations" now controls editing a staff pass's Location Access on
 * Card Info, which used to come with "Edit". So nobody loses that, every role
 * that already has Staff Pass List > Edit is given Manage Locations too.
 * Roles with an empty access JSON already have full access and are left alone.
 */
class AddStaffManageLocationsPermission extends Migration
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
            $staff = $access['staff_pass_list'] ?? null;
            if (! is_array($staff) || empty($staff['edit']) || array_key_exists('manage_locations', $staff)) {
                continue;
            }
            $staff['manage_locations']  = true;
            $access['staff_pass_list'] = $staff;
            $this->db->table('roles')->where('id', $role['id'])->update(['access' => json_encode($access)]);
        }
    }

    public function down()
    {
        // Left in place: removing it would take Location Access editing away from roles.
    }
}
