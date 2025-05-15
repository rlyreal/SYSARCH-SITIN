<?php

session_start();
include 'db.php';

// Verify admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Mark all reservation notifications as read
$query = "UPDATE notifications 
          SET IS_READ = 1 
          WHERE ADMIN_NOTIFICATION = 1 
          AND RESERVATION_ID IS NOT NULL 
          AND IS_READ = 0";

if ($conn->query($query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating notifications']);
}
?>