<?php
session_start();
include 'db.php';

// Reset all session counts if requested
if (isset($_GET['reset_session'])) {
    // Update all session counts in sit_in table to 30
    $reset_sql = "UPDATE sit_in SET session_count = 30";
    if ($conn->query($reset_sql) === TRUE) {
        // Add success message to session
        $_SESSION['message'] = "All session counts have been reset to 30 successfully.";
    } else {
        $_SESSION['message'] = "Error resetting session counts: " . $conn->error;
    }
    header("Location: Students.php");
    exit();
}

// Update SQL query to handle session count with default value of 30 for new users
$sql = "SELECT u.id, u.id_no, CONCAT(u.first_name, ' ', u.middle_name, ' ', u.last_name) AS full_name,
        u.year_level, u.course, 
        COALESCE(s.session_count, 30) as session_count 
        FROM users u 
        LEFT JOIN sit_in s ON u.id_no = s.idno 
            AND s.id = (SELECT MAX(id) FROM sit_in WHERE idno = u.id_no)
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
            <span class="text-2xl font-bold text-white">Admin</span>
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
                <li><a href="feedback.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        <span>Feedback Reports</span>
                    </a></li>
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
    <?php if (isset($_SESSION['message'])): ?>
        <div class="mb-4 p-4 rounded-lg <?php echo strpos($_SESSION['message'], 'Error') !== false ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
            <?php 
            echo $_SESSION['message'];
            unset($_SESSION['message']); // Clear the message after displaying
            ?>
        </div>
    <?php endif; ?>
    
<!-- Header with title and reset button -->
<div class="flex justify-between items-center mb-6">
<h1 class="text-3xl font-bold text-gray-800">Student Information</h1>
<a href="#" class="bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center" id="resetButton">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
    </svg>
    Reset All Sessions
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
<button onclick="editStudent(<?php echo htmlspecialchars(json_encode([
    'id' => $row['id'],
    'id_no' => $row['id_no'],
    'full_name' => $row['full_name'],
    'year_level' => $row['year_level'],
    'course' => $row['course']
])); ?>)" class="w-8 h-8 rounded-full bg-blue-100 hover:bg-blue-200 flex items-center justify-center transition duration-200" title="Edit">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
    </svg>
</button>
<button onclick="showDeleteConfirmation(<?php echo $row['id']; ?>)" 
        class="w-8 h-8 rounded-full bg-red-100 hover:bg-red-200 flex items-center justify-center transition duration-200" 
        title="Delete">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
    </svg>
</button>
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

<!-- Reset Sessions Modal -->
<div id="resetModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Reset All Sessions</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to reset ALL session counts to 30? This action cannot be undone.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmReset" class="px-4 py-2 bg-yellow-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    Reset All Sessions
                </button>
                <button id="cancelReset" class="mt-3 px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Message Modal -->
<div id="successModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Reset Successful!</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    All session counts have been successfully reset to 30.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="closeSuccessModal" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white animate-fade-in-down">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Success!</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    All session counts have been reset to 30 successfully.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="closeSuccessModal" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300 transition duration-200">
                    Got it!
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-[450px] shadow-lg rounded-lg bg-white">
        <!-- Modal Header -->
        <div class="mb-5">
            <h2 class="text-xl font-bold text-gray-800 text-center">Add New Student</h2>
        </div>

        <!-- Form -->
        <form id="addStudentForm" class="space-y-4">
            <!-- Basic Information -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID Number*</label>
                    <input type="text" name="id_no" required placeholder="Enter ID number"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email*</label>
                    <input type="email" name="email" required placeholder="Enter email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name*</label>
                    <input type="text" name="last_name" required placeholder="Enter last name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name*</label>
                    <input type="text" name="first_name" required placeholder="Enter first name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                    <input type="text" name="middle_name" placeholder="Enter middle name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Course*</label>
                    <select name="course" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Course</option>
                        <option value="BSIT">BSIT</option>
                        <option value="BSCS">BSCS</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username*</label>
                    <input type="text" name="username" required placeholder="Enter username"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year Level*</label>
                    <select name="year_level" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Year</option>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password*</label>
                    <input type="password" name="password" required placeholder="Enter password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="address" placeholder="Enter complete address"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" id="cancelAddStudent"
                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600">
                    Add Student
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Student Success Modal -->
<div id="addSuccessModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white animate-fade-in-down">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Student Added Successfully!</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    The new student has been successfully added to the system.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="closeAddSuccessModal" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300 transition duration-200">
                    Got it!
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div id="editStudentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-[450px] shadow-lg rounded-lg bg-white">
        <div class="mb-5">
            <h2 class="text-xl font-bold text-gray-800 text-center">Edit Student</h2>
        </div>

        <form id="editStudentForm" class="space-y-4">
            <input type="hidden" id="edit_student_id" name="student_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID Number*</label>
                    <input type="text" id="edit_id_no" name="id_no" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name*</label>
                    <input type="text" id="edit_last_name" name="last_name" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name*</label>
                    <input type="text" id="edit_first_name" name="first_name" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                    <input type="text" id="edit_middle_name" name="middle_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year Level*</label>
                    <select id="edit_year_level" name="year_level" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Course*</label>
                    <select id="edit_course" name="course" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="BSIT">BSIT</option>
                        <option value="BSCS">BSCS</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" id="cancelEditStudent"
                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Success Modal -->
