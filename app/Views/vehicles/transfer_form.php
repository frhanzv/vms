<?php
echo view('vehicles/_head', ['pageTitle' => $pageTitle]);
$in = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm';
$lb = 'block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1';
$row = static function (string $label, $value) { return '<div><p class="text-xs font-semibold text-gray-500 dark:text-gray-400">' . esc($label) . '</p><p class="text-sm mt-0.5">' . (($value === null || $value === '') ? '-' : esc((string) $value)) . '</p></div>'; };
$d = static fn($x) => $x ? date('d/m/Y', strtotime($x)) : '-';
?>
<div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= base_url('vehicle-transfer') ?>" class="text-gray-500 hover:text-primary"><span class="material-symbols-outlined">arrow_back</span></a>
        <h1 class="text-xl md:text-2xl font-bold tracking-tight uppercase">Transfer Ownership</h1>
    </div>
    <p class="text-sm text-gray-500 mb-4">Search a vehicle by plate number. If it belongs to another company you can take it over; the pass keeps its sticker and expiry.</p>
    <form method="get" action="<?= base_url('vehicle-transfer/new') ?>" class="flex gap-2 max-w-xl mb-6">
        <input name="plate" value="<?= esc($plate) ?>" placeholder="Plate number" class="<?= $in ?>"/>
        <button class="bg-primary text-white rounded px-4 text-sm font-medium">Search</button>
    </form>
    <?php if ($notFound): ?><p class="text-sm text-red-600"><?= esc($notFound) ?></p><?php endif; ?>
    <?php if ($v): ?>
    <h2 class="text-sm font-bold uppercase mb-3 flex items-center gap-2"><span class="material-symbols-outlined text-base">apartment</span>Company</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
        <?= $row('Company Registration ID', $v['co_reg_no'] ?? '') ?><?= $row('Company Name', ($v['co_name'] ?? '') !== '' ? $v['co_name'] : $v['company_name']) ?>
    </div>
    <h2 class="text-sm font-bold uppercase mb-3 flex items-center gap-2"><span class="material-symbols-outlined text-base">directions_car</span>Vehicle Info</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
        <?= $row('Vehicle Operation', $v['vehicle_operation'] ?? '') ?><?= $row('Type Of Vehicle', $v['type_name'] ?? '') ?><?= $row('Registration Number', $v['plate_no']) ?><?= $row('Year Made', $v['year_made'] ?? '') ?>
        <?= $row('Colour', $v['colour'] ?? '') ?><?= $row('Brand', $v['brand'] ?? '') ?><?= $row('Road Tax Expiry Date', $d($v['road_tax_expiry'] ?? null)) ?><?= $row('Insurance Expiry Date', $d($v['insurance_expiry'] ?? null)) ?>
    </div>
    <?php if ($v['expired']): ?>
        <div class="rounded border border-amber-200 bg-amber-50 text-amber-800 px-4 py-3 text-sm">
            This pass expired on <?= $d($v['pass_expiry']) ?> and cannot be taken over. Submit a new vehicle pass request for this plate instead.
            <a class="underline font-semibold ml-1" href="<?= base_url('vehicles') ?>">Go to Vehicle Pass</a>
        </div>
    <?php else: ?>
        <form method="post" action="<?= base_url('vehicle-transfer/store') ?>" onsubmit="return confirm('Take over this vehicle? It will move to the receiving company straight away.')">
            <?= csrf_field() ?>
            <input type="hidden" name="vehicle_id" value="<?= (int) $v['id'] ?>"/>
            <input type="hidden" name="from_company_id" value="<?= (int) $v['vendor_company_id'] ?>"/>
            <div class="max-w-xl mb-6">
                <label class="<?= $lb ?>">Receiving Company *</label>
                <?php if ($ownCompany): ?>
                    <input value="<?= esc($ownCompany['name']) ?>" disabled class="<?= $in ?> bg-gray-100 dark:bg-gray-700"/>
                <?php else: ?>
                    <select name="to_company_id" class="<?= $in ?>"><option value="">-- Select company --</option>
                        <?php foreach ($companies as $c): if ((int) $c['id'] === (int) $v['vendor_company_id']) { continue; } ?>
                        <option value="<?= (int) $c['id'] ?>"><?= esc($c['name']) ?></option><?php endforeach; ?></select>
                <?php endif; ?>
            </div>
            <button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Transfer Ownership</button>
        </form>
    <?php endif; endif; ?>
</div>
<?= view('vehicles/_foot') ?>
