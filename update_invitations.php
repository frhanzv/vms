<?php
$c = mysqli_connect('localhost', 'root', '', 'vms');
mysqli_query($c, "UPDATE invitations SET invited_by = 'Demo Super Admin' WHERE invited_by = 'Ahmad Farhan'");
echo "Invitations updated successfully.\n";
