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
        .kpi-card { position: relative; overflow: hidden; text-align: left; }
        .kpi-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--accent, #94a3b8); }
        .kpi-card[data-active="1"] { box-shadow: 0 0 0 2px var(--accent, #137fec); }
        #staffReportTable tbody tr:hover { background: rgba(19,127,236,0.04); }
        .avatar-chip { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 9999px; font-size: 11px; font-weight: 700; flex-shrink: 0; }
        .skel { background: linear-gradient(90deg, rgba(0,0,0,0.06) 25%, rgba(0,0,0,0.12) 37%, rgba(0,0,0,0.06) 63%); background-size: 400% 100%; animation: skel-shimmer 1.4s ease infinite; border-radius: 6px; }
        .dark .skel { background: linear-gradient(90deg, rgba(255,255,255,0.06) 25%, rgba(255,255,255,0.12) 37%, rgba(255,255,255,0.06) 63%); background-size: 400% 100%; }
        @keyframes skel-shimmer { 0% { background-position: 100% 50%; } 100% { background-position: 0 50%; } }
        button:focus-visible, input:focus-visible, select:focus-visible, a:focus-visible { outline: 2px solid #137fec; outline-offset: 2px; }
        .view-tab[aria-selected="true"] { background: #137fec; color: #fff; }
        input:disabled, select:disabled { opacity: .45; cursor: not-allowed; }
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
                        <span class="material-symbols-outlined">badge</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Staff Report</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Gate in / gate out of staff pass holders, who is on site, and who has been on site <?= (int) $oowHours ?>h or more</p>
                    </div>
                </div>
                <button id="exportBtn" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:border-emerald-400 dark:hover:border-emerald-500 text-gray-700 dark:text-gray-200 hover:text-emerald-700 dark:hover:text-emerald-400 px-4 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 shadow-sm transition-colors">
                    <span class="material-symbols-outlined text-[18px]">file_download</span>
                    Export to Excel
                </button>
            </div>

            <?php if (! $trackingOn): ?>
            <div class="mb-6 text-sm text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg px-4 py-3 flex items-start gap-2">
                <span class="material-symbols-outlined text-[18px]">warning</span>
                <span>Staff gate tracking is not set up on this server yet. Run <code class="font-mono">php spark migrate</code>, then staff card taps at the gate will appear here.</span>
            </div>
            <?php endif; ?>

            <!-- View tabs (KPK: Staff Report / Staff In-Premise / Staff Out-of-Window) -->
            <div role="tablist" class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-1 mb-4 text-xs font-semibold shadow-sm">
                <button type="button" role="tab" class="view-tab px-3 py-1.5 rounded-md text-gray-600 dark:text-gray-300" data-view="all" aria-selected="true">All Movements</button>
                <button type="button" role="tab" class="view-tab px-3 py-1.5 rounded-md text-gray-600 dark:text-gray-300" data-view="in_premise" aria-selected="false">In Premise</button>
                <button type="button" role="tab" class="view-tab px-3 py-1.5 rounded-md text-gray-600 dark:text-gray-300" data-view="out_of_window" aria-selected="false">Out of Window</button>
            </div>

            <!-- Filters -->
            <form id="filterForm" class="flex flex-wrap items-end gap-4 mb-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm">
                <div class="flex-1 min-w-[220px]">
                    <label for="searchInput" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Search</label>
                    <input id="searchInput" type="search" placeholder="Name, staff no., IC / passport, contact" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>
                <div>
                    <label for="fromDate" class="flex items-center gap-1 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">
                        <span class="material-symbols-outlined text-[14px]">calendar_today</span> Time in from
                    </label>
                    <input id="fromDate" type="text" class="flatpickr border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm w-36 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none" placeholder="YYYY-MM-DD">
                </div>
                <div>
                    <label for="toDate" class="flex items-center gap-1 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">
                        <span class="material-symbols-outlined text-[14px]">calendar_today</span> To
                    </label>
                    <input id="toDate" type="text" class="flatpickr border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm w-36 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none" placeholder="YYYY-MM-DD">
                </div>
                <div>
                    <label for="deptFilter" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Department</label>
                    <select id="deptFilter" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm w-40 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <option value="">All departments</option>
                        <?php foreach ($departments as $d): ?>
                        <option value="<?= esc($d, 'attr') ?>"><?= esc($d) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="statusFilter" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Status</label>
                    <select id="statusFilter" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm w-36 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <option value="all">All status</option>
                        <option value="gate_in">Gate In (on site)</option>
                        <option value="gate_out">Gate Out</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div>
                    <label for="sortFilter" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Sort by</label>
                    <select id="sortFilter" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm w-40 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <option value="time_desc">Time in (newest)</option>
                        <option value="time_asc">Time in (oldest)</option>
                        <option value="name_asc">Name A–Z</option>
                        <option value="name_desc">Name Z–A</option>
                        <option value="staff_asc">Staff no. ascending</option>
                        <option value="staff_desc">Staff no. descending</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-primary hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow-sm transition-colors">Generate</button>
                    <button type="button" id="resetBtn" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 px-2 py-2 text-sm font-medium transition-colors">Reset</button>
                </div>
                <p id="viewHint" class="w-full text-xs text-gray-400 -mt-1"></p>
            </form>

            <!-- Summary cards (click to jump to that view) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <?php foreach ([
                    ['gate_ins',      'Gate Ins',          'login',          '#137fec', 'text-gray-900 dark:text-white',         'all',           'all'],
                    ['unique_staff',  'Staff',             'groups',         '#64748b', 'text-gray-900 dark:text-white',         'all',           'all'],
                    ['in_premise',    'In Premise Now',    'location_on',    '#10b981', 'text-emerald-600 dark:text-emerald-400', 'in_premise',    'all'],
                    ['out_of_window', 'Out of Window',     'warning',        '#ef4444', 'text-red-600 dark:text-red-400',         'out_of_window', 'all'],
                    ['checked_out',   'Gate Outs',         'logout',         '#94a3b8', 'text-slate-500 dark:text-slate-300',     'all',           'gate_out'],
                    ['rejected',      'Rejected',          'block',          '#f59e0b', 'text-amber-600 dark:text-amber-400',     'all',           'rejected'],
                ] as [$key, $label, $icon, $accent, $cls, $toView, $toStatus]): ?>
                <button type="button" class="kpi-card bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 pl-5 shadow-sm hover:border-gray-300 transition-colors" style="--accent:<?= $accent ?>" data-goto-view="<?= $toView ?>" data-goto-status="<?= $toStatus ?>" data-kpi="<?= $key ?>">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400"><?= esc($label) ?></p>
                        <span class="material-symbols-outlined text-[16px]" style="color:<?= $accent ?>"><?= $icon ?></span>
                    </div>
                    <p class="text-3xl font-extrabold mt-1.5 tabular-nums <?= $cls ?>" data-count="<?= $key ?>">0</p>
                </button>
                <?php endforeach; ?>
            </div>

            <p id="truncatedNotice" class="hidden mb-4 text-xs text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg px-3 py-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px]">info</span>
                <span id="truncatedNoticeText"></span>
            </p>

            <!-- Table -->
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="staffReportTable" class="w-full min-w-max text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/40 text-gray-600 dark:text-gray-300 font-semibold uppercase tracking-wide">
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Date</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Name</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Staff No</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">IC / Passport</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Contact No</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Department</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Location</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Time In</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Time Out</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Duration</th>
                                <th class="p-3 border-b border-gray-200 dark:border-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody id="staffReportBody" class="text-gray-600 dark:text-gray-300 divide-y divide-gray-100 dark:divide-gray-700"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        const DEFAULT_FROM = '<?= date('Y-m-d', strtotime('-30 days')) ?>';
        const DEFAULT_TO   = '<?= date('Y-m-d') ?>';
        const OOW_HOURS    = <?= (int) $oowHours ?>;
        const VIEW_LABEL   = { all: 'All Movements', in_premise: 'In Premise', out_of_window: 'Out of Window' };
        const VIEW_HINT    = {
            all: 'Every staff gate-in whose time in falls in the date range.',
            in_premise: 'Everyone gated in and not yet out, whatever date they came in. Dates and status do not apply.',
            out_of_window: 'On site for ' + OOW_HOURS + 'h or more: still inside for ' + OOW_HOURS + 'h+, or a visit in the date range that lasted ' + OOW_HOURS + 'h+.',
        };

        flatpickr('#fromDate', { dateFormat: 'Y-m-d', defaultDate: DEFAULT_FROM });
        flatpickr('#toDate',   { dateFormat: 'Y-m-d', defaultDate: DEFAULT_TO });

        let currentView = 'all';
        let dataTable   = null;
        let lastRows    = [];

        const esc = (v) => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        const statusBadge = {
            'Gate In':  { cls: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', dot: 'bg-emerald-500' },
            'Gate Out': { cls: 'bg-slate-100 text-slate-700 dark:bg-slate-700/40 dark:text-slate-300', dot: 'bg-slate-400' },
            'Rejected': { cls: 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', dot: 'bg-amber-500' },
        };
        const avatarColors = ['bg-blue-100 text-blue-700', 'bg-purple-100 text-purple-700', 'bg-teal-100 text-teal-700', 'bg-orange-100 text-orange-700', 'bg-pink-100 text-pink-700'];

        function statusChip(r) {
            const cfg = statusBadge[r.status] || { cls: 'bg-gray-100 text-gray-700', dot: 'bg-gray-400' };
            let html = `<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold ${cfg.cls}"><span class="size-1.5 rounded-full ${cfg.dot}"></span>${esc(r.status)}</span>`;
            if (r.out_of_window) {
                html += ` <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400">Out of Window</span>`;
            }
            if (r.remark) {
                html += `<div class="mt-1 text-[10px] text-gray-500 dark:text-gray-400 max-w-[220px] whitespace-normal">${esc(r.remark)}</div>`;
            }
            return html;
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

        function setView(view, status) {
            currentView = view;
            document.querySelectorAll('.view-tab').forEach(b => b.setAttribute('aria-selected', b.dataset.view === view ? 'true' : 'false'));
            if (status !== undefined) document.getElementById('statusFilter').value = status;
            const presenceOnly = view === 'in_premise';
            document.getElementById('fromDate').disabled = presenceOnly;
            document.getElementById('toDate').disabled = presenceOnly;
            document.getElementById('statusFilter').disabled = view !== 'all';
            document.getElementById('viewHint').textContent = VIEW_HINT[view];
        }

        function showSkeleton() {
            document.getElementById('staffReportBody').innerHTML = Array.from({ length: 6 }).map(() =>
                `<tr>${Array.from({ length: 11 }).map(() => '<td class="p-3"><div class="skel h-3 w-full"></div></td>').join('')}</tr>`
            ).join('');
        }

        function renderTable(rows) {
            const body = document.getElementById('staffReportBody');
            if (rows.length === 0) {
                body.innerHTML = `
                    <tr><td colspan="11" class="py-14 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">search_off</span>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">No staff gate records found</p>
                            <p class="text-xs text-gray-400">Try widening the date range or clearing a filter.</p>
                        </div>
                    </td></tr>`;
                return;
            }
            body.innerHTML = rows.map(r => `
                <tr class="transition-colors">
                    <td class="p-3 text-gray-500 dark:text-gray-400">${esc(r.date)}</td>
                    <td class="p-3">
                        <a href="${esc(r.detail_url)}" class="flex items-center gap-2 hover:underline">
                            <span class="avatar-chip ${colorFor(r.full_name)}">${esc(initials(r.full_name))}</span>
                            <span class="font-semibold text-gray-800 dark:text-white">${esc(r.full_name)}</span>
                        </a>
                    </td>
                    <td class="p-3 font-medium">${esc(r.staff_no)}</td>
                    <td class="p-3">${esc(r.ic_masked)}</td>
                    <td class="p-3">${esc(r.contact)}</td>
                    <td class="p-3">${esc(r.department)}</td>
                    <td class="p-3">${esc(r.location)}</td>
                    <td class="p-3" data-order="${esc(r.time_in_sort ?? '')}">${esc(r.time_in)}</td>
                    <td class="p-3">${esc(r.time_out)}</td>
                    <td class="p-3 tabular-nums" data-order="${r.minutes ?? -1}">${esc(r.duration)}</td>
                    <td class="p-3">${statusChip(r)}</td>
                </tr>`).join('');
        }

        function loadReport() {
            showSkeleton();
            if (dataTable) { dataTable.destroy(); dataTable = null; }

            const params = new URLSearchParams({
                view: currentView,
                from: document.getElementById('fromDate').value,
                to: document.getElementById('toDate').value,
                status: document.getElementById('statusFilter').value,
                sort: document.getElementById('sortFilter').value,
                search: document.getElementById('searchInput').value.trim(),
                department: document.getElementById('deptFilter').value,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
            });

            fetch('<?= base_url('report/staff/generate') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>', 'X-Requested-With': 'XMLHttpRequest' },
                body: params,
            })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        lastRows = [];
                        renderTable([]);
                        alert(data.message || 'Failed to load report.');
                        return;
                    }
                    lastRows = data.rows;
                    Object.keys(data.counts).forEach(key => {
                        const el = document.querySelector(`[data-count="${key}"]`);
                        if (el) el.textContent = Number(data.counts[key]).toLocaleString();
                    });
                    document.querySelectorAll('.kpi-card').forEach(c => {
                        const status = document.getElementById('statusFilter').value;
                        const on = c.dataset.gotoView === currentView && (currentView !== 'all' || c.dataset.gotoStatus === status) && c.dataset.kpi !== 'unique_staff';
                        c.dataset.active = on ? '1' : '0';
                    });

                    const notice = document.getElementById('truncatedNotice');
                    notice.classList.toggle('hidden', !data.truncated);
                    if (data.truncated) document.getElementById('truncatedNoticeText').textContent = data.message;

                    renderTable(data.rows);
                    if (data.rows.length) {
                        dataTable = $('#staffReportTable').DataTable({ pageLength: 25, order: [], searching: false });
                    }
                })
                .catch(() => { renderTable([]); alert('Failed to load report.'); });
        }

        document.querySelectorAll('.view-tab').forEach(b => b.addEventListener('click', () => { setView(b.dataset.view); loadReport(); }));
        document.querySelectorAll('.kpi-card').forEach(c => c.addEventListener('click', () => { setView(c.dataset.gotoView, c.dataset.gotoStatus); loadReport(); }));
        ['statusFilter', 'sortFilter', 'deptFilter'].forEach(id => document.getElementById(id).addEventListener('change', loadReport));

        document.getElementById('filterForm').addEventListener('submit', e => { e.preventDefault(); loadReport(); });

        document.getElementById('resetBtn').addEventListener('click', () => {
            document.getElementById('fromDate')._flatpickr.setDate(DEFAULT_FROM);
            document.getElementById('toDate')._flatpickr.setDate(DEFAULT_TO);
            document.getElementById('searchInput').value = '';
            document.getElementById('deptFilter').value = '';
            document.getElementById('sortFilter').value = 'time_desc';
            setView('all', 'all');
            loadReport();
        });

        document.getElementById('exportBtn').addEventListener('click', () => {
            if (lastRows.length === 0) { alert('Nothing to export yet — generate a report first.'); return; }
            const header = ['Date', 'Full Name', 'Staff No', 'IC/Passport', 'Contact No', 'Department', 'Location In', 'Location Out',
                            'Time In', 'Time Out', 'Duration (hrs)', 'Status', 'Out of Window', 'Remark'];
            const rows = lastRows.map(r => [
                r.date, r.full_name, r.staff_no, r.ic_masked, r.contact, r.department, r.location, r.exit_location,
                r.time_in, r.time_out, r.minutes === null ? '' : Math.round(r.minutes / 60 * 100) / 100,
                r.status, r.out_of_window ? 'Yes' : 'No', r.remark,
            ]);
            const ws = XLSX.utils.aoa_to_sheet([header, ...rows]);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Staff Report');
            const stamp = currentView === 'in_premise' ? DEFAULT_TO : document.getElementById('fromDate').value + '_to_' + document.getElementById('toDate').value;
            XLSX.writeFile(wb, 'staff_report_' + currentView + '_' + stamp + '.xlsx');
        });

        setView('all', 'all');
        loadReport();
    </script>
</body>
</html>
