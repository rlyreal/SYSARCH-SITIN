<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Resources Management</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Admin Navbar -->
    <div class="navbar bg-[#2c343c] shadow-lg">
        <div class="navbar-start">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-xl font-bold text-white ml-2">Admin</span>
            </div>
        </div>
        
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 gap-2">
                <li>
                    <a href="admin_dashboard.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <a href="search.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                    </a>
                </li>
                <li>
                    <a href="students.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Students
                    </a>
                </li>
                <li>
                    <a href="sit_in.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Sit-in
                    </a>
                </li>
                <li>
                    <a href="sit_in_records.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        View Records
                    </a>
                </li>
                <li>
                    <a href="admin_reservation.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reservation
                    </a>
                </li>
                <li>
                    <a href="reports.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Reports
                    </a>
                </li>
                <li>
                    <a href="feedback.php" class="btn btn-ghost text-white hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        Feedback Reports
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="navbar-end">
            <button id="logoutBtn" class="btn btn-error btn-outline gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Resource Hub Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Resource Hub</h2>
                    <p class="text-gray-600">Access and manage educational assets</p>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="flex justify-end mb-6">
                <div class="relative w-64">
                    <input type="text" placeholder="Find resources..." 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="absolute right-3 top-2.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- No Resources Message -->
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <div class="mb-4">
                    <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-600 mb-2">No Resources Found</h3>
                <p class="text-gray-500 mb-6">Start adding educational resources to build your collection</p>
                <button class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Resource
                </button>
            </div>

            <!-- Resources Table -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recently Added</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resource</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500" colspan="5">No resources added yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Resource Modal -->
    <div id="addResourceModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold">New Resource</h3>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="resourceForm" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-sm text-gray-600">Drop files here or click to upload</p>
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 5MB</p>
                        </div>
                        <input type="file" class="hidden" accept="image/png, image/jpeg" id="coverImage">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Resource Title</label>
                    <input type="text" id="resourceTitle" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Professor</label>
                    <input type="text" id="professor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Resource Link</label>
                    <input type="url" id="resourceLink" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mt-8">
                    <h4 class="text-sm font-medium text-gray-700 mb-4">Resource Preview</h4>
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="flex items-start space-x-4">
                            <div class="w-24 h-24 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0" id="previewImage">
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-800" id="previewTitle">Resource Title</h3>
                                <p class="text-sm text-gray-600 mt-1" id="previewProfessor">Professor Name</p>
                                <p class="text-sm text-gray-700 mt-2" id="previewDescription">Resource description will appear here...</p>
                                <a href="#" class="text-sm text-blue-600 hover:underline mt-2 inline-block" id="previewLink">Resource Link</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Save Resource
                    </button>
                </div>
            </form>
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

    <script>
        // Modal functions
        function openModal() {
            document.getElementById('addResourceModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('addResourceModal').classList.add('hidden');
        }

        // Update the "Add First Resource" button click handler
        document.querySelector('.btn-primary').addEventListener('click', openModal);

        // Handle form submission
        document.getElementById('resourceForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('title', document.getElementById('resourceTitle').value);
            formData.append('professor', document.getElementById('professor').value);
            formData.append('description', document.getElementById('description').value);
            formData.append('resource_link', document.getElementById('resourceLink').value);
            formData.append('cover_image', document.getElementById('coverImage').files[0]);
            formData.append('action', 'add_resource');

            try {
                const response = await fetch('handle_resource.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    closeModal();
                    loadRecentResources(); // We'll create this function next
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while saving the resource');
            }
        });

        // Function to load recent resources
        function loadRecentResources() {
            fetch('handle_resource.php?action=get_recent')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('tbody');
                    if (data.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500" colspan="5">No resources added yet.</td>
                            </tr>`;
                        return;
                    }

                    tbody.innerHTML = data.map(resource => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <img class="h-10 w-10 rounded-lg object-cover" 
                                             src="${resource.cover_image || 'placeholder.png'}" 
                                             alt="${resource.title}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">${resource.title}</div>
                                        <div class="text-xs text-gray-500 truncate max-w-xs">${resource.resource_link}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <span class="font-medium">${resource.professor}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-500 truncate max-w-md">${resource.description}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                ${new Date(resource.created_at).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                })}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="${resource.resource_link}" 
                                       target="_blank" 
                                       class="p-1.5 bg-blue-50 rounded-lg text-blue-600 hover:bg-blue-100 transition-colors"
                                       title="Open resource">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                    <button onclick="editResource(${resource.id})" 
                                            class="p-1.5 bg-gray-50 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
                                            title="Edit resource">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button onclick="deleteResource(${resource.id})" 
                                            class="p-1.5 bg-red-50 rounded-lg text-red-600 hover:bg-red-100 transition-colors"
                                            title="Delete resource">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Add these functions to handle edit and delete actions
        function editResource(id) {
            // Fetch resource details and open edit modal
            fetch(`handle_resource.php?action=get_resource&id=${id}`)
                .then(response => response.json())
                .then(resource => {
                    // Populate form with resource details
                    document.getElementById('resourceTitle').value = resource.title;
                    document.getElementById('professor').value = resource.professor;
                    document.getElementById('description').value = resource.description;
                    document.getElementById('resourceLink').value = resource.resource_link;
                    // Show modal
                    openModal();
                });
        }

        function deleteResource(id) {
            if (confirm('Are you sure you want to delete this resource?')) {
                fetch('handle_resource.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_resource&id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadRecentResources(); // Refresh the table
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the resource');
                });
            }
        }

        // Handle file drop zone
        const dropZone = document.querySelector('.border-dashed');
        const fileInput = document.getElementById('coverImage');

        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-500');
        });
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-blue-500');
        });
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500');
            fileInput.files = e.dataTransfer.files;
            
            const file = e.dataTransfer.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImage = document.getElementById('previewImage');
                    previewImage.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(file);
            }
        });

        // Live Preview Updates
        document.getElementById('resourceTitle').addEventListener('input', function(e) {
            document.getElementById('previewTitle').textContent = e.target.value || 'Resource Title';
        });

        document.getElementById('professor').addEventListener('input', function(e) {
            document.getElementById('previewProfessor').textContent = e.target.value || 'Professor Name';
        });

        document.getElementById('description').addEventListener('input', function(e) {
            document.getElementById('previewDescription').textContent = e.target.value || 'Resource description will appear here...';
        });

        document.getElementById('resourceLink').addEventListener('input', function(e) {
            const link = document.getElementById('previewLink');
            link.href = e.target.value;
            link.textContent = e.target.value || 'Resource Link';
        });

        // Image Preview
        document.getElementById('coverImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImage = document.getElementById('previewImage');
                    previewImage.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(file);
            }
        });

        // Add this at the end of your DOMContentLoaded event listener
        document.addEventListener('DOMContentLoaded', function() {
            loadRecentResources();
        });

        // Add this to your existing JavaScript section
        function searchResources(query) {
            fetch(`handle_resource.php?action=search&query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('tbody');
                    if (data.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500" colspan="5">No resources found matching "${query}"</td>
                            </tr>`;
                        return;
                    }

                    tbody.innerHTML = data.map(resource => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <img class="h-10 w-10 rounded-lg object-cover" 
                                             src="${resource.cover_image || 'placeholder.png'}" 
                                             alt="${resource.title}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">${resource.title}</div>
                                        <div class="text-xs text-gray-500 truncate max-w-xs">${resource.resource_link}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <span class="font-medium">${resource.professor}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-500 truncate max-w-md">${resource.description}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                ${new Date(resource.created_at).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                })}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="${resource.resource_link}" 
                                       target="_blank" 
                                       class="p-1.5 bg-blue-50 rounded-lg text-blue-600 hover:bg-blue-100 transition-colors"
                                       title="Open resource">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                    <button onclick="editResource(${resource.id})" 
                                            class="p-1.5 bg-gray-50 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
                                            title="Edit resource">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button onclick="deleteResource(${resource.id})" 
                                            class="p-1.5 bg-red-50 rounded-lg text-red-600 hover:bg-red-100 transition-colors"
                                            title="Delete resource">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Add debounce function to prevent too many requests
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Add event listener to search input
        document.querySelector('input[placeholder="Find resources..."]').addEventListener('input', debounce(function(e) {
            const query = e.target.value.trim();
            if (query) {
                searchResources(query);
            } else {
                loadRecentResources(); // Show all resources when search is empty
            }
        }, 300)); // Wait 300ms after user stops typing before searching

        // Logout modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.getElementById('logoutBtn');
            const logoutModal = document.getElementById('logoutModal');
            const cancelLogout = document.getElementById('cancelLogout');
            const confirmLogout = document.getElementById('confirmLogout');

            logoutBtn.addEventListener('click', function() {
                logoutModal.classList.remove('hidden');
            });

            cancelLogout.addEventListener('click', function() {
                logoutModal.classList.add('hidden');
            });

            confirmLogout.addEventListener('click', function() {
                window.location.href = 'logout.php';
            });

            logoutModal.addEventListener('click', function(e) {
                if (e.target === logoutModal) {
                    logoutModal.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>