<?php

session_start();
include 'db.php';

// Verify admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Mark all admin notifications as read
$query = "UPDATE notifications 
          SET IS_READ = 1 
          WHERE ADMIN_NOTIFICATION = 1 
          AND IS_READ = 0";

if ($conn->query($query)) {
    $affected = $conn->affected_rows;
    echo json_encode([
        'success' => true, 
        'message' => $affected . ' notifications marked as read',
        'affected_rows' => $affected
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Error updating notifications: ' . $conn->error
    ]);
}
?>