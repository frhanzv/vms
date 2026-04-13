<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle ?? 'Blacklist Individual Request List') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
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
                        "sans": ["Montserrat", "sans-serif"],
                        "brand": ["Montserrat", "sans-serif"],
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white overflow-hidden">
    <div class="flex h-screen w-full">

        <!-- Sidebar -->
        <aside class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark flex flex-col justify-between p-4 hidden md:flex h-full">
            <div class="flex flex-col gap-8">
                <div class="flex items-center gap-3 px-2">
                    <div class="bg-center bg-no-repeat bg-cover rounded-lg size-10 bg-primary/10 flex items-center justify-center text-primary">
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
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('logbook') ?>">
                        <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">menu_book</span>
                        <p class="text-sm font-medium">Visitor Logbook</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('workflow') ?>">
                        <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">account_tree</span>
                        <p class="text-sm font-medium">Visitor Workflow</p>
                    </a>

                    <!-- Blacklist Dropdown (active) -->
                    <div x-data="{ 
                            openBlacklist: <?= str_contains($current, 'blacklist') ? 'true' : 'false' ?>, 
                            openIndividual: <?= str_contains($current, 'blacklist') ? 'true' : 'false' ?> 
                        }">
                        <button type="button" @click="openBlacklist = !openBlacklist"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg bg-primary/10 text-primary transition-colors group">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[22px]">person_cancel</span>
                                <p class="text-sm font-semibold">Blacklist</p>
                            </div>
                            <span class="material-symbols-outlined text-[18px] transition-transform duration-200" :class="openBlacklist ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        <div x-show="openBlacklist" class="ml-4 mt-1 flex flex-col gap-1">
                            <div>
                                <button type="button" @click="openIndividual = !openIndividual"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-primary bg-primary/5 transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-[18px]">person</span>
                                        <p class="text-sm font-semibold">Individual</p>
                                    </div>
                                    <span class="material-symbols-outlined text-[16px] transition-transform duration-200" :class="openIndividual ? 'rotate-180' : ''">expand_more</span>
                                </button>
                                <div x-show="openIndividual" class="ml-4 mt-1 flex flex-col gap-1">
                                    <a href="<?= base_url('blacklist/blacklistrequest') ?>"
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-colors text-sm font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 flex-shrink-0"></span>
                                        Request List
                                    </a>
                                    <a href="<?= base_url('blacklist/closedlist') ?>"
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary/10 text-primary text-sm font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary flex-shrink-0"></span>
                                        Closed List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Report Dropdown -->
                    <div x-data="{ openReport: <?= str_contains($current, 'report') ? 'true' : 'false' ?> }">
                        <button type="button" @click="openReport = !openReport"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg <?= str_contains($current, 'report') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary' ?> transition-colors group">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">description</span>
                                <p class="text-sm font-medium">REPORT</p>
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
                                ACCESS REPORT
                            </a>
                            <a href="<?= base_url('report/visitor') ?>"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'report/visitor' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $current == 'report/visitor' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                                VISITOR REPORT
                            </a>
                            <a href="<?= base_url('report/chronology') ?>"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'report/chronology') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'report/chronology') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                                VISITOR CHRONOLOGY
                            </a>
                            <a href="<?= base_url('report/bydoor') ?>"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'report/bydoor') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'report/bydoor') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                                VISITOR INFO BY DOOR
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

        <main class="flex-1 flex flex-col h-screen overflow-hidden">
            <div class="flex-1 overflow-y-auto p-6 md:p-8 no-scrollbar">

                <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-5">

                    <div class="flex items-center justify-between mb-4">
                        
                        <h2 class="text-lg font-semibold text-gray-700 uppercase tracking-tight">
                            Blacklist Individual Closed List
                        </h2>

                        <div class="flex items-center gap-2">
                            <a href="<?= base_url('blacklist/closedlist/export') ?>"
                                class="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary hover:bg-primary-dark text-white text-sm font-bold transition-colors shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">download</span>
                                Export
                            </a>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                        <div class="md:col-span-6 flex gap-0">
                            <input
                                type="text"
                                name="search"
                                placeholder="IC NO / PASSPORT NO / NAME / STAFF ID"
                                class="flex-1 h-10 px-4 text-sm bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 dark:text-white placeholder-slate-400 font-sans uppercase placeholder:normal-case"
                            />
                            <button type="button"
                                class="flex items-center justify-center h-10 w-10 bg-primary hover:bg-primary-dark text-white rounded-r-lg transition-colors flex-shrink-0">
                                <span class="material-symbols-outlined text-[20px]">search</span>
                            </button>
                        </div>

                        <div class="md:col-span-3">
                            <select name="type_of_blacklist"
                                class="w-full h-10 pl-3 pr-8 text-sm bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-600 dark:text-slate-300 font-sans appearance-none cursor-pointer">
                                <option value="">TYPE OF BLACKLIST</option>
                                <option value="visitor">Visitor</option>
                                <option value="staff">Staff</option>
                                <option value="contractor">Contractor</option>
                            </select>
                        </div>

                        <div class="md:col-span-3">
                            <select name="sort_by"
                                class="w-full h-10 pl-3 pr-8 text-sm bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-600 dark:text-slate-300 font-sans appearance-none cursor-pointer">
                                <option value="">SORT BY</option>
                                <option value="released_desc">Released Date (Newest)</option>
                                <option value="name_asc">Name (A-Z)</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto w-full rounded-xl border border-slate-200 dark:border-slate-700">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">No</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Created Date</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Blacklist Date</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">IC / Passport No</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Staff ID</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Released Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-surface-dark">
                                <?php if (!empty($closed_blacklist)): ?>
                                    <?php foreach ($closed_blacklist as $index => $entry): ?>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                        <td class="px-4 py-3.5 text-sm text-slate-500 dark:text-slate-400 font-medium"><?= $index + 1 ?></td>
                                        <td class="px-4 py-3.5 text-sm text-slate-600 dark:text-slate-300"><?= esc($entry['created_date']) ?></td>
                                        <td class="px-4 py-3.5 text-sm text-slate-600 dark:text-slate-300"><?= esc($entry['blacklist_date']) ?></td>
                                        <td class="px-4 py-3.5 text-sm text-slate-600 dark:text-slate-300 font-mono"><?= esc($entry['ic_passport_no']) ?></td>
                                        <td class="px-4 py-3.5 text-sm text-slate-600 dark:text-slate-300"><?= esc($entry['staff_id'] ?? '') ?></td>
                                        <td class="px-4 py-3.5 text-sm font-semibold text-slate-900 dark:text-white uppercase"><?= esc($entry['name']) ?></td>
                                        <td class="px-4 py-3.5 text-sm text-slate-600 dark:text-slate-300"><?= esc($entry['type']) ?></td>
                                        <td class="px-4 py-3.5 text-sm text-slate-600 dark:text-slate-300"><?= esc($entry['released_date'] ?? '—') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <div class="size-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600">history</span>
                                                </div>
                                                <p class="text-sm font-bold text-slate-500 dark:text-slate-400">No Closed Records</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center gap-1">
                            <button class="flex items-center justify-center size-8 rounded border border-slate-200 text-slate-400 hover:bg-slate-100" disabled>«</button>
                            <button class="flex items-center justify-center size-8 rounded border border-primary bg-primary text-white text-xs font-bold">1</button>
                            <button class="flex items-center justify-center size-8 rounded border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs">2</button>
                            <button class="flex items-center justify-center size-8 rounded border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs">3</button>
                            <button class="flex items-center justify-center size-8 rounded border border-slate-200 text-slate-400 hover:bg-slate-100">»</button>
                        </div>
                        
                        <select name="per_page"
                            class="h-9 pl-3 pr-7 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 outline-none appearance-none cursor-pointer">
                            <option value="10">10 ITEMS PER PAGE</option>
                            <option value="25">25 ITEMS PER PAGE</option>
                            <option value="50">50 ITEMS PER PAGE</option>
                        </select>
                    </div>

                </div>
            </div>
        </main>
    <script>
    
    </script>
</body>
</html>