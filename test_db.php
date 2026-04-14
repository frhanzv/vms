<?php
$conn = new mysqli("localhost", "root", "", "vms");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM locations";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"]. " - Branch: " . $row["branch"]. " - Location: " . $row["location_access"]. " - Status: " . $row["status"]. "\n";
    }
} else {
    echo "0 results";
}
$conn->close();
?>
