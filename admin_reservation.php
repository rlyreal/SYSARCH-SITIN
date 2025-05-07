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
                // First check if user is currently sitting in
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

                // Insert into sit_in table with current session count
                $stmt = $conn->prepare("INSERT INTO sit_in (idno, fullname, purpose, laboratory, time_in, session_count, created_at) VALUES (?, ?, ?, ?, NOW(), ?, NOW())");
                $stmt->bind_param("ssssi", 
                    $reservation['idno'],
                    $reservation['full_name'],
                    $reservation['purpose'],
                    $reservation['laboratory'],
                    $current_sessions
                );
                $stmt->execute();
                $stmt->close();
                
                // Update reservation status
                $status = 'approved';
                $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $reservation_id);
                $stmt->execute();
                $stmt->close();

                // Log the action
                $stmt = $conn->prepare("INSERT INTO reservation_logs (
                    reservation_id, idno, full_name, course, year_level, 
                    purpose, laboratory, date, time_in, pc_number, 
                    status, action_type, action_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
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
                $stmt->execute();
                $stmt->close();
                
                $conn->commit();
                echo "<script>alert('Reservation approved and added to sit-in sessions.');</script>";
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
    } else {
        // Handle disapproval
        $status = 'disapproved';
        $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $reservation_id);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Reservation disapproved.');</script>";
    }
    
    // Redirect to refresh the page
    echo "<script>window.location.href = 'admin_reservation.php';</script>";
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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Pending Reservations</h1>
        
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
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

        <h1 class="text-2xl font-bold mb-6 mt-12">Reservation Logs</h1>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
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
</body>
</html>