<?php
// filepath: c:\xampp\htdocs\SYSARCH-SITIN\get_announcements.php
session_start();
include 'db.php';

// Fetch announcements, sorting by most recent
$result = $conn->query("SELECT id, admin_name, message, date FROM announcements ORDER BY date DESC LIMIT 10");
$announcements = [];

// Check if there are any announcements
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Add each announcement to the array
        $announcements[] = $row;
    }
}

// Set the response header to JSON and output the announcements as JSON
header('Content-Type: application/json');
echo json_encode($announcements);
?>
