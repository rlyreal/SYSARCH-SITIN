<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id_no, first_name, last_name, course, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$profile_picture = !empty($user_data['profile_picture']) ? $user_data['profile_picture'] : 'profile.jpg';
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>Lab Resources | Sit-In System</title>
    
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
        .resource-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .resource-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        }
        
        .card-img-top {
            height: 180px;
            object-fit: cover;
            background-color: #f8f9fa;
        }
        
        .resource-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        
        .resource-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            color: white;
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .card-img-wrapper {
            position: relative;
            overflow: hidden;
        }
        
        .card-img-wrapper:hover .resource-overlay {
            opacity: 1;
        }
        
        .resource-actions {
            display: flex;
            gap: 10px;
        }
        
        .resource-description {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
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

                    <!-- Resources -->
                    <li class="menu-item active">
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
                                <input 
                                    type="text" 
                                    id="searchInput"
                                    class="form-control border-0 shadow-none" 
                                    placeholder="Search resources..." 
                                    aria-label="Search resources...">
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Filter Dropdown -->
                            <li class="nav-item dropdown d-none d-lg-block me-3">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <i class="bi bi-funnel-fill fs-4"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item filter-item" href="javascript:void(0);" data-filter="all">
                                        <i class="bi bi-check-all me-2"></i>
                                        <span class="align-middle">All Resources</span>
                                    </a>
                                    <a class="dropdown-item filter-item" href="javascript:void(0);" data-filter="Programming">
                                        <i class="bi bi-code-slash me-2"></i>
                                        <span class="align-middle">Programming</span>
                                    </a>
                                    <a class="dropdown-item filter-item" href="javascript:void(0);" data-filter="Database">
                                        <i class="bi bi-database me-2"></i>
                                        <span class="align-middle">Database</span>
                                    </a>
                                    <a class="dropdown-item filter-item" href="javascript:void(0);" data-filter="Web">
                                        <i class="bi bi-globe me-2"></i>
                                        <span class="align-middle">Web Development</span>
                                    </a>
                                    <a class="dropdown-item filter-item" href="javascript:void(0);" data-filter="Recent">
                                        <i class="bi bi-clock me-2"></i>
                                        <span class="align-middle">Recently Added</span>
                                    </a>
                                </div>
                            </li>

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
                                                    <span class="fw-semibold d-block"><?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']); ?></span>
                                                    <small class="text-muted"><?php echo htmlspecialchars($user_data['course']); ?></small>
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
                            <i class="bi bi-box me-1"></i> Laboratory Resources
                        </h4>

                        <!-- Resources Filters -->
                        <div class="mb-4">
                            <div class="nav-align-top">
                                <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active filter-btn" role="tab" data-bs-toggle="tab" data-filter="all">
                                            <i class="bi bi-grid me-1"></i> All Resources
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link filter-btn" role="tab" data-bs-toggle="tab" data-filter="Programming">
                                            <i class="bi bi-code-slash me-1"></i> Programming
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link filter-btn" role="tab" data-bs-toggle="tab" data-filter="Database">
                                            <i class="bi bi-database me-1"></i> Database
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link filter-btn" role="tab" data-bs-toggle="tab" data-filter="Web">
                                            <i class="bi bi-globe me-1"></i> Web Development
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Resources Grid -->
                        <div class="row g-4 mb-4" id="resourcesGrid">
                            <!-- Resources will be loaded here -->
                            
                            <!-- Loading Spinner -->
                            <div id="loadingSpinner" class="col-12 text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted">Loading resources...</p>
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

    <!-- Resource Viewer Modal -->
    <div class="modal fade" id="resourceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resourceTitle">Resource Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <img id="resourceImage" src="" alt="Resource Preview" class="img-fluid rounded">
                        </div>
                        <div class="col-md-6">
                            <h5 id="resourceModalTitle" class="mb-2"></h5>
                            <div class="mb-2">
                                <span class="badge bg-label-primary me-1" id="resourceCategory"></span>
                                <span class="badge bg-label-info" id="resourceDate"></span>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Provided by:</small>
                                <p class="mb-0" id="resourceProfessor"></p>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Description:</small>
                                <p id="resourceDescription"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <a href="#" id="resourceLink" target="_blank" class="btn btn-primary">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Open Resource
                    </a>
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
            const grid = document.getElementById('resourcesGrid');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const resourceModal = new bootstrap.Modal(document.getElementById('resourceModal'));
            
            // Load resources from API
            async function loadResources(query = '', filter = 'all') {
                try {
                    // Show loading spinner
                    loadingSpinner.style.display = 'block';
                    
                    // API endpoint with query params if available
                    const endpoint = `get_user_resources.php${query ? '?query=' + encodeURIComponent(query) : ''}`;
                    const response = await fetch(endpoint);
                    const data = await response.json();
                    
                    // Hide loading spinner
                    loadingSpinner.style.display = 'none';
                    
                    // Clear previous resources
                    grid.innerHTML = '';
                    
                    // Filter resources if needed
                    let filteredData = data;
                    if (filter !== 'all') {
                        if (filter === 'Recent') {
                            // Sort by date, most recent first
                            filteredData = [...data].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                            // Get first 6 items only
                            filteredData = filteredData.slice(0, 6);
                        } else {
                            // Filter by category
                            filteredData = data.filter(resource => 
                                resource.category && resource.category.includes(filter)
                            );
                        }
                    }
                    
                    // Display no resources message if needed
                    if (filteredData.length === 0) {
                        grid.innerHTML = `
                            <div class="col-12 text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-search text-primary" style="font-size: 3rem;"></i>
                                </div>
                                <h5 class="mb-2">No Resources Found</h5>
                                <p class="text-muted">
                                    ${query ? `No resources matching "${query}"` : 'No resources available in this category.'}
                                </p>
                            </div>
                        `;
                        return;
                    }
                    
                    // Generate resource cards
                    filteredData.forEach(resource => {
                        // Default image if none is provided
                        const coverImage = resource.cover_image || 'placeholder.png';
                        
                        // Format date
                        const date = new Date(resource.created_at);
                        const formattedDate = date.toLocaleDateString('en-US', {
                            year: 'numeric', 
                            month: 'short', 
                            day: 'numeric'
                        });
                        
                        // Create category badge
                        let categoryBadge = '';
                        if (resource.category) {
                            const category = resource.category;
                            let badgeClass = 'bg-label-primary';
                            let icon = 'bi-book';
                            
                            if (category.includes('Programming')) {
                                badgeClass = 'bg-label-danger';
                                icon = 'bi-code-slash';
                            } else if (category.includes('Database')) {
                                badgeClass = 'bg-label-info';
                                icon = 'bi-database';
                            } else if (category.includes('Web')) {
                                badgeClass = 'bg-label-success';
                                icon = 'bi-globe';
                            }
                            
                            categoryBadge = `
                                <span class="badge ${badgeClass} resource-badge">
                                    <i class="bi ${icon} me-1"></i> ${category}
                                </span>
                            `;
                        }
                        
                        // Create card HTML
                        const card = document.createElement('div');
                        card.className = 'col-md-6 col-lg-4';
                        card.innerHTML = `
                            <div class="card resource-card">
                                <div class="card-img-wrapper">
                                    <img src="${coverImage}" class="card-img-top" alt="${resource.title}">
                                    ${categoryBadge}
                                    <div class="resource-overlay">
                                        <div class="resource-actions">
                                            <button type="button" class="btn btn-icon btn-sm btn-primary view-resource" 
                                                data-id="${resource.id}" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <a href="${resource.resource_link}" target="_blank" 
                                                class="btn btn-icon btn-sm btn-secondary" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                title="Open Resource">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title mb-1">${resource.title}</h5>
                                    <div class="d-flex align-items-center mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-person me-1"></i> ${resource.professor}
                                        </small>
                                    </div>
                                    <p class="card-text resource-description">${resource.description}</p>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-event me-1"></i> ${formattedDate}
                                        </small>
                                        <button type="button" class="btn btn-sm btn-primary view-resource" data-id="${resource.id}">
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        grid.appendChild(card);
                    });
                    
                    // Initialize tooltips
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                    
                    // Add event listeners to view buttons
                    document.querySelectorAll('.view-resource').forEach(button => {
                        button.addEventListener('click', () => {
                            const resourceId = button.getAttribute('data-id');
                            const resource = filteredData.find(r => r.id == resourceId);
                            
                            if (resource) {
                                document.getElementById('resourceModalTitle').textContent = resource.title;
                                document.getElementById('resourceTitle').textContent = resource.title;
                                document.getElementById('resourceImage').src = resource.cover_image || 'placeholder.png';
                                document.getElementById('resourceCategory').textContent = resource.category || 'General';
                                document.getElementById('resourceProfessor').textContent = resource.professor;
                                document.getElementById('resourceDescription').textContent = resource.description;
                                document.getElementById('resourceLink').href = resource.resource_link;
                                
                                const date = new Date(resource.created_at);
                                const formattedDate = date.toLocaleDateString('en-US', {
                                    year: 'numeric', 
                                    month: 'short', 
                                    day: 'numeric'
                                });
                                document.getElementById('resourceDate').textContent = formattedDate;
                                
                                resourceModal.show();
                            }
                        });
                    });
                    
                } catch (error) {
                    console.error('Error loading resources:', error);
                    
                    // Hide loading spinner
                    loadingSpinner.style.display = 'none';
                    
                    // Show error message
                    grid.innerHTML = `
                        <div class="col-12 text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="mb-2">Failed to Load Resources</h5>
                            <p class="text-muted">An error occurred while loading resources. Please try again later.</p>
                        </div>
                    `;
                }
            }
            
            // Initialize resources
            loadResources();
            
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            let debounceTimer;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    loadResources(this.value.trim());
                }, 300);
            });
            
            // Filter functionality
            document.querySelectorAll('.filter-btn, .filter-item').forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    
                    // Update active state for nav buttons
                    document.querySelectorAll('.filter-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    if (this.classList.contains('filter-btn')) {
                        this.classList.add('active');
                    }
                    
                    // Load resources with filter
                    loadResources(searchInput.value.trim(), filter);
                });
            });
            
            // Logout modal
            document.getElementById('logoutBtn').addEventListener('click', function() {
                const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
                logoutModal.show();
            });
        });
    </script>
</body>
</html>