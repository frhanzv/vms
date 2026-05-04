<?php
$db = new mysqli('localhost', 'root', '', 'vms');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$res = $db->query("SELECT id, code FROM email_templates");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
