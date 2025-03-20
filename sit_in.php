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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
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

<!-- Replace the existing table container section with this fixed version -->
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Current Sit-in Sessions</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID Number
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Purpose
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sit Lab
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Session
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    // Alternative version using created_at timestamp
                    $sql = "SELECT id, idno, fullname, purpose, laboratory, session_count 
                            FROM sit_in 
                            WHERE time_out IS NULL 
                            ORDER BY created_at DESC, id DESC";
                    
                    $result = $conn->query($sql);
                    
                    // Update colspan from 6 to 7 in error messages
                    if (!$result) {
                        echo '<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">SQL Error: ' . $conn->error . '</td></tr>';
                    } elseif ($result->num_rows == 0) {
                        echo '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No active sit-in sessions found</td></tr>';
                    } else {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr id="row-' . $row['id'] . '" class="hover:bg-gray-50 transition-colors duration-200">';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['idno']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['fullname']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['purpose']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['laboratory']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">';
                            echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">' 
                                . htmlspecialchars($row['session_count']) . '</span>';
                            echo '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">';
                            echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Active</span>';
                            echo '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">';
                            echo '<button class="logout-btn inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200" data-id="' . $row['id'] . '">';
                            echo '<svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                            echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />';
                            echo '</svg>Time Out</button>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Replace the existing script with this version -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const confirmPopup = document.getElementById("confirmPopup");
    const timeoutPopup = document.getElementById("timeoutPopup");
    const timeoutMessage = document.getElementById("timeoutMessage");
    const closeTimeoutPopup = document.getElementById("closeTimeoutPopup");
    const confirmTimeOut = document.getElementById("confirmTimeOut");
    const cancelTimeOut = document.getElementById("cancelTimeOut");
    
    let currentLogoutId = null;
    let currentRow = null;

    document.querySelectorAll(".logout-btn").forEach(button => {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            currentLogoutId = this.getAttribute("data-id");
            currentRow = document.getElementById("row-" + currentLogoutId);
            confirmPopup.classList.remove('hidden');
        });
    });

    confirmTimeOut.addEventListener("click", function() {
        confirmPopup.classList.add('hidden');
        
        fetch("sit_in.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "logout_id=" + currentLogoutId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentRow.remove();
                timeoutMessage.textContent = data.message;
                timeoutPopup.classList.remove('hidden');
            } else {
                timeoutMessage.textContent = "Error: " + data.message;
                timeoutPopup.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error("Error:", error);
            timeoutMessage.textContent = "An error occurred while processing your request.";
            timeoutPopup.classList.remove('hidden');
        });
    });

    cancelTimeOut.addEventListener("click", function() {
        confirmPopup.classList.add('hidden');
    });

    // Close confirm popup when clicking outside
    confirmPopup.addEventListener("click", function(e) {
        if (e.target === confirmPopup) {
            confirmPopup.classList.add('hidden');
        }
    });

    // Close timeout popup when clicking continue button
    closeTimeoutPopup.addEventListener("click", function() {
        timeoutPopup.classList.add('hidden');
    });

    // Close timeout popup when clicking outside
    timeoutPopup.addEventListener("click", function(e) {
        if (e.target === timeoutPopup) {
            timeoutPopup.classList.add('hidden');
        }
    });
});
</script>

<!-- Add this before the timeoutPopup div -->
<div id="confirmPopup" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Confirm Time Out</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Are you sure you want to time out this student?</p>
            </div>
            <div class="items-center px-4 py-3 flex space-x-4">
                <button id="confirmTimeOut" 
                        class="flex-1 px-4 py-2 bg-yellow-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    Yes
                </button>
                <button id="cancelTimeOut"
                        class="flex-1 px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add this before </body> -->
<div id="timeoutPopup" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Success!</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="timeoutMessage"></p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="closeTimeoutPopup" 
                        class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
