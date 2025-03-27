<?php
header('Content-Type: application/json');
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Collect form data
        $id_no = trim($_POST['id']);
        $lName = trim($_POST['lName']);
        $fname = trim($_POST['fname']);
        $mname = trim($_POST['mname']);
        $course = trim($_POST['course']);
        $yearlevel = trim($_POST['yearlevel']);
        $email = trim($_POST['email']);
        $address = trim($_POST['address']);
        $username = trim($_POST['username']);
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

        // Check if username already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            echo json_encode([
                "status" => "error",
                "message" => "Username already exists!"
            ]);
            $check_stmt->close();
            exit;
        }
        $check_stmt->close();

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (id_no, last_name, first_name, middle_name, course, year_level, email, address, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssssssss", 
            $id_no,
            $lName,
            $fname,
            $mname,
            $course,
            $yearlevel,
            $email,
            $address,
            $username,
            $password
        );

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Registration successful!"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Registration failed: " . $stmt->error
            ]);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Registration failed: " . $e->getMessage()
        ]);
    }

    $conn->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}
?>
