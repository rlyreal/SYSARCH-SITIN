<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, id_no, last_name, first_name, middle_name, course, year_level, email, address, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_number = $row['id_no'];  
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $middle_name = $row['middle_name'];
    $course = $row['course'];
    $year_level = $row['year_level'];
    $email = $row['email'];
    $address = $row['address'];
    $profile_picture = !empty($row['profile_picture']) ? $row['profile_picture'] : 'default_profile.png';
} else {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="editprofile.css">
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const profileInput = document.getElementById("profile_picture");
        const profilePreview = document.getElementById("profile_preview");
        const profileContainer = document.querySelector(".profile-container");

        profileContainer.addEventListener("click", function () {
            profileInput.click();
        });

        profileInput.addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (file) {
                profilePreview.src = URL.createObjectURL(file);
            }
        });
    });
    </script>
    <style>
        .profile-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #555;
            transition: transform 0.3s ease-in-out;
        }

        .profile-container:hover .profile-picture {
            transform: scale(1.1);
        }

        .upload-text {
            margin-top: 10px;
            font-size: 14px;
            color: #555;
        }

        #profile_picture {
            display: none;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">Dashboard</div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="#">Notifications</a></li>
            <li><a href="editprofile.php">Edit Profile</a></li>
            <li><a href="#">History</a></li>
            <li><a href="#">Reservation</a></li>
        </ul>
        <a href="logout.php" class="logout-btn">Log Out</a>
    </nav>

    <div class="edit-profile-container">
        <h2>Edit Profile</h2>
        <form action="updateprofile.php" method="POST" enctype="multipart/form-data">
            <!-- Modern Profile Picture Upload -->
            <div class="profile-container">
                <img id="profile_preview" src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-picture">
                <span class="upload-text">Click to upload</span>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
            </div>

            <div class="form-container">
                <div class="form-group">
                    <label for="id_number">ID Number</label>
                    <input type="text" id="id_number" name="id_number" value="<?php echo htmlspecialchars($id_number); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" pattern="[A-Za-z\s]+" required>
                </div>
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" pattern="[A-Za-z\s]+" required>
                </div>
                <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($middle_name); ?>" pattern="[A-Za-z\s]+">
                </div>
                <div class="form-group">
                    <label for="course_level">Course Level</label>
                    <select id="course_level" name="course_level">
                        <option value="1" <?php echo ($year_level == 1) ? 'selected' : ''; ?>>1</option>
                        <option value="2" <?php echo ($year_level == 2) ? 'selected' : ''; ?>>2</option>
                        <option value="3" <?php echo ($year_level == 3) ? 'selected' : ''; ?>>3</option>
                        <option value="4" <?php echo ($year_level == 4) ? 'selected' : ''; ?>>4</option>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="form-group full-width">
                    <label for="course">Course</label>
                    <select id="course" name="course">
                        <option value="BSIT" <?php echo ($course == "BSIT") ? 'selected' : ''; ?>>BSIT</option>
                        <option value="BSCS" <?php echo ($course == "BSCS") ? 'selected' : ''; ?>>BSCS</option>
                        <option value="BSECE" <?php echo ($course == "BSECE") ? 'selected' : ''; ?>>BSECE</option>
                        <option value="BSIS" <?php echo ($course == "BSIS") ? 'selected' : ''; ?>>BSIS</option>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>">
                </div>
            </div>
            <button type="submit" class="save-btn">Save</button>
        </form>
    </div>
</body>
</html>
