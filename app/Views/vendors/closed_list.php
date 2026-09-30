<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: { extend: {
                colors: { primary: "#137fec", "background-light": "#f6f7f8", "background-dark": "#111827", "card-light": "#ffffff", "card-dark": "#1f2937" },
                fontFamily: { sans: ["Montserrat", "sans-serif"] },
            } },
        };
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden">

    <?= view('partials/sidebar') ?>

    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8">
        <div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-7xl">

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">Vendor Pass Closed List</h1>
                <div class="flex gap-2">
                    <button id="exportBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-1.5 shadow">
                        <span class="material-symbols-outlined text-[18px]">download</span> Export
                    </button>
                    <a href="<?= base_url('vendors/vendorpassrequest') ?>" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-1.5 shadow">
                        <span class="material-icons text-[18px]">add</span> Request
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <form id="filterForm" method="get" action="<?= base_url('vendors/closed-list') ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Search</label>
                    <input name="search" value="<?= esc($searchTerm ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2 text-sm" placeholder="IC / Passport / Company / Full Name / Receipt No / App No"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Issue Date From</label>
                    <input id="issueDateFrom" name="issue_date_from" value="<?= esc($issueDateFrom ?? '') ?>" class="flatpickr w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2 text-sm" placeholder="dd/mm/yyyy"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Issue Date To</label>
                    <input id="issueDateTo" name="issue_date_to" value="<?= esc($issueDateTo ?? '') ?>" class="flatpickr w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2 text-sm" placeholder="dd/mm/yyyy"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">App Date</label>
                    <input id="appDate" name="app_date" value="<?= esc($appDate ?? '') ?>" class="flatpickr w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2 text-sm" placeholder="dd/mm/yyyy"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Resident</label>
                    <select name="resident" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2 text-sm">
                        <?php foreach (['all' => 'All', 'Malaysian' => 'Malaysian', 'Non-Malaysian' => 'Non-Malaysian'] as $val => $lbl): ?>
                        <option value="<?= $val ?>" <?= ($resident ?? 'all') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Card Status</label>
                    <select name="card_status" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2 text-sm">
                        <?php foreach (['all' => 'All', 'Active' => 'Active', 'Inactive' => 'Inactive', 'Terminated' => 'Terminated'] as $val => $lbl): ?>
                        <option value="<?= $val ?>" <?= ($cardStatus ?? 'all') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Card Expiry Date</label>
                    <input id="cardExpiry" name="card_expiry" value="<?= esc($cardExpiry ?? '') ?>" class="flatpickr w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2 text-sm" placeholder="dd/mm/yyyy"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Sort By</label>
                    <select name="sort_by" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2 text-sm">
                        <?php foreach ([
                            'date_desc'    => 'Date (Newest First)',
                            'date_asc'     => 'Date (Oldest First)',
                            'name_asc'     => 'Full Name (A-Z)',
                            'name_desc'    => 'Full Name (Z-A)',
                            'company_asc'  => 'Company (A-Z)',
                            'company_desc' => 'Company (Z-A)',
                        ] as $val => $lbl): ?>
                        <option value="<?= $val ?>" <?= ($sortBy ?? 'date_desc') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full h-[38px] bg-primary hover:bg-blue-700 text-white rounded text-sm font-medium">Filter</button>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-6">
                <table id="closedListTable" class="w-full min-w-max text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold uppercase tracking-wide">
                            <th class="p-3 border-b dark:border-gray-600">No</th>
                            <th class="p-3 border-b dark:border-gray-600">Action</th>
                            <th class="p-3 border-b dark:border-gray-600">App No</th>
                            <th class="p-3 border-b dark:border-gray-600">Receipt No</th>
                            <th class="p-3 border-b dark:border-gray-600">App Date</th>
                            <th class="p-3 border-b dark:border-gray-600">Vendor Company</th>
                            <th class="p-3 border-b dark:border-gray-600">Full Name</th>
                            <th class="p-3 border-b dark:border-gray-600">IC / Passport No</th>
                            <th class="p-3 border-b dark:border-gray-600">Vehicle Registration</th>
                            <th class="p-3 border-b dark:border-gray-600">Card Type</th>
                            <th class="p-3 border-b dark:border-gray-600">Card ID</th>
                            <th class="p-3 border-b dark:border-gray-600">Card Status</th>
                            <th class="p-3 border-b dark:border-gray-600">Card Expiry</th>
                            <th class="p-3 border-b dark:border-gray-600">Collected By</th>
                            <th class="p-3 border-b dark:border-gray-600">Issued At</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 dark:text-gray-300">
                        <?php if (empty($closedList)): ?>
                        <tr><td colspan="15" class="p-8 text-center text-gray-500">No closed vendor pass records found.</td></tr>
                        <?php else: foreach ($closedList as $row): ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="p-3"><?= $row['no'] ?></td>
                            <td class="p-3">
                                <a href="<?= base_url('vendors/card-info/view/' . $row['id']) ?>" class="text-primary hover:underline text-xs font-semibold">Card Details</a>
                            </td>
                            <td class="p-3"><?= esc($row['app_no']) ?></td>
                            <td class="p-3"><?= esc($row['receipt_no']) ?></td>
                            <td class="p-3"><?= esc($row['app_date']) ?></td>
                            <td class="p-3"><?= esc($row['vendor_company_name']) ?></td>
                            <td class="p-3 font-semibold text-gray-800 dark:text-white"><?= esc($row['full_name']) ?></td>
                            <td class="p-3"><?= esc($row['ic_passport_masked']) ?></td>
                            <td class="p-3"><?= esc($row['vehicle_registration']) ?></td>
                            <td class="p-3"><?= esc($row['card_type']) ?></td>
                            <td class="p-3"><?= esc($row['card_id']) ?></td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $row['card_status'] === 'Active' ? 'bg-emerald-50 text-emerald-700' : ($row['card_status'] === 'Terminated' ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-700') ?>"><?= esc($row['card_status']) ?></span>
                            </td>
                            <td class="p-3"><?= esc($row['card_expiry']) ?></td>
                            <td class="p-3"><?= esc($row['collector_name']) ?></td>
                            <td class="p-3"><?= esc($row['issued_at']) ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php $p = $pagination; ?>
            <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                <span>Showing page <?= $p['current_page'] ?> of <?= $p['last_page'] ?> (<?= number_format($p['total']) ?> total)</span>
                <div class="flex gap-1">
                    <?php
                    $qs = $_GET;
                    for ($i = 1; $i <= $p['last_page']; $i++):
                        $qs['page'] = $i;
                        $url = base_url('vendors/closed-list') . '?' . http_build_query($qs);
                    ?>
                    <a href="<?= $url ?>" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded <?= $i === $p['current_page'] ? 'bg-primary text-white' : 'hover:bg-gray-50 dark:hover:bg-gray-800' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        flatpickr('.flatpickr', { dateFormat: 'Y-m-d', allowInput: true });

        document.getElementById('exportBtn').addEventListener('click', function () {
            const table = document.getElementById('closedListTable');
            const wb = XLSX.utils.table_to_book(table, { sheet: 'Vendor Closed List' });
            XLSX.writeFile(wb, 'vendor_closed_list.xlsx');
        });
    </script>
</body>
</html>
