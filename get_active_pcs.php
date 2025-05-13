<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if (!isset($_GET['laboratory']) || !isset($_GET['date']) || !isset($_GET['time'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$laboratory = $_GET['laboratory'];
$date = $_GET['date'];
$time = $_GET['time'];

try {
    $active_pcs = [];
    
    // Check PC reservations for the selected date and time
    $stmt = $conn->prepare("SELECT pc_number FROM reservations WHERE laboratory = ? AND date = ? AND time_in = ? AND status IN ('pending', 'approved')");
    $stmt->bind_param("sss", $laboratory, $date, $time);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $active_pcs[] = $row['pc_number'];
    }
    
    // Check currently active PCs (where time_out is NULL)
    $stmt = $conn->prepare("SELECT pc_number FROM sit_in WHERE laboratory = ? AND time_out IS NULL");
    $stmt->bind_param("s", $laboratory);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        if (!in_array($row['pc_number'], $active_pcs)) {
            $active_pcs[] = $row['pc_number'];
        }
    }
    
    // Check PCs marked as unavailable in pc_status table
    $stmt = $conn->prepare("SELECT pc_number FROM pc_status WHERE laboratory = ? AND status = 'unavailable'");
    $stmt->bind_param("s", $laboratory);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        if (!in_array($row['pc_number'], $active_pcs)) {
            $active_pcs[] = $row['pc_number'];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'active_pcs' => $active_pcs]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>