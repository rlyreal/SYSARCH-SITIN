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
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex justify-center gap-4 mb-6">
                    <img src="University-of-Cebu-Logo.jpg" alt="Logo" class="h-20">
                    <img src="ccs.png" alt="Logo1" class="h-20">
                </div>
                <h2 class="card-title text-2xl font-bold text-center mb-6">CCS Sitin Monitoring System</h2>
                
                <form id="loginForm" class="space-y-4">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="username" placeholder="Username" 
                               class="input input-bordered w-full pl-10" required>
                    </div>
                    
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" placeholder="Password" 
                               class="input input-bordered w-full pl-10" required>
                    </div>

                    <div class="card-actions justify-end mt-6">
                        <button type="submit" class="btn btn-primary bg-[#2c343c] hover:bg-[#363e46] border-none">
                            Login
                        </button>
                        <button type="button" id="signUpButton" 
                                class="btn bg-gray-200 hover:bg-gray-300 text-gray-800 border-none">
                            Register
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("id_no").addEventListener("input", function (event) {
            this.value = this.value.replace(/\D/g, ''); 
            if (this.value.length > 8) {
                this.value = this.value.slice(0, 8);
            }
        });

        function validateText(input) {
            input.value = input.value.replace(/[^A-Za-z ]/g, ''); 
        }
    </script>

    <script src="script.js"></script>
</body>
</html>
