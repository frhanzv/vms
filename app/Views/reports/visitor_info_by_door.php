<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>" />

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- jQuery (Needed for DataTables & Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Flatpickr for datetime picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>



    <!-- SheetJS for Export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec", // Matches the actual system colors
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Montserrat", "sans-serif"],
                        "sans": ["Montserrat", "sans-serif"]
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }
    </script>

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

        /* DataTables overrides mapped consistently */
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
            box-shadow: 0 0 0 2px rgba(19, 127, 236, 0.15);
        }

        table.dataTable thead th {
            border-bottom: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #475569;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 0.05em;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #e2e8f0;
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
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased overflow-hidden">
    <div class="flex h-screen w-full flex-col">
        <!-- Navbar placeholder from typical layout -->

        <div class="flex flex-1 overflow-hidden">
            <?= view('reports/partials/report_sidebar', ['current' => $current]) ?>

            <main
                class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark custom-scrollbar p-6 lg:p-10 relative">
                <div class="mx-auto max-w-7xl flex flex-col gap-6">

                    <!-- Page Header -->
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-[#137fec] mb-1">Reports</p>
                            <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-2">Visitor
                                Info By Door</h1>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Analyse visitors by
                                entrance or location</p>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div
                        class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 grid grid-cols-1 lg:grid-cols-4 gap-6 items-end">
                        <!-- Location Dropdown -->
                        <div class="flex flex-col gap-1.5 min-w-0">
                            <label class="text-xs font-semibold text-slate-500 tracking-wider" for="laneSelect">
                                Select Location
                            </label>
                            <div class="relative">
                                <select id="laneSelect" style="width: 100%;"
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-lg pl-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary appearance-none chronology-select cursor-pointer">
                                    <option value="">Select Location</option>
                                    <?php foreach ($lanes as $lane): ?>
                                        <option value="<?= esc($lane['id']) ?>"><?= esc($lane['lane']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <p class="text-xs text-slate-400">Select scanning location</p>
                        </div>

                        <!-- From Date -->
                        <div class="flex flex-col gap-1.5 min-w-0">
                            <label class="text-xs font-semibold text-slate-500 tracking-wider" for="fromDateSelect">
                                From Date
                            </label>
                            <div class="relative">
                                <input type="text" id="fromDateSelect"
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary cursor-pointer"
                                    placeholder="Select start date">
                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-slate-400 pointer-events-none">calendar_today</span>
                            </div>
                            <p class="text-xs text-slate-400">Start date</p>
                        </div>

                        <!-- To Date -->
                        <div class="flex flex-col gap-1.5 min-w-0">
                            <label class="text-xs font-semibold text-slate-500 tracking-wider" for="toDateSelect">
                                To Date
                            </label>
                            <div class="relative">
                                <input type="text" id="toDateSelect"
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary cursor-pointer"
                                    placeholder="Select end date">
                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-slate-400 pointer-events-none">calendar_today</span>
                            </div>
                            <p class="text-xs text-slate-400">End date</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 mb-6">
                            <button onclick="fetchVisitors()"
                                class="flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-[#137fec] hover:bg-[#0f6ecf] text-white font-bold text-sm shadow-md shadow-primary/20 transition-all whitespace-nowrap h-[42px] min-w-[140px]">
                                <span class="material-symbols-outlined text-[18px]">search</span>
                                Fetch Visitors
                            </button>
                        </div>
                    </div>

                    <!-- Results Section -->
                    <div id="resultsSection" class="hidden flex-col gap-0">

                        <!-- Alert Banner -->
                        <div
                            class="px-5 py-4 bg-[#cceef0] border border-[#a2e2e7] rounded-md flex justify-between items-center bg-opacity-70 dark:bg-[#164e63] dark:border-[#0891b2] mb-5">
                            <div class="flex items-center text-[#0f5f68] dark:text-cyan-100 text-[13px]">
                                <span class="material-symbols-outlined text-[18px] mr-2">info</span>
                                <span class="font-medium">Showing visitors for: <b id="lblLocation"
                                        class="font-bold"></b> | Date Range: <b id="lblDateRange" class="font-bold"></b> | Total
                                    Visitors: </span>
                                <span id="lblTotal"
                                    class="ml-2 bg-[#2d7bf4] text-white text-[11px] font-bold px-2 py-0.5 rounded-full"></span>
                            </div>
                            <div class="text-[#0f5f68] dark:text-cyan-100 text-[13px] font-medium">
                                Last Updated: <span id="lblUpdated"></span>
                            </div>
                        </div>

                        <!-- Data Table Header & Actions -->
                        <div
                            class="flex flex-col md:flex-row md:items-end justify-between items-center bg-white dark:bg-slate-900 rounded-t-xl border border-slate-200 dark:border-slate-700 shadow-sm border-b-0 p-5 mt-2">
                            <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Visitor
                                Records</h2>

                            <div class="flex flex-wrap items-center gap-3">
                                <button type="button" onclick="openColumnsModal()"
                                    class="flex items-center gap-2 bg-[#137fec] hover:bg-[#0f6ecf] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm h-[38px]">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    Show/Hide Columns
                                </button>
                                <button type="button" onclick="exportExcel()"
                                    class="flex items-center gap-2 bg-[#53b2ec] hover:bg-[#46a2db] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm h-[38px]">
                                    <span class="material-symbols-outlined text-[18px]">query_stats</span>
                                    Export
                                </button>
                                <div class="flex items-center gap-2 ml-2">
                                    <label class="text-sm font-medium text-slate-500 whitespace-nowrap">Search
                                        logs:</label>
                                    <input type="text" id="customSearchBox"
                                        class="border border-slate-300 dark:border-slate-600 rounded-md px-3 py-[7px] text-sm focus:ring-[#137fec] focus:border-[#137fec] outline-none min-w-[200px] w-full max-w-[280px]">
                                </div>
                            </div>
                        </div>

                        <!-- Data Table Card -->
                        <div
                            class="bg-white dark:bg-slate-900 rounded-b-xl border border-slate-200 dark:border-slate-700 shadow-sm border-t-0 p-5 pt-0">
                            <div class="overflow-x-auto custom-scrollbar pb-2 min-h-[380px] lg:min-h-[520px]">
                                <table id="doorTable" class="w-full whitespace-nowrap text-left" style="width:100%">
                                    <thead>
                                        <tr>
                                            <!-- Keep track of these exact column headers for Export mapping -->
                                            <th>#</th>
                                            <th>Visitor Name</th>
                                            <th>Contact No</th>
                                            <th>Staff No</th>
                                            <th>Host Name</th>
                                            <th>Check-in Time</th>
                                            <th>Location</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody" class="text-sm">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State Wrapper placeholder -->
                    <div id="emptyState"
                        class="hidden bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm py-16 flex flex-col items-center justify-center text-center">
                        <span
                            class="material-symbols-outlined text-6xl text-slate-200 dark:text-slate-700 mb-4 block">search_off</span>
                        <p class="text-slate-500 font-medium">No results found for the selected parameters.</p>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- Reused Detail Modal Logic -->
    <div id="detailModal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl max-w-[650px] w-full transform transition-all">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#137fec]">assignment_ind</span>
                    Log Details
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-6">
                <div class="flex items-start gap-4 pb-6 border-b border-gray-100 dark:border-gray-700">
                    <div
                        class="h-14 w-14 rounded-full bg-[#f1f5f9] dark:bg-slate-700 flex items-center justify-center text-slate-400 border border-slate-200">
                        <span class="material-symbols-outlined text-3xl">person</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-1 truncate" id="mFullName">-</h4>
                        <p class="text-sm text-slate-500 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">domain</span>
                            <span id="mCompany" class="truncate">-</span>
                        </p>
                    </div>
                    <div id="mStatusBadge"></div>
                </div>

                <div class="grid grid-cols-2 gap-y-6 gap-x-8">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Checkin
                            Time</label>
                        <p class="text-slate-800 dark:text-slate-200 font-medium text-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">login</span>
                            <span id="mCheckIn">-</span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Location
                            Scanned</label>
                        <p class="text-slate-800 dark:text-slate-200 font-medium text-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">door_front</span>
                            <span id="mLocation">-</span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">IC /
                            Passport</label>
                        <p class="text-slate-800 dark:text-slate-200 font-medium text-sm" id="mIC">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Contact
                            No</label>
                        <p class="text-slate-800 dark:text-slate-200 font-medium text-sm" id="mContact">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Host Name</label>
                        <p class="text-[#137fec] font-medium text-sm" id="mPerson">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Purpose
                            of Visit</label>
                        <p class="text-slate-800 dark:text-slate-200 font-medium text-sm" id="mReason">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Columns Modal Overlay -->
    <div id="columnsModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div id="columnsModalBackdrop" onclick="closeColumnsModal()"
            class="absolute inset-0 bg-slate-900/55 dark:bg-black/65 cursor-pointer"></div>
        <div
            class="relative flex w-full max-w-[600px] flex-col rounded-xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold tracking-tight text-[#3b5998] dark:text-white">Show/Hide Columns</h2>
                <button type="button" onclick="closeColumnsModal()" class="text-slate-300 hover:text-slate-500">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="px-6 py-5 overflow-y-auto max-h-[60vh] custom-scrollbar">
                <div class="mb-5 flex items-center gap-2">
                    <input type="checkbox" id="selectAllColumns" onchange="toggleAllColumns(this)"
                        class="rounded border-slate-300 text-[#137fec] focus:ring-[#137fec] h-4 w-4 cursor-pointer"
                        checked>
                    <label for="selectAllColumns"
                        class="text-sm font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Select All
                        Columns</label>
                </div>
                <div class="grid grid-cols-2 gap-y-3 gap-x-4" id="columnsCheckboxesList">
                    <!-- Checkboxes populated here -->
                </div>
            </div>
            <div
                class="flex justify-end gap-3 border-t border-slate-100 bg-white px-6 py-4 dark:border-slate-700 dark:bg-slate-900 rounded-b-xl border-t-2">
                <button type="button" onclick="closeColumnsModal()"
                    class="rounded-md border border-slate-400 bg-slate-500 hover:bg-slate-600 px-5 py-2 text-sm font-semibold text-white transition-colors">Close</button>
                <button type="button" onclick="applyColumnsVisibility()"
                    class="rounded-md bg-[#137fec] hover:bg-[#0f6ecf] px-5 py-2 text-sm font-semibold text-white shadow-md transition-colors">Apply
                    Changes</button>
            </div>
        </div>
    </div>

    <script>
        let dtTable = null;
        let globalVisitorData = [];

        // Table Headers specifically mapped for Export
        const tableHeaders = [
            "No.", "Visitor Name", "Contact No", "Staff No", "Host Name", "Check-in Time", "Location", "Actions"
        ];

        function cellTextForCheckboxFilter(raw) {
            if (raw === null || raw === undefined) return '-';
            var t = $('<div>').html(String(raw)).text().trim();
            if (!t || t === 'NULL' || t === 'null') return '-';
            return t;
        }

        (function installVmsCheckboxColumnFilterSearchOnce() {
            if (window.__vmsDtCheckboxColumnFilterSearchInstalled) return;
            window.__vmsDtCheckboxColumnFilterSearchInstalled = true;
            if (typeof $ === 'undefined' || !$.fn.dataTable || !$.fn.dataTable.ext) return;
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                if (!settings.oInit || !settings.oInit._checkboxColumnFilter) return true;
                var cf = settings._colCheckboxFilters;
                if (!cf) return true;
                for (var key in cf) {
                    if (!Object.prototype.hasOwnProperty.call(cf, key)) continue;
                    var allowed = cf[key];
                    if (!(allowed instanceof Set)) continue;
                    if (allowed.size === 0) return false;
                    var colIdx = parseInt(key, 10);
                    var cellText = cellTextForCheckboxFilter(data[colIdx]);
                    if (!allowed.has(cellText)) return false;
                }
                return true;
            });
        })();

        document.addEventListener('DOMContentLoaded', () => {

            flatpickr("#fromDateSelect", {
                dateFormat: "Y-m-d",
                defaultDate: "today",
                disableMobile: true
            });

            flatpickr("#toDateSelect", {
                dateFormat: "Y-m-d",
                defaultDate: "today",
                disableMobile: true
            });

            initTable();
        });

        function initTable() {
            dtTable = $('#doorTable').DataTable({
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50],
                    ['10 ITEMS PER PAGE', '25 ITEMS PER PAGE', '50 ITEMS PER PAGE']
                ],
                ordering: true,
                responsive: false,
                _checkboxColumnFilter: true,
                dom: '<"overflow-x-auto min-h-[380px] lg:min-h-[520px]"t><"flex flex-col md:flex-row justify-between items-center gap-4 mt-6"p<"ml-auto"l>>',
                language: {
                    lengthMenu: "_MENU_",
                    paginate: {
                        previous: "&laquo;",
                        next: "&raquo;"
                    }
                },
                columnDefs: [
                    { orderable: false, targets: [7] }, // Disable sorting on Action
                    { className: "text-center", targets: [7] }
                ],
                initComplete: function () {
                    var api = this.api();
                    var st = api.settings()[0];
                    st._colCheckboxFilters = {};
                    api.columns().every(function () {
                        this.search('');
                    });
                    api.draw(false);

                    var syncingFilterOptions = false;

                    function uniqueOptionsIgnoringColumnSearch(colIdx) {
                        if (!st._colCheckboxFilters) st._colCheckboxFilters = {};
                        var cf = st._colCheckboxFilters;
                        var had = Object.prototype.hasOwnProperty.call(cf, colIdx);
                        var saved = had ? cf[colIdx] : undefined;
                        delete st._colCheckboxFilters[colIdx];
                        api.draw(false);
                        var opts = [];
                        var seen = {};
                        api.rows({ search: 'applied' }).every(function () {
                            var rowData = this.data();
                            var textVal = cellTextForCheckboxFilter(rowData[colIdx]);
                            if (textVal && textVal !== '-' && textVal !== 'View' && textVal !== 'NULL' && textVal !== 'null' && !seen[textVal]) {
                                seen[textVal] = true;
                                opts.push(textVal);
                            }
                        });
                        opts.sort();
                        if (had) {
                            st._colCheckboxFilters[colIdx] = saved;
                        }
                        api.draw(false);
                        return opts;
                    }

                    function syncOtherColumnFilterDropdowns(sourceColIdx) {
                        api.columns().every(function () {
                            var col2 = this;
                            var idx2 = col2.index();
                            if (idx2 === sourceColIdx) return;
                            var dd2 = $(col2.header()).find('.filter-dropdown');
                            if (!dd2.length) return;

                            var prev = {};
                            dd2.find('.filter-item input').each(function () {
                                var k = cellTextForCheckboxFilter($(this).val());
                                prev[k] = $(this).prop('checked');
                            });
                            var newOpts = uniqueOptionsIgnoringColumnSearch(idx2);
                            dd2.find('.filter-item').remove();
                            var applyFn2 = dd2.data('applyFilter');
                            var newItemCbs = [];
                            newOpts.forEach(function (val) {
                                var itemLabel = $('<label class="filter-item flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer text-slate-600 capitalize"></label>');
                                itemLabel.attr('data-filter-text', val.toLowerCase());
                                var itemCb = $('<input type="checkbox" checked value="' + val.replace(/"/g, '&quot;') + '" class="form-checkbox h-4 w-4 text-[#137fec] accent-[#137fec] rounded border-slate-300 cursor-pointer">');
                                itemLabel.append(itemCb).append('<span class="select-none">' + val + '</span>');
                                dd2.append(itemLabel);
                                itemCb.prop('checked', Object.prototype.hasOwnProperty.call(prev, val) ? prev[val] : true);
                                if (applyFn2) itemCb.on('change', applyFn2);
                                newItemCbs.push(itemCb);
                            });
                            dd2.data('itemCbs', newItemCbs);
                            if (applyFn2) applyFn2();
                        });
                    }

                    api.columns().every(function () {
                        var column = this;
                        var header = $(column.header());
                        var headerText = header.clone().children().remove().end().text().trim().toUpperCase();
                        if (headerText !== 'ACTIONS' && headerText !== 'NO' && headerText !== 'NO.') {
                            header.find('.dt-filter-wrapper').remove();

                            var wrapper = $('<div class="dt-filter-wrapper inline-block relative ml-1 align-middle" onclick="event.stopPropagation()"></div>');
                            var icon = $('<span class="material-symbols-outlined text-[16px] text-slate-300 hover:text-[#137fec] transition-colors cursor-pointer" style="vertical-align: middle;">filter_alt</span>');
                            var dropdown = $('<div class="filter-dropdown hidden absolute left-0 top-full z-[200] mt-1 bg-white border border-slate-200 rounded shadow-xl p-2 text-left text-sm max-h-[min(60vh,32rem)] overflow-y-auto" style="min-width: 160px; font-weight: normal;"></div>');

                            wrapper.append(icon).append(dropdown);
                            header.append(wrapper);

                            var searchInput = $('<input type="text" placeholder="Search in this column..." class="w-full mb-2 border border-slate-200 rounded px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-primary/20">');
                            dropdown.append(searchInput);

                            var options = [];
                            column.data().unique().sort().each(function (d, j) {
                                var textVal = cellTextForCheckboxFilter(d);
                                if (textVal && textVal !== '-' && textVal !== 'View' && textVal !== 'NULL' && textVal !== 'null') {
                                    options.push(textVal);
                                }
                            });

                            var allLabel = $('<label class="flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer font-semibold text-slate-700 capitalize mb-1"></label>');
                            var allCb = $('<input type="checkbox" checked class="form-checkbox h-4 w-4 text-[#137fec] accent-[#137fec] rounded border-slate-300 cursor-pointer">');
                            allLabel.append(allCb).append('<span class="select-none">All</span>');
                            dropdown.append(allLabel);

                            var removeAllLabel = $('<label class="flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer font-semibold text-slate-700 capitalize mb-1"></label>');
                            var removeAllCb = $('<input type="checkbox" class="form-checkbox h-4 w-4 text-red-500 accent-red-500 rounded border-slate-300 cursor-pointer">');
                            removeAllLabel.append(removeAllCb).append('<span class="select-none">Remove All</span>');
                            dropdown.append(removeAllLabel);
                            dropdown.append('<hr class="my-1 border-slate-200">');

                            var itemCbs = [];
                            options.forEach(function (val) {
                                var itemLabel = $('<label class="filter-item flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer text-slate-600 capitalize"></label>');
                                itemLabel.attr('data-filter-text', val.toLowerCase());
                                var itemCb = $('<input type="checkbox" checked value="' + val.replace(/"/g, '&quot;') + '" class="form-checkbox h-4 w-4 text-[#137fec] accent-[#137fec] rounded border-slate-300 cursor-pointer">');
                                itemLabel.append(itemCb).append('<span class="select-none">' + val + '</span>');
                                dropdown.append(itemLabel);
                                itemCbs.push(itemCb);
                            });
                            dropdown.data('itemCbs', itemCbs);

                            searchInput.on('input', function () {
                                var q = $(this).val().toLowerCase();
                                dropdown.find('.filter-item').each(function () {
                                    var text = ($(this).attr('data-filter-text') || '');
                                    $(this).toggle(text.includes(q));
                                });
                            });

                            icon.on('click', function (e) {
                                e.stopPropagation();
                                $('.filter-dropdown').not(dropdown).addClass('hidden');
                                dropdown.toggleClass('hidden');
                            });

                            $(document).on('click', function (e) {
                                if (!$(e.target).closest(wrapper).length && !$(e.target).closest(dropdown).length) {
                                    dropdown.addClass('hidden');
                                }
                            });

                            function applyFilter() {
                                var cbs = dropdown.data('itemCbs') || itemCbs;
                                var colIdx = column.index();
                                if (!st._colCheckboxFilters) st._colCheckboxFilters = {};

                                var checkedCount = 0;
                                var allowedSet = new Set();
                                cbs.forEach(function (cb) {
                                    if (cb.prop('checked')) {
                                        checkedCount++;
                                        allowedSet.add(cellTextForCheckboxFilter(cb.val()));
                                    }
                                });
                                var optCount = cbs.length;
                                var allChecked = optCount > 0 && checkedCount === optCount;
                                var noneChecked = checkedCount === 0;

                                allCb.prop('checked', allChecked);
                                removeAllCb.prop('checked', noneChecked);
                                allCb.prop('indeterminate', false);
                                removeAllCb.prop('indeterminate', false);

                                if (optCount === 0) {
                                    icon.removeClass('text-[#137fec] text-red-500').addClass('text-slate-300');
                                    delete st._colCheckboxFilters[colIdx];
                                } else if (checkedCount > 0 && checkedCount < optCount) {
                                    icon.removeClass('text-slate-300 text-red-500').addClass('text-[#137fec]');
                                    st._colCheckboxFilters[colIdx] = allowedSet;
                                } else if (checkedCount === 0) {
                                    icon.removeClass('text-slate-300 text-[#137fec]').addClass('text-red-500');
                                    st._colCheckboxFilters[colIdx] = new Set();
                                } else {
                                    icon.removeClass('text-[#137fec] text-red-500').addClass('text-slate-300');
                                    delete st._colCheckboxFilters[colIdx];
                                }

                                column.search('', false, false);
                                api.draw(false);

                                if (!syncingFilterOptions) {
                                    syncingFilterOptions = true;
                                    try {
                                        syncOtherColumnFilterDropdowns(colIdx);
                                    } finally {
                                        syncingFilterOptions = false;
                                    }
                                }
                            }
                            dropdown.data('applyFilter', applyFilter);

                            allCb.on('change', function () {
                                var isChecked = $(this).prop('checked');
                                removeAllCb.prop('checked', false);
                                (dropdown.data('itemCbs') || itemCbs).forEach(function (cb) { cb.prop('checked', isChecked); });
                                applyFilter();
                            });

                            removeAllCb.on('change', function () {
                                var isChecked = $(this).prop('checked');
                                var cbs = dropdown.data('itemCbs') || itemCbs;
                                if (isChecked) {
                                    allCb.prop('checked', false);
                                    cbs.forEach(function (cb) { cb.prop('checked', false); });
                                } else {
                                    allCb.prop('checked', true);
                                    cbs.forEach(function (cb) { cb.prop('checked', true); });
                                }
                                applyFilter();
                            });

                            itemCbs.forEach(function (cb) {
                                cb.on('change', applyFilter);
                            });
                        }
                    });
                }
            });

            // Bind custom search box directly to data table API
            $('#customSearchBox').off('keyup').on('keyup', function () {
                dtTable.search(this.value).draw();
            });
        }

        function esc(s) {
            if (s === null || s === undefined) return '-';
            return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }

        function getStatusPillBadge(status) {
            if (!status || status === 'N/A' || status === '-') return '<span class="text-slate-300 font-semibold uppercase">NULL</span>';
            const s = status.toLowerCase();
            const baseClasses = "px-3 py-1 rounded-md text-xs font-medium inline-flex items-center justify-center min-w-[80px]";

            if (s === 'completed' || s === 'approved') {
                return `<span class="${baseClasses} bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300 capitalize">${status}</span>`;
            } else if (s === 'active' || s === 'pending') {
                return `<span class="${baseClasses} bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300 capitalize">${status}</span>`;
            }
            return `<span class="${baseClasses} bg-gray-100 text-gray-700 capitalize">${esc(status)}</span>`;
        }

        function fetchVisitors() {
            const laneId = $('#laneSelect').val();
            const fromDate = document.getElementById('fromDateSelect').value;
            const toDate = document.getElementById('toDateSelect').value;

            if (!laneId || !fromDate || !toDate) {
                alert('Please select a Location and complete the date range to proceed.');
                return;
            }

            if (fromDate > toDate) {
                alert('From Date cannot be later than To Date.');
                return;
            }

            const btn = document.querySelector('button[onclick="fetchVisitors()"]');
            const origContent = btn.innerHTML;
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px] mr-1.5 animate-spin">autorenew</span>Loading...`;
            btn.disabled = true;

            // Hide UI while loading
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('resultsSection').classList.add('hidden');
            document.getElementById('resultsSection').classList.remove('flex');

            const fd = new FormData();
            fd.append('lane_id', laneId);
            fd.append('from_date', fromDate);
            fd.append('to_date', toDate);

            fetch('<?= base_url('report/bydoor/generate') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            })
                .then(res => res.json())
                .then(data => {
                    btn.innerHTML = origContent;
                    btn.disabled = false;

                    if (!data.success) {
                        alert(data.message || 'An error occurred fetching records.');
                        return;
                    }

                    globalVisitorData = data.visitors;

                    if (globalVisitorData.length === 0) {
                        if (dtTable) dtTable.clear().draw();
                        document.getElementById('emptyState').classList.remove('hidden');
                        return;
                    }

                    // Build visual dynamic rows manually
                    buildTable(data.visitors);

                    // Paint Banner Params
                    document.getElementById('lblLocation').textContent = data.location_text;
                    document.getElementById('lblDateRange').textContent = data.date_range_text;
                    document.getElementById('lblTotal').textContent = data.total_visitors;
                    document.getElementById('lblUpdated').textContent = data.last_updated;

                    document.getElementById('resultsSection').classList.remove('hidden');
                    document.getElementById('resultsSection').classList.add('flex');

                    initColumnsCheckboxes();
                })
                .catch(e => {
                    console.error(e);
                    btn.innerHTML = origContent;
                    btn.disabled = false;
                    alert('A network error triggered. Try again.');
                });
        }

        function buildTable(rows) {
            if (dtTable) {
                dtTable.destroy();
            }

            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';

            rows.forEach((v, i) => {
                // Null parsing for staff no pill
                let staffPill = v.staff_no;
                if (!staffPill || staffPill === 'null' || staffPill === 'N/A') {
                    staffPill = `<span class="bg-[#1fc9ea] text-white px-2.5 py-0.5 rounded-full text-[11px] font-bold">null</span>`;
                } else {
                    staffPill = `<span class="text-slate-600 font-medium">${esc(staffPill)}</span>`;
                }

                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td class="py-3 align-middle"><span class="text-slate-500 font-medium">${i + 1}</span></td>
                <td class="py-3 align-middle"><span class="text-slate-600 font-medium tracking-tight text-[13px] capitalize">${esc(v.visitor_name)}</span></td>
                <td class="py-3 align-middle"><span class="text-slate-500 uppercase tracking-tight text-[13px]">${esc(v.contact_no)}</span></td>
                <td class="py-3 align-middle">${staffPill}</td>
                <td class="py-3 align-middle"><span class="text-slate-500 tracking-tight text-[13px] capitalize">${esc(v.person_visited)}</span></td>
                <td class="py-3 align-middle"><span class="text-slate-500 tracking-tight text-[13px] font-medium">${esc(v.checkin_time)}</span></td>
                <td class="py-3 align-middle"><span class="bg-gray-400 text-white rounded-full px-2 py-0.5 text-[11px] font-bold">${esc(v.location_name)}</span></td>
                <td class="py-3 align-middle"><div class="flex justify-center"><span class="material-symbols-outlined text-[18px] text-[#137fec] cursor-pointer hover:text-blue-800 transition-colors" onclick="openModal(${i})">visibility</span></div></td>
            `;
                tbody.appendChild(tr);
            });

            initTable();
        }

        function exportExcel() {
            if (!globalVisitorData.length || !dtTable) {
                alert("No data available to export! Please fetch visitors first.");
                return;
            }

            // Find visible columns (exclude action column 7 if desired, but respect visibility filter)
            const visibleIndices = dtTable.columns().visible().toArray().map((v, i) => v && i !== 7 ? i : -1).filter(v => v !== -1);
            const expHeaders = visibleIndices.map(i => tableHeaders[i]);

            const exportData = [expHeaders];

            let exportIndex = 1;
            dtTable.rows({ search: 'applied' }).every(function () {
                const rawData = this.data();
                const fullRowData = rawData.map(val => {
                    if (typeof val === 'string') {
                        return val.replace(/<[^>]*>?/gm, '').trim();
                    }
                    return val;
                });

                fullRowData[0] = exportIndex++;

                const rowData = visibleIndices.map(idx => fullRowData[idx] || '-');
                exportData.push(rowData);
            });

            const ws = XLSX.utils.aoa_to_sheet(exportData);

            for (let C = 0; C < expHeaders.length; ++C) {
                const cell_ref = XLSX.utils.encode_cell({ c: C, r: 0 });
                if (!ws[cell_ref]) continue;
                ws[cell_ref].s = {
                    fill: { fgColor: { rgb: "FF535DEC" } },
                    font: { color: { rgb: "FFFFFFFF" }, bold: true }
                };
            }

            const wscols = visibleIndices.map(() => ({ wch: 20 }));
            ws['!cols'] = wscols;

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Visitor Report");
            XLSX.writeFile(wb, "Visitor_Door_Report_" + new Date().toISOString().slice(0, 10) + ".xlsx");
        }

        // Modal & Columns Toggle Logic
        function initColumnsCheckboxes() {
            const container = document.getElementById('columnsCheckboxesList');
            container.innerHTML = '';
            let allChecked = true;

            tableHeaders.forEach((colName, idx) => {
                const isVisible = dtTable.column(idx).visible();
                if (!isVisible) allChecked = false;

                const div = document.createElement('div');
                div.className = 'flex items-center gap-2';
                div.innerHTML = `
                <input type="checkbox" id="col_${idx}" data-col-idx="${idx}" class="col-toggle-cb rounded border-slate-300 text-[#137fec] focus:ring-[#137fec] h-4 w-4 cursor-pointer" ${isVisible ? 'checked' : ''}>
                <label for="col_${idx}" class="text-sm text-slate-600 dark:text-slate-300 uppercase cursor-pointer">${colName}</label>
            `;
                container.appendChild(div);
            });

            document.getElementById('selectAllColumns').checked = allChecked;

            // Add event listeners
            document.querySelectorAll('.col-toggle-cb').forEach(cb => {
                cb.addEventListener('change', function () {
                    const total = document.querySelectorAll('.col-toggle-cb').length;
                    const checked = document.querySelectorAll('.col-toggle-cb:checked').length;
                    document.getElementById('selectAllColumns').checked = (total === checked);
                });
            });
        }

        function openColumnsModal() {
            if (!dtTable) return;
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
                dtTable.column(idx).visible(cb.checked);
            });
            document.getElementById('columnsModal').classList.add('hidden');
        }

        function openModal(idx) {
            const v = globalVisitorData[idx];
            if (!v) return;

            document.getElementById('mFullName').textContent = v.full_name;
            document.getElementById('mCompany').textContent = v.company;
            document.getElementById('mStatusBadge').innerHTML = getStatusPillBadge(v.status);

            document.getElementById('mIC').textContent = v.ic_passport;
            document.getElementById('mContact').textContent = v.contact_no;
            document.getElementById('mPerson').textContent = v.person_visited;
            document.getElementById('mCheckIn').textContent = v.checkin_time;
            document.getElementById('mLocation').textContent = v.location;
            document.getElementById('mReason').textContent = v.reason;

            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
</body>

</html>
            const ws = XLSX.utils.aoa_to_sheet(exportData);

            for (let C = 0; C < expHeaders.length; ++C) {
                const cell_ref = XLSX.utils.encode_cell({ c: C, r: 0 });
                if (!ws[cell_ref]) continue;
                ws[cell_ref].s = {
                    fill: { fgColor: { rgb: "FF535DEC" } },
                    font: { color: { rgb: "FFFFFFFF" }, bold: true }
                };
            }

            const wscols = visibleIndices.map(() => ({ wch: 20 }));
            ws['!cols'] = wscols;

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Visitor Report");
            XLSX.writeFile(wb, "Visitor_Door_Report_" + new Date().toISOString().slice(0, 10) + ".xlsx");
        }

        // Modal & Columns Toggle Logic
        function initColumnsCheckboxes() {
            const container = document.getElementById('columnsCheckboxesList');
            container.innerHTML = '';
            let allChecked = true;

            tableHeaders.forEach((colName, idx) => {
                const isVisible = dtTable.column(idx).visible();
                if (!isVisible) allChecked = false;

                const div = document.createElement('div');
                div.className = 'flex items-center gap-2';
                div.innerHTML = `
                <input type="checkbox" id="col_${idx}" data-col-idx="${idx}" class="col-toggle-cb rounded border-slate-300 text-[#137fec] focus:ring-[#137fec] h-4 w-4 cursor-pointer" ${isVisible ? 'checked' : ''}>
                <label for="col_${idx}" class="text-sm text-slate-600 dark:text-slate-300 uppercase cursor-pointer">${colName}</label>
            `;
                container.appendChild(div);
            });

            document.getElementById('selectAllColumns').checked = allChecked;

            // Add event listeners
            document.querySelectorAll('.col-toggle-cb').forEach(cb => {
                cb.addEventListener('change', function () {
                    const total = document.querySelectorAll('.col-toggle-cb').length;
                    const checked = document.querySelectorAll('.col-toggle-cb:checked').length;
                    document.getElementById('selectAllColumns').checked = (total === checked);
                });
            });
        }

        function openColumnsModal() {
            if (!dtTable) return;
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
                dtTable.column(idx).visible(cb.checked);
            });
            document.getElementById('columnsModal').classList.add('hidden');
        }

        function openModal(idx) {
            const v = globalVisitorData[idx];
            if (!v) return;

            document.getElementById('mFullName').textContent = v.full_name;
            document.getElementById('mCompany').textContent = v.company;
            document.getElementById('mStatusBadge').innerHTML = getStatusPillBadge(v.status);

            document.getElementById('mIC').textContent = v.ic_passport;
            document.getElementById('mContact').textContent = v.contact_no;
            document.getElementById('mPerson').textContent = v.person_visited;
            document.getElementById('mCheckIn').textContent = v.checkin_time;
            document.getElementById('mLocation').textContent = v.location;
            document.getElementById('mReason').textContent = v.reason;

            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
</body>

</html>