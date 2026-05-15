<?php
$c = mysqli_connect('localhost', 'root', '', 'vms');
mysqli_query($c, "UPDATE staff SET full_name = 'Demo Super Admin' WHERE full_name = 'Ahmad Farhan'");
echo "Staff updated successfully.\n";
