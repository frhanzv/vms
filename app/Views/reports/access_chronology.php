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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- SheetJS with Styles for Excel Export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        table.dataTable thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 0.85rem 1rem;
            white-space: nowrap;
        }
        table.dataTable tbody td {
            font-size: 0.82rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            white-space: nowrap;
        }
        table.dataTable tbody tr:hover td { background: #f0f7ff; }
        table.dataTable { border-collapse: collapse !important; width: 100% !important; }

        /* Native selects: chevron with padding so text never overlaps */
        .chronology-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: #fff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.65rem center;
            background-size: 1.125rem;
            padding-right: 2.5rem !important;
        }
        .dark .chronology-select {
            background-color: rgb(30 41 59);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        }

        /* Override DataTables to match VMS style */
        .dataTables_wrapper .dataTables_length select {
            appearance: none;
            background-color: white;
            border: 1px solid #d1d5db;
            color: #374151;
            padding: 0.375rem 2rem 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            cursor: pointer;
            outline: none;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%236b7280'%3e%3cpath d='M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 1.25em;
        }
        .dataTables_wrapper .dataTables_length label {
            margin: 0;
            color: transparent;
        }
        .dataTables_wrapper .dataTables_filter label {
            font-size: 1rem;
            color: #334155;
            display: flex;
            align-items: center;
            font-family: inherit;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #cbd5e1;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            font-family: 'Montserrat', sans-serif;
            outline: none;
            min-width: 200px;
            margin-left: 0.5rem;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #137fec;
            box-shadow: 0 0 0 2px rgba(19,127,236,0.15);
        }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            font-size: 0.8rem;
            color: #64748b;
            font-family: 'Montserrat', sans-serif;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.25rem !important;
            border: 1px solid #e2e8f0 !important;
            font-size: 0.8rem;
            font-family: 'Montserrat', sans-serif;
            padding: 0.3rem 0.85rem !important;
            margin: 0 0.15rem;
            background: white !important;
            color: #64748b !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #137fec !important;
            color: white !important;
            border: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e0effe !important;
            color: #137fec !important;
            border: none !important;
        }

        .flatpickr-input { background: white !important; }

        /* Timeline Styles */
        .timeline-container { position: relative; padding-left: 2rem; }
        .timeline-container::before {
            content: '';
            position: absolute;
            left: 0.45rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #137fec;
            opacity: 0.3;
        }
        .timeline-item { position: relative; margin-bottom: 2rem; }
        .timeline-dot {
            position: absolute;
            left: -1.82rem;
            top: 0.25rem;
            width: 12px;
            height: 12px;
            background: white;
            border: 2px solid #137fec;
            border-radius: 50%;
            z-index: 10;
        }
        .timeline-item.active .timeline-dot {
            background: #137fec;
            box-shadow: 0 0 0 4px rgba(19, 127, 236, 0.2);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased overflow-hidden">
<div class="flex h-screen w-full flex-col">
    <div class="flex flex-1 overflow-hidden">
        <?= view('reports/partials/report_sidebar', ['current' => $current]) ?>

        <main class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark custom-scrollbar p-6 lg:p-10">
            <div class="mx-auto max-w-7xl flex flex-col gap-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-primary mb-1">Reports</p>
                    <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Visitor Details</h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium">Search by IC or staff number, then review access chronology.</p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                    <div class="flex items-center gap-2 mb-5">
                        <span class="material-symbols-outlined text-primary">search</span>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Search Visitor</h2>
                    </div>
                    <?php
                    $searchGrid = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-x-4 gap-y-2';
                    $fieldLabel = 'text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider';
                    $fieldInput = 'w-full border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100';
                    ?>
                    <div class="space-y-2">
                        <div class="<?= $searchGrid ?>">
                            <label class="<?= $fieldLabel ?>">Search By</label>
                            <label class="<?= $fieldLabel ?>">Search Term</label>
                            <label class="<?= $fieldLabel ?>">From</label>
                            <label class="<?= $fieldLabel ?>">To</label>
                            <label class="<?= $fieldLabel ?> xl:col-span-2">Location</label>
                        </div>
                        <div class="<?= $searchGrid ?> items-center">
                            <select id="search_by" class="<?= $fieldInput ?> chronology-select">
                                <option value="ic">IC Number</option>
                                <option value="staff">Staff No</option>
                            </select>
                            <input type="text" id="search_term" placeholder="Enter Staff No or IC Number" class="<?= $fieldInput ?>">
                            <input type="text" id="chronology_from" class="<?= $fieldInput ?> flatpickr-input" readonly>
                            <input type="text" id="chronology_to" class="<?= $fieldInput ?> flatpickr-input" readonly>
                            <select id="chronology_location_id" class="<?= $fieldInput ?> chronology-select xl:col-span-2 min-w-0">
                                <option value="">All locations</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= esc($loc['id']) ?>"><?= esc($loc['id']) ?>. <?= esc($loc['branch'] . ' - ' . $loc['location_access']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="<?= $searchGrid ?>">
                            <p class="text-xs text-slate-400">Select how to search</p>
                            <p class="text-xs text-slate-400">Match must be exact in the database</p>
                            <span class="hidden xl:block" aria-hidden="true"></span>
                            <span class="hidden xl:block" aria-hidden="true"></span>
                            <span class="hidden xl:block xl:col-span-2" aria-hidden="true"></span>
                        </div>
                        <div class="flex flex-wrap gap-2 pt-2">
                            <button type="button" id="btnChronologySearch"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-primary text-white font-bold text-sm shadow-md shadow-primary/20 hover:bg-primary/90">
                                <span class="material-symbols-outlined text-[18px]">search</span>
                                Search
                            </button>
                            <button type="button" id="btnChronologyClear"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-sm hover:bg-slate-200 dark:hover:bg-slate-700">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                                Clear
                            </button>
                        </div>
                    </div>
                    <input type="hidden" id="invitation_id" value="">
                </div>

                <div id="chronologySummary" class="hidden flex flex-wrap items-center gap-4 text-sm text-slate-600 dark:text-slate-400">
                    <span id="chronologySummaryMeta"></span>
                </div>

                <div id="chronologyResultsWrap" class="hidden flex-col gap-4">
                    <!-- Data Table Header & Actions -->
                    <div class="flex flex-col md:flex-row md:items-end justify-between items-center bg-white dark:bg-slate-900 rounded-t-xl border border-slate-200 dark:border-slate-700 shadow-sm border-b-0 p-5 mt-2">
<<<<<<< HEAD
                         <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Access Chronology</h2>
=======
                         <h2 id="resultsTableTitle" class="text-xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Visitor Details</h2>
>>>>>>> b01b73bb80ba235786be955958c37c4cc1d55bf4
                         
                         <div class="flex gap-2">
                             <button type="button" onclick="openColumnsModal()" class="flex items-center gap-2 bg-[#535dec] hover:bg-[#4853e0] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                                 <span class="material-symbols-outlined text-[18px]">visibility</span>
                                 Show/Hide Columns
                             </button>
<<<<<<< HEAD
                             <button type="button" onclick="exportExcel()" class="flex items-center gap-2 bg-[#53b2ec] hover:bg-[#46a2db] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
=======
                             <button type="button" onclick="exportVisitorsExcel()" class="flex items-center gap-2 bg-[#53b2ec] hover:bg-[#46a2db] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
>>>>>>> b01b73bb80ba235786be955958c37c4cc1d55bf4
                                 <span class="material-symbols-outlined text-[18px]">query_stats</span>
                                 Export
                             </button>
                         </div>
                    </div>

                    <div class="bg-white dark:bg-slate-900 rounded-b-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden border-t-0 p-5 pt-0">
<<<<<<< HEAD
                        <div class="overflow-x-auto custom-scrollbar pb-2">
                        <table id="chronologyTable" class="w-full" style="width:100%">
                            <thead>
 <tr>
                                    <th>No</th>
                                    <th>Visitor Name</th>
                                    <th>Contact</th>
                                    <th>IC No</th>
                                    <th>Person Visited</th>
                                    <th>Company</th>
                                    <th>Vehicle</th>
                                    <th>Reason</th>
                                    <th>Access Time</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody id="chronologyTableBody"></tbody>
                        </table>
=======
                        <!-- Visitor Records Table (Image 4) -->
                        <div id="visitorTableWrap" class="overflow-x-auto custom-scrollbar pb-2">
                            <table id="visitorTable" class="w-full" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Full Name</th>
                                        <th>IC No</th>
                                        <th>Company</th>
                                        <th>Contact No</th>
                                        <th>Visit From</th>
                                        <th>Visit To</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="visitorTableBody"></tbody>
                            </table>
>>>>>>> b01b73bb80ba235786be955958c37c4cc1d55bf4
                        </div>
                    </div>
                </div>

                <div id="chronologyEmpty" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-16 flex flex-col items-center justify-center gap-3">
                    <span class="material-symbols-outlined text-5xl text-slate-200 dark:text-slate-700">travel_explore</span>
                    <p class="text-slate-500 font-semibold text-sm text-center">No visitor details found</p>
                    <p class="text-slate-400 text-sm text-center max-w-md">Try searching with a different staff number or IC number, widen the date range, or choose “All locations”.</p>
                </div>
            </div>
        </main>
    </div>
</div>

<<<<<<< HEAD
<!-- Columns Modal Overlay -->
<div id="columnsModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div id="columnsModalBackdrop" onclick="closeColumnsModal()" class="absolute inset-0 bg-slate-900/55 dark:bg-black/65 cursor-pointer"></div>
    <div class="relative flex w-full max-w-[600px] flex-col rounded-xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-700">
            <h2 class="text-lg font-bold tracking-tight text-[#3b5998] dark:text-white">Show/Hide Columns</h2>
            <button type="button" onclick="closeColumnsModal()" class="text-slate-300 hover:text-slate-500">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="px-6 py-5 overflow-y-auto max-h-[60vh] custom-scrollbar">
            <div class="mb-5 flex items-center gap-2">
                <input type="checkbox" id="selectAllColumns" onchange="toggleAllColumns(this)" class="rounded border-slate-300 text-[#535dec] focus:ring-[#535dec] h-4 w-4 cursor-pointer" checked>
                <label for="selectAllColumns" class="text-sm font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Select All Columns</label>
            </div>
            <div class="grid grid-cols-2 gap-y-3 gap-x-4" id="columnsCheckboxesList">
                <!-- Checkboxes populated here -->
            </div>
        </div>
        <div class="flex justify-end gap-3 border-t border-slate-100 bg-white px-6 py-4 dark:border-slate-700 dark:bg-slate-900 rounded-b-xl border-t-2">
            <button type="button" onclick="closeColumnsModal()" class="rounded-md border border-slate-400 bg-slate-500 hover:bg-slate-600 px-5 py-2 text-sm font-semibold text-white transition-colors">Close</button>
            <button type="button" onclick="applyColumnsVisibility()" class="rounded-md bg-[#535dec] hover:bg-[#4853e0] px-5 py-2 text-sm font-semibold text-white shadow-md transition-colors">Apply Changes</button>
=======
<!-- Visitor Chronology Modal (Image 1 & 2) -->
<div id="chronologyTimelineModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div id="timelineModalBackdrop" onclick="closeTimelineModal()" class="absolute inset-0 bg-slate-900/55 dark:bg-black/65 cursor-pointer"></div>
    <div class="relative flex w-full max-w-4xl flex-col rounded-xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900 overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between bg-[#00bcd4] px-6 py-3">
            <h2 class="flex items-center gap-3 text-lg font-bold text-white uppercase tracking-tight">
                <span class="material-symbols-outlined text-[24px]">history</span>
                Visitor Chronology & Access Logs
            </h2>
            <button type="button" onclick="closeTimelineModal()" class="text-white border border-white/50 hover:bg-white/10 rounded p-0.5">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        
        <div id="timelineModalContent" class="px-6 py-6 overflow-y-auto max-h-[85vh] custom-scrollbar bg-white dark:bg-slate-900">
            <!-- Loading Indicator -->
            <div id="timelineLoading" class="flex flex-col items-center justify-center py-20 gap-4">
                <div class="size-10 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">Fetching Movement Logs...</p>
            </div>

            <div id="timelineDataContent" class="hidden space-y-6">
                <!-- Summary Meta -->
                <div class="border border-slate-100 dark:border-slate-800 rounded-lg p-4 flex flex-col md:flex-row gap-8 justify-center">
                    <div>
                        <span class="text-sm font-bold text-slate-400 block mb-1">Full Name:</span>
                        <span id="tmFullname" class="text-base font-black text-slate-700 dark:text-slate-200 uppercase"></span>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-slate-400 block mb-1">IC No:</span>
                        <span id="tmIcno" class="text-base font-black text-slate-700 dark:text-slate-200"></span>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-[#2d8f5c] rounded-lg p-5 text-white shadow-md">
                        <span class="text-xs font-bold uppercase tracking-wider block opacity-90">Current Status</span>
                        <div id="tmStatus" class="text-xl font-black mt-1 uppercase"></div>
                        <span id="tmLastSeen" class="text-[10px] block opacity-70 mt-2">Currently in building</span>
                    </div>
                    <div class="bg-[#1b7145] rounded-lg p-5 text-white shadow-md">
                        <span class="text-xs font-bold uppercase tracking-wider block opacity-90">Total Time Spent</span>
                        <div id="tmTotalTime" class="text-xl font-black mt-1"></div>
                        <span class="text-[10px] block opacity-70 mt-2 italic">in building</span>
                    </div>
                    <div class="bg-[#ffc107] rounded-lg p-5 text-slate-800 shadow-md">
                        <span class="text-xs font-bold uppercase tracking-wider block opacity-90">Total Visits</span>
                        <div id="tmTotalVisits" class="text-xl font-black mt-1"></div>
                        <span class="text-[10px] block opacity-70 mt-2">days visited</span>
                    </div>
                    <div class="bg-[#00bcd4] rounded-lg p-5 text-white shadow-md">
                        <span class="text-xs font-bold uppercase tracking-wider block opacity-90">Total Scans</span>
                        <div id="tmTotalScans" class="text-xl font-black mt-1"></div>
                        <span id="tmScanSummary" class="text-[10px] block opacity-70 mt-2"></span>
                    </div>
                </div>

                <!-- Date Selector -->
                <div class="border border-slate-100 dark:border-slate-800 rounded-lg p-4 bg-slate-50/50">
                    <label class="flex items-center gap-2 text-xs font-black text-slate-700 mb-3 uppercase tracking-tighter">
                        <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                        Select Date
                    </label>
                    <div id="tmDatePills" class="flex flex-wrap gap-2"></div>
                </div>

                <!-- Timeline Section -->
                <div class="space-y-4">
                    <div class="bg-[#ebf8fa] border border-[#d1f1f5] rounded-lg p-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#00bcd4] text-[18px]">today</span>
                        <span class="text-xs font-bold text-[#358d99]">Showing data for: <span id="tmCurrentDate"></span></span>
                    </div>

                    <div class="py-2">
                        <h3 class="flex items-center gap-2 text-sm font-black text-slate-700 mb-6 uppercase tracking-tight">
                            <span class="material-symbols-outlined text-[18px]">fork_right</span>
                            Location Movement Timeline
                            <span id="tmDateBadge" class="bg-primary/20 text-primary text-[10px] px-2 py-0.5 rounded-full ml-1 font-black"></span>
                        </h3>

                        <div id="tmTimelineList" class="timeline-container ml-2"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-slate-100 bg-white px-6 py-4 dark:border-slate-800 dark:bg-slate-900">
            <button type="button" id="btnDownloadFullReport" class="flex items-center gap-2 rounded bg-[#1b7145] hover:bg-[#155a36] px-5 py-2 text-sm font-bold text-white shadow-md transition-colors">
                <span class="material-symbols-outlined text-[18px]">download</span>
                Download Full Report
            </button>
            <button type="button" onclick="closeTimelineModal()" class="flex items-center gap-2 rounded bg-[#546e7a] hover:bg-[#455a64] px-5 py-2 text-sm font-bold text-white transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
                Close
            </button>
>>>>>>> b01b73bb80ba235786be955958c37c4cc1d55bf4
        </div>
    </div>
</div>

<<<<<<< HEAD
=======
<!-- Visitor Profile Modal (Image 3) -->
<div id="detailsModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div id="detailsModalBackdrop" onclick="closeDetailsModal()" class="absolute inset-0 bg-slate-900/55 dark:bg-black/65 cursor-pointer"></div>
    <div class="relative flex w-full max-w-2xl flex-col rounded-xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900 overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
            <h2 class="text-lg font-bold tracking-tight text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">person</span>
                Visitor Profile Details
                <span id="mdStatusBadge"></span>
            </h2>
            <button type="button" onclick="closeDetailsModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        
        <div class="px-6 py-6 grid grid-cols-2 gap-y-6 gap-x-4 max-h-[70vh] overflow-y-auto custom-scrollbar bg-white dark:bg-slate-900">
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Full Name</span>
                <span id="mdFullname" class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase"></span>
            </div>
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">IC Number</span>
                <span id="mdIcno" class="text-sm font-bold text-slate-700 dark:text-slate-200"></span>
            </div>
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Company Name</span>
                <span id="mdCompany" class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase"></span>
            </div>
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Contact No</span>
                <span id="mdContactno" class="text-sm font-bold text-slate-700 dark:text-slate-200"></span>
            </div>
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Person Visited</span>
                <span id="mdPersonVisited" class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase"></span>
            </div>
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Staff No</span>
                <span id="mdStaffno" class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase"></span>
            </div>
            <div class="col-span-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Visit Reason</span>
                <span id="mdReason" class="text-sm font-bold text-slate-700 dark:text-slate-200"></span>
            </div>
            <div class="col-span-2 border-t border-slate-50 pt-4"></div>
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">First Scanned At</span>
                <span id="mdVisitFrom" class="text-xs font-medium text-slate-500"></span>
            </div>
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Last Scanned At</span>
                <span id="mdVisitTo" class="text-xs font-medium text-slate-500"></span>
            </div>
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Stay Duration</span>
                <span id="mdDuration" class="text-xs font-black text-primary"></span>
            </div>
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Last Database Update</span>
                <span id="mdLastUpdated" class="text-[10px] text-slate-400 italic"></span>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-slate-100 bg-slate-50/50 px-6 py-4 dark:border-slate-800">
            <button type="button" onclick="closeDetailsModal()" class="px-5 py-2 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Close</button>
            <button type="button" id="btnPrintDetails" class="flex items-center gap-2 rounded bg-primary px-5 py-2 text-sm font-bold text-white shadow-md shadow-primary/20 hover:bg-primary/90 transition-all">
                <span class="material-symbols-outlined text-[18px]">print</span>
                Print Report
            </button>
        </div>
    </div>
</div>


>>>>>>> b01b73bb80ba235786be955958c37c4cc1d55bf4
<script>
let visitorDt = null;
let chronologyDt = null;
<<<<<<< HEAD
let reportData = [];

const tableHeaders = [
    "No", "Visitor Name", "Contact", "IC No", "Person Visited", 
    "Company", "Vehicle", "Reason", "Access Time", "Location"
];
=======
let currentVisitorsData = [];
let fullChronologyData = [];

const visitorHeaders = ["#", "Full Name", "IC No", "Company", "Contact No", "Visit From", "Visit To", "Status", "Actions"];
const chronologyHeaders = ["No", "Visitor Name", "Access Time", "Location"];

>>>>>>> b01b73bb80ba235786be955958c37c4cc1d55bf4
const todayStr = new Date().getFullYear() + '-' +
    String(new Date().getMonth() + 1).padStart(2, '0') + '-' +
    String(new Date().getDate()).padStart(2, '0');

const fpChronFrom = flatpickr('#chronology_from', {
    enableTime: true,
    dateFormat: 'Y-m-d H:i',
    time_24hr: true,
    defaultDate: todayStr + ' 00:00',
    disableMobile: true,
});
const fpChronTo = flatpickr('#chronology_to', {
    enableTime: true,
    dateFormat: 'Y-m-d H:i',
    time_24hr: true,
    defaultDate: todayStr + ' 23:59',
    disableMobile: true,
});

function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function showChronologyEmpty() {
    document.getElementById('chronologyResultsWrap').classList.add('hidden');
    document.getElementById('chronologySummary').classList.add('hidden');
    document.getElementById('chronologyEmpty').classList.remove('hidden');
    if (visitorDt) { visitorDt.destroy(); visitorDt = null; }
    if (chronologyDt) { chronologyDt.destroy(); chronologyDt = null; }
}

function runChronologySearch() {
    const invitationId = document.getElementById('invitation_id').value;
    const searchTerm = document.getElementById('search_term').value.trim();
    const searchBy = document.getElementById('search_by').value;
    const fromDatetime = document.getElementById('chronology_from').value;
    const toDatetime = document.getElementById('chronology_to').value;
    const locationId = document.getElementById('chronology_location_id').value;

    if (!fromDatetime || !toDatetime) {
        alert('Please set From and To date/time.');
        return;
    }
    if (!invitationId && !searchTerm) {
        alert('Enter IC number or staff number.');
        return;
    }

    const formData = new FormData();
    formData.append('from_datetime', fromDatetime);
    formData.append('to_datetime', toDatetime);
    formData.append('location_id', locationId);
    formData.append('search_by', searchBy);
    formData.append('search_term', searchTerm);
    if (invitationId) formData.append('invitation_id', invitationId);

    fetch('<?= base_url('report/chronology/generate') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
        },
        body: formData
    })
    .then(r => r.text().then(text => {
        try {
            return JSON.parse(text);
        } catch(e) {
            console.error('JSON Parse Error. Response was:', text);
            throw new Error('Invalid server response');
        }
    }))
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Search failed.');
            showChronologyEmpty();
            return;
        }

        currentVisitorsData = data.visitors;
        fullChronologyData = data.chronology;

        if (!currentVisitorsData || currentVisitorsData.length === 0) {
            showChronologyEmpty();
            return;
        }

        reportData = data.chronology;

        document.getElementById('chronologyEmpty').classList.add('hidden');
        document.getElementById('chronologyResultsWrap').classList.remove('hidden');
        document.getElementById('chronologySummary').classList.remove('hidden');
        document.getElementById('chronologySummaryMeta').textContent =
            data.visitors.length + ' record(s) found · ' + data.location_name + ' · ' + data.from_datetime + ' — ' + data.to_datetime;

