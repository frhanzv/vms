<?php helper('list_columns'); $col = static fn(string $k): bool => list_col('staff_process_list', $k); ?>
<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => 'staffs/process-list', 'showTabs' => false]) ?>
<style>[x-cloak] { display: none !important; }</style>
<?php
$field  = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm';
// Dropdowns keep room on the right for the arrow (forms plugin draws it at the right edge).
$select = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded pl-3 pr-9 py-2 text-sm bg-white';
?>

        <div x-data="{ view: (function () { try { return localStorage.getItem('staffProcessListView') || 'card'; } catch (e) { return 'card'; } })() }"
             x-init="$watch('view', v => { try { localStorage.setItem('staffProcessListView', v); } catch (e) {} })">

            <h1 class="text-xl font-bold uppercase mb-2 text-gray-800 dark:text-white">Staff Process List</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Approved staff passes whose card has not been issued yet. Open a pass (View Details) to check the details, take the photo and confirm the Staff No. — a card can only be printed once both are in. After printing, press Issue there when the card is collected: the card becomes Active and the pass moves to the Closed List.</p>

            <div class="flex items-center gap-6 mb-4">
                <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                    <input type="radio" name="viewmode" value="card" x-model="view"/> Card View
                </label>
                <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                    <input type="radio" name="viewmode" value="table" x-model="view"/> Table View
                </label>
            </div>

            <form method="get" action="<?= base_url('staffs/process-list') ?>" class="mb-4 flex flex-wrap gap-2 items-center">
                <input name="search" value="<?= esc($searchTerm) ?>" class="<?= $field ?> w-80 max-w-full" placeholder="IC / Passport / Full Name / Staff No / App No / Department"/>
                <select name="card_type" class="<?= $select ?>" onchange="this.form.submit()">
                    <?php foreach (['' => 'All Worker Types', 'CONTRACT' => 'Contract', 'PERMANENT' => 'Permanent', 'FAMILY PASS' => 'Family Pass'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $cardType === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="missing" class="<?= $select ?>" onchange="this.form.submit()">
                    <?php foreach (['' => 'Ready & Incomplete', 'photo' => 'Photo missing', 'staff_no' => 'Staff No missing'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $missing === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="sort_by" class="<?= $select ?>" onchange="this.form.submit()">
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
                <button type="submit" class="h-9 px-4 rounded-lg bg-primary hover:bg-blue-700 text-white text-sm font-semibold">Search</button>
            </form>

            <!-- Card View -->
            <div x-show="view === 'card'" x-cloak>
                <?php if (empty($list)): ?>
                <p class="p-6 text-center text-gray-500 text-sm">No approved staff passes waiting to be printed.</p>
                <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($list as $r): ?>
                    <a href="<?= base_url('staffs/card-info/' . $r['id']) ?>" class="flex items-center justify-between gap-4 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-primary hover:shadow-sm transition">
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-gray-800 dark:text-white"><?= esc($r['app_no']) ?></div>
                            <div class="text-xs text-gray-600 dark:text-gray-300 mt-1"><?= esc($r['department']) ?></div>
                            <div class="text-xs text-gray-600 dark:text-gray-300"><?= esc($r['full_name']) ?><?= $r['staff_no'] !== '' ? ' · ' . esc($r['staff_no']) : '' ?></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400"><?= esc($r['ic_passport_masked']) ?></div>
                            <?php if ($r['printed']): ?>
                            <div class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 mt-1">Printed · <?= esc($r['printed']) ?> — ready to issue</div>
                            <?php elseif (! $r['ready']): ?>
                            <div class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 mt-1">
                                <?= esc(implode(' & ', array_filter([$r['photo_url'] === null ? 'Photo' : null, $r['staff_no'] === '' ? 'Staff No' : null]))) ?> missing
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($r['photo_url']): ?>
                        <img src="<?= esc($r['photo_url'], 'attr') ?>" alt="" class="w-12 h-12 rounded-full object-cover border border-gray-200 dark:border-gray-700 flex-shrink-0"/>
                        <?php else: ?>
                        <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-gray-400">photo_camera</span>
                        </div>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Table View -->
            <div x-show="view === 'table'" x-cloak class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase">
                            <th class="p-3 border-b dark:border-gray-600">No</th>
                            <?php if ($col('app_no')): ?><th class="p-3 border-b dark:border-gray-600">App No</th><?php endif; ?>
                            <th class="p-3 border-b dark:border-gray-600">Full Name</th>
                            <?php if ($col('department')): ?><th class="p-3 border-b dark:border-gray-600">Department</th><?php endif; ?>
                            <?php if ($col('ic_passport')): ?><th class="p-3 border-b dark:border-gray-600">IC/Passport</th><?php endif; ?>
                            <?php if ($col('staff_no')): ?><th class="p-3 border-b dark:border-gray-600">Staff No</th><?php endif; ?>
                            <?php if ($col('card_type')): ?><th class="p-3 border-b dark:border-gray-600">Worker Type</th><?php endif; ?>
                            <?php if ($col('card_expiry')): ?><th class="p-3 border-b dark:border-gray-600">Card Expiry</th><?php endif; ?>
                            <?php if ($col('ready')): ?><th class="p-3 border-b dark:border-gray-600">Ready To Print</th><?php endif; ?>
                            <th class="p-3 border-b dark:border-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($list)): ?>
                        <tr><td colspan="<?= list_col_count('staff_process_list') ?>" class="p-6 text-center text-gray-500">No approved staff passes waiting to be printed.</td></tr>
                        <?php else: foreach ($list as $r): ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="p-3"><?= $r['no'] ?></td>
                            <?php if ($col('app_no')): ?><td class="p-3"><?= esc($r['app_no']) ?></td><?php endif; ?>
                            <td class="p-3 font-semibold"><?= esc($r['full_name']) ?></td>
                            <?php if ($col('department')): ?><td class="p-3"><?= esc($r['department']) ?></td><?php endif; ?>
                            <?php if ($col('ic_passport')): ?><td class="p-3"><?= esc($r['ic_passport_masked']) ?></td><?php endif; ?>
                            <?php if ($col('staff_no')): ?><td class="p-3"><?= $r['staff_no'] !== '' ? esc($r['staff_no']) : '<span class="text-red-500">Missing</span>' ?></td><?php endif; ?>
                            <?php if ($col('card_type')): ?><td class="p-3"><?= esc($r['card_type']) ?></td><?php endif; ?>
                            <?php if ($col('card_expiry')): ?><td class="p-3"><?= esc($r['card_expiry']) ?></td><?php endif; ?>
                            <?php if ($col('ready')): ?>
                            <td class="p-3">
                                <?php if ($r['printed']): ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700" title="Card <?= esc($r['printed'], 'attr') ?>">Printed — ready to issue</span>
                                <?php elseif ($r['ready']): ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">Ready</span>
                                <?php else: ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700">Incomplete</span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                <a href="<?= base_url('staffs/card-info/' . $r['id']) ?>" class="text-primary hover:underline text-xs font-semibold">View Details</a>
                                <?php if ($canIssue && $r['printed']): ?>
                                    <span class="text-gray-300">|</span>
                                    <button onclick="staffIssueOpen(this)" data-id="<?= $r['id'] ?>" data-name="<?= esc($r['full_name'], 'attr') ?>" class="text-indigo-600 hover:underline text-xs font-semibold">Issue Card</button>
                                <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                <?= view('staffs/_pager', ['pagination' => $pagination, 'baseUrl' => base_url('staffs/process-list'), 'showPerPage' => true]) ?>
            </div>
        </div>

<?= view('staffs/_layout_bottom') ?>
<?= view('staffs/_issue_modal') ?>
</body>
</html>
