<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sitin_system");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add this PHP code before the <script> tag to get the data
// Query for programming languages count
$languageQuery = "SELECT purpose, COUNT(*) as count 
                 FROM sit_in 
                 GROUP BY purpose 
                 ORDER BY count DESC";
$languageResult = $conn->query($languageQuery);

$languages = [];
$languageCounts = [];
while($row = $languageResult->fetch_assoc()) {
    $languages[] = $row['purpose'];
    $languageCounts[] = $row['count'];
}

// Query for laboratory rooms count
$roomQuery = "SELECT laboratory, COUNT(*) as count 
             FROM sit_in 
             GROUP BY laboratory 
             ORDER BY count DESC";
$roomResult = $conn->query($roomQuery);

$rooms = [];
$roomCounts = [];
while($row = $roomResult->fetch_assoc()) {
    $rooms[] = $row['laboratory'];
    $roomCounts[] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in Records</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            themes: ["light"],
            plugins: [require("daisyui")],
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
</head>
<body class="bg-gray-100">
    
    <!-- Replace the existing navbar in sit_in_records.php -->
    <div class="navbar bg-[#2c343c] shadow-lg">
        <div class="navbar-start">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-xl font-bold text-white ml-2">Admin</span>
            </div>
        </div>
        
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 gap-2">
                <li>
                    <a href="admin_dashboard.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <a href="search.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                    </a>
                </li>
                <li>
                    <a href="students.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Students
                    </a>
                </li>
                <li>
                    <a href="sit_in.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Sit-in
                    </a>
                </li>
                <li>
                    <a href="sit_in_records.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        View Records
                    </a>
                </li>
                <li>
                    <a href="admin_reservation.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reservation
                    </a>
                </li>
                <li>
                    <a href="reports.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Reports
                    </a>
                </li>
                <li>
                    <a href="feedback.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        Feedback Reports
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="navbar-end">
            <button id="logoutBtn" class="btn btn-error btn-outline gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </div>
    </div>
    
    <!-- Logout confirmation modal -->
    <div id="logoutModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Confirm Logout</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to logout?</p>
                </div>
                <div class="flex justify-center gap-4 mt-3">
                    <button id="cancelLogout" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-md">
                        Cancel
                    </button>
                    <button id="confirmLogout" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md">
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Current Sit-in Records</h2>
    
    <!-- Charts Container -->
    <div class="grid grid-cols-2 gap-8 mb-8">
        <div class="bg-[#2c343c] p-8 rounded-lg shadow-lg">
            <div class="w-[500px] h-[500px] mx-auto" id="chart1"></div>
        </div>
        <div class="bg-[#2c343c] p-8 rounded-lg shadow-lg">
            <div class="w-[500px] h-[500px] mx-auto" id="chart2"></div>
        </div>
    </div>

    <!-- Replace the existing table section with this -->
    <div class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Search Bar -->
        <div class="p-4 border-b border-gray-200">
            <input type="text" 
                   id="search" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                   placeholder="Search records...">
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID Number
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Purpose
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Lab
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Login
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Logout
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sessions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="records-table">
                    <?php
                    // Update the SQL query to include session_count
                    $sql = "SELECT *, 
                            DATE_FORMAT(time_in, '%l:%i %p') as formatted_time_in,
                            DATE_FORMAT(time_out, '%l:%i %p') as formatted_time_out 
                            FROM sit_in 
                            ORDER BY STR_TO_DATE(CONCAT(date, ' ', time_in), '%Y-%m-%d %H:%i:%s') DESC";

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr class="hover:bg-gray-50 transition-colors duration-200">';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['idno']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['fullname']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['purpose']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['laboratory']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['formatted_time_in']) . '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">';
                            echo $row['time_out'] ? htmlspecialchars($row['formatted_time_out']) : 
                                 '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>';
                            echo '</td>';
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' . htmlspecialchars($row['date']) . '</td>';
                            // Add the new session count column
                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">';
                            echo '<span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">' 
                                 . htmlspecialchars($row['session_count']) 
                                 . '</span>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">No records found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
    
<script>
document.addEventListener('DOMContentLoaded', function() {
    // First Chart (Programming Languages)
    const chart1 = echarts.init(document.getElementById('chart1'));
    const languagesOption = {
        backgroundColor: '#2c343c',
        title: {
            text: 'Programming Languages Distribution',
            left: 'center',
            top: 20,
            textStyle: {
                color: '#ccc'
            }
        },
        tooltip: {
            trigger: 'item'
        },
        visualMap: {
            show: false,
            min: 0,
            max: Math.max(...<?php echo json_encode($languageCounts); ?>),
            inRange: {
                colorLightness: [0, 1]
            }
        },
        series: [{
            name: 'Programming Language',
            type: 'pie',
            radius: '55%',
            center: ['50%', '50%'],
            data: <?php 
                $languageData = array_map(function($name, $value) {
                    return ['value' => $value, 'name' => $name];
                }, $languages, $languageCounts);
                echo json_encode($languageData);
            ?>.sort(function(a, b) {
                return a.value - b.value;
            }),
            roseType: 'radius',
            label: {
                color: 'rgba(255, 255, 255, 0.3)'
            },
            labelLine: {
                lineStyle: {
                    color: 'rgba(255, 255, 255, 0.3)'
                },
                smooth: 0.2,
                length: 10,
                length2: 20
            },
            itemStyle: {
                color: '#c23531',
                shadowBlur: 200,
                shadowColor: 'rgba(0, 0, 0, 0.5)'
            },
            animationType: 'scale',
            animationEasing: 'elasticOut',
            animationDelay: function(idx) {
                return Math.random() * 200;
            }
        }]
    };
    chart1.setOption(languagesOption);

    // Second Chart (Laboratory Rooms)
    const chart2 = echarts.init(document.getElementById('chart2'));
    const roomsOption = {
        backgroundColor: '#2c343c',
        title: {
            text: 'Laboratory Room Distribution',
            left: 'center',
            top: 20,
            textStyle: {
                color: '#ccc'
            }
        },
        tooltip: {
            trigger: 'item'
        },
        visualMap: {
            show: false,
            min: 0,
            max: Math.max(...<?php echo json_encode($roomCounts); ?>),
            inRange: {
                colorLightness: [0, 1]
            }
        },
        series: [{
            name: 'Laboratory',
            type: 'pie',
            radius: '55%',
            center: ['50%', '50%'],
            data: <?php 
                $roomData = array_map(function($name, $value) {
                    return ['value' => $value, 'name' => $name];
                }, $rooms, $roomCounts);
                echo json_encode($roomData);
            ?>.sort(function(a, b) {
                return a.value - b.value;
            }),
            roseType: 'radius',
            label: {
                color: 'rgba(255, 255, 255, 0.3)'
            },
            labelLine: {
                lineStyle: {
                    color: 'rgba(255, 255, 255, 0.3)'
                },
                smooth: 0.2,
                length: 10,
                length2: 20
            },
            itemStyle: {
                color: '#c23531',
                shadowBlur: 200,
                shadowColor: 'rgba(0, 0, 0, 0.5)'
            },
            animationType: 'scale',
            animationEasing: 'elasticOut',
            animationDelay: function(idx) {
                return Math.random() * 200;
            }
        }]
    };
    chart2.setOption(roomsOption);

    // Handle window resize
    window.addEventListener('resize', function() {
        chart1.resize();
        chart2.resize();
    });

    // Logout Modal Functionality
    const logoutBtn = document.getElementById('logoutBtn');
    const logoutModal = document.getElementById('logoutModal');
    const cancelLogout = document.getElementById('cancelLogout');
    const confirmLogout = document.getElementById('confirmLogout');

    if (logoutBtn && logoutModal && cancelLogout && confirmLogout) {
        // Show modal when logout button is clicked
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            logoutModal.classList.remove('hidden');
        });

        // Hide modal when cancel is clicked
        cancelLogout.addEventListener('click', function() {
            logoutModal.classList.add('hidden');
        });

        // Perform logout when confirm is clicked
        confirmLogout.addEventListener('click', function() {
            window.location.href = 'logout.php';
        });

        // Close modal when clicking outside
        logoutModal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    } else {
        console.error('One or more logout elements not found');
    }

    // Search functionality
    document.getElementById('search').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#records-table tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    });
});
</script>
</body>
</html>
