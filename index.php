<?php 
    // PHP code for session handling (if necessary)
    session_start();
?>

<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>CCS Sitin Monitoring System</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap" rel="stylesheet" />
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/pages/page-auth.css" />
    
    <!-- Alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Helper JS -->
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/js/helpers.js"></script>
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/js/config.js"></script>

    <style>
        .app-brand-logo {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        .app-brand-logo img {
            height: 80px;
            width: auto;
        }
        .authentication-inner {
            max-width: 450px;
        }
        .authentication-inner.register {
            max-width: 700px;
        }
        .auth-footer-btn {
            gap: 1rem;
        }
    </style>
</head>

<body>
    <!-- Content -->
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <!-- Login -->
            <div class="authentication-inner" id="signIn">
                <!-- Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4 mt-2">
                            <div class="app-brand-logo">
                                <img src="University-of-Cebu-Logo.jpg" alt="UC Logo">
                                <img src="ccs.png" alt="CCS Logo">
                            </div>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2 text-center fw-bold">CCS Sitin Monitoring System</h4>
                        <p class="mb-4 text-center">Please sign-in to your account</p>

                        <form id="loginForm" class="mb-3">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="username"
                                        name="username"
                                        placeholder="Enter your username"
                                        autofocus
                                        required
                                    />
                                </div>
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                </div>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="password"
                                        name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password"
                                        required
                                    />
                                    <span class="input-group-text cursor-pointer toggle-password"><i class="bi bi-eye-slash"></i></span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                            </div>
                        </form>

                        <p class="text-center">
                            <span>New student?</span>
                            <a href="javascript:void(0);" id="signUpButton">
                                <span>Create an account</span>
                            </a>
                        </p>
                    </div>
                </div>
                <!-- /Card -->
            </div>
            <!-- /Login -->

            <!-- Register -->
            <div class="authentication-inner register" id="signup" style="display: none;">
                <!-- Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4 mt-2">
                            <div class="app-brand-logo">
                                <img src="University-of-Cebu-Logo.jpg" alt="UC Logo">
                                <img src="ccs.png" alt="CCS Logo">
                            </div>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2 text-center">Register your Account</h4>
                        <p class="mb-4 text-center">Complete the registration form below</p>

                        <form id="registerForm" class="mb-3">
                            <div class="row">
                                <!-- ID Number -->
                                <div class="col-12 mb-3">
                                    <label for="id_no" class="form-label">ID Number</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="id_no"
                                            name="id"
                                            placeholder="Enter your 8-digit ID number"
                                            required
                                            minlength="8"
                                            maxlength="8"
                                            pattern="\d{8}"
                                        />
                                    </div>
                                </div>
                                
                                <!-- Name Fields -->
                                <div class="col-md-4 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="last_name"
                                            name="lName"
                                            placeholder="Last name"
                                            required
                                        />
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="first_name"
                                            name="fname"
                                            placeholder="First name"
                                            required
                                        />
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="middle_name" class="form-label">Middle Name</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="middle_name"
                                            name="mname"
                                            placeholder="Middle name"
                                            required
                                        />
                                    </div>
                                </div>
                                
                                <!-- Course and Year Level -->
                                <div class="col-md-6 mb-3">
                                    <label for="course" class="form-label">Course</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                                        <select name="course" id="course" class="form-select" required>
                                            <option value="" disabled selected>Select your course</option>
                                            <option value="BSIT">BSIT (Information Technology)</option>
                                            <option value="BSCS">BSCS (Computer Science)</option>
                                            <option value="BSIS">BSIS (Information Systems)</option>
                                            <option value="BSCE">BSCE (Civil Engineering)</option>
                                            <option value="BSEE">BSEE (Electrical Engineering)</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="yearlevel" class="form-label">Year Level</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bi bi-layers"></i></span>
                                        <select name="yearlevel" id="yearlevel" class="form-select" required>
                                            <option value="" disabled selected>Select your year level</option>
                                            <option value="1st Year">1st Year</option>
                                            <option value="2nd Year">2nd Year</option>
                                            <option value="3rd Year">3rd Year</option>
                                            <option value="4th Year">4th Year</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Email and Address -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input
                                            type="email"
                                            class="form-control"
                                            id="email"
                                            name="email"
                                            placeholder="Enter your email"
                                            required
                                        />
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="address"
                                            name="address"
                                            placeholder="Enter your address"
                                            required
                                        />
                                    </div>
                                </div>
                                
                                <!-- Username and Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="register_username" class="form-label">Username</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="register_username"
                                            name="username"
                                            placeholder="Choose a username"
                                            required
                                        />
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="register_password" class="form-label">Password</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input
                                            type="password"
                                            class="form-control"
                                            id="register_password"
                                            name="password"
                                            placeholder="Create a password"
                                            required
                                        />
                                        <span class="input-group-text cursor-pointer register-toggle-password"><i class="bi bi-eye-slash"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" id="registerSubmitBtn" class="btn btn-primary me-2">Register</button>
                                <button type="button" id="signInButton" class="btn btn-secondary">Sign In</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Card -->
            </div>
            <!-- /Register -->
        </div>
    </div>
    <!-- / Content -->

    <!-- Core JS -->
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/popper/popper.js"></script>
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/js/bootstrap.js"></script>
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/js/menu.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Toggle password visibility
            document.querySelector('.toggle-password').addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            });
            
            document.querySelector('.register-toggle-password').addEventListener('click', function() {
                const passwordInput = document.getElementById('register_password');
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            });
            
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
                signInForm.style.display = "none";
                signUpForm.style.display = "block";
            });

            signInButton?.addEventListener("click", function () {
                signInForm.style.display = "block";
                signUpForm.style.display = "none";
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
                        // Use SweetAlert for success
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            text: 'Your account has been created successfully. You can now log in.',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false,
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            registerForm.reset();
                            signUpForm.style.display = "none";
                            signInForm.style.display = "block";
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            text: data.message,
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
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
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
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
                    if (data.status === "success") {
                        // Show loading animation with SweetAlert
                        Swal.fire({
                            title: data.role === 'admin' ? 'Welcome, Administrator!' : 'Hello, Student!',
                            text: 'Loading your dashboard...',
                            imageUrl: 'https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/img/illustrations/girl-doing-yoga-light.png',
                            imageWidth: 200,
                            imageHeight: 200,
                            imageAlt: 'Loading',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            timer: 1500,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        }).then(() => {
                            // Redirect after animation completes
                            if (data.role === "admin") {
                                window.location.href = "admin_dashboard.php";
                            } else if (data.role === "user") {
                                window.location.href = "dashboard.php";
                            }
                        });
                    } else {
                        // Show error with SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: data.message,
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            clearLoginInputs();
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
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