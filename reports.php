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

// Update the SQL query first
$sql = "SELECT f.created_at, s.idno, s.fullname, s.purpose, s.time_in, s.time_out 
        FROM feedback f 
        JOIN sit_in s ON f.sit_in_id = s.id 
        ORDER BY f.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-In Monitoring - Feedbacks</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Update these script imports (remove the old ones first) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

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
</head>
<body class="bg-gray-100">

<!-- Admin Navbar -->
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
                <li><a href="admin_dashboard.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Home</span>
                </a></li>
                <li><a href="search.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Search</span>
                </a></li>
                <li><a href="students.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Students</span>
                </a></li>
                <li><a href="sit_in.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Sit-in</span>
                </a></li>
                <li><a href="sit_in_records.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>View Records</span>
                </a></li>
                <li><a href="reservation.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Reservation</span>
                </a></li>
                <li><a href="reports.php" class="text-white hover:text-yellow-200 transition-colors duration-200 font-medium flex items-center space-x-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Reports</span>
                </a></li>
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
            <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Log out</span>
            </a>
        </div>
    </div>
</nav>

<!-- Rest of your existing content -->
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-lg mb-8 p-6">
        <h2 class="text-3xl font-bold text-center text-blue-600">REPORTS</h2>
    </div>

    <!-- Search Section -->
    <div class="max-w-md mx-auto mb-6">
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
document.getElementById("searchInput").addEventListener("keyup", function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#feedbackTable tr:not(:first-child)");
    
    rows.forEach(row => {
        const idNumber = row.children[1].textContent.toLowerCase();
        const name = row.children[2].textContent.toLowerCase();
        const purpose = row.children[3].textContent.toLowerCase();
        const date = row.children[0].textContent.toLowerCase();
        
        if (idNumber.includes(filter) || 
            name.includes(filter) || 
            purpose.includes(filter) || 
            date.includes(filter)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>

<script>
// Replace the existing export functions with these updated versions
function exportToCSV() {
    // Create header rows with proper formatting
    const headers = [
        '"UNIVERSITY OF CEBU"',
        '"COLLEGE OF COMPUTER STUDIES"',
        '"Sit-In Monitoring System"',
        `"Generated on: ${new Date().toLocaleString()}"`,
        '', // Empty line for spacing
        '"Date","ID Number","Name","Purpose","Time In","Time Out","Type"' // Quoted headers
    ];
    
    const table = document.getElementById('feedbackTable');
    let csv = headers;
    const rows = table.getElementsByTagName('tr');
    
    // Get data
    for(let i = 0; i < rows.length; i++) {
        const cols = rows[i].getElementsByTagName('td');
        
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

    // Create and trigger download with UTF-8 BOM for Excel compatibility
    const BOM = '\uFEFF';
    const csvContent = BOM + csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', `UC_SitIn_Records_${new Date().toLocaleDateString()}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToExcel() {
    const wb = XLSX.utils.book_new();
    
    // Create header data with proper formatting
    const headerData = [
        ['UNIVERSITY OF CEBU'],
        ['COLLEGE OF COMPUTER STUDIES'],
        ['Sit-In Monitoring System'],
        [`Generated on: ${new Date().toLocaleString()}`],
        [''], // Empty row for spacing
        ['Date', 'ID Number', 'Name', 'Purpose', 'Time In', 'Time Out', 'Type'] // Column headers
    ];
    
    // Get table data
    const tableRows = Array.from(document.querySelectorAll('#feedbackTable tr')).map(row => 
        Array.from(row.querySelectorAll('td')).map(cell => cell.textContent.trim())
    ).filter(row => row.length > 0); // Remove empty rows

    // Combine headers and table data
    const wsData = [...headerData, ...tableRows];
    
    // Create worksheet
    const ws = XLSX.utils.aoa_to_sheet(wsData);
    
    // Set column widths
    ws['!cols'] = [
        { wch: 22 },    // Date
        { wch: 22 },    // ID Number
        { wch: 35 },    // Name
        { wch: 35 },    // Purpose
        { wch: 20 },    // Time In
        { wch: 20 },    // Time Out
        { wch: 20 }     // Type
    ];

    // Style the headers (first 6 rows)
    for (let i = 0; i < 6; i++) {
        const cellRef = XLSX.utils.encode_cell({r: i, c: 0});
        if (!ws[cellRef]) continue;
        
        ws[cellRef].s = {
            font: {
                bold: true,
                sz: i < 4 ? 14 : 12 // Larger font for institution name, smaller for headers
            },
            alignment: {
                horizontal: 'center',
                vertical: 'center'
            }
        };
    }

    // Center align and merge cells for the header text
    for (let i = 0; i < 4; i++) {
        const range = { s: {r: i, c: 0}, e: {r: i, c: 6} };
        ws['!merges'] = ws['!merges'] || [];
        ws['!merges'].push(range);
    }

    // Style the column headers (row 6)
    const headerRow = 5;
    for (let i = 0; i < 7; i++) {
        const cellRef = XLSX.utils.encode_cell({r: headerRow, c: i});
        if (ws[cellRef]) {
            ws[cellRef].s = {
                font: { bold: true, color: { rgb: "FFFFFF" } },
                fill: { fgColor: { rgb: "2C343C" } },
                alignment: { horizontal: 'left', vertical: 'center' }
            };
        }
    }

    // Style the data cells
    tableRows.forEach((row, rowIndex) => {
        row.forEach((cell, colIndex) => {
            const cellRef = XLSX.utils.encode_cell({r: rowIndex + headerData.length, c: colIndex});
            if (ws[cellRef]) {
                ws[cellRef].s = {
                    font: {
                        sz: 11,
                        color: { rgb: colIndex === 6 ? "22C55E" : "4B5563" }, // Green for SIT-IN
                        bold: colIndex === 6 // Bold for SIT-IN
                    },
                    alignment: {
                        horizontal: colIndex === 4 || colIndex === 5 || colIndex === 6 ? 'center' : 'left',
                        vertical: 'center'
                    }
                };
            }
        });
    });

    XLSX.utils.book_append_sheet(wb, ws, "Reports");
    XLSX.writeFile(wb, "UC_SitIn_Records.xlsx");
}

function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4'); // Changed to portrait orientation
    
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const margin = 15;
    
    // Load both images
    const ucLogo = new Image();
    const ccsLogo = new Image();
    ucLogo.src = 'University-of-Cebu-Logo.jpg';
    ccsLogo.src = 'ccs.png';

    // Wait for both images to load before creating PDF
    Promise.all([
        new Promise(resolve => ucLogo.onload = resolve),
        new Promise(resolve => ccsLogo.onload = resolve)
    ]).then(() => {
        // Calculate image dimensions
        const logoWidth = 15; // Smaller logo size for portrait
        const logoHeight = 15;
        
        // Calculate positions for centered logos
        const ucLogoX = pageWidth/2 - logoWidth - 5;
        const ccsLogoX = pageWidth/2 + 5;
        const logosY = margin;

        // Add both logos
        doc.addImage(ucLogo, 'JPEG', ucLogoX, logosY, logoWidth, logoHeight);
        doc.addImage(ccsLogo, 'PNG', ccsLogoX, logosY, logoWidth, logoHeight);

        // Add headers below logos with adjusted spacing
        const textStartY = margin + logoHeight + 5;

        // Add University Header with adjusted positions
        doc.setFontSize(14); // Reduced font sizes for portrait
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(44, 52, 60);
        doc.text('UNIVERSITY OF CEBU', pageWidth/2, textStartY + 5, { align: 'center' });
        
        doc.setFontSize(12);
        doc.text('COLLEGE OF COMPUTER STUDIES', pageWidth/2, textStartY + 12, { align: 'center' });
        
        doc.setFontSize(11);
        doc.text('Sit-In Monitoring System', pageWidth/2, textStartY + 19, { align: 'center' });
        
        doc.setFontSize(9);
        doc.setFont('helvetica', 'normal');
        doc.text('Generated on: ' + new Date().toLocaleString(), pageWidth/2, textStartY + 26, { align: 'center' });

        // Table configuration with adjusted dimensions for portrait
        doc.autoTable({
            // Define explicit headers
            head: [[
                'Date',
                'ID Number',
                'Name', 
                'Purpose',
                'Time In',
                'Time Out',
                'Type'
            ]],
            body: Array.from(document.querySelectorAll('#feedbackTable tr')).map(row => 
                Array.from(row.querySelectorAll('td')).map(cell => cell.textContent.trim())
            ),
            startY: textStartY + 35,
            theme: 'grid',
            styles: {
                font: 'helvetica',
                fontSize: 7,
                cellPadding: 3,
                lineWidth: 0.1,
                lineColor: [221, 221, 221],
                minCellHeight: 8
            },
            headStyles: {
                fillColor: [44, 52, 60],
                textColor: [255, 255, 255],
                fontSize: 8,
                fontStyle: 'bold',
                halign: 'left',
                valign: 'middle'
            },
            columnStyles: {
                0: { cellWidth: 22, halign: 'left' },   // Date
                1: { cellWidth: 22, halign: 'left' },   // ID Number
                2: { cellWidth: 35, halign: 'left' },   // Name
                3: { cellWidth: 35, halign: 'left' },   // Purpose
                4: { cellWidth: 20, halign: 'center' }, // Time In
                5: { cellWidth: 20, halign: 'center' }, // Time Out
                6: { 
                    cellWidth: 20,
                    halign: 'center',
                    textColor: [34, 197, 94],
                    fontStyle: 'bold'
                }  // Type
            },
            margin: {
                top: margin,
                right: margin,
                bottom: margin,
                left: margin
            },
            didDrawPage: function(data) {
                if (data.pageNumber > 1) {
                    // Repeat logos and headers on new pages
                    doc.addImage(ucLogo, 'JPEG', ucLogoX, margin, logoWidth, logoHeight);
                    doc.addImage(ccsLogo, 'PNG', ccsLogoX, margin, logoWidth, logoHeight);
                    doc.setFontSize(12);
                    doc.setFont('helvetica', 'bold');
                    doc.text('UNIVERSITY OF CEBU - CCS Sit-In Records', pageWidth/2, margin + logoHeight + 10, { align: 'center' });
                }
                // Page numbers
                doc.setFontSize(8);
                doc.setTextColor(128, 128, 128);
                doc.text(`Page ${data.pageNumber}`, pageWidth - margin, pageHeight - 10);
            }
        });
        
        doc.save('UC_SitIn_Records.pdf');
    })
    .catch(error => {
        console.error('Error loading images:', error);
        alert('Error loading images. Please make sure both images exist in the correct directory.');
    });
}

function printTable() {
    // Create a printable area
    const printContent = document.createElement('div');
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
        </div>
    `;

    // Clone the original table
    const originalTable = document.querySelector('table');
    const tableClone = originalTable.cloneNode(true);

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
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    // Wait for images and styles to load
    setTimeout(() => {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }, 500);
}
</script>

</body>
</html>

<?php
$conn->close();
?>
