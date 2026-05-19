<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Seeds the sub_locations table with the real ISK facility sub-location
 * entries (doors/rooms that are sub-divisions of a Location Access point).
 */
class SeedSubLocationData extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        // Sub location name => parent location_access name
        $subLocations = [
            '1. VISITOR STAIRCASE'              => 'TOOLS ROOM IN',
            '2. OPERATION OFFICE STAIRCASE'     => 'PACKAGING AREA IN',
            '3. HR & ADMIN ENTRANCE'            => 'QA ROOM IN',
            '4. VISITOR ENTRANCE TO PRODUCTION' => 'PACKAGING AREA IN',
            '5. TOOL ROOM'                      => 'TOOLS ROOM IN',
            '6. CMM ROOM'                       => 'CMM ROOM IN',
            '7. PRODUCTION EMPLOYEE ENTRANCE'   => 'PRODUCTION 4 IN',
            '8. POLISHING ROOM'                 => 'POLISHING ROOM IN',
            '9. ULTRASONIC ROOM'                => 'ULTRA SONIC ROOM IN',
            '10. 10K CLEANROOM'                 => 'CHANGING ROOM 1 IN',
            '11. 1K CLEANROOM'                  => 'CHANGING ROOM 2 IN',
            '12. ROBOTIC WELDING ROOM'          => 'ROBOTIC WELDING ROOM IN',
            '13.TURNSTILE'                      => 'TOILET IN',
        ];

        // Look up location IDs by name
        $locations = $this->db->table('locations')
            ->select('id, location_access')
            ->whereIn('location_access', array_unique(array_values($subLocations)))
            ->get()
            ->getResultArray();

        $locationMap = array_column($locations, 'id', 'location_access');

        $batch = [];
        foreach ($subLocations as $name => $parentAccess) {
            if (!isset($locationMap[$parentAccess])) {
                continue;
            }
            $batch[] = [
                'name'        => $name,
                'location_id' => $locationMap[$parentAccess],
                'status'      => 'active',
                'version'     => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        if (!empty($batch)) {
            $this->db->table('sub_locations')->insertBatch($batch);
        }
    }

    public function down()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->table('sub_locations')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
