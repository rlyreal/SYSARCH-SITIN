<?php
session_start();
include 'db.php';

// Redirect if not logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$admin_username = $_SESSION['username'] ?? 'Admin User';

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM feedback WHERE id = '$delete_id'");
    header("Location: feedback.php");
    exit();
}

// Fetch feedback records with join to get student details
$sql = "SELECT f.*, s.idno, s.laboratory, s.fullname 
        FROM feedback f 
        JOIN sit_in s ON f.sit_in_id = s.id 
        ORDER BY f.created_at DESC";
$result = $conn->query($sql);

// Get average rating
$avgQuery = "SELECT AVG(rating) as avg_rating FROM feedback";
$avgResult = $conn->query($avgQuery);
$avgRow = $avgResult->fetch_assoc();
$avgRating = number_format($avgRow['avg_rating'], 1);

// Get total feedbacks count
$countQuery = "SELECT COUNT(*) as total FROM feedback";
$countResult = $conn->query($countQuery);
$countRow = $countResult->fetch_assoc();
$totalFeedbacks = $countRow['total'];

// Get latest feedback date
$latestQuery = "SELECT created_at FROM feedback ORDER BY created_at DESC LIMIT 1";
$latestResult = $conn->query($latestQuery);
$latestRow = $latestResult->fetch_assoc();
$latestDate = $latestRow ? date('M d, Y', strtotime($latestRow['created_at'])) : 'No feedback yet';
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Reports | Admin</title>
    
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

    <!-- Custom CSS -->
    <style>
        @keyframes glow {
            0% { text-shadow: 0 0 5px #ffd700; }
            50% { text-shadow: 0 0 20px #ffd700, 0 0 30px #ffd700; }
            100% { text-shadow: 0 0 5px #ffd700; }
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-3px); }
            100% { transform: translateY(0px); }
        }
        
        .star-rating {
            color: #ffd700;
            animation: glow 2s ease-in-out infinite, float 3s ease-in-out infinite;
        }
        
        .feedback-text {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                    <li class="menu-item">
                        <a href="admin_reservation.php" class="menu-link">
                            <i class="menu-icon bi bi-calendar-check"></i>
                            <div data-i18n="Reservation">Reservation</div>
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
                    <li class="menu-item active">
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
                                <input type="text" id="navbarSearch" class="form-control border-0 shadow-none" placeholder="Search..." aria-label="Search...">
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
                            <span class="text-muted fw-light">Student Management /</span> Feedback Reports
                        </h4>

                        <!-- Summary Cards -->
                        <div class="row g-4 mb-4">
                            <!-- Average Rating Card -->
                            <div class="col-lg-4">
                                <div class="card h-100">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    <i class="bi bi-star-fill"></i>
                                                </span>
                                            </div>
                                            <h5 class="mb-0">Average Rating</h5>
                                        </div>
                                        <div class="text-center mt-3">
                                            <h1 class="display-3 mb-2 fw-bold"><?php echo $avgRating; ?></h1>
                                            <div class="star-rating text-2xl fs-3">
                                                <?php
                                                $fullStars = floor($avgRating);
                                                $decimal = $avgRating - $fullStars;
                                                
                                                for ($i = 0; $i < $fullStars; $i++) {
                                                    echo "<span class='ms-1'>★</span>";
                                                }
                                                if ($decimal >= 0.5) {
                                                    echo "<span class='ms-1'>★</span>";
                                                    $fullStars++;
                                                }
                                                for ($i = $fullStars; $i < 5; $i++) {
                                                    echo "<span class='ms-1 opacity-25'>☆</span>";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Feedbacks Card -->
                            <div class="col-lg-4">
                                <div class="card h-100">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-success">
                                                    <i class="bi bi-chat-square-text-fill"></i>
                                                </span>
                                            </div>
                                            <h5 class="mb-0">Total Feedbacks</h5>
                                        </div>
                                        <div class="text-center mt-3">
                                            <h1 class="display-3 mb-2 fw-bold"><?php echo $totalFeedbacks; ?></h1>
                                            <span class="text-muted">responses received</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Latest Feedback Card -->
                            <div class="col-lg-4">
                                <div class="card h-100">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-info">
                                                    <i class="bi bi-calendar-check-fill"></i>
                                                </span>
                                            </div>
                                            <h5 class="mb-0">Latest Feedback</h5>
                                        </div>
                                        <div class="text-center mt-3">
                                            <h2 class="mb-2 fw-bold"><?php echo $latestDate; ?></h2>
                                            <span class="text-muted">last feedback received</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Feedback Table Card -->
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Student Feedback</h5>
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" id="searchInput" class="form-control" placeholder="Search feedback...">
                                    </div>
                                    <button id="exportBtn" class="btn btn-primary">
                                        <i class="bi bi-download me-1"></i> Export
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID Number</th>
                                            <th>Name</th>
                                            <th>Laboratory</th>
                                            <th>Date</th>
                                            <th>Rating</th>
                                            <th>Feedback</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0" id="feedbackTable">
                                        <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $date = date('M d, Y', strtotime($row['created_at']));
                                                ?>
                                                <tr>
                                                    <td><strong><?php echo $row['idno']; ?></strong></td>
                                                    <td><?php echo $row['fullname']; ?></td>
                                                    <td><?php echo $row['laboratory']; ?></td>
                                                    <td><?php echo $date; ?></td>
                                                    <td>
                                                        <div class="star-rating">
                                                            <?php
                                                            for ($i = 0; $i < $row['rating']; $i++) {
                                                                echo "<span>★</span>";
                                                            }
                                                            for ($i = $row['rating']; $i < 5; $i++) {
                                                                echo "<span class='opacity-25'>☆</span>";
                                                            }
                                                            ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="feedback-text" title="<?php echo htmlspecialchars($row['feedback_text']); ?>" data-bs-toggle="tooltip" data-bs-placement="top">
                                                            <?php echo htmlspecialchars($row['feedback_text']); ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="dropdown">
                                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item" href="javascript:void(0);" 
                                                                   onclick="viewFeedback('<?php echo htmlspecialchars($row['fullname']); ?>', '<?php echo htmlspecialchars($row['laboratory']); ?>', '<?php echo $date; ?>', <?php echo $row['rating']; ?>, '<?php echo htmlspecialchars($row['feedback_text']); ?>')">
                                                                    <i class="bi bi-eye me-2"></i> View
                                                                </a>
                                                                <a class="dropdown-item text-danger" href="javascript:void(0);" 
                                                                   onclick="confirmDelete(<?php echo $row['id']; ?>)">
                                                                    <i class="bi bi-trash me-2"></i> Delete
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo '<tr><td colspan="7" class="text-center py-4">No feedback available</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted">Showing <?php echo $result->num_rows; ?> feedbacks</span>
                                    </div>
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination pagination-sm mb-0">
                                            <li class="page-item prev">
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
                        </div>
                        <!-- / Feedback Table Card -->

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

    <!-- Feedback View Modal -->
    <div class="modal fade" id="viewFeedbackModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Feedback Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3 text-center">
                            <div class="avatar avatar-md mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class="bi bi-chat-quote-fill"></i>
                                </span>
                            </div>
                            <h5 id="modalStudentName" class="mb-0">Student Name</h5>
                            <div class="mb-3 mt-2">
                                <div id="modalStarRating" class="star-rating d-flex justify-content-center">
                                    <!-- Stars will be added here -->
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-building me-2 text-primary"></i>
                                    <span class="fw-medium">Laboratory:</span>
                                </div>
                                <p id="modalLaboratory" class="mb-0 ps-4">Lab 517</p>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-calendar me-2 text-primary"></i>
                                    <span class="fw-medium">Date:</span>
                                </div>
                                <p id="modalDate" class="mb-0 ps-4">May 15, 2023</p>
                            </div>
                            <div class="mb-0">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-chat-left-text me-2 text-primary"></i>
                                    <span class="fw-medium">Feedback:</span>
                                </div>
                                <div class="border rounded p-3 bg-light">
                                    <p id="modalFeedbackText" class="mb-0">Feedback text will appear here.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3 text-center">
                            <div class="avatar avatar-md mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-danger">
                                    <i class="bi bi-trash"></i>
                                </span>
                            </div>
                            <p>Are you sure you want to delete this feedback? This action cannot be undone.</p>
                            <input type="hidden" id="deleteFeedbackId">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDelete" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Feedback Reports</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary d-flex align-items-center justify-content-between" onclick="exportToPDF()">
                            <span><i class="bi bi-file-earmark-pdf me-2"></i> Export as PDF</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                        <button type="button" class="btn btn-outline-success d-flex align-items-center justify-content-between" onclick="exportToExcel()">
                            <span><i class="bi bi-file-earmark-excel me-2"></i> Export as Excel</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary d-flex align-items-center justify-content-between" onclick="printTable()">
                            <span><i class="bi bi-printer me-2"></i> Print Report</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
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

    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

    <!-- Main JS -->
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            
            // Initialize Bootstrap modals
            const viewFeedbackModal = new bootstrap.Modal(document.getElementById('viewFeedbackModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const exportModal = new bootstrap.Modal(document.getElementById('exportModal'));
            const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
            
            // Search functionality
            document.getElementById("searchInput").addEventListener("keyup", function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll("#feedbackTable tr");
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? "" : "none";
                });
            });
            
            // Navbar search link to main search
            document.getElementById('navbarSearch').addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    document.getElementById('searchInput').value = this.value;
                    document.getElementById('searchInput').dispatchEvent(new Event('keyup'));
                    document.getElementById('searchInput').focus();
                }
            });
            
            // Export button handler
            document.getElementById('exportBtn').addEventListener('click', function() {
                exportModal.show();
            });
            
            // Delete confirmation
            window.confirmDelete = function(id) {
                document.getElementById('deleteFeedbackId').value = id;
                deleteModal.show();
            };
            
            // Handle confirm delete button
            document.getElementById('confirmDelete').addEventListener('click', function() {
                const id = document.getElementById('deleteFeedbackId').value;
                window.location.href = `feedback.php?delete_id=${id}`;
            });
            
            // View feedback details
            window.viewFeedback = function(name, laboratory, date, rating, feedbackText) {
                document.getElementById('modalStudentName').textContent = name;
                document.getElementById('modalLaboratory').textContent = laboratory;
                document.getElementById('modalDate').textContent = date;
                document.getElementById('modalFeedbackText').textContent = feedbackText;
                
                // Create star rating
                const starRatingContainer = document.getElementById('modalStarRating');
                starRatingContainer.innerHTML = '';
                for (let i = 0; i < 5; i++) {
                    const star = document.createElement('span');
                    star.classList.add('fs-3', 'mx-1');
                    if (i < rating) {
                        star.textContent = '★';
                        star.classList.add('star-rating');
                    } else {
                        star.textContent = '☆';
                        star.classList.add('opacity-25');
                    }
                    starRatingContainer.appendChild(star);
                }
                
                viewFeedbackModal.show();
            };
            
            // Logout button handler
            document.getElementById('logoutBtn').addEventListener('click', function() {
                logoutModal.show();
            });
            
            // Export functions
            window.exportToPDF = function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                
                // Add title and header info
                doc.setFontSize(18);
                doc.setTextColor(44, 52, 60);
                doc.text('Feedback Reports', doc.internal.pageSize.width / 2, 15, { align: 'center' });
                
                doc.setFontSize(12);
                doc.text('Average Rating: ' + <?php echo $avgRating; ?> + ' / 5', doc.internal.pageSize.width / 2, 25, { align: 'center' });
                doc.text('Total Feedbacks: ' + <?php echo $totalFeedbacks; ?>, doc.internal.pageSize.width / 2, 32, { align: 'center' });
                doc.text('Generated on: ' + new Date().toLocaleDateString(), doc.internal.pageSize.width / 2, 39, { align: 'center' });
                
                // Create table
                const tableColumn = ["ID Number", "Name", "Laboratory", "Date", "Rating", "Feedback"];
                const tableRows = [];
                
                const rows = document.querySelectorAll('#feedbackTable tr');
                rows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const rowData = [];
                        
                        // Extract data from each cell (except the last action column)
                        for (let i = 0; i < row.cells.length - 1; i++) {
                            const cell = row.cells[i];
                            if (i === 4) { // Rating column
                                // Count stars for rating
                                const stars = cell.querySelectorAll('.star-rating span');
                                rowData.push(stars.length + ' / 5');
                            } else {
                                rowData.push(cell.textContent.trim());
                            }
                        }
                        
                        tableRows.push(rowData);
                    }
                });
                
                doc.autoTable({
                    head: [tableColumn],
                    body: tableRows,
                    startY: 45,
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2
                    },
                    headStyles: {
                        fillColor: [105, 108, 255],
                        textColor: [255, 255, 255],
                        fontSize: 9,
                        fontStyle: 'bold'
                    },
                    columnStyles: {
                        5: { cellWidth: 'auto' } // Make feedback column wider
                    }
                });
                
                doc.save('Feedback_Report_' + new Date().toISOString().slice(0, 10) + '.pdf');
                exportModal.hide();
            };
            
            window.exportToExcel = function() {
                const wb = XLSX.utils.book_new();
                
                // Create header data
                const headerData = [
                    ['Feedback Reports'],
                    ['Average Rating: ' + <?php echo $avgRating; ?> + ' / 5'],
                    ['Total Feedbacks: ' + <?php echo $totalFeedbacks; ?>],
                    ['Generated on: ' + new Date().toLocaleDateString()],
                    [''],
                    ['ID Number', 'Name', 'Laboratory', 'Date', 'Rating', 'Feedback']
                ];
                
                // Get table data
                const tableRows = [];
                
                const rows = document.querySelectorAll('#feedbackTable tr');
                rows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const rowData = [];
                        
                        // Extract data from each cell (except the last action column)
                        for (let i = 0; i < row.cells.length - 1; i++) {
                            const cell = row.cells[i];
                            if (i === 4) { // Rating column
                                // Count stars for rating
                                const stars = cell.querySelectorAll('.star-rating span');
                                rowData.push(stars.length + ' / 5');
                            } else {
                                rowData.push(cell.textContent.trim());
                            }
                        }
                        
                        tableRows.push(rowData);
                    }
                });
                
                // Combine headers and data
                const wsData = [...headerData, ...tableRows];
                
                // Create worksheet
                const ws = XLSX.utils.aoa_to_sheet(wsData);
                
                // Set column widths
                ws['!cols'] = [
                    { wch: 15 }, // ID Number
                    { wch: 20 }, // Name
                    { wch: 15 }, // Laboratory
                    { wch: 15 }, // Date
                    { wch: 10 }, // Rating
                    { wch: 50 }  // Feedback
                ];
                
                // Add worksheet to workbook
                XLSX.utils.book_append_sheet(wb, ws, "Feedback_Reports");
                
                // Create filename and save
                const fileName = 'Feedback_Report_' + new Date().toISOString().slice(0, 10) + '.xlsx';
                XLSX.writeFile(wb, fileName);
                exportModal.hide();
            };
            
            window.printTable = function() {
                const printContent = document.createElement('div');
                
                // Add header
                const header = document.createElement('div');
                header.innerHTML = `
                    <h2 style="text-align: center; margin-bottom: 5px;">Feedback Reports</h2>
                    <p style="text-align: center; margin-bottom: 3px;">Average Rating: ${<?php echo $avgRating; ?>} / 5</p>
                    <p style="text-align: center; margin-bottom: 3px;">Total Feedbacks: ${<?php echo $totalFeedbacks; ?>}</p>
                    <p style="text-align: center; margin-bottom: 20px;">Generated on: ${new Date().toLocaleDateString()}</p>
                `;
                printContent.appendChild(header);
                
                // Create table
                const table = document.createElement('table');
                table.style.width = '100%';
                table.style.borderCollapse = 'collapse';
                
                // Add table header
                const thead = document.createElement('thead');
                thead.innerHTML = `
                    <tr>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #696cff; color: white;">ID Number</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #696cff; color: white;">Name</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #696cff; color: white;">Laboratory</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #696cff; color: white;">Date</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #696cff; color: white;">Rating</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #696cff; color: white;">Feedback</th>
                    </tr>
                `;
                table.appendChild(thead);
                
                // Add table body
                const tbody = document.createElement('tbody');
                
                const rows = document.querySelectorAll('#feedbackTable tr');
                rows.forEach((row, index) => {
                    if (row.style.display !== 'none') {
                        const newRow = document.createElement('tr');
                        newRow.style.backgroundColor = index % 2 === 0 ? '#f9f9f9' : 'white';
                        
                        // Extract data from each cell (except the last action column)
                        for (let i = 0; i < row.cells.length - 1; i++) {
                            const cell = row.cells[i];
                            const newCell = document.createElement('td');
                            newCell.style.border = '1px solid #ddd';
                            newCell.style.padding = '8px';
                            
                            if (i === 4) { // Rating column
                                // Create star rating for print
                                const rating = cell.querySelectorAll('.star-rating span').length;
                                newCell.style.color = '#ffd700';
                                newCell.innerHTML = '★'.repeat(rating) + '☆'.repeat(5 - rating);
                            } else {
                                newCell.textContent = cell.textContent.trim();
                            }
                            
                            newRow.appendChild(newCell);
                        }
                        
                        tbody.appendChild(newRow);
                    }
                });
                
                table.appendChild(tbody);
                printContent.appendChild(table);
                
                // Open print window
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Feedback Reports</title>
                            <style>
                                body { font-family: Arial, sans-serif; margin: 20px; }
                                @media print {
                                    @page { margin: 0.5cm; }
                                    body { margin: 1cm; }
                                }
                            </style>
                        </head>
                        <body>
                            ${printContent.innerHTML}
                        </body>
                    </html>
                `);
                
                printWindow.document.close();
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 500);
                
                exportModal.hide();
            };
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>