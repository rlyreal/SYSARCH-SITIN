<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get updated data from the form
$last_name = $_POST['last_name'];
$first_name = $_POST['first_name'];
$middle_name = $_POST['middle_name'];
$course_level = $_POST['course_level'];
$email = $_POST['email'];
$course = $_POST['course'];
$address = $_POST['address'];

// File upload handling
if (!empty($_FILES['profile_picture']['name'])) {
    $target_dir = "uploads/"; // Folder to store images
    $file_name = basename($_FILES['profile_picture']['name']);
    $target_file = $target_dir . time() . "_" . $file_name; // Unique filename
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is an image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check === false) {
        $uploadOk = 0;
        echo "File is not an image.";
    }

    // Allow only certain formats
    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_types)) {
        $uploadOk = 0;
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    }

    // Limit file size (2MB max)
    if ($_FILES["profile_picture"]["size"] > 2 * 1024 * 1024) {
        $uploadOk = 0;
        echo "Sorry, your file is too large.";
    }

    // If everything is okay, move file to server
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Update profile picture in the database
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $target_file, $user_id);
            $stmt->execute();
            $_SESSION['profile_picture'] = $target_file; // Update session
        } else {
            echo "Error uploading file.";
        }
    }
}

// Update user info in the database
$stmt = $conn->prepare("UPDATE users SET last_name = ?, first_name = ?, middle_name = ?, course = ?, year_level = ?, email = ?, address = ? WHERE id = ?");
$stmt->bind_param("sssssssi", $last_name, $first_name, $middle_name, $course, $course_level, $email, $address, $user_id);

if ($stmt->execute()) {
    // Update session data
    $_SESSION['last_name'] = $last_name;
    $_SESSION['first_name'] = $first_name;
    $_SESSION['middle_name'] = $middle_name;
    $_SESSION['course'] = $course;
    $_SESSION['year_level'] = $course_level;
    $_SESSION['email'] = $email;
    $_SESSION['address'] = $address;

    header('Location: dashboard.php?message=Profile updated successfully');
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
