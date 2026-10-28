<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php
$dt    = static fn($v) => $v ? date('d/m/Y H:i', strtotime((string) $v)) : '-';
$e     = static fn($v) => ($v === null || $v === '') ? '-' : esc((string) $v);
$input = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm';
$card  = static function (string $title, array $rows) use ($e) {
    echo '<div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4"><h2 class="font-bold uppercase text-sm mb-3 text-primary">' . esc($title) . '</h2><dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">';
    foreach ($rows as $k => $v) {
        echo '<div><dt class="text-xs text-gray-500">' . esc($k) . '</dt><dd class="font-medium">' . $e($v) . '</dd></div>';
    }
    echo '</dl></div>';
};
$canDecide = in_array($r['status'], ['Pending', 'Rejected'], true);
?>
<div class="-m-2 space-y-4" x-data="{ modal: '' }">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-bold uppercase tracking-wide"><?= esc($r['app_no']) ?>
            <span class="ml-2 text-xs px-2 py-0.5 rounded font-semibold <?= ['Approved' => 'bg-green-100 text-green-700', 'Rejected' => 'bg-red-100 text-red-700'][$r['status']] ?? 'bg-yellow-100 text-yellow-700' ?>"><?= esc($r['status']) ?></span></h1>
        <div class="flex gap-2">
            <a href="<?= base_url('twp/requests') ?>" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-semibold">Back</a>
            <?php if ($canDecide): ?><button type="button" @click="modal = 'approve'" class="px-4 py-2 rounded bg-green-600 text-white text-sm font-semibold">Approve</button><?php endif; ?>
            <button type="button" @click="modal = 'reject'" class="px-4 py-2 rounded bg-red-600 text-white text-sm font-semibold">Reject</button>
        </div>
    </div>
    <?= view('report_templates/_flash') ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <?php $card('Applicant Info', ['Company' => $r['company_name'], 'Company Reg No' => $r['company_reg_id'], 'Applicant Name' => $r['full_name'], 'Resident' => $r['resident'],
            $r['resident'] === 'FOREIGNER' ? 'Passport Number' : 'IC Number' => $r['resident'] === 'FOREIGNER' ? $r['passport_no'] : $r['ic_no'],
            'Contact Number' => $r['contact_no'], 'Email' => $r['email'], 'Requested By' => $by($r['requested_by'])]); ?>
        <?php $card('Application Info', ['Date Of Application' => $dt($r['app_date']), 'Type Of Work' => $r['type_of_work'], 'Date and Time In' => $dt($r['date_from']),
            'Date and Time Out' => $dt($r['date_to']), 'Location Of Work' => str_replace(',', ', ', (string) $r['location_access']), 'Remark' => $r['remark'],
            'Reject Reason' => $r['reject_reason'], 'Reviewed By' => $r['reviewed_by'] ? $by($r['reviewed_by']) . ' (' . $dt($r['reviewed_at']) . ')' : null]); ?>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 space-y-3">
        <h2 class="font-bold uppercase text-sm text-primary">Payment Info</h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
            <div><div class="text-xs text-gray-500">Amount (RM)</div><div class="font-bold"><?= number_format((float) $r['amount'], 2) ?></div></div>
            <div><div class="text-xs text-gray-500">Payment Status</div><div class="font-medium"><?= $e($r['payment_status']) ?></div></div>
            <div><div class="text-xs text-gray-500">Payment Method</div><div class="font-medium"><?= $e($r['payment_type']) ?></div></div>
            <div><div class="text-xs text-gray-500">Receipt No</div><div class="font-medium"><?= $e($r['receipt_no']) ?></div></div>
            <div><div class="text-xs text-gray-500">Ref</div><div class="font-medium"><?= $e($r['payment_ref']) ?></div></div>
        </div>
        <?php if ($r['payment_status'] === 'Pending'): ?>
            <div class="flex flex-wrap items-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                <form method="post" action="<?= base_url('twp/requests/' . $r['id'] . '/payment') ?>" class="flex flex-wrap items-end gap-2">
                    <?= csrf_field() ?>
                    <div><label class="block text-xs text-gray-500 mb-1">Payment Method</label><select name="payment_type" class="<?= $input ?>"><option>CASH</option><option>CHEQUE</option><option>CREDIT CARD</option><option>BANK TRANSFER</option></select></div>
                    <div><label class="block text-xs text-gray-500 mb-1">Receipt No *</label><input name="receipt_no" required class="<?= $input ?>"></div>
                    <div><label class="block text-xs text-gray-500 mb-1">Cheque No / Ref</label><input name="payment_ref" class="<?= $input ?>"></div>
                    <button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Record Payment</button>
                </form>
                <?php if ($onlinePay): ?>
                    <form method="post" action="<?= base_url('twp/requests/' . $r['id'] . '/pay-online') ?>"><?= csrf_field() ?><button class="px-4 py-2 rounded bg-green-600 text-white text-sm font-semibold">Pay Online (FPX)</button></form>
                <?php else: ?>
                    <span class="text-xs text-gray-500">Online FPX payment runs in the Java payment service, which is not connected yet.</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-x-auto">
        <div class="px-4 py-3 font-bold uppercase text-sm text-primary">Workers (<?= count($workers) ?>)</div>
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300"><tr><th class="p-2">No</th><th class="p-2">Name</th><th class="p-2">Resident</th><th class="p-2">IC / Passport No</th><th class="p-2">Contact No</th><th class="p-2">Vehicle No</th><th class="p-2">Pass No</th><th class="p-2">Card</th><th class="p-2">Status</th></tr></thead>
            <tbody>
            <?php foreach ($workers as $i => $w): ?>
                <tr class="border-t border-gray-100 dark:border-gray-800"><td class="p-2"><?= $i + 1 ?></td><td class="p-2"><?= esc($w['name']) ?></td><td class="p-2"><?= esc($w['resident']) ?></td>
                    <td class="p-2"><?= $e($w['ic_no'] ?: $w['passport_no']) ?></td><td class="p-2"><?= $e($w['contact_no']) ?></td><td class="p-2"><?= $e($w['vehicle_no']) ?></td>
                    <td class="p-2"><?= $e($w['pass_no']) ?></td><td class="p-2"><?= $e($w['card_no'] ? $w['card_no'] . ' (' . $w['card_status'] . ')' : $w['card_status']) ?></td><td class="p-2"><?= esc($w['status']) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div x-show="modal" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="modal = ''">
        <form method="post" :action="modal === 'approve' ? '<?= base_url('twp/requests/' . $r['id'] . '/approve') ?>' : '<?= base_url('twp/requests/' . $r['id'] . '/reject') ?>'" class="bg-white dark:bg-gray-900 rounded-lg w-full max-w-md p-5 space-y-3">
            <?= csrf_field() ?>
            <h3 class="font-bold text-lg" x-text="modal === 'approve' ? 'Approval Confirmation' : 'Reject Confirmation'"></h3>
            <div x-show="modal === 'reject'"><label class="block text-sm mb-1">Reject Reason *</label>
                <input name="reject_reason" list="twpReasons" :required="modal === 'reject'" class="<?= $input ?>">
                <datalist id="twpReasons"><?php foreach ($reasons as $rs): ?><option value="<?= esc($rs) ?>"><?php endforeach; ?></datalist></div>
            <div><label class="block text-sm mb-1">Remark</label><textarea name="remark" rows="3" class="<?= $input ?>"></textarea></div>
            <div class="flex justify-end gap-2"><button type="button" @click="modal = ''" class="px-4 py-2 rounded border text-sm">Cancel</button>
                <button class="px-4 py-2 rounded text-white text-sm font-semibold" :class="modal === 'approve' ? 'bg-green-600' : 'bg-red-600'">Confirm</button></div>
        </form>
    </div>
</div>

<?= view('staffs/_layout_bottom') ?>
