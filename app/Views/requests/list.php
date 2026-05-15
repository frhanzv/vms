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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#111827",
                    },
                    fontFamily: {
                        "display": ["Montserrat", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <!-- Blacklist dropdown function-->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-gray-800 dark:text-white font-display h-screen flex overflow-hidden">
<div class="flex h-screen w-full">
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
                        <?php if (client_feature_enabled('invitations')): ?>
                        <a href="<?= base_url('invitations') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'invitations') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'invitations') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            Invitations
                        </a>
                        <?php endif; ?>
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
                <?php if (client_feature_enabled('staff_pass')): ?>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isStaff ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('staffs') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">badge</span>
                    <p class="text-sm font-medium">Staff Pass List</p>
                </a>
                <?php endif; ?>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isWorkflow ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('workflow') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">account_tree</span>
                    <p class="text-sm font-medium">Visitor Workflow</p>
                </a>
                <?php if (client_feature_enabled('blacklist')): ?>
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
                <?php endif; ?>
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

    <div class="flex-1 flex h-full overflow-hidden">
        <main class="flex-1 flex flex-col h-full overflow-hidden relative bg-background-light dark:bg-background-dark">
            <!-- Header section -->
            <div class="px-8 pt-8 pb-6 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-slate-900 shadow-sm z-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-primary/10 text-primary p-2 rounded-lg">
                        <span class="material-symbols-outlined text-2xl">recent_actors</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Visitor Pass Requests</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">Manage and review pending visitor approvals</p>
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <div class="relative w-full md:w-96 group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary transition-colors">
                                <span class="material-symbols-outlined text-lg">search</span>
                            </div>
                            <input type="text" placeholder="Search IC, Passport or Name..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white dark:focus:bg-slate-900 outline-none transition-all shadow-sm">
                        </div>
                        <button class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors shadow-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">filter_list</span>
                            Filter
                        </button>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                        <div class="flex items-center gap-2 bg-gray-50 dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700">
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sort:</span>
                            <select class="border-none text-sm text-gray-700 dark:text-gray-300 font-semibold focus:ring-0 cursor-pointer bg-transparent py-1 pr-6 hover:text-primary transition-colors">
                                <option>Newest First</option>
                                <option>Oldest First</option>
                            </select>
                        </div>
                        <button id="batch-approve-btn" class="bg-primary text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-primary/30 hover:bg-blue-600 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            Batch Approve
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table section -->
            <div class="flex-1 overflow-auto p-8">
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left whitespace-nowrap">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-200 dark:border-gray-800">
                                    <th class="py-4 px-6 w-12">
                                        <div class="flex items-center justify-center">
                                            <input type="checkbox" id="select-all-requests" class="rounded-md border-gray-300 dark:border-gray-600 text-primary focus:ring-primary focus:ring-offset-0 cursor-pointer w-5 h-5 transition-all">
                                        </div>
                                    </th>
                                    <th class="py-4 px-4 font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">No</th>
                                    <th class="py-4 px-4 font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Submitted</th>
                                    <th class="py-4 px-4 font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Visitor Info</th>
                                    <th class="py-4 px-4 font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID Document</th>
                                    <th class="py-4 px-4 font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pass Type</th>
                                    <th class="py-4 px-4 font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="py-4 px-4 font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Purpose</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <?php if (empty($requestsList)): ?>
                                <tr>
                                    <td colspan="8" class="py-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <div class="bg-gray-50 dark:bg-slate-800 rounded-full p-4 mb-3">
                                                <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">inbox</span>
                                            </div>
                                            <p class="text-base font-semibold text-gray-900 dark:text-white">All caught up!</p>
                                            <p class="text-sm mt-1">No pending requests available for review.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($requestsList as $req): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/80 transition-colors group cursor-pointer">
                                        <td class="py-4 px-6">
                                            <div class="flex items-center justify-center">
                                                <input type="checkbox" name="request_batch[]" value="<?= (int) $req['id'] ?>" class="request-select-cb rounded-md border-gray-300 dark:border-gray-600 text-primary focus:ring-primary cursor-pointer w-5 h-5 transition-all">
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-sm font-semibold text-gray-400">#<?= esc($req['no']) ?></td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-[16px] text-gray-400">calendar_today</span>
                                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300"><?= esc($req['date']) ?></span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="size-9 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">
                                                    <?= strtoupper(substr(esc($req['name']), 0, 2)) ?>
                                                </div>
                                                <span class="text-sm font-bold text-gray-900 dark:text-white"><?= esc($req['name']) ?></span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-gray-300 rounded-lg border border-gray-200 dark:border-gray-700 text-xs font-mono font-medium">
                                                <span class="material-symbols-outlined text-[14px]">badge</span>
                                                <?= esc($req['ic_passport']) ?>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-sm font-medium text-gray-600 dark:text-gray-300"><?= esc($req['type']) ?></td>
                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                <?= esc($req['status']) ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-[200px]" title="<?= esc($req['reason']) ?>">
                                                <?= esc($req['reason']) ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination footer -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-slate-800/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">Showing <span class="font-bold text-gray-900 dark:text-white"><?= count($requestsList) ?></span> requests</span>
                        <div class="flex items-center gap-2">
                            <button class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-gray-600 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-50">
                                <span class="material-symbols-outlined text-sm">chevron_left</span>
                            </button>
                            <button class="w-8 h-8 rounded-lg bg-primary text-white text-sm font-bold shadow-sm flex items-center justify-center">1</button>
                            <button class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-gray-600 hover:bg-white dark:hover:bg-slate-700 transition-colors disabled:opacity-50">
                                <span class="material-symbols-outlined text-sm">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>


<script>
// Modern Alert/Confirm Modal Functions
let confirmCallback = null;

function showAlert(title, message, type = 'info') {
    const modal = document.getElementById('alertModal');
    const iconEl = document.getElementById('alertIcon');
    const titleEl = document.getElementById('alertTitle');
    const messageEl = document.getElementById('alertMessage');
    
    // Set icon and colors based on type
    if (type === 'success') {
        iconEl.className = 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 p-3 rounded-full';
        iconEl.querySelector('span').textContent = 'check_circle';
    } else if (type === 'error') {
        iconEl.className = 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 p-3 rounded-full';
        iconEl.querySelector('span').textContent = 'error';
    } else if (type === 'warning') {
        iconEl.className = 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 p-3 rounded-full';
        iconEl.querySelector('span').textContent = 'warning';
    } else {
        iconEl.className = 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 p-3 rounded-full';
        iconEl.querySelector('span').textContent = 'info';
    }
    
    titleEl.textContent = title;
    messageEl.textContent = message;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAlertModal() {
    document.getElementById('alertModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function showConfirm(title, message, type = 'info', callback = null, showRejectReason = false) {
    const modal = document.getElementById('confirmModal');
    const iconEl = document.getElementById('confirmIcon');
    const titleEl = document.getElementById('confirmTitle');
    const messageEl = document.getElementById('confirmMessage');
    const actionBtn = document.getElementById('confirmActionBtn');
    const actionText = document.getElementById('confirmActionText');
    const rejectContainer = document.getElementById('rejectReasonContainer');
    const rejectInput = document.getElementById('rejectReasonInput');
    
    confirmCallback = callback;
    
    // Set icon and colors based on type
    if (type === 'approve') {
        iconEl.className = 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 p-3 rounded-full';
        iconEl.querySelector('span').textContent = 'check_circle';
        actionBtn.className = 'flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2';
        actionText.textContent = 'Approve';
    } else if (type === 'reject') {
        iconEl.className = 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 p-3 rounded-full';
        iconEl.querySelector('span').textContent = 'cancel';
        actionBtn.className = 'flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2';
        actionText.textContent = 'Reject';
    } else {
        iconEl.className = 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 p-3 rounded-full';
        iconEl.querySelector('span').textContent = 'help';
        actionBtn.className = 'flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2';
        actionText.textContent = 'Confirm';
    }
    
    titleEl.textContent = title;
    messageEl.textContent = message;
    
    // Show/hide reject reason input
    if (showRejectReason) {
        rejectContainer.classList.remove('hidden');
        rejectInput.value = '';
    } else {
        rejectContainer.classList.add('hidden');
    }
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    confirmCallback = null;
}

function handleConfirmAction() {
    if (confirmCallback) {
        confirmCallback();
    }
    closeConfirmModal();
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAlertModal();
        closeConfirmModal();
    }
});

// Close modals on backdrop click
document.getElementById('alertModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAlertModal();
    }
});

