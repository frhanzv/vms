<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => 'staffs/issuance-list']) ?>

            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase mb-2">Staff Issuance List</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Printed cards waiting to be collected. Issuing records who collected the card, activates it and moves the pass to the Closed List.</p>

            <form method="get" action="<?= base_url('staffs/issuance-list') ?>" class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
                <div class="flex shadow-sm w-full max-w-lg">
                    <input name="search" value="<?= esc($searchTerm) ?>" placeholder="IC / PASSPORT / FULL NAME / STAFF NO / CARD SERIAL" type="text"
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
                            <th class="p-4 border-b dark:border-gray-600">No</th>
                            <th class="p-4 border-b dark:border-gray-600">Card Serial</th>
                            <th class="p-4 border-b dark:border-gray-600">App No</th>
                            <th class="p-4 border-b dark:border-gray-600">Staff No</th>
                            <th class="p-4 border-b dark:border-gray-600">Full Name</th>
                            <th class="p-4 border-b dark:border-gray-600">Department</th>
                            <th class="p-4 border-b dark:border-gray-600">Card Expiry</th>
                            <th class="p-4 border-b dark:border-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                    <?php if (empty($list)): ?>
                        <tr><td colspan="8" class="p-8 text-center text-gray-500">No cards waiting to be issued.</td></tr>
                    <?php else: foreach ($list as $r): ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="p-4"><?= $r['no'] ?></td>
                            <td class="p-4 font-mono"><?= esc($r['receipt_no']) ?></td>
                            <td class="p-4"><?= esc($r['app_no']) ?></td>
                            <td class="p-4 font-semibold"><?= esc($r['staff_no']) ?></td>
                            <td class="p-4 font-semibold text-gray-800 dark:text-white"><?= esc($r['full_name']) ?></td>
                            <td class="p-4"><?= esc($r['department']) ?></td>
                            <td class="p-4"><?= esc($r['card_expiry']) ?></td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <a href="<?= base_url('staffs/card-info/' . $r['id']) ?>" class="text-primary hover:underline font-semibold">View</a>
                                    <?php if ($canIssue): ?>
                                    <span class="text-gray-300">|</span>
                                    <button type="button" data-id="<?= $r['id'] ?>" data-name="<?= esc($r['full_name'], 'attr') ?>" onclick="openIssue(this)" class="text-emerald-600 hover:underline font-semibold flex items-center gap-1">
                                        <span class="material-icons text-sm">outbox</span>Issue
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <?= view('staffs/_pager', ['pagination' => $pagination, 'baseUrl' => base_url('staffs/issuance-list')]) ?>

<?= view('staffs/_layout_bottom') ?>

    <div id="issueModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-sm w-full p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-1">Issue Card</h3>
            <p class="text-sm text-gray-500 mb-4" id="issueFor"></p>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Collector Name <span class="text-red-500">*</span></label>
            <input id="collectorName" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm mb-3"/>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Collector IC / Passport <span class="text-red-500">*</span></label>
            <input id="collectorIc" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm mb-4"/>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('issueModal').classList.add('hidden')" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-medium">Cancel</button>
                <button type="button" onclick="submitIssue(this)" class="px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Issue &amp; Close</button>
            </div>
        </div>
    </div>
    <script>
        let issueId = null;
        function openIssue(btn) {
            issueId = btn.dataset.id;
            document.getElementById('issueFor').textContent = 'Card for ' + btn.dataset.name;
            // Most staff collect their own card — prefill with the cardholder's name.
            document.getElementById('collectorName').value = btn.dataset.name;
            document.getElementById('collectorIc').value = '';
            document.getElementById('issueModal').classList.remove('hidden');
        }
        function submitIssue(btn) {
            staffPostReload('<?= base_url('staffs/issuance-list/issue/') ?>' + issueId, {
                collector_name: document.getElementById('collectorName').value.trim(),
                collector_ic_passport: document.getElementById('collectorIc').value.trim(),
            }, btn);
        }
    </script>
</body>
</html>
