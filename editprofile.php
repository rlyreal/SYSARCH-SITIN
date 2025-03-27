<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, id_no, last_name, first_name, middle_name, course, year_level, email, address, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_number = $row['id_no'];  
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $middle_name = $row['middle_name'];
    $course = $row['course'];
    $year_level = $row['year_level'];
    $email = $row['email'];
    $address = $row['address'];
    $profile_picture = !empty($row['profile_picture']) ? $row['profile_picture'] : 'default_profile.png';
} else {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="editprofile.css">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            themes: ["light"],
            plugins: [require("daisyui")],
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const profileInput = document.getElementById("profile_picture");
        const profilePreview = document.getElementById("profile_preview");
        const profileContainer = document.querySelector(".profile-container");

        profileContainer.addEventListener("click", function () {
            profileInput.click();
        });

        profileInput.addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (file) {
                profilePreview.src = URL.createObjectURL(file);
            }
        });

        // SweetAlert2 for success message with redirect
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has("message")) {
            Swal.fire({
                title: "Success!",
                text: urlParams.get("message"),
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "dashboard.php"; // Redirect to dashboard
            });

            // Remove the message from URL after showing the alert
            window.history.replaceState(null, "", window.location.pathname);
        }
    });
    </script>
    <style>
        #success_toast {
            display: none;
            opacity: 1;
            transition: opacity 0.3s ease-in-out;
        }
    </style>
