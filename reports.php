<?php
session_start();
include 'db.php';

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM feedback WHERE id = '$delete_id'");
    header("Location: feedback.php");
    exit();
}

// Get unique purposes for the filter dropdown
$purposesQuery = "SELECT DISTINCT purpose FROM sit_in WHERE time_out IS NOT NULL ORDER BY purpose ASC";
$purposesResult = $conn->query($purposesQuery);
$purposes = [];
if ($purposesResult->num_rows > 0) {
    while($purposeRow = $purposesResult->fetch_assoc()) {
        $purposes[] = $purposeRow['purpose'];
    }
}

// Update the SQL query to fetch only completed sit-ins
$sql = "SELECT s.created_at, s.idno, s.fullname, s.purpose, s.time_in, s.time_out 
        FROM sit_in s 
        WHERE s.time_out IS NOT NULL 
        ORDER BY s.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-In Monitoring - Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            themes: ["light"],
            plugins: [require("daisyui")],
        }
    </script>
    <style>
        @keyframes glow {
            0% { text-shadow: 0 0 5px #ffd700; }
            50% { text-shadow: 0 0 20px #ffd700, 0 0 30px #ffd700; }
            100% { text-shadow: 0 0 5px #ffd700; }
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-3px); }
            100% { transform: translateY(0px); }
        }
        
        .star-rating {
            color: #ffd700;
            animation: glow 2s ease-in-out infinite, float 3s ease-in-out infinite;
        }
        
        @keyframes glowGreen {
            0% { text-shadow: 0 0 5px #4ade80; }
            50% { text-shadow: 0 0 20px #4ade80, 0 0 30px #4ade80; }
            100% { text-shadow: 0 0 5px #4ade80; }
        }
        
        .type-glow {
            color: #22c55e;
            animation: glowGreen 2s ease-in-out infinite;
            font-weight: 600;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
</head>
<body class="bg-gray-100">

<!-- Admin Navbar -->
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
        <button id="logoutBtn" class="btn btn-error btn-outline gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Logout
        </button>
    </div>
</div>

<!-- Rest of your existing content -->
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-lg mb-8 p-6">
        <h2 class="text-3xl font-bold text-center text-blue-600">REPORTS</h2>
    </div>

    <!-- Search and Filter Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <!-- Search Input -->
        <div class="relative">
            <input type="text" 
                   id="searchInput" 
                   placeholder="Search reports..." 
                   class="w-full px-4 py-2 pl-10 pr-4 text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <!-- Purpose Filter Dropdown -->
        <div class="relative">
            <select id="purposeFilter" class="w-full px-4 py-2 pl-10 pr-4 text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none">
                <option value="">All Purposes</option>
                <?php foreach($purposes as $purpose): ?>
                <option value="<?php echo htmlspecialchars($purpose); ?>"><?php echo htmlspecialchars($purpose); ?></option>
                <?php endforeach; ?>
            </select>
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                </svg>
            </div>
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Export Buttons Section -->
    <div class="mb-6 flex justify-end space-x-4">
        <!-- CSV Export -->
        <button onclick="exportToCSV()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            CSV
        </button>

        <!-- Excel Export -->
        <button onclick="exportToExcel()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Excel
        </button>

        <!-- PDF Export -->
        <button onclick="exportToPDF()" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            PDF
        </button>

        <!-- Print -->
        <button onclick="printTable()" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print
        </button>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-[#2c343c] text-white">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Purpose</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Time In</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Time Out</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Type</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200" id="feedbackTable">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $date = date('M d, Y', strtotime($row['created_at']));
                    $timeIn = date('h:i A', strtotime($row['time_in']));
                    $timeOut = $row['time_out'] ? date('h:i A', strtotime($row['time_out'])) : 'Ongoing';
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium"><?php echo $date; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['idno']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['fullname']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['purpose']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $timeIn; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $timeOut; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="type-glow">SIT-IN</span>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No records available</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div> <!-- End of table div -->

<script>
// Update the search function to also consider the purpose filter
function applyFilters() {
    const searchText = document.getElementById("searchInput").value.toLowerCase();
    const purposeFilter = document.getElementById("purposeFilter").value;
    const rows = document.querySelectorAll("#feedbackTable tr");
        
    rows.forEach(row => {
        const cells = row.getElementsByTagName('td');
        if (cells.length > 0) {
            const idNumber = cells[1].textContent.toLowerCase();
            const name = cells[2].textContent.toLowerCase();
            const purpose = cells[3].textContent.trim(); // Don't lowercase for exact comparison
            const date = cells[0].textContent.toLowerCase();
            
            const matchesSearch = idNumber.includes(searchText) || 
                                  name.includes(searchText) || 
                                  purpose.toLowerCase().includes(searchText) || 
                                  date.includes(searchText);
            
            // Exact purpose matching
            const matchesPurpose = !purposeFilter || purpose === purposeFilter;
            
            if (matchesSearch && matchesPurpose) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    });
}

// Event listeners for filters
document.getElementById("searchInput").addEventListener("keyup", applyFilters);
document.getElementById("purposeFilter").addEventListener("change", applyFilters);

function exportToCSV() {
    // Create header rows with proper formatting
    const purposeFilter = document.getElementById("purposeFilter").value;
    const purposeText = purposeFilter ? `Purpose: ${purposeFilter}` : "All Purposes";
    
    const headers = [
        '"UNIVERSITY OF CEBU"',
        '"COLLEGE OF COMPUTER STUDIES"',
        '"Sit-In Monitoring System"',
        `"Generated on: ${new Date().toLocaleString()}"`,
        `"Filter: ${purposeText}"`,
        '', // Empty line for spacing
        '"Date","ID Number","Name","Purpose","Time In","Time Out","Type"' // Quoted headers
    ];
    
    const csv = [...headers];
    
    // Get data from visible rows only
    document.querySelectorAll('#feedbackTable tr').forEach(row => {
        if (row.style.display !== 'none') {
            const cols = row.getElementsByTagName('td');
            
            if (cols.length > 0) {
                // Get values and handle special characters
                const date = cols[0].textContent.trim();
                const idNumber = cols[1].textContent.trim();
                const name = cols[2].textContent.trim();
                const purpose = cols[3].textContent.trim();
                const timeIn = cols[4].textContent.trim();
                const timeOut = cols[5].textContent.trim();
                
                // Format each row with proper quoting and spacing
                const rowData = [
                    `"${date}"`,
                    `"${idNumber}"`,
                    `"${name}"`,
                    `"${purpose}"`,
                    `"${timeIn}"`,
                    `"${timeOut}"`,
                    '"SIT-IN"'
                ];
                
                csv.push(rowData.join(','));
            }
        }
    });

    // Create and trigger download with UTF-8 BOM for Excel compatibility
    const BOM = '\uFEFF';
    const csvContent = BOM + csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    
    const fileName = purposeFilter ? 
        `UC_SitIn_${purposeFilter}_${new Date().toLocaleDateString()}.csv` :
        `UC_SitIn_Records_${new Date().toLocaleDateString()}.csv`;
    
    link.setAttribute('download', fileName);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToExcel() {
    // Initialize new workbook
    const wb = XLSX.utils.book_new();
    
    // Get purpose filter value
    const purposeFilter = document.getElementById("purposeFilter").value;
    const purposeText = purposeFilter ? `Purpose: ${purposeFilter}` : "All Purposes";
    
    // Create header data
    const headerData = [
        ['UNIVERSITY OF CEBU'],
        ['COLLEGE OF COMPUTER STUDIES'],
        ['Sit-In Monitoring System'],
        [`Generated on: ${new Date().toLocaleString()}`],
        [`Filter: ${purposeText}`],
        [''], // Empty row for spacing
        ['Date', 'ID Number', 'Name', 'Purpose', 'Time In', 'Time Out', 'Type']
    ];
    
    // Get visible table data using DOM traversal
    const tableRows = [];
    document.querySelectorAll('#feedbackTable tr').forEach(row => {
        if (row.style.display !== 'none') {
            const rowData = Array.from(row.querySelectorAll('td')).map(cell => cell.textContent.trim());
            if (rowData.length > 0) {
                tableRows.push(rowData);
            }
        }
    });

    // Combine headers and data
    const wsData = [...headerData, ...tableRows];
    
    // Create worksheet and set properties
    const ws = XLSX.utils.aoa_to_sheet(wsData);
    ws['!cols'] = [
        { wch: 22 }, // Date
        { wch: 22 }, // ID Number
        { wch: 35 }, // Name
        { wch: 35 }, // Purpose
        { wch: 20 }, // Time In
        { wch: 20 }, // Time Out
        { wch: 20 }  // Type
    ];

    // Set filename based on purpose filter
    const fileName = purposeFilter ? 
        `UC_SitIn_${purposeFilter}.xlsx` : 
        "UC_SitIn_Records.xlsx";
    
    // Add worksheet to workbook and save
    XLSX.utils.book_append_sheet(wb, ws, "Reports");
    XLSX.writeFile(wb, fileName);
}

function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    
    // Get purpose filter value
    const purposeFilter = document.getElementById("purposeFilter").value;
    const purposeText = purposeFilter ? `Purpose: ${purposeFilter}` : "All Purposes";
    
    // Create a clone of the table for manipulation
    const originalTable = document.querySelector('table');
    const tableClone = originalTable.cloneNode(true);
    
    // Get all visible rows from the original table
    const visibleRows = [];
    document.querySelectorAll('#feedbackTable tr').forEach((row, index) => {
        if (row.style.display !== 'none') {
            visibleRows.push(index);
        }
    });
    
    // Remove all rows from the clone that aren't in our visible set
    const cloneRows = tableClone.querySelectorAll('tbody tr');
    Array.from(cloneRows).forEach((row, index) => {
        if (!visibleRows.includes(index)) {
            row.remove();
        }
    });
    
    // Style the header row
    const headerRow = tableClone.querySelector('thead tr');
    if (headerRow) {
        headerRow.style.backgroundColor = '#2c343c';
        headerRow.style.color = 'white';
    }
    
    // Update image paths to point to project root
    const ucLogo = new Image();
    ucLogo.src = 'University-of-Cebu-Logo.jpg';
    
    const ccsLogo = new Image();
    ccsLogo.src = 'ccs.png';
    
    Promise.all([
        new Promise((resolve) => {
            ucLogo.onload = resolve;
            ucLogo.onerror = () => {
                console.error('Error loading UC logo');
                resolve();
            };
        }),
        new Promise((resolve) => {
            ccsLogo.onload = resolve;
            ccsLogo.onerror = () => {
                console.error('Error loading CCS logo');
                resolve();
            };
        })
    ]).then(() => {
        try {
            const pageWidth = doc.internal.pageSize.getWidth();
            
            // Add logos
            doc.addImage(ucLogo, 'JPEG', pageWidth/2 - 30, 10, 20, 20);
            doc.addImage(ccsLogo, 'PNG', pageWidth/2 + 10, 10, 20, 20);
            
            let yPos = 40;
            
            // Add headers
            doc.setFontSize(16);
            doc.setFont('helvetica', 'bold');
            doc.text('UNIVERSITY OF CEBU', pageWidth/2, yPos, { align: 'center' });
            
            yPos += 8;
            doc.setFontSize(14);
            doc.text('COLLEGE OF COMPUTER STUDIES', pageWidth/2, yPos, { align: 'center' });
            
            yPos += 8;
            doc.setFontSize(12);
            doc.text('Sit-In Monitoring System', pageWidth/2, yPos, { align: 'center' });
            
            yPos += 8;
            doc.setFontSize(10);
            doc.setFont('helvetica', 'normal');
            doc.text(`Generated on: ${new Date().toLocaleString()}`, pageWidth/2, yPos, { align: 'center' });
            
            yPos += 6;
            doc.setFontSize(10);
            doc.setFont('helvetica', 'bold');
            doc.text(`Filter: ${purposeText}`, pageWidth/2, yPos, { align: 'center' });
            
            // Add table with headers using the cloned and styled table
            doc.autoTable({
                html: tableClone,
                startY: yPos + 10,
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 2
                },
                headStyles: {
                    fillColor: [44, 52, 60],
                    textColor: [255, 255, 255],
                    fontSize: 9,
                    fontStyle: 'bold'
                },
                columnStyles: {
                    0: { cellWidth: 25 }, // Date
                    1: { cellWidth: 25 }, // ID Number
                    2: { cellWidth: 35 }, // Name
                    3: { cellWidth: 35 }, // Purpose
                    4: { cellWidth: 20 }, // Time In
                    5: { cellWidth: 20 }, // Time Out
                    6: { // Type column
                        cellWidth: 20,
                        textColor: [34, 197, 94],
                        fontStyle: 'bold'
                    }
                },
                margin: { top: 10 },
                didDrawPage: function(data) {
                    // Add page number at the bottom
                    doc.setFontSize(8);
                    doc.text(`Page ${doc.internal.getNumberOfPages()}`, data.settings.margin.left, doc.internal.pageSize.height - 10);
                }
            });

            // Set filename based on purpose filter
            const fileName = purposeFilter ? 
                `UC_SitIn_${purposeFilter.replace(/[^a-z0-9]/gi, '_')}.pdf` : 
                'UC_SitIn_Records.pdf';

            // Save the PDF
            doc.save(fileName);
        } catch (error) {
            console.error('Error generating PDF:', error);
            alert('Error generating PDF. Please check the console for details.');
        }
    });
}

function printTable() {
    // Create a printable area
    const printContent = document.createElement('div');
    
    // Get purpose filter value
    const purposeFilter = document.getElementById("purposeFilter").value;
    const purposeText = purposeFilter ? `Purpose: ${purposeFilter}` : "All Purposes";
    
    printContent.innerHTML = `
        <style>
            @media print {
                .header { 
                    text-align: center;
                }
                .logo-container {
                    text-align: center;
                    margin-bottom: 10px;
                }
                .logo {
                    height: 50px;
                    margin: 0 10px;
                }
                table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-top: 15px;
                }
                thead { 
                    display: table-header-group; 
                }
                th { 
                    background-color: #2c343c !important; 
                    color: white !important;
                    font-weight: bold;
                    padding: 8px;
                    text-transform: uppercase;
                    font-size: 9px;
                    text-align: left;
                }
                th, td { 
                    border: 1px solid #ddd; 
                    padding: 6px; 
                }
                td {
                    font-size: 8px;
                    color: #4b5563;
                }
                tr:nth-child(even) { 
                    background-color: #f9fafb; 
                }
                .type-glow { 
                    color: #22c55e !important; 
                    font-weight: bold; 
                }
                .filter-info {
                    text-align: center;
                    font-size: 10px;
                    font-weight: bold;
                    margin: 5px 0;
                }
                @page { 
                    size: portrait;
                    margin: 15mm;
                }
                body {
                    font-family: helvetica, sans-serif;
                }
                .page-number {
                    position: fixed;
                    bottom: 10mm;
                    right: 10mm;
                    font-size: 8px;
                    color: #808080;
                }
            }
        </style>
        <div class="logo-container">
            <img src="University-of-Cebu-Logo.jpg" class="logo" style="height: 50px;">
            <img src="ccs.png" class="logo" style="height: 50px;">
        </div>
        <div class="header">
            <h1 style="margin: 0; font-size: 14px; color: #2c343c; font-weight: bold;">UNIVERSITY OF CEBU</h1>
            <h2 style="margin: 5px 0; font-size: 12px; color: #2c343c; font-weight: bold;">COLLEGE OF COMPUTER STUDIES</h2>
            <h3 style="margin: 5px 0; font-size: 11px; color: #2c343c;">Sit-In Monitoring System</h3>
            <p style="margin: 5px 0; font-size: 9px; color: #4b5563;">Generated on: ${new Date().toLocaleString()}</p>
            <p class="filter-info" style="color: #2c343c;">Filter: ${purposeText}</p>
        </div>
    `;

    // Clone the original table
    const originalTable = document.querySelector('table');
    const tableClone = originalTable.cloneNode(true);
    
    // Get all visible rows
    const visibleRows = [];
    document.querySelectorAll('#feedbackTable tr').forEach((row, index) => {
        if (row.style.display !== 'none') {
            visibleRows.push(index);
        }
    });
    
    // Remove all rows from the clone that aren't in our visible set
    const cloneRows = tableClone.querySelectorAll('tbody tr');
    Array.from(cloneRows).forEach((row, index) => {
        if (!visibleRows.includes(index)) {
            row.remove();
        }
    });

    // Ensure the header has the correct styling
    const headerRow = tableClone.querySelector('thead tr');
    if (headerRow) {
        headerRow.style.backgroundColor = '#2c343c';
        headerRow.style.color = 'white';
    }

    printContent.appendChild(tableClone);

    // Add page number div
    printContent.innerHTML += '<div class="page-number"></div>';

    // Create a new window for printing
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print</title>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContent.innerHTML);
    printWindow.document.close();

    // Wait for images and styles to load
    setTimeout(() => {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }, 500);
}

// Initialize filters when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Apply filters on page load in case URL parameters set a filter
    applyFilters();
    
    const logoutBtn = document.getElementById('logoutBtn');
    const logoutModal = document.getElementById('logoutModal');
    const cancelLogout = document.getElementById('cancelLogout');
    const confirmLogout = document.getElementById('confirmLogout');

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
});
</script>

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

</body>
</html>

<?php
$conn->close();
?>
