<?php
echo view('vehicles/_head', ['pageTitle' => $pageTitle]);
$badge = ['Draft' => 'bg-gray-100 text-gray-700', 'Pending' => 'bg-amber-100 text-amber-800', 'Approved' => 'bg-emerald-100 text-emerald-800', 'Rejected' => 'bg-red-100 text-red-800'];
$dd  = static fn($x) => $x ? date('d/m/Y', strtotime($x)) : '-';
$row = static function (string $label, $value) { return '<div><p class="text-xs font-semibold text-gray-500 dark:text-gray-400">' . esc($label) . '</p><p class="text-sm mt-0.5">' . (($value === null || $value === '') ? '-' : esc((string) $value)) . '</p></div>'; };
$btn = 'px-4 py-2 rounded text-sm font-semibold';
?>
<div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="<?= base_url('vehicle-daily') ?>" class="text-gray-500 hover:text-primary"><span class="material-symbols-outlined">arrow_back</span></a>
            <div>
                <h1 class="text-xl md:text-2xl font-bold tracking-tight uppercase"><?= esc($d['plate_no']) ?>
                    <span class="ml-2 align-middle text-xs px-2 py-0.5 rounded-full font-semibold <?= $badge[$d['status']] ?? '' ?>"><?= esc($d['status']) ?></span></h1>
                <p class="text-xs text-gray-500">Daily Pass No: <?= esc($d['app_no']) ?></p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php if ($can['edit']): ?><a href="<?= base_url('vehicle-daily/edit/' . $d['id']) ?>" class="<?= $btn ?> bg-primary text-white">Edit</a><?php endif; ?>
            <?php if ($can['submit']): ?><form method="post" action="<?= base_url('vehicle-daily/' . $d['id'] . '/submit') ?>"><?= csrf_field() ?><button class="<?= $btn ?> bg-primary text-white">Submit</button></form><?php endif; ?>
            <?php if ($can['delete']): ?><form method="post" action="<?= base_url('vehicle-daily/' . $d['id'] . '/delete') ?>" onsubmit="return confirm('Delete this daily pass?')"><?= csrf_field() ?><button class="<?= $btn ?> border border-red-300 text-red-600">Delete</button></form><?php endif; ?>
        </div>
    </div>
    <?php if ($d['status'] === 'Rejected' && $d['reject_reason']): ?><div class="mb-5 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><strong>Rejected:</strong> <?= esc($d['reject_reason']) ?></div><?php endif; ?>
    <?php $staff = ($d['app_for'] ?? 'Individual') === 'Staff'; ?>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <?= $row('Application For', $d['app_for'] ?? 'Individual') ?>
        <?php if ($staff): ?><?= $row('Staff ID', $d['staff_no'] ?? '') ?><?= $row('Department', $d['department'] ?? '') ?><?= $row('HOD', $d['hod'] ?? '') ?>
        <?php else: ?><?= $row('Company', $d['company_name']) ?><?php endif; ?>
        <?= $row($staff ? 'Staff Name' : 'Name', $d['driver_name']) ?><?= $row('IC / Passport', $d['driver_ic']) ?><?= $row('Contact No.', $d['contact_no']) ?>
        <?= $row('Vehicle Type', $d['type_name']) ?><?= $row('Plate Number', $d['plate_no']) ?><?= $row('Year Made', $d['year_made'] ?? '') ?><?= $row('Brand', $d['brand'] ?? '') ?>
        <?= $row('Colour', $d['colour'] ?? '') ?><?= $row('Road Tax Expiry', $dd($d['road_tax_expiry'] ?? null)) ?><?= $row('Insurance Expiry', $dd($d['insurance_expiry'] ?? null)) ?>
        <?= $row('Purpose', $d['purpose']) ?><?= $row('Requested By', $d['requested_by']) ?><?= $row('Approved By', $d['approved_by']) ?><?= $row('Approved On', $dd($d['approved_at'])) ?>
        <?= $row('Remark', $d['remark'] ?? '') ?>
    </div>
    <h2 class="text-sm font-bold uppercase mb-3">Visit Dates</h2>
    <div class="flex flex-wrap gap-2 mb-6">
        <?php $vd = json_decode((string) ($d['visit_dates'] ?? ''), true);
        if (! is_array($vd) || ! $vd) { $vd = $d['visit_from'] ? [['from' => $d['visit_from'], 'to' => $d['visit_to'] ?: $d['visit_from']]] : []; }
        foreach ($vd as $r): ?><span class="rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1 text-sm"><?= $dd($r['from']) ?><?= $r['to'] !== $r['from'] ? ' - ' . $dd($r['to']) : '' ?></span><?php endforeach;
        if (! $vd): ?><span class="text-sm text-gray-500">-</span><?php endif; ?>
    </div>
    <h2 class="text-sm font-bold uppercase mb-3">Location Access</h2>
    <div class="flex flex-wrap gap-2 mb-6">
        <?php $codes = array_filter(array_map('trim', explode(',', (string) $d['location_access']))); foreach ($codes as $c): ?>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 text-sm font-medium"><?= esc($labels[$c] ?? ucwords(str_replace('_', ' ', $c))) ?></span>
        <?php endforeach; if (! $codes): ?><span class="text-sm text-gray-500">No location access.</span><?php endif; ?>
    </div>
    <?php if ($can['decide']): ?>
    <div class="flex flex-wrap items-start gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
        <form method="post" action="<?= base_url('vehicle-daily/' . $d['id'] . '/approve') ?>"><?= csrf_field() ?><button class="<?= $btn ?> bg-emerald-600 text-white">Approve</button></form>
        <form method="post" action="<?= base_url('vehicle-daily/' . $d['id'] . '/reject') ?>" class="flex gap-2"><?= csrf_field() ?>
            <input name="note" placeholder="Reason for rejecting" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm w-64"/>
            <button class="<?= $btn ?> border border-red-300 text-red-600">Reject</button></form>
    </div>
    <?php endif; ?>
</div>
<?= view('vehicles/_foot') ?>
