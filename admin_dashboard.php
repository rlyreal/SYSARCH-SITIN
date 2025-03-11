<?php
session_start();
include 'db.php';

// Redirect if not logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch admin details
$stmt = $conn->prepare("SELECT first_name, last_name FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name);
$stmt->fetch();
$stmt->close();

// Fetch statistics
$students = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$current_sit_in = $conn->query("SELECT COUNT(*) AS total FROM sit_in WHERE date = CURDATE()")->fetch_assoc()['total'];
$total_sit_in = $conn->query("SELECT COUNT(*) AS total FROM sit_in")->fetch_assoc()['total'];

// Handle announcement submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $announcement = trim($_POST['announcement']);
    if (!empty($announcement)) {
        $admin_name = $first_name . " " . $last_name;
        $query = $conn->prepare("INSERT INTO announcements (admin_name, message, date) VALUES (?, ?, NOW())");
        $query->bind_param("ss", $admin_name, $announcement);
        if ($query->execute()) {
            echo "<script>alert('Announcement posted successfully!');</script>";
        } else {
            echo "<script>alert('Error posting announcement.');</script>";
        }
        $query->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin_dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <!-- ✅ Admin Navbar -->
    <nav class="navbar">
        <div class="logo">College of Computer Studies Admin</div>
        <ul>
            <li><a href="admin_dashboard.php">Home</a></li>
            <li><a href="search.php">Search</a></li>
            <li><a href="students.php">Students</a></li>
            <li><a href="sit_in.php">Sit-in</a></li>
            <li><a href="view_sit_in.php">View Sit-in Records</a></li>
            <li><a href="reports.php">Generate Reports</a></li>
            <li><a href="reservation.php">Reservation</a></li>
        </ul>
        <a href="logout.php" class="logout-button">Log out</a>
    </nav>

    <div class="container mt-5">
        <h2>Welcome, <?php echo htmlspecialchars($first_name . " " . $last_name); ?>!</h2>

        <div class="row g-4">
            <!-- ✅ Statistics Section -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">📊 Statistics</div>
                    <div class="card-body">
                        <p><strong>Students Registered:</strong> <?= $students; ?></p>
                        <p><strong>Currently Sit-in:</strong> <?= $current_sit_in; ?></p>
                        <p><strong>Total Sit-in:</strong> <?= $total_sit_in; ?></p>
                        <canvas id="statsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- ✅ Announcements Section -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning">📢 Announcements</div>
                    <div class="card-body">
                        <form method="POST">
                            <textarea name="announcement" class="form-control mb-2" placeholder="Write an announcement..." required></textarea>
                            <button type="submit" class="btn btn-success btn-sm">Post</button>
                        </form>
                        <hr>
                        <h5>Recent Announcements</h5>
                        <div id="announcementContainer">
                            <!-- Announcements will load here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ✅ Pie Chart for Statistics
        new Chart(document.getElementById('statsChart'), {
            type: 'pie',
            data: {
                labels: ['Registered Students', 'Currently Sit-in', 'Total Sit-in'],
                datasets: [{
                    data: [<?= $students; ?>, <?= $current_sit_in; ?>, <?= $total_sit_in; ?>],
                    backgroundColor: ['#007bff', '#28a745', '#ffc107']
                }]
            }
        });

        // ✅ Load Announcements
        function loadAnnouncements() {
            fetch('get_announcements.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('announcementContainer');
                    container.innerHTML = ''; // Clear previous content
                    if (data.status === 'success') {
                        data.announcements.forEach(announcement => {
                            const announcementDiv = document.createElement('div');
                            announcementDiv.innerHTML = `<div class="announcement-box">
                                <h3>${announcement.admin_name} | ${announcement.date}</h3>
                                <p>${announcement.message}</p>
                                <hr>
                            </div>`;
                            container.appendChild(announcementDiv);
                        });
                    } else {
                        container.innerHTML = '<p>No announcements available.</p>';
                    }
                })
                .catch(error => console.error('Error loading announcements:', error));
        }
        
        // Load announcements on page load & refresh every minute
        window.onload = loadAnnouncements;
        setInterval(loadAnnouncements, 60000);
    </script>

</body>
</html>
