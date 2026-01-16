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
                                    <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search role name..." type="text"/>
                                    <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                        <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                    </button>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
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
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Super Admin</td>
                                            <td class="px-4 py-3">Full system access and control</td>
                                            <td class="px-4 py-3">2</td>
                                            <td class="px-4 py-3">10/01/2026</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Security Officer</td>
                                            <td class="px-4 py-3">Manage visitor check-in/out and security operations</td>
                                            <td class="px-4 py-3">15</td>
                                            <td class="px-4 py-3">10/01/2026</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Host</td>
                                            <td class="px-4 py-3">Create invitations and manage own visitors</td>
                                            <td class="px-4 py-3">48</td>
                                            <td class="px-4 py-3">10/01/2026</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Receptionist</td>
                                            <td class="px-4 py-3">Process walk-in visitors and view invitations</td>
                                            <td class="px-4 py-3">8</td>
                                            <td class="px-4 py-3">10/01/2026</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Viewer</td>
                                            <td class="px-4 py-3">Read-only access to visitor records</td>
                                            <td class="px-4 py-3">12</td>
                                            <td class="px-4 py-3">10/01/2026</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">5</span> roles
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
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search username, name, email..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="username_asc">Username (A-Z)</option>
                                            <option value="username_desc">Username (Z-A)</option>
                                            <option value="name_asc">Full Name (A-Z)</option>
                                            <option value="name_desc">Full Name (Z-A)</option>
                                            <option value="email_asc">Email (A-Z)</option>
                                            <option value="email_desc">Email (Z-A)</option>
                                            <option value="status">Status</option>
                                            <option value="verified">Verified</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
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
                                            <th class="px-4 py-3">IC No/Passport No/Staff ID</th>
                                            <th class="px-4 py-3">Email</th>
                                            <th class="px-4 py-3">Contact Number</th>
                                            <th class="px-4 py-3">Verified</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">admin01</td>
                                            <td class="px-4 py-3">Ahmad Bin Abdullah</td>
                                            <td class="px-4 py-3">850215-12-5678</td>
                                            <td class="px-4 py-3">ahmad.abdullah@safeg.com.my</td>
                                            <td class="px-4 py-3">+60 12-345 6789</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">security05</td>
                                            <td class="px-4 py-3">Mohd Rizal Bin Hassan</td>
                                            <td class="px-4 py-3">900823-10-4321</td>
                                            <td class="px-4 py-3">rizal.hassan@safeg.com.my</td>
                                            <td class="px-4 py-3">+60 13-987 6543</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">host_siti</td>
                                            <td class="px-4 py-3">Siti Nurhaliza Binti Tarudin</td>
                                            <td class="px-4 py-3">920501-14-8765</td>
                                            <td class="px-4 py-3">siti.nurhaliza@safeg.com.my</td>
                                            <td class="px-4 py-3">+60 19-456 7890</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">reception02</td>
                                            <td class="px-4 py-3">Tan Mei Ling</td>
                                            <td class="px-4 py-3">STF-2024-089</td>
                                            <td class="px-4 py-3">meiling.tan@safeg.com.my</td>
                                            <td class="px-4 py-3">+60 16-234 5678</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-orange-500/20 text-orange-400 rounded text-xs font-semibold">Pending</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">kumar_view</td>
                                            <td class="px-4 py-3">Kumar A/L Raman</td>
                                            <td class="px-4 py-3">880612-08-3456</td>
                                            <td class="px-4 py-3">kumar.raman@safeg.com.my</td>
                                            <td class="px-4 py-3">+60 17-765 4321</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">host_lee</td>
                                            <td class="px-4 py-3">Lee Chong Wei</td>
                                            <td class="px-4 py-3">P8765432A</td>
                                            <td class="px-4 py-3">chongwei.lee@safeg.com.my</td>
                                            <td class="px-4 py-3">+60 18-876 5432</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">6</span> of <span class="font-medium">85</span> users
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <span class="px-2 text-gray-400">...</span>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">15</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
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
                            <!-- Search, Export and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search company name, reg ID..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="company_asc">Company Name (A-Z)</option>
                                            <option value="company_desc">Company Name (Z-A)</option>
                                            <option value="regid_asc">Reg ID (A-Z)</option>
                                            <option value="regid_desc">Reg ID (Z-A)</option>
                                            <option value="branch">Branch</option>
                                            <option value="account_type">Account Type</option>
                                            <option value="status">Haulier Status</option>
                                            <option value="created_date">Created Date</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <div class="flex gap-2 w-full sm:w-auto">
                                    <button class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm flex items-center gap-2 flex-1 sm:flex-initial">
                                        <span class="material-symbols-outlined text-base">download</span>
                                        Export
                                    </button>
                                    <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 flex-1 sm:flex-initial">
                                        <span class="material-symbols-outlined text-base">add</span>
                                        Create Company
                                    </button>
                                </div>
                            </div>

                            <!-- Companies Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Branch</th>
                                            <th class="px-4 py-3">Company Reg ID</th>
                                            <th class="px-4 py-3">Company Name</th>
                                            <th class="px-4 py-3">Company Name In Port Pass</th>
                                            <th class="px-4 py-3">Ledger</th>
                                            <th class="px-4 py-3">Account Type</th>
                                            <th class="px-4 py-3">Haulier Status</th>
                                            <th class="px-4 py-3">Patched</th>
                                            <th class="px-4 py-3">Registered</th>
                                            <th class="px-4 py-3">Created By</th>
                                            <th class="px-4 py-3">Created Date</th>
                                            <th class="px-4 py-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">KK01</td>
                                            <td class="px-4 py-3">202301234-V</td>
                                            <td class="px-4 py-3">ABC Construction Sdn Bhd</td>
                                            <td class="px-4 py-3">ABC CONSTRUCTION</td>
                                            <td class="px-4 py-3">L-001245</td>
                                            <td class="px-4 py-3">Corporate</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3">12/01/2023</td>
                                            <td class="px-4 py-3">Ahmad Abdullah</td>
                                            <td class="px-4 py-3">12/01/2023</td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">KK02</td>
                                            <td class="px-4 py-3">201905678-K</td>
                                            <td class="px-4 py-3">Express Delivery Services</td>
                                            <td class="px-4 py-3">EXPRESS DELIVERY SVC</td>
                                            <td class="px-4 py-3">L-001789</td>
                                            <td class="px-4 py-3">SME</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3">05/03/2024</td>
                                            <td class="px-4 py-3">Siti Nurhaliza</td>
                                            <td class="px-4 py-3">05/03/2024</td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">KK01</td>
                                            <td class="px-4 py-3">202208456-A</td>
                                            <td class="px-4 py-3">Catering Solutions Enterprise</td>
                                            <td class="px-4 py-3">CATERING SOLUTIONS</td>
                                            <td class="px-4 py-3">L-002156</td>
                                            <td class="px-4 py-3">Individual</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs font-semibold">No</span></td>
                                            <td class="px-4 py-3">20/08/2024</td>
                                            <td class="px-4 py-3">Mohd Rizal</td>
                                            <td class="px-4 py-3">20/08/2024</td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">SP01</td>
                                            <td class="px-4 py-3">201712345-W</td>
                                            <td class="px-4 py-3">Tech Engineering & Consultant</td>
                                            <td class="px-4 py-3">TECH ENGINEERING</td>
                                            <td class="px-4 py-3">L-000934</td>
                                            <td class="px-4 py-3">Corporate</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-orange-500/20 text-orange-400 rounded text-xs font-semibold">Pending</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3">15/06/2025</td>
                                            <td class="px-4 py-3">Kumar Raman</td>
                                            <td class="px-4 py-3">15/06/2025</td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">KK03</td>
                                            <td class="px-4 py-3">202010987-M</td>
                                            <td class="px-4 py-3">Security Systems & Services</td>
                                            <td class="px-4 py-3">SECURITY SYSTEMS</td>
                                            <td class="px-4 py-3">L-001567</td>
                                            <td class="px-4 py-3">Corporate</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Yes</span></td>
                                            <td class="px-4 py-3">10/10/2024</td>
                                            <td class="px-4 py-3">Lee Chong Wei</td>
                                            <td class="px-4 py-3">10/10/2024</td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">127</span> companies
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <span class="px-2 text-gray-400">...</span>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">26</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
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
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search sub company name..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Filter By Company</option>
                                            <option value="abc_construction">ABC Construction Sdn Bhd</option>
                                            <option value="express_delivery">Express Delivery Services</option>
                                            <option value="catering_solutions">Catering Solutions Enterprise</option>
                                            <option value="tech_engineering">Tech Engineering & Consultant</option>
                                            <option value="security_systems">Security Systems & Services</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
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
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">ABC Construction - KK Branch</td>
                                            <td class="px-4 py-3">ABC Construction Sdn Bhd</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">ABC Construction - Sandakan Division</td>
                                            <td class="px-4 py-3">ABC Construction Sdn Bhd</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Express Delivery - Sabah Region</td>
                                            <td class="px-4 py-3">Express Delivery Services</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Tech Engineering - Project Management Unit</td>
                                            <td class="px-4 py-3">Tech Engineering & Consultant</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-orange-500/20 text-orange-400 rounded text-xs font-semibold">Pending</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Catering Solutions - Event Division</td>
                                            <td class="px-4 py-3">Catering Solutions Enterprise</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Security Systems - Installation Team</td>
                                            <td class="px-4 py-3">Security Systems & Services</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">6</span> of <span class="font-medium">43</span> sub companies
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <span class="px-2 text-gray-400">...</span>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">8</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
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
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search country name, code..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="country_asc">Country (A-Z)</option>
                                            <option value="country_desc">Country (Z-A)</option>
                                            <option value="code_asc">Code (A-Z)</option>
                                            <option value="code_desc">Code (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create Country
                                </button>
                            </div>

                            <!-- Countries Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Country</th>
                                            <th class="px-4 py-3">Code</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Malaysia</td>
                                            <td class="px-4 py-3">MY</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3 w-32">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Singapore</td>
                                            <td class="px-4 py-3">SG</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Indonesia</td>
                                            <td class="px-4 py-3">ID</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Thailand</td>
                                            <td class="px-4 py-3">TH</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Philippines</td>
                                            <td class="px-4 py-3">PH</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">China</td>
                                            <td class="px-4 py-3">CN</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Active</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Japan</td>
                                            <td class="px-4 py-3">JP</td>
                                            <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-semibold">Inactive</span></td>
                                            <td class="px-4 py-3">
                                                <button class="text-primary hover:text-primary/80 mr-2">
                                                    <span class="material-symbols-outlined text-base">edit</span>
                                                </button>
                                                <button class="text-red-500 hover:text-red-400">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                                <p class="text-sm text-gray-600 dark:text-slate-400">
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">7</span> of <span class="font-medium">195</span> countries
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <span class="px-2 text-gray-400">...</span>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">28</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
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
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search state name..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="state_asc">State (A-Z)</option>
                                            <option value="state_desc">State (Z-A)</option>
                                            <option value="country_asc">Country (A-Z)</option>
                                            <option value="country_desc">Country (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create State
                                </button>
                            </div>

                            <!-- States Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">State</th>
                                            <th class="px-4 py-3">Country</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Selangor</td>
                                            <td class="px-4 py-3">Malaysia</td>
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
                                            <td class="px-4 py-3 font-medium">Kuala Lumpur</td>
                                            <td class="px-4 py-3">Malaysia</td>
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
                                            <td class="px-4 py-3 font-medium">Penang</td>
                                            <td class="px-4 py-3">Malaysia</td>
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
                                            <td class="px-4 py-3 font-medium">Johor</td>
                                            <td class="px-4 py-3">Malaysia</td>
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
                                            <td class="px-4 py-3 font-medium">Central</td>
                                            <td class="px-4 py-3">Singapore</td>
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
                                            <td class="px-4 py-3 font-medium">West Java</td>
                                            <td class="px-4 py-3">Indonesia</td>
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
                                            <td class="px-4 py-3 font-medium">Bangkok</td>
                                            <td class="px-4 py-3">Thailand</td>
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
                                            <td class="px-4 py-3 font-medium">Metro Manila</td>
                                            <td class="px-4 py-3">Philippines</td>
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
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">8</span> of <span class="font-medium">324</span> states
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <span class="px-2 text-gray-400">...</span>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">41</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
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
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search city name..." type="text"/>
                                        <button class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <div class="relative w-full sm:w-48">
                                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm appearance-none focus:ring-primary focus:border-primary text-gray-700 dark:text-gray-300">
                                            <option value="">Sort By</option>
                                            <option value="city_asc">City (A-Z)</option>
                                            <option value="city_desc">City (Z-A)</option>
                                            <option value="state_asc">State (A-Z)</option>
                                            <option value="state_desc">State (Z-A)</option>
                                            <option value="status">Status</option>
                                        </select>
                                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                                <button class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Create City
                                </button>
                            </div>

                            <!-- Cities Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">City</th>
                                            <th class="px-4 py-3">State</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 w-32">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">Petaling Jaya</td>
                                            <td class="px-4 py-3">Selangor</td>
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
                                            <td class="px-4 py-3 font-medium">Shah Alam</td>
                                            <td class="px-4 py-3">Selangor</td>
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
                                            <td class="px-4 py-3 font-medium">Subang Jaya</td>
                                            <td class="px-4 py-3">Selangor</td>
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
                                            <td class="px-4 py-3 font-medium">Ampang</td>
                                            <td class="px-4 py-3">Selangor</td>
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
                                            <td class="px-4 py-3 font-medium">Cheras</td>
                                            <td class="px-4 py-3">Kuala Lumpur</td>
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
                                            <td class="px-4 py-3 font-medium">Georgetown</td>
                                            <td class="px-4 py-3">Penang</td>
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
                                            <td class="px-4 py-3 font-medium">Johor Bahru</td>
                                            <td class="px-4 py-3">Johor</td>
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
                                            <td class="px-4 py-3 font-medium">Iskandar Puteri</td>
                                            <td class="px-4 py-3">Johor</td>
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
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">8</span> of <span class="font-medium">567</span> cities
                                </p>
                                <div class="flex items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button class="px-3 py-2 rounded-lg bg-primary text-white font-medium text-sm min-w-[40px]">1</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">2</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">3</button>
                                    <span class="px-2 text-gray-400">...</span>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors font-medium text-sm min-w-[40px]">71</button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
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
                                    <button class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-300 dark:hover:bg-slate-600 transition-colors text-sm">All Logs</button>
                                    <button class="px-4 py-2 rounded-lg text-gray-600 dark:text-slate-400 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Errors Only</button>
                                    <button class="px-4 py-2 rounded-lg text-gray-600 dark:text-slate-400 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Warnings</button>
                                </div>
                                <button class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base">download</span>
                                    Export Logs
                                </button>
                            </div>
                            <div class="bg-white dark:bg-slate-900 rounded-lg p-4 font-mono text-xs text-gray-700 dark:text-slate-300 max-h-64 overflow-y-auto border border-gray-200 dark:border-slate-700">
                                <div class="mb-2"><span class="text-green-600 dark:text-green-400">[INFO]</span> 2026-01-16 10:30:15 - User login successful: admin@safeg.com</div>
                                <div class="mb-2"><span class="text-blue-600 dark:text-blue-400">[DEBUG]</span> 2026-01-16 10:29:42 - Database connection established</div>
                                <div class="mb-2"><span class="text-green-600 dark:text-green-400">[INFO]</span> 2026-01-16 10:28:33 - Visitor check-in processed: VIS-2345</div>
                                <div class="mb-2"><span class="text-yellow-600 dark:text-yellow-400">[WARN]</span> 2026-01-16 10:27:18 - Session timeout extended for user: john.doe</div>
                                <div class="mb-2"><span class="text-green-600 dark:text-green-400">[INFO]</span> 2026-01-16 10:25:52 - Email notification sent successfully</div>
                                <div class="mb-2"><span class="text-red-600 dark:text-red-400">[ERROR]</span> 2026-01-16 10:24:10 - Failed to upload document: timeout exceeded</div>
                                <div class="mb-2"><span class="text-green-600 dark:text-green-400">[INFO]</span> 2026-01-16 10:22:45 - New invitation created: INV-7892</div>
                                <div><span class="text-blue-600 dark:text-blue-400">[DEBUG]</span> 2026-01-16 10:20:33 - Cache cleared successfully</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleSection(section) {
            const content = document.getElementById(`${section}-content`);
            const icon = document.getElementById(`${section}-icon`);
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</body>
</html>
