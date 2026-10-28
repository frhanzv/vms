<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php
$dt    = static fn($v) => $v ? date('d/m/Y H:i', strtotime((string) $v)) : '-';
$input = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm';
?>
<div class="-m-2 space-y-4" x-data="{ issue: null }">
    <?= view('twp/_tabs') ?>

    <form method="get" class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 flex gap-3">
        <input name="q" value="<?= esc($q) ?>" placeholder="Name, IC / passport, pass no, company or card no" class="<?= $input ?> flex-1">
        <button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Search</button>
    </form>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                <tr><th class="p-3">Temporary Work Pass No</th><th class="p-3">Person</th><th class="p-3">IC / Passport</th><th class="p-3">Company</th><th class="p-3">Vehicle No</th><th class="p-3">Valid</th><th class="p-3">Location</th><th class="p-3">Card Status</th><th class="p-3">Card Details</th></tr>
            </thead>
            <tbody>
            <?php if (! $rows): ?><tr><td colspan="9" class="p-6 text-center text-gray-500">No Record</td></tr><?php endif; ?>
            <?php foreach ($rows as $w): $expired = $w['date_to'] && strtotime($w['date_to']) < time(); ?>
                <tr class="border-t border-gray-100 dark:border-gray-800">
                    <td class="p-3 font-semibold"><?= esc($w['pass_no']) ?></td>
                    <td class="p-3"><?= esc($w['name']) ?></td>
                    <td class="p-3"><?= esc($w['ic_no'] ?: $w['passport_no']) ?></td>
                    <td class="p-3"><?= esc($w['company_name']) ?></td>
                    <td class="p-3"><?= esc($w['vehicle_no'] ?: '-') ?></td>
                    <td class="p-3 text-xs <?= $expired ? 'text-red-600' : '' ?>"><?= $dt($w['date_from']) ?><br><?= $dt($w['date_to']) ?><?= $expired ? ' (expired)' : '' ?></td>
                    <td class="p-3 text-xs"><?= esc(str_replace(',', ', ', (string) $w['location_access'])) ?></td>
                    <td class="p-3"><span class="text-xs px-2 py-0.5 rounded font-semibold <?= $w['card_status'] === 'Issued' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' ?>"><?= esc($w['card_status']) ?></span></td>
                    <td class="p-3">
                        <?php if ($w['card_status'] === 'Issued'): ?>
                            <span class="mr-2"><?= esc($w['card_no']) ?></span>
                            <form method="post" action="<?= base_url('twp/passes/' . $w['id'] . '/return') ?>" class="inline" onsubmit="return confirm('Return this card?')"><?= csrf_field() ?>
                                <button class="text-xs px-2 py-1 rounded border border-red-300 text-red-600 font-semibold">Return Card</button></form>
                        <?php elseif (! $expired): ?>
                            <button type="button" @click="issue = <?= (int) $w['id'] ?>" class="text-xs px-2 py-1 rounded bg-primary text-white font-semibold">Issue Card</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div x-show="issue" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="issue = null">
        <form method="post" :action="'<?= base_url('twp/passes') ?>/' + issue + '/issue'" class="bg-white dark:bg-gray-900 rounded-lg w-full max-w-md p-5 space-y-3">
            <?= csrf_field() ?>
            <h3 class="font-bold text-lg">Card Details</h3>
            <div><label class="block text-sm mb-1">Scan Card (Card ID)</label><input name="card_epc" autofocus placeholder="Tap the card on the reader" class="<?= $input ?> w-full"></div>
            <div class="text-center text-xs text-gray-500">or pick a free card</div>
            <div><select name="card_id" class="<?= $input ?> w-full"><option value="">Select card</option>
                <?php foreach ($freeCards as $c): ?><option value="<?= (int) $c['id'] ?>"><?= esc($c['card_no']) ?> (<?= esc($c['card_id']) ?>)</option><?php endforeach; ?></select>
                <?php if (! $freeCards): ?><span class="text-xs text-red-600">No free TWP cards. Add cards under TWP Card.</span><?php endif; ?></div>
            <div class="flex justify-end gap-2"><button type="button" @click="issue = null" class="px-4 py-2 rounded border text-sm">Cancel</button><button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Save</button></div>
        </form>
    </div>
</div>

<?= view('staffs/_layout_bottom') ?>
