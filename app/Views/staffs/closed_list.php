<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => 'staffs/closed-list']) ?>
<?php
$f = $filters;
$cardCls = ['Active' => 'bg-emerald-50 text-emerald-700', 'Terminated' => 'bg-red-50 text-red-700', 'Inactive' => 'bg-gray-100 text-gray-600'];
$exportQs = http_build_query(array_filter($f + ['search' => $searchTerm], fn($v) => $v !== '' && $v !== 'all'));
$input = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2 text-xs bg-white';
?>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">Staff Pass Closed List</h1>
                <?php if ($canExport): ?>
                <a href="<?= base_url('staffs/closed-list/export') . ($exportQs ? '?' . $exportQs : '') ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow">
                    <span class="material-icons text-sm mr-1">file_upload</span>Export
                </a>
                <?php endif; ?>
            </div>

            <form method="get" action="<?= base_url('staffs/closed-list') ?>" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6 items-end">
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-semibold text-gray-500 mb-1">Search</label>
                    <input name="search" value="<?= esc($searchTerm) ?>" placeholder="IC / NAME / STAFF NO / CARD SERIAL" class="w-full <?= $input ?>"/>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 mb-1">Issued From</label>
                    <input type="date" name="issue_date_from" value="<?= esc($f['issue_date_from']) ?>" class="w-full <?= $input ?>"/>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 mb-1">Issued To</label>
                    <input type="date" name="issue_date_to" value="<?= esc($f['issue_date_to']) ?>" class="w-full <?= $input ?>"/>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 mb-1">Card Status</label>
                    <select name="card_status" class="w-full <?= $input ?>">
                        <?php foreach (['all' => 'All', 'Active' => 'Active', 'Terminated' => 'Terminated', 'Suspended' => 'Suspended'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= $f['card_status'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 mb-1">Employee</label>
                    <select name="employee" class="w-full <?= $input ?>">
                        <?php foreach (['all' => 'All', 'active' => 'Active', 'inactive' => 'Inactive'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= $f['employee'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 mb-1">Card Expiry</label>
                    <input type="date" name="card_expiry" value="<?= esc($f['card_expiry']) ?>" class="w-full <?= $input ?>"/>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 mb-1">Department</label>
                    <input name="department" value="<?= esc($f['department']) ?>" class="w-full <?= $input ?>"/>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 mb-1">Sort</label>
                    <select name="sort_by" class="w-full <?= $input ?>">
                        <?php foreach (['date_desc' => 'Date (Newest)', 'date_asc' => 'Date (Oldest)', 'staffno_asc' => 'Staff No (A - Z)', 'staffno_desc' => 'Staff No (Z - A)', 'name_asc' => 'Name (A - Z)', 'name_desc' => 'Name (Z - A)'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= $sortBy === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded text-xs font-semibold">Filter</button>
                    <a href="<?= base_url('staffs/closed-list') ?>" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-xs font-semibold">Reset</a>
                </div>
            </form>

            <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-6">
                <table class="w-full min-w-max text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-bold uppercase tracking-wide">
                            <th class="p-4 border-b dark:border-gray-600">No</th>
                            <th class="p-4 border-b dark:border-gray-600">Action</th>
                            <th class="p-4 border-b dark:border-gray-600">Card Serial</th>
                            <th class="p-4 border-b dark:border-gray-600">Staff No</th>
                            <th class="p-4 border-b dark:border-gray-600">Full Name</th>
                            <th class="p-4 border-b dark:border-gray-600">IC / Passport</th>
                            <th class="p-4 border-b dark:border-gray-600">Department</th>
                            <th class="p-4 border-b dark:border-gray-600">Card Status</th>
                            <th class="p-4 border-b dark:border-gray-600">Card Expiry</th>
                            <th class="p-4 border-b dark:border-gray-600">Issued</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                    <?php if (empty($closedList)): ?>
                        <tr><td colspan="10" class="p-8 text-center text-gray-500">No closed staff passes found.</td></tr>
                    <?php else: foreach ($closedList as $r): ?>
                        <?php
                        $label = $r['status'] === 'Suspended' ? 'Suspended' : $r['card_status'];
                        $cls   = $r['status'] === 'Suspended' ? 'bg-amber-50 text-amber-700' : ($cardCls[$r['card_status']] ?? 'bg-gray-100 text-gray-600');
                        ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="p-4"><?= $r['no'] ?></td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <?php if ($canCardDetails): ?>
                                    <a href="<?= base_url('staffs/card-info/' . $r['id']) ?>" class="text-primary hover:text-blue-700" title="Card Details"><span class="material-symbols-outlined text-[20px]">id_card</span></a>
                                    <?php endif; ?>
                                    <?php if ($canRenew && $r['can_renew']): ?>
                                    <a href="<?= base_url('staffs/renew/' . $r['id']) ?>" class="text-emerald-600 hover:text-emerald-800" title="Renew"><span class="material-symbols-outlined text-[20px]">autorenew</span></a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="p-4 font-mono"><?= esc($r['receipt_no']) ?></td>
                            <td class="p-4 font-semibold"><?= esc($r['staff_no']) ?></td>
                            <td class="p-4 font-semibold text-gray-800 dark:text-white">
                                <?= esc($r['full_name']) ?>
                                <?php if (! $r['is_active']): ?><span class="ml-1 text-[10px] text-gray-400">(inactive)</span><?php endif; ?>
                                <?php if ($r['status'] === 'Pending'): ?><span class="ml-1 text-[10px] text-amber-500">(renewal pending)</span><?php endif; ?>
                            </td>
                            <td class="p-4"><?= esc($r['ic_passport_masked']) ?></td>
                            <td class="p-4"><?= esc($r['department']) ?></td>
                            <td class="p-4"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold <?= $cls ?>"><?= esc($label) ?></span></td>
                            <td class="p-4"><?= esc($r['card_expiry']) ?></td>
                            <td class="p-4"><?= esc($r['issued_at']) ?><p class="text-[10px] text-gray-400"><?= esc($r['collector_name']) ?></p></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <?= view('staffs/_pager', ['pagination' => $pagination, 'baseUrl' => base_url('staffs/closed-list'), 'showPerPage' => true]) ?>

<?= view('staffs/_layout_bottom') ?>
</body>
</html>
