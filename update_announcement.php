<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if (isset($_POST['id']) && isset($_POST['message'])) {
    $id = intval($_POST['id']);
    $message = trim($_POST['message']);
    
    $stmt = $conn->prepare("UPDATE announcements SET message = ? WHERE id = ?");
    $stmt->bind_param("si", $message, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }
    $stmt->close();
}