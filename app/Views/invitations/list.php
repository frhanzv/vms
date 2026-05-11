<!DOCTYPE html>
<?php
$current = service('uri')->getPath();
$isDashboard = ($current === '' || $current === 'dashboard');
$isStaff = str_contains($current, 'staffs') || str_contains($current, 'staff-pass-request');
$isWorkflow = str_contains($current, 'workflow');
$isConfig = str_contains($current, 'config');
$isSettings = str_contains($current, 'settings');
?>
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

    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-20 right-4 z-[9999] space-y-2 max-w-sm"></div>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden transition-colors duration-200">
    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col p-4 hidden md:flex h-full overflow-hidden">
        <div class="flex flex-col gap-8 flex-1 min-h-0">
            <div class="flex items-center gap-3 px-2">
                <div class="bg-center bg-no-repeat bg-cover rounded-lg size-10 bg-primary/10 flex items-center justify-center text-primary" data-alt="SafeG Logo abstract blue square">
                    <span class="material-symbols-outlined text-3xl">shield_person</span>
                </div>
                <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">SafeG</h1>
            </div>
            <nav class="flex flex-col gap-2 overflow-y-auto pr-1 custom-scrollbar">
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isDashboard ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('dashboard') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">dashboard</span>
                    <p class="text-sm font-medium">Dashboard</p>
                </a>
                <div x-data="{ openVisitorPass: <?= (str_contains($current, 'invitations') || str_contains($current, 'requests') || str_contains($current, 'visitors')) ? 'true' : 'false' ?> }">
                    <button type="button" @click="openVisitorPass = !openVisitorPass"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg <?= (str_contains($current, 'invitations') || str_contains($current, 'requests') || str_contains($current, 'visitors')) ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary' ?> transition-colors group">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">badge</span>
                            <p class="text-sm font-medium">Visitor Pass List</p>
                        </div>
                        <span class="material-symbols-outlined text-[18px] transition-transform duration-200" :class="openVisitorPass ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="openVisitorPass"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="ml-4 mt-1 flex flex-col gap-1">
                        <a href="<?= base_url('invitations') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'invitations') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'invitations') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            Invitations
                        </a>
                        <a href="<?= base_url('requests') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'requests') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'requests') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            Request List
                        </a>
                        <a href="<?= base_url('visitors') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'visitors') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'visitors') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            Visitors List
                        </a>
                    </div>
                </div>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isStaff ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('staffs') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">badge</span>
                    <p class="text-sm font-medium">Staff Pass List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isWorkflow ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('workflow') ?>">
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
                            <p class="text-sm font-medium">Report</p>
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
                            Access Report
                            </a>
                            <a href="<?= base_url('report/visitor') ?>"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'report/visitor' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $current == 'report/visitor' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            Visitor Report
                            </a>
                            <a href="<?= base_url('report/chronology') ?>"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'report/chronology') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'report/chronology') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            Visitor Chronology
                            </a>
                            <a href="<?= base_url('report/bydoor') ?>"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'report/bydoor') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'report/bydoor') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            Visitor Info By Door
                            </a>
                        </div>
                    </div>

                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isConfig ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('config') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">tune</span>
                    <p class="text-sm font-medium">Config</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isSettings ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('settings') ?>">
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
                    Invitation List
                </h1>
                <div class="flex gap-2">
                    <?php
                    $exportParams = array_filter([
                        'search'    => $filters['search'],
                        'status'    => $filters['status'],
                        'reason'    => $filters['reason'],
                        'location'  => $filters['location'],
                        'date_from' => $filters['date_from'],
                        'date_to'   => $filters['date_to'],
                        'sort'      => ($filters['sort'] !== 'date_desc') ? $filters['sort'] : '',
                    ]);
                    $exportUrl = base_url('invitations/export') . ($exportParams ? '?' . http_build_query($exportParams) : '');
                    ?>
                    <a id="exportBtn" href="<?= $exportUrl ?>" class="bg-secondary hover:bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">file_download</span>
                        Export
                    </a>
                    <a href="<?= base_url('invitations/create') ?>" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">add</span>
                        New Invitation
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-indigo-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Invitations</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['total']) ?></p>
                        <p class="text-[10px] text-gray-400 mt-1">All invitation records</p>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-full text-indigo-600 dark:text-indigo-400">
                        <span class="material-symbols-outlined text-2xl">mail</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-orange-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pending</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['pending']) ?></p>
                        <p class="text-[10px] text-orange-500 mt-1 font-medium">Awaiting approval</p>
                    </div>
                    <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-full text-orange-600 dark:text-orange-400">
                        <span class="material-symbols-outlined text-2xl">pending_actions</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-l-blue-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Submitted</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['submitted']) ?></p>
                        <p class="text-[10px] text-blue-500 mt-1 font-medium">Form completed</p>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-full text-blue-600 dark:text-blue-400">
                        <span class="material-symbols-outlined text-2xl">task_alt</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-green-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Approved</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['approved']) ?></p>
                        <p class="text-[10px] text-green-600 mt-1 font-medium">Ready for visit</p>
                    </div>
                    <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-full text-green-600 dark:text-green-400">
                        <span class="material-symbols-outlined text-2xl">check_circle</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-red-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Rejected</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['rejected']) ?></p>
                        <p class="text-[10px] text-red-500 mt-1 font-medium">Declined requests</p>
                    </div>
                    <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-full text-red-600 dark:text-red-400">
                        <span class="material-symbols-outlined text-2xl">cancel</span>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <form id="filterForm" method="GET" action="<?= base_url('invitations') ?>">
                <input type="hidden" name="page" value="<?= (int) ($pagination['current_page'] ?? 1) ?>">
                <input type="hidden" name="per_page" value="<?= (int) ($filters['per_page'] ?? 10) ?>">
                <div class="flex flex-col gap-4 mb-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                        <div class="lg:col-span-6 flex shadow-sm">
                            <input
                                id="searchInput"
                                name="search"
                                value="<?= esc($filters['search']) ?>"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-xs focus:ring-primary focus:border-primary outline-none"
                                placeholder="SEARCH ALL FIELDS"
                                type="text"
                            />
                            <button type="button" tabindex="-1" aria-hidden="true" class="bg-primary text-white px-4 py-2 rounded-r flex items-center justify-center cursor-default select-none">
                                <span class="material-icons text-white">search</span>
                            </button>
                        </div>
                        <div class="lg:col-span-3">
                            <input
                                name="date_from"
                                value="<?= esc($filters['date_from']) ?>"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs focus:ring-primary focus:border-primary"
                                type="date"
                            />
                        </div>
                        <div class="lg:col-span-3">
                            <input
                                name="date_to"
                                value="<?= esc($filters['date_to']) ?>"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs focus:ring-primary focus:border-primary"
                                type="date"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        <div class="relative">
                            <select name="status" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                                <option value="">STATUS</option>
                                <?php foreach (['Pending', 'Submitted', 'Approved', 'Rejected'] as $s): ?>
                                <option value="<?= esc($s) ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= esc($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                        </div>
                        <div class="relative">
                            <select name="reason" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                                <option value="">REASON</option>
                                <?php foreach ($reasonList as $r): ?>
                                <option value="<?= esc($r) ?>" <?= $filters['reason'] === $r ? 'selected' : '' ?>><?= esc($r) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                        </div>
                        <div class="relative">
                            <select name="location" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                                <option value="">LOCATION</option>
                                <?php foreach ($locationList as $l): ?>
                                <option value="<?= esc($l) ?>" <?= $filters['location'] === $l ? 'selected' : '' ?>><?= esc($l) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                        </div>
                        <div class="relative">
                            <select name="sort" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                                <option value="date_desc" <?= $filters['sort'] === 'date_desc' ? 'selected' : '' ?>>DATE DESC</option>
                                <option value="date_asc"  <?= $filters['sort'] === 'date_asc'  ? 'selected' : '' ?>>DATE ASC</option>
                                <option value="name_asc"  <?= $filters['sort'] === 'name_asc'  ? 'selected' : '' ?>>NAME A-Z</option>
                                <option value="name_desc" <?= $filters['sort'] === 'name_desc' ? 'selected' : '' ?>>NAME Z-A</option>
                            </select>
                            <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                        </div>
                        <a id="clearFiltersBtn" href="<?= base_url('invitations') ?>" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2.5 rounded text-xs font-semibold uppercase shadow transition-colors flex items-center justify-center">
                            <span class="material-icons text-sm mr-1 align-middle">filter_alt_off</span>
                            Clear Filters
                        </a>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-6">
                <table id="invitationTable" class="w-full min-w-max text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-bold uppercase tracking-wide">
                            <th class="p-4 border-b dark:border-gray-600">No</th>
                            <th class="p-4 border-b dark:border-gray-600">Date</th>
                            <th class="p-4 border-b dark:border-gray-600">Full Name</th>
                            <th class="p-4 border-b dark:border-gray-600">IC / Passport No</th>
                            <th class="p-4 border-b dark:border-gray-600">Contact No</th>
                            <th class="p-4 border-b dark:border-gray-600">Company</th>
                            <th class="p-4 border-b dark:border-gray-600">Vehicle Registration</th>
                            <th class="p-4 border-b dark:border-gray-600">Location</th>
                            <th class="p-4 border-b dark:border-gray-600">Invited By</th>
                            <th class="p-4 border-b dark:border-gray-600">Status</th>
                            <th class="p-4 border-b dark:border-gray-600">Reason</th>
                        </tr>
                    </thead>
                    <tbody id="invitationsTableBody" class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                        <?php if (empty($invitations)): ?>
                        <tr>
                            <td colspan="11" class="p-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-5xl">inbox</span>
                                    <div>
                                        <h3 class="text-gray-500 dark:text-gray-400 font-semibold">No Data Available</h3>
                                        <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">No invitation records found. Create your first invitation to get started.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($invitations as $invitation): ?>
                        <tr onclick='openDetailModal(<?= json_encode($invitation) ?>)' class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border-b border-gray-100 dark:border-gray-700 cursor-pointer">
                            <td class="p-4"><?= $invitation['no'] ?></td>
                            <td class="p-4"><?= esc($invitation['date']) ?></td>
                            <td class="p-4 font-semibold text-gray-800 dark:text-white"><?= esc($invitation['full_name']) ?></td>
                            <td class="p-4 <?= empty($invitation['ic_passport']) ? 'text-gray-400' : '' ?>">
                                <?= empty($invitation['ic_passport']) ? 'NULL' : esc($invitation['ic_passport']) ?>
                            </td>
                            <td class="p-4"><?= esc($invitation['contact']) ?></td>
                            <td class="p-4"><?= esc($invitation['company']) ?></td>
                            <td class="p-4 <?= empty($invitation['vehicle_reg']) ? 'text-gray-400' : '' ?>">
                                <?= empty($invitation['vehicle_reg']) ? 'NULL' : esc($invitation['vehicle_reg']) ?>
                            </td>
                            <td class="p-4"><?= esc($invitation['location']) ?></td>
                            <td class="p-4"><?= esc($invitation['invited_by']) ?></td>
                            <td class="p-4">
                                <?php if ($invitation['status'] === 'Approved'): ?>
                                <span class="bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 px-2 py-1 rounded-full text-[10px] uppercase font-bold">Approved</span>
                                <?php elseif ($invitation['status'] === 'Pending'): ?>
                                <span class="bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300 px-2 py-1 rounded-full text-[10px] uppercase font-bold">Pending</span>
                                <?php elseif ($invitation['status'] === 'Submitted'): ?>
                                <span class="bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 px-2 py-1 rounded-full text-[10px] uppercase font-bold">Submitted</span>
                                <?php elseif ($invitation['status'] === 'Rejected'): ?>
                                <span class="bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 px-2 py-1 rounded-full text-[10px] uppercase font-bold">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4"><?= esc($invitation['reason']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php
            $curPage  = $pagination['current_page'];
            $lastPage = $pagination['last_page'];
            $pgTotal  = $pagination['total'];
            $pgPer    = $pagination['per_page'];

            $buildUrl = function (int $pg, int $pp = 0) use ($filters, $pgPer): string {
                $pp = $pp ?: $pgPer;
                $params = [];
                if ($filters['search']    !== '') $params['search']    = $filters['search'];
                if ($filters['status']    !== '') $params['status']    = $filters['status'];
                if ($filters['reason']    !== '') $params['reason']    = $filters['reason'];
                if ($filters['location']  !== '') $params['location']  = $filters['location'];
                if ($filters['date_from'] !== '') $params['date_from'] = $filters['date_from'];
                if ($filters['date_to']   !== '') $params['date_to']   = $filters['date_to'];
                if ($filters['sort'] !== 'date_desc') $params['sort']  = $filters['sort'];
                if ($pp !== 10)                   $params['per_page']  = $pp;
                if ($pg > 1)                      $params['page']      = $pg;
                $qs = http_build_query($params);
                return base_url('invitations') . ($qs ? '?' . $qs : '');
            };

            // Build page-number list with ellipsis
            $pgNumbers = [];
            if ($lastPage <= 7) {
                for ($i = 1; $i <= $lastPage; $i++) $pgNumbers[] = $i;
            } else {
                $pgNumbers[] = 1;
                if ($curPage > 3) $pgNumbers[] = '...';
                for ($i = max(2, $curPage - 1); $i <= min($lastPage - 1, $curPage + 1); $i++) $pgNumbers[] = $i;
                if ($curPage < $lastPage - 2) $pgNumbers[] = '...';
                $pgNumbers[] = $lastPage;
            }

            $firstItem = ($pgTotal === 0) ? 0 : ($curPage - 1) * $pgPer + 1;
            $lastItem  = min($curPage * $pgPer, $pgTotal);
            ?>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-medium text-gray-500 dark:text-gray-400">
                <div id="paginationContainer" class="flex items-center gap-1">
                    <?php if ($curPage > 1): ?>
                    <a href="<?= $buildUrl($curPage - 1) ?>" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">«</a>
                    <?php else: ?>
                    <span class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded opacity-40 cursor-not-allowed">«</span>
                    <?php endif; ?>

                    <?php foreach ($pgNumbers as $pn): ?>
                        <?php if ($pn === '...'): ?>
                        <span class="w-8 h-8 flex items-center justify-center">...</span>
                        <?php elseif ($pn === $curPage): ?>
                        <span class="w-8 h-8 flex items-center justify-center bg-primary text-white rounded shadow-sm"><?= $pn ?></span>
                        <?php else: ?>
                        <a href="<?= $buildUrl((int) $pn) ?>" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"><?= $pn ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if ($curPage < $lastPage): ?>
                    <a href="<?= $buildUrl($curPage + 1) ?>" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">»</a>
                    <?php else: ?>
                    <span class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded opacity-40 cursor-not-allowed">»</span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-3">
                    <span id="paginationSummary" class="text-gray-400">Showing <?= number_format($firstItem) ?>–<?= number_format($lastItem) ?> of <?= number_format($pgTotal) ?></span>
                    <div class="relative">
                        <select id="perPageSelect" class="appearance-none bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 py-1.5 pl-3 pr-8 rounded focus:outline-none focus:ring-1 focus:ring-primary text-xs font-medium cursor-pointer shadow-sm">
                            <?php foreach ([10, 25, 50] as $pp): ?>
                            <option value="<?= $pp ?>" <?= $pgPer === $pp ? 'selected' : '' ?>><?= $pp ?> ITEMS PER PAGE</option>
                            <?php endforeach; ?>
                        </select>
                        <span class="absolute right-2 top-1.5 pointer-events-none material-icons text-sm text-gray-500">expand_more</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Detail Modal -->
    <div id="detailModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between z-10">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">description</span>
                    Invitation Details
                </h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-6 space-y-6">
                <!-- Status Badge -->
                <div class="flex items-center justify-center">
                    <span id="modalStatus" class="px-4 py-2 rounded-full text-sm font-bold"></span>
                </div>

                <!-- Link expiry + visitor count (preview summary) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-100 dark:border-gray-700">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Link expiry</p>
                        <p id="modalLinkExpiry" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Number of visitors</p>
                        <p id="modalVisitorCount" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                    </div>
                </div>

                <!-- Visitor Information -->
                <div>
                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">person</span>
                        Visitor Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Full Name</p>
                            <p id="modalFullName" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">IC / Passport No</p>
                            <p id="modalIcPassport" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Contact No</p>
                            <p id="modalContact" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Company</p>
                            <p id="modalCompany" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Email</p>
                            <p id="modalVisitorEmail" class="text-sm font-semibold text-gray-900 dark:text-white break-all"></p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Registration link</p>
                            <a id="modalRegistrationLink" href="#" target="_blank" rel="noopener noreferrer" class="text-sm font-medium text-primary hover:underline break-all block"></a>
                        </div>
                    </div>
                </div>

                <!-- Visit Details -->
                <div>
                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">event</span>
                        Visit Details
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Visit date (from)</p>
                            <p id="modalVisitFrom" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Visit date (to)</p>
                            <p id="modalVisitTo" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Location</p>
                            <p id="modalLocation" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Reason for Visit</p>
                            <p id="modalReason" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Vehicle Registration</p>
                            <p id="modalVehicle" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                    </div>
                </div>

                <!-- Host Information -->
                <div>
                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">badge</span>
                        Host Information
                    </h4>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Invited By</p>
                        <p id="modalInvitedBy" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 bg-gray-50 dark:bg-slate-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex gap-3 justify-end">
                <button onclick="closeDetailModal()" class="px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors duration-200">
                    Close
                </button>
                <button id="sendInvitationBtn" onclick="sendInvitation()" class="px-4 py-2.5 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors duration-200 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="material-symbols-outlined text-lg">send</span>
                    Send
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentInvitationId = null;
        const invitationDataUrl = "<?= base_url('invitations/data') ?>";
        const invitationListUrl = "<?= base_url('invitations') ?>";
        const invitationExportUrl = "<?= base_url('invitations/export') ?>";
        const filterForm = document.getElementById('filterForm');
        const invitationTable = document.getElementById('invitationTable');
        const tableBody = document.getElementById('invitationsTableBody');
        const paginationContainer = document.getElementById('paginationContainer');
        const paginationSummary = document.getElementById('paginationSummary');
        const perPageSelect = document.getElementById('perPageSelect');
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');
        const exportBtn = document.getElementById('exportBtn');
        const searchInput = document.getElementById('searchInput');
        const columnFilterState = {};
        let activeFilterDropdown = null;
        let searchDebounceTimer = null;

        // Toast notification functions
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toastContainer');
            const toastId = 'toast_' + Date.now();
            const toast = document.createElement('div');
            toast.id = toastId;
            
            let bgClass, iconColor, icon;
            if (type === 'success') {
                bgClass = 'bg-green-500';
                iconColor = 'text-white';
                icon = 'check_circle';
            } else if (type === 'error') {
                bgClass = 'bg-red-500';
                iconColor = 'text-white';
                icon = 'error';
            } else if (type === 'warning') {
                bgClass = 'bg-yellow-500';
                iconColor = 'text-white';
                icon = 'warning';
            } else {
                bgClass = 'bg-blue-500';
                iconColor = 'text-white';
                icon = 'info';
            }
            
            toast.className = `transform transition-all duration-300 ease-in-out translate-x-full opacity-0 ${bgClass} text-white shadow-2xl rounded-lg mb-3 min-w-[320px]`;
            
            toast.innerHTML = `
                <div class="p-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined ${iconColor} text-2xl flex-shrink-0">${icon}</span>
                        <p class="text-base font-semibold text-white">${message}</p>
                    </div>
                    <button onclick="closeToast('${toastId}')" class="text-white hover:text-gray-200 flex-shrink-0">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
                toast.classList.add('translate-x-0', 'opacity-100');
            }, 100);
            
            // Auto remove after 4 seconds
            setTimeout(() => {
                closeToast(toastId);
            }, 4000);
        }

        function closeToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    if (toast && toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function buildStatusBadge(status) {
            if (status === 'Approved') return '<span class="bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 px-2 py-1 rounded-full text-[10px] uppercase font-bold">Approved</span>';
            if (status === 'Pending') return '<span class="bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300 px-2 py-1 rounded-full text-[10px] uppercase font-bold">Pending</span>';
            if (status === 'Submitted') return '<span class="bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 px-2 py-1 rounded-full text-[10px] uppercase font-bold">Submitted</span>';
            if (status === 'Rejected') return '<span class="bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 px-2 py-1 rounded-full text-[10px] uppercase font-bold">Rejected</span>';
            return `<span class="px-2 py-1 rounded-full text-[10px] uppercase font-bold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">${escapeHtml(status)}</span>`;
        }

        function buildPageNumbers(currentPage, lastPage) {
            const pages = [];
            if (lastPage <= 7) {
                for (let i = 1; i <= lastPage; i += 1) pages.push(i);
                return pages;
            }

            pages.push(1);
            if (currentPage > 3) pages.push('...');
            for (let i = Math.max(2, currentPage - 1); i <= Math.min(lastPage - 1, currentPage + 1); i += 1) {
                pages.push(i);
            }
            if (currentPage < lastPage - 2) pages.push('...');
            pages.push(lastPage);
            return pages;
        }

        function getTableDataRows() {
            return Array.from(tableBody.querySelectorAll('tr')).filter((row) => row.querySelectorAll('td').length > 1);
        }

        function getCellText(row, columnIndex) {
            const cell = row.cells[columnIndex];
            return cell ? cell.textContent.trim() : '';
        }

        function getColumnValues(columnIndex) {
            const values = getTableDataRows()
                .map((row) => getCellText(row, columnIndex))
                .filter((value) => value !== '');
            return Array.from(new Set(values)).sort((a, b) => a.localeCompare(b));
        }

        function updateColumnFilterIcon(columnIndex) {
            const icon = invitationTable.querySelector(`.js-column-filter-icon[data-col="${columnIndex}"]`);
            if (!icon) return;

            const state = columnFilterState[columnIndex];
            if (!state || !Array.isArray(state.allValues) || state.allValues.length === 0 || !state.selectedValues || state.selectedValues.size === state.allValues.length) {
                icon.classList.remove('text-[#535dec]');
                icon.classList.add('text-gray-300');
                return;
            }

            icon.classList.remove('text-gray-300');
            icon.classList.add('text-[#535dec]');
        }

        function applyColumnFilters() {
            const rows = getTableDataRows();
            rows.forEach((row) => {
                let visible = true;
                Object.keys(columnFilterState).forEach((key) => {
                    if (!visible) return;
                    const columnIndex = Number(key);
                    const state = columnFilterState[columnIndex];
                    if (!state || !state.selectedValues || state.selectedValues.size === 0 || state.selectedValues.size === state.allValues.length) {
                        return;
                    }
                    const cellValue = getCellText(row, columnIndex);
                    if (!state.selectedValues.has(cellValue)) {
                        visible = false;
                    }
                });
                row.style.display = visible ? '' : 'none';
            });
        }

        function closeAllColumnFilterDropdowns() {
            invitationTable.querySelectorAll('.js-column-filter-dropdown').forEach((dropdown) => {
                dropdown.classList.add('hidden');
            });
            activeFilterDropdown = null;
        }

        function resetColumnFilterState() {
            Object.keys(columnFilterState).forEach((key) => {
                delete columnFilterState[key];
            });
            closeAllColumnFilterDropdowns();
        }

        function buildColumnFilterDropdown(columnIndex, options) {
            const dropdown = document.createElement('div');
            dropdown.className = 'js-column-filter-dropdown hidden absolute top-full left-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-50 p-2 text-left text-xs max-h-[260px] overflow-y-auto';
            dropdown.style.minWidth = '220px';
            dropdown.addEventListener('click', (event) => event.stopPropagation());

            const state = columnFilterState[columnIndex] || { allValues: [], selectedValues: new Set() };
            state.allValues = options;
            if (!(state.selectedValues instanceof Set) || state.selectedValues.size === 0) {
                state.selectedValues = new Set(options);
            } else {
                state.selectedValues = new Set(options.filter((value) => state.selectedValues.has(value)));
                if (state.selectedValues.size === 0) {
                    state.selectedValues = new Set(options);
                }
            }
            columnFilterState[columnIndex] = state;

            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.placeholder = 'Search in this column...';
            searchInput.className = 'w-full mb-2 border border-gray-200 dark:border-gray-600 rounded px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 dark:bg-gray-700 dark:text-white';
            dropdown.appendChild(searchInput);

            const allLabel = document.createElement('label');
            allLabel.className = 'flex items-center gap-2 px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer font-semibold text-gray-700 dark:text-gray-200 mb-1';
            const allCb = document.createElement('input');
            allCb.type = 'checkbox';
            allCb.className = 'h-4 w-4 cursor-pointer accent-[#535dec]';
            allCb.checked = state.selectedValues.size === options.length && options.length > 0;
            allLabel.appendChild(allCb);
            allLabel.appendChild(document.createTextNode('All'));
            dropdown.appendChild(allLabel);

            const removeAllLabel = document.createElement('label');
            removeAllLabel.className = 'flex items-center gap-2 px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer font-semibold text-gray-700 dark:text-gray-200 mb-1';
            const removeAllCb = document.createElement('input');
            removeAllCb.type = 'checkbox';
            removeAllCb.className = 'h-4 w-4 cursor-pointer accent-red-500';
            removeAllLabel.appendChild(removeAllCb);
            removeAllLabel.appendChild(document.createTextNode('Remove All'));
            dropdown.appendChild(removeAllLabel);

            const separator = document.createElement('hr');
            separator.className = 'my-1 border-gray-200 dark:border-gray-700';
            dropdown.appendChild(separator);

            const itemRows = [];
            options.forEach((value) => {
                const itemLabel = document.createElement('label');
                itemLabel.className = 'js-filter-item flex items-center gap-2 px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer text-gray-600 dark:text-gray-300';
                itemLabel.dataset.filterText = value.toLowerCase();

                const itemCb = document.createElement('input');
                itemCb.type = 'checkbox';
                itemCb.className = 'h-4 w-4 cursor-pointer accent-[#535dec]';
                itemCb.value = value;
                itemCb.checked = state.selectedValues.has(value);

                const textNode = document.createElement('span');
                textNode.className = 'select-none';
                textNode.textContent = value;

                itemLabel.appendChild(itemCb);
                itemLabel.appendChild(textNode);
                dropdown.appendChild(itemLabel);
                itemRows.push({ label: itemLabel, cb: itemCb });
            });

            function syncStateAndApply() {
                const checkedValues = itemRows.filter((item) => item.cb.checked).map((item) => item.cb.value);
                if (checkedValues.length === 0 || checkedValues.length === options.length) {
                    state.selectedValues = new Set(options);
                } else {
                    state.selectedValues = new Set(checkedValues);
                }
                allCb.checked = checkedValues.length === options.length && options.length > 0;
                removeAllCb.checked = false;
                updateColumnFilterIcon(columnIndex);
                applyColumnFilters();
            }

            searchInput.addEventListener('input', function () {
                const term = this.value.trim().toLowerCase();
                itemRows.forEach((item) => {
                    item.label.style.display = item.label.dataset.filterText.includes(term) ? '' : 'none';
                });
            });

            allCb.addEventListener('change', function () {
                itemRows.forEach((item) => {
                    item.cb.checked = this.checked;
                });
                syncStateAndApply();
            });

            removeAllCb.addEventListener('change', function () {
                if (!this.checked) return;
                itemRows.forEach((item) => {
                    item.cb.checked = false;
                });
                // Match access report behavior: remove-all clears active filtering.
                state.selectedValues = new Set(options);
                allCb.checked = false;
                this.checked = false;
                updateColumnFilterIcon(columnIndex);
                applyColumnFilters();
            });

            itemRows.forEach((item) => {
                item.cb.addEventListener('change', syncStateAndApply);
            });

            return dropdown;
        }

        function setupColumnFilters() {
            const headers = invitationTable.querySelectorAll('thead th');
            headers.forEach((header, index) => {
                const old = header.querySelector('.js-column-filter-wrapper');
                if (old) {
                    old.remove();
                }
                if (index === 0) return;

                const options = getColumnValues(index);
                if (options.length === 0) return;

                header.classList.add('relative');
                const wrapper = document.createElement('div');
                wrapper.className = 'js-column-filter-wrapper inline-block relative ml-1 align-middle';
                wrapper.addEventListener('click', (event) => event.stopPropagation());

                const icon = document.createElement('span');
                icon.className = 'js-column-filter-icon material-symbols-outlined text-[16px] text-gray-300 hover:text-[#535dec] transition-colors cursor-pointer align-middle';
                icon.dataset.col = String(index);
                icon.textContent = 'filter_alt';

                const dropdown = buildColumnFilterDropdown(index, options);
                wrapper.appendChild(icon);
                wrapper.appendChild(dropdown);
                header.appendChild(wrapper);

                icon.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (activeFilterDropdown && activeFilterDropdown !== dropdown) {
                        closeAllColumnFilterDropdowns();
                    }
                    dropdown.classList.toggle('hidden');
                    activeFilterDropdown = dropdown.classList.contains('hidden') ? null : dropdown;
                });

                updateColumnFilterIcon(index);
            });
            applyColumnFilters();
        }

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.js-column-filter-wrapper')) {
                closeAllColumnFilterDropdowns();
            }
        });

        function renderTableRows(invitations) {
            if (!Array.isArray(invitations) || invitations.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="11" class="p-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-5xl">inbox</span>
                                <div>
                                    <h3 class="text-gray-500 dark:text-gray-400 font-semibold">No Data Available</h3>
                                    <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">No invitation records found. Create your first invitation to get started.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = invitations.map((invitation) => {
                const invitationJson = JSON.stringify(invitation).replace(/'/g, '&#39;');
                const icPassportEmpty = !invitation.ic_passport;
                const vehicleEmpty = !invitation.vehicle_reg;
                return `
                    <tr onclick='openDetailModal(${invitationJson})' class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border-b border-gray-100 dark:border-gray-700 cursor-pointer">
                        <td class="p-4">${escapeHtml(invitation.no)}</td>
                        <td class="p-4">${escapeHtml(invitation.date)}</td>
                        <td class="p-4 font-semibold text-gray-800 dark:text-white">${escapeHtml(invitation.full_name)}</td>
                        <td class="p-4 ${icPassportEmpty ? 'text-gray-400' : ''}">${icPassportEmpty ? 'NULL' : escapeHtml(invitation.ic_passport)}</td>
                        <td class="p-4">${escapeHtml(invitation.contact)}</td>
                        <td class="p-4">${escapeHtml(invitation.company)}</td>
                        <td class="p-4 ${vehicleEmpty ? 'text-gray-400' : ''}">${vehicleEmpty ? 'NULL' : escapeHtml(invitation.vehicle_reg)}</td>
                        <td class="p-4">${escapeHtml(invitation.location)}</td>
                        <td class="p-4">${escapeHtml(invitation.invited_by)}</td>
                        <td class="p-4">${buildStatusBadge(invitation.status)}</td>
                        <td class="p-4">${escapeHtml(invitation.reason)}</td>
                    </tr>
                `;
            }).join('');
        }

        function renderPagination(pagination) {
            const currentPage = Number(pagination.current_page) || 1;
            const lastPage = Math.max(1, Number(pagination.last_page) || 1);
            const total = Number(pagination.total) || 0;
            const perPage = Number(pagination.per_page) || 10;

            const pageNumbers = buildPageNumbers(currentPage, lastPage);
            let paginationHtml = '';

            if (currentPage > 1) {
                paginationHtml += `<button type="button" data-page="${currentPage - 1}" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">«</button>`;
            } else {
                paginationHtml += '<span class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded opacity-40 cursor-not-allowed">«</span>';
            }

            pageNumbers.forEach((page) => {
                if (page === '...') {
                    paginationHtml += '<span class="w-8 h-8 flex items-center justify-center">...</span>';
                    return;
                }
                if (page === currentPage) {
                    paginationHtml += `<span class="w-8 h-8 flex items-center justify-center bg-primary text-white rounded shadow-sm">${page}</span>`;
                    return;
                }
                paginationHtml += `<button type="button" data-page="${page}" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">${page}</button>`;
            });

            if (currentPage < lastPage) {
                paginationHtml += `<button type="button" data-page="${currentPage + 1}" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">»</button>`;
            } else {
                paginationHtml += '<span class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded opacity-40 cursor-not-allowed">»</span>';
            }

            paginationContainer.innerHTML = paginationHtml;

            const firstItem = total === 0 ? 0 : (currentPage - 1) * perPage + 1;
            const lastItem = Math.min(currentPage * perPage, total);
            paginationSummary.textContent = `Showing ${firstItem.toLocaleString()}-${lastItem.toLocaleString()} of ${total.toLocaleString()}`;
            perPageSelect.value = String(perPage);
        }

        function syncExportLink() {
            const params = new URLSearchParams(new FormData(filterForm));
            params.delete('page');
            params.delete('per_page');
            if (params.get('sort') === 'date_desc') {
                params.delete('sort');
            }
            const query = params.toString();
            exportBtn.href = query ? `${invitationExportUrl}?${query}` : invitationExportUrl;
        }

        function csvSafe(value) {
            const text = String(value ?? '');
            if (text.includes('"') || text.includes(',') || text.includes('\n')) {
                return `"${text.replace(/"/g, '""')}"`;
            }
            return text;
        }

        function exportCurrentTableView() {
            const visibleRows = getTableDataRows().filter((row) => row.style.display !== 'none');
            if (visibleRows.length === 0) {
                showToast('No data to export for current filters.', 'warning');
                return;
            }

            const headerCells = Array.from(invitationTable.querySelectorAll('thead th'));
            const headers = headerCells.map((th) => {
                const clone = th.cloneNode(true);
                clone.querySelectorAll('.js-column-filter-wrapper').forEach((node) => node.remove());
                return clone.textContent.trim();
            });

            const csvRows = [];
            csvRows.push(headers.map(csvSafe).join(','));

            visibleRows.forEach((row) => {
                const cols = Array.from(row.cells).map((cell) => csvSafe(cell.textContent.trim()));
                csvRows.push(cols.join(','));
            });

            const csvContent = "\uFEFF" + csvRows.join('\r\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            const stamp = new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-');
            link.href = url;
            link.download = `invitations-filtered-${stamp}.csv`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        async function fetchInvitations(pushState = true) {
            const params = new URLSearchParams(new FormData(filterForm));
            const queryString = params.toString();
            const requestUrl = queryString ? `${invitationDataUrl}?${queryString}` : invitationDataUrl;

            try {
                const response = await fetch(requestUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error('Failed to load invitation data');
                }

                renderTableRows(result.invitations || []);
                resetColumnFilterState();
                setupColumnFilters();
                renderPagination(result.pagination || {});
                syncExportLink();

                if (pushState) {
                    const listQuery = queryString;
                    const listUrl = listQuery ? `${invitationListUrl}?${listQuery}` : invitationListUrl;
                    window.history.replaceState({}, '', listUrl);
                }
            } catch (error) {
                console.error('Invitation list fetch failed:', error);
                showToast('Failed to load filtered invitations. Please try again.', 'error');
            }
        }

        filterForm.addEventListener('submit', function (event) {
            event.preventDefault();
            filterForm.elements.page.value = '1';
            fetchInvitations();
        });

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                if (searchDebounceTimer) {
                    clearTimeout(searchDebounceTimer);
                }
                searchDebounceTimer = setTimeout(() => {
                    filterForm.elements.page.value = '1';
                    fetchInvitations();
                }, 300);
            });
        }

        filterForm.querySelectorAll('select, input[type="date"]').forEach((element) => {
            element.addEventListener('change', function () {
                filterForm.elements.page.value = '1';
                fetchInvitations();
            });
        });

        paginationContainer.addEventListener('click', function (event) {
            const button = event.target.closest('[data-page]');
            if (!button) return;
            event.preventDefault();
            filterForm.elements.page.value = String(button.dataset.page);
            fetchInvitations();
        });

        perPageSelect.addEventListener('change', function () {
            filterForm.elements.per_page.value = this.value;
            filterForm.elements.page.value = '1';
            fetchInvitations();
        });

        clearFiltersBtn.addEventListener('click', function (event) {
            event.preventDefault();
            filterForm.elements.search.value = '';
            filterForm.elements.date_from.value = '';
            filterForm.elements.date_to.value = '';
            filterForm.elements.status.value = '';
            filterForm.elements.reason.value = '';
            filterForm.elements.location.value = '';
            filterForm.elements.sort.value = 'date_desc';
            filterForm.elements.page.value = '1';
            filterForm.elements.per_page.value = '10';
            fetchInvitations();
        });

        exportBtn.addEventListener('click', function (event) {
            event.preventDefault();
            exportCurrentTableView();
        });

        setupColumnFilters();

        function openDetailModal(invitation) {
            // Store current invitation ID for sending
            currentInvitationId = invitation.id;
            
            // Set status with appropriate styling
            const statusEl = document.getElementById('modalStatus');
            if (invitation.status === 'Approved') {
                statusEl.className = 'px-4 py-2 rounded-full text-sm font-bold bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300';
                statusEl.textContent = 'Approved';
            } else if (invitation.status === 'Pending') {
                statusEl.className = 'px-4 py-2 rounded-full text-sm font-bold bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300';
                statusEl.textContent = 'Pending';
            } else if (invitation.status === 'Submitted') {
                statusEl.className = 'px-4 py-2 rounded-full text-sm font-bold bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300';
                statusEl.textContent = 'Submitted';
            } else if (invitation.status === 'Rejected') {
                statusEl.className = 'px-4 py-2 rounded-full text-sm font-bold bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300';
                statusEl.textContent = 'Rejected';
            }

            // Fill in the details
            document.getElementById('modalLinkExpiry').textContent = invitation.link_expiry || '—';
            document.getElementById('modalVisitorCount').textContent = String(invitation.visitor_count != null ? invitation.visitor_count : 1);
            document.getElementById('modalFullName').textContent = invitation.full_name;
            document.getElementById('modalIcPassport').textContent = invitation.ic_passport || 'NULL';
            document.getElementById('modalContact').textContent = invitation.contact;
            document.getElementById('modalCompany').textContent = invitation.company;
            document.getElementById('modalVisitorEmail').textContent = invitation.visitor_email || '—';
            const regLink = document.getElementById('modalRegistrationLink');
            if (regLink && invitation.registration_link) {
                regLink.href = invitation.registration_link;
                regLink.textContent = invitation.registration_link;
            } else if (regLink) {
                regLink.removeAttribute('href');
                regLink.textContent = '—';
            }
            const vf = document.getElementById('modalVisitFrom');
            const vt = document.getElementById('modalVisitTo');
            if (vf) vf.textContent = invitation.visit_from || invitation.date || '—';
            if (vt) vt.textContent = invitation.visit_to || '—';
            document.getElementById('modalLocation').textContent = invitation.location;
            document.getElementById('modalReason').textContent = invitation.reason;
            document.getElementById('modalVehicle').textContent = invitation.vehicle_reg || 'Not Provided';
            document.getElementById('modalInvitedBy').textContent = invitation.invited_by;

            // Show modal
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
            currentInvitationId = null;
        }

        async function sendInvitation() {
            if (!currentInvitationId) {
                showToast('No invitation selected', 'error');
                return;
            }

            const sendBtn = document.getElementById('sendInvitationBtn');
            const originalText = sendBtn.innerHTML;
            
            // Disable button and show loading
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">refresh</span> Sending...';

            try {
                const response = await fetch(`<?= base_url('invitations/resend') ?>/${currentInvitationId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Invitation email sent successfully!', 'success');
                    closeDetailModal();
                } else {
                    showToast('Failed to send invitation: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Error sending invitation:', error);
                showToast('An error occurred while sending the invitation', 'error');
            } finally {
                // Re-enable button
                sendBtn.disabled = false;
                sendBtn.innerHTML = originalText;
            }
        }

        // Close modal on backdrop click
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDetailModal();
            }
        });
    </script>
</body>
</html>
