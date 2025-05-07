<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_resource') {
    $title = $_POST['title'];
    $professor = $_POST['professor'];
    $description = $_POST['description'];
    $resource_link = $_POST['resource_link'];
    $added_by = $_SESSION['admin_id'];
    
    // Handle file upload
    $cover_image = null;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === 0) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = uniqid() . '_' . basename($_FILES['cover_image']['name']);
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $target_path)) {
            $cover_image = $target_path;
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO resources (title, professor, description, resource_link, cover_image, added_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $title, $professor, $description, $resource_link, $cover_image, $added_by);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_recent') {
    $stmt = $conn->prepare("SELECT * FROM resources ORDER BY created_at DESC LIMIT 10");
    $stmt->execute();
    $result = $stmt->get_result();
    $resources = [];
    
    while ($row = $result->fetch_assoc()) {
        $resources[] = $row;
    }
    
    echo json_encode($resources);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'search') {
    $query = isset($_GET['query']) ? '%' . $_GET['query'] . '%' : '';
    
    $stmt = $conn->prepare("
        SELECT * FROM resources 
        WHERE title LIKE ? 
        OR professor LIKE ? 
        OR description LIKE ?
        ORDER BY created_at DESC
    ");
    
    $stmt->bind_param("sss", $query, $query, $query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $resources = [];
    while ($row = $result->fetch_assoc()) {
        $resources[] = $row;
    }
    
    echo json_encode($resources);
    exit;
}