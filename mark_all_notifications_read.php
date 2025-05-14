<?php
// filepath: c:\xampp\htdocs\SYSARCH-SITIN\mark_all_notifications_read.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Mark all notifications as read
$stmt = $conn->prepare("UPDATE notifications SET IS_READ = 1 WHERE USER_ID = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update notifications']);
}
?>