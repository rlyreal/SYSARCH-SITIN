<?php
// filepath: c:\xampp\htdocs\SYSARCH-SITIN\get_reservation_notifications.php
session_start();
include 'db.php';

// Verify admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Direct count query for unread notifications
$count_query = "SELECT COUNT(*) as count FROM notifications 
                WHERE ADMIN_NOTIFICATION = 1 
                AND IS_READ = 0";
$count_result = $conn->query($count_query);
$unread_count = ($count_result) ? (int)$count_result->fetch_assoc()['count'] : 0;

// Fetch notifications
$query = "SELECT 
            n.NOTIF_ID, 
            n.USER_ID,
            n.RESERVATION_ID, 
            n.MESSAGE, 
            n.IS_READ, 
            n.CREATED_AT,
            u.first_name,
            u.last_name,
            r.laboratory,
            r.purpose,
            r.date,
            r.status
          FROM 
            notifications n
          JOIN 
            users u ON n.USER_ID = u.id
          JOIN 
            reservations r ON n.RESERVATION_ID = r.id
          WHERE 
            n.ADMIN_NOTIFICATION = 1 
            AND n.RESERVATION_ID IS NOT NULL
          ORDER BY 
            n.CREATED_AT DESC 
          LIMIT 10";
            
$result = $conn->query($query);

$notifications = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'notifications' => $notifications,
    'unread_count' => $unread_count
]);
?>