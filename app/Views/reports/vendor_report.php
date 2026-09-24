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
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden">

    <?= view('reports/partials/report_sidebar', ['current' => $current]) ?>

    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8">
        <div class="max-w-7xl mx-auto">

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">Vendor Report</h1>
                <button id="exportBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-1.5 shadow transition-colors">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Export to Excel
                </button>
            </div>

            <!-- Filters -->
            <form id="filterForm" class="flex flex-wrap items-end gap-4 mb-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">From</label>
                    <input id="fromDate" type="text" class="flatpickr border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm w-40" placeholder="YYYY-MM-DD">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">To</label>
                    <input id="toDate" type="text" class="flatpickr border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm w-40" placeholder="YYYY-MM-DD">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Status</label>
                    <select id="statusFilter" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm w-40">
                        <?php foreach (['all' => 'All Status', 'Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected', 'Suspended' => 'Suspended'] as $val => $label): ?>
                        <option value="<?= $val ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="bg-primary hover:bg-blue-700 text-white px-5 py-2 rounded text-sm font-medium shadow transition-colors">
                    Generate
                </button>
            </form>

            <!-- Summary cards -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6" id="summaryCards">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">Total</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1" data-count="total">0</p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">Active Pass</p>
                    <p class="text-2xl font-bold text-emerald-500 mt-1" data-count="active">0</p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">Expiring Soon</p>
                    <p class="text-2xl font-bold text-amber-500 mt-1" data-count="expiring_soon">0</p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">Expired</p>
                    <p class="text-2xl font-bold text-red-500 mt-1" data-count="expired">0</p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">Not Issued</p>
                    <p class="text-2xl font-bold text-gray-500 mt-1" data-count="not_issued">0</p>
                </div>
            </div>

            <p id="truncatedNotice" class="hidden mb-4 text-xs text-amber-600 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded px-3 py-2"></p>

            <!-- Table -->
            <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <table id="vendorReportTable" class="w-full min-w-max text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold uppercase tracking-wide">
                            <th class="p-3 border-b dark:border-gray-600">App No</th>
                            <th class="p-3 border-b dark:border-gray-600">Date</th>
                            <th class="p-3 border-b dark:border-gray-600">Full Name</th>
                            <th class="p-3 border-b dark:border-gray-600">IC / Passport</th>
                            <th class="p-3 border-b dark:border-gray-600">Vendor Company</th>
                            <th class="p-3 border-b dark:border-gray-600">Designation</th>
                            <th class="p-3 border-b dark:border-gray-600">Status</th>
                            <th class="p-3 border-b dark:border-gray-600">Pass Expiry</th>
                            <th class="p-3 border-b dark:border-gray-600">Pass Validity</th>
                        </tr>
                    </thead>
                    <tbody id="vendorReportBody" class="text-gray-600 dark:text-gray-300"></tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        flatpickr('#fromDate', { dateFormat: 'Y-m-d', defaultDate: '<?= date('Y-m-d', strtotime('-30 days')) ?>' });
        flatpickr('#toDate', { dateFormat: 'Y-m-d', defaultDate: '<?= date('Y-m-d') ?>' });

        let dataTable = null;
        let lastVendors = [];

        const validityBadge = {
            'Active':          'bg-emerald-50 text-emerald-700',
            'Expiring Soon':   'bg-amber-50 text-amber-700',
            'Expired':         'bg-red-50 text-red-700',
            'Not Issued':      'bg-gray-100 text-gray-700',
        };
        const statusBadge = {
            'Pending':   'bg-amber-50 text-amber-700',
            'Approved':  'bg-emerald-50 text-emerald-700',
            'Rejected':  'bg-red-50 text-red-700',
            'Suspended': 'bg-gray-100 text-gray-700',
        };

        function badge(map, value) {
            const cls = map[value] || 'bg-gray-100 text-gray-700';
            return `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${cls}">${value}</span>`;
        }

        function renderTable(vendors) {
            const body = document.getElementById('vendorReportBody');
            body.innerHTML = '';

            if (vendors.length === 0) {
                body.innerHTML = '<tr><td colspan="9" class="p-8 text-center text-gray-500">No vendor pass records found for this range.</td></tr>';
                return;
            }

            vendors.forEach(v => {
                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-100 dark:border-gray-700';
                tr.innerHTML = `
                    <td class="p-3">${v.app_no}</td>
                    <td class="p-3">${v.date_of_application}</td>
                    <td class="p-3 font-semibold text-gray-800 dark:text-white">${v.full_name}</td>
                    <td class="p-3">${v.ic_no_masked}</td>
                    <td class="p-3">${v.vendor_company_name}</td>
                    <td class="p-3">${v.designation}</td>
                    <td class="p-3">${badge(statusBadge, v.status)}</td>
                    <td class="p-3">${v.pass_expiry}</td>
                    <td class="p-3">${badge(validityBadge, v.pass_validity)}</td>
                `;
                body.appendChild(tr);
            });
        }

        function loadReport() {
            const from = document.getElementById('fromDate').value;
            const to = document.getElementById('toDate').value;
            const status = document.getElementById('statusFilter').value;

            fetch('<?= base_url('report/vendor/generate') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
                body: new URLSearchParams({ from, to, status }),
            })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) { alert('Failed to load report.'); return; }

                    lastVendors = data.vendors;

                    Object.keys(data.counts).forEach(key => {
                        const el = document.querySelector(`[data-count="${key}"]`);
                        if (el) el.textContent = data.counts[key];
                    });

                    const notice = document.getElementById('truncatedNotice');
                    if (data.truncated) {
                        notice.textContent = data.message;
                        notice.classList.remove('hidden');
                    } else {
                        notice.classList.add('hidden');
                    }

                    if (dataTable) { dataTable.destroy(); }
                    renderTable(data.vendors);
                    dataTable = $('#vendorReportTable').DataTable({ pageLength: 25, order: [] });
                });
        }

        document.getElementById('filterForm').addEventListener('submit', function (e) {
            e.preventDefault();
            loadReport();
        });

        document.getElementById('exportBtn').addEventListener('click', function () {
            if (lastVendors.length === 0) { alert('Nothing to export yet — generate a report first.'); return; }

            const header = ['App No', 'Date', 'Full Name', 'IC/Passport', 'Vendor Company', 'Designation', 'Status', 'Pass Expiry', 'Pass Validity'];
            const rows = lastVendors.map(v => [
                v.app_no, v.date_of_application, v.full_name, v.ic_no_masked,
                v.vendor_company_name, v.designation, v.status, v.pass_expiry, v.pass_validity,
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
