<?php
echo view('vehicles/_head', ['pageTitle' => $pageTitle]);
$badge = ['Draft' => 'bg-gray-100 text-gray-700', 'Pending' => 'bg-amber-100 text-amber-800', 'Approved' => 'bg-blue-100 text-blue-800',
    'Rejected' => 'bg-red-100 text-red-800', 'Issued' => 'bg-emerald-100 text-emerald-800', 'Terminated' => 'bg-slate-200 text-slate-700'];
$d   = static fn($x) => $x ? date('d/m/Y', strtotime($x)) : '-';
$row = static function (string $label, $value) { return '<div><p class="text-xs font-semibold text-gray-500 dark:text-gray-400">' . esc($label) . '</p><p class="text-sm mt-0.5">' . (($value === null || $value === '') ? '-' : esc((string) $value)) . '</p></div>'; };
$btn = 'px-4 py-2 rounded text-sm font-semibold';
$in  = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm';
$needPay = (int) $v['payment_required'] === 1 && $v['payment_status'] !== 'Paid';
?>
<div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="<?= base_url($listUrl) ?>" class="text-gray-500 hover:text-primary"><span class="material-symbols-outlined">arrow_back</span></a>
            <div>
                <h1 class="text-xl md:text-2xl font-bold tracking-tight uppercase"><?= esc($v['plate_no']) ?>
                    <span class="ml-2 align-middle text-xs px-2 py-0.5 rounded-full font-semibold <?= $badge[$v['status']] ?? '' ?>"><?= esc($v['status']) ?></span></h1>
                <p class="text-xs text-gray-500">Application No: <?= esc($v['app_no']) ?> &middot; <?= esc(\App\Libraries\VehicleRules::GROUPS[$group]) ?><?= ($v['pass_for'] ?? '') === 'staff' ? ' &middot; Staff pass' : '' ?><?= ($v['type_of_app'] ?? '') === 'Renewal' ? ' &middot; Renewal' : '' ?></p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php if ($can['edit']): ?><a href="<?= base_url('vehicles/edit/' . $v['id']) ?>" class="<?= $btn ?> bg-primary text-white">Edit</a><?php endif; ?>
            <?php if ($can['submit']): ?>
            <form method="post" action="<?= base_url('vehicles/' . $v['id'] . '/submit') ?>"><?= csrf_field() ?><button class="<?= $btn ?> bg-primary text-white">Submit</button></form>
            <?php endif; ?>
            <?php if ($can['renew']): ?><a href="<?= base_url('vehicles/renew/' . $v['id']) ?>" class="<?= $btn ?> bg-emerald-600 text-white">Renew</a><?php endif; ?>
            <?php if ($can['transfer']): ?><a href="<?= base_url('vehicle-transfer/new/' . $v['id']) ?>" class="<?= $btn ?> border border-primary text-primary">Transfer Ownership</a><?php endif; ?>
            <?php if ($can['delete']): ?>
            <form method="post" action="<?= base_url('vehicles/' . $v['id'] . '/delete') ?>" onsubmit="return confirm('Delete this vehicle pass?')"><?= csrf_field() ?><button class="<?= $btn ?> border border-red-300 text-red-600">Delete</button></form>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($v['status'] === 'Rejected' && $v['reject_reason']): ?>
    <div class="mb-5 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><strong>Rejected:</strong> <?= esc($v['reject_reason']) ?></div>
    <?php endif; ?>

    <h2 class="text-sm font-bold uppercase mb-3">Vehicle</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <?= $row('Vehicle Type', $v['type_name']) ?><?= $row('Plate Number', $v['plate_no']) ?><?= $row('Brand / Model', $v['brand']) ?><?= $row('Year Made', $v['year_made']) ?>
        <?= $row('Colour', $v['colour']) ?><?= $row('Road Tax Expiry', $d($v['road_tax_expiry'])) ?><?= $row('Insurance Expiry', $d($v['insurance_expiry'])) ?>
        <?php foreach ($typeFields as [$field, $label]): if (! empty($v[$field])) { echo $row($label, $v[$field]); } endforeach; ?>
    </div>

    <h2 class="text-sm font-bold uppercase mb-3">Owner</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <?= $row('Owner Type', $v['owner_type']) ?><?= $row('Owner Name', $v['owner_name']) ?><?= $row('IC / Passport', $v['owner_ic']) ?>
        <?= $row('Company', $v['company_name']) ?><?= $row('Contact No.', $v['contact_no']) ?><?= $row('Requested By', $v['requested_by']) ?><?= $row('Application Date', $d($v['app_date'])) ?>
    </div>

    <h2 class="text-sm font-bold uppercase mb-3">Location Access</h2>
    <div class="flex flex-wrap gap-2 mb-6">
        <?php $codes = array_filter(array_map('trim', explode(',', (string) $v['location_access']))); foreach ($codes as $c): ?>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 text-sm font-medium">
            <span class="material-symbols-outlined text-[16px]">check_circle</span><?= esc($labels[$c] ?? ucwords(str_replace('_', ' ', $c))) ?></span>
        <?php endforeach; if (! $codes): ?><span class="text-sm text-gray-500">No location access.</span><?php endif; ?>
    </div>

    <?php if ($files): ?>
    <h2 class="text-sm font-bold uppercase mb-3">Documents</h2>
    <div class="flex flex-wrap gap-3 mb-6">
        <?php foreach ($files as $f): ?><a href="<?= base_url($f['file_path']) ?>" target="_blank" class="text-primary text-sm underline"><?= esc($f['original_name'] ?: 'Document') ?></a><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ((int) $v['payment_required'] === 1 || $v['status'] === 'Issued' || $v['status'] === 'Terminated'): ?>
    <h2 class="text-sm font-bold uppercase mb-3">Payment &amp; Sticker</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <?php if ((int) $v['payment_required'] === 1): ?><?= $row('Payment', $v['payment_status']) ?><?= $row('Receipt No.', $v['receipt_no']) ?><?= $row('Payment Type', $v['payment_type']) ?><?php endif; ?>
        <?= $row('Sticker Serial No.', $v['sticker_serial']) ?><?= $row('Sticker Year', $v['sticker_year']) ?><?= $row('Pass Expiry', $d($v['pass_expiry'])) ?>
        <?= $row('Issued By', $v['issued_by']) ?><?= $row('Collected By', $v['collector_name']) ?>
    </div>
    <?php endif; ?>

    <?php if ($v['status'] === 'Pending' && $can['approve']): ?>
    <div class="rounded border border-gray-200 dark:border-gray-700 p-4 mb-2">
        <h2 class="text-sm font-bold uppercase mb-3">Approval</h2>
        <div class="flex flex-wrap items-start gap-3">
            <form method="post" action="<?= base_url('vehicles/' . $v['id'] . '/approve') ?>"><?= csrf_field() ?><button class="<?= $btn ?> bg-emerald-600 text-white">Approve</button></form>
            <form method="post" action="<?= base_url('vehicles/' . $v['id'] . '/reject') ?>" class="flex flex-wrap gap-2 items-start"><?= csrf_field() ?>
                <input name="note" required placeholder="Reason for rejecting" class="<?= $in ?> w-72"/><button class="<?= $btn ?> bg-red-600 text-white">Reject</button></form>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($v['status'] === 'Approved' && $can['issue']): ?>
    <div class="rounded border border-gray-200 dark:border-gray-700 p-4 mb-2">
        <h2 class="text-sm font-bold uppercase mb-3">Issue Sticker</h2>
        <?php if ($needPay && $payOnline): ?>
        <form method="post" action="<?= base_url('vehicles/' . $v['id'] . '/pay') ?>" class="mb-3"><?= csrf_field() ?><button class="<?= $btn ?> bg-primary text-white">Pay online</button>
            <span class="text-xs text-gray-500 ml-2">Payment is <?= esc($v['payment_status']) ?>. You can also enter the receipt number below.</span></form>
        <?php endif; ?>
        <form method="post" action="<?= base_url('vehicles/' . $v['id'] . '/issue') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end"><?= csrf_field() ?>
            <div><label class="block text-xs font-semibold mb-1">Sticker Serial No. *</label><input name="sticker_serial" required class="<?= $in ?> w-full"/></div>
            <div><label class="block text-xs font-semibold mb-1">Sticker Year</label><input name="sticker_year" value="<?= date('Y') ?>" maxlength="4" class="<?= $in ?> w-full"/></div>
            <div><label class="block text-xs font-semibold mb-1">Pass Expiry *</label><input name="pass_expiry" required class="flatpickr <?= $in ?> w-full" placeholder="dd/mm/yyyy"/></div>
            <div><label class="block text-xs font-semibold mb-1">Collected By</label><input name="collector_name" class="<?= $in ?> w-full"/></div>
            <?php if ($needPay): ?>
            <div><label class="block text-xs font-semibold mb-1">Receipt No. *</label><input name="receipt_no" class="<?= $in ?> w-full"/></div>
            <div><label class="block text-xs font-semibold mb-1">Payment Type</label>
                <select name="payment_type" class="<?= $in ?> w-full"><?php foreach (['Cash', 'Cheque', 'Online Banking', 'Other'] as $o): ?><option><?= $o ?></option><?php endforeach; ?></select></div>
            <?php endif; ?>
            <div><button class="<?= $btn ?> bg-primary text-white">Issue Sticker</button></div>
        </form>
    </div>
    <?php endif; ?>

    <?php if ($v['status'] === 'Issued' && $can['terminate']): ?>
    <form method="post" action="<?= base_url('vehicles/' . $v['id'] . '/terminate') ?>" onsubmit="return confirm('Terminate this vehicle pass? The plate becomes free for a new pass.')" class="mt-2"><?= csrf_field() ?>
        <button class="<?= $btn ?> bg-red-600 text-white">Terminate Pass</button></form>
    <?php endif; ?>
</div>

<div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <h2 class="text-sm font-bold uppercase mb-3">History</h2>
    <?php foreach ($logs as $l): ?>
    <p class="text-xs py-1.5 border-b border-gray-100 dark:border-gray-700"><span class="text-gray-500"><?= esc($l['created_at']) ?></span> &middot;
        <strong><?= esc(($l['from_status'] ? $l['from_status'] . ' → ' : '') . $l['to_status']) ?></strong> by <?= esc($l['done_by']) ?><?= $l['note'] ? ' — ' . esc($l['note']) : '' ?></p>
    <?php endforeach; ?>
</div>
<?= view('vehicles/_foot') ?>
