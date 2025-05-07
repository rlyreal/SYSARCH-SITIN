<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Resources</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="navbar bg-[#2c343c] shadow-lg">
        <div class="navbar-start">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-xl font-bold text-white ml-2">Resources</span>
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
            <a href="#" onclick="event.preventDefault(); document.getElementById('logoutModal').classList.remove('hidden');" 
               class="btn btn-error btn-outline gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </a>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-800">Lab Resources</h1>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Search Section -->
        <div class="px-4 sm:px-0 mb-8">
            <div class="max-w-xl mx-auto">
                <div class="relative flex items-center">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Search resources..."
                           class="w-full px-4 py-2 pl-10 pr-4 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resources Grid -->
        <div id="resourcesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-4 sm:px-0">
            <!-- Resources will be loaded here -->
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="hidden">
            <div class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>
        </div>
    </main>

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
    document.addEventListener('DOMContentLoaded', function() {
        const grid = document.getElementById('resourcesGrid');
        const loadingState = document.getElementById('loadingState');
        
        function showLoading() {
            loadingState.classList.remove('hidden');
            grid.classList.add('opacity-50');
        }
        
        function hideLoading() {
            loadingState.classList.add('hidden');
            grid.classList.remove('opacity-50');
        }

        async function loadResources(query = '') {
            showLoading();
            try {
                const response = await fetch(`get_user_resources.php${query ? '?query=' + encodeURIComponent(query) : ''}`);
                const data = await response.json();
                
                if (data.length === 0) {
                    grid.innerHTML = `
                        <div class="col-span-full flex flex-col items-center justify-center py-12">
                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <p class="text-gray-500 text-lg">${query ? `No resources found matching "${query}"` : 'No resources available'}</p>
                        </div>`;
                    return;
                }

                grid.innerHTML = data.map(resource => `
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                        <div class="aspect-w-16 aspect-h-9 bg-gray-100">
                            <img src="${resource.cover_image || 'placeholder.png'}" 
                                 alt="${resource.title}"
                                 class="w-full h-48 object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-1">${resource.title}</h3>
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                ${resource.professor}
                            </div>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">${resource.description}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400">
                                    Added ${new Date(resource.created_at).toLocaleDateString()}
                                </span>
                                <a href="${resource.resource_link}" 
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors duration-200">
                                    Open
                                    <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error:', error);
                grid.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <p class="text-red-500">Failed to load resources. Please try again later.</p>
                    </div>`;
            } finally {
                hideLoading();
            }
        }

        // Initialize resources
        loadResources();

        // Search with debounce
        const searchInput = document.getElementById('searchInput');
        let debounceTimer;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                loadResources(this.value.trim());
            }, 300);
        });
    });

    // Logout modal functionality
    document.getElementById('cancelLogout').addEventListener('click', function() {
        document.getElementById('logoutModal').classList.add('hidden');
    });

    document.getElementById('confirmLogout').addEventListener('click', function() {
        window.location.href = 'logout.php';
    });

    document.getElementById('logoutModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
    </script>
</body>
</html>