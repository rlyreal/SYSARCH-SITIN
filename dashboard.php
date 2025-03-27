<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT profile_picture, first_name, middle_name, last_name, course, year_level, email, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_picture, $first_name, $middle_name, $last_name, $course, $year_level, $email, $address);
$stmt->fetch();
$stmt->close();

// Replace the existing session count fetch code with this:
$stmt = $conn->prepare("SELECT COALESCE(
    (SELECT session_count FROM sit_in WHERE idno = (SELECT id_no FROM users WHERE id = ?) ORDER BY id DESC LIMIT 1), 
    30
) as session_count");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($session_count);
$stmt->fetch();
$stmt->close();

$user_profile = (!empty($profile_picture) && file_exists($profile_picture)) ? $profile_picture : "profile.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

    <nav class="navbar">
        <div class="navbar-title">Dashboard</div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="#">Notifications</a></li>
            <li><a href="editprofile.php">Edit Profile</a></li>
            <li><a href="history.php">History</a></li>
            <li><a href="#">Reservation</a></li>
        </ul>
        <a href="logout.php" class="logout-btn">Log Out</a>
    </nav>

    <div class="dashboard-container">
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
            <p><strong>Remaining Sessions:</strong> <?php echo htmlspecialchars($session_count); ?></p>
        </div>

        <div class="announcements">
            <h2>📢 Announcements</h2>
            <div id="announcementContainer"></div>
        </div>

        <!-- ✅ Rules and Regulations -->
        <div class="rules">
            <h2>📜 Rules and Regulations</h2>
            <h3>University of Cebu</h3>
            <h4>COLLEGE OF INFORMATION & COMPUTER STUDIES</h4>
            <h4>LABORATORY RULES AND REGULATIONS</h4>

            <div class="rules-content">
                <p>To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:</p>

                <ul>
                    <li> Maintain silence, proper decorum, and discipline inside the laboratory. Mobile phones, walkmans, and other personal pieces of equipment must be switched off.</li>
                    <li> Games are not allowed inside the lab. This includes computer-related games, card games, and other games that may disturb the operation of the lab.</li>
                    <li> Surfing the Internet is allowed only with the permission of the instructor. Downloading and installing of software are strictly prohibited.</li>
                    <li> Getting access to other websites not related to the course (especially pornographic and illicit sites) is strictly prohibited.</li>
                    <li> Deleting computer files and changing the set-up of the computer is a major offense.</li>
                    <li> Observe computer time usage carefully. A fifteen-minute allowance is given for each use. Otherwise, the unit will be given to those who wish to "sit-in".</li>
                    <li> Observe proper decorum while inside the laboratory. Do not get inside the lab unless the instructor is present. All bags, knapsacks, and the likes must be deposited at the counter. Follow the seating arrangement of your instructor. At the end of class, all software programs must be closed. Return all chairs to their proper places after using.</li>
                    <li> Chewing gum, eating, drinking, smoking, and other forms of vandalism are prohibited inside the lab.</li>
                    <li> Anyone causing a continual disturbance will be asked to leave the lab. Acts or gestures offensive to the members of the community, including public display of physical intimacy, are not tolerated.</li>
                    <li> Persons exhibiting hostile or threatening behavior such as yelling, swearing, or disregarding requests made by lab personnel will be asked to leave the lab.</li>
                    <li> For serious offenses, the lab personnel may call the Civil Security Office (CSU) for assistance.</li>
                    <li> Any technical problem or difficulty must be addressed to the laboratory supervisor, student assistant, or instructor immediately.</li>
                </ul>

                <h4>DISCIPLINARY ACTION</h4>
                <ul>
                    <li><strong>First Offense:</strong> The Head or the Dean or OIC recommends to the Guidance Center for a suspension from classes for each offender.</li>
                    <li><strong>Second and Subsequent Offenses:</strong> A recommendation for a heavier sanction will be endorsed to the Guidance Center.</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Replace the existing loadAnnouncements function
        function loadAnnouncements() {
            fetch('get_announcements.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('announcementContainer');
                    container.innerHTML = ''; // Clear previous content
                    if (data.status === 'success') {
                        data.announcements.forEach(announcement => {
                            const announcementDiv = document.createElement('div');
                            announcementDiv.classList.add('announcement-box', 'mb-4', 'p-4', 'bg-[#2c343c]', 'rounded-lg', 'shadow-md');
                            announcementDiv.innerHTML = `
                                <div class="flex flex-col">
                                    <div class="flex items-center mb-2">
                                        <span class="text-white font-bold">${announcement.admin_name}</span>
                                        <span class="text-gray-400 mx-2">|</span>
                                        <span class="text-gray-400">${announcement.date}</span>
                                    </div>
                                    <p class="text-white">${announcement.message}</p>
                                </div>`;
                            container.appendChild(announcementDiv);
                        });
                    } else {
                        container.innerHTML = '<p class="text-gray-500">No announcements available.</p>';
                    }
                })
                .catch(error => console.error('Error loading announcements:', error));
        }

        // Initial load and refresh every 30 seconds
        loadAnnouncements();
        setInterval(loadAnnouncements, 30000);
    </script>
</body>
</html>
