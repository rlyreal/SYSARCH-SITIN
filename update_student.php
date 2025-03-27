<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
        $id_no = mysqli_real_escape_string($conn, $_POST['id_no']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $year_level = mysqli_real_escape_string($conn, $_POST['year_level']);
        $course = mysqli_real_escape_string($conn, $_POST['course']);

        // Check if ID number already exists for other students
        $check_sql = "SELECT id FROM users WHERE id_no = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $id_no, $student_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID number already exists'
            ]);
            exit;
        }

        // Update student information
        $sql = "UPDATE users SET id_no=?, first_name=?, middle_name=?, last_name=?, year_level=?, course=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $id_no, $first_name, $middle_name, $last_name, $year_level, $course, $student_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Student updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error updating student: ' . $conn->error
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>