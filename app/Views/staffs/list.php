<?php
helper('list_columns');
$requestMode = $requestMode ?? false;
$listKey     = $requestMode ? 'staff_request_list' : 'staff_pass_list';
$col         = static fn(string $k): bool => list_col($listKey, $k);
$isInactive  = ($tab ?? 'active') === 'inactive';
$listUrl     = $listUrl ?? base_url('staffs');
?>
<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => 'staffs', 'showTabs' => false]) ?>
<?php
$badge = [
    'Draft'     => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
    'Pending'   => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
    'Approved'  => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
    'Rejected'  => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    'Suspended' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
];
$stageTone = [
    'amber'  => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
    'blue'   => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    'purple' => 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
    'red'    => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    'gray'   => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
];
$keep = array_filter(['search' => $searchTerm, 'status' => $status !== 'all' ? $status : '', 'type' => $type !== 'ALL' ? $type : '', 'tab' => $isInactive ? 'inactive' : '']);
$selectCls = 'w-full md:w-40 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-xs focus:ring-primary focus:border-primary outline-none appearance-none bg-white';
?>

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">
                    <?= $requestMode ? 'Request List' : 'Staff List' ?>
                </h1>
                <div class="no-print flex flex-wrap gap-2">
                    <button type="button" onclick="document.getElementById('staffColumnsModal').classList.remove('hidden')"
                        class="bg-[#535dec] hover:bg-[#4853e0] text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">visibility</span>
                        Show/Hide Columns
                    </button>
                    <?php if ($canImport): ?>
                    <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
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
                    <a href="<?= base_url('staffs/export') . ($keep ? '?' . http_build_query($keep) : '') ?>"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">file_upload</span>
                        Export
                    </a>
                    <?php endif; ?>
                    <?php if ($showPrintButton): ?>
                    <button onclick="window.print()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">print</span>
                        Print
                    </button>
                    <?php endif; ?>
                    <?php if ($canRequest): ?>
                    <a href="<?= base_url('staffs/staffpassrequest') ?>" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                        <span class="material-icons text-sm mr-1">add</span>
                        Request
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Stat cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold"><?= $isInactive ? 'Inactive Staff Passes' : 'Total Staff Passes' ?></p>
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

            <!-- Filter -->
            <form method="get" action="<?= $listUrl ?>" class="no-print flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
                <div class="flex shadow-sm w-full max-w-lg">
                    <input name="search" value="<?= esc($searchTerm) ?>"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-xs focus:ring-primary focus:border-primary outline-none"
                        placeholder="IC / PASSPORT / FULL NAME / STAFF NO / APP NO" type="text"/>
                    <button type="submit" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-r flex items-center justify-center transition-colors">
                        <span class="material-icons text-white">search</span>
                    </button>
                </div>
                <div class="flex flex-wrap md:flex-nowrap gap-3 w-full md:w-auto">
                    <!-- KPK: Staff List vs Inactive Staff List -->
                    <select name="tab" onchange="this.form.submit()" class="<?= $selectCls ?>">
                        <option value="" <?= ! $isInactive ? 'selected' : '' ?>>Active Staff</option>
                        <option value="inactive" <?= $isInactive ? 'selected' : '' ?>>Inactive Staff</option>
                    </select>
                    <select name="type" onchange="this.form.submit()" class="<?= $selectCls ?>">
                        <?php foreach (['ALL' => 'All Types', 'NEW' => 'New', 'RENEWAL' => 'Renewal', 'REPLACEMENT' => 'Replacement'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $type === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="status" onchange="this.form.submit()" class="<?= $selectCls ?>">
                        <?php foreach (['all' => 'All Status', 'Draft' => 'Draft', 'Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected', 'Suspended' => 'Suspended'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $status === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="sort" onchange="this.form.submit()" class="<?= str_replace('md:w-40', 'md:w-48', $selectCls) ?>">
                        <?php foreach (['date_desc' => 'Date (Newest)', 'date_asc' => 'Date (Oldest)', 'name_asc' => 'Name (A - Z)', 'name_desc' => 'Name (Z - A)', 'staffno_asc' => 'Staff No (A - Z)', 'staffno_desc' => 'Staff No (Z - A)'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $sortBy === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <?php if ($searchTerm !== ''): ?>
            <div class="mb-4 text-xs text-gray-500 dark:text-gray-400">
                Showing results for <strong class="text-gray-800 dark:text-white"><?= esc($searchTerm) ?></strong>
                — <?= number_format($pagination['total']) ?> match<?= (int) $pagination['total'] === 1 ? '' : 'es' ?>
                <a href="<?= $listUrl . ($isInactive ? '?tab=inactive' : '') ?>" class="ml-2 text-primary hover:underline">Clear</a>
            </div>
            <?php endif; ?>

            <!-- Table -->
            <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-6">
                <table class="w-full min-w-max text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-bold uppercase tracking-wide">
                            <?php if ($col('no')): ?><th class="p-4 border-b dark:border-gray-600">No</th><?php endif; ?>
                            <?php if ($col('action')): ?><th class="p-4 border-b dark:border-gray-600 no-print">Action</th><?php endif; ?>
                            <?php if ($col('date')): ?><th class="p-4 border-b dark:border-gray-600">Date</th><?php endif; ?>
                            <?php if ($col('app_no')): ?><th class="p-4 border-b dark:border-gray-600">App No</th><?php endif; ?>
                            <?php if ($col('type')): ?><th class="p-4 border-b dark:border-gray-600">Type</th><?php endif; ?>
                            <?php if ($col('staff_no')): ?><th class="p-4 border-b dark:border-gray-600">Staff No</th><?php endif; ?>
                            <?php if ($col('full_name')): ?><th class="p-4 border-b dark:border-gray-600">Full Name</th><?php endif; ?>
                            <?php if ($col('ic_passport')): ?><th class="p-4 border-b dark:border-gray-600">IC / Passport No</th><?php endif; ?>
                            <?php if ($col('department')): ?><th class="p-4 border-b dark:border-gray-600">Department</th><?php endif; ?>
                            <?php if ($col('status')): ?><th class="p-4 border-b dark:border-gray-600">Status</th><?php endif; ?>
                            <?php if ($col('pass_expiry')): ?><th class="p-4 border-b dark:border-gray-600">Pass Expiry</th><?php endif; ?>
                            <?php if (! $requestMode && $col('current_location')): ?><th class="p-4 border-b dark:border-gray-600">Current Location</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                    <?php if (empty($staffList)): ?>
                        <tr>
                            <td colspan="<?= list_col_count($listKey) ?>" class="p-8 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-4">
                                        <span class="material-symbols-outlined text-4xl text-gray-400 dark:text-gray-500">folder_off</span>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-gray-700 dark:text-gray-300">No Data Available</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= $isInactive ? 'There are no inactive staff at the moment.' : 'There are no staff pass records at the moment.' ?></p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else: foreach ($staffList as $s): $stage = $s['stage'] ?? ['label' => '-', 'tone' => 'gray']; ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <?php if ($col('no')): ?><td class="p-4"><?= $s['no'] ?></td><?php endif; ?>
                            <?php if ($col('action')): ?>
                            <td class="p-4 no-print">
                                <div class="flex items-center gap-2">
                                    <a href="<?= base_url('staffpassrequest/view/' . $s['id']) ?>" class="text-primary hover:text-blue-700 transition-colors" title="View Details">
                                        <span class="material-symbols-outlined text-[20px]">search</span>
                                    </a>
                                    <?php if ($s['can_approve']): ?>
                                    <button type="button" data-id="<?= $s['id'] ?>" data-name="<?= esc($s['full_name'], 'attr') ?>" data-app="<?= esc($s['app_no'], 'attr') ?>" onclick="openApprove(this)" class="text-emerald-500 hover:text-emerald-700 transition-colors" title="Approve">
                                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($s['can_reject']): ?>
                                    <button type="button" data-id="<?= $s['id'] ?>" data-name="<?= esc($s['full_name'], 'attr') ?>" data-app="<?= esc($s['app_no'], 'attr') ?>" onclick="openReject(this)" class="text-red-500 hover:text-red-700 transition-colors" title="Reject">
                                        <span class="material-symbols-outlined text-[20px]">cancel</span>
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($canEdit && $s['can_edit_row']): ?>
                                    <a href="<?= base_url('staffpassrequest/edit/' . $s['id']) ?>" class="text-amber-500 hover:text-amber-700 transition-colors" title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($canManage): ?>
                                    <button type="button" onclick="toggleActive(<?= $s['id'] ?>, <?= $s['is_active'] ? 0 : 1 ?>, this)" class="<?= $s['is_active'] ? 'text-gray-400 hover:text-gray-600' : 'text-emerald-500 hover:text-emerald-700' ?> transition-colors" title="<?= $s['is_active'] ? 'Move to Inactive' : 'Move back to Active' ?>">
                                        <span class="material-symbols-outlined text-[20px]"><?= $s['is_active'] ? 'person_off' : 'person_check' ?></span>
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($canDelete): ?>
                                    <button type="button" onclick="confirmDelete(<?= $s['id'] ?>, this)" class="text-red-500 hover:text-red-700 transition-colors" title="Delete">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <?php endif; ?>
                            <?php if ($col('date')): ?><td class="p-4"><?= esc($s['date']) ?></td><?php endif; ?>
                            <?php if ($col('app_no')): ?><td class="p-4"><?= esc($s['app_no']) ?></td><?php endif; ?>
                            <?php if ($col('type')): ?><td class="p-4"><?= esc($s['type']) ?></td><?php endif; ?>
                            <?php if ($col('staff_no')): ?><td class="p-4"><?= esc($s['staff_no']) ?></td><?php endif; ?>
                            <?php if ($col('full_name')): ?><td class="p-4 font-semibold text-gray-800 dark:text-white"><?= esc($s['full_name']) ?></td><?php endif; ?>
                            <?php if ($col('ic_passport')): ?><td class="p-4"><?= esc(mask_ic_passport($s['ic_passport'])) ?></td><?php endif; ?>
                            <?php if ($col('department')): ?><td class="p-4"><?= esc($s['department']) ?></td><?php endif; ?>
                            <?php if ($col('status')): ?>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold <?= $badge[$s['status']] ?? 'bg-gray-100 text-gray-700' ?>"><?= esc($s['status']) ?></span>
                                <?php if ($s['awaiting']): ?><p class="text-[10px] text-gray-400 mt-1"><?= esc($s['awaiting']) ?></p><?php endif; ?>
                                <?php if ($s['status'] === 'Rejected' && $s['reject_reason']): ?><p class="text-[10px] text-red-400 mt-1 max-w-[180px] truncate" title="<?= esc($s['reject_reason'], 'attr') ?>"><?= esc($s['reject_reason']) ?></p><?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <?php if ($col('pass_expiry')): ?><td class="p-4"><?= esc($s['card_expiry']) ?></td><?php endif; ?>
                            <?php if (! $requestMode && $col('current_location')): ?>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold whitespace-nowrap <?= $stageTone[$stage['tone']] ?? $stageTone['gray'] ?>"><?= esc($stage['label']) ?></span>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <?= view('staffs/_pager', ['pagination' => $pagination, 'baseUrl' => $listUrl, 'showPerPage' => true]) ?>

<?= view('staffs/_layout_bottom') ?>

    <?php if ($canImport): ?>
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
            <input type="hidden" name="list" value="<?= esc($listKey) ?>"/>
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
                    <?php foreach ((list_column_registry()[$listKey]['columns'] ?? []) as $ckey => $cdef): $locked = ! empty($cdef['locked']); ?>
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

    <?php if ($requestMode): ?>
    <!-- Approve -->
    <div id="approveModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-sm w-full p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-1">Approve Staff Pass</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4"><span id="approveName"></span> — <span id="approveApp"></span></p>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Remark (optional)</label>
            <textarea id="approveRemark" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm mb-4"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-medium">Cancel</button>
                <button type="button" onclick="submitApprove(this)" class="px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Approve</button>
            </div>
        </div>
    </div>

    <!-- Reject -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-sm w-full p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-1">Reject Staff Pass</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4"><span id="rejectName"></span> — <span id="rejectApp"></span></p>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Reason <span class="text-red-500">*</span></label>
            <select id="rejectReasonId" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm mb-3">
                <option value="">-- Select a reason --</option>
                <?php foreach ($rejectReasons as $r): ?>
                <option value="<?= (int) $r['id'] ?>"><?= esc($r['reason']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (empty($rejectReasons)): ?>
            <p class="text-xs text-amber-600 dark:text-amber-400 mb-3">No active reject reasons are configured yet — add some under Config first.</p>
            <?php endif; ?>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Remark (optional)</label>
            <textarea id="rejectRemark" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm mb-4"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-medium">Cancel</button>
                <button type="button" onclick="submitReject(this)" class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white text-sm font-semibold">Reject</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        let activeId = null;
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
        function openApprove(btn) {
            activeId = btn.dataset.id;
            document.getElementById('approveName').textContent = btn.dataset.name;
            document.getElementById('approveApp').textContent = btn.dataset.app;
            document.getElementById('approveRemark').value = '';
            document.getElementById('approveModal').classList.remove('hidden');
        }
        function openReject(btn) {
            activeId = btn.dataset.id;
            document.getElementById('rejectName').textContent = btn.dataset.name;
            document.getElementById('rejectApp').textContent = btn.dataset.app;
            document.getElementById('rejectReasonId').value = '';
            document.getElementById('rejectRemark').value = '';
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        function submitApprove(btn) {
            staffPostReload('<?= base_url('staffs/approve') ?>', { id: activeId, remark: document.getElementById('approveRemark').value.trim() }, btn);
        }
        function submitReject(btn) {
            const reasonId = document.getElementById('rejectReasonId').value;
            if (!reasonId) { alert('Please select a reason for rejecting this staff pass.'); return; }
            staffPostReload('<?= base_url('staffs/reject') ?>', { id: activeId, reject_reason_id: reasonId, remark: document.getElementById('rejectRemark').value.trim() }, btn);
        }
        function toggleActive(id, active, btn) {
            const msg = active ? 'Move this staff member back to the active list?' : 'Move this staff member to the inactive list? (e.g. resigned)';
            if (!confirm(msg)) return;
            staffPostReload('<?= base_url('staffs/set-active/') ?>' + id, { active: active }, btn);
        }
        function confirmDelete(id, btn) {
            if (!confirm('Are you sure you want to delete this staff record? This action cannot be undone.')) return;
            staffPostReload('<?= base_url('staffs/delete/') ?>' + id, {}, btn);
        }
    </script>
</body>
</html>
