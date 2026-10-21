<?php
echo view('vehicles/_head', ['pageTitle' => $pageTitle]);
$badge = ['Draft' => 'bg-gray-100 text-gray-700', 'Pending' => 'bg-amber-100 text-amber-800', 'Approved' => 'bg-blue-100 text-blue-800',
    'Rejected' => 'bg-red-100 text-red-800', 'Expired' => 'bg-orange-100 text-orange-800', 'Issued' => 'bg-emerald-100 text-emerald-800', 'Terminated' => 'bg-slate-200 text-slate-700'];
helper('list_columns');
$listKey = 'vehicle_' . $stage;
$K = \App\Libraries\VehicleRules::KEYS[$group];
$base = 'vehicles/' . $group;
$tabs = [
    'all'      => ['My Vehicle Pass', $base, [$K['all']]],
    'staff'    => ['Staff Vehicle Pass', $base . '/staff', isset($K['staff']) ? [$K['staff']] : []],
    'request'  => ['Request List', $base . '/request-list', [$K['request']]],
    'reviewed' => ['Approved / Reject List', $base . '/reviewed-list', [$K['approved'], $K['reject']]],
    'closed'   => ['Closed List', $base . '/closed-list', [$K['closed']]],
];
?>
<div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-4">
        <h1 class="text-xl md:text-2xl font-bold tracking-tight uppercase"><?= esc($title) ?></h1>
        <div class="flex flex-wrap gap-2 self-start">
        <?php if (empty($canColumns) === false): ?>
        <button type="button" onclick="document.getElementById('vehColumnsModal').classList.remove('hidden')" class="bg-[#535dec] hover:bg-[#4853e0] text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-1.5 shadow">
            <span class="material-icons text-[18px]">visibility</span> Show/Hide Columns
        </button>
        <?php endif; ?>
        <?php if ($canStaffRequest && \App\Libraries\VehicleRules::buttonOn('veh_staff_request_button')): ?>
        <a href="<?= base_url('vehicles/public/staff-request') ?>" class="bg-white border border-primary text-primary px-4 py-2 rounded text-sm font-medium flex items-center gap-1.5">
            <span class="material-icons text-[18px]">badge</span> New Staff Request
        </a>
        <?php endif; ?>
        <?php if (! empty($canDirect) && \App\Libraries\VehicleRules::buttonOn('veh_direct_close_button')): ?>
        <a href="<?= base_url('vehicles/direct-close/' . $group) ?>" class="bg-white border border-primary text-primary px-4 py-2 rounded text-sm font-medium flex items-center gap-1.5">
            <span class="material-icons text-[18px]">verified</span> New Issued Pass
        </a>
        <?php endif; ?>
        <?php if ($canCreate && \App\Libraries\VehicleRules::buttonOn('veh_request_button')): ?>
        <a href="<?= base_url($base . '/request') ?>" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-1.5 shadow">
            <span class="material-icons text-[18px]">add</span> Request
        </a>
        <?php endif; ?>
        </div>
    </div>

    <?php if (! empty($statCards) && \App\Libraries\VehicleRules::buttonOn('veh_stat_cards_' . $stage)): ?><?= view('partials/stat_cards', ['cards' => $statCards]) ?><?php endif; ?>
    <?php $sel = static fn($a, $b) => (string) $a === (string) $b ? 'selected' : ''; $f = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm'; ?>
    <form method="get" class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-5">
        <input name="search" value="<?= esc($search) ?>" placeholder="App No / Name / IC / Passport / Staff ID / Company / Department / Reg No / Serial / Receipt" class="col-span-2 md:col-span-3 <?= $f ?>"/>
        <select name="sort" class="<?= $f ?>"><option value="">Sort By</option>
            <?php foreach (['app_asc' => 'App No A-Z', 'app_desc' => 'App No Z-A', 'name_asc' => 'Application Name A-Z', 'name_desc' => 'Application Name Z-A', 'reg_asc' => 'Reg No A-Z', 'reg_desc' => 'Reg No Z-A',
                'date_asc' => 'Date ASC', 'date_desc' => 'Date DESC', 'company_asc' => 'Company A-Z', 'company_desc' => 'Company Z-A', 'collect_asc' => 'Collection Date ASC', 'collect_desc' => 'Collection Date DESC'] as $k => $l): ?>
            <option value="<?= $k ?>" <?= $sel($sort, $k) ?>><?= $l ?></option><?php endforeach; ?></select>
        <select name="per_page" class="<?= $f ?>"><?php foreach ([10, 20, 30, 40] as $n): ?><option value="<?= $n ?>" <?= $sel($per, $n) ?>><?= $n ?> items per page</option><?php endforeach; ?></select>
        <select name="app_for" class="<?= $f ?>"><option value="">Application For</option>
            <?php foreach ($group === 'cnc' ? ['Company'] : ['Individual', 'Company', 'Staff'] as $o): ?><option <?= $sel($appFor, $o) ?>><?= $o ?></option><?php endforeach; ?></select>
        <select name="type_id" class="<?= $f ?>"><option value="">Vehicle</option>
            <?php foreach ($types as $t): ?><option value="<?= $t['id'] ?>" <?= $sel($typeId, $t['id']) ?>><?= esc($t['name']) ?></option><?php endforeach; ?></select>
        <select name="type_of_app" class="<?= $f ?>"><option value="">Type Of App</option>
            <?php foreach (['New', 'Renewal'] as $o): ?><option <?= $sel($appTyp, $o) ?>><?= $o ?></option><?php endforeach; ?></select>
        <?php if ($filterable): ?>
        <select name="status" class="<?= $f ?>"><option value="">All status</option>
            <?php foreach ($filterable as $s): ?><option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select>
        <?php endif; ?>
        <button class="bg-primary text-white rounded px-4 py-2 text-sm font-medium">Search</button>
    </form>

    <?php
    $d   = static fn($x) => $x ? date('d/m/Y', strtotime($x)) : '-';
    $who = static fn($r) => ($r['owner_type'] ?? '') === 'Staff' ? ($r['staff_no'] ?: '-') : ($r['owner_ic'] ?: '-');
    $co  = static fn($r) => ($r['owner_type'] ?? '') === 'Staff' ? ($r['department'] ?: '-') : ($r['company_name'] ?: '-');
    $stickerState = static function ($r) {
        if ($r['status'] === 'Terminated') { return 'Terminated'; }
        if ($r['status'] !== 'Issued') { return '-'; }
        return ($r['pass_expiry'] && $r['pass_expiry'] < date('Y-m-d')) ? 'Expired' : 'Active';
    };
    // Road tax / insurance expiry: red once the date has passed, green while it is still valid.
    $expiryCell = static function ($date) use ($d) {
        if (empty($date)) {
            return '';
        }
        $cls = $date < date('Y-m-d') ? 'text-red-600' : 'text-green-600';

        return '<span class="font-medium ' . $cls . '">' . $d($date) . '</span>';
    };
    // key => label. Which of them show is set per client in Config > Page Settings (and the Show/Hide Columns button).
    $defs = \App\Libraries\VehicleRules::listColumns($stage);
    $cols = [];
    foreach ($defs as $key => $def) {
        if (! empty($def['cnc_only']) && $group !== 'cnc') { continue; }
        if (list_col($listKey, $key)) { $cols[$key] = $def['label']; }
    }
    ?>
    <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-4">
        <table class="w-full min-w-max text-left text-xs">
            <thead><tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase tracking-wide">
                <th class="p-3 border-b dark:border-gray-600">No</th>
                <th class="p-3 border-b dark:border-gray-600">Action</th>
                <?php foreach ($cols as $h): ?><th class="p-3 border-b dark:border-gray-600"><?= esc($h) ?></th><?php endforeach; ?>
            </tr></thead>
            <tbody>
            <?php if (! $rows): ?>
                <tr><td colspan="<?= count($cols) + 2 ?>" class="p-8 text-center text-gray-500">No vehicle pass found.</td></tr>
            <?php else: foreach ($rows as $i => $r): ?>
                <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="p-3"><?= ($page - 1) * $per + $i + 1 ?></td>
                    <td class="p-3"><?php if (\App\Libraries\VehicleRules::buttonOn('veh_details_' . $stage)): ?><a href="<?= base_url('vehicles/view/' . $r['id']) ?>" class="text-primary hover:text-blue-700 transition-colors inline-flex" title="View Details"><span class="material-symbols-outlined text-[20px]">search</span></a><?php endif; ?>
                        <?php if ($stage === 'request' && ! empty($r['_can_approve']) && \App\Libraries\VehicleRules::buttonOn('veh_approve_action')): ?>
                        <button type="button" onclick="vehApprove(<?= (int) $r['id'] ?>, '<?= esc($r['plate_no'], 'js') ?>', '<?= esc($r['app_no'], 'js') ?>')" class="text-emerald-600 hover:text-emerald-700 transition-colors inline-flex align-middle ml-2" title="Approve"><span class="material-symbols-outlined text-[22px]">check_circle</span></button>
                        <?php endif; ?>
                        <?php if ($stage === 'request' && ! empty($r['_can_approve']) && \App\Libraries\VehicleRules::buttonOn('veh_reject_action')): ?>
                        <button type="button" onclick="vehReject(<?= (int) $r['id'] ?>, '<?= esc($r['plate_no'], 'js') ?>', '<?= esc($r['app_no'], 'js') ?>')" class="text-red-600 hover:text-red-700 transition-colors inline-flex align-middle ml-1" title="Reject"><span class="material-symbols-outlined text-[22px]">cancel</span></button>
                        <?php endif; ?>
                        <?php if ($stage === 'request' && ! empty($r['_can_edit']) && \App\Libraries\VehicleRules::buttonOn('veh_edit_request')): ?>
                        <a href="<?= base_url('vehicles/edit/' . $r['id']) ?>" class="text-amber-500 hover:text-amber-600 transition-colors inline-flex align-middle ml-1" title="Edit"><span class="material-symbols-outlined text-[20px]">edit</span></a>
                        <?php endif; ?>
                        <?php if ($stage === 'request' && ! empty($r['_can_delete']) && \App\Libraries\VehicleRules::buttonOn('veh_delete_request')): ?>
                        <form method="post" action="<?= base_url('vehicles/' . $r['id'] . '/delete') ?>" class="inline ml-1" onsubmit="return confirm('Delete this vehicle pass?')"><?= csrf_field() ?><button type="submit" class="text-red-600 hover:text-red-700 transition-colors inline-flex align-middle" title="Delete"><span class="material-symbols-outlined text-[20px]">delete</span></button></form>
                        <?php endif; ?></td>
                    <?php foreach ($cols as $key => $h): switch ($key) {
                        case 'type_of_app': $c = esc($r['type_of_app']); break;
                        case 'app_no': $c = '<span class="font-medium">' . esc($r['app_no']) . '</span>'; break;
                        case 'receipt_no': $c = esc($r['receipt_no'] ?: '-'); break;
                        case 'date': $c = $d($r['app_date']); break;
                        case 'app_for': $c = esc($r['owner_type']); break;
                        case 'year': $c = esc($r['sticker_year'] ?: '-'); break;
                        case 'name': $c = ($r['owner_type'] ?? '') === 'Staff' ? esc($r['owner_name']) : ''; break; // Individual / Company: no name asked on the request form
                        case 'ic': $c = ($r['owner_type'] ?? '') === 'Staff' ? esc($who($r)) : ''; break;
                        case 'company': $c = esc($co($r)); break;
                        case 'reg_no': $c = '<span class="font-semibold">' . esc($r['plate_no']) . '</span>'; break;
                        case 'type': $c = esc($r['type_name'] ?? '-'); break;
                        case 'operation': $c = esc($r['vehicle_operation'] ?: '-'); break;
                        case 'head_number': $c = esc($r['truck_head_number'] ?: '-'); break;
                        case 'collect_date': $c = $d($r['collect_date'] ?? null); break;
                        case 'serial_no': $c = esc($r['sticker_serial'] ?: '-'); break;
                        case 'issue_date': $c = $d($r['issue_date'] ?? null); break;
                        case 'status': $ds = \App\Libraries\VehicleRules::displayStatus($r); $c = '<span class="px-2 py-0.5 rounded-full font-semibold ' . ($badge[$ds] ?? '') . '">' . esc($ds) . '</span>'; break;
                        case 'next_action': $c = esc(\App\Libraries\VehicleRules::nextAction($r)); break;
                        case 'sticker_status': $c = $stickerState($r); break;
                        case 'road_tax': $c = $expiryCell($r['road_tax_expiry']); break;
                        case 'insurance': $c = $expiryCell($r['insurance_expiry']); break;
                        case 'remarks': $c = esc($r['reject_reason'] ?: ''); break;
                        default: $c = '';
                    } ?><td class="p-3"><?= $c ?></td><?php endforeach; ?>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-between text-xs text-gray-500">
        <span><?= (int) $total ?> record(s)</span>
        <?php if ($pages > 1): $q = http_build_query(['search' => $search, 'status' => $status, 'app_for' => $appFor, 'type_id' => $typeId, 'type_of_app' => $appTyp, 'sort' => $sort, 'per_page' => $per]); ?>
        <div class="flex gap-1">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?<?= $q ?>&page=<?= $i ?>" class="px-2.5 py-1 rounded <?= $i === $page ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-gray-700' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php if (! empty($canColumns)): ?>
