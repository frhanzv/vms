<?php
$db = new \mysqli('localhost', 'root', '', 'vms');
$db->query("UPDATE invitations SET registration_source = 'Invitation' WHERE id = 9");
echo "Done";
