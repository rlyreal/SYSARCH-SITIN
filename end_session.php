<?php
// filepath: c:\xampp\htdocs\SYSARCH-SITIN\end_session.php
session_start();
include 'db.php';

// Restrict access to admins
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if (!isset($_POST['session_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Session ID is required']);
    exit;
}

$session_id = $_POST['session_id'];

try {
    // Get the current time
    $current_time = date('H:i:s');
    
    // Update the sit_in record to end the session
    $stmt = $conn->prepare("UPDATE sit_in SET time_out = ? WHERE id = ?");
    $stmt->bind_param("ss", $current_time, $session_id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Session not found or already ended']);
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>