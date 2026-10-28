<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php
$badge = static fn($s) => ['Approved' => 'bg-green-100 text-green-700', 'Rejected' => 'bg-red-100 text-red-700'][$s] ?? 'bg-yellow-100 text-yellow-700';
$input = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm';
?>
<div class="-m-2 space-y-4">
    <?= view('twp/_tabs') ?>

    <form method="get" class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 flex flex-wrap items-end gap-3">
        <input name="q" value="<?= esc($q) ?>" placeholder="App No, company, applicant, IC / passport" class="<?= $input ?> flex-1 min-w-[220px]">
        <select name="status" class="<?= $input ?>"><option value="">All Status</option>
            <?php foreach (['Pending', 'Approved', 'Rejected'] as $s): ?><option <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select>
        <select name="sort" class="<?= $input ?>">
            <?php foreach (['' => 'Date Time DESC', 'dt-a' => 'Date Time ASC', 'cn-a' => 'Company Name A-Z', 'cn-d' => 'Company Name Z-A'] as $k => $l): ?><option value="<?= $k ?>" <?= $sort === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select>
        <button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Search</button>
        <a href="<?= base_url('twp/requests/new') ?>" class="ml-auto px-4 py-2 rounded bg-green-600 text-white text-sm font-semibold">+ Request</a>
    </form>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                <tr><th class="p-3">App No</th><th class="p-3">App Date</th><th class="p-3">Company</th><th class="p-3">Applicant Name</th><th class="p-3">Contact No</th><th class="p-3">Workers</th><th class="p-3">Date In / Out</th><th class="p-3">Payment Status</th><th class="p-3">Status</th><th class="p-3"></th></tr>
            </thead>
            <tbody>
            <?php if (! $rows): ?><tr><td colspan="10" class="p-6 text-center text-gray-500">No Record</td></tr><?php endif; ?>
            <?php foreach ($rows as $r): ?>
                <tr class="border-t border-gray-100 dark:border-gray-800">
                    <td class="p-3 font-semibold"><?= esc($r['app_no']) ?></td>
                    <td class="p-3"><?= date('d/m/Y H:i', strtotime($r['app_date'])) ?></td>
                    <td class="p-3"><?= esc($r['company_name']) ?></td>
                    <td class="p-3"><?= esc($r['full_name']) ?></td>
                    <td class="p-3"><?= esc($r['contact_no']) ?></td>
                    <td class="p-3 text-center"><?= (int) $r['workers'] ?></td>
                    <td class="p-3 text-xs"><?= date('d/m/Y H:i', strtotime($r['date_from'])) ?><br><?= date('d/m/Y H:i', strtotime($r['date_to'])) ?></td>
                    <td class="p-3"><?= esc($r['payment_status']) ?></td>
                    <td class="p-3"><span class="text-xs px-2 py-0.5 rounded font-semibold <?= $badge($r['status']) ?>"><?= esc($r['status']) ?></span></td>
                    <td class="p-3"><a href="<?= base_url('twp/requests/' . $r['id']) ?>" class="text-primary"><span class="material-symbols-outlined text-[20px]">visibility</span></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= view('staffs/_layout_bottom') ?>
