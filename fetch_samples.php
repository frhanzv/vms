<?php
$c = mysqli_connect('localhost', 'root', '', 'vms');
echo "STAFF:\n";
$r = mysqli_query($c, 'SELECT id, staff_no, full_name FROM staff');
while($f = mysqli_fetch_assoc($r)) print_r($f);
