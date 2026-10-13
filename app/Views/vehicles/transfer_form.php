<?php
echo view('vehicles/_head', ['pageTitle' => $pageTitle]);
$in  = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm';
$lb  = 'block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1';
$val = static fn(string $k) => esc($old[$k] ?? '');
$sel = (int) ($old['vehicle_id'] ?? $pick);
?>
<div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= base_url('vehicle-transfer') ?>" class="text-gray-500 hover:text-primary"><span class="material-symbols-outlined">arrow_back</span></a>
        <h1 class="text-xl md:text-2xl font-bold tracking-tight uppercase">New Ownership Transfer</h1>
    </div>
    <?php if (! $cands): ?>
        <p class="text-sm text-gray-500">There is no issued vehicle pass that can be transferred right now.</p>
    <?php else: ?>
    <form method="post" action="<?= base_url('vehicle-transfer/store') ?>" x-data="{ type: '<?= ($old['to_owner_type'] ?? 'Company') === 'Individual' ? 'Individual' : 'Company' ?>' }">
        <?= csrf_field() ?>
        <div class="max-w-xl mb-6">
            <label class="<?= $lb ?>">Vehicle Pass (issued) *</label>
            <select name="vehicle_id" class="<?= $in ?>"><option value="">-- Select --</option>
                <?php foreach ($cands as $c): ?><option value="<?= $c['id'] ?>" <?= $sel === (int) $c['id'] ? 'selected' : '' ?>><?= esc($c['plate_no']) ?> - <?= esc($c['owner_name']) ?> (<?= esc($c['app_no']) ?>)</option><?php endforeach; ?></select>
        </div>
        <h2 class="text-sm font-bold uppercase text-gray-700 dark:text-gray-200 mb-3">New Owner</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div><label class="<?= $lb ?>">Owner Type *</label>
                <select name="to_owner_type" x-model="type" class="<?= $in ?>"><option>Company</option><option>Individual</option></select></div>
            <div><label class="<?= $lb ?>">Owner Name *</label><input name="to_owner_name" value="<?= $val('to_owner_name') ?>" class="<?= $in ?>"/></div>
            <div><label class="<?= $lb ?>">IC / Passport</label><input name="to_owner_ic" value="<?= $val('to_owner_ic') ?>" class="<?= $in ?>"/></div>
            <div x-show="type === 'Company'"><label class="<?= $lb ?>">Company Name *</label><input name="to_company" value="<?= $val('to_company') ?>" class="<?= $in ?>"/></div>
            <div><label class="<?= $lb ?>">Contact No. *</label><input name="to_contact_no" value="<?= $val('to_contact_no') ?>" class="<?= $in ?>"/></div>
        </div>
        <div class="mb-6"><label class="<?= $lb ?>">Reason</label><textarea name="reason" rows="2" class="<?= $in ?>"><?= $val('reason') ?></textarea></div>
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="<?= base_url('vehicle-transfer') ?>" class="px-4 py-2 rounded border border-gray-300 text-sm">Cancel</a>
            <button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Submit Transfer</button>
        </div>
    </form>
    <?php endif; ?>
</div>
<?= view('vehicles/_foot') ?>
