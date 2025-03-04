<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$updateSuccess = false;

// Handle profile picture upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
    $uploadDir = "uploads/";
    $fileName = basename($_FILES["profile_picture"]["name"]);
    $targetFilePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->bind_param("si", $targetFilePath, $user_id);
        $stmt->execute();
    }
}

// Updating user details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $course = $_POST['course'];
    $year_level = $_POST['course_level'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE users SET last_name = ?, first_name = ?, middle_name = ?, course = ?, year_level = ?, email = ?, address = ? WHERE id = ?");
    $stmt->bind_param("ssssissi", $last_name, $first_name, $middle_name, $course, $year_level, $email, $address, $user_id);

    if ($stmt->execute()) {
        // Redirect to editprofile.php with success message
        header("Location: editprofile.php?message=Profile updated successfully!");
        exit();
    } else {
        header("Location: editprofile.php?message=Error updating profile!");
        exit();
    }
}
?>
