<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SeedDevices extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'seed:devices';
    protected $description = 'Seed device assignments based on lanes';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        // Disable foreign key checks temporarily
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        
        // Try to drop the old foreign key (ignore errors if it doesn't exist)
        try {
            $db->query('ALTER TABLE device_assignments DROP FOREIGN KEY device_assignments_location_id_foreign');
        } catch (\Exception $e) {
            // Ignore if it's already dropped
        }
        
        // Try to add the new foreign key
        try {
            $db->query('ALTER TABLE device_assignments ADD CONSTRAINT device_assignments_lane_id_foreign FOREIGN KEY (location_id) REFERENCES lanes (id) ON DELETE SET NULL ON UPDATE SET NULL');
        } catch (\Exception $e) {
            // Ignore if it already exists
        }
        
        $lanes = $db->query('SELECT id, lane FROM lanes')->getResultArray();
        
        $db->query('TRUNCATE TABLE device_assignments');
        
        $deviceCount = 1;
        foreach ($lanes as $lane) {
            $laneName = strtoupper($lane['lane']);
            
            // Create Check-In
            $deviceIdIn = '008825113' . str_pad($deviceCount, 3, '0', STR_PAD_LEFT);
            $ipIn = '192.168.0.' . (200 + $deviceCount);
            $deviceCount++;
            
            $db->table('device_assignments')->insert([
                'device_id' => $deviceIdIn,
                'ip_address' => $ipIn,
                'status' => 'Offline',
                'registration_status' => 'Registered',
                'location_id' => $lane['id'], // Storing lane_id in location_id
                'type' => 'Check-In',
                'last_heartbeat' => date('Y-m-d H:i:s'),
                'version' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        
            // Create Check-Out
            $deviceIdOut = '008825113' . str_pad($deviceCount, 3, '0', STR_PAD_LEFT);
            $ipOut = '192.168.0.' . (200 + $deviceCount);
            $deviceCount++;
            
            $db->table('device_assignments')->insert([
                'device_id' => $deviceIdOut,
                'ip_address' => $ipOut,
                'status' => 'Offline',
                'registration_status' => 'Registered',
                'location_id' => $lane['id'], // Storing lane_id in location_id
                'type' => 'Check-Out',
                'last_heartbeat' => date('Y-m-d H:i:s'),
                'version' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        CLI::write("Device assignments seeded successfully based on lanes.", 'green');
    }
}
