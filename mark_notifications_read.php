<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Add a read_status column to your announcements table if you haven't already
$stmt = $conn->prepare("
    INSERT INTO announcement_reads (user_id, announcement_id, read_at) 
    SELECT ?, id, NOW() 
    FROM announcements 
    WHERE id NOT IN (
        SELECT announcement_id 
        FROM announcement_reads 
        WHERE user_id = ?
    )
");

$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();

echo json_encode(['status' => 'success']);