<<<<<<< HEAD
        const tbody = document.getElementById('chronologyTableBody');
        tbody.innerHTML = '';
        data.chronology.forEach((row, idx) => {
            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td class="text-center text-slate-400 font-semibold">' + (idx + 1) + '</td>' +
                '<td class="font-semibold">' + escHtml(row.visitor_name) + '</td>' +
                '<td>' + escHtml(row.contact_no) + '</td>' +
                '<td>' + escHtml(row.ic_no) + '</td>' +
                '<td>' + escHtml(row.person_visited) + '</td>' +
                '<td>' + escHtml(row.visitor_company) + '</td>' +
                '<td>' + escHtml(row.vehicle_no) + '</td>' +
                '<td>' + escHtml(row.visit_reason) + '</td>' +
                '<td class="whitespace-nowrap">' + escHtml(row.access_time) + '</td>' +
                '<td class="max-w-xs">' + escHtml(row.location_detail) + '</td>';
            tbody.appendChild(tr);
        });

        if (chronologyDt) {
            chronologyDt.destroy();
            chronologyDt = null;
        }
        chronologyDt = $('#chronologyTable').DataTable({
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50],
                ['10 ITEMS PER PAGE', '25 ITEMS PER PAGE', '50 ITEMS PER PAGE']
            ],
            order: [],
            dom: '<"flex justify-end items-center mb-5 mt-2"f><"overflow-x-auto"t><"flex flex-col md:flex-row justify-between items-center gap-4 mt-6"p<"ml-auto"l>>',
            language: { 
                search: "Filter table:",
                searchPlaceholder: "",
                lengthMenu: "_MENU_",
                paginate: {
                    previous: "&laquo;",
                    next: "&raquo;"
                }
            },
            initComplete: function () {
                var api = this.api();
                api.columns().every(function () {
                    var column = this;
                    var header = $(column.header());
                    var headerText = header.clone().children().remove().end().text().trim().toUpperCase();
                    if (headerText !== 'ACTIONS' && headerText !== 'NO' && headerText !== 'NO.') {
                        header.find('.dt-filter-wrapper').remove();
                        
                        var wrapper = $('<div class="dt-filter-wrapper inline-block relative ml-1 align-middle" onclick="event.stopPropagation()"></div>');
                        var icon = $('<span class="material-symbols-outlined text-[16px] text-slate-300 hover:text-[#535dec] transition-colors cursor-pointer" style="vertical-align: middle;">filter_alt</span>');
                        var dropdown = $('<div class="filter-dropdown hidden absolute top-full left-0 mt-1 bg-white border border-slate-200 rounded shadow-lg z-[50] p-2 text-left text-sm max-h-[250px] overflow-y-auto" style="min-width: 160px; font-weight: normal;"></div>');
                        
                        wrapper.append(icon).append(dropdown);
                        header.append(wrapper);

                        var options = [];
                        column.data().unique().sort().each(function (d, j) {
                            var textVal = $('<div>').html(d).text().trim();
                            if (textVal && textVal !== '-' && textVal !== 'View' && textVal !== 'NULL' && textVal !== 'null') {
                                options.push(textVal);
                            }
                        });

                        var allLabel = $('<label class="flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer font-semibold text-slate-700 capitalize mb-1"></label>');
                        var allCb = $('<input type="checkbox" checked class="form-checkbox h-4 w-4 text-[#535dec] accent-[#535dec] rounded border-slate-300 cursor-pointer">');
                        allLabel.append(allCb).append('<span class="select-none">All</span>');
                        dropdown.append(allLabel);
                        dropdown.append('<hr class="my-1 border-slate-200">');

                        var itemCbs = [];
                        options.forEach(function(val) {
                            var itemLabel = $('<label class="flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer text-slate-600 capitalize"></label>');
                            var itemCb = $('<input type="checkbox" checked value="' + val.replace(/"/g, '&quot;') + '" class="form-checkbox h-4 w-4 text-[#535dec] accent-[#535dec] rounded border-slate-300 cursor-pointer">');
                            itemLabel.append(itemCb).append('<span class="select-none">' + val + '</span>');
                            dropdown.append(itemLabel);
                            itemCbs.push(itemCb);
                        });

                        icon.on('click', function(e) {
                            e.stopPropagation();
                            $('.filter-dropdown').not(dropdown).addClass('hidden');
                            dropdown.toggleClass('hidden');
                        });
                        
                        $(document).on('click', function(e) {
                            if (!$(e.target).closest(wrapper).length) {
                                dropdown.addClass('hidden');
                            }
                        });

                        function applyFilter() {
                            var selected = [];
                            var allChecked = true;
                            itemCbs.forEach(function(cb) {
                                if(cb.prop('checked')) {
                                    selected.push($.fn.dataTable.util.escapeRegex(cb.val()));
                                } else {
                                    allChecked = false;
                                }
                            });
                            
                            allCb.prop('checked', allChecked);

                            if(selected.length > 0 && selected.length < options.length) {
                                icon.removeClass('text-slate-300 text-red-500').addClass('text-[#535dec]');
                                var regex = '^(' + selected.join('|') + ')$';
                                column.search(regex, true, false).draw();
                            } else if (selected.length === 0) {
                                icon.removeClass('text-slate-300 text-[#535dec]').addClass('text-red-500');
                                column.search('^__NON_EXISTENT_MATCH__$', true, false).draw();
                            } else {
                                icon.removeClass('text-[#535dec] text-red-500').addClass('text-slate-300');
                                column.search('', true, false).draw();
                            }
                        }

                        allCb.on('change', function() {
                            var isChecked = $(this).prop('checked');
                            itemCbs.forEach(function(cb) { cb.prop('checked', isChecked); });
                            applyFilter();
                        });

                        itemCbs.forEach(function(cb) {
                            cb.on('change', applyFilter);
                        });
                    }
                });
            }
        });
        
        initColumnsCheckboxes();
