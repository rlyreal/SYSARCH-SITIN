<?php
session_start();
include 'db.php';

// Debugging: Ensure the database connection works
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Disable caching to always get the latest session count
$conn->query("SET SESSION query_cache_type = OFF;");

// Add this after your existing session_start() and database connection
if (isset($_GET['action']) && $_GET['action'] === 'get_points') {
    header('Content-Type: application/json');
    
    $sql = "SELECT 
        u.id_no as idno,
        CONCAT(u.last_name, ', ', u.first_name, ' ', COALESCE(u.middle_name, '')) as full_name,
        u.course,
        u.year_level,
        u.points,
        COALESCE(
            (SELECT si.session_count 
             FROM sit_in si 
             WHERE si.idno = u.id_no 
             ORDER BY si.created_at DESC 
             LIMIT 1),
            30
        ) as current_sessions
    FROM users u
    ORDER BY u.last_name, u.first_name";
    
    $result = $conn->query($sql);
    $students = array();
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $students[] = array(
                'idno' => htmlspecialchars($row['idno']),
                'full_name' => htmlspecialchars($row['full_name']),
                'course' => htmlspecialchars($row['course']),
                'year_level' => htmlspecialchars($row['year_level']),
                'points' => htmlspecialchars($row['points']),
                'current_sessions' => htmlspecialchars($row['current_sessions'])
            );
        }
    }
    
    echo json_encode(['success' => true, 'students' => $students]);
    exit;
}

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

