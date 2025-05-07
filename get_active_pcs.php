<?php
include 'db.php';

header('Content-Type: application/json');

$laboratory = isset($_GET['laboratory']) ? $_GET['laboratory'] : '';

if (!empty($laboratory)) {
    // Get PCs currently in use (where time_out is NULL) for the specific laboratory
    $stmt = $conn->prepare("SELECT pc_number FROM sit_in WHERE laboratory = ? AND time_out IS NULL");
    $stmt->bind_param("s", $laboratory);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $active_pcs = [];
    while ($row = $result->fetch_assoc()) {
        // Only add PC numbers that are not NULL and belong to this laboratory
        if ($row['pc_number'] !== NULL) {
            $active_pcs[] = $row['pc_number'];
        }
    }
    
    // Also check approved reservations for the same laboratory if date and time are provided
    if (isset($_GET['date']) && isset($_GET['time'])) {
        $stmt = $conn->prepare("SELECT pc_number FROM reservations 
                              WHERE laboratory = ? 
                              AND date = ? 
                              AND time_in = ? 
                              AND status = 'approved'");
        $stmt->bind_param("sss", $laboratory, $_GET['date'], $_GET['time']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if (!in_array($row['pc_number'], $active_pcs)) {
                $active_pcs[] = $row['pc_number'];
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'active_pcs' => $active_pcs
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Laboratory not specified'
    ]);
}
?>