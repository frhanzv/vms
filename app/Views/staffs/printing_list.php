<?php helper('list_columns'); $col = static fn(string $k): bool => list_col('staff_printing_list', $k); ?>
<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => 'staffs/printing-list']) ?>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-2 gap-4">
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">Staff Printing List</h1>
                <?php if ($canPrint): ?>
                <button id="printSelectedBtn" onclick="printSelected()" disabled class="bg-primary hover:bg-blue-700 disabled:opacity-40 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow">
                    <span class="material-icons text-sm mr-1">print</span>Print Selected (<span id="selectedCount">0</span>)
                </button>
                <?php endif; ?>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Same passes as the Process List. Printing a card gives it a serial number; the pass stays here until the card is issued — open View Details and press Issue when it is collected, which moves it to the Closed List. Rows without a photo or Staff No. can't be printed yet.</p>

            <form method="get" action="<?= base_url('staffs/printing-list') ?>" class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
                <div class="flex shadow-sm w-full max-w-lg">
                    <input name="search" value="<?= esc($searchTerm) ?>" placeholder="IC / PASSPORT / FULL NAME / STAFF NO / APP NO" type="text"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-xs outline-none"/>
                    <button type="submit" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded-r flex items-center"><span class="material-icons">search</span></button>
                </div>
                <select name="sort_by" onchange="this.form.submit()" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-xs bg-white">
                    <?php foreach (['date_desc' => 'Date (Newest)', 'date_asc' => 'Date (Oldest)', 'staffno_asc' => 'Staff No (A - Z)', 'staffno_desc' => 'Staff No (Z - A)', 'name_asc' => 'Name (A - Z)', 'name_desc' => 'Name (Z - A)'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $sortBy === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </form>

            <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-6">
                <table class="w-full min-w-max text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-bold uppercase tracking-wide">
                            <th class="p-4 border-b dark:border-gray-600"><?php if ($canPrint): ?><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this.checked)" class="rounded"/><?php endif; ?></th>
                            <th class="p-4 border-b dark:border-gray-600">No</th>
                            <?php if ($col('photo')): ?><th class="p-4 border-b dark:border-gray-600">Photo</th><?php endif; ?>
                            <?php if ($col('staff_no')): ?><th class="p-4 border-b dark:border-gray-600">Staff No</th><?php endif; ?>
                            <th class="p-4 border-b dark:border-gray-600">Full Name</th>
                            <?php if ($col('ic_passport')): ?><th class="p-4 border-b dark:border-gray-600">IC / Passport</th><?php endif; ?>
                            <?php if ($col('department')): ?><th class="p-4 border-b dark:border-gray-600">Department</th><?php endif; ?>
                            <th class="p-4 border-b dark:border-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                    <?php if (empty($list)): ?>
                        <tr><td colspan="<?= list_col_count('staff_printing_list') ?>" class="p-8 text-center text-gray-500">No cards waiting to be printed.</td></tr>
                    <?php else: foreach ($list as $r): ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="p-4"><?php if ($canPrint && $r['ready']): ?><input type="checkbox" class="row-check rounded" value="<?= $r['id'] ?>" onchange="onRowCheck()"/><?php endif; ?></td>
                            <td class="p-4"><?= $r['no'] ?></td>
                            <?php if ($col('photo')): ?>
                            <td class="p-4">
                                <?php if ($r['photo_url']): ?>
                                <img src="<?= esc($r['photo_url'], 'attr') ?>" alt="" class="w-8 h-10 object-cover rounded border border-gray-200"/>
                                <?php else: ?><span class="text-[10px] text-gray-400">N/A</span><?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <?php if ($col('staff_no')): ?><td class="p-4 font-semibold"><?= $r['staff_no'] !== '' ? esc($r['staff_no']) : '<span class="text-red-500">Missing</span>' ?></td><?php endif; ?>
                            <td class="p-4 font-semibold text-gray-800 dark:text-white"><?= esc($r['full_name']) ?></td>
                            <?php if ($col('ic_passport')): ?><td class="p-4"><?= esc($r['ic_passport_masked']) ?></td><?php endif; ?>
                            <?php if ($col('department')): ?><td class="p-4"><?= esc($r['department']) ?></td><?php endif; ?>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <a href="<?= base_url('staffs/card-info/' . $r['id'] . '?from=printing') ?>" class="text-primary hover:underline font-semibold">View Details</a>
                                    <?php if ($r['printed']): ?>
                                        <span class="text-gray-300">|</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700" title="Card <?= esc($r['printed'], 'attr') ?>">Printed — ready to issue</span>
                                    <?php endif; ?>
                                    <?php if ($canPrint): ?>
                                        <span class="text-gray-300">|</span>
                                        <?php if ($r['ready']): ?>
                                        <button onclick="staffPrintOne(<?= $r['id'] ?>)" class="text-primary hover:underline font-semibold flex items-center gap-1"><span class="material-icons text-sm">print</span><?= $r['printed'] ? 'Reprint' : 'Print' ?></button>
                                        <?php else: ?>
                                        <span class="text-amber-600"><?= esc($r['blocker']) ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
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

            <?= view('staffs/_pager', ['pagination' => $pagination, 'baseUrl' => base_url('staffs/printing-list'), 'showPerPage' => true]) ?>

<?= view('staffs/_layout_bottom') ?>
<?= view('staffs/_issue_modal') ?>
<?= view('staffs/_card_print') ?>
    <script>
        function onRowCheck() {
            const boxes = document.querySelectorAll('.row-check');
            const checked = document.querySelectorAll('.row-check:checked');
            const all = document.getElementById('selectAll');
            if (all) all.checked = boxes.length > 0 && checked.length === boxes.length;
            document.getElementById('selectedCount').textContent = checked.length;
            document.getElementById('printSelectedBtn').disabled = checked.length === 0;
        }
        function toggleSelectAll(checked) {
            document.querySelectorAll('.row-check').forEach(cb => cb.checked = checked);
            onRowCheck();
        }
        function printSelected() {
            const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.value);
            if (ids.length) staffPrintMany(ids);
        }
    </script>
</body>
</html>
