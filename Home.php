<?php
// Start session


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="icon" type="image/png" href="images/ccswb.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<title>CCS | Home</title>
<style>
.glass-effect {
background: rgba(255, 255, 255, 0.15);
backdrop-filter: blur(10px);
-webkit-backdrop-filter: blur(10px);
border: 1px solid rgba(255, 255, 255, 0.2);
}

.particle {
position: absolute;
border-radius: 50%;
background: rgba(255, 255, 255, 0.2);
animation: float 15s infinite ease-in-out;
}

.particle:nth-child(1) {
width: 80px;
height: 80px;
left: 10%;
top: 20%;
animation-delay: 0s;
animation-duration: 18s;
}

.particle:nth-child(2) {
width: 120px;
height: 120px;
left: 25%;
top: 60%;
animation-delay: 1s;
animation-duration: 22s;
}

.particle:nth-child(3) {
width: 60px;
height: 60px;
left: 40%;
top: 15%;
animation-delay: 2s;
animation-duration: 16s;
}

.particle:nth-child(4) {
width: 100px;
height: 100px;
left: 60%;
top: 70%;
animation-delay: 0.5s;
animation-duration: 20s;
}

.particle:nth-child(5) {
width: 70px;
height: 70px;
left: 75%;
top: 25%;
animation-delay: 1.5s;
animation-duration: 19s;
}

.particle:nth-child(6) {
width: 90px;
height: 90px;
left: 85%;
top: 50%;
animation-delay: 3s;
animation-duration: 21s;
}

.particle:nth-child(7) {
width: 65px;
height: 65px;
left: 50%;
top: 40%;
animation-delay: 2.5s;
animation-duration: 17s;
}

.particle:nth-child(8) {
width: 110px;
height: 110px;
left: 15%;
top: 80%;
animation-delay: 1.2s;
animation-duration: 23s;
}

@keyframes float {
0%, 100% {
transform: translateY(0) translateX(0);
}
25% {
transform: translateY(-20px) translateX(10px);
}
50% {
transform: translateY(10px) translateX(-15px);
}
75% {
transform: translateY(15px) translateX(20px);
}
}

