<?php
echo view('vehicles/_head', ['pageTitle' => $pageTitle]);
$badge = ['Draft' => 'bg-gray-100 text-gray-700', 'Pending' => 'bg-amber-100 text-amber-800', 'Approved' => 'bg-blue-100 text-blue-800',
    'Rejected' => 'bg-red-100 text-red-800', 'Expired' => 'bg-orange-100 text-orange-800', 'Issued' => 'bg-emerald-100 text-emerald-800', 'Terminated' => 'bg-slate-200 text-slate-700'];
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
        <div class="flex gap-2 self-start">
        <?php if ($canStaffRequest): ?>
        <a href="<?= base_url('vehicles/public/staff-request') ?>" class="bg-white border border-primary text-primary px-4 py-2 rounded text-sm font-medium flex items-center gap-1.5">
            <span class="material-icons text-[18px]">badge</span> New Staff Request
        </a>
        <?php endif; ?>
        <?php if (! empty($canDirect)): ?>
        <a href="<?= base_url('vehicles/direct-close/' . $group) ?>" class="bg-white border border-primary text-primary px-4 py-2 rounded text-sm font-medium flex items-center gap-1.5">
            <span class="material-icons text-[18px]">verified</span> New Issued Pass
        </a>
        <?php endif; ?>
        <?php if ($canCreate): ?>
        <a href="<?= base_url($base . '/request') ?>" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-1.5 shadow">
            <span class="material-icons text-[18px]">add</span> Request
        </a>
        <?php endif; ?>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 mb-5 border-b border-gray-200 dark:border-gray-700 pb-3">
        <?php foreach ($tabs as $k => [$label, $path, $keys]): $show = false; foreach ($keys as $kk) { $show = $show || has_access('vehicle', $kk); } if (! $show) { continue; } ?>
        <a href="<?= base_url($path) ?>" class="px-3 py-1.5 rounded-full text-xs font-semibold <?= $k === $stage ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200' ?>"><?= $label ?></a>
        <?php endforeach; ?>
    </div>

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
    $cols = ['App No', 'Receipt No', 'Date', 'App For', 'Year', 'Name', 'IC / Passport / Staff ID', 'Company / Department', 'Reg No', 'Type Of Vehicle'];
    if (in_array($stage, ['request', 'reviewed'], true)) { array_unshift($cols, 'Type Of App'); }
    if ($group === 'cnc') { $cols[] = 'Operation'; $cols[] = 'Head Number'; }
    if (in_array($stage, ['all', 'staff', 'reviewed'], true)) { $cols[] = 'Date Collection'; }
    if ($stage === 'closed') { $cols[] = 'Serial No'; $cols[] = 'Date Of Issuance'; }
    $cols[] = 'Status';
    if (in_array($stage, ['all', 'staff', 'reviewed'], true)) { $cols[] = 'Next Action'; }
    if (in_array($stage, ['all', 'staff', 'closed'], true)) { $cols[] = 'Sticker Status'; $cols[] = 'Road Tax Expiry'; $cols[] = 'Insurance Expiry'; }
    $cols[] = 'Remarks';
    $cols[] = '';
    ?>
    <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-4">
        <table class="w-full min-w-max text-left text-xs">
            <thead><tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase tracking-wide">
                <th class="p-3 border-b dark:border-gray-600">No</th>
                <?php foreach ($cols as $h): ?><th class="p-3 border-b dark:border-gray-600"><?= $h ?></th><?php endforeach; ?>
            </tr></thead>
            <tbody>
            <?php if (! $rows): ?>
                <tr><td colspan="<?= count($cols) + 1 ?>" class="p-8 text-center text-gray-500">No vehicle pass found.</td></tr>
            <?php else: foreach ($rows as $i => $r): ?>
                <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="p-3"><?= ($page - 1) * $per + $i + 1 ?></td>
                    <?php foreach ($cols as $h): switch ($h) {
                        case 'Type Of App': $c = $r['type_of_app']; break;
                        case 'App No': $c = '<span class="font-medium">' . esc($r['app_no']) . '</span>'; break;
                        case 'Receipt No': $c = esc($r['receipt_no'] ?: '-'); break;
                        case 'Date': $c = $d($r['app_date']); break;
                        case 'App For': $c = esc($r['owner_type']); break;
                        case 'Year': $c = esc($r['sticker_year'] ?: '-'); break;
                        case 'Name': $c = esc($r['owner_name']); break;
                        case 'IC / Passport / Staff ID': $c = esc($who($r)); break;
                        case 'Company / Department': $c = esc($co($r)); break;
                        case 'Reg No': $c = '<span class="font-semibold">' . esc($r['plate_no']) . '</span>'; break;
                        case 'Type Of Vehicle': $c = esc($r['type_name'] ?? '-'); break;
                        case 'Operation': $c = esc($r['vehicle_operation'] ?: '-'); break;
                        case 'Head Number': $c = esc($r['truck_head_number'] ?: '-'); break;
                        case 'Date Collection': $c = $d($r['collect_date'] ?? null); break;
                        case 'Serial No': $c = esc($r['sticker_serial'] ?: '-'); break;
                        case 'Date Of Issuance': $c = $d($r['issue_date'] ?? null); break;
                        case 'Status': $ds = \App\Libraries\VehicleRules::displayStatus($r); $c = '<span class="px-2 py-0.5 rounded-full font-semibold ' . ($badge[$ds] ?? '') . '">' . esc($ds) . '</span>'; break;
                        case 'Next Action': $c = esc(\App\Libraries\VehicleRules::nextAction($r)); break;
                        case 'Sticker Status': $c = $stickerState($r); break;
                        case 'Road Tax Expiry': $c = $d($r['road_tax_expiry']); break;
                        case 'Insurance Expiry': $c = $d($r['insurance_expiry']); break;
                        case 'Remarks': $c = esc($r['reject_reason'] ?: ''); break;
                        default: $c = '<a href="' . base_url('vehicles/view/' . $r['id']) . '" class="text-primary font-semibold hover:underline">Details</a>';
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
<?= view('vehicles/_foot') ?>
