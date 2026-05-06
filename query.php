<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'vms');
$res = $mysqli->query('SELECT id, logo_url FROM email_templates');
while($row = $res->fetch_assoc()){
    echo $row['id'] . ': ' . $row['logo_url'] . PHP_EOL;
}
