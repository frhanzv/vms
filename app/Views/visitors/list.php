<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
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
    <!-- Blacklist dropdown function-->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('staffs') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">badge</span>
                    <p class="text-sm font-medium">Staff Pass List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary group transition-colors" href="<?= base_url('visitors') ?>">
                    <span class="material-symbols-outlined text-[22px] font-medium fill-1 group-hover:scale-110 transition-transform">group</span>
                    <p class="text-sm font-semibold">Visitors List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('logbook') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">menu_book</span>
                    <p class="text-sm font-medium">Visitor Logbook</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('workflow') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">account_tree</span>
                    <p class="text-sm font-medium">Visitor Workflow</p>
                </a>
                <div x-data="{ openBlacklist: <?= str_contains($current, 'blacklist') ? 'true' : 'false' ?>, openIndividual: <?= str_contains($current, 'blacklist') ? 'true' : 'false' ?> }">
    
                    <!-- Blacklist Parent Button -->
                    <button 
                        type="button"
                        @click="openBlacklist = !openBlacklist"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg <?= str_contains($current, 'blacklist') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary' ?> transition-colors group"
                    >
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">person_cancel</span>
                            <p class="text-sm font-medium">Blacklist</p>
                        </div>
                        <span 
                            class="material-symbols-outlined text-[18px] transition-transform duration-200"
                            :class="openBlacklist ? 'rotate-180' : ''"
                        >expand_more</span>
                    </button>

                    <!-- Blacklist Dropdown -->
                    <div 
                        x-show="openBlacklist" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="ml-4 mt-1 flex flex-col gap-1"
                    >
                        <!-- Individual Submenu Button -->
                        <button
                            type="button"
                            @click="openIndividual = !openIndividual"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-lg <?= str_contains($current, 'blacklist') ? 'text-primary bg-primary/5' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary' ?> transition-colors group"
                        >
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[18px] group-hover:scale-110 transition-transform">person</span>
                                <p class="text-sm font-medium">Individual</p>
                            </div>
                            <span 
                                class="material-symbols-outlined text-[16px] transition-transform duration-200"
                                :class="openIndividual ? 'rotate-180' : ''"
                            >expand_more</span>
                        </button>

                        <!-- Request List & Closed List Links -->
                        <div 
                            x-show="openIndividual"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="ml-4 mt-1 flex flex-col gap-1"
                        >   
                            <!-- Request List -->
                            <a href="<?= base_url('blacklist/blacklistrequest') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                            <?= $current == 'blacklist/blacklistrequest' 
                                ? 'bg-primary/10 text-primary font-medium' 
                                : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $current == 'blacklist/blacklistrequest' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                                Request List
                            </a>

                            <!-- Closed List -->
                            <a href="<?= base_url('blacklist/closedlist') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                            <?= $current == 'blacklist/closedlist' 
                                ? 'bg-primary/10 text-primary font-medium' 
                                : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $current == 'blacklist/closedlist' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                                Closed List
                            </a>
                        </div>
                    </div>
                </div>
                    <!-- Report Dropdown -->
                    <div x-data="{ openReport: <?= str_contains($current, 'report') ? 'true' : 'false' ?> }">
                        <button type="button" @click="openReport = !openReport"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg <?= str_contains($current, 'report') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary' ?> transition-colors group">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">description</span>
                                <p class="text-sm font-medium">REPORT</p>
                            </div>
                            <span class="material-symbols-outlined text-[18px] transition-transform duration-200" :class="openReport ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        <div x-show="openReport"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="ml-4 mt-1 flex flex-col gap-1">
                            <a href="<?= base_url('report/access') ?>"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'report/access' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $current == 'report/access' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                                ACCESS REPORT
                            </a>
                            <a href="<?= base_url('report/visitor') ?>"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'report/visitor' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $current == 'report/visitor' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                                VISITOR REPORT
                            </a>
                            <a href="<?= base_url('report/chronology') ?>"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'report/chronology') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'report/chronology') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                                VISITOR CHRONOLOGY
                            </a>
                            <a href="<?= base_url('report/bydoor') ?>"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'report/bydoor') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'report/bydoor') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                                VISITOR INFO BY DOOR
                            </a>
                        </div>
                    </div>

                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('config') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">tune</span>
                    <p class="text-sm font-medium">Config</p>
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
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">
                    Visitor Pass List
                </h1>
                <div class="flex gap-2">
                    <button class="bg-secondary hover:bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">file_download</span>
                        Export
                    </button>
                    <a href="<?= base_url('visitor-pass-request') ?>" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">add</span>
                        Request
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-indigo-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Visitors</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['total']) ?></p>
                        <p class="text-[10px] text-gray-400 mt-1">Total recorded entries</p>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-full text-indigo-600 dark:text-indigo-400">
                        <span class="material-symbols-outlined text-2xl">groups</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-green-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Checked In</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['checkedIn']) ?></p>
                        <p class="text-[10px] text-green-600 mt-1 font-medium">Currently on site</p>
                    </div>
                    <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-full text-green-600 dark:text-green-400">
                        <span class="material-symbols-outlined text-2xl">how_to_reg</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-orange-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pending Approval</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['pending']) ?></p>
                        <p class="text-[10px] text-orange-500 mt-1 font-medium">Action required</p>
                    </div>
                    <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-full text-orange-600 dark:text-orange-400">
                        <span class="material-symbols-outlined text-2xl">pending_actions</span>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col gap-4 mb-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                    <div class="lg:col-span-5 flex shadow-sm">
                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-xs focus:ring-primary focus:border-primary outline-none" placeholder="IC / PASSPORT / VISITOR PASS NO / FULL NAME / VEHICLE REGISTRATION NO" type="text"/>
                        <button class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-r flex items-center justify-center transition-colors">
                            <span class="material-icons text-white">search</span>
                        </button>
                    </div>
                    <div class="lg:col-span-4 flex gap-2">
                        <button class="bg-success hover:bg-emerald-600 text-white px-4 py-2.5 rounded text-xs font-semibold uppercase shadow transition-colors flex-1 text-center whitespace-nowrap">
                            Read MyKad
                        </button>
                        <button class="bg-success hover:bg-emerald-600 text-white px-4 py-2.5 rounded text-xs font-semibold uppercase shadow transition-colors flex-1 text-center whitespace-nowrap">
                            Return Card
                        </button>
                    </div>
                    <div class="lg:col-span-3">
                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs focus:ring-primary focus:border-primary uppercase placeholder-gray-500 dark:placeholder-gray-400" placeholder="DATE OF VISIT TO" type="text"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="relative">
                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                            <option>VISIT TYPE</option>
                            <option>Walk-In</option>
                            <option>Invitation</option>
                        </select>
                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                    </div>
                    <div class="relative">
                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                            <option>APP DATE</option>
                        </select>
                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                    </div>
                    <div>
                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs focus:ring-primary focus:border-primary uppercase text-gray-500 dark:text-gray-300" placeholder="DATE FROM" type="text"/>
                    </div>
                    <div>
                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs focus:ring-primary focus:border-primary uppercase text-gray-500 dark:text-gray-300" placeholder="DATE TO" type="text"/>
                    </div>
                    <div class="relative">
                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                            <option>DATE TIME DESC</option>
                        </select>
                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                    </div>
                    <div class="relative">
                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                            <option>KSB</option>
                            <option>Other Locations</option>
                        </select>
                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-6">
                <table class="w-full min-w-max text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-bold uppercase tracking-wide">
                            <th class="p-4 border-b dark:border-gray-600">No</th>
                            <th class="p-4 border-b dark:border-gray-600">Date</th>
                            <th class="p-4 border-b dark:border-gray-600">Full Name</th>
                            <th class="p-4 border-b dark:border-gray-600">IC / Passport No</th>
                            <th class="p-4 border-b dark:border-gray-600">Contact No</th>
                            <th class="p-4 border-b dark:border-gray-600">Vehicle Registration Number</th>
                            <th class="p-4 border-b dark:border-gray-600">Location</th>
                            <th class="p-4 border-b dark:border-gray-600">Type</th>
                            <th class="p-4 border-b dark:border-gray-600">Card Status</th>
                            <th class="p-4 border-b dark:border-gray-600">Visitor Pass No</th>
                            <th class="p-4 border-b dark:border-gray-600">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                        <?php if (empty($visitors)): ?>
                        <tr>
                            <td colspan="11" class="p-8 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-4">
                                        <span class="material-symbols-outlined text-4xl text-gray-400 dark:text-gray-500">folder_off</span>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-gray-700 dark:text-gray-300">No Data Available</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">There are no approved visitors at the moment.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($visitors as $visitor): ?>
                        <tr onclick='openDetailModal(<?= json_encode($visitor) ?>)' class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border-b border-gray-100 dark:border-gray-700 cursor-pointer">
                            <td class="p-4"><?= $visitor['no'] ?></td>
                            <td class="p-4"><?= esc($visitor['date']) ?></td>
                            <td class="p-4 font-semibold text-gray-800 dark:text-white"><?= esc($visitor['full_name']) ?></td>
                            <td class="p-4"><?= esc($visitor['ic_passport']) ?></td>
                            <td class="p-4"><?= esc($visitor['contact']) ?></td>
                            <td class="p-4 <?= empty($visitor['vehicle_reg']) ? 'text-gray-400' : '' ?>">
                                <?= empty($visitor['vehicle_reg']) ? 'NULL' : esc($visitor['vehicle_reg']) ?>
                            </td>
                            <td class="p-4"><?= esc($visitor['location']) ?></td>
                            <td class="p-4"><?= esc($visitor['type']) ?></td>
                            <td class="p-4">
                                <?php if ($visitor['card_status'] === 'Active'): ?>
                                <span class="bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 px-2 py-1 rounded-full text-[10px] uppercase font-bold">Active</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4"><?= esc($visitor['pass_no'] ?? '') ?></td>
                            <td class="p-4"><?= esc($visitor['reason']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-medium text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-1">
                    <button class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors disabled:opacity-50">«</button>
                    <button class="w-8 h-8 flex items-center justify-center bg-primary text-white rounded shadow-sm">1</button>
                    <button class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">2</button>
                    <button class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">3</button>
                    <span class="w-8 h-8 flex items-center justify-center">...</span>
                    <button class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">1392</button>
                    <button class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">»</button>
                </div>
                <div class="relative">
                    <select class="appearance-none bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 py-1.5 pl-3 pr-8 rounded focus:outline-none focus:ring-1 focus:ring-primary text-xs font-medium cursor-pointer shadow-sm">
                        <option>10 ITEMS PER PAGE</option>
                        <option>25 ITEMS PER PAGE</option>
                        <option>50 ITEMS PER PAGE</option>
                    </select>
                    <span class="absolute right-2 top-1.5 pointer-events-none material-icons text-sm text-gray-500">expand_more</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-gradient-to-r from-primary to-blue-600 text-white px-6 py-4 flex justify-between items-center shadow-md z-10">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                        <span class="material-symbols-outlined text-2xl">person</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Visitor Details</h2>
                        <p class="text-sm text-blue-100">Complete visitor information</p>
                    </div>
                </div>
                <button onclick="closeDetailModal()" class="hover:bg-white/20 p-2 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto px-6 py-6">
                <!-- Status Badge -->
                <div id="statusBadge" class="mb-6"></div>

                <!-- Visitor Information -->
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">badge</span>
                        Visitor Information
                    </h3>
                    <div class="bg-gray-50 dark:bg-slate-800 rounded-lg p-4 grid grid-cols-1 md:grid-cols-2 gap-4 border border-gray-200 dark:border-gray-700">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Full Name</p>
                            <p id="detailFullName" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">IC / Passport No</p>
                            <p id="detailIcPassport" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Contact Number</p>
                            <p id="detailContact" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Vehicle Registration</p>
                            <p id="detailVehicle" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                    </div>
                </div>

                <!-- Visit Details -->
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">event</span>
                        Visit Details
                    </h3>
                    <div class="bg-gray-50 dark:bg-slate-800 rounded-lg p-4 grid grid-cols-1 md:grid-cols-2 gap-4 border border-gray-200 dark:border-gray-700">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Date of Visit</p>
                            <p id="detailDate" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Visit Type</p>
                            <p id="detailType" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Location</p>
                            <p id="detailLocation" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Reason</p>
                            <p id="detailReason" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                    </div>
                </div>

                <!-- Pass Information -->
                <div>
                    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">credit_card</span>
                        Pass Information
                    </h3>
                    <div class="bg-gray-50 dark:bg-slate-800 rounded-lg p-4 grid grid-cols-1 md:grid-cols-2 gap-4 border border-gray-200 dark:border-gray-700">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Visitor Pass No</p>
                            <p id="detailPassNo" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Card Status</p>
                            <p id="detailCardStatus" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 bg-gray-50 dark:bg-slate-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex gap-3 justify-between items-center">
                <button onclick="closeDetailModal()" class="px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors duration-200">
                    Close
                </button>
                <div class="flex gap-3">
                    <button onclick="openCardBindingModal()" class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">badge</span>
                        i Card Details
                    </button>
                    <button class="px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">qr_code</span>
                        QR Code
                    </button>
                    <button class="px-4 py-2.5 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">print</span>
                        Print Slip
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Binding Modal -->
    <div id="cardBindingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl max-w-md w-full">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-white text-3xl">badge</span>
                        <h3 class="text-xl font-bold text-white">Bind Visitor Card</h3>
                    </div>
                    <button onclick="closeCardBindingModal()" class="text-white hover:bg-white/20 rounded-lg p-1 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Select an available card or enter a new EPC number to bind to this visitor.
                    </p>
                    
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Card EPC Number
                    </label>
                    <select id="cardEpcSelect" class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Select Available Card --</option>
                        <?php foreach ($availableCards as $card): ?>
                        <option value="<?= $card['id'] ?>"><?= esc($card['card_id']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Or Enter New EPC Number
                        </label>
                        <input type="text" id="newCardEpc" placeholder="Enter 24-character EPC (e.g., DD123456789012345678901)" 
                               class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               maxlength="24">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            EPC format: 24 hex characters (DD/E2/30 prefixes)
                        </p>
                    </div>
                </div>

                <div id="cardBindingError" class="hidden mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <p class="text-sm text-red-600 dark:text-red-400"></p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 dark:bg-slate-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 rounded-b-xl flex gap-3 justify-end">
                <button onclick="closeCardBindingModal()" class="px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                <button onclick="bindCardToVisitor()" class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">link</span>
                    Bind Card
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentVisitorId = null;
        let currentInvitationVisitorId = null;

        function openDetailModal(visitor) {
            const modal = document.getElementById('detailModal');
            
            // Store visitor IDs for card binding
            currentVisitorId = visitor.id;
            currentInvitationVisitorId = visitor.id;
            
            // Populate visitor information
            document.getElementById('detailFullName').textContent = visitor.full_name || 'N/A';
            document.getElementById('detailIcPassport').textContent = visitor.ic_passport || 'N/A';
            document.getElementById('detailContact').textContent = visitor.contact || 'N/A';
            document.getElementById('detailVehicle').textContent = visitor.vehicle_reg || 'N/A';
            
            // Populate visit details
            document.getElementById('detailDate').textContent = visitor.date || 'N/A';
            document.getElementById('detailType').textContent = visitor.type || 'N/A';
            document.getElementById('detailLocation').textContent = visitor.location || 'N/A';
            document.getElementById('detailReason').textContent = visitor.reason || 'N/A';
            
            // Populate pass information
            document.getElementById('detailPassNo').textContent = visitor.pass_no || 'N/A';
            
            // Card status badge
            const statusBadge = document.getElementById('statusBadge');
            if (visitor.card_status === 'Active') {
                statusBadge.innerHTML = '<span class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg text-sm font-semibold border border-green-200 dark:border-green-800"><span class="material-symbols-outlined text-base">check_circle</span>Card Active</span>';
            } else {
                statusBadge.innerHTML = '<span class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-lg text-sm font-semibold border border-gray-200 dark:border-gray-700"><span class="material-symbols-outlined text-base">info</span>No Card Issued</span>';
            }
            
            document.getElementById('detailCardStatus').textContent = visitor.card_status || 'N/A';
            
            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            const modal = document.getElementById('detailModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        function openCardBindingModal() {
            document.getElementById('cardBindingModal').classList.remove('hidden');
            document.getElementById('cardBindingModal').classList.add('flex');
            document.getElementById('cardEpcSelect').value = '';
            document.getElementById('newCardEpc').value = '';
            hideCardBindingError();
        }

        function closeCardBindingModal() {
            document.getElementById('cardBindingModal').classList.add('hidden');
            document.getElementById('cardBindingModal').classList.remove('flex');
        }

        function showCardBindingError(message) {
            const errorDiv = document.getElementById('cardBindingError');
            errorDiv.querySelector('p').textContent = message;
            errorDiv.classList.remove('hidden');
        }

        function hideCardBindingError() {
            document.getElementById('cardBindingError').classList.add('hidden');
        }

        function bindCardToVisitor() {
            const selectedCardId = document.getElementById('cardEpcSelect').value;
            const newCardEpc = document.getElementById('newCardEpc').value.trim();
            
            if (!selectedCardId && !newCardEpc) {
                showCardBindingError('Please select a card or enter a new EPC number');
                return;
            }

            if (newCardEpc && newCardEpc.length !== 24) {
                showCardBindingError('EPC number must be exactly 24 characters');
                return;
            }

            // Validate EPC format (hex characters)
            if (newCardEpc && !/^[0-9A-Fa-f]{24}$/.test(newCardEpc)) {
                showCardBindingError('EPC must contain only hexadecimal characters (0-9, A-F)');
                return;
            }

            const data = {
                invitation_visitor_id: currentInvitationVisitorId,
                card_id: selectedCardId || null,
                new_card_epc: newCardEpc || null
            };

            // Show loading state
            const bindButton = event.target.closest('button');
            const originalText = bindButton.innerHTML;
            bindButton.disabled = true;
            bindButton.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">refresh</span> Binding...';

            fetch('<?= base_url('visitors/bindCard') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert(result.message);
                    closeCardBindingModal();
                    closeDetailModal();
                    location.reload(); // Refresh the page to show updated data
                } else {
                    showCardBindingError(result.message || 'Failed to bind card');
                    bindButton.disabled = false;
                    bindButton.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCardBindingError('An error occurred while binding the card');
                bindButton.disabled = false;
                bindButton.innerHTML = originalText;
            });
        }

        // Close modal on backdrop click
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });

        document.getElementById('cardBindingModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCardBindingModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const cardModal = document.getElementById('cardBindingModal');
                const detailModal = document.getElementById('detailModal');
                
                if (!cardModal.classList.contains('hidden')) {
                    closeCardBindingModal();
                } else if (!detailModal.classList.contains('hidden')) {
                    closeDetailModal();
                }
            }
        });

        // Clear new EPC input when selecting from dropdown
        document.getElementById('cardEpcSelect').addEventListener('change', function() {
            if (this.value) {
                document.getElementById('newCardEpc').value = '';
            }
        });

        // Clear dropdown when typing in new EPC input
        document.getElementById('newCardEpc').addEventListener('input', function() {
            if (this.value) {
                document.getElementById('cardEpcSelect').value = '';
            }
        });
    </script>
</body>
</html>