<?php
$db = new mysqli('127.0.0.1', 'root', '', 'vms');
$res = $db->query('SELECT * FROM lanes LIMIT 5');
while($r=$res->fetch_assoc()) echo json_encode($r)."\n";
