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
                <div class="no-print flex flex-wrap gap-2">
                    <button type="button" onclick="document.getElementById('staffColumnsModal').classList.remove('hidden')"
                        class="bg-[#535dec] hover:bg-[#4853e0] text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">visibility</span>
                        Show/Hide Columns
                    </button>
                    <?php if ($canImport ?? false): ?>
                    <button type="button" onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
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
                    <?php if ($canExport): ?>
                    <a href="<?= base_url('staffs/closed-list/export') . ($exportQs ? '?' . $exportQs : '') ?>"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">file_upload</span>
                        Export
                    </a>
                    <?php endif; ?>
                    <?php if ($canRequest ?? false): ?>
                    <a href="<?= base_url('staffs/staffpassrequest') ?>" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">add</span>
                        Request
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

    <?php if ($canImport ?? false): ?>
    <div id="uploadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between p-4 border-b dark:border-slate-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">UPLOAD FILE</h3>
                <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><span class="material-icons">close</span></button>
            </div>
            <form action="<?= base_url('staff-pass/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="p-6">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Choose Excel File</label>
                    <input name="upload_file" type="file" accept=".xlsx, .xls" required
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-slate-700 dark:border-slate-600"/>
                    <p class="mt-2 text-xs text-gray-500">Only .xlsx or .xls files allowed. Download the Template first to see the expected columns. Imported staff are treated as HR-approved (as in KPK) and go straight to the Process List; a "Status" column of Active / Inactive sets the employee status.</p>
                </div>
                <div class="flex justify-end gap-2 p-4 border-t dark:border-slate-700">
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded text-sm font-medium flex items-center transition-colors"><span class="material-icons text-sm mr-1">publish</span>Import</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Reminder Modal — shown before the Template downloads, same as the Vendor List. -->
    <?php if ($canTemplate ?? false): ?>
    <div id="reminderModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between p-4 border-b dark:border-slate-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">REMINDER</h3>
                <button onclick="document.getElementById('reminderModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><span class="material-icons">close</span></button>
            </div>
            <div class="p-6">
                <ul class="list-disc pl-5 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                    <li>Do not use dashes (-) in IC Number or Phone Number. Use only numbers.</li>
                    <li>For Date of Birth and License Expiry, use the slash ( / ) format only (e.g., 24/06/2025). Do not use dots ( . )</li>
                    <li>Location Access is mandatory and must match a location you can use.</li>
                    <li>Each IC / Passport may appear only once, and not for a staff pass that already exists.</li>
                    <li>Status column: Active or Inactive (blank = Active).</li>
                </ul>
            </div>
            <div class="flex justify-end gap-2 p-4 border-t dark:border-slate-700">
                <a href="<?= base_url('files/StaffTemplateNew.xlsx') ?>" download="StaffTemplateNew.xlsx" onclick="document.getElementById('reminderModal').classList.add('hidden')"
                    class="bg-primary hover:bg-indigo-700 text-white px-6 py-2 rounded text-sm font-medium flex items-center transition-colors">
                    <span class="material-icons text-sm mr-1">file_download</span>
                    Download
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Show/Hide Columns (same settings as Config > Page Settings) -->
    <div id="staffColumnsModal" class="hidden fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div onclick="closeModal('staffColumnsModal')" class="absolute inset-0 bg-slate-900/55 dark:bg-black/65 cursor-pointer"></div>
        <form method="POST" action="<?= base_url('staffs/saveColumnSettings') ?>" class="relative flex w-full max-w-[600px] flex-col rounded-xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
            <?= csrf_field() ?>
            <input type="hidden" name="list" value="staff_closed_list"/>
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold tracking-tight text-[#3b5998] dark:text-white">Show/Hide Columns</h2>
                <button type="button" onclick="closeModal('staffColumnsModal')" class="text-slate-300 hover:text-slate-500"><span class="material-icons text-[20px]">close</span></button>
            </div>
            <div class="px-6 py-5 overflow-y-auto max-h-[60vh]">
                <div class="mb-5 flex items-center gap-2">
                    <input type="checkbox" id="selectAllStaffColumns" onchange="document.querySelectorAll('.staff-column-toggle:not(:disabled)').forEach(c => c.checked = this.checked)" class="rounded border-slate-300 text-[#535dec] focus:ring-[#535dec] h-4 w-4 cursor-pointer">
                    <label for="selectAllStaffColumns" class="text-sm font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Select All Columns</label>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-4">
                    <?php foreach ((list_column_registry()['staff_closed_list']['columns'] ?? []) as $ckey => $cdef): $locked = ! empty($cdef['locked']); ?>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="staff_col_<?= esc($ckey) ?>" <?= $locked ? 'disabled' : 'name="columns[' . esc($ckey) . ']" value="1"' ?>
                               class="staff-column-toggle rounded border-slate-300 text-[#535dec] focus:ring-[#535dec] h-4 w-4 <?= $locked ? 'opacity-60' : 'cursor-pointer' ?>"
                               <?= $col($ckey) ? 'checked' : '' ?>>
                        <label for="staff_col_<?= esc($ckey) ?>" class="text-sm text-slate-600 dark:text-slate-300 uppercase <?= $locked ? '' : 'cursor-pointer' ?>">
                            <?= esc($cdef['label']) ?><?= $locked ? ' <span class="normal-case text-xs text-slate-400">(always shown)</span>' : '' ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t-2 border-slate-100 bg-white px-6 py-4 dark:border-slate-700 dark:bg-slate-900 rounded-b-xl">
                <button type="button" onclick="closeModal('staffColumnsModal')" class="rounded-md border border-slate-400 bg-slate-500 hover:bg-slate-600 px-5 py-2 text-sm font-semibold text-white transition-colors">Close</button>
                <button type="submit" class="rounded-md bg-[#535dec] hover:bg-[#4853e0] px-5 py-2 text-sm font-semibold text-white shadow-md transition-colors">Apply Changes</button>
            </div>
        </form>
    </div>

    <script>
        flatpickr('.flatpickr', { dateFormat: 'Y-m-d', allowInput: true });
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    </script>
</body>
</html>
