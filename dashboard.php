<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT profile_picture, first_name, middle_name, last_name, course, year_level, email, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_picture, $first_name, $middle_name, $last_name, $course, $year_level, $email, $address);
$stmt->fetch();
$stmt->close();

// Replace the existing session count fetch code with this:
$stmt = $conn->prepare("SELECT COALESCE(
    (SELECT session_count FROM sit_in WHERE idno = (SELECT id_no FROM users WHERE id = ?) ORDER BY id DESC LIMIT 1), 
    30
) as session_count");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($session_count);
$stmt->fetch();
$stmt->close();

$user_profile = (!empty($profile_picture) && file_exists($profile_picture)) ? $profile_picture : "profile.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            themes: ["light"],
            plugins: [require("daisyui")],
        }
    </script>
    <style>
        @keyframes scan {
            0% {
                transform: translateY(-100%);
            }
            100% {
                transform: translateY(100%);
            }
        }
        .animate-scan {
            animation: scan 2s linear infinite;
        }
    </style>
</head>
<body>
    <!-- Replace the existing animated background with this -->
    <div class="fixed inset-0 -z-10 overflow-hidden bg-gradient-to-br from-gray-900 to-gray-800">
        <?php 
        // Enhanced array of different device SVG paths
        $devices = [
            // Desktop PC with Monitor
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17h6m-3-3v3M4 14h16M6 6h12a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z"/>',
            
            // Modern Laptop
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 18h16M4 14h16M6 6h12a2 2 0 012 2v8H4V8a2 2 0 012-2z M7 19h10a1 1 0 001-1v0a1 1 0 00-1-1H7a1 1 0 00-1 1v0a1 1 0 001 1z"/>',
            
            // Gaming Monitor
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z M12 16v4 M8 20h8"/>',
            
            // All-in-One PC
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z M9 11l2 2 4-4"/>',
        ];
        
        for($i = 0; $i < 30; $i++): 
            $randomDevice = $devices[array_rand($devices)];
        ?>
            <div class="tech-device absolute">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="<?php echo rand(0,1) ? 'h-8 w-8' : 'h-10 w-10'; ?> text-blue-400/30" 
                     fill="none" 
                     viewBox="0 0 24 24" 
                     stroke="currentColor">
                    <?php echo $randomDevice; ?>
                </svg>
            </div>
        <?php endfor; ?>
    </div>

    <!-- Update the style section in your head tag -->
    <style>
        .tech-device {
            animation: float-device 20s linear infinite;
            left: -50px;
            opacity: 0;
        }
        
        @keyframes float-device {
            0% {
                transform: translateX(-100%) translateY(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateX(100vw) translateY(100px) rotate(360deg);
                opacity: 0;
            }
        }

        /* Distribute devices at different positions and with different behaviors */
        <?php for($i = 0; $i < 30; $i++): ?>
        .tech-device:nth-child(<?php echo $i + 1; ?>) {
            top: <?php echo rand(5, 95); ?>%;
            animation-delay: <?php echo $i * 0.5; ?>s;
            animation-duration: <?php echo rand(15, 30); ?>s;
            filter: blur(<?php echo rand(0, 1) ? '0px' : '1px'; ?>);
        }
        <?php endfor; ?>

        /* Add a subtle glow effect to devices */
        .tech-device svg {
            filter: drop-shadow(0 0 2px rgba(59, 130, 246, 0.5));
        }

        /* Keep the blur effect for the container */
        .container {
            position: relative;
            z-index: 10;
            backdrop-filter: blur(8px);
        }

        /* Add hover effect for devices */
        .tech-device:hover {
            transform: scale(1.2);
            transition: transform 0.3s ease;
        }
    </style>

    <!-- Navbar -->
    <div class="navbar bg-[#2c343c] shadow-lg">
        <div class="navbar-start">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-xl font-bold text-white ml-2">Dashboard</span>
            </div>
        </div>
        
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 gap-2">
                <li>
                    <a href="dashboard.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <a href="#" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Notifications
                    </a>
                </li>
                <li>
                    <a href="editprofile.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Profile
                    </a>
                </li>
                <li>
                    <a href="history.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        History
                    </a>
                </li>
                <li>
                    <a href="#" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reservation
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="navbar-end">
            <a href="logout.php" class="btn btn-error btn-outline gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </a>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Student Information Card -->
        <div class="card bg-base-100 shadow-xl h-[400px]">
            <div class="card-header bg-[#2c343c] px-4 py-2">
                <h2 class="card-title text-white text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Student Information
                </h2>
            </div>
            <div class="card-body p-2">
                <div class="flex justify-center mb-2">
                    <div class="avatar">
                        <div class="w-16 h-16 rounded-full ring ring-[#2c343c] ring-offset-1">
                            <img src="<?php echo htmlspecialchars($user_profile); ?>" alt="Profile"/>
                        </div>
                    </div>
                </div>
                <div class="stats stats-vertical shadow w-full text-xs">
                    <div class="stat py-0.5">
                        <div class="stat-title text-[10px]">Name</div>
                        <div class="stat-value text-sm"><?php echo htmlspecialchars("$first_name $middle_name $last_name"); ?></div>
                    </div>
                    <div class="stat py-0.5">
                        <div class="stat-title text-[10px]">Course</div>
                        <div class="stat-value text-sm"><?php echo htmlspecialchars($course); ?></div>
                    </div>
                    <div class="stat py-0.5">
                        <div class="stat-title text-[10px]">Year Level</div>
                        <div class="stat-value text-sm"><?php echo htmlspecialchars($year_level); ?></div>
                    </div>
                    <div class="stat py-0.5">
                        <div class="stat-title text-[10px]">Email</div>
                        <div class="stat-value text-sm"><?php echo htmlspecialchars($email); ?></div>
                    </div>
                    <div class="stat py-0.5">
                        <div class="stat-title text-[10px]">Address</div>
                        <div class="stat-value text-sm"><?php echo htmlspecialchars($address); ?></div>
                    </div>
                </div>
                <div class="stat bg-base-200 rounded-box mt-1 py-1">
                    <div class="stat-title text-[10px] flex justify-between items-center">
                        <span>Available Sessions</span>
                        <span class="text-green-600 font-bold"><?php echo htmlspecialchars($session_count); ?>/30</span>
                    </div>
                    <div class="relative w-full h-4 bg-gray-700 rounded-full mt-1 overflow-hidden border-2 border-gray-800">
                        <!-- Progress bar with glowing effect -->
                        <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-green-600 via-green-400 to-green-600 rounded-full transition-all duration-300 animate-pulse"
                             style="width: <?php echo ($session_count/30) * 100; ?>%">
                        </div>
                        <!-- Overlay effect -->
                        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-transparent via-transparent to-black opacity-20"></div>
                        <!-- Scanline effect -->
                        <div class="absolute top-0 left-0 w-full h-full animate-scan">
                            <div class="h-[1px] w-full bg-green-200/30"></div>
                        </div>
                        <!-- Additional glow effect -->
                        <div class="absolute top-0 left-0 w-full h-full">
                            <div class="absolute inset-0 bg-gradient-to-t from-green-500/0 via-green-500/10 to-green-500/0"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Announcements Card -->
        <div class="card bg-base-100 shadow-xl h-[400px]">
            <div class="card-header bg-[#2c343c] px-4 py-2">
                <h2 class="card-title text-white text-base">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Announcements
                </h2>
            </div>
            <div class="card-body p-3 overflow-y-auto">
                <div id="announcementContainer" class="space-y-2">
                    <!-- Announcements will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Rules Card -->
        <div class="card bg-base-100 shadow-xl h-[400px]">
            <div class="card-header bg-[#2c343c] px-4 py-2">
                <h2 class="card-title text-white text-base">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Laboratory Rules
                </h2>
            </div>
            <div class="card-body p-3 overflow-y-auto">
                <div class="space-y-2">
                    <div class="collapse collapse-arrow bg-base-200">
                        <input type="radio" name="rules-accordion" checked="checked" /> 
                        <div class="collapse-title font-medium text-sm py-2">
                            <div class="text-center space-y-0.5">
                                <h3 class="font-bold text-sm">University of Cebu</h3>
                                <h4 class="font-semibold text-xs">COLLEGE OF INFORMATION & COMPUTER STUDIES</h4>
                                <h5 class="font-medium text-xs">LABORATORY RULES AND REGULATIONS</h5>
                            </div>
                        </div>
                        <div class="collapse-content text-xs space-y-2">
                            <p class="italic text-gray-600 mb-2">
                                To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:
                            </p>
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Maintain silence, proper decorum, and discipline</li>
                                <li>No computer games or card games allowed</li>
                                <li>Internet surfing requires instructor permission</li>
                                <li>No access to non-course related websites</li>
                                <li>No deleting files or changing computer setup</li>
                                <li>15-minute time limit per session</li>
                                <li>Proper laboratory etiquette:
                                    <ul class="list-disc pl-4 pt-1 space-y-0.5">
                                        <li>Wait for instructor before entering</li>
                                        <li>Deposit bags at counter</li>
                                        <li>Follow seating arrangements</li>
                                        <li>Close all programs after use</li>
                                        <li>Return chairs to proper place</li>
                                    </ul>
                                </li>
                                <li>No food, drinks, or vandalism</li>
                            </ol>
                        </div>
                    </div>

                    <div class="collapse collapse-arrow bg-base-200">
                        <input type="radio" name="rules-accordion" />
                        <div class="collapse-title font-medium text-sm py-2">
                            Additional Policies
                        </div>
                        <div class="collapse-content text-xs space-y-2">
                            <ul class="list-disc list-inside space-y-1">
                                <li>No public display of physical intimacy</li>
                                <li>No hostile or threatening behavior</li>
                                <li>Report technical issues immediately</li>
                                <li>Civil Security may be called for serious offenses</li>
                            </ul>
                        </div>
                    </div>

                    <div class="collapse collapse-arrow bg-base-200">
                        <input type="radio" name="rules-accordion" />
                        <div class="collapse-title font-medium text-sm py-2">
                            Sanctions
                        </div>
                        <div class="collapse-content">
                            <div class="alert alert-warning py-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="text-xs">
                                    <h3 class="font-bold">First Offense</h3>
                                    <div class="text-xs">Suspension from classes (via Guidance Center)</div>
                                </div>
                            </div>
                            <div class="alert alert-error py-2 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-xs">
                                    <h3 class="font-bold">Second Offense</h3>
                                    <div class="text-xs">Heavier sanctions via Guidance Center</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update the loadAnnouncements function in your script tag
        function loadAnnouncements() {
            fetch('get_announcements.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('announcementContainer');
                    container.innerHTML = ''; // Clear previous content
                    if (data.status === 'success') {
                        data.announcements.forEach(announcement => {
                            const announcementDiv = document.createElement('div');
                            announcementDiv.classList.add('bg-gray-50', 'rounded-lg', 'p-4', 'shadow-sm');
                            announcementDiv.innerHTML = `
                                <div class="alert shadow-sm py-2">
                                    <div class="w-full">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-bold text-sm">${announcement.admin_name}</span>
                                            <span class="text-xs opacity-70">${announcement.date}</span>
                                        </div>
                                        <div class="text-sm">
                                            ${announcement.message}
                                        </div>
                                    </div>
                                </div>`;
                            container.appendChild(announcementDiv);
                        });
                    } else {
                        container.innerHTML = '<p class="text-gray-500 text-center py-4">No announcements available.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading announcements:', error);
                    document.getElementById('announcementContainer').innerHTML = 
                        '<p class="text-red-500 text-center py-4">Failed to load announcements.</p>';
                });
        }

        // Initial load and refresh every 30 seconds
        loadAnnouncements();
        setInterval(loadAnnouncements, 30000);
    </script>
</body>
</html>
