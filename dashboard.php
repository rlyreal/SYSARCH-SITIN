<?php
session_start();
include 'db.php';
date_default_timezone_set('Asia/Manila'); // Set to your appropriate timezone

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT profile_picture, first_name, middle_name, last_name, course, year_level, email, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_picture, $first_name, $middle_name, $last_name, $course, $year_level, $email, $address);
$stmt->fetch();
$stmt->close();

// Replace the existing session count fetch code with this:
$stmt = $conn->prepare("SELECT COALESCE(
    (SELECT session_count FROM sit_in WHERE idno = (SELECT id_no FROM users WHERE id = ?) ORDER BY id DESC LIMIT 1), 
    30
) as session_count");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($session_count);
$stmt->fetch();
$stmt->close();

// Add this to dashboard.php to fetch notifications
function fetchUserNotifications($conn, $user_id) {
    $query = "SELECT n.NOTIF_ID, n.RESERVATION_ID, n.ANNOUNCEMENT_ID, n.MESSAGE, n.IS_READ, n.CREATED_AT,
              r.laboratory, r.status, a.admin_name, a.message as announcement_message
              FROM notifications n
              LEFT JOIN reservations r ON n.RESERVATION_ID = r.id
              LEFT JOIN announcements a ON n.ANNOUNCEMENT_ID = a.id
              WHERE n.USER_ID = ?
              AND n.ADMIN_NOTIFICATION = 0  /* Add this line to filter out admin notifications */
              ORDER BY n.CREATED_AT DESC
              LIMIT 10";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    
    return $notifications;
}

// Then call this function to get the user's notifications
$user_notifications = fetchUserNotifications($conn, $user_id);

// Update the notifications query to include both announcements and reservations
$notificationsQuery = "
    (SELECT 
        'announcement' as type,
        id,
        admin_name,
        message,
        date as timestamp,
        NULL as status
    FROM announcements
    ORDER BY date DESC
    LIMIT 1)
    
    UNION ALL
    
    (SELECT 
        'reservation' as type,
        r.id,
        'System' as admin_name,
        CASE 
            WHEN r.status = 'approved' THEN 'Your reservation has been approved'
            WHEN r.status = 'disapproved' THEN 'Your reservation has been disapproved'
        END as message,
        r.created_at as timestamp,
        r.status
    FROM reservations r
    WHERE r.idno = (SELECT id_no FROM users WHERE id = ?)
    AND r.status IN ('approved', 'disapproved')
    ORDER BY r.created_at DESC
    LIMIT 1)
    
    ORDER BY timestamp DESC";