document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});

(function initBatchApprove() {
    const selectAll = document.getElementById('select-all-requests');
    const batchBtn = document.getElementById('batch-approve-btn');
    const listCbs = () => Array.from(document.querySelectorAll('.request-select-cb'));

    function syncSelectAllState() {
        if (!selectAll) return;
        const boxes = listCbs();
        if (boxes.length === 0) return;
        const allOn = boxes.every(function (b) { return b.checked; });
        const noneOn = boxes.every(function (b) { return !b.checked; });
        selectAll.checked = allOn;
        selectAll.indeterminate = !allOn && !noneOn;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            listCbs().forEach(function (cb) { cb.checked = selectAll.checked; });
            selectAll.indeterminate = false;
        });
    }

    listCbs().forEach(function (cb) {
        cb.addEventListener('change', syncSelectAllState);
    });
    syncSelectAllState();

    if (batchBtn) {
        batchBtn.addEventListener('click', function () {
            const ids = listCbs().filter(function (cb) { return cb.checked; }).map(function (cb) { return parseInt(cb.value, 10); });
            if (ids.length === 0) {
                showAlert('No selection', 'Select at least one request in the queue, then click Approve.', 'warning');
                return;
            }
            showConfirm(
                'Batch approve',
                'Approve ' + ids.length + ' selected request(s)?',
                'approve',
                function () {
                    fetch('<?= base_url('requests/batchApprove') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ ids: ids })
                    })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (data.success) {
                                showAlert('Success', data.message || 'Requests approved', 'success');
                                setTimeout(function () { location.reload(); }, 1500);
                            } else {
                                showAlert('Could not approve', data.message || 'Batch approve failed', 'error');
                            }
                        })
                        .catch(function (err) {
                            showAlert('Error', err.message || 'Request failed', 'error');
                        });
                }
            );
        });
    }
})();

</script>
</body>
</html>
