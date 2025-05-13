<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, id_no, last_name, first_name, middle_name, course, year_level, email, address, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_number = $row['id_no'];  
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $middle_name = $row['middle_name'];
    $course = $row['course'];
    $year_level = $row['year_level'];
    $email = $row['email'];
    $address = $row['address'];
    $profile_picture = !empty($row['profile_picture']) ? $row['profile_picture'] : 'profile.jpg';
} else {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>Edit Profile | Sit-In System</title>
    
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
    
    <!-- Alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Helpers -->
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/js/helpers.js"></script>
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/js/config.js"></script>
    
    <style>
        .upload-area {
            border: 2px dashed #ced4da;
            border-radius: 50%;
            width: 160px;
            height: 160px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            overflow: hidden;
            margin: 0 auto;
        }
        
        .upload-area:hover {
            border-color: #696cff;
        }
        
        .upload-area .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 50%;
        }
        
        .upload-area:hover .overlay {
            opacity: 1;
        }
        
        .upload-area img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .upload-area .overlay i {
            color: white;
            font-size: 24px;
        }
    </style>
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
                    <li class="menu-item active">
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
                            <span class="text-muted fw-light">Account Settings /</span> Profile
                        </h4>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <h5 class="card-header">Profile Details</h5>
                                    <!-- Account -->
                                    <div class="card-body">
                                        <form id="formAccountSettings" action="updateprofile.php" method="POST" enctype="multipart/form-data">
                                            <div class="d-flex align-items-start align-items-sm-center gap-4 mb-4">
                                                <div class="upload-area" id="profile_upload_area">
                                                    <img id="profile_preview" src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="d-block rounded-circle">
                                                    <div class="overlay">
                                                        <i class="bi bi-camera"></i>
                                                    </div>
                                                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="d-none">
                                                </div>
                                                <div class="button-wrapper">
                                                    <label for="profile_picture" class="btn btn-primary me-2 mb-3">
                                                        <i class="bi bi-upload me-2"></i>
                                                        <span class="d-none d-sm-block">Upload new photo</span>
                                                    </label>
                                                    <button type="button" id="reset_image" class="btn btn-outline-secondary mb-3">
                                                        <i class="bi bi-arrow-counterclockwise me-2"></i>
                                                        <span class="d-none d-sm-block">Reset</span>
                                                    </button>
                                                    <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
                                                </div>
                                            </div>
                                            <hr class="my-4" />
                                            <div class="row">
                                                <div class="mb-3 col-md-6">
                                                    <label for="id_number" class="form-label">ID Number</label>
                                                    <input class="form-control" type="text" id="id_number" name="id_number" value="<?php echo htmlspecialchars($id_number); ?>" readonly />
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="course" class="form-label">Course</label>
                                                    <select id="course" name="course" class="select2 form-select">
                                                        <option value="BSIT" <?php echo ($course == "BSIT") ? 'selected' : ''; ?>>BSIT (Information Technology)</option>
                                                        <option value="BSCS" <?php echo ($course == "BSCS") ? 'selected' : ''; ?>>BSCS (Computer Science)</option>
                                                        <option value="BSIS" <?php echo ($course == "BSIS") ? 'selected' : ''; ?>>BSIS (Information Systems)</option>
                                                        <option value="BSECE" <?php echo ($course == "BSECE") ? 'selected' : ''; ?>>BSECE (Electronics Engineering)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3 col-md-4">
                                                    <label for="last_name" class="form-label">Last Name</label>
                                                    <input class="form-control" type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" pattern="[A-Za-z\s]+" required />
                                                </div>
                                                <div class="mb-3 col-md-4">
                                                    <label for="first_name" class="form-label">First Name</label>
                                                    <input class="form-control" type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" pattern="[A-Za-z\s]+" required />
                                                </div>
                                                <div class="mb-3 col-md-4">
                                                    <label for="middle_name" class="form-label">Middle Name</label>
                                                    <input class="form-control" type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($middle_name); ?>" pattern="[A-Za-z\s]+" />
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="course_level" class="form-label">Year Level</label>
                                                    <select id="course_level" name="course_level" class="select2 form-select">
                                                        <option value="1" <?php echo ($year_level == 1) ? 'selected' : ''; ?>>1st Year</option>
                                                        <option value="2" <?php echo ($year_level == 2) ? 'selected' : ''; ?>>2nd Year</option>
                                                        <option value="3" <?php echo ($year_level == 3) ? 'selected' : ''; ?>>3rd Year</option>
                                                        <option value="4" <?php echo ($year_level == 4) ? 'selected' : ''; ?>>4th Year</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input class="form-control" type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required />
                                                </div>
                                                <div class="mb-3 col-md-12">
                                                    <label for="address" class="form-label">Address</label>
                                                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required />
                                                </div>
                                            </div>
                                            <div class="mt-2 d-flex justify-content-end">
                                                <button type="button" class="btn btn-outline-secondary me-2" onclick="window.location.href='dashboard.php'">Cancel</button>
                                                <button type="button" id="saveChangesBtn" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /Account -->
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

    <!-- Save Changes Modal -->
    <div class="modal fade" id="saveChangesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Changes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-question-circle text-primary" style="font-size: 3rem;"></i>
                        <h4 class="mt-3">Are you sure you want to save these changes?</h4>
                        <p class="text-muted">Your profile information will be updated.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmSaveBtn" class="btn btn-primary">Save Changes</button>
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
                    <div class="text-center mb-3">
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
            // Profile picture upload preview
            const profileInput = document.getElementById('profile_picture');
            const profilePreview = document.getElementById('profile_preview');
            const profileUploadArea = document.getElementById('profile_upload_area');
            const initialImageSrc = profilePreview.src;

            // Trigger file input when clicking on the upload area
            profileUploadArea.addEventListener('click', function() {
                profileInput.click();
            });

            // Change preview image when a file is selected
            profileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profilePreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Reset image button
            document.getElementById('reset_image').addEventListener('click', function() {
                profilePreview.src = initialImageSrc;
                profileInput.value = '';
            });

            // Name input validation - only allow letters and spaces
            const nameInputs = document.querySelectorAll('input[pattern="[A-Za-z\\s]+"]');
            nameInputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^A-Za-z\s]/g, '');
                });
            });

            // Save changes button
            const saveChangesBtn = document.getElementById('saveChangesBtn');
            const saveChangesModal = new bootstrap.Modal(document.getElementById('saveChangesModal'));
            const confirmSaveBtn = document.getElementById('confirmSaveBtn');
            const form = document.getElementById('formAccountSettings');

            saveChangesBtn.addEventListener('click', function() {
                if (form.checkValidity()) {
                    saveChangesModal.show();
                } else {
                    form.reportValidity();
                }
            });

            confirmSaveBtn.addEventListener('click', function() {
                form.submit();
            });

            // Logout button
            document.getElementById('logoutBtn').addEventListener('click', function() {
                const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
                logoutModal.show();
            });

            // SweetAlert2 for success message
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has("message")) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: urlParams.get("message"),
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false,
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = "dashboard.php"; // Redirect to dashboard
                });

                // Remove the message from URL after showing the alert
                window.history.replaceState(null, "", window.location.pathname);
            }
        });
    </script>
</body>
</html>