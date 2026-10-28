<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php
$fmt = static fn($v) => $v ? date('d/m/Y H:i:s', strtotime((string) $v)) : '-';
$d   = static fn($v) => $v ? date('d/m/Y', strtotime((string) $v)) : '-';
$t   = static fn($v) => $v ? date('H:i:s', strtotime((string) $v)) : '-';
$e   = static fn($v) => ($v === null || $v === '') ? '-' : esc((string) $v);
$in  = $tx['time_in'] ?? null;
$out = $tx['time_out'] ?? null;
$rej = ($tx['status'] ?? '') === 'Rejected';
$card = static function (string $title, array $rows) use ($e) {
    echo '<div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4"><h2 class="font-bold uppercase text-sm mb-3 text-primary">' . esc($title) . '</h2><dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">';
    foreach ($rows as $k => $v) {
        echo '<div><dt class="text-xs text-gray-500">' . esc($k) . '</dt><dd class="font-medium">' . $e($v) . '</dd></div>';
    }
    echo '</dl></div>';
};
$photo = ! empty($tx['photo']) ? base_url($tx['photo']) : base_url('assets/images/head_placeholder.png');
?>

<div class="-m-2 space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold uppercase tracking-wide">Transaction Info</h1>
        <a href="<?= base_url('monitoring') ?>" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-semibold hover:bg-gray-100 dark:hover:bg-gray-800">Back</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 flex flex-col items-center">
            <img src="<?= esc($photo) ?>" onerror="this.style.opacity=.35;this.removeAttribute('onerror')" alt="photo" class="max-h-56 rounded-lg object-contain">
            <span class="mt-3 text-xs px-2 py-0.5 rounded bg-primary/10 text-primary font-semibold"><?= esc($tx['pass_type']) ?></span>
            <span class="mt-2 text-xs px-2 py-0.5 rounded font-semibold <?= $rej ? 'bg-red-100 text-red-700' : (($tx['status'] ?? '') === 'Gate In' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700') ?>"><?= esc($tx['status']) ?></span>
        </div>
        <div class="lg:col-span-2 space-y-4">
            <?php $card('Person', ['Name' => $tx['name'], 'IC / Passport No' => $tx['ic'], 'Type Of Pass' => $tx['pass_type'], 'Card ID' => $tx['card_no'],
                'Type' => $tx['driver_type'] ?? null, 'Is Driver' => ($tx['driver_type'] ?? '') === 'DRIVER' ? 'YES' : 'NO']); ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <?php
        // KPK transaction-info: each side shows its own location / lane, whether it was opened by hand and why
        $manualIn  = ($tx['mode_in'] ?? '') === 'MANUAL';
        $manualOut = ($tx['mode_out'] ?? '') === 'MANUAL';
        $card('Gate In', $in === null ? ['Status' => 'No gate in'] : ['Date' => $d($in), 'Time' => $t($in), 'Location' => $tx['location'], 'Lane' => $tx['in_lane'],
            'Type Of Vehicle' => $tx['vehicle_type'] ?? null, 'Plate Number' => $tx['plate_no'], 'Mode' => $tx['mode_in'] ?? null,
            'Manual' => $manualIn ? 'YES' : 'NO', 'Reason' => $manualIn ? $tx['manual_reason'] : null]);
        $card('Gate Out', $rej ? ['Status' => 'Entry denied - no gate out'] : ($out === null ? ['Status' => 'Still inside'] : ['Date' => $d($out), 'Time' => $t($out),
            'Location' => ($tx['out_location'] ?? '') !== '' ? $tx['out_location'] : $tx['location'], 'Lane' => $tx['out_lane'],
            'Type Of Vehicle' => $tx['vehicle_type'] ?? null, 'Plate Number' => $tx['plate_no'], 'Mode' => $tx['mode_out'] ?? null,
            'Manual' => $manualOut ? 'YES' : 'NO', 'Reason' => $manualOut ? $tx['manual_reason'] : null]));
        ?>
    </div>

    <?php if (! empty($vehicle)): ?>
        <?php $card('Vehicle Info', [
            'Vehicle Name' => $vehicle['name'] ?? null,
            'Vehicle Type' => $vehicle['vehicle_type_name'] ?? null,
            'Plate Number' => $vehicle['plate_no'] ?? null,
            'Year Made'    => $vehicle['year_made'] ?? null,
            'Colour'       => $vehicle['colour'] ?? null,
            'Brand'        => $vehicle['brand'] ?? null,
            'Road Tax Expiry' => isset($vehicle['road_tax_expiry']) ? $d($vehicle['road_tax_expiry']) : null,
            'Insurance Expiry' => isset($vehicle['insurance_expiry']) ? $d($vehicle['insurance_expiry']) : null,
        ]); ?>
    <?php elseif (! empty($tx['plate_no'])): ?>
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 text-sm text-gray-500">No registered vehicle found for plate <b><?= esc($tx['plate_no']) ?></b>.</div>
    <?php endif; ?>
</div>

<?= view('staffs/_layout_bottom') ?>
