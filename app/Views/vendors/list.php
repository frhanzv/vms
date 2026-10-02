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
                <div class="flex flex-wrap gap-2">
                    <?php if ($canImport ?? false): ?>
                    <button onclick="document.getElementById('uploadModal').classList.toggle('hidden')" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">add</span>
                        Import
                    </button>
                    <?php endif; ?>
                    <?php if ($canTemplate ?? false): ?>
                    <button type="button" onclick="document.getElementById('reminderModal').classList.remove('hidden')"
                        class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">file_download</span>
                        Template
                    </button>
                    <?php endif; ?>
                    <?php if ($canExport ?? false): ?>
                    <a href="<?= base_url('vendors/export') ?><?= $searchTerm || ($status ?? 'all') !== 'all' ? '?' . http_build_query(array_filter(['search' => $searchTerm ?? '', 'status' => ($status ?? 'all') !== 'all' ? $status : ''])) : '' ?>"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">file_upload</span>
                        Export
                    </a>
                    <?php endif; ?>
                    <?php if ($canRequest ?? false): ?>
                    <a href="<?= base_url('vendors/vendorpassrequest') ?>" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">add</span>
                        Request
                    </a>
                    <?php endif; ?>
                </div>
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
                <?php $errorLines = explode("\n", (string) session()->getFlashdata('error')); ?>
                <div class="mb-4 flex items-start gap-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 text-sm rounded-lg px-4 py-3">
                    <span class="material-symbols-outlined text-[20px] flex-shrink-0 mt-0.5">error</span>
                    <div>
                        <?php foreach ($errorLines as $line): ?>
                            <p><?= esc($line) ?></p>
                        <?php endforeach; ?>
                    </div>
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
                                        <?php
                                            // Per the supervisor: no QR action here. The QR is a vendor-detail
                                            // lookup (not a pass-verification code) and only makes sense once
                                            // the card has actually been issued — it lives in Closed List now.
                                        ?>
                                        <?php if ($vendor['can_approve'] ?? false): ?>
                                        <button type="button"
                                            data-id="<?= (int) $vendor['id'] ?>" data-name="<?= esc($vendor['full_name'], 'attr') ?>" data-app="<?= esc($vendor['app_no'], 'attr') ?>"
                                            onclick="event.stopPropagation(); openApprove(this)"
                                            class="text-emerald-500 hover:text-emerald-700 transition-colors" title="Approve">
                                            <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($vendor['can_reject'] ?? false): ?>
                                        <button type="button"
                                            data-id="<?= (int) $vendor['id'] ?>" data-name="<?= esc($vendor['full_name'], 'attr') ?>" data-app="<?= esc($vendor['app_no'], 'attr') ?>"
                                            onclick="event.stopPropagation(); openReject(this)"
                                            class="text-red-500 hover:text-red-700 transition-colors" title="Reject">
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
                                    <?php if (!empty($vendor['awaiting'])): ?>
                                    <p class="text-[10px] text-gray-400 mt-1"><?= esc($vendor['awaiting']) ?></p>
                                    <?php endif; ?>
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

    <!-- Import Modal -->
    <?php if ($canImport ?? false): ?>
    <div id="uploadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between p-4 border-b dark:border-slate-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">UPLOAD FILE</h3>
                <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <form action="<?= base_url('vendors/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="p-6">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Choose Excel File</label>
                    <input name="upload_file" type="file" accept=".xlsx, .xls" required
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-slate-700 dark:border-slate-600 dark:placeholder-gray-400">
                    <p class="mt-2 text-xs text-gray-500">Only .xlsx or .xls files allowed. Download the Template first to see the expected columns.</p>
                </div>
                <div class="flex justify-end gap-2 p-4 border-t dark:border-slate-700">
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded text-sm font-medium flex items-center transition-colors">
                        <span class="material-icons text-sm mr-1">publish</span>
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Reminder Modal — shown before the Template file actually downloads,
         same as the real KPK "Online Vendor List" page's Reminder popup. -->
    <?php if ($canTemplate ?? false): ?>
    <div id="reminderModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between p-4 border-b dark:border-slate-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">REMINDER</h3>
                <button onclick="document.getElementById('reminderModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div class="p-6">
                <?php $reminderLocationLabels = implode(', ', (new \App\Models\VendorLocationModel())->getActiveOptions()); ?>
                <ul class="list-disc pl-5 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                    <li>Do not use dashes (-) in IC Number or Phone Number. Use only numbers.</li>
                    <li>Resident must be <strong>Malaysian</strong> or <strong>Non-Malaysian</strong>.</li>
                    <li>Worker Type must be <strong>Permanent</strong> or <strong>Temporary</strong>.</li>
                    <li>For Date of Birth and License Expiry, use the slash ( / ) format only (e.g., 24/06/2025). Do not use dots ( . ).</li>
                    <li>Individual License Class Format (eg,B). Multiple License Class Format (eg,B,C,D).</li>
                    <li>Location Access accepts one or more of: <?= esc($reminderLocationLabels) ?> — separate multiple values with a comma.</li>
                </ul>
            </div>
            <div class="flex justify-end gap-2 p-4 border-t dark:border-slate-700">
                <a href="<?= base_url('files/VendorTemplateNew.xlsx') ?>" download="VendorTemplateNew.xlsx"
                    class="bg-primary hover:bg-indigo-700 text-white px-6 py-2 rounded text-sm font-medium flex items-center transition-colors">
                    <span class="material-icons text-sm mr-1">file_download</span>
                    Download
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Approve Modal -->
    <div id="approveModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-sm w-full p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-1">Approve Vendor Pass</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4"><span id="approveName"></span> — <span id="approveApp"></span></p>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Remark (optional)</label>
            <textarea id="approveRemark" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm mb-4"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-medium">Cancel</button>
                <button type="button" id="approveSubmitBtn" onclick="submitApprove()" class="px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Approve</button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-sm w-full p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-1">Reject Vendor Pass</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4"><span id="rejectName"></span> — <span id="rejectApp"></span></p>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Reason <span class="text-red-500">*</span></label>
            <select id="rejectReasonId" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm mb-3">
                <option value="">-- Select a reason --</option>
                <?php foreach (($rejectReasons ?? []) as $r): ?>
                <option value="<?= (int) $r['id'] ?>"><?= esc($r['reason']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (empty($rejectReasons)): ?>
            <p class="text-xs text-amber-600 dark:text-amber-400 mb-3">No active reject reasons are configured yet — add some under Config, or a remark alone won't be accepted.</p>
            <?php endif; ?>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Remark (optional)</label>
            <textarea id="rejectRemark" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm mb-4"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-medium">Cancel</button>
                <button type="button" id="rejectSubmitBtn" onclick="submitReject()" class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white text-sm font-semibold">Reject</button>
            </div>
        </div>
    </div>
    <script>
        function confirmDelete(id) {
            if (!confirm('Are you sure you want to delete this vendor pass record? This action cannot be undone.')) return;
            fetch('<?= base_url('vendors/delete/') ?>' + id, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
            })
                .then(r => r.ok ? location.reload() : alert('Delete failed. Please try again.'))
                .catch(() => alert('Could not reach the server. Please check your connection and try again.'));
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        let activeApproveId = null;
        let activeRejectId  = null;

        function openApprove(btn) {
            activeApproveId = btn.dataset.id;
            document.getElementById('approveName').textContent = btn.dataset.name;
            document.getElementById('approveApp').textContent = btn.dataset.app;
            document.getElementById('approveRemark').value = '';
            document.getElementById('approveModal').classList.remove('hidden');
        }

        function openReject(btn) {
            activeRejectId = btn.dataset.id;
            document.getElementById('rejectName').textContent = btn.dataset.name;
            document.getElementById('rejectApp').textContent = btn.dataset.app;
            document.getElementById('rejectReasonId').value = '';
            document.getElementById('rejectRemark').value = '';
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function postAction(url, payload, submitBtnId) {
            const btn = document.getElementById(submitBtnId);
            btn.disabled = true;
            const originalText = btn.textContent;
            btn.textContent = 'Please wait…';

            return fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
                body: JSON.stringify(payload),
            })
                .then(r => {
                    if (!r.ok) throw new Error('http_' + r.status);
                    return r.json();
                })
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload();
                    } else {
                        btn.disabled = false;
                        btn.textContent = originalText;
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.textContent = originalText;
                    if (String(err.message).startsWith('http_')) {
                        alert('Something went wrong on the server (' + err.message.replace('http_', '') + '). If this keeps happening, check that all migrations have been run.');
                    } else {
                        alert('Could not reach the server. Please check your connection and try again.');
                    }
                });
        }

        function submitApprove() {
            postAction('<?= base_url('vendors/approve') ?>', {
                id: activeApproveId,
                remark: document.getElementById('approveRemark').value.trim(),
            }, 'approveSubmitBtn');
        }

        function submitReject() {
            const reasonId = document.getElementById('rejectReasonId').value;
            if (!reasonId) {
                alert('Please select a reason for rejecting this vendor pass.');
                return;
            }
            postAction('<?= base_url('vendors/reject') ?>', {
                id: activeRejectId,
                reject_reason_id: reasonId,
                remark: document.getElementById('rejectRemark').value.trim(),
            }, 'rejectSubmitBtn');
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
