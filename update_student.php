<?php
session_start();
include 'db.php';

$response = [
    'success' => false,
    'message' => 'Invalid request'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $id_no = $_POST['id_no'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $year_level = $_POST['year_level'];
    $course = $_POST['course'];

    try {
        $sql = "UPDATE users SET 
                id_no = ?,
                first_name = ?,
                middle_name = ?,
                last_name = ?,
                year_level = ?,
                course = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", 
            $id_no,
            $first_name,
            $middle_name,
            $last_name,
            $year_level,
            $course,
            $student_id
        );

        if ($stmt->execute()) {
            $response = [
                'success' => true,
                'message' => 'Student updated successfully'
            ];
        } else {
            throw new Exception("Error updating student");
        }

        $stmt->close();

    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>