<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => 'staffs']) ?>
<?php
$isInactive = ($tab ?? 'active') === 'inactive';
$badge = [
    'Draft'     => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
    'Pending'   => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
    'Approved'  => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
    'Rejected'  => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    'Suspended' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
];
$cardBadge = [
    'Active'     => 'text-emerald-600',
    'Inactive'   => 'text-gray-400',
    'Terminated' => 'text-red-500',
];
$keep = array_filter(['search' => $searchTerm, 'status' => $status !== 'all' ? $status : '', 'type' => $type !== 'ALL' ? $type : '', 'tab' => $isInactive ? 'inactive' : '']);
?>

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">
                    <?= $isInactive ? 'Inactive Staff List' : 'Staff Pass List' ?>
                </h1>
                <div class="no-print flex flex-wrap gap-2">
                    <?php if ($canImport): ?>
                    <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow">
                        <span class="material-icons text-sm mr-1">add</span>Import
                    </button>
                    <a href="<?= base_url('files/StaffTemplateNew.xlsx') ?>" download="StaffTemplateNew.xlsx" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow">
                        <span class="material-icons text-sm mr-1">file_download</span>Template
                    </a>
                    <?php endif; ?>
                    <?php if ($canExport): ?>
                    <a href="<?= base_url('staffs/export') . ($keep ? '?' . http_build_query($keep) : '') ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow">
                        <span class="material-icons text-sm mr-1">file_upload</span>Export
                    </a>
                    <?php endif; ?>
                    <?php if ($showPrintButton): ?>
                    <button onclick="window.print()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow">
                        <span class="material-icons text-sm mr-1">print</span>Print
                    </button>
                    <?php endif; ?>
                    <?php if ($canRequest): ?>
                    <a href="<?= base_url('staffs/staffpassrequest') ?>" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow">
                        <span class="material-icons text-sm mr-1">add</span>Request
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <?php foreach ([
                    ['Active Staff Passes', $stats['total'], 'text-gray-800 dark:text-white'],
                    ['Pending Approval', $stats['pending'], 'text-amber-500'],
                    ['Approved', $stats['approved'], 'text-emerald-500'],
                    ['Inactive Staff', $stats['inactive'], 'text-gray-400'],
                ] as [$label, $value, $cls]): ?>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold"><?= $label ?></p>
                    <p class="text-2xl font-bold mt-1 <?= $cls ?>"><?= number_format($value) ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Active / Inactive (KPK: Staff List vs Inactive Staff List) -->
            <div class="no-print inline-flex rounded-lg border border-gray-200 dark:border-gray-700 p-1 mb-4 text-xs font-semibold">
                <a href="<?= base_url('staffs') ?>" class="px-4 py-1.5 rounded-md <?= ! $isInactive ? 'bg-primary text-white' : 'text-gray-500 hover:text-primary' ?>">Active</a>
                <a href="<?= base_url('staffs?tab=inactive') ?>" class="px-4 py-1.5 rounded-md <?= $isInactive ? 'bg-primary text-white' : 'text-gray-500 hover:text-primary' ?>">Inactive</a>
            </div>

            <!-- Filters -->
            <form method="get" action="<?= base_url('staffs') ?>" class="no-print flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
                <?php if ($isInactive): ?><input type="hidden" name="tab" value="inactive"/><?php endif; ?>
                <div class="flex shadow-sm w-full max-w-lg">
                    <input name="search" value="<?= esc($searchTerm) ?>" placeholder="IC / PASSPORT / FULL NAME / STAFF NO / APP NO" type="text"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-xs focus:ring-primary focus:border-primary outline-none"/>
                    <button type="submit" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded-r flex items-center"><span class="material-icons">search</span></button>
                </div>
                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <select name="type" onchange="this.form.submit()" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-xs bg-white">
                        <?php foreach (['ALL' => 'All Types', 'NEW' => 'New', 'RENEWAL' => 'Renewal', 'REPLACEMENT' => 'Replacement'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $type === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="status" onchange="this.form.submit()" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-xs bg-white">
                        <?php foreach (['all' => 'All Status', 'Draft' => 'Draft', 'Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected', 'Suspended' => 'Suspended'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $status === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="sort" onchange="this.form.submit()" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-xs bg-white">
                        <?php foreach (['date_desc' => 'Date (Newest)', 'date_asc' => 'Date (Oldest)', 'staffno_asc' => 'Staff No (A - Z)', 'staffno_desc' => 'Staff No (Z - A)', 'name_asc' => 'Name (A - Z)', 'name_desc' => 'Name (Z - A)'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $sortBy === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-6">
                <table class="w-full min-w-max text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-bold uppercase tracking-wide">
                            <th class="p-4 border-b dark:border-gray-600">No</th>
                            <th class="p-4 border-b dark:border-gray-600 no-print">Action</th>
                            <th class="p-4 border-b dark:border-gray-600">Date</th>
                            <th class="p-4 border-b dark:border-gray-600">App No</th>
                            <th class="p-4 border-b dark:border-gray-600">Type</th>
                            <th class="p-4 border-b dark:border-gray-600">Staff No</th>
                            <th class="p-4 border-b dark:border-gray-600">Full Name</th>
                            <th class="p-4 border-b dark:border-gray-600">IC / Passport</th>
                            <th class="p-4 border-b dark:border-gray-600">Department</th>
                            <th class="p-4 border-b dark:border-gray-600">Status</th>
                            <th class="p-4 border-b dark:border-gray-600">Card</th>
                            <th class="p-4 border-b dark:border-gray-600">Card Expiry</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                    <?php if (empty($staffList)): ?>
                        <tr><td colspan="12" class="p-8 text-center">
                            <span class="material-symbols-outlined text-4xl text-gray-300">folder_off</span>
                            <p class="text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">No Data Available</p>
                            <p class="text-sm text-gray-500 mt-1"><?= $isInactive ? 'No inactive staff.' : 'There are no staff pass records at the moment.' ?></p>
                        </td></tr>
                    <?php else: foreach ($staffList as $s): ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50/50 dark:hover:bg-gray-800/40">
                            <td class="p-4"><?= $s['no'] ?></td>
                            <td class="p-4 no-print">
                                <div class="flex items-center gap-2">
                                    <a href="<?= base_url('staffpassrequest/view/' . $s['id']) ?>" class="text-primary hover:text-blue-700" title="View Details"><span class="material-symbols-outlined text-[20px]">search</span></a>
                                    <?php if ($s['status'] === 'Approved' || $s['status'] === 'Suspended'): ?>
                                    <a href="<?= base_url('staffs/card-info/' . $s['id']) ?>" class="text-indigo-500 hover:text-indigo-700" title="Pass / Card Details"><span class="material-symbols-outlined text-[20px]">id_card</span></a>
                                    <?php endif; ?>
                                    <?php if ($s['can_approve']): ?>
                                    <button type="button" data-id="<?= $s['id'] ?>" data-name="<?= esc($s['full_name'], 'attr') ?>" data-app="<?= esc($s['app_no'], 'attr') ?>" onclick="openApprove(this)" class="text-emerald-500 hover:text-emerald-700" title="Approve"><span class="material-symbols-outlined text-[20px]">check_circle</span></button>
                                    <?php endif; ?>
                                    <?php if ($s['can_reject']): ?>
                                    <button type="button" data-id="<?= $s['id'] ?>" data-name="<?= esc($s['full_name'], 'attr') ?>" data-app="<?= esc($s['app_no'], 'attr') ?>" onclick="openReject(this)" class="text-red-500 hover:text-red-700" title="Reject"><span class="material-symbols-outlined text-[20px]">cancel</span></button>
                                    <?php endif; ?>
                                    <?php if ($canEdit && $s['can_edit_row']): ?>
                                    <a href="<?= base_url('staffpassrequest/edit/' . $s['id']) ?>" class="text-amber-500 hover:text-amber-700" title="Edit"><span class="material-symbols-outlined text-[20px]">edit</span></a>
                                    <?php endif; ?>
                                    <?php if ($canManage): ?>
                                    <button type="button" onclick="toggleActive(<?= $s['id'] ?>, <?= $s['is_active'] ? 0 : 1 ?>, this)" class="<?= $s['is_active'] ? 'text-gray-400 hover:text-gray-600' : 'text-emerald-500 hover:text-emerald-700' ?>" title="<?= $s['is_active'] ? 'Move to Inactive' : 'Move back to Active' ?>">
                                        <span class="material-symbols-outlined text-[20px]"><?= $s['is_active'] ? 'person_off' : 'person_check' ?></span>
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($canDelete): ?>
                                    <button type="button" onclick="confirmDelete(<?= $s['id'] ?>, this)" class="text-red-500 hover:text-red-700" title="Delete"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="p-4"><?= esc($s['date']) ?></td>
                            <td class="p-4"><?= esc($s['app_no']) ?></td>
                            <td class="p-4"><?= esc($s['type']) ?></td>
                            <td class="p-4 font-semibold"><?= esc($s['staff_no']) ?></td>
                            <td class="p-4 font-semibold text-gray-800 dark:text-white"><?= esc($s['full_name']) ?></td>
                            <td class="p-4"><?= esc(mask_ic_passport($s['ic_passport'])) ?></td>
                            <td class="p-4"><?= esc($s['department']) ?></td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold <?= $badge[$s['status']] ?? 'bg-gray-100 text-gray-700' ?>"><?= esc($s['status']) ?></span>
                                <?php if ($s['awaiting']): ?><p class="text-[10px] text-gray-400 mt-1"><?= esc($s['awaiting']) ?></p><?php endif; ?>
                                <?php if ($s['status'] === 'Rejected' && $s['reject_reason']): ?><p class="text-[10px] text-red-400 mt-1 max-w-[180px] truncate" title="<?= esc($s['reject_reason'], 'attr') ?>"><?= esc($s['reject_reason']) ?></p><?php endif; ?>
                            </td>
                            <td class="p-4 font-semibold <?= $cardBadge[$s['card_status']] ?? 'text-gray-400' ?>"><?= esc($s['card_status']) ?></td>
                            <td class="p-4"><?= esc($s['card_expiry']) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <?= view('staffs/_pager', ['pagination' => $pagination, 'baseUrl' => base_url('staffs'), 'showPerPage' => true]) ?>

<?= view('staffs/_layout_bottom') ?>

    <?php if ($canImport): ?>
    <div id="uploadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between p-4 border-b dark:border-slate-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">UPLOAD FILE</h3>
                <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><span class="material-icons">close</span></button>
            </div>
            <form action="<?= base_url('staff-pass/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="p-6">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Choose Excel File</label>
                    <input name="upload_file" type="file" accept=".xlsx, .xls" required class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-slate-700 dark:border-slate-600"/>
                    <p class="mt-2 text-xs text-gray-500">Imported staff are treated as HR-approved (as in KPK) and go straight to the Process List for their card. A "Status" column of Active / Inactive sets the employee status.</p>
                </div>
                <div class="flex justify-end gap-2 p-4 border-t dark:border-slate-700">
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded text-sm font-medium flex items-center"><span class="material-icons text-sm mr-1">publish</span>Import</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Approve -->
    <div id="approveModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-sm w-full p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-1">Approve Staff Pass</h3>
            <p class="text-sm text-gray-500 mb-4"><span id="approveName"></span> — <span id="approveApp"></span></p>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Remark (optional)</label>
            <textarea id="approveRemark" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm mb-4"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-medium">Cancel</button>
                <button type="button" id="approveSubmitBtn" onclick="submitApprove(this)" class="px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Approve</button>
            </div>
        </div>
    </div>

    <!-- Reject -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-sm w-full p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-1">Reject Staff Pass</h3>
            <p class="text-sm text-gray-500 mb-4"><span id="rejectName"></span> — <span id="rejectApp"></span></p>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Reason <span class="text-red-500">*</span></label>
            <select id="rejectReasonId" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm mb-3">
                <option value="">-- Select a reason --</option>
                <?php foreach ($rejectReasons as $r): ?>
                <option value="<?= (int) $r['id'] ?>"><?= esc($r['reason']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (empty($rejectReasons)): ?>
            <p class="text-xs text-amber-600 mb-3">No active reject reasons are configured yet — add some under Config first.</p>
            <?php endif; ?>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Remark (optional)</label>
            <textarea id="rejectRemark" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded px-3 py-2 text-sm mb-4"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-medium">Cancel</button>
                <button type="button" onclick="submitReject(this)" class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white text-sm font-semibold">Reject</button>
            </div>
        </div>
    </div>

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
