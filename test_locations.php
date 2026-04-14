<?php
require 'public/index.php';
$db = \Config\Database::connect();
$query = $db->query('SELECT id, branch, location_access, status FROM locations');
$results = $query->getResultArray();
foreach($results as $row) {
    echo "ID: " . $row['id'] . ", Branch: " . $row['branch'] . ", Access: " . $row['location_access'] . ", Status: " . $row['status'] . "\n";
}
