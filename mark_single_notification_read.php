<?php
session_start();
include 'db.php';

// Verify admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get data from request
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['notification_id'])) {
    $notification_id = $data['notification_id'];
    
    // Mark the specific notification as read
    $query = "UPDATE notifications 
              SET IS_READ = 1 
              WHERE NOTIF_ID = ? 
              AND ADMIN_NOTIFICATION = 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $notification_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating notification']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Notification ID not provided']);
}

$conn->close();
?>