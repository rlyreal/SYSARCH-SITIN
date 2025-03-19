<?php
session_start();
include 'db.php';

// Debugging: Ensure the database connection works
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Disable caching to always get the latest session count
$conn->query("SET SESSION query_cache_type = OFF;");

// Replace the existing AJAX handler with this code
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout_id'])) {
    $logout_id = intval($_POST['logout_id']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get current session count
        $stmt = $conn->prepare("SELECT session_count FROM sit_in WHERE id = ?");
        $stmt->bind_param("i", $logout_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            $new_session_count = max(0, $row['session_count'] - 1); // Deduct 1 session, minimum 0

            // Update time_out and session count
            $stmt = $conn->prepare("UPDATE sit_in SET 
                time_out = NOW(), 
                session_count = ? 
                WHERE id = ?");
            $stmt->bind_param("ii", $new_session_count, $logout_id);
            
            if ($stmt->execute()) {
                $conn->commit();
                echo json_encode([
                    "success" => true, 
                    "message" => "Student successfully timed out. Remaining sessions: " . $new_session_count
                ]);
            } else {
                throw new Exception($conn->error);
            }
            $stmt->close();
        } else {
            throw new Exception("Record not found");
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            "success" => false, 
            "message" => "Error updating record: " . $e->getMessage()
        ]);
    }

    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Sit-in</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .navbar {
            background-color: #333;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .navbar .logo {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .navbar ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            transition: 0.3s;
        }

        .navbar ul li a:hover {
            background-color: #555;
            border-radius: 5px;
        }

        .logout-button {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            background-color: #dc3545;
            border-radius: 5px;
            transition: 0.3s;
        }

        .logout-button:hover {
            background-color: #c82333;
            color: white;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="logo">College of Computer Studies Admin</div>
    <ul>
        <li><a href="admin_dashboard.php">Home</a></li>
        <li><a href="search.php">Search</a></li>
        <li><a href="students.php">Students</a></li>
        <li><a href="sit_in.php">Sit-in</a></li>
        <li><a href="sit_in_records.php">View Sit-in Records</a></li>
        <li><a href="reports.php">Generate Reports</a></li>
        <li><a href="reservation.php">Reservation</a></li>
    </ul>
    <a href="logout.php" class="logout-button">Log out</a>
</nav>
<div class="container mt-5">
    <h2 class="text-center">Current Sit-in</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sit ID Number</th>
                <th>ID Number</th>
                <th>Name</th>
                <th>Purpose</th>
                <th>Sit Lab</th>
                <th>Session</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch sit-in records where time_out is NULL
            $sql = "SELECT id, idno, fullname, purpose, laboratory, session_count 
                    FROM sit_in
                    WHERE time_out IS NULL
                    ORDER BY created_at DESC";
            
            $result = $conn->query($sql);
            
            if (!$result) {
                die("<tr><td colspan='7' class='text-danger text-center'>SQL Error: " . $conn->error . "</td></tr>");
            }

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr id='row-" . $row['id'] . "'>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['idno']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['purpose']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['laboratory']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['session_count']) . "</td>";
                    
                    // Log Out button using AJAX
                    echo "<td class='actions-cell'><button class='btn btn-warning btn-sm logout-btn' data-id='" . $row['id'] . "'>Log Out</button></td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>No data available</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".logout-btn").forEach(button => {
        button.addEventListener("click", function (event) {
            event.preventDefault();

            let logoutId = this.getAttribute("data-id"); // Get student ID
            let row = document.getElementById("row-" + logoutId); // Get the row element

            if (confirm("Are you sure you want to log out this student?")) {
                fetch("sit_in.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "logout_id=" + logoutId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        row.remove(); // Remove row from table
                        alert(data.message);
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => console.error("Error:", error));
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
