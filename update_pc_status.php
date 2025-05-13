<?php

session_start();
include 'db.php';

// Restrict access to admins
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get request data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

$lab_id = $data['lab_id'] ?? null;
$pc_number = $data['pc_number'] ?? null;
$action = $data['action'] ?? null;
$session_id = $data['session_id'] ?? null;

if (!$lab_id || !$pc_number || !$action) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Handle different actions
    if ($action === 'make-unavailable') {
        // Check if PC is already in use
        $stmt = $conn->prepare("SELECT id FROM sit_in WHERE laboratory = ? AND pc_number = ? AND time_out IS NULL");
        $stmt->bind_param("si", $lab_id, $pc_number);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $conn->rollback();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cannot mark PC as unavailable - it is currently in use']);
            exit;
        }
        
        // Mark PC as unavailable by creating a dummy record
        $stmt = $conn->prepare("INSERT INTO pc_status (laboratory, pc_number, status, updated_by, updated_at) VALUES (?, ?, 'unavailable', ?, NOW())");
        $stmt->bind_param("sii", $lab_id, $pc_number, $_SESSION['admin_id']);
        $stmt->execute();
        
        $message = "PC $pc_number has been marked as unavailable";
    } 
    else if ($action === 'make-available') {
        if ($session_id) {
            // End active session if exists
            $current_time = date('H:i:s');
            $stmt = $conn->prepare("UPDATE sit_in SET time_out = ? WHERE id = ?");
            $stmt->bind_param("si", $current_time, $session_id);
            $stmt->execute();
            
            if ($stmt->affected_rows === 0) {
                $conn->rollback();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to end session']);
                exit;
            }
        }
        
        // Mark PC as available by updating status or deleting dummy record
        $stmt = $conn->prepare("DELETE FROM pc_status WHERE laboratory = ? AND pc_number = ?");
        $stmt->bind_param("si", $lab_id, $pc_number);
        $stmt->execute();
        
        $message = $session_id 
            ? "Session has been ended and PC $pc_number is now available" 
            : "PC $pc_number has been marked as available";
    }
    
    // Commit transaction
    $conn->commit();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => $message]);
    
} catch (Exception $e) {
    $conn->rollback();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>