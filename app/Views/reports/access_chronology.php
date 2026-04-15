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
                         <h2 id="resultsTableTitle" class="text-xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Visitor Details</h2>
                         
                         <div class="flex gap-2">
                             <button type="button" id="btnBackToVisitors" onclick="showVisitorsTable()" class="hidden flex items-center gap-2 bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                                 <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                 Back to Records
                             </button>
                             <button type="button" onclick="openColumnsModal()" class="flex items-center gap-2 bg-[#535dec] hover:bg-[#4853e0] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                                 <span class="material-symbols-outlined text-[18px]">visibility</span>
                                 Show/Hide Columns
                             </button>
                             <button type="button" onclick="exportExcel()" class="flex items-center gap-2 bg-[#53b2ec] hover:bg-[#46a2db] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                                 <span class="material-symbols-outlined text-[18px]">query_stats</span>
                                 Export
                             </button>
                         </div>
                    </div>

                    <div class="bg-white dark:bg-slate-900 rounded-b-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden border-t-0 p-5 pt-0">
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
                        </div>

                        <!-- Access Chronology Table (Image 5) -->
                        <div id="chronologyTableWrap" class="hidden overflow-x-auto custom-scrollbar pb-2">
                            <table id="chronologyTable" class="w-full" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Visitor Name</th>
                                        <th>Access Time</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody id="chronologyTableBody"></tbody>
                            </table>
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

<!-- Visitor Details Modal (Image 3) -->
<div id="detailsModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div id="detailsModalBackdrop" onclick="closeDetailsModal()" class="absolute inset-0 bg-slate-900/55 dark:bg-black/65 cursor-pointer"></div>
    <div class="relative flex w-full max-w-2xl flex-col rounded-xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900 overflow-hidden">
        <!-- Blue Header -->
        <div class="flex items-center justify-between bg-primary px-6 py-3">
            <h2 class="flex items-center gap-3 text-lg font-bold text-white">
                <span class="material-symbols-outlined text-[24px]">account_circle</span>
                Visitor Details
            </h2>
            <button type="button" onclick="closeDetailsModal()" class="text-white/80 hover:text-white transition-colors bg-white/10 hover:bg-white/20 rounded-lg p-1">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        
        <div class="px-6 py-6 overflow-y-auto max-h-[80vh] custom-scrollbar bg-white dark:bg-slate-900">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Full Name:</label>
                        <div id="mdFullname" class="w-full border border-slate-100 rounded-md px-3 py-2 text-sm font-medium text-slate-700 bg-slate-50/50 uppercase"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">IC No:</label>
                        <div id="mdIcno" class="w-full border border-slate-100 rounded-md px-3 py-2 text-sm font-medium text-slate-700 bg-slate-50/50"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Person Visited:</label>
                        <div id="mdPersonVisited" class="w-full border border-slate-100 rounded-md px-3 py-2 text-sm font-medium text-slate-700 bg-slate-50/50 uppercase"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Visit From:</label>
                        <div id="mdVisitFrom" class="w-full border border-slate-100 rounded-md px-3 py-2 text-xs font-medium text-slate-600 bg-slate-50/50"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Search Type:</label>
                        <div id="mdSearchType" class="w-full border border-slate-100 rounded-md px-3 py-2 text-sm font-medium text-slate-700 bg-slate-50/50 italic">Auto Detect</div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Reason for Visit:</label>
                        <div id="mdReason" class="w-full border border-slate-100 rounded-md px-3 py-2 text-sm font-medium text-slate-700 bg-slate-50/50 uppercase"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Staff No:</label>
                        <div id="mdStaffno" class="w-full border border-slate-100 rounded-md px-3 py-2 text-sm font-medium text-slate-700 bg-slate-50/50"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Contact No:</label>
                        <div id="mdContactno" class="w-full border border-slate-100 rounded-md px-3 py-2 text-sm font-medium text-slate-700 bg-slate-50/50"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Visit To:</label>
                        <div id="mdVisitTo" class="w-full border border-slate-100 rounded-md px-3 py-2 text-xs font-medium text-slate-600 bg-slate-50/50"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Last Updated:</label>
                        <div id="mdLastUpdated" class="w-full border border-slate-100 rounded-md px-3 py-2 text-sm font-medium text-slate-700 bg-slate-50/50"></div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-xs font-semibold text-slate-500 mb-1">Visit Duration:</label>
                <div class="w-full border border-slate-100 rounded-md px-3 py-2 text-sm font-bold text-primary bg-blue-50/30">
                    <span id="mdDuration"></span>
                    <span id="mdStatusBadge" class="ml-2 px-2 py-0.5 rounded text-[10px] font-black uppercase text-white"></span>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-semibold text-slate-500 mb-1 text-[16px]">Company Name:</label>
                <div id="mdCompany" class="w-full border border-slate-100 rounded-md px-3 py-3 text-sm font-medium text-slate-700 bg-slate-50/50 uppercase min-h-[60px]"></div>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-slate-100 bg-white px-6 py-4 dark:border-slate-800 dark:bg-slate-900">
            <button type="button" onclick="closeDetailsModal()" class="flex items-center gap-2 rounded-md bg-slate-500 hover:bg-slate-600 px-5 py-2 text-sm font-bold text-white transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
                Close
            </button>
            <button type="button" id="btnPrintDetails" class="flex items-center gap-2 rounded-md bg-primary hover:bg-primary/90 px-5 py-2 text-sm font-bold text-white shadow-md transition-colors">
                <span class="material-symbols-outlined text-[18px]">print</span>
                Print
            </button>
        </div>
    </div>
