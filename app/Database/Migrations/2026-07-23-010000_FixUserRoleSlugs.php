<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixUserRoleSlugs extends Migration
{
    public function up()
    {
        helper('role');

        $users = $this->db->table('users')->select('id, role, company_id')->get()->getResultArray();

        foreach ($users as $user) {
            $slug = normalize_role_slug($user['role']);
            if ($slug === $user['role']) {
                continue;
            }

            $update = ['role' => $slug];
            if ($slug === 'superadmin') {
                $update['company_id'] = null;
            }

            $this->db->table('users')->where('id', $user['id'])->update($update);
        }
    }

    public function down()
    {
        // Irreversible data normalization.
    }
}
