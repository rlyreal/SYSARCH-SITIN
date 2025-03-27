<?php
session_start();
include 'db.php';

$response = [
    'success' => false,
    'message' => 'Invalid request'
];

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete from users table
        $delete_sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $conn->commit();
            $response = [
                'success' => true,
                'message' => 'Student deleted successfully'
            ];
        } else {
            throw new Exception("Error deleting student");
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        $response = [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);