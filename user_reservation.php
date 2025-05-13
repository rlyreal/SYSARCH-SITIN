<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Add this before processing the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user has active session
    $stmt = $conn->prepare("SELECT id FROM sit_in WHERE idno = ? AND time_out IS NULL");
    $stmt->bind_param("s", $id_no);
    $stmt->execute();
    $active_session = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($active_session) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showErrorModal('Active Session Found', 'You currently have an active session. Please complete your current session before making a new reservation.');
            });
        </script>";
        exit;
    }

    // Check session count
    $stmt = $conn->prepare("SELECT session_count FROM sit_in WHERE idno = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $id_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $session_data = $result->fetch_assoc();
    $current_sessions = $session_data ? $session_data['session_count'] : 30;

    if ($current_sessions <= 0) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showErrorModal('No Sessions Available', 'You have no sessions remaining.');
            });
        </script>";
        exit;
    }

    $id_no = $_POST['id_number'];
    $full_name = $_POST['full_name'];
    $course = $_POST['course'];
    $year_level = $_POST['year_level'];
    $purpose = $_POST['purpose'];
    $laboratory = $_POST['laboratory'];
    $date = $_POST['date'];
    $time_in = $_POST['time_in'];
    $pc_number = $_POST['pc_number'];
    $status = $_POST['status'];

    // Insert into reservations table
    $stmt = $conn->prepare("INSERT INTO reservations (idno, full_name, course, year_level, purpose, laboratory, date, time_in, pc_number, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssssssss", $id_no, $full_name, $course, $year_level, $purpose, $laboratory, $date, $time_in, $pc_number, $status);
    
    if ($stmt->execute()) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showSuccessModal();
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showErrorModal('Error', 'Error submitting reservation.');
            });
        </script>";
    }
    $stmt->close();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id_no, last_name, first_name, middle_name, course, year_level, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($id_no, $last_name, $first_name, $middle_name, $course, $year_level, $profile_picture);
$stmt->fetch();
$stmt->close();

// Format full name
$full_name = "$last_name, $first_name " . ($middle_name ? "$middle_name" : "");

// Check if profile picture exists, otherwise use default
$profile_picture = (!empty($profile_picture) && file_exists($profile_picture)) ? $profile_picture : "profile.jpg";

