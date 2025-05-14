<?php
include 'db.php';
session_start();

// Redirect if not logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$admin_username = $_SESSION['username'] ?? 'Admin User';

$sit_in = null;
$message = "";
$showModal = false;

if (isset($_POST['search']) && !empty($_POST['idno'])) {
    $idno = trim($_POST['idno']);

    // Fetch student details from users table
    $stmt = $conn->prepare("SELECT id_no, first_name, last_name FROM users WHERE id_no = ?");
    $stmt->bind_param("s", $idno);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        // Check for active sit-in status with no time_out
        $stmt = $conn->prepare("SELECT status FROM sit_in WHERE idno = ? AND status = 'active' AND time_out IS NULL LIMIT 1");
        $stmt->bind_param("s", $idno);
        $stmt->execute();
        $active_result = $stmt->get_result();
        $active_sitin = $active_result->fetch_assoc();
        $stmt->close();

        if ($active_sitin) {
            $message = '<div class="hidden" id="activeSitInError">active</div>';
            $showModal = false;
        } else {
            // Fetch last sit-in record
            $stmt = $conn->prepare("SELECT id, session_count FROM sit_in WHERE idno = ? ORDER BY id DESC LIMIT 1");
            $stmt->bind_param("s", $idno);
            $stmt->execute();
            $result = $stmt->get_result();
            $last_record = $result->fetch_assoc();
            $stmt->close();

            $session_count = $last_record['session_count'] ?? 30; // Default session count

            $sit_in = [
                'id' => $last_record['id'] ?? null,
                'idno' => $user['id_no'],
                'fullname' => $user['last_name'] . ', ' . $user['first_name'],
                'remaining_sessions' => $session_count
            ];
            
            $showModal = true;
        }
    } else {
        $message = '<div class="alert alert-danger">User not found!</div>';
    }
}

// Handle sit-in submission
if (isset($_POST['sit_in_submit']) && !empty($_POST['idno'])) {
    $idno = trim($_POST['idno']);
    $purpose = trim($_POST['purpose']);
    $laboratory = trim($_POST['laboratory']);
    $status = "active";

    // Get the last record with its session count
    $stmt = $conn->prepare("SELECT id, session_count FROM sit_in WHERE idno = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $idno);
    $stmt->execute();
    $result = $stmt->get_result();
    $last_record = $result->fetch_assoc();
    $stmt->close();

    if ($last_record) {
        // Use the existing session count
        $session_count = $last_record['session_count'];
        $source = "direct"; // Set source to direct for search.php

        // Update the existing record instead of creating a new one
        $stmt = $conn->prepare("UPDATE sit_in SET 
            purpose = ?,
            laboratory = ?,
            time_in = NOW(),
            time_out = NULL,
            status = ?,
            source = ?,
            date = CURRENT_DATE()
            WHERE id = ?");
        
        $stmt->bind_param("ssssi", $purpose, $laboratory, $status, $source, $last_record['id']);

        if ($stmt->execute()) {
            $message = '<div class="hidden" id="sitInSuccess" data-sessions="' . $session_count . '">success</div>';
        } else {
            $message = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
        }
        $stmt->close();
    } else {
        // First time sit-in, create new record with default 30 sessions
        $session_count = 30;
        $source = "direct"; // Set source to direct for search.php
        $reservation_id = NULL; // Explicitly set reservation_id to NULL

        $stmt = $conn->prepare("INSERT INTO sit_in (idno, fullname, purpose, laboratory, status, session_count, source, reservation_id, time_in, date) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), CURRENT_DATE())");
        $stmt->bind_param("ssssiisi", $idno, $_POST['fullname'], $purpose, $laboratory, $status, $session_count, $source, $reservation_id);

        if ($stmt->execute()) {
            $message = '<div class="hidden" id="sitInSuccess" data-sessions="' . $session_count . '">success</div>';
        } else {
            $message = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search & Sit-In | Admin</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap" rel="stylesheet">

    <!-- Icons. Required if you use Bootstrap Icons-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Sneat Template Core CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/css/demo.css" />
    
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->

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
                    <li class="menu-item active">
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
                    <li class="menu-item">
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
                            <span class="text-muted fw-light">Student Management /</span> Search & Sit-in
                        </h4>
                        
                        <!-- Search Form Card -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <h5 class="card-header">Search Student</h5>
                                    <div class="card-body">
                                        <?= $message ?>
                                        <form method="POST" action="">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="idno">Enter Student ID</label>
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               id="idno" 
                                                               name="idno" 
                                                               placeholder="Enter Student ID" 
                                                               required>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" name="search" class="btn btn-primary">
                                                <i class="bi bi-search me-1"></i> Search
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sit-in Form Card -->
                        <?php if ($sit_in): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <h5 class="card-header">Sit-in Form</h5>
                                    <div class="card-body">
                                        <form action="" method="POST">
                                            <input type="hidden" name="idno" value="<?= htmlspecialchars($sit_in['idno']) ?>">
                                            <input type="hidden" name="fullname" value="<?= htmlspecialchars($sit_in['fullname']) ?>">

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="basic-default-idno">ID Number</label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="basic-default-idno" 
                                                           value="<?= htmlspecialchars($sit_in['idno']) ?>" 
                                                           disabled>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label" for="basic-default-name">Student Name</label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="basic-default-name" 
                                                           value="<?= htmlspecialchars($sit_in['fullname']) ?>" 
                                                           disabled>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="purpose">Purpose</label>
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
                                                <div class="col-md-6">
                                                    <label class="form-label" for="laboratory">Laboratory</label>
                                                    <select class="form-select" id="laboratory" name="laboratory" required>
                                                        <option value="">Select Laboratory</option>
                                                        <option value="517">517</option>
                                                        <option value="524">524</option>
                                                        <option value="526">526</option>
                                                        <option value="528">528</option>
                                                        <option value="530">530</option>
                                                        <option value="542">542</option>
                                                        <option value="544">544</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="remaining-sessions">Remaining Sessions</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-clock-history"></i></span>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               id="remaining-sessions" 
                                                               value="<?= htmlspecialchars($sit_in['remaining_sessions']) ?>" 
                                                               disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit" name="sit_in_submit" class="btn btn-success">
                                                <i class="bi bi-play-circle me-1"></i> Start Sit-in Session
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
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

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Cannot Start Sit-in</h5>
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
                            <p>This student already has an active sit-in session. Please time-out the current session before starting a new one.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
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
        document.addEventListener('DOMContentLoaded', function() {
            // Error modal handling
            const errorDiv = document.getElementById('activeSitInError');
            if (errorDiv && errorDiv.textContent === 'active') {
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            }

            // Success modal handling
            const successDiv = document.getElementById('sitInSuccess');
            if (successDiv) {
                const sessions = successDiv.getAttribute('data-sessions');
                document.getElementById('successMessage').textContent = 
                    `Sit-in session started successfully! You have ${sessions} remaining sessions.`;
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                
                // Redirect after closing success modal
                document.getElementById('successModal').addEventListener('hidden.bs.modal', function () {
                    window.location.href = 'search.php';
                });
            }

            // Logout modal functionality
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
