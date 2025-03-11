<?php
// Ensure session starts only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php'; // Ensure database connection is included

// Redirect if not logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// ✅ Define functions for fetching statistics
function get_total_students($conn) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM users"); 
    return $result->fetch_assoc()['total'];
}

function get_current_sit_in($conn) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM sit_in WHERE date = CURDATE()");
    return $result->fetch_assoc()['total'];
}

function get_total_sit_in($conn) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM sit_in");
    return $result->fetch_assoc()['total'];
}

// ✅ Fetch announcements from the database
function get_announcements($conn) {
    $result = $conn->query("SELECT * FROM announcements ORDER BY date DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>
