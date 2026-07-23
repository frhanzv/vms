<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedAdminAndClientSuperAdminRoles extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        $superAdmin = $this->db->table('roles')->where('name', 'Super Admin')->get()->getRowArray();
        $accessJson = $superAdmin['access'] ?? null;

        $toSeed = [
            [
                'name'        => 'Admin',
                'description' => 'Company administrator — access controlled via Role Management',
                'status'      => 'active',
            ],
            [
                'name'        => 'Client Super Admin',
                'description' => 'Top administrator within a client company',
                'status'      => 'active',
            ],
        ];

        foreach ($toSeed as $row) {
            $exists = $this->db->table('roles')->where('name', $row['name'])->countAllResults();
            if ($exists > 0) {
                continue;
            }

            $this->db->table('roles')->insert([
                'name'        => $row['name'],
                'description' => $row['description'],
                'status'      => $row['status'],
                'access'      => $accessJson,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }

    public function down()
    {
        $this->db->table('roles')->whereIn('name', ['Admin', 'Client Super Admin'])->delete();
    }
}
