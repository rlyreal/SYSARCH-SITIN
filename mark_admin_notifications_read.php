<?php

session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Mark viewed notifications as read
$query = "UPDATE notifications SET IS_READ = 1 WHERE IS_READ = 0 AND ADMIN_NOTIFICATION = 1";
if ($conn->query($query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$conn->close();
?>