<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>

<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "primary-dark": "#0f66be",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1a2634",
                    },
                    fontFamily: {
                        "display": ["Montserrat", "sans-serif"],
                        "sans": ["Montserrat", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.375rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <!-- Blacklist dropdown function-->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white overflow-hidden">
<div class="flex h-screen w-full">
    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark flex flex-col justify-between p-4 hidden md:flex h-full">
        <div class="flex flex-col gap-8">
            <div class="flex items-center gap-3 px-2">
                <div class="bg-center bg-no-repeat bg-cover rounded-lg size-10 bg-primary/10 flex items-center justify-center text-primary" data-alt="SafeG Logo abstract blue square">
                    <span class="material-symbols-outlined text-3xl">shield_person</span>
                </div>
                <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">SafeG</h1>
            </div>
            <nav class="flex flex-col gap-2">
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary group transition-colors" href="<?= base_url('dashboard') ?>">
                    <span class="material-symbols-outlined text-[22px] font-medium fill-1 group-hover:scale-110 transition-transform">dashboard</span>
                    <p class="text-sm font-semibold">Dashboard</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('compliance') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">health_and_safety</span>
                    <p class="text-sm font-medium">Compliance</p>
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
                <?php if (!empty($userPhoto)): ?>
                    <img src="<?= base_url('assets/uploads/profiles/' . $userPhoto) ?>" 
                         alt="Profile" 
                         class="size-9 rounded-full object-cover shadow-sm ring-2 ring-white dark:ring-slate-900"/>
                <?php else: ?>
                    <div class="size-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shadow-sm ring-2 ring-white dark:ring-slate-900">
                        <?= strtoupper(substr(session()->get('full_name') ?? $userName ?? 'U', 0, 2)) ?>
                    </div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white truncate"><?= esc(session()->get('full_name') ?? $userName) ?></p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate"><?= esc(ucfirst(session()->get('role') ?? $userRole)) ?></p>
                </div>
                <a href="<?= base_url('auth/logout') ?>" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <span class="material-symbols-outlined text-xl">logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <!-- Header -->
        <header class="flex-shrink-0 border-b border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark px-8 py-3 flex items-center justify-between z-10 min-h-[5rem]">
            <button class="md:hidden p-2 text-slate-600 dark:text-slate-400">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <div class="flex flex-col justify-center">
                <h2 class="text-slate-900 dark:text-white text-lg font-bold leading-tight">Host Dashboard</h2>
                <p class="text-slate-500 dark:text-slate-400 text-xs">Today, <?= $currentDate ?></p>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative hidden sm:block">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input class="h-10 pl-10 pr-4 text-sm bg-slate-100 dark:bg-slate-800 border-none rounded-full focus:ring-2 focus:ring-primary text-slate-900 dark:text-white placeholder-slate-400 w-64" placeholder="Search visitors..." type="text"/>
                </div>
                <div class="flex items-center gap-2">
                    <select class="text-sm bg-transparent font-medium text-slate-600 dark:text-slate-400 border-none focus:ring-0 cursor-pointer">
                        <option value="en">EN</option>
                        <option value="ms">MS</option>
                    </select>
                </div>
                <button class="flex items-center justify-center size-10 rounded-full text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors relative">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border border-white dark:border-slate-900"></span>
                </button>
                <div class="flex flex-col gap-2 items-end">
                    <a href="<?= base_url('invitations/create') ?>" class="flex items-center justify-center h-10 px-4 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-lg shadow-sm transition-colors gap-2 w-full sm:w-48">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span class="hidden sm:inline">New Invitation</span>
                    </a>
                    <a href="<?= base_url('visitor-pass-request') ?>" class="flex items-center justify-center h-10 px-4 bg-surface-light dark:bg-surface-dark border border-primary text-primary hover:bg-slate-50 dark:hover:bg-slate-800 text-sm font-bold rounded-lg shadow-sm transition-colors gap-2 w-full sm:w-48">
                        <span class="material-symbols-outlined text-[20px]">person_add</span>
                        <span class="hidden sm:inline">Walk-in Visitor</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-8 pb-20 no-scrollbar">
            <!-- Welcome Section -->
            <div class="flex flex-col gap-2">
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Welcome back, <?= esc(session()->get('full_name') ?? $userName) ?></h1>
                <p class="text-slate-500 dark:text-slate-400 text-base">Here's what's happening at your facility today.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <!-- Expected Today Card -->
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-4 relative overflow-hidden group cursor-pointer hover:border-indigo-300 dark:hover:border-indigo-800 hover:ring-1 hover:ring-indigo-100 dark:hover:ring-indigo-900/50 transition-all">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-slate-900 dark:text-white">calendar_today</span>
                    </div>
                    <div class="size-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <span class="material-symbols-outlined">calendar_month</span>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Expected Today</p>
                        <div class="flex items-baseline gap-2">
                            <p class="text-3xl font-bold text-slate-900 dark:text-white"><?= $stats['expectedToday'] ?></p>
                            <?php if ($trend != 0): ?>
                            <span class="text-xs <?= $trend > 0 ? 'text-green-600' : 'text-red-600' ?> font-medium flex items-center <?= $trend > 0 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' ?> px-1.5 py-0.5 rounded">
                                <span class="material-symbols-outlined text-[14px] mr-0.5"><?= $trend > 0 ? 'trending_up' : 'trending_down' ?></span> <?= ($trend > 0 ? '+' : '') . $trend ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Currently On-Site Card -->
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-4 relative overflow-hidden group cursor-pointer hover:border-primary/50 dark:hover:border-primary/50 hover:ring-1 hover:ring-primary/10 transition-all">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-primary">group</span>
                    </div>
                    <div class="size-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined fill-1">check_circle</span>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Currently On-Site</p>
                        <div class="flex items-baseline gap-2">
                            <p class="text-3xl font-bold text-slate-900 dark:text-white"><?= $stats['currentlyOnSite'] ?></p>
                            <span class="text-sm text-slate-400 font-normal">visitors</span>
                        </div>
                    </div>
                </div>

                <!-- Checked Out Card -->
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-4 relative overflow-hidden group cursor-pointer hover:border-slate-400 dark:hover:border-slate-600 hover:ring-1 hover:ring-slate-200 dark:hover:ring-slate-700/50 transition-all">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-slate-900 dark:text-white">logout</span>
                    </div>
                    <div class="size-10 rounded-lg bg-slate-100 dark:bg-slate-700/50 flex items-center justify-center text-slate-600 dark:text-slate-400">
                        <span class="material-symbols-outlined">logout</span>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Checked Out</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white"><?= $stats['checkedOut'] ?></p>
                    </div>
                </div>

                <!-- Out of Window Card -->
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-4 relative overflow-hidden group cursor-pointer hover:border-red-300 dark:hover:border-red-800 transition-colors">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-slate-900 dark:text-white">timer_off</span>
                    </div>
                    <div class="size-10 rounded-lg bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400">
                        <span class="material-symbols-outlined">timer_off</span>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Out of Window</p>
                        <div class="flex items-center gap-3">
                            <p class="text-3xl font-bold text-slate-900 dark:text-white"><?= $stats['outOfWindow'] ?></p>
                            <button class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 text-xs font-bold transition-colors z-20" onclick="event.stopPropagation()">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Activity Section -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Visitor Occupancy Chart -->
                <div class="xl:col-span-2 bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Visitor Occupancy</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Real-time capacity tracking</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-primary"><?= $stats['currentlyOnSite'] ?><span class="text-slate-400 text-lg font-normal">/50</span></p>
                            <p class="text-xs text-slate-400">Capacity</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-6 gap-4 items-end h-48 px-2">
                        <?php foreach ($occupancyChart as $bar): ?>
                        <div class="flex flex-col items-center gap-2 h-full justify-end group cursor-pointer">
                            <div class="relative w-full max-w-[40px] bg-indigo-50 dark:bg-slate-800 rounded-t-sm h-full flex items-end overflow-hidden">
                                <div class="w-full <?= $bar['isPeak'] ? 'bg-primary' : 'bg-indigo-200 dark:bg-indigo-900' ?> transition-all duration-500<?= $bar['isPeak'] ? ' relative' : '' ?>" style="height: <?= max($bar['percentage'], 2) ?>%">
                                    <?php if ($bar['isPeak']): ?>
                                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10"><?= $bar['label'] ?> (Peak: <?= $bar['count'] ?>)</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="text-xs <?= $bar['isPeak'] ? 'font-bold text-primary' : 'font-medium text-slate-500' ?>"><?= $bar['label'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Recent Activity</h3>
                        <button class="text-xs font-medium text-primary hover:text-primary-dark">View All</button>
                    </div>
                    <div class="flex flex-col gap-5 overflow-y-auto max-h-[220px] pr-2 custom-scrollbar">
                        <?php foreach ($recentActivity as $activity): ?>
                        <div class="flex gap-3 items-start">
                            <div class="relative">
                                <?php if (isset($activity['image'])): ?>
                                <div class="size-8 rounded-full bg-cover bg-center" style="background-image: url('<?= $activity['image'] ?>');"></div>
                                <?php else: ?>
                                <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xs font-bold">
                                    <?= $activity['initials'] ?>
                                </div>
                                <?php endif; ?>
                                <div class="absolute -bottom-1 -right-1 bg-surface-light dark:bg-surface-dark rounded-full p-0.5">
                                    <?php
                                    $statusColor = 'bg-slate-400';
                                    if ($activity['status'] === 'online') $statusColor = 'bg-green-500';
                                    elseif ($activity['status'] === 'pending') $statusColor = 'bg-amber-400';
                                    ?>
                                    <div class="size-2.5 <?= $statusColor ?> rounded-full border border-white dark:border-slate-900"></div>
                                </div>
                            </div>
                            <div class="flex flex-col flex-1">
                                <?php if ($activity['action'] === 'invitation created'): ?>
                                <p class="text-sm text-slate-900 dark:text-white">Invitation created for <span class="font-semibold"><?= esc($activity['name']) ?></span>.</p>
                                <?php else: ?>
                                <p class="text-sm text-slate-900 dark:text-white"><span class="font-semibold"><?= esc($activity['name']) ?></span> <?= $activity['action'] ?>.</p>
                                <?php endif; ?>
                                <p class="text-xs text-slate-400 mt-0.5"><?= $activity['time'] ?> • <?= $activity['location'] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Visitors Table -->
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <!-- Tabs -->
                <div class="border-b border-slate-200 dark:border-slate-700 px-6 pt-2">
                    <div class="flex gap-8 overflow-x-auto no-scrollbar">
                        <a class="flex items-center gap-2 border-b-[3px] border-primary text-primary pb-3 pt-2 cursor-pointer group whitespace-nowrap">
                            <p class="text-sm font-bold">All Visitors</p>
                            <span class="bg-primary/10 text-primary text-[10px] px-1.5 py-0.5 rounded-full font-bold"><?= $tabCounts['all'] ?></span>
                        </a>
                        <a class="flex items-center gap-2 border-b-[3px] border-transparent hover:border-slate-300 dark:hover:border-slate-600 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 pb-3 pt-2 cursor-pointer transition-all whitespace-nowrap">
                            <p class="text-sm font-medium">Pre-Arrival</p>
                            <span class="bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] px-1.5 py-0.5 rounded-full font-bold"><?= $tabCounts['preArrival'] ?></span>
                        </a>
                        <a class="flex items-center gap-2 border-b-[3px] border-transparent hover:border-slate-300 dark:hover:border-slate-600 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 pb-3 pt-2 cursor-pointer transition-all whitespace-nowrap">
                            <p class="text-sm font-medium">Checked In</p>
                            <span class="bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] px-1.5 py-0.5 rounded-full font-bold"><?= $tabCounts['checkedIn'] ?></span>
                        </a>
                        <a class="flex items-center gap-2 border-b-[3px] border-transparent hover:border-slate-300 dark:hover:border-slate-600 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 pb-3 pt-2 cursor-pointer transition-all whitespace-nowrap">
                            <p class="text-sm font-medium">Checked Out</p>
                            <span class="bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] px-1.5 py-0.5 rounded-full font-bold"><?= $tabCounts['checkedOut'] ?></span>
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="p-4 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50 dark:bg-slate-800/20 flex-wrap">
                    <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto">
                        <div class="relative w-full sm:w-auto flex-1 sm:flex-initial">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">filter_list</span>
                            <select class="pl-9 pr-8 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300 w-full sm:w-48 appearance-none cursor-pointer">
                                <option>Filter by Company</option>
                                <?php foreach ($companyList as $company): ?>
                                <option><?= esc($company) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex-1 sm:flex-initial">
                                <input class="py-2 px-3 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300 w-full sm:w-auto" placeholder="Start Date" type="datetime-local"/>
                            </div>
                            <span class="text-slate-400 hidden sm:inline">-</span>
                            <div class="relative flex-1 sm:flex-initial">
                                <input class="py-2 px-3 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300 w-full sm:w-auto" placeholder="End Date" type="datetime-local"/>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto justify-end">
                        <button class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            Export
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Visitor Name</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Host</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-surface-dark">
                            <?php foreach ($visitors as $visitor): ?>
                            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <?php if ($visitor['hasImage']): ?>
                                        <div class="size-9 rounded-full bg-cover bg-center" style="background-image: url('<?= $visitor['image'] ?>');"></div>
                                        <?php else: ?>
                                        <div class="size-9 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xs font-bold">
                                            <?= $visitor['initials'] ?>
                                        </div>
                                        <?php endif; ?>
                                        <div>
                                            <p class="text-sm font-medium text-slate-900 dark:text-white <?= $visitor['status'] === 'Checked Out' ? 'opacity-60' : '' ?>"><?= esc($visitor['name']) ?></p>
                                            <p class="text-xs text-slate-500"><?= esc($visitor['email']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300"><?= esc($visitor['company']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300"><?= esc($visitor['host']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300"><?= esc($visitor['time']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusClasses = [
                                        'green' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-800 bg-green-500',
                                        'amber' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800 bg-amber-500',
                                        'slate' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700 bg-slate-400'
                                    ];
                                    $classes = explode(' ', $statusClasses[$visitor['statusClass']]);
                                    ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium <?= $classes[0] ?> <?= $classes[1] ?> <?= $classes[2] ?> <?= $classes[3] ?> border <?= $classes[4] ?> <?= $classes[5] ?>">
                                        <span class="size-1.5 rounded-full <?= $classes[6] ?>"></span>
                                        <?= esc($visitor['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <?php if ($visitor['status'] === 'On-Site'): ?>
                                    <button class="text-slate-400 hover:text-primary transition-colors p-1" title="View Details">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>
                                    <button class="text-slate-400 hover:text-red-600 transition-colors p-1" title="Check Out">
                                        <span class="material-symbols-outlined text-[20px]">logout</span>
                                    </button>
                                    <?php elseif ($visitor['status'] === 'Pre-Arrival'): ?>
                                    <button class="inline-flex items-center justify-center px-3 py-1.5 bg-primary/10 hover:bg-primary/20 text-primary text-xs font-bold rounded transition-colors mr-2">
                                        Check In
                                    </button>
                                    <button class="text-slate-400 hover:text-primary transition-colors p-1">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    <?php else: ?>
                                    <button class="text-slate-400 hover:text-primary transition-colors p-1" title="View History">
                                        <span class="material-symbols-outlined text-[20px]">history</span>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-surface-dark px-6 py-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400">Showing <span class="font-bold">1-<?= count($visitors) ?></span> of <span class="font-bold"><?= $tabCounts['all'] ?></span> visitors</p>
                    <div class="flex gap-2">
                        <button class="px-3 py-1 text-xs border border-slate-200 dark:border-slate-700 rounded hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 disabled:opacity-50" disabled>Previous</button>
                        <button class="px-3 py-1 text-xs border border-slate-200 dark:border-slate-700 rounded hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
