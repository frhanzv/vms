<?php helper('list_columns'); $col = static fn(string $k): bool => list_col('staff_closed_list', $k); ?>
<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => 'staffs/closed-list', 'showTabs' => false]) ?>
<?php
$f        = $filters;
$exportQs = http_build_query(array_filter($f + ['search' => $searchTerm, 'sort_by' => $sortBy], fn($v) => $v !== '' && $v !== 'all'));
$input    = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2 text-sm';
$label    = 'block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1';
?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">Staff Pass Closed List</h1>
                <div class="flex gap-2">
                    <?php if ($canExport): ?>
                    <a href="<?= base_url('staffs/closed-list/export') . ($exportQs ? '?' . $exportQs : '') ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-1.5 shadow">
                        <span class="material-symbols-outlined text-[18px]">download</span> Export
                    </a>
                    <?php endif; ?>
                    <?php if ($canRequest ?? false): ?>
                    <a href="<?= base_url('staffs/staffpassrequest') ?>" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-1.5 shadow">
                        <span class="material-icons text-[18px]">add</span> Request
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Filters -->
            <form method="get" action="<?= base_url('staffs/closed-list') ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="lg:col-span-2">
                    <label class="<?= $label ?>">Search</label>
                    <input name="search" value="<?= esc($searchTerm) ?>" class="<?= $input ?>" placeholder="IC / Passport / Full Name / Staff No / Receipt No / App No"/>
                </div>
                <div>
                    <label class="<?= $label ?>">Issue Date From</label>
                    <input name="issue_date_from" value="<?= esc($f['issue_date_from']) ?>" class="flatpickr <?= $input ?>" placeholder="dd/mm/yyyy"/>
                </div>
                <div>
                    <label class="<?= $label ?>">Issue Date To</label>
                    <input name="issue_date_to" value="<?= esc($f['issue_date_to']) ?>" class="flatpickr <?= $input ?>" placeholder="dd/mm/yyyy"/>
                </div>
                <div>
                    <label class="<?= $label ?>">App Date</label>
                    <input name="app_date" value="<?= esc($f['app_date']) ?>" class="flatpickr <?= $input ?>" placeholder="dd/mm/yyyy"/>
                </div>
                <div>
                    <label class="<?= $label ?>">Resident</label>
                    <select name="resident" class="<?= $input ?>">
                        <?php foreach (['all' => 'All', 'LOCAL' => 'Local', 'FOREIGN' => 'Foreign'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= $f['resident'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="<?= $label ?>">Card Status</label>
                    <select name="card_status" class="<?= $input ?>">
                        <?php foreach (['all' => 'All', 'Active' => 'Active', 'Suspended' => 'Suspended', 'Terminated' => 'Terminated'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= $f['card_status'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="<?= $label ?>">Card Expiry Date</label>
                    <input name="card_expiry" value="<?= esc($f['card_expiry']) ?>" class="flatpickr <?= $input ?>" placeholder="dd/mm/yyyy"/>
                </div>
                <div>
                    <label class="<?= $label ?>">Sort By</label>
                    <select name="sort_by" class="<?= $input ?>">
                        <?php foreach ([
                            'date_desc'    => 'Date (Newest First)',
                            'date_asc'     => 'Date (Oldest First)',
                            'name_asc'     => 'Full Name (A-Z)',
                            'name_desc'    => 'Full Name (Z-A)',
                            'dept_asc'     => 'Department (A-Z)',
                            'dept_desc'    => 'Department (Z-A)',
                            'staffno_asc'  => 'Staff No (A-Z)',
                            'staffno_desc' => 'Staff No (Z-A)',
                        ] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= $sortBy === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full h-[38px] bg-primary hover:bg-blue-700 text-white rounded text-sm font-medium">Filter</button>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-6">
                <table class="w-full min-w-max text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold uppercase tracking-wide">
                            <th class="p-3 border-b dark:border-gray-600">No</th>
                            <th class="p-3 border-b dark:border-gray-600">Action</th>
                            <?php if ($col('app_no')): ?><th class="p-3 border-b dark:border-gray-600">App No</th><?php endif; ?>
                            <?php if ($col('receipt_no')): ?><th class="p-3 border-b dark:border-gray-600">Receipt No</th><?php endif; ?>
                            <?php if ($col('app_date')): ?><th class="p-3 border-b dark:border-gray-600">App Date</th><?php endif; ?>
                            <?php if ($col('department')): ?><th class="p-3 border-b dark:border-gray-600">Department</th><?php endif; ?>
                            <th class="p-3 border-b dark:border-gray-600">Full Name</th>
                            <?php if ($col('ic_passport')): ?><th class="p-3 border-b dark:border-gray-600">IC / Passport No</th><?php endif; ?>
                            <?php if ($col('staff_no')): ?><th class="p-3 border-b dark:border-gray-600">Staff No</th><?php endif; ?>
                            <?php if ($col('card_type')): ?><th class="p-3 border-b dark:border-gray-600">Card Type</th><?php endif; ?>
                            <?php if ($col('card_id')): ?><th class="p-3 border-b dark:border-gray-600">Card ID</th><?php endif; ?>
                            <?php if ($col('card_status')): ?><th class="p-3 border-b dark:border-gray-600">Card Status</th><?php endif; ?>
                            <?php if ($col('card_expiry')): ?><th class="p-3 border-b dark:border-gray-600">Card Expiry</th><?php endif; ?>
                            <?php if ($col('collected_by')): ?><th class="p-3 border-b dark:border-gray-600">Collected By</th><?php endif; ?>
                            <?php if ($col('issued_at')): ?><th class="p-3 border-b dark:border-gray-600">Issued At</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 dark:text-gray-300">
                    <?php if (empty($closedList)): ?>
                        <tr><td colspan="<?= list_col_count('staff_closed_list') ?>" class="p-8 text-center text-gray-500">No closed staff pass records found.</td></tr>
                    <?php else: foreach ($closedList as $r): ?>
                        <?php
                        $suspended = $r['status'] === 'Suspended';
                        $cardLabel = $suspended ? 'Suspended' : $r['card_status'];
                        $cardCls   = $suspended ? 'bg-amber-50 text-amber-700' : ($r['card_status'] === 'Active' ? 'bg-emerald-50 text-emerald-700' : ($r['card_status'] === 'Terminated' ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-700'));
                        $showRenew = $canRenew && $r['can_renew'];
                        ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="p-3"><?= $r['no'] ?></td>
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    <?php if ($canCardDetails): ?>
                                    <a href="<?= base_url('staffs/card-info/' . $r['id']) ?>" class="text-primary hover:underline text-xs font-semibold">Card Details</a>
                                    <?php endif; ?>
                                    <?php if ($canCardDetails && $showRenew): ?>
                                    <span class="text-gray-300">|</span>
                                    <?php endif; ?>
                                    <?php if ($showRenew): ?>
                                    <a href="<?= base_url('staffs/renew/' . $r['id']) ?>" class="text-emerald-600 hover:underline text-xs font-semibold">Renew</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <?php if ($col('app_no')): ?><td class="p-3"><?= esc($r['app_no']) ?></td><?php endif; ?>
                            <?php if ($col('receipt_no')): ?><td class="p-3"><?= esc($r['receipt_no']) ?></td><?php endif; ?>
                            <?php if ($col('app_date')): ?><td class="p-3"><?= esc($r['app_date']) ?></td><?php endif; ?>
                            <?php if ($col('department')): ?><td class="p-3"><?= esc($r['department']) ?></td><?php endif; ?>
                            <td class="p-3 font-semibold text-gray-800 dark:text-white">
                                <?= esc($r['full_name']) ?>
                                <?php if (! $r['is_active']): ?><span class="ml-1 text-[10px] font-normal text-gray-400">(inactive)</span><?php endif; ?>
                                <?php if ($r['status'] === 'Pending'): ?><span class="ml-1 text-[10px] font-normal text-amber-500">(renewal pending)</span><?php endif; ?>
                            </td>
                            <?php if ($col('ic_passport')): ?><td class="p-3"><?= esc($r['ic_passport_masked']) ?></td><?php endif; ?>
                            <?php if ($col('staff_no')): ?><td class="p-3"><?= esc($r['staff_no'] !== '' ? $r['staff_no'] : '-') ?></td><?php endif; ?>
                            <?php if ($col('card_type')): ?><td class="p-3"><?= esc($r['card_type']) ?></td><?php endif; ?>
                            <?php if ($col('card_id')): ?><td class="p-3"><?= esc($r['card_id']) ?></td><?php endif; ?>
                            <?php if ($col('card_status')): ?>
                            <td class="p-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $cardCls ?>"><?= esc($cardLabel) ?></span></td>
                            <?php endif; ?>
                            <?php if ($col('card_expiry')): ?><td class="p-3"><?= esc($r['card_expiry']) ?></td><?php endif; ?>
                            <?php if ($col('collected_by')): ?><td class="p-3"><?= esc($r['collector_name']) ?></td><?php endif; ?>
                            <?php if ($col('issued_at')): ?><td class="p-3"><?= esc($r['issued_at']) ?></td><?php endif; ?>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination (same as Vendor Closed List) -->
            <?php $p = $pagination; ?>
            <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                <span>Showing page <?= $p['current_page'] ?> of <?= $p['last_page'] ?> (<?= number_format($p['total']) ?> total)</span>
                <div class="flex gap-1 flex-wrap justify-end">
                    <?php
                    $qs   = $_GET;
                    $cur  = (int) $p['current_page'];
                    $last = (int) $p['last_page'];
                    $prev = 0;
                    for ($i = 1; $i <= $last; $i++):
                        // Same buttons as Vendor Closed List, but only first / last / around the current page.
                        if ($i !== 1 && $i !== $last && abs($i - $cur) > 2) {
                            continue;
                        }
                        if ($prev && $i - $prev > 1): ?>
                    <span class="w-8 h-8 flex items-center justify-center">…</span>
                        <?php endif;
                        $prev = $i;
                        $qs['page'] = $i;
                        $url = base_url('staffs/closed-list') . '?' . http_build_query($qs);
                    ?>
                    <a href="<?= esc($url, 'attr') ?>" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded <?= $i === $cur ? 'bg-primary text-white' : 'hover:bg-gray-50 dark:hover:bg-gray-800' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            </div>

<?= view('staffs/_layout_bottom') ?>
    <script>
        flatpickr('.flatpickr', { dateFormat: 'Y-m-d', allowInput: true });
    </script>
</body>
</html>