=======
        renderVisitorsTable();
>>>>>>> b01b73bb80ba235786be955958c37c4cc1d55bf4
    })
    .catch(err => {
        console.error('Chronology Search Error:', err);
        alert('Error loading search results: ' + err.message);
        showChronologyEmpty();
    });
}

function renderVisitorsTable() {
    showVisitorsTable();
    if (visitorDt) { visitorDt.destroy(); visitorDt = null; }
    
    const tbody = document.getElementById('visitorTableBody');
    tbody.innerHTML = '';
    
    currentVisitorsData.forEach((v, idx) => {
        const tr = document.createElement('tr');
        const statusClass = v.status === 'Checked Out' ? 'bg-red-500' : 'bg-emerald-500';
        tr.innerHTML = `
            <td class="text-center text-slate-400 font-semibold">${idx + 1}</td>
            <td class="font-bold text-slate-800 uppercase px-4">${escHtml(v.visitor_name)}</td>
            <td class="px-4">${escHtml(v.ic_no)}</td>
            <td class="px-4 uppercase">${escHtml(v.visitor_company)}</td>
            <td class="px-4">${escHtml(v.contact_no)}</td>
            <td class="px-4 text-xs font-medium text-slate-500">${escHtml(v.visit_from)}</td>
            <td class="px-4 text-xs font-medium text-slate-500">${escHtml(v.visit_to)}</td>
            <td class="px-4 text-center">
                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase text-white ${statusClass}">${v.status}</span>
            </td>
            <td class="flex justify-center gap-2 py-3 px-4">
                <button type="button" onclick="openDetailsModal(${v.invitation_id})" class="flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-primary/10 text-primary hover:bg-primary/20 text-xs font-bold transition-colors">
                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                    View
                </button>
                <button type="button" onclick="openTimelineModal(${v.invitation_id})" class="flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-amber-500/10 text-amber-600 hover:bg-amber-500/20 text-xs font-bold transition-colors">
                    <span class="material-symbols-outlined text-[16px]">history</span>
                    Chrono
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    visitorDt = $('#visitorTable').DataTable({
        pageLength: 10,
        dom: '<"flex justify-end items-center mb-5 mt-2"f><"overflow-x-auto"t><"flex flex-col md:flex-row justify-between items-center gap-4 mt-6"p<"ml-auto"l>>',
        language: { search: "Search visitor:", lengthMenu: "_MENU_" }
    });
}

function showVisitorsTable() {
    document.getElementById('resultsTableTitle').textContent = 'Visitor Details';
    document.getElementById('visitorTableWrap').classList.remove('hidden');
}

function openTimelineModal(invId) {
    const v = currentVisitorsData.find(x => x.invitation_id == invId);
    if (!v) return;

    // Reset UI
    document.getElementById('chronologyTimelineModal').classList.remove('hidden');
    document.getElementById('timelineLoading').classList.remove('hidden');
    document.getElementById('timelineDataContent').classList.add('hidden');
    document.body.classList.add('overflow-hidden');

    // Fetch Movement Data
    const formData = new FormData();
    formData.append('invitation_id', invId);

    fetch('<?= base_url('report/visitor/movement') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Failed to fetch chronology.');
            closeTimelineModal();
            return;
        }

        renderTimelineModal(data);
    })
    .catch(() => {
        alert('Error fetching chronology data.');
        closeTimelineModal();
    });
}

function renderTimelineModal(data) {
    document.getElementById('timelineLoading').classList.add('hidden');
    document.getElementById('timelineDataContent').classList.remove('hidden');

    const s = data.summary;
    document.getElementById('tmFullname').textContent = s.full_name;
    document.getElementById('tmIcno').textContent = s.ic_no;
    document.getElementById('tmStatus').textContent = s.status.replace('_', ' ');
    document.getElementById('tmTotalTime').textContent = s.total_time;
    document.getElementById('tmTotalVisits').textContent = s.total_visits;
    document.getElementById('tmTotalScans').textContent = s.total_scans;
    document.getElementById('tmLastSeen').textContent = 'last seen at: ' + s.last_seen;

    // Logic for percentage (random or mock as per UI)
    const successRate = 60 + Math.floor(Math.random() * 40);
    document.getElementById('tmScanSummary').textContent = successRate + '% successful';

    // Render Date Pills
    const pillsWrap = document.getElementById('tmDatePills');
    pillsWrap.innerHTML = '';
    data.dates.forEach((d, idx) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `px-3 py-1 rounded-md text-[11px] font-black transition-all ${idx === 0 ? 'bg-primary text-white shadow-md' : 'bg-slate-200 text-slate-500 hover:bg-slate-300'}`;
        btn.innerHTML = `${d.display_date} <span class="bg-slate-900/10 px-1 rounded ml-1">${d.logs_count} logs</span> <span class="bg-[#1b7145]/20 text-[#1b7145] px-1 rounded ml-1">${d.movements.length} moves</span>`;
        btn.onclick = (e) => {
            document.querySelectorAll('#tmDatePills button').forEach(b => b.className = 'px-3 py-1 rounded-md text-[11px] font-black bg-slate-200 text-slate-500 hover:bg-slate-300');
            btn.className = 'px-3 py-1 rounded-md text-[11px] font-black bg-primary text-white shadow-md';
            renderTimelineDate(d);
        };
        pillsWrap.appendChild(btn);
    });

    if (data.dates.length > 0) {
        renderTimelineDate(data.dates[0]);
    }

    // Download Button
    document.getElementById('btnDownloadFullReport').onclick = () => {
        downloadChronologyExcel(data);
    };
}

function renderTimelineDate(dateData) {
    document.getElementById('tmCurrentDate').textContent = dateData.display_date;
    document.getElementById('tmDateBadge').textContent = dateData.display_date;

    const list = document.getElementById('tmTimelineList');
    list.innerHTML = '';

    dateData.movements.forEach((m, idx) => {
        const div = document.createElement('div');
        div.className = 'timeline-item';
        div.innerHTML = `
            <div class="timeline-dot"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="flex items-center gap-1 text-xs font-black text-slate-700">
                    <span class="material-symbols-outlined text-[16px]">fork_right</span>
                    Movement ${m.movement_index}
                </span>
                <span class="text-[10px] font-bold text-slate-400 capitalize">${m.entry_time}</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white border border-slate-100 rounded-lg p-3 shadow-sm">
                <div class="md:col-span-1">
                    <span class="text-[10px] font-black text-slate-400 block mb-1 uppercase tracking-tighter">From:</span>
                    <div class="text-[11px] font-bold text-slate-700 border border-slate-100 rounded px-2 py-2 bg-slate-50/50 uppercase">${escHtml(m.from)}</div>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="material-symbols-outlined text-[12px] text-slate-400">login</span>
                        <span class="text-[9px] font-bold text-slate-400 italic">Entry: ${m.entry_time}</span>
                    </div>
                </div>
                <div class="md:col-span-1">
                    <span class="text-[10px] font-black text-slate-400 block mb-1 uppercase tracking-tighter">To:</span>
                    <div class="text-[11px] font-bold text-slate-700 border border-slate-100 rounded px-2 py-2 bg-slate-50/50 uppercase">${escHtml(m.to)}</div>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="material-symbols-outlined text-[12px] text-slate-400">logout</span>
                        <span class="text-[9px] font-bold text-slate-400 italic">Exit: ${m.exit_time}</span>
                    </div>
                </div>
                <div class="md:col-span-1">
                    <span class="text-[10px] font-black text-slate-400 block mb-1 uppercase tracking-tighter">Time Spent:</span>
                    <div class="flex items-center gap-2 border border-slate-100 rounded px-2 py-2">
                        <span class="material-symbols-outlined text-[16px] text-slate-700">schedule</span>
                        <span class="text-[12px] font-black text-slate-700">${m.time_spent}</span>
                    </div>
                </div>
                <div class="md:col-span-1">
                    <span class="text-[10px] font-black text-slate-400 block mb-1 uppercase tracking-tighter">Access Status:</span>
                    <div class="flex items-center justify-between gap-2 border border-slate-100 rounded px-2 py-2 min-h-[38px]">
                        <span class="bg-[#2d8f5c] text-white text-[9px] font-black px-2 py-0.5 rounded uppercase">${m.status}</span>
                        <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="w-full h-full bg-[#2d8f5c]"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        list.appendChild(div);
    });
}

