<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$admin_username = $_SESSION['username'] ?? 'Admin User';

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
            
            if (!$reservation) {
                echo json_encode([
                    "success" => false,
                    "message" => "Reservation not found."
                ]);
                exit;
            }
            
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
                $stmt = $conn->prepare("INSERT INTO sit_in (idno, fullname, purpose, laboratory, pc_number, time_in, status, session_count, source, reservation_id, created_at) 
                                       VALUES (?, ?, ?, ?, ?, NOW(), 'active', ?, 'reservation', ?, NOW())");
                $stmt->bind_param("sssssii", 
                    $reservation['idno'],
                    $reservation['full_name'],
                    $reservation['purpose'],
                    $reservation['laboratory'],
                    $reservation['pc_number'],
                    $current_sessions,
                    $reservation_id  // Link to the reservation ID
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
                    // Get user_id from the reservation
                    $user_query = $conn->prepare("SELECT u.id, u.first_name, u.last_name 
                                                FROM reservations r 
                                                JOIN users u ON r.idno = u.id_no 
                                                WHERE r.id = ?");
                    $user_query->bind_param("i", $reservation_id);
                    $user_query->execute();
                    $user_result = $user_query->get_result();
                    $user_data = $user_result->fetch_assoc();
                    
                    if ($user_data) {
                        $user_id = $user_data['id'];
                        $notif_message = "Your reservation for Laboratory {$reservation['laboratory']} has been approved";
                        
                        // Create notification for the user
                        $notify_user = $conn->prepare("INSERT INTO notifications (USER_ID, RESERVATION_ID, MESSAGE, IS_READ) VALUES (?, ?, ?, 0)");
                        $notify_user->bind_param("iis", $user_id, $reservation_id, $notif_message);
                        
                        if ($stmt->execute() && $notify_user->execute()) {
                            $conn->commit();
                            echo json_encode([
                                "success" => true,
                                "message" => "Reservation approved successfully."
                            ]);
                            exit;
                        } else {
                            $conn->rollback();
                            echo json_encode([
                                "success" => false,
                                "message" => "Failed to process reservation."
                            ]);
                            exit;
                        }
                    }
                    
                    $_SESSION['success_msg'] = "Reservation approved successfully!";
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
    } else if ($action === 'disapprove') {
        // Handle disapproval
        $status = 'disapproved';
        $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $reservation_id);
        
        if ($stmt->execute()) {
            // Get the reservation details first
            $stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ?");
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $reservation = $result->fetch_assoc();
            $stmt->close();
            
            // Get user_id from the reservation
            $user_query = $conn->prepare("SELECT u.id, u.first_name, u.last_name 
                                      FROM reservations r 
                                      JOIN users u ON r.idno = u.id_no 
                                      WHERE r.id = ?");
            $user_query->bind_param("i", $reservation_id);
            $user_query->execute();
            $user_result = $user_query->get_result();
            $user_data = $user_result->fetch_assoc();
            
            if ($user_data) {
                $user_id = $user_data['id'];
                $notif_message = "Your reservation for Laboratory " . $reservation['laboratory'] . " has been declined";
                
                // Create notification for the user
                $notify_user = $conn->prepare("INSERT INTO notifications (USER_ID, RESERVATION_ID, MESSAGE, IS_READ) VALUES (?, ?, ?, 0)");
                $notify_user->bind_param("iis", $user_id, $reservation_id, $notif_message);
                $notify_user->execute();
            }
            
            $_SESSION['success_msg'] = "Reservation declined successfully!";

            // Insert into reservation_logs for disapproval
            $stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ?");
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $reservation = $result->fetch_assoc();
            $stmt->close();
            
            if ($reservation) {
                $stmt = $conn->prepare("INSERT INTO reservation_logs (
                    reservation_id, idno, full_name, course, year_level, 
                    purpose, laboratory, date, time_in, pc_number, 
                    status, action_type, action_by, action_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                
                $action_type = "Disapproved";
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
            }
            
            echo json_encode([
                "success" => true,
                "message" => "Reservation has been disapproved."
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Failed to update reservation status."
            ]);
        }
        exit;
    }
}

// Fetch pending reservations
$stmt = $conn->prepare("SELECT * FROM reservations WHERE status = 'pending' ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();

// Fetch reservation logs
$logs_query = $conn->prepare("SELECT rl.*, a.username as admin_name 
          FROM reservation_logs rl 
          LEFT JOIN admin a ON rl.action_by = a.id 
          ORDER BY rl.action_date DESC");
$logs_query->execute();
$logs_result = $logs_query->get_result();
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations | Admin</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap" rel="stylesheet" />
    
    <!-- Icons. Required if you use Bootstrap Icons-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    
    <!-- Sneat Template Core CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/css/demo.css" />
    
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Helpers -->
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/js/helpers.js"></script>
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/js/config.js"></script>

    <style>
        /* Update these styles to make PC cards smaller */
        .pc-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr); /* Changed from 5 to 6 columns */
            gap: 10px; /* Reduced gap from 15px to 10px */
        }
        
        @media (max-width: 1200px) {
            .pc-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }
        
        @media (max-width: 992px) {
            .pc-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .pc-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .pc-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        .pc-card {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            border: 2px solid #e0e0e0;
            background-color: white;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
            padding: 8px; /* Added padding */
            font-size: 0.85rem; /* Reduced base font size */
        }
        
        .pc-card .pc-icon {
            font-size: 1.25rem; /* Reduced from 1.75rem */
            margin-bottom: 0.25rem; /* Reduced from 0.5rem */
            color: #697a8d;
        }
        
        .pc-card .pc-number {
            font-weight: 600;
            margin-bottom: 0.1rem; /* Reduced from 0.25rem */
            font-size: 0.9rem; /* Added specific font size */
        }
        
        .pc-card .pc-status {
            font-size: 0.7rem; /* Reduced from 0.75rem */
            margin-bottom: 0.25rem; /* Reduced from 0.5rem */
        }
        
        /* Maintain remaining styles for colors */
        .pc-card.available {
            border-color: #71dd37;
            background-color: #f6fff4;
        }
        
        .pc-card.available .pc-status {
            color: #71dd37;
        }
        
        .pc-card.unavailable {
            border-color: #ff3e1d;
            background-color: #fff5f4;
        }
        
        .pc-card.unavailable .pc-status {
            color: #ff3e1d;
        }
        
        .pc-card.in-use {
            border-color: #696cff;
            background-color: #f5f5ff;
        }
        
        .pc-card.in-use .pc-status {
            color: #696cff;
        }
        
        .pc-card .pc-user {
            font-size: 0.7rem; /* Reduced from 0.75rem */
            margin-bottom: 0.25rem; /* Reduced from 0.5rem */
            text-align: center;
            width: 100%;
            padding: 0 0.25rem; /* Reduced from 0.5rem */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .pc-card .pc-actions {
            display: flex;
            gap: 0.25rem; /* Reduced from 0.5rem */
        }
        
        .pc-card .pc-actions .btn-sm {
            padding: 0.2rem 0.4rem; /* Make buttons smaller */
            font-size: 0.7rem;
        }
        
        /* Make the grid container more compact */
        .pc-grid-container {
            max-height: 450px; /* Reduced from 500px */
            overflow-y: auto;
            padding: 8px; /* Reduced from 10px */
            border-radius: 0.375rem;
            background-color: #f9fafb;
            margin-bottom: 1rem;
        }
        
        /* Make toggle button smaller */
        .pc-toggle-btn {
            position: absolute;
            top: 2px; /* Reduced from 5px */
            right: 2px; /* Reduced from 5px */
            width: 20px; /* Reduced from 24px */
            height: 20px; /* Reduced from 24px */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px; /* Reduced from 12px */
            z-index: 10;
            padding: 0; /* Remove padding */
        }

        .pc-toggle-btn i {
            font-size: 8px; /* Even smaller icons */
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="admin_dashboard.php" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <svg width="25" viewBox="0 0 25 42" xmlns="http://www.w3.org/2000/svg">
                                <defs><linearGradient id="a" x1="50%" x2="50%" y1="0%" y2="100%">
                                <stop offset="0%" stop-color="#5A8DEE"/><stop offset="100%" stop-color="#699AF9"/></linearGradient></defs>
                                <path fill="url(#a)" d="M12.5 0 25 14H0z"/><path fill="#FDAC41" d="M0 14 12.5 28 25 14H0z"/>
                                <path fill="#E89A3C" d="M0 28 12.5 42 25 28H0z"/><path fill="#FDAC41" d="M12.5 14 25 28 12.5 42 0 28z"/>
                            </svg>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">Sit-In Admin</span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bi bi-x bi-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item">
                        <a href="admin_dashboard.php" class="menu-link">
                            <i class="menu-icon bi bi-house-door"></i>
                            <div data-i18n="Dashboard">Dashboard</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Management</span>
                    </li>

                    <!-- Search -->
                    <li class="menu-item">
                        <a href="search.php" class="menu-link">
                            <i class="menu-icon bi bi-search"></i>
                            <div data-i18n="Search">Search</div>
                        </a>
                    </li>

                    <!-- Students -->
                    <li class="menu-item">
                        <a href="students.php" class="menu-link">
                            <i class="menu-icon bi bi-people"></i>
                            <div data-i18n="Students">Students</div>
                        </a>
                    </li>

                    <!-- Sit-in -->
                    <li class="menu-item">
                        <a href="sit_in.php" class="menu-link">
                            <i class="menu-icon bi bi-clipboard-check"></i>
                            <div data-i18n="Sit-in">Sit-in</div>
                        </a>
                    </li>

                    <!-- View Records -->
                    <li class="menu-item">
                        <a href="sit_in_records.php" class="menu-link">
                            <i class="menu-icon bi bi-clipboard-data"></i>
                            <div data-i18n="Records">View Records</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Features</span>
                    </li>

                    <!-- Reservation -->
                    <li class="menu-item active">
                        <a href="admin_reservation.php" class="menu-link">
                            <i class="menu-icon bi bi-calendar-check"></i>
                            <div data-i18n="Reservation">Reservation</div>
                        </a>
                    </li>

                    <!-- Schedule - New Item Added -->
                    <li class="menu-item">
                        <a href="admin_sched.php" class="menu-link">
                            <i class="menu-icon bi bi-calendar3"></i>
                            <div data-i18n="Schedule">Schedule</div>
                        </a>
                    </li>

                    <!-- Reports -->
                    <li class="menu-item">
                        <a href="reports.php" class="menu-link">
                            <i class="menu-icon bi bi-file-earmark-bar-graph"></i>
                            <div data-i18n="Reports">Reports</div>
                        </a>
                    </li>

                    <!-- Feedback Reports -->
                    <li class="menu-item">
                        <a href="feedback.php" class="menu-link">
                            <i class="menu-icon bi bi-chat-left-text"></i>
                            <div data-i18n="Feedback">Feedback Reports</div>
                        </a>
                    </li>

                    <!-- Resources -->
                    <li class="menu-item">
                        <a href="lab_resources.php" class="menu-link">
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
                                <input type="text" class="form-control border-0 shadow-none" id="navbarSearch" placeholder="Search..." aria-label="Search...">
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_username); ?>&background=696cff&color=fff" alt class="w-px-40 h-auto rounded-circle">
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_username); ?>&background=696cff&color=fff" alt class="w-px-40 h-auto rounded-circle">
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block"><?php echo htmlspecialchars($admin_username); ?></span>
                                                    <small class="text-muted">Administrator</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bi bi-gear me-2"></i>
                                            <span class="align-middle">Settings</span>
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
                        <h4 class="fw-bold py-3 mb-4">
                            <span class="text-muted fw-light">Student Management /</span> Reservations
                        </h4>

                        <!-- Pending Reservations Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Pending Reservations</h5>
                            </div>
                            <div class="card-body">
                                <!-- Filters -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <label class="form-label">Laboratory</label>
                                        <select id="labFilter" class="form-select">
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
                                    <div class="col-md-3">
                                        <label class="form-label">Search</label>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                                            <input type="text" id="searchFilter" class="form-control" placeholder="Search name or ID...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date</label>
                                        <input type="date" id="dateFilter" class="form-control">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button onclick="resetFilters()" class="btn btn-secondary">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                                        </button>
                                    </div>
                                </div>

                                <!-- Reservation Table -->
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-hover" id="pendingReservationsTable">
                                        <thead>
                                            <tr>
                                                <th>ID Number</th>
                                                <th>Name</th>
                                                <th>Laboratory</th>
                                                <th>PC Number</th>
                                                <th>Date & Time</th>
                                                <th>Purpose</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <?php 
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) { 
                                            ?>
                                            <tr>
                                                <td><i class="bi bi-person-badge text-primary me-2"></i><?php echo htmlspecialchars($row['idno']); ?></td>
                                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                                <td>Laboratory <?php echo htmlspecialchars($row['laboratory']); ?></td>
                                                <td>PC <?php echo htmlspecialchars($row['pc_number']); ?></td>
                                                <td>
                                                    <div><?php echo htmlspecialchars($row['date']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($row['time_in']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex">
                                                        <button 
                                                            class="btn btn-sm btn-success me-2 approve-btn" 
                                                            data-id="<?php echo $row['id']; ?>"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="Approve Reservation">
                                                            <i class="bi bi-check-lg me-1"></i> Approve
                                                        </button>
                                                        <button 
                                                            class="btn btn-sm btn-danger disapprove-btn" 
                                                            data-id="<?php echo $row['id']; ?>"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="Disapprove Reservation">
                                                            <i class="bi bi-x-lg me-1"></i> Disapprove
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php 
                                                }
                                            } else {
                                            ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">No pending reservations found</td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- / Pending Reservations Section -->

                        <!-- PC Availability Control Section -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">PC Availability Control</h5>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-display me-1"></i> Select Laboratory
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php foreach ([517, 524, 526, 528, 530, 542, 544] as $labNumber): ?>
                                            <li><a class="dropdown-item" href="javascript:void(0);" onclick="loadLabPCs(<?php echo $labNumber; ?>)">
                                                Laboratory <?php echo $labNumber; ?>
                                            </a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="labControlContainer">
                                    <div class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="bi bi-display text-primary" style="font-size: 3rem;"></i>
                                        </div>
                                        <h6 class="mb-2">Select Laboratory</h6>
                                        <p class="text-muted mb-0">Please select a laboratory to manage PC availability</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- / PC Availability Control Section -->

                        <!-- Reservation Logs Section -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Reservation Logs</h5>
                                <button id="exportLogsBtn" class="btn btn-primary btn-sm">
                                    <i class="bi bi-download me-1"></i> Export Logs
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="reservationLogsTable">
                                        <thead>
                                            <tr>
                                                <th>Date & Time</th>
                                                <th>ID Number</th>
                                                <th>Name</th>
                                                <th>Laboratory</th>
                                                <th>PC Number</th>
                                                <th>Status</th>
                                                <th>Action By</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <?php 
                                            if ($logs_result->num_rows > 0) {
                                                while ($log = $logs_result->fetch_assoc()) { 
                                            ?>
                                            <tr>
                                                <td>
                                                    <div><?php echo date('M d, Y', strtotime($log['action_date'])); ?></div>
                                                    <small class="text-muted"><?php echo date('h:i A', strtotime($log['action_date'])); ?></small>
                                                </td>
                                                <td><i class="bi bi-person-badge text-primary me-2"></i><?php echo htmlspecialchars($log['idno']); ?></td>
                                                <td><?php echo htmlspecialchars($log['full_name']); ?></td>
                                                <td>Laboratory <?php echo htmlspecialchars($log['laboratory']); ?></td>
                                                <td>PC <?php echo htmlspecialchars($log['pc_number']); ?></td>
                                                <td>
                                                    <?php if ($log['action_type'] === 'Approved'): ?>
                                                        <span class="badge bg-label-success">Approved</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-label-danger">Disapproved</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($log['admin_name']); ?></td>
                                            </tr>
                                            <?php 
                                                }
                                            } else {
                                            ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">No reservation logs found</td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- / Reservation Logs Section -->

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
                                Sit-In System Admin Dashboard
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

    <!-- Approve Confirmation Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3 text-center">
                            <div class="avatar avatar-md mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-success">
                                    <i class="bi bi-check-lg"></i>
                                </span>
                            </div>
                            <p>Are you sure you want to approve this reservation? This will add the student to sit-in sessions.</p>
                            <input type="hidden" id="approveReservationId">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmApprove" class="btn btn-success">Approve</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Disapprove Confirmation Modal -->
    <div class="modal fade" id="disapproveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Disapprove Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3 text-center">
                            <div class="avatar avatar-md mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-danger">
                                    <i class="bi bi-x-lg"></i>
                                </span>
                            </div>
                            <p>Are you sure you want to disapprove this reservation?</p>
                            <input type="hidden" id="disapproveReservationId">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDisapprove" class="btn btn-danger">Disapprove</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalTitle">Success!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3 text-center">
                            <div id="successModalIcon" class="avatar avatar-md mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-success">
                                    <i class="bi bi-check-lg"></i>
                                </span>
                            </div>
                            <p id="successModalMessage">Operation completed successfully.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="successModalContinue">Continue</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3 text-center">
                            <div class="avatar avatar-md mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-danger">
                                    <i class="bi bi-box-arrow-right"></i>
                                </span>
                            </div>
                            <p>Are you sure you want to logout?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- PC User Info Modal -->
    <div class="modal fade" id="pcUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Current User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-column align-items-center mb-4">
                        <div class="avatar avatar-lg mb-3">
                            <span class="avatar-initial rounded-circle bg-label-info">
                                <i class="bi bi-person"></i>
                            </span>
                        </div>
                        <h5 id="userPcIdentifier">PC Number</h5>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ID Number</label>
                            <div id="userId" class="form-control bg-lighter">12345678</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <div id="userName" class="form-control bg-lighter">John Doe</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Time In</label>
                            <div id="userTimeIn" class="form-control bg-lighter">10:00 AM</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Purpose</label>
                            <div id="userPurpose" class="form-control bg-lighter">Programming</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="forceEndSession" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right me-1"></i> End Session
                    </button>
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

    <!-- Export to Excel -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.17.0/dist/xlsx.full.min.js"></script>

    <!-- Main JS -->
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize modals
            const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
            const disapproveModal = new bootstrap.Modal(document.getElementById('disapproveModal'));
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));

            // Approve button handler
            document.querySelectorAll('.approve-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('approveReservationId').value = this.getAttribute('data-id');
                    approveModal.show();
                });
            });

            // Disapprove button handler
            document.querySelectorAll('.disapprove-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('disapproveReservationId').value = this.getAttribute('data-id');
                    disapproveModal.show();
                });
            });

            // Confirm approve handler
            document.getElementById('confirmApprove').addEventListener('click', function() {
                const reservationId = document.getElementById('approveReservationId').value;
                processReservation(reservationId, 'approve');
                approveModal.hide();
            });

            // Confirm disapprove handler
            document.getElementById('confirmDisapprove').addEventListener('click', function() {
                const reservationId = document.getElementById('disapproveReservationId').value;
                processReservation(reservationId, 'disapprove');
                disapproveModal.hide();
            });

            // Function to process approval/disapproval
            function processReservation(reservationId, action) {
                // Disable all buttons while processing
                const allButtons = document.querySelectorAll('.approve-btn, .disapprove-btn');
                allButtons.forEach(btn => btn.disabled = true);

                fetch('admin_reservation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=${action}&reservation_id=${reservationId}`
                })
                .then(response => response.json())
                .then(data => {
                    // Show success message
                    document.getElementById('successModalTitle').textContent = action === 'approve' ? 'Approved!' : 'Disapproved!';
                    
                    if (action === 'approve') {
                        document.getElementById('successModalIcon').innerHTML = `
                            <span class="avatar-initial rounded-circle bg-label-success">
                                <i class="bi bi-check-lg"></i>
                            </span>
                        `;
                    } else {
                        document.getElementById('successModalIcon').innerHTML = `
                            <span class="avatar-initial rounded-circle bg-label-danger">
                                <i class="bi bi-x-lg"></i>
                            </span>
                        `;
                    }
                    
                    document.getElementById('successModalMessage').textContent = data.message;
                    
                    // Event listener for after modal is dismissed
                    document.getElementById('successModalContinue').addEventListener('click', function() {
                        window.location.reload();
                    }, { once: true });
                    
                    successModal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    allButtons.forEach(btn => btn.disabled = false);
                });
            }

            // Filter functions
            const labFilter = document.getElementById('labFilter');
            const searchFilter = document.getElementById('searchFilter');
            const dateFilter = document.getElementById('dateFilter');

            function applyFilters() {
                const lab = labFilter.value.toLowerCase();
                const search = searchFilter.value.toLowerCase();
                const date = dateFilter.value;

                document.querySelectorAll('#pendingReservationsTable tbody tr').forEach(row => {
                    // Skip "No results" row
                    if (row.querySelector('td[colspan]')) return;

                    const labValue = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    const idValue = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                    const nameValue = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const dateValue = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

                    const matchesLab = !lab || labValue.includes(lab);
                    const matchesSearch = !search || 
                                        idValue.includes(search) || 
                                        nameValue.includes(search);
                    const matchesDate = !date || dateValue.includes(date);

                    row.style.display = (matchesLab && matchesSearch && matchesDate) ? '' : 'none';
                });

                // Count visible rows
                const visibleRows = Array.from(document.querySelectorAll('#pendingReservationsTable tbody tr')).filter(row => 
                    row.style.display !== 'none' && !row.querySelector('td[colspan]')
                ).length;

                // Display "No results" message if needed
                let noResultsRow = document.querySelector('.no-results-row');
                
                if (visibleRows === 0) {
                    if (!noResultsRow) {
                        const tbody = document.querySelector('#pendingReservationsTable tbody');
                        noResultsRow = document.createElement('tr');
                        noResultsRow.className = 'no-results-row';
                        noResultsRow.innerHTML = `
                            <td colspan="7" class="text-center py-4 text-muted">
                                No reservations matching your filters
                            </td>
                        `;
                        tbody.appendChild(noResultsRow);
                    }
                } else if (noResultsRow) {
                    noResultsRow.remove();
                }
            }

            // Attach event listeners to filters
            labFilter.addEventListener('change', applyFilters);
            searchFilter.addEventListener('input', applyFilters);
            dateFilter.addEventListener('input', applyFilters);

            // Reset filters
            window.resetFilters = function() {
                labFilter.value = '';
                searchFilter.value = '';
                dateFilter.value = '';
                applyFilters();
            };

            // Navbar search functionality
            document.getElementById('navbarSearch').addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    searchFilter.value = this.value;
                    applyFilters();
                    searchFilter.focus();
                }
            });

            // Export Logs to Excel
            document.getElementById('exportLogsBtn').addEventListener('click', function() {
                const table = document.getElementById('reservationLogsTable');
                const ws = XLSX.utils.table_to_sheet(table);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Reservation_Logs");
                
                // Format date for filename
                const now = new Date();
                const dateStr = now.toISOString().split('T')[0];
                
                // Generate and download Excel file
                XLSX.writeFile(wb, `Reservation_Logs_${dateStr}.xlsx`);
            });

            // Logout button handler
            document.getElementById('logoutBtn').addEventListener('click', function() {
                logoutModal.show();
            });

            // Function to force end a session
            document.getElementById('forceEndSession').addEventListener('click', function() {
                const sessionId = this.getAttribute('data-session-id');
                
                if (confirm('Are you sure you want to end this session?')) {
                    // Show loading state in button
                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                    this.disabled = true;
                    
                    fetch('end_session.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `session_id=${sessionId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        const pcUserModal = bootstrap.Modal.getInstance(document.getElementById('pcUserModal'));
                        pcUserModal.hide();
                        
                        if (data.success) {
                            // Update success modal
                            document.getElementById('successModalTitle').textContent = 'Session Ended';
                            document.getElementById('successModalIcon').innerHTML = `
                                <span class="avatar-initial rounded-circle bg-label-success">
                                    <i class="bi bi-check-lg"></i>
                                </span>
                            `;
                            document.getElementById('successModalMessage').textContent = 'Session was ended successfully.';
                            
                            // Show success modal
                            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                            successModal.show();
                            
                            // Reload PC grid
                            const labId = document.getElementById('currentLabId')?.value;
                            if (labId) {
                                loadLabPCs(labId);
                            }
                        } else {
                            alert('Error: ' + (data.message || 'Could not end session.'));
                        }
                        
                        // Reset button
                        this.innerHTML = '<i class="bi bi-box-arrow-right me-1"></i> End Session';
                        this.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Connection error. Please try again later.');
                        
                        // Reset button
                        this.innerHTML = '<i class="bi bi-box-arrow-right me-1"></i> End Session';
                        this.disabled = false;
                    });
                }
            });
        });

        // PC Availability functions - add these outside the DOMContentLoaded event
        function loadLabPCs(labId) {
            const container = document.getElementById('labControlContainer');
            
            // Add hidden input to track current lab
            let currentLabInput = document.getElementById('currentLabId');
            if (!currentLabInput) {
                currentLabInput = document.createElement('input');
                currentLabInput.type = 'hidden';
                currentLabInput.id = 'currentLabId';
                container.appendChild(currentLabInput);
            }
            currentLabInput.value = labId;
            
            // Show loading spinner
            container.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h6 class="mb-2">Loading PC Status</h6>
                    <p class="text-muted mb-0">Please wait while we fetch PC information</p>
                </div>
            `;
            
            // Fetch PC statuses from sit_in table
            fetch(`get_lab_pcs.php?lab=${labId}`)
                .then(response => response.json())
                .then(data => {
                    // Create teacher's desk
                    let html = `
                        <input type="hidden" id="currentLabId" value="${labId}">
                        <div class="teacher-desk mb-4">
                            <i class="bi bi-person-workspace me-2"></i>
                            <span class="fw-semibold">Laboratory ${labId} - Teacher's Desk</span>
                        </div>
                    `;
                    
                    // Create PC grid with scrollable container
                    html += `<div class="pc-grid-container"><div class="pc-grid">`;
                    
                    // Track which PCs are accounted for
                    const accountedPCs = new Set();
                    
                    for (let i = 1; i <= 30; i++) {
                        // Find PC in returned data
                        const pcData = data.find(pc => pc.pc_number == i);
                        let statusClass, statusText, userInfo = '', actionButtons = '', toggleButton = '';
                        
                        if (pcData) {
                            accountedPCs.add(i);
                            
                            if (pcData.status === 'in-use') {
                                // PC is in use
                                statusClass = 'in-use';
                                statusText = 'IN USE';
                                userInfo = `<div class="pc-user text-truncate">${pcData.fullname || ''}</div>`;
                                actionButtons = `
                                    <button type="button" class="btn btn-sm btn-info" 
                                        onclick="viewUserInfo(${labId}, ${i}, '${pcData.id}', '${pcData.idno}', '${pcData.fullname}', '${pcData.time_in}', '${pcData.purpose}')">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                `;
                                
                                // Add toggle button
                                toggleButton = `
                                    <button type="button" class="btn btn-danger pc-toggle-btn" 
                                        onclick="togglePcStatus(${labId}, ${i}, '${pcData.id}', 'make-available')" 
                                        title="End session & make available">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                `;
                            } else if (pcData.status === 'unavailable') {
                                // PC is marked as unavailable
                                statusClass = 'unavailable';
                                statusText = 'UNAVAILABLE';
                                toggleButton = `
                                    <button type="button" class="btn btn-success pc-toggle-btn" 
                                        onclick="togglePcStatus(${labId}, ${i}, null, 'make-available')" 
                                        title="Make available">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                `;
                            }
                        } else {
                            // PC is available
                            statusClass = 'available';
                            statusText = 'AVAILABLE';
                            toggleButton = `
                                <button type="button" class="btn btn-secondary pc-toggle-btn" 
                                    onclick="togglePcStatus(${labId}, ${i}, null, 'make-unavailable')" 
                                    title="Make unavailable">
                                    <i class="bi bi-dash-lg"></i>
                                </button>
                            `;
                        }
                        
                        html += `
                            <div class="pc-card ${statusClass}">
                                ${toggleButton}
                                <div class="pc-icon">
                                    <i class="bi bi-display"></i>
                                </div>
                                <div class="pc-number">PC ${i}</div>
                                <div class="pc-status">${statusText}</div>
                                ${userInfo}
                                ${actionButtons ? `
                                    <div class="mt-2">
                                        ${actionButtons}
                                    </div>
                                ` : ''}
                            </div>
                        `;
                    }
                    
                    html += `</div></div>`;
                    
                    // Add legend
                    html += `
                        <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-label-success me-2">
                                    <i class="bi bi-square-fill"></i>
                                </span>
                                <span>Available</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-label-primary me-2">
                                    <i class="bi bi-square-fill"></i>
                                </span>
                                <span>In Use</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-label-danger me-2">
                                    <i class="bi bi-square-fill"></i>
                                </span>
                                <span>Unavailable</span>
                            </div>
                        </div>
                    `;
                    
                    container.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = `
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            </div>
                            <h6 class="mb-2">Connection Error</h6>
                            <p class="text-muted mb-0">Could not connect to the server. Please try again later.</p>
                        </div>
                    `;
                });
        }

        // Function to view user information
        function viewUserInfo(labId, pcNumber, sessionId, userId, userName, timeIn, purpose) {
            const pcUserModal = new bootstrap.Modal(document.getElementById('pcUserModal'));
            
            // Update modal content
            document.getElementById('userPcIdentifier').textContent = `Laboratory ${labId} - PC ${pcNumber}`;
            document.getElementById('userId').textContent = userId;
            document.getElementById('userName').textContent = userName;
            document.getElementById('userTimeIn').textContent = timeIn;
            document.getElementById('userPurpose').textContent = purpose;
            
            // Set session ID for end session function
            document.getElementById('forceEndSession').setAttribute('data-session-id', sessionId);
            
            // Show modal
            pcUserModal.show();
        }

        // Add this function to toggle PC availability status
        function togglePcStatus(labId, pcNumber, sessionId, action) {
            // Show confirmation based on action
            let confirmMessage = '';
            if (action === 'make-unavailable') {
                confirmMessage = `Are you sure you want to mark PC ${pcNumber} as unavailable?`;
            } else if (action === 'make-available') {
                confirmMessage = sessionId 
                    ? `Are you sure you want to end the current session and mark PC ${pcNumber} as available?` 
                    : `Are you sure you want to mark PC ${pcNumber} as available?`;
            }
            
            if (confirm(confirmMessage)) {
                // Show processing overlay
                const overlay = document.createElement('div');
                overlay.style.position = 'fixed';
                overlay.style.top = '0';
                overlay.style.left = '0';
                overlay.style.width = '100%';
                overlay.style.height = '100%';
                overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                overlay.style.display = 'flex';
                overlay.style.alignItems = 'center';
                overlay.style.justifyContent = 'center';
                overlay.style.zIndex = '9999';
                overlay.innerHTML = `
                    <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Processing...</span>
                    </div>
                `;
                document.body.appendChild(overlay);
                
                // Prepare request data
                const requestData = {
                    lab_id: labId,
                    pc_number: pcNumber,
                    action: action
                };
                
                if (sessionId) {
                    requestData.session_id = sessionId;
                }
                
                // Send request to update PC status
                fetch('update_pc_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                })
                .then(response => response.json())
                .then(data => {
                    // Remove overlay
                    document.body.removeChild(overlay);
                    
                    if (data.success) {
                        // Show success message
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        document.getElementById('successModalTitle').textContent = 'PC Status Updated';
                        document.getElementById('successModalIcon').innerHTML = `
                            <span class="avatar-initial rounded-circle bg-label-success">
                                <i class="bi bi-check-lg"></i>
                            </span>
                        `;
                        document.getElementById('successModalMessage').textContent = data.message;
                        
                        // Reload PC grid on success
                        successModal.show();
                        
                        // Add event listener for modal close
                        document.getElementById('successModalContinue').addEventListener('click', function() {
                            loadLabPCs(labId);
                        }, { once: true });
                    } else {
                        // Show error message
                        alert('Error: ' + (data.message || 'Failed to update PC status'));
                    }
                })
                .catch(error => {
                    // Remove overlay
                    document.body.removeChild(overlay);
                    
                    console.error('Error:', error);
                    alert('Connection error. Please try again later.');
                });
            }
        }
    </script>
</body>
</html>