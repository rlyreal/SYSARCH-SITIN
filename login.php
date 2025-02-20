<?php
session_start();
include 'db.php';

header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepare SQL query to fetch user data including full name, email, address, etc.
    $stmt = $conn->prepare("SELECT id, username, password, first_name, middle_name, last_name, course, year_level, email, address FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Check if the password is correct
        if (password_verify($password, $row['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['middle_name'] = $row['middle_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['course'] = $row['course'];
            $_SESSION['year_level'] = $row['year_level'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['address'] = $row['address'];

            echo json_encode(["status" => "success", "message" => "Login successful!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid username or password!"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid username or password!"]);
    }

    $stmt->close();
    $conn->close();
}
?>
