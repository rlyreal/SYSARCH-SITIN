<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Handle different actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'get_recent') {
        // Get recent resources
        $query = "SELECT * FROM resources ORDER BY created_at DESC";
        $result = $conn->query($query);
        
        $resources = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $resources[] = $row;
            }
        }
        
        echo json_encode($resources);
        exit();
    }
    
    if ($action === 'search') {
        $query = $_GET['query'];
        $sql = "SELECT * FROM resources WHERE 
                title LIKE ? OR 
                professor LIKE ? OR 
                description LIKE ?
                ORDER BY created_at DESC";
        
        $stmt = $conn->prepare($sql);
        $searchParam = "%{$query}%";
        $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $resources = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $resources[] = $row;
            }
        }
        
        echo json_encode($resources);
        exit();
    }
    
    if ($action === 'get_resource') {
        $id = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM resources WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            echo json_encode(['success' => false, 'message' => 'Resource not found']);
        }
        exit();
    }
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'delete_resource') {
        $id = $_POST['id'];
        
        // First get the resource to delete any associated files
        $stmt = $conn->prepare("SELECT cover_image FROM resources WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // If there's a cover image, delete it from the server
            if ($row['cover_image'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $row['cover_image'])) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $row['cover_image']);
            }
        }
        
        // Now delete the resource
        $stmt = $conn->prepare("DELETE FROM resources WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete resource']);
        }
        exit();
    }
    
    if ($_POST['action'] === 'add_resource' || $_POST['action'] === 'update_resource') {
        $title = $_POST['title'];
        $professor = $_POST['professor'];
        $description = $_POST['description'];
        $resource_link = $_POST['resource_link'];
        
        // Handle file upload if present
        $cover_image_path = null;
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
            $upload_dir = '/uploads/resources/';
            // Create directory if it doesn't exist
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $upload_dir)) {
                mkdir($_SERVER['DOCUMENT_ROOT'] . $upload_dir, 0755, true);
            }
            
            $file_name = time() . '_' . $_FILES['cover_image']['name'];
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $file_path)) {
                $cover_image_path = $file_path;
            }
        }
        
        if ($_POST['action'] === 'add_resource') {
            // Add new resource
            $stmt = $conn->prepare("INSERT INTO resources (title, professor, description, resource_link, cover_image, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssss", $title, $professor, $description, $resource_link, $cover_image_path);
        } else {
            // Update existing resource
            $id = $_POST['id'];
            
            if ($cover_image_path) {
                // If new image uploaded, update with new image
                $stmt = $conn->prepare("UPDATE resources SET title = ?, professor = ?, description = ?, resource_link = ?, cover_image = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $title, $professor, $description, $resource_link, $cover_image_path, $id);
            } else {
                // Otherwise keep existing image
                $stmt = $conn->prepare("UPDATE resources SET title = ?, professor = ?, description = ?, resource_link = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $title, $professor, $description, $resource_link, $id);
            }
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save resource']);
        }
        exit();
    }
}

// If we get here, it means no valid action was provided
echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>