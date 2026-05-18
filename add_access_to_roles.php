<?php
require 'public/index.php';

$db = \Config\Database::connect();
$forge = \Config\Database::forge();

$fields = [
    'access' => [
        'type' => 'TEXT',
        'null' => true,
    ],
];

if (!$db->fieldExists('access', 'roles')) {
    $forge->addColumn('roles', $fields);
    echo "Added access column to roles table.\n";
} else {
    echo "access column already exists in roles table.\n";
}