$stmt = $conn->prepare($notificationsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notificationResult = $stmt->get_result();

$user_profile = (!empty($profile_picture) && file_exists($profile_picture)) ? $profile_picture : "profile.jpg";

// Replace your existing human_time_diff function with this improved version
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
    } elseif ($time_diff < 2592000) { // 30 days
        $weeks = floor($time_diff / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    } elseif ($time_diff < 31536000) { // 1 year
        $months = floor($time_diff / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}

// Add this debugging code inside your loop to check what's happening
foreach($user_notifications as $notification) {
    // Debug: Check the raw timestamp format
    $raw_timestamp = $notification['CREATED_AT'];
    $unix_timestamp = strtotime($notification['CREATED_AT']);
    $current_time = time();
    $time_diff = $current_time - $unix_timestamp;
    
    // Convert using your function
    $timeAgo = human_time_diff($unix_timestamp);
    
    // Rest of your notification code...
    // Use $timeAgo as before
}

?>

<script>
// JavaScript functions
function markNotificationsAsRead() {
    fetch('mark_notifications_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide the notification badge
            document.querySelector('.notification-badge').style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error marking notifications as read:', error);
    });
}

function markAllAsRead() {
    fetch('mark_all_notifications_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide the notification badge
            document.querySelector('.notification-badge').style.display = 'none';
            
            // Remove new indicators
            document.querySelectorAll('.dropdown-notifications-item').forEach(item => {
                item.classList.remove('bg-light');
                item.querySelector('.dropdown-notifications-actions')?.remove();
            });
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}
</script>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>Student Dashboard | Sit-In System</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap" rel="stylesheet" />
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    
    <!-- Page CSS -->
    <style>
        @keyframes scan {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(100%); }
        }
        
        .progress-bar-sessions {
            position: relative;
            height: 10px;
            border-radius: 0.375rem;
            overflow: hidden;
        }
        
        .progress-bar-sessions .progress {
            height: 100%;
            border-radius: 0.375rem;
            background: linear-gradient(to right, #696cff, #8592ff);
            transition: width 0.6s ease;
        }
        
        .progress-bar-sessions .scanline {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .progress-bar-sessions .scanline::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: rgba(255, 255, 255, 0.6);
            box-shadow: 0 0 8px 2px rgba(255, 255, 255, 0.4);
            animation: scan 2s linear infinite;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        #announcementContainer {
            max-height: 350px;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        #announcementContainer::-webkit-scrollbar {
            width: 6px;
        }

        #announcementContainer::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 8px;
        }

        #announcementContainer::-webkit-scrollbar-thumb {
            background: #d9dee3;
            border-radius: 8px;
        }

        #announcementContainer::-webkit-scrollbar-thumb:hover {
            background: #adb5bd;
        }
    </style>
    
    <!-- Helpers -->
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/js/helpers.js"></script>
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="dashboard.php" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <svg width="25" viewBox="0 0 25 42" xmlns="http://www.w3.org/2000/svg">
                                <defs><linearGradient id="a" x1="50%" x2="50%" y1="0%" y2="100%">
                                <stop offset="0%" stop-color="#5A8DEE"/><stop offset="100%" stop-color="#699AF9"/></linearGradient></defs>
                                <path fill="url(#a)" d="M12.5 0 25 14H0z"/><path fill="#FDAC41" d="M0 14 12.5 28 25 14H0z"/>
                                <path fill="#E89A3C" d="M0 28 12.5 42 25 28H0z"/><path fill="#FDAC41" d="M12.5 14 25 28 12.5 42 0 28z"/>
                            </svg>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">UC Sit-In</span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bi bi-x bi-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item active">
                        <a href="dashboard.php" class="menu-link">
                            <i class="menu-icon bi bi-house-door"></i>
                            <div data-i18n="Dashboard">Dashboard</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Student Features</span>
                    </li>

                    <!-- Edit Profile -->
                    <li class="menu-item">
                        <a href="editprofile.php" class="menu-link">
                            <i class="menu-icon bi bi-person-gear"></i>
                            <div data-i18n="Edit Profile">Edit Profile</div>
                        </a>
                    </li>

                    <!-- History -->
                    <li class="menu-item">
                        <a href="history.php" class="menu-link">
                            <i class="menu-icon bi bi-clock-history"></i>
                            <div data-i18n="History">History</div>
                        </a>
                    </li>

                    <!-- Reservation -->
                    <li class="menu-item">
                        <a href="user_reservation.php" class="menu-link">
                            <i class="menu-icon bi bi-calendar-check"></i>
                            <div data-i18n="Reservation">Reservation</div>
                        </a>
                    </li>

                    <!-- Schedule -->
                    <li class="menu-item">
                        <a href="user_sched.php" class="menu-link">
                            <i class="menu-icon bi bi-calendar3"></i>
                            <div data-i18n="Schedule">Schedule</div>
                        </a>
                    </li>

                    <!-- Resources -->
                    <li class="menu-item">
                        <a href="user_resources.php" class="menu-link">
                            <i class="menu-icon bi bi-box"></i>
                            <div data-i18n="Resources">Resources</div>
                        </a>
                    </li>
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bi bi-list bi-middle"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <i class="bi bi-search fs-4 lh-0"></i>
                                <input type="text" class="form-control border-0 shadow-none" placeholder="Search..." aria-label="Search...">
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Notification Dropdown -->
                            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" onclick="markNotificationsAsRead()">
                                    <i class="bi bi-bell bi-middle"></i>
                                    <?php if(count(array_filter($user_notifications, function($n) { return $n['IS_READ'] == 0; })) > 0): ?>
                                        <span class="badge bg-danger rounded-pill badge-notifications notification-badge">
                                            <?php echo count(array_filter($user_notifications, function($n) { return $n['IS_READ'] == 0; })); ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end py-0">
                                    <li class="dropdown-menu-header border-bottom">
                                        <div class="dropdown-header d-flex align-items-center py-3">
                                            <h5 class="text-body mb-0 me-auto">Notifications</h5>
                                            <a href="javascript:void(0)" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read" onclick="markAllAsRead()">
                                                <i class="bi bi-check2-all"></i>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="dropdown-notifications-list scrollable-container">
                                        <ul class="list-group list-group-flush">
                                            <?php 
                                            if(count($user_notifications) > 0) {
                                                foreach($user_notifications as $notification) {
                                                    $timestamp = strtotime($notification['CREATED_AT']);
                                                    $timeAgo = human_time_diff($timestamp);
                                                    
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
                                                    
                                                    // Add "new" class if unread
                                                    $newClass = $notification['IS_READ'] == 0 ? 'bg-light' : '';
                                            ?>
                                            <li class="list-group-item list-group-item-action dropdown-notifications-item <?php echo $newClass; ?>">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar">
                                                            <span class="avatar-initial rounded-circle <?php echo $bgColor; ?>">
                                                                <i class="<?php echo $icon; ?>"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">
                                                            <?php 
                                                            if($notification['ANNOUNCEMENT_ID']) {
                                                                echo htmlspecialchars($notification['admin_name']); 
                                                            } else {
                                                                echo "System Notification";
                                                            }
                                                            ?>
                                                        </h6>
                                                        <p class="mb-0"><?php echo htmlspecialchars($notification['MESSAGE']); ?></p>
                                                        <small class="text-muted"><?php echo $timeAgo; ?></small>
                                                    </div>
                                                    <?php if($notification['IS_READ'] == 0): ?>
                                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                                        <a href="javascript:void(0)" class="dropdown-notifications-read">
                                                            <span class="badge badge-dot bg-primary"></span>
                                                        </a>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                            <?php 
                                                }
                                            } else {
                                            ?>
                                            <li class="list-group-item list-group-item-action dropdown-notifications-item p-4 text-center">
                                                <p class="text-muted mb-0">No notifications</p>
                                            </li>
                                            <?php 
                                            }
                                            ?>
                                        </ul>
                                    </li>
                                    <li class="dropdown-menu-footer border-top">
                                        <a href="all_notifications.php" class="dropdown-item d-flex justify-content-center p-3">
                                            View all notifications
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- /Notification Dropdown -->

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="<?php echo htmlspecialchars($user_profile); ?>" alt="profile" class="w-px-40 h-auto rounded-circle">
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="<?php echo htmlspecialchars($user_profile); ?>" alt="profile" class="w-px-40 h-auto rounded-circle">
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block"><?php echo htmlspecialchars("$first_name $last_name"); ?></span>
                                                    <small class="text-muted"><?php echo htmlspecialchars($course); ?></small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="editprofile.php">
                                            <i class="bi bi-person-gear me-2"></i>
                                            <span class="align-middle">My Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="history.php">
                                            <i class="bi bi-clock-history me-2"></i>
                                            <span class="align-middle">History</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" id="logoutBtn">
                                            <i class="bi bi-box-arrow-right me-2"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-lg-8 mb-4 order-0">
                                <div class="card">
                                    <div class="d-flex align-items-end row">
                                        <div class="col-sm-7">
                                            <div class="card-body">
                                                <h5 class="card-title text-primary">Welcome <?php echo htmlspecialchars($first_name); ?>! 🎉</h5>
                                                <p class="mb-4">You have <span class="fw-bold"><?php echo htmlspecialchars($session_count); ?></span> sit-in sessions available. Make the most of your lab time with UC CCS!</p>

                                                <a href="user_reservation.php" class="btn btn-sm btn-outline-primary">Make Reservation</a>
                                            </div>
                                        </div>
                                        <div class="col-sm-5 text-center text-sm-left">
                                            <div class="card-body pb-0 px-0 px-md-4">
                                                <!-- Badge removed -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sessions Available -->
                            <div class="col-lg-4 col-md-4 order-1">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar">
                                                    <span class="avatar-initial rounded bg-label-primary"><i class="bi bi-calendar-check"></i></span>
                                                </div>
                                                <div class="ms-3">
                                                    <span class="fw-semibold d-block mb-1">Sessions Available</span>
                                                    <div class="d-flex align-items-center">
                                                        <h4 class="card-title mb-0 me-2 text-nowrap"><?php echo htmlspecialchars($session_count); ?>/30</h4>
                                                        <small class="text-success fw-semibold">remaining</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="progress-bar-sessions">
                                            <div class="progress" style="width: <?php echo ($session_count/30) * 100; ?>%;"></div>
                                            <div class="scanline"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Student Information -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h5 class="card-title m-0">Student Information</h5>
                                        <div class="dropdown">
                                            <button class="btn p-0" type="button" id="studentInfoMenu" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="studentInfoMenu">
                                                <a class="dropdown-item" href="editprofile.php">Edit Profile</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex flex-column align-items-center pb-4">
                                            <div class="avatar avatar-xl mb-3">
                                                <img src="<?php echo htmlspecialchars($user_profile); ?>" alt="Avatar" class="rounded-circle">
                                            </div>
                                            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars("$first_name $middle_name $last_name"); ?></h5>
                                            <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($course); ?></span>
                                            <small class="text-muted"><?php echo htmlspecialchars($year_level); ?></small>
                                        </div>
                                        <div class="divider">
                                            <div class="divider-text">Contact Details</div>
                                        </div>
                                        <div class="info-container">
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Email:</span>
                                                    <span><?php echo htmlspecialchars($email); ?></span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Address:</span>
                                                    <span><?php echo htmlspecialchars($address); ?></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Announcements Card -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h5 class="card-title m-0">Announcements</h5>
                                        <div class="badge bg-label-primary">Latest Updates</div>
                                    </div>
                                    <div class="card-body px-0">
                                        <div id="announcementContainer" class="p-0" style="max-height: 350px; overflow-y: auto;">
                                            <!-- Announcements will be loaded here -->
                                            <div class="d-flex justify-content-center my-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Laboratory Rules Card -->
                            <div class="col-md-12 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h5 class="card-title m-0">Laboratory Rules</h5>
                                        <span class="badge bg-label-warning">Important</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="accordion" id="labRulesAccordion">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingOne">
                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                        General Rules
                                                    </button>
                                                </h2>
                                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#labRulesAccordion">
                                                    <div class="accordion-body pt-3 pb-0">
                                                        <p class="mb-2 small text-muted fst-italic">To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:</p>
                                                        <ol class="small ps-3 mb-3">
                                                            <li class="mb-1">Maintain silence, proper decorum, and discipline</li>
                                                            <li class="mb-1">No computer games or card games allowed</li>
                                                            <li class="mb-1">Internet surfing requires instructor permission</li>
                                                            <li class="mb-1">No access to non-course related websites</li>
                                                            <li class="mb-1">No deleting files or changing computer setup</li>
                                                            <li class="mb-1">15-minute time limit per session</li>
                                                        </ol>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingTwo">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                        Laboratory Etiquette
                                                    </button>
                                                </h2>
                                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#labRulesAccordion">
                                                    <div class="accordion-body pt-3 pb-0">
                                                        <ul class="small ps-3 mb-3">
                                                            <li class="mb-1">Wait for instructor before entering</li>
                                                            <li class="mb-1">Deposit bags at counter</li>
                                                            <li class="mb-1">Follow seating arrangements</li>
                                                            <li class="mb-1">Close all programs after use</li>
                                                            <li class="mb-1">Return chairs to proper place</li>
                                                            <li class="mb-1">No food or drinks allowed</li>
                                                            <li class="mb-1">No vandalism of equipment</li>
                                                            <li class="mb-1">Report technical issues immediately</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingThree">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                        Sanctions
                                                    </button>
                                                </h2>
                                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#labRulesAccordion">
                                                    <div class="accordion-body pt-3 pb-0">
                                                        <div class="alert alert-warning d-flex align-items-center mb-2 py-2" role="alert">
                                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                                            <div class="small">
                                                                <strong>First Offense:</strong> Suspension from classes (via Guidance Center)
                                                            </div>
                                                        </div>
                                                        <div class="alert alert-danger d-flex align-items-center mb-3 py-2" role="alert">
                                                            <i class="bi bi-x-octagon me-2"></i>
                                                            <div class="small">
                                                                <strong>Second Offense:</strong> Heavier sanctions via Guidance Center
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Action Cards -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="pb-1 mb-4">Quick Actions</h5>
                            </div>
                            
                            <div class="col-md-6 col-lg-3">
                                <div class="card card-hover mb-4 cursor-pointer" onclick="window.location.href='user_reservation.php'">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-primary">
                                                    <i class="bi bi-calendar-plus"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="card-title mb-1">Make Reservation</h5>
                                                <p class="card-text small text-muted">Schedule your lab session in advance</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-lg-3">
                                <div class="card card-hover mb-4 cursor-pointer" onclick="window.location.href='user_sched.php'">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-info">
                                                    <i class="bi bi-calendar3"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="card-title mb-1">Lab Schedule</h5>
                                                <p class="card-text small text-muted">View laboratory class schedules</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-lg-3">
                                <div class="card card-hover mb-4 cursor-pointer" onclick="window.location.href='history.php'">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-success">
                                                    <i class="bi bi-clock-history"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="card-title mb-1">View History</h5>
                                                <p class="card-text small text-muted">See your lab usage history</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-lg-3">
                                <div class="card card-hover mb-4 cursor-pointer" onclick="window.location.href='user_resources.php'">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-warning">
                                                    <i class="bi bi-box"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="card-title mb-1">Lab Resources</h5>
                                                <p class="card-text small text-muted">Access learning materials</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

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

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- / Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-box-arrow-right text-danger" style="font-size: 3rem;"></i>
                        <h4 class="mt-3">Are you sure you want to logout?</h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/popper/popper.js"></script>
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/js/bootstrap.js"></script>
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/js/menu.js"></script>

    <script>
