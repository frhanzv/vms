<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';
$db = \Config\Database::connect();
$rows = $db->table('workflows')->get()->getResultArray();
print_r($rows);