</head>
<body>
    <!-- Add this toast container after body tag -->
    <div class="toast toast-end z-50" id="success_toast">
        <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span id="toast_message"></span>
        </div>
    </div>
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

    <div class="container mx-auto px-4 py-6 max-w-2xl"> <!-- Changed from max-w-xl and increased padding -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-header bg-[#2c343c] px-3 py-1.5"> <!-- Reduced padding -->
                <h2 class="card-title text-white text-sm">Edit Profile</h2> <!-- Smaller text -->
            </div>
            
            <div class="card-body p-4"> <!-- Increased padding -->
                <form action="updateprofile.php" method="POST" enctype="multipart/form-data" class="space-y-4"> <!-- Increased spacing -->
                    <!-- Profile Picture Upload -->
                    <div class="flex justify-center mb-3"> <!-- Increased margin -->
                        <div class="avatar cursor-pointer hover:opacity-80 transition-opacity" onclick="document.getElementById('profile_picture').click()">
                            <div class="w-24 h-24 rounded-full ring ring-[#2c343c] ring-offset-2"> <!-- Larger avatar -->
                                <img id="profile_preview" src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile"/>
                            </div>
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3"> <!-- Increased gap -->
                        <!-- ID Number -->
                        <div class="form-control">
                            <label class="label py-1"> <!-- Increased label padding -->
                                <span class="label-text text-sm">ID Number</span> <!-- Larger label text -->
                            </label>
                            <input type="text" id="id_number" name="id_number" 
                                   value="<?php echo htmlspecialchars($id_number); ?>" 
                                   class="input input-bordered input-sm w-full" readonly> <!-- Changed to input-sm -->
                        </div>

                        <!-- Course -->
                        <div class="form-control">
                            <label class="label py-1"> <!-- Increased label padding -->
                                <span class="label-text text-sm">Course</span> <!-- Larger label text -->
                            </label>
                            <select id="course" name="course" class="select select-bordered select-sm w-full"> <!-- Changed to select-sm -->
                                <option value="BSIT" <?php echo ($course == "BSIT") ? 'selected' : ''; ?>>BSIT</option>
                                <option value="BSCS" <?php echo ($course == "BSCS") ? 'selected' : ''; ?>>BSCS</option>
                                <option value="BSECE" <?php echo ($course == "BSECE") ? 'selected' : ''; ?>>BSECE</option>
                                <option value="BSIS" <?php echo ($course == "BSIS") ? 'selected' : ''; ?>>BSIS</option>
                            </select>
                        </div>

                        <!-- Last Name -->
                        <div class="form-control">
                            <label class="label py-1"> <!-- Increased label padding -->
                                <span class="label-text text-sm">Last Name</span> <!-- Larger label text -->
                            </label>
                            <input type="text" id="last_name" name="last_name" 
                                   value="<?php echo htmlspecialchars($last_name); ?>" 
                                   class="input input-bordered input-sm w-full" 
                                   pattern="[A-Za-z\s]+" required> <!-- Changed to input-sm -->
                        </div>

                        <!-- First Name -->
                        <div class="form-control">
                            <label class="label py-1"> <!-- Increased label padding -->
                                <span class="label-text text-sm">First Name</span> <!-- Larger label text -->
                            </label>
                            <input type="text" id="first_name" name="first_name" 
                                   value="<?php echo htmlspecialchars($first_name); ?>" 
                                   class="input input-bordered input-sm w-full" 
                                   pattern="[A-Za-z\s]+" required> <!-- Changed to input-sm -->
                        </div>

                        <!-- Middle Name -->
                        <div class="form-control">
                            <label class="label py-1"> <!-- Increased label padding -->
                                <span class="label-text text-sm">Middle Name</span> <!-- Larger label text -->
                            </label>
                            <input type="text" id="middle_name" name="middle_name" 
                                   value="<?php echo htmlspecialchars($middle_name); ?>" 
                                   class="input input-bordered input-sm w-full" 
                                   pattern="[A-Za-z\s]+"> <!-- Changed to input-sm -->
                        </div>

                        <!-- Year Level -->
                        <div class="form-control">
                            <label class="label py-1">
                                <span class="label-text text-sm">Year Level</span>
                            </label>
                            <select id="course_level" name="course_level" class="select select-bordered select-sm w-full">
                                <option value="1" <?php echo ($year_level == 1) ? 'selected' : ''; ?>>1st Year</option>
                                <option value="2" <?php echo ($year_level == 2) ? 'selected' : ''; ?>>2nd Year</option>
                                <option value="3" <?php echo ($year_level == 3) ? 'selected' : ''; ?>>3rd Year</option>
                                <option value="4" <?php echo ($year_level == 4) ? 'selected' : ''; ?>>4th Year</option>
                            </select>
                        </div>
                    </div>

                    <!-- Full Width Fields -->
                    <div class="space-y-3"> <!-- Reduced spacing -->
                        <div class="form-control">
                            <label class="label py-1"> <!-- Increased label padding -->
                                <span class="label-text text-sm">Email</span> <!-- Larger label text -->
                            </label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($email); ?>" 
                                   class="input input-bordered input-sm w-full"> <!-- Changed to input-sm -->
                        </div>

                        <div class="form-control">
                            <label class="label py-1"> <!-- Increased label padding -->
                                <span class="label-text text-sm">Address</span> <!-- Larger label text -->
                            </label>
                            <input type="text" id="address" name="address" 
                                   value="<?php echo htmlspecialchars($address); ?>" 
                                   class="input input-bordered input-sm w-full"> <!-- Changed to input-sm -->
                        </div>
                    </div>

                    <!-- Replace the existing save changes button and add this modal -->
                    <div class="card-actions justify-end mt-4">
                        <button type="button" id="saveButton" class="btn btn-primary btn-sm">Save Changes</button>
                        <a href="dashboard.php" class="btn btn-ghost btn-sm">Cancel</a>
                    </div>

                    <!-- Add this modal dialog -->
                    <div id="confirmModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                            <div class="mt-3 text-center">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Save Changes?</h3>
                                <div class="mt-2 px-7 py-3">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to save these changes to your profile?
                                    </p>
                                </div>
                                <div class="flex justify-end gap-2 mt-4">
                                    <button id="closeModal" type="button" 
                                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-md">
                                        Cancel
                                    </button>
                                    <button id="confirmSave" type="button" 
                                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add this script for modal functionality -->
                    <script>
                        const modal = document.getElementById('confirmModal');
                        const saveButton = document.getElementById('saveButton');
                        const closeModal = document.getElementById('closeModal');
                        const confirmSave = document.getElementById('confirmSave');
                        const form = document.querySelector('form');

                        // Show modal
                        saveButton.addEventListener('click', () => {
                            modal.classList.remove('hidden');
                            // Prevent body scrolling when modal is open
                            document.body.style.overflow = 'hidden';
                        });

                        // Hide modal
                        closeModal.addEventListener('click', () => {
                            modal.classList.add('hidden');
                            document.body.style.overflow = 'auto';
                        });

                        // Click outside to close
                        modal.addEventListener('click', (e) => {
                            if (e.target === modal) {
                                modal.classList.add('hidden');
                                document.body.style.overflow = 'auto';
                            }
                        });

                        // Handle form submission
                        confirmSave.addEventListener('click', () => {
                            form.submit();
                        });

                        // Show success message
                        const urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.has("message")) {
                            const successModal = document.createElement('div');
                            successModal.className = 'fixed inset-0 flex items-center justify-center z-50';
                            successModal.innerHTML = `
                                <div class="fixed inset-0 bg-gray-600 bg-opacity-50"></div>
                                <div class="relative bg-white rounded-lg px-8 py-6 max-w-sm mx-auto">
                                    <div class="flex items-center justify-center mb-4">
                                        <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <h3 class="text-center text-lg font-medium text-gray-900 mb-4">Success!</h3>
                                    <p class="text-center text-gray-500 mb-6">${urlParams.get("message")}</p>
                                </div>
                            `;
                            document.body.appendChild(successModal);

                            setTimeout(() => {
                                window.location.href = "dashboard.php";
                            }, 2000);

                            window.history.replaceState(null, "", window.location.pathname);
                        }
                    </script>
                </form>
            </div>
        </div>
    </div>

    <!-- Add this modal markup before closing body tag -->
    <dialog id="confirm_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Confirm Changes</h3>
            <p class="py-4">Are you sure you want to save these changes to your profile?</p>
            <div class="modal-action">
                <form method="dialog">
                    <button id="cancelBtn" class="btn btn-ghost btn-sm">Cancel</button>
                    <button id="confirmBtn" class="btn btn-primary btn-sm">Save Changes</button>
                </form>
            </div>
        </div>
    </dialog>

    <script>
        // Update the form submission handling
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent direct form submission
            const modal = document.getElementById('confirm_modal');
            modal.showModal();

            // Handle confirmation
            document.getElementById('confirmBtn').onclick = () => {
                this.submit(); // Submit the form
            };

            // Handle cancellation
            document.getElementById('cancelBtn').onclick = () => {
                modal.close();
            };
        });

        // Show success message using Daisy UI toast
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has("message")) {
            const toast = document.getElementById('success_toast');
            const toastMessage = document.getElementById('toast_message');
            
            toastMessage.textContent = urlParams.get("message");
            toast.style.display = 'flex';
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    window.location.href = "dashboard.php";
                }, 300);
            }, 2000);

            window.history.replaceState(null, "", window.location.pathname);
        }
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

    <script>
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
