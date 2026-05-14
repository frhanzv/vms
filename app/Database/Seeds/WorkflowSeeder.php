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
                'step_name'  => 'Security Briefing',
                'step_key'   => 'security_briefing',
                'step_order' => 2,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'step_name'  => 'Facial Verification',
                'step_key'   => 'facial_verification',
                'step_order' => 3,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'step_name'  => 'Approval',
                'step_key'   => 'approval',
                'step_order' => 4,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'step_name'  => 'Receive QR',
                'step_key'   => 'receive_qr',
                'step_order' => 5,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'step_name'  => 'Completion',
                'step_key'   => 'completion',
                'step_order' => 6,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Clear existing workflows first
        $this->db->table('workflows')->truncate();
        
        $this->db->table('workflows')->insertBatch($data);
    }
}
