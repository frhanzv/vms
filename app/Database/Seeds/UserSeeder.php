<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Demo accounts for local / QA. Idempotent: safe to run multiple times.
 *
 * admin    → superadmin (full config + dashboard)
 * host     → host (invitations)
 * officer  → officer (workflow, blacklist view, reports)
 * approver → admin (request approvals; redirects to /requests)
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $rows = [
            [
                'username'   => 'admin',
                'email'      => 'admin@safeg.com',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'full_name'  => 'Demo Super Admin',
                'role'       => 'superadmin',
                'is_active'  => 1,
            ],
            [
                'username'   => 'host',
                'email'      => 'host@safeg.com',
                'password'   => password_hash('host123', PASSWORD_DEFAULT),
                'full_name'  => 'Demo Host',
                'role'       => 'host',
                'is_active'  => 1,
            ],
            [
                'username'   => 'officer',
                'email'      => 'officer@safeg.com',
                'password'   => password_hash('officer123', PASSWORD_DEFAULT),
                'full_name'  => 'Demo Security Officer',
                'role'       => 'officer',
                'is_active'  => 1,
            ],
            [
                'username'   => 'approver',
                'email'      => 'approver@safeg.com',
                'password'   => password_hash('approver123', PASSWORD_DEFAULT),
                'full_name'  => 'Demo Site Admin',
                'role'       => 'admin',
                'is_active'  => 1,
            ],
        ];

        foreach ($rows as $row) {
            $table  = $this->db->table('users');
            $existing = $table->where('username', $row['username'])->get()->getRowArray();

            $payload = array_merge($row, ['updated_at' => $now]);

            if ($existing) {
                $payload['created_at'] = $existing['created_at'] ?? $now;
                $this->db->table('users')->where('id', (int) $existing['id'])->update($payload);
            } else {
                $payload['created_at'] = $now;
                if ($this->db->fieldExists('version', 'users')) {
                    $payload['version'] = 1;
                }
                $this->db->table('users')->insert($payload);
            }
        }
    }
}
