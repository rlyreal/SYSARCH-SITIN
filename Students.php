<?php
session_start();
include 'db.php';


// Reset session if requested
if (isset($_GET['reset_session'])) {
session_unset();
session_destroy();
header("Location: Students.php");
exit();
}

// Update SQL query to include session count
$sql = "SELECT u.id, u.id_no, CONCAT(u.first_name, ' ', u.middle_name, ' ', u.last_name) AS full_name,
        u.year_level, u.course, 
        COALESCE((SELECT session_count FROM sit_in WHERE idno = u.id_no ORDER BY id DESC LIMIT 1), 0) as session_count 
        FROM users u 
        ORDER BY u.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Information</title>
<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<!-- Admin Navbar -->
<nav class="bg-[#2c343c] px-6 py-4 shadow-lg">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <!-- Logo Section -->
        <div class="flex items-center space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="text-2xl font-bold text-white">Admin Dashboard</span>
        </div>

        <!-- Center Navigation Links -->
        <div class="flex-1 flex justify-center">
            <ul class="flex items-center space-x-6">
                <li>
                    <a href="admin_dashboard.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="search.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Search</span>
                    </a>
                </li>
                <li>
                    <a href="students.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Students</span>
                    </a>
                </li>
                <li>
                <a href="sit_in.php" 
                           class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span>Sit-in</span>
                    </a>
                </li>
                <a href="sit_in_records.php" 
                           class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span>View Records</span>
                        </a>
                    </li>
                <li>
                <a href="reservation.php" 
                       class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Reservation</span>
                    </a>
                </li>
                <li>
                    <a href="reports.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Reports</span>
                    </a>
                </li>
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

<div class="container mx-auto px-4 py-8">
<!-- Header with title and reset button -->
<div class="flex justify-between items-center mb-6">
<h1 class="text-3xl font-bold text-gray-800">Student Information</h1>
<a href="?reset_session" class="bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
</svg>
Reset Session
</a>
</div>

<!-- Search, filter, and add new student -->
<div class="flex flex-wrap justify-between items-center mb-6 gap-4">
<div class="relative">
<input type="text" id="searchInput" placeholder="Search students..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
</svg>
</div>
<a href="#" class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
</svg>
Add New Student
</a>
</div>

<!-- Student Table -->
<div class="overflow-x-auto bg-white rounded-xl shadow-md">
<table class="w-full table-auto">
<thead>
<tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
<th class="py-3 px-6 text-left">ID Number</th>
<th class="py-3 px-6 text-left">Name</th>
<th class="py-3 px-6 text-left">Year Level</th>
<th class="py-3 px-6 text-left">Course</th>
<th class="py-3 px-6 text-left">Sessions Left</th>
<th class="py-3 px-6 text-center">Actions</th>
</tr>
</thead>
<tbody class="text-gray-600 text-sm">
<?php
if ($result->num_rows > 0) {
$count = 0;
while ($row = $result->fetch_assoc()) {
$bgColor = $count % 2 === 0 ? "bg-white" : "bg-gray-50";
$count++;
?>
<tr class="<?php echo $bgColor; ?> border-b border-gray-200 hover:bg-gray-100 transition duration-150">
<td class="py-3 px-6 text-left whitespace-nowrap">
<?php echo $row['id_no']; ?>
</td>
<td class="py-3 px-6 text-left">
<?php echo $row['full_name']; ?>
</td>
<td class="py-3 px-6 text-left">
<?php echo $row['year_level']; ?>
</td>
<td class="py-3 px-6 text-left">
<?php echo $row['course']; ?>
</td>
<td class="py-3 px-6 text-left">
<?php echo $row['session_count']; ?>
</td>
<td class="py-3 px-6 text-center">
<div class="flex item-center justify-center gap-2">
<a href="edit_student.php?id=<?php echo $row['id']; ?>" class="w-8 h-8 rounded-full bg-blue-100 hover:bg-blue-200 flex items-center justify-center transition duration-200" title="Edit">
<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
</svg>
</a>
<a href="#" onclick="confirmDelete(<?php echo $row['id']; ?>)" class="w-8 h-8 rounded-full bg-red-100 hover:bg-red-200 flex items-center justify-center transition duration-200" title="Delete">
<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
</svg>
</a>
</div>
</td>
</tr>
<?php
}
} else {
?>
<tr>
<td colspan="6" class="py-8 text-center text-gray-500">No student records found</td>
</tr>
<?php
}
?>
</tbody>
</table>
</div>

<!-- Pagination -->
<div class="flex justify-between items-center mt-6">
<p class="text-sm text-gray-600">Showing <strong><?php echo min($result->num_rows, 10); ?></strong> of <strong><?php echo $result->num_rows; ?></strong> entries</p>
<div class="flex space-x-1">
<button class="px-3 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-100 disabled:opacity-50">Previous</button>
<button class="px-3 py-1 rounded border border-blue-500 bg-blue-500 text-white">1</button>
<button class="px-3 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-100">2</button>
<button class="px-3 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-100">3</button>
<button class="px-3 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-100">Next</button>
</div>
</div>
</div>

<script>
function confirmDelete(id) {
if(confirm("Are you sure you want to delete this student?")) {
window.location.href = "delete_student.php?id=" + id;
}
}

// Simple search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
const input = this.value.toLowerCase();
const table = document.querySelector('table');
const rows = table.querySelectorAll('tbody tr');

rows.forEach(row => {
const text = row.textContent.toLowerCase();
if(text.indexOf(input) > -1) {
row.style.display = "";
} else {
row.style.display = "none";
}
});
});
</script>
</body>
</html>