<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle ?? 'Blacklist Closed List') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "primary-dark": "#0f66be",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1a2634",
                    },
                    fontFamily: { "sans": ["Montserrat", "sans-serif"] },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white overflow-hidden">
<div class="flex h-screen w-full">

    <!-- Sidebar -->
    <?= view('partials/sidebar') ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <div class="flex-1 overflow-y-auto p-6 md:p-8 no-scrollbar">

            <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
            <?php endif; ?>

            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-5">

                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-700 uppercase tracking-tight">
                        Blacklist Individual Closed List
                    </h2>
                    <a href="<?= base_url('blacklist/closedlist/export') ?>"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary hover:bg-primary-dark text-white text-sm font-bold transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        Export
                    </a>
                </div>

                <!-- Search + Filters -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-6 flex gap-0">
                        <input type="text" id="searchInput" placeholder="IC NO / PASSPORT NO / NAME / STAFF ID"
                            class="flex-1 h-10 px-4 text-sm bg-white border border-slate-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 placeholder-slate-400 uppercase placeholder:normal-case"/>
                        <button type="button" onclick="applyClosedListFilters()"
                            class="flex items-center justify-center h-10 w-10 bg-primary hover:bg-primary-dark text-white rounded-r-lg transition-colors flex-shrink-0">
                            <span class="material-symbols-outlined text-[20px]">search</span>
                        </button>
                    </div>
                    <div class="md:col-span-3">
                        <select id="typeFilter" onchange="applyClosedListFilters()"
                            class="w-full h-10 pl-3 pr-8 text-sm bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-600 appearance-none cursor-pointer">
                            <option value="">TYPE OF BLACKLIST</option>
                            <option value="Staff">Staff</option>
                            <option value="Visitor">Visitor</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <select id="sortFilter" onchange="applyClosedListFilters()"
                            class="w-full h-10 pl-3 pr-8 text-sm bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-600 appearance-none cursor-pointer">
                            <option value="">SORT BY</option>
                            <option value="name_asc">Name (A-Z)</option>
                            <option value="date_desc">Blacklist Date (Newest)</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto w-full rounded-xl border border-slate-200 dark:border-slate-700">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Created Date</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Blacklist Date</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">IC / Passport No</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Staff ID</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Name</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Released Date</th>
                            </tr>
                        </thead>
                        <tbody id="closedTableBody" class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-surface-dark">
                            <?php if (!empty($closed_blacklist)): ?>
                                <?php foreach ($closed_blacklist as $index => $entry): ?>
                                <tr class="hover:bg-primary/5 cursor-pointer transition-colors"
                                    onclick="openModal(<?= $entry['id'] ?>)"
                                    data-name="<?= strtolower(esc($entry['name'])) ?>"
                                    data-ic="<?= strtolower(esc($entry['ic_passport_no'])) ?>"
                                    data-staff="<?= strtolower(esc($entry['staff_id'] ?? '')) ?>"
                                    data-type="<?= esc($entry['type']) ?>"
                                    data-date="<?= esc($entry['blacklist_date']) ?>">
                                    <td class="px-4 py-3.5 text-sm text-slate-500 font-medium closed-row-no"><?= $index + 1 ?></td>
                                    <td class="px-4 py-3.5 text-sm text-slate-600"><?= esc($entry['created_date'] ?? '—') ?></td>
                                    <td class="px-4 py-3.5 text-sm text-slate-600"><?= esc($entry['blacklist_date'] ?? '—') ?></td>
                                    <td class="px-4 py-3.5 text-sm text-slate-600 font-mono"><?= esc($entry['ic_passport_no']) ?></td>
                                    <td class="px-4 py-3.5 text-sm text-slate-600"><?= esc($entry['staff_id'] ?? '—') ?></td>
                                    <td class="px-4 py-3.5 text-sm font-semibold text-slate-900 uppercase"><?= esc($entry['name']) ?></td>
                                    <td class="px-4 py-3.5 text-sm text-slate-600"><?= esc($entry['type']) ?></td>
                                    <td class="px-4 py-3.5 text-sm text-slate-600"><?= esc($entry['released_date'] ?? '—') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="size-16 rounded-full bg-slate-100 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-4xl text-slate-300">history</span>
                                            </div>
                                            <p class="text-sm font-bold text-slate-500">No Closed Records</p>
                                            <p class="text-xs text-slate-400">No individuals have been blacklisted yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                    <span id="closedPaginationInfo" class="text-xs text-slate-500"></span>
                    <div class="flex items-center gap-3">
                        <div id="closedPaginationBtns" class="flex items-center gap-1"></div>
                        <select id="closedPerPageSelect" class="h-9 pl-3 pr-7 text-xs bg-white border border-slate-200 rounded-lg text-slate-600 outline-none appearance-none cursor-pointer">
                            <option value="10">10 ITEMS PER PAGE</option>
                            <option value="25">25 ITEMS PER PAGE</option>
                            <option value="50">50 ITEMS PER PAGE</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white dark:bg-surface-dark rounded-2xl shadow-xl w-full max-w-2xl mx-4 overflow-hidden">

        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wide">Blacklist Individual Info — Blacklist</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[22px]">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-5 space-y-6 overflow-y-auto max-h-[70vh]">

            <!-- Loading -->
            <div id="modalLoading" class="flex items-center justify-center py-12">
                <div class="flex flex-col items-center gap-3">
                    <div class="size-8 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm text-slate-400">Loading...</p>
                </div>
            </div>

            <!-- Content (hidden until loaded) -->
            <div id="modalContent" class="hidden space-y-6">

                <!-- Details -->
                <div>
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 pb-2 border-b border-slate-100">Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1 font-medium">Date of Blacklist</label>
                            <div id="m_blacklist_date" class="h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 flex items-center"></div>
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1 font-medium">Submitted By</label>
                            <div class="h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 flex items-center">Super Admin</div>
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1 font-medium">Reason</label>
                            <div id="m_reason" class="h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 flex items-center"></div>
                        </div>
                    </div>
                </div>

                <!-- Person -->
                <div>
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 pb-2 border-b border-slate-100">Person</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1 font-medium">IC / Passport No</label>
                            <div id="m_ic" class="h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 flex items-center font-mono"></div>
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1 font-medium">Name</label>
                            <div id="m_name" class="h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 flex items-center font-semibold uppercase"></div>
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1 font-medium">Type</label>
                            <div id="m_type" class="h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 flex items-center"></div>
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1 font-medium">Staff ID</label>
                            <div id="m_staff_id" class="h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 flex items-center"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-between px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <button onclick="closeModal()"
                class="px-5 py-2 rounded-lg bg-amber-400 hover:bg-amber-500 text-white text-sm font-semibold transition-colors">
                Back
            </button>
            <form id="releaseForm" method="POST" action="">
                <?= csrf_field() ?>
                <button type="submit"
                    onclick="return confirm('Are you sure you want to release this individual from the blacklist?')"
                    class="px-6 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white text-sm font-semibold transition-colors shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">lock_open</span>
                    Release
                </button>
            </form>
        </div>
    </div>
