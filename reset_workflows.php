<?php
$host = 'localhost';
$db   = 'vms';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     
     // Empty the table
     $pdo->exec('TRUNCATE TABLE workflows;');
     
     // Insert exactly the 6 requested workflows
     $data = [
         ['Visitor Registration', 'registration', 1],
         ['Security Briefing', 'security_briefing', 2],
         ['Facial Verification', 'facial_verification', 3],
         ['Approval', 'approval', 4],
         ['Receive QR', 'receive_qr', 5],
         ['Completion', 'completion', 6]
     ];
     
     $stmt = $pdo->prepare('INSERT INTO workflows (step_name, step_key, step_order, is_active, created_at, updated_at) VALUES (?, ?, ?, 1, NOW(), NOW())');
     foreach ($data as $row) {
         $stmt->execute([$row[0], $row[1], $row[2]]);
     }
     
     echo "Database updated successfully with the 6 workflows.\n";
} catch (\PDOException $e) {
     echo "Error: " . $e->getMessage() . "\n";
}