</div>


<script>
let visitorDt = null;
let chronologyDt = null;
let currentVisitorsData = [];
let fullChronologyData = [];

const visitorHeaders = ["#", "Full Name", "IC No", "Company", "Contact No", "Visit From", "Visit To", "Status", "Actions"];
const chronologyHeaders = ["No", "Visitor Name", "Access Time", "Location"];

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
    .then(r => r.json())
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

        document.getElementById('chronologyEmpty').classList.add('hidden');
        document.getElementById('chronologyResultsWrap').classList.remove('hidden');
        document.getElementById('chronologySummary').classList.remove('hidden');
        document.getElementById('chronologySummaryMeta').textContent =
            data.visitors.length + ' record(s) found · ' + data.location_name + ' · ' + data.from_datetime + ' — ' + data.to_datetime;

        renderVisitorsTable();
    })
    .catch(() => {
        alert('Error loading search results.');
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
                <button type="button" onclick="showChronologyForPerson(${v.invitation_id})" class="flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-amber-500/10 text-amber-600 hover:bg-amber-500/20 text-xs font-bold transition-colors">
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
    document.getElementById('chronologyTableWrap').classList.add('hidden');
    document.getElementById('btnBackToVisitors').classList.add('hidden');
}

function showChronologyForPerson(invitationId) {
    document.getElementById('resultsTableTitle').textContent = 'Access Chronology';
    document.getElementById('visitorTableWrap').classList.add('hidden');
    document.getElementById('chronologyTableWrap').classList.remove('hidden');
    document.getElementById('btnBackToVisitors').classList.remove('hidden');

    if (chronologyDt) { chronologyDt.destroy(); chronologyDt = null; }

    const tbody = document.getElementById('chronologyTableBody');
    tbody.innerHTML = '';

    // Filter chronology data if necessary, though generate() already filtered by the search term.
    // However, if there are multiple visitors in the list, we might want to filter the chron array to only match that inv ID.
    const perIdChron = fullChronologyData; // Ideally we'd store invitation_id in chronology rows too.
    
    fullChronologyData.forEach((row, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center text-slate-400 font-semibold">${idx + 1}</td>
            <td class="font-semibold">${escHtml(row.visitor_name)}</td>
            <td class="whitespace-nowrap">${escHtml(row.access_time)}</td>
            <td class="max-w-xs">${escHtml(row.location_detail)}</td>
        `;
        tbody.appendChild(tr);
    });

    chronologyDt = $('#chronologyTable').DataTable({
        pageLength: 10,
        dom: '<"flex justify-end items-center mb-5 mt-2"f><"overflow-x-auto"t><"flex flex-col md:flex-row justify-between items-center gap-4 mt-6"p<"ml-auto"l>>',
        language: { search: "Filter table:", lengthMenu: "_MENU_" }
    });
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

function exportExcel() {
    // Basic export for current visible table
    const isChron = !document.getElementById('chronologyTableWrap').classList.contains('hidden');
    const table = isChron ? chronologyDt : visitorDt;
    const name = isChron ? "Visitor_Chronology" : "Visitor_Details";
    
    // Simplification: Export all data to excel using SheetJS
    const data = isChron ? fullChronologyData : currentVisitorsData;
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

window.addEventListener('DOMContentLoaded', () => {
    const p = new URLSearchParams(window.location.search);
    const ic = p.get('ic_no');
    if (ic) {
        document.getElementById('search_by').value = 'ic';
        document.getElementById('search_term').value = ic;
        runChronologySearch();
    }
});
</script>
</body>
</html>

</body>
</html>
