<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#137fec",
                        secondary: "#3b82f6",
                        success: "#10b981",
                        "background-light": "#f6f7f8",
                        "background-dark": "#111827",
                        "card-light": "#ffffff",
                        "card-dark": "#1f2937",
                        "nav-active": "#e0efff",
                        "nav-text": "#344767",
                        "nav-icon": "#3b82f6",
                    },
                    fontFamily: {
                        display: ["Montserrat", "sans-serif"],
                        sans: ["Montserrat", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.375rem",
                    },
                },
            },
        };
    </script>
    <!-- Blacklist dropdown function-->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Remove default select arrow */
        select {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            background-image: none !important;
            padding-right: 2.5rem !important;
        }

        select::-ms-expand {
            display: none;
        }

        /* Ensure no background arrow */
        select:not([size]):not([multiple]) {
            background-image: none !important;
        }
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden transition-colors duration-200">
    <!-- Sidebar -->
    <?= view('reports/partials/report_sidebar', ['current' => $current]) ?>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8 bg-background-light dark:bg-background-dark">
        <div
            class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-7xl">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">
                        System Configuration
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage system settings and configurations
                    </p>
                </div>
            </div>

            <!-- Configuration Sections -->
            <div class="space-y-4">
                <!-- General Settings -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('general')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span
                                    class="material-symbols-outlined text-primary text-xl">settings_applications</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">General Settings</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Configure basic system
                                    information and preferences</p>
                            </div>
                        </div>
                        <span id="general-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="general-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Company
                                        Name</label>
                                    <input value="SafeG Enterprise Sdn Bhd"
                                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white"
                                        type="text" />
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Company
                                        Address</label>
                                    <input value="Jalan Teknologi 5, Kota Kinabalu Industrial Park"
                                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white"
                                        type="text" />
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Security
                                        Office Contact</label>
                                    <input value="+60 88-123 4567"
                                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white"
                                        type="tel" />
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">System
                                        Email</label>
                                    <input value="security@safeg.com.my"
                                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white"
                                        type="email" />
                                </div>
                                <div class="md:col-span-2 flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Date
                                        Format</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="date_format" value="DD/MM/YYYY" checked
                                                class="text-primary focus:ring-primary" />
                                            <span class="text-sm text-slate-700 dark:text-slate-300">DD/MM/YYYY</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="date_format" value="MM/DD/YYYY"
                                                class="text-primary focus:ring-primary" />
                                            <span class="text-sm text-slate-700 dark:text-slate-300">MM/DD/YYYY</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="date_format" value="YYYY-MM-DD"
                                                class="text-primary focus:ring-primary" />
                                            <span class="text-sm text-slate-700 dark:text-slate-300">YYYY-MM-DD</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 mt-6">
                                <button
                                    class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Cancel</button>
                                <button
                                    class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">Save
                                    Changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('role')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">admin_panel_settings</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Role Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage user roles and
                                    permissions</p>
                            </div>
                        </div>
                        <span id="role-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="role-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search and Create -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex shadow-sm w-full sm:w-96">
                                    <input id="role-search-input"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                        placeholder="Search role name..." type="text" />
                                    <button onclick="searchRoles()"
                                        class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                        <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                    </button>
                                </div>
                                <button onclick="openCreateRoleModal()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Role
                                </button>
                            </div>

                            <!-- Roles Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Role Name</th>
                                            <th class="px-4 py-3">Description</th>
                                            <th class="px-4 py-3">Users</th>
                                            <th class="px-4 py-3">Created Date</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="roles-table-body" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="6"
                                                class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2">
                                                    </div>
                                                    <span>Loading roles...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p id="role-pagination-info" class="text-sm text-gray-600 dark:text-slate-400">
                                    Loading...
                                </p>
                                <div id="role-pagination-buttons" class="flex items-center gap-2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('user')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">manage_accounts</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">User Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage system users and
                                    accounts</p>
                            </div>
                        </div>
                        <span id="user-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="user-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search and Create -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="user-search-input"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search username, name, email..." type="text" />
                                        <button onclick="searchUsers()"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="user-sort-select" onchange="sortUsers()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="username_asc">Username (A-Z)</option>
                                            <option value="username_desc">Username (Z-A)</option>
                                            <option value="name_asc">Full Name (A-Z)</option>
                                            <option value="name_desc">Full Name (Z-A)</option>
                                            <option value="email_asc">Email (A-Z)</option>
                                            <option value="email_desc">Email (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateUserModal()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create User
                                </button>
                            </div>

                            <!-- Users Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Username</th>
                                            <th class="px-4 py-3">Full Name</th>
                                            <th class="px-4 py-3">Staff ID</th>
                                            <th class="px-4 py-3">Email</th>
                                            <th class="px-4 py-3">Contact Number</th>
                                            <th class="px-4 py-3">Role</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="users-table-body" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="8"
                                                class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2">
                                                    </div>
                                                    <span>Loading users...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p id="user-pagination-info" class="text-sm text-gray-600 dark:text-slate-400">
                                    Loading...
                                </p>
                                <div id="user-pagination-buttons" class="flex items-center gap-2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('company')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">business</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Company Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage registered companies
                                    and contractors</p>
                            </div>
                        </div>
                        <span id="company-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="company-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="companySearchInput"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search company name, reg no, email..." type="text" />
                                        <button onclick="searchCompanies()"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="companySortSelect" onchange="sortCompanies()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="name_asc">Name (A-Z)</option>
                                            <option value="name_desc">Name (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateCompanyModal()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Company
                                </button>
                            </div>

                            <!-- Companies Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Registration No</th>
                                            <th class="px-4 py-3">Company Name</th>
                                            <th class="px-4 py-3">Contact No</th>
                                            <th class="px-4 py-3">Email</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3">Created At</th>
                                            <th class="px-4 py-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="companyTableBody" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="7"
                                                class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2">
                                                    </div>
                                                    <span>Loading companies...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div id="companyPagination"
                                class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="companyShowingFrom">0</span> to <span
                                        id="companyShowingTo">0</span> of <span id="companyTotalCount">0</span>
                                    companies
                                </p>
                                <div id="companyPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub Company Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('subcompany')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">corporate_fare</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Sub Company Management
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage subsidiary and branch
                                    companies</p>
                            </div>
                        </div>
                        <span id="subcompany-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="subcompany-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Filter and Create -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="subCompanySearchInput"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search sub company name..." type="text" />
                                        <button onclick="searchSubCompanies()"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="subCompanyFilterSelect" onchange="filterSubCompanies()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">All Companies</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateSubCompanyModal()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Sub Company
                                </button>
                            </div>

                            <!-- Sub Companies Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Sub Company</th>
                                            <th class="px-4 py-3">Company</th>
                                            <th class="px-4 py-3">Description</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="subCompanyTableBody" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="5"
                                                class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2">
                                                    </div>
                                                    <span>Loading sub companies...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div id="subCompanyPagination"
                                class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="subCompanyShowingFrom">0</span> to <span
                                        id="subCompanyShowingTo">0</span> of <span id="subCompanyTotalCount">0</span>
                                    sub companies
                                </p>
                                <div id="subCompanyPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Country Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('country')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">public</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Country Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage country list and
                                    codes</p>
                            </div>
                        </div>
                        <span id="country-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="country-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="countrySearchInput"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search country name, code..." type="text" />
                                        <button onclick="searchCountries()"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="countrySortSelect" onchange="sortCountries()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="name_asc">Country (A-Z)</option>
                                            <option value="name_desc">Country (Z-A)</option>
                                            <option value="code_asc">Code (A-Z)</option>
                                            <option value="code_desc">Code (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateCountryModal()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Country
                                </button>
                            </div>

                            <!-- Countries Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Country Code</th>
                                            <th class="px-4 py-3">Country Name</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="countryTableBody" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="4"
                                                class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2">
                                                    </div>
                                                    <span>Loading countries...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div id="countryPagination"
                                class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="countryShowingFrom">0</span> to <span
                                        id="countryShowingTo">0</span> of <span id="countryTotalCount">0</span>
                                    countries
                                </p>
                                <div id="countryPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- State Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('state')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">map</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">State Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage state list and
                                    country associations</p>
                            </div>
                        </div>
                        <span id="state-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="state-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="stateSearchInput"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search state name..." type="text" />
                                        <button onclick="searchStates()"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="stateCountryFilter" onchange="filterStates()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">All Countries</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="stateSortSelect" onchange="sortStates()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="name_asc">State (A-Z)</option>
                                            <option value="name_desc">State (Z-A)</option>
                                            <option value="country_asc">Country (A-Z)</option>
                                            <option value="country_desc">Country (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateStateModal()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create State
                                </button>
                            </div>

                            <!-- States Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">State Code</th>
                                            <th class="px-4 py-3">State Name</th>
                                            <th class="px-4 py-3">Country</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="stateTableBody" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="5"
                                                class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2">
                                                    </div>
                                                    <span>Loading states...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div id="statePagination"
                                class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="stateShowingFrom">0</span> to <span id="stateShowingTo">0</span>
                                    of <span id="stateTotalCount">0</span> states
                                </p>
                                <div id="statePaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- City Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('city')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">location_city</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">City Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage city list and state
                                    associations</p>
                            </div>
                        </div>
                        <span id="city-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="city-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col gap-3 mb-4">
                                <div class="flex flex-col lg:flex-row gap-3">
                                    <div class="flex shadow-sm w-full lg:w-80">
                                        <input id="citySearchInput"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search city name..." type="text" />
                                        <button onclick="searchCities()"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full lg:w-44">
                                        <select id="cityCountryFilter" onchange="filterCitiesByCountry()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">All Countries</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                    <div class="relative w-full lg:w-40">
                                        <select id="cityStateFilter" onchange="filterCities()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">All States</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                    <div class="relative w-full lg:w-40">
                                        <select id="citySortSelect" onchange="sortCities()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="name_asc">City (A-Z)</option>
                                            <option value="name_desc">City (Z-A)</option>
                                            <option value="state_asc">State (A-Z)</option>
                                            <option value="state_desc">State (Z-A)</option>
                                            <option value="country_asc">Country (A-Z)</option>
                                            <option value="country_desc">Country (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                    <button onclick="openCreateCityModal()"
                                        class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center justify-center gap-2 w-full lg:w-auto lg:ml-auto whitespace-nowrap">
                                        <span class="material-symbols-outlined text-base">add</span>
                                        Create City
                                    </button>
                                </div>
                            </div>

                            <!-- Cities Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">City Code</th>
                                            <th class="px-4 py-3">City Name</th>
                                            <th class="px-4 py-3">State</th>
                                            <th class="px-4 py-3">Country</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cityTableBody" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="6"
                                                class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2">
                                                    </div>
                                                    <span>Loading cities...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div id="cityPagination"
                                class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="cityShowingFrom">0</span> to <span id="cityShowingTo">0</span> of
                                    <span id="cityTotalCount">0</span> cities
                                </p>
                                <div id="cityPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('department')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">corporate_fare</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Department Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage department list</p>
                            </div>
                        </div>
                        <span id="department-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="department-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div
                                class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="departmentSearchInput"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search department name..." type="text" />
                                        <button onclick="searchDepartments()"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="departmentSortSelect" onchange="sortDepartments()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="name_asc">Department (A-Z)</option>
                                            <option value="name_desc">Department (Z-A)</option>
                                            <option value="code_asc">Code (A-Z)</option>
                                            <option value="code_desc">Code (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateDepartmentModal()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center justify-center gap-2 w-full sm:w-auto whitespace-nowrap">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Department
                                </button>
                            </div>

                            <!-- Departments Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Code</th>
                                            <th class="px-4 py-3">Department Name</th>
                                            <th class="px-4 py-3">Description</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="departmentTableBody" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="5"
                                                class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2">
                                                    </div>
                                                    <span>Loading departments...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="departmentShowingFrom" class="font-medium">0</span> to <span
                                        id="departmentShowingTo" class="font-medium">0</span> of <span
                                        id="departmentTotalCount" class="font-medium">0</span> departments
                                </p>
                                <div id="departmentPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Designation Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('designation')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">work</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Designation Management
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage job designation list
                                </p>
                            </div>
                        </div>
                        <span id="designation-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="designation-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div
                                class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="designationSearchInput"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search designation name..." type="text" />
                                        <button onclick="searchDesignations()"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="designationSortSelect" onchange="sortDesignations()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="name_asc">Name (A-Z)</option>
                                            <option value="name_desc">Name (Z-A)</option>
                                            <option value="code_asc">Code (A-Z)</option>
                                            <option value="code_desc">Code (Z-A)</option>
                                            <option value="wo_flag">WO Flag</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateDesignationModal()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center justify-center gap-2 w-full sm:w-auto whitespace-nowrap">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Designation
                                </button>
                            </div>

                            <!-- Designations Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Code</th>
                                            <th class="px-4 py-3">Designation Name</th>
                                            <th class="px-4 py-3">Description</th>
                                            <th class="px-4 py-3">WO Flag</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="designationTableBody" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="6"
                                                class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2">
                                                    </div>
                                                    <span>Loading designations...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="designationShowingFrom" class="font-medium">0</span> to <span
                                        id="designationShowingTo" class="font-medium">0</span> of <span
                                        id="designationTotalCount" class="font-medium">0</span> designations
                                </p>
                                <div id="designationPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Access Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('location')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">location_on</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Location Access Management
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage location access
                                    configurations</p>
                            </div>
                        </div>
                        <span id="location-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="location-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div
                                class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="locationSearchInput"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search branch, location..." type="text" />
                                        <button onclick="searchLocations()"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="locationSortSelect" onchange="sortLocations()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="branch_asc">Branch (A-Z)</option>
                                            <option value="branch_desc">Branch (Z-A)</option>
                                            <option value="location_asc">Location (A-Z)</option>
                                            <option value="location_desc">Location (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateLocationModal()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center justify-center gap-2 w-full sm:w-auto whitespace-nowrap">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Location
                                </button>
                            </div>

                            <!-- Location Access Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Branch</th>
                                            <th class="px-4 py-3">Location Access</th>
                                            <th class="px-4 py-3">Adam IP</th>
                                            <th class="px-4 py-3">Mobile APP</th>
                                            <th class="px-4 py-3">Is Hold Area</th>
                                            <th class="px-4 py-3">Visitor Pass Print</th>
                                            <th class="px-4 py-3">Turnstile</th>
                                            <th class="px-4 py-3">In/Out Bound</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="locationTableBody" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="10"
                                                class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2">
                                                    </div>
                                                    <span>Loading locations...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="locationShowingFrom" class="font-medium">0</span> to <span
                                        id="locationShowingTo" class="font-medium">0</span> of <span
                                        id="locationTotalCount" class="font-medium">0</span> locations
                                </p>
                                <div id="locationPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lane Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('lane')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">alt_route</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Lane Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage lane configurations
                                    and equipment</p>
                            </div>
                        </div>
                        <span id="lane-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="lane-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="laneSearchInput"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search lane, location..." type="text" />
                                        <button onclick="searchLanes()"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="laneSortSelect" onchange="sortLanes()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="lane_asc">Lane (A-Z)</option>
                                            <option value="lane_desc">Lane (Z-A)</option>
                                            <option value="location_asc">Location (A-Z)</option>
                                            <option value="location_desc">Location (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateLaneModal()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Lane
                                </button>
                            </div>

                            <!-- Lane Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Lane</th>
                                            <th class="px-4 py-3">Location Access</th>
                                            <th class="px-4 py-3">Barrier No</th>
                                            <th class="px-4 py-3">Weight ID</th>
                                            <th class="px-4 py-3">Slip Print</th>
                                            <th class="px-4 py-3">Antena IP</th>
                                            <th class="px-4 py-3">KIOSK IP</th>
                                            <th class="px-4 py-3">Cam ID 1</th>
                                            <th class="px-4 py-3">Cam ID 2</th>
                                            <th class="px-4 py-3">Cam ID 3</th>
                                            <th class="px-4 py-3">Cam Photo IP 1</th>
                                            <th class="px-4 py-3">Cam Photo IP 2</th>
                                            <th class="px-4 py-3">In</th>
                                            <th class="px-4 py-3">Out</th>
                                            <th class="px-4 py-3">Last Logged In By</th>
                                            <th class="px-4 py-3">Last Logged In Date Time</th>
                                            <th class="px-4 py-3">Last Changed On Printer Paper</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="laneTableBody" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="19" class="px-4 py-8 text-center">
                                                <div class="flex justify-center items-center">
                                                    <div
                                                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="laneShowingFrom" class="font-medium">0</span> to <span
                                        id="laneShowingTo" class="font-medium">0</span> of <span id="laneTotalCount"
                                        class="font-medium">0</span> lanes
                                </p>
                                <div id="lanePaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reject Reason Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('reject')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">block</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Reject Reason Management
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage rejection reasons for
                                    applications</p>
                            </div>
                        </div>
                        <span id="reject-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="reject-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="rejectReasonSearchInput"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search reject reason..." type="text" />
                                        <button onclick="searchRejectReasons()"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="rejectReasonSortSelect" onchange="sortRejectReasons()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="reason_asc">Reason (A-Z)</option>
                                            <option value="reason_desc">Reason (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateRejectReasonModal()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Reject Reason
                                </button>
                            </div>

                            <!-- Reject Reasons Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Reject Reason</th>
                                            <th class="px-4 py-3">Mobile App</th>
                                            <th class="px-4 py-3">Commercial</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rejectReasonTableBody" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center">
                                                <div class="flex justify-center items-center">
                                                    <div
                                                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="rejectReasonShowingFrom" class="font-medium">0</span> to <span
                                        id="rejectReasonShowingTo" class="font-medium">0</span> of <span
                                        id="rejectReasonTotalCount" class="font-medium">0</span> reject reasons
                                </p>
                                <div id="rejectReasonPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visitor Card Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('card')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">badge</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Visitor Card Management
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage visitor card
                                    inventory</p>
                            </div>
                        </div>
                        <span id="card-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="card-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="visitorCardSearchInput"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search card EPC..." type="text" />
                                        <button onclick="searchVisitorCards()"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="visitorCardSortSelect" onchange="sortVisitorCards()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="card_asc">Card EPC (A-Z)</option>
                                            <option value="card_desc">Card EPC (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateVisitorCardModal()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Card
                                </button>
                            </div>

                            <!-- Visitor Cards Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Card EPC</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="visitorCardTableBody" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="3" class="px-4 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-4">
                                                    </div>
                                                    <p class="text-gray-500 dark:text-slate-400">Loading visitor
                                                        cards...</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="visitorCardShowingFrom" class="font-medium">0</span> to <span
                                        id="visitorCardShowingTo" class="font-medium">0</span> of <span
                                        id="visitorCardTotalCount" class="font-medium">0</span> cards
                                </p>
                                <div id="visitorCardPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be dynamically generated -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Video Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('video')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">videocam</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Video Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage video content</p>
                            </div>
                        </div>
                        <span id="video-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="video-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="videoSearchInput"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search video name..." type="text" />
                                        <button onclick="searchVideos()"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="videoSortSelect" onchange="sortVideos()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="name_asc">Name (A-Z)</option>
                                            <option value="name_desc">Name (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateVideoModal()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Upload Video
                                </button>
                            </div>

                            <!-- Videos Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Name</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="videoTableBody" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="3" class="px-4 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-4">
                                                    </div>
                                                    <p class="text-gray-500 dark:text-slate-400">Loading videos...</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="videoShowingFrom" class="font-medium">0</span> to <span
                                        id="videoShowingTo" class="font-medium">0</span> of <span id="videoTotalCount"
                                        class="font-medium">0</span> videos
                                </p>
                                <div id="videoPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be dynamically generated -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visit Reason Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('reason')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">help</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Visit Reason Management
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage visit purpose reasons
                                </p>
                            </div>
                        </div>
                        <span id="reason-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="reason-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="visitReasonSearchInput"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search visit reason..." type="text"
                                            onkeyup="searchVisitReasons()" />
                                        <button onclick="searchVisitReasons()"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="visitReasonSortSelect" onchange="sortVisitReasons()"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="created_at_desc">Newest First</option>
                                            <option value="created_at_asc">Oldest First</option>
                                            <option value="reason_asc">Reason (A-Z)</option>
                                            <option value="reason_desc">Reason (Z-A)</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateVisitReasonModal()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Visit Reason
                                </button>
                            </div>

                            <!-- Visit Reasons Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">No</th>
                                            <th class="px-4 py-3">Visit Reason</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="visitReasonTableBody" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="3"
                                                class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2">
                                                    </div>
                                                    <span>Loading visit reasons...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div id="visitReasonPagination"
                                class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="visitReasonFrom">0</span> to <span id="visitReasonTo">0</span> of
                                    <span id="visitReasonTotal">0</span> visit reasons
                                </p>
                                <div id="visitReasonPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visitor QR Code Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('qrcode')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">qr_code_2</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Visitor QR Code Management
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage QR codes for visitor
                                    access</p>
                            </div>
                        </div>
                        <span id="qrcode-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="qrcode-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search URL, location..." type="text" />
                                        <button
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="date_desc">Created Date (Newest)</option>
                                            <option value="date_asc">Created Date (Oldest)</option>
                                            <option value="location_asc">Location (A-Z)</option>
                                            <option value="location_desc">Location (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span
                                            class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Generate QR Code
                                </button>
                            </div>

                            <!-- QR Codes Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">QR Code</th>
                                            <th class="px-4 py-3">URL Link</th>
                                            <th class="px-4 py-3">Location Covered</th>
                                            <th class="px-4 py-3">Created Date</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr
                                            class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3">
                                                <div
                                                    class="w-16 h-16 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                                                    <span
                                                        class="material-symbols-outlined text-3xl text-gray-400">qr_code_2</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-medium text-primary truncate max-w-xs">https://vms.company.com/check-in/rec001</span>
                                                    <button class="text-gray-400 hover:text-primary" title="Copy URL">
                                                        <span
                                                            class="material-symbols-outlined text-lg">content_copy</span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">Reception Area</td>
                                            <td class="px-4 py-3">2026-01-15 10:30</td>
                                            <td class="px-4 py-3"><span
                                                    class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span>
                                            </td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80"
                                                        title="Download QR">
                                                        <span class="material-symbols-outlined text-xl">download</span>
                                                    </button>
                                                    <button class="text-primary hover:text-primary/80" title="Edit">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400" title="Delete">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr
                                            class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3">
                                                <div
                                                    class="w-16 h-16 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                                                    <span
                                                        class="material-symbols-outlined text-3xl text-gray-400">qr_code_2</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-medium text-primary truncate max-w-xs">https://vms.company.com/check-in/gate001</span>
                                                    <button class="text-gray-400 hover:text-primary" title="Copy URL">
                                                        <span
                                                            class="material-symbols-outlined text-lg">content_copy</span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">Security Gate</td>
                                            <td class="px-4 py-3">2026-01-14 14:20</td>
                                            <td class="px-4 py-3"><span
                                                    class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span>
                                            </td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80"
                                                        title="Download QR">
                                                        <span class="material-symbols-outlined text-xl">download</span>
                                                    </button>
                                                    <button class="text-primary hover:text-primary/80" title="Edit">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400" title="Delete">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr
                                            class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3">
                                                <div
                                                    class="w-16 h-16 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                                                    <span
                                                        class="material-symbols-outlined text-3xl text-gray-400">qr_code_2</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-medium text-primary truncate max-w-xs">https://vms.company.com/check-in/lobby001</span>
                                                    <button class="text-gray-400 hover:text-primary" title="Copy URL">
                                                        <span
                                                            class="material-symbols-outlined text-lg">content_copy</span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">Lobby Entrance</td>
                                            <td class="px-4 py-3">2026-01-13 09:15</td>
                                            <td class="px-4 py-3"><span
                                                    class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span>
                                            </td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80"
                                                        title="Download QR">
                                                        <span class="material-symbols-outlined text-xl">download</span>
                                                    </button>
                                                    <button class="text-primary hover:text-primary/80" title="Edit">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400" title="Delete">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr
                                            class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3">
                                                <div
                                                    class="w-16 h-16 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                                                    <span
                                                        class="material-symbols-outlined text-3xl text-gray-400">qr_code_2</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-medium text-primary truncate max-w-xs">https://vms.company.com/check-in/park001</span>
                                                    <button class="text-gray-400 hover:text-primary" title="Copy URL">
                                                        <span
                                                            class="material-symbols-outlined text-lg">content_copy</span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">Parking Entry</td>
                                            <td class="px-4 py-3">2026-01-12 16:45</td>
                                            <td class="px-4 py-3"><span
                                                    class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded text-xs font-semibold">Pending</span>
                                            </td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80"
                                                        title="Download QR">
                                                        <span class="material-symbols-outlined text-xl">download</span>
                                                    </button>
                                                    <button class="text-primary hover:text-primary/80" title="Edit">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400" title="Delete">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr
                                            class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3">
                                                <div
                                                    class="w-16 h-16 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                                                    <span
                                                        class="material-symbols-outlined text-3xl text-gray-400">qr_code_2</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-medium text-primary truncate max-w-xs">https://vms.company.com/check-in/main001</span>
                                                    <button class="text-gray-400 hover:text-primary" title="Copy URL">
                                                        <span
                                                            class="material-symbols-outlined text-lg">content_copy</span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">Main Gate</td>
                                            <td class="px-4 py-3">2026-01-11 11:30</td>
                                            <td class="px-4 py-3"><span
                                                    class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span>
                                            </td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80"
                                                        title="Download QR">
                                                        <span class="material-symbols-outlined text-xl">download</span>
                                                    </button>
                                                    <button class="text-primary hover:text-primary/80" title="Edit">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400" title="Delete">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of
                                    <span class="font-medium">16</span> QR codes
                                </p>
                                <div class="flex items-center gap-2">
                                    <button
                                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                        disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button
                                        class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button
                                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button
                                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <span class="px-2 text-gray-400">...</span>
                                    <button
                                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">4</button>
                                    <button
                                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visitor Settings -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('visitor')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">badge</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Visitor Settings</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Configure visitor
                                    registration and check-in preferences</p>
                            </div>
                        </div>
                        <span id="visitor-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="visitor-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50 space-y-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Auto-approve
                                        Invitations</p>
                                    <p class="text-xs text-slate-500 mt-1">Automatically approve visitor invitations
                                        without manual review</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                    </div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Require Photo
                                        Upload</p>
                                    <p class="text-xs text-slate-500 mt-1">Mandatory photo upload for all visitor
                                        registrations</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                    </div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Enable QR Code
                                        Check-in</p>
                                    <p class="text-xs text-slate-500 mt-1">Allow visitors to check-in using QR code
                                        scanning</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Template Settings -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('email-template')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">mail</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Email Template</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Enable or disable visitor form lines shown from the email link</p>
                            </div>
                        </div>
                        <span id="email-template-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="email-template-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50 space-y-6">
                            <div>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Visitor registration form from invitation email</p>
                                <p class="text-xs text-slate-500 mt-1">Turn off any line you do not want visitors to see in the linked registration form.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="emailTemplateFormFields">
                                <?php
                                $emailTemplateFields = [
                                    'staff_id' => 'Staff ID Of Person Visited',
                                    'host_contact' => 'Contact No Of Person Visited',
                                    'company_visited' => 'Name Of Company Visited',
                                    'visit_reason' => 'Reason',
                                    'resident' => 'Resident',
                                    'ic_number' => 'IC Number',
                                    'date_of_birth' => 'Date of Birth',
                                    'sex' => 'Sex',
                                    'full_name' => 'Full Name',
                                    'contact_number' => 'Contact Number',
                                    'email' => 'Email Address',
                                    'address_1' => 'Address 1',
                                    'address_2' => 'Address 2',
                                    'address_3' => 'Address 3',
                                    'city' => 'City',
                                    'state' => 'State',
                                    'postal_code' => 'Postal Code',
                                    'country' => 'Country',
                                    'category' => 'Vehicle Category',
                                    'vehicle_type' => 'Type Of Vehicle',
                                    'vehicle_registration' => 'Vehicle Registration Number',
                                    'driving_license_section' => 'Driving License Section',
                                    'company_details_section' => 'Company Details Section',
                                    'asset_equipment_section' => 'Asset/Equipment Section',
                                    'document_upload_section' => 'Document Upload Section',
                                    'profile_photo_section' => 'Profile Photo Section',
                                ];
                                ?>
                                <?php foreach ($emailTemplateFields as $fieldKey => $fieldLabel): ?>
                                    <label class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-3">
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200"><?= esc($fieldLabel) ?></span>
                                        <input
                                            type="checkbox"
                                            class="email-template-field h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary"
                                            data-field="<?= esc($fieldKey) ?>"
                                        >
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="button"
                                    onclick="saveEmailTemplateFormSettings()"
                                    class="px-4 py-2.5 rounded-lg bg-primary text-white hover:bg-primary-hover transition-colors font-medium text-sm">
                                    Save Email Template
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('security')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">security</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Security Settings</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Configure authentication and
                                    security preferences</p>
                            </div>
                        </div>
                        <span id="security-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="security-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50 space-y-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Two-Factor
                                        Authentication</p>
                                    <p class="text-xs text-slate-500 mt-1">Require 2FA for all user accounts</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                    </div>
                                </label>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Session Timeout
                                    (Minutes)</label>
                                <input value="30" type="number" min="5" max="120"
                                    class="w-full md:w-48 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Password Minimum
                                    Length</label>
                                <input value="8" type="number" min="6" max="20"
                                    class="w-full md:w-48 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('notification')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">notifications</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Notification Settings</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage email, SMS, and push
                                    notifications</p>
                            </div>
                        </div>
                        <span id="notification-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="notification-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50 space-y-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Email
                                        Notifications</p>
                                    <p class="text-xs text-slate-500 mt-1">Send email notifications for visitor arrivals
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                    </div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">SMS
                                        Notifications</p>
                                    <p class="text-xs text-slate-500 mt-1">Send SMS alerts to hosts when visitors arrive
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                    </div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Push
                                        Notifications</p>
                                    <p class="text-xs text-slate-500 mt-1">Enable browser push notifications</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Device Assignments Management -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('device-assignment')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">devices</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Device Assignments</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage physical devices and
                                    IP ranges</p>
                            </div>
                        </div>
                        <span id="device-assignment-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="device-assignment-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <!-- IP Range Warning Banner -->
                        <div id="ipRangeWarningBanner"
                            class="hidden bg-[#ab2b4a] text-white px-6 py-3 items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined">warning</span>
                                <div>
                                    <h4 class="font-bold text-sm">Devices Outside Permitted IP Range</h4>
                                    <p class="text-xs opacity-90 text-[#f5f5f5]">We have detected that some devices have
                                        IP Addresses that falls outside the allowed IP range.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Current IP Range Banner -->
                        <div
                            class="bg-[#e0f2f1] dark:bg-teal-900/30 text-teal-800 dark:text-teal-200 px-6 py-3 flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-[#b2dfdb] dark:border-teal-800 gap-3">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[20px]">info</span>
                                <div class="text-sm font-medium">
                                    Current IP Range: <span id="displayIpRangeFrom">-</span> - <span
                                        id="displayIpRangeTo">-</span>
                                </div>
                            </div>
                            <button onclick="openIpRangeModal()"
                                class="px-3 py-1.5 rounded border border-teal-600/30 bg-teal-600/10 hover:bg-teal-600/20 text-xs font-bold transition-colors uppercase tracking-wider flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">edit</span> Change
                            </button>
                        </div>

                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="deviceSearch"
                                            onkeyup="if(event.key === 'Enter') loadDeviceAssignments(1, this.value)"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                            placeholder="Search Device ID, IP Address..." type="text" />
                                        <button
                                            onclick="loadDeviceAssignments(1, document.getElementById('deviceSearch').value)"
                                            class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                </div>
                                <button onclick="openDeviceAssignmentModal()"
                                    class="px-4 py-2.5 rounded bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Assign New Device
                                </button>
                            </div>

                            <!-- Devices Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Device ID</th>
                                            <th class="px-4 py-3">IP Address</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3">Registration</th>
                                            <th class="px-4 py-3">Location</th>
                                            <th class="px-4 py-3">Type</th>
                                            <th class="px-4 py-3">Last Heartbeat</th>
                                            <th class="px-4 py-3 w-32 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="device-assignment-table-body" class="text-gray-700 dark:text-slate-300">
                                        <!-- Data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <div class="text-sm text-gray-500 dark:text-slate-400">
                                    Showing <span id="deviceFrom"
                                        class="font-medium text-gray-900 dark:text-white">-</span> to <span
                                        id="deviceTo" class="font-medium text-gray-900 dark:text-white">-</span> of
                                    <span id="deviceTotal" class="font-medium text-gray-900 dark:text-white">-</span>
                                    devices
                                </div>
                                <div id="devicePaginationButtons" class="flex items-center gap-1">
                                    <!-- Pagination buttons -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- System Logs -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('logs')"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">description</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">System Logs</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">View and manage system
                                    activity logs</p>
                            </div>
                        </div>
                        <span id="logs-icon"
                            class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="logs-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex gap-2">
                                    <button onclick="filterLogs('all')" id="filter-all"
                                        class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-300 dark:hover:bg-slate-600 transition-colors text-sm">All
                                        Logs</button>
                                    <button onclick="filterLogs('critical')" id="filter-critical"
                                        class="px-4 py-2 rounded-lg text-gray-600 dark:text-slate-400 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Critical</button>
                                    <button onclick="filterLogs('error')" id="filter-error"
                                        class="px-4 py-2 rounded-lg text-gray-600 dark:text-slate-400 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Errors
                                        Only</button>
                                    <button onclick="filterLogs('warning')" id="filter-warning"
                                        class="px-4 py-2 rounded-lg text-gray-600 dark:text-slate-400 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Warnings</button>
                                    <button onclick="filterLogs('info')" id="filter-info"
                                        class="px-4 py-2 rounded-lg text-gray-600 dark:text-slate-400 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Info</button>
                                    <button onclick="filterLogs('debug')" id="filter-debug"
                                        class="px-4 py-2 rounded-lg text-gray-600 dark:text-slate-400 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Debug</button>
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="refreshLogs()"
                                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base">refresh</span>
                                        Refresh
                                    </button>
                                    <button onclick="exportLogs()"
                                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base">download</span>
                                        Export Logs
                                    </button>
                                </div>
                            </div>
                            <div id="logs-container"
                                class="bg-white dark:bg-slate-900 rounded-lg p-4 font-mono text-xs text-gray-700 dark:text-slate-300 max-h-96 overflow-y-auto border border-gray-200 dark:border-slate-700">
                                <div class="text-gray-500 dark:text-slate-400 text-center py-8">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2">
                                        </div>
                                        <span>Loading logs...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Role Create/Edit Modal -->
    <div id="roleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <h3 id="roleModalTitle" class="text-lg font-bold text-gray-800 dark:text-white">Create New Role</h3>
                <button onclick="closeRoleModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="roleForm" onsubmit="submitRoleForm(event)">
                <div class="p-6 space-y-4">
                    <input type="hidden" id="roleId" name="roleId">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Role Name
                            <span class="text-red-500">*</span></label>
                        <input type="text" id="roleName" name="name" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                            placeholder="Enter role name">
                        <p id="roleNameError" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Description</label>
                        <textarea id="roleDescription" name="description" rows="3"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                            placeholder="Enter role description"></textarea>
                        <p id="roleDescriptionError" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Status <span
                                class="text-red-500">*</span></label>
                        <select id="roleStatus" name="status" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <p id="roleStatusError" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>
                </div>
                <div
                    class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeRoleModal()"
                        class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                        Cancel
                    </button>
                    <button type="submit" id="roleSubmitBtn"
                        class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">save</span>
                        Save Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteRoleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Confirm Delete</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 dark:text-slate-300">Are you sure you want to delete this role? This action
                    cannot be undone.</p>
                <p id="deleteRoleName" class="mt-2 font-semibold text-gray-900 dark:text-white"></p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                    Cancel
                </button>
                <button onclick="confirmDeleteRole()"
                    class="px-4 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">delete</span>
                    Delete Role
                </button>
            </div>
        </div>
    </div>

    <!-- User Create/Edit Modal -->
    <div id="userModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-2xl mx-4 my-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <h3 id="userModalTitle" class="text-lg font-bold text-gray-800 dark:text-white">Create New User</h3>
                <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="userForm" onsubmit="submitUserForm(event)">
                <div class="p-6 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                    <input type="hidden" id="userId" name="userId">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Username
                                <span class="text-red-500">*</span></label>
                            <input type="text" id="userUsername" name="username" required
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                placeholder="Enter username">
                            <p id="userUsernameError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Full Name
                                <span class="text-red-500">*</span></label>
                            <input type="text" id="userFullName" name="full_name" required
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                placeholder="Enter full name">
                            <p id="userFull_nameError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Email
                                <span class="text-red-500">*</span></label>
                            <input type="email" id="userEmail" name="email" required
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                placeholder="Enter email">
                            <p id="userEmailError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">
                                Password <span id="passwordRequired" class="text-red-500">*</span>
                                <span id="passwordOptional" class="text-gray-500 text-xs hidden">(leave blank to keep
                                    current)</span>
                            </label>
                            <input type="password" id="userPassword" name="password"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                placeholder="Enter password">
                            <p id="userPasswordError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Staff
                                ID</label>
                            <input type="text" id="userStaffId" name="staff_id"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                placeholder="Enter staff ID">
                            <p id="userStaff_idError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Contact
                                Number</label>
                            <input type="text" id="userContactNo" name="contact_no"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                                placeholder="Enter contact number">
                            <p id="userContact_noError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Role <span
                                    class="text-red-500">*</span></label>
                            <select id="userRole" name="role" required
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="">Select Role</option>
                            </select>
                            <p id="userRoleError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Status
                                <span class="text-red-500">*</span></label>
                            <select id="userStatus" name="is_active" required
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <p id="userIs_activeError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                    </div>
                </div>
                <div
                    class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeUserModal()"
                        class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                        Cancel
                    </button>
                    <button type="submit" id="userSubmitBtn"
                        class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">save</span>
                        Save User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete User Confirmation Modal -->
    <div id="deleteUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Confirm Delete</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 dark:text-slate-300">Are you sure you want to delete this user? This action
                    cannot be undone.</p>
                <p id="deleteUserName" class="mt-2 font-semibold text-gray-900 dark:text-white"></p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                <button onclick="closeDeleteUserModal()"
                    class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                    Cancel
                </button>
                <button onclick="confirmDeleteUser()"
                    class="px-4 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">delete</span>
                    Delete User
                </button>
            </div>
        </div>
    </div>
    <!-- Device Assignment Create/Edit Modal -->
    <div id="deviceAssignmentModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-2xl mx-4 my-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <h3 id="deviceAssignmentModalTitle" class="text-lg font-bold text-gray-800 dark:text-white">Assign New
                    Device</h3>
                <button onclick="closeDeviceAssignmentModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="deviceAssignmentForm" onsubmit="submitDeviceAssignmentForm(event)">
                <div class="p-6 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                    <input type="hidden" id="deviceAssignmentId" name="deviceAssignmentId">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Select
                                Device <span class="text-red-500">*</span></label>
                            <select id="daDeviceSelect" required
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="">-- Select Device --</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Select
                                Location <span class="text-red-500">*</span></label>
                            <select id="daLocation" name="location_id" required
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="">-- Select Location --</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Assignment
                                Type <span class="text-red-500">*</span></label>
                            <select id="daType" name="type" required
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="">-- Select Type --</option>
                                <option value="Check-In">Check-In</option>
                                <option value="Check-Out">Check-Out</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Check-In: For entry devices, Check-Out: For exit
                                devices</p>
                        </div>
                    </div>
                </div>
                <div
                    class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeDeviceAssignmentModal()"
                        class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">save</span>
                        Save Device
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Device Assignment Modal -->
    <div id="deleteDeviceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Confirm Delete</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 dark:text-slate-300">Are you sure you want to delete this device? This action
                    cannot be undone.</p>
                <p id="deleteDeviceName" class="mt-2 font-semibold text-gray-900 dark:text-white"></p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                <button onclick="closeDeleteDeviceModal()"
                    class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Cancel</button>
                <button onclick="confirmDeleteDevice()"
                    class="px-4 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">delete</span>
                    Delete Device
                </button>
            </div>
        </div>
    </div>

    <!-- IP Range Settings Modal -->
    <div id="ipRangeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">IP Range Settings</h3>
                <button onclick="closeIpRangeModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form onsubmit="submitIpRangeForm(event)">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">IP Range From
                            <span class="text-red-500">*</span></label>
                        <input type="text" id="ipRangeFrom" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                            placeholder="192.168.1.1">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">IP Range To
                            <span class="text-red-500">*</span></label>
                        <input type="text" id="ipRangeTo" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none"
                            placeholder="192.168.1.255">
                    </div>
                </div>
                <div
                    class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeIpRangeModal()"
                        class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">save</span>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-[60] flex flex-col gap-2"></div>

    <script>
        let currentFilter = 'all';
        let currentPage = 1;
        let currentSearch = '';
        let deleteRoleId = null;

        // Toast Notification Function
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            const icon = type === 'success' ? 'check_circle' : type === 'error' ? 'error' : 'info';

            toast.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 min-w-[300px] max-w-md transform transition-all duration-300 translate-x-0`;
            toast.innerHTML = `
                <span class="material-symbols-outlined">${icon}</span>
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            `;

            toastContainer.appendChild(toast);

            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.style.transform = 'translateX(400px)';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        function toggleSection(section) {
            const content = document.getElementById(`${section}-content`);
            const icon = document.getElementById(`${section}-icon`);

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';

                // Load logs when System Logs section is opened
                if (section === 'logs') {
                    loadLogs();
                }
                // Load roles when Role Management section is opened
                if (section === 'role') {
                    loadRoles();
                }
                // Load users when User Management section is opened
                if (section === 'user') {
                    loadUsers();
                }
                // Load companies when Company Management section is opened
                if (section === 'company') {
                    loadCompanies();
                }
                // Load sub companies when Sub Company Management section is opened
                if (section === 'subcompany') {
                    loadSubCompanies();
                    loadCompaniesForFilter();
                }
                // Load countries when Country Management section is opened
                if (section === 'country') {
                    loadCountries();
                }
                // Load states when State Management section is opened
                if (section === 'state') {
                    loadStates();
                    loadCountriesForFilter();
                }
                // Load cities when City Management section is opened
                if (section === 'city') {
                    loadCities();
                    loadCountriesForCityFilter();
                    loadStatesForCityFilter();
                }
                // Load departments when Department Management section is opened
                if (section === 'department') {
                    loadDepartments();
                }
                // Load designations when Designation Management section is opened
                if (section === 'designation') {
                    loadDesignations();
                }
                // Load locations when Location Access Management section is opened
                if (section === 'location') {
                    loadLocations();
                }
                // Load lanes when Lane Management section is opened
                if (section === 'lane') {
                    loadLanes();
                }
                // Load reject reasons when Reject Reason Management section is opened
                if (section === 'reject') {
                    loadRejectReasons();
                }
                // Load visitor cards when Visitor Card Management section is opened
                if (section === 'card') {
                    loadVisitorCards();
                }
                // Load videos when Video Management section is opened
                if (section === 'video') {
                    loadVideos();
                }
                // Load visit reasons when Visit Reason Management section is opened
                if (section === 'reason') {
                    loadVisitReasons();
                }
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // ============== ROLE MANAGEMENT FUNCTIONS ==============

        function loadRoles(page = 1, search = '') {
            currentPage = page;
            currentSearch = search;

            const tbody = document.getElementById('roles-table-body');
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                            <span>Loading roles...</span>
                        </div>
                    </td>
                </tr>
            `;

            fetch(`<?= base_url('config/getRoles') ?>?page=${page}&per_page=10&search=${encodeURIComponent(search)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayRoles(data.data);
                        updatePagination(data.pagination);
                    } else {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-red-500">Error loading roles</td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading roles:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-red-500">Failed to load roles</td>
                        </tr>
                    `;
                });
        }

        function displayRoles(roles) {
            const tbody = document.getElementById('roles-table-body');

            if (roles.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">No roles found</td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = roles.map((role, index) => {
                const statusClass = role.status === 'active'
                    ? 'bg-green-500/20 text-green-400'
                    : 'bg-gray-500/20 text-gray-400';
                const statusText = role.status.charAt(0).toUpperCase() + role.status.slice(1);
                const createdDate = new Date(role.created_at).toLocaleDateString('en-GB');
                const borderClass = index < roles.length - 1 ? 'border-b border-gray-100 dark:border-slate-700' : '';

                return `
                    <tr class="${borderClass} hover:bg-gray-100 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 font-medium">${escapeHtml(role.name)}</td>
                        <td class="px-4 py-3">${escapeHtml(role.description || '-')}</td>
                        <td class="px-4 py-3">${role.user_count || 0}</td>
                        <td class="px-4 py-3">${createdDate}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 ${statusClass} rounded text-xs font-semibold">${statusText}</span>
                        </td>
                        <td class="px-4 py-3">
                            <button onclick="openEditRoleModal(${role.id})" class="text-primary hover:text-primary/80 mr-2" title="Edit Role">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button onclick="openDeleteModal(${role.id}, '${escapeHtml(role.name)}')" class="text-red-500 hover:text-red-400" title="Delete Role">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function updatePagination(pagination) {
            const infoEl = document.getElementById('role-pagination-info');
            const buttonsEl = document.getElementById('role-pagination-buttons');

            if (pagination.total === 0) {
                infoEl.textContent = 'No roles found';
                buttonsEl.innerHTML = '';
                return;
            }

            infoEl.innerHTML = `
                Showing <span class="font-medium">${pagination.from}</span> to 
                <span class="font-medium">${pagination.to}</span> of 
                <span class="font-medium">${pagination.total}</span> roles
            `;

            let buttonsHTML = `
                <button onclick="loadRoles(${pagination.current_page - 1}, '${currentSearch}')" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    ${pagination.current_page === 1 ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            `;

            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i === 1 || i === pagination.total_pages || (i >= pagination.current_page - 1 && i <= pagination.current_page + 1)) {
                    const activeClass = i === pagination.current_page
                        ? 'bg-primary text-white'
                        : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700';

                    buttonsHTML += `
                        <button onclick="loadRoles(${i}, '${currentSearch}')" 
                            class="px-3 py-2 rounded-lg ${activeClass} transition-colors font-medium text-sm min-w-[40px]">
                            ${i}
                        </button>
                    `;
                } else if (i === pagination.current_page - 2 || i === pagination.current_page + 2) {
                    buttonsHTML += `<span class="px-2 text-gray-400">...</span>`;
                }
            }

            buttonsHTML += `
                <button onclick="loadRoles(${pagination.current_page + 1}, '${currentSearch}')" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
            `;

            buttonsEl.innerHTML = buttonsHTML;
        }

        function searchRoles() {
            const searchInput = document.getElementById('role-search-input');
            loadRoles(1, searchInput.value.trim());
        }

        // Add enter key and real-time search support
        let searchTimeout;
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('role-search-input');
            if (searchInput) {
                // Enter key support
                searchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchRoles();
                    }
                });
            }
        });

        function openCreateRoleModal() {
            document.getElementById('roleModalTitle').textContent = 'Create New Role';
            document.getElementById('roleId').value = '';
            document.getElementById('roleForm').reset();
            clearRoleErrors();
            document.getElementById('roleModal').classList.remove('hidden');
            document.getElementById('roleModal').classList.add('flex');
        }

        function openEditRoleModal(roleId) {
            fetch(`<?= base_url('config/getRole') ?>/${roleId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('roleModalTitle').textContent = 'Edit Role';
                        document.getElementById('roleId').value = data.data.id;
                        document.getElementById('roleName').value = data.data.name;
                        document.getElementById('roleDescription').value = data.data.description || '';
                        document.getElementById('roleStatus').value = data.data.status;
                        clearRoleErrors();
                        document.getElementById('roleModal').classList.remove('hidden');
                        document.getElementById('roleModal').classList.add('flex');
                    } else {
                        showToast('Failed to load role data', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading role:', error);
                    showToast('Error loading role data', 'error');
                });
        }

        function closeRoleModal() {
            document.getElementById('roleModal').classList.add('hidden');
            document.getElementById('roleModal').classList.remove('flex');
            document.getElementById('roleForm').reset();
            clearRoleErrors();
        }

        function clearRoleErrors() {
            ['roleNameError', 'roleDescriptionError', 'roleStatusError'].forEach(id => {
                document.getElementById(id).classList.add('hidden');
                document.getElementById(id).textContent = '';
            });
        }

        function submitRoleForm(event) {
            event.preventDefault();
            clearRoleErrors();

            const roleId = document.getElementById('roleId').value;
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData.entries());

            const url = roleId
                ? `<?= base_url('config/updateRole') ?>/${roleId}`
                : `<?= base_url('config/createRole') ?>`;

            const submitBtn = document.getElementById('roleSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined text-base animate-spin">progress_activity</span> Saving...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeRoleModal();
                        loadRoles(currentPage, currentSearch);
                        showNotification(data.message, 'success');
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const errorEl = document.getElementById(`role${field.charAt(0).toUpperCase() + field.slice(1)}Error`);
                                if (errorEl) {
                                    errorEl.textContent = data.errors[field];
                                    errorEl.classList.remove('hidden');
                                }
                            });
                        }
                        showNotification(data.message || 'Failed to save role', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error saving role:', error);
                    showNotification('Error saving role', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span class="material-symbols-outlined text-base">save</span> Save Role';
                });
        }

        function openDeleteModal(roleId, roleName) {
            deleteRoleId = roleId;
            document.getElementById('deleteRoleName').textContent = roleName;
            document.getElementById('deleteRoleModal').classList.remove('hidden');
            document.getElementById('deleteRoleModal').classList.add('flex');
        }

        function closeDeleteModal() {
            document.getElementById('deleteRoleModal').classList.add('hidden');
            document.getElementById('deleteRoleModal').classList.remove('flex');
            deleteRoleId = null;
        }

        function confirmDeleteRole() {
            if (!deleteRoleId) return;

            fetch(`<?= base_url('config/deleteRole') ?>/${deleteRoleId}`, {
                method: 'DELETE'
            })
                .then(response => response.json())
                .then(data => {
                    closeDeleteModal();
                    if (data.success) {
                        loadRoles(currentPage, currentSearch);
                        showNotification(data.message, 'success');
                    } else {
                        showNotification(data.message || 'Failed to delete role', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting role:', error);
                    closeDeleteModal();
                    showNotification('Error deleting role', 'error');
                });
        }

        function showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                info: 'bg-blue-500'
            };

            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2`;
            notification.innerHTML = `
                <span class="material-symbols-outlined text-base">${type === 'success' ? 'check_circle' : type === 'error' ? 'error' : 'info'}</span>
                <span>${message}</span>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // ============== USER MANAGEMENT FUNCTIONS ==============

        let currentUserPage = 1;
        let currentUserSearch = '';
        let currentUserSort = '';
        let deleteUserId = null;
        let userSearchTimeout;

        function loadUsers(page = 1, search = '', sortBy = '') {
            currentUserPage = page;
            currentUserSearch = search;
            currentUserSort = sortBy;

            const tbody = document.getElementById('users-table-body');
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                            <span>Loading users...</span>
                        </div>
                    </td>
                </tr>
            `;

            fetch(`<?= base_url('config/getUsers') ?>?page=${page}&per_page=10&search=${encodeURIComponent(search)}&sort_by=${sortBy}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Users data:', data);
                    if (data.success) {
                        displayUsers(data.data);
                        updateUserPagination(data.pagination);
                    } else {
                        const errorMsg = data.message || 'Error loading users';
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-red-500">${errorMsg}</td>
                            </tr>
                        `;
                        console.error('Error from server:', data);
                    }
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-red-500">Failed to load users: ${error.message}</td>
                        </tr>
                    `;
                });
        }

        function displayUsers(users) {
            const tbody = document.getElementById('users-table-body');

            if (users.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">No users found</td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = users.map((user, index) => {
                const statusClass = user.is_active == 1
                    ? 'bg-green-500/20 text-green-400'
                    : 'bg-gray-500/20 text-gray-400';
                const statusText = user.is_active == 1 ? 'Active' : 'Inactive';
                const borderClass = index < users.length - 1 ? 'border-b border-gray-100 dark:border-slate-700' : '';

                return `
                    <tr class="${borderClass} hover:bg-gray-100 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 font-medium">${escapeHtml(user.username)}</td>
                        <td class="px-4 py-3">${escapeHtml(user.full_name)}</td>
                        <td class="px-4 py-3">${escapeHtml(user.staff_id || '-')}</td>
                        <td class="px-4 py-3">${escapeHtml(user.email)}</td>
                        <td class="px-4 py-3">${escapeHtml(user.contact_no || '-')}</td>
                        <td class="px-4 py-3">${escapeHtml(user.role || '-')}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 ${statusClass} rounded text-xs font-semibold">${statusText}</span>
                        </td>
                        <td class="px-4 py-3">
                            <button onclick="openEditUserModal(${user.id})" class="text-primary hover:text-primary/80 mr-2" title="Edit User">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button onclick="openDeleteUserModal(${user.id}, '${escapeHtml(user.username)}')" class="text-red-500 hover:text-red-400" title="Delete User">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function updateUserPagination(pagination) {
            const infoEl = document.getElementById('user-pagination-info');
            const buttonsEl = document.getElementById('user-pagination-buttons');

            if (pagination.total === 0) {
                infoEl.textContent = 'No users found';
                buttonsEl.innerHTML = '';
                return;
            }

            infoEl.innerHTML = `
                Showing <span class="font-medium">${pagination.from}</span> to 
                <span class="font-medium">${pagination.to}</span> of 
                <span class="font-medium">${pagination.total}</span> users
            `;

            let buttonsHTML = `
                <button onclick="loadUsers(${pagination.current_page - 1}, '${currentUserSearch}', '${currentUserSort}')" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    ${pagination.current_page === 1 ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            `;

            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i === 1 || i === pagination.total_pages || (i >= pagination.current_page - 1 && i <= pagination.current_page + 1)) {
                    const activeClass = i === pagination.current_page
                        ? 'bg-primary text-white'
                        : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700';

                    buttonsHTML += `
                        <button onclick="loadUsers(${i}, '${currentUserSearch}', '${currentUserSort}')" 
                            class="px-3 py-2 rounded-lg ${activeClass} transition-colors font-medium text-sm min-w-[40px]">
                            ${i}
                        </button>
                    `;
                } else if (i === pagination.current_page - 2 || i === pagination.current_page + 2) {
                    buttonsHTML += `<span class="px-2 text-gray-400">...</span>`;
                }
            }

            buttonsHTML += `
                <button onclick="loadUsers(${pagination.current_page + 1}, '${currentUserSearch}', '${currentUserSort}')" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
            `;

            buttonsEl.innerHTML = buttonsHTML;
        }

        function searchUsers() {
            const searchInput = document.getElementById('user-search-input');
            const sortSelect = document.getElementById('user-sort-select');
            loadUsers(1, searchInput.value.trim(), sortSelect.value);
        }

        function sortUsers() {
            const searchInput = document.getElementById('user-search-input');
            const sortSelect = document.getElementById('user-sort-select');
            loadUsers(1, searchInput.value.trim(), sortSelect.value);
        }

        function loadRolesForDropdown() {
            fetch(`<?= base_url('config/getAllRoles') ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('userRole');
                        select.innerHTML = '<option value="">Select Role</option>';
                        data.data.forEach(role => {
                            select.innerHTML += `<option value="${escapeHtml(role.name)}">${escapeHtml(role.name)}</option>`;
                        });
                    }
                })
                .catch(error => console.error('Error loading roles:', error));
        }

        function openCreateUserModal() {
            document.getElementById('userModalTitle').textContent = 'Create New User';
            document.getElementById('userId').value = '';
            document.getElementById('userForm').reset();
            document.getElementById('userPassword').required = true;
            document.getElementById('passwordRequired').classList.remove('hidden');
            document.getElementById('passwordOptional').classList.add('hidden');
            clearUserErrors();
            loadRolesForDropdown();
            document.getElementById('userModal').classList.remove('hidden');
            document.getElementById('userModal').classList.add('flex');
        }

        function openEditUserModal(userId) {
            fetch(`<?= base_url('config/getUser') ?>/${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('userModalTitle').textContent = 'Edit User';
                        document.getElementById('userId').value = data.data.id;
                        document.getElementById('userUsername').value = data.data.username;
                        document.getElementById('userFullName').value = data.data.full_name;
                        document.getElementById('userEmail').value = data.data.email;
                        document.getElementById('userPassword').value = '';
                        document.getElementById('userPassword').required = false;
                        document.getElementById('passwordRequired').classList.add('hidden');
                        document.getElementById('passwordOptional').classList.remove('hidden');
                        document.getElementById('userStaffId').value = data.data.staff_id || '';
                        document.getElementById('userContactNo').value = data.data.contact_no || '';
                        document.getElementById('userStatus').value = data.data.is_active;
                        clearUserErrors();
                        loadRolesForDropdown();
                        // Set role after roles are loaded
                        setTimeout(() => {
                            document.getElementById('userRole').value = data.data.role;
                        }, 100);
                        document.getElementById('userModal').classList.remove('hidden');
                        document.getElementById('userModal').classList.add('flex');
                    } else {
                        showToast('Failed to load user data', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading user:', error);
                    showToast('Error loading user data', 'error');
                });
        }

        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
            document.getElementById('userModal').classList.remove('flex');
            document.getElementById('userForm').reset();
            clearUserErrors();
        }

        function clearUserErrors() {
            ['userUsernameError', 'userFull_nameError', 'userEmailError', 'userPasswordError',
                'userStaff_idError', 'userContact_noError', 'userRoleError', 'userIs_activeError'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.classList.add('hidden');
                        el.textContent = '';
                    }
                });
        }

        function submitUserForm(event) {
            event.preventDefault();
            clearUserErrors();

            const userId = document.getElementById('userId').value;
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData.entries());

            // Remove password if empty in edit mode
            if (!userId || data.password) {
                // Password is required for create or provided for edit
            } else {
                delete data.password;
            }

            const url = userId
                ? `<?= base_url('config/updateUser') ?>/${userId}`
                : `<?= base_url('config/createUser') ?>`;

            const submitBtn = document.getElementById('userSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined text-base animate-spin">progress_activity</span> Saving...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeUserModal();
                        loadUsers(currentUserPage, currentUserSearch, currentUserSort);
                        showNotification(data.message, 'success');
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const errorEl = document.getElementById(`user${field.charAt(0).toUpperCase() + field.slice(1).replace('_', '_')}Error`);
                                if (errorEl) {
                                    errorEl.textContent = data.errors[field];
                                    errorEl.classList.remove('hidden');
                                }
                            });
                        }
                        showNotification(data.message || 'Failed to save user', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error saving user:', error);
                    showNotification('Error saving user', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span class="material-symbols-outlined text-base">save</span> Save User';
                });
        }

        function openDeleteUserModal(userId, username) {
            deleteUserId = userId;
            document.getElementById('deleteUserName').textContent = username;
            document.getElementById('deleteUserModal').classList.remove('hidden');
            document.getElementById('deleteUserModal').classList.add('flex');
        }

        function closeDeleteUserModal() {
            document.getElementById('deleteUserModal').classList.add('hidden');
            document.getElementById('deleteUserModal').classList.remove('flex');
            deleteUserId = null;
        }

        function confirmDeleteUser() {
            if (!deleteUserId) return;

            fetch(`<?= base_url('config/deleteUser') ?>/${deleteUserId}`, {
                method: 'DELETE'
            })
                .then(response => response.json())
                .then(data => {
                    closeDeleteUserModal();
                    if (data.success) {
                        loadUsers(currentUserPage, currentUserSearch, currentUserSort);
                        showNotification(data.message, 'success');
                    } else {
                        showNotification(data.message || 'Failed to delete user', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting user:', error);
                    closeDeleteUserModal();
                    showNotification('Error deleting user', 'error');
                });
        }

        // Add real-time search for users
        document.addEventListener('DOMContentLoaded', function () {
            const userSearchInput = document.getElementById('user-search-input');
            if (userSearchInput) {
                userSearchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchUsers();
                    }
                });
            }
        });

        // ============== SYSTEM LOGS FUNCTIONS ==============

        function filterLogs(level) {
            currentFilter = level;
            loadLogs(level);

            // Update button states
            const buttons = {
                'all': document.getElementById('filter-all'),
                'critical': document.getElementById('filter-critical'),
                'error': document.getElementById('filter-error'),
                'warning': document.getElementById('filter-warning'),
                'info': document.getElementById('filter-info'),
                'debug': document.getElementById('filter-debug')
            };

            // Reset all buttons
            Object.values(buttons).forEach(btn => {
                btn.className = 'px-4 py-2 rounded-lg text-gray-600 dark:text-slate-400 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm';
            });

            // Highlight active button
            if (buttons[level]) {
                buttons[level].className = 'px-4 py-2 rounded-lg bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-300 dark:hover:bg-slate-600 transition-colors text-sm';
            }
        }

        // ============== COMPANY MANAGEMENT FUNCTIONS ==============

        let currentCompanyPage = 1;
        let currentCompanySearch = '';
        let currentCompanySort = '';
        let companySearchTimeout = null;

        function loadCompanies(page = 1, search = '', sort = '') {
            currentCompanyPage = page;
            currentCompanySearch = search;
            currentCompanySort = sort;

            const tbody = document.getElementById('companyTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                            <span>Loading companies...</span>
                        </div>
                    </td>
                </tr>
            `;

            fetch(`<?= base_url('config/getCompanies') ?>?page=${page}&per_page=10&search=${encodeURIComponent(search)}&sort=${encodeURIComponent(sort)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderCompanyTable(data.data, data.pagination);
                    } else {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                                    Failed to load companies
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading companies:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                                Error loading companies
                            </td>
                        </tr>
                    `;
                });
        }

        function renderCompanyTable(companies, pagination) {
            const tbody = document.getElementById('companyTableBody');

            if (companies.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                            No companies found
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = companies.map(company => `
                <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                    <td class="px-4 py-3">${escapeHtml(company.registration_no || '-')}</td>
                    <td class="px-4 py-3 font-medium">${escapeHtml(company.name)}</td>
                    <td class="px-4 py-3">${escapeHtml(company.contact_no || '-')}</td>
                    <td class="px-4 py-3">${escapeHtml(company.email || '-')}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 ${company.status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'} rounded text-xs font-semibold">
                            ${company.status === 'active' ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="px-4 py-3">${new Date(company.created_at).toLocaleDateString()}</td>
                    <td class="px-4 py-3">
                        <button onclick="openEditCompanyModal(${company.id})" class="text-primary hover:text-primary/80 mr-2" title="Edit">
                            <span class="material-symbols-outlined text-base">edit</span>
                        </button>
                        <button onclick="openDeleteCompanyModal(${company.id}, '${escapeHtml(company.name)}')" class="text-red-500 hover:text-red-400" title="Delete">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </td>
                </tr>
            `).join('');

            // Update pagination
            const showingFromEl = document.getElementById('companyShowingFrom');
            const showingToEl = document.getElementById('companyShowingTo');
            const totalCountEl = document.getElementById('companyTotalCount');
            const buttonsEl = document.getElementById('companyPaginationButtons');

            const from = (pagination.current_page - 1) * pagination.per_page + 1;
            const to = Math.min(pagination.current_page * pagination.per_page, pagination.total);

            showingFromEl.textContent = pagination.total === 0 ? 0 : from;
            showingToEl.textContent = to;
            totalCountEl.textContent = pagination.total;

            let buttonsHTML = `
                <button onclick="loadCompanies(${pagination.current_page - 1}, '${currentCompanySearch}', '${currentCompanySort}')" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    ${pagination.current_page === 1 ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            `;

            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i === 1 || i === pagination.total_pages || (i >= pagination.current_page - 1 && i <= pagination.current_page + 1)) {
                    const activeClass = i === pagination.current_page
                        ? 'bg-primary text-white'
                        : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700';

                    buttonsHTML += `
                        <button onclick="loadCompanies(${i}, '${currentCompanySearch}', '${currentCompanySort}')" 
                            class="px-3 py-2 rounded-lg ${activeClass} transition-colors font-medium text-sm min-w-[40px]">
                            ${i}
                        </button>
                    `;
                } else if (i === pagination.current_page - 2 || i === pagination.current_page + 2) {
                    buttonsHTML += `<span class="px-2 text-gray-400">...</span>`;
                }
            }

            buttonsHTML += `
                <button onclick="loadCompanies(${pagination.current_page + 1}, '${currentCompanySearch}', '${currentCompanySort}')" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
            `;

            buttonsEl.innerHTML = buttonsHTML;
        }

        function searchCompanies() {
            const search = document.getElementById('companySearchInput').value;
            loadCompanies(1, search, currentCompanySort);
        }

        function sortCompanies() {
            const sort = document.getElementById('companySortSelect').value;
            loadCompanies(1, currentCompanySearch, sort);
        }

        // Search on Enter key
        document.getElementById('companySearchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchCompanies();
            }
        });

        function openCreateCompanyModal() {
            const modalHTML = `
                <div id="companyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 id="companyModalTitle" class="text-xl font-bold text-gray-800 dark:text-white">Create New Company</h3>
                            <button onclick="closeCompanyModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <form id="companyForm" class="p-6 space-y-4 overflow-y-auto">
                            <input type="hidden" id="companyId" name="id">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                    Company Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="companyName" name="name" required
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                <div id="companyNameError" class="text-red-500 text-sm mt-1 hidden"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Registration Number</label>
                                <input type="text" id="companyRegistrationNo" name="registration_no"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                <div id="companyRegistrationNoError" class="text-red-500 text-sm mt-1 hidden"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Address</label>
                                <textarea id="companyAddress" name="address" rows="3"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary"></textarea>
                                <div id="companyAddressError" class="text-red-500 text-sm mt-1 hidden"></div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Contact Number</label>
                                    <input type="text" id="companyContactNo" name="contact_no"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                    <div id="companyContactNoError" class="text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Email</label>
                                    <input type="email" id="companyEmail" name="email"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                    <div id="companyEmailError" class="text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select id="companyStatus" name="status" required
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div id="companyStatusError" class="text-red-500 text-sm mt-1 hidden"></div>
                            </div>
                        </form>
                        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700">
                            <button onclick="closeCompanyModal()" type="button"
                                class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                Cancel
                            </button>
                            <button onclick="saveCompany()" type="button"
                                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-blue-600 transition-colors">
                                <span id="companySaveBtn">Save Company</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        function openEditCompanyModal(companyId) {
            fetch(`<?= base_url('config/getCompany') ?>/${companyId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modalHTML = `
                            <div id="companyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                <div class="bg-white dark:bg-slate-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                                        <h3 id="companyModalTitle" class="text-xl font-bold text-gray-800 dark:text-white">Edit Company</h3>
                                        <button onclick="closeCompanyModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                            <span class="material-symbols-outlined">close</span>
                                        </button>
                                    </div>
                                    <form id="companyForm" class="p-6 space-y-4 overflow-y-auto">
                                        <input type="hidden" id="companyId" name="id" value="${data.data.id}">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                                Company Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="companyName" name="name" required value="${escapeHtml(data.data.name)}"
                                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                            <div id="companyNameError" class="text-red-500 text-sm mt-1 hidden"></div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Registration Number</label>
                                            <input type="text" id="companyRegistrationNo" name="registration_no" value="${escapeHtml(data.data.registration_no || '')}"
                                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                            <div id="companyRegistrationNoError" class="text-red-500 text-sm mt-1 hidden"></div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Address</label>
                                            <textarea id="companyAddress" name="address" rows="3"
                                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">${escapeHtml(data.data.address || '')}</textarea>
                                            <div id="companyAddressError" class="text-red-500 text-sm mt-1 hidden"></div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Contact Number</label>
                                                <input type="text" id="companyContactNo" name="contact_no" value="${escapeHtml(data.data.contact_no || '')}"
                                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                                <div id="companyContactNoError" class="text-red-500 text-sm mt-1 hidden"></div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Email</label>
                                                <input type="email" id="companyEmail" name="email" value="${escapeHtml(data.data.email || '')}"
                                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                                <div id="companyEmailError" class="text-red-500 text-sm mt-1 hidden"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                                Status <span class="text-red-500">*</span>
                                            </label>
                                            <select id="companyStatus" name="status" required
                                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                                <option value="active" ${data.data.status === 'active' ? 'selected' : ''}>Active</option>
                                                <option value="inactive" ${data.data.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                            </select>
                                            <div id="companyStatusError" class="text-red-500 text-sm mt-1 hidden"></div>
                                        </div>
                                    </form>
                                    <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700">
                                        <button onclick="closeCompanyModal()" type="button"
                                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                            Cancel
                                        </button>
                                        <button onclick="saveCompany()" type="button"
                                            class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-blue-600 transition-colors">
                                            <span id="companySaveBtn">Update Company</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        document.body.insertAdjacentHTML('beforeend', modalHTML);
                    } else {
                        showNotification('Failed to load company data', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading company:', error);
                    showNotification('Error loading company data', 'error');
                });
        }

        function closeCompanyModal() {
            const modal = document.getElementById('companyModal');
            if (modal) {
                modal.remove();
            }
        }

        function clearCompanyErrors() {
            ['companyName', 'companyRegistrationNo', 'companyAddress', 'companyContactNo', 'companyEmail', 'companyStatus'].forEach(field => {
                const errorEl = document.getElementById(field + 'Error');
                if (errorEl) {
                    errorEl.classList.add('hidden');
                    errorEl.textContent = '';
                }
            });
        }

        function saveCompany() {
            clearCompanyErrors();

            const companyId = document.getElementById('companyId').value;
            const formData = {
                name: document.getElementById('companyName').value,
                registration_no: document.getElementById('companyRegistrationNo').value,
                address: document.getElementById('companyAddress').value,
                contact_no: document.getElementById('companyContactNo').value,
                email: document.getElementById('companyEmail').value,
                status: document.getElementById('companyStatus').value
            };

            const url = companyId
                ? `<?= base_url('config/updateCompany') ?>/${companyId}`
                : `<?= base_url('config/createCompany') ?>`;

            const saveBtn = document.getElementById('companySaveBtn');
            saveBtn.textContent = 'Saving...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeCompanyModal();
                        loadCompanies(currentCompanyPage, currentCompanySearch, currentCompanySort);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const errorEl = document.getElementById('company' + field.charAt(0).toUpperCase() + field.slice(1) + 'Error');
                                if (errorEl) {
                                    errorEl.textContent = data.errors[field];
                                    errorEl.classList.remove('hidden');
                                }
                            });
                        }
                        showNotification(data.message || 'Failed to save company', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error saving company:', error);
                    showNotification('An error occurred while saving company', 'error');
                })
                .finally(() => {
                    saveBtn.textContent = companyId ? 'Update Company' : 'Save Company';
                });
        }

        function openDeleteCompanyModal(companyId, companyName) {
            const modalHTML = `
                <div id="deleteCompanyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg max-w-md w-full">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Confirm Delete</h3>
                            <button onclick="closeDeleteCompanyModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 dark:text-slate-300">
                                Are you sure you want to delete company "<strong>${escapeHtml(companyName)}</strong>"? This action cannot be undone.
                            </p>
                        </div>
                        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700">
                            <button onclick="closeDeleteCompanyModal()" type="button"
                                class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                Cancel
                            </button>
                            <button onclick="deleteCompany(${companyId})" type="button"
                                class="px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-colors">
                                <span id="companyDeleteBtn">Delete Company</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        function closeDeleteCompanyModal() {
            const modal = document.getElementById('deleteCompanyModal');
            if (modal) {
                modal.remove();
            }
        }

        function deleteCompany(companyId) {
            const deleteBtn = document.getElementById('companyDeleteBtn');
            deleteBtn.textContent = 'Deleting...';

            fetch(`<?= base_url('config/deleteCompany') ?>/${companyId}`, {
                method: 'DELETE',
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeDeleteCompanyModal();
                        loadCompanies(currentCompanyPage, currentCompanySearch, currentCompanySort);
                    } else {
                        showNotification(data.message || 'Failed to delete company', 'error');
                        deleteBtn.textContent = 'Delete Company';
                    }
                })
                .catch(error => {
                    console.error('Error deleting company:', error);
                    showNotification('An error occurred while deleting company', 'error');
                    deleteBtn.textContent = 'Delete Company';
                });
        }

        // ============== SUB COMPANY MANAGEMENT FUNCTIONS ==============

        let currentSubCompanyPage = 1;
        let currentSubCompanySearch = '';
        let currentSubCompanyFilter = '';
        let subCompanySearchTimeout = null;

        function loadSubCompanies(page = 1, search = '', companyFilter = '') {
            currentSubCompanyPage = page;
            currentSubCompanySearch = search;
            currentSubCompanyFilter = companyFilter;

            const tbody = document.getElementById('subCompanyTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                            <span>Loading sub companies...</span>
                        </div>
                    </td>
                </tr>
            `;

            fetch(`<?= base_url('config/getSubCompanies') ?>?page=${page}&per_page=10&search=${encodeURIComponent(search)}&company_id=${encodeURIComponent(companyFilter)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderSubCompanyTable(data.data, data.pagination);
                    } else {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                                    Failed to load sub companies
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading sub companies:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                                Error loading sub companies
                            </td>
                        </tr>
                    `;
                });
        }

        function renderSubCompanyTable(subCompanies, pagination) {
            const tbody = document.getElementById('subCompanyTableBody');

            if (subCompanies.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                            No sub companies found
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = subCompanies.map(subCompany => `
                <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                    <td class="px-4 py-3 font-medium">${escapeHtml(subCompany.name)}</td>
                    <td class="px-4 py-3">${escapeHtml(subCompany.company_name || '-')}</td>
                    <td class="px-4 py-3">${escapeHtml(subCompany.description || '-')}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 ${subCompany.status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'} rounded text-xs font-semibold">
                            ${subCompany.status === 'active' ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <button onclick="openEditSubCompanyModal(${subCompany.id})" class="text-primary hover:text-primary/80 mr-2" title="Edit">
                            <span class="material-symbols-outlined text-base">edit</span>
                        </button>
                        <button onclick="openDeleteSubCompanyModal(${subCompany.id}, '${escapeHtml(subCompany.name)}')" class="text-red-500 hover:text-red-400" title="Delete">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </td>
                </tr>
            `).join('');

            // Update pagination
            const showingFromEl = document.getElementById('subCompanyShowingFrom');
            const showingToEl = document.getElementById('subCompanyShowingTo');
            const totalCountEl = document.getElementById('subCompanyTotalCount');
            const buttonsEl = document.getElementById('subCompanyPaginationButtons');

            const from = (pagination.current_page - 1) * pagination.per_page + 1;
            const to = Math.min(pagination.current_page * pagination.per_page, pagination.total);

            showingFromEl.textContent = pagination.total === 0 ? 0 : from;
            showingToEl.textContent = to;
            totalCountEl.textContent = pagination.total;

            let buttonsHTML = `
                <button onclick="loadSubCompanies(${pagination.current_page - 1}, '${currentSubCompanySearch}', '${currentSubCompanyFilter}')" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    ${pagination.current_page === 1 ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            `;

            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i === 1 || i === pagination.total_pages || (i >= pagination.current_page - 1 && i <= pagination.current_page + 1)) {
                    const activeClass = i === pagination.current_page
                        ? 'bg-primary text-white'
                        : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700';

                    buttonsHTML += `
                        <button onclick="loadSubCompanies(${i}, '${currentSubCompanySearch}', '${currentSubCompanyFilter}')" 
                            class="px-3 py-2 rounded-lg ${activeClass} transition-colors font-medium text-sm min-w-[40px]">
                            ${i}
                        </button>
                    `;
                } else if (i === pagination.current_page - 2 || i === pagination.current_page + 2) {
                    buttonsHTML += `<span class="px-2 text-gray-400">...</span>`;
                }
            }

            buttonsHTML += `
                <button onclick="loadSubCompanies(${pagination.current_page + 1}, '${currentSubCompanySearch}', '${currentSubCompanyFilter}')" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
            `;

            buttonsEl.innerHTML = buttonsHTML;
        }

        function searchSubCompanies() {
            const search = document.getElementById('subCompanySearchInput').value;
            loadSubCompanies(1, search, currentSubCompanyFilter);
        }

        function filterSubCompanies() {
            const filter = document.getElementById('subCompanyFilterSelect').value;
            loadSubCompanies(1, currentSubCompanySearch, filter);
        }

        // Search on Enter key
        document.getElementById('subCompanySearchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchSubCompanies();
            }
        });

        function loadCompaniesForFilter() {
            fetch(`<?= base_url('config/getAllCompanies') ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('subCompanyFilterSelect');
                        select.innerHTML = '<option value="">All Companies</option>';
                        data.data.forEach(company => {
                            select.innerHTML += `<option value="${company.id}">${escapeHtml(company.name)}</option>`;
                        });
                    }
                })
                .catch(error => console.error('Error loading companies:', error));
        }

        function openCreateSubCompanyModal() {
            fetch(`<?= base_url('config/getAllCompanies') ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const companyOptions = data.data.map(company =>
                            `<option value="${company.id}">${escapeHtml(company.name)}</option>`
                        ).join('');

                        const modalHTML = `
                            <div id="subCompanyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                <div class="bg-white dark:bg-slate-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                                        <h3 id="subCompanyModalTitle" class="text-xl font-bold text-gray-800 dark:text-white">Create New Sub Company</h3>
                                        <button onclick="closeSubCompanyModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                            <span class="material-symbols-outlined">close</span>
                                        </button>
                                    </div>
                                    <form id="subCompanyForm" class="p-6 space-y-4 overflow-y-auto">
                                        <input type="hidden" id="subCompanyId" name="id">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                                Company <span class="text-red-500">*</span>
                                            </label>
                                            <select id="subCompanyCompanyId" name="company_id" required
                                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                                <option value="">Select Company</option>
                                                ${companyOptions}
                                            </select>
                                            <div id="subCompanyCompany_idError" class="text-red-500 text-sm mt-1 hidden"></div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                                Sub Company Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="subCompanyName" name="name" required
                                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                            <div id="subCompanyNameError" class="text-red-500 text-sm mt-1 hidden"></div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Description</label>
                                            <textarea id="subCompanyDescription" name="description" rows="3"
                                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary"></textarea>
                                            <div id="subCompanyDescriptionError" class="text-red-500 text-sm mt-1 hidden"></div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                                Status <span class="text-red-500">*</span>
                                            </label>
                                            <select id="subCompanyStatus" name="status" required
                                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                            <div id="subCompanyStatusError" class="text-red-500 text-sm mt-1 hidden"></div>
                                        </div>
                                    </form>
                                    <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700">
                                        <button onclick="closeSubCompanyModal()" type="button"
                                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                            Cancel
                                        </button>
                                        <button onclick="saveSubCompany()" type="button"
                                            class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-blue-600 transition-colors">
                                            <span id="subCompanySaveBtn">Save Sub Company</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        document.body.insertAdjacentHTML('beforeend', modalHTML);
                    }
                })
                .catch(error => {
                    console.error('Error loading companies:', error);
                    showNotification('Error loading companies', 'error');
                });
        }

        function openEditSubCompanyModal(subCompanyId) {
            Promise.all([
                fetch(`<?= base_url('config/getSubCompany') ?>/${subCompanyId}`).then(r => r.json()),
                fetch(`<?= base_url('config/getAllCompanies') ?>`).then(r => r.json())
            ])
                .then(([subCompanyData, companiesData]) => {
                    if (subCompanyData.success && companiesData.success) {
                        const companyOptions = companiesData.data.map(company =>
                            `<option value="${company.id}" ${company.id == subCompanyData.data.company_id ? 'selected' : ''}>${escapeHtml(company.name)}</option>`
                        ).join('');

                        const modalHTML = `
                        <div id="subCompanyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                            <div class="bg-white dark:bg-slate-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Edit Sub Company</h3>
                                    <button onclick="closeSubCompanyModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>
                                </div>
                                <form id="subCompanyForm" class="p-6 space-y-4 overflow-y-auto">
                                    <input type="hidden" id="subCompanyId" name="id" value="${subCompanyData.data.id}">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                            Company <span class="text-red-500">*</span>
                                        </label>
                                        <select id="subCompanyCompanyId" name="company_id" required
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                            <option value="">Select Company</option>
                                            ${companyOptions}
                                        </select>
                                        <div id="subCompanyCompany_idError" class="text-red-500 text-sm mt-1 hidden"></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                            Sub Company Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="subCompanyName" name="name" required value="${escapeHtml(subCompanyData.data.name)}"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                        <div id="subCompanyNameError" class="text-red-500 text-sm mt-1 hidden"></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Description</label>
                                        <textarea id="subCompanyDescription" name="description" rows="3"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">${escapeHtml(subCompanyData.data.description || '')}</textarea>
                                        <div id="subCompanyDescriptionError" class="text-red-500 text-sm mt-1 hidden"></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <select id="subCompanyStatus" name="status" required
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                            <option value="active" ${subCompanyData.data.status === 'active' ? 'selected' : ''}>Active</option>
                                            <option value="inactive" ${subCompanyData.data.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                        </select>
                                        <div id="subCompanyStatusError" class="text-red-500 text-sm mt-1 hidden"></div>
                                    </div>
                                </form>
                                <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700">
                                    <button onclick="closeSubCompanyModal()" type="button"
                                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        Cancel
                                    </button>
                                    <button onclick="saveSubCompany()" type="button"
                                        class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-blue-600 transition-colors">
                                        <span id="subCompanySaveBtn">Update Sub Company</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                        document.body.insertAdjacentHTML('beforeend', modalHTML);
                    } else {
                        showNotification('Failed to load sub company data', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading sub company:', error);
                    showNotification('Error loading sub company data', 'error');
                });
        }

        function closeSubCompanyModal() {
            const modal = document.getElementById('subCompanyModal');
            if (modal) {
                modal.remove();
            }
        }

        function clearSubCompanyErrors() {
            ['subCompanyCompany_id', 'subCompanyName', 'subCompanyDescription', 'subCompanyStatus'].forEach(field => {
                const errorEl = document.getElementById(field + 'Error');
                if (errorEl) {
                    errorEl.classList.add('hidden');
                    errorEl.textContent = '';
                }
            });
        }

        function saveSubCompany() {
            clearSubCompanyErrors();

            const subCompanyId = document.getElementById('subCompanyId').value;
            const formData = {
                company_id: parseInt(document.getElementById('subCompanyCompanyId').value),
                name: document.getElementById('subCompanyName').value,
                description: document.getElementById('subCompanyDescription').value,
                status: document.getElementById('subCompanyStatus').value
            };

            const url = subCompanyId
                ? `<?= base_url('config/updateSubCompany') ?>/${subCompanyId}`
                : `<?= base_url('config/createSubCompany') ?>`;

            const saveBtn = document.getElementById('subCompanySaveBtn');
            saveBtn.textContent = 'Saving...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeSubCompanyModal();
                        loadSubCompanies(currentSubCompanyPage, currentSubCompanySearch, currentSubCompanyFilter);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const errorEl = document.getElementById('subCompany' + field.charAt(0).toUpperCase() + field.slice(1).replace('_', '_') + 'Error');
                                if (errorEl) {
                                    errorEl.textContent = data.errors[field];
                                    errorEl.classList.remove('hidden');
                                }
                            });
                        }
                        showNotification(data.message || 'Failed to save sub company', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error saving sub company:', error);
                    showNotification('An error occurred while saving sub company', 'error');
                })
                .finally(() => {
                    saveBtn.textContent = subCompanyId ? 'Update Sub Company' : 'Save Sub Company';
                });
        }

        function openDeleteSubCompanyModal(subCompanyId, subCompanyName) {
            const modalHTML = `
                <div id="deleteSubCompanyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg max-w-md w-full">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Confirm Delete</h3>
                            <button onclick="closeDeleteSubCompanyModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 dark:text-slate-300">
                                Are you sure you want to delete sub company "<strong>${escapeHtml(subCompanyName)}</strong>"? This action cannot be undone.
                            </p>
                        </div>
                        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700">
                            <button onclick="closeDeleteSubCompanyModal()" type="button"
                                class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                Cancel
                            </button>
                            <button onclick="deleteSubCompany(${subCompanyId})" type="button"
                                class="px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-colors">
                                <span id="subCompanyDeleteBtn">Delete Sub Company</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        function closeDeleteSubCompanyModal() {
            const modal = document.getElementById('deleteSubCompanyModal');
            if (modal) {
                modal.remove();
            }
        }

        function deleteSubCompany(subCompanyId) {
            const deleteBtn = document.getElementById('subCompanyDeleteBtn');
            deleteBtn.textContent = 'Deleting...';

            fetch(`<?= base_url('config/deleteSubCompany') ?>/${subCompanyId}`, {
                method: 'DELETE',
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeDeleteSubCompanyModal();
                        loadSubCompanies(currentSubCompanyPage, currentSubCompanySearch, currentSubCompanyFilter);
                    } else {
                        showNotification(data.message || 'Failed to delete sub company', 'error');
                        deleteBtn.textContent = 'Delete Sub Company';
                    }
                })
                .catch(error => {
                    console.error('Error deleting sub company:', error);
                    showNotification('An error occurred while deleting sub company', 'error');
                    deleteBtn.textContent = 'Delete Sub Company';
                });
        }

        // ============== LOG MANAGEMENT FUNCTIONS ==============

        function loadLogs(level = 'all') {
            const container = document.getElementById('logs-container');

            // Show loading state
            container.innerHTML = `
                <div class="text-gray-500 dark:text-slate-400 text-center py-8">
                    <span class="material-symbols-outlined text-4xl mb-2 animate-spin">progress_activity</span>
                    <div>Loading logs...</div>
                </div>
            `;

            const filterParam = level === 'all' ? '' : level;
            fetch(`<?= base_url('config/getLogs') ?>?level=${filterParam}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.logs.length === 0) {
                            container.innerHTML = '<div class="text-gray-500 dark:text-slate-400 text-center py-4">No logs available</div>';
                        } else {
                            container.innerHTML = data.logs.map(log => `
                                <div class="mb-2 log-entry" data-level="${log.level.toLowerCase()}">
                                    <span class="text-${log.color}-600 dark:text-${log.color}-400">[${log.level}]</span> 
                                    ${log.timestamp} - ${escapeHtml(log.message)}
                                </div>
                            `).join('');
                        }
                    } else {
                        container.innerHTML = '<div class="text-red-500 dark:text-red-400 text-center py-4">Error loading logs</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading logs:', error);
                    container.innerHTML = '<div class="text-red-500 dark:text-red-400 text-center py-4">Failed to load logs</div>';
                });
        }

        function refreshLogs() {
            loadLogs(currentFilter);
        }

        function exportLogs() {
            const level = currentFilter === 'all' ? '' : currentFilter;
            window.location.href = `<?= base_url('config/exportLogs') ?>?level=${level}`;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatDateTime(dateTimeString) {
            if (!dateTimeString) return '-';
            try {
                const date = new Date(dateTimeString);
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                return `${year}-${month}-${day} ${hours}:${minutes}`;
            } catch (e) {
                return dateTimeString;
            }
        }

        // ============== COUNTRY MANAGEMENT FUNCTIONS ==============

        let currentCountryPage = 1;
        let currentCountrySearch = '';
        let currentCountrySort = '';

        function loadCountries(page = 1, search = '', sortBy = '') {
            currentCountryPage = page;
            currentCountrySearch = search;
            currentCountrySort = sortBy;

            const tbody = document.getElementById('countryTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                            <span>Loading countries...</span>
                        </div>
                    </td>
                </tr>
            `;

            fetch(`<?= base_url('config/getCountries') ?>?page=${page}&per_page=10&search=${encodeURIComponent(search)}&sort_by=${sortBy}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayCountries(data.data);
                        updateCountryPagination(data.pagination);
                    }
                })
                .catch(error => {
                    console.error('Error loading countries:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-red-500">
                                Error loading countries. Please try again.
                            </td>
                        </tr>
                    `;
                });
        }

        function displayCountries(countries) {
            const tbody = document.getElementById('countryTableBody');

            if (countries.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                            No countries found
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = countries.map((country, index) => `
                <tr class="${index === countries.length - 1 ? '' : 'border-b border-gray-100 dark:border-slate-700'} hover:bg-gray-100 dark:hover:bg-slate-700/30">
                    <td class="px-4 py-3 font-medium">${escapeHtml(country.code)}</td>
                    <td class="px-4 py-3">${escapeHtml(country.name)}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 ${country.status === 'Active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'} rounded text-xs font-semibold">
                            ${country.status}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <button onclick="openEditCountryModal(${country.id})" class="text-primary hover:text-primary/80 mr-2">
                            <span class="material-symbols-outlined text-base">edit</span>
                        </button>
                        <button onclick="openDeleteCountryModal(${country.id}, '${escapeHtml(country.name)}')" class="text-red-500 hover:text-red-400">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function updateCountryPagination(pagination) {
            const showingFrom = pagination.total === 0 ? 0 : ((pagination.current_page - 1) * pagination.per_page) + 1;
            const showingTo = Math.min(pagination.current_page * pagination.per_page, pagination.total);

            document.getElementById('countryShowingFrom').textContent = showingFrom;
            document.getElementById('countryShowingTo').textContent = showingTo;
            document.getElementById('countryTotalCount').textContent = pagination.total;

            const buttonsContainer = document.getElementById('countryPaginationButtons');
            let buttonsHTML = '';

            // Previous button
            buttonsHTML += `
                <button onclick="loadCountries(${pagination.current_page - 1}, '${currentCountrySearch}', '${currentCountrySort}')" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors ${pagination.current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                    ${pagination.current_page === 1 ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            `;

            // Page numbers
            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i === 1 || i === pagination.total_pages || (i >= pagination.current_page - 1 && i <= pagination.current_page + 1)) {
                    const isActive = i === pagination.current_page;
                    buttonsHTML += `
                        <button onclick="loadCountries(${i}, '${currentCountrySearch}', '${currentCountrySort}')"
                            class="px-3 py-2 rounded-lg ${isActive ? 'bg-primary text-white' : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'} transition-colors font-medium text-sm min-w-[40px]">
                            ${i}
                        </button>
                    `;
                } else if (i === pagination.current_page - 2 || i === pagination.current_page + 2) {
                    buttonsHTML += `<span class="px-2 text-gray-400">...</span>`;
                }
            }

            // Next button
            buttonsHTML += `
                <button onclick="loadCountries(${pagination.current_page + 1}, '${currentCountrySearch}', '${currentCountrySort}')"
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors ${pagination.current_page === pagination.total_pages ? 'opacity-50 cursor-not-allowed' : ''}"
                    ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
            `;

            buttonsContainer.innerHTML = buttonsHTML;
        }

        function searchCountries() {
            const search = document.getElementById('countrySearchInput').value;
            loadCountries(1, search, currentCountrySort);
        }

        function sortCountries() {
            const sort = document.getElementById('countrySortSelect').value;
            loadCountries(1, currentCountrySearch, sort);
        }

        // Search on Enter key
        document.getElementById('countrySearchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchCountries();
            }
        });

        // State search Enter key
        document.addEventListener('DOMContentLoaded', function () {
            // Delegate event listener for state search input (since it's loaded dynamically)
            document.addEventListener('keypress', function (e) {
                if (e.target && e.target.id === 'stateSearchInput' && e.key === 'Enter') {
                    e.preventDefault();
                    searchStates();
                }
            });
        });

        function openCreateCountryModal() {
            const modalHTML = `
                <div id="countryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 id="countryModalTitle" class="text-xl font-bold text-gray-800 dark:text-white">Create New Country</h3>
                            <button onclick="closeCountryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <form id="countryForm" class="p-6 space-y-4 overflow-y-auto">
                            <input type="hidden" id="countryId" name="id">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                    Country Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="countryName" name="name" required
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                <div id="countryNameError" class="text-red-500 text-sm mt-1 hidden"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                    Country Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="countryCode" name="code" required maxlength="10"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary uppercase">
                                <div id="countryCodeError" class="text-red-500 text-sm mt-1 hidden"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select id="countryStatus" name="status" required
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                                <div id="countryStatusError" class="text-red-500 text-sm mt-1 hidden"></div>
                            </div>
                        </form>
                        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700">
                            <button onclick="closeCountryModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                Cancel
                            </button>
                            <button onclick="saveCountry()" id="countrySubmitBtn" class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                Save Country
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        function openEditCountryModal(countryId) {
            fetch(`<?= base_url('config/getCountry') ?>/${countryId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        openCreateCountryModal();
                        document.getElementById('countryModalTitle').textContent = 'Edit Country';
                        document.getElementById('countryId').value = data.data.id;
                        document.getElementById('countryName').value = data.data.name;
                        document.getElementById('countryCode').value = data.data.code;
                        document.getElementById('countryStatus').value = data.data.status;
                    }
                })
                .catch(error => console.error('Error loading country:', error));
        }

        function closeCountryModal() {
            const modal = document.getElementById('countryModal');
            if (modal) modal.remove();
        }

        function clearCountryErrors() {
            ['countryName', 'countryCode', 'countryStatus'].forEach(field => {
                document.getElementById(field + 'Error').classList.add('hidden');
            });
        }

        function saveCountry() {
            clearCountryErrors();

            const countryId = document.getElementById('countryId').value;
            const formData = {
                name: document.getElementById('countryName').value,
                code: document.getElementById('countryCode').value.toUpperCase(),
                status: document.getElementById('countryStatus').value
            };

            const url = countryId
                ? `<?= base_url('config/updateCountry') ?>/${countryId}`
                : `<?= base_url('config/createCountry') ?>`;

            const submitBtn = document.getElementById('countrySubmitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeCountryModal();
                        loadCountries(currentCountryPage, currentCountrySearch, currentCountrySort);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const errorElement = document.getElementById(`country${field.charAt(0).toUpperCase() + field.slice(1)}Error`);
                                if (errorElement) {
                                    errorElement.textContent = data.errors[field];
                                    errorElement.classList.remove('hidden');
                                }
                            });
                        }
                        showNotification(data.message || 'Failed to save country', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error saving country:', error);
                    showNotification('An error occurred while saving the country', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = countryId ? 'Update Country' : 'Save Country';
                });
        }

        function openDeleteCountryModal(countryId, countryName) {
            const modalHTML = `
                <div id="deleteCountryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg max-w-md w-full">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Confirm Delete</h3>
                            <button onclick="closeDeleteCountryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 dark:text-slate-300">
                                Are you sure you want to delete country "<strong>${escapeHtml(countryName)}</strong>"? This action cannot be undone.
                            </p>
                        </div>
                        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700">
                            <button onclick="closeDeleteCountryModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                Cancel
                            </button>
                            <button onclick="deleteCountry(${countryId})" id="countryDeleteBtn" class="px-5 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">delete</span>
                                Delete Country
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        function closeDeleteCountryModal() {
            const modal = document.getElementById('deleteCountryModal');
            if (modal) {
                modal.remove();
            }
        }

        function deleteCountry(countryId) {
            const deleteBtn = document.getElementById('countryDeleteBtn');
            deleteBtn.textContent = 'Deleting...';

            fetch(`<?= base_url('config/deleteCountry') ?>/${countryId}`, { method: 'DELETE' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeDeleteCountryModal();
                        loadCountries(currentCountryPage, currentCountrySearch, currentCountrySort);
                    } else {
                        showNotification(data.message || 'Failed to delete country', 'error');
                        deleteBtn.textContent = 'Delete Country';
                    }
                })
                .catch(error => {
                    console.error('Error deleting country:', error);
                    showNotification('An error occurred while deleting the country', 'error');
                    deleteBtn.textContent = 'Delete Country';
                });
        }

        // =============== State Management Functions ===============
        let currentStatePage = 1;
        let currentStateSearch = '';
        let currentStateCountryFilter = '';
        let currentStateSort = '';

        function loadStates(page = 1, search = '', countryFilter = '', sortBy = '') {
            currentStatePage = page;
            currentStateSearch = search;
            currentStateCountryFilter = countryFilter;
            currentStateSort = sortBy;

            const tbody = document.getElementById('stateTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                            <span>Loading states...</span>
                        </div>
                    </td>
                </tr>
            `;

            const params = new URLSearchParams({ page, search, country_filter: countryFilter, sort_by: sortBy });

            fetch(`<?= base_url('config/getStates') ?>?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayStates(data.states);
                        updateStatePagination(data.pagination);
                    } else {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                                    ${data.message || 'Failed to load states'}
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading states:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                                An error occurred while loading states
                            </td>
                        </tr>
                    `;
                });
        }

        function displayStates(states) {
            const tbody = document.getElementById('stateTableBody');

            if (states.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                            No states found
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = states.map(state => {
                const statusBadge = state.status === 'active'
                    ? '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span>';

                return `
                    <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 font-medium">${escapeHtml(state.code)}</td>
                        <td class="px-4 py-3 font-medium">${escapeHtml(state.name)}</td>
                        <td class="px-4 py-3">${escapeHtml(state.country_name)}</td>
                        <td class="px-4 py-3">${statusBadge}</td>
                        <td class="px-4 py-3 w-32">
                            <div class="flex gap-2">
                                <button onclick="openEditStateModal(${state.id})" class="text-primary hover:text-primary/80 mr-2">
                                    <span class="material-symbols-outlined text-xl">edit</span>
                                </button>
                                <button onclick="openDeleteStateModal(${state.id}, '${escapeHtml(state.name)}')" class="text-red-500 hover:text-red-400">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function updateStatePagination(pagination) {
            document.getElementById('stateShowingFrom').textContent = pagination.from;
            document.getElementById('stateShowingTo').textContent = pagination.to;
            document.getElementById('stateTotalCount').textContent = pagination.total;

            const paginationButtons = document.getElementById('statePaginationButtons');
            let buttonsHtml = '';

            // Previous button
            buttonsHtml += `
                <button onclick="loadStates(${pagination.currentPage - 1}, currentStateSearch, currentStateCountryFilter, currentStateSort)" 
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                        ${pagination.currentPage === 1 ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            `;

            // Page numbers
            for (let i = 1; i <= pagination.totalPages; i++) {
                if (i === 1 || i === pagination.totalPages || (i >= pagination.currentPage - 2 && i <= pagination.currentPage + 2)) {
                    const activeClass = i === pagination.currentPage
                        ? 'bg-primary text-white'
                        : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700';

                    buttonsHtml += `
                        <button onclick="loadStates(${i}, currentStateSearch, currentStateCountryFilter, currentStateSort)" 
                                class="px-3 py-2 rounded-lg ${activeClass} transition-colors font-medium text-sm min-w-[40px]">
                            ${i}
                        </button>
                    `;
                } else if (i === pagination.currentPage - 3 || i === pagination.currentPage + 3) {
                    buttonsHtml += '<span class="px-2 text-gray-400">...</span>';
                }
            }

            // Next button
            buttonsHtml += `
                <button onclick="loadStates(${pagination.currentPage + 1}, currentStateSearch, currentStateCountryFilter, currentStateSort)" 
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                        ${pagination.currentPage === pagination.totalPages ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
            `;

            paginationButtons.innerHTML = buttonsHtml;
        }

        function searchStates() {
            const search = document.getElementById('stateSearchInput').value;
            loadStates(1, search, currentStateCountryFilter, currentStateSort);
        }

        function sortStates() {
            const sortBy = document.getElementById('stateSortSelect').value;
            loadStates(1, currentStateSearch, currentStateCountryFilter, sortBy);
        }

        function filterStates() {
            const countryFilter = document.getElementById('stateCountryFilter').value;
            loadStates(1, currentStateSearch, countryFilter, currentStateSort);
        }

        function loadCountriesForFilter() {
            fetch(`<?= base_url('config/getAllCountries') ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('stateCountryFilter');
                        data.countries.forEach(country => {
                            const option = document.createElement('option');
                            option.value = country.id;
                            option.textContent = country.name;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading countries:', error));
        }

        function openCreateStateModal() {
            const modalHtml = `
                <div id="stateModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 id="stateModalTitle" class="text-xl font-bold text-gray-800 dark:text-white">Create New State</h3>
                            <button onclick="closeStateModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <form id="stateForm" class="p-6 space-y-4">
                            <input type="hidden" id="stateId" value="">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Country <span class="text-red-500">*</span></label>
                                <select id="stateCountrySelect" autocomplete="off" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                    <option value="">Select Country</option>
                                </select>
                                <span id="countryError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">State Code <span class="text-red-500">*</span></label>
                                <input id="stateCode" type="text" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter state code"/>
                                <span id="codeError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">State Name <span class="text-red-500">*</span></label>
                                <input id="stateName" type="text" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter state name"/>
                                <span id="nameError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                                <select id="stateStatus" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <span id="statusError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div class="flex justify-end gap-3 pt-4">
                                <button onclick="closeStateModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                    Cancel
                                </button>
                                <button type="submit" id="saveStateBtn" class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                    Save State
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Load countries for dropdown
            fetch(`<?= base_url('config/getAllCountries') ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('stateCountrySelect');
                        data.countries.forEach(country => {
                            const option = document.createElement('option');
                            option.value = country.id;
                            option.textContent = country.name;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading countries:', error));

            document.getElementById('stateForm').addEventListener('submit', saveState);
        }

        function openEditStateModal(stateId) {
            fetch(`<?= base_url('config/getState') ?>/${stateId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const stateData = data.state;

                        // Create modal first
                        const modalHtml = `
                            <div id="stateModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                                        <h3 id="stateModalTitle" class="text-xl font-bold text-gray-800 dark:text-white">Edit State</h3>
                                        <button onclick="closeStateModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                            <span class="material-symbols-outlined">close</span>
                                        </button>
                                    </div>
                                    <form id="stateForm" class="p-6 space-y-4">
                                        <input type="hidden" id="stateId" value="${stateData.id}">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Country <span class="text-red-500">*</span></label>
                                            <select id="stateCountrySelect" autocomplete="off" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                                <option value="">Select Country</option>
                                            </select>
                                            <span id="countryError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">State Code <span class="text-red-500">*</span></label>
                                            <input id="stateCode" type="text" value="${escapeHtml(stateData.code)}" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter state code"/>
                                            <span id="codeError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">State Name <span class="text-red-500">*</span></label>
                                            <input id="stateName" type="text" value="${escapeHtml(stateData.name)}" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter state name"/>
                                            <span id="nameError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                                            <select id="stateStatus" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                                <option value="active" ${stateData.status === 'active' ? 'selected' : ''}>Active</option>
                                                <option value="inactive" ${stateData.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                            </select>
                                            <span id="statusError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div class="flex justify-end gap-3 pt-4">
                                            <button type="button" onclick="closeStateModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                                Cancel
                                            </button>
                                            <button type="submit" id="saveStateBtn" class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                                Save State
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        `;
                        document.body.insertAdjacentHTML('beforeend', modalHtml);

                        // Load countries and set the selected value
                        fetch(`<?= base_url('config/getAllCountries') ?>`)
                            .then(response => response.json())
                            .then(countryData => {
                                if (countryData.success) {
                                    const select = document.getElementById('stateCountrySelect');
                                    countryData.countries.forEach(country => {
                                        const option = document.createElement('option');
                                        option.value = country.id;
                                        option.textContent = country.name;
                                        if (country.id == stateData.country_id) {
                                            option.selected = true;
                                        }
                                        select.appendChild(option);
                                    });
                                }
                            })
                            .catch(error => console.error('Error loading countries:', error));

                        document.getElementById('stateForm').addEventListener('submit', saveState);
                    } else {
                        showNotification(data.message || 'Failed to load state', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading state:', error);
                    showNotification('An error occurred while loading the state', 'error');
                });
        }

        function closeStateModal() {
            const modal = document.getElementById('stateModal');
            if (modal) {
                modal.remove();
            }
        }

        function clearStateErrors() {
            document.getElementById('countryError').classList.add('hidden');
            document.getElementById('codeError').classList.add('hidden');
            document.getElementById('nameError').classList.add('hidden');
            document.getElementById('statusError').classList.add('hidden');
        }

        function saveState(e) {
            e.preventDefault();
            clearStateErrors();

            const stateId = document.getElementById('stateId').value;
            const country_id = document.getElementById('stateCountrySelect').value;
            const code = document.getElementById('stateCode').value;
            const name = document.getElementById('stateName').value;
            const status = document.getElementById('stateStatus').value;

            const saveBtn = document.getElementById('saveStateBtn');
            saveBtn.textContent = 'Saving...';

            const url = stateId ? `<?= base_url('config/updateState') ?>/${stateId}` : `<?= base_url('config/createState') ?>`;
            const method = stateId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ country_id, code, name, status })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeStateModal();
                        loadStates(currentStatePage, currentStateSearch, currentStateCountryFilter, currentStateSort);
                    } else {
                        if (data.errors) {
                            if (data.errors.country_id) {
                                document.getElementById('countryError').textContent = data.errors.country_id;
                                document.getElementById('countryError').classList.remove('hidden');
                            }
                            if (data.errors.code) {
                                document.getElementById('codeError').textContent = data.errors.code;
                                document.getElementById('codeError').classList.remove('hidden');
                            }
                            if (data.errors.name) {
                                document.getElementById('nameError').textContent = data.errors.name;
                                document.getElementById('nameError').classList.remove('hidden');
                            }
                            if (data.errors.status) {
                                document.getElementById('statusError').textContent = data.errors.status;
                                document.getElementById('statusError').classList.remove('hidden');
                            }
                        } else {
                            showNotification(data.message || 'Failed to save state', 'error');
                        }
                        saveBtn.textContent = 'Save State';
                    }
                })
                .catch(error => {
                    console.error('Error saving state:', error);
                    showNotification('An error occurred while saving the state', 'error');
                    saveBtn.textContent = 'Save State';
                });
        }

        function openDeleteStateModal(stateId, stateName) {
            const modalHtml = `
                <div id="deleteStateModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Delete State</h3>
                            <button onclick="closeDeleteStateModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 dark:text-slate-300">
                                Are you sure you want to delete state "<strong>${escapeHtml(stateName)}</strong>"? This action cannot be undone.
                            </p>
                        </div>
                        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700">
                            <button onclick="closeDeleteStateModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                Cancel
                            </button>
                            <button onclick="deleteState(${stateId})" id="stateDeleteBtn" class="px-5 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">delete</span>
                                Delete State
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        function closeDeleteStateModal() {
            const modal = document.getElementById('deleteStateModal');
            if (modal) {
                modal.remove();
            }
        }

        function deleteState(stateId) {
            const deleteBtn = document.getElementById('stateDeleteBtn');
            deleteBtn.textContent = 'Deleting...';

            fetch(`<?= base_url('config/deleteState') ?>/${stateId}`, { method: 'DELETE' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeDeleteStateModal();
                        loadStates(currentStatePage, currentStateSearch, currentStateCountryFilter, currentStateSort);
                    } else {
                        showNotification(data.message || 'Failed to delete state', 'error');
                        deleteBtn.textContent = 'Delete State';
                    }
                })
                .catch(error => {
                    console.error('Error deleting state:', error);
                    showNotification('An error occurred while deleting the state', 'error');
                    deleteBtn.textContent = 'Delete State';
                });
        }

        // =============== City Management Functions ===============
        let currentCityPage = 1;
        let currentCitySearch = '';
        let currentCityStateFilter = '';
        let currentCityCountryFilter = '';
        let currentCitySort = '';

        function loadCities(page = 1, search = '', stateFilter = '', countryFilter = '', sortBy = '') {
            currentCityPage = page;
            currentCitySearch = search;
            currentCityStateFilter = stateFilter;
            currentCityCountryFilter = countryFilter;
            currentCitySort = sortBy;

            const tbody = document.getElementById('cityTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                            <span>Loading cities...</span>
                        </div>
                    </td>
                </tr>
            `;

            const params = new URLSearchParams({ page, search, state_filter: stateFilter, country_filter: countryFilter, sort_by: sortBy });

            fetch(`<?= base_url('config/getCities') ?>?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayCities(data.cities);
                        updateCityPagination(data.pagination);
                    } else {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                                    ${data.message || 'Failed to load cities'}
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading cities:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                                An error occurred while loading cities
                            </td>
                        </tr>
                    `;
                });
        }

        function displayCities(cities) {
            const tbody = document.getElementById('cityTableBody');

            if (cities.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                            No cities found
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = cities.map(city => {
                const statusBadge = city.status === 'active'
                    ? '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span>';

                return `
                    <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 font-medium">${escapeHtml(city.code || '-')}</td>
                        <td class="px-4 py-3 font-medium">${escapeHtml(city.name)}</td>
                        <td class="px-4 py-3">${escapeHtml(city.state_name)}</td>
                        <td class="px-4 py-3">${escapeHtml(city.country_name)}</td>
                        <td class="px-4 py-3">${statusBadge}</td>
                        <td class="px-4 py-3 w-32">
                            <div class="flex gap-2">
                                <button onclick="openEditCityModal(${city.id})" class="text-primary hover:text-primary/80 mr-2">
                                    <span class="material-symbols-outlined text-xl">edit</span>
                                </button>
                                <button onclick="openDeleteCityModal(${city.id}, '${escapeHtml(city.name)}')" class="text-red-500 hover:text-red-400">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function updateCityPagination(pagination) {
            document.getElementById('cityShowingFrom').textContent = pagination.from;
            document.getElementById('cityShowingTo').textContent = pagination.to;
            document.getElementById('cityTotalCount').textContent = pagination.total;

            const paginationButtons = document.getElementById('cityPaginationButtons');
            let buttonsHtml = '';

            buttonsHtml += `
                <button onclick="loadCities(${pagination.currentPage - 1}, currentCitySearch, currentCityStateFilter, currentCityCountryFilter, currentCitySort)" 
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                        ${pagination.currentPage === 1 ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            `;

            for (let i = 1; i <= pagination.totalPages; i++) {
                if (i === 1 || i === pagination.totalPages || (i >= pagination.currentPage - 2 && i <= pagination.currentPage + 2)) {
                    const activeClass = i === pagination.currentPage
                        ? 'bg-primary text-white'
                        : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700';

                    buttonsHtml += `
                        <button onclick="loadCities(${i}, currentCitySearch, currentCityStateFilter, currentCityCountryFilter, currentCitySort)" 
                                class="px-3 py-2 rounded-lg ${activeClass} transition-colors font-medium text-sm min-w-[40px]">
                            ${i}
                        </button>
                    `;
                } else if (i === pagination.currentPage - 3 || i === pagination.currentPage + 3) {
                    buttonsHtml += '<span class="px-2 text-gray-400">...</span>';
                }
            }

            buttonsHtml += `
                <button onclick="loadCities(${pagination.currentPage + 1}, currentCitySearch, currentCityStateFilter, currentCityCountryFilter, currentCitySort)" 
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                        ${pagination.currentPage === pagination.totalPages ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
            `;

            paginationButtons.innerHTML = buttonsHtml;
        }

        function searchCities() {
            const search = document.getElementById('citySearchInput').value;
            loadCities(1, search, currentCityStateFilter, currentCityCountryFilter, currentCitySort);
        }

        function sortCities() {
            const sortBy = document.getElementById('citySortSelect').value;
            loadCities(1, currentCitySearch, currentCityStateFilter, currentCityCountryFilter, sortBy);
        }

        function filterCities() {
            const stateFilter = document.getElementById('cityStateFilter').value;
            loadCities(1, currentCitySearch, stateFilter, currentCityCountryFilter, currentCitySort);
        }

        function filterCitiesByCountry() {
            const countryFilter = document.getElementById('cityCountryFilter').value;
            document.getElementById('cityStateFilter').value = '';
            loadStatesForCityFilter(countryFilter);
            loadCities(1, currentCitySearch, '', countryFilter, currentCitySort);
        }

        function loadCountriesForCityFilter() {
            fetch(`<?= base_url('config/getAllCountries') ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('cityCountryFilter');
                        select.innerHTML = '<option value="">All Countries</option>';
                        data.countries.forEach(country => {
                            const option = document.createElement('option');
                            option.value = country.id;
                            option.textContent = country.name;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading countries:', error));
        }

        function loadStatesForCityFilter(countryId = '') {
            const url = countryId ? `<?= base_url('config/getAllStates') ?>?country_id=${countryId}` : `<?= base_url('config/getAllStates') ?>`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('cityStateFilter');
                        select.innerHTML = '<option value="">All States</option>';
                        data.states.forEach(state => {
                            const option = document.createElement('option');
                            option.value = state.id;
                            option.textContent = state.name;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading states:', error));
        }

        function openCreateCityModal() {
            const modalHtml = `
                <div id="cityModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 id="cityModalTitle" class="text-xl font-bold text-gray-800 dark:text-white">Create New City</h3>
                            <button onclick="closeCityModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <form id="cityForm" class="p-6 space-y-4">
                            <input type="hidden" id="cityId" value="">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Country <span class="text-red-500">*</span></label>
                                <select id="cityCountrySelect" onchange="loadStatesForCityModal()" autocomplete="off" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                    <option value="">Select Country</option>
                                </select>
                                <span id="cityCountryError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">State <span class="text-red-500">*</span></label>
                                <select id="cityStateSelect" autocomplete="off" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                    <option value="">Select State</option>
                                </select>
                                <span id="cityStateError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">City Name <span class="text-red-500">*</span></label>
                                <input id="cityName" type="text" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter city name"/>
                                <span id="cityNameError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">City Code</label>
                                <input id="cityCode" type="text" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter city code (optional)"/>
                                <span id="cityCodeError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                                <select id="cityStatus" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <span id="cityStatusError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div class="flex justify-end gap-3 pt-4">
                                <button type="button" onclick="closeCityModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                    Cancel
                                </button>
                                <button type="submit" id="saveCityBtn" class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                    Save City
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Load countries for dropdown
            fetch(`<?= base_url('config/getAllCountries') ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('cityCountrySelect');
                        data.countries.forEach(country => {
                            const option = document.createElement('option');
                            option.value = country.id;
                            option.textContent = country.name;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading countries:', error));

            document.getElementById('cityForm').addEventListener('submit', saveCity);
        }

        function openEditCityModal(cityId) {
            fetch(`<?= base_url('config/getCity') ?>/${cityId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cityData = data.city;

                        // Create modal first
                        const modalHtml = `
                            <div id="cityModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                                        <h3 id="cityModalTitle" class="text-xl font-bold text-gray-800 dark:text-white">Edit City</h3>
                                        <button onclick="closeCityModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                            <span class="material-symbols-outlined">close</span>
                                        </button>
                                    </div>
                                    <form id="cityForm" class="p-6 space-y-4">
                                        <input type="hidden" id="cityId" value="${cityData.id}">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Country <span class="text-red-500">*</span></label>
                                            <select id="cityCountrySelect" onchange="loadStatesForCityModal()" autocomplete="off" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                                <option value="">Select Country</option>
                                            </select>
                                            <span id="cityCountryError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">State <span class="text-red-500">*</span></label>
                                            <select id="cityStateSelect" autocomplete="off" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                                <option value="">Select State</option>
                                            </select>
                                            <span id="cityStateError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">City Name <span class="text-red-500">*</span></label>
                                            <input id="cityName" type="text" value="${escapeHtml(cityData.name)}" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter city name"/>
                                            <span id="cityNameError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">City Code</label>
                                            <input id="cityCode" type="text" value="${escapeHtml(cityData.code || '')}" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter city code (optional)"/>
                                            <span id="cityCodeError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                                            <select id="cityStatus" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                                <option value="active" ${cityData.status === 'active' ? 'selected' : ''}>Active</option>
                                                <option value="inactive" ${cityData.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                            </select>
                                            <span id="cityStatusError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div class="flex justify-end gap-3 pt-4">
                                            <button type="button" onclick="closeCityModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                                Cancel
                                            </button>
                                            <button type="submit" id="saveCityBtn" class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                                Save City
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        `;
                        document.body.insertAdjacentHTML('beforeend', modalHtml);

                        // Load countries first
                        fetch(`<?= base_url('config/getAllCountries') ?>`)
                            .then(response => response.json())
                            .then(countryData => {
                                if (countryData.success) {
                                    const countrySelect = document.getElementById('cityCountrySelect');
                                    countryData.countries.forEach(country => {
                                        const option = document.createElement('option');
                                        option.value = country.id;
                                        option.textContent = country.name;
                                        if (country.id == cityData.country_id) {
                                            option.selected = true;
                                        }
                                        countrySelect.appendChild(option);
                                    });

                                    // Load states for the selected country
                                    return fetch(`<?= base_url('config/getAllStates') ?>?country_id=${cityData.country_id}`);
                                }
                            })
                            .then(response => response.json())
                            .then(stateData => {
                                if (stateData.success) {
                                    const stateSelect = document.getElementById('cityStateSelect');
                                    stateData.states.forEach(state => {
                                        const option = document.createElement('option');
                                        option.value = state.id;
                                        option.textContent = state.name;
                                        if (state.id == cityData.state_id) {
                                            option.selected = true;
                                        }
                                        stateSelect.appendChild(option);
                                    });
                                }
                            })
                            .catch(error => console.error('Error loading dropdown data:', error));

                        document.getElementById('cityForm').addEventListener('submit', saveCity);
                    } else {
                        showNotification(data.message || 'Failed to load city', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading city:', error);
                    showNotification('An error occurred while loading the city', 'error');
                });
        }

        function openDeleteCityModal(cityId, cityName) {
            const modalHTML = `
                <div id="deleteCityModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg max-w-md w-full">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Confirm Delete</h3>
                            <button onclick="closeDeleteCityModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 dark:text-slate-300">
                                Are you sure you want to delete city "<strong>${escapeHtml(cityName)}</strong>"? This action cannot be undone.
                            </p>
                        </div>
                        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700">
                            <button onclick="closeDeleteCityModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                Cancel
                            </button>
                            <button onclick="deleteCity(${cityId})" id="cityDeleteBtn" class="px-5 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">delete</span>
                                Delete City
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        function closeCityModal() {
            const modal = document.getElementById('cityModal');
            if (modal) {
                modal.remove();
            }
        }

        function closeDeleteCityModal() {
            const modal = document.getElementById('deleteCityModal');
            if (modal) {
                modal.remove();
            }
        }

        function clearCityErrors() {
            ['cityCountryError', 'cityStateError', 'cityNameError', 'cityCodeError', 'cityStatusError'].forEach(errorId => {
                const errorElement = document.getElementById(errorId);
                if (errorElement) {
                    errorElement.classList.add('hidden');
                }
            });
        }

        function loadStatesForCityModal() {
            const countryId = document.getElementById('cityCountrySelect').value;
            const stateSelect = document.getElementById('cityStateSelect');

            // Clear existing options
            stateSelect.innerHTML = '<option value="">Select State</option>';

            if (!countryId) return;

            fetch(`<?= base_url('config/getAllStates') ?>?country_id=${countryId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.states.forEach(state => {
                            const option = document.createElement('option');
                            option.value = state.id;
                            option.textContent = state.name;
                            stateSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading states:', error));
        }

        function saveCity(e) {
            e.preventDefault();
            clearCityErrors();

            const cityId = document.getElementById('cityId').value;
            const state_id = document.getElementById('cityStateSelect').value;
            const name = document.getElementById('cityName').value;
            const code = document.getElementById('cityCode').value;
            const status = document.getElementById('cityStatus').value;

            const saveBtn = document.getElementById('saveCityBtn');
            saveBtn.textContent = 'Saving...';
            saveBtn.disabled = true;

            const url = cityId ? `<?= base_url('config/updateCity') ?>/${cityId}` : `<?= base_url('config/createCity') ?>`;
            const method = cityId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ state_id, name, code, status })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeCityModal();
                        loadCities(currentCityPage, currentCitySearch, currentCityStateFilter, currentCityCountryFilter, currentCitySort);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                let errorId = 'city';
                                if (field === 'state_id') {
                                    errorId += 'StateError';
                                } else if (field === 'name') {
                                    errorId += 'NameError';
                                } else if (field === 'code') {
                                    errorId += 'CodeError';
                                } else if (field === 'status') {
                                    errorId += 'StatusError';
                                }
                                const errorElement = document.getElementById(errorId);
                                if (errorElement) {
                                    errorElement.textContent = data.errors[field];
                                    errorElement.classList.remove('hidden');
                                }
                            });
                        }
                        showNotification(data.message || 'Failed to save city', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error saving city:', error);
                    showNotification('An error occurred while saving the city', 'error');
                })
                .finally(() => {
                    saveBtn.disabled = false;
                    saveBtn.textContent = cityId ? 'Save City' : 'Save City';
                });
        }

        function deleteCity(cityId) {
            const deleteBtn = document.getElementById('cityDeleteBtn');
            deleteBtn.textContent = 'Deleting...';
            deleteBtn.disabled = true;

            fetch(`<?= base_url('config/deleteCity') ?>/${cityId}`, { method: 'DELETE' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeDeleteCityModal();
                        loadCities(currentCityPage, currentCitySearch, currentCityStateFilter, currentCityCountryFilter, currentCitySort);
                    } else {
                        showNotification(data.message || 'Failed to delete city', 'error');
                        deleteBtn.textContent = 'Delete City';
                        deleteBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error deleting city:', error);
                    showNotification('An error occurred while deleting the city', 'error');
                    deleteBtn.textContent = 'Delete City';
                    deleteBtn.disabled = false;
                });
        }

        // City search Enter key
        document.addEventListener('keypress', function (e) {
            if (e.target && e.target.id === 'citySearchInput' && e.key === 'Enter') {
                e.preventDefault();
                searchCities();
            }
        });

        // =============== Department Management Functions ===============
        let currentDepartmentPage = 1;
        let currentDepartmentSearch = '';
        let currentDepartmentSort = '';

        function loadDepartments(page = 1, search = '', sortBy = '') {
            currentDepartmentPage = page;
            currentDepartmentSearch = search;
            currentDepartmentSort = sortBy;

            const tbody = document.getElementById('departmentTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                            <span>Loading departments...</span>
                        </div>
                    </td>
                </tr>
            `;

            const params = new URLSearchParams({ page, search, sort_by: sortBy });

            fetch(`<?= base_url('config/getDepartments') ?>?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayDepartments(data.departments);
                        updateDepartmentPagination(data.pagination);
                    } else {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                                    ${data.message || 'Failed to load departments'}
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading departments:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                                An error occurred while loading departments
                            </td>
                        </tr>
                    `;
                });
        }

        function displayDepartments(departments) {
            const tbody = document.getElementById('departmentTableBody');

            if (departments.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                            No departments found
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = departments.map(dept => {
                const statusBadge = dept.status === 'active'
                    ? '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span>';

                return `
                    <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 font-medium">${escapeHtml(dept.code || '-')}</td>
                        <td class="px-4 py-3 font-medium">${escapeHtml(dept.name)}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-slate-400">${escapeHtml(dept.description || '-')}</td>
                        <td class="px-4 py-3">${statusBadge}</td>
                        <td class="px-4 py-3 w-32">
                            <div class="flex gap-2">
                                <button onclick="openEditDepartmentModal(${dept.id})" class="text-primary hover:text-primary/80">
                                    <span class="material-symbols-outlined text-xl">edit</span>
                                </button>
                                <button onclick="openDeleteDepartmentModal(${dept.id}, '${escapeHtml(dept.name)}')" class="text-red-500 hover:text-red-400">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function updateDepartmentPagination(pagination) {
            document.getElementById('departmentShowingFrom').textContent = pagination.from || 0;
            document.getElementById('departmentShowingTo').textContent = pagination.to || 0;
            document.getElementById('departmentTotalCount').textContent = pagination.total || 0;

            const paginationContainer = document.getElementById('departmentPaginationButtons');
            paginationContainer.innerHTML = '';

            // Always show pagination buttons for consistency
            const totalPages = pagination.totalPages || 1;
            const currentPage = pagination.currentPage || 1;

            // Previous button
            const prevBtn = document.createElement('button');
            prevBtn.className = `px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}`;
            prevBtn.innerHTML = '<span class="material-symbols-outlined text-base">chevron_left</span>';
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => {
                if (currentPage > 1) {
                    loadDepartments(currentPage - 1, currentDepartmentSearch, currentDepartmentSort);
                }
            };
            paginationContainer.appendChild(prevBtn);

            // Page numbers
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);

            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = i === currentPage
                    ? 'px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]'
                    : 'px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]';
                pageBtn.textContent = i;
                pageBtn.onclick = () => loadDepartments(i, currentDepartmentSearch, currentDepartmentSort);
                paginationContainer.appendChild(pageBtn);
            }

            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.className = `px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}`;
            nextBtn.innerHTML = '<span class="material-symbols-outlined text-base">chevron_right</span>';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => {
                if (currentPage < totalPages) {
                    loadDepartments(currentPage + 1, currentDepartmentSearch, currentDepartmentSort);
                }
            };
            paginationContainer.appendChild(nextBtn);
        }

        function searchDepartments() {
            const search = document.getElementById('departmentSearchInput').value;
            loadDepartments(1, search, currentDepartmentSort);
        }

        function sortDepartments() {
            const sortBy = document.getElementById('departmentSortSelect').value;
            loadDepartments(1, currentDepartmentSearch, sortBy);
        }

        function openCreateDepartmentModal() {
            const modalHtml = `
                <div id="departmentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 id="departmentModalTitle" class="text-xl font-bold text-gray-800 dark:text-white">Create New Department</h3>
                            <button onclick="closeDepartmentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <form id="departmentForm" class="p-6 space-y-4">
                            <input type="hidden" id="departmentId" value="">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Department Name <span class="text-red-500">*</span></label>
                                <input id="departmentName" type="text" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter department name"/>
                                <span id="departmentNameError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Department Code</label>
                                <input id="departmentCode" type="text" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter department code (optional)"/>
                                <span id="departmentCodeError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                                <textarea id="departmentDescription" rows="3" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter department description (optional)"></textarea>
                                <span id="departmentDescriptionError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                                <select id="departmentStatus" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <span id="departmentStatusError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div class="flex justify-end gap-3 pt-4">
                                <button type="button" onclick="closeDepartmentModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                    Cancel
                                </button>
                                <button type="submit" id="saveDepartmentBtn" class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                    Save Department
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            document.getElementById('departmentForm').addEventListener('submit', saveDepartment);
        }

        function openEditDepartmentModal(departmentId) {
            fetch(`<?= base_url('config/getDepartment') ?>/${departmentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const dept = data.department;

                        const modalHtml = `
                            <div id="departmentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                                        <h3 id="departmentModalTitle" class="text-xl font-bold text-gray-800 dark:text-white">Edit Department</h3>
                                        <button onclick="closeDepartmentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                            <span class="material-symbols-outlined">close</span>
                                        </button>
                                    </div>
                                    <form id="departmentForm" class="p-6 space-y-4">
                                        <input type="hidden" id="departmentId" value="${dept.id}">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Department Name <span class="text-red-500">*</span></label>
                                            <input id="departmentName" type="text" value="${escapeHtml(dept.name)}" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter department name"/>
                                            <span id="departmentNameError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Department Code</label>
                                            <input id="departmentCode" type="text" value="${escapeHtml(dept.code || '')}" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter department code (optional)"/>
                                            <span id="departmentCodeError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                                            <textarea id="departmentDescription" rows="3" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter department description (optional)">${escapeHtml(dept.description || '')}</textarea>
                                            <span id="departmentDescriptionError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                                            <select id="departmentStatus" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                                <option value="active" ${dept.status === 'active' ? 'selected' : ''}>Active</option>
                                                <option value="inactive" ${dept.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                            </select>
                                            <span id="departmentStatusError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div class="flex justify-end gap-3 pt-4">
                                            <button type="button" onclick="closeDepartmentModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                                Cancel
                                            </button>
                                            <button type="submit" id="saveDepartmentBtn" class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                                Save Department
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        `;
                        document.body.insertAdjacentHTML('beforeend', modalHtml);
                        document.getElementById('departmentForm').addEventListener('submit', saveDepartment);
                    } else {
                        showNotification(data.message || 'Failed to load department', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading department:', error);
                    showNotification('An error occurred while loading the department', 'error');
                });
        }

        function openDeleteDepartmentModal(departmentId, departmentName) {
            const modalHTML = `
                <div id="deleteDepartmentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg max-w-md w-full">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Confirm Delete</h3>
                            <button onclick="closeDeleteDepartmentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 dark:text-slate-300">
                                Are you sure you want to delete department "<strong>${escapeHtml(departmentName)}</strong>"? This action cannot be undone.
                            </p>
                        </div>
                        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700">
                            <button onclick="closeDeleteDepartmentModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                Cancel
                            </button>
                            <button onclick="deleteDepartment(${departmentId})" id="departmentDeleteBtn" class="px-5 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">delete</span>
                                Delete Department
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        function closeDepartmentModal() {
            const modal = document.getElementById('departmentModal');
            if (modal) modal.remove();
        }

        function closeDeleteDepartmentModal() {
            const modal = document.getElementById('deleteDepartmentModal');
            if (modal) modal.remove();
        }

        function clearDepartmentErrors() {
            ['departmentNameError', 'departmentCodeError', 'departmentDescriptionError', 'departmentStatusError'].forEach(errorId => {
                const errorElement = document.getElementById(errorId);
                if (errorElement) errorElement.classList.add('hidden');
            });
        }

        function saveDepartment(e) {
            e.preventDefault();
            clearDepartmentErrors();

            const departmentId = document.getElementById('departmentId').value;
            const name = document.getElementById('departmentName').value;
            const code = document.getElementById('departmentCode').value;
            const description = document.getElementById('departmentDescription').value;
            const status = document.getElementById('departmentStatus').value;

            const saveBtn = document.getElementById('saveDepartmentBtn');
            saveBtn.textContent = 'Saving...';
            saveBtn.disabled = true;

            const url = departmentId ? `<?= base_url('config/updateDepartment') ?>/${departmentId}` : `<?= base_url('config/createDepartment') ?>`;
            const method = departmentId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, code, description, status })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeDepartmentModal();
                        loadDepartments(currentDepartmentPage, currentDepartmentSearch, currentDepartmentSort);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const errorId = 'department' + field.charAt(0).toUpperCase() + field.slice(1) + 'Error';
                                const errorElement = document.getElementById(errorId);
                                if (errorElement) {
                                    errorElement.textContent = data.errors[field];
                                    errorElement.classList.remove('hidden');
                                }
                            });
                        }
                        showNotification(data.message || 'Failed to save department', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error saving department:', error);
                    showNotification('An error occurred while saving the department', 'error');
                })
                .finally(() => {
                    saveBtn.disabled = false;
                    saveBtn.textContent = departmentId ? 'Save Department' : 'Save Department';
                });
        }

        function deleteDepartment(departmentId) {
            const deleteBtn = document.getElementById('departmentDeleteBtn');
            deleteBtn.textContent = 'Deleting...';
            deleteBtn.disabled = true;

            fetch(`<?= base_url('config/deleteDepartment') ?>/${departmentId}`, { method: 'DELETE' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeDeleteDepartmentModal();
                        loadDepartments(currentDepartmentPage, currentDepartmentSearch, currentDepartmentSort);
                    } else {
                        showNotification(data.message || 'Failed to delete department', 'error');
                        deleteBtn.textContent = 'Delete Department';
                        deleteBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error deleting department:', error);
                    showNotification('An error occurred while deleting the department', 'error');
                    deleteBtn.textContent = 'Delete Department';
                    deleteBtn.disabled = false;
                });
        }

        // Department search Enter key
        document.addEventListener('keypress', function (e) {
            if (e.target && e.target.id === 'departmentSearchInput' && e.key === 'Enter') {
                e.preventDefault();
                searchDepartments();
            }
        });

        // =============== Designation Management Functions ===============
        let currentDesignationPage = 1;
        let currentDesignationSearch = '';
        let currentDesignationSort = '';

        function loadDesignations(page = 1, search = '', sortBy = '') {
            currentDesignationPage = page;
            currentDesignationSearch = search;
            currentDesignationSort = sortBy;

            const tbody = document.getElementById('designationTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                            <span>Loading designations...</span>
                        </div>
                    </td>
                </tr>
            `;

            const params = new URLSearchParams({ page, search, sort_by: sortBy });

            fetch(`<?= base_url('config/getDesignations') ?>?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayDesignations(data.designations);
                        updateDesignationPagination(data.pagination);
                    } else {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                                    ${data.message || 'Failed to load designations'}
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading designations:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                                An error occurred while loading designations
                            </td>
                        </tr>
                    `;
                });
        }

        function displayDesignations(designations) {
            const tbody = document.getElementById('designationTableBody');

            if (designations.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                            No designations found
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = designations.map(desig => {
                const woFlagBadge = desig.wo_flag === 'yes'
                    ? '<span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span>';
                const statusBadge = desig.status === 'active'
                    ? '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span>';

                return `
                    <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 font-medium">${escapeHtml(desig.code || '-')}</td>
                        <td class="px-4 py-3 font-medium">${escapeHtml(desig.name)}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-slate-400">${escapeHtml(desig.description || '-')}</td>
                        <td class="px-4 py-3">${woFlagBadge}</td>
                        <td class="px-4 py-3">${statusBadge}</td>
                        <td class="px-4 py-3 w-32">
                            <div class="flex gap-2">
                                <button onclick="openEditDesignationModal(${desig.id})" class="text-primary hover:text-primary/80">
                                    <span class="material-symbols-outlined text-xl">edit</span>
                                </button>
                                <button onclick="openDeleteDesignationModal(${desig.id}, '${escapeHtml(desig.name)}')" class="text-red-500 hover:text-red-400">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function updateDesignationPagination(pagination) {
            document.getElementById('designationShowingFrom').textContent = pagination.from || 0;
            document.getElementById('designationShowingTo').textContent = pagination.to || 0;
            document.getElementById('designationTotalCount').textContent = pagination.total || 0;

            const paginationContainer = document.getElementById('designationPaginationButtons');
            paginationContainer.innerHTML = '';

            if (pagination.totalPages <= 1) return;

            // Previous button
            const prevBtn = document.createElement('button');
            prevBtn.className = `px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors ${pagination.currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}`;
            prevBtn.innerHTML = '<span class="material-symbols-outlined text-base">chevron_left</span>';
            prevBtn.disabled = pagination.currentPage === 1;
            prevBtn.onclick = () => {
                if (pagination.currentPage > 1) {
                    loadDesignations(pagination.currentPage - 1, currentDesignationSearch, currentDesignationSort);
                }
            };
            paginationContainer.appendChild(prevBtn);

            // Page numbers
            const startPage = Math.max(1, pagination.currentPage - 2);
            const endPage = Math.min(pagination.totalPages, pagination.currentPage + 2);

            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = i === pagination.currentPage
                    ? 'px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]'
                    : 'px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]';
                pageBtn.textContent = i;
                pageBtn.onclick = () => loadDesignations(i, currentDesignationSearch, currentDesignationSort);
                paginationContainer.appendChild(pageBtn);
            }

            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.className = `px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors ${pagination.currentPage === pagination.totalPages ? 'opacity-50 cursor-not-allowed' : ''}`;
            nextBtn.innerHTML = '<span class="material-symbols-outlined text-base">chevron_right</span>';
            nextBtn.disabled = pagination.currentPage === pagination.totalPages;
            nextBtn.onclick = () => {
                if (pagination.currentPage < pagination.totalPages) {
                    loadDesignations(pagination.currentPage + 1, currentDesignationSearch, currentDesignationSort);
                }
            };
            paginationContainer.appendChild(nextBtn);
        }

        function searchDesignations() {
            const search = document.getElementById('designationSearchInput').value;
            loadDesignations(1, search, currentDesignationSort);
        }

        function sortDesignations() {
            const sortBy = document.getElementById('designationSortSelect').value;
            loadDesignations(1, currentDesignationSearch, sortBy);
        }

        function openCreateDesignationModal() {
            const modalHtml = `
                <div id="designationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Create New Designation</h3>
                            <button onclick="closeDesignationModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <form id="designationForm" class="p-6 space-y-4">
                            <input type="hidden" id="designationId" value="">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Designation Name <span class="text-red-500">*</span></label>
                                <input id="designationName" type="text" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter designation name"/>
                                <span id="designationNameError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Designation Code</label>
                                <input id="designationCode" type="text" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter designation code (optional)"/>
                                <span id="designationCodeError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                                <textarea id="designationDescription" rows="3" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter designation description (optional)"></textarea>
                                <span id="designationDescriptionError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">WO Flag <span class="text-red-500">*</span></label>
                                <select id="designationWoFlag" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                                <span id="designationWoFlagError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                                <select id="designationStatus" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <span id="designationStatusError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div class="flex justify-end gap-3 pt-4">
                                <button type="button" onclick="closeDesignationModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                    Cancel
                                </button>
                                <button type="submit" id="saveDesignationBtn" class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                    Save Designation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            document.getElementById('designationForm').addEventListener('submit', saveDesignation);
        }

        function openEditDesignationModal(designationId) {
            fetch(`<?= base_url('config/getDesignation') ?>/${designationId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const desig = data.designation;

                        const modalHtml = `
                            <div id="designationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Edit Designation</h3>
                                        <button onclick="closeDesignationModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                            <span class="material-symbols-outlined">close</span>
                                        </button>
                                    </div>
                                    <form id="designationForm" class="p-6 space-y-4">
                                        <input type="hidden" id="designationId" value="${desig.id}">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Designation Name <span class="text-red-500">*</span></label>
                                            <input id="designationName" type="text" value="${escapeHtml(desig.name)}" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter designation name"/>
                                            <span id="designationNameError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Designation Code</label>
                                            <input id="designationCode" type="text" value="${escapeHtml(desig.code || '')}" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter designation code (optional)"/>
                                            <span id="designationCodeError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                                            <textarea id="designationDescription" rows="3" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter designation description (optional)">${escapeHtml(desig.description || '')}</textarea>
                                            <span id="designationDescriptionError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">WO Flag <span class="text-red-500">*</span></label>
                                            <select id="designationWoFlag" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                                <option value="no" ${desig.wo_flag === 'no' ? 'selected' : ''}>No</option>
                                                <option value="yes" ${desig.wo_flag === 'yes' ? 'selected' : ''}>Yes</option>
                                            </select>
                                            <span id="designationWoFlagError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                                            <select id="designationStatus" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded px-3 py-2.5 focus:ring-primary focus:border-primary">
                                                <option value="active" ${desig.status === 'active' ? 'selected' : ''}>Active</option>
                                                <option value="inactive" ${desig.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                            </select>
                                            <span id="designationStatusError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div class="flex justify-end gap-3 pt-4">
                                            <button type="button" onclick="closeDesignationModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                                Cancel
                                            </button>
                                            <button type="submit" id="saveDesignationBtn" class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                                Save Designation
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        `;
                        document.body.insertAdjacentHTML('beforeend', modalHtml);
                        document.getElementById('designationForm').addEventListener('submit', saveDesignation);
                    } else {
                        showNotification(data.message || 'Failed to load designation', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading designation:', error);
                    showNotification('An error occurred while loading the designation', 'error');
                });
        }

        function openDeleteDesignationModal(designationId, designationName) {
            const modalHTML = `
                <div id="deleteDesignationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg max-w-md w-full">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Confirm Delete</h3>
                            <button onclick="closeDeleteDesignationModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 dark:text-slate-300">
                                Are you sure you want to delete designation "<strong>${escapeHtml(designationName)}</strong>"? This action cannot be undone.
                            </p>
                        </div>
                        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700">
                            <button onclick="closeDeleteDesignationModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                Cancel
                            </button>
                            <button onclick="deleteDesignation(${designationId})" id="designationDeleteBtn" class="px-5 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">delete</span>
                                Delete Designation
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        function closeDesignationModal() {
            const modal = document.getElementById('designationModal');
            if (modal) modal.remove();
        }

        function closeDeleteDesignationModal() {
            const modal = document.getElementById('deleteDesignationModal');
            if (modal) modal.remove();
        }

        function clearDesignationErrors() {
            ['designationNameError', 'designationCodeError', 'designationDescriptionError', 'designationWoFlagError', 'designationStatusError'].forEach(errorId => {
                const errorElement = document.getElementById(errorId);
                if (errorElement) errorElement.classList.add('hidden');
            });
        }

        function saveDesignation(e) {
            e.preventDefault();
            clearDesignationErrors();

            const designationId = document.getElementById('designationId').value;
            const name = document.getElementById('designationName').value;
            const code = document.getElementById('designationCode').value;
            const description = document.getElementById('designationDescription').value;
            const wo_flag = document.getElementById('designationWoFlag').value;
            const status = document.getElementById('designationStatus').value;

            const saveBtn = document.getElementById('saveDesignationBtn');
            saveBtn.textContent = 'Saving...';
            saveBtn.disabled = true;

            const url = designationId ? `<?= base_url('config/updateDesignation') ?>/${designationId}` : `<?= base_url('config/createDesignation') ?>`;
            const method = designationId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, code, description, wo_flag, status })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeDesignationModal();
                        loadDesignations(currentDesignationPage, currentDesignationSearch, currentDesignationSort);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const errorId = 'designation' + field.charAt(0).toUpperCase() + field.slice(1) + 'Error';
                                const errorElement = document.getElementById(errorId);
                                if (errorElement) {
                                    errorElement.textContent = data.errors[field];
                                    errorElement.classList.remove('hidden');
                                }
                            });
                        }
                        showNotification(data.message || 'Failed to save designation', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error saving designation:', error);
                    showNotification('An error occurred while saving the designation', 'error');
                })
                .finally(() => {
                    saveBtn.disabled = false;
                    saveBtn.textContent = designationId ? 'Save Designation' : 'Save Designation';
                });
        }

        function deleteDesignation(designationId) {
            const deleteBtn = document.getElementById('designationDeleteBtn');
            deleteBtn.textContent = 'Deleting...';
            deleteBtn.disabled = true;

            fetch(`<?= base_url('config/deleteDesignation') ?>/${designationId}`, { method: 'DELETE' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeDeleteDesignationModal();
                        loadDesignations(currentDesignationPage, currentDesignationSearch, currentDesignationSort);
                    } else {
                        showNotification(data.message || 'Failed to delete designation', 'error');
                        deleteBtn.textContent = 'Delete Designation';
                        deleteBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error deleting designation:', error);
                    showNotification('An error occurred while deleting the designation', 'error');
                    deleteBtn.textContent = 'Delete Designation';
                    deleteBtn.disabled = false;
                });
        }

        // Designation search Enter key
        document.addEventListener('keypress', function (e) {
            if (e.target && e.target.id === 'designationSearchInput' && e.key === 'Enter') {
                e.preventDefault();
                searchDesignations();
            }
        });

        // =============== Location Access Management Functions ===============
        let currentLocationPage = 1;
        let currentLocationSearch = '';
        let currentLocationSort = '';

        function loadLocations(page = 1, search = '', sortBy = '') {
            currentLocationPage = page;
            currentLocationSearch = search;
            currentLocationSort = sortBy;

            const tbody = document.getElementById('locationTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                            <span>Loading locations...</span>
                        </div>
                    </td>
                </tr>
            `;

            const params = new URLSearchParams({ page, search, sort_by: sortBy });

            fetch(`<?= base_url('config/getLocations') ?>?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayLocations(data.locations);
                        updateLocationPagination(data.pagination);
                    } else {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="10" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                                    ${data.message || 'Failed to load locations'}
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading locations:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                                An error occurred while loading locations
                            </td>
                        </tr>
                    `;
                });
        }

        function displayLocations(locations) {
            const tbody = document.getElementById('locationTableBody');

            if (locations.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                            No locations found
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = locations.map(loc => {
                const mobileAppBadge = loc.mobile_app === 'enabled'
                    ? '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Disabled</span>';

                const holdAreaBadge = loc.is_hold_area === 'yes'
                    ? '<span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span>';

                const passPrintBadge = loc.visitor_pass_print === 'enabled'
                    ? '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Disabled</span>';

                const turnstileBadge = loc.turnstile === 'active'
                    ? '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span>';

                const statusBadge = loc.status === 'active'
                    ? '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span>';

                const inOutBoundText = loc.in_out_bound.charAt(0).toUpperCase() + loc.in_out_bound.slice(1);

                return `
                    <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 font-medium">${escapeHtml(loc.branch)}</td>
                        <td class="px-4 py-3">${escapeHtml(loc.location_access)}</td>
                        <td class="px-4 py-3">${escapeHtml(loc.adam_ip || '-')}</td>
                        <td class="px-4 py-3">${mobileAppBadge}</td>
                        <td class="px-4 py-3">${holdAreaBadge}</td>
                        <td class="px-4 py-3">${passPrintBadge}</td>
                        <td class="px-4 py-3">${turnstileBadge}</td>
                        <td class="px-4 py-3">${inOutBoundText}</td>
                        <td class="px-4 py-3">${statusBadge}</td>
                        <td class="px-4 py-3 w-32">
                            <div class="flex gap-2">
                                <button onclick="openEditLocationModal(${loc.id})" class="text-primary hover:text-primary/80">
                                    <span class="material-symbols-outlined text-xl">edit</span>
                                </button>
                                <button onclick="openDeleteLocationModal(${loc.id}, '${escapeHtml(loc.branch)} - ${escapeHtml(loc.location_access)}')" class="text-red-500 hover:text-red-400">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function updateLocationPagination(pagination) {
            document.getElementById('locationShowingFrom').textContent = pagination.from || 0;
            document.getElementById('locationShowingTo').textContent = pagination.to || 0;
            document.getElementById('locationTotalCount').textContent = pagination.total || 0;

            const paginationContainer = document.getElementById('locationPaginationButtons');
            paginationContainer.innerHTML = '';

            // Always show pagination buttons for consistency
            const totalPages = pagination.totalPages || 1;
            const currentPage = pagination.currentPage || 1;

            // Previous button
            const prevBtn = document.createElement('button');
            prevBtn.className = `px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}`;
            prevBtn.innerHTML = '<span class="material-symbols-outlined text-base">chevron_left</span>';
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => {
                if (currentPage > 1) {
                    loadLocations(currentPage - 1, currentLocationSearch, currentLocationSort);
                }
            };
            paginationContainer.appendChild(prevBtn);

            // Page numbers
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);

            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = i === currentPage
                    ? 'px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]'
                    : 'px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]';
                pageBtn.textContent = i;
                pageBtn.onclick = () => loadLocations(i, currentLocationSearch, currentLocationSort);
                paginationContainer.appendChild(pageBtn);
            }

            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.className = `px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}`;
            nextBtn.innerHTML = '<span class="material-symbols-outlined text-base">chevron_right</span>';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => {
                if (currentPage < totalPages) {
                    loadLocations(currentPage + 1, currentLocationSearch, currentLocationSort);
                }
            };
            paginationContainer.appendChild(nextBtn);
        }

        function searchLocations() {
            const search = document.getElementById('locationSearchInput').value;
            loadLocations(1, search, currentLocationSort);
        }

        function sortLocations() {
            const sortBy = document.getElementById('locationSortSelect').value;
            loadLocations(1, currentLocationSearch, sortBy);
        }

        function openCreateLocationModal() {
            const modalHtml = `
                <div id="locationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700 sticky top-0 bg-white dark:bg-slate-800 z-10">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Create New Location</h3>
                            <button onclick="closeLocationModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <form id="locationForm" class="p-6 space-y-4">
                            <input type="hidden" id="locationId" value="">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Branch <span class="text-red-500">*</span></label>
                                    <input id="locationBranch" type="text" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter branch name"/>
                                    <span id="locationBranchError" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Location Access <span class="text-red-500">*</span></label>
                                    <input id="locationLocationAccess" type="text" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter location access"/>
                                    <span id="locationLocationAccessError" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Adam IP</label>
                                    <input id="locationAdamIp" type="text" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter IP address (optional)"/>
                                    <span id="locationAdamIpError" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Adam Password</label>
                                    <input id="locationAdamPassword" type="password" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter password (optional)"/>
                                    <span id="locationAdamPasswordError" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Mobile App <span class="text-red-500">*</span></label>
                                    <select id="locationMobileApp" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary">
                                        <option value="enabled">Enabled</option>
                                        <option value="disabled">Disabled</option>
                                    </select>
                                    <span id="locationMobileAppError" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Is Hold Area <span class="text-red-500">*</span></label>
                                    <select id="locationIsHoldArea" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                    <span id="locationIsHoldAreaError" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Visitor Pass Print <span class="text-red-500">*</span></label>
                                    <select id="locationVisitorPassPrint" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary">
                                        <option value="enabled">Enabled</option>
                                        <option value="disabled">Disabled</option>
                                    </select>
                                    <span id="locationVisitorPassPrintError" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Turnstile <span class="text-red-500">*</span></label>
                                    <select id="locationTurnstile" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    <span id="locationTurnstileError" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">In/Out Bound <span class="text-red-500">*</span></label>
                                    <select id="locationInOutBound" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary">
                                        <option value="both">Both</option>
                                        <option value="inbound">Inbound</option>
                                        <option value="outbound">Outbound</option>
                                    </select>
                                    <span id="locationInOutBoundError" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                                    <select id="locationStatus" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    <span id="locationStatusError" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                                <button type="button" onclick="closeLocationModal()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                    Cancel
                                </button>
                                <button type="submit" id="saveLocationBtn" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                    Save Location
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            document.getElementById('locationForm').addEventListener('submit', saveLocation);
        }

        function openEditLocationModal(locationId) {
            fetch(`<?= base_url('config/getLocation') ?>/${locationId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const loc = data.location;

                        const modalHtml = `
                            <div id="locationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700 sticky top-0 bg-white dark:bg-slate-800 z-10">
                                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Edit Location</h3>
                                        <button onclick="closeLocationModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                            <span class="material-symbols-outlined">close</span>
                                        </button>
                                    </div>
                                    <form id="locationForm" class="p-6 space-y-4">
                                        <input type="hidden" id="locationId" value="${loc.id}">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Branch <span class="text-red-500">*</span></label>
                                                <input id="locationBranch" type="text" value="${escapeHtml(loc.branch)}" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter branch name"/>
                                                <span id="locationBranchError" class="text-red-500 text-xs mt-1 hidden"></span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Location Access <span class="text-red-500">*</span></label>
                                                <input id="locationLocationAccess" type="text" value="${escapeHtml(loc.location_access)}" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter location access"/>
                                                <span id="locationLocationAccessError" class="text-red-500 text-xs mt-1 hidden"></span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Adam IP</label>
                                                <input id="locationAdamIp" type="text" value="${escapeHtml(loc.adam_ip || '')}" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Enter IP address (optional)"/>
                                                <span id="locationAdamIpError" class="text-red-500 text-xs mt-1 hidden"></span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Adam Password</label>
                                                <input id="locationAdamPassword" type="password" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary" placeholder="Leave blank to keep current password"/>
                                                <span id="locationAdamPasswordError" class="text-red-500 text-xs mt-1 hidden"></span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Mobile App <span class="text-red-500">*</span></label>
                                                <select id="locationMobileApp" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary">
                                                    <option value="enabled" ${loc.mobile_app === 'enabled' ? 'selected' : ''}>Enabled</option>
                                                    <option value="disabled" ${loc.mobile_app === 'disabled' ? 'selected' : ''}>Disabled</option>
                                                </select>
                                                <span id="locationMobileAppError" class="text-red-500 text-xs mt-1 hidden"></span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Is Hold Area <span class="text-red-500">*</span></label>
                                                <select id="locationIsHoldArea" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary">
                                                    <option value="no" ${loc.is_hold_area === 'no' ? 'selected' : ''}>No</option>
                                                    <option value="yes" ${loc.is_hold_area === 'yes' ? 'selected' : ''}>Yes</option>
                                                </select>
                                                <span id="locationIsHoldAreaError" class="text-red-500 text-xs mt-1 hidden"></span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Visitor Pass Print <span class="text-red-500">*</span></label>
                                                <select id="locationVisitorPassPrint" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary">
                                                    <option value="enabled" ${loc.visitor_pass_print === 'enabled' ? 'selected' : ''}>Enabled</option>
                                                    <option value="disabled" ${loc.visitor_pass_print === 'disabled' ? 'selected' : ''}>Disabled</option>
                                                </select>
                                                <span id="locationVisitorPassPrintError" class="text-red-500 text-xs mt-1 hidden"></span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Turnstile <span class="text-red-500">*</span></label>
                                                <select id="locationTurnstile" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary">
                                                    <option value="active" ${loc.turnstile === 'active' ? 'selected' : ''}>Active</option>
                                                    <option value="inactive" ${loc.turnstile === 'inactive' ? 'selected' : ''}>Inactive</option>
                                                </select>
                                                <span id="locationTurnstileError" class="text-red-500 text-xs mt-1 hidden"></span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">In/Out Bound <span class="text-red-500">*</span></label>
                                                <select id="locationInOutBound" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary">
                                                    <option value="both" ${loc.in_out_bound === 'both' ? 'selected' : ''}>Both</option>
                                                    <option value="inbound" ${loc.in_out_bound === 'inbound' ? 'selected' : ''}>Inbound</option>
                                                    <option value="outbound" ${loc.in_out_bound === 'outbound' ? 'selected' : ''}>Outbound</option>
                                                </select>
                                                <span id="locationInOutBoundError" class="text-red-500 text-xs mt-1 hidden"></span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                                                <select id="locationStatus" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg px-3 py-2.5 focus:ring-primary focus:border-primary">
                                                    <option value="active" ${loc.status === 'active' ? 'selected' : ''}>Active</option>
                                                    <option value="inactive" ${loc.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                                </select>
                                                <span id="locationStatusError" class="text-red-500 text-xs mt-1 hidden"></span>
                                            </div>
                                        </div>
                                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                                            <button type="button" onclick="closeLocationModal()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                                Cancel
                                            </button>
                                            <button type="submit" id="saveLocationBtn" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                                Save Location
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        `;
                        document.body.insertAdjacentHTML('beforeend', modalHtml);
                        document.getElementById('locationForm').addEventListener('submit', saveLocation);
                    } else {
                        showNotification(data.message || 'Failed to load location', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading location:', error);
                    showNotification('An error occurred while loading the location', 'error');
                });
        }

        function openDeleteLocationModal(locationId, locationName) {
            const modalHTML = `
                <div id="deleteLocationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg max-w-md w-full">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Confirm Delete</h3>
                            <button onclick="closeDeleteLocationModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 dark:text-slate-300">
                                Are you sure you want to delete location "<strong>${escapeHtml(locationName)}</strong>"? This action cannot be undone.
                            </p>
                        </div>
                        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700">
                            <button onclick="closeDeleteLocationModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                Cancel
                            </button>
                            <button onclick="deleteLocation(${locationId})" id="locationDeleteBtn" class="px-5 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">delete</span>
                                Delete Location
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        function closeLocationModal() {
            const modal = document.getElementById('locationModal');
            if (modal) modal.remove();
        }

        function closeDeleteLocationModal() {
            const modal = document.getElementById('deleteLocationModal');
            if (modal) modal.remove();
        }

        function clearLocationErrors() {
            const errorFields = ['locationBranchError', 'locationLocationAccessError', 'locationAdamIpError', 'locationAdamPasswordError', 'locationMobileAppError', 'locationIsHoldAreaError', 'locationVisitorPassPrintError', 'locationTurnstileError', 'locationInOutBoundError', 'locationStatusError'];
            errorFields.forEach(errorId => {
                const errorElement = document.getElementById(errorId);
                if (errorElement) errorElement.classList.add('hidden');
            });
        }

        function saveLocation(e) {
            e.preventDefault();
            clearLocationErrors();

            const locationId = document.getElementById('locationId').value;
            const branch = document.getElementById('locationBranch').value;
            const location_access = document.getElementById('locationLocationAccess').value;
            const adam_ip = document.getElementById('locationAdamIp').value;
            const adam_password = document.getElementById('locationAdamPassword').value;
            const mobile_app = document.getElementById('locationMobileApp').value;
            const is_hold_area = document.getElementById('locationIsHoldArea').value;
            const visitor_pass_print = document.getElementById('locationVisitorPassPrint').value;
            const turnstile = document.getElementById('locationTurnstile').value;
            const in_out_bound = document.getElementById('locationInOutBound').value;
            const status = document.getElementById('locationStatus').value;

            const saveBtn = document.getElementById('saveLocationBtn');
            saveBtn.textContent = 'Saving...';
            saveBtn.disabled = true;

            const url = locationId ? `<?= base_url('config/updateLocation') ?>/${locationId}` : `<?= base_url('config/createLocation') ?>`;
            const method = locationId ? 'PUT' : 'POST';

            const requestData = { branch, location_access, adam_ip, mobile_app, is_hold_area, visitor_pass_print, turnstile, in_out_bound, status };

            // Only include password if it's not empty (for updates)
            if (adam_password || !locationId) {
                requestData.adam_password = adam_password;
            }

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(requestData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeLocationModal();
                        loadLocations(currentLocationPage, currentLocationSearch, currentLocationSort);
                        
                        // Update device assignments dropdown lists
                        if (typeof fetchLocationsForDevice === 'function') {
                            fetchLocationsForDevice();
                        }
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const errorId = 'location' + field.charAt(0).toUpperCase() + field.slice(1).replace('_', '') + 'Error';
                                const errorElement = document.getElementById(errorId) || document.getElementById('location' + field.charAt(0).toUpperCase() + field.slice(1) + 'Error');
                                if (errorElement) {
                                    errorElement.textContent = data.errors[field];
                                    errorElement.classList.remove('hidden');
                                }
                            });
                        }
                        showNotification(data.message || 'Failed to save location', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error saving location:', error);
                    showNotification('An error occurred while saving the location', 'error');
                })
                .finally(() => {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Save Location';
                });
        }

        function deleteLocation(locationId) {
            const deleteBtn = document.getElementById('locationDeleteBtn');
            deleteBtn.textContent = 'Deleting...';
            deleteBtn.disabled = true;

            fetch(`<?= base_url('config/deleteLocation') ?>/${locationId}`, { method: 'DELETE' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeDeleteLocationModal();
                        loadLocations(currentLocationPage, currentLocationSearch, currentLocationSort);
                        
                        // Update device assignments dropdown lists
                        if (typeof fetchLocationsForDevice === 'function') {
                            fetchLocationsForDevice();
                        }
                    } else {
                        showNotification(data.message || 'Failed to delete location', 'error');
                        deleteBtn.textContent = 'Delete Location';
                        deleteBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error deleting location:', error);
                    showNotification('An error occurred while deleting the location', 'error');
                    deleteBtn.textContent = 'Delete Location';
                    deleteBtn.disabled = false;
                });
        }

        // Location search Enter key
        document.addEventListener('keypress', function (e) {
            if (e.target && e.target.id === 'locationSearchInput' && e.key === 'Enter') {
                e.preventDefault();
                searchLocations();
            }
        });

        // ============================================
        // Lane Management Functions
        // ============================================

        let currentLanePage = 1;
        let currentLaneSearch = '';
        let currentLaneSort = '';
        let currentLaneId = null;

        function loadLanes(page = 1, search = '', sortBy = '') {
            currentLanePage = page;
            currentLaneSearch = search;
            currentLaneSort = sortBy;

            const tbody = document.getElementById('laneTableBody');
            tbody.innerHTML = '<tr><td colspan="19" class="px-4 py-8 text-center"><div class="flex justify-center items-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div></div></td></tr>';

            const params = new URLSearchParams({ page, limit: 10, search, sortBy });

            fetch(`<?= base_url('config/getLanes') ?>?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayLanes(data.data);
                        updateLanePagination(data.pagination);
                    } else {
                        tbody.innerHTML = '<tr><td colspan="19" class="px-4 py-8 text-center text-red-500">Failed to load lanes</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error loading lanes:', error);
                    tbody.innerHTML = '<tr><td colspan="19" class="px-4 py-8 text-center text-red-500">An error occurred while loading lanes</td></tr>';
                });
        }

        function displayLanes(lanes) {
            const tbody = document.getElementById('laneTableBody');

            if (lanes.length === 0) {
                tbody.innerHTML = '<tr><td colspan="19" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">No lanes found</td></tr>';
                return;
            }

            tbody.innerHTML = lanes.map(lane => {
                const slipPrintBadge = lane.slip_print === 'enabled'
                    ? '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Disabled</span>';

                const inBoundBadge = lane.in_bound === 'yes'
                    ? '<span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span>';

                const outBoundBadge = lane.out_bound === 'yes'
                    ? '<span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span>';

                const statusBadge = lane.status === 'active'
                    ? '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span>';

                return `
                    <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 font-medium">${escapeHtml(lane.lane)}</td>
                        <td class="px-4 py-3">${escapeHtml(lane.location_access || '-')}</td>
                        <td class="px-4 py-3">${escapeHtml(lane.barrier_no || '-')}</td>
                        <td class="px-4 py-3">${escapeHtml(lane.weight_id || '-')}</td>
                        <td class="px-4 py-3">${slipPrintBadge}</td>
                        <td class="px-4 py-3">${escapeHtml(lane.antena_ip || '-')}</td>
                        <td class="px-4 py-3">${escapeHtml(lane.kiosk_ip || '-')}</td>
                        <td class="px-4 py-3">${escapeHtml(lane.cam_id_1 || '-')}</td>
                        <td class="px-4 py-3">${escapeHtml(lane.cam_id_2 || '-')}</td>
                        <td class="px-4 py-3">${escapeHtml(lane.cam_id_3 || '-')}</td>
                        <td class="px-4 py-3">${escapeHtml(lane.cam_photo_ip_1 || '-')}</td>
                        <td class="px-4 py-3">${escapeHtml(lane.cam_photo_ip_2 || '-')}</td>
                        <td class="px-4 py-3">${inBoundBadge}</td>
                        <td class="px-4 py-3">${outBoundBadge}</td>
                        <td class="px-4 py-3">${escapeHtml(lane.last_logged_in_by || '-')}</td>
                        <td class="px-4 py-3">${lane.last_logged_in_datetime ? formatDateTime(lane.last_logged_in_datetime) : '-'}</td>
                        <td class="px-4 py-3">${lane.last_changed_on_printer_paper ? formatDateTime(lane.last_changed_on_printer_paper) : '-'}</td>
                        <td class="px-4 py-3">${statusBadge}</td>
                        <td class="px-4 py-3 w-32">
                            <div class="flex gap-2">
                                <button onclick="openEditLaneModal(${lane.id})" class="text-primary hover:text-primary/80">
                                    <span class="material-symbols-outlined text-xl">edit</span>
                                </button>
                                <button onclick="openDeleteLaneModal(${lane.id}, '${escapeHtml(lane.lane)}')" class="text-red-500 hover:text-red-400">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function updateLanePagination(pagination) {
            const showingFrom = pagination.total === 0 ? 0 : ((pagination.page - 1) * pagination.limit) + 1;
            const showingTo = Math.min(pagination.page * pagination.limit, pagination.total);

            document.getElementById('laneShowingFrom').textContent = showingFrom;
            document.getElementById('laneShowingTo').textContent = showingTo;
            document.getElementById('laneTotalCount').textContent = pagination.total;

            const paginationButtons = document.getElementById('lanePaginationButtons');
            let buttonsHTML = '';

            // Previous button
            buttonsHTML += `
                <button onclick="loadLanes(${pagination.page - 1}, currentLaneSearch, currentLaneSort)" 
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                        ${pagination.page === 1 ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            `;

            // Page numbers
            for (let i = 1; i <= pagination.totalPages; i++) {
                if (i === pagination.page) {
                    buttonsHTML += `
                        <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">${i}</button>
                    `;
                } else {
                    buttonsHTML += `
                        <button onclick="loadLanes(${i}, currentLaneSearch, currentLaneSort)" 
                                class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">${i}</button>
                    `;
                }
            }

            // Next button
            buttonsHTML += `
                <button onclick="loadLanes(${pagination.page + 1}, currentLaneSearch, currentLaneSort)" 
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                        ${pagination.page === pagination.totalPages ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
            `;

            paginationButtons.innerHTML = buttonsHTML;
        }

        function searchLanes() {
            const searchInput = document.getElementById('laneSearchInput');
            const sortSelect = document.getElementById('laneSortSelect');
            loadLanes(1, searchInput.value, sortSelect.value);
        }

        function sortLanes() {
            const searchInput = document.getElementById('laneSearchInput');
            const sortSelect = document.getElementById('laneSortSelect');
            loadLanes(1, searchInput.value, sortSelect.value);
        }

        function openCreateLaneModal() {
            // Load locations for dropdown
            fetch(`<?= base_url('config/getLocations') ?>?limit=100`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        showNotification('Failed to load locations', 'error');
                        return;
                    }

                    const locationOptions = data.locations.map(loc =>
                        `<option value="${loc.id}">${escapeHtml(loc.branch)} - ${escapeHtml(loc.location_access)}</option>`
                    ).join('');

                    const modalHTML = `
                        <div id="laneModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                            <div class="bg-white dark:bg-slate-800 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Create New Lane</h3>
                                    <button onclick="closeLaneModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>
                                </div>
                                <form onsubmit="saveLane(event)" class="p-6">
                                    <div id="laneErrorContainer" class="hidden mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg text-sm"></div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Lane Name <span class="text-red-500">*</span></label>
                                            <input type="text" id="laneName" name="lane" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Location <span class="text-red-500">*</span></label>
                                            <select id="laneLocation" name="location_id" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                                <option value="">Select Location</option>
                                                ${locationOptions}
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Barrier No</label>
                                            <input type="text" id="laneBarrierNo" name="barrier_no" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Weight ID</label>
                                            <input type="text" id="laneWeightId" name="weight_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Slip Print <span class="text-red-500">*</span></label>
                                            <select id="laneSlipPrint" name="slip_print" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                                <option value="enabled">Enabled</option>
                                                <option value="disabled">Disabled</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Antena IP</label>
                                            <input type="text" id="laneAntenaIp" name="antena_ip" placeholder="e.g., 192.168.1.50" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">KIOSK IP</label>
                                            <input type="text" id="laneKioskIp" name="kiosk_ip" placeholder="e.g., 192.168.1.51" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Camera ID 1</label>
                                            <input type="text" id="laneCamId1" name="cam_id_1" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Camera ID 2</label>
                                            <input type="text" id="laneCamId2" name="cam_id_2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Camera ID 3</label>
                                            <input type="text" id="laneCamId3" name="cam_id_3" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Camera Photo IP 1</label>
                                            <input type="text" id="laneCamPhotoIp1" name="cam_photo_ip_1" placeholder="e.g., 192.168.1.60" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Camera Photo IP 2</label>
                                            <input type="text" id="laneCamPhotoIp2" name="cam_photo_ip_2" placeholder="e.g., 192.168.1.61" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">In Bound <span class="text-red-500">*</span></label>
                                            <select id="laneInBound" name="in_bound" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Out Bound <span class="text-red-500">*</span></label>
                                            <select id="laneOutBound" name="out_bound" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Status <span class="text-red-500">*</span></label>
                                            <select id="laneStatus" name="status" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- RFID Reader Settings -->
                                    <div class="mt-6 mb-6">
                                        <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                                            <span class="material-symbols-outlined text-primary">sensors</span>
                                            RFID Reader Settings
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">RFID Reader IP</label>
                                                <input type="text" id="laneRfidReaderIp" name="rfid_reader_ip" placeholder="e.g., 192.168.1.100" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">IP address of RFID antenna</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">RFID Reader Port</label>
                                                <input type="number" id="laneRfidReaderPort" name="rfid_reader_port" value="49152" placeholder="49152" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Default: 49152</p>
                                            </div>
                                            <div class="flex items-center">
                                                <label class="relative inline-flex items-center cursor-pointer mt-6">
                                                    <input type="checkbox" id="laneRfidEnabled" name="rfid_enabled" value="1" class="sr-only peer">
                                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300">RFID Enabled</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex gap-3 justify-end pt-4 border-t border-gray-200 dark:border-slate-700">
                                        <button type="button" onclick="closeLaneModal()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                            Create Lane
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    `;

                    document.body.insertAdjacentHTML('beforeend', modalHTML);
                })
                .catch(error => {
                    console.error('Error loading locations:', error);
                    showNotification('Failed to load locations', 'error');
                });
        }

        function openEditLaneModal(laneId) {
            currentLaneId = laneId;

            // Load locations and lane data in parallel
            Promise.all([
                fetch(`<?= base_url('config/getLocations') ?>?limit=100`).then(r => r.json()),
                fetch(`<?= base_url('config/getLane') ?>/${laneId}`).then(r => r.json())
            ])
                .then(([locationsData, laneData]) => {
                    if (!locationsData.success || !laneData.success) {
                        showNotification('Failed to load data', 'error');
                        return;
                    }

                    const locationOptions = locationsData.locations.map(loc =>
                        `<option value="${loc.id}" ${loc.id === laneData.data.location_id ? 'selected' : ''}>${escapeHtml(loc.branch)} - ${escapeHtml(loc.location_access)}</option>`
                    ).join('');

                    const lane = laneData.data;

                    const modalHTML = `
                    <div id="laneModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                        <div class="bg-white dark:bg-slate-800 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Edit Lane</h3>
                                <button onclick="closeLaneModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <span class="material-symbols-outlined">close</span>
                                </button>
                            </div>
                            <form onsubmit="saveLane(event)" class="p-6">
                                <div id="laneErrorContainer" class="hidden mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg text-sm"></div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Lane Name <span class="text-red-500">*</span></label>
                                        <input type="text" id="laneName" name="lane" value="${escapeHtml(lane.lane)}" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Location <span class="text-red-500">*</span></label>
                                        <select id="laneLocation" name="location_id" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                            <option value="">Select Location</option>
                                            ${locationOptions}
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Barrier No</label>
                                        <input type="text" id="laneBarrierNo" name="barrier_no" value="${escapeHtml(lane.barrier_no || '')}" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Weight ID</label>
                                        <input type="text" id="laneWeightId" name="weight_id" value="${escapeHtml(lane.weight_id || '')}" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Slip Print <span class="text-red-500">*</span></label>
                                        <select id="laneSlipPrint" name="slip_print" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                            <option value="enabled" ${lane.slip_print === 'enabled' ? 'selected' : ''}>Enabled</option>
                                            <option value="disabled" ${lane.slip_print === 'disabled' ? 'selected' : ''}>Disabled</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Antena IP</label>
                                        <input type="text" id="laneAntenaIp" name="antena_ip" value="${escapeHtml(lane.antena_ip || '')}" placeholder="e.g., 192.168.1.50" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">KIOSK IP</label>
                                        <input type="text" id="laneKioskIp" name="kiosk_ip" value="${escapeHtml(lane.kiosk_ip || '')}" placeholder="e.g., 192.168.1.51" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Camera ID 1</label>
                                        <input type="text" id="laneCamId1" name="cam_id_1" value="${escapeHtml(lane.cam_id_1 || '')}" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Camera ID 2</label>
                                        <input type="text" id="laneCamId2" name="cam_id_2" value="${escapeHtml(lane.cam_id_2 || '')}" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Camera ID 3</label>
                                        <input type="text" id="laneCamId3" name="cam_id_3" value="${escapeHtml(lane.cam_id_3 || '')}" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Camera Photo IP 1</label>
                                        <input type="text" id="laneCamPhotoIp1" name="cam_photo_ip_1" value="${escapeHtml(lane.cam_photo_ip_1 || '')}" placeholder="e.g., 192.168.1.60" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Camera Photo IP 2</label>
                                        <input type="text" id="laneCamPhotoIp2" name="cam_photo_ip_2" value="${escapeHtml(lane.cam_photo_ip_2 || '')}" placeholder="e.g., 192.168.1.61" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">In Bound <span class="text-red-500">*</span></label>
                                        <select id="laneInBound" name="in_bound" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                            <option value="yes" ${lane.in_bound === 'yes' ? 'selected' : ''}>Yes</option>
                                            <option value="no" ${lane.in_bound === 'no' ? 'selected' : ''}>No</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Out Bound <span class="text-red-500">*</span></label>
                                        <select id="laneOutBound" name="out_bound" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                            <option value="yes" ${lane.out_bound === 'yes' ? 'selected' : ''}>Yes</option>
                                            <option value="no" ${lane.out_bound === 'no' ? 'selected' : ''}>No</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Status <span class="text-red-500">*</span></label>
                                        <select id="laneStatus" name="status" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                            <option value="active" ${lane.status === 'active' ? 'selected' : ''}>Active</option>
                                            <option value="inactive" ${lane.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- RFID Reader Settings -->
                                <div class="mt-6 mb-6">
                                    <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary">sensors</span>
                                        RFID Reader Settings
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">RFID Reader IP</label>
                                            <input type="text" id="laneRfidReaderIp" name="rfid_reader_ip" value="${escapeHtml(lane.rfid_reader_ip || '')}" placeholder="e.g., 192.168.1.100" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">IP address of RFID antenna</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">RFID Reader Port</label>
                                            <input type="number" id="laneRfidReaderPort" name="rfid_reader_port" value="${lane.rfid_reader_port || 49152}" placeholder="49152" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Default: 49152</p>
                                        </div>
                                        <div class="flex items-center">
                                            <label class="relative inline-flex items-center cursor-pointer mt-6">
                                                <input type="checkbox" id="laneRfidEnabled" name="rfid_enabled" value="1" ${lane.rfid_enabled ? 'checked' : ''} class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300">RFID Enabled</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex gap-3 justify-end pt-4 border-t border-gray-200 dark:border-slate-700">
                                    <button type="button" onclick="closeLaneModal()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                        Update Lane
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;

                    document.body.insertAdjacentHTML('beforeend', modalHTML);
                })
                .catch(error => {
                    console.error('Error loading data:', error);
                    showNotification('Failed to load data', 'error');
                });
        }

        function openDeleteLaneModal(laneId, laneName) {
            currentLaneId = laneId;

            const modalHTML = `
                <div id="deleteLaneModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg max-w-md w-full">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Delete Lane</h3>
                            <button onclick="closeDeleteLaneModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-700 dark:text-slate-300 mb-6">
                                Are you sure you want to delete <strong>${laneName}</strong>? This action cannot be undone.
                            </p>
                            <div class="flex gap-3 justify-end">
                                <button onclick="closeDeleteLaneModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                    Cancel
                                </button>
                                <button onclick="deleteLane()" class="px-5 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm">
                                    Delete Lane
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        function closeLaneModal() {
            const modal = document.getElementById('laneModal');
            if (modal) modal.remove();
            currentLaneId = null;
        }

        function closeDeleteLaneModal() {
            const modal = document.getElementById('deleteLaneModal');
            if (modal) modal.remove();
            currentLaneId = null;
        }

        function clearLaneErrors() {
            const errorContainer = document.getElementById('laneErrorContainer');
            if (errorContainer) {
                errorContainer.classList.add('hidden');
                errorContainer.innerHTML = '';
            }
        }

        function saveLane(e) {
            e.preventDefault();
            clearLaneErrors();

            const formData = {
                lane: document.getElementById('laneName').value,
                location_id: document.getElementById('laneLocation').value,
                barrier_no: document.getElementById('laneBarrierNo').value || null,
                weight_id: document.getElementById('laneWeightId').value || null,
                slip_print: document.getElementById('laneSlipPrint').value,
                antena_ip: document.getElementById('laneAntenaIp').value || null,
                kiosk_ip: document.getElementById('laneKioskIp').value || null,
                cam_id_1: document.getElementById('laneCamId1').value || null,
                cam_id_2: document.getElementById('laneCamId2').value || null,
                cam_id_3: document.getElementById('laneCamId3').value || null,
                cam_photo_ip_1: document.getElementById('laneCamPhotoIp1').value || null,
                cam_photo_ip_2: document.getElementById('laneCamPhotoIp2').value || null,
                in_bound: document.getElementById('laneInBound').value,
                out_bound: document.getElementById('laneOutBound').value,
                status: document.getElementById('laneStatus').value,
                rfid_reader_ip: document.getElementById('laneRfidReaderIp')?.value || null,
                rfid_reader_port: document.getElementById('laneRfidReaderPort')?.value || null,
                rfid_enabled: document.getElementById('laneRfidEnabled')?.checked ? 1 : 0
            };

            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = currentLaneId ? 'Updating...' : 'Creating...';

            const url = currentLaneId ? `<?= base_url('config/updateLane') ?>/${currentLaneId}` : `<?= base_url('config/createLane') ?>`;
            const method = currentLaneId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeLaneModal();
                        loadLanes(currentLanePage, currentLaneSearch, currentLaneSort);
                    } else {
                        const errorContainer = document.getElementById('laneErrorContainer');
                        if (data.errors) {
                            const errorMessages = Object.values(data.errors).flat();
                            errorContainer.innerHTML = errorMessages.map(msg => `<div>• ${msg}</div>`).join('');
                        } else {
                            errorContainer.innerHTML = data.message || 'Failed to save lane';
                        }
                        errorContainer.classList.remove('hidden');
                        submitBtn.textContent = currentLaneId ? 'Update Lane' : 'Create Lane';
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error saving lane:', error);
                    showNotification('An error occurred while saving the lane', 'error');
                    submitBtn.textContent = currentLaneId ? 'Update Lane' : 'Create Lane';
                    submitBtn.disabled = false;
                });
        }

        function deleteLane() {
            const deleteBtn = event.target;
            deleteBtn.textContent = 'Deleting...';
            deleteBtn.disabled = true;

            fetch(`<?= base_url('config/deleteLane') ?>/${currentLaneId}`, {
                method: 'DELETE'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeDeleteLaneModal();
                        loadLanes(currentLanePage, currentLaneSearch, currentLaneSort);
                    } else {
                        showNotification(data.message || 'Failed to delete lane', 'error');
                        deleteBtn.textContent = 'Delete Lane';
                        deleteBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error deleting lane:', error);
                    showNotification('An error occurred while deleting the lane', 'error');
                    deleteBtn.textContent = 'Delete Lane';
                    deleteBtn.disabled = false;
                });
        }

        // Lane search Enter key
        document.addEventListener('keypress', function (e) {
            if (e.target && e.target.id === 'laneSearchInput' && e.key === 'Enter') {
                e.preventDefault();
                searchLanes();
            }
        });

        // ============================================
        // Reject Reason Management Functions
        // ============================================

        let currentRejectReasonPage = 1;
        let currentRejectReasonSearch = '';
        let currentRejectReasonSort = '';
        let currentRejectReasonId = null;

        function loadRejectReasons(page = 1, search = '', sortBy = '') {
            currentRejectReasonPage = page;
            currentRejectReasonSearch = search;
            currentRejectReasonSort = sortBy;

            const tbody = document.getElementById('rejectReasonTableBody');
            tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center"><div class="flex justify-center items-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div></div></td></tr>';

            const params = new URLSearchParams({ page, limit: 10, search, sortBy });

            fetch(`<?= base_url('config/getRejectReasons') ?>?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayRejectReasons(data.data);
                        updateRejectReasonPagination(data.pagination);
                    } else {
                        tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-red-500">Failed to load reject reasons</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error loading reject reasons:', error);
                    tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-red-500">An error occurred while loading reject reasons</td></tr>';
                });
        }

        function displayRejectReasons(reasons) {
            const tbody = document.getElementById('rejectReasonTableBody');

            if (reasons.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">No reject reasons found</td></tr>';
                return;
            }

            tbody.innerHTML = reasons.map(reason => {
                const mobileAppBadge = reason.mobile_app === 'enabled'
                    ? '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Disabled</span>';

                const commercialBadge = reason.commercial === 'yes'
                    ? '<span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span>';

                const statusBadge = reason.status === 'active'
                    ? '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span>';

                return `
                    <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 font-medium">${escapeHtml(reason.reason)}</td>
                        <td class="px-4 py-3">${mobileAppBadge}</td>
                        <td class="px-4 py-3">${commercialBadge}</td>
                        <td class="px-4 py-3">${statusBadge}</td>
                        <td class="px-4 py-3 w-32">
                            <div class="flex gap-2">
                                <button onclick="openEditRejectReasonModal(${reason.id})" class="text-primary hover:text-primary/80">
                                    <span class="material-symbols-outlined text-xl">edit</span>
                                </button>
                                <button onclick="openDeleteRejectReasonModal(${reason.id}, '${escapeHtml(reason.reason)}')" class="text-red-500 hover:text-red-400">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function updateRejectReasonPagination(pagination) {
            const showingFrom = pagination.total === 0 ? 0 : ((pagination.page - 1) * pagination.limit) + 1;
            const showingTo = Math.min(pagination.page * pagination.limit, pagination.total);

            document.getElementById('rejectReasonShowingFrom').textContent = showingFrom;
            document.getElementById('rejectReasonShowingTo').textContent = showingTo;
            document.getElementById('rejectReasonTotalCount').textContent = pagination.total;

            const paginationButtons = document.getElementById('rejectReasonPaginationButtons');
            let buttonsHTML = '';

            // Previous button
            buttonsHTML += `
                <button onclick="loadRejectReasons(${pagination.page - 1}, currentRejectReasonSearch, currentRejectReasonSort)" 
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                        ${pagination.page === 1 ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            `;

            // Page numbers
            for (let i = 1; i <= pagination.totalPages; i++) {
                if (i === pagination.page) {
                    buttonsHTML += `
                        <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">${i}</button>
                    `;
                } else {
                    buttonsHTML += `
                        <button onclick="loadRejectReasons(${i}, currentRejectReasonSearch, currentRejectReasonSort)" 
                                class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">${i}</button>
                    `;
                }
            }

            // Next button
            buttonsHTML += `
                <button onclick="loadRejectReasons(${pagination.page + 1}, currentRejectReasonSearch, currentRejectReasonSort)" 
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                        ${pagination.page === pagination.totalPages ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
            `;

            paginationButtons.innerHTML = buttonsHTML;
        }

        function searchRejectReasons() {
            const searchInput = document.getElementById('rejectReasonSearchInput');
            const sortSelect = document.getElementById('rejectReasonSortSelect');
            loadRejectReasons(1, searchInput.value, sortSelect.value);
        }

        function sortRejectReasons() {
            const searchInput = document.getElementById('rejectReasonSearchInput');
            const sortSelect = document.getElementById('rejectReasonSortSelect');
            loadRejectReasons(1, searchInput.value, sortSelect.value);
        }

        function openCreateRejectReasonModal() {
            const modalHTML = `
                <div id="rejectReasonModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Create New Reject Reason</h3>
                            <button onclick="closeRejectReasonModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <form onsubmit="saveRejectReason(event)" class="p-6">
                            <div id="rejectReasonErrorContainer" class="hidden mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg text-sm"></div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Reject Reason <span class="text-red-500">*</span></label>
                                    <input type="text" id="rejectReasonName" name="reason" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Mobile App <span class="text-red-500">*</span></label>
                                    <select id="rejectReasonMobileApp" name="mobile_app" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                        <option value="enabled">Enabled</option>
                                        <option value="disabled">Disabled</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Commercial <span class="text-red-500">*</span></label>
                                    <select id="rejectReasonCommercial" name="commercial" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Status <span class="text-red-500">*</span></label>
                                    <select id="rejectReasonStatus" name="status" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="flex gap-3 justify-end pt-4 border-t border-gray-200 dark:border-slate-700">
                                <button type="button" onclick="closeRejectReasonModal()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                    Create Reject Reason
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        function openEditRejectReasonModal(reasonId) {
            currentRejectReasonId = reasonId;

            fetch(`<?= base_url('config/getRejectReason') ?>/${reasonId}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        showNotification('Failed to load reject reason', 'error');
                        return;
                    }

                    const reason = data.data;

                    const modalHTML = `
                        <div id="rejectReasonModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                            <div class="bg-white dark:bg-slate-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Edit Reject Reason</h3>
                                    <button onclick="closeRejectReasonModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>
                                </div>
                                <form onsubmit="saveRejectReason(event)" class="p-6">
                                    <div id="rejectReasonErrorContainer" class="hidden mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg text-sm"></div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Reject Reason <span class="text-red-500">*</span></label>
                                            <input type="text" id="rejectReasonName" name="reason" value="${escapeHtml(reason.reason)}" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Mobile App <span class="text-red-500">*</span></label>
                                            <select id="rejectReasonMobileApp" name="mobile_app" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                                <option value="enabled" ${reason.mobile_app === 'enabled' ? 'selected' : ''}>Enabled</option>
                                                <option value="disabled" ${reason.mobile_app === 'disabled' ? 'selected' : ''}>Disabled</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Commercial <span class="text-red-500">*</span></label>
                                            <select id="rejectReasonCommercial" name="commercial" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                                <option value="yes" ${reason.commercial === 'yes' ? 'selected' : ''}>Yes</option>
                                                <option value="no" ${reason.commercial === 'no' ? 'selected' : ''}>No</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Status <span class="text-red-500">*</span></label>
                                            <select id="rejectReasonStatus" name="status" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary">
                                                <option value="active" ${reason.status === 'active' ? 'selected' : ''}>Active</option>
                                                <option value="inactive" ${reason.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="flex gap-3 justify-end pt-4 border-t border-gray-200 dark:border-slate-700">
                                        <button type="button" onclick="closeRejectReasonModal()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                            Update Reject Reason
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    `;

                    document.body.insertAdjacentHTML('beforeend', modalHTML);
                })
                .catch(error => {
                    console.error('Error loading reject reason:', error);
                    showNotification('Failed to load reject reason', 'error');
                });
        }

        function openDeleteRejectReasonModal(reasonId, reasonName) {
            currentRejectReasonId = reasonId;

            const modalHTML = `
                <div id="deleteRejectReasonModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg max-w-md w-full">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Delete Reject Reason</h3>
                            <button onclick="closeDeleteRejectReasonModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-700 dark:text-slate-300 mb-6">
                                Are you sure you want to delete <strong>${reasonName}</strong>? This action cannot be undone.
                            </p>
                            <div class="flex gap-3 justify-end">
                                <button onclick="closeDeleteRejectReasonModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                    Cancel
                                </button>
                                <button onclick="deleteRejectReason()" class="px-5 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm">
                                    Delete Reject Reason
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        function closeRejectReasonModal() {
            const modal = document.getElementById('rejectReasonModal');
            if (modal) modal.remove();
            currentRejectReasonId = null;
        }

        function closeDeleteRejectReasonModal() {
            const modal = document.getElementById('deleteRejectReasonModal');
            if (modal) modal.remove();
            currentRejectReasonId = null;
        }

        function clearRejectReasonErrors() {
            const errorContainer = document.getElementById('rejectReasonErrorContainer');
            if (errorContainer) {
                errorContainer.classList.add('hidden');
                errorContainer.innerHTML = '';
            }
        }

        function saveRejectReason(e) {
            e.preventDefault();
            clearRejectReasonErrors();

            const formData = {
                reason: document.getElementById('rejectReasonName').value,
                mobile_app: document.getElementById('rejectReasonMobileApp').value,
                commercial: document.getElementById('rejectReasonCommercial').value,
                status: document.getElementById('rejectReasonStatus').value
            };

            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = currentRejectReasonId ? 'Updating...' : 'Creating...';

            const url = currentRejectReasonId ? `<?= base_url('config/updateRejectReason') ?>/${currentRejectReasonId}` : `<?= base_url('config/createRejectReason') ?>`;
            const method = currentRejectReasonId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeRejectReasonModal();
                        loadRejectReasons(currentRejectReasonPage, currentRejectReasonSearch, currentRejectReasonSort);
                    } else {
                        const errorContainer = document.getElementById('rejectReasonErrorContainer');
                        if (data.errors) {
                            const errorMessages = Object.values(data.errors).flat();
                            errorContainer.innerHTML = errorMessages.map(msg => `<div>• ${msg}</div>`).join('');
                        } else {
                            errorContainer.innerHTML = data.message || 'Failed to save reject reason';
                        }
                        errorContainer.classList.remove('hidden');
                        submitBtn.textContent = currentRejectReasonId ? 'Update Reject Reason' : 'Create Reject Reason';
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error saving reject reason:', error);
                    showNotification('An error occurred while saving the reject reason', 'error');
                    submitBtn.textContent = currentRejectReasonId ? 'Update Reject Reason' : 'Create Reject Reason';
                    submitBtn.disabled = false;
                });
        }

        function deleteRejectReason() {
            const deleteBtn = event.target;
            deleteBtn.textContent = 'Deleting...';
            deleteBtn.disabled = true;

            fetch(`<?= base_url('config/deleteRejectReason') ?>/${currentRejectReasonId}`, {
                method: 'DELETE'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeDeleteRejectReasonModal();
                        loadRejectReasons(currentRejectReasonPage, currentRejectReasonSearch, currentRejectReasonSort);
                    } else {
                        showNotification(data.message || 'Failed to delete reject reason', 'error');
                        deleteBtn.textContent = 'Delete Reject Reason';
                        deleteBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error deleting reject reason:', error);
                    showNotification('An error occurred while deleting the reject reason', 'error');
                    deleteBtn.textContent = 'Delete Reject Reason';
                    deleteBtn.disabled = false;
                });
        }

        // Reject reason search Enter key
        document.addEventListener('keypress', function (e) {
            if (e.target && e.target.id === 'rejectReasonSearchInput' && e.key === 'Enter') {
                e.preventDefault();
                searchRejectReasons();
            }
        });

        // ========================
        // Visitor Card Management Functions
        // ========================

        let currentVisitorCardPage = 1;
        let currentVisitorCardSearch = '';
        let currentVisitorCardSort = '';
        let currentVisitorCardId = null;

        function loadVisitorCards(page = 1) {
            currentVisitorCardPage = page;

            fetch(`<?= base_url('config/getVisitorCards') ?>?page=${page}&search=${encodeURIComponent(currentVisitorCardSearch)}&sort=${currentVisitorCardSort}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayVisitorCards(data.data, data.pagination);
                    } else {
                        document.getElementById('visitorCardTableBody').innerHTML = `
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="material-symbols-outlined text-red-500 text-5xl mb-2">error</span>
                                        <p class="text-gray-500 dark:text-slate-400">${data.message || 'Error loading visitor cards'}</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading visitor cards:', error);
                    document.getElementById('visitorCardTableBody').innerHTML = `
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-symbols-outlined text-red-500 text-5xl mb-2">error</span>
                                    <p class="text-gray-500 dark:text-slate-400">An error occurred while loading visitor cards</p>
                                </div>
                            </td>
                        </tr>
                    `;
                });
        }

        function displayVisitorCards(visitorCards, pagination) {
            const tbody = document.getElementById('visitorCardTableBody');

            if (visitorCards.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <span class="material-symbols-outlined text-gray-400 text-5xl mb-2">inventory_2</span>
                                <p class="text-gray-500 dark:text-slate-400">No visitor cards found</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = visitorCards.map(card => {
                let statusBadge = '';
                const status = card.status.toLowerCase();

                if (status === 'active') {
                    statusBadge = '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span>';
                } else if (status === 'in_use') {
                    statusBadge = '<span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded text-xs font-semibold">In Use</span>';
                } else if (status === 'lost') {
                    statusBadge = '<span class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs font-semibold">Lost</span>';
                } else if (status === 'inactive') {
                    statusBadge = '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span>';
                }

                return `
                    <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 font-medium">${escapeHtml(card.card_id)}</td>
                        <td class="px-4 py-3">${statusBadge}</td>
                        <td class="px-4 py-3 w-32">
                            <div class="flex gap-2">
                                <button onclick="openEditVisitorCardModal(${card.id})" class="text-primary hover:text-primary/80">
                                    <span class="material-symbols-outlined text-xl">edit</span>
                                </button>
                                <button onclick="openDeleteVisitorCardModal(${card.id}, '${escapeHtml(card.card_id)}')" class="text-red-500 hover:text-red-400">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            updateVisitorCardPagination(pagination);
        }

        function updateVisitorCardPagination(pagination) {
            const from = pagination.total === 0 ? 0 : ((pagination.current_page - 1) * pagination.per_page) + 1;
            const to = Math.min(pagination.current_page * pagination.per_page, pagination.total);

            document.getElementById('visitorCardShowingFrom').textContent = from;
            document.getElementById('visitorCardShowingTo').textContent = to;
            document.getElementById('visitorCardTotalCount').textContent = pagination.total;

            const paginationContainer = document.getElementById('visitorCardPaginationButtons');
            const totalPages = parseInt(pagination.total_pages) || 1;
            const currentPage = parseInt(pagination.current_page) || 1;

            let buttonsHTML = `
                <button onclick="if(${currentPage} > 1) loadVisitorCards(${currentPage - 1})" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    ${currentPage === 1 ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    const activeClass = i === currentPage
                        ? 'bg-primary text-white'
                        : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700';

                    buttonsHTML += `
                        <button onclick="loadVisitorCards(${i})" 
                            class="px-3 py-2 rounded-lg ${activeClass} transition-colors font-medium text-sm min-w-[40px]">
                            ${i}
                        </button>
                    `;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    buttonsHTML += `<span class="px-2 text-gray-400">...</span>`;
                }
            }

            buttonsHTML += `
                <button onclick="if(${currentPage} < ${totalPages}) loadVisitorCards(${currentPage + 1})" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    ${currentPage === totalPages ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
            `;

            paginationContainer.innerHTML = buttonsHTML;
        }

        function searchVisitorCards() {
            currentVisitorCardSearch = document.getElementById('visitorCardSearchInput').value;
            loadVisitorCards(1);
        }

        function sortVisitorCards() {
            currentVisitorCardSort = document.getElementById('visitorCardSortSelect').value;
            loadVisitorCards(1);
        }

        function openCreateVisitorCardModal() {
            document.body.insertAdjacentHTML('beforeend', `
                <div id="visitorCardModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                        <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Create New Visitor Card</h3>
                            <button onclick="closeVisitorCardModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <form id="visitorCardForm" class="p-6">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Card EPC <span class="text-red-500">*</span></label>
                                    <input type="text" id="cardId" name="card_id" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Enter card EPC" required>
                                    <span id="cardIdError" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                                    <select id="cardStatus" name="status" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" required>
                                        <option value="">Select Status</option>
                                        <option value="active">Active</option>
                                        <option value="in_use">In Use</option>
                                        <option value="lost">Lost</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    <span id="cardStatusError" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-slate-700">
                                <button type="button" onclick="closeVisitorCardModal()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                    Create Card
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `);

            document.getElementById('visitorCardForm').addEventListener('submit', saveVisitorCard);
            currentVisitorCardId = null;
        }

        function openEditVisitorCardModal(cardId) {
            fetch(`<?= base_url('config/getVisitorCard') ?>/${cardId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const card = data.data;
                        document.body.insertAdjacentHTML('beforeend', `
                        <div id="visitorCardModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                                <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between">
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Edit Visitor Card</h3>
                                    <button onclick="closeVisitorCardModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>
                                </div>
                                <form id="visitorCardForm" class="p-6">
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Card EPC <span class="text-red-500">*</span></label>
                                            <input type="text" id="cardId" name="card_id" value="${escapeHtml(card.card_id)}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Enter card EPC" required>
                                            <span id="cardIdError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                                            <select id="cardStatus" name="status" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" required>
                                                <option value="">Select Status</option>
                                                <option value="active" ${card.status === 'active' ? 'selected' : ''}>Active</option>
                                                <option value="in_use" ${card.status === 'in_use' ? 'selected' : ''}>In Use</option>
                                                <option value="lost" ${card.status === 'lost' ? 'selected' : ''}>Lost</option>
                                                <option value="inactive" ${card.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                            </select>
                                            <span id="cardStatusError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-slate-700">
                                        <button type="button" onclick="closeVisitorCardModal()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                            Update Card
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    `);

                        document.getElementById('visitorCardForm').addEventListener('submit', saveVisitorCard);
                        currentVisitorCardId = cardId;
                    } else {
                        showNotification('Failed to load visitor card details', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading visitor card:', error);
                    showNotification('An error occurred while loading visitor card details', 'error');
                });
        }

        function openDeleteVisitorCardModal(cardId, cardName) {
            currentVisitorCardId = cardId;

            document.body.insertAdjacentHTML('beforeend', `
                <div id="deleteVisitorCardModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md">
                        <div class="border-b border-gray-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Delete Visitor Card</h3>
                            <button onclick="closeDeleteVisitorCardModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-700 dark:text-slate-300 mb-4">Are you sure you want to delete the visitor card "<strong>${cardName}</strong>"?</p>
                            <p class="text-sm text-gray-500 dark:text-slate-400">This action cannot be undone.</p>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 dark:bg-slate-700/50 flex justify-end gap-3">
                            <button onclick="closeDeleteVisitorCardModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                Cancel
                            </button>
                            <button onclick="deleteVisitorCard()" class="px-5 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm">
                                Delete Card
                            </button>
                        </div>
                    </div>
                </div>
            `);
        }

        function closeVisitorCardModal() {
            const modal = document.getElementById('visitorCardModal');
            if (modal) {
                modal.remove();
            }
        }

        function closeDeleteVisitorCardModal() {
            const modal = document.getElementById('deleteVisitorCardModal');
            if (modal) {
                modal.remove();
            }
        }

        function clearVisitorCardErrors() {
            const cardIdError = document.getElementById('cardIdError');
            const cardStatusError = document.getElementById('cardStatusError');

            if (cardIdError) cardIdError.classList.add('hidden');
            if (cardStatusError) cardStatusError.classList.add('hidden');
        }

        function saveVisitorCard(e) {
            e.preventDefault();
            clearVisitorCardErrors();

            const formData = new FormData();
            formData.append('card_id', document.getElementById('cardId').value.trim());
            formData.append('status', document.getElementById('cardStatus').value);

            const url = currentVisitorCardId
                ? `<?= base_url('config/updateVisitorCard') ?>/${currentVisitorCardId}`
                : `<?= base_url('config/createVisitorCard') ?>`;

            const method = currentVisitorCardId ? 'PUT' : 'POST';

            const submitButton = e.target.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = currentVisitorCardId ? 'Updating...' : 'Creating...';

            fetch(url, {
                method: method,
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeVisitorCardModal();
                        loadVisitorCards(currentVisitorCardPage);
                    } else {
                        if (data.errors) {
                            if (data.errors.card_id) {
                                document.getElementById('cardIdError').textContent = data.errors.card_id;
                                document.getElementById('cardIdError').classList.remove('hidden');
                            }
                            if (data.errors.status) {
                                document.getElementById('cardStatusError').textContent = data.errors.status;
                                document.getElementById('cardStatusError').classList.remove('hidden');
                            }
                        }
                        showNotification(data.message || 'Failed to save visitor card', 'error');
                        submitButton.textContent = originalButtonText;
                        submitButton.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error saving visitor card:', error);
                    showNotification('An error occurred while saving the visitor card', 'error');
                    submitButton.textContent = originalButtonText;
                    submitButton.disabled = false;
                });
        }

        function deleteVisitorCard() {
            const deleteBtn = event.target;
            deleteBtn.disabled = true;
            deleteBtn.textContent = 'Deleting...';

            fetch(`<?= base_url('config/deleteVisitorCard') ?>/${currentVisitorCardId}`, {
                method: 'DELETE'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeDeleteVisitorCardModal();
                        loadVisitorCards(currentVisitorCardPage);
                    } else {
                        showNotification(data.message || 'Failed to delete visitor card', 'error');
                        deleteBtn.textContent = 'Delete Card';
                        deleteBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error deleting visitor card:', error);
                    showNotification('An error occurred while deleting the visitor card', 'error');
                    deleteBtn.textContent = 'Delete Card';
                    deleteBtn.disabled = false;
                });
        }

        // Visitor card search Enter key
        document.addEventListener('keypress', function (e) {
            if (e.target && e.target.id === 'visitorCardSearchInput' && e.key === 'Enter') {
                e.preventDefault();
                searchVisitorCards();
            }
        });

        // ========================
        // Video Management Functions
        // ========================

        let currentVideoPage = 1;
        let currentVideoSearch = '';
        let currentVideoSort = '';
        let currentVideoId = null;

        function loadVideos(page = 1) {
            currentVideoPage = page;

            fetch(`<?= base_url('config/getVideos') ?>?page=${page}&search=${encodeURIComponent(currentVideoSearch)}&sort=${currentVideoSort}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayVideos(data.data, data.pagination);
                    } else {
                        document.getElementById('videoTableBody').innerHTML = `
                            <tr>
                                <td colspan="3" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="material-symbols-outlined text-red-500 text-5xl mb-2">error</span>
                                        <p class="text-gray-500 dark:text-slate-400">${data.message || 'Error loading videos'}</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading videos:', error);
                    document.getElementById('videoTableBody').innerHTML = `
                        <tr>
                            <td colspan="3" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-symbols-outlined text-red-500 text-5xl mb-2">error</span>
                                    <p class="text-gray-500 dark:text-slate-400">An error occurred while loading videos</p>
                                </div>
                            </td>
                        </tr>
                    `;
                });
        }

        function displayVideos(videos, pagination) {
            const tbody = document.getElementById('videoTableBody');

            if (videos.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <span class="material-symbols-outlined text-gray-400 text-5xl mb-2">videocam</span>
                                <p class="text-gray-500 dark:text-slate-400">No videos found</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = videos.map(video => {
                const statusBadge = video.status === 'active'
                    ? '<span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span>'
                    : '<span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span>';

                return `
                    <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 font-medium cursor-pointer hover:text-primary" onclick="openVideoPreviewModal(${video.id}, '${escapeHtml(video.name)}', '${video.file_path}')">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-xl text-gray-400">play_circle</span>
                                ${escapeHtml(video.name)}
                            </div>
                        </td>
                        <td class="px-4 py-3">${statusBadge}</td>
                        <td class="px-4 py-3 w-32">
                            <div class="flex gap-2">
                                <button onclick="openVideoPreviewModal(${video.id}, '${escapeHtml(video.name)}', '${video.file_path}')" class="text-blue-500 hover:text-blue-400" title="Preview Video">
                                    <span class="material-symbols-outlined text-xl">visibility</span>
                                </button>
                                <button onclick="openEditVideoModal(${video.id})" class="text-primary hover:text-primary/80">
                                    <span class="material-symbols-outlined text-xl">edit</span>
                                </button>
                                <button onclick="openDeleteVideoModal(${video.id}, '${escapeHtml(video.name)}')" class="text-red-500 hover:text-red-400">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            updateVideoPagination(pagination);
        }

        function openVideoPreviewModal(videoId, videoName, filePath) {
            document.body.insertAdjacentHTML('beforeend', `
                <div id="videoPreviewModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                        <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between rounded-t-lg">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">${videoName}</h3>
                            <button onclick="closeVideoPreviewModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-6">
                            <div class="bg-black rounded-lg overflow-hidden">
                                <video id="previewVideoPlayer" class="w-full" controls controlsList="nodownload">
                                    <source src="<?= base_url() ?>${filePath}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }

        function closeVideoPreviewModal() {
            const modal = document.getElementById('videoPreviewModal');
            if (modal) {
                const video = document.getElementById('previewVideoPlayer');
                if (video) {
                    video.pause();
                }
                modal.remove();
            }
        }

        function updateVideoPagination(pagination) {
            const from = pagination.total === 0 ? 0 : ((pagination.current_page - 1) * pagination.per_page) + 1;
            const to = Math.min(pagination.current_page * pagination.per_page, pagination.total);

            document.getElementById('videoShowingFrom').textContent = from;
            document.getElementById('videoShowingTo').textContent = to;
            document.getElementById('videoTotalCount').textContent = pagination.total;

            const paginationContainer = document.getElementById('videoPaginationButtons');
            const totalPages = parseInt(pagination.total_pages) || 1;
            const currentPage = parseInt(pagination.current_page) || 1;

            let buttonsHTML = `
                <button onclick="if(${currentPage} > 1) loadVideos(${currentPage - 1})" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    ${currentPage === 1 ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    const activeClass = i === currentPage
                        ? 'bg-primary text-white'
                        : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700';

                    buttonsHTML += `
                        <button onclick="loadVideos(${i})" 
                            class="px-3 py-2 rounded-lg ${activeClass} transition-colors font-medium text-sm min-w-[40px]">
                            ${i}
                        </button>
                    `;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    buttonsHTML += `<span class="px-2 text-gray-400">...</span>`;
                }
            }

            buttonsHTML += `
                <button onclick="if(${currentPage} < ${totalPages}) loadVisitorCards(${currentPage + 1})" 
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    ${currentPage === totalPages ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
            `;

            paginationContainer.innerHTML = buttonsHTML;
        }

        function searchVideos() {
            currentVideoSearch = document.getElementById('videoSearchInput').value;
            loadVideos(1);
        }

        function sortVideos() {
            currentVideoSort = document.getElementById('videoSortSelect').value;
            loadVideos(1);
        }

        function openCreateVideoModal() {
            document.body.insertAdjacentHTML('beforeend', `
                <div id="videoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                        <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Upload Video</h3>
                            <button onclick="closeVideoModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <form id="videoForm" class="p-6" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Video Name <span class="text-red-500">*</span></label>
                                    <input type="text" id="videoName" name="name" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Enter video name" required>
                                    <span id="videoNameError" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Video File <span class="text-red-500">*</span></label>
                                    <div id="videoDropZone" 
                                         class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg p-6 text-center cursor-pointer hover:border-primary hover:bg-primary/5 transition-all duration-200">
                                        <span class="material-symbols-outlined text-5xl text-slate-400 dark:text-slate-500 mb-2 block">cloud_upload</span>
                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                            Drop your video here or click to browse
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            MP4, AVI, MOV, WMV, MKV up to 50MB
                                        </p>
                                        <input type="file" id="videoFile" name="video_file" accept="video/mp4,video/avi,video/mov,video/wmv,video/x-matroska" class="hidden" required>
                                    </div>
                                    <span id="videoFileError" class="text-red-500 text-xs mt-1 hidden"></span>
                                    
                                    <!-- Selected File Info -->
                                    <div id="videoFileInfo" class="hidden mt-3">
                                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <span class="material-symbols-outlined text-primary">videocam</span>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-slate-900 dark:text-white truncate" id="videoFileName">video.mp4</p>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400" id="videoFileSize">0 MB</p>
                                                </div>
                                            </div>
                                            <button type="button" id="videoCancelBtn" 
                                                    class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                                <span class="material-symbols-outlined text-xl">close</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                                    <select id="videoStatus" name="status" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" required>
                                        <option value="">Select Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    <span id="videoStatusError" class="text-red-500 text-xs mt-1 hidden"></span>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-slate-700">
                                <button type="button" onclick="closeVideoModal()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                    Upload Video
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `);

            // Setup drag and drop functionality
            const dropZone = document.getElementById('videoDropZone');
            const fileInput = document.getElementById('videoFile');
            const fileInfo = document.getElementById('videoFileInfo');
            const fileName = document.getElementById('videoFileName');
            const fileSize = document.getElementById('videoFileSize');
            const cancelBtn = document.getElementById('videoCancelBtn');

            // Click to browse
            dropZone.addEventListener('click', () => fileInput.click());

            // Drag and drop events
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-primary', 'bg-primary/10');
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-primary', 'bg-primary/10');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-primary', 'bg-primary/10');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    displayVideoFileInfo(files[0]);
                }
            });

            // File input change
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    displayVideoFileInfo(e.target.files[0]);
                }
            });

            // Cancel file selection
            cancelBtn.addEventListener('click', () => {
                fileInput.value = '';
                fileInfo.classList.add('hidden');
                dropZone.classList.remove('hidden');
            });

            function displayVideoFileInfo(file) {
                fileName.textContent = file.name;
                fileSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                fileInfo.classList.remove('hidden');
                dropZone.classList.add('hidden');
            }

            document.getElementById('videoForm').addEventListener('submit', saveVideo);
            currentVideoId = null;
        }

        function openEditVideoModal(videoId) {
            fetch(`<?= base_url('config/getVideo') ?>/${videoId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const video = data.data;
                        document.body.insertAdjacentHTML('beforeend', `
                        <div id="videoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                                <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between">
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Edit Video</h3>
                                    <button onclick="closeVideoModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>
                                </div>
                                <form id="videoForm" class="p-6" enctype="multipart/form-data">
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Video Name <span class="text-red-500">*</span></label>
                                            <input type="text" id="videoName" name="name" value="${escapeHtml(video.name)}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Enter video name" required>
                                            <span id="videoNameError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Video File <span class="text-gray-500">(Optional - leave empty to keep current)</span></label>
                                            <div id="videoDropZone" 
                                                 class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg p-6 text-center cursor-pointer hover:border-primary hover:bg-primary/5 transition-all duration-200">
                                                <span class="material-symbols-outlined text-5xl text-slate-400 dark:text-slate-500 mb-2 block">cloud_upload</span>
                                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                                    Drop new video here or click to browse
                                                </p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                                    MP4, AVI, MOV, WMV, MKV up to 50MB
                                                </p>
                                                <input type="file" id="videoFile" name="video_file" accept="video/mp4,video/avi,video/mov,video/wmv,video/x-matroska" class="hidden">
                                            </div>
                                            <span id="videoFileError" class="text-red-500 text-xs mt-1 hidden"></span>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Current: ${escapeHtml(video.file_path.split('/').pop())}</p>
                                            
                                            <!-- Selected File Info -->
                                            <div id="videoFileInfo" class="hidden mt-3">
                                                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                                        <span class="material-symbols-outlined text-primary">videocam</span>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-slate-900 dark:text-white truncate" id="videoFileName">video.mp4</p>
                                                            <p class="text-xs text-slate-500 dark:text-slate-400" id="videoFileSize">0 MB</p>
                                                        </div>
                                                    </div>
                                                    <button type="button" id="videoCancelBtn" 
                                                            class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                                        <span class="material-symbols-outlined text-xl">close</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                                            <select id="videoStatus" name="status" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" required>
                                                <option value="">Select Status</option>
                                                <option value="active" ${video.status === 'active' ? 'selected' : ''}>Active</option>
                                                <option value="inactive" ${video.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                            </select>
                                            <span id="videoStatusError" class="text-red-500 text-xs mt-1 hidden"></span>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-slate-700">
                                        <button type="button" onclick="closeVideoModal()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                            Update Video
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    `);

                        // Setup drag and drop functionality
                        const dropZone = document.getElementById('videoDropZone');
                        const fileInput = document.getElementById('videoFile');
                        const fileInfo = document.getElementById('videoFileInfo');
                        const fileName = document.getElementById('videoFileName');
                        const fileSize = document.getElementById('videoFileSize');
                        const cancelBtn = document.getElementById('videoCancelBtn');

                        // Click to browse
                        dropZone.addEventListener('click', () => fileInput.click());

                        // Drag and drop events
                        dropZone.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            dropZone.classList.add('border-primary', 'bg-primary/10');
                        });

                        dropZone.addEventListener('dragleave', () => {
                            dropZone.classList.remove('border-primary', 'bg-primary/10');
                        });

                        dropZone.addEventListener('drop', (e) => {
                            e.preventDefault();
                            dropZone.classList.remove('border-primary', 'bg-primary/10');

                            const files = e.dataTransfer.files;
                            if (files.length > 0) {
                                fileInput.files = files;
                                displayVideoFileInfo(files[0]);
                            }
                        });

                        // File input change
                        fileInput.addEventListener('change', (e) => {
                            if (e.target.files.length > 0) {
                                displayVideoFileInfo(e.target.files[0]);
                            }
                        });

                        // Cancel file selection
                        cancelBtn.addEventListener('click', () => {
                            fileInput.value = '';
                            fileInfo.classList.add('hidden');
                            dropZone.classList.remove('hidden');
                        });

                        function displayVideoFileInfo(file) {
                            fileName.textContent = file.name;
                            fileSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                            fileInfo.classList.remove('hidden');
                            dropZone.classList.add('hidden');
                        }

                        document.getElementById('videoForm').addEventListener('submit', saveVideo);
                        currentVideoId = videoId;
                    } else {
                        showNotification('Failed to load video details', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading video:', error);
                    showNotification('An error occurred while loading video details', 'error');
                });
        }

        function openDeleteVideoModal(videoId, videoName) {
            currentVideoId = videoId;

            document.body.insertAdjacentHTML('beforeend', `
                <div id="deleteVideoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md">
                        <div class="border-b border-gray-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Delete Video</h3>
                            <button onclick="closeDeleteVideoModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-700 dark:text-slate-300 mb-4">Are you sure you want to delete the video "<strong>${videoName}</strong>"?</p>
                            <p class="text-sm text-gray-500 dark:text-slate-400">This action cannot be undone and will permanently delete the video file.</p>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 dark:bg-slate-700/50 flex justify-end gap-3">
                            <button onclick="closeDeleteVideoModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                Cancel
                            </button>
                            <button onclick="deleteVideo()" class="px-5 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm">
                                Delete Video
                            </button>
                        </div>
                    </div>
                </div>
            `);
        }

        function closeVideoModal() {
            const modal = document.getElementById('videoModal');
            if (modal) {
                modal.remove();
            }
        }

        function closeDeleteVideoModal() {
            const modal = document.getElementById('deleteVideoModal');
            if (modal) {
                modal.remove();
            }
        }

        function clearVideoErrors() {
            document.getElementById('videoNameError').classList.add('hidden');
            const fileError = document.getElementById('videoFileError');
            if (fileError) fileError.classList.add('hidden');
            document.getElementById('videoStatusError').classList.add('hidden');
        }

        function saveVideo(e) {
            e.preventDefault();
            clearVideoErrors();

            const formData = new FormData();
            formData.append('name', document.getElementById('videoName').value.trim());
            formData.append('status', document.getElementById('videoStatus').value);

            const videoFileInput = document.getElementById('videoFile');
            if (videoFileInput.files.length > 0) {
                formData.append('video_file', videoFileInput.files[0]);
            }

            const url = currentVideoId
                ? `<?= base_url('config/updateVideo') ?>/${currentVideoId}`
                : `<?= base_url('config/createVideo') ?>`;

            const submitButton = e.target.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = currentVideoId ? 'Updating...' : 'Uploading...';

            fetch(url, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeVideoModal();
                        loadVideos(currentVideoPage);
                    } else {
                        if (data.errors) {
                            if (data.errors.name) {
                                document.getElementById('videoNameError').textContent = data.errors.name;
                                document.getElementById('videoNameError').classList.remove('hidden');
                            }
                            if (data.errors.video_file) {
                                document.getElementById('videoFileError').textContent = data.errors.video_file;
                                document.getElementById('videoFileError').classList.remove('hidden');
                            }
                            if (data.errors.status) {
                                document.getElementById('videoStatusError').textContent = data.errors.status;
                                document.getElementById('videoStatusError').classList.remove('hidden');
                            }
                        }
                        showNotification(data.message || 'Failed to save video', 'error');
                        submitButton.textContent = originalButtonText;
                        submitButton.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error saving video:', error);
                    showNotification('An error occurred while saving the video', 'error');
                    submitButton.textContent = originalButtonText;
                    submitButton.disabled = false;
                });
        }

        function deleteVideo() {
            const deleteBtn = event.target;
            deleteBtn.disabled = true;
            deleteBtn.textContent = 'Deleting...';

            fetch(`<?= base_url('config/deleteVideo') ?>/${currentVideoId}`, {
                method: 'DELETE'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeDeleteVideoModal();
                        loadVideos(currentVideoPage);
                    } else {
                        showNotification(data.message || 'Failed to delete video', 'error');
                        deleteBtn.textContent = 'Delete Video';
                        deleteBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error deleting video:', error);
                    showNotification('An error occurred while deleting the video', 'error');
                    deleteBtn.textContent = 'Delete Video';
                    deleteBtn.disabled = false;
                });
        }

        // Video search Enter key
        document.addEventListener('keypress', function (e) {
            if (e.target && e.target.id === 'videoSearchInput' && e.key === 'Enter') {
                e.preventDefault();
                searchVideos();
            }
        });

        // ====================================================================================
        // Visit Reason Management
        // ====================================================================================
        let currentVisitReasonPage = 1;
        let visitReasonPerPage = 10;
        let currentVisitReasonSearch = '';
        let currentVisitReasonSort = 'created_at_desc';

        function loadVisitReasons(page = 1) {
            currentVisitReasonPage = page;

            // Parse sort - handle 'created_at' specially
            let sortBy = 'created_at';
            let sortOrder = 'DESC';

            if (currentVisitReasonSort === 'created_at_desc') {
                sortBy = 'created_at';
                sortOrder = 'DESC';
            } else if (currentVisitReasonSort === 'created_at_asc') {
                sortBy = 'created_at';
                sortOrder = 'ASC';
            } else if (currentVisitReasonSort === 'reason_asc') {
                sortBy = 'reason';
                sortOrder = 'ASC';
            } else if (currentVisitReasonSort === 'reason_desc') {
                sortBy = 'reason';
                sortOrder = 'DESC';
            }

            fetch(`<?= base_url('config/getVisitReasons') ?>?page=${page}&per_page=${visitReasonPerPage}&search=${encodeURIComponent(currentVisitReasonSearch)}&sort_by=${sortBy}&sort_order=${sortOrder}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayVisitReasons(data.data, data.pagination);
                    } else {
                        console.error('Error loading visit reasons:', data.message);
                        showNoData();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNoData();
                });
        }

        function showNoData() {
            const tbody = document.getElementById('visitReasonTableBody');
            if (!tbody) return;

            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-4xl">error</span>
                            <p>Failed to load visit reasons</p>
                        </div>
                    </td>
                </tr>
            `;
        }

        function displayVisitReasons(reasons, pagination) {
            const tbody = document.getElementById('visitReasonTableBody');
            if (!tbody) return;

            if (reasons.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-4xl">search_off</span>
                                <p>No visit reasons found</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = reasons.map((reason, index) => `
                <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                    <td class="px-4 py-3 font-medium">${pagination.from + index}</td>
                    <td class="px-4 py-3">${escapeHtml(reason.reason)}</td>
                    <td class="px-4 py-3 w-32">
                        <div class="flex gap-2">
                            <button onclick="openEditVisitReasonModal(${reason.id}, '${escapeHtml(reason.reason)}')" class="text-primary hover:text-primary/80">
                                <span class="material-symbols-outlined text-xl">edit</span>
                            </button>
                            <button onclick="deleteVisitReason(${reason.id})" class="text-red-500 hover:text-red-400">
                                <span class="material-symbols-outlined text-xl">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

            // Update pagination info
            document.getElementById('visitReasonFrom').textContent = pagination.from;
            document.getElementById('visitReasonTo').textContent = pagination.to;
            document.getElementById('visitReasonTotal').textContent = pagination.total;

            // Update pagination buttons
            updateVisitReasonPaginationButtons(pagination);
        }

        function updateVisitReasonPaginationButtons(pagination) {
            const container = document.getElementById('visitReasonPaginationButtons');
            if (!container) return;

            let buttons = '';

            // Previous button
            buttons += `
                <button onclick="loadVisitReasons(${pagination.current_page - 1})" 
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors ${pagination.current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''}" 
                        ${pagination.current_page === 1 ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            `;

            // Always show first page
            buttons += `
                <button onclick="loadVisitReasons(1)" 
                        class="px-3 py-2 rounded-lg ${1 === pagination.current_page ? 'bg-primary text-white' : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'} font-medium text-sm min-w-[40px] transition-colors">
                    1
                </button>
            `;

            // Show ellipsis if needed
            if (pagination.current_page > 3) {
                buttons += `<span class="px-2 text-gray-400">...</span>`;
            }

            // Show pages around current page
            for (let i = Math.max(2, pagination.current_page - 1); i <= Math.min(pagination.last_page - 1, pagination.current_page + 1); i++) {
                buttons += `
                    <button onclick="loadVisitReasons(${i})" 
                            class="px-3 py-2 rounded-lg ${i === pagination.current_page ? 'bg-primary text-white' : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'} font-medium text-sm min-w-[40px] transition-colors">
                        ${i}
                    </button>
                `;
            }

            // Show ellipsis if needed
            if (pagination.current_page < pagination.last_page - 2) {
                buttons += `<span class="px-2 text-gray-400">...</span>`;
            }

            // Always show last page if there's more than 1 page
            if (pagination.last_page > 1) {
                buttons += `
                    <button onclick="loadVisitReasons(${pagination.last_page})" 
                            class="px-3 py-2 rounded-lg ${pagination.last_page === pagination.current_page ? 'bg-primary text-white' : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'} font-medium text-sm min-w-[40px] transition-colors">
                        ${pagination.last_page}
                    </button>
                `;
            }

            // Next button
            buttons += `
                <button onclick="loadVisitReasons(${pagination.current_page + 1})" 
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors ${pagination.current_page === pagination.last_page ? 'opacity-50 cursor-not-allowed' : ''}" 
                        ${pagination.current_page === pagination.last_page ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
            `;

            container.innerHTML = buttons;
        }

        function searchVisitReasons() {
            currentVisitReasonSearch = document.getElementById('visitReasonSearchInput').value;
            loadVisitReasons(1);
        }

        function sortVisitReasons() {
            currentVisitReasonSort = document.getElementById('visitReasonSortSelect').value;
            loadVisitReasons(1);
        }

        function openCreateVisitReasonModal() {
            document.body.insertAdjacentHTML('beforeend', `
                <div id="visitReasonModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                        <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between rounded-t-lg">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Create Visit Reason</h3>
                            <button onclick="closeVisitReasonModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <form id="visitReasonForm" class="p-6">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Visit Reason <span class="text-red-500">*</span></label>
                                <input type="text" id="visitReasonName" name="reason" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Enter visit reason" required>
                                <span id="visitReasonError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                                <button type="button" onclick="closeVisitReasonModal()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                    Create Reason
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `);

            document.getElementById('visitReasonForm').addEventListener('submit', function (e) {
                e.preventDefault();
                createVisitReason();
            });
        }

        function createVisitReason() {
            const formData = new FormData();
            formData.append('reason', document.getElementById('visitReasonName').value);

            fetch('<?= base_url('config/createVisitReason') ?>', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeVisitReasonModal();
                        loadVisitReasons(currentVisitReasonPage);
                        showToast('Visit reason created successfully', 'success');
                    } else {
                        if (data.errors) {
                            document.getElementById('visitReasonError').textContent = data.errors.reason || data.message;
                            document.getElementById('visitReasonError').classList.remove('hidden');
                        } else {
                            showToast(data.message, 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred. Please try again.', 'error');
                });
        }

        function openEditVisitReasonModal(id, reason) {
            document.body.insertAdjacentHTML('beforeend', `
                <div id="visitReasonModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                        <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between rounded-t-lg">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Edit Visit Reason</h3>
                            <button onclick="closeVisitReasonModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <form id="visitReasonForm" class="p-6">
                            <input type="hidden" id="visitReasonId" value="${id}">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Visit Reason <span class="text-red-500">*</span></label>
                                <input type="text" id="visitReasonName" name="reason" value="${reason}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Enter visit reason" required>
                                <span id="visitReasonError" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                                <button type="button" onclick="closeVisitReasonModal()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">
                                    Update Reason
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `);

            document.getElementById('visitReasonForm').addEventListener('submit', function (e) {
                e.preventDefault();
                updateVisitReason();
            });
        }

        function updateVisitReason() {
            const id = document.getElementById('visitReasonId').value;
            const formData = new FormData();
            formData.append('reason', document.getElementById('visitReasonName').value);

            fetch(`<?= base_url('config/updateVisitReason/') ?>${id}`, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeVisitReasonModal();
                        loadVisitReasons(currentVisitReasonPage);
                        showToast('Visit reason updated successfully', 'success');
                    } else {
                        if (data.errors) {
                            document.getElementById('visitReasonError').textContent = data.errors.reason || data.message;
                            document.getElementById('visitReasonError').classList.remove('hidden');
                        } else {
                            showToast(data.message, 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred. Please try again.', 'error');
                });
        }

        function deleteVisitReason(id) {
            if (!confirm('Are you sure you want to delete this visit reason?')) {
                return;
            }

            fetch(`<?= base_url('config/deleteVisitReason/') ?>${id}`, {
                method: 'DELETE'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadVisitReasons(currentVisitReasonPage);
                        showToast('Visit reason deleted successfully', 'success');
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred. Please try again.', 'error');
                });
        }

        function closeVisitReasonModal() {
            const modal = document.getElementById('visitReasonModal');
            if (modal) {
                modal.remove();
            }
        }

        // Visit Reason search Enter key
        document.addEventListener('keypress', function (e) {
            if (e.target && e.target.id === 'visitReasonSearchInput' && e.key === 'Enter') {
                e.preventDefault();
                searchVisitReasons();
            }
        });

        // ============== DEVICE ASSIGNMENTS & IP RANGE ==============

        let deleteDeviceId = null;
        let globalIpRange = { from: '', to: '' };

        function ip2long(ip) {
            let parts = ip.split('.');
            if (parts.length !== 4) return 0;
            return (parts[0] << 24) | (parts[1] << 16) | (parts[2] << 8) | parts[3];
        }

        function isIpInRange(ip, from, to) {
            if (!from || !to) return true;
            let ipL = ip2long(ip);
            let fromL = ip2long(from);
            let toL = ip2long(to);
            return ipL >= fromL && ipL <= toL;
        }

        function fetchIpRangeSettings() {
            fetch('<?= base_url('config/getIpRangeSettings') ?>')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        globalIpRange.from = data.data.ip_range_from || '';
                        globalIpRange.to = data.data.ip_range_to || '';

                        const elFrom = document.getElementById('displayIpRangeFrom');
                        const elTo = document.getElementById('displayIpRangeTo');
                        if (elFrom) elFrom.textContent = globalIpRange.from || '-';
                        if (elTo) elTo.textContent = globalIpRange.to || '-';
                    }
                });
        }

        // Initialize fetch IP range
        fetchIpRangeSettings();

        // Tie loadDeviceAssignments onto the toggle wrapper
        const originalToggle = toggleSection;
        toggleSection = function (section) {
            originalToggle(section);
            if (section === 'device-assignment') {
                fetchIpRangeSettings();
                loadDeviceAssignments();
                fetchLocationsForDevice();
            }
        };

        function fetchLocationsForDevice() {
            fetch('<?= base_url('config/getAllLocations') ?>')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const locSelect = document.getElementById('daLocation');
                        locSelect.innerHTML = '<option value="">-- Select Location --</option>' +
                            data.data.map((loc, idx) => `<option value="${loc.id}">${idx + 1}. ${escapeHtml(loc.location_access)}</option>`).join('');

                        const devSelect = document.getElementById('daDeviceSelect');
                        devSelect.innerHTML = '<option value="">-- Select Device --</option>' +
                            data.data.filter(loc => loc.adam_ip).map(loc => {
                                // Extract integer portion for fake generic device serial formatting similar to the user screenshot for visual parity
                                const fakeId = '008825113' + loc.id.toString().padStart(3, '0');
                                const valStr = fakeId + '|' + loc.adam_ip;
                                return `<option value="${valStr}">${fakeId} - ${escapeHtml(loc.adam_ip)}</option>`;
                            }).join('');
                    }
                })
                .catch(err => console.log(err));
        }

        function loadDeviceAssignments(page = 1, search = '') {
            fetch(`<?= base_url('config/getDeviceAssignments') ?>?page=${page}&per_page=10&search=${encodeURIComponent(search)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayDevices(data.data);
                        updateDevicePagination(data.pagination);
                    }
                });
        }

        function updateDevicePagination(pagination) {
            document.getElementById('deviceFrom').textContent = pagination.from || 0;
            document.getElementById('deviceTo').textContent = pagination.to || 0;
            document.getElementById('deviceTotal').textContent = pagination.total || 0;

            const container = document.getElementById('devicePaginationButtons');
            if (!container) return;

            let buttons = '';

            // Previous button
            buttons += `
                <button onclick="loadDeviceAssignments(${pagination.current_page - 1}, document.getElementById('deviceSearch').value)" 
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors ${pagination.current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''}" 
                        ${pagination.current_page === 1 ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            `;

            // Last page alias because API uses total_pages but JS expects last_page logic
            const lastPage = pagination.total_pages;

            if (lastPage > 0) {
                // Always show first page
                buttons += `
                    <button onclick="loadDeviceAssignments(1, document.getElementById('deviceSearch').value)" 
                            class="px-3 py-2 rounded-lg ${1 === pagination.current_page ? 'bg-primary text-white' : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'} font-medium text-sm min-w-[40px] transition-colors">
                        1
                    </button>
                `;

                // Show ellipsis if needed
                if (pagination.current_page > 3) {
                    buttons += `<span class="px-2 text-gray-400">...</span>`;
                }

                // Show pages around current page
                for (let i = Math.max(2, pagination.current_page - 1); i <= Math.min(lastPage - 1, pagination.current_page + 1); i++) {
                    buttons += `
                        <button onclick="loadDeviceAssignments(${i}, document.getElementById('deviceSearch').value)" 
                                class="px-3 py-2 rounded-lg ${i === pagination.current_page ? 'bg-primary text-white' : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'} font-medium text-sm min-w-[40px] transition-colors">
                            ${i}
                        </button>
                    `;
                }

                // Show ellipsis if needed
                if (pagination.current_page < lastPage - 2) {
                    buttons += `<span class="px-2 text-gray-400">...</span>`;
                }

                // Always show last page if there's more than 1 page
                if (lastPage > 1) {
                    buttons += `
                        <button onclick="loadDeviceAssignments(${lastPage}, document.getElementById('deviceSearch').value)" 
                                class="px-3 py-2 rounded-lg ${lastPage === pagination.current_page ? 'bg-primary text-white' : 'border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'} font-medium text-sm min-w-[40px] transition-colors">
                            ${lastPage}
                        </button>
                    `;
                }
            }

            // Next button
            buttons += `
                <button onclick="loadDeviceAssignments(${pagination.current_page + 1}, document.getElementById('deviceSearch').value)" 
                        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors ${pagination.current_page === lastPage || lastPage === 0 ? 'opacity-50 cursor-not-allowed' : ''}" 
                        ${pagination.current_page === lastPage || lastPage === 0 ? 'disabled' : ''}>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
            `;

            container.innerHTML = buttons;
        }

        function checkRealtimeDeviceStatus(id) {
            fetch(`<?= base_url('config/checkDeviceStatus') ?>/${id}`, { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    const statusEl = document.getElementById(`status-dev-${id}`);
                    const heartbeatEl = document.getElementById(`heartbeat-dev-${id}`);
                    if (!statusEl) return; // if element no longer exists (page changed)

                    if (data.success) {
                        const isOnline = data.status === 'Online';
                        const statusClass = isOnline ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-500';
                        statusEl.className = `px-2 py-1 rounded text-xs font-semibold ${statusClass}`;
                        statusEl.innerHTML = escapeHtml(data.status);
                        
                        if (heartbeatEl) {
                            heartbeatEl.textContent = data.last_heartbeat ? new Date(data.last_heartbeat).toLocaleString() : '-';
                        }
                    } else {
                        statusEl.className = `px-2 py-1 rounded text-xs font-semibold bg-gray-500/20 text-gray-500`;
                        statusEl.innerHTML = 'Error';
                    }
                })
                .catch(() => {
                    const statusEl = document.getElementById(`status-dev-${id}`);
                    if (statusEl) {
                        statusEl.className = `px-2 py-1 rounded text-xs font-semibold bg-gray-500/20 text-gray-500`;
                        statusEl.innerHTML = 'Timeout';
                    }
                });
        }

        function displayDevices(devices) {
            const tbody = document.getElementById('device-assignment-table-body');
            const banner = document.getElementById('ipRangeWarningBanner');

            if (devices.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No device assignments found</td></tr>';
                if (banner) { banner.classList.add('hidden'); banner.classList.remove('flex'); }
                return;
            }

            let hasOutOfRange = false;
            let rowsHtml = devices.map(device => {
                const isOutOfRange = !isIpInRange(device.ip_address, globalIpRange.from, globalIpRange.to);
                if (isOutOfRange) hasOutOfRange = true;

                const statusClass = device.status === 'Online' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-500';
                const regClass = device.registration_status === 'Registered' ? 'bg-blue-500/20 text-blue-500' : 'bg-yellow-500/20 text-yellow-600';
                const warningIcon = isOutOfRange ? '<span class="material-symbols-outlined text-red-500 text-sm ml-1" title="Out of IP Range">warning</span>' : '';
                const typeClass = device.type === 'Check-In' ? 'bg-green-500/20 text-green-500 border border-green-500/30' : 'bg-yellow-500/20 text-yellow-600 border border-yellow-500/30';

                // Show a loading spinner initially for status to indicate it is being checked
                const loadingStatus = `<span id="status-dev-${device.id}" class="px-2 py-1 rounded text-xs font-semibold bg-gray-500/20 text-gray-500 flex items-center w-max"><span class="material-symbols-outlined animate-spin mr-1" style="font-size: 14px;">sync</span>Checking...</span>`;
                const heartbeatText = device.last_heartbeat ? new Date(device.last_heartbeat).toLocaleString() : '-';

                return `
                    <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 font-medium">${escapeHtml(device.device_id)}</td>
                        <td class="px-4 py-3 flex items-center">${escapeHtml(device.ip_address)} ${warningIcon}</td>
                        <td class="px-4 py-3">${loadingStatus}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded text-xs font-semibold ${regClass}">${device.registration_status}</span></td>
                        <td class="px-4 py-3">${escapeHtml(device.location_name || '-')}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded text-xs font-semibold ${typeClass}">${escapeHtml(device.type)}</span></td>
                        <td class="px-4 py-3 text-slate-500" id="heartbeat-dev-${device.id}">${heartbeatText}</td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="openDeviceAssignmentModal(${device.id})" class="text-primary hover:text-primary/80 mr-2"><span class="material-symbols-outlined text-base">edit</span></button>
                            <button onclick="openDeleteDeviceModal(${device.id}, '${escapeHtml(device.device_id)}')" class="text-red-500 hover:text-red-400"><span class="material-symbols-outlined text-base">delete</span></button>
                        </td>
                    </tr>
                `;
            }).join('');

            tbody.innerHTML = rowsHtml;

            // Trigger background checks for each device async
            devices.forEach(device => {
                checkRealtimeDeviceStatus(device.id);
            });

            if (banner) {
                if (hasOutOfRange) {
                    banner.classList.remove('hidden');
                    banner.classList.add('flex');
                } else {
                    banner.classList.add('hidden');
                    banner.classList.remove('flex');
                }
            }
        }

        function openDeviceAssignmentModal(id = null) {
            document.getElementById('deviceAssignmentForm').reset();
            document.getElementById('deviceAssignmentId').value = '';
            document.getElementById('deviceAssignmentModalTitle').innerText = 'Assign New Device';

            if (id) {
                document.getElementById('deviceAssignmentModalTitle').innerText = 'Edit Device';
                fetch(`<?= base_url('config/getDeviceAssignment') ?>/${id}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const d = data.data;
                            document.getElementById('deviceAssignmentId').value = d.id;

                            // Try to select existing device combination
                            const devSelect = document.getElementById('daDeviceSelect');
                            const valToMatch = d.device_id + '|' + d.ip_address;
                            let matchFound = false;
                            for (let i = 0; i < devSelect.options.length; i++) {
                                if (devSelect.options[i].value === valToMatch) {
                                    devSelect.selectedIndex = i;
                                    matchFound = true;
                                    break;
                                }
                            }
                            // If edit mapping not natively found, inject it visually to preserve edit UX
                            if (!matchFound) {
                                devSelect.innerHTML += `<option value="${valToMatch}" selected>${escapeHtml(d.device_id)} - ${escapeHtml(d.ip_address)}</option>`;
                            }

                            document.getElementById('daLocation').value = d.location_id;
                            document.getElementById('daType').value = d.type;
                        }
                    });
            }
            document.getElementById('deviceAssignmentModal').classList.remove('hidden');
            document.getElementById('deviceAssignmentModal').classList.add('flex');
        }

        function closeDeviceAssignmentModal() {
            document.getElementById('deviceAssignmentModal').classList.add('hidden');
            document.getElementById('deviceAssignmentModal').classList.remove('flex');
        }

        function submitDeviceAssignmentForm(e) {
            e.preventDefault();
            const id = document.getElementById('deviceAssignmentId').value;
            const url = id ? `<?= base_url('config/updateDeviceAssignment') ?>/${id}` : '<?= base_url('config/createDeviceAssignment') ?>';

            const deviceSelectStr = document.getElementById('daDeviceSelect').value;
            let deviceId = '';
            let ipAddress = '';
            if (deviceSelectStr) {
                const parts = deviceSelectStr.split('|');
                deviceId = parts[0];
                ipAddress = parts[1] || '';
            }

            const payload = {
                device_id: deviceId,
                ip_address: ipAddress,
                status: 'Online',
                registration_status: 'Registered',
                location_id: document.getElementById('daLocation').value,
                type: document.getElementById('daType').value,
            };

            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    closeDeviceAssignmentModal();
                    loadDeviceAssignments();
                } else {
                    showToast(data.message || 'Error saving device', 'error');
                }
            });
        }

        function openDeleteDeviceModal(id, name) {
            deleteDeviceId = id;
            document.getElementById('deleteDeviceName').innerText = name;
            document.getElementById('deleteDeviceModal').classList.remove('hidden');
            document.getElementById('deleteDeviceModal').classList.add('flex');
        }

        function closeDeleteDeviceModal() {
            document.getElementById('deleteDeviceModal').classList.add('hidden');
            document.getElementById('deleteDeviceModal').classList.remove('flex');
            deleteDeviceId = null;
        }

        function confirmDeleteDevice() {
            fetch(`<?= base_url('config/deleteDeviceAssignment') ?>/${deleteDeviceId}`, { method: 'POST' })
                .then(r => r.json()).then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        closeDeleteDeviceModal();
                        loadDeviceAssignments();
                    } else {
                        showToast(data.message || 'Error occurred', 'error');
                    }
                });
        }

        function openIpRangeModal() {
            document.getElementById('ipRangeFrom').value = globalIpRange.from;
            document.getElementById('ipRangeTo').value = globalIpRange.to;
            document.getElementById('ipRangeModal').classList.remove('hidden');
            document.getElementById('ipRangeModal').classList.add('flex');
        }

        function closeIpRangeModal() {
            document.getElementById('ipRangeModal').classList.add('hidden');
            document.getElementById('ipRangeModal').classList.remove('flex');
        }

        function submitIpRangeForm(e) {
            e.preventDefault();
            const from = document.getElementById('ipRangeFrom').value;
            const to = document.getElementById('ipRangeTo').value;

            fetch('<?= base_url('config/saveIpRangeSettings') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ip_range_from: from, ip_range_to: to })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    globalIpRange.from = from;
                    globalIpRange.to = to;

                    const elFrom = document.getElementById('displayIpRangeFrom');
                    const elTo = document.getElementById('displayIpRangeTo');
                    if (elFrom) elFrom.textContent = from || '-';
                    if (elTo) elTo.textContent = to || '-';

                    closeIpRangeModal();
                    loadDeviceAssignments(1, document.getElementById('deviceSearch').value);
                } else {
                    showToast(data.message || 'Error saving settings', 'error');
                }
            });
        }

        function fetchEmailTemplateFormSettings() {
            fetch('<?= base_url('config/getEmailTemplateFormSettings') ?>')
                .then(response => response.json())
                .then(data => {
                    if (!data.success || !data.data) {
                        return;
                    }

                    document.querySelectorAll('.email-template-field').forEach(input => {
                        const field = input.dataset.field;
                        input.checked = data.data[field] !== false;
                    });
                })
                .catch(error => {
                    console.error('Error loading email template settings:', error);
                });
        }

        function saveEmailTemplateFormSettings() {
            const payload = {};

            document.querySelectorAll('.email-template-field').forEach(input => {
                payload[input.dataset.field] = input.checked;
            });

            fetch('<?= base_url('config/saveEmailTemplateFormSettings') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message || 'Error saving email template settings', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error saving email template settings:', error);
                    showToast('Error saving email template settings', 'error');
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            fetchEmailTemplateFormSettings();
        });
    </script>
</body>

</html>