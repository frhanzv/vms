<?php
$host = '127.0.0.1';
$db   = 'vms';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Drop existing foreign key pointing to lanes
    try {
        $pdo->exec("ALTER TABLE device_assignments DROP FOREIGN KEY device_assignments_location_id_fk_lanes");
        echo "Dropped old foreign key.\n";
    } catch (\Exception $e) {
        echo "Old foreign key might not exist: " . $e->getMessage() . "\n";
    }
    
    // Add new foreign key pointing to sub_locations
    try {
        $pdo->exec("ALTER TABLE device_assignments ADD CONSTRAINT device_assignments_location_id_fk_sub_locations FOREIGN KEY (location_id) REFERENCES sub_locations (id) ON DELETE SET NULL ON UPDATE CASCADE");
        echo "Added new foreign key to sub_locations.\n";
    } catch (\Exception $e) {
        echo "New foreign key might already exist: " . $e->getMessage() . "\n";
    }
    
    // Get sub locations mapping
    $stmt = $pdo->query("SELECT id, name FROM sub_locations");
    $subLocs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $subLocMap = [];
    foreach ($subLocs as $sl) {
        $subLocMap[$sl['name']] = $sl['id'];
    }

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

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE device_assignments");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    $insert = $pdo->prepare("INSERT INTO device_assignments (device_id, ip_address, status, registration_status, location_id, type, created_at, updated_at) VALUES (?, ?, 'Offline', 'Registered', ?, ?, NOW(), NOW())");

    foreach ($devices as $dev) {
        if (!isset($subLocMap[$dev[2]])) {
            echo "Warning: Sub Location '{$dev[2]}' not found!\n";
            continue;
        }
        $locId = $subLocMap[$dev[2]];
        $insert->execute([$dev[0], $dev[1], $locId, $dev[3]]);
    }
    
    echo "Successfully seeded " . count($devices) . " devices!\n";
} catch (\PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
