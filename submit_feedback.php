<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input data
if (!isset($data['sit_in_id']) || !isset($data['rating']) || !isset($data['feedback_text'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

// Insert feedback
try {
    $stmt = $conn->prepare("INSERT INTO feedback (sit_in_id, rating, feedback_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $data['sit_in_id'], $data['rating'], $data['feedback_text']);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>