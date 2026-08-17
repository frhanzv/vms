<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Restores the ISK facility reference data used by the upstream VMS project.
 * Idempotent: existing records are updated by their natural identifiers.
 */
class FacilityDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

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

        foreach (array_unique(array_values($subLocations)) as $locationAccess) {
            $existing = $this->db->table('locations')
                ->where('location_access', $locationAccess)
                ->get()->getRowArray();
            $data = [
                'branch'          => 'ISK',
                'location_access' => $locationAccess,
                'status'          => 'active',
                'updated_at'      => $now,
            ];
            if ($existing) {
                $this->db->table('locations')->where('id', $existing['id'])->update($data);
            } else {
                $data['created_at'] = $now;
                $this->db->table('locations')->insert($data);
            }
        }

        $locationRows = $this->db->table('locations')
            ->select('id, location_access')->get()->getResultArray();
        $locationIds = array_column($locationRows, 'id', 'location_access');

        foreach ($subLocations as $name => $parentAccess) {
            $existing = $this->db->table('sub_locations')->where('name', $name)->get()->getRowArray();
            $data = [
                'location_id' => $locationIds[$parentAccess],
                'status'      => 'active',
                'updated_at'  => $now,
            ];
            if ($existing) {
                $this->db->table('sub_locations')->where('id', $existing['id'])->update($data);
            } else {
                $data += ['name' => $name, 'created_at' => $now];
                $this->db->table('sub_locations')->insert($data);
            }
        }

        $laneParents = $subLocations + [
            'HRN ADMIN ENTERENCE' => 'QA ROOM IN',
            'TURNSTILE'           => 'TOILET IN',
        ];
        foreach ($laneParents as $lane => $parentAccess) {
            $existing = $this->db->table('lanes')->where('lane', $lane)->get()->getRowArray();
            $data = [
                'location_id' => $locationIds[$parentAccess],
                'slip_print'  => 'enabled',
                'in_bound'    => 'yes',
                'out_bound'   => $lane === 'TURNSTILE' ? 'yes' : 'no',
                'status'      => 'active',
                'updated_at'  => $now,
            ];
            if ($existing) {
                $this->db->table('lanes')->where('id', $existing['id'])->update($data);
            } else {
                $data += ['lane' => $lane, 'created_at' => $now];
                $this->db->table('lanes')->insert($data);
            }
        }

        $subLocationRows = $this->db->table('sub_locations')
            ->select('id, name')->get()->getResultArray();
        $subLocationIds = array_column($subLocationRows, 'id', 'name');

        $devices = [
            ['008825113521', '192.168.0.250', '3. HR & ADMIN ENTRANCE', 'Check-In'],
            ['008825113511', '192.168.0.249', '8. POLISHING ROOM', 'Check-Out'],
            ['008825113503', '192.168.0.217', '5. TOOL ROOM', 'Check-In'],
            ['008825113517', '192.168.0.228', '7. PRODUCTION EMPLOYEE ENTRANCE', 'Check-In'],
            ['008825113518', '192.168.0.229', '5. TOOL ROOM', 'Check-Out'],
            ['008825113520', '192.168.0.231', '10. 10K CLEANROOM', 'Check-In'],
            ['008825113500', '192.168.0.230', '1. VISITOR STAIRCASE', 'Check-In'],
            ['008825113514', '192.168.0.225', '10. 10K CLEANROOM', 'Check-Out'],
            ['008825113523', '192.168.0.234', '11. 1K CLEANROOM', 'Check-In'],
            ['008825113522', '192.168.0.233', '2. OPERATION OFFICE STAIRCASE', 'Check-In'],
            ['008825113505', '192.168.0.219', '6. CMM ROOM', 'Check-Out'],
            ['008825113502', '192.168.0.216', '11. 1K CLEANROOM', 'Check-Out'],
            ['008825113501', '192.168.0.215', '1. VISITOR STAIRCASE', 'Check-Out'],
            ['008825113526', '192.168.0.247', '9. ULTRASONIC ROOM', 'Check-Out'],
            ['008825113515', '192.168.0.226', '6. CMM ROOM', 'Check-In'],
            ['008825113510', '192.168.0.221', '2. OPERATION OFFICE STAIRCASE', 'Check-Out'],
            ['008825113525', '192.168.0.244', '7. PRODUCTION EMPLOYEE ENTRANCE', 'Check-Out'],
            ['008825113527', '192.168.0.238', '8. POLISHING ROOM', 'Check-In'],
            ['008825113528', '192.168.0.239', '12. ROBOTIC WELDING ROOM', 'Check-In'],
            ['008825113504', '192.168.0.218', '3. HR & ADMIN ENTRANCE', 'Check-Out'],
            ['008825113516', '192.168.0.248', '12. ROBOTIC WELDING ROOM', 'Check-Out'],
            ['008825113513', '192.168.0.224', '4. VISITOR ENTRANCE TO PRODUCTION', 'Check-In'],
            ['008825113524', '192.168.0.235', '4. VISITOR ENTRANCE TO PRODUCTION', 'Check-Out'],
            ['008825038133', '192.168.0.223', '9. ULTRASONIC ROOM', 'Check-In'],
            ['008825113519', '192.168.0.227', '13.TURNSTILE', 'Check-In'],
            ['008825113529', '192.168.0.241', '13.TURNSTILE', 'Check-Out'],
        ];

        foreach ($devices as [$deviceId, $ipAddress, $subLocation, $type]) {
            $existing = $this->db->table('device_assignments')
                ->where('device_id', $deviceId)->get()->getRowArray();
            $data = [
                'ip_address'          => $ipAddress,
                'status'              => 'Offline',
                'registration_status' => 'Registered',
                'location_id'         => $subLocationIds[$subLocation],
                'type'                => $type,
                'updated_at'          => $now,
            ];
            if ($existing) {
                $this->db->table('device_assignments')->where('id', $existing['id'])->update($data);
            } else {
                $data += ['device_id' => $deviceId, 'created_at' => $now];
                $this->db->table('device_assignments')->insert($data);
            }
        }

        echo '  FacilityDataSeeder: ' . count($locationIds) . ' locations, '
            . count($subLocations) . ' sub-locations, ' . count($laneParents)
            . ' lanes and ' . count($devices) . " devices available.\n";
    }
}