.feature-card:hover {
transform: translateY(-5px);
box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.announcement-card:hover {
transform: scale(1.01);
}
</style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
<!-- Hero Section with Blurred Glass Navbar -->
<div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-800 h-[500px]">
<!-- Animated Particles Background -->
<div class="absolute inset-0 overflow-hidden">
<div class="particles-container">
<div class="particle"></div>
<div class="particle"></div>
<div class="particle"></div>
<div class="particle"></div>
<div class="particle"></div>
<div class="particle"></div>
<div class="particle"></div>
<div class="particle"></div>
</div>
</div>

<!-- Glass Navbar -->
<nav class="glass-effect fixed w-full z-10 py-4">
<div class="container mx-auto px-4">
<div class="flex justify-between items-center">
<div class="flex items-center">
<img src="images/ccswb.png" alt="CCS Logo" class="h-10 w-auto mr-3">
<span class="text-white text-xl font-bold">College of Computer Studies</span>
</div>
<div class="hidden md:flex items-center space-x-6">
<a href="#" class="text-white hover:text-blue-200 transition duration-300 font-medium">Home</a>
<a href="#" class="text-white hover:text-blue-200 transition duration-300 font-medium">Students</a>
<a href="#" class="text-white hover:text-blue-200 transition duration-300 font-medium">Programs</a>
<a href="#" class="text-white hover:text-blue-200 transition duration-300 font-medium">About</a>
<a href="#" class="text-white hover:text-blue-200 transition duration-300 font-medium">Contact</a>
<a href="index.php" class="bg-white text-blue-600 hover:bg-blue-100 px-4 py-2 rounded-lg font-medium transition duration-300">Login</a>
</div>
<button class="md:hidden text-white focus:outline-none">
<i class="fas fa-bars text-2xl"></i>
</button>
</div>
</div>
</nav>

<!-- Hero Content -->
<div class="container mx-auto px-4 h-full flex items-center relative z-1">
<div class="max-w-3xl animate__animated animate__fadeInUp">
<h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight">
Student Information Management System
</h1>
<p class="text-xl text-blue-100 mt-6 max-w-2xl">
Streamlined student management platform for the College of Computer Studies.
Track academic progress and manage student records efficiently.
</p>
<div class="mt-8 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
<a href="Students.php" class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-lg font-medium transition duration-300 text-center">
Student Database
</a>
<a href="#learn-more" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-600 px-6 py-3 rounded-lg font-medium transition duration-300 text-center">
Learn More
</a>
</div>
</div>
</div>

<!-- Wave Divider -->
<div class="absolute bottom-0 left-0 right-0">
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 220">
<path fill="#ffffff" fill-opacity="1" d="M0,128L48,117.3C96,107,192,85,288,90.7C384,96,480,128,576,149.3C672,171,768,181,864,170.7C960,160,1056,128,1152,122.7C1248,117,1344,139,1392,149.3L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
</svg>
</div>
</div>

<!-- Main Content Section -->
<div class="container mx-auto px-4 py-12" id="learn-more">
<!-- System Overview Section -->
<div class="text-center mb-16">
<h2 class="text-3xl font-bold text-gray-800 mb-2">System Overview</h2>
<p class="text-gray-600 max-w-2xl mx-auto">Our comprehensive student information management system provides all the tools needed to efficiently manage student records and academic data.</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
<!-- Students Card -->
<div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-blue-500 hover:shadow-lg transition duration-300">
<div class="flex items-center justify-center mb-4">
<div class="rounded-full bg-blue-100 p-3">
<i class="fas fa-user-graduate text-blue-500 text-2xl"></i>
</div>
</div>
<h3 class="text-xl font-semibold text-center text-gray-800">Students</h3>
<p class="text-3xl font-bold text-center text-blue-600 mt-2">1,248</p>
<p class="text-gray-500 text-center mt-2">Currently enrolled</p>
</div>

<!-- Programs Card -->
<div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-green-500 hover:shadow-lg transition duration-300">
<div class="flex items-center justify-center mb-4">
<div class="rounded-full bg-green-100 p-3">
<i class="fas fa-book text-green-500 text-2xl"></i>
</div>
</div>
<h3 class="text-xl font-semibold text-center text-gray-800">Programs</h3>
<p class="text-3xl font-bold text-center text-green-600 mt-2">5</p>
<p class="text-gray-500 text-center mt-2">Degree programs</p>
</div>

<!-- Faculty Card -->
<div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-purple-500 hover:shadow-lg transition duration-300">
<div class="flex items-center justify-center mb-4">
<div class="rounded-full bg-purple-100 p-3">
<i class="fas fa-chalkboard-teacher text-purple-500 text-2xl"></i>
</div>
</div>
<h3 class="text-xl font-semibold text-center text-gray-800">Faculty</h3>
<p class="text-3xl font-bold text-center text-purple-600 mt-2">42</p>
<p class="text-gray-500 text-center mt-2">Expert instructors</p>
</div>

<!-- Courses Card -->
<div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-yellow-500 hover:shadow-lg transition duration-300">
<div class="flex items-center justify-center mb-4">
<div class="rounded-full bg-yellow-100 p-3">
<i class="fas fa-laptop-code text-yellow-500 text-2xl"></i>
</div>
</div>
<h3 class="text-xl font-semibold text-center text-gray-800">Courses</h3>
<p class="text-3xl font-bold text-center text-yellow-600 mt-2">87</p>
<p class="text-gray-500 text-center mt-2">Available courses</p>
</div>
</div>

<!-- Features Section -->
<div class="mb-16">
<h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Key Features</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
<!-- Feature 1 -->
<div class="bg-white p-6 rounded-xl shadow-md feature-card transition duration-300 hover:shadow-lg">
<div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
<i class="fas fa-database text-blue-600 text-2xl"></i>
</div>
<h3 class="text-xl font-semibold text-gray-800 mb-2">Student Management</h3>
<p class="text-gray-600">Comprehensive database for tracking student information, academic history, and personal details.</p>
</div>

<!-- Feature 2 -->
<div class="bg-white p-6 rounded-xl shadow-md feature-card transition duration-300 hover:shadow-lg">
<div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center mb-4">
<i class="fas fa-chart-line text-green-600 text-2xl"></i>
</div>
<h3 class="text-xl font-semibold text-gray-800 mb-2">Performance Tracking</h3>
<p class="text-gray-600">Monitor academic progress, attendance records, and performance metrics for each student.</p>
</div>

<!-- Feature 3 -->
<div class="bg-white p-6 rounded-xl shadow-md feature-card transition duration-300 hover:shadow-lg">
<div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
<i class="fas fa-clipboard-list text-purple-600 text-2xl"></i>
</div>
<h3 class="text-xl font-semibold text-gray-800 mb-2">Course Registration</h3>
<p class="text-gray-600">Streamlined process for enrolling students in courses, managing class rosters and schedules.</p>
</div>

<!-- Feature 4 -->
<div class="bg-white p-6 rounded-xl shadow-md feature-card transition duration-300 hover:shadow-lg">
<div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center mb-4">
<i class="fas fa-file-alt text-red-600 text-2xl"></i>
</div>
<h3 class="text-xl font-semibold text-gray-800 mb-2">Report Generation</h3>
<p class="text-gray-600">Create detailed reports on student performance, class statistics, and enrollment figures.</p>
</div>

<!-- Feature 5 -->
<div class="bg-white p-6 rounded-xl shadow-md feature-card transition duration-300 hover:shadow-lg">
<div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
<i class="fas fa-bell text-yellow-600 text-2xl"></i>
</div>
<h3 class="text-xl font-semibold text-gray-800 mb-2">Announcement System</h3>
<p class="text-gray-600">Centralized platform for sharing important notifications, updates and announcements.</p>
</div>

<!-- Feature 6 -->
<div class="bg-white p-6 rounded-xl shadow-md feature-card transition duration-300 hover:shadow-lg">
<div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
<i class="fas fa-shield-alt text-indigo-600 text-2xl"></i>
</div>
<h3 class="text-xl font-semibold text-gray-800 mb-2">Secure Access</h3>
<p class="text-gray-600">Role-based access controls ensuring data privacy and information security.</p>
</div>
</div>
</div>

<!-- Announcements Section -->
<div>
<h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Latest Announcements</h2>
<div class="grid grid-cols-1 gap-6">
<?php if (!empty($announcements)): ?>
<?php foreach ($announcements as $announcement): ?>
<div class="bg-white rounded-xl shadow-md overflow-hidden announcement-card transition duration-300">
<div class="md:flex">
<div class="p-6 flex-1">
<div class="flex items-center mb-2">
<span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full mr-2">
<?php echo date('M d, Y', strtotime($announcement['created_at'])); ?>
</span>
<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
<?php echo htmlspecialchars($announcement['category']); ?>
</span>
</div>
<h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($announcement['title']); ?></h3>
<p class="text-gray-600 mb-4"><?php echo htmlspecialchars($announcement['content']); ?></p>
<div class="flex items-center text-sm text-gray-500">
<i class="fas fa-user-circle mr-1"></i>
<span>Posted by: <?php echo htmlspecialchars($announcement['posted_by']); ?></span>
</div>
</div>
<?php if (!empty($announcement['image'])): ?>
<div class="md:w-48 flex-shrink-0">
<img src="<?php echo htmlspecialchars($announcement['image']); ?>" alt="Announcement image" class="h-full w-full object-cover">
</div>
<?php endif; ?>
</div>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="bg-white rounded-xl shadow-md p-6 text-center">
<i class="fas fa-info-circle text-blue-500 text-4xl mb-4"></i>
<p class="text-gray-600">No announcements available at the moment.</p>
</div>
<?php endif; ?>
</div>
</div>
</div>

<!-- Call-to-Action Section -->
<div class="bg-gradient-to-r from-blue-500 to-indigo-700 py-16">
<div class="container mx-auto px-4 text-center">
<h2 class="text-3xl font-bold text-white mb-4">Ready to Get Started?</h2>
<p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">Experience the power of our student information management system. Login now to access all features.</p>
<a href="login.php" class="bg-white text-blue-600 hover:bg-blue-50 px-8 py-3 rounded-lg font-medium transition duration-300 inline-flex items-center">
Access Student Database
<i class="fas fa-arrow-right ml-2"></i>
</a>
</div>
</div>

<!-- Footer -->
<footer class="bg-gray-800 text-white pt-12 pb-8">
<div class="container mx-auto px-4">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
<div>
<div class="flex items-center mb-4">
<img src="images/ccswb.png" alt="CCS Logo" class="h-10 w-auto mr-3">
<span class="text-xl font-bold">College of Computer Studies</span>
</div>
<p class="text-gray-400 mb-4">Providing quality computer science and information technology education.</p>
<div class="flex space-x-4">
<a href="#" class="text-gray-400 hover:text-white transition duration-300">
<i class="fab fa-facebook-f"></i>
</a>
<a href="#" class="text-gray-400 hover:text-white transition duration-300">
<i class="fab fa-twitter"></i>
</a>
<a href="#" class="text-gray-400 hover:text-white transition duration-300">
<i class="fab fa-instagram"></i>
</a>
<a href="#" class="text-gray-400 hover:text-white transition duration-300">
<i class="fab fa-linkedin-in"></i>
</a>
</div>
</div>

<div>
<h3 class="text-lg font-semibold mb-4">Quick Links</h3>
<ul class="space-y-2">
<li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Home</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition duration-300">About Us</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Programs</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Students</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Contact</a></li>
</ul>
</div>

<div>
<h3 class="text-lg font-semibold mb-4">Resources</h3>
<ul class="space-y-2">
<li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Student Portal</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Faculty Directory</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Library</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Research</a></li>
<li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Help Center</a></li>
</ul>
</div>

<div>
<h3 class="text-lg font-semibold mb-4">Contact</h3>
<ul class="space-y-2 text-gray-400">
<li class="flex items-start">
<i class="fas fa-map-marker-alt mt-1 mr-3"></i>
<span>123 University Ave., Your City, State 12345</span>
</li>
<li class="flex items-center">
<i class="fas fa-phone mr-3"></i>
<span>(123) 456-7890</span>
</li>
<li class="flex items-center">
<i class="fas fa-envelope mr-3"></i>
<span>info@ccs-university.edu</span>
</li>
</ul>
</div>
</div>

<div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-400">
<p>&copy; <?php echo date('Y'); ?> College of Computer Studies. All rights reserved.</p>
</div>
</div>
</footer>

<script>
// Mobile menu toggle
document.querySelector('.md\\:hidden').addEventListener('click', function() {
const mobileMenu = document.createElement('div');
mobileMenu.className = 'fixed inset-0 bg-gray-900 bg-opacity-95 z-50 flex flex-col items-center justify-center';
mobileMenu.innerHTML = `
<button class="absolute top-6 right-6 text-white focus:outline-none">
<i class="fas fa-times text-2xl"></i>
</button>
<div class="flex flex-col space-y-6 items-center">
<a href="#" class="text-white text-xl hover:text-blue-300 transition duration-300">Home</a>
<a href="#" class="text-white text-xl hover:text-blue-300 transition duration-300">Students</a>
<a href="#" class="text-white text-xl hover:text-blue-300 transition duration-300">Programs</a>
<a href="#" class="text-white text-xl hover:text-blue-300 transition duration-300">About</a>
<a href="#" class="text-white text-xl hover:text-blue-300 transition duration-300">Contact</a>
<a href="login.php" class="mt-4 bg-white text-blue-600 hover:bg-blue-100 px-6 py-3 rounded-lg font-medium transition duration-300">Login</a>
</div>
`;
document.body.appendChild(mobileMenu);
document.body.style.overflow = 'hidden';

mobileMenu.querySelector('button').addEventListener('click', function() {
document.body.removeChild(mobileMenu);
document.body.style.overflow = '';
});
});

// Scroll animation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
anchor.addEventListener('click', function (e) {
e.preventDefault();
document.querySelector(this.getAttribute('href')).scrollIntoView({
behavior: 'smooth'
});
});
});
</script>
</body>
</html>