// Fetch session count
$stmt = $conn->prepare("SELECT 
    CASE 
        WHEN EXISTS (SELECT 1 FROM sit_in WHERE idno = ? AND time_out IS NULL)
        THEN 'Active Session'
        ELSE COALESCE((SELECT session_count FROM sit_in WHERE idno = ? ORDER BY id DESC LIMIT 1), 30)
    END as session_status");
$stmt->bind_param("ss", $id_no, $id_no);
$stmt->execute();
$stmt->bind_result($session_status);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>Laboratory Reservation | Sit-In System</title>
    
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

    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    
    <!-- Page CSS -->
    <style>
        .pc-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
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
        
        .pc-button {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            border: 2px solid #e0e0e0;
            background-color: white;
            transition: all 0.3s;
        }
        
        .pc-button:hover:not([disabled]) {
            border-color: #696cff;
            background-color: #f5f5ff;
        }
        
        .pc-button.selected {
            border-color: #696cff;
            background-color: #eaeaff;
        }
        
        .pc-button[disabled] {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .pc-button .pc-icon {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            color: #697a8d;
        }
        
        .pc-button .pc-number {
            font-weight: 600;
        }
        
        .pc-button .pc-status {
            font-size: 0.75rem;
        }
        
        .pc-button .pc-status.available {
            color: #71dd37;
        }
        
        .pc-button .pc-status.unavailable {
            color: #ff3e1d;
        }
        
        /* For the teacher's table representation */
        .teacher-desk {
            background-color: #f6f8fa;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px dashed #d9dee3;
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
                    <li class="menu-item active">
                        <a href="user_reservation.php" class="menu-link">
                            <i class="menu-icon bi bi-calendar-check"></i>
                            <div data-i18n="Reservation">Reservation</div>
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
                                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="profile" class="w-px-40 h-auto rounded-circle">
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="profile" class="w-px-40 h-auto rounded-circle">
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
                        <h4 class="fw-bold py-3 mb-4">
                            <i class="bi bi-calendar-plus me-1"></i> Laboratory Reservation
                        </h4>

                        <div class="row">
                            <!-- Reservation Form -->
                            <div class="col-md-5">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Reservation Details</h5>
                                        <span class="badge bg-label-primary">Fill all fields</span>
                                    </div>
                                    <div class="card-body">
                                        <form id="reservationForm" method="POST">
                                            <div class="row mb-3">
                                                <label class="col-sm-3 col-form-label" for="id_number">ID Number</label>
                                                <div class="col-sm-9">
                                                    <input type="text" 
                                                        class="form-control" 
                                                        id="id_number" 
                                                        name="id_number" 
                                                        value="<?php echo htmlspecialchars($id_no); ?>" 
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-3 col-form-label" for="full_name">Full Name</label>
                                                <div class="col-sm-9">
                                                    <input type="text" 
                                                        class="form-control" 
                                                        id="full_name" 
                                                        name="full_name" 
                                                        value="<?php echo htmlspecialchars($full_name); ?>" 
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-3 col-form-label" for="course">Course</label>
                                                <div class="col-sm-9">
                                                    <input type="text" 
                                                        class="form-control" 
                                                        id="course" 
                                                        name="course" 
                                                        value="<?php echo htmlspecialchars($course); ?>" 
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-3 col-form-label" for="year_level">Year Level</label>
                                                <div class="col-sm-9">
                                                    <input type="text" 
                                                        class="form-control" 
                                                        id="year_level" 
                                                        name="year_level" 
                                                        value="<?php echo htmlspecialchars($year_level); ?>" 
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-3 col-form-label" for="purpose">Purpose</label>
                                                <div class="col-sm-9">
                                                    <select class="form-select" id="purpose" name="purpose" required>
                                                        <option value="" disabled selected>Select Purpose</option>
                                                        <option value="C Programming">C Programming</option>
                                                        <option value="C++ Programming">C++ Programming</option>
                                                        <option value="C# Programming">C# Programming</option>
                                                        <option value="Java Programming">Java Programming</option>
                                                        <option value="Python Programming">Python Programming</option>
                                                        <option value="Database">Database</option>
                                                        <option value="Digital Logic & Design">Digital Logic & Design</option>
                                                        <option value="Embedded System & IOT">Embedded System & IOT</option>
                                                        <option value="System Integration & Architecture">System Integration & Architecture</option>
                                                        <option value="Computer Application">Computer Application</option>
                                                        <option value="Web Design & Development">Web Design & Development</option>
                                                        <option value="Project Management">Project Management</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-3 col-form-label" for="laboratory">Laboratory</label>
                                                <div class="col-sm-9">
                                                    <select class="form-select" id="laboratory" name="laboratory" required onchange="updatePcOptions()">
                                                        <option value="" disabled selected>Select Laboratory</option>
                                                        <?php foreach ([517, 524, 526, 528, 530, 542, 544] as $labNumber): ?>
                                                            <option value="<?php echo $labNumber; ?>">Laboratory <?php echo $labNumber; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-3 col-form-label" for="date">Date</label>
                                                <div class="col-sm-9">
                                                    <input type="date" 
                                                        class="form-control" 
                                                        id="date" 
                                                        name="date" 
                                                        required
                                                        min="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-3 col-form-label" for="time_in">Time In</label>
                                                <div class="col-sm-9">
                                                    <input type="time" 
                                                        class="form-control" 
                                                        id="time_in" 
                                                        name="time_in" 
                                                        required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-3 col-form-label">Sessions</label>
                                                <div class="col-sm-9">
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="bi bi-clipboard-check"></i>
                                                        </span>
                                                        <div class="form-control bg-lighter">
                                                            <?php if ($session_status === 'Active Session'): ?>
                                                                <span class="text-primary fw-semibold">Currently in session</span>
                                                            <?php else: ?>
                                                                <span class="<?php echo ($session_status <= 0) ? 'text-danger' : 'text-success'; ?> fw-semibold">
                                                                    <?php echo $session_status; ?> sessions remaining
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <?php if ($session_status <= 0): ?>
                                                        <div class="mt-1 text-danger small">
                                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                            No sessions remaining
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12 d-flex justify-content-end">
                                                    <button type="button" id="proceedToSelection" class="btn btn-primary">
                                                        <i class="bi bi-arrow-right me-1"></i> Next: Select PC
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- / Reservation Form -->

                            <!-- PC Selection -->
                            <div class="col-md-7">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">PC Selection</h5>
                                        <div>
                                            <span class="badge bg-label-success me-1">
                                                <i class="bi bi-square-fill me-1"></i> Available
                                            </span>
                                            <span class="badge bg-label-danger">
                                                <i class="bi bi-square-fill me-1"></i> Unavailable
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="teacher-desk mb-4">
                                            <i class="bi bi-person-workspace me-2"></i>
                                            <span class="fw-semibold">Teacher's Desk</span>
                                        </div>
                                        
                                        <div id="pcSelectionContainer" class="mb-4">
                                            <div class="text-center py-5">
                                                <div class="mb-3">
                                                    <i class="bi bi-display text-primary" style="font-size: 3rem;"></i>
                                                </div>
                                                <h6 class="mb-2">Select Laboratory First</h6>
                                                <p class="text-muted mb-0">Please fill out the reservation form to view available PCs</p>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center">
                                            <button type="button" id="confirmSelection" class="btn btn-primary btn-lg" disabled>
                                                <i class="bi bi-check-circle me-1"></i> Confirm Reservation
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- / PC Selection -->
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
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize form validation and button states
            const form = document.getElementById('reservationForm');
            const proceedBtn = document.getElementById('proceedToSelection');
            const confirmBtn = document.getElementById('confirmSelection');
            const requiredFields = form.querySelectorAll('[required]');
            
            // Check form validity
            function checkFormValidity() {
                let valid = true;
                requiredFields.forEach(field => {
                    if (!field.value) {
                        valid = false;
                    }
                });
                return valid;
            }
            
            // Proceed to PC selection
            proceedBtn.addEventListener('click', function() {
                if (checkFormValidity()) {
                    updatePcOptions();
                    // Scroll to PC selection on mobile
                    if (window.innerWidth < 768) {
                        document.querySelector('.card:nth-child(2)').scrollIntoView({ behavior: 'smooth' });
                    }
                } else {
                    showErrorModal('Form Incomplete', 'Please fill in all required fields before proceeding to PC selection.');
                }
            });
            
            // Logout button
            document.getElementById('logoutBtn').addEventListener('click', function() {
                const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
                logoutModal.show();
            });
            
            // Confirm selection button
            confirmBtn.addEventListener('click', function() {
                const selectedPc = document.querySelector('.pc-button.selected');
                if (!selectedPc) {
                    showErrorModal('PC Not Selected', 'Please select a PC before confirming your reservation.');
                    return;
                }
                
                // Check if user has active session
                const sessionStatus = document.querySelector('.input-group .form-control').textContent.trim();
                if (sessionStatus === 'Currently in session') {
                    showErrorModal('Active Session Found', 'You currently have an active session. Please complete your current session before making a new reservation.');
                    return;
                }
                
                // Show confirmation dialog
                const pcNumber = selectedPc.getAttribute('data-pc');
                const laboratory = document.getElementById('laboratory').value;
                const date = document.getElementById('date').value;
                const time = document.getElementById('time_in').value;
                
                // Format date for display
                const formatDate = new Date(date).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                
                // Format time for display
                const formatTime = time => {
                    const [hours, minutes] = time.split(':');
                    const hour = parseInt(hours);
                    return `${hour % 12 || 12}:${minutes} ${hour >= 12 ? 'PM' : 'AM'}`;
                };
                
                Swal.fire({
                    title: 'Confirm Reservation',
                    html: `
                        <div class="text-start">
                            <p><strong>Laboratory:</strong> Laboratory ${laboratory}</p>
                            <p><strong>PC Number:</strong> PC ${pcNumber}</p>
                            <p><strong>Date:</strong> ${formatDate}</p>
                            <p><strong>Time:</strong> ${formatTime(time)}</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm Reservation',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#696cff',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create hidden fields for PC number and status
                        const pcInput = document.createElement('input');
                        pcInput.type = 'hidden';
                        pcInput.name = 'pc_number';
                        pcInput.value = pcNumber;
                        form.appendChild(pcInput);
                        
                        const statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'status';
                        statusInput.value = 'pending';
                        form.appendChild(statusInput);
                        
                        // Submit the form
                        form.submit();
                    }
                });
            });
        });
        
        // Update PC options based on lab selection
        function updatePcOptions() {
            const selectedLab = document.getElementById('laboratory').value;
            const pcContainer = document.getElementById('pcSelectionContainer');
            const confirmBtn = document.getElementById('confirmSelection');
            
            if (!selectedLab) {
                pcContainer.innerHTML = `
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-display text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="mb-2">Select Laboratory First</h6>
                        <p class="text-muted mb-0">Please complete the reservation form to view available PCs</p>
                    </div>
                `;
                confirmBtn.disabled = true;
                return;
            }
            
            // Check if all required fields are filled
            const requiredFields = document.querySelectorAll('#reservationForm [required]');
            let allFieldsFilled = true;
            requiredFields.forEach(field => {
                if (!field.value) {
                    allFieldsFilled = false;
                }
            });
            
            if (!allFieldsFilled) {
                pcContainer.innerHTML = `
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="mb-2">Complete All Fields</h6>
                        <p class="text-muted mb-0">Please fill in all required fields in the reservation form</p>
                    </div>
                `;
                confirmBtn.disabled = true;
                return;
            }
            
            // Show loading indicator
            pcContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h6 class="mb-2">Loading PC Availability</h6>
                    <p class="text-muted mb-0">Please wait while we check available PCs</p>
                </div>
            `;
            
            // Fetch active PC usage
            const date = document.getElementById('date').value;
            const time = document.getElementById('time_in').value;
            
            fetch(`get_active_pcs.php?laboratory=${selectedLab}&date=${date}&time=${time}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Generate PC grid
                        const activePcs = data.active_pcs || [];
                        
                        // Format the PC selection grid
                        let gridHtml = `<div class="pc-grid">`;
                        for (let i = 1; i <= 30; i++) {
                            const isInUse = activePcs.includes(i.toString());
                            const statusClass = isInUse ? 'unavailable' : 'available';
                            const statusText = isInUse ? 'UNAVAILABLE' : 'AVAILABLE';
                            
                            gridHtml += `
                                <button type="button" 
                                    class="pc-button" 
                                    data-pc="${i}" 
                                    onclick="selectPc(${i})" 
                                    ${isInUse ? 'disabled' : ''}>
                                    <i class="bi bi-display pc-icon"></i>
                                    <span class="pc-number">PC ${i}</span>
                                    <span class="pc-status ${statusClass}">${statusText}</span>
                                </button>
                            `;
                        }
                        gridHtml += `</div>`;
                        
                        pcContainer.innerHTML = gridHtml;
                        
                        // Enable the confirm button if there are available PCs
                        const availablePCs = 30 - activePcs.length;
                        if (availablePCs === 0) {
                            // If no PCs available, show a message
                            pcContainer.innerHTML += `
                                <div class="alert alert-warning mt-3">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    No PCs are currently available in this laboratory for the selected date and time.
                                </div>
                            `;
                            confirmBtn.disabled = true;
                        } else {
                            // If PCs are available but none selected yet
                            confirmBtn.disabled = true;
                        }
                    } else {
                        pcContainer.innerHTML = `
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                                </div>
                                <h6 class="mb-2">Error Loading PC Status</h6>
                                <p class="text-muted mb-0">${data.message || 'Error loading PC status. Please try again.'}</p>
                            </div>
                        `;
                        confirmBtn.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    pcContainer.innerHTML = `
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                            </div>
                            <h6 class="mb-2">Connection Error</h6>
                            <p class="text-muted mb-0">Error connecting to the server. Please try again later.</p>
                        </div>
                    `;
                    confirmBtn.disabled = true;
                });
        }
        
        // Select PC function
        function selectPc(pcNumber) {
            const pcButtons = document.querySelectorAll('.pc-button');
            pcButtons.forEach(btn => btn.classList.remove('selected'));
            
            // Find and select the clicked PC
            const selectedButton = document.querySelector(`.pc-button[data-pc="${pcNumber}"]`);
            if (selectedButton) {
                selectedButton.classList.add('selected');
                
                // Enable confirm button
                document.getElementById('confirmSelection').disabled = false;
            }
        }
        
        // Show error modal
        function showErrorModal(title, message) {
            Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                confirmButtonText: 'Understood',
                confirmButtonColor: '#696cff',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        }
        
        // Show success modal
        function showSuccessModal() {
            Swal.fire({
                title: 'Reservation Submitted',
                text: 'Your reservation has been submitted successfully! Waiting for admin approval.',
                icon: 'success',
                confirmButtonText: 'Great!',
                confirmButtonColor: '#696cff',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            }).then(() => {
                window.location.href = 'dashboard.php';
            });
        }
    </script>
</body>
</html>