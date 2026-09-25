<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#137fec",
                        secondary: "#3b82f6",
                        success: "#10b981",
                        "background-light": "#f6f7f8",
                        "background-dark": "#111827",
                        "card-light": "#ffffff",
                        "card-dark": "#1f2937",
                        "nav-active": "#e0efff",
                        "nav-text": "#344767",
                        "nav-icon": "#3b82f6",
                    },
                    fontFamily: {
                        display: ["Montserrat", "sans-serif"],
                        sans: ["Montserrat", "sans-serif"],
                    },
                    borderRadius: { DEFAULT: "0.375rem" },
                },
            },
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden transition-colors duration-200">

    <!-- Sidebar -->
    <?= view('partials/sidebar') ?>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8 bg-background-light dark:bg-background-dark">
        <div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-7xl">

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">
                    Vendor Pass List
                </h1>
                <a href="<?= base_url('vendors/vendorpassrequest') ?>" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                    <span class="material-icons text-sm mr-1">add</span>
                    Request
                </a>
            </div>

            <!-- Stat cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">Total Vendor Passes</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format($stats['total']) ?></p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">Pending</p>
                    <p class="text-2xl font-bold text-amber-500 mt-1"><?= number_format($stats['pending']) ?></p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">Approved</p>
                    <p class="text-2xl font-bold text-emerald-500 mt-1"><?= number_format($stats['approved']) ?></p>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-4 flex items-center gap-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 text-sm rounded-lg px-4 py-3">
                    <span class="material-symbols-outlined text-[20px] flex-shrink-0">check_circle</span>
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 flex items-start gap-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 text-sm rounded-lg px-4 py-3">
                    <span class="material-symbols-outlined text-[20px] flex-shrink-0 mt-0.5">error</span>
                    <span><?= esc(session()->getFlashdata('error')) ?></span>
                </div>
            <?php endif; ?>

            <!-- Filter -->
            <form id="vendorSearchForm" method="get" action="<?= base_url('vendors') ?>" class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
                <div class="flex shadow-sm w-full max-w-lg">
                    <input name="search" value="<?= esc($searchTerm ?? '') ?>"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-xs focus:ring-primary focus:border-primary outline-none"
                        placeholder="IC / PASSPORT / FULL NAME / APP NO / COMPANY" type="text"/>
                    <button type="submit" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-r flex items-center justify-center transition-colors">
                        <span class="material-icons text-white">search</span>
                    </button>
                </div>
                <div class="flex gap-3 w-full md:w-auto">
                    <select name="status" onchange="this.form.submit()"
                        class="w-full md:w-40 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-xs focus:ring-primary focus:border-primary outline-none appearance-none bg-white">
                        <?php foreach (['all' => 'All Status', 'Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected', 'Suspended' => 'Suspended'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= ($status ?? 'all') === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="sort" onchange="this.form.submit()"
                        class="w-full md:w-48 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-xs focus:ring-primary focus:border-primary outline-none appearance-none bg-white">
                        <option value="date_desc" <?= ($sortBy ?? 'date_desc') === 'date_desc' ? 'selected' : '' ?>>Date (Newest)</option>
                        <option value="date_asc" <?= ($sortBy ?? '') === 'date_asc' ? 'selected' : '' ?>>Date (Oldest)</option>
                        <option value="name_asc" <?= ($sortBy ?? '') === 'name_asc' ? 'selected' : '' ?>>Name (A - Z)</option>
                        <option value="name_desc" <?= ($sortBy ?? '') === 'name_desc' ? 'selected' : '' ?>>Name (Z - A)</option>
                    </select>
                </div>
            </form>

            <?php if (! empty($searchTerm)): ?>
            <div class="mb-4 text-xs text-gray-500 dark:text-gray-400">
                Showing results for <strong class="text-gray-800 dark:text-white"><?= esc($searchTerm) ?></strong>
                — <?= number_format($pagination['total'] ?? count($vendorList)) ?> match<?= ($pagination['total'] ?? count($vendorList)) === 1 ? '' : 'es' ?>
                <a href="<?= base_url('vendors') ?>" class="ml-2 text-primary hover:underline">Clear</a>
            </div>
            <?php endif; ?>

            <!-- Table -->
            <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-6">
                <table class="w-full min-w-max text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-bold uppercase tracking-wide">
                            <th class="p-4 border-b dark:border-gray-600">No</th>
                            <th class="p-4 border-b dark:border-gray-600">Action</th>
                            <th class="p-4 border-b dark:border-gray-600">Date</th>
                            <th class="p-4 border-b dark:border-gray-600">App No</th>
                            <th class="p-4 border-b dark:border-gray-600">Full Name</th>
                            <th class="p-4 border-b dark:border-gray-600">IC / Passport No</th>
                            <th class="p-4 border-b dark:border-gray-600">Vendor Company</th>
                            <th class="p-4 border-b dark:border-gray-600">Status</th>
                            <th class="p-4 border-b dark:border-gray-600">Pass Expiry</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                        <?php if (empty($vendorList)): ?>
                        <tr>
                            <td colspan="9" class="p-8 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-4">
                                        <span class="material-symbols-outlined text-4xl text-gray-400 dark:text-gray-500">folder_off</span>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-gray-700 dark:text-gray-300">No Data Available</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">There are no vendor pass records at the moment.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php
                            $badgeClass = [
                                'Pending'   => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                'Approved'  => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                'Rejected'  => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'Suspended' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
                            ];
                            ?>
                            <?php foreach ($vendorList as $vendor): ?>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="p-4"><?= $vendor['no'] ?></td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <button
                                            onclick="event.stopPropagation(); window.location.href='<?= base_url('vendorpassrequest/view/') ?><?= $vendor['id'] ?>'"
                                            class="text-primary hover:text-blue-700 transition-colors"
                                            title="View Details">
                                            <span class="material-symbols-outlined text-[20px]">search</span>
                                        </button>
                                        <?php if ($vendor['status'] === 'Approved'): ?>
                                        <button onclick="event.stopPropagation(); window.location.href='<?= base_url('vendors/qr/') ?><?= $vendor['id'] ?>'" class="text-slate-600 hover:text-slate-900 transition-colors" title="QR Pass">
                                            <span class="material-symbols-outlined text-[20px]">qr_code_2</span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (($canApprove ?? false) && in_array($vendor['status'], ['Pending', 'Rejected'], true)): ?>
                                        <button onclick="event.stopPropagation(); confirmApprove(<?= $vendor['id'] ?>)" class="text-emerald-500 hover:text-emerald-700 transition-colors" title="Approve">
                                            <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (($canReject ?? false) && in_array($vendor['status'], ['Pending', 'Approved'], true)): ?>
                                        <button onclick="event.stopPropagation(); confirmReject(<?= $vendor['id'] ?>)" class="text-red-500 hover:text-red-700 transition-colors" title="Reject">
                                            <span class="material-symbols-outlined text-[20px]">cancel</span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($canEdit ?? false): ?>
                                        <button onclick="event.stopPropagation(); window.location.href='<?= base_url('vendorpassrequest/edit/') ?><?= $vendor['id'] ?>'" class="text-amber-500 hover:text-amber-700 transition-colors" title="Edit">
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($canDelete ?? false): ?>
                                        <button onclick="event.stopPropagation(); confirmDelete(<?= $vendor['id'] ?>)" class="text-red-500 hover:text-red-700 transition-colors" title="Delete">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="p-4"><?= esc($vendor['date']) ?></td>
                                <td class="p-4"><?= esc($vendor['app_no']) ?></td>
                                <td class="p-4 font-semibold text-gray-800 dark:text-white"><?= esc($vendor['full_name']) ?></td>
                                <td class="p-4"><?= esc(mask_ic_passport($vendor['ic_passport'])) ?></td>
                                <td class="p-4"><?= esc($vendor['vendor_company_name']) ?></td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold <?= $badgeClass[$vendor['status']] ?? 'bg-gray-100 text-gray-700' ?>">
                                        <?= esc($vendor['status']) ?>
                                    </span>
                                </td>
                                <td class="p-4"><?= esc($vendor['pass_expiry']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php
            $curPage  = $pagination['current_page'] ?? 1;
            $lastPage = $pagination['last_page'] ?? 1;
            $pgTotal  = $pagination['total'] ?? count($vendorList);
            $pgPer    = $pagination['per_page'] ?? 10;

            $buildUrl = function (int $pg, int $pp = 0) use ($searchTerm, $sortBy, $status, $pgPer): string {
                $pp = $pp ?: $pgPer;
                $params = [];
                if (($searchTerm ?? '') !== '') $params['search'] = $searchTerm;
                if (($status ?? 'all') !== 'all') $params['status'] = $status;
                if (($sortBy ?? 'date_desc') !== 'date_desc') $params['sort'] = $sortBy;
                if ($pp !== 10) $params['per_page'] = $pp;
                if ($pg > 1) $params['page'] = $pg;
                $qs = http_build_query($params);
                return base_url('vendors') . ($qs ? '?' . $qs : '');
            };

            $pgNumbers = [];
            if ($lastPage <= 7) {
                for ($i = 1; $i <= $lastPage; $i++) $pgNumbers[] = $i;
            } else {
                $pgNumbers[] = 1;
                if ($curPage > 3) $pgNumbers[] = '...';
                for ($i = max(2, $curPage - 1); $i <= min($lastPage - 1, $curPage + 1); $i++) $pgNumbers[] = $i;
                if ($curPage < $lastPage - 2) $pgNumbers[] = '...';
                $pgNumbers[] = $lastPage;
            }

            $firstItem = ($pgTotal === 0) ? 0 : ($curPage - 1) * $pgPer + 1;
            $lastItem  = min($curPage * $pgPer, $pgTotal);
            ?>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-medium text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-1">
                    <?php if ($curPage > 1): ?>
                    <a href="<?= $buildUrl($curPage - 1) ?>" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">«</a>
                    <?php else: ?>
                    <span class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded opacity-40 cursor-not-allowed">«</span>
                    <?php endif; ?>

                    <?php foreach ($pgNumbers as $pn): ?>
                        <?php if ($pn === '...'): ?>
                        <span class="w-8 h-8 flex items-center justify-center">...</span>
                        <?php elseif ($pn === $curPage): ?>
                        <span class="w-8 h-8 flex items-center justify-center bg-primary text-white rounded shadow-sm"><?= $pn ?></span>
                        <?php else: ?>
                        <a href="<?= $buildUrl((int) $pn) ?>" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"><?= $pn ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if ($curPage < $lastPage): ?>
                    <a href="<?= $buildUrl($curPage + 1) ?>" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">»</a>
                    <?php else: ?>
                    <span class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded opacity-40 cursor-not-allowed">»</span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-gray-400">Showing <?= number_format($firstItem) ?>–<?= number_format($lastItem) ?> of <?= number_format($pgTotal) ?></span>
                    <div class="relative">
                        <select id="vendorPerPageSelect" class="appearance-none bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 py-1.5 pl-3 pr-8 rounded focus:outline-none focus:ring-1 focus:ring-primary text-xs font-medium cursor-pointer shadow-sm">
                            <?php foreach ([10, 25, 50] as $pp): ?>
                            <option value="<?= $pp ?>" <?= $pgPer === $pp ? 'selected' : '' ?>><?= $pp ?> ITEMS PER PAGE</option>
                            <?php endforeach; ?>
                        </select>
                        <span class="absolute right-2 top-1.5 pointer-events-none material-icons text-sm text-gray-500">expand_more</span>
                    </div>
                </div>
            </div>

        </div>
    </main>
    <script>
        function confirmDelete(id) {
            if (!confirm('Are you sure you want to delete this vendor pass record? This action cannot be undone.')) return;
            fetch('<?= base_url('vendors/delete/') ?>' + id, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
            }).then(r => r.ok ? location.reload() : alert('Delete failed.'));
        }

        function confirmApprove(id) {
            if (!confirm('Approve this vendor pass?')) return;
            fetch('<?= base_url('vendors/approve') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
                body: JSON.stringify({ id: id }),
            })
                .then(r => r.json())
                .then(data => { alert(data.message); if (data.success) location.reload(); });
        }

        function confirmReject(id) {
            const reason = prompt('Reason for rejecting this vendor pass (optional):', '');
            if (reason === null) return; // cancelled
            fetch('<?= base_url('vendors/reject') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
                body: JSON.stringify({ id: id, reason: reason }),
            })
                .then(r => r.json())
                .then(data => { alert(data.message); if (data.success) location.reload(); });
        }

        document.getElementById('vendorPerPageSelect')?.addEventListener('change', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    </script>
</body>
</html>
