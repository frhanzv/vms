<?php
require_once 'vendor/autoload.php';

// Bootstrap CodeIgniter
$pathsConfig = 'app/Config/Paths.php';
require realpath($pathsConfig) ?: $pathsConfig;

$paths = new Config\Paths();
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';
require realpath($bootstrap) ?: $bootstrap;

$app = Config\Services::codeigniter();
$app->initialize();

// Get database connection
$db = \Config\Database::connect();

// Query invitations
$query = $db->query('SELECT * FROM invitations LIMIT 1');
$result = $query->getResultArray();

echo "Invitation Data:\n";
print_r($result);

if (!empty($result)) {
    echo "\n\nChecking specific fields:\n";
    echo "staff_id: " . ($result[0]['staff_id'] ?? 'NULL') . "\n";
    echo "company_visited: " . ($result[0]['company_visited'] ?? 'NULL') . "\n";
    echo "host_contact: " . ($result[0]['host_contact'] ?? 'NULL') . "\n";
}
