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
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('dashboard') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">dashboard</span>
                    <p class="text-sm font-medium">Dashboard</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('invitations') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">mail</span>
                    <p class="text-sm font-medium">Invitations</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary group transition-colors" href="<?= base_url('requests') ?>">
                    <span class="material-symbols-outlined text-[22px] font-medium fill-1 group-hover:scale-110 transition-transform">assignment</span>
                    <p class="text-sm font-semibold">Request List</p>
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
        <!-- Request Queue Sidebar -->
        <aside class="w-80 flex flex-col border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-slate-900 shrink-0 z-10">
        <div class="p-4 border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-bold text-sm uppercase tracking-wider text-gray-400">Request Queue</h3>
                <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full"><?= $stats['pending'] ?> Pending</span>
            </div>
            <div class="flex gap-2 text-xs">
                <button class="bg-primary text-white px-3 py-1.5 rounded-full font-medium shadow-sm shadow-primary/30">All</button>
                <button class="bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 px-3 py-1.5 rounded-full font-medium transition-colors">VIP</button>
                <button class="bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 px-3 py-1.5 rounded-full font-medium transition-colors">Flagged</button>
            </div>
            <?php if (! empty($queueRequests)): ?>
            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-800 flex items-center gap-2 flex-wrap">
                <label class="inline-flex items-center gap-2 text-xs font-medium text-gray-600 dark:text-gray-300 cursor-pointer select-none">
                    <input type="checkbox" id="select-all-requests" class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary" title="Select all in queue"/>
                    <span>Select all</span>
                </label>
                <button type="button" id="batch-approve-btn" class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-bold shadow-sm hover:bg-blue-600 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    Approve
                </button>
            </div>
            <?php endif; ?>
        </div>
        <div class="flex-1 overflow-y-auto scrollbar-hide p-2 space-y-2">
            <?php foreach ($queueRequests as $index => $request): ?>
            <div class="group flex items-start gap-3 p-3 rounded-lg <?= $index === 0 ? 'bg-primary/5 border border-primary/20' : 'hover:bg-gray-50 dark:hover:bg-slate-800 border border-transparent' ?> cursor-pointer relative overflow-hidden transition-colors">
                <?php if ($index === 0): ?>
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary"></div>
                <?php endif; ?>
                <input type="checkbox" name="request_batch[]" value="<?= (int) $request['id'] ?>" class="request-select-cb mt-2.5 shrink-0 rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary" title="Select for batch approve" onclick="event.stopPropagation()"/>
                <div class="relative size-10 rounded-full overflow-hidden shrink-0">
                    <?php if (!empty($request['photo'])): ?>
                    <img alt="<?= esc($request['name']) ?> Portrait" class="w-full h-full object-cover" src="<?= esc($request['photo']) ?>"/>
                    <?php else: ?>
                    <div class="w-full h-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold text-sm"><?= esc($request['initials']) ?></div>
                    <?php endif; ?>
                    <?php if ($request['status'] === 'warning'): ?>
                    <div class="absolute bottom-0 right-0 size-3 bg-red-500 border-2 border-white dark:border-slate-900 rounded-full"></div>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                        <h4 class="text-sm <?= $index === 0 ? 'font-bold' : 'font-semibold' ?> truncate text-gray-900 dark:text-white"><?= esc($request['name']) ?></h4>
                        <span class="text-xs font-medium <?= $request['time'] === '15m' ? 'text-orange-600 bg-orange-100 dark:bg-orange-900/30 dark:text-orange-400 px-1.5 rounded' : 'text-gray-400' ?>"><?= esc($request['time']) ?></span>
                    </div>
                    <p class="text-xs text-gray-400 truncate"><?= esc($request['company']) ?></p>
                    <?php if ($request['is_flagged']): ?>
                    <p class="text-xs text-red-600 dark:text-red-400 font-medium truncate mt-0.5 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] fill-current">warning</span> Flagged ID
                    </p>
                    <?php elseif (!empty($request['host'])): ?>
                    <p class="text-xs text-gray-400 truncate mt-0.5 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]"><?= $request['icon'] ?? 'person' ?></span> <?= esc($request['host']) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </aside>
        <main class="flex-1 flex flex-col h-full overflow-hidden relative">
            <div class="bg-background-light dark:bg-background-dark p-6 pb-2 shrink-0">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="flex flex-col gap-1 rounded-lg p-4 bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Pending Requests</p>
                        <span class="material-symbols-outlined text-orange-500 text-lg">pending</span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <p class="text-gray-900 dark:text-white text-2xl font-bold leading-tight"><?= $stats['pending'] ?></p>
                        <span class="text-green-600 bg-green-100 dark:bg-green-900/30 text-[10px] font-bold px-1.5 py-0.5 rounded">+2%</span>
                    </div>
                </div>
                <div class="flex flex-col gap-1 rounded-lg p-4 bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-800 shadow-sm relative overflow-hidden">
                    <div class="absolute right-0 top-0 p-1">
                        <span class="flex size-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full size-2 bg-red-500"></span>
                        </span>
                    </div>
                    <div class="flex justify-between items-start">
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Flagged for Review</p>
                        <span class="material-symbols-outlined text-red-500 text-lg">flag</span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <p class="text-gray-900 dark:text-white text-2xl font-bold leading-tight"><?= $stats['flagged'] ?></p>
                        <span class="text-red-600 dark:text-red-400 text-xs font-medium">Action Required</span>
                    </div>
                </div>
                <div class="flex flex-col gap-1 rounded-lg p-4 bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Expected Today</p>
                        <span class="material-symbols-outlined text-primary text-lg">calendar_today</span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <p class="text-gray-900 dark:text-white text-2xl font-bold leading-tight"><?= $stats['expected'] ?></p>
                        <span class="text-gray-400 text-xs font-medium">On Track</span>
                    </div>
                </div>
                <div class="flex flex-col gap-1 rounded-lg p-4 bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="flex justify-between items-start">
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Rejected</p>
                        <span class="material-symbols-outlined text-gray-400 text-lg">block</span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <p class="text-gray-900 dark:text-white text-2xl font-bold leading-tight"><?= $stats['rejected'] ?></p>
                        <span class="text-gray-400 text-xs font-medium">Last 24h</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-6 pt-2">
            <div class="max-w-5xl mx-auto flex flex-col gap-6">
                <?php if ($currentRequest): ?>
                <div class="bg-white dark:bg-slate-900 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-800">
                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <div class="relative group">
                            <?php if (!empty($currentRequest['photo'])): ?>
                            <div class="w-32 h-32 rounded-lg bg-cover bg-center shadow-inner" style='background-image: url("<?= esc($currentRequest['photo']) ?>");'></div>
                            <?php else: ?>
                            <div class="w-32 h-32 rounded-lg bg-gray-200 dark:bg-gray-700 shadow-inner flex items-center justify-center">
                                <span class="material-symbols-outlined text-5xl text-gray-400">account_circle</span>
                            </div>
                            <?php endif; ?>
                            <div class="absolute -bottom-2 -right-2 bg-green-500 text-white rounded-full p-1 border-4 border-white dark:border-slate-900">
                                <span class="material-symbols-outlined text-sm font-bold">check</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight"><?= esc($currentRequest['name']) ?></h1>
                                    <div class="flex items-center gap-2 mt-1 text-gray-400">
                                        <span class="material-symbols-outlined text-lg">business</span>
                                        <span class="text-base"><?= esc($currentRequest['company']) ?></span>
                                        <span class="mx-1">•</span>
                                        <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded text-xs font-medium border border-gray-200 dark:border-gray-600">ID: <?= esc($currentRequest['id']) ?></span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="flex gap-2">
                                        <button class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                            <span class="material-symbols-outlined text-lg">history</span>
                                            Past Visits
                                        </button>
                                        <button class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                            <span class="material-symbols-outlined text-lg">mail</span>
                                            Contact Host
                                        </button>
                                    </div>
                                    <div class="hidden sm:flex flex-col items-center bg-white p-2 rounded-lg border border-gray-200 shadow-sm shrink-0">
                                        <span class="material-symbols-outlined text-4xl text-black">qr_code_2</span>
                                        <span class="text-[10px] font-mono font-bold text-black mt-0.5"><?= esc($currentRequest['id']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                                <div class="bg-background-light dark:bg-slate-800 p-3 rounded-lg flex items-center gap-3">
                                    <div class="bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 p-2 rounded-md">
                                        <span class="material-symbols-outlined">person</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 font-medium">Host</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white"><?= esc($currentRequest['host']) ?></p>
                                    </div>
                                </div>
                                <div class="bg-background-light dark:bg-slate-800 p-3 rounded-lg flex items-center gap-3">
                                    <div class="bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 p-2 rounded-md">
                                        <span class="material-symbols-outlined">schedule</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 font-medium">Arrival</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white"><?= esc($currentRequest['arrival']) ?></p>
                                    </div>
                                </div>
                                <div class="bg-background-light dark:bg-slate-800 p-3 rounded-lg flex items-center gap-3">
                                    <div class="bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 p-2 rounded-md">
                                        <span class="material-symbols-outlined">meeting_room</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 font-medium">Purpose</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white"><?= esc($currentRequest['purpose']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div class="xl:col-span-1 bg-white dark:bg-slate-900 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-800 flex flex-col h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Watchlist Screening</h3>
                            <button class="text-primary text-xs font-semibold hover:underline">Re-run Check</button>
                        </div>
                        <div class="flex flex-col gap-4 flex-1 justify-center">
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-lg p-4 flex items-center gap-4">
                                <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-300 p-3 rounded-full shrink-0">
                                    <span class="material-symbols-outlined text-2xl">check_circle</span>
                                </div>
                                <div>
                                    <p class="text-green-800 dark:text-green-400 font-bold text-lg">Cleared</p>
                                    <p class="text-green-700 dark:text-green-500 text-sm">No matches found in global database.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                ID Verification 
                                <span class="bg-primary text-white text-xs font-bold px-2 py-0.5 rounded-full">AI Match <?= $currentRequest['ai_match'] ?>%</span>
                            </h3>
                            <div class="flex gap-2">
                                <button class="text-gray-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined">zoom_in</span>
                                </button>
                                <button class="text-gray-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined">download</span>
                                </button>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-6 items-stretch">
                            <div class="flex-1 space-y-2">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Uploaded ID Document</p>
                                <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-2 border border-dashed border-gray-300 dark:border-gray-600 relative group h-48 flex items-center justify-center overflow-hidden">
                                    <?php if (!empty($currentRequest['government_id_image'])): ?>
                                    <img class="max-w-full max-h-full object-contain opacity-90 transition-opacity group-hover:opacity-100" alt="Government ID" src="<?= esc($currentRequest['government_id_image']) ?>"/>
                                    <div class="absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">verified</span> Valid
                                    </div>
                                    <?php else: ?>
                                    <div class="text-center text-gray-400">
                                        <span class="material-symbols-outlined text-5xl mb-2">badge</span>
                                        <p class="text-xs">No ID uploaded</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex justify-between text-xs text-gray-400">
                                    <span>Type: <?= !empty($currentRequest['ic_passport']) ? 'MyKad/Passport' : 'N/A' ?></span>
                                    <span>IC: <?= esc($currentRequest['ic_passport']) ?></span>
                                </div>
                            </div>
                            <div class="flex-1 space-y-2">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Documents Uploaded</p>
                                <div class="h-48 flex flex-col gap-3 justify-center">
                                    <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-blue-100 dark:bg-blue-900/30 text-primary p-2 rounded-md">
                                                <span class="material-symbols-outlined text-xl">badge</span>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Driver License</span>
                                        </div>
                                        <button class="text-gray-400 hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined">download</span>
                                        </button>
                                    </div>
                                    <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-blue-100 dark:bg-blue-900/30 text-primary p-2 rounded-md">
                                                <span class="material-symbols-outlined text-xl">description</span>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Letter of Invitation</span>
                                        </div>
                                        <button class="text-gray-400 hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined">download</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-400">
                                    <span>Source: Pre-registration</span>
                                    <span>Date: Today</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-800 mb-20">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Access Control &amp; Assets</h3>
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="flex-1">
                            <label class="text-xs font-medium text-gray-400 mb-2 block">Requested Access Zones</label>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($currentRequest['access_zones'] as $zone): ?>
                                <div class="px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-md text-sm font-semibold border border-blue-200 dark:border-blue-800 flex items-center gap-1">
                                    <?= esc($zone) ?>
                                </div>
                                <?php endforeach; ?>
                                <div class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-md text-sm font-medium border border-gray-200 dark:border-gray-600 flex items-center gap-1 opacity-50 cursor-not-allowed" title="Not requested">
                                    Server Room
                                </div>
                            </div>
                        </div>
                        <div class="flex-1">
                            <label class="text-xs font-medium text-gray-400 mb-2 block">Assets Declared</label>
                            <ul class="text-sm text-gray-900 dark:text-white space-y-2">
                                <?php foreach ($currentRequest['assets'] as $asset): ?>
                                <li class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-gray-400"><?= esc($asset['type']) ?></span>
                                    <?= esc($asset['name']) ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="flex-1">
                            <label class="text-xs font-medium text-gray-400 mb-2 block">Notes</label>
                            <p class="text-sm text-gray-600 dark:text-gray-400 italic">"<?= esc($currentRequest['notes']) ?>"</p>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- No Current Request State -->
                <div class="bg-white dark:bg-slate-900 rounded-xl p-12 shadow-sm border border-gray-200 dark:border-gray-800 text-center">
                    <div class="flex flex-col items-center justify-center gap-4">
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-6">
                            <span class="material-symbols-outlined text-6xl text-gray-400 dark:text-gray-500">inbox</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No Pending Requests</h3>
                            <p class="text-gray-600 dark:text-gray-400">All visitor requests have been processed. New requests will appear here.</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($currentRequest): ?>
        <div class="absolute bottom-0 left-0 right-0 bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-gray-800 p-4 px-6 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-20">
            <div class="max-w-5xl mx-auto flex items-center justify-between">
                <div class="hidden sm:flex items-center gap-2 text-sm text-gray-400">
                    <span class="material-symbols-outlined text-base">info</span>
                    <span>This request was submitted 45 minutes ago.</span>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <button class="flex-1 sm:flex-none px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white font-semibold hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 text-sm">
                        More Info
                    </button>
                    <button class="flex-1 sm:flex-none px-5 py-2.5 rounded-lg border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 font-semibold hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-red-200 text-sm flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">block</span> Reject
                    </button>
                    <button class="flex-1 sm:flex-none px-8 py-2.5 rounded-lg bg-primary text-white font-bold shadow-md shadow-primary/30 hover:bg-blue-600 transition-all focus:ring-2 focus:ring-offset-2 focus:ring-primary text-sm flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">check_circle</span> Approve Entry
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        </main>
    </div>
</div>

<!-- Image Zoom Modal -->
<div id="imageZoomModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="relative max-w-6xl max-h-full">
        <button onclick="closeImageZoom()" class="absolute top-4 right-4 bg-white dark:bg-gray-800 rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <span class="material-symbols-outlined">close</span>
        </button>
        <img id="zoomedImage" src="" alt="Zoomed Image" class="max-w-full max-h-screen object-contain rounded-lg">
    </div>
</div>

<!-- Past Visits Modal -->
<div id="pastVisitsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="text-xl font-semibold text-gray-900">Past Visits History</h3>
            <button onclick="closePastVisitsModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div id="pastVisitsContent" class="p-6 overflow-y-auto max-h-[70vh]">
            <div class="flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div id="confirmModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-gray-700 transform transition-all">
        <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
                <div id="confirmIcon" class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 p-3 rounded-full">
                    <span class="material-symbols-outlined text-3xl">help</span>
                </div>
                <div class="flex-1">
                    <h3 id="confirmTitle" class="text-xl font-bold text-gray-900 dark:text-white">Confirm Action</h3>
                </div>
            </div>
            <p id="confirmMessage" class="text-gray-600 dark:text-gray-300 text-base">Are you sure you want to proceed?</p>
            <div id="rejectReasonContainer" class="hidden mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason for Rejection</label>
                <textarea id="rejectReasonInput" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-slate-800 dark:text-white" placeholder="Please provide a reason for rejection..."></textarea>
            </div>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 flex gap-3 rounded-b-xl">
            <button onclick="closeConfirmModal()" class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                Cancel
            </button>
            <button id="confirmActionBtn" onclick="handleConfirmAction()" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                <span id="confirmActionText">Confirm</span>
            </button>
        </div>
    </div>
</div>

<!-- Alert Modal -->
<div id="alertModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-gray-700 transform transition-all">
        <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
                <div id="alertIcon" class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 p-3 rounded-full">
                    <span class="material-symbols-outlined text-3xl">info</span>
                </div>
                <div class="flex-1">
                    <h3 id="alertTitle" class="text-xl font-bold text-gray-900 dark:text-white">Information</h3>
                </div>
            </div>
            <p id="alertMessage" class="text-gray-600 dark:text-gray-300 text-base whitespace-pre-line"></p>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 flex gap-3 rounded-b-xl">
            <button onclick="closeAlertModal()" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors">
                OK
            </button>
        </div>
    </div>
</div>

<!-- Contact Host Modal -->
<div id="contactHostModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="text-xl font-semibold text-gray-900">Contact Host</h3>
            <button onclick="closeContactHostModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                    <span class="material-symbols-outlined text-2xl">person</span>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500">Host Name</p>
                    <p class="text-lg font-semibold text-gray-900"><?= esc($currentRequest['host'] ?? 'N/A') ?></p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="bg-green-100 text-green-600 p-3 rounded-full">
                    <span class="material-symbols-outlined text-2xl">phone</span>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500">Contact Number</p>
                    <a href="tel:<?= esc($currentRequest['host_contact'] ?? '') ?>" class="text-lg font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                        <?= esc($currentRequest['host_contact'] ?? 'N/A') ?>
                    </a>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
                    <span class="material-symbols-outlined text-2xl">business</span>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500">Company</p>
                    <p class="text-base font-medium text-gray-900"><?= esc($currentRequest['company_visited'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>
        <div class="p-6 border-t bg-gray-50 flex gap-3">
            <button onclick="closeContactHostModal()" class="flex-1 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <a href="tel:<?= esc($currentRequest['host_contact'] ?? '') ?>" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium text-center hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">call</span>
                Call Now
            </a>
        </div>
    </div>
</div>

<script>
<?php if ($currentRequest): ?>
// Zoom image functionality
document.querySelectorAll('button').forEach(btn => {
    if (btn.querySelector('.material-symbols-outlined')?.textContent === 'zoom_in') {
        btn.addEventListener('click', function() {
            const govIdImage = '<?= esc($currentRequest['government_id_image'] ?? '') ?>';
            if (govIdImage) {
                document.getElementById('zoomedImage').src = govIdImage;
                document.getElementById('imageZoomModal').classList.remove('hidden');
            }
        });
    }
});

function closeImageZoom() {
    document.getElementById('imageZoomModal').classList.add('hidden');
}

// Close modal on background click
document.getElementById('imageZoomModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageZoom();
    }
});

// Past Visits button
document.querySelectorAll('button').forEach(btn => {
    const text = btn.textContent.trim();
    if (text.includes('Past Visits')) {
        btn.addEventListener('click', function() {
            const icPassport = '<?= esc($currentRequest['ic_passport']) ?>';
            showPastVisits(icPassport);
        });
    }
});

function showPastVisits(icPassport) {
    if (!icPassport) {
        showAlert('No Information', 'No visitor IC/Passport information available', 'error');
        return;
    }
    
    document.getElementById('pastVisitsModal').classList.remove('hidden');
    document.getElementById('pastVisitsContent').innerHTML = '<div class="flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';
    
    fetch('<?= base_url('requests/pastVisits') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ ic_passport: icPassport })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.visits.length > 0) {
            let html = '<div class="space-y-4">';
            data.visits.forEach(visit => {
                html += `
                    <div class="border rounded-lg p-4 hover:bg-gray-50">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Visit Date</p>
                                <p class="font-medium">${visit.visit_date}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full ${visit.status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                                    ${visit.status}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Purpose</p>
                                <p class="font-medium">${visit.purpose || 'N/A'}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Host Name</p>
                                <p class="font-medium">${visit.host_name || 'N/A'}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Company Visited</p>
                                <p class="font-medium">${visit.company_visited || 'N/A'}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Check-in Time</p>
                                <p class="font-medium">${visit.checked_in_at || 'Not checked in'}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            document.getElementById('pastVisitsContent').innerHTML = html;
        } else {
            document.getElementById('pastVisitsContent').innerHTML = '<div class="text-center py-8 text-gray-500">No past visits found for this visitor.</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('pastVisitsContent').innerHTML = '<div class="text-center py-8 text-red-500">Error loading past visits. Please try again.</div>';
    });
}

function closePastVisitsModal() {
    document.getElementById('pastVisitsModal').classList.add('hidden');
}

// Close modal on background click
document.getElementById('pastVisitsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePastVisitsModal();
    }
});

// Contact Host button
document.querySelectorAll('button').forEach(btn => {
    const text = btn.textContent.trim();
    if (text.includes('Contact Host')) {
        btn.addEventListener('click', function() {
            const hostContact = '<?= esc($currentRequest['host_contact'] ?? '') ?>';
            if (hostContact && hostContact !== 'N/A') {
                document.getElementById('contactHostModal').classList.remove('hidden');
            } else {
                showAlert('No Information', 'No host contact information available', 'error');
            }
        });
    }
});

function closeContactHostModal() {
    document.getElementById('contactHostModal').classList.add('hidden');
}

// Close modal on background click
document.getElementById('contactHostModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeContactHostModal();
    }
});

// More Info button
document.querySelectorAll('button').forEach(btn => {
    const text = btn.textContent.trim();
    if (text === 'More Info') {
        btn.addEventListener('click', function() {
            const info = 'IC/Passport: <?= esc($currentRequest['ic_passport']) ?>\nContact: <?= esc($currentRequest['contact']) ?>\nEmail: <?= esc($currentRequest['email']) ?>\nVehicle: <?= esc($currentRequest['vehicle']) ?>\nStaff ID: <?= esc($currentRequest['staff_id']) ?>\nCompany Visited: <?= esc($currentRequest['company_visited']) ?>';
            showAlert('Detailed Information', info, 'info');
        });
    }
});

// Reject button
document.querySelectorAll('button').forEach(btn => {
    const text = btn.textContent.trim();
    if (text.includes('Reject')) {
        btn.addEventListener('click', function() {
            showConfirm(
                'Reject Request',
                'Are you sure you want to reject this request?',
                'reject',
                function() {
                    const reason = document.getElementById('rejectReasonInput').value.trim();
                    if (reason) {
                        rejectRequest(<?= $currentRequest ? explode('-', $currentRequest['id'])[1] : 0 ?>, reason);
                    } else {
                        showAlert('Required Field', 'Please provide a reason for rejection', 'error');
                    }
                },
                true
            );
        });
    }
});

// Approve button
document.querySelectorAll('button').forEach(btn => {
    const text = btn.textContent.trim();
    if (text.includes('Approve Entry')) {
        btn.addEventListener('click', function() {
            showConfirm(
                'Approve Request',
                'Approve this visitor entry request?',
                'approve',
                function() {
                    approveRequest(<?= $currentRequest ? explode('-', $currentRequest['id'])[1] : 0 ?>);
                }
            );
        });
    }
});

function rejectRequest(id, reason) {
    fetch('<?= base_url('requests/reject') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ id: id, reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Success', 'Request rejected successfully', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('Error', data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Error', 'An error occurred: ' + error.message, 'error');
    });
}

function approveRequest(id) {
    fetch('<?= base_url('requests/approve') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Success', 'Request approved successfully', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('Error', data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Error', 'An error occurred: ' + error.message, 'error');
    });
}

// Download button for documents
document.querySelectorAll('button').forEach(btn => {
    if (btn.querySelector('.material-symbols-outlined')?.textContent === 'download') {
        btn.addEventListener('click', function() {
            const parent = this.closest('.flex');
            if (parent && parent.textContent.includes('Driver License')) {
                showAlert('Coming Soon', 'Driver license download feature coming soon', 'info');
            } else {
                const govIdImage = '<?= esc($currentRequest['government_id_image'] ?? '') ?>';
                if (govIdImage) {
                    window.open(govIdImage, '_blank');
                }
            }
        });
    }
});

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
<?php endif; ?>
</script>
</body>
</html>
