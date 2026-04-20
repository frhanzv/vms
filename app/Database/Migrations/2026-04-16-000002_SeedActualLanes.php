<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedActualLanes extends Migration
{
    public function up()
    {
        // Remove old sample lanes (disable FK checks to allow truncate)
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->table('lanes')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        $now = date('Y-m-d H:i:s');

        $doors = [
            '1. VISITOR STAIRCASE',
            '2. OPERATION OFFICE STAIRCASE',
            '3. HR & ADMIN ENTRANCE',
            '4. VISITOR ENTRANCE TO PRODUCTION',
            '5. TOOL ROOM',
            '6. CMM ROOM',
            '7. PRODUCTION EMPLOYEE ENTRANCE',
            '8. POLISHING ROOM',
            '9. ULTRASONIC ROOM',
            '10. 10K CLEANROOM',
            '11. 1K CLEANROOM',
            '12. ROBOTIC WELDING ROOM',
            '13. TURNSTILE',
            'HRN ADMIN ENTERENCE',
            'TURNSTILE',
        ];

        $data = [];
        foreach ($doors as $door) {
            $data[] = [
                'lane'        => $door,
                'location_id' => 1,
                'slip_print'  => 'enabled',
                'in_bound'    => 'yes',
                'out_bound'   => 'no',
                'status'      => 'active',
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        $this->db->table('lanes')->insertBatch($data);
    }

    public function down()
    {
        // No rollback — original sample data is not worth restoring
    }
}
