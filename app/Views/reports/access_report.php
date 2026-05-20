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
    <!-- Flatpickr for datetime picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- SheetJS with Styles for Excel Export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

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
            overflow: visible;
        }
        table.dataTable tbody td {
            font-size: 0.82rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
            white-space: nowrap;
        }
        table.dataTable tbody tr:hover td { background: #f0f7ff; }
        table.dataTable { border-collapse: collapse !important; width: 100% !important; }
        /* Avoid heavy / dark edge on the right of the report table */
        #accessTable_wrapper table.dataTable { border-right: none !important; }
        #accessTable thead th:last-child,
        #accessTable tbody td:last-child {
            border-right: none !important;
        }

        /* Flatpickr custom */
        .flatpickr-input {
            background: white !important;
        }
        .flatpickr-calendar {
            font-family: 'Montserrat', sans-serif;
            font-size: 0.82rem;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased overflow-hidden">
<div class="flex h-screen w-full flex-col">
    <div class="flex flex-1 overflow-hidden">

        <?= view('reports/partials/report_sidebar', ['current' => $current]) ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark custom-scrollbar p-6 lg:p-10">
            <div class="mx-auto max-w-7xl flex flex-col gap-6">

                <!-- Page Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-primary mb-1">Reports</p>
                        <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-2">Access Logs Report</h1>
                        <p class="text-slate-500 dark:text-slate-400 text-base font-medium">Visitor Access History</p>
                    </div>
                </div>

                <!-- Filter Card -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
                    <div class="flex flex-col md:flex-row gap-4 items-start md:items-end">

                        <!-- From Date & Time -->
                        <div class="flex flex-col gap-1.5 flex-1 min-w-0">
                            <label class="text-xs font-semibold text-slate-500 tracking-wider">From Date &amp; Time</label>
                            <div class="relative">
                                <input type="text" id="from_datetime" name="from_datetime"
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary cursor-pointer"
                                    placeholder="Select start date &amp; time" readonly>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-slate-400 pointer-events-none">calendar_today</span>
                            </div>
                            <p class="text-xs text-slate-400">Start date and time</p>
                        </div>

                        <!-- To Date & Time -->
                        <div class="flex flex-col gap-1.5 flex-1 min-w-0">
                            <label class="text-xs font-semibold text-slate-500 tracking-wider">To Date &amp; Time</label>
                            <div class="relative">
                                <input type="text" id="to_datetime" name="to_datetime"
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary cursor-pointer"
                                    placeholder="Select end date &amp; time" readonly>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-slate-400 pointer-events-none">calendar_today</span>
                            </div>
                            <p class="text-xs text-slate-400">End date and time</p>
                        </div>

                        <!-- Select Locations (multi-select checkbox dropdown) -->
                        <div class="flex flex-col gap-1.5 flex-1 min-w-0" id="locationDropdownWrapper">
                            <label class="text-xs font-semibold text-slate-500 tracking-wider">Select Locations</label>
                            <div class="relative" id="locationDropdownContainer">
                                <!-- Trigger button -->
                                <button type="button" id="locationDropdownBtn"
                                    onclick="toggleLocationDropdown(event)"
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-lg pl-4 pr-10 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary text-left cursor-pointer flex items-center justify-between">
                                    <span id="locationDropdownLabel" class="truncate">-- Select Locations --</span>
                                    <span class="material-symbols-outlined text-[18px] text-slate-400 flex-shrink-0 ml-2" id="locationDropdownChevron">expand_more</span>
                                </button>

                                <!-- Dropdown panel -->
                                <div id="locationDropdownPanel"
                                    class="hidden absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg max-h-64 overflow-y-auto">
                                    <!-- Select All -->
                                    <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-700 sticky top-0 bg-white dark:bg-slate-800">
                                        <label class="flex items-center gap-2 cursor-pointer select-none">
                                            <input type="checkbox" id="locationSelectAll" onchange="toggleSelectAllLocations(this)"
                                                class="rounded border-slate-300 text-primary focus:ring-primary/30 h-4 w-4 cursor-pointer">
                                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Select All Locations</span>
                                        </label>
                                    </div>
                                    <div class="py-1">
                                        <?php foreach ($subLocations as $sl): ?>
                                            <label class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer select-none">
                                                <input type="checkbox" name="sub_location_ids[]"
                                                    value="<?= esc($sl['id']) ?>"
                                                    class="location-checkbox rounded border-slate-300 text-primary focus:ring-primary/30 h-4 w-4 cursor-pointer"
                                                    onchange="onLocationCheckboxChange()">
                                                <span class="text-sm text-slate-700 dark:text-slate-200">
                                                    <?= esc(strtoupper($sl['name'])) ?>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400">Select one or more locations</p>
                        </div>

                        <!-- Generate Button -->
                        <div class="flex-shrink-0 md:mb-5">
                            <button id="generateBtn" onclick="generateReport()"
                                class="flex items-center gap-2 px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white font-bold text-sm shadow-md shadow-primary/20 transition-all whitespace-nowrap">
                                <span class="material-symbols-outlined text-[18px]">search</span>
                                Generate
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Section (hidden until generated) -->
                <div id="resultsSection" class="hidden flex flex-col gap-4">

                    <!-- Summary Card -->
                    <div class="flex flex-col md:flex-row gap-4 items-start">
                        <!-- Total Visitor Count -->
                        <div class="bg-slate-900 dark:bg-slate-800 rounded-xl p-6 flex flex-col items-center justify-center min-w-[160px] shadow-md">
                            <p id="totalCount" class="text-5xl font-black text-white">0</p>
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mt-2">Total Visitor</p>
                        </div>
                        <!-- Report Info -->
                        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-5 flex-1 shadow-sm flex flex-col justify-center gap-2">
                            <div>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Report Period: </span>
                                <span id="reportPeriod" class="text-sm text-slate-500"></span>
                            </div>
                            <div>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Location: </span>
                                <span id="reportLocation" class="text-sm text-slate-500"></span>
                            </div>
                        </div>

                    </div>

                    <!-- Data Table Header & Actions -->
                    <div class="flex flex-col md:flex-row md:items-end justify-between items-center bg-white dark:bg-slate-900 rounded-t-xl border border-slate-200 dark:border-slate-700 shadow-sm border-b-0 p-5 mt-2">
                         <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Visitor Details</h2>
                         
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

                    <!-- Data Table Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-b-xl border border-slate-200 dark:border-slate-700 shadow-sm border-t-0 p-5 pt-0">
                        <div class="p-5 overflow-x-auto min-h-[380px] lg:min-h-[520px]">
                            <table id="accessTable" class="w-full" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Visitor Name</th>
                                        <th>Contact No</th>
                                        <th>IC No</th>
                                        <th>Host Name</th>
                                        <th>Company</th>
                                        <th>Vehicle No</th>
                                        <th>Visit Reason</th>
                                        <th>Location</th>
                                        <th>Total Access</th>
                                        <th>First Access</th>
                                        <th>Last Access</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Empty State (shown before first generate) -->
                <div id="emptyState" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-16 flex flex-col items-center justify-center gap-3">
                    <span class="material-symbols-outlined text-5xl text-slate-200 dark:text-slate-700">bar_chart</span>
                    <p class="text-slate-400 font-semibold text-sm">Select a date range and location, then click Generate</p>
                </div>

                <!-- Loading State -->
                <div id="loadingState" class="hidden bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-16 flex flex-col items-center justify-center gap-3">
                    <div class="size-10 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                    <p class="text-slate-400 font-semibold text-sm">Generating report...</p>
                </div>

                <!-- No Data State -->
                <div id="noDataState" class="hidden bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-16 flex flex-col items-center justify-center gap-3">
                    <span class="material-symbols-outlined text-5xl text-slate-200 dark:text-slate-700">search_off</span>
                    <p class="text-slate-400 font-semibold text-sm">No visitors found for the selected period and location</p>
                </div>

            </div>
        </main>
    </div>
</div>

<!-- Movement history modal -->
<div id="movementModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4" aria-modal="true" role="dialog">
    <div id="movementModalBackdrop" class="absolute inset-0 bg-slate-900/55 dark:bg-black/65"></div>
    <div class="relative flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
        <div class="flex shrink-0 items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-700">
            <div class="min-w-0">
                <h2 id="movementModalTitle" class="truncate text-lg font-black tracking-tight text-slate-900 dark:text-white">Movement History</h2>
            </div>
            <button type="button" id="movementModalCloseX" class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white" aria-label="Close">
                <span class="material-symbols-outlined text-[22px]">close</span>
            </button>
        </div>
        <div class="min-h-[220px] flex-1 overflow-y-auto px-5 py-4 custom-scrollbar">
            <div id="movementModalLoading" class="hidden flex flex-col items-center justify-center gap-3 py-12">
                <div class="size-9 animate-spin rounded-full border-4 border-primary/20 border-t-primary"></div>
                <p class="text-sm font-medium text-slate-500">Loading movement history…</p>
            </div>
            <table id="movementModalTable" class="hidden w-full border-collapse text-sm">
                <thead>
                    <tr class="border-b-2 border-slate-200 bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <th class="px-3 py-3">Date &amp; Time</th>
                        <th class="px-3 py-3">Current Location</th>
                        <th class="px-3 py-3">Location Accessed</th>
                        <th class="px-3 py-3 text-center">Access</th>
                        <th class="px-3 py-3">Reason</th>
                        <th class="px-3 py-3 text-center">Type</th>
                        <th class="px-3 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="movementModalTableBody" class="text-slate-700 dark:text-slate-200"></tbody>
            </table>
            <p id="movementModalEmpty" class="hidden py-12 text-center text-sm font-medium text-slate-500">No movement records for this visitor in the selected period.</p>
        </div>
        <div class="flex shrink-0 justify-end gap-3 border-t border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-900/80">
            <button type="button" id="movementModalBtnClose" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                Close
            </button>
            <button type="button" id="movementModalBtnChronology" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-primary/25 transition-colors hover:bg-primary/90">
                View Chronology
            </button>
        </div>
    </div>
</div>
<input type="hidden" id="movementModalInvitationId" value="">
<input type="hidden" id="movementModalIcNo" value="">

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
    let dtTable = null;
    let reportData = [];
    
    const tableHeaders = [
        "No", "Visitor Name", "Contact No", "IC No", "Host Name", 
        "Company", "Vehicle No", "Visit Reason", "Location", "Total Access", "First Access", 
        "Last Access", "Actions"
    ];

    /** Match checkbox values to DataTables cell text (DOM / HTML cells). */
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

    /** Escape for use inside double-quoted HTML attributes */
    function escAttr(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Init flatpickr datetime pickers
    const today = new Date();
    const todayStr = today.getFullYear() + '-' +
        String(today.getMonth() + 1).padStart(2, '0') + '-' +
        String(today.getDate()).padStart(2, '0');

    flatpickr('#from_datetime', {
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        time_24hr: true,
        defaultDate: todayStr + ' 00:00',
        disableMobile: true,
    });

    flatpickr('#to_datetime', {
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        time_24hr: true,
        defaultDate: todayStr + ' 23:59',
        disableMobile: true,
    });

    function showState(state) {
        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('loadingState').classList.add('hidden');
        document.getElementById('noDataState').classList.add('hidden');
        document.getElementById('resultsSection').classList.add('hidden');

        if (state === 'empty')   document.getElementById('emptyState').classList.remove('hidden');
        if (state === 'loading') document.getElementById('loadingState').classList.remove('hidden');
        if (state === 'nodata')  document.getElementById('noDataState').classList.remove('hidden');
        if (state === 'results') document.getElementById('resultsSection').classList.remove('hidden');
    }

    function generateReport() {
        const fromDatetime = document.getElementById('from_datetime').value;
        const toDatetime   = document.getElementById('to_datetime').value;
        const checkedBoxes = document.querySelectorAll('.location-checkbox:checked');
        const laneIds  = Array.from(checkedBoxes).map(cb => cb.value);

        if (!fromDatetime || !toDatetime || laneIds.length === 0) {
            alert('Please fill in all fields and select at least one location before generating the report.');
            return;
        }

        showState('loading');

        const formData = new FormData();
        formData.append('from_datetime', fromDatetime);
        formData.append('to_datetime',   toDatetime);
        laneIds.forEach(id => formData.append('sub_location_ids[]', id));

        fetch('<?= base_url('report/access/generate') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'An error occurred.');
                showState('empty');
                return;
            }

            if (data.visitors.length === 0) {
                showState('nodata');
                return;
            }

            reportData = data.visitors;
            renderTable(data);
        })
        .catch(err => {
            console.error(err);
            alert('Failed to fetch report. Please try again.');
            showState('empty');
        });
    }

    function renderTable(data) {
        // Update summary
        document.getElementById('totalCount').textContent = data.total_visitors;
        document.getElementById('reportPeriod').textContent = data.from_datetime + ' to ' + data.to_datetime;
        document.getElementById('reportLocation').textContent = data.location_name;

        // Destroy existing DataTable if any
        if (dtTable) {
            dtTable.destroy();
            dtTable = null;
        }

        // Build table rows
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';
        data.visitors.forEach((v, idx) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center font-semibold text-slate-400">${idx + 1}</td>
                <td class="font-semibold text-slate-800">${escHtml(v.visitor_name)}</td>
                <td>${escHtml(v.contact_no)}</td>
                <td>${escHtml(v.ic_no)}</td>
                <td>${escHtml(v.person_visited)}</td>
                <td>${escHtml(v.visitor_company)}</td>
                <td>${escHtml(v.vehicle_no)}</td>
                <td>${escHtml(v.visit_reason)}</td>
                <td>${escHtml(v.location_name)}</td>
                <td class="text-center">
                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary">${v.total_access}</span>
                </td>
                <td class="whitespace-nowrap">${escHtml(v.first_access)}</td>
                <td class="whitespace-nowrap">${escHtml(v.last_access)}</td>
                <td class="text-center px-2 py-2">
                    <button type="button"
                        class="js-access-view inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm bg-primary/10 text-primary hover:bg-primary/20 transition-colors mx-auto"
                        data-invitation-id="${escAttr(String(v.invitation_id))}"
                        data-ic-no="${escAttr(v.ic_no)}"
                        data-visitor-name="${escAttr(v.visitor_name)}"
                        data-visit-reason="${escAttr(v.visit_reason)}">
                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                        View
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Init DataTable
        dtTable = $('#accessTable').DataTable({
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50],
                ['10 ITEMS PER PAGE', '25 ITEMS PER PAGE', '50 ITEMS PER PAGE']
            ],
            ordering: true,
            responsive: false,
            _checkboxColumnFilter: true,
            dom: '<"flex justify-end items-center mb-5 mt-2"f><"overflow-x-auto min-h-[380px] lg:min-h-[520px]"t><"flex flex-col md:flex-row justify-between items-center gap-4 mt-6"p<"ml-auto"l>>',
            language: {
                search: 'Search records:',
                searchPlaceholder: "",
                lengthMenu: '_MENU_',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                paginate: {
                    previous: "&laquo;",
                    next: "&raquo;"
                }
            },
            columnDefs: [
                { orderable: false, targets: [0, 12] },
                { className: 'text-center', targets: [0, 9, 12] }
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
                            var itemCb = $('<input type="checkbox" checked value="' + val.replace(/"/g, '&quot;') + '" class="form-checkbox h-4 w-4 text-[#535dec] accent-[#535dec] rounded border-slate-300 cursor-pointer">');
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
                        var icon = $('<span class="material-symbols-outlined text-[16px] text-slate-300 hover:text-[#535dec] transition-colors cursor-pointer" style="vertical-align: middle;">filter_alt</span>');
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
                        var allCb = $('<input type="checkbox" checked class="form-checkbox h-4 w-4 text-[#535dec] accent-[#535dec] rounded border-slate-300 cursor-pointer">');
                        allLabel.append(allCb).append('<span class="select-none">All</span>');
                        dropdown.append(allLabel);

                        var removeAllLabel = $('<label class="flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer font-semibold text-slate-700 capitalize mb-1"></label>');
                        var removeAllCb = $('<input type="checkbox" class="form-checkbox h-4 w-4 text-red-500 accent-red-500 rounded border-slate-300 cursor-pointer">');
                        removeAllLabel.append(removeAllCb).append('<span class="select-none">Remove All</span>');
                        dropdown.append(removeAllLabel);
                        dropdown.append('<hr class="my-1 border-slate-200">');

                        var itemCbs = [];
                        options.forEach(function(val) {
                            var itemLabel = $('<label class="filter-item flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer text-slate-600 capitalize"></label>');
                            itemLabel.attr('data-filter-text', val.toLowerCase());
                            var itemCb = $('<input type="checkbox" checked value="' + val.replace(/"/g, '&quot;') + '" class="form-checkbox h-4 w-4 text-[#535dec] accent-[#535dec] rounded border-slate-300 cursor-pointer">');
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

                        icon.on('click', function(e) {
                            e.stopPropagation();
                            $('.filter-dropdown').not(dropdown).addClass('hidden');
                            dropdown.toggleClass('hidden');
                        });
                        
                        $(document).on('click', function(e) {
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
                                icon.removeClass('text-[#535dec] text-red-500').addClass('text-slate-300');
                                delete st._colCheckboxFilters[colIdx];
                            } else if (checkedCount > 0 && checkedCount < optCount) {
                                icon.removeClass('text-slate-300 text-red-500').addClass('text-[#535dec]');
                                st._colCheckboxFilters[colIdx] = allowedSet;
                            } else if (checkedCount === 0) {
                                icon.removeClass('text-slate-300 text-[#535dec]').addClass('text-red-500');
                                st._colCheckboxFilters[colIdx] = new Set();
                            } else {
                                icon.removeClass('text-[#535dec] text-red-500').addClass('text-slate-300');
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

                        allCb.on('change', function() {
                            var isChecked = $(this).prop('checked');
                            removeAllCb.prop('checked', false);
                            (dropdown.data('itemCbs') || itemCbs).forEach(function(cb) { cb.prop('checked', isChecked); });
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

                        itemCbs.forEach(function(cb) {
                            cb.on('change', applyFilter);
                        });
                    }
                });
            }
        });

        initColumnsCheckboxes();

        showState('results');
    }

    function escHtml(str) {
        if (!str) return '-';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // Modal & Columns Toggle Logic
    function initColumnsCheckboxes() {
        const container = document.getElementById('columnsCheckboxesList');
        container.innerHTML = '';
        let allChecked = true;
        
        tableHeaders.forEach((colName, idx) => {
            const isVisible = dtTable.column(idx).visible();
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
        if(!dtTable) return;
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

    function exportExcel() {
        if (!reportData || reportData.length === 0 || !dtTable) return;

        // Find visible columns to determine which to export (exclude Action column 11 if desired, but we'll respect visibility)
        const visibleIndices = dtTable.columns().visible().toArray().map((v, i) => v && i !== 12 ? i : -1).filter(v => v !== -1);
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

            // Override first column with sequential number
            fullRowData[0] = exportIndex++;

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
        XLSX.utils.book_append_sheet(wb, ws, "Access Report");
        XLSX.writeFile(wb, "Access_Report_" + new Date().toISOString().slice(0, 10) + ".xlsx");
    }

    function closeMovementModal() {
        document.getElementById('movementModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function movementBadgeYesNo(yes) {
        if (yes) {
            return '<span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Yes</span>';
        }
        return '<span class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-800 dark:bg-red-900/40 dark:text-red-300">No</span>';
    }

    function movementBadgeAction(allowed) {
        if (allowed) {
            return '<span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Allowed</span>';
        }
        return '<span class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-800 dark:bg-red-900/40 dark:text-red-300">Not Allowed</span>';
    }

    function movementTypeBadge(label) {
        return '<span class="inline-flex rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-bold text-slate-700 dark:bg-slate-700 dark:text-slate-200">' + escHtml(label) + '</span>';
    }

    function openMovementHistory(invitationId, icNo, visitorName = '', visitReason = '') {
        const checkedBoxes = document.querySelectorAll('.location-checkbox:checked');
        const laneIds  = Array.from(checkedBoxes).map(cb => cb.value);
        const fromDatetime = document.getElementById('from_datetime').value;
        const toDatetime = document.getElementById('to_datetime').value;
        if (laneIds.length === 0 || !fromDatetime || !toDatetime) {
            alert('Select location and date range, then generate the report before opening movement history.');
            return;
        }
        // Use first selected location for movement history detail
        const laneId = laneIds[0];

        document.getElementById('movementModalInvitationId').value = String(invitationId);
        const ic = icNo != null ? String(icNo).trim() : '';
        document.getElementById('movementModalIcNo').value = (ic && ic !== 'N/A') ? ic : '';
        document.getElementById('movementModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        document.getElementById('movementModalLoading').classList.remove('hidden');
        document.getElementById('movementModalTable').classList.add('hidden');
        document.getElementById('movementModalEmpty').classList.add('hidden');
        document.getElementById('movementModalTableBody').innerHTML = '';
        document.getElementById('movementModalTitle').textContent = 'Movement History' + (visitorName ? ' — ' + visitorName : '');

        const formData = new FormData();
        formData.append('invitation_id', invitationId);
        laneIds.forEach(id => formData.append('sub_location_ids[]', id));
        formData.append('from_datetime', fromDatetime);
        formData.append('to_datetime', toDatetime);

        fetch('<?= base_url('report/access/movementHistory') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('movementModalLoading').classList.add('hidden');
            if (!data.success) {
                alert(data.message || 'Could not load movement history.');
                closeMovementModal();
                return;
            }
            document.getElementById('movementModalTitle').textContent = 'Movement History' + (visitorName ? ' — ' + visitorName : '');
            const tbody = document.getElementById('movementModalTableBody');
            tbody.innerHTML = '';
            if (!data.movements || data.movements.length === 0) {
                document.getElementById('movementModalEmpty').classList.remove('hidden');
                return;
            }
            document.getElementById('movementModalTable').classList.remove('hidden');
            data.movements.forEach(row => {
                const tr = document.createElement('tr');
                tr.className = 'border-b border-slate-100 dark:border-slate-800';
                const accessYes = row.access_granted !== false && row.access !== 'No';
                const actOk = row.action_allowed !== false && row.action !== 'Not Allowed';
                const rowReason = visitReason ? visitReason : (row.reason || '—');
                const configUrl = '<?= base_url('config?tab=sublocation') ?>';
                tr.innerHTML =
                    '<td class="whitespace-nowrap px-3 py-3 font-medium">' + escHtml(row.date_time) + '</td>' +
                    '<td class="px-3 py-3">' + escHtml(row.current_location) + '</td>' +
                    '<td class="px-3 py-3">' + escHtml(row.location_accessed) + '</td>' +
                    '<td class="px-3 py-3 text-center">' + movementBadgeYesNo(accessYes) + '</td>' +
                    '<td class="max-w-[200px] px-3 py-3 text-slate-600 dark:text-slate-400">' + escHtml(rowReason) + '</td>' +
                    '<td class="px-3 py-3 text-center">' + movementTypeBadge(row.type || 'Checkin') + '</td>' +
                    '<td class="px-3 py-3 text-center">' + movementBadgeAction(actOk) + '</td>';
                tbody.appendChild(tr);
            });
        })
        .catch(err => {
            console.error(err);
            document.getElementById('movementModalLoading').classList.add('hidden');
            alert('Failed to load movement history.');
            closeMovementModal();
        });
    }

    document.getElementById('movementModalBackdrop').addEventListener('click', closeMovementModal);
    document.getElementById('movementModalCloseX').addEventListener('click', closeMovementModal);
    document.getElementById('movementModalBtnClose').addEventListener('click', closeMovementModal);
    document.getElementById('movementModalBtnChronology').addEventListener('click', function () {
        const invitationId = document.getElementById('movementModalInvitationId').value;
        const icRaw = document.getElementById('movementModalIcNo').value;
        const checkedBoxesC = document.querySelectorAll('.location-checkbox:checked');
        const laneIdsC  = Array.from(checkedBoxesC).map(cb => cb.value);
        const fromDatetime = document.getElementById('from_datetime').value;
        const toDatetime = document.getElementById('to_datetime').value;
        if (laneIdsC.length === 0 || !fromDatetime || !toDatetime) return;
        const q = new URLSearchParams();
        laneIdsC.forEach(id => q.append('sub_location_ids[]', id));
        q.set('from_datetime', fromDatetime);
        q.set('to_datetime', toDatetime);
        q.set('auto_search', '1');
        const ic = (icRaw && icRaw !== 'N/A') ? icRaw.trim() : '';
        if (ic) {
            q.set('ic_no', ic);
        } else if (invitationId) {
            q.set('invitation_id', invitationId);
        } else {
            return;
        }
        window.location.href = `<?= base_url('report/chronology') ?>?${q.toString()}`;
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !document.getElementById('movementModal').classList.contains('hidden')) {
            closeMovementModal();
        }
    });

    // Delegated handler: inline onclick breaks when IC contains " (JSON.stringify broke the attribute)
    $(document).on('click', '#accessTable .js-access-view', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const inv = this.getAttribute('data-invitation-id');
        const ic = this.getAttribute('data-ic-no');
        const visitorName = this.getAttribute('data-visitor-name') || '';
        const visitReason = this.getAttribute('data-visit-reason') || '';
        if (inv === null || inv === '') return;
        openMovementHistory(parseInt(inv, 10), ic || '', visitorName, visitReason);
    });

    // ── Location multi-select dropdown ──────────────────────────────────────
    function toggleLocationDropdown(e) {
        e.stopPropagation();
        const panel   = document.getElementById('locationDropdownPanel');
        const chevron = document.getElementById('locationDropdownChevron');
        const isOpen  = !panel.classList.contains('hidden');
        if (isOpen) {
            panel.classList.add('hidden');
            chevron.textContent = 'expand_more';
        } else {
            panel.classList.remove('hidden');
            chevron.textContent = 'expand_less';
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const container = document.getElementById('locationDropdownContainer');
        if (container && !container.contains(e.target)) {
            document.getElementById('locationDropdownPanel').classList.add('hidden');
            document.getElementById('locationDropdownChevron').textContent = 'expand_more';
        }
    });

    function toggleSelectAllLocations(masterCb) {
        document.querySelectorAll('.location-checkbox').forEach(cb => {
            cb.checked = masterCb.checked;
        });
        updateLocationLabel();
    }

    function onLocationCheckboxChange() {
        const all      = document.querySelectorAll('.location-checkbox');
        const checked  = document.querySelectorAll('.location-checkbox:checked');
        const masterCb = document.getElementById('locationSelectAll');
        masterCb.indeterminate = checked.length > 0 && checked.length < all.length;
        masterCb.checked       = checked.length === all.length;
        updateLocationLabel();
    }

    function updateLocationLabel() {
        const checked = document.querySelectorAll('.location-checkbox:checked');
        const total   = document.querySelectorAll('.location-checkbox').length;
        const label   = document.getElementById('locationDropdownLabel');
        if (checked.length === 0) {
            label.textContent = '-- Select Locations --';
        } else if (checked.length === total) {
            label.textContent = 'All Locations (' + total + ')';
        } else if (checked.length === 1) {
            label.textContent = checked[0].closest('label').querySelector('span').textContent.trim();
        } else {
            label.textContent = checked.length + ' Locations selected';
        }
    }
    // ────────────────────────────────────────────────────────────────────────
</script>
</body>
</html>