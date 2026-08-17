<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the GXO client.
 * Must run before UserSeeder so gxoadmin can reference the company by ID.
 * Idempotent: safe to run multiple times.
 */
class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $clients = [
            ['name' => 'GXO', 'status' => 'active'],
        ];

        foreach ($clients as $client) {
            $existing = $this->db->table('clients')
                ->where('name', $client['name'])
                ->get()
                ->getRowArray();

            if (!$existing) {
                $this->db->table('clients')->insert(array_merge($client, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }
}
