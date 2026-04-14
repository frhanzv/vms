<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'vms');
$res = $mysqli->query('SHOW COLUMNS FROM invitations');
while($r = $res->fetch_assoc()) echo $r['Field'] . "\n";
