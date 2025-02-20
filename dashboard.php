<?php
session_start();
include 'db.php'; // Ensure this file connects to your database

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch updated user data from the database to ensure session is always current
$stmt = $conn->prepare("SELECT profile_picture, first_name, middle_name, last_name, course, year_level, email, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_picture, $first_name, $middle_name, $last_name, $course, $year_level, $email, $address);
$stmt->fetch();
$stmt->close();

// Update session data with fresh values from the database
$_SESSION['profile_picture'] = $profile_picture;
$_SESSION['first_name'] = $first_name;
$_SESSION['middle_name'] = $middle_name;
$_SESSION['last_name'] = $last_name;
$_SESSION['course'] = $course;
$_SESSION['year_level'] = $year_level;
$_SESSION['email'] = $email;
$_SESSION['address'] = $address;

// Ensure the profile picture exists, otherwise, use default
$user_profile = (!empty($profile_picture) && file_exists($profile_picture)) ? $profile_picture : "profile.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body class="dashboard-page">

    <nav class="navbar">
        <div class="navbar-title">Dashboard</div>
        <ul class="nav-links">
            <li><a href="#">Home</a></li>
            <li><a href="#">Notifications</a></li>
            <li><a href="editprofile.php">Edit Profile</a></li>
            <li><a href="#">History</a></li>
            <li><a href="#">Reservation</a></li>
        </ul>
        <a href="logout.php" class="logout-btn">Log Out</a>
    </nav>

    <div class="dashboard-container">
        <!-- Student Information -->
        <div class="student-info">
            <h2>Student Information</h2>
            <div class="profile-container">
                <img src="<?php echo htmlspecialchars($user_profile); ?>" alt="Profile Picture" class="profile-picture">
            </div>
            <p><strong>Name:</strong> <?php echo htmlspecialchars("$first_name $middle_name $last_name"); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($course); ?></p>
            <p><strong>Year:</strong> <?php echo htmlspecialchars($year_level); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
            <p><strong>Session:</strong> 30</p> <!-- Placeholder for session count -->
        </div>

        <!-- Announcements -->
        <div class="announcements">
            <h2>📢 Announcement</h2>
            <div class="announcement-box">
                <h3>CCS Admin | 2025-Feb-03</h3>
                <p>The College of Computer Studies will open registration for Sit-in privilege tomorrow.</p>
            </div>
            <div class="announcement-box">
                <h3>CCS Admin | 2024-May-08</h3>
                <p>We are excited to announce the launch of our new website! 🚀 Explore our latest products now!</p>
            </div>
        </div>

        <!-- Rules and Regulations -->
        <div class="rules">
            <h2>📜 Rules and Regulations</h2>
            <h3>University of Cebu</h3>
            <h4>COLLEGE OF INFORMATION & COMPUTER STUDIES</h4>
            <h4>LABORATORY RULES AND REGULATIONS</h4>
            <p>1. Maintain silence, proper decorum, and discipline inside the lab.</p>
            <p>2. Games are not allowed inside the lab.</p>
            <p>3. Surfing the Internet is allowed only with instructor permission.</p>
        </div>
    </div>

</body>
</html>
