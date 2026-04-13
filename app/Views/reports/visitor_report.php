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
                        <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-2">Visitor Report</h1>
                        <p class="text-slate-500 dark:text-slate-400 text-base font-medium">Detailed Visitor Activity Log</p>
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
                        </div>

                        <!-- Generate Button -->
                        <div class="flex-shrink-0">
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
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mt-2">Total Visitors</p>
                        </div>
                        <!-- Report Info -->
                        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-5 flex-1 shadow-sm flex flex-col justify-center gap-2">
                            <div>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Report Period: </span>
                                <span id="reportPeriod" class="text-sm text-slate-500"></span>
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
                            <table id="visitorTable" class="w-full" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>IC/Passport</th>
                                        <th>Company</th>
                                        <th>Host</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Check-In</th>
                                        <th>Check-Out</th>
                                        <th>Scans</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-16 flex flex-col items-center justify-center gap-3">
                    <span class="material-symbols-outlined text-5xl text-slate-200 dark:text-slate-700">bar_chart</span>
                    <p class="text-slate-400 font-semibold text-sm">Select a date range and click Generate</p>
                </div>

                <!-- Loading State -->
                <div id="loadingState" class="hidden bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-16 flex flex-col items-center justify-center gap-3">
                    <div class="size-10 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                    <p class="text-slate-400 font-semibold text-sm">Generating report...</p>
                </div>

                <!-- No Data State -->
                <div id="noDataState" class="hidden bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-16 flex flex-col items-center justify-center gap-3">
                    <span class="material-symbols-outlined text-5xl text-slate-200 dark:text-slate-700">search_off</span>
                    <p class="text-slate-400 font-semibold text-sm">No visitors found for the selected period</p>
                </div>

            </div>
        </main>
    </div>
</div>

<script>
    let dtTable = null;
    let reportData = [];

    flatpickr('#from_datetime', {
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        time_24hr: true,
        defaultDate: new Date().toISOString().slice(0, 10) + ' 00:00',
    });

    flatpickr('#to_datetime', {
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        time_24hr: true,
        defaultDate: new Date().toISOString().slice(0, 10) + ' 23:59',
    });

    function showState(state) {
        document.getElementById('emptyState').classList.toggle('hidden', state !== 'empty');
        document.getElementById('loadingState').classList.toggle('hidden', state !== 'loading');
        document.getElementById('noDataState').classList.toggle('hidden', state !== 'nodata');
        document.getElementById('resultsSection').classList.toggle('hidden', state !== 'results');
    }

    function generateReport() {
        const from = document.getElementById('from_datetime').value;
        const to = document.getElementById('to_datetime').value;

        if (!from || !to) return alert('Please select date range');

        showState('loading');

        const formData = new FormData();
        formData.append('from_datetime', from);
        formData.append('to_datetime', to);

        fetch('<?= base_url('report/visitor/generate') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success || data.visitors.length === 0) return showState('nodata');
            reportData = data.visitors;
            renderTable(data);
        })
        .catch(() => showState('empty'));
    }

    function renderTable(data) {
        document.getElementById('totalCount').textContent = data.total_visitors;
        document.getElementById('reportPeriod').textContent = data.from_datetime + ' to ' + data.to_datetime;

        if (dtTable) dtTable.destroy();
        
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';
        data.visitors.forEach((v, i) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center">${i + 1}</td>
                <td class="font-semibold text-slate-800">${esc(v.visitor_name)}</td>
                <td>${esc(v.contact_no)}</td>
                <td>${esc(v.ic_no)}</td>
                <td>${esc(v.visitor_company)}</td>
                <td>${esc(v.person_visited)}</td>
                <td>${v.visit_date}</td>
                <td><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">${v.visit_status}</span></td>
                <td>${v.checkin_time}</td>
                <td>${v.checkout_time}</td>
                <td class="text-center"><span class="bg-primary/10 text-primary px-2 py-0.5 rounded-full font-bold">${v.total_scans}</span></td>
            `;
            tbody.appendChild(tr);
        });

        dtTable = $('#visitorTable').DataTable({
            pageLength: 10,
            ordering: true,
            language: { search: "Filter results:" }
        });
        showState('results');
    }

    function esc(s) {
        if (!s) return '-';
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function exportCSV() {
        if (!reportData.length) return;
        const headers = ['No','Name','Contact','IC','Company','Host','Date','Status','Check-In','Check-Out','Scans'];
        const rows = reportData.map((v, i) => [i+1, v.visitor_name, v.contact_no, v.ic_no, v.visitor_company, v.person_visited, v.visit_date, v.visit_status, v.checkin_time, v.checkout_time, v.total_scans]);
        let csv = headers.join(',') + '\n' + rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
        const a = document.createElement('a');
        a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
        a.download = 'visitor_report.csv';
        a.click();
    }
</script>
</body>
</html>
