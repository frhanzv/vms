<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RegistrationTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'         => 'Regular',
                'can_print_cp' => 1,
                'status'       => 'Active',
            ],
            [
                'name'         => 'Walk-In',
                'can_print_cp' => 1,
                'status'       => 'Active',
            ],
            [
                'name'         => 'Online',
                'can_print_cp' => 1,
                'status'       => 'Active',
            ],
            [
                'name'         => 'Group',
                'can_print_cp' => 0,
                'status'       => 'Active',
            ],
            [
                'name'         => 'VIP',
                'can_print_cp' => 1,
                'status'       => 'Active',
            ],
            [
                'name'         => 'Complimentary',
                'can_print_cp' => 0,
                'status'       => 'Inactive',
            ],
        ];

        $this->db->table('reg_type')->insertBatch($data);
    }
}