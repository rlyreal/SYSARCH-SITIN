<?php
include 'db.php';

$result = $conn->query("SELECT * FROM announcements ORDER BY date DESC");
$announcements = [];

while ($row = $result->fetch_assoc()) {
    $announcements[] = $row;
}

echo json_encode(["status" => "success", "announcements" => $announcements]);
?>
