<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php
$dt    = static fn($v) => $v ? date('d/m/Y H:i', strtotime((string) $v)) : '-';
$input = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm';
$tabs  = ['twp' => 'TWP', 'premise' => 'TWP In Premise', 'oow' => 'TWP Out Of Window'];
$qs    = static fn(array $extra) => http_build_query(array_filter(array_merge(['tab' => $tab, 'q' => $q, 'company' => $company, 'date_from' => $from, 'date_to' => $to, 'sort' => $sort], $extra)));
?>
<div class="-m-2 space-y-4">
    <?= view('twp/_tabs') ?>

    <div class="flex gap-1">
        <?php foreach ($tabs as $k => $l): ?>
            <a href="<?= base_url('twp/report?tab=' . $k) ?>" class="px-3 py-1.5 rounded text-sm font-semibold <?= $tab === $k ? 'bg-primary text-white' : 'bg-white dark:bg-gray-900 shadow text-gray-600 dark:text-gray-300' ?>"><?= $l ?></a>
        <?php endforeach; ?>
    </div>

    <form method="get" class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 flex flex-wrap items-end gap-3">
        <input type="hidden" name="tab" value="<?= esc($tab) ?>">
        <input name="q" value="<?= esc($q) ?>" placeholder="Search by IC, Passport, Company or Full Name" class="<?= $input ?> flex-1 min-w-[220px]">
        <input name="company" value="<?= esc($company) ?>" placeholder="Company Name" class="<?= $input ?>">
        <?php if ($tab !== 'premise'): ?>
            <div><label class="block text-xs text-gray-500 mb-1">Date From</label><input type="date" name="date_from" value="<?= esc($from) ?>" class="<?= $input ?>"></div>
            <div><label class="block text-xs text-gray-500 mb-1">Date To</label><input type="date" name="date_to" value="<?= esc($to) ?>" class="<?= $input ?>"></div>
        <?php endif; ?>
        <select name="sort" class="<?= $input ?>">
            <?php foreach (['' => 'Date Time DESC', 'dt-a' => 'Date Time ASC', 'n-a' => 'Name A-Z', 'n-d' => 'Name Z-A'] as $k => $l): ?><option value="<?= $k ?>" <?= $sort === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select>
        <button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Search</button>
        <a href="<?= base_url('twp/report?' . $qs(['export' => 1])) ?>" class="px-4 py-2 rounded bg-green-600 text-white text-sm font-semibold">Export</a>
    </form>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-x-auto">
        <div class="px-4 py-3 flex justify-between text-sm"><span><?= count($rows) ?> record(s)</span>
            <?php if ($tab !== 'premise'): ?><span class="font-semibold">Total Duration (Manhours): <?= $manhours ?></span><?php endif; ?></div>
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                <tr><th class="p-2">No</th><th class="p-2">Full Name</th><th class="p-2">IC No / Passport No</th><th class="p-2">Company Name</th><th class="p-2">Contact No</th>
                    <th class="p-2"><?= $tab === 'premise' ? 'Current Location' : 'Location' ?></th><th class="p-2">Registration Date</th><th class="p-2">Time In</th>
                    <?php if ($tab !== 'premise'): ?><th class="p-2">Time Out</th><th class="p-2">Duration (hrs)</th><?php endif; ?><th class="p-2">Pass Valid To</th></tr>
            </thead>
            <tbody>
            <?php if (! $rows): ?><tr><td colspan="11" class="p-6 text-center text-gray-500">No Record</td></tr><?php endif; ?>
            <?php foreach ($rows as $i => $r): ?>
                <tr class="border-t border-gray-100 dark:border-gray-800"><td class="p-2"><?= $i + 1 ?></td><td class="p-2"><?= esc($r['name']) ?></td><td class="p-2"><?= esc($r['ic']) ?></td>
                    <td class="p-2"><?= esc($r['company_name']) ?></td><td class="p-2"><?= esc($r['contact_no'] ?: '-') ?></td><td class="p-2"><?= esc($r['location'] ?: '-') ?></td>
                    <td class="p-2"><?= $dt($r['reg_date']) ?></td><td class="p-2"><?= $dt($r['check_in_time']) ?></td>
                    <?php if ($tab !== 'premise'): ?><td class="p-2"><?= $dt($r['check_out_time']) ?></td><td class="p-2"><?= esc((string) $r['hours']) ?></td><?php endif; ?>
                    <td class="p-2"><?= $dt($r['date_to']) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= view('staffs/_layout_bottom') ?>
