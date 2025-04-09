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

    // Replace the existing active sit-in check with this:
    if ($user) {
        // Check for active sit-in status with no time_out
        $stmt = $conn->prepare("SELECT status FROM sit_in WHERE idno = ? AND status = 'active' AND time_out IS NULL LIMIT 1");
        $stmt->bind_param("s", $idno);
        $stmt->execute();
        $active_result = $stmt->get_result();
        $active_sitin = $active_result->fetch_assoc();
        $stmt->close();

        if ($active_sitin) {
            $message = '<div class="hidden" id="activeSitInError">active</div>';
            $showModal = false;
        } else {
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
        }
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

        // Replace the success message in the sit_in_submit handler
        if ($stmt->execute()) {
            $message = '<div class="hidden" id="sitInSuccess" data-sessions="' . $session_count . '">success</div>';
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

        // Replace the success message in the sit_in_submit handler
        if ($stmt->execute()) {
            $message = '<div class="hidden" id="sitInSuccess" data-sessions="' . $session_count . '">success</div>';
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
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            themes: ["light"],
            plugins: [require("daisyui")],
        }
    </script>
</head>
<body class="bg-gray-100">
    
    <!-- ✅ Admin Navbar -->
    <div class="navbar bg-[#2c343c] shadow-lg">
        <div class="navbar-start">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-xl font-bold text-white ml-2">Admin</span>
            </div>
        </div>
        
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 gap-2">
                <li>
                    <a href="admin_dashboard.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <a href="search.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                    </a>
                </li>
                <li>
                    <a href="students.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Students
                    </a>
                </li>
                <li>
                    <a href="sit_in.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Sit-in
                    </a>
                </li>
                <li>
                    <a href="sit_in_records.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        View Records
                    </a>
                </li>
                <li>
                    <a href="reservation.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reservation
                    </a>
                </li>
                <li>
                    <a href="reports.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Reports
                    </a>
                </li>
                <li>
                    <a href="feedback.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        Feedback Reports
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="navbar-end">
            <button id="logoutBtn" class="btn btn-error btn-outline gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </div>
    </div>

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
                        <option value="C# Programming">C# Programming</option>
                        <option value="C Programming">C Programming</option>
                        <option value="Java Programming">Java Programming</option>
                        <option value="ASP.Net Programming">ASP.Net Programming</option>
                        <option value="Php Programming">Php Programming</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Laboratory</label>
                    <select name="laboratory" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                            required>
                        <option value="">Select Laboratory</option>
                        <option value="524">524</option>
                        <option value="526">526</option>
                        <option value="528">528</option>
                        <option value="530">530</option>
                        <option value="542">542</option>
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

<!-- Add this before </body> -->
<div id="popup" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Cannot Start Sit-in</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    This student already has an active sit-in session. Please time-out the current session before starting a new one.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="closePopup" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add this new success popup div before </body> -->
<div id="successPopup" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Success!</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="successMessage"></p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="closeSuccessPopup" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Logout confirmation modal -->
<div id="logoutModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Confirm Logout</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Are you sure you want to logout?</p>
            </div>
            <div class="flex justify-center gap-4 mt-3">
                <button id="cancelLogout" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-md">
                    Cancel
                </button>
                <button id="confirmLogout" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md">
                    Logout
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const errorDiv = document.getElementById('activeSitInError');
        const popup = document.getElementById('popup');
        const closePopup = document.getElementById('closePopup');

        // Only show popup if there's an active error and time_out is null
        if (errorDiv && errorDiv.textContent === 'active') {
            popup.classList.remove('hidden');
        }

        closePopup.addEventListener('click', function() {
            popup.classList.add('hidden');
        });

        // Close popup when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === popup) {
                popup.classList.add('hidden');
            }
        });
    });
</script>

<script>
    function timeOutStudent() {
        if (confirm("Are you sure you want to time out this student?")) {
            fetch("sit_in.php", {
                // ...existing code...
            })
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... existing popup code ...

        // Success popup handling
        const successDiv = document.getElementById('sitInSuccess');
        const successPopup = document.getElementById('successPopup');
        const closeSuccessPopup = document.getElementById('closeSuccessPopup');
        const successMessage = document.getElementById('successMessage');

        if (successDiv) {
            const sessions = successDiv.getAttribute('data-sessions');
            successMessage.textContent = `Sit-in session started successfully! You have ${sessions} remaining sessions.`;
            successPopup.classList.remove('hidden');
        }

        closeSuccessPopup.addEventListener('click', function() {
            successPopup.classList.add('hidden');
            window.location.href = 'search.php'; // Optional: refresh the page
        });

        // Close success popup when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === successPopup) {
                successPopup.classList.add('hidden');
                window.location.href = 'search.php'; // Optional: refresh the page
            }
        });

        // Logout modal functionality
        const logoutBtn = document.getElementById('logoutBtn');
        const logoutModal = document.getElementById('logoutModal');
        const cancelLogout = document.getElementById('cancelLogout');
        const confirmLogout = document.getElementById('confirmLogout');

        // Show modal when logout button is clicked
        logoutBtn.addEventListener('click', function() {
            logoutModal.classList.remove('hidden');
        });

        // Handle cancel button
        cancelLogout.addEventListener('click', function() {
            logoutModal.classList.add('hidden');
        });

        // Handle confirm button
        confirmLogout.addEventListener('click', function() {
            window.location.href = 'logout.php';
        });

        // Close modal when clicking outside
        logoutModal.addEventListener('click', function(e) {
            if (e.target === logoutModal) {
                logoutModal.classList.add('hidden');
            }
        });
    });
</script>

</body>
</html>
