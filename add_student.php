<?php
session_start();
include 'db.php';

header('Content-Type: application/json'); // Set JSON response header

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize input
        $id_no = mysqli_real_escape_string($conn, $_POST['id_no']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
        $course = mysqli_real_escape_string($conn, $_POST['course']);
        $year_level = mysqli_real_escape_string($conn, $_POST['year_level']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);

        // Check if ID number, username, or email already exists
        $check_sql = "SELECT * FROM users WHERE id_no = ? OR username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("sss", $id_no, $username, $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID number, username, or email already exists'
            ]);
            exit;
        }

        // Insert into users table
        $sql = "INSERT INTO users (id_no, last_name, first_name, middle_name, course, year_level, username, password, email, address) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $id_no, $last_name, $first_name, $middle_name, $course, $year_level, $username, $password, $email, $address);
        
        if ($stmt->execute()) {
            // Also create initial sit_in record with 30 sessions
            $sit_in_sql = "INSERT INTO sit_in (idno, session_count) VALUES (?, 30)";
            $sit_in_stmt = $conn->prepare($sit_in_sql);
            $sit_in_stmt->bind_param("s", $id_no);
            $sit_in_stmt->execute();

            echo json_encode([
                'success' => true,
                'message' => 'Student added successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $conn->error
            ]);
        }
        
        $stmt->close();
        $conn->close();
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