// Load announcements from get_announcements.php
function loadAnnouncements() {
  fetch('get_announcements.php')
    .then(response => response.json())
    .then(data => {
      const container = document.getElementById('announcementContainer');
      container.innerHTML = ''; // Clear loading spinner
      
      if (data && data.length > 0) {
        data.forEach(announcement => {
          const announcementDiv = document.createElement('div');
          announcementDiv.classList.add('p-3', 'border-bottom');
          
          // Format date
          const date = new Date(announcement.date);
          const formattedDate = date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          });
          
          announcementDiv.innerHTML = `
            <div class="d-flex align-items-start mb-2">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-primary">
                  <i class="bi bi-megaphone"></i>
                </span>
              </div>
              <div>
                <h6 class="mb-0">${announcement.admin_name}</h6>
                <small class="text-muted">${formattedDate}</small>
              </div>
            </div>
            <p class="mb-0">${announcement.message}</p>
          `;
          container.appendChild(announcementDiv);
        });
      } else {
        container.innerHTML = '<div class="text-center p-4"><p class="text-muted mb-0">No announcements available</p></div>';
      }
    })
    .catch(error => {
      console.error('Error loading announcements:', error);
      document.getElementById('announcementContainer').innerHTML = 
        '<div class="text-center p-4"><p class="text-danger mb-0">Failed to load announcements</p></div>';
    });
}

// Remove notification badge when dropdown is opened
function removeNotificationBadge() {
  const badge = document.querySelector('.notification-badge');
  if (badge) {
    badge.style.display = 'none';
  }
}

// Document ready handler
document.addEventListener('DOMContentLoaded', function() {
  // Load announcements
  loadAnnouncements();
  
  // Logout button functionality
  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function() {
      const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
      logoutModal.show();
    });
  }
});
</script>
</body>
</html>

