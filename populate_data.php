<?php
$c = mysqli_connect('localhost', 'root', '', 'vms');

// 1. Add a staff member
mysqli_query($c, "INSERT INTO staff (staff_no, full_name, status) VALUES ('S-001', 'Ahmad Farhan', 'Active')");
$staff_id = mysqli_insert_id($c);

// 2. Update invitations to point to this staff member (using ID instead of 'S-001' string if that's what the query expects)
// Wait, my query joined on staff.id = invitations.staff_id. 
// But invitations.staff_id currently has 'S-001'.
// I should update invitations.staff_id to be the numeric ID or change the query.
// Actually, it's better to update invitations.staff_id to the numeric ID for proper relational integrity.
mysqli_query($c, "UPDATE invitations SET staff_id = '$staff_id' WHERE staff_id = 'S-001'");

// 3. Update security_alerts with invitation_id and visitor_name to match existing invitations
// Alert 1 -> Winson (Invitation 8)
mysqli_query($c, "UPDATE security_alerts SET invitation_id = 8, visitor_name = 'Winson' WHERE id = 1");
// Alert 2 -> Alen (Invitation 9)
mysqli_query($c, "UPDATE security_alerts SET invitation_id = 9, visitor_name = 'Alen' WHERE id = 2");
// Alert 3 -> Benny (Invitation 10)
mysqli_query($c, "UPDATE security_alerts SET invitation_id = 10, visitor_name = 'Benny' WHERE id = 3");
// Alert 4 -> Chloe (Invitation 13)
mysqli_query($c, "UPDATE security_alerts SET invitation_id = 13, visitor_name = 'Chloe' WHERE id = 4");
// Alert 5 -> Ahmad Farhan (Invitation 3)
mysqli_query($c, "UPDATE security_alerts SET invitation_id = 3, visitor_name = 'WAN AHMAD FARHAN BIN WAN KAR MIZI' WHERE id = 5");

echo "Data populated successfully.\n";
