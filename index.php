<?php 
    // PHP code for session handling (if necessary)
    session_start();
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sitin Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .button-container {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1rem;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <!-- Register Form -->
    <div class="container max-w-md mx-auto hidden" id="signup">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl font-bold text-center mb-6">Register Account</h2>
                <form id="registerForm" class="space-y-4">
                    <!-- ID Field -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="far fa-id-card"></i>
                        </span>
                        <input type="text" name="id" id="id_no" placeholder="IDNO" 
                               class="input input-bordered w-full pl-10" required 
                               minlength="8" maxlength="8" pattern="\d{8}">
                    </div>

                    <!-- Names Section -->
                    <div class="grid grid-cols-1 gap-4">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" name="lName" id="last_name" placeholder="Last Name" 
                                   class="input input-bordered w-full pl-10" required>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" name="fname" id="first_name" placeholder="First Name" 
                                   class="input input-bordered w-full pl-10" required>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" name="mname" id="middle_name" placeholder="Middle Name" 
                                   class="input input-bordered w-full pl-10" required>
                        </div>
                    </div>

                    <!-- Course and Year Level -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                <i class="fas fa-graduation-cap"></i>
                            </span>
                            <select name="course" class="select select-bordered w-full pl-10" required>
                                <option value="" disabled selected>Course</option>
                                <option value="BSIT">BSIT (Information Technology)</option>
                                <option value="BSCS">BSCS (Computer Science)</option>
                                <option value="BSIS">BSIS (Information Systems)</option>
                                <option value="BSCE">BSCE (Civil Engineering)</option>
                                <option value="BSEE">BSEE (Electrical Engineering)</option>
                            </select>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                <i class="fas fa-layer-group"></i>
                            </span>
                            <select name="yearlevel" class="select select-bordered w-full pl-10" required>
                                <option value="" disabled selected>Year Level</option>
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                            </select>
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" placeholder="Email" 
                               class="input input-bordered w-full pl-10" required>
                    </div>

                    <!-- Address Field -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                        <input type="text" name="address" id="address" placeholder="Address" 
                               class="input input-bordered w-full pl-10" required>
                    </div>

                    <!-- Username Field -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="username" placeholder="Username" 
                               class="input input-bordered w-full pl-10" required>
                    </div>

                    <!-- Password Field -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" placeholder="Password" 
                               class="input input-bordered w-full pl-10" required>
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <button type="submit" id="registerSubmitBtn" 
                                class="btn bg-[#2c343c] hover:bg-[#363e46] text-white border-none">
                            Register
                        </button>
                        <button type="button" id="signInButton" 
                                class="btn bg-gray-200 hover:bg-gray-300 text-gray-800 border-none">
                            Sign In
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Login Form -->
    <div class="container max-w-md mx-auto" id="signIn">
        <div class="card bg-white shadow-2xl rounded-xl">
            <div class="card-body p-8">
                <!-- Logo Section -->
                <div class="flex justify-center items-center gap-6 mb-8">
                    <img src="University-of-Cebu-Logo.jpg" alt="UC Logo" class="h-24 w-auto">
                    <img src="ccs.png" alt="CCS Logo" class="h-24 w-auto">
                </div>
                
                <!-- Title -->
                <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">
                    CCS Sitin Monitoring System
                </h2>
                
                <!-- Login Form -->
                <form id="loginForm" class="space-y-6">
                    <!-- Username Input -->
                    <div class="space-y-2">
                        <label for="username" class="text-sm font-medium text-gray-700">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                name="username" 
                                id="username"
                                class="w-full pl-11 pr-4 py-3 text-gray-900 rounded-lg border border-gray-300 
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all
                                       placeholder:text-gray-400"
                                placeholder="Enter your username"
                                required
                            >
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-2">
                        <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input 
                                type="password" 
                                name="password" 
                                id="password"
                                class="w-full pl-11 pr-4 py-3 text-gray-900 rounded-lg border border-gray-300 
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all
                                       placeholder:text-gray-400"
                                placeholder="Enter your password"
                                required
                            >
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-4 pt-4">
                        <button 
                            type="submit"
                            class="px-6 py-3 bg-[#2c343c] text-white rounded-lg hover:bg-[#363e46] 
                                   focus:ring-4 focus:ring-gray-300 transition-all duration-200"
                        >
                            Sign In
                        </button>
                        <button 
                            type="button"
                            id="signUpButton"
                            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 
                                   focus:ring-4 focus:ring-gray-100 transition-all duration-200"
                        >
                            Register
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // ID number validation
            document.getElementById("id_no").addEventListener("input", function (event) {
                this.value = this.value.replace(/\D/g, ''); 
                if (this.value.length > 8) {
                    this.value = this.value.slice(0, 8);
                }
            });

            function validateText(input) {
                input.value = input.value.replace(/[^A-Za-z ]/g, ''); 
            }

            // Form switching functionality
            const signUpButton = document.getElementById("signUpButton");
            const signInButton = document.getElementById("signInButton");
            const signInForm = document.getElementById("signIn");
            const signUpForm = document.getElementById("signup");

            signUpButton?.addEventListener("click", function () {
                signInForm.classList.add("hidden");
                signUpForm.classList.remove("hidden");
            });

            signInButton?.addEventListener("click", function () {
                signInForm.classList.remove("hidden");
                signUpForm.classList.add("hidden");
            });

            // Register form submission
            const registerForm = document.getElementById("registerForm");
            registerForm?.addEventListener("submit", function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch("register.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        // Create and insert popup
                        const popup = document.createElement('div');
                        popup.innerHTML = `
                            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                <!-- Backdrop -->
                                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
                                
                                <!-- Popup Content -->
                                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6 opacity-0 translate-y-4 scale-95">
                                    <div>
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-5">
                                            <h3 class="text-xl font-semibold leading-6 text-gray-900 mb-2">
                                                Registration Successful!
                                            </h3>
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-500">
                                                    Your account has been created successfully. You can now log in.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        document.body.appendChild(popup);

                        // Animate popup entrance
                        requestAnimationFrame(() => {
                            const content = popup.querySelector('.bg-white');
                            content.classList.remove('opacity-0', 'translate-y-4', 'scale-95');
                            content.classList.add('opacity-100', 'translate-y-0', 'scale-100', 'transition-all', 'duration-300');
                        });

                        // Remove popup after delay
                        setTimeout(() => {
                            popup.querySelector('.bg-white').classList.add('opacity-0', 'translate-y-4', 'scale-95');
                            setTimeout(() => {
                                popup.remove();
                                registerForm.reset();
                                signUpForm.classList.add("hidden");
                                signInForm.classList.remove("hidden");
                            }, 300);
                        }, 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            text: data.message,
                            customClass: {
                                popup: 'bg-white rounded-lg shadow-xl',
                                title: 'text-xl font-bold text-gray-900',
                                text: 'text-gray-600',
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred during registration.',
                        customClass: {
                            popup: 'bg-white rounded-lg shadow-xl',
                            title: 'text-xl font-bold text-gray-900',
                            text: 'text-gray-600',
                        }
                    });
                });
            });

            // Login form submission
            document.getElementById("loginForm").addEventListener("submit", function (e) {
                e.preventDefault();
                let formData = new FormData(this);

                fetch("login.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Replace the success popup in the login form submission with this:
                    if (data.status === "success") {
                        const popup = document.createElement('div');
                        popup.innerHTML = `
                            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                <!-- Backdrop -->
                                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
                                
                                <!-- Popup Content -->
                                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6 opacity-0 translate-y-4 scale-95">
                                    <div>
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full ${data.role === 'admin' ? 'bg-blue-100' : 'bg-green-100'}">
                                            <!-- Loading Animation -->
                                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 ${data.role === 'admin' ? 'border-blue-600' : 'border-green-600'}"></div>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-5">
                                            <h3 class="text-xl font-semibold leading-6 text-gray-900 mb-2">
                                                ${data.role === 'admin' ? 'Welcome, Administrator!' : 'Hello, Student!'}
                                            </h3>
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-500">
                                                    Loading your dashboard...
                                                </p>
                                            </div>
                                            <!-- Progress bar -->
                                            <div class="mt-4">
                                                <div class="h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                                                    <div class="h-full ${data.role === 'admin' ? 'bg-blue-500' : 'bg-green-500'} rounded-full animate-load-progress"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        // Add the progress bar animation style
                        const style = document.createElement('style');
                        style.textContent = `
                            @keyframes load-progress {
                                0% { width: 0; }
                                100% { width: 100%; }
                            }
                            .animate-load-progress {
                                animation: load-progress 1.5s linear;
                            }
                        `;
                        document.head.appendChild(style);

                        document.body.appendChild(popup);

                        // Animate popup entrance
                        requestAnimationFrame(() => {
                            const content = popup.querySelector('.bg-white');
                            content.classList.remove('opacity-0', 'translate-y-4', 'scale-95');
                            content.classList.add('opacity-100', 'translate-y-0', 'scale-100', 'transition-all', 'duration-300');
                        });

                        // Redirect after animation completes
                        setTimeout(() => {
                            popup.querySelector('.bg-white').classList.add('opacity-0', 'translate-y-4', 'scale-95');
                            setTimeout(() => {
                                popup.remove();
                                if (data.role === "admin") {
                                    window.location.href = "admin_dashboard.php";
                                } else if (data.role === "user") {
                                    window.location.href = "dashboard.php";
                                }
                            }, 300);
                        }, 1500);
                    } else {
                        // Show error popup
                        const errorPopup = document.createElement('div');
                        errorPopup.innerHTML = `
                            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
                                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6 opacity-0 translate-y-4 scale-95">
                                    <div>
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-5">
                                            <h3 class="text-xl font-semibold leading-6 text-gray-900 mb-2">
                                                Login Failed
                                            </h3>
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-500">
                                                    ${data.message}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        document.body.appendChild(errorPopup);

                        requestAnimationFrame(() => {
                            const content = errorPopup.querySelector('.bg-white');
                            content.classList.remove('opacity-0', 'translate-y-4', 'scale-95');
                            content.classList.add('opacity-100', 'translate-y-0', 'scale-100', 'transition-all', 'duration-300');
                        });

                        setTimeout(() => {
                            errorPopup.querySelector('.bg-white').classList.add('opacity-0', 'translate-y-4', 'scale-95');
                            setTimeout(() => {
                                errorPopup.remove();
                                clearLoginInputs();
                            }, 300);
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                });
            });

            function clearLoginInputs() {
                let usernameField = document.querySelector("#loginForm input[name='username']");
                let passwordField = document.querySelector("#loginForm input[name='password']");

                usernameField.value = "";
                passwordField.value = "";
                passwordField.type = "text";
                setTimeout(() => passwordField.type = "password", 10);
                setTimeout(() => usernameField.focus(), 100);
            }
        });
    </script>
</body>
</html>
