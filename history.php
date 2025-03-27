<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get the current user's ID number from the users table
$user_id = $_SESSION['user_id'];
$id_query = "SELECT id_no FROM users WHERE id = ?";
$stmt = $conn->prepare($id_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$id_result = $stmt->get_result();
$user_data = $id_result->fetch_assoc();
$user_id_no = $user_data['id_no'];

// Fetch history data for current user only using their ID number
$sql = "SELECT 
    id,
    idno,
    fullname,
    purpose,
    laboratory,
    time_in,
    time_out,
    DATE(date) as date,
    CASE 
        WHEN time_out IS NOT NULL THEN 'Completed'
        ELSE 'Active'
    END as status
FROM sit_in
WHERE idno = ?
ORDER BY date DESC, time_in DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id_no);
$stmt->execute();
$result = $stmt->get_result();

// Add debug output to verify filtering
if ($result->num_rows === 0) {
    echo "<!-- Debug: No records found for user ID: " . htmlspecialchars($user_id_no) . " -->";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-In Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<!-- Add navigation bar -->
<nav class="navbar">
    <div class="navbar-title">Dashboard</div>
    <ul class="nav-links">
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="#">Notifications</a></li>
        <li><a href="editprofile.php">Edit Profile</a></li>
        <li><a href="history.php">History</a></li>
        <li><a href="#">Reservation</a></li>
    </ul>
    <a href="logout.php" class="logout-btn">Log Out</a>
</nav>

<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold text-center mb-4">CCS SIT-IN MONITORING SYSTEM</h2>
    <h4 class="text-xl font-semibold mb-6">📜 HISTORY INFORMATION</h4>

    <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-800">
            <thead class="text-xs text-white uppercase bg-blue-600">
                <tr>
                    <th class="px-6 py-3">ID Number</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Purpose</th>
                    <th class="px-6 py-3">Laboratory</th>
                    <th class="px-6 py-3">Time In</th>
                    <th class="px-6 py-3">Time Out</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $statusColor = $row['status'] === 'Completed' ? 'text-green-600' : 'text-yellow-600';
                        echo "<tr class='bg-white border-b hover:bg-gray-50'>
                            <td class='px-6 py-4'>{$row['idno']}</td>
                            <td class='px-6 py-4'>{$row['fullname']}</td>
                            <td class='px-6 py-4'>{$row['purpose']}</td>
                            <td class='px-6 py-4'>{$row['laboratory']}</td>
                            <td class='px-6 py-4'>{$row['time_in']}</td>
                            <td class='px-6 py-4'>{$row['time_out']}</td>
                            <td class='px-6 py-4'>{$row['date']}</td>
                            <td class='px-6 py-4 font-medium {$statusColor}'>{$row['status']}</td>
                            <td class='px-6 py-4'>
                                <button class='feedback-btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded' 
                                        data-sitinid='{$row['id']}'>
                                    Feedback
                                </button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='px-6 py-4 text-center'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Update the modal HTML with star rating -->
<div id="feedbackModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Feedback Form</h3>
            
            <!-- Add star rating system -->
            <div class="flex justify-center items-center space-x-1 my-4">
                <div class="rating">
                    <?php for($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" class="hidden" />
                    <label for="star<?= $i ?>" class="cursor-pointer text-3xl text-gray-300 hover:text-yellow-400 peer-checked:text-yellow-400">★</label>
                    <?php endfor; ?>
                </div>
            </div>
            
            <div class="mt-2 px-7 py-3">
                <textarea id="feedbackText" class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-blue-500" rows="4" placeholder="Enter your feedback here..."></textarea>
            </div>
            <div class="items-center px-4 py-3">
                <button id="submitFeedback" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Submit Feedback
                </button>
                <button id="closeFeedback" class="mt-3 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Update the JavaScript for handling ratings -->
<script>
    // Add styles for star rating
    const style = document.createElement('style');
    style.textContent = `
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
        }
        .rating input:checked ~ label,
        .rating label:hover,
        .rating label:hover ~ label {
            color: #FBBF24;
        }
    `;
    document.head.appendChild(style);

    // Get all feedback buttons
    document.querySelectorAll('.feedback-btn').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('feedbackModal').classList.remove('hidden');
        });
    });

    // Close modal when clicking close button
    document.getElementById('closeFeedback').addEventListener('click', () => {
        document.getElementById('feedbackModal').classList.add('hidden');
        resetFeedbackForm();
    });

    // Close modal when clicking outside
    document.getElementById('feedbackModal').addEventListener('click', (e) => {
        if (e.target.id === 'feedbackModal') {
            document.getElementById('feedbackModal').classList.add('hidden');
            resetFeedbackForm();
        }
    });

    // Replace the existing submitFeedback event listener
    document.getElementById('submitFeedback').addEventListener('click', () => {
        const feedback = document.getElementById('feedbackText').value;
        const rating = document.querySelector('input[name="rating"]:checked')?.value;
        const sitInId = document.querySelector('.feedback-btn[data-sitinid]').dataset.sitinid;

        if (!rating) {
            alert('Please select a rating');
            return;
        }

        if (!feedback.trim()) {
            alert('Please enter your feedback');
            return;
        }

        // Submit feedback using fetch
        fetch('submit_feedback.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                sit_in_id: sitInId,
                rating: rating,
                feedback_text: feedback
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Feedback submitted successfully!');
                document.getElementById('feedbackModal').classList.add('hidden');
                resetFeedbackForm();
            } else {
                alert(data.message || 'Error submitting feedback');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error submitting feedback');
        });
    });

    // Function to reset the feedback form
    function resetFeedbackForm() {
        document.getElementById('feedbackText').value = '';
        const checkedStar = document.querySelector('input[name="rating"]:checked');
        if (checkedStar) {
            checkedStar.checked = false;
        }
    }
</script>

</body>
</html>

<?php
$conn->close();
?>
