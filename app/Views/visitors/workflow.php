<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
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
                        "primary-hover": "#0f6bd0",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                        "surface-dark": "#1c2630",
                        "surface-light": "#ffffff",
                        "border-dark": "#2d3b4b",
                        "border-light": "#e5e7eb",
                        "text-main-dark": "#ffffff",
                        "text-secondary-dark": "#92adc9",
                        "text-main-light": "#111827",
                        "text-secondary-light": "#6b7280",
                    },
                    fontFamily: {
                        "display": ["Montserrat", "sans-serif"],
                        "sans": ["Montserrat", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <!-- Blacklist dropdown function-->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main-light dark:text-text-main-dark font-display antialiased overflow-hidden">
<div class="flex h-screen w-full flex-col">
    <div class="flex flex-1 overflow-hidden">
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
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('visitors') ?>">
                        <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">group</span>
                        <p class="text-sm font-medium">Visitors List</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('logbook') ?>">
                        <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">menu_book</span>
                        <p class="text-sm font-medium">Visitor Logbook</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary group transition-colors" href="<?= base_url('workflow') ?>">
                        <span class="material-symbols-outlined text-[22px] font-medium fill-1 group-hover:scale-110 transition-transform">account_tree</span>
                        <p class="text-sm font-semibold">Visitor Workflow</p>
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
        <main class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark custom-scrollbar">
            <div class="px-4 md:px-10 py-6 max-w-[1440px] mx-auto w-full">
                <!-- Top Actions -->
                <div class="flex justify-end items-center gap-3 mb-4">
                    <button class="relative p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors rounded-full hover:bg-slate-100 dark:hover:bg-slate-800">
                        <span class="material-symbols-outlined text-[24px]">notifications</span>
                        <span class="absolute top-2 right-2.5 size-2 bg-red-500 rounded-full border border-white dark:border-slate-900"></span>
                    </button>
                    <a href="<?= base_url('auth/logout') ?>" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors rounded-full hover:bg-slate-100 dark:hover:bg-slate-800">
                        <span class="material-symbols-outlined text-[24px]">account_circle</span>
                    </a>
                </div>

                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-5 pb-6 border-b border-border-light dark:border-border-dark mb-6">
                    <div class="flex min-w-72 flex-col gap-2">
                        <h1 class="text-text-main-light dark:text-white text-3xl md:text-4xl font-black leading-tight tracking-[-0.033em]">Visitor Workflows</h1>
                        <p class="text-text-secondary-light dark:text-[#92adc9] text-base font-normal leading-normal max-w-2xl">Manage automated entry processes. Design logic flows for check-ins, security screenings, and host notifications.</p>
                    </div>
                    <a href="<?= base_url('workflow/create') ?>" class="flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-lg shadow-lg shadow-primary/20 transition-all">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span class="text-sm font-bold leading-normal tracking-[0.015em]">Create New Workflow</span>
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="flex flex-col gap-1 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-border-light dark:border-[#324d67] shadow-sm">
                        <div class="flex justify-between items-center">
                            <p class="text-text-secondary-light dark:text-[#92adc9] text-sm font-medium">Active Workflows</p>
                            <span class="material-symbols-outlined text-primary text-xl">play_circle</span>
                        </div>
                        <p class="text-text-main-light dark:text-white text-2xl font-bold leading-tight mt-1"><?= esc($stats['active_workflows']) ?></p>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="material-symbols-outlined text-[#0bda5b] text-sm">trending_up</span>
                            <p class="text-[#0bda5b] text-xs font-medium"><?= esc($stats['active_change']) ?></p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-border-light dark:border-[#324d67] shadow-sm">
                        <div class="flex justify-between items-center">
                            <p class="text-text-secondary-light dark:text-[#92adc9] text-sm font-medium">Total Visitors</p>
                            <span class="material-symbols-outlined text-primary text-xl">group</span>
                        </div>
                        <p class="text-text-main-light dark:text-white text-2xl font-bold leading-tight mt-1"><?= esc($stats['total_visitors']) ?></p>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="material-symbols-outlined text-[#0bda5b] text-sm">trending_up</span>
                            <p class="text-[#0bda5b] text-xs font-medium"><?= esc($stats['visitors_change']) ?></p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-border-light dark:border-[#324d67] shadow-sm">
                        <div class="flex justify-between items-center">
                            <p class="text-text-secondary-light dark:text-[#92adc9] text-sm font-medium">Avg. Process Time</p>
                            <span class="material-symbols-outlined text-primary text-xl">timer</span>
                        </div>
                        <p class="text-text-main-light dark:text-white text-2xl font-bold leading-tight mt-1"><?= esc($stats['avg_time']) ?></p>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="material-symbols-outlined text-[#0bda5b] text-sm">trending_down</span>
                            <p class="text-[#0bda5b] text-xs font-medium"><?= esc($stats['time_change']) ?></p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-border-light dark:border-[#324d67] shadow-sm">
                        <div class="flex justify-between items-center">
                            <p class="text-text-secondary-light dark:text-[#92adc9] text-sm font-medium">Security Alerts</p>
                            <span class="material-symbols-outlined text-[#fa6238] text-xl">warning</span>
                        </div>
                        <p class="text-text-main-light dark:text-white text-2xl font-bold leading-tight mt-1"><?= esc($stats['alerts']) ?></p>
                        <p class="text-text-secondary-light dark:text-[#92adc9] text-xs font-medium mt-1"><?= esc($stats['alerts_text']) ?></p>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="flex flex-col lg:flex-row gap-4 mb-6">
                    <div class="flex-1">
                        <label class="flex flex-col h-11 w-full">
                            <div class="flex w-full flex-1 items-stretch rounded-lg h-full bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark focus-within:ring-2 focus-within:ring-primary focus-within:border-primary transition-all shadow-sm">
                                <div class="text-text-secondary-light dark:text-[#92adc9] flex items-center justify-center pl-4 pr-2">
                                    <span class="material-symbols-outlined">search</span>
                                </div>
                                <input class="flex w-full min-w-0 flex-1 bg-transparent text-text-main-light dark:text-white focus:outline-0 placeholder:text-text-secondary-light dark:placeholder:text-[#586e82] text-sm font-normal" placeholder="Search workflows by name, trigger, or ID..."/>
                            </div>
                        </label>
                    </div>
                    <div class="flex gap-2 overflow-x-auto no-scrollbar items-center">
                        <button class="whitespace-nowrap flex h-9 px-4 items-center justify-center rounded-lg bg-primary text-white text-sm font-medium transition-colors shadow-md shadow-primary/20">
                            All Workflows
                        </button>
                        <button class="whitespace-nowrap flex h-9 px-4 items-center justify-center rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-secondary-light dark:text-text-secondary-dark hover:text-primary hover:border-primary dark:hover:border-primary text-sm font-medium transition-all">
                            Active
                        </button>
                        <button class="whitespace-nowrap flex h-9 px-4 items-center justify-center rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-secondary-light dark:text-text-secondary-dark hover:text-primary hover:border-primary dark:hover:border-primary text-sm font-medium transition-all">
                            Draft
                        </button>
                        <button class="whitespace-nowrap flex h-9 px-4 items-center justify-center rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-secondary-light dark:text-text-secondary-dark hover:text-primary hover:border-primary dark:hover:border-primary text-sm font-medium transition-all">
                            Archived
                        </button>
                        <button class="whitespace-nowrap flex h-9 px-4 items-center justify-center rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-secondary-light dark:text-text-secondary-dark hover:text-primary hover:border-primary dark:hover:border-primary text-sm font-medium transition-all">
                            Security Clearance
                        </button>
                    </div>
                </div>

                <!-- Workflows Table -->
                <div class="flex flex-1 flex-col bg-surface-light dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark shadow-sm overflow-hidden">
                    <div class="grid grid-cols-12 gap-4 p-4 border-b border-border-light dark:border-border-dark bg-gray-50 dark:bg-[#1a242f] text-xs font-semibold text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider">
                        <div class="col-span-4 flex items-center">Workflow Name</div>
                        <div class="col-span-2 flex items-center">Trigger</div>
                        <div class="col-span-2 flex items-center">Steps</div>
                        <div class="col-span-2 flex items-center">Status</div>
                        <div class="col-span-2 flex items-center justify-end">Last Modified</div>
                    </div>
                    <div class="flex flex-col">
                        <?php foreach ($workflows as $workflow): ?>
                        <div class="group grid grid-cols-12 gap-4 p-4 border-b border-border-light dark:border-border-dark hover:bg-gray-50 dark:hover:bg-[#1f2b38] transition-colors items-center <?= $workflow['status'] === 'Archived' ? 'opacity-60' : '' ?>">
                            <div class="col-span-4 flex items-center gap-3">
                                <div class="size-10 rounded bg-<?= $workflow['icon_color'] === 'primary' ? 'primary' : ($workflow['icon_color'] === 'purple' ? 'purple-500' : ($workflow['icon_color'] === 'orange' ? 'orange-500' : ($workflow['icon_color'] === 'red' ? 'red-500' : 'gray-500'))) ?>/10 flex items-center justify-center text-<?= $workflow['icon_color'] === 'primary' ? 'primary' : ($workflow['icon_color'] === 'purple' ? 'purple-500' : ($workflow['icon_color'] === 'orange' ? 'orange-500' : ($workflow['icon_color'] === 'red' ? 'red-500' : 'gray-500'))) ?> shrink-0">
                                    <span class="material-symbols-outlined"><?= esc($workflow['icon']) ?></span>
                                </div>
                                <div>
                                    <p class="text-text-main-light dark:text-white font-medium text-sm"><?= esc($workflow['name']) ?></p>
                                    <p class="text-text-secondary-light dark:text-text-secondary-dark text-xs">ID: <?= esc($workflow['id']) ?></p>
                                </div>
                            </div>
                            <div class="col-span-2 text-text-main-light dark:text-gray-300 text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-xs"><?= esc($workflow['trigger_icon']) ?></span> <?= esc($workflow['trigger']) ?>
                            </div>
                            <div class="col-span-2">
                                <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 dark:bg-[#233648] text-text-secondary-light dark:text-gray-300 text-xs font-medium"><?= esc($workflow['steps']) ?> Steps</span>
                            </div>
                            <div class="col-span-2">
                                <?php if ($workflow['status_class'] === 'green'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-500/10 text-green-600 dark:text-green-400 text-xs font-bold border border-green-500/20">
                                    <span class="size-1.5 rounded-full bg-green-500"></span> <?= esc($workflow['status']) ?>
                                </span>
                                <?php elseif ($workflow['status_class'] === 'gray'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-500/10 text-gray-600 dark:text-gray-400 text-xs font-bold border border-gray-500/20">
                                    <span class="size-1.5 rounded-full bg-gray-400"></span> <?= esc($workflow['status']) ?>
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-bold border border-red-500/20">
                                    <span class="size-1.5 rounded-full bg-red-500"></span> <?= esc($workflow['status']) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <div class="col-span-2 flex items-center justify-end gap-2">
                                <span class="text-text-secondary-light dark:text-text-secondary-dark text-sm"><?= esc($workflow['modified']) ?></span>
                                <button class="size-8 rounded hover:bg-gray-200 dark:hover:bg-[#233648] flex items-center justify-center text-text-secondary-light dark:text-gray-400 transition-colors">
                                    <span class="material-symbols-outlined">more_vert</span>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="flex items-center justify-between p-4 border-t border-border-light dark:border-border-dark bg-gray-50 dark:bg-[#1a242f]">
                        <p class="text-xs text-text-secondary-light dark:text-text-secondary-dark">Showing 1-5 of 12 workflows</p>
                        <div class="flex gap-2">
                            <button class="px-3 py-1 rounded border border-border-light dark:border-border-dark text-xs font-medium text-text-main-light dark:text-text-secondary-dark hover:bg-gray-200 dark:hover:bg-[#233648] disabled:opacity-50" disabled>Previous</button>
                            <button class="px-3 py-1 rounded border border-border-light dark:border-border-dark text-xs font-medium text-text-main-light dark:text-text-secondary-dark hover:bg-gray-200 dark:hover:bg-[#233648]">Next</button>
                        </div>
                    </div>
                </div>

                <!-- Quick Preview -->
                <div class="mt-8">
                    <h3 class="text-text-main-light dark:text-white text-lg font-bold mb-4">Quick Preview: Standard Visitor Check-in</h3>
                    <div class="w-full h-64 bg-surface-light dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark relative overflow-hidden flex items-center justify-center">
                        <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05]" style="background-image: radial-gradient(#92adc9 1px, transparent 1px); background-size: 20px 20px;"></div>
                        <div class="flex items-center gap-4 relative z-10">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 rounded-full bg-surface-light dark:bg-[#233648] border-2 border-primary flex items-center justify-center shadow-lg z-10">
                                    <span class="material-symbols-outlined text-primary">qr_code_scanner</span>
                                </div>
                                <span class="text-xs font-medium text-text-secondary-light dark:text-[#92adc9]">Scan QR</span>
                            </div>
                            <div class="w-16 h-0.5 bg-border-light dark:bg-[#324d67]"></div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 rounded-lg bg-surface-light dark:bg-[#233648] border border-border-light dark:border-[#324d67] flex items-center justify-center shadow-lg z-10">
                                    <span class="material-symbols-outlined text-text-main-light dark:text-white">edit_document</span>
                                </div>
                                <span class="text-xs font-medium text-text-secondary-light dark:text-[#92adc9]">Sign NDA</span>
                            </div>
                            <div class="w-16 h-0.5 bg-border-light dark:bg-[#324d67]"></div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 rotate-45 bg-surface-light dark:bg-[#233648] border border-orange-500/50 flex items-center justify-center shadow-lg z-10">
                                    <span class="material-symbols-outlined text-orange-500 -rotate-45">question_mark</span>
                                </div>
                                <span class="text-xs font-medium text-text-secondary-light dark:text-[#92adc9]">Is VIP?</span>
                            </div>
                            <div class="relative w-16 h-16 flex items-center">
                                <div class="absolute top-[32px] left-0 w-8 h-0.5 bg-border-light dark:bg-[#324d67]"></div>
                                <div class="absolute top-[8px] left-8 w-8 h-[25px] border-l-2 border-t-2 border-border-light dark:border-[#324d67] rounded-tl-lg"></div>
                                <div class="absolute top-[32px] left-8 w-8 h-[25px] border-l-2 border-b-2 border-border-light dark:border-[#324d67] rounded-bl-lg"></div>
                            </div>
                            <div class="flex flex-col gap-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-10 h-10 rounded-lg bg-surface-light dark:bg-[#233648] border border-border-light dark:border-[#324d67] flex items-center justify-center shadow-lg">
                                        <span class="material-symbols-outlined text-sm text-text-main-light dark:text-white">notifications_active</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-10 h-10 rounded-lg bg-surface-light dark:bg-[#233648] border border-border-light dark:border-[#324d67] flex items-center justify-center shadow-lg">
                                        <span class="material-symbols-outlined text-sm text-text-main-light dark:text-white">print</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
