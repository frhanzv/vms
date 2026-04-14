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
        }
        table.dataTable tbody td {
            font-size: 0.82rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
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

        /* DataTables “Show N entries” — arrow must not cover the number */
        .dataTables_wrapper .dataTables_length select {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            min-width: 4.25rem !important;
            padding: 0.35rem 2rem 0.35rem 0.6rem !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.375rem !important;
            font-size: 0.875rem !important;
            background-color: #fff !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.45rem center !important;
            background-size: 1rem !important;
        }
        .dark .dataTables_wrapper .dataTables_length select {
            background-color: rgb(30 41 59) !important;
            border-color: rgb(51 65 85) !important;
            color: rgb(241 245 249) !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") !important;
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

                <div id="chronologyResultsWrap" class="hidden bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Access chronology</h3>
                    </div>
                    <div class="p-5 overflow-x-auto">
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

                <div id="chronologyEmpty" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-16 flex flex-col items-center justify-center gap-3">
                    <span class="material-symbols-outlined text-5xl text-slate-200 dark:text-slate-700">travel_explore</span>
                    <p class="text-slate-500 font-semibold text-sm text-center">No visitor details found</p>
                    <p class="text-slate-400 text-sm text-center max-w-md">Try searching with a different staff number or IC number, widen the date range, or choose “All locations”.</p>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
let chronologyDt = null;
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
            pageLength: 25,
            order: [],
            language: { search: 'Filter table:' }
        });
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
