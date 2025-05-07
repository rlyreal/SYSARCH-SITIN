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
    DATE_FORMAT(time_in, '%h:%i %p') as time_in,
    DATE_FORMAT(time_out, '%h:%i %p') as time_out,
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
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            themes: ["light"],
            plugins: [require("daisyui")],
        }
    </script>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<!-- Add navigation bar -->
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
                <a href="user_reservation.php" class="btn btn-ghost text-white hover:bg-white/10">
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

<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <div class="flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h4 class="text-xl font-semibold">HISTORY INFORMATION</h4>
        </div>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <!-- Table head -->
                    <thead class="text-xs uppercase">
                        <tr class="bg-[#2c343c] text-white"> <!-- Changed to match navbar color -->
                            <th class="w-28">ID Number</th>
                            <th>Name</th>
                            <th>Purpose</th>
                            <th>Laboratory</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="w-24">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $statusBadge = $row['status'] === 'Completed' 
                                    ? 'badge bg-green-100 text-green-800 border-none' // Light green for completed
                                    : 'badge badge-warning';
                                ?>
                                <tr class="hover">
                                    <td class="font-mono"><?php echo $row['idno']; ?></td>
                                    <td><?php echo $row['fullname']; ?></td>
                                    <td><?php echo $row['purpose']; ?></td>
                                    <td>
                                        <div class="badge badge-ghost"><?php echo $row['laboratory']; ?></div>
                                    </td>
                                    <td class="font-mono"><?php echo date('h:i A', strtotime($row['time_in'])); ?></td>
                                    <td class="font-mono"><?php echo $row['time_out'] ? date('h:i A', strtotime($row['time_out'])) : '-'; ?></td>
                                    <td class="font-mono"><?php echo $row['date']; ?></td>
                                    <td>
                                        <div class="<?php echo $statusBadge; ?>">
                                            <?php echo $row['status']; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-xs feedback-btn" 
                                                data-sitinid="<?php echo $row['id']; ?>">
                                            Feedback
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <span class="font-medium">No records found</span>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
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
                // Create and show success popup
                const popup = document.createElement('div');
                popup.className = 'fixed inset-0 flex items-center justify-center z-50';
                popup.innerHTML = `
                    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
                    <div class="bg-white rounded-lg px-8 py-6 max-w-sm mx-auto relative transform transition-all">
                        <div class="flex items-center justify-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-center text-lg font-medium text-gray-900 mb-4">Success!</h3>
                        <p class="text-center text-gray-500 mb-6">Your feedback has been submitted successfully.</p>
                    </div>
                `;

                document.body.appendChild(popup);

                // Close feedback modal
                document.getElementById('feedbackModal').classList.add('hidden');
                resetFeedbackForm();

                // Remove success popup after 2 seconds
                setTimeout(() => {
                    popup.classList.add('opacity-0');
                    setTimeout(() => {
                        document.body.removeChild(popup);
                    }, 300);
                }, 2000);
            } else {
                // Create and show error popup
                const popup = document.createElement('div');
                popup.className = 'fixed inset-0 flex items-center justify-center z-50';
                popup.innerHTML = `
                    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
                    <div class="bg-white rounded-lg px-8 py-6 max-w-sm mx-auto relative transform transition-all">
                        <div class="flex items-center justify-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-center text-lg font-medium text-gray-900 mb-4">Error</h3>
                        <p class="text-center text-gray-500 mb-6">${data.message || 'Error submitting feedback'}</p>
                    </div>
                `;

                document.body.appendChild(popup);

                // Remove error popup after 3 seconds
                setTimeout(() => {
                    popup.classList.add('opacity-0');
                    setTimeout(() => {
                        document.body.removeChild(popup);
                    }, 300);
                }, 3000);
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

    // Update the logout button click handler
    document.querySelector('a[href="logout.php"]').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('logoutModal').classList.remove('hidden');
    });

    // Handle cancel button
    document.getElementById('cancelLogout').addEventListener('click', function() {
        document.getElementById('logoutModal').classList.add('hidden');
    });

    // Handle confirm logout
    document.getElementById('confirmLogout').addEventListener('click', function() {
        window.location.href = 'logout.php';
    });

    // Close modal when clicking outside
    document.getElementById('logoutModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
</script>

</body>
</html>

<?php
$conn->close();
?>
