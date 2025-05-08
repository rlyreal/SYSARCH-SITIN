<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

// Handle approval/disapproval
if (isset($_POST['action']) && isset($_POST['reservation_id'])) {
    $action = $_POST['action'];
    $reservation_id = $_POST['reservation_id'];
    
    if ($action === 'approve') {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // First get the reservation details
            $stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ?");
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $reservation = $result->fetch_assoc();
            $stmt->close();
            
            if ($reservation) {
                // Check if user is currently sitting in
                $stmt = $conn->prepare("SELECT id FROM sit_in WHERE idno = ? AND time_out IS NULL");
                $stmt->bind_param("s", $reservation['idno']);
                $stmt->execute();
                $active_session = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($active_session) {
                    throw new Exception("User already has an active session!");
                }

                // Get user's remaining sessions
                $stmt = $conn->prepare("SELECT session_count FROM sit_in WHERE idno = ? ORDER BY id DESC LIMIT 1");
                $stmt->bind_param("s", $reservation['idno']);
                $stmt->execute();
                $result = $stmt->get_result();
                $session_data = $result->fetch_assoc();
                $current_sessions = $session_data ? $session_data['session_count'] : 30;
                $stmt->close();

                if ($current_sessions <= 0) {
                    throw new Exception("No sessions remaining!");
                }

                // Insert into sit_in table
                $stmt = $conn->prepare("INSERT INTO sit_in (idno, fullname, purpose, laboratory, pc_number, time_in, session_count, created_at) VALUES (?, ?, ?, ?, ?, NOW(), ?, NOW())");
                $stmt->bind_param("sssssi", 
                    $reservation['idno'],
                    $reservation['full_name'],
                    $reservation['purpose'],
                    $reservation['laboratory'],
                    $reservation['pc_number'],
                    $current_sessions
                );
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to insert into sit_in table: " . $stmt->error);
                }
                $stmt->close();

                // Update reservation status
                $status = 'approved';
                $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $reservation_id);
                
                if ($stmt->execute()) {
                    $conn->commit();
                    echo json_encode([
                        "success" => true,
                        "message" => "Reservation approved successfully"
                    ]);
                    echo "<script>
                        document.getElementById('approvalSuccessModal').classList.remove('hidden');
                    </script>";
                } else {
                    throw new Exception("Failed to update reservation status");
                }
                $stmt->close();

                // Insert into reservation_logs
                $stmt = $conn->prepare("INSERT INTO reservation_logs (
                    reservation_id, idno, full_name, course, year_level, 
                    purpose, laboratory, date, time_in, pc_number, 
                    status, action_type, action_by, action_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                
                $action_type = "Approved";
                $stmt->bind_param("isssssssssssi", 
                    $reservation_id,
                    $reservation['idno'],
                    $reservation['full_name'],
                    $reservation['course'],
                    $reservation['year_level'],
                    $reservation['purpose'],
                    $reservation['laboratory'],
                    $reservation['date'],
                    $reservation['time_in'],
                    $reservation['pc_number'],
                    $status,
                    $action_type,
                    $_SESSION['admin_id']
                );
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to insert into reservation_logs: " . $stmt->error);
                }
                $stmt->close();

                $conn->commit();
                echo json_encode([
                    "success" => true,
                    "message" => "Reservation approved and added to sit-in sessions."
                ]);
                exit;
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode([
                "success" => false,
                "message" => "Error: " . $e->getMessage()
            ]);
            exit;
        }
    } else {
        // Handle disapproval
        $status = 'disapproved';
        $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $reservation_id);
        $stmt->execute();
        $stmt->close();
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showModal('disapprove', 'Reservation has been disapproved.');
            });
        </script>";
    }
}

