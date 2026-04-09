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
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
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
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased overflow-hidden">
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
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary group transition-colors" href="<?= base_url('logbook') ?>">
                        <span class="material-symbols-outlined text-[22px] font-medium fill-1 group-hover:scale-110 transition-transform">menu_book</span>
                        <p class="text-sm font-semibold">Visitor Logbook</p>
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
        <main class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark custom-scrollbar p-6 lg:p-10">
            <div class="mx-auto max-w-7xl flex flex-col gap-6">
                <!-- Top Actions -->
                <div class="flex justify-end items-center gap-3 mb-1">
                    <button class="relative p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors rounded-full hover:bg-slate-100 dark:hover:bg-slate-800">
                        <span class="material-symbols-outlined text-[24px]">notifications</span>
                        <span class="absolute top-2 right-2.5 size-2 bg-red-500 rounded-full border border-white dark:border-slate-900"></span>
                    </button>
                    <a href="<?= base_url('auth/logout') ?>" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors rounded-full hover:bg-slate-100 dark:hover:bg-slate-800">
                        <span class="material-symbols-outlined text-[24px]">account_circle</span>
                    </a>
                </div>

                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 -mt-4">
                    <div>
                        <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-2">Dynamic Reporting</h1>
                        <p class="text-slate-500 dark:text-slate-400 text-base font-medium max-w-2xl">Build custom reports by selecting data fields, applying filters, and previewing results in real-time.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="relative group">
                            <select class="appearance-none bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 py-2.5 pl-4 pr-10 rounded-lg text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-primary/50 shadow-sm cursor-pointer hover:border-primary transition-colors">
                                <option disabled selected value="">Load Saved Template</option>
                                <option value="monthly_visitors">Monthly Visitor Summary</option>
                                <option value="contractor_logs">Contractor Time Logs</option>
                                <option value="delivery_audit">Delivery Audit Q3</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-2.5 text-slate-400 pointer-events-none text-[20px]">expand_more</span>
                        </div>
                        <button class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2.5 rounded-lg text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-[20px]">restart_alt</span>
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Main Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-full">
                    <!-- Left Panel -->
                    <div class="lg:col-span-4 flex flex-col gap-6">
                        <!-- Criteria Selection -->
                        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
                            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">1. Report Criteria Selection</h3>
                            </div>
                            <div class="p-5 flex flex-col gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Select Entity</label>
                                    <div class="relative">
                                        <select class="w-full appearance-none bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg py-3 pl-4 pr-10 focus:ring-2 focus:ring-primary focus:border-transparent transition-shadow font-medium">
                                            <option>Visitors Log</option>
                                            <option>Contractor Access</option>
                                            <option>Deliveries</option>
                                            <option>Employee Hosts</option>
                                        </select>
                                        <span class="material-symbols-outlined absolute right-3 top-3.5 text-slate-400 pointer-events-none">expand_more</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Available Data Fields</label>
                                    <div class="max-h-60 overflow-y-auto custom-scrollbar border border-slate-200 dark:border-slate-700 rounded-lg p-1 bg-slate-50 dark:bg-slate-800/50">
                                        <?php 
                                        $fields = [
                                            ['name' => 'Visitor Name', 'checked' => true],
                                            ['name' => 'Company / Organization', 'checked' => true],
                                            ['name' => 'Host Name', 'checked' => true],
                                            ['name' => 'Check-in Time', 'checked' => true],
                                            ['name' => 'Check-out Time', 'checked' => false],
                                            ['name' => 'Status', 'checked' => true],
                                            ['name' => 'Badge ID', 'checked' => false],
                                            ['name' => 'Purpose of Visit', 'checked' => false],
                                        ];
                                        foreach ($fields as $field): 
                                        ?>
                                        <div class="flex items-center p-2 hover:bg-white dark:hover:bg-slate-800 rounded-md cursor-grab active:cursor-grabbing group">
                                            <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 mr-2 text-[20px]">drag_indicator</span>
                                            <label class="flex items-center gap-3 flex-1 cursor-pointer">
                                                <input <?= $field['checked'] ? 'checked' : '' ?> class="rounded border-slate-300 text-primary focus:ring-primary/25 size-4" type="checkbox"/>
                                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white"><?= $field['name'] ?></span>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-2 font-medium">5 fields selected. Drag to reorder.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Options -->
                        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col flex-1">
                            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">2. Filter Options</h3>
                            </div>
                            <div class="p-5 flex flex-col gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Date Range</label>
                                    <div class="flex gap-2">
                                        <div class="relative flex-1">
                                            <input class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-white px-3 py-2.5 focus:ring-primary focus:border-primary font-medium" type="date"/>
                                        </div>
                                        <span class="flex items-center text-slate-400 font-medium">-</span>
                                        <div class="relative flex-1">
                                            <input class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-white px-3 py-2.5 focus:ring-primary focus:border-primary font-medium" type="date"/>
                                        </div>
                                    </div>
                                    <div class="mt-2 flex gap-2 overflow-x-auto pb-1 no-scrollbar">
                                        <button class="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold hover:bg-primary hover:text-white transition">Today</button>
                                        <button class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition">Last 7 Days</button>
                                        <button class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition">This Month</button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Conditions</label>
                                    <div class="flex flex-col gap-3">
                                        <div class="flex items-center gap-2">
                                            <select class="flex-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-md py-2 px-2 text-slate-700 dark:text-white focus:ring-1 focus:ring-primary font-medium">
                                                <option>Status</option>
                                            </select>
                                            <select class="w-20 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-md py-2 px-2 text-slate-700 dark:text-white focus:ring-1 focus:ring-primary font-medium">
                                                <option>is</option>
                                                <option>is not</option>
                                            </select>
                                            <input class="flex-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-md py-2 px-2 text-slate-700 dark:text-white focus:ring-1 focus:ring-primary font-medium" type="text" value="Checked In"/>
                                            <button class="p-1 text-slate-400 hover:text-red-500 transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">remove_circle</span>
                                            </button>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <select class="flex-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-md py-2 px-2 text-slate-700 dark:text-white focus:ring-1 focus:ring-primary font-medium">
                                                <option>Host Dept</option>
                                            </select>
                                            <select class="w-20 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-md py-2 px-2 text-slate-700 dark:text-white focus:ring-1 focus:ring-primary font-medium">
                                                <option>contains</option>
                                            </select>
                                            <input class="flex-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-md py-2 px-2 text-slate-700 dark:text-white focus:ring-1 focus:ring-primary font-medium" type="text" value="Sales"/>
                                            <button class="p-1 text-slate-400 hover:text-red-500 transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">remove_circle</span>
                                            </button>
                                        </div>
                                    </div>
                                    <button class="mt-3 flex items-center gap-1.5 text-sm text-primary font-bold hover:text-blue-600 transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">add</span>
                                        Add Filter Rule
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel - Report Preview -->
                    <div class="lg:col-span-8 flex flex-col h-full">
                        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col h-full overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">3. Report Preview</h3>
                                    <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs px-2 py-0.5 rounded-full border border-slate-200 dark:border-slate-700 font-semibold">Displaying <?= count($records) ?> of 124 records</span>
                                </div>
                                <button class="text-primary text-xs font-bold flex items-center gap-1 hover:underline">
                                    <span class="material-symbols-outlined text-[16px]">refresh</span> Refresh Preview
                                </button>
                            </div>
                            <div class="flex-1 overflow-auto custom-scrollbar relative">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-slate-50 dark:bg-slate-800 sticky top-0 z-10 shadow-sm">
                                        <tr>
                                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 whitespace-nowrap">Visitor Name</th>
                                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 whitespace-nowrap">Company</th>
                                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 whitespace-nowrap">Host</th>
                                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 whitespace-nowrap">Check-in</th>
                                            <th class="px-5 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 whitespace-nowrap">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-slate-900 divide-y divide-slate-100 dark:divide-slate-800">
                                        <?php foreach ($records as $record): ?>
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-900 dark:text-slate-200 font-semibold"><?= esc($record['visitor_name']) ?></td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400 font-medium"><?= esc($record['company']) ?></td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400 font-medium"><?= esc($record['host']) ?></td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400 font-medium"><?= esc($record['checkin']) ?></td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-sm">
                                                <?php if ($record['status_class'] === 'green'): ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                                                    <span class="size-1.5 rounded-full bg-green-500"></span> <?= esc($record['status']) ?>
                                                </span>
                                                <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                                    <?= esc($record['status']) ?>
                                                </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/80 backdrop-blur flex flex-col sm:flex-row justify-between items-center gap-4">
                                <div class="flex items-center gap-6">
                                    <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Format:</span>
                                    <div class="flex items-center gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input checked class="text-primary focus:ring-primary border-slate-300 dark:border-slate-600 dark:bg-slate-800" name="format" type="radio"/>
                                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 group-hover:text-primary transition-colors">CSV</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input class="text-primary focus:ring-primary border-slate-300 dark:border-slate-600 dark:bg-slate-800" name="format" type="radio"/>
                                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 group-hover:text-primary transition-colors">Excel</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input class="text-primary focus:ring-primary border-slate-300 dark:border-slate-600 dark:bg-slate-800" name="format" type="radio"/>
                                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 group-hover:text-primary transition-colors">PDF</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="flex gap-3 w-full sm:w-auto">
                                    <button class="flex-1 sm:flex-none px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 font-bold text-sm transition-colors shadow-sm whitespace-nowrap">
                                        Save Template
                                    </button>
                                    <button class="flex-1 sm:flex-none px-5 py-2.5 rounded-lg border border-primary text-primary bg-primary/5 hover:bg-primary/10 font-bold text-sm transition-colors shadow-sm flex items-center justify-center gap-2 whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[20px]">sync</span>
                                        Generate Report
                                    </button>
                                    <button class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white font-bold text-sm shadow-md shadow-primary/20 transition-all flex items-center justify-center gap-2 whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[20px]">download</span>
                                        Export
                                    </button>
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
