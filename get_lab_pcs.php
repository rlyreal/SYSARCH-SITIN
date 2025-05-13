<?php

session_start();
include 'db.php';

// Restrict access to admins
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

if (!isset($_GET['lab'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Laboratory ID is required']);
    exit;
}

$lab = $_GET['lab'];

// Get active PC usage from sit_in table where time_out is NULL
$stmt = $conn->prepare("SELECT id, idno, fullname, pc_number, purpose, 
                       DATE_FORMAT(time_in, '%h:%i %p') as time_in, 'in-use' as status
                       FROM sit_in 
                       WHERE laboratory = ? AND time_out IS NULL");
$stmt->bind_param("s", $lab);
$stmt->execute();
$result = $stmt->get_result();

$pcs = [];
while ($row = $result->fetch_assoc()) {
    $pcs[] = $row;
}

// Get unavailable PCs from pc_status table
$stmt = $conn->prepare("SELECT NULL as id, NULL as idno, NULL as fullname, 
                      pc_number, NULL as purpose, NULL as time_in, 'unavailable' as status
                      FROM pc_status 
                      WHERE laboratory = ? AND status = 'unavailable'");
$stmt->bind_param("s", $lab);
$stmt->execute();
$unavailable_result = $stmt->get_result();

while ($row = $unavailable_result->fetch_assoc()) {
    // Only add if not already in use by a student
    if (!array_filter($pcs, function($pc) use ($row) {
        return $pc['pc_number'] == $row['pc_number'];
    })) {
        $pcs[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($pcs);
?>