<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php
// KPK port-pass/work-pass-list "Work Pass Info"
$v       = static fn($x) => esc((string) ($x ?? '') !== '' ? $x : '-');
$box     = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm uppercase min-h-[40px]';
$input   = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm';
$field   = static fn(string $label, $val, string $span = '') => '<div class="' . $span . '"><label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">' . $label . '</label><div class="' . 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm uppercase min-h-[40px]' . '">' . $val . '</div></div>';
$local   = strtoupper((string) ($w['resident'] ?: $w['request_resident'])) !== 'FOREIGNER';
$issued  = $w['card_status'] === 'Issued';
$expired = $w['date_to'] && strtotime((string) $w['date_to']) < time();
$dtl     = static fn($x) => $x ? date('Y-m-d\TH:i', strtotime((string) $x)) : '';
$paid    = in_array($w['payment_status'], ['Paid', 'None'], true) || ! empty($w['receipt_no']);
?>
<div class="-m-2 space-y-4" x-data="{ modal: '' }">
    <?= view('twp/_header', ['title' => 'Work Pass Info',
        'actions' => '<a href="' . base_url('twp/passes') . '" title="Close" class="w-9 h-9 rounded-full bg-gray-700 text-white flex items-center justify-center"><span class="material-icons text-[20px]">close</span></a>']) ?>

    <form method="post" action="<?= base_url('twp/passes/' . $w['id'] . '/dates') ?>" class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 space-y-6">
        <?= csrf_field() ?>
        <div>
            <h2 class="font-bold uppercase text-sm pb-2 mb-4 border-b-2 border-gray-700 dark:border-gray-400">Application Info</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <?= $field('Application Number:', $v($w['pass_no'] ?: $w['app_no'])) ?>
                <?= $field('Date Of Application:', $w['app_date'] ? date('d/m/Y', strtotime($w['app_date'])) : '-') ?>
                <?= $field('Resident:', $local ? 'LOCAL' : 'FOREIGNER') ?>
                <?= $field('Type Of Work:', $v($w['sub_type'] ?: $w['type_of_work'])) ?>
                <?= $field('Company:', $v($w['company_name']), 'md:col-span-2') ?>
                <?= $field('Requested By:', $v($requestedBy)) ?>
                <div></div>
                <?php if (! empty($w['remark'])): ?><?= $field('Remark:', $v($w['remark']), 'md:col-span-4') ?><?php endif; ?>
                <div><label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Date and Time In:</label><input type="datetime-local" name="date_from" required value="<?= $dtl($w['date_from']) ?>" class="<?= $input ?>"></div>
                <div><label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Date and Time Out:</label><input type="datetime-local" name="date_to" required value="<?= $dtl($w['date_to']) ?>" class="<?= $input ?> <?= $expired ? 'text-red-600' : '' ?>"></div>
                <div class="md:col-span-4">
                    <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Location Of Work:</label>
                    <div class="flex flex-wrap gap-x-6 gap-y-1 pl-4">
                        <?php foreach (array_filter(array_map('trim', explode(',', (string) $w['location_access'])), 'strlen') as $loc): ?>
                            <label class="flex items-center gap-2 text-sm uppercase"><input type="checkbox" checked disabled class="rounded text-primary"> <?= esc($loc) ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <h2 class="font-bold uppercase text-sm pb-2 mb-4 border-b-2 border-gray-700 dark:border-gray-400">Person</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <?= $field('Name:', $v($w['name'])) ?>
                <?= $local ? $field('IC Number:', $v($w['ic_no'])) : $field('Passport Number:', $v($w['passport_no'])) ?>
                <?= $field('Contact Number:', $v($w['contact_no'])) ?>
                <?= $field('Vehicle Number:', $v($w['vehicle_no'])) ?>
                <?= $field('Card Status:', $v($w['card_status'])) ?>
                <?php if ($issued): ?><?= $field('Card No:', $v($w['card_no'])) ?><?php endif; ?>
            </div>
        </div>

        <div class="flex flex-wrap justify-between gap-3 pt-2">
            <div class="flex flex-wrap gap-2">
                <a href="<?= base_url('twp/passes') ?>" class="px-4 py-2 rounded bg-amber-400 hover:bg-amber-500 text-white text-sm font-semibold">Back</a>
                <?php if ($w['payment_status']): ?><button type="button" @click="modal = 'receipt'" class="px-4 py-2 rounded bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold flex items-center gap-1"><span class="material-icons text-[16px]">receipt_long</span>Receipt</button><?php endif; ?>
                <button class="px-4 py-2 rounded bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold">Update</button>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="modal = 'qr'" class="px-4 py-2 rounded bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold">QR Code</button>
                <?php if ($issued): ?>
                    <button type="button" @click="modal = 'return'" class="px-4 py-2 rounded bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold">Return Card</button>
                <?php elseif (! $expired): ?>
                    <button type="button" @click="modal = 'issue'" class="px-4 py-2 rounded bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold">Issue Card</button>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <!-- QR Code -->
    <div x-show="modal === 'qr'" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="modal = ''">
        <div class="bg-white dark:bg-gray-900 rounded-lg w-full max-w-sm p-5 text-center space-y-3">
            <h3 class="font-bold text-lg">Scan QR Code for details</h3>
            <img src="<?= esc($qrUrl, 'attr') ?>" alt="QR code" class="mx-auto w-56 h-56">
            <div class="font-semibold"><?= esc($w['pass_no']) ?></div>
            <button type="button" @click="modal = ''" class="px-4 py-2 rounded border text-sm">Close</button>
        </div>
    </div>

    <!-- Return Card -->
    <div x-show="modal === 'return'" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="modal = ''">
        <form method="post" action="<?= base_url('twp/passes/' . $w['id'] . '/return') ?>" class="bg-white dark:bg-gray-900 rounded-lg w-full max-w-md p-5 space-y-3">
            <?= csrf_field() ?><input type="hidden" name="from" value="info">
            <h3 class="font-bold text-lg">Return Card</h3>
            <p class="text-sm">Return card <b><?= esc($w['card_no']) ?></b> from <b><?= esc($w['name']) ?></b>?</p>
            <div class="flex justify-end gap-2"><button type="button" @click="modal = ''" class="px-4 py-2 rounded border text-sm">Cancel</button><button class="px-4 py-2 rounded bg-indigo-500 text-white text-sm font-semibold">Return</button></div>
        </form>
    </div>

    <!-- Issue Card (KPK Scan Card) -->
    <div x-show="modal === 'issue'" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="modal = ''">
        <form method="post" action="<?= base_url('twp/passes/' . $w['id'] . '/issue') ?>" class="bg-white dark:bg-gray-900 rounded-lg w-full max-w-md p-5 space-y-3">
            <?= csrf_field() ?><input type="hidden" name="from" value="info">
            <h3 class="font-bold text-lg">Scan Card</h3>
            <div><label class="block text-sm mb-1">Temporary Work Pass No</label><div class="<?= $box ?>"><?= esc($w['pass_no']) ?></div></div>
            <div><label class="block text-sm mb-1">Card ID</label><input name="card_epc" placeholder="Tap the card on the reader" class="<?= $input ?>"></div>
            <div class="text-center text-xs text-gray-500">or pick a free card</div>
            <div><select name="card_id" class="<?= $input ?>"><option value="">Select card</option>
                <?php foreach ($freeCards as $c): ?><option value="<?= (int) $c['id'] ?>"><?= esc($c['card_no']) ?> (<?= esc($c['card_id']) ?>)</option><?php endforeach; ?></select>
                <?php if (! $freeCards): ?><span class="text-xs text-red-600">No free TWP cards. Add cards under TWP Card.</span><?php endif; ?></div>
            <div class="flex justify-end gap-2"><button type="button" @click="modal = ''" class="px-4 py-2 rounded border text-sm">Cancel</button><button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Save</button></div>
        </form>
    </div>

    <!-- Receipt -->
    <div x-show="modal === 'receipt'" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="modal = ''">
        <div class="bg-white dark:bg-gray-900 rounded-lg w-full max-w-2xl p-6 space-y-4">
            <div id="twpReceipt" class="space-y-4 text-sm">
                <h3 class="font-bold text-lg text-center">OFFICIAL RECEIPT</h3>
                <div class="flex justify-between gap-4">
                    <div><div class="font-semibold uppercase"><?= esc($w['company_name']) ?></div></div>
                    <table class="text-xs"><tr><td class="pr-2 text-gray-500">Receipt No</td><td class="font-semibold"><?= $v($w['receipt_no']) ?></td></tr>
                        <tr><td class="pr-2 text-gray-500">Payment</td><td><?= $v($w['payment_type'] === 'FPX' ? 'Online banking' : $w['payment_type']) ?></td></tr>
                        <tr><td class="pr-2 text-gray-500">Ref</td><td><?= $v($w['payment_ref']) ?></td></tr>
                        <tr><td class="pr-2 text-gray-500">Date</td><td><?= $paid && $w['request_updated'] ? date('d/m/Y', strtotime($w['request_updated'])) : '-' ?></td></tr></table>
                </div>
                <table class="w-full border text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-800"><tr><th class="p-2 text-left">Being payment of</th><th class="p-2 text-left">REF</th><th class="p-2 text-right">AMOUNT (RM)</th></tr></thead>
                    <tbody><tr class="border-t"><td class="p-2">Payment For Temporary Work Pass</td><td class="p-2"><?= esc($w['app_no']) ?></td><td class="p-2 text-right"><?= number_format((float) $w['amount'], 2) ?></td></tr></tbody>
                    <tfoot><tr class="border-t font-semibold"><td class="p-2" colspan="2">Total</td><td class="p-2 text-right"><?= number_format((float) $w['amount'], 2) ?></td></tr></tfoot>
                </table>
                <div class="text-xs text-gray-500">Payment status: <?= esc($w['payment_status']) ?></div>
            </div>
            <div class="flex justify-end gap-2"><button type="button" @click="modal = ''" class="px-4 py-2 rounded border text-sm">Close</button>
                <button type="button" onclick="const w = window.open('', '_blank'); w.document.write('<html><head><title>Receipt</title><script src=&quot;https://cdn.tailwindcss.com&quot;></' + 'script></head><body class=&quot;p-8&quot;>' + document.getElementById('twpReceipt').innerHTML + '</body></html>'); w.document.close(); setTimeout(() => w.print(), 800);" class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Print</button></div>
        </div>
    </div>
</div>

<?= view('staffs/_layout_bottom') ?>
