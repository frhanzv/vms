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
                <?php
                $curPage  = $pagination['current_page'] ?? 1;
                $lastPage = $pagination['last_page'] ?? 1;
                $pgTotal  = $pagination['total'] ?? count($closed_blacklist);
                $pgPer    = $pagination['per_page'] ?? 10;
                $buildUrl = function (int $pg, int $pp = 0) use ($searchTerm, $typeFilter, $sortBy, $pgPer): string {
                    $pp = $pp ?: $pgPer;
                    $params = [];
                    if (($searchTerm ?? '') !== '') {
                        $params['search'] = $searchTerm;
                    }
                    if (($typeFilter ?? '') !== '') {
                        $params['type'] = $typeFilter;
                    }
                    if (($sortBy ?? 'date_desc') !== 'date_desc') {
                        $params['sort'] = $sortBy;
                    }
                    if ($pp !== 10) {
                        $params['per_page'] = $pp;
                    }
                    if ($pg > 1) {
                        $params['page'] = $pg;
                    }
                    $qs = http_build_query($params);
                    return base_url('blacklist/closedlist') . ($qs ? '?' . $qs : '');
                };
                ?>
                <form method="get" action="<?= base_url('blacklist/closedlist') ?>" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-6 flex gap-0">
                        <input type="text" id="searchInput" name="search" value="<?= esc($searchTerm ?? '') ?>" placeholder="IC NO / PASSPORT NO / NAME / STAFF ID"
                            class="flex-1 h-10 px-4 text-sm bg-white border border-slate-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-900 placeholder-slate-400 uppercase placeholder:normal-case"/>
                        <button type="submit"
                            class="flex items-center justify-center h-10 w-10 bg-primary hover:bg-primary-dark text-white rounded-r-lg transition-colors flex-shrink-0">
                            <span class="material-symbols-outlined text-[20px]">search</span>
                        </button>
                    </div>
                    <div class="md:col-span-3">
                        <select id="typeFilter" name="type" onchange="this.form.submit()"
                            class="w-full h-10 pl-3 pr-8 text-sm bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-600 appearance-none cursor-pointer">
                            <option value="">TYPE OF BLACKLIST</option>
                            <option value="Staff" <?= ($typeFilter ?? '') === 'Staff' ? 'selected' : '' ?>>Staff</option>
                            <option value="Visitor" <?= ($typeFilter ?? '') === 'Visitor' ? 'selected' : '' ?>>Visitor</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <select id="sortFilter" name="sort" onchange="this.form.submit()"
                            class="w-full h-10 pl-3 pr-8 text-sm bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-600 appearance-none cursor-pointer">
                            <option value="date_desc" <?= ($sortBy ?? 'date_desc') === 'date_desc' ? 'selected' : '' ?>>Blacklist Date (Newest)</option>
                            <option value="name_asc" <?= ($sortBy ?? '') === 'name_asc' ? 'selected' : '' ?>>Name (A-Z)</option>
                        </select>
                    </div>
                </form>

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
                                    onclick="openModal(<?= $entry['id'] ?>)">
                                    <td class="px-4 py-3.5 text-sm text-slate-500 font-medium"><?= ($rowOffset ?? 0) + $index + 1 ?></td>
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
                <?php
                $pgNumbers = [];
                if ($lastPage <= 7) {
                    for ($i = 1; $i <= $lastPage; $i++) {
                        $pgNumbers[] = $i;
                    }
                } else {
                    $pgNumbers[] = 1;
                    if ($curPage > 3) {
                        $pgNumbers[] = '...';
                    }
                    for ($i = max(2, $curPage - 1); $i <= min($lastPage - 1, $curPage + 1); $i++) {
                        $pgNumbers[] = $i;
                    }
                    if ($curPage < $lastPage - 2) {
                        $pgNumbers[] = '...';
                    }
                    $pgNumbers[] = $lastPage;
                }
                $firstItem = ($pgTotal === 0) ? 0 : ($curPage - 1) * $pgPer + 1;
                $lastItem  = min($curPage * $pgPer, $pgTotal);
                ?>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                    <span id="closedPaginationInfo" class="text-xs text-slate-500">Showing <?= number_format($firstItem) ?>–<?= number_format($lastItem) ?> of <?= number_format($pgTotal) ?></span>
                    <div class="flex items-center gap-3">
                        <div id="closedPaginationBtns" class="flex items-center gap-1">
                            <?php if ($curPage > 1): ?>
                            <a href="<?= $buildUrl($curPage - 1) ?>" class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg hover:bg-slate-50 text-xs">«</a>
                            <?php else: ?>
                            <span class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg opacity-40 text-xs">«</span>
                            <?php endif; ?>

                            <?php foreach ($pgNumbers as $pn): ?>
                                <?php if ($pn === '...'): ?>
                                <span class="w-8 h-8 flex items-center justify-center text-xs">…</span>
                                <?php elseif ($pn === $curPage): ?>
                                <span class="w-8 h-8 flex items-center justify-center bg-primary text-white rounded-lg text-xs"><?= $pn ?></span>
                                <?php else: ?>
                                <a href="<?= $buildUrl((int) $pn) ?>" class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg hover:bg-slate-50 text-xs"><?= $pn ?></a>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <?php if ($curPage < $lastPage): ?>
                            <a href="<?= $buildUrl($curPage + 1) ?>" class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg hover:bg-slate-50 text-xs">»</a>
                            <?php else: ?>
                            <span class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg opacity-40 text-xs">»</span>
                            <?php endif; ?>
                        </div>
                        <select id="closedPerPageSelect" class="h-9 pl-3 pr-7 text-xs bg-white border border-slate-200 rounded-lg text-slate-600 outline-none appearance-none cursor-pointer">
                            <?php foreach ([10, 25, 50] as $pp): ?>
                            <option value="<?= $pp ?>" <?= $pgPer === $pp ? 'selected' : '' ?>><?= $pp ?> ITEMS PER PAGE</option>
                            <?php endforeach; ?>
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

document.getElementById('closedPerPageSelect')?.addEventListener('change', function () {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', this.value);
    url.searchParams.delete('page');
    window.location.href = url.toString();
});
</script>
</body>
</html>