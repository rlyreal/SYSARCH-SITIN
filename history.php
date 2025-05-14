<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get the current user's ID number from the users table
$user_id = $_SESSION['user_id'];
$id_query = "SELECT id_no, profile_picture, first_name, last_name, course FROM users WHERE id = ?";
$stmt = $conn->prepare($id_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$id_result = $stmt->get_result();
$user_data = $id_result->fetch_assoc();
$user_id_no = $user_data['id_no'];
$profile_picture = !empty($user_data['profile_picture']) ? $user_data['profile_picture'] : 'profile.jpg';

// Fetch history data for current user only using their ID number
$sql = "SELECT 
    id,
    idno,
    fullname,
    purpose,
    laboratory,
    DATE_FORMAT(time_in, '%h:%i %p') as time_in,
    DATE_FORMAT(time_out, '%h:%i %p') as time_out,
    DATE(date) as date,
    CASE 
        WHEN time_out IS NOT NULL THEN 'Completed'
        ELSE 'Active'
    END as status
FROM sit_in
WHERE idno = ?
ORDER BY date DESC, time_in DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id_no);
$stmt->execute();
$result = $stmt->get_result();

// Add debug output to verify filtering
if ($result->num_rows === 0) {
    echo "<!-- Debug: No records found for user ID: " . htmlspecialchars($user_id_no) . " -->";
}
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>History | Sit-In System</title>
    
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
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
        }
        
        .rating input {
            display: none;
        }
        
        .rating label {
            cursor: pointer;
            width: 1.5rem;
            font-size: 1.5rem;
            color: #d4d4d4;
            transition: all 0.2s;
        }
        
        .rating input:checked ~ label,
        .rating label:hover,
        .rating label:hover ~ label {
            color: #FBBF24;
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
                    <li class="menu-item active">
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
                    <li class="menu-item">
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
                                <input type="text" id="historySearch" class="form-control border-0 shadow-none" placeholder="Search history..." aria-label="Search...">
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
                            <i class="bi bi-clock-history me-2"></i> Sit-In History
                        </h4>

                        <!-- History Table -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Your Lab Activity History</h5>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-filter me-1"></i> Filter
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item filter-btn" data-filter="all" href="javascript:void(0);">All</a></li>
                                        <li><a class="dropdown-item filter-btn" data-filter="active" href="javascript:void(0);">Active</a></li>
                                        <li><a class="dropdown-item filter-btn" data-filter="completed" href="javascript:void(0);">Completed</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item filter-btn" data-filter="thisweek" href="javascript:void(0);">This Week</a></li>
                                        <li><a class="dropdown-item filter-btn" data-filter="thismonth" href="javascript:void(0);">This Month</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover" id="historyTable">
                                    <thead>
                                        <tr>
                                            <th>ID Number</th>
                                            <th>Name</th>
                                            <th>Purpose</th>
                                            <th>Laboratory</th>
                                            <th>Time In</th>
                                            <th>Time Out</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        <?php
                                        if ($result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                $statusBadge = $row['status'] === 'Completed' 
                                                    ? 'badge bg-label-success' 
                                                    : 'badge bg-label-warning';
                                                ?>
                                                <tr>
                                                    <td><span class="fw-medium"><?php echo htmlspecialchars($row['idno']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                                                    <td><span class="badge bg-label-primary"><?php echo htmlspecialchars($row['laboratory']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($row['time_in']); ?></td>
                                                    <td><?php echo $row['time_out'] ? htmlspecialchars($row['time_out']) : '<i class="text-muted">--:--</i>'; ?></td>
                                                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                                                    <td><span class="<?php echo $statusBadge; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary feedback-btn" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#feedbackModal" 
                                                                data-sitinid="<?php echo $row['id']; ?>">
                                                            <i class="bi bi-chat-dots me-1"></i> Feedback
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="9" class="text-center py-3">
                                                    <div class="d-flex justify-content-center align-items-center flex-column">
                                                        <i class="bi bi-folder2-open text-primary mb-2" style="font-size: 2rem;"></i>
                                                        <h6 class="mb-0 text-muted">No records found</h6>
                                                        <p class="mb-0 small text-muted">You haven't used the laboratory yet.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--/ History Table -->
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

    <!-- Feedback Modal -->
    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lab Session Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-chat-square-heart text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-2">Rate your experience</h5>
                    </div>

                    <!-- Star Rating -->
                    <div class="rating mb-4">
                        <input type="radio" id="star5" name="rating" value="5" />
                        <label for="star5">★</label>
                        <input type="radio" id="star4" name="rating" value="4" />
                        <label for="star4">★</label>
                        <input type="radio" id="star3" name="rating" value="3" />
                        <label for="star3">★</label>
                        <input type="radio" id="star2" name="rating" value="2" />
                        <label for="star2">★</label>
                        <input type="radio" id="star1" name="rating" value="1" />
                        <label for="star1">★</label>
                    </div>

                    <div class="mb-3">
                        <label for="feedbackText" class="form-label">Comments & Suggestions</label>
                        <textarea class="form-control" id="feedbackText" rows="3" placeholder="Share your experience and suggestions for improvement..."></textarea>
                    </div>

                    <input type="hidden" id="sitInIdField" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" id="submitFeedback" class="btn btn-primary">Submit Feedback</button>
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

    <!-- Sweet Alert for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Main JS -->
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the feedback modal
            const feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
            
            // Handle feedback button clicks
            const feedbackBtns = document.querySelectorAll('.feedback-btn');
            feedbackBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const sitInId = this.getAttribute('data-sitinid');
                    document.getElementById('sitInIdField').value = sitInId;
                    // Reset form on open
                    document.querySelectorAll('input[name="rating"]').forEach(radio => radio.checked = false);
                    document.getElementById('feedbackText').value = '';
                });
            });
            
            // Handle submit feedback
            document.getElementById('submitFeedback').addEventListener('click', function() {
                const sitInId = document.getElementById('sitInIdField').value;
                const rating = document.querySelector('input[name="rating"]:checked')?.value;
                const feedbackText = document.getElementById('feedbackText').value.trim();
                
                if (!rating) {
                    Swal.fire({
                        title: 'Rating Required',
                        text: 'Please select a star rating',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }
                
                if (!feedbackText) {
                    Swal.fire({
                        title: 'Feedback Required',
                        text: 'Please enter your feedback or comments',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }
                
                // Submit feedback using fetch
                fetch('submit_feedback.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        sit_in_id: sitInId,
                        rating: rating,
                        feedback_text: feedbackText
                    })
                })
                .then(response => response.json())
                .then(data => {
                    feedbackModal.hide();
                    
                    if (data.success) {
                        Swal.fire({
                            title: 'Thank You!',
                            text: 'Your feedback has been submitted successfully',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'There was a problem submitting your feedback',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    feedbackModal.hide();
                    
                    Swal.fire({
                        title: 'Error',
                        text: 'An unexpected error occurred. Please try again later.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                });
            });
            
            // Search functionality
            document.getElementById('historySearch').addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase();
                const table = document.getElementById('historyTable');
                const rows = table.getElementsByTagName('tr');
                
                for (let i = 1; i < rows.length; i++) { // Start from 1 to skip header
                    const row = rows[i];
                    const cols = row.getElementsByTagName('td');
                    
                    // Skip the no records row
                    if (cols.length === 1) continue;
                    
                    let found = false;
                    for (let j = 0; j < cols.length - 1; j++) { // Exclude the actions column
                        const text = cols[j].textContent.toLowerCase();
                        if (text.indexOf(searchValue) > -1) {
                            found = true;
                            break;
                        }
                    }
                    
                    row.style.display = found ? '' : 'none';
                }
            });
            
            // Filter functionality
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const filterType = this.getAttribute('data-filter');
                    const table = document.getElementById('historyTable');
                    const rows = table.getElementsByTagName('tr');
                    
                    for (let i = 1; i < rows.length; i++) { // Start from 1 to skip header
                        const row = rows[i];
                        const cols = row.getElementsByTagName('td');
                        
                        // Skip the no records row
                        if (cols.length === 1) continue;
                        
                        const statusCell = cols[7]; // Status column
                        const dateCell = cols[6];   // Date column
                        
                        let show = true;
                        
                        if (filterType === 'active') {
                            show = statusCell.textContent.trim() === 'Active';
                        } else if (filterType === 'completed') {
                            show = statusCell.textContent.trim() === 'Completed';
                        } else if (filterType === 'thisweek') {
                            const date = new Date(dateCell.textContent);
                            const now = new Date();
                            const weekStart = new Date(now.setDate(now.getDate() - now.getDay()));
                            const weekEnd = new Date(new Date().setDate(weekStart.getDate() + 6));
                            show = date >= weekStart && date <= weekEnd;
                        } else if (filterType === 'thismonth') {
                            const date = new Date(dateCell.textContent);
                            const now = new Date();
                            show = date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear();
                        }
                        
                        row.style.display = show ? '' : 'none';
                    }
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

<?php
$conn->close();
?>