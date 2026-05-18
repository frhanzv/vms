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
                    <span class="material-symbols-outlined text-[22px] <?= $isStaff ? 'font-medium fill-1' : '' ?> group-hover:scale-110 transition-transform">badge</span>
                    <p class="text-sm <?= $isStaff ? 'font-semibold' : 'font-medium' ?>">Staff Pass List</p>
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

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8 bg-background-light dark:bg-background-dark">
        <div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-7xl">

            <!-- Header -->

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">
                        Staff List
                    </h1>
                    <div class="flex gap-2">
                        <button onclick="document.getElementById('uploadModal').classList.toggle('hidden')" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                            <span class="material-icons text-sm mr-1">add</span>
                            Import
                        </button>
                        <a href="<?= base_url('files/StaffTemplate.xlsx') ?>" 
                            download="StaffTemplate.xlsx" 
                            class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                            <span class="material-icons text-sm mr-1">file_download</span>
                            Template
                        </a>
                        <a href="<?= base_url('staffs/staffpassrequest') ?>" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                            <span class="material-icons text-sm mr-1">add</span>
                            Request
                        </a>
                    </div>
            </div>
        
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-4 flex items-center gap-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 text-sm rounded-lg px-4 py-3">
                    <span class="material-symbols-outlined text-[20px] flex-shrink-0">check_circle</span>
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 flex items-center gap-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 text-sm rounded-lg px-4 py-3">
                    <span class="material-symbols-outlined text-[20px] flex-shrink-0">error</span>
                    <span><?= esc(session()->getFlashdata('error')) ?></span>
                </div>
            <?php endif; ?>

            <!-- Filter -->

            <div class="flex items-center justify-between gap-4 mb-6">
                <div class="flex shadow-sm w-full max-w-lg">
                    <input id="staffSearchInput" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-xs focus:ring-primary focus:border-primary outline-none" placeholder="IC / PASSPORT / FULL NAME / STAFF NO" type="text"/>
                    <button id="staffSearchBtn" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-r flex items-center justify-center transition-colors">
                        <span class="material-icons text-white">search</span>
                    </button>
                </div>
                <div class="w-48">
                    <select id="staffSortSelect" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-xs focus:ring-primary focus:border-primary outline-none appearance-none bg-white">
                        <option value="" disabled selected>SORT BY</option>
                        <option value="name_asc">Name (A - Z)</option>
                        <option value="name_desc">Name (Z - A)</option>
                        <option value="date_asc">Date (Oldest)</option>
                        <option value="date_desc">Date (Newest)</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-6">
                <table class="w-full min-w-max text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-bold uppercase tracking-wide">
                            <th class="p-4 border-b dark:border-gray-600">No</th>
                            <th class="p-4 border-b dark:border-gray-600">Action</th>
                            <th class="p-4 border-b dark:border-gray-600">Date</th>
                            <th class="p-4 border-b dark:border-gray-600">App No</th>
                            <th class="p-4 border-b dark:border-gray-600">Full Name</th>
                            <th class="p-4 border-b dark:border-gray-600">IC / Passport No</th>
                            <th class="p-4 border-b dark:border-gray-600">Staff No</th>
                            <th class="p-4 border-b dark:border-gray-600">Status</th>
                            <th class="p-4 border-b dark:border-gray-600">Remark</th>
                        </tr>
                    </thead>
                    <tbody id="staffTableBody" class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                        <?php if (empty($staffList)): ?>
                        <tr>
                            <td colspan="9" class="p-8 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-4">
                                        <span class="material-symbols-outlined text-4xl text-gray-400 dark:text-gray-500">folder_off</span>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-gray-700 dark:text-gray-300">No Data Available</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">There are no staff records at the moment.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($staffList as $staff): ?>
                            <tr data-name="<?= strtolower(esc($staff['full_name'])) ?>"
                                data-ic="<?= strtolower(esc($staff['ic_passport'])) ?>"
                                data-staffno="<?= strtolower(esc($staff['staff_no'])) ?>"
                                data-appno="<?= strtolower(esc($staff['app_no'])) ?>"
                                data-status="<?= strtolower(esc($staff['status'])) ?>"
                                data-created="<?= esc($staff['created_at']) ?>"
                                onclick='openDetailModal(<?= json_encode($staff) ?>)' class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border-b border-gray-100 dark:border-gray-700 cursor-pointer">
                                <td class="p-4"><?= $staff['no'] ?></td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <button 
                                            onclick="event.stopPropagation(); window.location.href='<?= base_url('staffpassrequest/view/') ?><?= $staff['id'] ?>'" 
                                            class="text-primary hover:text-blue-700 transition-colors" 
                                            title="View Details">
                                            <span class="material-symbols-outlined text-[20px]">search</span>
                                        </button>
                                        <?php if ($showPrintButton): ?>
                                        <button onclick="event.stopPropagation(); printStaff(<?= json_encode($staff) ?>)" class="text-primary hover:text-blue-700 transition-colors" title="Print">
                                            <span class="material-symbols-outlined text-[20px]">print</span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($canEdit ?? false): ?>
                                        <button onclick="event.stopPropagation(); window.location.href='<?= base_url('staffpassrequest/edit/') ?><?= $staff['id'] ?>'" class="text-amber-500 hover:text-amber-700 transition-colors" title="Edit">
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($canDelete ?? false): ?>
                                        <button onclick="event.stopPropagation(); confirmDelete(<?= $staff['id'] ?>)" class="text-red-500 hover:text-red-700 transition-colors" title="Delete">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="p-4"><?= esc($staff['date']) ?></td>
                                <td class="p-4"><?= esc($staff['app_no'] ?? 'N/A') ?></td>
                                <td class="p-4 font-semibold text-gray-800 dark:text-white"><?= esc($staff['full_name']) ?></td>
                                <td class="p-4"><?= esc($staff['ic_passport']) ?></td>
                                <td class="p-4"><?= esc($staff['staff_no'] ?? 'N/A') ?></td>
                                <td class="p-4"><?= esc($staff['status'] ?? 'N/A') ?></td>
                                <td class="p-4"><?= esc($staff['remark'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div id="uploadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-4">

                    <div class="flex items-center justify-between p-4 border-b dark:border-slate-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">UPLOAD FILE</h3>
                        <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                            <span class="material-icons">close</span>
                        </button>
                    </div>

                    <form action="<?= base_url('staff-pass/import') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="p-6">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Choose Excel File</label>
                            <input name="upload_file" type="file" accept=".xlsx, .xls" required
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-slate-700 dark:border-slate-600 dark:placeholder-gray-400">
                            <p class="mt-2 text-xs text-gray-500">Only .xlsx or .xls files allowed.</p>
                        </div>

                        <div class="flex justify-end gap-2 p-4 border-t dark:border-slate-700">
                            <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">
                                Cancel
                            </button>
                            <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded text-sm font-medium flex items-center transition-colors">
                                <span class="material-icons text-sm mr-1">publish</span>
                                Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-medium text-gray-500 dark:text-gray-400">
                <span id="staffPaginationInfo"></span>
                <div class="flex items-center gap-2">
                    <div id="staffPaginationBtns" class="flex items-center gap-1"></div>
                    <div class="relative">
                        <select id="staffPerPageSelect" class="appearance-none bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 py-1.5 pl-3 pr-8 rounded focus:outline-none focus:ring-1 focus:ring-primary text-xs font-medium cursor-pointer shadow-sm">
                            <option value="10" selected>10 ITEMS PER PAGE</option>
                            <option value="25">25 ITEMS PER PAGE</option>
                            <option value="50">50 ITEMS PER PAGE</option>
                        </select>
                        <span class="absolute right-2 top-1.5 pointer-events-none material-icons text-sm text-gray-500">expand_more</span>
                    </div>
                </div>
            </div>
            
        </div>
    </main>
    <script>
        const _canEdit   = <?= json_encode($canEdit   ?? false) ?>;
        const _canDelete = <?= json_encode($canDelete ?? false) ?>;

        function openDetailModal(staff) {
            document.getElementById('staffDetailModal')?.remove();

            const statusColors = {
                'ACTIVE':    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                'INACTIVE':  'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                'SUSPENDED': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                'BLACKLIST': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            };
            const statusBadge = statusColors[(staff.status || '').toUpperCase()] || 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300';

            const field = (label, value) => `
                <div class="space-y-1">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide">${label}</p>
                    <p class="text-sm text-gray-800 dark:text-white font-semibold">${value || '—'}</p>
                </div>`;

            const editBtn = _canEdit ? `
                <button onclick="event.stopPropagation(); document.getElementById('staffDetailModal').remove(); window.location.href='<?= base_url('staffpassrequest/edit/') ?>${staff.id}'"
                    class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-base">edit</span> Edit
                </button>` : '';

            const deleteBtn = _canDelete ? `
                <button onclick="event.stopPropagation(); document.getElementById('staffDetailModal').remove(); confirmDelete(${staff.id})"
                    class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white text-sm font-medium transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-base">delete</span> Delete
                </button>` : '';

            document.body.insertAdjacentHTML('beforeend', `
            <div id="staffDetailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.remove()">
                <div class="bg-white dark:bg-slate-800 rounded-xl max-w-lg w-full shadow-2xl" onclick="event.stopPropagation()">
                    <div class="flex items-start justify-between p-6 border-b border-gray-200 dark:border-slate-700">
                        <div>
                            <h3 class="text-base font-bold text-gray-800 dark:text-white">${staff.full_name || '—'}</h3>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full ${statusBadge}">${staff.status || 'N/A'}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">${staff.app_no || ''}</span>
                            </div>
                        </div>
                        <button onclick="document.getElementById('staffDetailModal').remove()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 ml-4 flex-shrink-0">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            ${field('IC / Passport', staff.ic_passport)}
                            ${field('Staff No', staff.staff_no)}
                            ${field('Date', staff.date)}
                            ${field('Card Status', staff.card_status)}
                            ${field('Card Expiry', staff.card_expiry)}
                            ${field('Suspension Period', staff.suspension_period)}
                            ${field('Next Action', staff.next_action)}
                            ${field('Remark', staff.remark)}
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-6 border-t border-gray-200 dark:border-slate-700 gap-3">
                        <div class="flex gap-2">
                            ${editBtn}
                            ${deleteBtn}
                        </div>
                        <div class="flex gap-2 ml-auto">
                            <button onclick="document.getElementById('staffDetailModal').remove()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">Close</button>
                            <a href="<?= base_url('staffpassrequest/view/') ?>${staff.id}" class="px-4 py-2 rounded-lg bg-primary hover:bg-blue-600 text-white text-sm font-medium transition-colors flex items-center gap-1">
                                <span class="material-symbols-outlined text-base">open_in_new</span> Full Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>`);
        }

        function printStaff(staff) {
            window.print();
        }

        function confirmDelete(id) {
            if (!confirm('Are you sure you want to delete this staff record? This action cannot be undone.')) return;
            fetch('<?= base_url('staffs/delete/') ?>' + id, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
            }).then(r => r.ok ? location.reload() : alert('Delete failed.'));
        }

        (function () {
            const tbody  = document.getElementById('staffTableBody');
            const search = document.getElementById('staffSearchInput');
            const sortSel = document.getElementById('staffSortSelect');
            const perSel  = document.getElementById('staffPerPageSelect');
            const info    = document.getElementById('staffPaginationInfo');
            const btnCon  = document.getElementById('staffPaginationBtns');

            if (!tbody) return;

            const allRows = Array.from(tbody.querySelectorAll('tr[data-name]'));
            let filtered = [...allRows];
            let currentPage = 1;
            let perPage = 10;

            function applyFilter() {
                const q = (search?.value || '').toLowerCase().trim();
                filtered = allRows.filter(row =>
                    !q ||
                    row.dataset.name.includes(q) ||
                    row.dataset.ic.includes(q) ||
                    row.dataset.staffno.includes(q) ||
                    row.dataset.appno.includes(q)
                );
                currentPage = 1;
                applySort();
            }

            function applySort() {
                const s = sortSel?.value || '';
                filtered.sort((a, b) => {
                    switch (s) {
                        case 'name_asc':  return a.dataset.name.localeCompare(b.dataset.name);
                        case 'name_desc': return b.dataset.name.localeCompare(a.dataset.name);
                        case 'date_asc':  return a.dataset.created.localeCompare(b.dataset.created);
                        case 'date_desc': return b.dataset.created.localeCompare(a.dataset.created);
                        default: return 0;
                    }
                });
                render();
            }

            function render() {
                const start = (currentPage - 1) * perPage;
                const end   = start + perPage;
                allRows.forEach(r => (r.style.display = 'none'));
                filtered.forEach((r, i) => {
                    r.style.display = (i >= start && i < end) ? '' : 'none';
                });
                renderPagination();
            }

            function renderPagination() {
                const total      = filtered.length;
                const totalPages = Math.max(1, Math.ceil(total / perPage));
                if (currentPage > totalPages) currentPage = totalPages;

                const start = total === 0 ? 0 : (currentPage - 1) * perPage + 1;
                const end   = Math.min(currentPage * perPage, total);
                if (info) info.textContent = `Showing ${start}–${end} of ${total}`;

                if (!btnCon) return;
                btnCon.innerHTML = '';

                const makeBtn = (label, disabled, active = false) => {
                    const b = document.createElement('button');
                    b.textContent = label;
                    b.className = active
                        ? 'w-8 h-8 flex items-center justify-center bg-primary text-white rounded shadow-sm text-xs'
                        : 'w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-xs' + (disabled ? ' opacity-50 cursor-not-allowed' : '');
                    b.disabled = disabled;
                    return b;
                };

                const prev = makeBtn('«', currentPage === 1);
                prev.onclick = () => { currentPage--; render(); };
                btnCon.appendChild(prev);

                pageNumbers(currentPage, totalPages).forEach(p => {
                    if (p === '…') {
                        const sp = document.createElement('span');
                        sp.textContent = '…';
                        sp.className = 'w-8 h-8 flex items-center justify-center text-xs';
                        btnCon.appendChild(sp);
                    } else {
                        const b = makeBtn(p, false, p === currentPage);
                        b.onclick = () => { currentPage = p; render(); };
                        btnCon.appendChild(b);
                    }
                });

                const next = makeBtn('»', currentPage === totalPages);
                next.onclick = () => { currentPage++; render(); };
                btnCon.appendChild(next);
            }

            function pageNumbers(cur, total) {
                if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
                const p = [1];
                if (cur > 3) p.push('…');
                for (let i = Math.max(2, cur - 1); i <= Math.min(total - 1, cur + 1); i++) p.push(i);
                if (cur < total - 2) p.push('…');
                p.push(total);
                return p;
            }

            search?.addEventListener('input', applyFilter);
            document.getElementById('staffSearchBtn')?.addEventListener('click', applyFilter);
            sortSel?.addEventListener('change', () => { currentPage = 1; applySort(); });
            perSel?.addEventListener('change', () => {
                perPage = parseInt(perSel.value);
                currentPage = 1;
                render();
            });

            render();
        })();
    </script>
</body>
</html>