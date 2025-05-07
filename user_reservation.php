<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Add this before processing the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user has active session
    $stmt = $conn->prepare("SELECT id FROM sit_in WHERE idno = ? AND time_out IS NULL");
    $stmt->bind_param("s", $id_no);
    $stmt->execute();
    $active_session = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($active_session) {
        echo "<script>document.getElementById('activeSessionModal').classList.remove('hidden');</script>";
        exit;
    }

    // Check session count
    $stmt = $conn->prepare("SELECT session_count FROM sit_in WHERE idno = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $id_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $session_data = $result->fetch_assoc();
    $current_sessions = $session_data ? $session_data['session_count'] : 30;

    if ($current_sessions <= 0) {
        echo "<script>alert('You have no sessions remaining.');</script>";
        exit;
    }

    $id_no = $_POST['id_number'];
    $full_name = $_POST['full_name'];
    $course = $_POST['course'];
    $year_level = $_POST['year_level'];
    $purpose = $_POST['purpose'];
    $laboratory = $_POST['laboratory'];
    $date = $_POST['date'];
    $time_in = $_POST['time_in'];
    $pc_number = $_POST['pc_number'];
    $status = $_POST['status'];

    // Insert into reservations table
    $stmt = $conn->prepare("INSERT INTO reservations (idno, full_name, course, year_level, purpose, laboratory, date, time_in, pc_number, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssssssss", $id_no, $full_name, $course, $year_level, $purpose, $laboratory, $date, $time_in, $pc_number, $status);
    
    if ($stmt->execute()) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('successModal').classList.remove('hidden');
            });
            
            document.getElementById('closeSuccessModal').addEventListener('click', function() {
                document.getElementById('successModal').classList.add('hidden');
                window.location.href = 'dashboard.php'; // Optional: redirect after closing
            });
            
            document.getElementById('successModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                    window.location.href = 'dashboard.php'; // Optional: redirect after closing
                }
            });
        </script>";
    } else {
        echo "<script>alert('Error submitting reservation.');</script>";
    }
    $stmt->close();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id_no, last_name, first_name, middle_name, course, year_level FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($id_no, $last_name, $first_name, $middle_name, $course, $year_level);
$stmt->fetch();
$stmt->close();

// Format full name
$full_name = "$last_name, $first_name " . ($middle_name ? "$middle_name" : "");

