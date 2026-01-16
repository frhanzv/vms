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
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">System Name</label>
                                    <input value="SafeG Visitor Management System" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white" type="text"/>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">System Email</label>
                                    <input value="admin@safeg.com" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white" type="email"/>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Contact Number</label>
                                    <input value="+60 12-345 6789" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white" type="tel"/>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Time Zone</label>
                                    <select class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white">
                                        <option value="Asia/Kuala_Lumpur" selected>Asia/Kuala Lumpur (GMT+8)</option>
                                        <option value="Asia/Singapore">Asia/Singapore (GMT+8)</option>
                                        <option value="Asia/Bangkok">Asia/Bangkok (GMT+7)</option>
                                    </select>
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
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Default Visit Duration (Hours)</label>
                                <input value="8" type="number" min="1" max="24" class="w-full md:w-48 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-sm focus:border-primary focus:ring-primary text-gray-800 dark:text-white"/>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('location')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">location_on</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Location Management</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage company locations and facilities</p>
                            </div>
                        </div>
                        <span id="location-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="location-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <div class="flex justify-end mb-4">
                                <button class="px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Add Location
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Location Code</th>
                                            <th class="px-4 py-3">Location Name</th>
                                            <th class="px-4 py-3">Type</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 dark:text-slate-300">
                                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                                            <td class="px-4 py-3 font-medium">LOC-001</td>
                                            <td class="px-4 py-3">PHASE 1</td>
                                            <td class="px-4 py-3">Building</td>
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
                                            <td class="px-4 py-3 font-medium">LOC-002</td>
                                            <td class="px-4 py-3">WORKSHOP PHASE 2</td>
                                            <td class="px-4 py-3">Workshop</td>
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
                                            <td class="px-4 py-3 font-medium">LOC-003</td>
                                            <td class="px-4 py-3">ADMIN B</td>
                                            <td class="px-4 py-3">Office</td>
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
