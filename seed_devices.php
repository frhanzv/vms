<?php
require 'public/index.php';
$db = \Config\Database::connect();

$lanes = $db->query('SELECT id, lane FROM lanes')->getResultArray();

$db->query('TRUNCATE TABLE device_assignments');

$deviceCount = 1;
foreach ($lanes as $lane) {
    $laneName = strtoupper($lane['lane']);
    
    // Skip TURNSTILE and HR & ADMIN ENTRANCE as requested by user
    if (strpos($laneName, 'TURNSTILE') !== false || strpos($laneName, 'ADMIN') !== false) {
        continue;
    }

    // Create Check-In
    $deviceIdIn = '008825113' . str_pad($deviceCount++, 3, '0', STR_PAD_LEFT);
    $ipIn = '192.168.0.' . (200 + $deviceCount);
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
    $deviceIdOut = '008825113' . str_pad($deviceCount++, 3, '0', STR_PAD_LEFT);
    $ipOut = '192.168.0.' . (200 + $deviceCount);
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

echo "Device assignments seeded successfully based on lanes.\n";
