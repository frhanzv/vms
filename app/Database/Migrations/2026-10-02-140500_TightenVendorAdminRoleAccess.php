<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Rewrites the vendor_admin role's access JSON to only vendor_pass_list
 * view + edit (no dashboard). Needed because an earlier build of
 * 2026-10-02-140100 also granted the staff dashboard menu, and editing a
 * migration that has already run does nothing.
 */
class TightenVendorAdminRoleAccess extends Migration
{
    public function up()
    {
        $access = ['vendor_pass_list' => ['view' => true, 'edit' => true]];

        $this->db->table('roles')
            ->where('name', 'vendor_admin')
            ->update([
                'access'     => json_encode($access),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    public function down()
    {
        // Nothing to restore — the previous access set was a superset.
    }
}
