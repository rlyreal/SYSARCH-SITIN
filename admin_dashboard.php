<?php
session_start();
include 'db.php';

// Redirect if not logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Remove the admin details fetch since we only have username now
// Just use the username from session
$admin_username = $_SESSION['username'];

// Fetch statistics
$students = $conn->query("SELECT COUNT(DISTINCT id_no) AS total FROM users WHERE id_no IS NOT NULL")->fetch_assoc()['total'];
$current_sit_in = $conn->query("SELECT COUNT(*) AS total FROM sit_in WHERE time_out IS NULL")->fetch_assoc()['total'];
$total_sit_in = $conn->query("SELECT COUNT(id) AS total FROM sit_in")->fetch_assoc()['total'];

// Remove any existing year level queries and add this one
$yearLevelQuery = "SELECT year_level, COUNT(*) as count 
                   FROM users 
                   WHERE year_level IS NOT NULL 
                   GROUP BY year_level 
                   ORDER BY FIELD(year_level, '1st Year', '2nd Year', '3rd Year', '4th Year')";
$yearLevelResult = $conn->query($yearLevelQuery);

$yearLevels = [];
$yearLevelCounts = [];
while($row = $yearLevelResult->fetch_assoc()) {
    $yearLevels[] = $row['year_level'];
    $yearLevelCounts[] = $row['count'];
}

// Add after your existing statistics queries
$programmingQuery = "SELECT purpose, COUNT(*) as count 
                    FROM sit_in 
                    GROUP BY purpose 
                    ORDER BY count DESC";
$programmingResult = $conn->query($programmingQuery);

$languages = [];
$languageCounts = [];
while($row = $programmingResult->fetch_assoc()) {
    $languages[] = $row['purpose'];
    $languageCounts[] = $row['count'];
}

// Add after your existing queries at the top of the file
$purposeQuery = "SELECT purpose, COUNT(*) as count 
                FROM sit_in 
                GROUP BY purpose 
                ORDER BY count DESC";
$purposeResult = $conn->query($purposeQuery);

$purposes = [];
$purposeCounts = [];
while($row = $purposeResult->fetch_assoc()) {
    $purposes[] = $row['purpose'];
    $purposeCounts[] = $row['count'];
}

// Function to fetch only reservation notifications 
function getReservationNotifications($conn) {
    $query = "SELECT 
                n.NOTIF_ID, 
                n.USER_ID,
                n.RESERVATION_ID, 
                n.MESSAGE, 
                n.IS_READ, 
                n.CREATED_AT,
                n.ADMIN_NOTIFICATION,
                u.first_name,
                u.last_name,
                r.laboratory,
                r.purpose,
                r.date,
                r.status
              FROM 
                notifications n
              JOIN 
                users u ON n.USER_ID = u.id
              JOIN 
                reservations r ON n.RESERVATION_ID = r.id
              WHERE 
                n.ADMIN_NOTIFICATION = 1 
                AND n.RESERVATION_ID IS NOT NULL
              ORDER BY 
                n.CREATED_AT DESC 
              LIMIT 10";
                
    $result = $conn->query($query);
    
    $notifications = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
    }
    
    $unread_count = 0;
    foreach ($notifications as $notification) {
        if ($notification['IS_READ'] == 0) {
            $unread_count++;
        }
    }
    
    return [
        'notifications' => $notifications,
        'unread_count' => $unread_count
    ];
}

// Get reservation notifications
$reservation_notifications = getReservationNotifications($conn);

// Update the leaderboard query to get top 5 based on points
$leaderboardQuery = "
    SELECT 
        u.id,
        u.first_name,
        u.last_name,
        u.course,
        COUNT(ph.id) as total_points
    FROM 
        users u
    LEFT JOIN 
        points_history ph ON u.id = ph.user_id
    WHERE 
        ph.id IS NOT NULL
    GROUP BY 
        u.id, u.first_name, u.last_name, u.course
    ORDER BY 
        total_points DESC, u.last_name ASC
    LIMIT 5
";

$leaderboardResult = $conn->query($leaderboardQuery);
$leaderboardData = [];
while($row = $leaderboardResult->fetch_assoc()) {
    $leaderboardData[] = $row;
}

