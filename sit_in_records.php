<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sitin_system");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in Records</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">College of Computer Studies Admin</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Search</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Students</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Sit-in</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">View Sit-in Records</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Sit-in Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Feedback Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Reservation</a></li>
                </ul>
                <button class="btn btn-warning">Log out</button>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h2 class="text-center">Current Sit-in Records</h2>
        <div class="row">
            <div class="col-md-6">
                <canvas id="chart1"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="chart2"></canvas>
            </div>
        </div>

        <div class="mt-4">
            <input type="text" id="search" class="form-control" placeholder="Search...">
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Sit-in Number</th>
                        <th>ID Number</th>
                        <th>Name</th>
                        <th>Purpose</th>
                        <th>Lab</th>
                        <th>Login</th>
                        <th>Logout</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="records-table">
                    <?php
                    $sql = "SELECT * FROM sit_in";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['idno']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['purpose']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['laboratory']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['time_in']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['time_out']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        const ctx1 = document.getElementById('chart1').getContext('2d');
        new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: ['C#', 'C', 'Java', 'ASP.Net', 'Php'],
                datasets: [{
                    data: [1, 0, 0, 0, 4],
                    backgroundColor: ['blue', 'red', 'orange', 'gray', 'green']
                }]
            }
        });

        const ctx2 = document.getElementById('chart2').getContext('2d');
        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['524', '526', '528', '530', '542'],
                datasets: [{
                    data: [1, 0, 0, 0, 0],
                    backgroundColor: ['pink', 'lightblue', 'yellow', 'purple', 'gray']
                }]
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
