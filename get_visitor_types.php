<?php
$db = new \mysqli('localhost', 'root', '', 'vms');
$result = $db->query("SELECT id, status, check_in_time, created_at FROM invitations LIMIT 10");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo $db->error;
}
