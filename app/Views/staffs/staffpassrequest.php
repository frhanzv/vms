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
                        "primary-hover": "#0f6ac6",        // ← ADD THIS
                        secondary: "#3b82f6",
                        success: "#10b981",
                        "background-light": "#f6f7f8",
                        "background-dark": "#111827",
                        "card-light": "#ffffff",
                        "card-dark": "#1f2937",
                        "nav-active": "#e0efff",
                        "nav-text": "#344767",
                        "nav-icon": "#3b82f6",
                        "surface-light": "#ffffff",         // ← ADD THIS
                        "surface-dark": "#1a2632",          // ← ADD THIS
                        "text-main": "#0d141b",             // ← ADD THIS
                        "text-sub": "#4c739a",              // ← ADD THIS
                        "border-color": "#c4d0dc",          // ← ADD THIS
                    },
                    fontFamily: {
                        display: ["Montserrat", "sans-serif"],
                        sans: ["Montserrat", "sans-serif"],
                        brand: ["Montserrat", "sans-serif"], // ← ADD THIS
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
                    <span class="material-symbols-outlined text-[22px] <?= $isStaff ? 'font-medium fill-1' : '' ?> group-hover:scale-110 transition-transform">badge</span>
                    <p class="text-sm <?= $isStaff ? 'font-semibold' : 'font-medium' ?>">Staff Pass List</p>
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
                    <h1 class="text-3xl sm:text-4xl font-black text-text-main dark:text-white font-brand tracking-tight">Staff Pass Request</h1>
                </div>

                <form action="<?= base_url('staffs/staffpassrequest/store') ?>" method="post" enctype="multipart/form-data" class="space-y-8">
                    <?= csrf_field() ?>

                    <!-- Application Information -->
                    <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                            <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">business</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Application Info</h2>
                            </div>
                        </div>
                        <?php $fs = $fieldSettings ?? []; ?>
                        <div class="space-y-6">

                            <!-- Row 1: Date of Application, Type of Application, Designation, Resident -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date Of Application</label>
                                    <input name="date_of_application" value="<?= date('d/m/Y') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                                </div>
                                <?php if ($fs['type_of_application'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Type Of Application</label>
                                    <select name="type_of_application" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                        <option value="NEW">NEW</option>
                                        <option value="RENEWAL">RENEWAL</option>
                                        <option value="REPLACEMENT">REPLACEMENT</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['designation'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Designation <span class="text-red-500">*</span></label>
                                    <select name="designation" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" required>
                                        <option value="">SELECT</option>
                                        <option value="PEN. LORI">PEN. LORI</option>
                                        <option value="OPERATOR">OPERATOR</option>
                                        <option value="SUPERVISOR">SUPERVISOR</option>
                                        <option value="ENGINEER">ENGINEER</option>
                                        <option value="MANAGER">MANAGER</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['resident'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Resident</label>
                                    <select name="resident" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                        <option value="LOCAL">LOCAL</option>
                                        <option value="FOREIGN">FOREIGN</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Row 2: Sub Type -->
                            <?php if ($fs['sub_type'] ?? true): ?>
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Sub Type</label>
                                    <select name="sub_type" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                        <option value="PERMANENT">PERMANENT</option>
                                        <option value="TEMPORARY">TEMPORARY</option>
                                        <option value="CONTRACT">CONTRACT</option>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Location Access -->
                            <?php if ($fs['location_access'] ?? true): ?>
                            <?php
                            $locationGroups = [
                                'Changing Rooms' => [
                                    ['label' => 'Changing Room 1',                   'in' => 'CHANGING ROOM 1 IN',                    'out' => 'CHANGING ROOM 1 OUT'],
                                    ['label' => 'Changing Room 2',                   'in' => 'CHANGING ROOM 2 IN',                    'out' => 'CHANGING ROOM 2 OUT'],
                                ],
                                'Production' => [
                                    ['label' => 'Production 1 - Clean Room 10K',     'in' => 'PRODUCTION 1 - CLEAN ROOM 10K IN',      'out' => 'PRODUCTION 1 - CLEAN ROOM 10K OUT'],
                                    ['label' => 'Production 2 - Clean Room 1K',      'in' => 'PRODUCTION 2 - CLEAN ROOM 1K IN',       'out' => 'PRODUCTION 2 - CLEAN ROOM 1K OUT'],
                                    ['label' => 'Production 3',                       'in' => 'PRODUCTION 3 IN',                       'out' => 'PRODUCTION 3 OUT'],
                                    ['label' => 'Production 4',                       'in' => 'PRODUCTION 4 IN',                       'out' => 'PRODUCTION 4 OUT'],
                                    ['label' => 'Production 5 - Work In Progress',    'in' => 'PRODUCTION 5 - WORK IN PROGRESS IN',    'out' => 'PRODUCTION 5 - WORN IN PROGRESS OUT'],
                                    ['label' => 'Production Office',                  'in' => 'PRODUCTION OFFICE IN',                  'out' => 'PRODUCTION OFFICE OUT'],
                                    ['label' => 'Production WIP',                     'in' => 'PRODUCTION WIP IN',                     'out' => 'PRODUCTION WIP OUT'],
                                ],
                                'Rooms' => [
                                    ['label' => 'CMM Room',                           'in' => 'CMM ROOM IN',                           'out' => 'CMM ROOM OUT'],
                                    ['label' => 'Jitter Bug Room',                    'in' => 'JITTER BUG ROOM IN',                    'out' => 'JITTER BUG ROOM OUT'],
                                    ['label' => 'Polishing Room',                     'in' => 'POLISHING ROOM IN',                     'out' => 'POLISHING ROOM OUT'],
                                    ['label' => 'Polishing/Deburing Room',            'in' => 'POLISHING/DEBURING ROOM IN',            'out' => 'POLISHING/DEBURING ROOM OUT'],
                                    ['label' => 'QA Room',                            'in' => 'QA ROOM IN',                            'out' => 'QA ROOM OUT'],
                                    ['label' => 'Robotic Jitter Bug Room',            'in' => 'ROBOTIC JITTER BUG ROOM IN',            'out' => 'ROBOTIC JITTER BUG ROOM OUT'],
                                    ['label' => 'Robotic Welding Room',               'in' => 'ROBOTIC WELDING ROOM IN',               'out' => 'ROBOTIC WELDING ROOM OUT'],
                                    ['label' => 'Tools Room',                         'in' => 'TOOLS ROOM IN',                         'out' => 'TOOLS ROOM OUT'],
                                    ['label' => 'Ultra Sonic Room',                   'in' => 'ULTRA SONIC ROOM IN',                   'out' => 'ULTRA SONIC ROOM OUT'],
                                ],
                                'Areas & Others' => [
                                    ['label' => 'Chemical Waste',                     'in' => 'CHEMICAL WASTE IN',                     'out' => 'CHEMICAL WASTE OUT'],
                                    ['label' => 'Finished Good Area 1',               'in' => 'FINISHED GOODS AREA 1 IN',              'out' => 'FINISHED GOOD AREA 1 OUT'],
                                    ['label' => 'Finished Good Area 2',               'in' => 'FINISHED GOOD AREA 2 IN',               'out' => 'FINISHED GOOD AREA 2 OUT'],
                                    ['label' => 'Maintenance Department',             'in' => 'MAINTENANCE DEPARTMENT IN',             'out' => 'MAINTENANCE DEPARTMENT OUT'],
                                    ['label' => 'Packaging Area',                     'in' => 'PACKAGING AREA IN',                     'out' => 'PACKAGING AREA OUT'],
                                    ['label' => 'Raw Material Area',                  'in' => 'RAW MATERIAL AREA IN',                  'out' => 'RAW MATERIAL OUT'],
                                    ['label' => 'Schedule Waste',                     'in' => 'SCHEDULE WASTE IN',                     'out' => 'SCHEDULE WASTE OUT'],
                                    ['label' => 'Toilet',                             'in' => 'TOILET IN',                             'out' => 'TOILET OUT'],
                                    ['label' => 'Utility',                            'in' => 'UTILITY IN',                            'out' => 'UTILITY OUT'],
                                    ['label' => 'Water Treatment Area',               'in' => 'WATER TREATMENT AREA IN',               'out' => 'WATER TREATMENT AREA OUT'],
                                ],
                            ];
                            ?>
                            <div class="space-y-3" x-data="{
                                search: '',
                                selectGroup(groupId) {
                                    document.querySelectorAll('[data-group=\'' + groupId + '\']').forEach(cb => cb.checked = true);
                                },
                                clearGroup(groupId) {
                                    document.querySelectorAll('[data-group=\'' + groupId + '\']').forEach(cb => cb.checked = false);
                                },
                                selectAll() {
                                    document.querySelectorAll('input[name=\'location_access[]\']').forEach(cb => cb.checked = true);
                                },
                                clearAll() {
                                    document.querySelectorAll('input[name=\'location_access[]\']').forEach(cb => cb.checked = false);
                                }
                            }">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Location Access <span class="text-red-500">*</span></label>
                                    <div class="flex gap-2">
                                        <button type="button" @click="selectAll()" class="text-xs px-3 py-1.5 rounded-md bg-primary/10 text-primary hover:bg-primary/20 font-brand font-semibold transition-colors">Select All</button>
                                        <button type="button" @click="clearAll()" class="text-xs px-3 py-1.5 rounded-md bg-gray-100 dark:bg-gray-800 text-text-sub dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 font-brand font-semibold transition-colors">Clear All</button>
                                    </div>
                                </div>

                                <!-- Search -->
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-text-sub text-[20px] pointer-events-none">search</span>
                                    <input x-model="search" type="text" placeholder="Search locations..." class="w-full h-10 rounded-lg border border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white pl-10 pr-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand text-sm"/>
                                </div>

                                <!-- Column header -->
                                <div class="flex items-center justify-end pr-4 gap-4">
                                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 font-brand w-8 text-center">IN</span>
                                    <span class="text-xs font-bold uppercase tracking-wider text-rose-500 dark:text-rose-400 font-brand w-8 text-center">OUT</span>
                                </div>

                                <div class="rounded-xl border border-border-color dark:border-gray-700 overflow-hidden divide-y divide-border-color dark:divide-gray-700">
                                    <?php $groupIndex = 0; foreach ($locationGroups as $groupName => $locations): $groupId = 'grp-' . $groupIndex++; ?>
                                    <div>
                                        <!-- Group header -->
                                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-800/60 px-4 py-2.5">
                                            <span class="text-xs font-bold uppercase tracking-wider text-text-sub dark:text-gray-400 font-brand"><?= $groupName ?></span>
                                            <div class="flex gap-1.5">
                                                <button type="button" @click="selectGroup('<?= $groupId ?>')" class="text-xs px-2 py-1 rounded text-primary bg-primary/10 hover:bg-primary/20 font-brand font-semibold transition-colors">All</button>
                                                <button type="button" @click="clearGroup('<?= $groupId ?>')" class="text-xs px-2 py-1 rounded text-text-sub dark:text-gray-400 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 font-brand font-semibold transition-colors">None</button>
                                            </div>
                                        </div>
                                        <!-- Location rows -->
                                        <?php foreach ($locations as $loc): ?>
                                        <div x-show="search === '' || '<?= strtolower($loc['label']) ?>'.includes(search.toLowerCase())"
                                             class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors border-t border-border-color dark:border-gray-700">
                                            <span class="text-sm font-medium text-text-main dark:text-gray-100 font-brand flex-1 pr-4"><?= $loc['label'] ?></span>
                                            <div class="flex items-center gap-4 flex-shrink-0">
                                                <label class="flex items-center justify-center cursor-pointer w-8">
                                                    <input data-group="<?= $groupId ?>" name="location_access[]" value="<?= $loc['in'] ?>" class="form-checkbox rounded text-primary border-2 border-gray-300 dark:border-gray-600 focus:ring-primary h-5 w-5 cursor-pointer" type="checkbox"/>
                                                </label>
                                                <label class="flex items-center justify-center cursor-pointer w-8">
                                                    <input data-group="<?= $groupId ?>" name="location_access[]" value="<?= $loc['out'] ?>" class="form-checkbox rounded text-primary border-2 border-gray-300 dark:border-gray-600 focus:ring-primary h-5 w-5 cursor-pointer" type="checkbox"/>
                                                </label>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                        </div>
                    </section>

                    <!-- Date of Visit -->
                    

                    <!-- Details of Visit -->
                    
                    <!-- Staff Details -->
                    <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600 dark:text-green-400">
                                    <span class="material-symbols-outlined">person</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Staff Details</h2>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-6">

                            <!-- Upload IC Button -->
                            <div>
                                <button type="button" class="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-lg text-sm font-semibold uppercase shadow transition-all font-brand">
                                    Upload IC
                                </button>
                            </div>

                            <!-- Row 1: IC Number, Date of Birth -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <?php if ($fs['ic_number'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">IC Number <span class="text-red-500">*</span></label>
                                    <input name="ic_number" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="Enter IC / Passport Number" type="text" required/>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['date_of_birth'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date Of Birth <span class="text-red-500">*</span></label>
                                    <input name="date_of_birth" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="DD/MM/YYYY" type="date" required/>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Row 2: Sex, Full Name, Name On Staff Pass -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <?php if ($fs['sex'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Sex</label>
                                    <select name="sex" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                        <option value="MALE">MALE</option>
                                        <option value="FEMALE">FEMALE</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['full_name'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Full Name <span class="text-red-500">*</span></label>
                                    <input name="full_name" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="Full name as per ID" type="text" required/>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['name_on_staff_pass'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Name On Staff Pass <span class="text-red-500">*</span></label>
                                    <input name="name_on_staff_pass" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" required/>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Row 3: Staff No, Contact Number, Email Address -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <?php if ($fs['staff_no'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Staff No <span class="text-red-500">*</span></label>
                                    <input name="staff_no" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" required/>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['contact_number'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Contact Number <span class="text-red-500">*</span></label>
                                    <input name="contact_number" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="+60 1x-xxx xxxx" type="tel" required/>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['email'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Email Address <span class="text-red-500">*</span></label>
                                    <input name="email" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="staff@example.com" type="email" required/>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Row 4: Department -->
                            <?php if ($fs['department'] ?? true): ?>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Department <span class="text-red-500">*</span></label>
                                    <select name="department" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" required>
                                        <option value="">SELECT</option>
                                        <option value="EPIC">EPIC</option>
                                        <option value="HR">HR</option>
                                        <option value="FINANCE">FINANCE</option>
                                        <option value="OPERATIONS">OPERATIONS</option>
                                        <option value="IT">IT</option>
                                        <option value="MAINTENANCE">MAINTENANCE</option>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Row 5: Address 1, Address 2, Address 3 -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <?php if ($fs['address_1'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 1 <span class="text-red-500">*</span></label>
                                    <input name="address_1" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" required/>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['address_2'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 2</label>
                                    <input name="address_2" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['address_3'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 3</label>
                                    <input name="address_3" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Row 6: Country, State, City -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <?php if ($fs['country'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Country</label>
                                    <select name="country" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                        <option value="MALAYSIA">MALAYSIA</option>
                                        <option value="OTHER">OTHER</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['state'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">State <span class="text-red-500">*</span></label>
                                    <select name="state" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" required>
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
                                <?php endif; ?>
                                <?php if ($fs['city'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">City <span class="text-red-500">*</span></label>
                                    <select name="city" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" required>
                                        <option value="">SELECT</option>
                                        <option value="JOHOR">KUALA LUMPUR</option>
                                        <option value="KEDAH">KOTA BHARU</option>
                                        <option value="KELANTAN">SIBU</option>
                                        <option value="MELAKA">IPOH</option>
                                        <option value="NEGERI SEMBILAN">KANGAR</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Row 7: Postal Code -->
                            <?php if ($fs['postal_code'] ?? true): ?>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Postal Code <span class="text-red-500">*</span></label>
                                    <input name="postal_code" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" required/>
                                </div>
                            </div>
                            <?php endif; ?>

                        </div>
                    </section>

                    <!-- Driving License Section -->
                    <?php if ($fs['driving_license'] ?? true): ?>
                    <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 mt-8">
                        <div class="p-6 sm:p-8">
                            <div class="flex items-center justify-between mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400">
                                        <span class="material-symbols-outlined">cards_stack</span>
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
                    <?php endif; ?>

                    <!-- CSP Details -->

                    <!-- <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                            <div class="size-10 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400">
                                <span class="material-symbols-outlined">apartment</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">CSP</h2>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">CSP Number</label>
                                <input name="company_reg_id" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Expiry Date</label>
                                <div class="relative">
                                    <input name="csp_expiry_date" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="DD/MM/YYYY" type="date"/>
                                    <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub cursor-pointer" onclick="this.previousElementSibling.showPicker()">calendar_month</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    E-Vetting

                    <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                            <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <span class="material-symbols-outlined">verified_user</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">E-Vetting</h2>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">
                                    Date of Application <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        name="evetting_date_of_application"
                                        class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand"
                                        placeholder="DD/MM/YYYY"
                                        type="date"
                                    />
                                    <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub cursor-pointer" onclick="this.previousElementSibling.showPicker()">calendar_month</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">
                                    Date of Result
                                </label>
                                <div class="relative">
                                    <input
                                        name="evetting_date_of_result"
                                        class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand"
                                        placeholder="DD/MM/YYYY"
                                        type="date"
                                    />
                                    <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub cursor-pointer" onclick="this.previousElementSibling.showPicker()">calendar_month</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">
                                    Result
                                </label>
                                <select
                                    name="evetting_result"
                                    class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand appearance-none cursor-pointer"
                                >
                                    <option value="In Progress" selected>In Progress</option>
                                    <option value="Pass">Pass</option>
                                    <option value="Fail">Fail</option>
                                </select>
                            </div>
                        </div>
                    </section> -->
                    
                    
                    <!-- Document Upload -->
                    <?php if ($fs['document_upload'] ?? true): ?>
                    <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8 mt-8">
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
                                <p class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">IC / MyKad <span class="text-red-500">*</span></p>
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
                                <p class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Other Documents (Optional)</p>
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
                    <?php endif; ?>

                    <!-- Profile Photo -->

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

</body>

<script>

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


// Function for removing license
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



</script>
</html>