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
                        "sans": ["Montserrat", "sans-serif"],
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
    <?= view('partials/sidebar') ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <div class="flex-1 overflow-y-auto p-6 md:p-8 no-scrollbar">

            <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
            <?php endif; ?>

            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-5">

                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-700 uppercase tracking-tight">
                        Blacklist Individual Request List
                    </h2>
                    <div class="flex items-center gap-2">
                        <a href="<?= base_url('files/Blacklist_Individual_Request_List.xlsx') ?>"
                            download
                            class="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary hover:bg-primary-dark text-white text-sm font-bold transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            Export
                        </a>
                        <a href="<?= site_url('blacklist/entry') ?>"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary hover:bg-primary-dark text-white text-sm font-bold transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                            Entry
                        </a>
                    </div>
                </div>

                <!-- Search + Filters (decorative, no data) -->
                <div class="flex items-center gap-3">
                    <div class="flex flex-1 gap-0">
                        <input type="text"
                            placeholder="IC / PASSPORT / FULL NAME / STAFF NO"
                            disabled
                            class="flex-1 h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-l-lg text-slate-400 placeholder-slate-300 font-sans uppercase placeholder:normal-case cursor-not-allowed"/>
                        <button type="button" disabled
                            class="flex items-center justify-center h-10 w-10 bg-slate-200 text-slate-400 rounded-r-lg cursor-not-allowed flex-shrink-0">
                            <span class="material-symbols-outlined text-[20px]">search</span>
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <select disabled class="h-10 pl-3 pr-8 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-400 font-sans appearance-none cursor-not-allowed min-w-[180px]">
                        <option>TYPE OF BLACKLIST</option>
                    </select>
                    <select disabled class="h-10 pl-3 pr-8 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-400 font-sans appearance-none cursor-not-allowed min-w-[160px]">
                        <option>STATUS</option>
                    </select>
                    <select disabled class="h-10 pl-3 pr-8 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-400 font-sans appearance-none cursor-not-allowed min-w-[180px]">
                        <option>SORT BY</option>
                    </select>
                </div>

                <!-- Table -->
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
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-surface-dark">
                            <tr>
                                <td colspan="8" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="size-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600">person_cancel</span>
                                        </div>
                                        <p class="text-sm font-bold text-slate-500 dark:text-slate-400">No Data Available</p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500">Click <strong>+ Entry</strong> to blacklist an individual.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (decorative) -->
                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center gap-1">
                        <button disabled class="flex items-center justify-center size-8 rounded border border-slate-200 text-slate-300 cursor-not-allowed text-xs font-bold">«</button>
                        <button class="flex items-center justify-center size-8 rounded border border-primary bg-primary text-white text-xs font-bold">1</button>
                        <button disabled class="flex items-center justify-center size-8 rounded border border-slate-200 text-slate-300 cursor-not-allowed text-xs font-bold">»</button>
                    </div>
                    <select class="h-9 pl-3 pr-7 text-xs bg-white border border-slate-200 rounded-lg text-slate-600 outline-none font-sans appearance-none cursor-pointer">
                        <option value="10">10 ITEMS PER PAGE</option>
                        <option value="25">25 ITEMS PER PAGE</option>
                        <option value="50">50 ITEMS PER PAGE</option>
                    </select>
                </div>

            </div>
        </div>
    </main>
</div>
</body>
</html>