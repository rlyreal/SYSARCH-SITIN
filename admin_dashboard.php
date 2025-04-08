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

// Handle announcement submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $announcement = trim($_POST['announcement']);
    if (!empty($announcement)) {
        // Use username instead of first_name and last_name
        $admin_name = $admin_username; // Using the username from session
        $query = $conn->prepare("INSERT INTO announcements (admin_name, message, date) VALUES (?, ?, NOW())");
        $query->bind_param("ss", $admin_name, $announcement);
        if ($query->execute()) {
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
    <!-- Update these script sources -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            themes: ["light"],
            plugins: [require("daisyui")],
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Add this in the head section -->
    <style>
        .chart-container {
            background-color: #2c343c;
            border-radius: 0.5rem;
            padding: 1rem;
            position: relative;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Replace the existing navbar with this -->
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
                    <a href="reservation.php" class="btn btn-ghost text-white hover:bg-white/10">
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
            <a href="logout.php" class="btn btn-error btn-outline gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </a>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mt-6">
        <!-- ✅ Statistics Section -->
        <div class="col-span-1">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                    <div class="text-gray-500 text-sm">Students Registered</div>
                    <div class="text-2xl font-bold text-blue-600"><?= $students ?></div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm">Currently Sit-in</div>
                    <div class="text-2xl font-bold text-green-600"><?= $current_sit_in ?></div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-yellow-500">
                    <div class="text-gray-500 text-sm">Total Sit-in</div>
                    <div class="text-2xl font-bold text-yellow-600"><?= $total_sit_in ?></div>
                </div>
            </div>

            <!-- Pie Chart Card -->
            <div class="bg-white p-4 shadow-md rounded-lg">
                <div class="chart-container" style="height: 400px;">
                    <div id="statsChart" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>

        <!-- ✅ Announcements Section -->
        <div class="bg-white p-4 shadow-md rounded-md max-h-[500px]">
            <h3 class="text-lg font-semibold">📢 Announcements</h3>
            <form method="POST" class="mb-4">
                <textarea 
                    name="announcement" 
                    class="w-full p-2 border rounded-md resize-none h-24" 
                    placeholder="Write an announcement..." 
                    required
                ></textarea>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-700 mt-2">Post</button>
            </form>
            <h5 class="font-semibold">Recent Announcements</h5>
            <div id="announcementContainer" class="mt-2 border-t pt-2 h-[200px] overflow-y-auto">
                <!-- Announcements will load here -->
            </div>
        </div>
    </div> <!-- End of grid md:grid-cols-2 -->
    
    <!-- Bar Graph Card - Full Width -->
    <div class="mt-6 bg-white p-6 shadow-md rounded-lg">
        <div class="h-[300px]">
            <canvas id="yearLevelChart"></canvas>
        </div>
    </div>
</div> <!-- End of container -->
<script>
    // Replace the existing statsChart initialization with this
    const statsChart = echarts.init(document.getElementById('statsChart'));

    const statsOption = {
        backgroundColor: '#2c343c',
        title: {
            text: 'Programming Language Distribution',
            left: 'center',
            top: 20,
            textStyle: {
                color: '#ccc'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        visualMap: {
            show: false,
            min: 0,
            max: Math.max(...<?php echo json_encode($purposeCounts); ?>),
            inRange: {
                colorLightness: [0, 1]
            }
        },
        series: [
            {
                name: 'Programming Languages',
                type: 'pie',
                radius: '55%',
                center: ['50%', '50%'],
                data: <?php
                    $chartData = array_map(function($name, $value) {
                        return ['value' => $value, 'name' => $name];
                    }, $purposes, $purposeCounts);
                    echo json_encode($chartData);
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
            }
        ]
    };

    statsChart.setOption(statsOption);

    // Add window resize handler
    window.addEventListener('resize', function() {
        statsChart.resize();
    });

    // Replace the existing yearLevelChart initialization
    new Chart(document.getElementById('yearLevelChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($yearLevels); ?>,
            datasets: [{
                label: 'Number of Students',
                data: <?php echo json_encode($yearLevelCounts); ?>,
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)', // blue
                    'rgba(16, 185, 129, 0.8)', // green
                    'rgba(251, 191, 36, 0.8)', // yellow
                    'rgba(239, 68, 68, 0.8)'   // red
                ],
                borderColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(251, 191, 36)',
                    'rgb(239, 68, 68)'
                ],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.6,
                categoryPercentage: 0.8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Student Year Level Distribution',
                    color: '#1f2937',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart',
                delay: (context) => context.dataIndex * 300
            },
            hover: {
                mode: 'index',
                intersect: false
            },
            elements: {
                bar: {
                    shadowOffsetX: 3,
                    shadowOffsetY: 3,
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 0, 0, 0.2)'
                }
            }
        }
    });

    // Replace the existing loadAnnouncements function
    function loadAnnouncements() {
        fetch('get_announcements.php')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('announcementContainer');
                container.innerHTML = ''; // Clear previous content
                if (data.status === 'success') {
                    data.announcements.forEach(announcement => {
                        const announcementDiv = document.createElement('div');
                        announcementDiv.classList.add('p-2', 'border-b', 'relative', 'group');
                        announcementDiv.innerHTML = `
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <strong class="text-blue-600">${announcement.admin_name}</strong>
                                        <span class="text-gray-500">${announcement.date}</span>
                                    </div>
                                    <p class="mt-1">${announcement.message}</p>
                                </div>
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick="editAnnouncement(${announcement.id})" 
                                            class="text-blue-600 hover:text-blue-800 p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button onclick="deleteAnnouncement(${announcement.id})" 
                                            class="text-red-600 hover:text-red-800 p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>`;
                        container.appendChild(announcementDiv);
                    });
                } else {
                    container.innerHTML = '<p class="text-gray-500">No announcements available.</p>';
                }
            })
            .catch(error => console.error('Error loading announcements:', error));
    }

    // Replace the existing editAnnouncement function with this:
    function editAnnouncement(id) {
        if (confirm('Do you want to edit this announcement?')) {
            // First get the announcements data
            fetch('get_announcements.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Find the specific announcement
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

    // Add these new functions for edit and delete functionality
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

    // Load announcements on page load & refresh every minute
    window.onload = loadAnnouncements;
    setInterval(loadAnnouncements, 60000);
</script>
</body>
</html>
