<?php
include 'db.php';

$sit_in = null;
$message = "";
$showModal = false;

if (isset($_POST['search']) && !empty($_POST['idno'])) {
    $idno = trim($_POST['idno']);

    // Fetch student details from users table
    $stmt = $conn->prepare("SELECT id_no, first_name, last_name FROM users WHERE id_no = ?");
    $stmt->bind_param("s", $idno);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        // Fetch last sit-in record
        $stmt = $conn->prepare("SELECT id, session_count FROM sit_in WHERE idno = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("s", $idno);
        $stmt->execute();
        $result = $stmt->get_result();
        $last_record = $result->fetch_assoc();
        $stmt->close();

        $session_count = $last_record['session_count'] ?? 30; // Default session count

        $sit_in = [
            'id' => $last_record['id'] ?? null,
            'idno' => $user['id_no'],
            'fullname' => $user['last_name'] . ', ' . $user['first_name'],
            'remaining_sessions' => $session_count
        ];
        
        $showModal = true;
    } else {
        $message = '<div class="alert alert-danger">User not found!</div>';
    }
}

// Replace the sit_in_submit handler with this code
if (isset($_POST['sit_in_submit']) && !empty($_POST['idno'])) {
    $idno = trim($_POST['idno']);
    $purpose = trim($_POST['purpose']);
    $laboratory = trim($_POST['laboratory']);
    $status = "active";

    // Get the last record with its session count
    $stmt = $conn->prepare("SELECT id, session_count FROM sit_in WHERE idno = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $idno);
    $stmt->execute();
    $result = $stmt->get_result();
    $last_record = $result->fetch_assoc();
    $stmt->close();

    if ($last_record) {
        // Use the existing session count
        $session_count = $last_record['session_count'];

        // Update the existing record instead of creating a new one
        $stmt = $conn->prepare("UPDATE sit_in SET 
            purpose = ?,
            laboratory = ?,
            time_in = NOW(),
            time_out = NULL,
            status = ?,
            date = CURRENT_DATE()
            WHERE id = ?");
        
        $stmt->bind_param("sssi", $purpose, $laboratory, $status, $last_record['id']);

        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Sit-in updated successfully! Remaining Sessions: ' . $session_count . '</div>';
        } else {
            $message = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
        }
        $stmt->close();
    } else {
        // First time sit-in, create new record with default 30 sessions
        $session_count = 30;
        $stmt = $conn->prepare("INSERT INTO sit_in (idno, fullname, purpose, laboratory, status, session_count, time_in, date) 
                               VALUES (?, ?, ?, ?, ?, ?, NOW(), CURRENT_DATE())");
        $stmt->bind_param("sssssi", $idno, $_POST['fullname'], $purpose, $laboratory, $status, $session_count);

        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">New sit-in recorded successfully! Sessions: 30</div>';
        } else {
            $message = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search & Sit-in</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-gray-100">
    
    <!-- ✅ Admin Navbar -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <!-- Logo Section -->
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-2xl font-bold text-white">College of Computer Studies Admin</span>
            </div>

            <!-- Center Navigation Links -->
            <div class="flex-1 flex justify-center">
                <ul class="flex items-center space-x-6">
                    <li>
                        <a href="admin_dashboard.php" 
                           class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="search.php" 
                           class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>Search</span>
                        </a>
                    </li>
                    <li>
                        <a href="students.php" 
                           class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
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
                    <li>
                        <a href="reports.php" 
                           class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
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
                <a href="logout.php" 
                   class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Log out</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Rest of your search.php content -->

<div class="container mx-auto px-4 py-8">
    <!-- Search Form Card -->
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6 mb-6">
        <?= $message ?>
        
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Search Student</h2>
        <form method="POST" action="" class="space-y-4">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700">Enter Student ID</label>
                <input type="text" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                       name="idno" 
                       required>
            </div>
            <button type="submit" 
                    name="search" 
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                Search
            </button>
        </form>
    </div>

    <!-- Sit-in Form Card -->
    <?php if ($sit_in): ?>
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Sit-in Form</h2>
        <form action="" method="POST" class="space-y-4">
            <input type="hidden" name="idno" value="<?= htmlspecialchars($sit_in['idno']) ?>">
            <input type="hidden" name="fullname" value="<?= htmlspecialchars($sit_in['fullname']) ?>">

            <!-- Student Info -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID Number</label>
                    <input type="text" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" 
                           value="<?= htmlspecialchars($sit_in['idno']) ?>" 
                           disabled>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student Name</label>
                    <input type="text" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" 
                           value="<?= htmlspecialchars($sit_in['fullname']) ?>" 
                           disabled>
                </div>
            </div>

            <!-- Purpose & Laboratory -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purpose</label>
                    <select name="purpose" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                            required>
                        <option value="">Select Purpose</option>
                        <option value="Research">Research</option>
                        <option value="Assignment">Assignment</option>
                        <option value="Project">Project</option>
                        <option value="Practice">Practice</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Laboratory</label>
                    <select name="laboratory" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                            required>
                        <option value="">Select Laboratory</option>
                        <option value="ComLab 1">ComLab 1</option>
                        <option value="ComLab 2">ComLab 2</option>
                        <option value="ComLab 3">ComLab 3</option>
                        <option value="ComLab 4">ComLab 4</option>
                    </select>
                </div>
            </div>

            <!-- Remaining Sessions -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Remaining Sessions</label>
                <input type="text" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" 
                       value="<?= htmlspecialchars($sit_in['remaining_sessions']) ?>" 
                       disabled>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    name="sit_in_submit" 
                    class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200">
                Start Sit-in Session
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>

<script>
    function timeOutStudent() {
        if (confirm("Are you sure you want to time out this student?")) {
            fetch("sit_in.php", {
                // ...existing code...
            })
        }
    }
</script>

</body>
</html>
