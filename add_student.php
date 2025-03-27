<?php
session_start();
include 'db.php';

header('Content-Type: application/json'); // Set JSON response header

// Set default response
$response = [
    'success' => false,
    'message' => 'An error occurred'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id_no = $_POST['id_no'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $year_level = $_POST['year_level'];
    $course = $_POST['course'];
    $address = $_POST['address'];

    try {
        // Sanitize input
        $id_no = mysqli_real_escape_string($conn, $id_no);
        $last_name = mysqli_real_escape_string($conn, $last_name);
        $first_name = mysqli_real_escape_string($conn, $first_name);
        $middle_name = mysqli_real_escape_string($conn, $middle_name);
        $course = mysqli_real_escape_string($conn, $course);
        $year_level = mysqli_real_escape_string($conn, $year_level);
        $username = mysqli_real_escape_string($conn, $username);
        $email = mysqli_real_escape_string($conn, $email);
        $address = mysqli_real_escape_string($conn, $address);

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

        // Insert into users table only
        $user_sql = "INSERT INTO users (id_no, email, username, password, first_name, middle_name, last_name, year_level, course, address) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param("ssssssssss", 
            $id_no, $email, $username, $password, $first_name, 
            $middle_name, $last_name, $year_level, $course, $address
        );
        
        if ($user_stmt->execute()) {
            $response = [
                'success' => true,
                'message' => 'Student added successfully'
            ];
        }

        $user_stmt->close();
        $conn->close();
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

// Return JSON response
echo json_encode($response);
?>