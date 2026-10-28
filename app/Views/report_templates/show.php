<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php
$q     = http_build_query(array_filter(['date_from' => $from, 'date_to' => $to]));
$input = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm';
?>
<div class="-m-2 space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold uppercase tracking-wide"><?= esc($tpl['name']) ?></h1>
            <p class="text-sm text-gray-500">Preview report sections and choose a reporting period. <?= esc($rb->scheduleLabel($tpl)) ?>.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= base_url('report/templates') ?>" class="inline-flex items-center gap-1 px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-semibold hover:bg-gray-100 dark:hover:bg-gray-800"><span class="material-symbols-outlined text-[18px]">arrow_back</span> Back</a>
            <a href="<?= base_url('report/templates/' . $tpl['id'] . '/edit') ?>" class="inline-flex items-center gap-1 px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-semibold hover:bg-gray-100 dark:hover:bg-gray-800"><span class="material-symbols-outlined text-[18px]">edit</span> Edit Template</a>
            <a href="<?= base_url('report/templates/' . $tpl['id'] . '/export' . ($q ? '?' . $q : '')) ?>" class="inline-flex items-center gap-1 px-4 py-2 rounded bg-primary text-white text-sm font-semibold hover:opacity-90"><span class="material-symbols-outlined text-[18px]">file_download</span> Download</a>
            <form method="post" action="<?= base_url('report/templates/' . $tpl['id'] . '/send') ?>">
                <?= csrf_field() ?><input type="hidden" name="date_from" value="<?= esc($from ?? '') ?>"><input type="hidden" name="date_to" value="<?= esc($to ?? '') ?>">
                <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 rounded bg-green-600 text-white text-sm font-semibold hover:opacity-90"><span class="material-symbols-outlined text-[18px]">send</span> Send Email</button>
            </form>
        </div>
    </div>

    <?= view('report_templates/_flash') ?>

    <form method="get" class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 flex flex-wrap items-end gap-3">
        <div><label class="block text-xs text-gray-500 mb-1">Date From</label><input type="date" name="date_from" value="<?= esc($from ?? '') ?>" class="<?= $input ?>"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Date To</label><input type="date" name="date_to" value="<?= esc($to ?? '') ?>" class="<?= $input ?>"></div>
        <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 rounded bg-primary text-white text-sm font-semibold"><span class="material-symbols-outlined text-[18px]">filter_alt</span> Filter</button>
        <a href="<?= base_url('report/templates/' . $tpl['id']) ?>" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-semibold">Reset</a>
    </form>

    <?php foreach ($data as $s): ?>
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800 flex justify-between"><h2 class="font-bold uppercase text-sm text-primary"><?= esc($s['label']) ?></h2><span class="text-xs text-gray-500"><?= count($s['rows']) ?> record(s)</span></div>
            <div class="overflow-x-auto max-h-[480px]">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300 sticky top-0">
                        <tr><th class="p-2">No</th><?php foreach ($s['columns'] as $l): ?><th class="p-2"><?= esc($l) ?></th><?php endforeach; ?></tr>
                    </thead>
                    <tbody>
                    <?php if (! $s['rows']): ?><tr><td colspan="<?= count($s['columns']) + 1 ?>" class="p-4 text-center text-gray-500">No records found for this period.</td></tr><?php endif; ?>
                    <?php foreach ($s['rows'] as $i => $r): ?>
                        <tr class="border-t border-gray-100 dark:border-gray-800"><td class="p-2"><?= $i + 1 ?></td><?php foreach ($r as $v): ?><td class="p-2"><?= esc((string) $v) ?: '-' ?></td><?php endforeach; ?></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?= view('staffs/_layout_bottom') ?>
