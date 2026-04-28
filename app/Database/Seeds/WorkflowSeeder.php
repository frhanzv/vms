<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'step_name'  => 'Visitor Registration',
                'step_key'   => 'registration',
                'step_order' => 1,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'step_name'  => 'Scan MYKAD',
                'step_key'   => 'scan_mykad',
                'step_order' => 2,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'step_name'  => 'Take Photo / FR',
                'step_key'   => 'take_photo',
                'step_order' => 3,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'step_name'  => 'Video',
                'step_key'   => 'video',
                'step_order' => 4,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'step_name'  => 'Approval',
                'step_key'   => 'approval',
                'step_order' => 5,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'step_name'  => 'Receive QR',
                'step_key'   => 'receive_qr',
                'step_order' => 6,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('workflows')->insertBatch($data);
    }
}
