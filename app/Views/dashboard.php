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
    <aside class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark flex flex-col p-4 hidden md:flex h-full overflow-hidden">
        <div class="flex flex-col gap-8 flex-1 min-h-0">
            <div class="flex items-center gap-3 px-2">
                <div class="bg-center bg-no-repeat bg-cover rounded-lg size-10 bg-primary/10 flex items-center justify-center text-primary" data-alt="SafeG Logo abstract blue square">
                    <span class="material-symbols-outlined text-3xl">shield_person</span>
                </div>
                <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">SafeG</h1>
            </div>
            <nav class="flex flex-col gap-2 overflow-y-auto pr-1 custom-scrollbar">
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary group transition-colors" href="<?= base_url('dashboard') ?>">
                    <span class="material-symbols-outlined text-[22px] font-medium fill-1 group-hover:scale-110 transition-transform">dashboard</span>
                    <p class="text-sm font-semibold">Dashboard</p>
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
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('staffs') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">badge</span>
                    <p class="text-sm font-medium">Staff Pass List</p>
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

            <!-- Critical Security Alert: one card; multiple alerts rotate here after each Acknowledge -->
            <div id="critical-alert-section">
                <div id="critical-alert-active-wrapper" class="<?= !empty($criticalAlerts) ? '' : 'hidden' ?> bg-gradient-to-r from-red-900 via-red-800 to-red-900 rounded-xl p-5 border border-red-700 shadow-lg relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-red-700/20 rounded-full -mr-10 -mt-10"></div>
                    <div id="critical-alert-card-content" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10"></div>
                </div>
                <div id="critical-alert-all-clear-wrapper" class="<?= !empty($criticalAlerts) ? 'hidden' : '' ?> bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 rounded-xl p-5 border border-slate-600 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-slate-600/20 rounded-full -mr-10 -mt-10"></div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="size-10 rounded-lg bg-green-700/30 flex items-center justify-center text-green-300 flex-shrink-0">
                            <span class="material-symbols-outlined fill-1">verified_user</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-white font-bold text-base">Critical Security Alert</h3>
                            <p class="text-slate-300 text-sm mt-0.5">No critical security alerts at this time. All clear.</p>
                        </div>
                        <button type="button" onclick="openModal('activeAlerts')" class="flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-bold rounded-lg transition-colors backdrop-blur-sm flex-shrink-0">
                            <span class="material-symbols-outlined text-[18px]">shield</span> View All Alerts
                        </button>
                    </div>
                </div>
            </div>
            <?php if (!empty($criticalAlerts)): ?>
            <script type="application/json" id="critical-alerts-json"><?= json_encode($criticalAlerts, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?></script>
            <?php endif; ?>

            <div id="ack-access-denied-prompt" class="hidden"></div>

            <!-- Recent Alerts Summary -->
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-slate-700 dark:text-slate-300 fill-1">notifications_active</span>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Recent Alerts</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div onclick="openModal('accessDenied')" class="bg-gradient-to-br from-red-800 to-red-900 rounded-xl p-6 border border-red-700 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow cursor-pointer">
                        <div class="absolute top-3 right-3"><span class="px-2 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full">Access Denied</span></div>
                        <p class="text-4xl font-black text-white mb-1" id="dash-widget-access-denied"><?= $accessDeniedCount ?></p>
                        <p class="text-red-100 font-semibold text-sm">Access Denied Incidents</p>
                        <p class="text-red-300 text-xs mt-1">Acknowledged — last 24 hours</p>
                        <div class="flex items-center gap-1 mt-3 text-red-200 text-xs font-medium group-hover:text-white transition-colors"><span class="material-symbols-outlined text-[14px]">arrow_forward</span> View all incidents</div>
                    </div>
                    <div onclick="openModal('overstay')" class="bg-gradient-to-br from-amber-700 to-amber-800 rounded-xl p-6 border border-amber-600 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow cursor-pointer">
                        <div class="absolute top-3 right-3"><span class="px-2 py-0.5 bg-amber-500 text-white text-[10px] font-bold rounded-full">Overstay</span></div>
                        <p class="text-4xl font-black text-white mb-1" id="dash-widget-overstay"><?= $overstayCount ?></p>
                        <p class="text-amber-100 font-semibold text-sm">Visitor Overstay Alerts</p>
                        <p class="text-amber-300 text-xs mt-1">Active alerts</p>
                        <div class="flex items-center gap-1 mt-3 text-amber-200 text-xs font-medium group-hover:text-white transition-colors"><span class="material-symbols-outlined text-[14px]">arrow_forward</span> View all alerts</div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <!-- Expected Today Card -->
                <div onclick="openModal('expectedToday')" class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-4 relative overflow-hidden group cursor-pointer hover:border-indigo-300 dark:hover:border-indigo-800 hover:ring-1 hover:ring-indigo-100 dark:hover:ring-indigo-900/50 transition-all">
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
                <div onclick="openModal('onSite')" class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-4 relative overflow-hidden group cursor-pointer hover:border-primary/50 dark:hover:border-primary/50 hover:ring-1 hover:ring-primary/10 transition-all">
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
                <div onclick="openModal('checkedOut')" class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-4 relative overflow-hidden group cursor-pointer hover:border-slate-400 dark:hover:border-slate-600 hover:ring-1 hover:ring-slate-200 dark:hover:ring-slate-700/50 transition-all">
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

                <!-- Active Security Alerts Card -->
                <div onclick="openModal('activeAlerts')" class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-4 relative overflow-hidden group cursor-pointer hover:border-red-300 dark:hover:border-red-800 transition-colors">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-slate-900 dark:text-white">shield</span>
                    </div>
                    <div class="size-10 rounded-lg bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400">
                        <span class="material-symbols-outlined fill-1">shield</span>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Active Security Alerts</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white" id="dash-widget-active-alerts"><?= $activeSecurityAlertCount ?></p>
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
                            <p class="text-2xl font-bold text-primary"><?= $stats['currentlyOnSite'] ?></p>
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
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Recent Activity</h3>
                            <p class="text-xs text-slate-400 mt-0.5">All system events from the last 7 days</p>
                        </div>
                        <button onclick="openModal('recentActivity')" class="text-xs font-medium text-primary hover:text-primary-dark">View All</button>
                    </div>
                    <!-- Legend -->
                    <div class="flex flex-wrap gap-x-3 gap-y-1 mb-4">
                        <span class="flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400"><span class="size-2 rounded-full bg-amber-400 inline-block"></span>Created</span>
                        <span class="flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400"><span class="size-2 rounded-full bg-green-500 inline-block"></span>Approved</span>
                        <span class="flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400"><span class="size-2 rounded-full bg-red-500 inline-block"></span>Rejected</span>
                        <span class="flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400"><span class="size-2 rounded-full bg-blue-500 inline-block"></span>Door Access</span>
                        <span class="flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400"><span class="size-2 rounded-full bg-slate-400 inline-block"></span>Check-out</span>
                    </div>
                    <div class="flex flex-col gap-0 overflow-y-auto max-h-[340px] pr-1 custom-scrollbar divide-y divide-slate-100 dark:divide-slate-700/50">
                        <?php if (empty($recentActivity)): ?>
                        <p class="text-sm text-slate-400 text-center py-6">No recent activity in the last 7 days.</p>
                        <?php else: ?>
                        <?php foreach ($recentActivity as $activity): ?>
                        <div class="flex gap-3 items-start py-3">
                            <div class="size-8 rounded-full <?= $activity['iconBg'] ?> flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[17px] <?= $activity['iconColor'] ?>"><?= $activity['icon'] ?></span>
                            </div>
                            <div class="flex flex-col flex-1 min-w-0">
                                <p class="text-sm text-slate-900 dark:text-white leading-snug"><?= $activity['description'] ?>.</p>
                                <p class="text-xs text-slate-400 mt-0.5"><?= $activity['time'] ?> • <?= $activity['location'] ?></p>
                            </div>
                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full flex-shrink-0 mt-1
                                <?php
                                echo match($activity['type']) {
                                    'approved'        => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'rejected'        => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'check_in'        => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'check_out'       => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                    'door_access'     => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'security_alert'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    default           => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                };
                                ?>"><?= esc($activity['label']) ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Visitors Table -->
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <!-- Tabs -->
                <div class="border-b border-slate-200 dark:border-slate-700 px-6 pt-2">
                    <div class="flex gap-8 overflow-x-auto no-scrollbar">
                        <button type="button" data-visitor-tab="all" class="flex items-center gap-2 border-b-[3px] border-primary text-primary pb-3 pt-2 cursor-pointer group whitespace-nowrap">
                            <p class="text-sm font-bold">All Visitors</p>
                            <span data-tab-count class="bg-primary/10 text-primary text-[10px] px-1.5 py-0.5 rounded-full font-bold"><?= $tabCounts['all'] ?></span>
                        </button>
                        <button type="button" data-visitor-tab="prearrival" class="flex items-center gap-2 border-b-[3px] border-transparent hover:border-slate-300 dark:hover:border-slate-600 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 pb-3 pt-2 cursor-pointer transition-all whitespace-nowrap">
                            <p class="text-sm font-medium">Pre-Arrival</p>
                            <span data-tab-count class="bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] px-1.5 py-0.5 rounded-full font-bold"><?= $tabCounts['preArrival'] ?></span>
                        </button>
                        <button type="button" data-visitor-tab="checkedin" class="flex items-center gap-2 border-b-[3px] border-transparent hover:border-slate-300 dark:hover:border-slate-600 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 pb-3 pt-2 cursor-pointer transition-all whitespace-nowrap">
                            <p class="text-sm font-medium">Checked In</p>
                            <span data-tab-count class="bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] px-1.5 py-0.5 rounded-full font-bold"><?= $tabCounts['checkedIn'] ?></span>
                        </button>
                        <button type="button" data-visitor-tab="checkedout" class="flex items-center gap-2 border-b-[3px] border-transparent hover:border-slate-300 dark:hover:border-slate-600 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 pb-3 pt-2 cursor-pointer transition-all whitespace-nowrap">
                            <p class="text-sm font-medium">Checked Out</p>
                            <span data-tab-count class="bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] px-1.5 py-0.5 rounded-full font-bold"><?= $tabCounts['checkedOut'] ?></span>
                        </button>
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
                        <tbody id="visitors-table-body" class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-surface-dark">
                            <?php foreach ($visitors as $visitor): ?>
                            <?php
                                $statusToken = 'prearrival';
                                if ($visitor['status'] === 'On-Site') {
                                    $statusToken = 'checkedin';
                                } elseif ($visitor['status'] === 'Checked Out') {
                                    $statusToken = 'checkedout';
                                }
                            ?>
                            <tr data-visitor-status="<?= $statusToken ?>" data-invitation-id="<?= $visitor['id'] ?>" class="visitor-row group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
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
                                            <p class="text-xs text-slate-500"><?= esc($visitor['contact']) ?></p>
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
                                    <span data-status-badge class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium <?= $classes[0] ?> <?= $classes[1] ?> <?= $classes[2] ?> <?= $classes[3] ?> border <?= $classes[4] ?> <?= $classes[5] ?>">
                                        <span class="size-1.5 rounded-full <?= $classes[6] ?>"></span>
                                        <?= esc($visitor['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <?php if ($visitor['status'] === 'On-Site'): ?>
                                    <button onclick="dashboardViewVisitor(this)" class="text-slate-400 hover:text-primary transition-colors p-1" title="View Details">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>
                                    <button onclick="dashboardCheckOut(this)" class="text-slate-400 hover:text-red-600 transition-colors p-1" title="Check Out">
                                        <span class="material-symbols-outlined text-[20px]">logout</span>
                                    </button>
                                    <?php elseif ($visitor['status'] === 'Pre-Arrival'): ?>
                                    <button onclick="dashboardCheckIn(this)" class="text-slate-400 hover:text-green-600 transition-colors p-1" title="Check In">
                                        <span class="material-symbols-outlined text-[20px]">login</span>
                                    </button>
                                    <button onclick="dashboardEditVisitor(this)" class="text-slate-400 hover:text-primary transition-colors p-1" title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    <?php else: ?>
                                    <button onclick="dashboardViewVisitor(this)" class="text-slate-400 hover:text-primary transition-colors p-1" title="View History">
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
                    <p class="text-xs text-slate-500 dark:text-slate-400">Showing <span id="visitors-showing-range" class="font-bold">1-<?= count($visitors) ?></span> of <span id="visitors-total-count" class="font-bold"><?= $tabCounts['all'] ?></span> visitors</p>
                    <div class="flex gap-2">
                        <button class="px-3 py-1 text-xs border border-slate-200 dark:border-slate-700 rounded hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 disabled:opacity-50" disabled>Previous</button>
                        <button class="px-3 py-1 text-xs border border-slate-200 dark:border-slate-700 rounded hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400">Next</button>
                    </div>
                </div>
            </div>

            <!-- Currently On-Site & Appointments Row -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Currently On-Site Table -->
                <div class="xl:col-span-2 bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between p-6 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary fill-1">group</span>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Currently On-Site</h3>
                            <span class="bg-primary/10 text-primary text-[10px] px-2 py-0.5 rounded-full font-bold"><?= $onSiteVisitorCount ?></span>
                        </div>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">search</span>
                            <input class="h-8 pl-8 pr-3 text-xs bg-slate-100 dark:bg-slate-800 border-none rounded-full focus:ring-1 focus:ring-primary text-slate-900 dark:text-white placeholder-slate-400 w-40" placeholder="Search visitors..." type="text" id="onsite-search"/>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="onsite-table">
                            <thead>
                                <tr class="border-y border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Visitor Name</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Host</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Check-in Time</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Location</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-surface-dark">
                                <?php if (empty($onSiteVisitors)): ?>
                                <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-slate-400">No visitors currently on-site</td></tr>
                                <?php else: ?>
                                <?php foreach ($onSiteVisitors as $ov): ?>
                                <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-3 whitespace-nowrap"><div class="flex items-center gap-2"><div class="size-7 rounded-full bg-primary/10 flex items-center justify-center text-primary text-[10px] font-bold"><?= strtoupper(substr($ov['name'], 0, 2)) ?></div><span class="text-sm font-medium text-slate-900 dark:text-white"><?= esc($ov['name']) ?></span></div></td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300"><?= esc($ov['host']) ?></td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300"><?= esc($ov['check_in_time']) ?></td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300"><?= esc($ov['location']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Appointments Sidebar -->
                <div class="flex flex-col gap-6">
                    <!-- Upcoming Appointments -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col">
                        <div class="flex items-center gap-2 mb-4"><span class="material-symbols-outlined text-indigo-500 fill-1">event_upcoming</span><h3 class="text-base font-bold text-slate-900 dark:text-white">Upcoming Appointments</h3></div>
                        <?php if (empty($upcomingAppointments)): ?>
                        <div class="flex flex-col items-center justify-center py-8 text-center"><p class="text-4xl font-black text-slate-300 dark:text-slate-600 mb-2">0</p><p class="text-sm text-slate-400 italic">No upcoming appointments</p></div>
                        <?php else: ?>
                        <div class="flex flex-col gap-3 max-h-[200px] overflow-y-auto pr-1">
                            <?php foreach ($upcomingAppointments as $appt): ?>
                            <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <div class="size-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 flex-shrink-0 mt-0.5"><span class="material-symbols-outlined text-[18px]">schedule</span></div>
                                <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-slate-900 dark:text-white truncate"><?= esc($appt['visitor_name']) ?></p><p class="text-xs text-slate-500"><?= esc($appt['time']) ?> - <?= esc($appt['date']) ?></p><p class="text-xs text-slate-400">Host: <?= esc($appt['host_name']) ?></p></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <!-- Today's Appointments -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col">
                        <div class="flex items-center gap-2 mb-4"><span class="material-symbols-outlined text-emerald-500 fill-1">today</span><h3 class="text-base font-bold text-slate-900 dark:text-white">Today's Appointments</h3><span class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] px-2 py-0.5 rounded-full font-bold"><?= count($todayAppointments) ?></span></div>
                        <?php if (empty($todayAppointments)): ?>
                        <p class="text-sm text-slate-400 italic text-center py-4">No appointments today</p>
                        <?php else: ?>
                        <div class="flex flex-col gap-2.5 max-h-[250px] overflow-y-auto pr-1">
                            <?php foreach ($todayAppointments as $ta): ?>
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 dark:border-slate-700 hover:border-emerald-200 dark:hover:border-emerald-800 transition-colors">
                                <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-slate-900 dark:text-white truncate"><?= esc($ta['visitor_name']) ?></p><p class="text-xs text-slate-500"><?= esc($ta['time']) ?> - <?= esc($ta['end_time']) ?></p><p class="text-xs text-slate-400">Host: <?= esc($ta['host_name']) ?></p></div>
                                <?php $apptStatusColor = match($ta['status']) { 'In Progress' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'Completed' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400', default => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }; ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold flex-shrink-0 <?= $apptStatusColor ?>"><?= esc($ta['status']) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Visitor Traffic Analytics -->
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-2"><span class="material-symbols-outlined text-primary fill-1">bar_chart</span><h3 class="text-base font-bold text-slate-900 dark:text-white">Visitor Traffic Analytics</h3></div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="flex items-center gap-2"><label class="text-xs font-medium text-slate-500">From</label><input type="date" id="traffic-from" value="<?= date('Y-m-d') ?>" class="h-9 px-3 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300"/></div>
                        <div class="flex items-center gap-2"><label class="text-xs font-medium text-slate-500">To</label><input type="date" id="traffic-to" value="<?= date('Y-m-d') ?>" class="h-9 px-3 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300"/></div>
                        <button onclick="updateTrafficGraph()" class="flex items-center gap-1.5 h-9 px-4 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-lg transition-colors"><span class="material-symbols-outlined text-[18px]">bar_chart</span> Update Graph</button>
                    </div>
                </div>
                <div class="relative">
                    <div class="flex items-center gap-1.5 mb-3"><div class="w-3 h-3 bg-primary rounded-sm"></div><span class="text-xs text-slate-500 font-medium">Scans</span></div>
                    <div class="flex items-end gap-1 h-48 border-b border-l border-slate-200 dark:border-slate-700 pl-8 pb-1 relative" id="traffic-chart">
                        <div class="absolute left-0 top-0 bottom-0 flex flex-col justify-between text-[10px] text-slate-400 font-medium py-1" id="traffic-y-axis"></div>
                        <?php foreach ($trafficHours as $th): ?>
                        <div class="flex-1 flex flex-col items-center justify-end h-full gap-1 group cursor-pointer traffic-bar-container" data-count="<?= $th['count'] ?>">
                            <div class="w-full max-w-[28px] mx-auto bg-primary/80 hover:bg-primary rounded-t transition-all duration-300 relative" style="height: 0%"><div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[9px] py-0.5 px-1.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10"><?= $th['count'] ?></div></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="flex gap-1 pl-8 mt-1" id="traffic-x-labels">
                        <?php foreach ($trafficHours as $th): ?>
                        <div class="flex-1 text-center text-[9px] text-slate-400 font-medium truncate"><?= $th['label'] ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Dashboard Drill-Down Modal -->
<div id="dash-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="absolute inset-4 md:inset-y-8 md:inset-x-auto md:left-1/2 md:-translate-x-1/2 md:w-full md:max-w-4xl bg-surface-light dark:bg-surface-dark rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div id="modal-icon" class="size-9 rounded-lg flex items-center justify-center"></div>
                <div>
                    <h3 id="modal-title" class="text-base font-bold text-slate-900 dark:text-white"></h3>
                    <p id="modal-subtitle" class="text-xs text-slate-500"></p>
                </div>
            </div>
            <button onclick="closeModal()" class="size-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div id="modal-body" class="flex-1 overflow-y-auto p-6">
            <div class="flex items-center justify-center py-12">
                <div class="flex items-center gap-3 text-slate-400">
                    <svg class="animate-spin size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span class="text-sm font-medium">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const BASE = '<?= base_url() ?>';
function esc(s) { const d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }
let criticalAlertQueue = [];

function parseCriticalAlertsJson() {
    const el = document.getElementById('critical-alerts-json');
    if (!el) return;
    try {
        const raw = JSON.parse(el.textContent || '[]');
        criticalAlertQueue = Array.isArray(raw) ? raw : [];
    } catch (e) {
        criticalAlertQueue = [];
    }
    el.remove();
}

function buildCriticalAlertCardHtml(a) {
    const sev = esc((String(a.severity || '')).charAt(0).toUpperCase() + String(a.severity || '').slice(1));
    const n = criticalAlertQueue.length;
    const queueLine = n > 1
        ? '<p class="text-red-300/90 text-xs font-medium mt-2">' + esc('1 of ' + n + ' — acknowledge to review the next alert') + '</p>'
        : '';
    let inner = '<div class="flex items-start gap-4 flex-1 min-w-0">';
    inner += '<div class="size-10 rounded-lg bg-red-700/50 flex items-center justify-center text-red-200 flex-shrink-0 mt-0.5"><span class="material-symbols-outlined fill-1">warning</span></div>';
    inner += '<div class="flex-1 min-w-0"><div class="flex items-center gap-3 mb-1.5 flex-wrap">';
    inner += '<h3 class="text-white font-bold text-base">Critical Security Alert</h3>';
    inner += '<span class="px-2 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full uppercase tracking-wider">' + sev + '</span></div>';
    inner += '<div class="grid grid-cols-1 sm:grid-cols-3 gap-x-8 gap-y-1 text-sm mb-2">';
    inner += '<div><span class="text-red-300 font-medium">INCIDENT TYPE</span><p class="text-white font-semibold break-words">' + esc(a.incident_type) + '</p></div>';
    inner += '<div><span class="text-red-300 font-medium">LOCATION</span><p class="text-white font-semibold break-words">' + esc(a.location) + '</p></div>';
    inner += '<div><span class="text-red-300 font-medium">TIME</span><p class="text-white font-semibold break-words">' + esc(a.time) + '</p></div></div>';
    if (a.description) inner += '<p class="text-red-200 text-sm break-words">' + esc(a.description) + '</p>';
    inner += queueLine + '</div></div>';
    inner += '<div class="flex items-center gap-2 flex-shrink-0">';
    inner += '<button type="button" onclick="openAlertFromBanner(' + Number(a.id) + ')" class="flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-500 border border-red-500 text-white text-sm font-bold rounded-lg transition-colors">';
    inner += '<span class="material-symbols-outlined text-[18px]">visibility</span> View Incident</button>';
    inner += '<button type="button" onclick="acknowledgeAlert(' + Number(a.id) + ')" class="flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-bold rounded-lg transition-colors backdrop-blur-sm">';
    inner += '<span class="material-symbols-outlined text-[18px]">check</span> Acknowledge</button></div>';
    return inner;
}

function renderCriticalAlertCard() {
    const content = document.getElementById('critical-alert-card-content');
    const activeW = document.getElementById('critical-alert-active-wrapper');
    const clearW = document.getElementById('critical-alert-all-clear-wrapper');
    if (!content || !activeW || !clearW) return;
    if (criticalAlertQueue.length === 0) {
        activeW.classList.add('hidden');
        clearW.classList.remove('hidden');
        content.innerHTML = '';
        return;
    }
    clearW.classList.add('hidden');
    activeW.classList.remove('hidden');
    content.innerHTML = buildCriticalAlertCardHtml(criticalAlertQueue[0]);
}

function applySecurityWidgetPayload(d) {
    if (!d || d.success === false) return;
    if (Array.isArray(d.criticalAlerts)) {
        criticalAlertQueue = d.criticalAlerts;
        renderCriticalAlertCard();
    }
    const setText = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = String(v); };
    if (d.accessDeniedCount !== undefined) setText('dash-widget-access-denied', d.accessDeniedCount);
    if (d.overstayCount !== undefined) setText('dash-widget-overstay', d.overstayCount);
    if (d.activeSecurityAlertCount !== undefined) setText('dash-widget-active-alerts', d.activeSecurityAlertCount);
}

function refreshDashboardWidgets() {
    return fetch(BASE + '/dashboard/widgetSnapshot', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(d => { applySecurityWidgetPayload(d); })
        .catch(() => {});
}

function initCriticalAlerts() {
    parseCriticalAlertsJson();
    renderCriticalAlertCard();
}

function clearAckAccessDeniedPrompt() {
    const host = document.getElementById('ack-access-denied-prompt');
    if (!host) return;
    host.className = 'hidden';
    host.innerHTML = '';
}

function showAckAccessDeniedPrompt() {
    const host = document.getElementById('ack-access-denied-prompt');
    if (!host) return;
    host.className = 'rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/80 px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3';
    host.innerHTML = '<p class="text-sm text-slate-700 dark:text-slate-300"><span class="font-semibold text-slate-900 dark:text-white">Alert acknowledged.</span> Open Access Denied Incidents when you are ready.</p>'
        + '<div class="flex flex-wrap items-center gap-2 shrink-0">'
        + '<button type="button" class="px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">Dismiss</button>'
        + '<button type="button" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-primary hover:bg-primary-dark text-white transition-colors">View Access Denied Incidents</button>'
        + '</div>';
    const btns = host.querySelectorAll('button');
    btns[0].addEventListener('click', clearAckAccessDeniedPrompt);
    btns[1].addEventListener('click', function () {
        clearAckAccessDeniedPrompt();
        openModal('accessDenied');
    });
}

function acknowledgeAlert(id) {
    fetch(BASE + '/dashboard/acknowledgeAlert', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'alert_id=' + id
    }).then(r => r.json()).then(d => {
        if (!d.success) return;
        showAckAccessDeniedPrompt();
        applySecurityWidgetPayload(d);
    });
}

function updateTrafficGraph() {
    const f = document.getElementById('traffic-from').value, t = document.getElementById('traffic-to').value;
    fetch(BASE + '/dashboard/trafficData?from=' + f + '&to=' + t).then(r => r.json()).then(d => {
        if (!d.success) return;
        const c = document.getElementById('traffic-chart'), l = document.getElementById('traffic-x-labels');
        c.querySelectorAll('.traffic-bar-container').forEach(e => e.remove()); l.innerHTML = '';
        const m = Math.max(1, ...d.data.map(x => x.count));
        d.data.forEach(x => {
            const p = (x.count / m) * 100, cn = document.createElement('div');
            cn.className = 'flex-1 flex flex-col items-center justify-end h-full gap-1 group cursor-pointer traffic-bar-container';
            cn.dataset.count = x.count;
            cn.innerHTML = '<div class="w-full max-w-[28px] mx-auto bg-primary/80 hover:bg-primary rounded-t transition-all duration-300 relative" style="height:' + p + '%"><div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[9px] py-0.5 px-1.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">' + x.count + '</div></div>';
            c.appendChild(cn);
            const lb = document.createElement('div');
            lb.className = 'flex-1 text-center text-[9px] text-slate-400 font-medium truncate';
            lb.textContent = x.label; l.appendChild(lb);
        });
        renderYAxis(m);
    });
}

document.getElementById('onsite-search')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#onsite-table tbody tr').forEach(r => { r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none'; });
});

function initVisitorStatusTabs() {
    const tabButtons = document.querySelectorAll('[data-visitor-tab]');
    const rows = Array.from(document.querySelectorAll('#visitors-table-body .visitor-row'));
    const showingRange = document.getElementById('visitors-showing-range');
    const totalCount = document.getElementById('visitors-total-count');
    if (!tabButtons.length || !rows.length || !showingRange || !totalCount) return;

    const updateTabStyles = (activeTab) => {
        tabButtons.forEach((btn) => {
            const isActive = btn.dataset.visitorTab === activeTab;
            const label = btn.querySelector('p');
            const badge = btn.querySelector('[data-tab-count]');

            btn.classList.toggle('border-primary', isActive);
            btn.classList.toggle('text-primary', isActive);
            btn.classList.toggle('border-transparent', !isActive);
            btn.classList.toggle('text-slate-500', !isActive);
            btn.classList.toggle('dark:text-slate-400', !isActive);

            if (label) {
                label.classList.toggle('font-bold', isActive);
                label.classList.toggle('font-medium', !isActive);
            }

            if (badge) {
                badge.classList.toggle('bg-primary/10', isActive);
                badge.classList.toggle('text-primary', isActive);
                badge.classList.toggle('bg-slate-100', !isActive);
                badge.classList.toggle('dark:bg-slate-800', !isActive);
                badge.classList.toggle('text-slate-500', !isActive);
            }
        });
    };

    const applyFilter = (activeTab) => {
        const tabTotal = activeTab === 'all'
            ? rows.length
            : rows.filter((row) => row.dataset.visitorStatus === activeTab).length;

        let visibleCount = 0;
        rows.forEach((row) => {
            const shouldShow = activeTab === 'all' || row.dataset.visitorStatus === activeTab;
            row.style.display = shouldShow ? '' : 'none';
            if (shouldShow) visibleCount++;
        });

        showingRange.textContent = visibleCount > 0 ? `1-${visibleCount}` : '0-0';
        totalCount.textContent = String(tabTotal);
        updateTabStyles(activeTab);
    };

    tabButtons.forEach((btn) => {
        btn.addEventListener('click', () => applyFilter(btn.dataset.visitorTab || 'all'));
    });
}

function renderYAxis(m) {
    const y = document.getElementById('traffic-y-axis'); if (!y) return; y.innerHTML = '';
    for (let i = 4; i >= 0; i--) { const s = document.createElement('span'); s.textContent = Math.round((m / 4) * i); y.appendChild(s); }
}

document.addEventListener('DOMContentLoaded', function () {
    initCriticalAlerts();
    initVisitorStatusTabs();
    const bars = document.querySelectorAll('.traffic-bar-container'); let mx = 1;
    bars.forEach(b => { const c = parseInt(b.dataset.count || 0); if (c > mx) mx = c; }); renderYAxis(mx);
    setTimeout(() => { bars.forEach(b => { const bar = b.querySelector('div'), c = parseInt(b.dataset.count || 0), p = mx > 0 ? (c / mx) * 100 : 0; if (bar) bar.style.height = Math.max(p, c > 0 ? 3 : 0) + '%'; }); }, 100);
});

/* ========== MODAL SYSTEM ========== */
const modalEl = document.getElementById('dash-modal');
const modalTitle = document.getElementById('modal-title');
const modalSubtitle = document.getElementById('modal-subtitle');
const modalIcon = document.getElementById('modal-icon');
const modalBody = document.getElementById('modal-body');
const LOADER = '<div class="flex items-center justify-center py-12"><div class="flex items-center gap-3 text-slate-400"><svg class="animate-spin size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span class="text-sm font-medium">Loading...</span></div></div>';
const EMPTY = (msg) => '<div class="flex flex-col items-center justify-center py-12 text-center"><span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600 mb-3">inbox</span><p class="text-slate-400 font-medium">' + msg + '</p></div>';

function fmtTime(dt) { if (!dt) return 'N/A'; const d = new Date(dt.replace(' ', 'T')); return d.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit', hour12: true}); }
function fmtDateTime(dt) { if (!dt) return 'N/A'; const d = new Date(dt.replace(' ', 'T')); return d.toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) + ' ' + d.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit', hour12: true}); }
function initials(n) { return (n || 'NA').substring(0, 2).toUpperCase(); }
function sevBadge(s) { const c = {critical:'bg-red-100 text-red-700',high:'bg-orange-100 text-orange-700',medium:'bg-amber-100 text-amber-700',low:'bg-slate-100 text-slate-600'}; return '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold ' + (c[s] || c.low) + '">' + esc((s||'').charAt(0).toUpperCase()+(s||'').slice(1)) + '</span>'; }

function openModal(type) {
    modalBody.innerHTML = LOADER;
    modalEl.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    const cfg = modalConfigs[type];
    if (!cfg) return;
    modalTitle.textContent = cfg.title;
    modalSubtitle.textContent = cfg.subtitle;
    modalIcon.className = 'size-9 rounded-lg flex items-center justify-center ' + cfg.iconCls;
    modalIcon.innerHTML = '<span class="material-symbols-outlined fill-1">' + cfg.icon + '</span>';
    fetch(BASE + cfg.url).then(r => r.json()).then(d => cfg.render(d)).catch(() => { modalBody.innerHTML = EMPTY('Failed to load data'); });
}

function closeModal() {
    modalEl.classList.add('hidden');
    document.body.style.overflow = '';
}

/* ── Dashboard visitor row actions ─────────────────────────────────────── */
function getRowInvitationId(btn) {
    return btn.closest('tr').dataset.invitationId;
}

function dashboardCheckIn(btn) {
    const invId = getRowInvitationId(btn);
    if (!invId) return;
    btn.disabled = true;
    btn.querySelector('span').textContent = 'hourglass_empty';
    fetch('<?= site_url('dashboard/quickCheckIn') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: 'invitation_id=' + encodeURIComponent(invId)
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) {
            btn.disabled = false;
            btn.querySelector('span').textContent = 'login';
            alert(d.message || 'Check-in failed');
            return;
        }
        // Update row status badge and swap action buttons without full reload
        const row = btn.closest('tr');
        row.dataset.visitorStatus = 'checkedin';
        const statusCell = row.querySelector('[data-status-badge]');
        if (statusCell) {
            statusCell.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
            statusCell.innerHTML = '<span class="size-1.5 rounded-full bg-green-500 inline-block"></span>On-Site';
        }
        const actionCell = row.querySelector('td:last-child');
        if (actionCell) {
            actionCell.innerHTML =
                '<button onclick="dashboardViewVisitor(this)" class="text-slate-400 hover:text-primary transition-colors p-1" title="View Details"><span class="material-symbols-outlined text-[20px]">visibility</span></button>' +
                '<button onclick="dashboardCheckOut(this)" class="text-slate-400 hover:text-red-600 transition-colors p-1" title="Check Out"><span class="material-symbols-outlined text-[20px]">logout</span></button>';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.querySelector('span').textContent = 'login';
        alert('Network error, please try again');
    });
}

function dashboardCheckOut(btn) {
    // Placeholder — will be implemented with full check-out flow later
    alert('Check-out flow coming soon.');
}

function dashboardViewVisitor(btn) {
    window.location.href = '<?= site_url('invitations') ?>';
}

function dashboardEditVisitor(btn) {
    window.location.href = '<?= site_url('invitations') ?>';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

function buildTable(headers, rows) {
    if (!rows.length) return '';
    let h = '<table class="w-full text-left border-collapse"><thead><tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">';
    headers.forEach(th => { h += '<th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">' + th + '</th>'; });
    h += '</tr></thead><tbody class="divide-y divide-slate-200 dark:divide-slate-700">';
    rows.forEach(tr => { h += '<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">'; tr.forEach(td => { h += '<td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">' + td + '</td>'; }); h += '</tr>'; });
    h += '</tbody></table>';
    return '<div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">' + h + '</div>';
}

function ackFromModal(id, btn) {
    btn.disabled = true; btn.innerHTML = '<svg class="animate-spin size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
    fetch(BASE + '/dashboard/acknowledgeAlert', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}, body: 'alert_id=' + id })
    .then(r => r.json()).then(d => {
        if (!d.success) { btn.disabled = false; btn.textContent = 'Acknowledge'; return; }
        const row = btn.closest('tr'); if (row) { row.style.transition = 'opacity .3s'; row.style.opacity = '0'; setTimeout(() => row.remove(), 300); }
        applySecurityWidgetPayload(d);
    });
}

const modalConfigs = {
    accessDenied: {
        title: 'Access Denied Incidents', subtitle: 'Acknowledged — last 24 hours',
        icon: 'gpp_bad', iconCls: 'bg-red-100 text-red-600',
        url: '/dashboard/accessDeniedData',
        render(d) {
            if (!d.success || !d.data.length) { modalBody.innerHTML = EMPTY('No acknowledged access denied incidents in the last 24 hours'); return; }
            const rows = d.data.map(a => [
                '<div class="flex items-center gap-2"><span class="material-symbols-outlined text-red-500 text-[16px]">block</span>' + esc(a.incident_type) + '</div>',
                sevBadge(a.severity),
                esc(a.visitor_name || 'Unknown'),
                esc(a.location || 'N/A'),
                fmtDateTime(a.created_at),
                a.is_acknowledged == 1
                    ? '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700"><span class="material-symbols-outlined text-[12px]">check_circle</span>Acknowledged</span>'
                    : '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700"><span class="material-symbols-outlined text-[12px]">error</span>Pending</span>',
            ]);
            modalBody.innerHTML = '<p class="text-sm text-slate-500 mb-4">' + d.data.length + ' incident' + (d.data.length !== 1 ? 's' : '') + ' found</p>' + buildTable(['Incident', 'Severity', 'Visitor', 'Location', 'Time', 'Status'], rows);
        }
    },
    overstay: {
        title: 'Visitor Overstay Alerts', subtitle: 'Visitors past scheduled window',
        icon: 'timer_off', iconCls: 'bg-amber-100 text-amber-600',
        url: '/dashboard/overstayData',
        render(d) {
            if (!d.success) { modalBody.innerHTML = EMPTY('Failed to load data'); return; }
            let html = '';
            if (d.physicalOverstays && d.physicalOverstays.length) {
                html += '<h4 class="text-sm font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2"><span class="material-symbols-outlined text-amber-500 text-[18px] fill-1">warning</span>Currently Overstaying (' + d.physicalOverstays.length + ')</h4>';
                const rows = d.physicalOverstays.map(v => {
                    const endTs = new Date(v.schedule_end.replace(' ', 'T')).getTime();
                    const overMin = Math.max(0, Math.floor((Date.now() - endTs) / 60000));
                    const overH = Math.floor(overMin / 60), overM = overMin % 60;
                    const overLabel = overH > 0 ? overH + 'h ' + overM + 'm' : overM + 'm';
                    return [
                        '<div class="flex items-center gap-2"><div class="size-7 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 text-[10px] font-bold">' + initials(v.visitor_name) + '</div>' + esc(v.visitor_name) + '</div>',
                        esc(v.host_name), fmtTime(v.check_in_time), fmtDateTime(v.schedule_end), esc(v.location),
                        '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700"><span class="material-symbols-outlined text-[12px]">schedule</span>+' + overLabel + '</span>'
                    ];
                });
                html += buildTable(['Visitor', 'Host', 'Check-in', 'Schedule End', 'Location', 'Overstay'], rows);
            }
            if (d.alertRows && d.alertRows.length) {
                html += '<h4 class="text-sm font-bold text-slate-900 dark:text-white mt-6 mb-3 flex items-center gap-2"><span class="material-symbols-outlined text-amber-500 text-[18px]">notifications_active</span>Overstay Alert Records (' + d.alertRows.length + ')</h4>';
                const rows = d.alertRows.map(a => [esc(a.incident_type), sevBadge(a.severity), esc(a.visitor_name || 'Unknown'), esc(a.location || 'N/A'), fmtDateTime(a.created_at)]);
                html += buildTable(['Incident', 'Severity', 'Visitor', 'Location', 'Time'], rows);
            }
            if (!html) html = EMPTY('No visitor overstay alerts at this time');
            modalBody.innerHTML = html;
        }
    },
    alertDetail: {
        title: 'Security Alert Detail', subtitle: 'Incident information',
        icon: 'warning', iconCls: 'bg-red-100 text-red-600',
        url: '',
        render(d) {
            if (!d.success || !d.data) { modalBody.innerHTML = EMPTY('Alert not found'); return; }
            const a = d.data;
            let html = '<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">';
            const fields = [['Incident Type', a.incident_type], ['Severity', null], ['Location', a.location || 'N/A'], ['Time', fmtDateTime(a.created_at)], ['Visitor Name', a.visitor_name || 'Unknown'], ['Status', null]];
            fields.forEach(([label, val]) => {
                html += '<div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50"><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">' + label + '</p>';
                if (label === 'Severity') html += sevBadge(a.severity);
                else if (label === 'Status') html += a.is_acknowledged == 1 ? '<span class="inline-flex items-center gap-1 text-sm font-medium text-green-600"><span class="material-symbols-outlined text-[16px] fill-1">check_circle</span>Acknowledged</span>' : '<span class="inline-flex items-center gap-1 text-sm font-medium text-red-600"><span class="material-symbols-outlined text-[16px] fill-1">error</span>Pending</span>';
                else html += '<p class="text-sm font-medium text-slate-900 dark:text-white">' + esc(val) + '</p>';
                html += '</div>';
            });
            html += '</div>';
            if (a.description) html += '<div class="mt-4 p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50"><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Description</p><p class="text-sm text-slate-700 dark:text-slate-300">' + esc(a.description) + '</p></div>';
            if (a.is_acknowledged == 1 && a.acknowledged_by_name) html += '<div class="mt-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800"><p class="text-sm text-green-700 dark:text-green-400 font-medium">Acknowledged by ' + esc(a.acknowledged_by_name) + ' on ' + fmtDateTime(a.acknowledged_at) + '</p></div>';
            if (a.is_acknowledged == 0) html += '<div class="mt-4 flex justify-end"><button onclick="ackFromDetail(' + a.id + ', this)" class="flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-lg transition-colors"><span class="material-symbols-outlined text-[18px]">check</span>Acknowledge</button></div>';
            modalBody.innerHTML = html;
        }
    },
    expectedToday: {
        title: 'Expected Today', subtitle: new Date().toLocaleDateString('en-US', {month: 'long', day: 'numeric', year: 'numeric'}),
        icon: 'calendar_month', iconCls: 'bg-indigo-100 text-indigo-600',
        url: '/dashboard/expectedTodayData',
        render(d) {
            if (!d.success || !d.data.length) { modalBody.innerHTML = EMPTY('No visitors expected today'); return; }
            const rows = d.data.map(v => {
                let status, cls;
                if (v.check_in_time) { if (v.check_out_time) { status = 'Checked Out'; cls = 'bg-slate-100 text-slate-600'; } else { status = 'On-Site'; cls = 'bg-green-100 text-green-700'; } } else { status = 'Pre-Arrival'; cls = 'bg-amber-100 text-amber-700'; }
                return [
                    '<div class="flex items-center gap-2"><div class="size-7 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-[10px] font-bold">' + initials(v.full_name) + '</div><div><p class="font-medium text-slate-900 dark:text-white">' + esc(v.full_name || 'N/A') + '</p></div></div>',
                    esc(v.company || 'N/A'), esc(v.host_name || 'N/A'),
                    fmtTime(v.date_from) + ' - ' + fmtTime(v.date_to),
                    '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold ' + cls + '">' + status + '</span>'
                ];
            });
            modalBody.innerHTML = '<p class="text-sm text-slate-500 mb-4">' + d.data.length + ' visitor' + (d.data.length !== 1 ? 's' : '') + ' expected</p>' + buildTable(['Visitor', 'Company', 'Host', 'Schedule', 'Status'], rows);
        }
    },
    onSite: {
        title: 'Currently On-Site', subtitle: 'Visitors currently on premises',
        icon: 'group', iconCls: 'bg-primary/10 text-primary',
        url: '/dashboard/onSiteData',
        render(d) {
            if (!d.success || !d.data.length) { modalBody.innerHTML = EMPTY('No visitors currently on-site'); return; }
            const rows = d.data.map(v => {
                const nm = v.visitor_name || v.visitor_email || '-';
                const avatarEl = '<div class="size-7 rounded-full bg-primary/10 flex items-center justify-center text-primary text-[10px] font-bold relative overflow-hidden">'
                    + initials(nm)
                    + (v.profile_photo_path ? '<img src="' + BASE + 'uploads/' + esc(v.profile_photo_path) + '" class="absolute inset-0 size-7 object-cover" alt="" onerror="this.remove()">' : '')
                    + '</div>';
                return [
                    '<div class="flex items-center gap-2">' + avatarEl + '<div><p class="font-medium text-slate-900 dark:text-white">' + esc(nm) + '</p><p class="text-[11px] text-slate-400">' + esc(v.contact || '') + '</p></div></div>',
                    esc(v.company || 'N/A'), esc(v.host_name), fmtTime(v.check_in_time), esc(v.location)
                ];
            });
            modalBody.innerHTML = '<p class="text-sm text-slate-500 mb-4">' + d.data.length + ' visitor' + (d.data.length !== 1 ? 's' : '') + ' on-site</p>' + buildTable(['Visitor', 'Company', 'Host', 'Check-in', 'Location'], rows);
        }
    },
    checkedOut: {
        title: 'Checked Out Today', subtitle: new Date().toLocaleDateString('en-US', {month: 'long', day: 'numeric', year: 'numeric'}),
        icon: 'logout', iconCls: 'bg-slate-200 text-slate-600',
        url: '/dashboard/checkedOutData',
        render(d) {
            if (!d.success || !d.data.length) { modalBody.innerHTML = EMPTY('No visitors checked out today'); return; }
            const rows = d.data.map(v => {
                let dur = '';
                if (v.check_in_time && v.check_out_time) {
                    const m = Math.floor((new Date(v.check_out_time.replace(' ','T')) - new Date(v.check_in_time.replace(' ','T'))) / 60000);
                    const h = Math.floor(m / 60), mm = m % 60;
                    dur = h > 0 ? h + 'h ' + mm + 'm' : mm + 'm';
                }
                return [
                    '<div class="flex items-center gap-2"><div class="size-7 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 text-[10px] font-bold">' + initials(v.visitor_name) + '</div>' + esc(v.visitor_name) + '</div>',
                    esc(v.company || 'N/A'), esc(v.host_name), fmtTime(v.check_in_time), fmtTime(v.check_out_time), dur, esc(v.location)
                ];
            });
            modalBody.innerHTML = '<p class="text-sm text-slate-500 mb-4">' + d.data.length + ' checked out today</p>' + buildTable(['Visitor', 'Company', 'Host', 'Check-in', 'Check-out', 'Duration', 'Location'], rows);
        }
    },
    recentActivity: {
        title: 'Recent Activity', subtitle: 'All system events — last 30 days',
        icon: 'history', iconCls: 'bg-primary/10 text-primary',
        url: '/dashboard/recentActivityData',
        render(d) {
            if (!d.success || !d.data.length) { modalBody.innerHTML = EMPTY('No activity in the last 30 days'); return; }
            const typeIcon = {
                created:        { icon: 'add_circle',   cls: 'text-amber-500'  },
                approved:       { icon: 'check_circle', cls: 'text-green-500'  },
                rejected:       { icon: 'cancel',       cls: 'text-red-500'    },
                check_in:       { icon: 'login',        cls: 'text-green-500'  },
                check_out:      { icon: 'logout',       cls: 'text-slate-400'  },
                door_access:    { icon: 'sensor_door',  cls: 'text-blue-500'   },
                security_alert: { icon: 'warning',      cls: 'text-red-500'    },
            };
            const typeBadge = {
                created:        'bg-amber-100 text-amber-700',
                approved:       'bg-green-100 text-green-700',
                rejected:       'bg-red-100 text-red-700',
                check_in:       'bg-green-100 text-green-700',
                check_out:      'bg-slate-100 text-slate-600',
                door_access:    'bg-blue-100 text-blue-700',
                security_alert: 'bg-red-100 text-red-700',
            };
            const rows = d.data.map(a => {
                const ti = typeIcon[a.type] || { icon: 'info', cls: 'text-slate-400' };
                const badgeCls = typeBadge[a.type] || 'bg-slate-100 text-slate-600';
                let desc = '';
                if (a.type === 'created')        desc = 'Invitation created for <strong>' + esc(a.name) + '</strong>';
                else if (a.type === 'approved')  desc = 'Invitation approved for <strong>' + esc(a.name) + '</strong>';
                else if (a.type === 'rejected')  desc = 'Invitation rejected for <strong>' + esc(a.name) + '</strong>';
                else if (a.type === 'check_in')  desc = '<strong>' + esc(a.name) + '</strong> checked in';
                else if (a.type === 'check_out') desc = '<strong>' + esc(a.name) + '</strong> checked out';
                else if (a.type === 'door_access') desc = '<strong>' + esc(a.name) + '</strong> ' + (a.extra === 'checkin' ? 'entered via ' : 'exited via ') + esc(a.actor);
                else desc = 'Alert: <strong>' + esc(a.extra) + '</strong>';
                return [
                    '<div class="flex items-center gap-2"><span class="material-symbols-outlined text-[16px] ' + ti.cls + '">' + ti.icon + '</span><span class="text-xs font-medium px-1.5 py-0.5 rounded-full ' + badgeCls + '">' + esc(a.label) + '</span></div>',
                    '<span class="text-sm">' + desc + '</span>',
                    esc(a.actor),
                    '<span class="text-xs text-slate-400" title="' + esc(a.time) + '">' + esc(a.time_ago) + '</span>',
                ];
            });
            modalBody.innerHTML = '<p class="text-sm text-slate-500 mb-4">' + d.data.length + ' event' + (d.data.length !== 1 ? 's' : '') + ' found</p>' + buildTable(['Type', 'Description', 'By / Location', 'When'], rows);
        }
    },
    activeAlerts: {
        title: 'Active Security Alerts', subtitle: 'Unacknowledged alerts',
        icon: 'shield', iconCls: 'bg-red-100 text-red-600',
        url: '/dashboard/activeAlertsData',
        render(d) {
            if (!d.success || !d.data.length) { modalBody.innerHTML = EMPTY('All security alerts have been acknowledged'); return; }
            const rows = d.data.map(a => [
                '<div class="flex items-center gap-2"><span class="material-symbols-outlined text-red-500 text-[16px] fill-1">warning</span>' + esc(a.incident_type) + '</div>',
                sevBadge(a.severity),
                esc(a.visitor_name || 'Unknown'),
                esc(a.location || 'N/A'),
                fmtDateTime(a.created_at),
                '<div class="flex gap-1"><button onclick="openAlertDetail(' + a.id + ')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold rounded transition-colors">View</button><button onclick="ackFromModal(' + a.id + ', this)" class="px-2 py-1 bg-primary hover:bg-primary-dark text-white text-[10px] font-bold rounded transition-colors">Acknowledge</button></div>'
            ]);
            modalBody.innerHTML = '<p class="text-sm text-slate-500 mb-4">' + d.data.length + ' active alert' + (d.data.length !== 1 ? 's' : '') + '</p>' + buildTable(['Incident', 'Severity', 'Visitor', 'Location', 'Time', 'Actions'], rows);
        }
    }
};

function openAlertDetail(id) {
    modalBody.innerHTML = LOADER;
    modalTitle.textContent = 'Security Alert #' + id;
    modalSubtitle.textContent = 'Incident detail';
    modalIcon.className = 'size-9 rounded-lg flex items-center justify-center bg-red-100 text-red-600';
    modalIcon.innerHTML = '<span class="material-symbols-outlined fill-1">warning</span>';
    fetch(BASE + '/dashboard/alertDetailData/' + id).then(r => r.json()).then(d => modalConfigs.alertDetail.render(d)).catch(() => { modalBody.innerHTML = EMPTY('Failed to load alert'); });
}

function openAlertFromBanner(id) {
    openAlertDetail(id);
    modalEl.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function ackFromDetail(id, btn) {
    btn.disabled = true; btn.textContent = 'Acknowledging...';
    fetch(BASE + '/dashboard/acknowledgeAlert', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}, body: 'alert_id=' + id })
    .then(r => r.json()).then(d => {
        openAlertDetail(id);
        if (d.success) applySecurityWidgetPayload(d);
    });
}
</script>
</body>
</html>
