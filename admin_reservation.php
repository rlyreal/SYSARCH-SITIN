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
            $stmt = $conn->prepare("SELECT idno, full_name, purpose, laboratory FROM reservations WHERE id = ?");
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $reservation = $result->fetch_assoc();
            $stmt->close();
            
            if ($reservation) {
                // Insert into sit_in table
                $stmt = $conn->prepare("INSERT INTO sit_in (idno, fullname, purpose, laboratory, time_in, session_count, created_at) VALUES (?, ?, ?, ?, NOW(), 30, NOW())");
                $stmt->bind_param("ssss", 
                    $reservation['idno'],
                    $reservation['full_name'],
                    $reservation['purpose'],
                    $reservation['laboratory']
                );
                $stmt->execute();
                $stmt->close();
                
                // Update reservation status
                $status = 'approved';
                $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $reservation_id);
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
    </div>
</body>
</html>