<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BlacklistReasonSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'reason' => 'Fraudulent Documents',
                'type'   => 'Permanent',
                'status' => 'Active',
            ],
            [
                'reason' => 'Non-Payment of Dues',
                'type'   => 'Temporary',
                'status' => 'Active',
            ],
            [
                'reason' => 'Criminal Record',
                'type'   => 'Permanent',
                'status' => 'Active',
            ],
            [
                'reason' => 'Repeated Policy Violations',
                'type'   => 'Temporary',
                'status' => 'Active',
            ],
            [
                'reason' => 'License Revoked',
                'type'   => 'Permanent',
                'status' => 'Active',
            ],
            [
                'reason' => 'Suspicious Activity',
                'type'   => 'Temporary',
                'status' => 'Inactive',
            ],
        ];

        $this->db->table('blacklistreason')->insertBatch($data);
    }
}