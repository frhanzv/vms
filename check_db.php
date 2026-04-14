<?php
$mysqli = new mysqli("localhost", "root", "", "vms");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}
$res = $mysqli->query("SHOW TABLES");
$tables = [];
while ($row = $res->fetch_array()) {
    $tables[] = $row[0];
}

$schema = [];
foreach ($tables as $table) {
    if (strpos($table, 'device') !== false || strpos($table, 'ip') !== false || strpos($table, 'setting') !== false || strpos($table, 'config') !== false) {
        $res = $mysqli->query("DESCRIBE `$table`");
        $cols = [];
        while ($row = $res->fetch_assoc()) {
            $cols[] = $row;
        }
        $schema[$table] = $cols;
    }
}
echo json_encode(["tables" => $tables, "related_schema" => $schema], JSON_PRETTY_PRINT);
