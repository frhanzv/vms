<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php
$dt    = static fn($v) => $v ? date('d/m/Y H:i', strtotime((string) $v)) : '-';
$input = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm';
$tiles = [['Total Check In', $sum['checked_in'], 'login', 'text-green-600'], ['Total Check Out', $sum['checked_out'], 'logout', 'text-blue-600'],
    ['In Premise', $sum['in_premise'], 'groups', 'text-primary'], ['Near Out Of Window', $sum['near_window'], 'schedule', 'text-yellow-600'],
    ['Out Of Window', $sum['out_window'], 'warning', 'text-red-600'], ['Denied Entry', $sum['denied'], 'block', 'text-red-600'],
    ['Average Time In Location (hrs)', $sum['avg_hours'] ?? '-', 'timer', 'text-gray-600']];
?>
<div class="-m-2 space-y-4">
    <?= view('twp/_tabs') ?>

    <form method="get" class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 flex flex-wrap items-end gap-3">
        <div><label class="block text-xs text-gray-500 mb-1">Date From</label><input type="date" name="date_from" value="<?= esc($from) ?>" class="<?= $input ?>"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Date To</label><input type="date" name="date_to" value="<?= esc($to) ?>" class="<?= $input ?>"></div>
        <button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Update Now</button>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-7 gap-3">
        <?php foreach ($tiles as [$l, $v, $icon, $cls]): ?>
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                <span class="material-symbols-outlined <?= $cls ?>"><?= $icon ?></span>
                <div class="text-2xl font-bold"><?= esc((string) ($v ?? 0)) ?></div><div class="text-xs text-gray-500 uppercase"><?= $l ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-x-auto">
            <div class="px-4 py-3 font-bold uppercase text-sm text-primary">By Location</div>
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300"><tr><th class="p-2">Location</th><th class="p-2">In</th><th class="p-2">Out</th><th class="p-2">Inside</th><th class="p-2">OOW</th></tr></thead>
                <tbody>
                <?php if (! $byLoc): ?><tr><td colspan="5" class="p-4 text-center text-gray-500">No Record</td></tr><?php endif; ?>
                <?php foreach ($byLoc as $l): ?><tr class="border-t border-gray-100 dark:border-gray-800"><td class="p-2"><?= esc($l['location'] ?: '-') ?></td><td class="p-2"><?= (int) $l['checked_in'] ?></td><td class="p-2"><?= (int) $l['checked_out'] ?></td><td class="p-2"><?= (int) $l['in_premise'] ?></td><td class="p-2 text-red-600"><?= (int) $l['out_window'] ?></td></tr><?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-lg shadow overflow-x-auto">
            <div class="px-4 py-3 font-bold uppercase text-sm text-primary">Currently In Premise</div>
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300"><tr><th class="p-2">Name</th><th class="p-2">IC / Passport No</th><th class="p-2">Company</th><th class="p-2">Location</th><th class="p-2">Lane No</th><th class="p-2">Time In</th><th class="p-2">Pass Ends</th></tr></thead>
                <tbody>
                <?php if (! $inside): ?><tr><td colspan="7" class="p-4 text-center text-gray-500">No Record</td></tr><?php endif; ?>
                <?php foreach ($inside as $x): $oow = $x['date_to'] && strtotime($x['date_to']) < time(); ?>
                    <tr class="border-t border-gray-100 dark:border-gray-800 <?= $oow ? 'bg-red-50 dark:bg-red-900/20' : '' ?>"><td class="p-2"><?= esc($x['name']) ?></td><td class="p-2"><?= esc($x['ic']) ?></td><td class="p-2"><?= esc($x['company_name']) ?></td>
                        <td class="p-2"><?= esc($x['location'] ?: '-') ?></td><td class="p-2"><?= esc($x['in_lane'] ?: '-') ?></td><td class="p-2"><?= $dt($x['check_in_time']) ?></td><td class="p-2 <?= $oow ? 'text-red-600 font-semibold' : '' ?>"><?= $dt($x['date_to']) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('staffs/_layout_bottom') ?>