<!-- Show/Hide Columns (same settings as Config > Page Settings) -->
<div id="vehColumnsModal" class="hidden fixed inset-0 z-[120] flex items-center justify-center p-4">
    <div onclick="document.getElementById('vehColumnsModal').classList.add('hidden')" class="absolute inset-0 bg-slate-900/55 cursor-pointer"></div>
    <form method="POST" action="<?= base_url('vehicles/save-columns') ?>" class="relative flex w-full max-w-[600px] flex-col rounded-xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
        <?= csrf_field() ?>
        <input type="hidden" name="list" value="<?= esc($listKey) ?>"/>
        <input type="hidden" name="back" value="<?= esc($tabs[$stage][1]) ?>"/>
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-700">
            <h2 class="text-lg font-bold tracking-tight text-[#3b5998] dark:text-white">Show/Hide Columns</h2>
            <button type="button" onclick="document.getElementById('vehColumnsModal').classList.add('hidden')" class="text-slate-300 hover:text-slate-500"><span class="material-icons text-[20px]">close</span></button>
        </div>
        <div class="px-6 py-5 overflow-y-auto max-h-[60vh]">
            <div class="mb-5 flex items-center gap-2">
                <input type="checkbox" id="selectAllVehCols" onchange="document.querySelectorAll('.veh-column-toggle:not(:disabled)').forEach(c => c.checked = this.checked)" class="rounded border-slate-300 h-4 w-4 cursor-pointer">
                <label for="selectAllVehCols" class="text-sm font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Select All Columns</label>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-4">
                <?php foreach (\App\Libraries\VehicleRules::listColumns($stage) as $ckey => $cdef): if (! empty($cdef['cnc_only']) && $group !== 'cnc') { continue; } $locked = ! empty($cdef['locked']); ?>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="veh_col_<?= esc($ckey) ?>" <?= $locked ? 'disabled' : 'name="columns[' . esc($ckey) . ']" value="1"' ?> class="veh-column-toggle rounded border-slate-300 h-4 w-4 <?= $locked ? 'opacity-60' : 'cursor-pointer' ?>" <?= list_col($listKey, $ckey) ? 'checked' : '' ?>>
                    <label for="veh_col_<?= esc($ckey) ?>" class="text-sm text-slate-600 dark:text-slate-300 uppercase <?= $locked ? '' : 'cursor-pointer' ?>"><?= esc($cdef['label']) ?><?= $locked ? ' <span class="normal-case text-xs text-slate-400">(always shown)</span>' : '' ?></label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="flex justify-end gap-3 border-t-2 border-slate-100 bg-white px-6 py-4 dark:border-slate-700 dark:bg-slate-900 rounded-b-xl">
            <button type="button" onclick="document.getElementById('vehColumnsModal').classList.add('hidden')" class="rounded-md border border-slate-400 bg-slate-500 hover:bg-slate-600 px-5 py-2 text-sm font-semibold text-white">Close</button>
            <button type="submit" class="rounded-md bg-[#535dec] hover:bg-[#4853e0] px-5 py-2 text-sm font-semibold text-white shadow-md">Apply Changes</button>
        </div>
    </form>
