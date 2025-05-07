<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$query = isset($_GET['query']) ? '%' . $_GET['query'] . '%' : '';

if (!empty($query)) {
    $stmt = $conn->prepare("
        SELECT * FROM resources 
        WHERE title LIKE ? 
        OR professor LIKE ? 
        OR description LIKE ?
        ORDER BY created_at DESC
    ");
    $stmt->bind_param("sss", $query, $query, $query);
} else {
    $stmt = $conn->prepare("SELECT * FROM resources ORDER BY created_at DESC");
}

$stmt->execute();
$result = $stmt->get_result();
$resources = [];

while ($row = $result->fetch_assoc()) {
    $resources[] = $row;
}

echo json_encode($resources);