// Fetch session count
$stmt = $conn->prepare("SELECT 
    CASE 
        WHEN EXISTS (SELECT 1 FROM sit_in WHERE idno = ? AND time_out IS NULL)
        THEN 'Active Session'
        ELSE COALESCE((SELECT session_count FROM sit_in WHERE idno = ? ORDER BY id DESC LIMIT 1), 30)
    END as session_status");
$stmt->bind_param("ss", $id_no, $id_no);
$stmt->execute();
$stmt->bind_result($session_status);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratory Reservation Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <div class="navbar bg-[#2c343c] shadow-lg">
        <div class="navbar-start">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-xl font-bold text-white ml-2">Dashboard</span>
            </div>
        </div>
        
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 gap-2">
                <li>
                    <a href="dashboard.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <a href="#" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Notifications
                    </a>
                </li>
                <li>
                    <a href="editprofile.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Profile
                    </a>
                </li>
                <li>
                    <a href="history.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        History
                    </a>
                </li>
                <li>
                    <a href="#" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reservation
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="navbar-end">
            <a href="logout.php" class="btn btn-error btn-outline gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </a>
        </div>
    </div>

    <!-- Existing content with adjusted top margin -->
    <div class="container mx-auto px-2 py-4 max-w-6xl mt-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Left side - Registration Form -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="bg-[#2c343c] text-white py-3 px-4 rounded-t-lg">
                    <h3 class="text-lg font-semibold text-center">Laboratory Reservation Form</h3>
                </div>
                <div class="p-4">
                    <form action="" method="POST">
                        <div class="grid grid-cols-1 gap-3 mb-3">
                            <div>
                                <label for="id_number" class="block text-gray-700 mb-1 text-sm">ID Number</label>
                                <input type="text" 
                                    class="w-full px-3 py-1.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-gray-100" 
                                    id="id_number" 
                                    name="id_number" 
                                    value="<?php echo htmlspecialchars($id_no); ?>" 
                                    readonly
                                    required>
                            </div>
                            <div>
                                <label for="full_name" class="block text-gray-700 mb-1 text-sm">Full Name</label>
                                <input type="text" 
                                    class="w-full px-3 py-1.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-gray-100" 
                                    id="full_name" 
                                    name="full_name" 
                                    value="<?php echo htmlspecialchars($full_name); ?>" 
                                    readonly
                                    required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label for="course" class="block text-gray-700 mb-1 text-sm">Course</label>
                                <input type="text" 
                                    class="w-full px-3 py-1.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-gray-100" 
                                    id="course" 
                                    name="course" 
                                    value="<?php echo htmlspecialchars($course); ?>" 
                                    readonly
                                    required>
                            </div>
                            <div>
                                <label for="year_level" class="block text-gray-700 mb-1 text-sm">Year Level</label>
                                <input type="text" 
                                    class="w-full px-3 py-1.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-gray-100" 
                                    id="year_level" 
                                    name="year_level" 
                                    value="<?php echo htmlspecialchars($year_level); ?>" 
                                    readonly
                                    required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="purpose" class="block text-gray-700 mb-1 text-sm">Purpose</label>
                            <select class="w-full px-3 py-1.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" 
                                id="purpose" 
                                name="purpose" 
                                required>
                                <option value="" disabled selected>Select Purpose</option>
                                <option value="C Programming">C Programming</option>
                                <option value="C++ Programming">C++ Programming</option>
                                <option value="C# Programming">C# Programming</option>
                                <option value="Java Programming">Java Programming</option>
                                <option value="Python Programming">Python Programming</option>
                                <option value="Database">Database</option>
                                <option value="Digital Logic & Design">Digital Logic & Design</option>
                                <option value="Embedded System & IOT">Embedded System & IOT</option>
                                <option value="System Integration & Architecture">System Integration & Architecture</option>
                                <option value="Computer Application">Computer Application</option>
                                <option value="Web Design & Development">Web Design & Development</option>
                                <option value="Project Management">Project Management</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label for="laboratory" class="block text-gray-700 mb-1 text-sm">Laboratory</label>
                                <select class="w-full px-3 py-1.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" 
                                    id="laboratory" 
                                    name="laboratory" 
                                    required 
                                    onchange="updatePcOptions()">
                                    <option value="" disabled selected>Select Laboratory</option>
                                    <?php foreach ([517, 524, 526, 528, 530, 542, 544] as $labNumber): ?>
                                        <option value="<?php echo $labNumber; ?>">Laboratory <?php echo $labNumber; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="date" class="block text-gray-700 mb-1 text-sm">Date</label>
                                <input type="date" class="w-full px-3 py-1.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" 
                                    id="date" name="date" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div>
                                <label for="time_in" class="block text-gray-700 mb-1 text-sm">Time In</label>
                                <input type="time" 
                                    class="w-full px-3 py-1.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" 
                                    id="time_in" 
                                    name="time_in" 
                                    required>
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-1 text-sm">Sessions</label>
                                <div class="px-3 py-1.5 border rounded-lg bg-gray-100 text-sm">
                                    <?php if ($session_status === 'Active Session'): ?>
                                        <span class="text-blue-600">Currently in session</span>
                                    <?php else: ?>
                                        <?php echo $session_status; ?> sessions remaining
                                        <?php if ($session_status <= 0): ?>
                                            <p class="text-red-500 text-xs mt-1">No sessions remaining</p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right side - PC Selection -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="bg-[#2c343c] text-white py-3 px-4 rounded-t-lg">
                    <h3 class="text-lg font-semibold text-center">Choose PC</h3>
                </div>
                <div id="pcSelection" class="p-4 h-[500px] overflow-y-auto">
                    <!-- PC grid will be inserted here by JavaScript -->
                    <div class="text-center text-gray-500 py-8">
                        Please fill out and submit the reservation form first
                    </div>
                </div>
                <div class="p-4 border-t text-center">
                    <button id="finalConfirm" 
                        class="bg-[#2c343c] text-white px-6 py-2 rounded-lg hover:bg-[#3a424b] focus:outline-none focus:ring-2 focus:ring-[#2c343c] focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                        Confirm PC Selection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add the logout modal at the end of body -->
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

    <!-- Add this HTML before the closing </body> tag -->
    <div id="activeSessionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Active Session Found</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">You currently have an active session. Please complete your current session before making a new reservation.</p>
                </div>
                <div class="flex justify-center mt-3">
                    <button id="closeActiveSessionModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                        Okay, Got it
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this before the closing </body> tag -->
    <div id="confirmationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Confirm Reservation</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to confirm this reservation?</p>
                    <div class="mt-2 text-sm text-left">
                        <p><span class="font-medium">Laboratory:</span> <span id="confirmLab"></span></p>
                        <p><span class="font-medium">PC Number:</span> <span id="confirmPC"></span></p>
                        <p><span class="font-medium">Date:</span> <span id="confirmDate"></span></p>
                        <p><span class="font-medium">Time:</span> <span id="confirmTime"></span></p>
                    </div>
                </div>
                <div class="flex justify-center gap-4 mt-3">
                    <button id="cancelConfirmation" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-md">
                        Cancel
                    </button>
                    <button id="proceedConfirmation" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this before closing </body> tag -->
    <div id="successModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Success!</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Reservation submitted successfully! Waiting for admin approval.</p>
                </div>
                <div class="flex justify-center mt-3">
                    <button id="closeSuccessModal" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md">
                        Okay
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Logout modal functionality
        document.querySelector('a[href="logout.php"]').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('logoutModal').classList.remove('hidden');
        });

        document.getElementById('cancelLogout').addEventListener('click', function() {
            document.getElementById('logoutModal').classList.add('hidden');
        });

        document.getElementById('confirmLogout').addEventListener('click', function() {
            window.location.href = 'logout.php';
        });

        document.getElementById('logoutModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Replace or update the existing form submit handler
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const finalConfirmButton = document.querySelector('#finalConfirm');
            const requiredFields = form.querySelectorAll('[required]');
            
            // Function to check if all fields are filled
            function checkFormValidity() {
                let allFieldsFilled = true;
                requiredFields.forEach(field => {
                    if (!field.value) {
                        allFieldsFilled = false;
                    }
                });
                
                // If PC is selected but form isn't complete, disable the final confirm button
                const selectedPc = document.querySelector('.pc-button.bg-purple-200');
                finalConfirmButton.disabled = !allFieldsFilled || !selectedPc;
                
                return allFieldsFilled;
            }

            // Add input event listeners to all required fields
            requiredFields.forEach(field => {
                field.addEventListener('input', checkFormValidity);
                field.addEventListener('change', checkFormValidity);
            });

            // Update the selectPc function
            window.selectPc = function(pcNumber) {
                const buttons = document.querySelectorAll('.pc-button');
                buttons.forEach(btn => btn.classList.remove('bg-purple-200', 'border-purple-500'));
                event.currentTarget.classList.add('bg-purple-200', 'border-purple-500');
                
                // Only enable final confirm if form is valid
                finalConfirmButton.disabled = !checkFormValidity();
            }

            // Update form submit handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!checkFormValidity()) {
                    alert('Please fill in all required fields before selecting a PC.');
                    return;
                }
                updatePcOptions();
            });
        });

        function updatePcOptions() {
            const selectedLab = document.getElementById('laboratory').value;
            const pcSelectionDiv = document.querySelector('#pcSelection');
            
            if (!selectedLab) {
                pcSelectionDiv.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        Please select a laboratory first
                    </div>
                `;
                return;
            }
            
            // Show loading state
            pcSelectionDiv.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    Loading PC availability...
                </div>
            `;
            
            // Fetch active PC usage via AJAX
            fetch(`get_active_pcs.php?laboratory=${selectedLab}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let pcGrid = `
                            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                ${generatePcGrid(30, data.active_pcs)}
                            </div>
                        `;
                        pcSelectionDiv.innerHTML = pcGrid;
                    } else {
                        pcSelectionDiv.innerHTML = `
                            <div class="text-center text-red-500 py-8">
                                ${data.message || 'Error loading PC status'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    pcSelectionDiv.innerHTML = `
                        <div class="text-center text-red-500 py-8">
                            Error loading PC status. Please try again.
                        </div>
                    `;
                });
        }

        // Update the event listeners to call updatePcOptions when date or time changes
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('date');
            const timeInput = document.getElementById('time_in');
            const labInput = document.getElementById('laboratory');
            
            dateInput.addEventListener('change', updatePcOptions);
            timeInput.addEventListener('change', updatePcOptions);
            labInput.addEventListener('change', updatePcOptions);
        });

        function generatePcGrid(numberOfPcs, activePcs) {
            let grid = '';
            for (let i = 1; i <= numberOfPcs; i++) {
                const isInUse = activePcs.includes(i.toString());
                grid += `
                    <div class="text-center">
                        <button type="button" 
                            class="pc-button w-full p-2 rounded-lg border ${isInUse ? 'bg-gray-200 cursor-not-allowed' : 'hover:bg-purple-100'} 
                            focus:outline-none focus:ring-2 focus:ring-purple-500"
                            onclick="${isInUse ? '' : `selectPc(${i})`}"
                            ${isInUse ? 'disabled' : ''}>
                            <svg class="w-8 h-8 mx-auto mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm">PC ${i}</span>
                            <span class="block text-xs ${isInUse ? 'text-red-500 font-bold' : 'text-green-500'}">
                                ${isInUse ? 'UNAVAILABLE' : 'AVAILABLE'}
                            </span>
                        </button>
                    </div>
                `;
            }
            return grid;
        }

        function selectPc(pcNumber) {
            // Add selected state to PC
            const buttons = document.querySelectorAll('.pc-button');
            buttons.forEach(btn => btn.classList.remove('bg-purple-200', 'border-purple-500'));
            
            event.currentTarget.classList.add('bg-purple-200', 'border-purple-500');
            
            // Enable final confirmation button
            document.querySelector('#finalConfirm').disabled = false;
        }

        // Replace the existing finalConfirm click handler with this
        document.getElementById('finalConfirm').addEventListener('click', function() {
            // Check if user has active session
            if (document.querySelector('.text-blue-600')?.textContent === 'Currently in session') {
                document.getElementById('activeSessionModal').classList.remove('hidden');
                return;
            }
            
            const selectedPc = document.querySelector('.pc-button.bg-purple-200')?.querySelector('.text-sm')?.textContent;
            const laboratory = document.getElementById('laboratory').value;
            const date = document.getElementById('date').value;
            const time = document.getElementById('time_in').value;
            
            // Update confirmation modal with details
            document.getElementById('confirmLab').textContent = 'Laboratory ' + laboratory;
            document.getElementById('confirmPC').textContent = selectedPc;
            document.getElementById('confirmDate').textContent = new Date(date).toLocaleDateString();
            document.getElementById('confirmTime').textContent = time;
            
            // Show confirmation modal
            document.getElementById('confirmationModal').classList.remove('hidden');
        });

        // Add modal button handlers
        document.getElementById('cancelConfirmation').addEventListener('click', function() {
            document.getElementById('confirmationModal').classList.add('hidden');
        });

        document.getElementById('proceedConfirmation').addEventListener('click', function() {
            const form = document.querySelector('form');
            const selectedPc = document.querySelector('.pc-button.bg-purple-200')?.querySelector('.text-sm')?.textContent;
            
            // Create hidden input for PC number
            const pcInput = document.createElement('input');
            pcInput.type = 'hidden';
            pcInput.name = 'pc_number';
            pcInput.value = selectedPc?.replace('PC ', '') || '';
            form.appendChild(pcInput);
            
            // Create hidden input for status
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = 'pending';
            form.appendChild(statusInput);
            
            // Submit the form
            form.submit();
        });

        // Close modal when clicking outside
        document.getElementById('confirmationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Add modal close handler
        document.getElementById('closeActiveSessionModal').addEventListener('click', function() {
            document.getElementById('activeSessionModal').classList.add('hidden');
        });

        // Close modal when clicking outside
        document.getElementById('activeSessionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Add modal close handler for success modal
        document.getElementById('closeSuccessModal').addEventListener('click', function() {
            document.getElementById('successModal').classList.add('hidden');
        });
    </script>
</body>
</html>