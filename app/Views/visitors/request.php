<!DOCTYPE html>
<?php
$current = service('uri')->getPath();
$isDashboard = ($current === '' || $current === 'dashboard');
$isStaff = str_contains($current, 'staffs') || str_contains($current, 'staff-pass-request');
$isWorkflow = str_contains($current, 'workflow');
$isConfig = str_contains($current, 'config');
$isSettings = str_contains($current, 'settings');
?>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $pageTitle ?? 'Visitor Pass Request - SafeG' ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "primary-hover": "#0f6ac6",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1a2632",
                        "text-main": "#0d141b",
                        "text-sub": "#4c739a",
                        "border-color": "#e7edf3",
                    },
                    fontFamily: {
                        "sans": ["Montserrat", "sans-serif"],
                        "display": ["Montserrat", "sans-serif"],
                        "brand": ["Montserrat", "sans-serif"],
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <!-- Blacklist dropdown function-->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>    

    <style>
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cfdbe7;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #4c739a;
        }
        /* Hide native calendar picker icon */
        input[type="datetime-local"]::-webkit-calendar-picker-indicator,
        input[type="date"]::-webkit-calendar-picker-indicator {
            display: none;
            -webkit-appearance: none;
            opacity: 0;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main dark:text-white font-brand antialiased h-screen flex overflow-hidden transition-colors duration-200">
    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 border-r border-border-color dark:border-gray-800 bg-surface-light dark:bg-surface-dark flex flex-col p-4 hidden md:flex h-full overflow-hidden">
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

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8">
        <div class="max-w-[960px] mx-auto">
            <!-- Page Header -->
            <div class="mb-8 space-y-2">
                <h1 class="text-3xl sm:text-4xl font-black text-text-main dark:text-white font-brand tracking-tight">Visitor Pass Request</h1>
                <p class="text-text-sub dark:text-gray-400 text-lg max-w-2xl font-brand">
                    Submit a new visitor pass request for walk-in visitors.
                </p>
            </div>

            <form action="<?= base_url('visitor-pass-request/store') ?>" method="post" enctype="multipart/form-data" class="space-y-8">
                <?= csrf_field() ?>

                <!-- Visit Information -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">business</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Visit Information</h2>
                            <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Where and when are you visiting?</p>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Company Visiting <span class="text-red-500">*</span></label>
                            <div class="bg-background-light dark:bg-background-dark p-4 rounded-lg border border-border-color dark:border-gray-700">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <?php 
                                $locations = [
                                    'ADMIN B', 'ADMIN D', 'AMPANG KL', 'ANNEXE BUILDING',
                                    'CFS', 'COMMON WAREHOUSE', 'EAST WHARF', 'EPIC SOLAR',
                                    'KPK GATE', 'KSB PHASE 2 GATE', 'KTSB K.TRG', 'KUALA TERENGGANU',
                                    'LCB', 'OPERATION PHASE 1', 'PHASE 1', 'PHASE 3',
                                    'PHASE 4', 'SUKMA SAMUDERA', 'TELUK KALONG', 'WH27',
                                    'WHSET WHARF', 'WORKSHOP MAINTENANCE', 'WORKSHOP PHASE 2'
                                ];
                                foreach ($locations as $location): 
                                ?>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="<?= $location ?>" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm"><?= $location ?></span>
                                </label>
                                <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Date of Visit -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 overflow-hidden">
                    <div class="flex items-center justify-between px-6 sm:px-8 py-4 border-b border-border-color dark:border-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <span class="material-symbols-outlined">event</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Date of Visit</h2>
                                <p class="text-sm text-text-sub dark:text-gray-400 font-brand">When will the visit occur?</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="addDateVisit()" class="text-green-600 hover:text-green-700 transition-colors p-1.5 rounded-full hover:bg-green-50 dark:hover:bg-green-900/20" title="Add Date">
                                <span class="material-symbols-outlined text-2xl">add_circle</span>
                            </button>
                            <button type="button" onclick="removeDateVisit()" class="text-red-600 hover:text-red-700 transition-colors p-1.5 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove Date">
                                <span class="material-symbols-outlined text-2xl">remove_circle</span>
                            </button>
                        </div>
                    </div>
                    <div id="dateVisitContainer" class="p-6 sm:p-8 flex flex-col gap-6">
                        <div class="date-visit-item bg-background-light dark:bg-background-dark/50 rounded-lg p-4 border border-border-color dark:border-gray-700">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-text-main dark:text-white font-brand">Date Visit 1</h4>
                                <button type="button" onclick="removeSpecificDateVisit(this)" class="text-red-600 hover:text-red-700 transition-colors p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Delete this date visit">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date From <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input name="dates[0][date_from]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="dd/mm/yyyy hh:mm" type="datetime-local" required/>
                                        <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub cursor-pointer" onclick="this.previousElementSibling.showPicker()">calendar_month</span>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date To <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input name="dates[0][date_to]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="dd/mm/yyyy hh:mm" type="datetime-local" required/>
                                        <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub cursor-pointer" onclick="this.previousElementSibling.showPicker()">event</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Details of Visit -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 dark:text-purple-400">
                            <span class="material-symbols-outlined">badge</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Details of Visit</h2>
                            <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Host and purpose details.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Staff ID Of Person Visited</label>
                            <input name="staff_id" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Contact No Of Person Visited</label>
                            <input name="host_contact" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Name Of Company Visited</label>
                            <input name="company_visited" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Reason</label>
                            <input name="visit_reason" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                    </div>
                </section>

                <!-- Person Details -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600 dark:text-green-400">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Person Details</h2>
                                <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Visitor identification information.</p>
                            </div>
                        </div>
                        <button type="button" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold uppercase shadow-lg transition-all flex items-center gap-2 font-brand">
                            <span class="material-symbols-outlined text-lg">credit_card</span>
                            Read MyKad
                        </button>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Resident <span class="text-red-500">*</span></label>
                            <select name="resident" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" required>
                                <option value="">Select...</option>
                                <option value="LOCAL">LOCAL</option>
                                <option value="FOREIGN">FOREIGN</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">IC Number <span class="text-red-500">*</span></label>
                            <input name="ic_number" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="Enter IC / Passport Number" type="text" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date of Birth <span class="text-red-500">*</span></label>
                            <input name="date_of_birth" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="date" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Sex <span class="text-red-500">*</span></label>
                            <select name="sex" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" required>
                                <option value="">Select...</option>
                                <option value="MALE">MALE</option>
                                <option value="FEMALE">FEMALE</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Full Name <span class="text-red-500">*</span></label>
                            <input name="full_name" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="Full name as per ID" type="text" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Contact Number <span class="text-red-500">*</span></label>
                            <input name="contact_number" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="+60 1x-xxx xxxx" type="tel" required/>
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Email Address <span class="text-red-500">*</span></label>
                            <input name="email" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="visitor@example.com" type="email" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 1</label>
                            <input name="address_1" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 2</label>
                            <input name="address_2" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 3</label>
                            <input name="address_3" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">City</label>
                            <input name="city" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">State</label>
                            <select name="state" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT</option>
                                <option value="JOHOR">JOHOR</option>
                                <option value="KEDAH">KEDAH</option>
                                <option value="KELANTAN">KELANTAN</option>
                                <option value="MELAKA">MELAKA</option>
                                <option value="NEGERI SEMBILAN">NEGERI SEMBILAN</option>
                                <option value="PAHANG">PAHANG</option>
                                <option value="PENANG">PENANG</option>
                                <option value="PERAK">PERAK</option>
                                <option value="PERLIS">PERLIS</option>
                                <option value="SABAH">SABAH</option>
                                <option value="SARAWAK">SARAWAK</option>
                                <option value="SELANGOR">SELANGOR</option>
                                <option value="TERENGGANU">TERENGGANU</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Postal Code</label>
                            <input name="postal_code" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Country</label>
                            <select name="country" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="MALAYSIA">MALAYSIA</option>
                                <option value="OTHER">OTHER</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Category</label>
                            <select name="category" id="vehicleCategory" onchange="updateVehicleType()" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT</option>
                                <option value="CAR">Car</option>
                                <option value="MOTORCYCLE">Motorcycle</option>
                                <option value="TRUCK">Truck</option>
                                <option value="BUS">Bus</option>
                                <option value="VAN">Van</option>
                                <option value="HEAVY_MACHINERY">Heavy Machinery</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Type Of Vehicle</label>
                            <select name="vehicle_type" id="vehicleType" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT CATEGORY FIRST</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Vehicle Registration Number</label>
                            <input name="vehicle_registration" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="e.g. ABC 1234" type="text"/>
                        </div>
                    </div>
                </section>

                <!-- Driving License Section -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 mt-8">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400">
                                    <span class="material-symbols-outlined">badge</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Driving License</h2>
                                    <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Optional driving license information.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                            <button type="button" onclick="addLicense()" class="text-green-600 hover:text-green-700 transition-colors p-1.5 rounded-full hover:bg-green-50 dark:hover:bg-green-900/20" title="Add License">
                                <span class="material-symbols-outlined text-2xl">add_circle</span>
                            </button>
                            <button type="button" onclick="removeLicense()" class="text-red-600 hover:text-red-700 transition-colors p-1.5 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove License">
                                <span class="material-symbols-outlined text-2xl">remove_circle</span>
                            </button>
                            </div>
                        </div>
                        <div id="licenseContainer" class="flex flex-col gap-4">
                            <div class="text-center py-8 text-text-sub dark:text-gray-400">
                                <span class="material-symbols-outlined text-5xl mb-3 block text-gray-300 dark:text-gray-600">badge</span>
                                <p class="text-sm">No licenses added yet. Click <span class="text-primary font-semibold">+</span> to add driving license.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Company Details -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400">
                            <span class="material-symbols-outlined">apartment</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Company Details</h2>
                            <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Your company information.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Company Registration ID</label>
                            <input name="company_reg_id" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Company Name</label>
                            <input name="company_name" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                    </div>
                </section>
                </section>

                <!-- Asset/Equipment Details Section -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 mt-8">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-full bg-cyan-50 dark:bg-cyan-900/20 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                                    <span class="material-symbols-outlined">inventory_2</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Asset/Equipment Details</h2>
                                    <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Equipment and assets being brought in.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                            <button type="button" onclick="addEquipment()" class="text-green-600 hover:text-green-700 transition-colors p-1.5 rounded-full hover:bg-green-50 dark:hover:bg-green-900/20" title="Add Equipment">
                                <span class="material-symbols-outlined text-2xl">add_circle</span>
                            </button>
                            <button type="button" onclick="removeEquipment()" class="text-red-600 hover:text-red-700 transition-colors p-1.5 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove Equipment">
                                <span class="material-symbols-outlined text-2xl">remove_circle</span>
                            </button>
                            </div>
                        </div>
                        <div id="equipmentContainer" class="flex flex-col gap-6">
                            <div class="text-center py-8 text-text-sub dark:text-gray-400">
                                <span class="material-symbols-outlined text-5xl mb-3 block text-gray-300 dark:text-gray-600">inventory_2</span>
                                <p class="text-sm">No equipment added yet. Click <span class="text-primary font-semibold">+</span> to add equipment.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Document Upload -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8 mt-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <span class="material-symbols-outlined">folder_open</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Document Upload</h2>
                            <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Required for identity verification.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-3">
                            <p class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Government ID <span class="text-red-500">*</span></p>
                            <div class="group relative flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                                <input name="government_id" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" type="file"/>
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                    <span class="material-symbols-outlined text-4xl text-gray-400 group-hover:text-primary mb-3 transition-colors">id_card</span>
                                    <p class="mb-1 text-sm text-text-main dark:text-gray-300 font-brand"><span class="font-semibold text-primary">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-text-sub dark:text-gray-500 font-brand">SVG, PNG, JPG or PDF (MAX. 5MB)</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3">
                            <p class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Invitation Letter (Optional)</p>
                            <div class="group relative flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                                <input name="invitation_letter" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" type="file"/>
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                    <span class="material-symbols-outlined text-4xl text-gray-400 group-hover:text-primary mb-3 transition-colors">upload_file</span>
                                    <p class="mb-1 text-sm text-text-main dark:text-gray-300 font-brand"><span class="font-semibold text-primary">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-text-sub dark:text-gray-500 font-brand">PDF or Images</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Profile Photo -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8 mt-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center text-teal-600 dark:text-teal-400">
                            <span class="material-symbols-outlined">photo_camera</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Profile Photo</h2>
                            <p class="text-sm text-text-sub dark:text-gray-400 font-brand">This will be used for your visitor badge.</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-8">
                        <div class="relative group">
                            <div class="size-32 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden border-4 border-white dark:border-gray-700 shadow-lg">
                                <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600">account_circle</span>
                            </div>
                            <button class="absolute bottom-0 right-0 p-2 bg-white dark:bg-gray-700 rounded-full shadow-md text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary transition-colors border border-gray-200 dark:border-gray-600" type="button">
                                <span class="material-symbols-outlined text-xl">edit</span>
                            </button>
                        </div>
                        <div class="flex-1 space-y-4 text-center sm:text-left">
                            <div>
                                <h3 class="font-medium text-text-main dark:text-white font-brand">Upload or Capture</h3>
                                <p class="text-sm text-text-sub dark:text-gray-400 mt-1 font-brand">Please ensure your face is clearly visible. No sunglasses or hats.</p>
                            </div>
                            <div class="flex flex-wrap justify-center sm:justify-start gap-3">
                                <button class="px-5 py-2.5 rounded-lg bg-primary hover:bg-primary-hover text-white font-medium text-sm flex items-center gap-2 transition-all shadow-lg shadow-primary/25 font-brand" type="button">
                                    <span class="material-symbols-outlined text-lg">upload</span>
                                    Upload Photo
                                </button>
                                <button class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-text-main dark:text-white font-medium text-sm flex items-center gap-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all font-brand" type="button">
                                    <span class="material-symbols-outlined text-lg">camera_alt</span>
                                    Take Photo
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-4 py-6 border-t border-border-color dark:border-gray-800 mt-8">
                    <button type="button" onclick="window.history.back()" class="px-6 py-3 rounded-lg border border-border-color dark:border-gray-700 text-text-main dark:text-gray-300 font-bold hover:bg-background-light dark:hover:bg-gray-800 transition-all font-brand">
                        Cancel
                    </button>
                    <button type="submit" class="px-8 py-3 rounded-lg bg-primary text-white font-bold hover:bg-primary-hover shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2 font-brand">
                        <span>Submit Request</span>
                        <span class="material-symbols-outlined text-sm">send</span>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Date Visit dynamic addition
        let dateVisitCount = 1;
        function addDateVisit() {
            const container = document.getElementById('dateVisitContainer');
            const items = container.querySelectorAll('.date-visit-item');
            const newIndex = items.length;
            
            const html = `
                <div class="date-visit-item bg-background-light dark:bg-background-dark/50 rounded-lg p-4 border border-border-color dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-text-main dark:text-white font-brand">Date Visit ${newIndex + 1}</h4>
                        <button type="button" onclick="removeSpecificDateVisit(this)" class="text-red-600 hover:text-red-700 transition-colors p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Delete this date visit">
                            <span class="material-symbols-outlined text-xl">delete</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date From <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input name="dates[${dateVisitCount}][date_from]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="dd/mm/yyyy hh:mm" type="datetime-local" required/>
                                <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub cursor-pointer" onclick="this.previousElementSibling.showPicker()">calendar_month</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date To <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input name="dates[${dateVisitCount}][date_to]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="dd/mm/yyyy hh:mm" type="datetime-local" required/>
                                <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub cursor-pointer" onclick="this.previousElementSibling.showPicker()">event</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            dateVisitCount++;
        }

        function removeDateVisit() {
            const container = document.getElementById('dateVisitContainer');
            const items = container.querySelectorAll('.date-visit-item');
            if (items.length > 1) {
                items[items.length - 1].remove();
                dateVisitCount--;
                updateDateVisitNumbers();
            }
        }

        function removeSpecificDateVisit(button) {
            const container = document.getElementById('dateVisitContainer');
            const items = container.querySelectorAll('.date-visit-item');
            
            if (items.length > 1) {
                const item = button.closest('.date-visit-item');
                item.remove();
                updateDateVisitNumbers();
            } else {
                alert('At least one date visit entry is required.');
            }
        }

        function updateDateVisitNumbers() {
            const container = document.getElementById('dateVisitContainer');
            const items = container.querySelectorAll('.date-visit-item');
            items.forEach((item, index) => {
                const header = item.querySelector('h4');
                if (header) {
                    header.textContent = `Date Visit ${index + 1}`;
                }
            });
        }

        // License dynamic addition
        let licenseCount = 0;
        function addLicense() {
            const container = document.getElementById('licenseContainer');
            
            // Remove empty state message if it exists
            const emptyState = container.querySelector('.text-center');
            if (emptyState) {
                emptyState.remove();
            }
            
            const items = container.querySelectorAll('.license-item');
            const newIndex = items.length;
            
            const html = `
                <div class="license-item bg-background-light dark:bg-background-dark/50 rounded-lg p-4 border border-border-color dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-text-main dark:text-white font-brand">License ${newIndex + 1}</h4>
                        <button type="button" onclick="removeSpecificLicense(this)" class="text-red-600 hover:text-red-700 transition-colors p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Delete this license">
                            <span class="material-symbols-outlined text-xl">delete</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">License Class</label>
                            <select name="licenses[${licenseCount}][class]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT</option>
                                <option value="B">B</option>
                                <option value="B2">B2</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="DA">DA</option>
                                <option value="E">E</option>
                                <option value="E1">E1</option>
                                <option value="E2">E2</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">License Expiry <span class="text-red-500">*</span></label>
                            <input name="licenses[${licenseCount}][expiry]" placeholder="DD/MM/YYYY" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="date"/>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            licenseCount++;
        }

        function removeLicense() {
            const container = document.getElementById('licenseContainer');
            const items = container.querySelectorAll('.license-item');
            if (items.length > 0) {
                items[items.length - 1].remove();
                updateLicenseNumbers();
                
                // Show empty state if no items left
                if (container.querySelectorAll('.license-item').length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-8 text-text-sub dark:text-gray-400">
                            <span class="material-symbols-outlined text-5xl mb-3 block text-gray-300 dark:text-gray-600">badge</span>
                            <p class="text-sm">No licenses added yet. Click <span class="text-primary font-semibold">+</span> to add driving license.</p>
                        </div>
                    `;
                    licenseCount = 0;
                }
            }
        }

        function removeSpecificLicense(button) {
            const container = document.getElementById('licenseContainer');
            const item = button.closest('.license-item');
            item.remove();
            
            updateLicenseNumbers();
            
            // Show empty state if no items left
            const items = container.querySelectorAll('.license-item');
            if (items.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-text-sub dark:text-gray-400">
                        <span class="material-symbols-outlined text-5xl mb-3 block text-gray-300 dark:text-gray-600">badge</span>
                        <p class="text-sm">No licenses added yet. Click <span class="text-primary font-semibold">+</span> to add driving license.</p>
                    </div>
                `;
                licenseCount = 0;
            }
        }

        function updateLicenseNumbers() {
            const container = document.getElementById('licenseContainer');
            const items = container.querySelectorAll('.license-item');
            items.forEach((item, index) => {
                const header = item.querySelector('h4');
                if (header) {
                    header.textContent = `License ${index + 1}`;
                }
            });
        }

        // Equipment dynamic addition
        let equipmentCount = 0;
        function addEquipment() {
            const container = document.getElementById('equipmentContainer');
            
            // Remove empty state message if it exists
            const emptyState = container.querySelector('.text-center');
            if (emptyState) {
                emptyState.remove();
            }
            
            const html = `
                <div class="equipment-item">
                    <div class="flex items-center justify-between mb-4">
                        <span class="equipment-number text-sm font-bold text-text-sub dark:text-gray-400">${equipmentCount + 1}.</span>
                        <button type="button" onclick="removeSpecificEquipment(this)" class="text-red-600 hover:text-red-700 transition-colors p-1.5 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove this equipment">
                            <span class="material-symbols-outlined text-xl">delete</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Category</label>
                            <select name="equipment[${equipmentCount}][category]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT</option>
                                <option value="TOOLS">TOOLS</option>
                                <option value="ELECTRONICS">ELECTRONICS</option>
                                <option value="MACHINERY">MACHINERY</option>
                                <option value="VEHICLE">VEHICLE</option>
                                <option value="OTHER">OTHER</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Size</label>
                            <select name="equipment[${equipmentCount}][size]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT</option>
                                <option value="SMALL">SMALL</option>
                                <option value="MEDIUM">MEDIUM</option>
                                <option value="LARGE">LARGE</option>
                                <option value="EXTRA LARGE">EXTRA LARGE</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Transportation Method</label>
                            <select name="equipment[${equipmentCount}][transport]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT</option>
                                <option value="HAND CARRY">HAND CARRY</option>
                                <option value="VEHICLE">VEHICLE</option>
                                <option value="TRUCK">TRUCK</option>
                                <option value="FORKLIFT">FORKLIFT</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Purpose</label>
                            <input name="equipment[${equipmentCount}][purpose]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Type of Equipment</label>
                            <input name="equipment[${equipmentCount}][type]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Voltage Use</label>
                            <input name="equipment[${equipmentCount}][voltage]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="e.g. 240V" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Quantity</label>
                            <input name="equipment[${equipmentCount}][quantity]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="number" min="1"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Serial Number</label>
                            <input name="equipment[${equipmentCount}][serial]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            equipmentCount++;
            updateEquipmentNumbers();
        }

        function removeEquipment() {
            const container = document.getElementById('equipmentContainer');
            const items = container.querySelectorAll('.equipment-item');
            if (items.length > 0) {
                items[items.length - 1].remove();
                updateEquipmentNumbers();
                
                // Show empty state if no items left
                if (container.querySelectorAll('.equipment-item').length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-8 text-text-sub dark:text-gray-400">
                            <span class="material-symbols-outlined text-5xl mb-3 block text-gray-300 dark:text-gray-600">inventory_2</span>
                            <p class="text-sm">No equipment added yet. Click <span class="text-primary font-semibold">+</span> to add equipment.</p>
                        </div>
                    `;
                    equipmentCount = 0;
                }
            }
        }

        function removeSpecificEquipment(button) {
            const container = document.getElementById('equipmentContainer');
            const equipmentItem = button.closest('.equipment-item');
            equipmentItem.remove();
            updateEquipmentNumbers();
            
            // Show empty state if no items left
            if (container.querySelectorAll('.equipment-item').length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-slate-500 dark:text-slate-400">
                        <span class="material-symbols-outlined text-5xl mb-3 block text-slate-300 dark:text-slate-600">inventory_2</span>
                        <p class="text-sm">No equipment added yet. Click <span class="text-primary font-semibold">+</span> to add equipment.</p>
                    </div>
                `;
                equipmentCount = 0;
            }
        }

        function updateEquipmentNumbers() {
            const numbers = document.querySelectorAll('.equipment-number');
            numbers.forEach((num, index) => {
                num.textContent = `${index + 1}.`;
            });
        }

        // Vehicle type mapping
        const vehicleTypes = {
            'CAR': [
                { value: 'SEDAN', label: 'Sedan' },
                { value: 'HATCHBACK', label: 'Hatchback' },
                { value: 'SUV', label: 'SUV' },
                { value: 'COUPE', label: 'Coupe' }
            ],
            'TRUCK': [
                { value: 'PICKUP', label: 'Pickup' },
                { value: 'LORRY', label: 'Lorry' },
                { value: 'TRAILER', label: 'Trailer' }
            ],
            'MOTORCYCLE': [
                { value: 'SCOOTER', label: 'Scooter' },
                { value: 'SPORT_BIKE', label: 'Sport bike' },
                { value: 'CRUISER', label: 'Cruiser' }
            ],
            'BUS': [
                { value: 'MINI_BUS', label: 'Mini Bus' },
                { value: 'COACH', label: 'Coach' },
                { value: 'SCHOOL_BUS', label: 'School Bus' }
            ],
            'VAN': [
                { value: 'CARGO_VAN', label: 'Cargo Van' },
                { value: 'PASSENGER_VAN', label: 'Passenger Van' },
                { value: 'MINIVAN', label: 'Minivan' }
            ],
            'HEAVY_MACHINERY': [
                { value: 'EXCAVATOR', label: 'Excavator' },
                { value: 'BULLDOZER', label: 'Bulldozer' },
                { value: 'CRANE', label: 'Crane' },
                { value: 'FORKLIFT', label: 'Forklift' }
            ]
        };

        function updateVehicleType() {
            const category = document.getElementById('vehicleCategory').value;
            const vehicleTypeSelect = document.getElementById('vehicleType');
            
            // Clear existing options
            vehicleTypeSelect.innerHTML = '';
            
            if (!category) {
                vehicleTypeSelect.innerHTML = '<option value="">SELECT CATEGORY FIRST</option>';
                return;
            }
            
            // Add default option
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'SELECT TYPE';
            vehicleTypeSelect.appendChild(defaultOption);
            
            // Add category-specific options
            if (vehicleTypes[category]) {
                vehicleTypes[category].forEach(type => {
                    const option = document.createElement('option');
                    option.value = type.value;
                    option.textContent = type.label;
                    vehicleTypeSelect.appendChild(option);
                });
            }
        }
    </script>
</body>
</html>
