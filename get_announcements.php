<?php
session_start();
include 'db.php';

$result = $conn->query("SELECT * FROM announcements ORDER BY date DESC");
$announcements = [];

while ($row = $result->fetch_assoc()) {
    // Format the date
    $date = new DateTime($row['date']);
    $row['date'] = $date->format('M d, Y h:i A');
    $announcements[] = $row;
}

echo json_encode(["status" => "success", "announcements" => $announcements]);
?>
