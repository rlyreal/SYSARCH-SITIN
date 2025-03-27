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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    
    <!-- ✅ Admin Navbar -->
    <nav class="bg-[#2c343c] px-6 py-4 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <!-- Logo Section -->
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-2xl font-bold text-white">Admin</span>
            </div>

            <!-- Center Navigation Links -->
            <div class="flex-1 flex justify-center">
                <ul class="flex items-center space-x-6">
                    <li>
                        <a href="admin_dashboard.php" 
                           class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="search.php" 
                           class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>Search</span>
                        </a>
                    </li>
                    <li>
                        <a href="students.php" 
                           class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Students</span>
                        </a>
                    </li>
                    <li>
                        <a href="sit_in.php" 
                           class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span>Sit-in</span>
                        </a>
                    </li>
                    <li>
                        <a href="sit_in_records.php" 
                           class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span>View Records</span>
                        </a>
                    </li>
                    <li>
                        <a href="reservation.php" 
                           class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Reservation</span>
                        </a>
                    </li>
                    <li>
                        <a href="reports.php" 
                           class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li><a href="feedback.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        <span>Feedback Reports</span>
                    </a></li>
                </ul>
            </div>

            <!-- Right-aligned Logout Button -->
            <div class="flex-shrink-0 ml-6">
                <a href="logout.php" 
                   class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Log out</span>
                </a>
            </div>
        </div>
    </nav>
    
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Current Sit-in Records</h2>
    
    <!-- Charts Container -->
    <div class="grid grid-cols-2 gap-8 mb-8">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold text-gray-700 mb-6 text-center"></h3>
            <div class="w-[500px] h-[500px] mx-auto"> <!-- Increased from w-96 h-96 -->
                <canvas id="chart1"></canvas>
            </div>
        </div>
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold text-gray-700 mb-6 text-center"></h3>
            <div class="w-[500px] h-[500px] mx-auto"> <!-- Increased from w-96 h-96 -->
                <canvas id="chart2"></canvas>
            </div>
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
    // Replace the existing chartOptions
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                padding: 25,
                labels: {
                    font: {
                        size: 16 // Increased font size
                    },
                    padding: 25
                }
            },
            tooltip: {
                enabled: true,
                titleFont: {
                    size: 18
                },
                bodyFont: {
                    size: 16
                }
            },
            title: {
                font: {
                    size: 20
                }
            }
        },
        radius: ['100%', '150%'],  // Increased radius range further
        layout: {
            padding: {
                top: 30,
                bottom: 30,
                left: 30,
                right: 30
            }
        }
    };

    // Replace the existing chart initialization code
    const ctx1 = document.getElementById('chart1').getContext('2d');
    new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($languages); ?>,
            datasets: [{
                data: <?php echo json_encode($languageCounts); ?>,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(201, 203, 207, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(51, 153, 102, 0.8)'
                ],
                borderColor: 'white',
                borderWidth: 2,
                hoverOffset: 15,
                offset: 10
            }]
        },
        options: {
            ...chartOptions,
            plugins: {
                ...chartOptions.plugins,
                title: {
                    display: true,
                    text: 'Programming Languages Distribution',
                    padding: 20
                }
            }
        }
    });

    const ctx2 = document.getElementById('chart2').getContext('2d');
    new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($rooms); ?>,
            datasets: [{
                data: <?php echo json_encode($roomCounts); ?>,
                backgroundColor: [
                    'rgba(255, 182, 193, 0.8)',
                    'rgba(135, 206, 235, 0.8)',
                    'rgba(255, 255, 224, 0.8)',
                    'rgba(147, 112, 219, 0.8)',
                    'rgba(169, 169, 169, 0.8)',
                    'rgba(152, 251, 152, 0.8)',
                    'rgba(255, 160, 122, 0.8)',
                    'rgba(176, 196, 222, 0.8)'
                ],
                borderColor: 'white',
                borderWidth: 2,
                hoverOffset: 15,
                offset: 10
            }]
        },
        options: {
            ...chartOptions,
            plugins: {
                ...chartOptions.plugins,
                title: {
                    display: true,
                    text: 'Laboratory Room Distribution',
                    padding: 20
                }
            }
        }
    });

    document.getElementById('search').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#records-table tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    });
</script>
</body>
</html>
