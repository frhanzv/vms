<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * The role self-registered vendor company accounts get (Auth::doRegister()).
 * Scoped tightly on purpose: a vendor company's own user manages only their
 * OWN company's vendor pass requests (view + edit, so Request/Import/
 * Template all work) — never approve/reject (that's KPK staff reviewing the
 * request), never delete, and no access to any other module. The explicit
 * 'access' JSON below is what makes this restrictive: an empty/unset
 * 'access' column on a role defaults to full access (see has_access() in
 * app/Helpers/access_helper.php), so this must stay fully populated.
 */
class SeedVendorAdminRole extends Migration
{
    private const ROLE_NAME = 'vendor_admin';

    public function up()
    {
        $existing = $this->db->table('roles')->where('name', self::ROLE_NAME)->get()->getRowArray();
        if ($existing) {
            return;
        }

        $access = [
            'dashboard'        => ['main_menu' => true],
            'vendor_pass_list' => ['view' => true, 'edit' => true],
        ];

        $this->db->table('roles')->insert([
            'name'        => self::ROLE_NAME,
            'description' => 'Vendor company administrator — self-registered via the public company registration page. Can view and submit their own company\'s vendor pass requests only.',
            'status'      => 'active',
            'access'      => json_encode($access),
            'version'     => 1,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->db->table('roles')->where('name', self::ROLE_NAME)->delete();
    }
}
