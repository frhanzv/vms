<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Seeds pathways, the TURNSTILE lane, and their pivot table entries
 * to match the old ISK system configuration.
 */
class SeedPathwayData extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        // 1. Seed a TURNSTILE lane (yellow badge in old system)
        $toiletLocation = $this->db->table('locations')
            ->where('location_access', 'TOILET IN')
            ->get()->getRowArray();

        $turnstileLocationId = $toiletLocation ? $toiletLocation['id'] : 1;

        $this->db->table('lanes')->insert([
            'lane'        => 'TURNSTILE',
            'location_id' => $turnstileLocationId,
            'slip_print'  => 'enabled',
            'in_bound'    => 'yes',
            'out_bound'   => 'yes',
            'status'      => 'active',
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);
        $turnstileLaneId = $this->db->insertID();

        // 2. Seed the 9 pathways
        $pathways = [
            1 => 'ROUTE FOR CUSTOMER BUSINESS MEET',
            2 => 'ROUTE FOR CUSTOMER BUYOFF',
            3 => 'ROUTE FOR SUPPLIER GOODS',
            4 => 'ROUTE FOR SUPPLIER SERVICE',
            5 => 'ROUTE FOR EXTERNAL STAFF',
            6 => 'ROUTE FOR VVIP',
            7 => 'INTERVIEW',
            8 => 'TRAINING',
            9 => 'ABC',
        ];

        $pathwayIds = [];
        foreach ($pathways as $idx => $name) {
            $this->db->table('pathways')->insert([
                'name'       => $name,
                'status'     => 'active',
                'version'    => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $pathwayIds[$idx] = $this->db->insertID();
        }

        // 3. Build sub_location name -> id map
        $subLocs = $this->db->table('sub_locations')
            ->select('id, name')
            ->get()->getResultArray();
        $slMap = array_column($subLocs, 'id', 'name');

        // 4. Define pathway -> sub_locations (blue badges) with sort order
        $pathwaySubLocations = [
            1 => ['10. 10K CLEANROOM', '11. 1K CLEANROOM', '9. ULTRASONIC ROOM', '8. POLISHING ROOM', '1. VISITOR STAIRCASE', '13.TURNSTILE', '3. HR & ADMIN ENTRANCE', '4. VISITOR ENTRANCE TO PRODUCTION'],
            2 => ['1. VISITOR STAIRCASE', '13.TURNSTILE', '9. ULTRASONIC ROOM'],
            3 => ['12. ROBOTIC WELDING ROOM', '13.TURNSTILE'],
            4 => ['10. 10K CLEANROOM', '11. 1K CLEANROOM', '8. POLISHING ROOM', '9. ULTRASONIC ROOM', '5. TOOL ROOM', '6. CMM ROOM', '13.TURNSTILE', '2. OPERATION OFFICE STAIRCASE', '7. PRODUCTION EMPLOYEE ENTRANCE'],
            5 => ['1. VISITOR STAIRCASE', '10. 10K CLEANROOM', '11. 1K CLEANROOM', '12. ROBOTIC WELDING ROOM', '13.TURNSTILE', '2. OPERATION OFFICE STAIRCASE', '3. HR & ADMIN ENTRANCE', '4. VISITOR ENTRANCE TO PRODUCTION', '5. TOOL ROOM', '6. CMM ROOM', '7. PRODUCTION EMPLOYEE ENTRANCE', '8. POLISHING ROOM'],
            6 => ['13.TURNSTILE', '1. VISITOR STAIRCASE', '2. OPERATION OFFICE STAIRCASE', '3. HR & ADMIN ENTRANCE', '4. VISITOR ENTRANCE TO PRODUCTION'],
            7 => ['13.TURNSTILE', '1. VISITOR STAIRCASE'],
            8 => ['13.TURNSTILE', '1. VISITOR STAIRCASE'],
        ];

        // 5. Define pathway -> lanes (yellow badges) with sort order
        $pathwayLanes = [
            7 => [$turnstileLaneId],
            9 => [$turnstileLaneId],
        ];

        // 6. Insert pathway_sub_locations
        $pslBatch = [];
        foreach ($pathwaySubLocations as $pathIdx => $doors) {
            foreach ($doors as $sortOrder => $slName) {
                if (!isset($slMap[$slName])) {
                    continue;
                }
                $pslBatch[] = [
                    'pathway_id'      => $pathwayIds[$pathIdx],
                    'sub_location_id' => $slMap[$slName],
                    'sort_order'      => $sortOrder,
                ];
            }
        }
        if (!empty($pslBatch)) {
            $this->db->table('pathway_sub_locations')->insertBatch($pslBatch);
        }

        // 7. Insert pathway_lanes
        $plBatch = [];
        foreach ($pathwayLanes as $pathIdx => $laneIds) {
            foreach ($laneIds as $sortOrder => $laneId) {
                $plBatch[] = [
                    'pathway_id' => $pathwayIds[$pathIdx],
                    'lane_id'    => $laneId,
                    'sort_order' => 100 + $sortOrder,
                ];
            }
        }
        if (!empty($plBatch)) {
            $this->db->table('pathway_lanes')->insertBatch($plBatch);
        }
    }

    public function down()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->table('pathway_sub_locations')->truncate();
        $this->db->table('pathway_lanes')->truncate();
        $this->db->table('pathways')->truncate();
        $this->db->table('lanes')->where('lane', 'TURNSTILE')->delete();
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
