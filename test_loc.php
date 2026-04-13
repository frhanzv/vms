<?php
$mysqli = new mysqli("localhost", "root", "", "vms");
$res = $mysqli->query("SELECT * FROM locations LIMIT 3");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
