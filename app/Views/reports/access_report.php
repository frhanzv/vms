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
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Override DataTables to match VMS style */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            font-family: 'Montserrat', sans-serif;
            outline: none;
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
            border-radius: 0.375rem !important;
            font-size: 0.8rem;
            font-family: 'Montserrat', sans-serif;
            padding: 0.3rem 0.7rem !important;
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
        }
        table.dataTable tbody td {
            font-size: 0.82rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
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
                        <p class="text-slate-500 dark:text-slate-400 text-base font-medium">Staff Access History</p>
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

                        <!-- Select Location -->
                        <div class="flex flex-col gap-1.5 flex-1 min-w-0">
                            <label class="text-xs font-semibold text-slate-500 tracking-wider">Select Locations</label>
                            <div class="relative">
                                <select id="location_id" name="location_id"
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-lg pl-4 pr-10 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary appearance-none bg-none cursor-pointer">
                                    <option value="">-- Select Location --</option>
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?= esc($loc['id']) ?>">
                                            <?= esc($loc['id']) ?>. <?= esc($loc['branch']) ?> - <?= esc($loc['location_access']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-slate-400 pointer-events-none">expand_more</span>
                            </div>
                            <p class="text-xs text-slate-400">Select reporting location</p>
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
                        <!-- Export Button -->
                        <div class="flex items-center">
                            <button onclick="exportCSV()"
                                class="flex items-center gap-2 px-5 py-2.5 rounded-lg border border-primary text-primary bg-primary/5 hover:bg-primary/10 font-bold text-sm transition-colors shadow-sm whitespace-nowrap">
                                <span class="material-symbols-outlined text-[18px]">download</span>
                                Export CSV
                            </button>
                        </div>
                    </div>

                    <!-- Data Table Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-slate-100 dark:border-slate-700">
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Visitor Details</h3>
                        </div>
                        <div class="p-5 overflow-x-auto">
                            <table id="accessTable" class="w-full" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Visitor Name</th>
                                        <th>Contact No</th>
                                        <th>IC No</th>
                                        <th>Person Visited</th>
                                        <th>Company</th>
                                        <th>Vehicle No</th>
                                        <th>Visit Reason</th>
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
                <p id="movementModalStaff" class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Staff No: —</p>
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
                        <th class="px-3 py-3">Location</th>
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

<script>
    let dtTable = null;
    let reportData = [];

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
        const locationId   = document.getElementById('location_id').value;

        if (!fromDatetime || !toDatetime || !locationId) {
            alert('Please fill in all fields before generating the report.');
            return;
        }

        showState('loading');

        const formData = new FormData();
        formData.append('from_datetime', fromDatetime);
        formData.append('to_datetime',   toDatetime);
        formData.append('location_id',   locationId);

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
                <td class="text-center">
                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary">${v.total_access}</span>
                </td>
                <td class="whitespace-nowrap">${escHtml(v.first_access)}</td>
                <td class="whitespace-nowrap">${escHtml(v.last_access)}</td>
                <td class="text-center px-2 py-2">
                    <button type="button"
                        class="js-access-view inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm bg-primary/10 text-primary hover:bg-primary/20 transition-colors mx-auto"
                        data-invitation-id="${escAttr(String(v.invitation_id))}"
                        data-ic-no="${escAttr(v.ic_no)}">
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
            lengthMenu: [10, 25, 50, 100],
            ordering: true,
            responsive: false,
            language: {
                search: 'Search records:',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                paginate: { previous: 'Previous', next: 'Next' }
            },
            columnDefs: [
                { orderable: false, targets: [0, 11] },
                { className: 'text-center', targets: [0, 8, 11] }
            ]
        });

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

    function exportCSV() {
        if (!reportData || reportData.length === 0) return;

        const headers = ['No','Visitor Name','Contact No','IC No','Person Visited','Company','Vehicle No','Visit Reason','Total Access','First Access','Last Access','Invitation ID'];
        const rows = reportData.map((v, i) => [
            i + 1,
            v.visitor_name,
            v.contact_no,
            v.ic_no,
            v.person_visited,
            v.visitor_company,
            v.vehicle_no,
            v.visit_reason,
            v.total_access,
            v.first_access,
            v.last_access,
            v.invitation_id
        ]);

        let csv = headers.join(',') + '\n';
        rows.forEach(r => {
            csv += r.map(c => '"' + String(c ?? '').replace(/"/g, '""') + '"').join(',') + '\n';
        });

        const blob = new Blob([csv], { type: 'text/csv' });
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = 'access_report_' + new Date().toISOString().slice(0,10) + '.csv';
        a.click();
        URL.revokeObjectURL(url);
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

    function openMovementHistory(invitationId, icNo) {
        const locationId = document.getElementById('location_id').value;
        const fromDatetime = document.getElementById('from_datetime').value;
        const toDatetime = document.getElementById('to_datetime').value;
        if (!locationId || !fromDatetime || !toDatetime) {
            alert('Select location and date range, then generate the report before opening movement history.');
            return;
        }

        document.getElementById('movementModalInvitationId').value = String(invitationId);
        const ic = icNo != null ? String(icNo).trim() : '';
        document.getElementById('movementModalIcNo').value = (ic && ic !== 'N/A') ? ic : '';
        document.getElementById('movementModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        document.getElementById('movementModalLoading').classList.remove('hidden');
        document.getElementById('movementModalTable').classList.add('hidden');
        document.getElementById('movementModalEmpty').classList.add('hidden');
        document.getElementById('movementModalTableBody').innerHTML = '';
        document.getElementById('movementModalTitle').textContent = 'Movement History';
        document.getElementById('movementModalStaff').textContent = 'Staff No: —';

        const formData = new FormData();
        formData.append('invitation_id', invitationId);
        formData.append('location_id', locationId);
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
            document.getElementById('movementModalTitle').textContent = 'Movement History — ' + (data.staff_no || '');
            document.getElementById('movementModalStaff').textContent = 'Staff No: ' + (data.staff_no || '—');
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
                tr.innerHTML =
                    '<td class="whitespace-nowrap px-3 py-3 font-medium">' + escHtml(row.date_time) + '</td>' +
                    '<td class="px-3 py-3">' + escHtml(row.location) + '</td>' +
                    '<td class="px-3 py-3 text-center">' + movementBadgeYesNo(accessYes) + '</td>' +
                    '<td class="max-w-[200px] px-3 py-3 text-slate-600 dark:text-slate-400">' + escHtml(row.reason || '—') + '</td>' +
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
        const locationId = document.getElementById('location_id').value;
        const fromDatetime = document.getElementById('from_datetime').value;
        const toDatetime = document.getElementById('to_datetime').value;
        if (!locationId || !fromDatetime || !toDatetime) return;
        const q = new URLSearchParams();
        q.set('location_id', locationId);
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
        if (inv === null || inv === '') return;
        openMovementHistory(parseInt(inv, 10), ic || '');
    });
</script>
</body>
</html>