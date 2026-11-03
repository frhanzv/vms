<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php
$col   = static fn(string $k): bool => list_col('twp_pass_list', $k);
$dt    = static fn($v) => $v ? date('d/m/Y H:i', strtotime((string) $v)) : '-';
$input = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm';
?>
<div class="-m-2 space-y-4">
    <?= view('twp/_header', ['title' => 'TWP Pass List', 'listKey' => 'twp_pass_list',
        'actions' => '<a href="' . base_url('twp/passes') . '?' . http_build_query(['q' => $q, 'export' => 1]) . '" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors"><span class="material-icons text-sm mr-1">file_upload</span>Export</a>',
        'cards' => [
            ['label' => 'Total Passes', 'value' => $stats['total'], 'sub' => 'Approved workers', 'color' => 'indigo', 'icon' => 'badge'],
            ['label' => 'Card Issued', 'value' => $stats['issued'], 'sub' => 'Holding a TWP card', 'color' => 'green', 'icon' => 'credit_card'],
            ['label' => 'Not Issued', 'value' => $stats['not_issued'], 'sub' => 'Waiting for a card', 'color' => 'orange', 'icon' => 'pending_actions'],
            ['label' => 'Expired', 'value' => $stats['expired'], 'sub' => 'Past the date out', 'color' => 'red', 'icon' => 'event_busy'],
        ]]) ?>

    <form method="get" class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 flex gap-3">
        <input name="q" value="<?= esc($q) ?>" placeholder="Name, IC / passport, pass no, receipt no, company, vehicle or card no" class="<?= $input ?> flex-1">
        <button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Search</button>
    </form>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="p-3">No</th>
                    <?php if ($col('app_date')): ?><th class="p-3">App Date</th><?php endif; ?>
                    <?php if ($col('receipt_no')): ?><th class="p-3">Receipt No</th><?php endif; ?>
                    <?php if ($col('company')): ?><th class="p-3">Company</th><?php endif; ?>
                    <th class="p-3">Applicant Name</th>
                    <?php if ($col('ic_passport')): ?><th class="p-3">IC / Passport No</th><?php endif; ?>
                    <?php if ($col('contact_no')): ?><th class="p-3">Contact No</th><?php endif; ?>
                    <?php if ($col('vehicle_no')): ?><th class="p-3">Vehicle No</th><?php endif; ?>
                    <?php if ($col('valid')): ?><th class="p-3">Date In / Out</th><?php endif; ?>
                    <?php if ($col('location')): ?><th class="p-3">Location</th><?php endif; ?>
                    <th class="p-3">Card Status</th>
                    <?php if ($col('status')): ?><th class="p-3">Status</th><?php endif; ?>
                    <th class="p-3">Temporary Work Pass No</th>
                </tr>
            </thead>
            <tbody>
            <?php if (! $rows): ?><tr><td colspan="<?= list_col_count('twp_pass_list') ?>" class="p-6 text-center text-gray-500">No Record</td></tr><?php endif; ?>
            <?php foreach ($rows as $i => $w): $status = \App\Controllers\Twp::passStatus($w); ?>
                <tr onclick="location.href='<?= base_url('twp/passes/' . $w['id']) ?>'" class="border-t border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer" title="View Work Pass Info">
                    <td class="p-3"><?= $i + 1 ?></td>
                    <?php if ($col('app_date')): ?><td class="p-3"><?= $w['app_date'] ? date('d/m/Y', strtotime($w['app_date'])) : '-' ?></td><?php endif; ?>
                    <?php if ($col('receipt_no')): ?><td class="p-3"><?= esc($w['receipt_no'] ?: '-') ?></td><?php endif; ?>
                    <?php if ($col('company')): ?><td class="p-3"><?= esc($w['company_name']) ?></td><?php endif; ?>
                    <td class="p-3 font-semibold uppercase"><a href="<?= base_url('twp/passes/' . $w['id']) ?>" class="text-primary hover:underline"><?= esc($w['name']) ?></a></td>
                    <?php if ($col('ic_passport')): ?><td class="p-3"><?= esc($w['ic_no'] ?: $w['passport_no']) ?></td><?php endif; ?>
                    <?php if ($col('contact_no')): ?><td class="p-3"><?= esc($w['contact_no'] ?: '-') ?></td><?php endif; ?>
                    <?php if ($col('vehicle_no')): ?><td class="p-3"><?= esc($w['vehicle_no'] ?: '-') ?></td><?php endif; ?>
                    <?php if ($col('valid')): ?><td class="p-3 text-xs <?= $status === 'Expired' ? 'text-red-600' : '' ?>"><?= $dt($w['date_from']) ?><br><?= $dt($w['date_to']) ?></td><?php endif; ?>
                    <?php if ($col('location')): ?><td class="p-3 text-xs"><?= esc(str_replace(',', ', ', (string) $w['location_access'])) ?></td><?php endif; ?>
                    <td class="p-3"><span class="text-xs px-2 py-0.5 rounded font-semibold <?= $w['card_status'] === 'Issued' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' ?>"><?= esc($w['card_status']) ?></span></td>
                    <?php if ($col('status')): ?><td class="p-3"><span class="text-xs px-2 py-0.5 rounded font-semibold <?= $status === 'Expired' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>"><?= $status ?></span></td><?php endif; ?>
                    <td class="p-3 font-semibold"><?= esc($w['pass_no']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= view('staffs/_layout_bottom') ?>
