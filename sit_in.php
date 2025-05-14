<?php
session_start();
include 'db.php';

// Debugging: Ensure the database connection works
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Disable caching to always get the latest session count
$conn->query("SET SESSION query_cache_type = OFF;");

// Keep your existing AJAX handlers

// For get_points action
if (isset($_GET['action']) && $_GET['action'] === 'get_points') {
    header('Content-Type: application/json');
    
    $sql = "SELECT 
        u.id_no as idno,
        CONCAT(u.last_name, ', ', u.first_name, ' ', COALESCE(u.middle_name, '')) as full_name,
        u.course,
        u.year_level,
        u.points,
        (SELECT COUNT(*) FROM points_history WHERE user_id = u.id) as total_points,
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
                'total_points' => htmlspecialchars($row['total_points'] ?? 0),
                'current_sessions' => htmlspecialchars($row['current_sessions'])
            );
        }
    }
    
    echo json_encode(['success' => true, 'students' => $students]);
    exit;
}

// For logout_id action
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

// For add_point action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_point'])) {
    $idno = $_POST['idno'];
    $conn->begin_transaction();
    
    try {
        // Keep your existing add_point logic here
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

        // After successfully adding a point
        $history_stmt = $conn->prepare("INSERT INTO points_history (user_id, points) VALUES ((SELECT id FROM users WHERE id_no = ?), 1)");
        $history_stmt->bind_param("s", $idno);
        $history_stmt->execute();
        $history_stmt->close();
        
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

// Keep other AJAX handlers as is
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Sit-in | Admin</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

    <!-- Icons. Required if you use Bootstrap Icons-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Sneat Template Core CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/css/demo.css" />
    
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

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
                            <div>Dashboard</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Management</span>
                    </li>

                    <!-- Search -->
                    <li class="menu-item">
                        <a href="search.php" class="menu-link">
                            <i class="menu-icon bi bi-search"></i>
                            <div>Search</div>
                        </a>
                    </li>

                    <!-- Students -->
                    <li class="menu-item">
                        <a href="students.php" class="menu-link">
                            <i class="menu-icon bi bi-people"></i>
                            <div>Students</div>
                        </a>
                    </li>

                    <!-- Sit-in -->
                    <li class="menu-item active">
                        <a href="sit_in.php" class="menu-link">
                            <i class="menu-icon bi bi-clipboard-check"></i>
                            <div>Sit-in</div>
                        </a>
                    </li>

                    <!-- View Records -->
                    <li class="menu-item">
                        <a href="sit_in_records.php" class="menu-link">
                            <i class="menu-icon bi bi-clipboard-data"></i>
                            <div>View Records</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Features</span>
                    </li>

                    <!-- Reservation -->
                    <li class="menu-item">
                        <a href="admin_reservation.php" class="menu-link">
                            <i class="menu-icon bi bi-calendar-check"></i>
                            <div>Reservation</div>
                        </a>
                    </li>

                    <!-- Reports -->
                    <li class="menu-item">
                        <a href="reports.php" class="menu-link">
                            <i class="menu-icon bi bi-file-earmark-bar-graph"></i>
                            <div>Reports</div>
                        </a>
                    </li>

                    <!-- Feedback Reports -->
                    <li class="menu-item">
                        <a href="feedback.php" class="menu-link">
                            <i class="menu-icon bi bi-chat-left-text"></i>
                            <div>Feedback Reports</div>
                        </a>
                    </li>

                    <!-- Resources -->
                    <li class="menu-item">
                        <a href="lab_resources.php" class="menu-link">
                            <i class="menu-icon bi bi-box"></i>
                            <div>Resources</div>
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
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="https://ui-avatars.com/api/?name=Admin&background=696cff&color=fff" alt class="w-px-40 h-auto rounded-circle">
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="https://ui-avatars.com/api/?name=Admin&background=696cff&color=fff" alt class="w-px-40 h-auto rounded-circle">
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">Admin</span>
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
                            <span class="text-muted fw-light">Student Management /</span> Current Sit-in Sessions
                        </h4>
                        
                        <!-- Current Sit-in Sessions Card -->
                        <div class="card">
                            <h5 class="card-header">Current Sit-in Sessions</h5>
                            
                            <!-- Tabs -->
                            <ul class="nav nav-tabs" id="sitInTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="direct-tab" data-bs-toggle="tab" data-bs-target="#direct-sessions" 
                                        type="button" role="tab" aria-controls="direct-sessions" aria-selected="true">
                                        <i class="bi bi-search me-1"></i> Direct Sit-ins
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="reservation-tab" data-bs-toggle="tab" data-bs-target="#reservation-sessions" 
                                        type="button" role="tab" aria-controls="reservation-sessions" aria-selected="false">
                                        <i class="bi bi-calendar-check me-1"></i> Reservation Sit-ins
                                    </button>
                                </li>
                            </ul>
                            
                            <!-- Tab Content -->
                            <div class="tab-content" id="sitInTabContent">
                                <!-- Direct Sit-ins Tab -->
                                <div class="tab-pane fade show active" id="direct-sessions" role="tabpanel" aria-labelledby="direct-tab">
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID Number</th>
                                                    <th>Name</th>
                                                    <th>Purpose</th>
                                                    <th>Sit Lab</th>
                                                    <th>Session</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                <?php
                                                $sql = "SELECT id, idno, fullname, purpose, laboratory, session_count 
                                                        FROM sit_in 
                                                        WHERE time_out IS NULL AND source = 'direct' 
                                                        ORDER BY created_at DESC, id DESC";
                                                
                                                $result = $conn->query($sql);
                                                
                                                if (!$result) {
                                                    echo '<tr><td colspan="7" class="text-center text-danger">SQL Error: ' . $conn->error . '</td></tr>';
                                                } elseif ($result->num_rows == 0) {
                                                    echo '<tr><td colspan="7" class="text-center text-muted">No active direct sit-in sessions found</td></tr>';
                                                } else {
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo '<tr id="row-' . $row['id'] . '">';
                                                        echo '<td><i class="bi bi-person-badge me-2"></i>' . htmlspecialchars($row['idno']) . '</td>';
                                                        echo '<td>' . htmlspecialchars($row['fullname']) . '</td>';
                                                        echo '<td>' . htmlspecialchars($row['purpose']) . '</td>';
                                                        echo '<td>' . htmlspecialchars($row['laboratory']) . '</td>';
                                                        echo '<td><span class="badge bg-label-primary">' . htmlspecialchars($row['session_count']) . '</span></td>';
                                                        echo '<td><span class="badge bg-label-success">Active</span></td>';
                                                        echo '<td class="text-center">';
                                                        // Add button
                                                        echo '<button class="add-point-btn btn btn-icon btn-sm btn-outline-primary me-2" data-idno="' . htmlspecialchars($row['idno']) . '">';
                                                        echo '<i class="bi bi-plus"></i>';
                                                        echo '</button>';
                                                        // Time Out button
                                                        echo '<button class="logout-btn btn btn-sm btn-warning" data-id="' . $row['id'] . '">';
                                                        echo '<i class="bi bi-box-arrow-right me-1"></i>Time Out</button>';
                                                        echo '</td>';
                                                        echo '</tr>';
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Reservation Sit-ins Tab -->
                                <div class="tab-pane fade" id="reservation-sessions" role="tabpanel" aria-labelledby="reservation-tab">
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID Number</th>
                                                    <th>Name</th>
                                                    <th>Purpose</th>
                                                    <th>Sit Lab</th>
                                                    <th>PC No.</th>
                                                    <th>Session</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                <?php
                                                $sql = "SELECT s.id, s.idno, s.fullname, s.purpose, s.laboratory, s.session_count, 
                                                               r.pc_number 
                                                        FROM sit_in s
                                                        LEFT JOIN reservations r ON s.reservation_id = r.id
                                                        WHERE s.time_out IS NULL AND s.source = 'reservation' 
                                                        ORDER BY s.created_at DESC, s.id DESC";
                                                
                                                $result = $conn->query($sql);
                                                
                                                if (!$result) {
                                                    echo '<tr><td colspan="8" class="text-center text-danger">SQL Error: ' . $conn->error . '</td></tr>';
                                                } elseif ($result->num_rows == 0) {
                                                    echo '<tr><td colspan="8" class="text-center text-muted">No active reservation sit-in sessions found</td></tr>';
                                                } else {
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo '<tr id="row-' . $row['id'] . '">';
                                                        echo '<td><i class="bi bi-person-badge me-2"></i>' . htmlspecialchars($row['idno']) . '</td>';
                                                        echo '<td>' . htmlspecialchars($row['fullname']) . '</td>';
                                                        echo '<td>' . htmlspecialchars($row['purpose']) . '</td>';
                                                        echo '<td>' . htmlspecialchars($row['laboratory']) . '</td>';
                                                        echo '<td>' . htmlspecialchars($row['pc_number'] ?? 'N/A') . '</td>';
                                                        echo '<td><span class="badge bg-label-primary">' . htmlspecialchars($row['session_count']) . '</span></td>';
                                                        echo '<td><span class="badge bg-label-success">Active</span></td>';
                                                        echo '<td class="text-center">';
                                                        // Add button
                                                        echo '<button class="add-point-btn btn btn-icon btn-sm btn-outline-primary me-2" data-idno="' . htmlspecialchars($row['idno']) . '">';
                                                        echo '<i class="bi bi-plus"></i>';
                                                        echo '</button>';
                                                        // Time Out button
                                                        echo '<button class="logout-btn btn btn-sm btn-warning" data-id="' . $row['id'] . '">';
                                                        echo '<i class="bi bi-box-arrow-right me-1"></i>Time Out</button>';
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
                        </div>
                        <!-- / Current Sit-in Sessions Card -->
                        
                        <!-- Student Points Card -->
                        <div class="card mt-4">
                            <h5 class="card-header">Student Points</h5>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover student-points-table">
                                    <thead>
                                        <tr>
                                            <th>ID Number</th>
                                            <th>Name</th>
                                            <th>Course</th>
                                            <th>Year Level</th>
                                            <th>Points <small class="text-muted">(Current)</small></th>
                                            <th>Total Points <small class="text-muted">(Lifetime)</small></th>
                                            <th>Current Sessions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        <?php
                                        // Modify your SQL query to include total points calculation
                                        $sql = "SELECT 
                                            u.id_no as idno,
                                            CONCAT(u.last_name, ', ', u.first_name, ' ', COALESCE(u.middle_name, '')) as full_name,
                                            u.course,
                                            u.year_level,
                                            u.points,
                                            (SELECT COUNT(*) FROM points_history WHERE user_id = u.id) as total_points,
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
                                            echo '<tr><td colspan="7" class="text-center text-danger">SQL Error: ' . $conn->error . '</td></tr>';
                                        } elseif ($result->num_rows == 0) {
                                            echo '<tr><td colspan="7" class="text-center text-muted">No active students found</td></tr>';
                                        } else {
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<tr>';
                                                echo '<td><i class="bi bi-person-badge me-2"></i>' . htmlspecialchars($row['idno']) . '</td>';
                                                echo '<td>' . htmlspecialchars($row['full_name']) . '</td>';
                                                echo '<td>' . htmlspecialchars($row['course']) . '</td>';
                                                echo '<td>' . htmlspecialchars($row['year_level']) . '</td>';
                                                echo '<td><span class="badge bg-label-info">' . htmlspecialchars($row['points']) . '</span></td>';
                                                echo '<td><span class="badge bg-label-success">' . htmlspecialchars($row['total_points'] ?? 0) . '</span></td>';
                                                echo '<td><span class="badge bg-label-primary">' . htmlspecialchars($row['current_sessions']) . '</span></td>';
                                                echo '</tr>';
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- / Student Points Card -->
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

    <!-- Modals -->
    <!-- Confirm Timeout Modal -->
    <div class="modal fade" id="confirmTimeoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Time Out</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3 text-center">
                            <div class="avatar avatar-md mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-warning">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </span>
                            </div>
                            <p>Are you sure you want to time out this student?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmTimeOut">
                        Time Out
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Success!</h5>
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
                            <p id="successMessage"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Max Sessions Modal -->
    <div class="modal fade" id="maxSessionsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Maximum Sessions Reached</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3 text-center">
                            <div class="avatar avatar-md mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-danger">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </span>
                            </div>
                            <p>Cannot add more sessions. Maximum limit of 30 sessions reached for this user.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        Understood
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Points Success Modal -->
    <div class="modal fade" id="addPointsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Points Added!</h5>
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
                            <p id="pointsMessage"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        Continue
                    </button>
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
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
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

    <!-- Main JS -->
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/js/main.js"></script>

    <script>
        // Function to update student points table
        function updateStudentPoints() {
            fetch('sit_in.php?action=get_points', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.students) {
                    const tbody = document.querySelector('.student-points-table tbody');
                    if (!tbody) return;
                    
                    tbody.innerHTML = data.students.map(student => `
                        <tr>
                            <td><i class="bi bi-person-badge me-2"></i>${student.idno}</td>
                            <td>${student.full_name}</td>
                            <td>${student.course}</td>
                            <td>${student.year_level}</td>
                            <td><span class="badge bg-label-info">${student.points}</span></td>
                            <td><span class="badge bg-label-success">${student.total_points || 0}</span></td>
                            <td><span class="badge bg-label-primary">${student.current_sessions}</span></td>
                        </tr>
                    `).join('');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        document.addEventListener("DOMContentLoaded", function() {
            let currentLogoutId = null;
            let currentRow = null;

            // Logout button handler
            document.querySelectorAll(".logout-btn").forEach(button => {
                button.addEventListener("click", function() {
                    currentLogoutId = this.getAttribute("data-id");
                    currentRow = document.getElementById("row-" + currentLogoutId);
                    const modal = new bootstrap.Modal(document.getElementById('confirmTimeoutModal'));
                    modal.show();
                });
            });

            // Confirm timeout button handler
            document.getElementById('confirmTimeOut').addEventListener('click', function() {
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmTimeoutModal'));
                modal.hide();
                
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
                        document.getElementById('successMessage').textContent = data.message;
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();
                        // Update student points table after successful timeout
                        updateStudentPoints();
                    } else {
                        document.getElementById('successMessage').textContent = "Error: " + data.message;
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    document.getElementById('successMessage').textContent = "An error occurred while processing your request.";
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                });
            });

            // Logout button handler
            document.getElementById('logoutBtn').addEventListener('click', function() {
                const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
                logoutModal.show();
            });

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
                            const addPointsModal = new bootstrap.Modal(document.getElementById('addPointsModal'));
                            addPointsModal.show();
                            // Update student points table
                            updateStudentPoints();
                        } else if (data.maxSessionsReached) {
                            const maxSessionsModal = new bootstrap.Modal(document.getElementById('maxSessionsModal'));
                            maxSessionsModal.show();
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

            // Modal events - refresh page after success
            document.getElementById('addPointsModal').addEventListener('hidden.bs.modal', function () {
                // No need to refresh when using AJAX update
                // updateStudentPoints();
            });

            document.getElementById('successModal').addEventListener('hidden.bs.modal', function () {
                // No need to refresh when using AJAX update
                // window.location.reload();
            });
        });
    </script>
</body>
</html>