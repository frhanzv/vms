<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'vms');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$res = $mysqli->query("SELECT id, logo_url FROM email_templates WHERE logo_url LIKE 'http://localhost/vms/%'");
while($row = $res->fetch_assoc()){
    $relPath = str_replace('http://localhost/vms/', '', $row['logo_url']);
    // Also remove the beginning slash if there's any
    $relPath = ltrim($relPath, '/');
    $mysqli->query("UPDATE email_templates SET logo_url='$relPath' WHERE id={$row['id']}");
    echo "Updated ID {$row['id']} to $relPath\n";
}

$res = $mysqli->query("SELECT id, logo_url FROM email_templates WHERE logo_url LIKE 'http://192.168.100.243:8080/vms/%'");
while($row = $res->fetch_assoc()){
    $relPath = str_replace('http://192.168.100.243:8080/vms/', '', $row['logo_url']);
    $relPath = ltrim($relPath, '/');
    $mysqli->query("UPDATE email_templates SET logo_url='$relPath' WHERE id={$row['id']}");
    echo "Updated ID {$row['id']} to $relPath\n";
}
echo "Done.";