</div>

<script>
const baseUrl = '<?= base_url() ?>';

function openModal(id) {
    const modal = document.getElementById('detailModal');
    const loading = document.getElementById('modalLoading');
    const content = document.getElementById('modalContent');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    loading.classList.remove('hidden');
    content.classList.add('hidden');

    // Set release form action
    document.getElementById('releaseForm').action = `${baseUrl}blacklist/closedlist/release/${id}`;

    fetch(`${baseUrl}blacklist/closedlist/view/${id}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert('Failed to load record.');
                closeModal();
                return;
            }

            const d = data.data;
            document.getElementById('m_blacklist_date').textContent = d.blacklist_date || '—';
            document.getElementById('m_reason').textContent         = d.reason || '—';
            document.getElementById('m_ic').textContent             = d.ic_passport_no || '—';
            document.getElementById('m_name').textContent           = d.name || '—';
            document.getElementById('m_type').textContent           = d.type || '—';
            document.getElementById('m_staff_id').textContent       = d.staff_id || '—';

            loading.classList.add('hidden');
            content.classList.remove('hidden');
        })
        .catch(() => {
            alert('An error occurred. Please try again.');
            closeModal();
        });
}

function closeModal() {
    const modal = document.getElementById('detailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modal on backdrop click
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Client-side filter + pagination
(function () {
    const tbody   = document.getElementById('closedTableBody');
    const search  = document.getElementById('searchInput');
    const typeSel = document.getElementById('typeFilter');
    const sortSel = document.getElementById('sortFilter');
    const perSel  = document.getElementById('closedPerPageSelect');
    const info    = document.getElementById('closedPaginationInfo');
    const btnCon  = document.getElementById('closedPaginationBtns');

    if (!tbody) return;

    const allRows = Array.from(tbody.querySelectorAll('tr[data-name]'));
    let filtered  = [...allRows];
    let currentPage = 1;
    let perPage = 10;

    function applyFilter() {
        const q    = (search?.value || '').toLowerCase().trim();
        const type = typeSel?.value || '';

        filtered = allRows.filter(row => {
            const matchSearch = !q ||
                row.dataset.name.includes(q) ||
                row.dataset.ic.includes(q) ||
                row.dataset.staff.includes(q);
            const matchType = !type || row.dataset.type === type;
            return matchSearch && matchType;
        });

        currentPage = 1;
        applySort();
    }

    function applySort() {
        const s = sortSel?.value || '';
        filtered.sort((a, b) => {
            switch (s) {
                case 'name_asc':
                    return a.dataset.name.localeCompare(b.dataset.name);
                case 'date_desc':
                    return (b.dataset.date || '').localeCompare(a.dataset.date || '');
                default:
                    return 0;
            }
        });
        render();
    }

    function render() {
        const total = filtered.length;
        const totalPages = Math.max(1, Math.ceil(total / perPage));
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * perPage;
        const end   = start + perPage;

        allRows.forEach(r => (r.style.display = 'none'));
        filtered.forEach((row, i) => {
            const visible = i >= start && i < end;
            row.style.display = visible ? '' : 'none';
            if (visible) {
                const noCell = row.querySelector('.closed-row-no');
                if (noCell) noCell.textContent = String(i + 1);
            }
        });

        renderPagination(total, totalPages, start, end);
    }

    function renderPagination(total, totalPages, start, end) {
        const first = total === 0 ? 0 : start + 1;
        const last  = Math.min(end, total);
        if (info) info.textContent = `Showing ${first}–${last} of ${total}`;

        if (!btnCon) return;
        btnCon.innerHTML = '';

        const makeBtn = (label, disabled, active = false) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.textContent = label;
            b.className = active
                ? 'flex items-center justify-center size-8 rounded border border-primary bg-primary text-white text-xs font-bold'
                : 'flex items-center justify-center size-8 rounded border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs' + (disabled ? ' opacity-50 cursor-not-allowed' : '');
            b.disabled = disabled;
            return b;
        };

        const prev = makeBtn('«', currentPage === 1);
        prev.onclick = () => { currentPage--; render(); };
        btnCon.appendChild(prev);

        pageNumbers(currentPage, totalPages).forEach(p => {
            if (p === '…') {
                const sp = document.createElement('span');
                sp.textContent = '…';
                sp.className = 'flex items-center justify-center size-8 text-xs text-slate-400';
                btnCon.appendChild(sp);
            } else {
                const b = makeBtn(String(p), false, p === currentPage);
                b.onclick = () => { currentPage = p; render(); };
                btnCon.appendChild(b);
            }
        });

        const next = makeBtn('»', currentPage === totalPages);
        next.onclick = () => { currentPage++; render(); };
        btnCon.appendChild(next);
    }

    function pageNumbers(cur, total) {
        if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
        const p = [1];
        if (cur > 3) p.push('…');
        for (let i = Math.max(2, cur - 1); i <= Math.min(total - 1, cur + 1); i++) p.push(i);
        if (cur < total - 2) p.push('…');
        p.push(total);
        return p;
    }

    window.applyClosedListFilters = applyFilter;

    search?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') applyFilter();
    });
    perSel?.addEventListener('change', () => {
        perPage = parseInt(perSel.value, 10) || 10;
        currentPage = 1;
        render();
    });

    render();
})();
</script>
</body>
</html>