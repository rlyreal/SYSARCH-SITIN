<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $userFound = false;

    // Check admin table first (changed from admins to admin)
    $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userFound = true;
        $row = $result->fetch_assoc();

        // Direct password comparison since it's stored as plain text
        if ($password === $row['password']) {
            session_regenerate_id(true);

            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = 'admin';

            echo json_encode(["status" => "success", "message" => "Admin login successful!", "role" => "admin"]);
            exit();
        }
    }
    $stmt->close();

    // Check Users Table (keeping this part for regular users)
    $stmt = $conn->prepare("SELECT id, username, password, first_name, last_name FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userFound = true;
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['role'] = 'user';

            echo json_encode(["status" => "success", "message" => "Login successful!", "role" => "user"]);
            exit();
        }
    }
    $stmt->close();

    echo json_encode(["status" => "error", "message" => $userFound ? "Invalid password!" : "Invalid username or password!"]);
    $conn->close();
}
?>
