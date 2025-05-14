<?php
// filepath: c:\xampp\htdocs\SYSARCH-SITIN\all_notifications.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get total count
$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM notifications WHERE USER_ID = ?");
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$total_notifications = $count_row['total'];
$total_pages = ceil($total_notifications / $per_page);

// Get notifications
$query = "SELECT n.NOTIF_ID, n.RESERVATION_ID, n.ANNOUNCEMENT_ID, n.MESSAGE, n.IS_READ, n.CREATED_AT,
          r.laboratory, r.status, a.admin_name, a.message as announcement_message
          FROM notifications n
          LEFT JOIN reservations r ON n.RESERVATION_ID = r.id
          LEFT JOIN announcements a ON n.ANNOUNCEMENT_ID = a.id
          WHERE n.USER_ID = ?
          ORDER BY n.CREATED_AT DESC
          LIMIT ? OFFSET ?";
          
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Mark all as read
$mark_read = $conn->prepare("UPDATE notifications SET IS_READ = 1 WHERE USER_ID = ?");
$mark_read->bind_param("i", $user_id);
$mark_read->execute();

// User details for navbar
$stmt = $conn->prepare("SELECT profile_picture, first_name, last_name, course FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_picture, $first_name, $last_name, $course);
$stmt->fetch();
$stmt->close();

$user_profile = (!empty($profile_picture) && file_exists($profile_picture)) ? $profile_picture : "profile.jpg";
$user_name = $first_name . ' ' . $last_name;

// Time ago function
function human_time_diff($timestamp) {
    $time_diff = time() - $timestamp;
    
    if ($time_diff < 60) {
        return 'just now';
    } elseif ($time_diff < 3600) {
        $mins = floor($time_diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($time_diff < 86400) {
        $hours = floor($time_diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($time_diff < 604800) {
        $days = floor($time_diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications - Sit-In System</title>
    
    <!-- Include your CSS/JS files here as in dashboard.php -->
    
    <style>
        .notification-item {
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        .notification-item:hover {
            background-color: rgba(67, 89, 113, 0.04);
        }
        
        .notification-item.unread {
            border-left-color: #696cff;
            background-color: rgba(105, 108, 255, 0.04);
        }
        
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }
    </style>
</head>

<body>
    <!-- Include your navigation and layout code as in dashboard.php -->
    
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-sm mb-2 mb-sm-0">
                        <h4 class="fw-bold"><i class="bi bi-bell me-2"></i>All Notifications</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">All Notifications</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            
            <!-- Notifications List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title mb-0">Notification History</h5>
                    <div class="text-muted">
                        Showing <?php echo min(($offset + 1), $total_notifications) . ' - ' . min(($offset + $per_page), $total_notifications); ?> of <?php echo $total_notifications; ?> notifications
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($notification = $result->fetch_assoc()): 
                                $timestamp = strtotime($notification['CREATED_AT']);
                                $timeAgo = human_time_diff($timestamp);
                                $dateFormatted = date('M d, Y h:i A', $timestamp);
                                
                                // Set notification color based on type
                                $bgColor = 'bg-light-primary';
                                $icon = 'bi-bell';
                                
                                if($notification['RESERVATION_ID']) {
                                    if(isset($notification['status'])) {
                                        if($notification['status'] === 'approved') {
                                            $bgColor = 'bg-light-success';
                                            $icon = 'bi-check-circle';
                                        } else if($notification['status'] === 'disapproved') {
                                            $bgColor = 'bg-light-danger';
                                            $icon = 'bi-x-circle';
                                        } else {
                                            $bgColor = 'bg-light-warning';
                                            $icon = 'bi-clock';
                                        }
                                    }
                                } else if($notification['ANNOUNCEMENT_ID']) {
                                    $bgColor = 'bg-light-info';
                                    $icon = 'bi-megaphone';
                                }
                                
                                $readClass = $notification['IS_READ'] == 0 ? 'unread' : '';
                            ?>
                            <div class="list-group-item notification-item p-4 <?php echo $readClass; ?>">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <span class="avatar-initial rounded-circle <?php echo $bgColor; ?>">
                                                <i class="<?php echo $icon; ?>"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0">
                                                <?php 
                                                if($notification['ANNOUNCEMENT_ID']) {
                                                    echo htmlspecialchars($notification['admin_name']); 
                                                } else {
                                                    echo "System Notification";
                                                }
                                                ?>
                                            </h6>
                                            <small class="text-muted" data-bs-toggle="tooltip" title="<?php echo $dateFormatted; ?>"><?php echo $timeAgo; ?></small>
                                        </div>
                                        <p class="mb-0"><?php echo htmlspecialchars($notification['MESSAGE']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center p-5">
                                <div class="mb-3">
                                    <i class="bi bi-bell-slash" style="font-size: 3rem; color: #d6d6d6;"></i>
                                </div>
                                <h5>No Notifications</h5>
                                <p class="text-muted">You don't have any notifications yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="card-footer">
                    <div class="pagination-container">
                        <ul class="pagination">
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page-1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            
                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                    ©
                    <script>
                        document.write(new Date().getFullYear());
                    </script>
                    University of Cebu - College of Computer Studies
                </div>
            </div>
        </footer>
        <!-- / Footer -->
    </div>
    
    <!-- Include your JS files here -->
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>