<?php
session_start();
include 'db.php';

// Redirect if not logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$admin_username = $_SESSION['username'] ?? 'Admin User';

// Get unique purposes for the filter dropdown
$purposesQuery = "SELECT DISTINCT purpose FROM sit_in WHERE time_out IS NOT NULL ORDER BY purpose ASC";
$purposesResult = $conn->query($purposesQuery);
$purposes = [];
if ($purposesResult->num_rows > 0) {
    while($purposeRow = $purposesResult->fetch_assoc()) {
        $purposes[] = $purposeRow['purpose'];
    }
}

// Get labs for filter dropdown
$labsQuery = "SELECT DISTINCT laboratory FROM sit_in WHERE time_out IS NOT NULL ORDER BY laboratory ASC";
$labsResult = $conn->query($labsQuery);
$labs = [];
if ($labsResult->num_rows > 0) {
    while($labRow = $labsResult->fetch_assoc()) {
        $labs[] = $labRow['laboratory'];
    }
}

// Update the SQL query to fetch only completed sit-ins
$sql = "SELECT s.created_at, s.idno, s.fullname, s.purpose, s.laboratory, s.time_in, s.time_out, 
        TIMESTAMPDIFF(MINUTE, s.time_in, s.time_out) as duration
        FROM sit_in s 
        WHERE s.time_out IS NOT NULL 
        ORDER BY s.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | Admin</title>
    
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
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->
    <style>
        .type-highlight {
            color: #22c55e;
            font-weight: 600;
        }
    </style>

    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
    
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
                    <li class="menu-item active">
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
                            <span class="text-muted fw-light">Student Management /</span> Reports
                        </h4>

                        <!-- Filter Card -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- Date Range -->
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">From Date</label>
                                        <input type="date" id="fromDateFilter" class="form-control">
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">To Date</label>
                                        <input type="date" id="toDateFilter" class="form-control">
                                    </div>
                                    
                                    <!-- Purpose Filter -->
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">Purpose</label>
                                        <select id="purposeFilter" class="form-select">
                                            <option value="">All Purposes</option>
                                            <?php foreach($purposes as $purpose): ?>
                                            <option value="<?php echo htmlspecialchars($purpose); ?>"><?php echo htmlspecialchars($purpose); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <!-- Laboratory Filter -->
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">Laboratory</label>
                                        <select id="labFilter" class="form-select">
                                            <option value="">All Laboratories</option>
                                            <?php foreach($labs as $lab): ?>
                                            <option value="<?php echo htmlspecialchars($lab); ?>"><?php echo htmlspecialchars($lab); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <!-- Search -->
                                    <div class="col-md-9">
                                        <label class="form-label">Search</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                                            <input type="text" id="searchInput" class="form-control" placeholder="Search by name, ID number...">
                                        </div>
                                    </div>
                                    
                                    <!-- Reset button -->
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button id="resetFilters" class="btn btn-secondary w-100">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Export Options and Summary Card -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="card-title mb-md-0 mb-3">Export Options</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex gap-2 justify-content-md-end">
                                            <button onclick="exportToExcel()" class="btn btn-primary">
                                                <i class="bi bi-file-earmark-excel me-1"></i> Excel
                                            </button>
                                            <button onclick="exportToPDF()" class="btn btn-danger">
                                                <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                                            </button>
                                            <button onclick="exportToCSV()" class="btn btn-success">
                                                <i class="bi bi-file-earmark-text me-1"></i> CSV
                                            </button>
                                            <button onclick="printTable()" class="btn btn-dark">
                                                <i class="bi bi-printer me-1"></i> Print
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reports Table Card -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Sit-In Records</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                            <a class="dropdown-item" href="javascript:void(0);" id="showAllRecords">Show All Records</a>
                                            <a class="dropdown-item" href="javascript:void(0);" id="showTodayRecords">Show Today's Records</a>
                                            <a class="dropdown-item" href="javascript:void(0);" id="showThisWeekRecords">Show This Week's Records</a>
                                            <a class="dropdown-item" href="javascript:void(0);" id="showThisMonthRecords">Show This Month's Records</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-striped table-hover" id="reportsTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>ID Number</th>
                                            <th>Name</th>
                                            <th>Purpose</th>
                                            <th>Laboratory</th>
                                            <th>Time In</th>
                                            <th>Time Out</th>
                                            <th>Duration</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $date = date('M d, Y', strtotime($row['created_at']));
                                                $timeIn = date('h:i A', strtotime($row['time_in']));
                                                $timeOut = $row['time_out'] ? date('h:i A', strtotime($row['time_out'])) : 'Ongoing';
                                                
                                                // Calculate duration in hours and minutes
                                                $duration = $row['duration'];
                                                $hours = floor($duration / 60);
                                                $minutes = $duration % 60;
                                                $durationFormatted = ($hours > 0 ? $hours . 'hr ' : '') . $minutes . 'min';
                                        ?>
                                        <tr>
                                            <td><strong><?php echo $date; ?></strong></td>
                                            <td><?php echo $row['idno']; ?></td>
                                            <td><?php echo $row['fullname']; ?></td>
                                            <td><?php echo $row['purpose']; ?></td>
                                            <td><?php echo $row['laboratory']; ?></td>
                                            <td><?php echo $timeIn; ?></td>
                                            <td><?php echo $timeOut; ?></td>
                                            <td><?php echo $durationFormatted; ?></td>
                                            <td><span class="badge bg-label-success type-highlight">SIT-IN</span></td>
                                        </tr>
                                        <?php
                                            }
                                        } else {
                                        ?>
                                        <tr>
                                            <td colspan="9" class="text-center">No records found</td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span id="totalRecords" class="text-muted">Showing <?php echo $result->num_rows; ?> records</span>
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
                        <!-- / Reports Table Card -->
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
            // Initialize Bootstrap tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            
            // Initialize Bootstrap modals
            const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
            
            // Event listeners for filters
            const searchInput = document.getElementById('searchInput');
            const purposeFilter = document.getElementById('purposeFilter');
            const labFilter = document.getElementById('labFilter');
            const fromDateFilter = document.getElementById('fromDateFilter');
            const toDateFilter = document.getElementById('toDateFilter');
            const resetFiltersBtn = document.getElementById('resetFilters');
            
            // Filter function
            function applyFilters() {
                const searchValue = searchInput.value.toLowerCase();
                const purposeValue = purposeFilter.value;
                const labValue = labFilter.value;
                const fromDate = fromDateFilter.value ? new Date(fromDateFilter.value) : null;
                const toDate = toDateFilter.value ? new Date(toDateFilter.value) : null;
                
                let visibleCount = 0;
                
                // Get all rows except header
                const rows = document.querySelectorAll('#reportsTable tbody tr');
                
                rows.forEach(row => {
                    if (row.querySelector('td[colspan]')) {
                        // This is a "no records" row, skip filtering
                        return;
                    }
                    
                    const cells = row.getElementsByTagName('td');
                    
                    // Get data from cells
                    const date = cells[0].textContent.trim();
                    const idNumber = cells[1].textContent.toLowerCase();
                    const name = cells[2].textContent.toLowerCase();
                    const purpose = cells[3].textContent.trim();
                    const lab = cells[4].textContent.trim();
                    
                    // Date filter
                    let dateMatch = true;
                    if (fromDate || toDate) {
                        const rowDate = new Date(date);
                        if (fromDate && rowDate < fromDate) dateMatch = false;
                        if (toDate) {
                            // Set toDate to end of the day
                            const endOfDay = new Date(toDate);
                            endOfDay.setHours(23, 59, 59, 999);
                            if (rowDate > endOfDay) dateMatch = false;
                        }
                    }
                    
                    // Search text match
                    const textMatch = !searchValue || 
                                    idNumber.includes(searchValue) || 
                                    name.includes(searchValue) || 
                                    date.toLowerCase().includes(searchValue) ||
                                    purpose.toLowerCase().includes(searchValue);
                    
                    // Purpose match
                    const purposeMatch = !purposeValue || purpose === purposeValue;
                    
                    // Lab match
                    const labMatch = !labValue || lab === labValue;
                    
                    // Determine visibility
                    const isVisible = dateMatch && textMatch && purposeMatch && labMatch;
                    
                    // Set display
                    row.style.display = isVisible ? '' : 'none';
                    
                    // Count visible rows
                    if (isVisible) visibleCount++;
                });
                
                // Update record counter
                document.getElementById('totalRecords').textContent = `Showing ${visibleCount} records`;
                
                // Handle no results
                if (visibleCount === 0 && rows.length > 0) {
                    // Check if "no results" row already exists
                    if (!document.querySelector('#reportsTable tbody tr td[colspan="9"]')) {
                        const tbody = document.querySelector('#reportsTable tbody');
                        const noResultsRow = document.createElement('tr');
                        noResultsRow.innerHTML = `<td colspan="9" class="text-center">No records found matching your filters</td>`;
                        tbody.appendChild(noResultsRow);
                    }
                } else {
                    // Remove any existing "no results" messages if we have results
                    const noResultsRow = document.querySelector('#reportsTable tbody tr td[colspan="9"]');
                    if (noResultsRow && visibleCount > 0) {
                        noResultsRow.parentElement.remove();
                    }
                }
            }
            
            // Event listeners for all filter inputs
            searchInput.addEventListener('input', applyFilters);
            purposeFilter.addEventListener('change', applyFilters);
            labFilter.addEventListener('change', applyFilters);
            fromDateFilter.addEventListener('change', applyFilters);
            toDateFilter.addEventListener('change', applyFilters);
            
            // Reset filters button
            resetFiltersBtn.addEventListener('click', function() {
                searchInput.value = '';
                purposeFilter.value = '';
                labFilter.value = '';
                fromDateFilter.value = '';
                toDateFilter.value = '';
                applyFilters();
            });
            
            // Navbar search link to main search
            document.getElementById('navbarSearch').addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    searchInput.value = this.value;
                    applyFilters();
                    searchInput.focus();
                }
            });
            
            // Predefined filter options
            document.getElementById('showAllRecords').addEventListener('click', function() {
                resetFiltersBtn.click();
            });
            
            document.getElementById('showTodayRecords').addEventListener('click', function() {
                const today = new Date().toISOString().split('T')[0];
                fromDateFilter.value = today;
                toDateFilter.value = today;
                applyFilters();
            });
            
            document.getElementById('showThisWeekRecords').addEventListener('click', function() {
                const today = new Date();
                const dayOfWeek = today.getDay(); // 0 = Sunday, 1 = Monday, etc.
                
                // Calculate start of week (Sunday)
                const startOfWeek = new Date(today);
                startOfWeek.setDate(today.getDate() - dayOfWeek);
                
                // Calculate end of week (Saturday)
                const endOfWeek = new Date(today);
                endOfWeek.setDate(today.getDate() + (6 - dayOfWeek));
                
                fromDateFilter.value = startOfWeek.toISOString().split('T')[0];
                toDateFilter.value = endOfWeek.toISOString().split('T')[0];
                applyFilters();
            });
            
            document.getElementById('showThisMonthRecords').addEventListener('click', function() {
                const today = new Date();
                const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                
                fromDateFilter.value = startOfMonth.toISOString().split('T')[0];
                toDateFilter.value = endOfMonth.toISOString().split('T')[0];
                applyFilters();
            });
            
            // Logout button handler
            document.getElementById('logoutBtn').addEventListener('click', function() {
                logoutModal.show();
            });
            
            // Export functions
            window.exportToExcel = function() {
                // Initialize workbook
                const wb = XLSX.utils.book_new();
                
                // Get filter values for the header
                const purposeValue = purposeFilter.value || 'All Purposes';
                const labValue = labFilter.value || 'All Laboratories';
                const fromDate = fromDateFilter.value || 'Any Date';
                const toDate = toDateFilter.value || 'Any Date';
                
                // Create header data
                const headerData = [
                    ['UNIVERSITY OF CEBU'],
                    ['COLLEGE OF COMPUTER STUDIES'],
                    ['Sit-In Monitoring System Report'],
                    [`Generated on: ${new Date().toLocaleString()}`],
                    [`Purpose: ${purposeValue} | Laboratory: ${labValue} | Date Range: ${fromDate} to ${toDate}`],
                    [''],
                    ['Date', 'ID Number', 'Name', 'Purpose', 'Laboratory', 'Time In', 'Time Out', 'Duration', 'Type']
                ];
                
                // Get visible table data
                const tableRows = [];
                const rows = document.querySelectorAll('#reportsTable tbody tr');
                
                rows.forEach(row => {
                    // Skip "no results" or hidden rows
                    if (row.style.display === 'none' || row.querySelector('td[colspan]')) {
                        return;
                    }
                    
                    const cells = row.querySelectorAll('td');
                    const rowData = Array.from(cells).map(cell => {
                        // For 'Type' column, get just the text without styling
                        if (cell.querySelector('.type-highlight')) {
                            return cell.querySelector('.type-highlight').textContent.trim();
                        }
                        return cell.textContent.trim();
                    });
                    
                    tableRows.push(rowData);
                });
                
                // Combine headers and data
                const wsData = [...headerData, ...tableRows];
                
                // Create worksheet
                const ws = XLSX.utils.aoa_to_sheet(wsData);
                
                // Set column widths
                ws['!cols'] = [
                    { wch: 15 }, // Date
                    { wch: 15 }, // ID Number
                    { wch: 25 }, // Name
                    { wch: 20 }, // Purpose
                    { wch: 15 }, // Laboratory
                    { wch: 12 }, // Time In
                    { wch: 12 }, // Time Out
                    { wch: 12 }, // Duration
                    { wch: 10 }  // Type
                ];
                
                // Add worksheet to workbook
                XLSX.utils.book_append_sheet(wb, ws, "Sit-In Records");
                
                // Create filename with date
                const now = new Date();
                const dateStr = now.toISOString().split('T')[0];
                const fileName = `UC_SitIn_Records_${dateStr}.xlsx`;
                
                // Write file and trigger download
                XLSX.writeFile(wb, fileName);
            };
            
            window.exportToPDF = function() {
                // Use jsPDF with autotable plugin
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('l', 'mm', 'a4'); // landscape
                
                // Get filter values for header
                const purposeValue = purposeFilter.value || 'All Purposes';
                const labValue = labFilter.value || 'All Laboratories';
                const fromDate = fromDateFilter.value || 'Any Date';
                const toDate = toDateFilter.value || 'Any Date';
                
                // Add title and subtitle
                doc.setFontSize(18);
                doc.setTextColor(44, 52, 60);
                doc.text('UNIVERSITY OF CEBU', doc.internal.pageSize.width / 2, 15, { align: 'center' });
                
                doc.setFontSize(14);
                doc.text('COLLEGE OF COMPUTER STUDIES', doc.internal.pageSize.width / 2, 22, { align: 'center' });
                
                doc.setFontSize(12);
                doc.text('Sit-In Monitoring System Report', doc.internal.pageSize.width / 2, 29, { align: 'center' });
                
                doc.setFontSize(10);
                const dateTimeStr = `Generated on: ${new Date().toLocaleString()}`;
                doc.text(dateTimeStr, doc.internal.pageSize.width / 2, 35, { align: 'center' });
                
                doc.setFontSize(9);
                const filterStr = `Purpose: ${purposeValue} | Laboratory: ${labValue} | Date Range: ${fromDate} to ${toDate}`;
                doc.text(filterStr, doc.internal.pageSize.width / 2, 40, { align: 'center' });
                
                // Extract table rows data
                const tableRows = [];
                const rows = document.querySelectorAll('#reportsTable tbody tr');
                
                rows.forEach(row => {
                    // Skip "no results" or hidden rows
                    if (row.style.display === 'none' || row.querySelector('td[colspan]')) {
                        return;
                    }
                    
                    const cells = row.querySelectorAll('td');
                    const rowData = Array.from(cells).map(cell => {
                        // For 'Type' column, get just the text
                        if (cell.querySelector('.type-highlight')) {
                            return cell.querySelector('.type-highlight').textContent.trim();
                        }
                        return cell.textContent.trim();
                    });
                    
                    tableRows.push(rowData);
                });
                
                // Create the table using autoTable plugin
                doc.autoTable({
                    startY: 45,
                    head: [['Date', 'ID Number', 'Name', 'Purpose', 'Laboratory', 'Time In', 'Time Out', 'Duration', 'Type']],
                    body: tableRows,
                    theme: 'grid',
                    headStyles: {
                        fillColor: [44, 52, 60],
                        textColor: [255, 255, 255],
                        fontSize: 8,
                        fontStyle: 'bold'
                    },
                    bodyStyles: {
                        fontSize: 8
                    },
                    columnStyles: {
                        8: { // Type column
                            fontStyle: 'bold',
                            textColor: [34, 197, 94]
                        }
                    },
                    margin: { top: 45 },
                    didDrawPage: function (data) {
                        // Add page number
                        const pageCount = doc.internal.getNumberOfPages();
                        const str = "Page " + doc.internal.getCurrentPageInfo().pageNumber + " of " + pageCount;
                        doc.setFontSize(8);
                        doc.text(str, doc.internal.pageSize.width - 20, doc.internal.pageSize.height - 10);
                    }
                });
                
                // Create filename with date
                const now = new Date();
                const dateStr = now.toISOString().split('T')[0];
                const fileName = `UC_SitIn_Records_${dateStr}.pdf`;
                
                // Save PDF
                doc.save(fileName);
            };
            
            window.exportToCSV = function() {
                // Create header rows
                const purposeValue = purposeFilter.value || 'All Purposes';
                const labValue = labFilter.value || 'All Laboratories';
                const fromDate = fromDateFilter.value || 'Any Date';
                const toDate = toDateFilter.value || 'Any Date';
                
                const headers = [
                    '"UNIVERSITY OF CEBU"',
                    '"COLLEGE OF COMPUTER STUDIES"',
                    '"Sit-In Monitoring System Report"',
                    `"Generated on: ${new Date().toLocaleString()}"`,
                    `"Purpose: ${purposeValue} | Laboratory: ${labValue} | Date Range: ${fromDate} to ${toDate}"`,
                    '',
                    '"Date","ID Number","Name","Purpose","Laboratory","Time In","Time Out","Duration","Type"'
                ];
                
                const csv = [...headers];
                
                // Extract table rows
                const rows = document.querySelectorAll('#reportsTable tbody tr');
                
                rows.forEach(row => {
                    // Skip "no results" or hidden rows
                    if (row.style.display === 'none' || row.querySelector('td[colspan]')) {
                        return;
                    }
                    
                    const cells = row.querySelectorAll('td');
                    const rowValues = Array.from(cells).map(cell => {
                        // For 'Type' column, get just the text
                        if (cell.querySelector('.type-highlight')) {
                            return `"${cell.querySelector('.type-highlight').textContent.trim()}"`;
                        }
                        // Quote text to handle commas and special characters
                        return `"${cell.textContent.trim()}"`;
                    });
                    
                    csv.push(rowValues.join(','));
                });
                
                // Create a Blob and download
                const BOM = '\uFEFF'; // For Excel CSV compatibility
                const csvContent = BOM + csv.join('\n');
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                
                // Create a download link and trigger it
                const downloadLink = document.createElement('a');
                const now = new Date();
                const dateStr = now.toISOString().split('T')[0];
                const fileName = `UC_SitIn_Records_${dateStr}.csv`;
                
                downloadLink.href = url;
                downloadLink.download = fileName;
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
                URL.revokeObjectURL(url);
            };
            
            window.printTable = function() {
                // Create a printable area with table styling
                const printWindow = window.open('', '_blank', 'width=800,height=600');
                
                // Get filter values
                const purposeValue = purposeFilter.value || 'All Purposes';
                const labValue = labFilter.value || 'All Laboratories';
                const fromDate = fromDateFilter.value || 'Any Date';
                const toDate = toDateFilter.value || 'Any Date';
                
                printWindow.document.write(`
                    <html>
                    <head>
                        <title>Sit-In Reports</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin: 20mm;
                            }
                            .header {
                                text-align: center;
                                margin-bottom: 20px;
                            }
                            .header h1 {
                                margin: 0;
                                font-size: 18px;
                            }
                            .header h2 {
                                margin: 5px 0;
                                font-size: 16px;
                            }
                            .header h3 {
                                margin: 5px 0;
                                font-size: 14px;
                            }
                            .header p {
                                margin: 5px 0;
                                font-size: 12px;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-top: 20px;
                            }
                            th {
                                background-color: #2c343c;
                                color: white;
                                text-align: left;
                                padding: 8px;
                                font-size: 12px;
                            }
                            td {
                                border: 1px solid #ddd;
                                padding: 8px;
                                font-size: 11px;
                            }
                            tr:nth-child(even) {
                                background-color: #f9f9f9;
                            }
                            .type-highlight {
                                color: #22c55e;
                                font-weight: bold;
                            }
                            .filter-info {
                                margin-top: 10px;
                                font-size: 11px;
                                font-style: italic;
                            }
                            @media print {
                                .pagebreak { page-break-before: always; }
                                tfoot { display: table-footer-group; }
                                thead { display: table-header-group; }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h1>UNIVERSITY OF CEBU</h1>
                            <h2>COLLEGE OF COMPUTER STUDIES</h2>
                            <h3>Sit-In Monitoring System Report</h3>
                            <p>Generated on: ${new Date().toLocaleString()}</p>
                            <p class="filter-info">Purpose: ${purposeValue} | Laboratory: ${labValue} | Date Range: ${fromDate} to ${toDate}</p>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>ID Number</th>
                                    <th>Name</th>
                                    <th>Purpose</th>
                                    <th>Laboratory</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Duration</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                `);
                
                // Get visible table rows
                const rows = document.querySelectorAll('#reportsTable tbody tr');
                let hasData = false;
                
                rows.forEach(row => {
                    // Skip "no results" or hidden rows
                    if (row.style.display === 'none' || row.querySelector('td[colspan]')) {
                        return;
                    }
                    
                    hasData = true;
                    const cells = row.querySelectorAll('td');
                    printWindow.document.write('<tr>');
                    
                    cells.forEach((cell, index) => {
                        if (index === 8) { // Type column with styling
                            printWindow.document.write(`<td><span class="type-highlight">${cell.textContent.trim()}</span></td>`);
                        } else {
                            printWindow.document.write(`<td>${cell.textContent.trim()}</td>`);
                        }
                    });
                    
                    printWindow.document.write('</tr>');
                });
                
                // If no data was added
                if (!hasData) {
                    printWindow.document.write('<tr><td colspan="9" style="text-align: center;">No records found matching your filters</td></tr>');
                }
                
                // Complete the HTML document
                printWindow.document.write(`
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9" style="text-align: right; border: none; font-size: 10px;">
                                        Page <span class="pageNumber"></span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <script>
                            // Add page numbers when printing
                            window.onload = function() {
                                window.print();
                                setTimeout(function() {
                                    window.close();
                                }, 500);
                            };
                        </script>
                    </body>
                    </html>
                `);
                
                printWindow.document.close();
            };
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
``` 
