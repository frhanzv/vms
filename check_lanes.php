<?php
$mysqli = new mysqli("localhost", "root", "", "vms");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

echo "--- Lanes ---\n";
$res = $mysqli->query("SELECT * FROM lanes");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}

echo "--- Locations ---\n";
$res = $mysqli->query("SELECT * FROM locations");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
