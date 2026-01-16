<!DOCTYPE html>
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
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden transition-colors duration-200">
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
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary group transition-colors" href="<?= base_url('invitations') ?>">
                    <span class="material-symbols-outlined text-[22px] font-medium fill-1 group-hover:scale-110 transition-transform">mail</span>
                    <p class="text-sm font-semibold">Invitations</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('requests') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">assignment</span>
                    <p class="text-sm font-medium">Request List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('visitors') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">group</span>
                    <p class="text-sm font-medium">Visitors List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('logbook') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">menu_book</span>
                    <p class="text-sm font-medium">Visitor Logbook</p>
                </a>
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
    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8 bg-background-light dark:bg-background-dark">
        <div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-7xl">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">
                    Invitation List
                </h1>
                <div class="flex gap-2">
                    <button class="bg-secondary hover:bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">file_download</span>
                        Export
                    </button>
                    <a href="<?= base_url('invitations/create') ?>" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">add</span>
                        New Invitation
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-indigo-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Invitations</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['total']) ?></p>
                        <p class="text-[10px] text-gray-400 mt-1">All invitation records</p>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-full text-indigo-600 dark:text-indigo-400">
                        <span class="material-symbols-outlined text-2xl">mail</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-orange-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pending</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['pending']) ?></p>
                        <p class="text-[10px] text-orange-500 mt-1 font-medium">Awaiting approval</p>
                    </div>
                    <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-full text-orange-600 dark:text-orange-400">
                        <span class="material-symbols-outlined text-2xl">pending_actions</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-green-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Approved</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['approved']) ?></p>
                        <p class="text-[10px] text-green-600 mt-1 font-medium">Ready for visit</p>
                    </div>
                    <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-full text-green-600 dark:text-green-400">
                        <span class="material-symbols-outlined text-2xl">check_circle</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-red-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Rejected</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['rejected']) ?></p>
                        <p class="text-[10px] text-red-500 mt-1 font-medium">Declined requests</p>
                    </div>
                    <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-full text-red-600 dark:text-red-400">
                        <span class="material-symbols-outlined text-2xl">cancel</span>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col gap-4 mb-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                    <div class="lg:col-span-6 flex shadow-sm">
                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-xs focus:ring-primary focus:border-primary outline-none" placeholder="IC / PASSPORT / FULL NAME / CONTACT / COMPANY NAME" type="text"/>
                        <button class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-r flex items-center justify-center transition-colors">
                            <span class="material-icons text-white">search</span>
                        </button>
                    </div>
                    <div class="lg:col-span-3">
                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs focus:ring-primary focus:border-primary uppercase placeholder-gray-500 dark:placeholder-gray-400" placeholder="DATE FROM" type="text"/>
                    </div>
                    <div class="lg:col-span-3">
                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs focus:ring-primary focus:border-primary uppercase placeholder-gray-500 dark:placeholder-gray-400" placeholder="DATE TO" type="text"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="relative">
                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                            <option>STATUS</option>
                            <option>Pending</option>
                            <option>Approved</option>
                            <option>Rejected</option>
                        </select>
                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                    </div>
                    <div class="relative">
                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                            <option>REASON</option>
                            <option>SITE VISIT</option>
                            <option>DELIVERY</option>
                            <option>MAINTENANCE</option>
                            <option>CATERING</option>
                            <option>OTHER</option>
                        </select>
                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                    </div>
                    <div class="relative">
                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                            <option>LOCATION</option>
                            <option>PHASE 1</option>
                            <option>PHASE 2</option>
                            <option>WORKSHOP PHASE 2</option>
                            <option>KSB PHASE 2 GATE</option>
                        </select>
                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                    </div>
                    <div class="relative">
                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                            <option>DATE DESC</option>
                            <option>DATE ASC</option>
                            <option>NAME A-Z</option>
                            <option>NAME Z-A</option>
                        </select>
                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                    </div>
                    <button class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2.5 rounded text-xs font-semibold uppercase shadow transition-colors">
                        <span class="material-icons text-sm mr-1 align-middle">filter_alt_off</span>
                        Clear Filters
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-6">
                <table class="w-full min-w-max text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-bold uppercase tracking-wide">
                            <th class="p-4 border-b dark:border-gray-600">No</th>
                            <th class="p-4 border-b dark:border-gray-600">Date</th>
                            <th class="p-4 border-b dark:border-gray-600">Full Name</th>
                            <th class="p-4 border-b dark:border-gray-600">IC / Passport No</th>
                            <th class="p-4 border-b dark:border-gray-600">Contact No</th>
                            <th class="p-4 border-b dark:border-gray-600">Company</th>
                            <th class="p-4 border-b dark:border-gray-600">Vehicle Registration</th>
                            <th class="p-4 border-b dark:border-gray-600">Location</th>
                            <th class="p-4 border-b dark:border-gray-600">Invited By</th>
                            <th class="p-4 border-b dark:border-gray-600">Status</th>
                            <th class="p-4 border-b dark:border-gray-600">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                        <?php foreach ($invitations as $invitation): ?>
                        <tr onclick='openDetailModal(<?= json_encode($invitation) ?>)' class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border-b border-gray-100 dark:border-gray-700 cursor-pointer">
                            <td class="p-4"><?= $invitation['no'] ?></td>
                            <td class="p-4"><?= esc($invitation['date']) ?></td>
                            <td class="p-4 font-semibold text-gray-800 dark:text-white"><?= esc($invitation['full_name']) ?></td>
                            <td class="p-4"><?= esc($invitation['ic_passport']) ?></td>
                            <td class="p-4"><?= esc($invitation['contact']) ?></td>
                            <td class="p-4"><?= esc($invitation['company']) ?></td>
                            <td class="p-4 <?= empty($invitation['vehicle_reg']) ? 'text-gray-400' : '' ?>">
                                <?= empty($invitation['vehicle_reg']) ? 'NULL' : esc($invitation['vehicle_reg']) ?>
                            </td>
                            <td class="p-4"><?= esc($invitation['location']) ?></td>
                            <td class="p-4"><?= esc($invitation['invited_by']) ?></td>
                            <td class="p-4">
                                <?php if ($invitation['status'] === 'Approved'): ?>
                                <span class="bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 px-2 py-1 rounded-full text-[10px] uppercase font-bold">Approved</span>
                                <?php elseif ($invitation['status'] === 'Pending'): ?>
                                <span class="bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300 px-2 py-1 rounded-full text-[10px] uppercase font-bold">Pending</span>
                                <?php elseif ($invitation['status'] === 'Rejected'): ?>
                                <span class="bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 px-2 py-1 rounded-full text-[10px] uppercase font-bold">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4"><?= esc($invitation['reason']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-medium text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-1">
                    <button class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors disabled:opacity-50">«</button>
                    <button class="w-8 h-8 flex items-center justify-center bg-primary text-white rounded shadow-sm">1</button>
                    <button class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">2</button>
                    <button class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">3</button>
                    <span class="w-8 h-8 flex items-center justify-center">...</span>
                    <button class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">53</button>
                    <button class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">»</button>
                </div>
                <div class="relative">
                    <select class="appearance-none bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 py-1.5 pl-3 pr-8 rounded focus:outline-none focus:ring-1 focus:ring-primary text-xs font-medium cursor-pointer shadow-sm">
                        <option>10 ITEMS PER PAGE</option>
                        <option>25 ITEMS PER PAGE</option>
                        <option>50 ITEMS PER PAGE</option>
                    </select>
                    <span class="absolute right-2 top-1.5 pointer-events-none material-icons text-sm text-gray-500">expand_more</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Detail Modal -->
    <div id="detailModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between z-10">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">description</span>
                    Invitation Details
                </h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-6 space-y-6">
                <!-- Status Badge -->
                <div class="flex items-center justify-center">
                    <span id="modalStatus" class="px-4 py-2 rounded-full text-sm font-bold"></span>
                </div>

                <!-- Visitor Information -->
                <div>
                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">person</span>
                        Visitor Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Full Name</p>
                            <p id="modalFullName" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">IC / Passport No</p>
                            <p id="modalIcPassport" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Contact No</p>
                            <p id="modalContact" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Company</p>
                            <p id="modalCompany" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                    </div>
                </div>

                <!-- Visit Details -->
                <div>
                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">event</span>
                        Visit Details
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Visit Date</p>
                            <p id="modalDate" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Location</p>
                            <p id="modalLocation" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Reason for Visit</p>
                            <p id="modalReason" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Vehicle Registration</p>
                            <p id="modalVehicle" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                        </div>
                    </div>
                </div>

                <!-- Host Information -->
                <div>
                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">badge</span>
                        Host Information
                    </h4>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium mb-1">Invited By</p>
                        <p id="modalInvitedBy" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 bg-gray-50 dark:bg-slate-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex gap-3 justify-end">
                <button onclick="closeDetailModal()" class="px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors duration-200">
                    Close
                </button>
                <button class="px-4 py-2.5 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">send</span>
                    Send
                </button>
            </div>
        </div>
    </div>

    <script>
        function openDetailModal(invitation) {
            // Set status with appropriate styling
            const statusEl = document.getElementById('modalStatus');
            if (invitation.status === 'Approved') {
                statusEl.className = 'px-4 py-2 rounded-full text-sm font-bold bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300';
                statusEl.textContent = 'Approved';
            } else if (invitation.status === 'Pending') {
                statusEl.className = 'px-4 py-2 rounded-full text-sm font-bold bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300';
                statusEl.textContent = 'Pending';
            } else if (invitation.status === 'Rejected') {
                statusEl.className = 'px-4 py-2 rounded-full text-sm font-bold bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300';
                statusEl.textContent = 'Rejected';
            }

            // Fill in the details
            document.getElementById('modalFullName').textContent = invitation.full_name;
            document.getElementById('modalIcPassport').textContent = invitation.ic_passport;
            document.getElementById('modalContact').textContent = invitation.contact;
            document.getElementById('modalCompany').textContent = invitation.company;
            document.getElementById('modalDate').textContent = invitation.date;
            document.getElementById('modalLocation').textContent = invitation.location;
            document.getElementById('modalReason').textContent = invitation.reason;
            document.getElementById('modalVehicle').textContent = invitation.vehicle_reg || 'Not Provided';
            document.getElementById('modalInvitedBy').textContent = invitation.invited_by;

            // Show modal
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // Close modal on backdrop click
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDetailModal();
            }
        });
    </script>
</body>
</html>
