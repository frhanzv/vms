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
                         <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Access Chronology</h2>
                         
                         <div class="flex gap-2">
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
        </div>
    </div>
</div>

<script>
let chronologyDt = null;
let reportData = [];

const tableHeaders = [
    "No", "Visitor Name", "Contact", "IC No", "Person Visited", 
    "Company", "Vehicle", "Reason", "Access Time", "Location"
];
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
    if (chronologyDt) {
        chronologyDt.destroy();
        chronologyDt = null;
    }
    document.getElementById('chronologyTableBody').innerHTML = '';
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
        alert('Enter IC number or staff number (or open this page from Access Report).');
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
        if (!data.chronology || data.chronology.length === 0) {
            showChronologyEmpty();
            return;
        }

        reportData = data.chronology;

        document.getElementById('chronologyEmpty').classList.add('hidden');
        document.getElementById('chronologyResultsWrap').classList.remove('hidden');
        document.getElementById('chronologySummary').classList.remove('hidden');
        document.getElementById('chronologySummaryMeta').textContent =
            data.total_records + ' event(s) · ' + data.location_name + ' · ' + data.from_datetime + ' — ' + data.to_datetime;

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
    })
    .catch(() => {
        alert('Error loading chronology.');
        showChronologyEmpty();
    });
}

document.getElementById('btnChronologySearch').addEventListener('click', runChronologySearch);
document.getElementById('btnChronologyClear').addEventListener('click', () => {
    document.getElementById('search_term').value = '';
    document.getElementById('invitation_id').value = '';
    document.getElementById('search_by').value = 'ic';
    document.getElementById('chronology_location_id').value = '';
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
    const staff = p.get('staff_no');
    const inv = p.get('invitation_id');
    const loc = p.get('location_id');
    const from = p.get('from_datetime');
    const to = p.get('to_datetime');

    if (loc !== null && loc !== undefined) {
        document.getElementById('chronology_location_id').value = loc;
    }
    if (from) {
        fpChronFrom.setDate(from.replace('T', ' '));
    }
    if (to) {
        fpChronTo.setDate(to.replace('T', ' '));
    }
    if (ic) {
        document.getElementById('search_by').value = 'ic';
        document.getElementById('search_term').value = ic;
    } else if (staff) {
        document.getElementById('search_by').value = 'staff';
        document.getElementById('search_term').value = staff;
    }
    if (inv) {
        document.getElementById('invitation_id').value = inv;
    }

    if (p.get('auto_search') === '1') {
        runChronologySearch();
    }
});
</script>
</body>
</html>
