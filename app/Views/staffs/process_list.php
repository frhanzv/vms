<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => 'staffs/process-list']) ?>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-2 gap-4">
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">Staff Process List</h1>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Approved staff passes waiting for their card. Open a pass to check details, take the photo, confirm the Staff No. and print — a card can only be printed once both the photo and Staff No. are in.</p>

            <form method="get" action="<?= base_url('staffs/process-list') ?>" class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
                <div class="flex shadow-sm w-full max-w-lg">
                    <input name="search" value="<?= esc($searchTerm) ?>" placeholder="IC / PASSPORT / FULL NAME / STAFF NO / APP NO / DEPARTMENT" type="text"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-xs outline-none focus:ring-primary focus:border-primary"/>
                    <button type="submit" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded-r flex items-center"><span class="material-icons">search</span></button>
                </div>
                <div class="flex gap-3">
                    <select name="missing" onchange="this.form.submit()" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-xs bg-white">
                        <option value="" <?= $missing === '' ? 'selected' : '' ?>>All</option>
                        <option value="photo" <?= $missing === 'photo' ? 'selected' : '' ?>>Photo missing</option>
                        <option value="staff_no" <?= $missing === 'staff_no' ? 'selected' : '' ?>>Staff No missing</option>
                    </select>
                    <select name="sort_by" onchange="this.form.submit()" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-xs bg-white">
                        <?php foreach (['date_desc' => 'Date (Newest)', 'date_asc' => 'Date (Oldest)', 'staffno_asc' => 'Staff No (A - Z)', 'staffno_desc' => 'Staff No (Z - A)', 'name_asc' => 'Name (A - Z)', 'name_desc' => 'Name (Z - A)'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= $sortBy === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-6">
                <table class="w-full min-w-max text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-bold uppercase tracking-wide">
                            <th class="p-4 border-b dark:border-gray-600">No</th>
                            <th class="p-4 border-b dark:border-gray-600">Action</th>
                            <th class="p-4 border-b dark:border-gray-600">Photo</th>
                            <th class="p-4 border-b dark:border-gray-600">App No</th>
                            <th class="p-4 border-b dark:border-gray-600">Type</th>
                            <th class="p-4 border-b dark:border-gray-600">Staff No</th>
                            <th class="p-4 border-b dark:border-gray-600">Full Name</th>
                            <th class="p-4 border-b dark:border-gray-600">IC / Passport</th>
                            <th class="p-4 border-b dark:border-gray-600">Department</th>
                            <th class="p-4 border-b dark:border-gray-600">Ready To Print</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                    <?php if (empty($list)): ?>
                        <tr><td colspan="10" class="p-8 text-center text-gray-500">Nothing waiting to be processed.</td></tr>
                    <?php else: foreach ($list as $r): ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="p-4"><?= $r['no'] ?></td>
                            <td class="p-4">
                                <a href="<?= base_url('staffs/card-info/' . $r['id']) ?>" class="inline-flex items-center gap-1 text-primary hover:text-blue-700 font-semibold">
                                    <span class="material-symbols-outlined text-[20px]">manage_accounts</span>Process
                                </a>
                            </td>
                            <td class="p-4">
                                <?php if ($r['photo_url']): ?>
                                <img src="<?= esc($r['photo_url'], 'attr') ?>" alt="" class="w-10 h-12 object-cover rounded border border-gray-200"/>
                                <?php else: ?>
                                <span class="w-10 h-12 rounded border border-dashed border-gray-300 flex items-center justify-center text-gray-300"><span class="material-symbols-outlined">person</span></span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4"><?= esc($r['app_no']) ?></td>
                            <td class="p-4"><?= esc($r['type']) ?></td>
                            <td class="p-4 font-semibold"><?= $r['staff_no'] !== '' ? esc($r['staff_no']) : '<span class="text-red-500">Missing</span>' ?></td>
                            <td class="p-4 font-semibold text-gray-800 dark:text-white"><?= esc($r['full_name']) ?></td>
                            <td class="p-4"><?= esc($r['ic_passport_masked']) ?></td>
                            <td class="p-4"><?= esc($r['department']) ?></td>
                            <td class="p-4">
                                <?php if ($r['ready']): ?>
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700">Ready</span>
                                <?php else: ?>
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700">Incomplete</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <?= view('staffs/_pager', ['pagination' => $pagination, 'baseUrl' => base_url('staffs/process-list')]) ?>

<?= view('staffs/_layout_bottom') ?>
</body>
</html>
