<?php
$host = '127.0.0.1';
$db   = 'vms';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SHOW CREATE TABLE device_assignments");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (\PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
