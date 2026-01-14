<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
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
                        primary: "#4f46e5",
                        secondary: "#3b82f6",
                        success: "#10b981",
                        "background-light": "#f3f4f6",
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
    <aside class="w-72 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex-col hidden lg:flex z-20 shadow-sm">
        <div class="h-24 flex items-center px-8">
            <div class="flex items-center gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/30 p-2.5 rounded-xl text-blue-500 dark:text-blue-400">
                    <span class="material-symbols-outlined text-3xl">shield_person</span>
                </div>
                <span class="font-bold text-2xl tracking-tight text-gray-900 dark:text-white">SafeG</span>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto py-4 px-6">
            <nav class="space-y-4">
                <a class="flex items-center gap-4 px-4 py-3 rounded-xl text-sm font-semibold text-gray-500 dark:text-gray-400 hover:bg-gray-50 hover:text-indigo-600 dark:hover:bg-gray-700 dark:hover:text-white transition-colors group" href="<?= base_url('dashboard') ?>">
                    <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">dashboard</span>
                    Dashboard
                </a>
                <a class="flex items-center gap-4 px-4 py-3 rounded-xl text-sm font-semibold text-gray-500 dark:text-gray-400 hover:bg-gray-50 hover:text-indigo-600 dark:hover:bg-gray-700 dark:hover:text-white transition-colors group" href="#">
                    <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">mail</span>
                    Invitations
                </a>
                <a class="flex items-center gap-4 px-4 py-3 rounded-xl text-sm font-semibold bg-nav-active dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 shadow-sm" href="<?= base_url('visitors') ?>">
                    <span class="material-symbols-outlined text-2xl">groups</span>
                    Visitors List
                </a>
                <a class="flex items-center gap-4 px-4 py-3 rounded-xl text-sm font-semibold text-gray-500 dark:text-gray-400 hover:bg-gray-50 hover:text-indigo-600 dark:hover:bg-gray-700 dark:hover:text-white transition-colors group" href="<?= base_url('logbook') ?>">
                    <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">menu_book</span>
                    Visitor Logbook
                </a>
                <a class="flex items-center gap-4 px-4 py-3 rounded-xl text-sm font-semibold text-gray-500 dark:text-gray-400 hover:bg-gray-50 hover:text-indigo-600 dark:hover:bg-gray-700 dark:hover:text-white transition-colors group" href="#">
                    <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">settings</span>
                    Settings
                </a>
            </nav>
        </div>
        <div class="p-6">
            <div class="flex items-center gap-3 border-t border-gray-100 dark:border-gray-700 pt-6">
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm shadow-sm ring-2 ring-white dark:ring-gray-800">
                    <?= strtoupper(substr(session()->get('full_name') ?? 'U', 0, 2)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate"><?= esc(session()->get('full_name') ?? 'User') ?></p>
                    <p class="text-xs text-gray-500 truncate"><?= esc(ucfirst(session()->get('role') ?? 'User')) ?></p>
                </div>
                <a href="<?= base_url('auth/logout') ?>" class="text-gray-400 hover:text-gray-600 dark:hover:text-white p-1 rounded-full hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <span class="material-icons text-xl">logout</span>
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
                    Visitor Pass List
                </h1>
                <div class="flex gap-2">
                    <button class="bg-secondary hover:bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">file_download</span>
                        Export
                    </button>
                    <button class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">add</span>
                        Request
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-indigo-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Visitors</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['total']) ?></p>
                        <p class="text-[10px] text-gray-400 mt-1">Total recorded entries</p>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-full text-indigo-600 dark:text-indigo-400">
                        <span class="material-symbols-outlined text-2xl">groups</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-emerald-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Checked In</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['checkedIn']) ?></p>
                        <p class="text-[10px] text-emerald-600 mt-1 font-medium">Currently on site</p>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-full text-emerald-600 dark:text-emerald-400">
                        <span class="material-symbols-outlined text-2xl">how_to_reg</span>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 border-orange-500 shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pending Approval</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['pending']) ?></p>
                        <p class="text-[10px] text-orange-500 mt-1 font-medium">Action required</p>
                    </div>
                    <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-full text-orange-600 dark:text-orange-400">
                        <span class="material-symbols-outlined text-2xl">pending_actions</span>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col gap-4 mb-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                    <div class="lg:col-span-5 flex shadow-sm">
                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-xs focus:ring-primary focus:border-primary outline-none" placeholder="IC / PASSPORT / VISITOR PASS NO / FULL NAME / VEHICLE REGISTRATION NO" type="text"/>
                        <button class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-r flex items-center justify-center transition-colors">
                            <span class="material-icons text-white">search</span>
                        </button>
                    </div>
                    <div class="lg:col-span-4 flex gap-2">
                        <button class="bg-success hover:bg-emerald-600 text-white px-4 py-2.5 rounded text-xs font-semibold uppercase shadow transition-colors flex-1 text-center whitespace-nowrap">
                            Read MyKad
                        </button>
                        <button class="bg-success hover:bg-emerald-600 text-white px-4 py-2.5 rounded text-xs font-semibold uppercase shadow transition-colors flex-1 text-center whitespace-nowrap">
                            Return Card
                        </button>
                    </div>
                    <div class="lg:col-span-3">
                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs focus:ring-primary focus:border-primary uppercase placeholder-gray-500 dark:placeholder-gray-400" placeholder="DATE OF VISIT TO" type="text"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="relative">
                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                            <option>VISIT TYPE</option>
                            <option>Walk-In</option>
                            <option>Invitation</option>
                        </select>
                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                    </div>
                    <div class="relative">
                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                            <option>APP DATE</option>
                        </select>
                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                    </div>
                    <div>
                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs focus:ring-primary focus:border-primary uppercase text-gray-500 dark:text-gray-300" placeholder="DATE FROM" type="text"/>
                    </div>
                    <div>
                        <input class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs focus:ring-primary focus:border-primary uppercase text-gray-500 dark:text-gray-300" placeholder="DATE TO" type="text"/>
                    </div>
                    <div class="relative">
                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                            <option>DATE TIME DESC</option>
                        </select>
                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                    </div>
                    <div class="relative">
                        <select class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-xs appearance-none focus:ring-primary focus:border-primary text-gray-500 dark:text-gray-300">
                            <option>KSB</option>
                            <option>Other Locations</option>
                        </select>
                        <span class="absolute right-3 top-2.5 pointer-events-none text-gray-400 material-icons text-sm">expand_more</span>
                    </div>
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
                            <th class="p-4 border-b dark:border-gray-600">Vehicle Registration Number</th>
                            <th class="p-4 border-b dark:border-gray-600">Location</th>
                            <th class="p-4 border-b dark:border-gray-600">Type</th>
                            <th class="p-4 border-b dark:border-gray-600">Card Status</th>
                            <th class="p-4 border-b dark:border-gray-600">Visitor Pass No</th>
                            <th class="p-4 border-b dark:border-gray-600">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                        <?php foreach ($visitors as $visitor): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border-b border-gray-100 dark:border-gray-700">
                            <td class="p-4"><?= $visitor['no'] ?></td>
                            <td class="p-4"><?= esc($visitor['date']) ?></td>
                            <td class="p-4 font-semibold text-gray-800 dark:text-white"><?= esc($visitor['full_name']) ?></td>
                            <td class="p-4"><?= esc($visitor['ic_passport']) ?></td>
                            <td class="p-4"><?= esc($visitor['contact']) ?></td>
                            <td class="p-4 <?= empty($visitor['vehicle_reg']) ? 'text-gray-400' : '' ?>">
                                <?= empty($visitor['vehicle_reg']) ? 'NULL' : esc($visitor['vehicle_reg']) ?>
                            </td>
                            <td class="p-4"><?= esc($visitor['location']) ?></td>
                            <td class="p-4"><?= esc($visitor['type']) ?></td>
                            <td class="p-4">
                                <?php if ($visitor['card_status'] === 'Active'): ?>
                                <span class="bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 px-2 py-1 rounded-full text-[10px] uppercase font-bold">Active</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4"><?= esc($visitor['pass_no'] ?? '') ?></td>
                            <td class="p-4"><?= esc($visitor['reason']) ?></td>
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
                    <button class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">1392</button>
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
</body>
</html>
