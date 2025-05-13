<?php
// filepath: c:\xampp\htdocs\SYSARCH-SITIN\admin_sched.php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Add new schedule
        if ($_POST['action'] === 'add_schedule') {
            $laboratory = $_POST['laboratory'];
            $day = $_POST['day'];
            $time_start = $_POST['time_start'];
            $time_end = $_POST['time_end'];
            $subject = $_POST['subject'];
            $professor = $_POST['professor'];
            
            // Modified SQL to match lab_schedule table structure
            $stmt = $conn->prepare("INSERT INTO lab_schedule (DAY, LABORATORY, TIME_START, TIME_END, SUBJECT, PROFESSOR) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $day, $laboratory, $time_start, $time_end, $subject, $professor);
            
            if ($stmt->execute()) {
                $success_message = "Schedule added successfully!";
            } else {
                $error_message = "Error adding schedule: " . $conn->error;
            }
        }
        
        // Delete schedule
        else if ($_POST['action'] === 'delete_schedule') {
            $schedule_id = $_POST['schedule_id'];
            
            // Modified SQL to match lab_schedule table structure
            $stmt = $conn->prepare("DELETE FROM lab_schedule WHERE SCHED_ID = ?");
            $stmt->bind_param("i", $schedule_id);
            
            if ($stmt->execute()) {
                $success_message = "Schedule deleted successfully!";
            } else {
                $error_message = "Error deleting schedule: " . $conn->error;
            }
        }
        
        // Update schedule
        else if ($_POST['action'] === 'update_schedule') {
            $schedule_id = $_POST['schedule_id'];
            $laboratory = $_POST['laboratory'];
            $day = $_POST['day'];
            $time_start = $_POST['time_start'];
            $time_end = $_POST['time_end'];
            $subject = $_POST['subject'];
            $professor = $_POST['professor'];
            
            // Modified SQL to match lab_schedule table structure
            $stmt = $conn->prepare("UPDATE lab_schedule SET LABORATORY = ?, DAY = ?, TIME_START = ?, 
                                   TIME_END = ?, SUBJECT = ?, PROFESSOR = ? WHERE SCHED_ID = ?");
            $stmt->bind_param("ssssssi", $laboratory, $day, $time_start, $time_end, $subject, $professor, $schedule_id);
            
            if ($stmt->execute()) {
                $success_message = "Schedule updated successfully!";
            } else {
                $error_message = "Error updating schedule: " . $conn->error;
            }
        }
    }
}

// Get filter parameters
$selected_lab = $_GET['lab'] ?? '517';
$selected_day = $_GET['day'] ?? 'Monday';

// Add "Lab " prefix to lab values to match database format
$selected_lab_query = 'Lab ' . $selected_lab;

