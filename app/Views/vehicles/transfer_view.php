<?php
echo view('vehicles/_head', ['pageTitle' => $pageTitle]);
$badge = ['Pending' => 'bg-amber-100 text-amber-800', 'Approved' => 'bg-emerald-100 text-emerald-800', 'Rejected' => 'bg-red-100 text-red-800'];
$row = static function (string $label, $value) { return '<div><p class="text-xs font-semibold text-gray-500 dark:text-gray-400">' . esc($label) . '</p><p class="text-sm mt-0.5">' . (($value === null || $value === '') ? '-' : esc((string) $value)) . '</p></div>'; };
$d = static fn($x) => $x ? date('d/m/Y H:i', strtotime($x)) : '-';
$btn = 'px-4 py-2 rounded text-sm font-semibold';
?>
<div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= base_url('vehicle-transfer') ?>" class="text-gray-500 hover:text-primary"><span class="material-symbols-outlined">arrow_back</span></a>
        <div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight uppercase"><?= esc($t['plate_no']) ?>
                <span class="ml-2 align-middle text-xs px-2 py-0.5 rounded-full font-semibold <?= $badge[$t['status']] ?? '' ?>"><?= esc($t['status']) ?></span></h1>
            <p class="text-xs text-gray-500">Transfer No: <?= esc($t['transfer_no']) ?> &middot; Pass <a class="text-primary underline" href="<?= base_url('vehicles/view/' . $t['vehicle_id']) ?>"><?= esc($t['pass_no']) ?></a></p>
        </div>
    </div>
    <?php if ($t['status'] === 'Rejected' && $t['reject_reason']): ?>
    <div class="mb-5 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><strong>Rejected:</strong> <?= esc($t['reject_reason']) ?></div>
    <?php endif; ?>
    <h2 class="text-sm font-bold uppercase mb-3">From (current owner)</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <?= $row('Owner Type', $t['from_owner_type']) ?><?= $row('Owner Name', $t['from_owner_name']) ?><?= $row('IC / Passport', $t['from_owner_ic']) ?><?= $row('Company', $t['from_company']) ?>
    </div>
    <h2 class="text-sm font-bold uppercase mb-3">To (new owner)</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <?= $row('Owner Type', $t['to_owner_type']) ?><?= $row('Owner Name', $t['to_owner_name']) ?><?= $row('IC / Passport', $t['to_owner_ic']) ?><?= $row('Company', $t['to_company']) ?><?= $row('Contact No.', $t['to_contact_no']) ?>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <?= $row('Reason', $t['reason']) ?><?= $row('Requested By', $t['requested_by']) ?><?= $row('Requested On', $d($t['created_at'])) ?><?= $row('Decided By', $t['decided_by']) ?><?= $row('Decided On', $d($t['decided_at'])) ?>
    </div>
    <?php if ($canDecide): ?>
    <div class="flex flex-wrap items-start gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
        <form method="post" action="<?= base_url('vehicle-transfer/' . $t['id'] . '/approve') ?>"><?= csrf_field() ?><button class="<?= $btn ?> bg-emerald-600 text-white">Approve</button></form>
        <form method="post" action="<?= base_url('vehicle-transfer/' . $t['id'] . '/reject') ?>" class="flex gap-2"><?= csrf_field() ?>
            <input name="note" placeholder="Reason for rejecting" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm w-64"/>
            <button class="<?= $btn ?> border border-red-300 text-red-600">Reject</button></form>
    </div>
    <?php endif; ?>
</div>
<?= view('vehicles/_foot') ?>