// Replace the existing add_point handler with this updated version
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_point'])) {
    $idno = $_POST['idno'];
    $conn->begin_transaction();
    
    try {
        // First check current session count
        $stmt = $conn->prepare("SELECT session_count FROM sit_in WHERE idno = ? AND time_out IS NULL");
        $stmt->bind_param("s", $idno);
        $stmt->execute();
        $result = $stmt->get_result();
        $session_data = $result->fetch_assoc();
        $current_sessions = $session_data['session_count'] ?? 0;
        $stmt->close();

        // If already at max sessions, don't proceed
        if ($current_sessions >= 30) {
            echo json_encode([
                "success" => false,
                "maxSessionsReached" => true,
                "message" => "Maximum session limit reached"
            ]);
            exit;
        }

        // Get current points from users table
        $stmt = $conn->prepare("SELECT points FROM users WHERE id_no = ?");
        $stmt->bind_param("s", $idno);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $current_points = $user['points'] ?? 0;
        $stmt->close();
        
        // Calculate new points
        $new_points = $current_points + 1;
        
        if ($new_points >= 3) {
            // Check if adding a session would exceed max
            if ($current_sessions >= 30) {
                echo json_encode([
                    "success" => false,
                    "maxSessionsReached" => true,
                    "message" => "Maximum session limit reached"
                ]);
                $conn->rollback();
                exit;
            }
            
            // Reset points to 0
            $stmt = $conn->prepare("UPDATE users SET points = 0 WHERE id_no = ?");
            $stmt->bind_param("s", $idno);
            $stmt->execute();
            $stmt->close();
            
            // Update session count
            $stmt = $conn->prepare("UPDATE sit_in SET session_count = session_count + 1 WHERE idno = ? AND time_out IS NULL");
            $stmt->bind_param("s", $idno);
            $stmt->execute();
            $stmt->close();
            
            $message = "Points reset to 0 and gained 1 session!";
        } else {
            // Just update points
            $stmt = $conn->prepare("UPDATE users SET points = ? WHERE id_no = ?");
            $stmt->bind_param("is", $new_points, $idno);
            $stmt->execute();
            $stmt->close();
            
            $message = "Point added successfully! Current points: " . $new_points;
        }
        
        $conn->commit();
        echo json_encode([
            "success" => true, 
            "message" => $message, 
            "points" => ($new_points >= 3 ? 0 : $new_points)
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}

// Add this check when inserting new sit-in records
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_sitin'])) {
    $idno = $_POST['idno'];
    $conn->begin_transaction();
    
    try {
        // Check if user already has an active session
        $stmt = $conn->prepare("SELECT id FROM sit_in WHERE idno = ? AND time_out IS NULL");
        $stmt->bind_param("s", $idno);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("This user already has an active sit-in session!");
        }
        $stmt->close();

        // If no active session, proceed with insert
        $stmt = $conn->prepare("INSERT INTO sit_in (idno, fullname, purpose, laboratory, session_count) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $idno, $fullname, $purpose, $laboratory, $session_count);
        $stmt->execute();
        $stmt->close();
        
        $conn->commit();
        echo json_encode(["success" => true, "message" => "Sit-in session started successfully"]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Sit-in</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            themes: ["light"],
            plugins: [require("daisyui")],
        }
    </script>
</head>
<body>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    View Records
                </a>
            </li>
            <li>
                <a href="admin_reservation.php" class="btn btn-ghost text-white hover:bg-white/10">
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
                            echo '<td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium flex items-center justify-center space-x-2">';
                            // Add button
                            echo '<button class="add-point-btn p-1 hover:bg-gray-100 rounded-full transition-colors duration-200 inline-flex items-center" data-idno="' . htmlspecialchars($row['idno']) . '">';
                            echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
                            echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />';
                            echo '</svg>';
                            echo '</button>';
                            // Time Out button
                            echo '<button class="logout-btn inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200" data-id="' . $row['id'] . '">';
                            echo '<svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                            echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />';
                            echo '</svg>Time Out</button>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Student Points Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mt-8">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Student Points</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="student-points-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID Number
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Course
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Year Level
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Points
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Current Sessions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    // Replace the existing Student Points SQL query with this fixed version
                    $sql = "SELECT 
                        u.id_no as idno,
                        CONCAT(u.last_name, ', ', u.first_name, ' ', COALESCE(u.middle_name, '')) as full_name,
                        u.course,
                        u.year_level,
                        u.points,
                        COALESCE(
                            (SELECT si.session_count 
                             FROM sit_in si 
                             WHERE si.idno = u.id_no 
                             ORDER BY si.created_at DESC 
                             LIMIT 1),
                            30
                        ) as current_sessions
                    FROM users u
                    ORDER BY u.last_name, u.first_name";
                    
                    $result = $conn->query($sql);
                    
                    if (!$result) {
                        echo '<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">SQL Error: ' . $conn->error . '</td></tr>';
                    } elseif ($result->num_rows == 0) {
                        echo '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No active students found</td></tr>';
                    } else {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr class="hover:bg-gray-50 transition-colors duration-200">';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['idno']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['full_name']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['course']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['year_level']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm">';
                            echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">' 
                                . htmlspecialchars($row['points']) . '</span>';
                            echo '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm">';
                            echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">' 
                                . htmlspecialchars($row['current_sessions']) . '</span>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Replace the existing script with this version -->
<script>
// Function to update student points table
function updateStudentPoints() {
    const tbody = document.querySelector('.student-points-table tbody');
    if (!tbody) return;

    fetch('sit_in.php?action=get_points', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.students) {
            tbody.innerHTML = data.students.map(student => `
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${student.idno}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${student.full_name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${student.course}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${student.year_level}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            ${student.points}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            ${student.current_sessions}
                        </span>
                    </td>
                </tr>
            `).join('');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Update the timeout success handler in your existing script
document.addEventListener("DOMContentLoaded", function() {
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
                // Update student points table after successful timeout
                updateStudentPoints();
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

    const logoutBtn = document.getElementById('logoutBtn');
    const logoutModal = document.getElementById('logoutModal');
    const cancelLogout = document.getElementById('cancelLogout');
    const confirmLogout = document.getElementById('confirmLogout');

    // Show modal when logout button is clicked
    logoutBtn.addEventListener('click', function(e) {
        e.preventDefault();
        logoutModal.classList.remove('hidden');
    });

    // Hide modal when cancel is clicked
    cancelLogout.addEventListener('click', function() {
        logoutModal.classList.add('hidden');
    });

    // Perform logout when confirm is clicked
    confirmLogout.addEventListener('click', function() {
        window.location.href = 'logout.php';
    });

    // Close modal when clicking outside
    logoutModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    // Add max sessions popup handlers
    const maxSessionsPopup = document.getElementById('maxSessionsPopup');
    const closeMaxSessionsPopup = document.getElementById('closeMaxSessionsPopup');

    if (closeMaxSessionsPopup) {
        closeMaxSessionsPopup.addEventListener('click', function() {
            maxSessionsPopup.classList.add('hidden');
        });
    }

    // Close popup when clicking outside
    if (maxSessionsPopup) {
        maxSessionsPopup.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    }

    // Add Points Modal Event Listeners
    const addPointsModal = document.getElementById('addPointsModal');
    const closeAddPointsModal = document.getElementById('closeAddPointsModal');

    if (closeAddPointsModal) {
        closeAddPointsModal.addEventListener('click', function() {
            addPointsModal.classList.add('hidden');
            location.reload(); // Refresh the page to update points display
        });
    }

    // Close modal when clicking outside
    if (addPointsModal) {
        addPointsModal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                location.reload(); // Refresh the page to update points display
            }
        });
    }

    // Add point button handler
    document.querySelectorAll('.add-point-btn').forEach(button => {
        button.addEventListener('click', function() {
            const idno = this.getAttribute('data-idno');
            
            fetch('sit_in.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `add_point=1&idno=${idno}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success modal with message
                    document.getElementById('pointsMessage').textContent = data.message;
                    document.getElementById('addPointsModal').classList.remove('hidden');
                    // Update student points table
                    updateStudentPoints();
                } else if (data.maxSessionsReached) {
                    maxSessionsPopup.classList.remove('hidden');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request.');
            });
        });
    });
});

// Add event listener for closing max sessions popup
document.getElementById('closeMaxSessionsPopup').addEventListener('click', function() {
    document.getElementById('maxSessionsPopup').classList.add('hidden');
});

// Close popup when clicking outside
document.getElementById('maxSessionsPopup').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

// Add event listener for closing add points modal
document.getElementById('closeAddPointsModal').addEventListener('click', function() {
    document.getElementById('addPointsModal').classList.add('hidden');
    window.location.reload(); // Refresh the page to show updated points
});

// Close modal when clicking outside
document.getElementById('addPointsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
        window.location.reload();
    }
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

<!-- Add max sessions popup -->
<div id="maxSessionsPopup" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Maximum Sessions Reached</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Cannot add more sessions. Maximum limit of 30 sessions reached for this user.</p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="closeMaxSessionsPopup" 
                    class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Understood
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Points Success Modal -->
<div id="addPointsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Points Added!</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="pointsMessage"></p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="closeAddPointsModal" 
                    class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
