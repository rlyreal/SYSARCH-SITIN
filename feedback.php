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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-In Monitoring - Feedbacks</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
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
    <!-- Header Section -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-2">FEEDBACK REPORTS</h2>
    </div>

    <!-- Search Section -->
    <div class="mb-6">
        <div class="relative">
            <input type="text" 
                   id="searchInput" 
                   placeholder="Search feedbacks..." 
                   class="w-full px-4 py-2 pl-10 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feedback</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="feedbackTable">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $date = date('Y-m-d', strtotime($row['created_at']));
                            $stars = str_repeat('★', $row['rating']) . str_repeat('☆', 5 - $row['rating']);
                            
                            echo "<tr class='hover:bg-gray-50 transition-colors duration-200'>
                                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>{$row['idno']}</td>
                                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>{$row['fullname']}</td>
                                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>{$row['laboratory']}</td>
                                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>{$date}</td>
                                <td class='px-6 py-4 whitespace-nowrap text-sm text-yellow-400'>{$stars}</td>
                                <td class='px-6 py-4 text-sm text-gray-900 max-w-xs truncate'>{$row['feedback_text']}</td>
                                <td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>
                                    <button onclick='if(confirm(\"Are you sure you want to delete this feedback?\")) window.location.href=\"feedback.php?delete_id={$row['id']}\"' 
                                            class='bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md transition-colors duration-200 flex items-center gap-1'>
                                        <svg class='h-4 w-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'/>
                                        </svg>
                                        Delete
                                    </button>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='px-6 py-4 text-center text-gray-500'>No feedback available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Live search function
document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#feedbackTable tr");
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>

</body>
</html>

<?php
$conn->close();
?>
