<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedWorkflowDefaults extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('workflows')) {
            return;
        }

        $defaults = [
            ['step_name' => 'Visitor Registration', 'step_key' => 'registration', 'step_order' => 1, 'is_active' => 1],
            ['step_name' => 'Scan MYKAD',          'step_key' => 'scan_mykad',   'step_order' => 2, 'is_active' => 1],
            ['step_name' => 'Take Photo / FR',     'step_key' => 'take_photo',   'step_order' => 3, 'is_active' => 1],
            ['step_name' => 'Video',               'step_key' => 'video',        'step_order' => 4, 'is_active' => 1],
            ['step_name' => 'Approval',            'step_key' => 'approval',     'step_order' => 5, 'is_active' => 1],
            ['step_name' => 'Receive QR',          'step_key' => 'receive_qr',   'step_order' => 6, 'is_active' => 1],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($defaults as $row) {
            $exists = $this->db->table('workflows')
                ->where('step_key', $row['step_key'])
                ->countAllResults() > 0;

            if ($exists) {
                continue;
            }

            $row['client_id'] = null;
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            $this->db->table('workflows')->insert($row);
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('workflows')) {
            return;
        }

        $keys = ['registration', 'scan_mykad', 'take_photo', 'video', 'approval', 'receive_qr'];
        $this->db->table('workflows')->whereIn('step_key', $keys)->delete();
    }
}