// Fetch pending reservations
$stmt = $conn->prepare("SELECT * FROM reservations WHERE status = 'pending' ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Reservation Approval</title>
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
    <div class="navbar bg-[#2c343c] shadow-lg">
        <div class="navbar-start">
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full ring ring-gray-400 ring-offset-base-100 ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-full w-full text-white p-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="background-color: #2c343c;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <li>
                        <a class="justify-between">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                            <span class="badge badge-primary">Admin</span>
                        </a>
                    </li>   
                    <li><a>Settings</a></li>
                </ul>
            </div>
            <span class="text-xl font-bold text-white ml-2">Admin</span>
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
            <a href="logout.php" class="btn btn-error btn-outline gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </a>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Pending Reservations</h1>

        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Laboratory</label>
                    <select id="labFilter" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        <option value="">All Laboratories</option>
                        <option value="517">517</option>
                        <option value="524">524</option>
                        <option value="526">526</option>
                        <option value="528">528</option>
                        <option value="530">530</option>
                        <option value="542">542</option>
                        <option value="544">544</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" id="searchFilter" placeholder="Search name or ID..." 
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="flex items-end">
                    <button onclick="resetFilters()" 
                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 text-sm">
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PC Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo htmlspecialchars($row['idno']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Laboratory <?php echo htmlspecialchars($row['laboratory']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">PC <?php echo htmlspecialchars($row['pc_number']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php echo htmlspecialchars($row['date']); ?><br>
                            <?php echo htmlspecialchars($row['time_in']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo htmlspecialchars($row['purpose']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <form method="POST" class="flex gap-2">
                                <input type="hidden" name="reservation_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="action" value="approve" 
                                    class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                    Approve
                                </button>
                                <button type="submit" name="action" value="disapprove" 
                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                    Disapprove
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php
        // Fetch reservation logs
        $logs_query = $conn->prepare("SELECT rl.*, a.username as admin_name 
                  FROM reservation_logs rl 
                  LEFT JOIN admin a ON rl.action_by = a.id 
                  ORDER BY rl.action_date DESC");
        $logs_query->execute();
        $logs_result = $logs_query->get_result();
        ?>

        <h1 class="text-2xl font-bold mb-6">Reservation Logs</h1>
        
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="max-h-[500px] overflow-y-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PC Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($log = $logs_result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php echo date('M d, Y h:i A', strtotime($log['action_date'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php echo htmlspecialchars($log['idno']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php echo htmlspecialchars($log['full_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                Laboratory <?php echo htmlspecialchars($log['laboratory']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                PC <?php echo htmlspecialchars($log['pc_number']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $log['action_type'] === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo htmlspecialchars($log['action_type']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php echo htmlspecialchars($log['admin_name']); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="successModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3 text-center">
                <div id="modalIcon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4">
                </div>
                <h3 id="modalTitle" class="text-lg leading-6 font-medium text-gray-900"></h3>
                <div class="mt-2 px-7 py-3">
                    <p id="modalMessage" class="text-sm text-gray-500"></p>
                </div>
                <div class="flex justify-center mt-3">
                    <button onclick="closeModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                        Okay
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this before closing </body> tag -->
    <div id="approvalSuccessModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Approval Successful!</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">The reservation has been successfully approved.</p>
                </div>
                <div class="flex justify-center mt-3">
                    <button id="closeApprovalSuccessModal" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        window.showModal = function(type, message) {
            const modal = document.getElementById('successModal');
            const icon = document.getElementById('modalIcon');
            const title = document.getElementById('modalTitle');
            const messageEl = document.getElementById('modalMessage');

            if (type === 'approve') {
                icon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4';
                icon.innerHTML = `<svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>`;
                title.textContent = 'Success';
            } else {
                icon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4';
                icon.innerHTML = `<svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>`;
                title.textContent = 'Disapproved';
            }

            messageEl.textContent = message;
            modal.classList.remove('hidden');
        };

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const action = e.submitter.value;
                const reservationId = this.querySelector('input[name="reservation_id"]').value;
                
                const buttons = this.querySelectorAll('button');
                buttons.forEach(button => button.disabled = true);

                fetch('admin_reservation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=${action}&reservation_id=${reservationId}`
                })
                .then(response => response.text())
                .then(html => {
                    if (html.includes('Success')) {
                        showModal('approve', action === 'approve' ? 
                            'Reservation approved and added to sit-in sessions.' : 
                            'Reservation has been disapproved.');
                    } else {
                        showModal('disapprove', 'Error processing request.');
                    }
                })
                .catch(error => {
                    showModal('disapprove', 'Error: ' + error.message);
                    buttons.forEach(button => button.disabled = false);
                });
            });
        });

        window.closeModal = function() {
            document.getElementById('successModal').classList.add('hidden');
            window.location.reload();
        };

        document.getElementById('successModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Add modal close handler
        document.getElementById('closeApprovalSuccessModal').addEventListener('click', function() {
            document.getElementById('approvalSuccessModal').classList.add('hidden');
            // Reload the page to refresh the reservation list
            window.location.reload();
        });

        // Close modal when clicking outside
        document.getElementById('approvalSuccessModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                window.location.reload();
            }
        });

        // Update the filter function to only target pending reservations table
        function applyFilters() {
            // Specifically target the first table's tbody rows (Pending Reservations)
            const rows = document.querySelector('.container table:first-of-type tbody').querySelectorAll('tr');
            const labValue = labFilter.value.toLowerCase();
            const dateValue = dateFilter.value;
            const searchValue = searchFilter.value.toLowerCase();

            rows.forEach(row => {
                // Get the cell values
                const lab = row.children[2].textContent.toLowerCase(); // Laboratory column
                const dateTime = row.children[4].textContent.toLowerCase(); // Date & Time column
                const name = row.children[1].textContent.toLowerCase(); // Name column
                const id = row.children[0].textContent.toLowerCase(); // ID column

                // Check if row matches all filters
                const matchesLab = !labValue || lab.includes(labValue);
                const matchesDate = !dateValue || dateTime.includes(dateValue);
                const matchesSearch = !searchValue || 
                                    name.includes(searchValue) || 
                                    id.includes(searchValue);

                // Show/hide row based on filter matches
                if (matchesLab && matchesDate && matchesSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Show "No results" message if all rows are hidden
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none').length;
            let noResults = document.getElementById('noResults');

            if (visibleRows === 0) {
                if (!noResults) {
                    const tbody = document.querySelector('.container table:first-of-type tbody');
                    noResults = document.createElement('tr');
                    noResults.id = 'noResults';
                    noResults.innerHTML = `
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No reservations found matching the filters
                        </td>
                    `;
                    tbody.appendChild(noResults);
                }
            } else if (noResults) {
                noResults.remove();
            }
        }

        // Update the event listeners
        const labFilter = document.getElementById('labFilter');
        const dateFilter = document.getElementById('dateFilter');
        const searchFilter = document.getElementById('searchFilter');

        // Add event listeners
        labFilter.addEventListener('change', applyFilters);
        dateFilter.addEventListener('input', applyFilters);
        searchFilter.addEventListener('input', applyFilters);

        // Reset filters function
        window.resetFilters = function() {
            labFilter.value = '';
            dateFilter.value = '';
            searchFilter.value = '';
            applyFilters();
        }
    });
    </script>
</body>
</html>