// Fetch schedules based on filters
$stmt = $conn->prepare("SELECT 
                      SCHED_ID as id, 
                      DAY as day, 
                      LABORATORY as laboratory, 
                      TIME_START as time_start, 
                      TIME_END as time_end, 
                      SUBJECT as subject, 
                      PROFESSOR as professor, 
                      CREATED_AT as created_at 
                      FROM lab_schedule 
                      WHERE LABORATORY = ? AND DAY = ? 
                      ORDER BY TIME_START");
$stmt->bind_param("ss", $selected_lab_query, $selected_day);
$stmt->execute();
$result = $stmt->get_result();

$admin_username = $_SESSION['username'] ?? 'Admin User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratory Schedule Management</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
    
    <!-- Sneat Template Core CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/css/demo.css" />
    
    <!-- Sneat Vendors CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap" rel="stylesheet" />
    
    <!-- Custom CSS -->
    <style>
        .lab-btn {
            border-radius: 100px;
            font-weight: 600;
            min-width: 60px;
            transition: all 0.3s;
        }
        
        .lab-btn.active {
            background-color: #696cff;
            color: white !important;
            box-shadow: 0 4px 8px rgba(105, 108, 255, 0.4);
        }
        
        .day-btn {
            border: 1px solid #dee2e6;
            background-color: white;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .day-btn.active {
            background-color: #696cff;
            border-color: #696cff;
            color: white !important;
        }
        
        .schedule-row:hover {
            background-color: #f0f0ff;
        }
        
        .location-badge {
            background-color: #e7f3ff;
            color: #1a73e8;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
        }
        
        .day-badge {
            background-color: #e7ffe7;
            color: #28a745;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
        }
        
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #6c757d;
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

          <!-- Schedule -->
          <li class="menu-item active">
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
                <input
                  type="text"
                  class="form-control border-0 shadow-none"
                  id="navbarSearch"
                  placeholder="Search..."
                  aria-label="Search..."
                />
              </div>
            </div>
            <!-- /Search -->

            <ul class="navbar-nav flex-row align-items-center ms-auto">
              <!-- Admin dropdown -->
              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                  <div class="avatar avatar-online">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_username); ?>&background=696cff&color=fff" alt class="w-px-40 h-auto rounded-circle" />
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="#">
                      <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                          <div class="avatar avatar-online">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_username); ?>&background=696cff&color=fff" alt class="w-px-40 h-auto rounded-circle" />
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
              <!--/ Admin dropdown -->
            </ul>
          </div>
        </nav>
        <!-- / Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
              <i class="bi bi-calendar3 me-2"></i> Laboratory Schedule Management
            </h4>

            <!-- Alert Messages -->
            <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <?php echo $success_message; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <?php echo $error_message; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <div class="row">
              <!-- Filter Options -->
              <div class="col-xl-4 col-md-12 mb-4">
                <div class="card h-100">
                  <div class="card-body">
                    <h5 class="card-title">Filter Options</h5>
                    
                    <div class="mb-4">
                      <label class="form-label">Select Laboratory</label>
                      <div class="d-flex flex-wrap gap-2">
                        <?php
                        // Update to include 530 and 542
                        $labs = ['517', '524', '526', '528', '530', '542'];
                        foreach ($labs as $lab) {
                            $active = ($lab == $selected_lab) ? 'active' : '';
                            echo '<a href="?lab='.$lab.'&day='.$selected_day.'" class="btn btn-outline-primary lab-btn '.$active.'">'.$lab.'</a>';
                        }
                        ?>
                      </div>
                    </div>
                    
                    <div>
                      <label class="form-label">Select Day</label>
                      <div class="row g-2">
                        <?php
                        // Update to include Friday and Saturday
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        foreach ($days as $day) {
                            $active = ($day == $selected_day) ? 'active' : '';
                            $shortDay = substr($day, 0, 3);
                            echo '
                            <div class="col-4">
                                <a href="?lab='.$selected_lab.'&day='.$day.'" class="btn btn-outline-primary day-btn '.$active.' w-100">'.$shortDay.'</a>
                            </div>';
                        }
                        ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Schedule Table -->
              <div class="col-xl-8 col-md-12 mb-4">
                <div class="card h-100">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                      <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="location-badge"><i class="bi bi-geo-alt me-2"></i>Laboratory <?php echo $selected_lab; ?></span>
                        <span class="day-badge"><i class="bi bi-calendar3 me-2"></i><?php echo $selected_day; ?></span>
                      </div>
                      <h5 class="mb-0">Class Schedule</h5>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                      <i class="bi bi-plus-circle me-1"></i> Add Schedule
                    </button>
                  </div>
                  <div class="card-body p-0">
                    <div class="table-responsive text-nowrap">
                      <table class="table table-hover">
                        <thead>
                          <tr>
                            <th>Time Slot</th>
                            <th>Subject</th>
                            <th>Professor</th>
                            <th class="text-center">Actions</th>
                          </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                          <?php 
                          if ($result->num_rows > 0) {
                              while ($row = $result->fetch_assoc()) {
                                  echo '<tr>';
                                  echo '<td><i class="bi bi-clock me-2"></i>' . date('h:i A', strtotime($row['time_start'])) . ' - ' . date('h:i A', strtotime($row['time_end'])) . '</td>';
                                  echo '<td>' . htmlspecialchars($row['subject']) . '</td>';
                                  echo '<td>' . htmlspecialchars($row['professor']) . '</td>';
                                  echo '<td class="text-center">';
                                  echo '<div class="dropdown">';
                                  echo '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>';
                                  echo '<div class="dropdown-menu">';
                                  echo '<a class="dropdown-item" href="javascript:void(0);" onclick="editSchedule('.$row['id'].')"><i class="bi bi-pencil-square me-1"></i> Edit</a>';
                                  echo '<a class="dropdown-item text-danger" href="javascript:void(0);" onclick="confirmDelete('.$row['id'].')"><i class="bi bi-trash me-1"></i> Delete</a>';
                                  echo '</div>';
                                  echo '</div>';
                                  echo '</td>';
                                  echo '</tr>';
                                  
                                  // Hidden row data for editing
                                  echo '<div id="scheduleData' . $row['id'] . '" style="display: none;">';
                                  echo json_encode($row);
                                  echo '</div>';
                              }
                          } else {
                              echo '<tr><td colspan="4" class="empty-state">No schedules found for this day and laboratory</td></tr>';
                          }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
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
                Sit-In System - Laboratory Management
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

  <!-- Add Schedule Modal -->
  <div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addScheduleModalLabel">Add New Schedule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="" method="POST" id="scheduleForm">
            <input type="hidden" name="action" value="add_schedule">
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="laboratory" class="form-label">Laboratory</label>
                <select class="form-select" id="laboratory" name="laboratory" required>
                  <?php
                  // Format lab options with "Lab " prefix to match database requirements
                  foreach ($labs as $lab) {
                    $labValue = 'Lab ' . $lab;
                    $selected = ($labValue == $selected_lab_query) ? 'selected' : '';
                    echo '<option value="'.$labValue.'" '.$selected.'>'.$labValue.'</option>';
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-6">
                <label for="day" class="form-label">Day</label>
                <select class="form-select" id="day" name="day" required>
                  <?php
                  foreach ($days as $day) {
                    $selected = ($day == $selected_day) ? 'selected' : '';
                    echo '<option value="'.$day.'" '.$selected.'>'.$day.'</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="time_start" class="form-label">Start Time</label>
                <input type="time" class="form-control" id="time_start" name="time_start" required>
              </div>
              <div class="col-md-6">
                <label for="time_end" class="form-label">End Time</label>
                <input type="time" class="form-control" id="time_end" name="time_end" required>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="subject" class="form-label">Subject</label>
              <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            
            <div class="mb-3">
              <label for="professor" class="form-label">Professor</label>
              <input type="text" class="form-control" id="professor" name="professor" required>
            </div>
            
            <div class="text-end">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save Schedule</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Schedule Modal -->
  <div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editScheduleModalLabel">Edit Schedule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="" method="POST" id="editScheduleForm">
            <input type="hidden" name="action" value="update_schedule">
            <input type="hidden" name="schedule_id" id="edit_schedule_id">
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="edit_laboratory" class="form-label">Laboratory</label>
                <select class="form-select" id="edit_laboratory" name="laboratory" required>
                  <?php
                  foreach ($labs as $lab) {
                    $labValue = 'Lab ' . $lab;
                    echo '<option value="'.$labValue.'">'.$labValue.'</option>';
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-6">
                <label for="edit_day" class="form-label">Day</label>
                <select class="form-select" id="edit_day" name="day" required>
                  <?php
                  foreach ($days as $day) {
                    echo '<option value="'.$day.'">'.$day.'</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="edit_time_start" class="form-label">Start Time</label>
                <input type="time" class="form-control" id="edit_time_start" name="time_start" required>
              </div>
              <div class="col-md-6">
                <label for="edit_time_end" class="form-label">End Time</label>
                <input type="time" class="form-control" id="edit_time_end" name="time_end" required>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="edit_subject" class="form-label">Subject</label>
              <input type="text" class="form-control" id="edit_subject" name="subject" required>
            </div>
            
            <div class="mb-3">
              <label for="edit_professor" class="form-label">Professor</label>
              <input type="text" class="form-control" id="edit_professor" name="professor" required>
            </div>
            
            <div class="text-end">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Update Schedule</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteScheduleModal" tabindex="-1" aria-labelledby="deleteScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-white" id="deleteScheduleModalLabel">Confirm Delete</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="text-center mb-4">
            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
            <h4 class="mt-3">Are you sure you want to delete this schedule?</h4>
            <p class="text-muted">This action cannot be undone.</p>
          </div>
          <form action="" method="POST" id="deleteScheduleForm">
            <input type="hidden" name="action" value="delete_schedule">
            <input type="hidden" name="schedule_id" id="delete_schedule_id">
            <div class="text-end">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-danger">Delete Schedule</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Logout Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
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
    // Edit schedule function
    function editSchedule(scheduleId) {
      // Get schedule data from hidden div
      const scheduleDataElement = document.getElementById('scheduleData' + scheduleId);
      const scheduleData = JSON.parse(scheduleDataElement.textContent);
      
      // Fill form with schedule data
      document.getElementById('edit_schedule_id').value = scheduleData.id;
      document.getElementById('edit_laboratory').value = scheduleData.laboratory;
      document.getElementById('edit_day').value = scheduleData.day;
      document.getElementById('edit_time_start').value = scheduleData.time_start;
      document.getElementById('edit_time_end').value = scheduleData.time_end;
      document.getElementById('edit_subject').value = scheduleData.subject;
      document.getElementById('edit_professor').value = scheduleData.professor;
      
      // Show edit modal
      const editModal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
      editModal.show();
    }
    
    // Delete schedule confirmation
    function confirmDelete(scheduleId) {
      document.getElementById('delete_schedule_id').value = scheduleId;
      
      // Show delete confirmation modal
      const deleteModal = new bootstrap.Modal(document.getElementById('deleteScheduleModal'));
      deleteModal.show();
    }
    
    // Navbar search functionality
    document.getElementById('navbarSearch').addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const rows = document.querySelectorAll('tbody tr');
      
      rows.forEach(row => {
        if (row.textContent.toLowerCase().includes(searchTerm) || searchTerm === '') {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
    
    // Logout modal
    document.getElementById('logoutBtn').addEventListener('click', function() {
      const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
      logoutModal.show();
    });
  </script>
</body>
</html>