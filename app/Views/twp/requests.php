<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php
$col   = static fn(string $k): bool => list_col('twp_request_list', $k);
$badge = static fn($s) => ['Approved' => 'bg-green-100 text-green-700', 'Rejected' => 'bg-red-100 text-red-700'][$s] ?? 'bg-yellow-100 text-yellow-700';
$input = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm';
?>
<div class="-m-2 space-y-4">
    <?= view('twp/_header', ['title' => 'TWP Request List', 'listKey' => 'twp_request_list',
        'actions' => '<a href="' . base_url('twp/requests/new') . '" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors"><span class="material-icons text-sm mr-1">add</span>Request</a>',
        'cards' => [
            ['label' => 'Total Requests', 'value' => $stats['total'], 'sub' => 'All TWP requests', 'color' => 'indigo', 'icon' => 'engineering'],
            ['label' => 'Pending', 'value' => $stats['pending'], 'sub' => 'Awaiting approval', 'color' => 'orange', 'icon' => 'pending_actions'],
            ['label' => 'Approved', 'value' => $stats['approved'], 'sub' => 'Passes created', 'color' => 'green', 'icon' => 'check_circle'],
            ['label' => 'Rejected', 'value' => $stats['rejected'], 'sub' => 'Not approved', 'color' => 'red', 'icon' => 'cancel'],
        ]]) ?>

    <form method="get" class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 flex flex-wrap items-end gap-3">
        <input name="q" value="<?= esc($q) ?>" placeholder="App No, company, applicant, IC / passport" class="<?= $input ?> flex-1 min-w-[220px]">
        <select name="status" class="<?= $input ?>"><option value="">All Status</option>
            <?php foreach (['Pending', 'Approved', 'Rejected'] as $s): ?><option <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select>
        <select name="sort" class="<?= $input ?>">
            <?php foreach (['' => 'Date Time DESC', 'dt-a' => 'Date Time ASC', 'cn-a' => 'Company Name A-Z', 'cn-d' => 'Company Name Z-A'] as $k => $l): ?><option value="<?= $k ?>" <?= $sort === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select>
        <button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Search</button>
    </form>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                <tr><th class="p-3">No</th><th class="p-3">App No</th><?php if ($col('app_date')): ?><th class="p-3">App Date</th><?php endif; ?><?php if ($col('receipt_no')): ?><th class="p-3">Receipt No</th><?php endif; ?><?php if ($col('company')): ?><th class="p-3">Company</th><?php endif; ?><th class="p-3">Applicant Name</th><?php if ($col('contact_no')): ?><th class="p-3">Contact No</th><?php endif; ?><?php if ($col('workers')): ?><th class="p-3">Workers</th><?php endif; ?><?php if ($col('date_in_out')): ?><th class="p-3">Date In / Out</th><?php endif; ?><?php if ($col('payment_status')): ?><th class="p-3">Payment Status</th><?php endif; ?><th class="p-3">Status</th></tr>
            </thead>
            <tbody>
            <?php if (! $rows): ?><tr><td colspan="<?= list_col_count('twp_request_list') ?>" class="p-6 text-center text-gray-500">No Record</td></tr><?php endif; ?>
            <?php foreach ($rows as $i => $r): ?>
                <tr onclick="location.href='<?= base_url('twp/requests/' . $r['id']) ?>'" class="border-t border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer" title="View request">
                    <td class="p-3"><?= $i + 1 ?></td>
                    <td class="p-3 font-semibold"><a href="<?= base_url('twp/requests/' . $r['id']) ?>" class="text-primary hover:underline"><?= esc($r['app_no']) ?></a></td>
                    <?php if ($col('app_date')): ?><td class="p-3"><?= date('d/m/Y H:i', strtotime($r['app_date'])) ?></td><?php endif; ?>
                    <?php if ($col('receipt_no')): ?><td class="p-3"><?= esc($r['receipt_no'] ?: '-') ?></td><?php endif; ?>
                    <?php if ($col('company')): ?><td class="p-3"><?= esc($r['company_name']) ?></td><?php endif; ?>
                    <td class="p-3"><?= esc($r['full_name']) ?></td>
                    <?php if ($col('contact_no')): ?><td class="p-3"><?= esc($r['contact_no']) ?></td><?php endif; ?>
                    <?php if ($col('workers')): ?><td class="p-3 text-center"><?= (int) $r['workers'] ?></td><?php endif; ?>
                    <?php if ($col('date_in_out')): ?><td class="p-3 text-xs"><?= date('d/m/Y H:i', strtotime($r['date_from'])) ?><br><?= date('d/m/Y H:i', strtotime($r['date_to'])) ?></td><?php endif; ?>
                    <?php if ($col('payment_status')): ?><td class="p-3"><?= esc($r['payment_status']) ?></td><?php endif; ?>
                    <td class="p-3"><span class="text-xs px-2 py-0.5 rounded font-semibold <?= $badge($r['status']) ?>"><?= esc($r['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= view('staffs/_layout_bottom') ?>
