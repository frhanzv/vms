<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
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
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden transition-colors duration-200">
    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col justify-between p-4 hidden md:flex h-full">
        <div class="flex flex-col gap-8">
            <div class="flex items-center gap-3 px-2">
                <div class="bg-center bg-no-repeat bg-cover rounded-lg size-10 bg-primary/10 flex items-center justify-center text-primary" data-alt="SafeG Logo abstract blue square">
                    <span class="material-symbols-outlined text-3xl">shield_person</span>
                </div>
                <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">SafeG</h1>
            </div>
            <nav class="flex flex-col gap-2">
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('dashboard') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">dashboard</span>
                    <p class="text-sm font-medium">Dashboard</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('invitations') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">mail</span>
                    <p class="text-sm font-medium">Invitations</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('requests') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">assignment</span>
                    <p class="text-sm font-medium">Request List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('visitors') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">group</span>
                    <p class="text-sm font-medium">Visitors List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('logbook') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">menu_book</span>
                    <p class="text-sm font-medium">Visitor Logbook</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('workflow') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">account_tree</span>
                    <p class="text-sm font-medium">Visitor Workflow</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary group transition-colors" href="<?= base_url('config') ?>">
                    <span class="material-symbols-outlined text-[22px] font-medium fill-1 group-hover:scale-110 transition-transform">tune</span>
                    <p class="text-sm font-semibold">Config</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('settings') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">settings</span>
                    <p class="text-sm font-medium">Settings</p>
                </a>
            </nav>
        </div>
        <div class="border-t border-slate-200 dark:border-slate-700 pt-4 px-2">
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shadow-sm ring-2 ring-white dark:ring-slate-900">
                    <?= strtoupper(substr(session()->get('full_name') ?? 'U', 0, 2)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white truncate"><?= esc(session()->get('full_name') ?? 'User') ?></p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate"><?= esc(ucfirst(session()->get('role') ?? 'User')) ?></p>
                </div>
                <a href="<?= base_url('auth/logout') ?>" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <span class="material-symbols-outlined text-xl">logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8 bg-background-light dark:bg-background-dark">
        <div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-7xl">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">
                        System Configuration
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage system settings and configurations</p>
                </div>
            </div>

            <!-- Configuration Sections -->
            <div class="space-y-4">
                <!-- General Settings -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('general')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">settings_applications</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">General Settings</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Configure basic system information and preferences</p>
                            </div>
                        </div>
                        <span id="general-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="general-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Company Name</label>
                                    <input value="SafeG Enterprise Sdn Bhd" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white" type="text"/>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Company Address</label>
                                    <input value="Jalan Teknologi 5, Kota Kinabalu Industrial Park" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white" type="text"/>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Security Office Contact</label>
                                    <input value="+60 88-123 4567" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white" type="tel"/>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">System Email</label>
                                    <input value="security@safeg.com.my" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white" type="email"/>
                                </div>
                                <div class="md:col-span-2 flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Date Format</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="date_format" value="DD/MM/YYYY" checked class="text-primary focus:ring-primary"/>
                                            <span class="text-sm text-slate-700 dark:text-slate-300">DD/MM/YYYY</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="date_format" value="MM/DD/YYYY" class="text-primary focus:ring-primary"/>
                                            <span class="text-sm text-slate-700 dark:text-slate-300">MM/DD/YYYY</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="date_format" value="YYYY-MM-DD" class="text-primary focus:ring-primary"/>
                                            <span class="text-sm text-slate-700 dark:text-slate-300">YYYY-MM-DD</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 mt-6">
                                <button class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Cancel</button>
                                <button class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm">Save Changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('role')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">admin_panel_settings</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Role Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage user roles and permissions</p>
                            </div>
                        </div>
                        <span id="role-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="role-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex shadow-sm w-full sm:w-96">
                                    <input id="role-search-input" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search role name..." type="text"/>
                                    <button onclick="searchRoles()" class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                        <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                    </button>
                                </div>
                                <button onclick="openCreateRoleModal()" class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Role
                                </button>
                            </div>

                            <!-- Roles Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
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
                                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
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
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('user')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">manage_accounts</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">User Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage system users and accounts</p>
                            </div>
                        </div>
                        <span id="user-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="user-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="user-search-input" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search username, name, email..." type="text"/>
                                        <button onclick="searchUsers()" class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="user-sort-select" onchange="sortUsers()" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="username_asc">Username (A-Z)</option>
                                            <option value="username_desc">Username (Z-A)</option>
                                            <option value="name_asc">Full Name (A-Z)</option>
                                            <option value="name_desc">Full Name (Z-A)</option>
                                            <option value="email_asc">Email (A-Z)</option>
                                            <option value="email_desc">Email (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateUserModal()" class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create User
                                </button>
                            </div>

                            <!-- Users Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
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
                                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
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
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('company')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">business</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Company Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage registered companies and contractors</p>
                            </div>
                        </div>
                        <span id="company-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="company-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="companySearchInput" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search company name, reg no, email..." type="text"/>
                                        <button onclick="searchCompanies()" class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="companySortSelect" onchange="sortCompanies()" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="name_asc">Name (A-Z)</option>
                                            <option value="name_desc">Name (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateCompanyModal()" class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Company
                                </button>
                            </div>

                            <!-- Companies Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
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
                                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                                                    <span>Loading companies...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div id="companyPagination" class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="companyShowingFrom">0</span> to <span id="companyShowingTo">0</span> of <span id="companyTotalCount">0</span> companies
                                </p>
                                <div id="companyPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub Company Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('subcompany')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">corporate_fare</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Sub Company Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage subsidiary and branch companies</p>
                            </div>
                        </div>
                        <span id="subcompany-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="subcompany-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Filter and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="subCompanySearchInput" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search sub company name..." type="text"/>
                                        <button onclick="searchSubCompanies()" class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="subCompanyFilterSelect" onchange="filterSubCompanies()" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">All Companies</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateSubCompanyModal()" class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Sub Company
                                </button>
                            </div>

                            <!-- Sub Companies Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
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
                                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                                                    <span>Loading sub companies...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div id="subCompanyPagination" class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="subCompanyShowingFrom">0</span> to <span id="subCompanyShowingTo">0</span> of <span id="subCompanyTotalCount">0</span> sub companies
                                </p>
                                <div id="subCompanyPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Country Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('country')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">public</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Country Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage country list and codes</p>
                            </div>
                        </div>
                        <span id="country-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="country-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="countrySearchInput" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search country name, code..." type="text"/>
                                        <button onclick="searchCountries()" class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="countrySortSelect" onchange="sortCountries()" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="name_asc">Country (A-Z)</option>
                                            <option value="name_desc">Country (Z-A)</option>
                                            <option value="code_asc">Code (A-Z)</option>
                                            <option value="code_desc">Code (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateCountryModal()" class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Country
                                </button>
                            </div>

                            <!-- Countries Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Country Code</th>
                                            <th class="px-4 py-3">Country Name</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="countryTableBody" class="text-gray-700 dark:text-slate-300">
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                                                    <span>Loading countries...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div id="countryPagination" class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="countryShowingFrom">0</span> to <span id="countryShowingTo">0</span> of <span id="countryTotalCount">0</span> countries
                                </p>
                                <div id="countryPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- State Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('state')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">map</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">State Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage state list and country associations</p>
                            </div>
                        </div>
                        <span id="state-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="state-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="stateSearchInput" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search state name..." type="text"/>
                                        <button onclick="searchStates()" class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="stateCountryFilter" onchange="filterStates()" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">All Countries</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select id="stateSortSelect" onchange="sortStates()" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="name_asc">State (A-Z)</option>
                                            <option value="name_desc">State (Z-A)</option>
                                            <option value="country_asc">Country (A-Z)</option>
                                            <option value="country_desc">Country (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button onclick="openCreateStateModal()" class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create State
                                </button>
                            </div>

                            <!-- States Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
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
                                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                                                    <span>Loading states...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div id="statePagination" class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="stateShowingFrom">0</span> to <span id="stateShowingTo">0</span> of <span id="stateTotalCount">0</span> states
                                </p>
                                <div id="statePaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- City Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('city')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">location_city</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">City Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage city list and state associations</p>
                            </div>
                        </div>
                        <span id="city-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="city-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col gap-3 mb-4">
                                <div class="flex flex-col lg:flex-row gap-3">
                                    <div class="flex shadow-sm w-full lg:w-80">
                                        <input id="citySearchInput" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search city name..." type="text"/>
                                        <button onclick="searchCities()" class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full lg:w-44">
                                        <select id="cityCountryFilter" onchange="filterCitiesByCountry()" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">All Countries</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                    <div class="relative w-full lg:w-40">
                                        <select id="cityStateFilter" onchange="filterCities()" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">All States</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                    <div class="relative w-full lg:w-40">
                                        <select id="citySortSelect" onchange="sortCities()" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="name_asc">City (A-Z)</option>
                                            <option value="name_desc">City (Z-A)</option>
                                            <option value="state_asc">State (A-Z)</option>
                                            <option value="state_desc">State (Z-A)</option>
                                            <option value="country_asc">Country (A-Z)</option>
                                            <option value="country_desc">Country (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                    <button onclick="openCreateCityModal()" class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center justify-center gap-2 w-full lg:w-auto lg:ml-auto whitespace-nowrap">
                                        <span class="material-symbols-outlined text-base">add</span>
                                        Create City
                                    </button>
                                </div>
                            </div>

                            <!-- Cities Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
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
                                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                                                    <span>Loading cities...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div id="cityPagination" class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span id="cityShowingFrom">0</span> to <span id="cityShowingTo">0</span> of <span id="cityTotalCount">0</span> cities
                                </p>
                                <div id="cityPaginationButtons" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('department')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">corporate_fare</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Department Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage department list</p>
                            </div>
                        </div>
                        <span id="department-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="department-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search department name..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="department_asc">Department (A-Z)</option>
                                            <option value="department_desc">Department (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Department
                                </button>
                            </div>

                            <!-- Departments Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Department</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Human Resources</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Finance</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Information Technology</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Marketing</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Operations</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Sales</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Customer Service</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Legal</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
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
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">8</span> of <span class="font-medium">42</span> departments
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <span class="px-2 text-gray-400">...</span>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">6</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Designation Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('designation')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">work</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Designation Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage job designation list</p>
                            </div>
                        </div>
                        <span id="designation-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="designation-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search designation name..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="name_asc">Name (A-Z)</option>
                                            <option value="name_desc">Name (Z-A)</option>
                                            <option value="wo_flag">WO Flag</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Designation
                                </button>
                            </div>

                            <!-- Designations Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Name</th>
                                            <th class="px-4 py-3">Description</th>
                                            <th class="px-4 py-3">WO Flag</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Manager</td>
                                            <td class="px-4 py-3">Manages team and operations</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Senior Executive</td>
                                            <td class="px-4 py-3">Senior level executive position</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Executive</td>
                                            <td class="px-4 py-3">Mid-level executive position</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Supervisor</td>
                                            <td class="px-4 py-3">Supervises daily operations</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Officer</td>
                                            <td class="px-4 py-3">General officer position</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Assistant</td>
                                            <td class="px-4 py-3">Provides administrative support</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Technician</td>
                                            <td class="px-4 py-3">Technical support specialist</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Intern</td>
                                            <td class="px-4 py-3">Temporary internship position</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
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
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">8</span> of <span class="font-medium">68</span> designations
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <span class="px-2 text-gray-400">...</span>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">9</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Access Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('location')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">location_on</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Location Access Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage location access configurations</p>
                            </div>
                        </div>
                        <span id="location-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="location-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search branch, location..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="branch_asc">Branch (A-Z)</option>
                                            <option value="branch_desc">Branch (Z-A)</option>
                                            <option value="location_asc">Location (A-Z)</option>
                                            <option value="location_desc">Location (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Location
                                </button>
                            </div>

                            <!-- Location Access Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Branch</th>
                                            <th class="px-4 py-3">Location Access</th>
                                            <th class="px-4 py-3">Adam IP</th>
                                            <th class="px-4 py-3">Adam Password</th>
                                            <th class="px-4 py-3">Mobile APP</th>
                                            <th class="px-4 py-3">Is Hold Area</th>
                                            <th class="px-4 py-3">Visitor Pass Print</th>
                                            <th class="px-4 py-3">Turnstile</th>
                                            <th class="px-4 py-3">In/Out Bound</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Main Office</td>
                                            <td class="px-4 py-3">Reception Area</td>
                                            <td class="px-4 py-3">192.168.1.100</td>
                                            <td class="px-4 py-3">••••••••</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">Inbound</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Main Office</td>
                                            <td class="px-4 py-3">Security Gate</td>
                                            <td class="px-4 py-3">192.168.1.101</td>
                                            <td class="px-4 py-3">••••••••</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">Both</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">North Branch</td>
                                            <td class="px-4 py-3">Lobby Entrance</td>
                                            <td class="px-4 py-3">192.168.2.100</td>
                                            <td class="px-4 py-3">••••••••</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3">Inbound</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">South Branch</td>
                                            <td class="px-4 py-3">Parking Entry</td>
                                            <td class="px-4 py-3">192.168.3.100</td>
                                            <td class="px-4 py-3">••••••••</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Disabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Disabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">Outbound</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">East Branch</td>
                                            <td class="px-4 py-3">Main Gate</td>
                                            <td class="px-4 py-3">192.168.4.100</td>
                                            <td class="px-4 py-3">••••••••</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">Both</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">West Branch</td>
                                            <td class="px-4 py-3">Service Entrance</td>
                                            <td class="px-4 py-3">192.168.5.100</td>
                                            <td class="px-4 py-3">••••••••</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Disabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3">Inbound</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
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
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">6</span> of <span class="font-medium">23</span> locations
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <span class="px-2 text-gray-400">...</span>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">4</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lane Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('lane')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">alt_route</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Lane Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage lane configurations and equipment</p>
                            </div>
                        </div>
                        <span id="lane-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="lane-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search lane, location..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="lane_asc">Lane (A-Z)</option>
                                            <option value="lane_desc">Lane (Z-A)</option>
                                            <option value="location_asc">Location (A-Z)</option>
                                            <option value="location_desc">Location (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Lane
                                </button>
                            </div>

                            <!-- Lane Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
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
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Lane 1</td>
                                            <td class="px-4 py-3">Reception Area</td>
                                            <td class="px-4 py-3">BR-001</td>
                                            <td class="px-4 py-3">WG-101</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3">192.168.1.50</td>
                                            <td class="px-4 py-3">192.168.1.51</td>
                                            <td class="px-4 py-3">CAM-001</td>
                                            <td class="px-4 py-3">CAM-002</td>
                                            <td class="px-4 py-3">CAM-003</td>
                                            <td class="px-4 py-3">192.168.1.60</td>
                                            <td class="px-4 py-3">192.168.1.61</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3">admin</td>
                                            <td class="px-4 py-3">2026-01-15 14:30</td>
                                            <td class="px-4 py-3">2026-01-10 09:00</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Lane 2</td>
                                            <td class="px-4 py-3">Security Gate</td>
                                            <td class="px-4 py-3">BR-002</td>
                                            <td class="px-4 py-3">WG-102</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3">192.168.1.52</td>
                                            <td class="px-4 py-3">192.168.1.53</td>
                                            <td class="px-4 py-3">CAM-004</td>
                                            <td class="px-4 py-3">CAM-005</td>
                                            <td class="px-4 py-3">CAM-006</td>
                                            <td class="px-4 py-3">192.168.1.62</td>
                                            <td class="px-4 py-3">192.168.1.63</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3">security1</td>
                                            <td class="px-4 py-3">2026-01-16 08:15</td>
                                            <td class="px-4 py-3">2026-01-12 11:30</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Lane 3</td>
                                            <td class="px-4 py-3">Lobby Entrance</td>
                                            <td class="px-4 py-3">BR-003</td>
                                            <td class="px-4 py-3">WG-103</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Disabled</span></td>
                                            <td class="px-4 py-3">192.168.2.50</td>
                                            <td class="px-4 py-3">192.168.2.51</td>
                                            <td class="px-4 py-3">CAM-007</td>
                                            <td class="px-4 py-3">CAM-008</td>
                                            <td class="px-4 py-3">-</td>
                                            <td class="px-4 py-3">192.168.2.60</td>
                                            <td class="px-4 py-3">-</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3">operator1</td>
                                            <td class="px-4 py-3">2026-01-14 16:45</td>
                                            <td class="px-4 py-3">2026-01-08 13:20</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Lane 4</td>
                                            <td class="px-4 py-3">Parking Entry</td>
                                            <td class="px-4 py-3">BR-004</td>
                                            <td class="px-4 py-3">WG-104</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3">192.168.3.50</td>
                                            <td class="px-4 py-3">192.168.3.51</td>
                                            <td class="px-4 py-3">CAM-009</td>
                                            <td class="px-4 py-3">CAM-010</td>
                                            <td class="px-4 py-3">CAM-011</td>
                                            <td class="px-4 py-3">192.168.3.60</td>
                                            <td class="px-4 py-3">192.168.3.61</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3">parking_staff</td>
                                            <td class="px-4 py-3">2026-01-15 10:20</td>
                                            <td class="px-4 py-3">2026-01-11 14:15</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
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
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">4</span> of <span class="font-medium">15</span> lanes
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <span class="px-2 text-gray-400">...</span>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">4</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reject Reason Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('reject')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">block</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Reject Reason Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage rejection reasons for applications</p>
                            </div>
                        </div>
                        <span id="reject-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="reject-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search reject reason..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="reason_asc">Reason (A-Z)</option>
                                            <option value="reason_desc">Reason (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Reject Reason
                                </button>
                            </div>

                            <!-- Reject Reasons Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Reject Reason</th>
                                            <th class="px-4 py-3">Mobile App</th>
                                            <th class="px-4 py-3">Commercial</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Incomplete Documentation</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Invalid Information</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Security Concerns</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Unauthorized Access</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Disabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Blacklisted Visitor</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Host Not Available</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">No Show</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Enabled</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
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
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">7</span> of <span class="font-medium">18</span> reject reasons
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visitor Card Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('card')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">badge</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Visitor Card Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage visitor card inventory</p>
                            </div>
                        </div>
                        <span id="card-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="card-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search card ID, serial number..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="card_asc">Card ID (A-Z)</option>
                                            <option value="card_desc">Card ID (Z-A)</option>
                                            <option value="serial_asc">Serial No (A-Z)</option>
                                            <option value="serial_desc">Serial No (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Card
                                </button>
                            </div>

                            <!-- Visitor Cards Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Card ID</th>
                                            <th class="px-4 py-3">Serial No</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">VC-001</td>
                                            <td class="px-4 py-3">SN-2024-001</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">VC-002</td>
                                            <td class="px-4 py-3">SN-2024-002</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">VC-003</td>
                                            <td class="px-4 py-3">SN-2024-003</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded text-xs font-semibold">In Use</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">VC-004</td>
                                            <td class="px-4 py-3">SN-2024-004</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded text-xs font-semibold">In Use</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">VC-005</td>
                                            <td class="px-4 py-3">SN-2024-005</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">VC-006</td>
                                            <td class="px-4 py-3">SN-2024-006</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs font-semibold">Lost</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">VC-007</td>
                                            <td class="px-4 py-3">SN-2024-007</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">VC-008</td>
                                            <td class="px-4 py-3">SN-2024-008</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
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
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">8</span> of <span class="font-medium">52</span> cards
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <span class="px-2 text-gray-400">...</span>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">7</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Video Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('video')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">videocam</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Video Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage video content</p>
                            </div>
                        </div>
                        <span id="video-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="video-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search video name..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="name_asc">Name (A-Z)</option>
                                            <option value="name_desc">Name (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Upload Video
                                </button>
                            </div>

                            <!-- Videos Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Name</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Safety Orientation Video</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Visitor Guidelines</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Emergency Procedures</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Company Introduction</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Security Training</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
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
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">12</span> videos
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visit Reason Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('reason')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">help</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Visit Reason Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage visit purpose reasons</p>
                            </div>
                        </div>
                        <span id="reason-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="reason-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search visit reason..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="no_asc">No (Ascending)</option>
                                            <option value="no_desc">No (Descending)</option>
                                            <option value="reason_asc">Reason (A-Z)</option>
                                            <option value="reason_desc">Reason (Z-A)</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Visit Reason
                                </button>
                            </div>

                            <!-- Visit Reasons Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">No</th>
                                            <th class="px-4 py-3">Visit Reason</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">1</td>
                                            <td class="px-4 py-3">Business Meeting</td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">2</td>
                                            <td class="px-4 py-3">Delivery</td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">3</td>
                                            <td class="px-4 py-3">Interview</td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">4</td>
                                            <td class="px-4 py-3">Maintenance</td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">5</td>
                                            <td class="px-4 py-3">Consultation</td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">6</td>
                                            <td class="px-4 py-3">Training</td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">7</td>
                                            <td class="px-4 py-3">Site Inspection</td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
                                                        <span class="material-symbols-outlined text-xl">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">8</td>
                                            <td class="px-4 py-3">Personal Visit</td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80">
                                                        <span class="material-symbols-outlined text-xl">edit</span>
                                                    </button>
                                                    <button class="text-red-500 hover:text-red-400">
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
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">8</span> of <span class="font-medium">24</span> visit reasons
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visitor QR Code Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('qrcode')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">qr_code_2</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Visitor QR Code Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage QR codes for visitor access</p>
                            </div>
                        </div>
                        <span id="qrcode-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="qrcode-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search URL, location..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="date_desc">Created Date (Newest)</option>
                                            <option value="date_asc">Created Date (Oldest)</option>
                                            <option value="location_asc">Location (A-Z)</option>
                                            <option value="location_desc">Location (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Generate QR Code
                                </button>
                            </div>

                            <!-- QR Codes Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
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
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3">
                                                <div class="w-16 h-16 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-3xl text-gray-400">qr_code_2</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium text-primary truncate max-w-xs">https://vms.company.com/check-in/rec001</span>
                                                    <button class="text-gray-400 hover:text-primary" title="Copy URL">
                                                        <span class="material-symbols-outlined text-lg">content_copy</span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">Reception Area</td>
                                            <td class="px-4 py-3">2026-01-15 10:30</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80" title="Download QR">
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
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3">
                                                <div class="w-16 h-16 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-3xl text-gray-400">qr_code_2</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium text-primary truncate max-w-xs">https://vms.company.com/check-in/gate001</span>
                                                    <button class="text-gray-400 hover:text-primary" title="Copy URL">
                                                        <span class="material-symbols-outlined text-lg">content_copy</span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">Security Gate</td>
                                            <td class="px-4 py-3">2026-01-14 14:20</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80" title="Download QR">
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
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3">
                                                <div class="w-16 h-16 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-3xl text-gray-400">qr_code_2</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium text-primary truncate max-w-xs">https://vms.company.com/check-in/lobby001</span>
                                                    <button class="text-gray-400 hover:text-primary" title="Copy URL">
                                                        <span class="material-symbols-outlined text-lg">content_copy</span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">Lobby Entrance</td>
                                            <td class="px-4 py-3">2026-01-13 09:15</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80" title="Download QR">
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
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3">
                                                <div class="w-16 h-16 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-3xl text-gray-400">qr_code_2</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium text-primary truncate max-w-xs">https://vms.company.com/check-in/park001</span>
                                                    <button class="text-gray-400 hover:text-primary" title="Copy URL">
                                                        <span class="material-symbols-outlined text-lg">content_copy</span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">Parking Entry</td>
                                            <td class="px-4 py-3">2026-01-12 16:45</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded text-xs font-semibold">Pending</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80" title="Download QR">
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
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3">
                                                <div class="w-16 h-16 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-3xl text-gray-400">qr_code_2</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium text-primary truncate max-w-xs">https://vms.company.com/check-in/main001</span>
                                                    <button class="text-gray-400 hover:text-primary" title="Copy URL">
                                                        <span class="material-symbols-outlined text-lg">content_copy</span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">Main Gate</td>
                                            <td class="px-4 py-3">2026-01-11 11:30</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <div class="flex gap-2">
                                                    <button class="text-primary hover:text-primary/80" title="Download QR">
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
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">16</span> QR codes
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <span class="px-2 text-gray-400">...</span>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">4</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visitor Settings -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('visitor')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">badge</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Visitor Settings</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Configure visitor registration and check-in preferences</p>
                            </div>
                        </div>
                        <span id="visitor-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="visitor-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50 space-y-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Auto-approve Invitations</p>
                                    <p class="text-xs text-slate-500 mt-1">Automatically approve visitor invitations without manual review</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Require Photo Upload</p>
                                    <p class="text-xs text-slate-500 mt-1">Mandatory photo upload for all visitor registrations</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Enable QR Code Check-in</p>
                                    <p class="text-xs text-slate-500 mt-1">Allow visitors to check-in using QR code scanning</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('security')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">security</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Security Settings</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Configure authentication and security preferences</p>
                            </div>
                        </div>
                        <span id="security-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="security-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50 space-y-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Two-Factor Authentication</p>
                                    <p class="text-xs text-slate-500 mt-1">Require 2FA for all user accounts</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Session Timeout (Minutes)</label>
                                <input value="30" type="number" min="5" max="120" class="w-full md:w-48 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white"/>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Password Minimum Length</label>
                                <input value="8" type="number" min="6" max="20" class="w-full md:w-48 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white"/>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('notification')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">notifications</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Notification Settings</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage email, SMS, and push notifications</p>
                            </div>
                        </div>
                        <span id="notification-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="notification-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50 space-y-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Email Notifications</p>
                                    <p class="text-xs text-slate-500 mt-1">Send email notifications for visitor arrivals</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">SMS Notifications</p>
                                    <p class="text-xs text-slate-500 mt-1">Send SMS alerts to hosts when visitors arrive</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Push Notifications</p>
                                    <p class="text-xs text-slate-500 mt-1">Enable browser push notifications</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Logs -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('logs')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">description</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">System Logs</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">View and manage system activity logs</p>
                            </div>
                        </div>
                        <span id="logs-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="logs-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex gap-2">
                                    <button onclick="filterLogs('all')" id="filter-all" class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-300 dark:hover:bg-slate-600 transition-colors text-sm">All Logs</button>
                                    <button onclick="filterLogs('error')" id="filter-error" class="px-4 py-2 rounded-lg text-gray-600 dark:text-slate-400 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Errors Only</button>
                                    <button onclick="filterLogs('warning')" id="filter-warning" class="px-4 py-2 rounded-lg text-gray-600 dark:text-slate-400 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Warnings</button>
                                    <button onclick="filterLogs('info')" id="filter-info" class="px-4 py-2 rounded-lg text-gray-600 dark:text-slate-400 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Info</button>
                                    <button onclick="filterLogs('debug')" id="filter-debug" class="px-4 py-2 rounded-lg text-gray-600 dark:text-slate-400 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Debug</button>
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="refreshLogs()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base">refresh</span>
                                        Refresh
                                    </button>
                                    <button onclick="exportLogs()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base">download</span>
                                        Export Logs
                                    </button>
                                </div>
                            </div>
                            <div id="logs-container" class="bg-white dark:bg-slate-900 rounded-lg p-4 font-mono text-xs text-gray-700 dark:text-slate-300 max-h-96 overflow-y-auto border border-gray-200 dark:border-slate-700">
                                <div class="text-gray-500 dark:text-slate-400 text-center py-8">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
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
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Role Name <span class="text-red-500">*</span></label>
                        <input type="text" id="roleName" name="name" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Enter role name">
                        <p id="roleNameError" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Description</label>
                        <textarea id="roleDescription" name="description" rows="3" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Enter role description"></textarea>
                        <p id="roleDescriptionError" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Status <span class="text-red-500">*</span></label>
                        <select id="roleStatus" name="status" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <p id="roleStatusError" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeRoleModal()" class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                        Cancel
                    </button>
                    <button type="submit" id="roleSubmitBtn" class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2">
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
                <p class="text-gray-700 dark:text-slate-300">Are you sure you want to delete this role? This action cannot be undone.</p>
                <p id="deleteRoleName" class="mt-2 font-semibold text-gray-900 dark:text-white"></p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                    Cancel
                </button>
                <button onclick="confirmDeleteRole()" class="px-4 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">delete</span>
                    Delete Role
                </button>
            </div>
        </div>
    </div>

    <!-- User Create/Edit Modal -->
    <div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
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
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Username <span class="text-red-500">*</span></label>
                            <input type="text" id="userUsername" name="username" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Enter username">
                            <p id="userUsernameError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" id="userFullName" name="full_name" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Enter full name">
                            <p id="userFull_nameError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="userEmail" name="email" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Enter email">
                            <p id="userEmailError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">
                                Password <span id="passwordRequired" class="text-red-500">*</span>
                                <span id="passwordOptional" class="text-gray-500 text-xs hidden">(leave blank to keep current)</span>
                            </label>
                            <input type="password" id="userPassword" name="password" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Enter password">
                            <p id="userPasswordError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Staff ID</label>
                            <input type="text" id="userStaffId" name="staff_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Enter staff ID">
                            <p id="userStaff_idError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Contact Number</label>
                            <input type="text" id="userContactNo" name="contact_no" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Enter contact number">
                            <p id="userContact_noError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Role <span class="text-red-500">*</span></label>
                            <select id="userRole" name="role" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="">Select Role</option>
                            </select>
                            <p id="userRoleError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Status <span class="text-red-500">*</span></label>
                            <select id="userStatus" name="is_active" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <p id="userIs_activeError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeUserModal()" class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                        Cancel
                    </button>
                    <button type="submit" id="userSubmitBtn" class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2">
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
                <p class="text-gray-700 dark:text-slate-300">Are you sure you want to delete this user? This action cannot be undone.</p>
                <p id="deleteUserName" class="mt-2 font-semibold text-gray-900 dark:text-white"></p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                <button onclick="closeDeleteUserModal()" class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">
                    Cancel
                </button>
                <button onclick="confirmDeleteUser()" class="px-4 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">delete</span>
                    Delete User
                </button>
            </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('role-search-input');
            if (searchInput) {
                // Enter key support
                searchInput.addEventListener('keypress', function(e) {
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
        document.addEventListener('DOMContentLoaded', function() {
            const userSearchInput = document.getElementById('user-search-input');
            if (userSearchInput) {
                userSearchInput.addEventListener('keypress', function(e) {
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
        document.getElementById('companySearchInput').addEventListener('keypress', function(e) {
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
        document.getElementById('subCompanySearchInput').addEventListener('keypress', function(e) {
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
        document.getElementById('countrySearchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchCountries();
            }
        });

        // State search Enter key
        document.addEventListener('DOMContentLoaded', function() {
            // Delegate event listener for state search input (since it's loaded dynamically)
            document.addEventListener('keypress', function(e) {
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
        document.addEventListener('keypress', function(e) {
            if (e.target && e.target.id === 'citySearchInput' && e.key === 'Enter') {
                e.preventDefault();
                searchCities();
            }
        });
    </script>
</body>
</html>
