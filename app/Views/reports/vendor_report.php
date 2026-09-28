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
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: { "primary": "#137fec", "background-light": "#f6f7f8", "background-dark": "#101922" },
                    fontFamily: { "display": ["Montserrat", "sans-serif"], "sans": ["Montserrat", "sans-serif"] },
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
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>
    <style>
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: 0.001ms !important; transition-duration: 0.001ms !important; } }
        .kpi-card { position: relative; overflow: hidden; }
        .kpi-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--accent, #94a3b8); }
        .comp-seg { transition: flex-basis 0.4s ease; }
        #vendorReportTable tbody tr:hover { background: rgba(19,127,236,0.04); }
        .avatar-chip { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 9999px; font-size: 11px; font-weight: 700; flex-shrink: 0; }
        .skel { background: linear-gradient(90deg, rgba(0,0,0,0.06) 25%, rgba(0,0,0,0.12) 37%, rgba(0,0,0,0.06) 63%); background-size: 400% 100%; animation: skel-shimmer 1.4s ease infinite; border-radius: 6px; }
        .dark .skel { background: linear-gradient(90deg, rgba(255,255,255,0.06) 25%, rgba(255,255,255,0.12) 37%, rgba(255,255,255,0.06) 63%); background-size: 400% 100%; }
        @keyframes skel-shimmer { 0% { background-position: 100% 50%; } 100% { background-position: 0 50%; } }
        button:focus-visible, input:focus-visible, select:focus-visible, a:focus-visible { outline: 2px solid #137fec; outline-offset: 2px; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden">

    <?= view('reports/partials/report_sidebar', ['current' => $current]) ?>

    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8">
        <div class="max-w-7xl mx-auto">

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
                <div class="flex items-start gap-3">
                    <div class="size-11 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0 mt-0.5">
                        <span class="material-symbols-outlined">local_shipping</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Vendor Report</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Pass status and on-site presence for vendor personnel</p>
                    </div>
                </div>
                <button id="exportBtn" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:border-emerald-400 dark:hover:border-emerald-500 text-gray-700 dark:text-gray-200 hover:text-emerald-700 dark:hover:text-emerald-400 px-4 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 shadow-sm transition-colors">
                    <span class="material-symbols-outlined text-[18px]">file_download</span>
                    Export to Excel
                </button>
            </div>

            <!-- Filters -->
            <form id="filterForm" class="flex flex-wrap items-end gap-4 mb-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm">
                <div>
                    <label class="flex items-center gap-1 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">
                        <span class="material-symbols-outlined text-[14px]">calendar_today</span> From
                    </label>
                    <input id="fromDate" type="text" class="flatpickr border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm w-40 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none" placeholder="YYYY-MM-DD">
                </div>
                <div>
                    <label class="flex items-center gap-1 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">
                        <span class="material-symbols-outlined text-[14px]">calendar_today</span> To
                    </label>
                    <input id="toDate" type="text" class="flatpickr border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm w-40 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none" placeholder="YYYY-MM-DD">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Status</label>
                    <select id="statusFilter" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm w-36 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <?php foreach (['all' => 'All Status', 'Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected', 'Suspended' => 'Suspended'] as $val => $label): ?>
                        <option value="<?= $val ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Presence</label>
                    <select id="presenceFilter" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm w-40 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <?php foreach (['all' => 'All', 'In Premise' => 'In Premise', 'Out of Window' => 'Out of Window', 'Checked Out' => 'Checked Out', 'Not Yet Arrived' => 'Not Yet Arrived'] as $val => $label): ?>
                        <option value="<?= $val ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-primary hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow-sm transition-colors">
                        Generate
                    </button>
                    <button type="button" id="resetBtn" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 px-2 py-2 text-sm font-medium transition-colors">
                        Reset
                    </button>
                </div>
            </form>

            <!-- Summary cards -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-4">
                <div class="kpi-card bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 pl-5 shadow-sm" style="--accent:#64748b">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total</p>
                        <span class="material-symbols-outlined text-[16px] text-gray-400">groups</span>
                    </div>
                    <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1.5 tabular-nums" data-count="total">0</p>
                </div>
                <div class="kpi-card bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 pl-5 shadow-sm" style="--accent:#10b981">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">In Premise</p>
                        <span class="material-symbols-outlined text-[16px] text-emerald-500">login</span>
                    </div>
                    <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1.5 tabular-nums" data-count="in_premise">0</p>
                </div>
                <div class="kpi-card bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 pl-5 shadow-sm" style="--accent:#ef4444">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Out Of Window</p>
                        <span class="material-symbols-outlined text-[16px] text-red-500">warning</span>
                    </div>
                    <p class="text-3xl font-extrabold text-red-600 dark:text-red-400 mt-1.5 tabular-nums" data-count="out_of_window">0</p>
                </div>
                <div class="kpi-card bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 pl-5 shadow-sm" style="--accent:#94a3b8">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Checked Out</p>
                        <span class="material-symbols-outlined text-[16px] text-slate-400">logout</span>
                    </div>
                    <p class="text-3xl font-extrabold text-slate-500 dark:text-slate-300 mt-1.5 tabular-nums" data-count="checked_out">0</p>
                </div>
                <div class="kpi-card bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 pl-5 shadow-sm" style="--accent:#f59e0b">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Not Yet Arrived</p>
                        <span class="material-symbols-outlined text-[16px] text-amber-500">schedule</span>
                    </div>
                    <p class="text-3xl font-extrabold text-amber-600 dark:text-amber-400 mt-1.5 tabular-nums" data-count="not_yet_arrived">0</p>
                </div>
            </div>

            <!-- Presence composition bar -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-6 shadow-sm">
                <div class="flex items-center justify-between mb-2.5">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Presence breakdown</p>
                    <p class="text-xs text-gray-400" id="compTotalLabel">0 records</p>
                </div>
                <div class="flex h-2.5 w-full rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700" id="compBar">
                    <div class="comp-seg bg-emerald-500" style="flex-basis:0%" data-seg="in_premise"></div>
                    <div class="comp-seg bg-red-500" style="flex-basis:0%" data-seg="out_of_window"></div>
                    <div class="comp-seg bg-slate-400" style="flex-basis:0%" data-seg="checked_out"></div>
                    <div class="comp-seg bg-amber-400" style="flex-basis:0%" data-seg="not_yet_arrived"></div>
                </div>
            </div>

            <p id="truncatedNotice" class="hidden mb-4 text-xs text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg px-3 py-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px]">info</span>
                <span id="truncatedNoticeText"></span>
            </p>

            <!-- Table -->
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="vendorReportTable" class="w-full min-w-max text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/40 text-gray-600 dark:text-gray-300 font-semibold uppercase tracking-wide">
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">App No</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Date</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Name</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">IC / Passport</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Vendor Company</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Designation</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Status</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Check In</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Check Out</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Presence</th>
                            </tr>
                        </thead>
                        <tbody id="vendorReportBody" class="text-gray-600 dark:text-gray-300 divide-y divide-gray-100 dark:divide-gray-700"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        flatpickr('#fromDate', { dateFormat: 'Y-m-d', defaultDate: '<?= date('Y-m-d', strtotime('-30 days')) ?>' });
        flatpickr('#toDate', { dateFormat: 'Y-m-d', defaultDate: '<?= date('Y-m-d') ?>' });

        let dataTable = null;
        let lastVendors = [];

        const presenceBadge = {
            'In Premise':      { cls: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', dot: 'bg-emerald-500' },
            'Out of Window':   { cls: 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400', dot: 'bg-red-500' },
            'Checked Out':     { cls: 'bg-slate-100 text-slate-700 dark:bg-slate-700/40 dark:text-slate-300', dot: 'bg-slate-400' },
            'Not Yet Arrived': { cls: 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', dot: 'bg-amber-400' },
        };
        const statusBadge = {
            'Pending':   'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
            'Approved':  'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
            'Rejected':  'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            'Suspended': 'bg-gray-100 text-gray-700 dark:bg-gray-700/40 dark:text-gray-300',
        };
        const avatarColors = ['bg-blue-100 text-blue-700', 'bg-purple-100 text-purple-700', 'bg-teal-100 text-teal-700', 'bg-orange-100 text-orange-700', 'bg-pink-100 text-pink-700'];

        function statusChip(value) {
            const cls = statusBadge[value] || 'bg-gray-100 text-gray-700';
            return `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${cls}">${value}</span>`;
        }

        function presenceChip(value) {
            const cfg = presenceBadge[value] || { cls: 'bg-gray-100 text-gray-700', dot: 'bg-gray-400' };
            return `<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold ${cfg.cls}"><span class="size-1.5 rounded-full ${cfg.dot}"></span>${value}</span>`;
        }

        function initials(name) {
            const parts = String(name || '').trim().split(/\s+/).filter(Boolean);
            if (parts.length === 0) return '?';
            return (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase();
        }

        function colorFor(name) {
            let h = 0;
            for (let i = 0; i < name.length; i++) h = name.charCodeAt(i) + ((h << 5) - h);
            return avatarColors[Math.abs(h) % avatarColors.length];
        }

        function showSkeleton() {
            const body = document.getElementById('vendorReportBody');
            body.innerHTML = Array.from({ length: 6 }).map(() => `
                <tr>${Array.from({ length: 10 }).map(() => `<td class="p-3"><div class="skel h-3 w-full"></div></td>`).join('')}</tr>
            `).join('');
        }

        function renderTable(vendors) {
            const body = document.getElementById('vendorReportBody');
            body.innerHTML = '';

            if (vendors.length === 0) {
                body.innerHTML = `
                    <tr><td colspan="10" class="py-14 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">search_off</span>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">No vendor pass records found</p>
                            <p class="text-xs text-gray-400">Try widening the date range or clearing a filter.</p>
                        </div>
                    </td></tr>`;
                return;
            }

            vendors.forEach(v => {
                const tr = document.createElement('tr');
                tr.className = 'transition-colors';
                tr.innerHTML = `
                    <td class="p-3 font-medium text-gray-500 dark:text-gray-400">${v.app_no}</td>
                    <td class="p-3">${v.date_of_application}</td>
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            <span class="avatar-chip ${colorFor(v.full_name)}">${initials(v.full_name)}</span>
                            <span class="font-semibold text-gray-800 dark:text-white">${v.full_name}</span>
                        </div>
                    </td>
                    <td class="p-3">${v.ic_no_masked}</td>
                    <td class="p-3">${v.vendor_company_name}</td>
                    <td class="p-3">${v.designation}</td>
                    <td class="p-3">${statusChip(v.status)}</td>
                    <td class="p-3">${v.check_in}</td>
                    <td class="p-3">${v.check_out}</td>
                    <td class="p-3">${presenceChip(v.presence)}</td>
                `;
                body.appendChild(tr);
            });
        }

        function updateComposition(counts) {
            const segKeys = ['in_premise', 'out_of_window', 'checked_out', 'not_yet_arrived'];
            const total = segKeys.reduce((sum, k) => sum + (counts[k] || 0), 0);
            document.getElementById('compTotalLabel').textContent = total.toLocaleString() + ' record' + (total === 1 ? '' : 's');
            segKeys.forEach(key => {
                const pct = total === 0 ? 0 : (counts[key] || 0) / total * 100;
                const el = document.querySelector(`[data-seg="${key}"]`);
                if (el) el.style.flexBasis = pct + '%';
            });
        }

        function loadReport() {
            const from = document.getElementById('fromDate').value;
            const to = document.getElementById('toDate').value;
            const status = document.getElementById('statusFilter').value;
            const presence = document.getElementById('presenceFilter').value;

            showSkeleton();
            if (dataTable) { dataTable.destroy(); dataTable = null; }

            fetch('<?= base_url('report/vendor/generate') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
                body: new URLSearchParams({ from, to, status, presence }),
            })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) { alert('Failed to load report.'); return; }

                    lastVendors = data.vendors;

                    Object.keys(data.counts).forEach(key => {
                        const el = document.querySelector(`[data-count="${key}"]`);
                        if (el) el.textContent = Number(data.counts[key]).toLocaleString();
                    });
                    updateComposition(data.counts);

                    const notice = document.getElementById('truncatedNotice');
                    if (data.truncated) {
                        document.getElementById('truncatedNoticeText').textContent = data.message;
                        notice.classList.remove('hidden');
                    } else {
                        notice.classList.add('hidden');
                    }

                    renderTable(data.vendors);
                    dataTable = $('#vendorReportTable').DataTable({ pageLength: 25, order: [] });
                });
        }

        document.getElementById('filterForm').addEventListener('submit', function (e) {
            e.preventDefault();
            loadReport();
        });

        document.getElementById('resetBtn').addEventListener('click', function () {
            document.getElementById('fromDate')._flatpickr.setDate('<?= date('Y-m-d', strtotime('-30 days')) ?>');
            document.getElementById('toDate')._flatpickr.setDate('<?= date('Y-m-d') ?>');
            document.getElementById('statusFilter').value = 'all';
            document.getElementById('presenceFilter').value = 'all';
            loadReport();
        });

        document.getElementById('exportBtn').addEventListener('click', function () {
            if (lastVendors.length === 0) { alert('Nothing to export yet — generate a report first.'); return; }

            const header = ['App No', 'Date', 'Full Name', 'IC/Passport', 'Vendor Company', 'Designation', 'Status', 'Check In', 'Check Out', 'Presence'];
            const rows = lastVendors.map(v => [
                v.app_no, v.date_of_application, v.full_name, v.ic_no_masked,
                v.vendor_company_name, v.designation, v.status, v.check_in, v.check_out, v.presence,
            ]);
            const ws = XLSX.utils.aoa_to_sheet([header, ...rows]);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Vendor Report');
            XLSX.writeFile(wb, 'vendor_report_' + document.getElementById('fromDate').value + '_to_' + document.getElementById('toDate').value + '.xlsx');
        });

        // Load once on page open with default date range.
        loadReport();
    </script>
</body>
</html>
