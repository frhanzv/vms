<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the GXO client company.
 * Must run before UserSeeder so gxoadmin can reference the company by ID.
 * Idempotent: safe to run multiple times.
 */
class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $companies = [
            ['name' => 'GXO', 'status' => 'active'],
        ];

        foreach ($companies as $company) {
            $existing = $this->db->table('companies')
                ->where('name', $company['name'])
                ->get()
                ->getRowArray();

            if (!$existing) {
                $this->db->table('companies')->insert(array_merge($company, [
                    'created_at' => $now,
                    'updated_at' => $now,
                    'version'    => 1,
                ]));
            }
        }
    }
}
