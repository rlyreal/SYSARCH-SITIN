<?php
session_start();
include 'db.php';

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM feedback WHERE id = '$delete_id'");
    header("Location: feedback.php");
    exit();
}

// Fetch feedback records with join to get student details
$sql = "SELECT f.*, s.idno, s.laboratory, s.fullname 
        FROM feedback f 
        JOIN sit_in s ON f.sit_in_id = s.id 
        ORDER BY f.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-In Monitoring - Feedbacks</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- Admin Navbar -->
<nav class="bg-[#2c343c] px-6 py-4 shadow-lg">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <!-- Logo Section -->
        <div class="flex items-center space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="text-2xl font-bold text-white">Admin</span>
        </div>

        <!-- Center Navigation Links -->
        <div class="flex-1 flex justify-center">
            <ul class="flex items-center space-x-6">
                <li><a href="admin_dashboard.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium">Home</a></li>
                <li><a href="search.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium">Search</a></li>
                <li><a href="students.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium">Students</a></li>
                <li><a href="sit_in.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium">Sit-in</a></li>
                <li><a href="sit_in_records.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium">View Records</a></li>
                <li><a href="reservation.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium">Reservation</a></li>
                <li><a href="reports.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium">Reports</a></li>
                <li><a href="feedback.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium">Feedback Reports</a></li>
            </ul>
        </div>

        <!-- Right-aligned Logout Button -->
        <div class="flex-shrink-0 ml-6">
            <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Log out</span>
            </a>
        </div>
    </div>
</nav>

<!-- Rest of your existing content -->
<div class="container mx-auto px-4 py-8">
    <!-- Header Section with DaisyUI Card -->
    <div class="card bg-base-100 shadow-xl mb-8">
        <div class="card-body">
            <h2 class="card-title text-3xl justify-center text-primary">FEEDBACK REPORTS</h2>
        </div>
    </div>

    <!-- Search Section with DaisyUI Input -->
    <div class="form-control w-full max-w-md mx-auto mb-6">
        <div class="input-group">
            <span class="input-group-addon">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
            <input type="text" 
                   id="searchInput" 
                   placeholder="Search feedbacks..." 
                   class="input input-bordered w-full" />
        </div>
    </div>

    <!-- Table Section with DaisyUI Table -->
    <div class="overflow-x-auto bg-base-100 rounded-lg shadow-xl">
        <table class="table table-zebra w-full">
            <!-- Table Head -->
            <thead>
                <tr class="bg-base-200">
                    <th class="text-base-content">ID Number</th>
                    <th class="text-base-content">Name</th>
                    <th class="text-base-content">Laboratory</th>
                    <th class="text-base-content">Date</th>
                    <th class="text-base-content">Rating</th>
                    <th class="text-base-content">Feedback</th>
                    <th class="text-base-content">Action</th>
                </tr>
            </thead>
            <tbody id="feedbackTable">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $date = date('Y-m-d', strtotime($row['created_at']));
                        $stars = str_repeat('★', $row['rating']) . str_repeat('☆', 5 - $row['rating']);
                        ?>
                        <tr class="hover">
                            <td class="font-medium"><?php echo $row['idno']; ?></td>
                            <td><?php echo $row['fullname']; ?></td>
                            <td><?php echo $row['laboratory']; ?></td>
                            <td><?php echo $date; ?></td>
                            <td class="text-warning"><?php echo $stars; ?></td>
                            <td class="max-w-xs truncate"><?php echo $row['feedback_text']; ?></td>
                            <td>
                                <button onclick="confirmDelete(<?php echo $row['id']; ?>)" 
                                        class="btn btn-error btn-sm gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="7" class="text-center text-base-content/70">No feedback available</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Enhanced search function
document.getElementById("searchInput").addEventListener("keyup", function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#feedbackTable tr");
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});

// Delete confirmation using DaisyUI modal
function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this feedback?")) {
        window.location.href = `feedback.php?delete_id=${id}`;
    }
}
</script>

</body>
</html>

<?php
$conn->close();
?>
