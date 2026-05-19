<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateWorkflowDefaults extends Migration
{
    public function up()
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

    public function down()
    {
        $this->db->table('workflows')->truncate();
        
        $defaults = [
            ['step_name' => 'Visitor Registration', 'step_key' => 'registration', 'step_order' => 1, 'is_active' => 1],
            ['step_name' => 'Scan MYKAD',          'step_key' => 'scan_mykad',   'step_order' => 2, 'is_active' => 1],
            ['step_name' => 'Take Photo / FR',     'step_key' => 'take_photo',   'step_order' => 3, 'is_active' => 1],
            ['step_name' => 'Video',               'step_key' => 'video',        'step_order' => 4, 'is_active' => 1],
            ['step_name' => 'Approval',            'step_key' => 'approval',     'step_order' => 5, 'is_active' => 1],
            ['step_name' => 'Receive QR',          'step_key' => 'receive_qr',   'step_order' => 6, 'is_active' => 1],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($defaults as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        $this->db->table('workflows')->insertBatch($defaults);
    }
}