</div>
<?php endif; ?>
<?php if ($stage === 'request'): $mi = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm'; ?>
<div id="vehApproveModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <form id="vehApproveForm" method="post" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6"><?= csrf_field() ?>
        <h3 class="text-lg font-bold mb-1">Approve Vehicle Pass</h3>
        <p class="text-sm text-gray-500 mb-4"><span id="vehApproveInfo"></span></p>
        <label class="block text-xs font-semibold mb-1">Collection Date</label><input name="collect_date" class="flatpickr <?= $mi ?> mb-3" placeholder="dd/mm/yyyy"/>
        <label class="block text-xs font-semibold mb-1">Remark</label><textarea name="note" rows="2" class="<?= $mi ?> mb-4"></textarea>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('vehApproveModal').classList.add('hidden')" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-medium">Cancel</button>
            <button class="px-4 py-2 rounded bg-emerald-600 text-white text-sm font-semibold">Approve</button>
        </div>
    </form>
</div>
<div id="vehRejectModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <form id="vehRejectForm" method="post" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6"><?= csrf_field() ?>
        <h3 class="text-lg font-bold mb-1">Reject Vehicle Pass</h3>
        <p class="text-sm text-gray-500 mb-4"><span id="vehRejectInfo"></span></p>
        <label class="block text-xs font-semibold mb-1">Reject Reason</label>
        <select name="reject_reason_id" class="<?= $mi ?> mb-3"><option value="">-- Select --</option>
            <?php foreach ($rejectReasons ?? [] as $rr): ?><option value="<?= (int) $rr['id'] ?>"><?= esc($rr['reason']) ?></option><?php endforeach; ?></select>
        <label class="block text-xs font-semibold mb-1">Remark</label><textarea name="note" rows="2" class="<?= $mi ?> mb-4"></textarea>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('vehRejectModal').classList.add('hidden')" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-medium">Cancel</button>
            <button class="px-4 py-2 rounded bg-red-600 text-white text-sm font-semibold">Reject</button>
        </div>
    </form>
</div>
<script>
function vehApprove(id, plate, app) {
    document.getElementById('vehApproveForm').action = '<?= base_url('vehicles') ?>/' + id + '/approve';
    document.getElementById('vehApproveInfo').textContent = plate + ' - ' + app;
    document.getElementById('vehApproveModal').classList.remove('hidden');
}
function vehReject(id, plate, app) {
    document.getElementById('vehRejectForm').action = '<?= base_url('vehicles') ?>/' + id + '/reject';
    document.getElementById('vehRejectInfo').textContent = plate + ' - ' + app;
    document.getElementById('vehRejectModal').classList.remove('hidden');
}
</script>
<?php endif; ?>
<?= view('vehicles/_foot') ?>
