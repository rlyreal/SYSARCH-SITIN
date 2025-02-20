<?php 
    // PHP code for session handling (if necessary)
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register & Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<!-- Register Form -->
<div class="container" id="signup" style="display:none;">
    <h1 class="form-title">Register</h1>
    <form id="registerForm">
        <!-- ID Field -->
        <div class="input-group">
            <i class="far fa-id-card"></i>
            <input type="text" name="id" id="id_no" placeholder="IDNO" required minlength="8" maxlength="8" pattern="\d{8}">
        </div>
        <!-- Last Name Field -->
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="lName" id="last_name" placeholder="Last Name" required pattern="[A-Za-z ]+" oninput="validateText(this)">
        </div>
        <!-- First Name Field -->
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="fname" id="first_name" placeholder="First Name" required pattern="[A-Za-z ]+" oninput="validateText(this)">
        </div>
        <!-- Middle Name Field -->
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="mname" id="middle_name" placeholder="Middle Name" required pattern="[A-Za-z ]+" oninput="validateText(this)">
        </div>
        <!-- Course Dropdown -->
        <div class="input-group select-with-icon">
            <i class="fas fa-graduation-cap"></i>
            <select name="course" required>
                <option value="" disabled selected>Course</option>
                <option value="BSIT">BSIT (Information Technology)</option>
                <option value="BSCS">BSCS (Computer Science)</option>
                <option value="BSIS">BSIS (Information Systems)</option>
                <option value="BSCE">BSCE (Civil Engineering)</option>
                <option value="BSEE">BSEE (Electrical Engineering)</option>
            </select>
        </div>
        <!-- Year Level Dropdown -->
        <div class="input-group select-with-icon">
            <i class="fas fa-layer-group"></i>
            <select name="yearlevel" required>
                <option value="" disabled selected>Year Level</option>
                <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
                <option value="4th Year">4th Year</option>
            </select>
        </div>
        <!-- Email Field -->
        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" id="email" placeholder="Email" required>
        </div>
        <!-- Address Field -->
        <div class="input-group">
            <i class="fas fa-map-marker-alt"></i>
            <input type="text" name="address" id="address" placeholder="Address" required>
        </div>
        <!-- Username Field -->
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <!-- Password Field -->
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="button-container">
            <!-- Register Button -->
            <input type="submit" class="btn" value="Register">
            <!-- Sign In Button -->
            <button id="signInButton" type="button" class="btn">Sign In</button>
        </div>
    </form>
</div>

<!-- Login Form -->
<div class="container" id="signIn">
    <img src="University-of-Cebu-Logo.jpg" alt="Logo" class="logo-img">
    <img src="ccs.png" alt="Logo1" class="logo-img1">
    <h1 class="form-title">CCS Sitin Monitoring System</h1>
    <form id="loginForm">
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="button-container">
            <!-- Login Button -->
            <input type="submit" class="btn" value="Login">
            <!-- Register Button -->
            <button id="signUpButton" type="button" class="btn">Register</button>
        </div>
    </form>
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
