<?php
session_start();
include 'db.php';

// Redirect if not logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$admin_username = $_SESSION['username'] ?? 'Admin User';

// Reset all session counts if requested
if (isset($_GET['reset_session'])) {
    // Update all session counts in sit_in table to 30
    $reset_sql = "UPDATE sit_in SET session_count = 30";
    if ($conn->query($reset_sql) === TRUE) {
        // Add success message to session
        $_SESSION['message'] = "All session counts have been reset to 30 successfully.";
    } else {
        $_SESSION['message'] = "Error resetting session counts: " . $conn->error;
    }
    header("Location: Students.php");
    exit();
}

// Update SQL query to handle session count with default value of 30 for new users
$sql = "SELECT u.id, u.id_no, CONCAT(u.first_name, ' ', u.middle_name, ' ', u.last_name) AS full_name,
        u.year_level, u.course, 
        COALESCE(s.session_count, 30) as session_count 
        FROM users u 
        LEFT JOIN sit_in s ON u.id_no = s.idno 
            AND s.id = (SELECT MAX(id) FROM sit_in WHERE idno = u.id_no)
        ORDER BY u.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information | Admin</title>
    
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
                    <li class="menu-item active">
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
                    <li class="menu-item">
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
                            <span class="text-muted fw-light">Student Management /</span> Student Information
                        </h4>

                        <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert <?php echo strpos($_SESSION['message'], 'Error') !== false ? 'alert-danger' : 'alert-success'; ?> alert-dismissible fade show">
                            <?php 
                            echo $_SESSION['message'];
                            unset($_SESSION['message']); // Clear the message after displaying
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php endif; ?>

                        <!-- Header with title and reset button -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h5 class="card-title mb-0">Student Information</h5>
                                        <p class="card-subtitle text-muted">Manage all student records</p>
                                    </div>
                                    <button id="resetButton" class="btn btn-primary d-flex align-items-center gap-1">
                                        <i class="bi bi-arrow-repeat"></i>
                                        Reset All Sessions
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Search, filter, and add new student -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                                            <input type="text" id="searchInput" class="form-control" placeholder="Search students...">
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <button type="button" id="addNewStudentBtn" class="btn btn-success d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-plus-lg"></i>
                                            Add New Student
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Student Table Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID Number</th>
                                                <th>Name</th>
                                                <th>Year Level</th>
                                                <th>Course</th>
                                                <th>Sessions Left</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                        <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                            ?>
                                            <tr>
                                                <td><i class="bi bi-person-badge text-primary me-2"></i><?php echo $row['id_no']; ?></td>
                                                <td><?php echo $row['full_name']; ?></td>
                                                <td><?php echo $row['year_level']; ?></td>
                                                <td><?php echo $row['course']; ?></td>
                                                <td>
                                                    <span class="badge bg-label-primary rounded-pill"><?php echo $row['session_count']; ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex gap-2">
                                                        <button onclick="editStudent(<?php echo htmlspecialchars(json_encode([
                                                            'id' => $row['id'],
                                                            'id_no' => $row['id_no'],
                                                            'full_name' => $row['full_name'],
                                                            'year_level' => $row['year_level'],
                                                            'course' => $row['course']
                                                        ])); ?>)" class="btn btn-icon btn-sm btn-outline-primary" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button onclick="showDeleteConfirmation(<?php echo $row['id']; ?>)" 
                                                                class="btn btn-icon btn-sm btn-outline-danger" 
                                                                title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                        <button onclick="resetStudentSession('<?php echo $row['id_no']; ?>')" 
                                                                class="btn btn-icon btn-sm btn-outline-warning" 
                                                                title="Reset Session">
                                                            <i class="bi bi-arrow-repeat"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">No student records found</td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="card-body d-flex justify-content-between align-items-center mt-3 px-0">
                            <p class="text-muted mb-0">Showing <strong><?php echo min($result->num_rows, 10); ?></strong> of <strong><?php echo $result->num_rows; ?></strong> entries</p>
                            <nav aria-label="Page navigation">
                                <ul class="pagination mb-0">
                                    <li class="page-item prev disabled">
                                        <a class="page-link" href="javascript:void(0);"><i class="bi bi-chevron-left"></i></a>
                                    </li>
                                    <li class="page-item active">
                                        <a class="page-link" href="javascript:void(0);">1</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="javascript:void(0);">2</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="javascript:void(0);">3</a>
                                    </li>
                                    <li class="page-item next">
                                        <a class="page-link" href="javascript:void(0);"><i class="bi bi-chevron-right"></i></a>
                                    </li>
                                </ul>
                            </nav>
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

    <!-- Reset Sessions Modal -->
    <div class="modal fade" id="resetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset All Sessions</h5>
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
                            <p>Are you sure you want to reset ALL session counts to 30? This action cannot be undone.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmReset" class="btn btn-warning">Reset All Sessions</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addStudentForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">ID Number*</label>
                                <input type="text" name="id_no" class="form-control" required placeholder="Enter ID number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email*</label>
                                <input type="email" name="email" class="form-control" required placeholder="Enter email">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name*</label>
                                <input type="text" name="last_name" class="form-control" required placeholder="Enter last name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">First Name*</label>
                                <input type="text" name="first_name" class="form-control" required placeholder="Enter first name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control" placeholder="Enter middle name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Course*</label>
                                <select name="course" class="form-select" required>
                                    <option value="" disabled selected>Course</option>
                                    <option value="BSIT">BSIT (Information Technology)</option>
                                    <option value="BSCS">BSCS (Computer Science)</option>
                                    <option value="BSIS">BSIS (Information Systems)</option>
                                    <option value="BSCE">BSCE (Civil Engineering)</option>
                                    <option value="BSEE">BSEE (Electrical Engineering)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Username*</label>
                                <input type="text" name="username" class="form-control" required placeholder="Enter username">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Year Level*</label>
                                <select name="year_level" class="form-select" required>
                                    <option value="">Select Year</option>
                                    <option value="1st Year">1st Year</option>
                                    <option value="2nd Year">2nd Year</option>
                                    <option value="3rd Year">3rd Year</option>
                                    <option value="4th Year">4th Year</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Password*</label>
                                <input type="password" name="password" class="form-control" required placeholder="Enter password">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" placeholder="Enter complete address">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="submitAddStudent" class="btn btn-primary">Add Student</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editStudentForm">
                        <input type="hidden" id="edit_student_id" name="student_id">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">ID Number*</label>
                                <input type="text" id="edit_id_no" name="id_no" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Last Name*</label>
                                <input type="text" id="edit_last_name" name="last_name" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">First Name*</label>
                                <input type="text" id="edit_first_name" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Middle Name</label>
                                <input type="text" id="edit_middle_name" name="middle_name" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Year Level*</label>
                                <select id="edit_year_level" name="year_level" class="form-select" required>
                                    <option value="1st Year">1st Year</option>
                                    <option value="2nd Year">2nd Year</option>
                                    <option value="3rd Year">3rd Year</option>
                                    <option value="4th Year">4th Year</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Course*</label>
                                <select id="edit_course" name="course" class="form-select" required>
                                    <option value="" disabled selected>Course</option>
                                    <option value="BSIT">BSIT (Information Technology)</option>
                                    <option value="BSCS">BSCS (Computer Science)</option>
                                    <option value="BSIS">BSIS (Information Systems)</option>
                                    <option value="BSCE">BSCE (Civil Engineering)</option>
                                    <option value="BSEE">BSEE (Electrical Engineering)</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="submitEditStudent" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Student</h5>
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
                            <p>Are you sure you want to delete this student? This action cannot be undone.</p>
                            <input type="hidden" id="deleteStudentId">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDelete" class="btn btn-danger">Delete Student</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Single Session Modal -->
    <div class="modal fade" id="resetSingleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset Student Sessions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3 text-center">
                            <div class="avatar avatar-md mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-warning">
                                    <i class="bi bi-arrow-repeat"></i>
                                </span>
                            </div>
                            <p>Are you sure you want to reset this student's session count to 30?</p>
                            <input type="hidden" id="resetStudentIdNo">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmSingleReset" class="btn btn-warning">Reset Sessions</button>
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
                            <div class="avatar avatar-md mx-auto mb-3">
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
            // Initialize Bootstrap modals
            const resetModal = new bootstrap.Modal(document.getElementById('resetModal'));
            const addStudentModal = new bootstrap.Modal(document.getElementById('addStudentModal'));
            const editStudentModal = new bootstrap.Modal(document.getElementById('editStudentModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const resetSingleModal = new bootstrap.Modal(document.getElementById('resetSingleModal'));
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));

            // Reset All Sessions button
            document.getElementById('resetButton').addEventListener('click', function() {
                resetModal.show();
            });

            // Confirm reset button
            document.getElementById('confirmReset').addEventListener('click', function() {
                window.location.href = 'Students.php?reset_session=true';
            });

            // Add new student button
            document.getElementById('addNewStudentBtn').addEventListener('click', function() {
                addStudentModal.show();
            });

            // Submit add student form
            document.getElementById('submitAddStudent').addEventListener('click', function() {
                const form = document.getElementById('addStudentForm');
                const formData = new FormData(form);
                
                fetch('add_student.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    addStudentModal.hide();
                    
                    if (data.success) {
                        document.getElementById('successModalTitle').textContent = 'Student Added!';
                        document.getElementById('successModalMessage').textContent = 'The new student has been successfully added to the system.';
                        successModal.show();
                        
                        // Reload page after closing success modal
                        document.getElementById('successModalContinue').addEventListener('click', function() {
                            window.location.reload();
                        }, { once: true });
                    } else {
                        alert(data.message || 'Error adding student');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error adding student');
                });
            });

            // Handle edit student
            window.editStudent = function(studentData) {
                document.getElementById('edit_student_id').value = studentData.id;
                document.getElementById('edit_id_no').value = studentData.id_no;
                
                // Split full name and handle name parts
                const fullName = studentData.full_name;
                const nameParts = fullName.trim().split(' ');
                
                // Handle name parts based on length
                if (nameParts.length === 3) {
                    document.getElementById('edit_first_name').value = nameParts[0];
                    document.getElementById('edit_middle_name').value = nameParts[1];
                    document.getElementById('edit_last_name').value = nameParts[2];
                } else if (nameParts.length === 2) {
                    document.getElementById('edit_first_name').value = nameParts[0];
                    document.getElementById('edit_middle_name').value = '';
                    document.getElementById('edit_last_name').value = nameParts[1];
                } else {
                    document.getElementById('edit_first_name').value = nameParts[0] || '';
                    document.getElementById('edit_middle_name').value = '';
                    document.getElementById('edit_last_name').value = nameParts[1] || '';
                }
                
                document.getElementById('edit_year_level').value = studentData.year_level;
                document.getElementById('edit_course').value = studentData.course;
                
                editStudentModal.show();
            };

            // Submit edit student form
            document.getElementById('submitEditStudent').addEventListener('click', function() {
                const form = document.getElementById('editStudentForm');
                const formData = new FormData(form);
                
                fetch('update_student.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    editStudentModal.hide();
                    
                    if (data.success) {
                        document.getElementById('successModalTitle').textContent = 'Student Updated!';
                        document.getElementById('successModalMessage').textContent = 'The student information has been successfully updated.';
                        successModal.show();
                        
                        // Reload page after closing success modal
                        document.getElementById('successModalContinue').addEventListener('click', function() {
                            window.location.reload();
                        }, { once: true });
                    } else {
                        alert(data.message || 'Error updating student');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating student');
                });
            });

            // Show delete confirmation
            window.showDeleteConfirmation = function(id) {
                document.getElementById('deleteStudentId').value = id;
                deleteModal.show();
            };

            // Confirm delete button
            document.getElementById('confirmDelete').addEventListener('click', function() {
                const id = document.getElementById('deleteStudentId').value;
                
                fetch('delete_student.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    deleteModal.hide();
                    
                    if (data.success) {
                        document.getElementById('successModalTitle').textContent = 'Student Deleted!';
                        document.getElementById('successModalMessage').textContent = 'The student has been successfully deleted from the system.';
                        successModal.show();
                        
                        // Reload page after closing success modal
                        document.getElementById('successModalContinue').addEventListener('click', function() {
                            window.location.reload();
                        }, { once: true });
                    } else {
                        alert(data.message || 'Error deleting student');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting student');
                });
            });

            // Reset single student session
            window.resetStudentSession = function(idNo) {
                document.getElementById('resetStudentIdNo').value = idNo;
                resetSingleModal.show();
            };

            // Confirm single reset button
            document.getElementById('confirmSingleReset').addEventListener('click', function() {
                const idNo = document.getElementById('resetStudentIdNo').value;
                
                fetch('reset_single_session.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id_no=${idNo}`
                })
                .then(response => response.json())
                .then(data => {
                    resetSingleModal.hide();
                    
                    if (data.success) {
                        document.getElementById('successModalTitle').textContent = 'Session Reset!';
                        document.getElementById('successModalMessage').textContent = 'The student\'s session count has been reset to 30 successfully.';
                        successModal.show();
                        
                        // Reload page after closing success modal
                        document.getElementById('successModalContinue').addEventListener('click', function() {
                            window.location.reload();
                        }, { once: true });
                    } else {
                        alert(data.message || 'Error resetting session count');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error resetting session count');
                });
            });

            // Search functionality
            document.getElementById('searchInput').addEventListener('keyup', function() {
                let searchText = this.value.toLowerCase();
                let rows = document.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    let text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchText) ? '' : 'none';
                });
            });

            // Navbar search redirection
            document.getElementById('navbarSearch').addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    document.getElementById('searchInput').value = this.value;
                    document.getElementById('searchInput').dispatchEvent(new Event('keyup'));
                    document.getElementById('searchInput').focus();
                }
            });

            // Logout button
            document.getElementById('logoutBtn').addEventListener('click', function() {
                logoutModal.show();
            });
        });
    </script>
</body>
</html>