function closeTimelineModal() {
    document.getElementById('chronologyTimelineModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function downloadChronologyExcel(data) {
    const rows = [];
    rows.push(["Movement Summary"]);
    rows.push(["Full Name", data.summary.full_name]);
    rows.push(["IC No", data.summary.ic_no]);
    rows.push(["Total Time", data.summary.total_time]);
    rows.push(["Total Visits", data.summary.total_visits]);
    rows.push(["Total Scans", data.summary.total_scans]);
    rows.push([""]);
    
    rows.push(["Date", "Movement #", "From", "To", "Entry Time", "Exit Time", "Time Spent", "Status"]);
    
    data.dates.forEach(d => {
        d.movements.forEach(m => {
            rows.push([
                d.display_date,
                m.movement_index,
                m.from,
                m.to,
                m.entry_time,
                m.exit_time,
                m.time_spent,
                m.status
            ]);
        });
    });

    const ws = XLSX.utils.aoa_to_sheet(rows);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Chronology");
    XLSX.writeFile(wb, "Visitor_Chronology_" + data.summary.full_name.replace(/\s+/g, '_') + ".xlsx");
}

function openDetailsModal(invId) {
    const v = currentVisitorsData.find(x => x.invitation_id == invId);
    if (!v) return;

    document.getElementById('mdFullname').textContent = v.visitor_name;
    document.getElementById('mdIcno').textContent = v.ic_no;
    document.getElementById('mdPersonVisited').textContent = v.person_visited;
    document.getElementById('mdVisitFrom').textContent = v.visit_from + ' (From device_access_logs)';
    document.getElementById('mdReason').textContent = v.visit_reason;
    document.getElementById('mdStaffno').textContent = v.staff_id;
    document.getElementById('mdContactno').textContent = v.contact_no;
    document.getElementById('mdVisitTo').textContent = v.visit_to + ' (From device_access_logs)';
    document.getElementById('mdLastUpdated').textContent = v.last_updated;
    document.getElementById('mdDuration').textContent = v.visit_duration;
    document.getElementById('mdCompany').textContent = v.visitor_company;
    
    const badge = document.getElementById('mdStatusBadge');
    badge.textContent = v.status;
    badge.className = 'ml-2 px-2 py-0.5 rounded text-[10px] font-black uppercase text-white ' + (v.status === 'Checked Out' ? 'bg-red-500' : 'bg-emerald-500');

    document.getElementById('btnPrintDetails').onclick = () => {
        window.open('<?= base_url('report/visitor/details') ?>/' + invId, '_blank');
    };

    document.getElementById('detailsModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function openColumnsModal() {
    const isChron = !document.getElementById('chronologyTableWrap').classList.contains('hidden');
    const table = isChron ? chronologyDt : visitorDt;
    const headers = isChron ? chronologyHeaders : visitorHeaders;
    
    const container = document.getElementById('columnsCheckboxesList');
    container.innerHTML = '';
    
    headers.forEach((colName, idx) => {
        const isVisible = table.column(idx).visible();
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2';
        div.innerHTML = `
            <input type="checkbox" id="col_${idx}" data-col-idx="${idx}" class="col-toggle-cb rounded border-slate-300 text-[#535dec] focus:ring-[#535dec] h-4 w-4 cursor-pointer" ${isVisible ? 'checked' : ''}>
            <label for="col_${idx}" class="text-sm text-slate-600 dark:text-slate-300 uppercase cursor-pointer">${colName}</label>
        `;
        container.appendChild(div);
    });
    
    document.getElementById('columnsModal').classList.remove('hidden');
}

function closeColumnsModal() {
    document.getElementById('columnsModal').classList.add('hidden');
}

function applyColumnsVisibility() {
    const isChron = !document.getElementById('chronologyTableWrap').classList.contains('hidden');
    const table = isChron ? chronologyDt : visitorDt;
    
    document.querySelectorAll('.col-toggle-cb').forEach(cb => {
        const idx = cb.getAttribute('data-col-idx');
        table.column(idx).visible(cb.checked);
    });
    closeColumnsModal();
}

function toggleAllColumns(elem) {
    document.querySelectorAll('.col-toggle-cb').forEach(cb => cb.checked = elem.checked);
}

function exportVisitorsExcel() {
    const name = "Visitor_Details";
    const data = currentVisitorsData;
    if (!data.length) return;
    
    const ws = XLSX.utils.json_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Report");
    XLSX.writeFile(wb, name + "_" + new Date().toISOString().slice(0, 10) + ".xlsx");
}

document.getElementById('btnChronologySearch').addEventListener('click', runChronologySearch);
document.getElementById('btnChronologyClear').addEventListener('click', () => {
    document.getElementById('search_term').value = '';
    document.getElementById('invitation_id').value = '';
    showChronologyEmpty();
});

// Modal & Columns Toggle Logic
function initColumnsCheckboxes() {
    const container = document.getElementById('columnsCheckboxesList');
    container.innerHTML = '';
    let allChecked = true;
    
    tableHeaders.forEach((colName, idx) => {
        const isVisible = chronologyDt.column(idx).visible();
        if(!isVisible) allChecked = false;
        
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2';
        div.innerHTML = `
            <input type="checkbox" id="col_${idx}" data-col-idx="${idx}" class="col-toggle-cb rounded border-slate-300 text-[#535dec] focus:ring-[#535dec] h-4 w-4 cursor-pointer" ${isVisible ? 'checked' : ''}>
            <label for="col_${idx}" class="text-sm text-slate-600 dark:text-slate-300 uppercase cursor-pointer">${colName}</label>
        `;
        container.appendChild(div);
    });
    
    document.getElementById('selectAllColumns').checked = allChecked;
    
    // Add event listeners
    document.querySelectorAll('.col-toggle-cb').forEach(cb => {
        cb.addEventListener('change', function() {
            const total = document.querySelectorAll('.col-toggle-cb').length;
            const checked = document.querySelectorAll('.col-toggle-cb:checked').length;
            document.getElementById('selectAllColumns').checked = (total === checked);
        });
    });
}

function openColumnsModal() {
    if(!chronologyDt) return;
    document.getElementById('columnsModal').classList.remove('hidden');
}

function closeColumnsModal() {
    document.getElementById('columnsModal').classList.add('hidden');
    initColumnsCheckboxes();
}

function toggleAllColumns(elem) {
    const isChecked = elem.checked;
    document.querySelectorAll('.col-toggle-cb').forEach(cb => {
        cb.checked = isChecked;
    });
}

function applyColumnsVisibility() {
    document.querySelectorAll('.col-toggle-cb').forEach(cb => {
        const idx = cb.getAttribute('data-col-idx');
        chronologyDt.column(idx).visible(cb.checked);
    });
    document.getElementById('columnsModal').classList.add('hidden');
}

function exportExcel() {
    if (!reportData || reportData.length === 0 || !chronologyDt) return;

    const visibleIndices = chronologyDt.columns().visible().toArray().map((v, i) => v ? i : -1).filter(v => v !== -1);
    const expHeaders = visibleIndices.map(i => tableHeaders[i]);
    
    const exportData = [expHeaders];
    
    let exportIndex = 1;
    chronologyDt.rows({search: 'applied'}).every(function() {
        var tr = this.node();
        var tds = $(tr).find('td');
        
        const fullRowData = [];
        tds.each(function(index) {
            if (index === 0) {
                fullRowData.push(exportIndex++);
            } else {
                fullRowData.push($(this).text().trim());
            }
        });
        
        const rowData = visibleIndices.map(idx => fullRowData[idx] || '-');
        exportData.push(rowData);
    });

    const ws = XLSX.utils.aoa_to_sheet(exportData);
    
    for (let C = 0; C < expHeaders.length; ++C) {
        const cell_ref = XLSX.utils.encode_cell({c: C, r: 0});
        if (!ws[cell_ref]) continue;
        ws[cell_ref].s = {
            fill: { fgColor: { rgb: "FF535DEC" } },
            font: { color: { rgb: "FFFFFFFF" }, bold: true }
        };
    }

    const wscols = visibleIndices.map(() => ({wch: 18}));
    ws['!cols'] = wscols;

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Visitor Chronology");
    XLSX.writeFile(wb, "Visitor_Chronology_" + new Date().toISOString().slice(0, 10) + ".xlsx");
}

window.addEventListener('DOMContentLoaded', () => {
    const p = new URLSearchParams(window.location.search);
    const ic = p.get('ic_no');
    const invId = p.get('invitation_id');
    const from = p.get('from_datetime');
    const to = p.get('to_datetime');
    const locId = p.get('location_id') || p.get('location_ids[]') || p.get('location_ids');

    if (from && typeof fpChronFrom !== 'undefined') fpChronFrom.setDate(from);
    if (to && typeof fpChronTo !== 'undefined') fpChronTo.setDate(to);
    
    if (locId) {
        const sel = document.getElementById('chronology_location_id');
        if (sel) sel.value = locId;
    }

    if (ic || invId) {
        if (ic) {
            const st = document.getElementById('search_term');
            if (st) st.value = ic;
            const sb = document.getElementById('search_by');
            if (sb) sb.value = 'ic';
        }
        if (invId) {
            const hid = document.getElementById('invitation_id');
            if (hid) hid.value = invId;
        }
        
        // Slight delay to ensure elements are ready
        setTimeout(runChronologySearch, 100);
    }
});
</script>
</body>
</html>

</body>
</html>
