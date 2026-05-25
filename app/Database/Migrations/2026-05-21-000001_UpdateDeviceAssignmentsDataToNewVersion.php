<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateDeviceAssignmentsDataToNewVersion extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('device_assignments') || ! $this->db->tableExists('sub_locations')) {
            return;
        }

        // Disable Foreign Key checks
        $this->db->disableForeignKeyChecks();

        // 1. Truncate device_assignments
        $this->db->table('device_assignments')->truncate();

        // 2. Get sub locations map (name => id)
        $subLocs = $this->db->table('sub_locations')->select('id, name')->get()->getResultArray();
        $subLocMap = [];
        foreach ($subLocs as $sl) {
            $subLocMap[$sl['name']] = $sl['id'];
        }

        // 3. Define the correct 26 devices
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

        $now = date('Y-m-d H:i:s');
        $batch = [];
        $hasVersion = $this->db->fieldExists('version', 'device_assignments');

        foreach ($devices as $dev) {
            $name = $dev[2];
            if (! isset($subLocMap[$name])) {
                log_message('warning', "UpdateDeviceAssignmentsDataToNewVersion: Sub Location '{$name}' not found when seeding!");
                continue;
            }
            $locId = $subLocMap[$name];
            
            $row = [
                'device_id'           => $dev[0],
                'ip_address'          => $dev[1],
                'status'              => 'Offline',
                'registration_status' => 'Registered',
                'location_id'         => $locId,
                'type'                => $dev[3],
                'last_heartbeat'      => null,
                'created_at'          => $now,
                'updated_at'          => $now,
            ];
            if ($hasVersion) {
                $row['version'] = 1;
            }
            $batch[] = $row;
        }

        if (! empty($batch)) {
            $this->db->table('device_assignments')->insertBatch($batch);
        }

        // 4. Clean up any existing foreign key on `location_id`
        $schema = $this->db->database;
        $rows   = $this->db->query(
            'SELECT DISTINCT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE '
            . 'WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? '
            . 'AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$schema, 'device_assignments', 'location_id']
        )->getResultArray();

        foreach ($rows as $row) {
            $name = $row['CONSTRAINT_NAME'] ?? '';
            if ($name === '') {
                continue;
            }
            $safe = str_replace('`', '``', $name);
            try {
                $this->db->query('ALTER TABLE `device_assignments` DROP FOREIGN KEY `' . $safe . '`');
            } catch (\Throwable $e) {
                log_message('debug', 'UpdateDeviceAssignmentsDataToNewVersion drop FK: ' . $e->getMessage());
            }
        }

        // 5. Add the correct foreign key constraint referencing `sub_locations`
        try {
            $this->db->query(
                'ALTER TABLE `device_assignments` '
                . 'ADD CONSTRAINT `device_assignments_location_id_fk_sub_locations` '
                . 'FOREIGN KEY (`location_id`) REFERENCES `sub_locations`(`id`) '
                . 'ON DELETE SET NULL ON UPDATE CASCADE'
            );
        } catch (\Throwable $e) {
            log_message('error', 'UpdateDeviceAssignmentsDataToNewVersion add FK: ' . $e->getMessage());
        }

        // Re-enable Foreign Key checks
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        if (! $this->db->tableExists('device_assignments')) {
            return;
        }

        $this->db->disableForeignKeyChecks();
        try {
            $this->db->query('ALTER TABLE `device_assignments` DROP FOREIGN KEY `device_assignments_location_id_fk_sub_locations`');
        } catch (\Throwable $e) {
            // Ignore
        }
        $this->db->enableForeignKeyChecks();
    }
}