<div id="editSuccessModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white animate-fade-in-down">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Student Updated Successfully!</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    The student information has been successfully updated.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="closeEditSuccessModal" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300 transition duration-200">
                    Got it!
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete Student</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete this student? This action cannot be undone.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <input type="hidden" id="deleteStudentId">
                <button id="confirmDelete" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 mb-2">
                    Delete Student
                </button>
                <button id="cancelDelete" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Modal Elements
const resetModal = document.getElementById('resetModal');
const deleteModal = document.getElementById('deleteModal');
const addStudentModal = document.getElementById('addStudentModal');
const editStudentModal = document.getElementById('editStudentModal');
const successModal = document.getElementById('successModal');

// Reset Sessions Functionality
const resetButton = document.getElementById('resetButton');
const confirmReset = document.getElementById('confirmReset');
const cancelReset = document.getElementById('cancelReset');

resetButton.addEventListener('click', () => {
    resetModal.classList.remove('hidden');
});

cancelReset.addEventListener('click', () => {
    resetModal.classList.add('hidden');
});

confirmReset.addEventListener('click', async () => {
    try {
        const response = await fetch('Students.php?reset_session=true', {
            method: 'GET'
        });
        
        if (response.ok) {
            resetModal.classList.add('hidden');
            successModal.classList.remove('hidden');
            
            // Reload the page after a short delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            alert('Error resetting sessions');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error resetting sessions');
    }
});

// Add close handler for success modal
const closeSuccessModal = document.getElementById('closeSuccessModal');
if (closeSuccessModal) {
    closeSuccessModal.addEventListener('click', () => {
        successModal.classList.add('hidden');
        window.location.reload();
    });
}

// Make sure the resetButton click handler is properly set
document.getElementById('resetButton').addEventListener('click', (e) => {
    e.preventDefault();
    resetModal.classList.remove('hidden');
});

// Add click outside handler for reset modal
resetModal.addEventListener('click', (e) => {
    if (e.target === resetModal) {
        resetModal.classList.add('hidden');
    }
});

// Delete Functionality
function showDeleteConfirmation(id) {
    document.getElementById('deleteStudentId').value = id;
    deleteModal.classList.remove('hidden');
}

const confirmDelete = document.getElementById('confirmDelete');
const cancelDelete = document.getElementById('cancelDelete');

cancelDelete.addEventListener('click', () => {
    deleteModal.classList.add('hidden');
});

// Add New Student Functionality
const addStudentButton = document.querySelector('a[href="#"].bg-green-500');
const cancelAddStudent = document.getElementById('cancelAddStudent');

addStudentButton.addEventListener('click', (e) => {
    e.preventDefault();
    addStudentModal.classList.remove('hidden');
});

cancelAddStudent.addEventListener('click', () => {
    addStudentModal.classList.add('hidden');
    addStudentForm.reset();
});

// Edit Student Functionality
function editStudent(studentData) {
    try {
        // Populate form fields
        document.getElementById('edit_student_id').value = studentData.id;
        document.getElementById('edit_id_no').value = studentData.id_no;
        
        // Split full name and handle name parts
        const fullName = studentData.full_name;
        const nameParts = fullName.trim().split(' ');
        
        // Handle name parts based on length
        if (nameParts.length === 3) {
            document.getElementById('edit_first_name').value = nameParts[0];
            document.getElementById('edit_middle_name').value = nameParts[1];
            document.getElementById('edit_last_name').value = nameParts[2];
        } else if (nameParts.length === 2) {
            document.getElementById('edit_first_name').value = nameParts[0];
            document.getElementById('edit_middle_name').value = '';
            document.getElementById('edit_last_name').value = nameParts[1];
        } else {
            document.getElementById('edit_first_name').value = nameParts[0] || '';
            document.getElementById('edit_middle_name').value = '';
            document.getElementById('edit_last_name').value = nameParts[1] || '';
        }
        
        document.getElementById('edit_year_level').value = studentData.year_level;
        document.getElementById('edit_course').value = studentData.course;

        // Show the modal
        editStudentModal.classList.remove('hidden');
    } catch (error) {
        console.error('Error populating edit form:', error);
    }
}

// Add these event listeners
document.getElementById('editStudentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('update_student.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        if (result.success) {
            editStudentModal.classList.add('hidden');
            window.location.reload();
        } else {
            alert(result.message || 'Error updating student');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating student');
    }
});

// Add cancel button handler
document.getElementById('cancelEditStudent').addEventListener('click', () => {
    editStudentModal.classList.add('hidden');
    document.getElementById('editStudentForm').reset();
});

// Generic modal close on outside click
[resetModal, deleteModal, addStudentModal, editStudentModal, successModal].forEach(modal => {
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    }
});

// Form submissions
const addStudentForm = document.getElementById('addStudentForm');
const editStudentForm = document.getElementById('editStudentForm');

// Add Student Form Submission
addStudentForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(addStudentForm);
    
    try {
        const response = await fetch('add_student.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        if (result.success) {
            addStudentModal.classList.add('hidden');
            addStudentForm.reset();
            window.location.reload();
        } else {
            alert(result.message || 'Error adding student');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adding student');
    }
});

// Delete Confirmation Handler
confirmDelete.addEventListener('click', async () => {
    const id = document.getElementById('deleteStudentId').value;
    try {
        const response = await fetch('delete_student.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}`
        });
        
        const result = await response.json();
        if (result.success) {
            deleteModal.classList.add('hidden');
            window.location.reload();
        } else {
            alert(result.message || 'Error deleting student');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting student');
    }
});

// Search functionality
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('keyup', function() {
    const input = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
});
</script>
</body>
</html>