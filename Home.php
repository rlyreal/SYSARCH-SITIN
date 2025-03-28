<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CCS Sit-in Monitoring System</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    @keyframes gradientAnimation {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
    }

    .animated-bg {
        background: linear-gradient(
            -45deg,
            #ffffff,
            #f3f4f6,
            #ffffff,
            #e5e7eb
        );
        background-size: 400% 400%;
        animation: gradientAnimation 15s ease infinite;
    }

    .floating {
        animation: floating 3s ease-in-out infinite;
    }

    @keyframes floating {
        0% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
        100% {
            transform: translateY(0px);
        }
    }
</style>
</head>

<body class="animated-bg min-h-screen">
<!-- Navbar -->
<nav class="bg-black/5 backdrop-blur-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="text-xl font-bold text-gray-900">Sit-in Monitoring</div>
            <div>
                <a href="index.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                    Login
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="relative min-h-screen flex items-center justify-center">
    <div class="absolute inset-0 bg-white/30 backdrop-blur-sm"></div>
    <div class="text-center px-4 relative z-10">
        <div class="floating">
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-8">CCS Sit-in Class Monitoring System</h1>
            <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">Streamline your classroom management with our advanced monitoring system.</p>
        </div>
        <a href="index.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
            Get Started
        </a>
    </div>
</div>

<!-- After Hero Section -->
<div class="py-24 bg-gradient-to-b from-white to-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1">
                <div class="text-base text-blue-600 font-semibold tracking-wide uppercase mb-2">Our Mission</div>
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl mb-4">
                    Transforming Classroom Management
                </h2>
                <p class="text-lg text-gray-600 mb-6">
                    We're dedicated to providing a seamless and efficient monitoring system that helps educational institutions track and manage classroom activities effectively.
                </p>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-gray-600">Real-time Updates</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-gray-600">Easy Integration</span>
                    </div>
                </div>
            </div>
            <div class="order-1 lg:order-2 relative group">
                <!-- Main Image -->
                <img src="ccs.png" 
                     alt="Desktop Computer Laboratory" 
                     class="rounded-2xl shadow-2xl w-full floating object-cover h-[500px]">
                
                <!-- Overlay with additional info -->
                <div class="absolute inset-0 bg-gray-900/0 group-hover:bg-gray-900/50 transition-all duration-300 rounded-2xl flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <div class="text-center text-white p-4">
                        <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 space-y-2">
                            <i class="fas fa-desktop text-3xl mb-2"></i>
                            <p class="text-sm mb-1">Modern PC Workstations</p>
                            <div class="flex justify-center space-x-4">
                                <span class="flex items-center text-xs">
                                    <i class="fas fa-monitor mr-1"></i> Monitor
                                </span>
                                <span class="flex items-center text-xs">
                                    <i class="fas fa-keyboard mr-1"></i> Keyboard
                                </span>
                                <span class="flex items-center text-xs">
                                    <i class="fas fa-mouse mr-1"></i> Mouse
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Decorative elements -->
                <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl"></div>
                <div class="absolute -top-4 -left-4 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl"></div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">System Features</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Real-time Monitoring Card -->
        <div class="bg-white shadow-xl p-6 transform hover:scale-105 transition-all duration-300 floating rounded-xl border border-gray-100">
            <div class="text-gray-900 text-4xl mb-4">
                <i class="fas fa-desktop"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Real-time Monitoring</h3>
            <p class="text-gray-600">Track classroom attendance and activities as they happen.</p>
        </div>

        <!-- Attendance Tracking Card -->
        <div class="bg-white shadow-xl p-6 transform hover:scale-105 transition-all duration-300 floating rounded-xl border border-gray-100" style="animation-delay: 0.2s">
            <div class="text-gray-900 text-4xl mb-4">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Attendance Tracking</h3>
            <p class="text-gray-600">Automated attendance system with detailed reporting.</p>
        </div>

        <!-- Analytics Card -->
        <div class="bg-white shadow-xl p-6 transform hover:scale-105 transition-all duration-300 floating rounded-xl border border-gray-100" style="animation-delay: 0.4s">
            <div class="text-gray-900 text-4xl mb-4">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Analytics</h3>
            <p class="text-gray-600">Comprehensive analytics and reporting system.</p>
        </div>
    </div>
</div>

<!-- Before Stats Section -->
<div class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">What Users Say</h2>
            <p class="text-lg text-gray-600">Discover how our system has improved classroom management for educators.</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="bg-gray-50 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center mb-6">
                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Prof. Sarah Johnson</h3>
                        <p class="text-gray-600">Computer Science Department</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">"This system has revolutionized how we track student attendance and participation in our laboratory sessions."</p>
            </div>

            <!-- Testimonial 2 -->
            <div class="bg-gray-50 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center mb-6">
                    <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Dr. Michael Chen</h3>
                        <p class="text-gray-600">Laboratory Coordinator</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">"The real-time monitoring features have made managing multiple laboratory sessions much more efficient."</p>
            </div>

            <!-- Testimonial 3 -->
            <div class="bg-gray-50 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center mb-6">
                    <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Prof. Emily Rodriguez</h3>
                        <p class="text-gray-600">IT Department Head</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">"An excellent tool for maintaining accurate records and generating comprehensive reports."</p>
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl p-6 text-center shadow-lg border border-gray-100">
                <div class="text-gray-900 text-3xl mb-2">
                    <i class="fas fa-users"></i>
                </div>
                <div class="text-2xl font-bold text-gray-900">2.5K</div>
                <div class="text-sm text-gray-500">Total Students</div>
            </div>
            
            <div class="bg-white rounded-xl p-6 text-center shadow-lg border border-gray-100">
                <div class="text-gray-900 text-3xl mb-2">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <div class="text-2xl font-bold text-gray-900">8</div>
                <div class="text-sm text-gray-500">Active Labs</div>
            </div>
            
            <div class="bg-white rounded-xl p-6 text-center shadow-lg border border-gray-100">
                <div class="text-gray-900 text-3xl mb-2">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="text-2xl font-bold text-gray-900">1.2K</div>
                <div class="text-sm text-gray-500">Hours Monitored</div>
            </div>
        </div>
    </div>
</div>

<!-- Before Footer -->
<div class="bg-gradient-to-r from-gray-900 to-gray-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to Get Started?</h2>
            <p class="text-lg text-gray-300 mb-8 max-w-2xl mx-auto">
                Join the growing number of institutions using our system to streamline their classroom management.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="index.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                    Sign Up Now
                </a>
                <a href="#" class="inline-flex items-center px-6 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                    Learn More
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-gray-900 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="mb-4">
            <p class="text-lg font-bold text-white">CCS Sit-in Monitoring System</p>
            <p class="text-sm text-gray-400">Copyright © <?php echo date('Y'); ?> - All rights reserved</p>
        </div>
        <div class="flex justify-center space-x-6">
            <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">About us</a>
            <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Contact</a>
            <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Support</a>
        </div>
    </div>
</footer>
</body>
</html>