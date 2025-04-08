<?php
session_start();
include 'db.php';

if (isset($_POST['id_no'])) {
    $id_no = $_POST['id_no'];
    
    // Update session count to 30 for specific student
    $stmt = $conn->prepare("UPDATE sit_in SET session_count = 30 WHERE idno = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $id_no);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Session count reset successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error resetting session count'
        ]);
    }
    
    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No ID number provided'
    ]);
}

$conn->close();
?>