// Fetch announcements for display on the page
$announcements_query = $conn->query("SELECT id, admin_name, message, date FROM announcements ORDER BY date DESC LIMIT 5");
$announcements = [];
while ($row = $announcements_query->fetch_assoc()) {
    // Format the date to be more readable
    $timestamp = strtotime($row['date']);
    $formatted_date = date('M j, Y g:i A', $timestamp);
    $row['date'] = $formatted_date;
    $announcements[] = $row;
}

// Handle announcement submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $announcement = trim($_POST['announcement']);
    if (!empty($announcement)) {
        // Use username instead of first_name and last_name
        $admin_name = $admin_username; // Using the username from session
        $query = $conn->prepare("INSERT INTO announcements (admin_name, message, date) VALUES (?, ?, NOW())");
        $query->bind_param("ss", $admin_name, $announcement);
        if ($query->execute()) {
            $announcement_id = $conn->insert_id;
            
            // Create notifications for all users - don't filter by role since that column doesn't exist
            $user_query = $conn->prepare("SELECT id FROM users");
            $user_query->execute();
            $user_result = $user_query->get_result();
            
            while ($user = $user_result->fetch_assoc()) {
                $notif_message = "New announcement: " . substr($announcement, 0, 50) . (strlen($announcement) > 50 ? "..." : "");
                
                $notify_user = $conn->prepare("INSERT INTO notifications (USER_ID, ANNOUNCEMENT_ID, MESSAGE, IS_READ) VALUES (?, ?, ?, 0)");
                $notify_user->bind_param("iis", $user['id'], $announcement_id, $notif_message);
                $notify_user->execute();
            }
            
            // Success message
            $_SESSION['success_msg'] = "Announcement posted successfully!";
            echo "<script>alert('Announcement posted successfully!');</script>";
        } else {
            echo "<script>alert('Error posting announcement.');</script>";
        }
        $query->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- Sneat Template Core CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/css/demo.css" />
    
    <!-- Sneat Vendors CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Charts -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap" rel="stylesheet" />
    
    <!-- Helpers -->
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/js/helpers.js"></script>
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/js/config.js"></script>

    <style>
      /* Add custom scrollbar styling to notifications */
      .dropdown-notifications-list {
        max-height: 350px;
        overflow-y: auto;
      }
      
      /* Custom scrollbar styling */
      .scrollable-container::-webkit-scrollbar {
        width: 6px;
      }
      
      .scrollable-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
      }
      
      .scrollable-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
      }
      
      .scrollable-container::-webkit-scrollbar-thumb:hover {
        background: #696cff;
      }

      /* Add to your existing style block in admin_dashboard.php */
      .badge-notifications {
        position: absolute;
        top: 0;
        right: 0;
        transform: translate(50%, -50%);
        z-index: 5;
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
          <a href="admin_dashboard.php" class="app-brand-link">
            <span class="app-brand-logo demo">
              <svg width="25" viewBox="0 0 25 42" xmlns="http://www.w3.org/2000/svg">
                <!-- Logo SVG content here or use your own logo -->
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
          <li class="menu-item active">
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
                <input
                  type="text"
                  class="form-control border-0 shadow-none"
                  placeholder="Search..."
                  aria-label="Search..."
                />
              </div>
            </div>
            <!-- /Search -->

            <ul class="navbar-nav flex-row align-items-center ms-auto">
              <!-- Notification Dropdown -->
              <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
                <a class="nav-link dropdown-toggle hide-arrow position-relative" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                  <i class="bi bi-bell bi-middle"></i>
                  <span class="badge bg-danger rounded-pill badge-notifications" id="notification-badge" style="display:none;"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0">
                  <li class="dropdown-menu-header border-bottom">
                    <div class="dropdown-header d-flex align-items-center py-3">
                      <h5 class="text-body mb-0 me-auto">Notifications</h5>
                      <a href="javascript:void(0)" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip" title="Mark all as read" onclick="markAllNotificationsAsRead()">
                        <i class="bi bi-check2-all"></i>
                      </a>
                    </div>
                  </li>
                  <li class="dropdown-notifications-list scrollable-container">
                    <ul class="list-group list-group-flush" id="notification-list">
                      <li class="list-group-item list-group-item-action dropdown-notifications-item text-center p-3">
                        <p class="mb-0">Loading notifications...</p>
                      </li>
                    </ul>
                  </li>
                  <li class="dropdown-menu-footer border-top">
                    <a href="admin_reservation.php" class="dropdown-item d-flex justify-content-center p-3">
                      View all reservations
                    </a>
                  </li>
                </ul>
              </li>
              <!-- /Notification Dropdown -->

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
            <!-- Student Leaderboard -->
            <div class="card mb-4">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Student Leaderboard</h5>
                <small class="text-muted float-end">Top students based on lifetime points</small>
              </div>
              <div class="card-body">
                <div class="row g-4">
                  <?php 
                  $medals = [
                      1 => [
                          'bg' => 'bg-label-warning',
                          'icon' => 'bi-trophy-fill text-warning',
                          'title' => 'Gold Medal'
                      ],
                      2 => [
                          'bg' => 'bg-label-secondary',
                          'icon' => 'bi-trophy text-secondary',
                          'title' => 'Silver Medal'
                      ],
                      3 => [
                          'bg' => 'bg-label-danger',
                          'icon' => 'bi-trophy text-danger',
                          'title' => 'Bronze Medal'
                      ],
                      4 => [
                          'bg' => 'bg-label-info',
                          'icon' => 'bi-award text-info',
                          'title' => '4th Place'
                      ],
                      5 => [
                          'bg' => 'bg-label-primary',
                          'icon' => 'bi-award text-primary',
                          'title' => '5th Place'
                      ]
                  ];

                  for($i = 0; $i < 5 && $i < count($leaderboardData); $i++) {
                      $rank = $i + 1;
                      $student = $leaderboardData[$i];
                      $style = $medals[$rank];
                  ?>
                    <div class="col-md-<?php echo ($i < 3) ? '4' : '6'; ?>">
                      <div class="card h-100">
                        <div class="card-body text-center">
                          <div class="avatar avatar-md mx-auto mb-3">
                            <span class="avatar-initial rounded-circle <?php echo $style['bg']; ?>">
                              <i class="bi <?php echo $style['icon']; ?> fs-3"></i>
                            </span>
                          </div>
                          <h5 class="card-title"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h5>
                          <p class="card-text text-muted"><?php echo $student['course']; ?></p>
                          <div class="d-flex justify-content-center align-items-center mt-3">
                            <div class="badge bg-primary rounded-pill">
                              <?php echo $student['total_points']; ?> Points
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
            <!-- / Student Leaderboard -->

            <!-- Statistics and Announcements Row -->
            <div class="row g-4 mb-4">
              <!-- Statistics Column -->
              <div class="col-md-6">
                <!-- Statistics Cards -->
                <div class="row g-3 mb-4">
                  <div class="col-sm-4">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                          <div class="content-left">
                            <span class="fw-semibold d-block mb-1">Students</span>
                            <div class="d-flex align-items-end mt-2">
                              <h3 class="mb-0 me-2"><?= $students ?></h3>
                              <small class="text-success">(+Registered)</small>
                            </div>
                          </div>
                          <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                              <i class="bi bi-person-badge fs-4"></i>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                          <div class="content-left">
                            <span class="fw-semibold d-block mb-1">Currently In</span>
                            <div class="d-flex align-items-end mt-2">
                              <h3 class="mb-0 me-2"><?= $current_sit_in ?></h3>
                              <small class="text-success">(Active)</small>
                            </div>
                          </div>
                          <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                              <i class="bi bi-people-fill fs-4"></i>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                          <div class="content-left">
                            <span class="fw-semibold d-block mb-1">Total Visits</span>
                            <div class="d-flex align-items-end mt-2">
                              <h3 class="mb-0 me-2"><?= $total_sit_in ?></h3>
                              <small class="text-muted">(Sessions)</small>
                            </div>
                          </div>
                          <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                              <i class="bi bi-clipboard-check fs-4"></i>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Pie Chart Card -->
                <div class="card h-100">
                  <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Programming Language Distribution</h5>
                  </div>
                  <div class="card-body">
                    <div id="statsChart" style="min-height: 350px;"></div>
                  </div>
                </div>
              </div>
              <!-- / Statistics Column -->
              
              <!-- Announcements Column -->
              <div class="col-md-6">
                <div class="card h-100">
                  <div class="card-header">
                    <h5 class="card-title"><i class="bi bi-megaphone me-2"></i>Announcements</h5>
                  </div>
                  <div class="card-body">
                    <form method="POST" class="mb-4">
                      <div class="mb-3">
                        <textarea 
                          name="announcement" 
                          class="form-control"
                          rows="3"
                          placeholder="Write an announcement..." 
                          required
                        ></textarea>
                      </div>
                      <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                          <i class="bi bi-send me-1"></i> Post Announcement
                        </button>
                      </div>
                    </form>
                    <h6 class="mb-3 fw-semibold">Recent Announcements</h6>
                    <div id="announcementContainer" class="overflow-auto" style="max-height: 295px;">
                        <?php if (count($announcements) > 0): ?>
                            <?php foreach ($announcements as $announcement): ?>
                                <div class="mb-3 p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="badge bg-label-primary mb-1"><?php echo htmlspecialchars($announcement['admin_name']); ?></span>
                                            <small class="text-muted d-block"><?php echo $announcement['date']; ?></small>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" onclick="editAnnouncement(<?php echo $announcement['id']; ?>)">
                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                </a></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteAnnouncement(<?php echo $announcement['id']; ?>)">
                                                    <i class="bi bi-trash me-2"></i>Delete
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($announcement['message'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No announcements available.</p>
                        <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
              <!-- / Announcements Column -->
            </div>
            <!-- / Statistics and Announcements Row -->

            <!-- Year Level Chart -->
            <div class="card mb-4">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Student Year Level Distribution</h5>
              </div>
              <div class="card-body">
                <div class="d-flex justify-content-center">
                  <div style="height: 300px; width: 100%;">
                    <canvas id="yearLevelChart"></canvas>
                  </div>
                </div>
              </div>
            </div>
            <!-- / Year Level Chart -->
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

  <!-- Add Schedule Modal -->
  <div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addScheduleModalLabel">Add New Schedule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="scheduleForm">
                    <input type="hidden" name="action" value="add_schedule">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="laboratory" class="form-label">Laboratory</label>
                            <select class="form-select" id="laboratory" name="laboratory" required>
                                <?php
                                // Update options to match ENUM values in the database
                                $labs = ['Lab 517', 'Lab 524', 'Lab 526', 'Lab 528'];
                                foreach ($labs as $lab) {
                                    $selected = ($lab == 'Lab ' . $selected_lab) ? 'selected' : '';
                                    echo '<option value="'.$lab.'" '.$selected.'>'.$lab.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="day" class="form-label">Day</label>
                            <select class="form-select" id="day" name="day" required>
                                <?php
                                // Update options to match ENUM values in the database
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday'];
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editScheduleModalLabel">Edit Schedule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                    echo '<option value="'.$lab.'">'.$lab.'</option>';
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Schedule</button>
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
    // Stats Chart (ECharts)
    const statsChart = echarts.init(document.getElementById('statsChart'));
    
    const statsOption = {
      tooltip: {
        trigger: 'item',
        formatter: '{a} <br/>{b}: {c} ({d}%)'
      },
      legend: {
        orient: 'vertical',
        left: 'left',
        textStyle: {
          color: '#697a8d'
        }
      },
      series: [
        {
          name: 'Programming Languages',
          type: 'pie',
          radius: ['40%', '70%'],
          avoidLabelOverlap: false,
          itemStyle: {
            borderRadius: 10,
            borderColor: '#fff',
            borderWidth: 2
          },
          label: {
            show: false,
            position: 'center'
          },
          emphasis: {
            label: {
              show: true,
              fontSize: '16',
              fontWeight: 'bold'
            }
          },
          labelLine: {
            show: false
          },
          data: <?php
              $chartData = array_map(function($name, $value) {
                  return ['value' => $value, 'name' => $name];
              }, $purposes, $purposeCounts);
              echo json_encode($chartData);
          ?>
        }
      ]
    };
    
    statsChart.setOption(statsOption);
    
    // Year Level Chart (Chart.js)
    const yearLevelCtx = document.getElementById('yearLevelChart').getContext('2d');
    const yearLevelChart = new Chart(yearLevelCtx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($yearLevels); ?>,
        datasets: [{
          label: 'Number of Students',
          data: <?php echo json_encode($yearLevelCounts); ?>,
          backgroundColor: [
            'rgba(105, 108, 255, 0.8)',
            'rgba(3, 195, 236, 0.8)',
            'rgba(255, 171, 0, 0.8)',
            'rgba(255, 62, 29, 0.8)'
          ],
          borderColor: [
            'rgb(105, 108, 255)',
            'rgb(3, 195, 236)',
            'rgb(255, 171, 0)',
            'rgb(255, 62, 29)'
          ],
          borderWidth: 1,
          borderRadius: 8,
          barPercentage: 0.6,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(105, 122, 141, 0.1)'
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
    
    // Format relative time
    function getTimeAgo(date) {
      const now = new Date();
      const diffMs = now - date;
      const diffSecs = Math.floor(diffMs / 1000);
      const diffMins = Math.floor(diffSecs / 60);
      const diffHours = Math.floor(diffMins / 60);
      const diffDays = Math.floor(diffHours / 24);
      
      if (diffSecs < 60) {
        return 'just now';
      } else if (diffMins < 60) {
        return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
      } else if (diffHours < 24) {
        return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
      } else if (diffDays < 7) {
        return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
      } else {
        // Format date for older notifications
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });
      }
    }
    
    // Handle announcements
    function loadAnnouncements() {
      fetch('get_announcements.php')
        .then(response => response.json())
        .then(data => {
          const container = document.getElementById('announcementContainer');
          container.innerHTML = ''; // Clear previous content
          if (data.status === 'success') {
            data.announcements.forEach(announcement => {
              const announcementDiv = document.createElement('div');
              announcementDiv.classList.add('mb-3', 'p-3', 'border', 'rounded');
              announcementDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div>
                    <span class="badge bg-label-primary mb-1">${announcement.admin_name}</span>
                    <small class="text-muted d-block">${announcement.date}</small>
                  </div>
                  <div class="dropdown">
                    <button class="btn p-0" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#" onclick="editAnnouncement(${announcement.id})">
                        <i class="bi bi-pencil me-2"></i>Edit
                      </a></li>
                      <li><a class="dropdown-item text-danger" href="#" onclick="deleteAnnouncement(${announcement.id})">
                        <i class="bi bi-trash me-2"></i>Delete
                      </a></li>
                    </ul>
                  </div>
                </div>
                <p class="mb-0">${announcement.message}</p>
              `;
              container.appendChild(announcementDiv);
            });
          } else {
            container.innerHTML = '<p class="text-muted">No announcements available.</p>';
          }
        })
        .catch(error => console.error('Error loading announcements:', error));
    }
    
    function editAnnouncement(id) {
      if (confirm('Do you want to edit this announcement?')) {
        fetch('get_announcements.php')
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              const announcement = data.announcements.find(a => a.id == id);
              if (announcement) {
                const newMessage = prompt('Edit announcement:', announcement.message);
                if (newMessage !== null && newMessage.trim() !== '') {
                  const formData = new FormData();
                  formData.append('id', id);
                  formData.append('message', newMessage.trim());

                  fetch('update_announcement.php', {
                    method: 'POST',
                    body: formData
                  })
                  .then(response => response.json())
                  .then(data => {
                    if (data.status === 'success') {
                      alert('Announcement updated successfully!');
                      loadAnnouncements();
                    } else {
                      alert('Failed to update announcement: ' + data.message);
                    }
                  })
                  .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the announcement');
                  });
                }
              } else {
                alert('Announcement not found');
              }
            } else {
              alert('Failed to fetch announcements');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching the announcements');
          });
      }
    }
    
    function deleteAnnouncement(id) {
      if (confirm('Are you sure you want to delete this announcement?')) {
        fetch('delete_announcement.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `id=${id}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            loadAnnouncements();
          } else {
            alert('Failed to delete announcement');
          }
        });
      }
    }
    
    // Replace your existing loadReservationNotifications function with this version
    function loadReservationNotifications() {
      console.log('Loading notifications...');
      
      fetch('get_reservation_notifications.php')
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          console.log('Notification data:', data); // Debug output
          
          const notificationList = document.getElementById('notification-list');
          const badge = document.getElementById('notification-badge');
          
          // Clear existing notifications
          notificationList.innerHTML = '';
          
          // Update badge with the direct count from the backend
          const unreadCount = parseInt(data.unread_count) || 0;
          console.log('Unread count:', unreadCount);
          
          // Set badge display and text
          if (unreadCount > 0) {
            badge.textContent = unreadCount;
            badge.style.display = 'inline-block';
            badge.style.position = 'absolute';
            badge.style.top = '0';
            badge.style.right = '0';
            badge.style.transform = 'translate(50%, -50%)';
            console.log('Badge should be visible with count:', unreadCount);
          } else {
            badge.style.display = 'none';
            console.log('Badge should be hidden - no unread notifications');
          }
          
          // Display notifications
          if (data.notifications && data.notifications.length > 0) {
            data.notifications.forEach(notification => {
              const date = new Date(notification.CREATED_AT);
              const timeAgo = getTimeAgo(date);
              
              const item = document.createElement('li');
              item.className = 'list-group-item list-group-item-action dropdown-notifications-item';
              if (notification.IS_READ == 0) {
                item.classList.add('bg-light');
              }
              
              item.innerHTML = `
                <div class="d-flex">
                  <div class="flex-shrink-0 me-3">
                    <div class="avatar">
                      <span class="avatar-initial rounded-circle bg-label-primary">
                        <i class="bi bi-calendar-check"></i>
                      </span>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-1">${notification.first_name} ${notification.last_name}</h6>
                    <p class="mb-0">${notification.MESSAGE}</p>
                    <small class="text-muted">${timeAgo}</small>
                  </div>
                  <div class="flex-shrink-0 dropdown-notifications-actions">
                    <a href="javascript:void(0)" class="dropdown-notifications-read" onclick="markSingleNotificationAsRead(event, ${notification.NOTIF_ID})">
                      <span class="badge ${notification.IS_READ == 0 ? 'badge-dot bg-danger' : 'bg-label-secondary'}"></span>
                    </a>
                  </div>
                </div>
              `;
              
              notificationList.appendChild(item);
            });
          } else {
            notificationList.innerHTML = '<li class="list-group-item list-group-item-action dropdown-notifications-item text-center p-3"><p class="mb-0">No notifications</p></li>';
          }
        })
        .catch(error => {
          console.error('Error loading notifications:', error);
          document.getElementById('notification-list').innerHTML = 
            '<li class="list-group-item list-group-item-action dropdown-notifications-item text-center text-danger p-3">Failed to load notifications</li>';
        });
    }

    function markSingleNotificationAsRead(event, notificationId) {
      // Prevent the click from propagating to the notification item
      event.stopPropagation();
      
      fetch('mark_single_notification_read.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ notification_id: notificationId })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Reload notifications to update the UI
          loadReservationNotifications();
        }
      })
      .catch(error => console.error('Error marking notification as read:', error));
    }

    function markAllNotificationsAsRead() {
      console.log('Marking all notifications as read...');
      
      fetch('mark_all_reservation_notifications_read.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        }
      })
      .then(response => response.json())
      .then(data => {
        console.log('Mark all read response:', data);
        
        if (data.success) {
          // Reload notifications to update the UI
          loadReservationNotifications();
        } else {
          console.error('Failed to mark all as read:', data.message);
        }
      })
      .catch(error => {
        console.error('Error marking all notifications as read:', error);
      });
    }

    // Make sure the logout button works
    document.addEventListener('DOMContentLoaded', function() {
      // Load notifications and other existing code
      loadReservationNotifications();
      
      // Fix logout button functionality
      const logoutBtn = document.getElementById('logoutBtn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
          const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
          logoutModal.show();
        });
      }
      
      // Refresh notifications every 60 seconds
      setInterval(loadReservationNotifications, 60000);
    });
  </script>
</body>
</html>
