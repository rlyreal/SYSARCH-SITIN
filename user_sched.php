<?php
// filepath: c:\xampp\htdocs\SYSARCH-SITIN\user_sched.php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get filter parameters
$selected_lab = isset($_GET['lab']) ? $_GET['lab'] : '528';
$selected_day = isset($_GET['day']) ? $_GET['day'] : 'Wednesday';

// Convert lab number to full format for the query
$selected_lab_query = 'Lab ' . $selected_lab;

// Fetch schedules based on filters
$stmt = $conn->prepare("SELECT 
                      TIME_START as time_start, 
                      TIME_END as time_end, 
                      SUBJECT as subject, 
                      PROFESSOR as professor
                      FROM lab_schedule 
                      WHERE LABORATORY = ? AND DAY = ? 
                      ORDER BY TIME_START");
$stmt->bind_param("ss", $selected_lab_query, $selected_day);
$stmt->execute();
$result = $stmt->get_result();

// Get user information for the navigation bar
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT profile_picture, first_name, last_name, course FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_picture, $first_name, $last_name, $course);
$stmt->fetch();
$stmt->close();

$user_profile = (!empty($profile_picture) && file_exists($profile_picture)) ? $profile_picture : "profile.jpg";
$user_name = $first_name . ' ' . $last_name;
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>Laboratory Schedule | Sit-In System</title>
    
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
        .lab-btn {
            border-radius: 0.5rem;
            font-weight: 500;
            min-width: 60px;
            transition: all 0.2s;
        }
        
        .lab-btn.active {
            background-color: #696cff;
            color: white !important;
            box-shadow: 0 0.125rem 0.25rem rgba(105, 108, 255, 0.4);
        }
        
        .day-btn {
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .day-btn.active {
            background-color: #696cff;
            color: white !important;
            border-color: #696cff;
            box-shadow: 0 0.125rem 0.25rem rgba(105, 108, 255, 0.4);
        }
        
        .schedule-table th {
            background-color: #f5f5f9;
            color: #566a7f;
            font-weight: 500;
        }
        
        .schedule-row {
            transition: all 0.15s;
        }
        
        .schedule-row:hover {
            background-color: #f6f7fe;
        }
        
        .time-slot {
            display: flex;
            align-items: center;
        }
        
        .time-icon {
            color: #696cff;
            margin-right: 0.5rem;
        }
        
        .empty-schedule {
            text-align: center;
            padding: 3rem 1rem;
            color: #697a8d;
        }
        
        .empty-schedule i {
            font-size: 2.5rem;
            color: #d9dee3;
            margin-bottom: 1rem;
        }
        
        .schedule-info-badge {
            border-radius: 0.25rem;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            font-weight: 500;
        }
        
        .location-badge {
            background-color: #e7f0ff;
            color: #4784ff;
            margin-right: 0.5rem;
        }
        
        .day-badge {
            background-color: #e8fadf;
            color: #71dd37;
        }
        
        .professor-badge {
            background-color: #eee6ff;
            color: #696cff;
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            display: inline-flex;
            align-items: center;
            font-size: 0.8125rem;
        }
        
        .footer-note {
            font-size: 0.8125rem;
            color: #697a8d;
            font-style: italic;
            margin-top: 1rem;
        }
        
        .cursor-pointer {
            cursor: pointer;
        }
        
        .page-header {
            margin-bottom: 1.5rem;
        }
        
        .page-header h4 {
            color: #566a7f;
        }
        
        .page-header .breadcrumb-item {
            font-size: 0.85rem;
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
                    <li class="menu-item">
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
                    <li class="menu-item active">
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
                                                    <span class="fw-semibold d-block"><?php echo htmlspecialchars($user_name); ?></span>
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
                        <div class="page-header">
                            <div class="row align-items-center">
                                <div class="col-sm mb-2 mb-sm-0">
                                    <h4 class="fw-bold py-3 mb-0"><i class="bi bi-calendar3 me-2"></i>Laboratory Schedule</h4>
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                            <li class="breadcrumb-item active">Laboratory Schedule</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Laboratory Selection -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Select Laboratory</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php
                                            $labs = ['517', '524', '526', '528', '530', '542'];
                                            foreach ($labs as $lab) {
                                                $active = ($lab == $selected_lab) ? 'active' : '';
                                                echo '<a href="?lab='.$lab.'&day='.$selected_day.'" class="btn btn-outline-primary lab-btn ' . $active . '">'.$lab.'</a>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Day Selection -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Select Day</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            <?php
                                            // Updated to include Friday and Saturday
                                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                            foreach ($days as $day) {
                                                $active = ($day == $selected_day) ? 'active' : '';
                                                echo '<div class="col-md-4 col-6"><a href="?lab='.$selected_lab.'&day='.$day.'" class="btn btn-outline-primary day-btn '.$active.' w-100">'.substr($day, 0, 3).'</a></div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="schedule-info-badge location-badge"><i class="bi bi-geo-alt me-1"></i>Laboratory <?php echo $selected_lab; ?></span>
                                        <span class="schedule-info-badge day-badge"><i class="bi bi-calendar3 me-1"></i><?php echo $selected_day; ?></span>
                                    </div>
                                    <h5 class="card-title mb-0">Class Schedule</h5>
                                </div>
                                <div class="card-subtitle text-muted">View current laboratory schedules</div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-hover mb-0 schedule-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 25%">TIME SLOT</th>
                                                <th style="width: 50%">SUBJECT</th>
                                                <th style="width: 25%">PROFESSOR</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <?php 
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo '<tr class="schedule-row">';
                                                    // Format time slot
                                                    echo '<td>';
                                                    echo '<div class="time-slot">';
                                                    echo '<i class="bi bi-clock time-icon"></i>';
                                                    echo '<span>' . date('h:i A', strtotime($row['time_start'])) . ' - ' . date('h:i A', strtotime($row['time_end'])) . '</span>';
                                                    echo '</div>';
                                                    echo '</td>';
                                                    
                                                    // Subject
                                                    echo '<td><div class="fw-semibold">' . htmlspecialchars($row['subject']) . '</div></td>';
                                                    
                                                    // Professor
                                                    echo '<td><div class="professor-badge"><i class="bi bi-person-badge me-1"></i>' . htmlspecialchars($row['professor']) . '</div></td>';
                                                    echo '</tr>';
                                                }
                                            } else {
                                                echo '<tr>
                                                    <td colspan="3">
                                                        <div class="empty-schedule">
                                                            <i class="bi bi-calendar-x"></i>
                                                            <h5>No Classes Scheduled</h5>
                                                            <p>There are no classes scheduled for Laboratory ' . $selected_lab . ' on ' . $selected_day . '.</p>
                                                        </div>
                                                    </td>
                                                </tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <p class="footer-note mb-0"><i class="bi bi-info-circle me-1"></i> Schedule may change without prior notice. Please check regularly for updates.</p>
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

    <!-- Main JS -->
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logout button functionality
            const logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function() {
                    const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
                    logoutModal.show();
                });
            }
            
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>