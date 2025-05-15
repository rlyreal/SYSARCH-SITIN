<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$admin_username = $_SESSION['username'] ?? 'Admin User';
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Resources | Admin</title>
    
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
                    <li class="menu-item active">
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
                                <input type="text" class="form-control border-0 shadow-none search-resource" placeholder="Search resources..." aria-label="Search...">
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
                            <span class="text-muted fw-light">Features /</span> Resources
                        </h4>

                        <!-- Resource Hub Section -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Resource Hub</h5>
                                <button class="btn btn-primary btn-sm" id="addResourceBtn">
                                    <i class="bi bi-plus-circle me-1"></i> Add Resource
                                </button>
                            </div>
                            <div class="card-body">
                                <!-- No Resources Message (shown conditionally) -->
                                <div id="noResourcesMessage" class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="bi bi-folder text-primary" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="mb-2">No Resources Found</h5>
                                    <p class="text-muted mb-4">Start adding educational resources to build your collection</p>
                                </div>

                                <!-- Resources Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover" id="resourcesTable">
                                        <thead>
                                            <tr>
                                                <th>Resource</th>
                                                <th>Professor</th>
                                                <th>Description</th>
                                                <th>Added</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td colspan="5" class="text-center py-3 text-muted">No resources added yet.</td>
                                            </tr>
                                        </tbody>
                                    </table>
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

    <!-- Add Resource Modal -->
    <div class="modal fade" id="addResourceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Resource</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="resourceForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cover Image</label>
                                <div class="border rounded-3 p-3 text-center upload-zone" id="dropZone">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-cloud-arrow-up text-primary mb-2" style="font-size: 2rem;"></i>
                                        <p class="mb-1">Drop files here or click to upload</p>
                                        <p class="text-muted small">PNG, JPG up to 5MB</p>
                                    </div>
                                    <input type="file" class="d-none" accept="image/png, image/jpeg" id="coverImage">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Resource Preview</label>
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar avatar-lg" id="previewImage">
                                                <div class="avatar-initial rounded bg-label-primary">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0" id="previewTitle">Resource Title</h6>
                                            <small class="text-muted d-block mb-2" id="previewProfessor">Professor Name</small>
                                            <p class="text-muted small mb-1" id="previewDescription">Resource description will appear here...</p>
                                            <a href="#" class="small" id="previewLink">Resource Link</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-7 mb-3">
                                <label class="form-label">Resource Title</label>
                                <input type="text" id="resourceTitle" class="form-control" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Professor</label>
                                <input type="text" id="professor" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea id="description" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Resource Link</label>
                                <input type="url" id="resourceLink" class="form-control" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="saveResource" class="btn btn-primary">Save Resource</button>
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
            // Initialize modals
            const addResourceModal = new bootstrap.Modal(document.getElementById('addResourceModal'));
            const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
            
            // Add Resource button
            document.getElementById('addResourceBtn').addEventListener('click', function() {
                // Reset form
                document.getElementById('resourceForm').reset();
                // Reset preview
                document.getElementById('previewTitle').textContent = 'Resource Title';
                document.getElementById('previewProfessor').textContent = 'Professor Name';
                document.getElementById('previewDescription').textContent = 'Resource description will appear here...';
                document.getElementById('previewLink').textContent = 'Resource Link';
                document.getElementById('previewLink').href = '#';
                document.getElementById('previewImage').innerHTML = `
                    <div class="avatar-initial rounded bg-label-primary">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>`;
                
                // Show modal
                addResourceModal.show();
            });

            // Logout button
            document.getElementById('logoutBtn').addEventListener('click', function() {
                logoutModal.show();
            });

            // File upload
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('coverImage');

            dropZone.addEventListener('click', () => fileInput.click());
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-primary');
            });
            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-primary');
            });
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-primary');
                fileInput.files = e.dataTransfer.files;
                handleFileUpload(e.dataTransfer.files[0]);
            });

            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFileUpload(e.target.files[0]);
                }
            });

            function handleFileUpload(file) {
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('previewImage').innerHTML = `
                            <img src="${e.target.result}" class="rounded" alt="Resource Cover">`;
                    };
                    reader.readAsDataURL(file);
                }
            }

            // Live preview updates
            document.getElementById('resourceTitle').addEventListener('input', function(e) {
                document.getElementById('previewTitle').textContent = e.target.value || 'Resource Title';
            });

            document.getElementById('professor').addEventListener('input', function(e) {
                document.getElementById('previewProfessor').textContent = e.target.value || 'Professor Name';
            });

            document.getElementById('description').addEventListener('input', function(e) {
                document.getElementById('previewDescription').textContent = e.target.value || 'Resource description will appear here...';
            });

            document.getElementById('resourceLink').addEventListener('input', function(e) {
                const link = document.getElementById('previewLink');
                link.href = e.target.value;
                link.textContent = e.target.value || 'Resource Link';
            });

            // Save resource
            document.getElementById('saveResource').addEventListener('click', async function() {
                const form = document.getElementById('resourceForm');
                const title = document.getElementById('resourceTitle').value;
                const professor = document.getElementById('professor').value;
                const description = document.getElementById('description').value;
                const resourceLink = document.getElementById('resourceLink').value;
                const coverImage = document.getElementById('coverImage').files[0];
                const resourceId = document.getElementById('resourceId')?.value; // Optional for updates
                
                if (!title || !professor || !description || !resourceLink) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                const formData = new FormData();
                formData.append('title', title);
                formData.append('professor', professor);
                formData.append('description', description);
                formData.append('resource_link', resourceLink);
                if (coverImage) {
                    formData.append('cover_image', coverImage);
                }
                
                // If resourceId exists, this is an update operation
                if (resourceId) {
                    formData.append('id', resourceId);
                    formData.append('action', 'update_resource');
                } else {
                    formData.append('action', 'add_resource');
                }
                
                try {
                    const response = await fetch('handle_resource.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        addResourceModal.hide();
                        loadResources();
                        // Reset the form after successful operation
                        form.reset();
                        // Reset modal title and button text
                        document.getElementById('modalTitle').textContent = 'Add New Resource';
                        document.getElementById('saveResource').textContent = 'Save Resource';
                        // Remove resource ID if it exists
                        const resourceIdInput = document.getElementById('resourceId');
                        if (resourceIdInput) resourceIdInput.remove();
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while saving the resource');
                }
            });

            // Load resources
            function loadResources() {
                fetch('handle_resource.php?action=get_recent')
                    .then(response => response.json())
                    .then(data => {
                        const noResourcesMsg = document.getElementById('noResourcesMessage');
                        const tbody = document.querySelector('#resourcesTable tbody');
                        
                        if (data.length === 0) {
                            noResourcesMsg.style.display = 'block';
                            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-3 text-muted">No resources added yet.</td></tr>`;
                            return;
                        }
                        
                        noResourcesMsg.style.display = 'none';
                        tbody.innerHTML = data.map(resource => `
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="avatar avatar-sm flex-shrink-0 me-3">
                                            ${resource.cover_image ? 
                                                `<img src="${resource.cover_image}" alt="${resource.title}" class="rounded">` : 
                                                `<span class="avatar-initial rounded bg-label-primary"><i class="bi bi-file-earmark-text"></i></span>`
                                            }
                                        </div>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-0">${resource.title}</h6>
                                            <small class="text-truncate" style="max-width: 150px;">${resource.resource_link}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>${resource.professor}</td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;">${resource.description}</span>
                                </td>
                                <td>${new Date(resource.created_at).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                })}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="${resource.resource_link}" target="_blank">
                                                <i class="bi bi-box-arrow-up-right me-2"></i> Open
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="editResource(${resource.id})">
                                                <i class="bi bi-pencil me-2"></i> Edit
                                            </a>
                                            <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteResource(${resource.id})">
                                                <i class="bi bi-trash me-2"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            // Search resources
            const searchInput = document.querySelector('.search-resource');
            searchInput.addEventListener('input', debounce(function(e) {
                const query = e.target.value.trim();
                if (query) {
                    searchResources(query);
                } else {
                    loadResources();
                }
            }, 300));

            function searchResources(query) {
                fetch(`handle_resource.php?action=search&query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        const noResourcesMsg = document.getElementById('noResourcesMessage');
                        const tbody = document.querySelector('#resourcesTable tbody');
                        
                        if (data.length === 0) {
                            noResourcesMsg.style.display = 'none';
                            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-3 text-muted">No resources found matching "${query}"</td></tr>`;
                            return;
                        }
                        
                        noResourcesMsg.style.display = 'none';
                        tbody.innerHTML = data.map(resource => `
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="avatar avatar-sm flex-shrink-0 me-3">
                                            ${resource.cover_image ? 
                                                `<img src="${resource.cover_image}" alt="${resource.title}" class="rounded">` : 
                                                `<span class="avatar-initial rounded bg-label-primary"><i class="bi bi-file-earmark-text"></i></span>`
                                            }
                                        </div>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-0">${resource.title}</h6>
                                            <small class="text-truncate" style="max-width: 150px;">${resource.resource_link}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>${resource.professor}</td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;">${resource.description}</span>
                                </td>
                                <td>${new Date(resource.created_at).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                })}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="${resource.resource_link}" target="_blank">
                                                <i class="bi bi-box-arrow-up-right me-2"></i> Open
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="editResource(${resource.id})">
                                                <i class="bi bi-pencil me-2"></i> Edit
                                            </a>
                                            <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteResource(${resource.id})">
                                                <i class="bi bi-trash me-2"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            // Debounce function
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Add edit and delete functions to window object
            window.editResource = function(id) {
                fetch(`handle_resource.php?action=get_resource&id=${id}`)
                    .then(response => response.json())
                    .then(resource => {
                        // Populate form with resource details
                        document.getElementById('resourceTitle').value = resource.title;
                        document.getElementById('professor').value = resource.professor;
                        document.getElementById('description').value = resource.description;
                        document.getElementById('resourceLink').value = resource.resource_link;
                        
                        // Update preview
                        document.getElementById('previewTitle').textContent = resource.title;
                        document.getElementById('previewProfessor').textContent = resource.professor;
                        document.getElementById('previewDescription').textContent = resource.description;
                        document.getElementById('previewLink').textContent = resource.resource_link;
                        document.getElementById('previewLink').href = resource.resource_link;
                        
                        if (resource.cover_image) {
                            document.getElementById('previewImage').innerHTML = `
                                <img src="${resource.cover_image}" class="rounded" alt="Resource Cover">`;
                        } else {
                            document.getElementById('previewImage').innerHTML = `
                                <div class="avatar-initial rounded bg-label-primary">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>`;
                        }
                        
                        // Add resource ID for update
                        const form = document.getElementById('resourceForm');
                        // Check if ID input already exists and remove it to prevent duplicates
                        const existingIdInput = document.getElementById('resourceId');
                        if (existingIdInput) {
                            existingIdInput.remove();
                        }
                        
                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.id = 'resourceId';
                        idInput.name = 'id'; // Make sure this matches what your server expects
                        idInput.value = id;
                        form.appendChild(idInput);
                        
                        // Show modal
                        document.getElementById('modalTitle').textContent = 'Edit Resource';
                        document.getElementById('saveResource').textContent = 'Update Resource';
                        addResourceModal.show();
                    })
                    .catch(error => {
                        console.error('Error fetching resource:', error);
                        alert('Failed to load resource information');
                    });
            };

            window.deleteResource = function(id) {
                if (confirm('Are you sure you want to delete this resource?')) {
                    fetch('handle_resource.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=delete_resource&id=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadResources();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the resource');
                    });
                }
            };

            // Initial load
            loadResources();
        });
    </script>
</body>
</html>