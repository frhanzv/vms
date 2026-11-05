<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php $input = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm'; ?>
<div class="-m-2 space-y-4" x-data="{ edit: null }">
    <?= view('twp/_header', ['title' => 'Temporary Work Pass Card', 'actions' => '<button type="button" onclick="window.print()" class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">print</span> Print</button>']) ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-lg shadow overflow-x-auto">
            <div class="px-4 py-3 flex items-center justify-between"><h2 class="font-bold uppercase text-sm text-primary">List Of Temporary Work Pass Card</h2>
                <button type="button" @click="edit = { id: 0, card_id: '', card_no: '', status: 'active' }" class="px-3 py-1.5 rounded bg-primary text-white text-xs font-semibold">Create</button></div>
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300"><tr><th class="p-3">Temporary Work Pass No</th><th class="p-3">Card ID</th><th class="p-3">Status</th><th class="p-3">Issued To</th><th class="p-3"></th></tr></thead>
                <tbody>
                <?php if (! $rows): ?><tr><td colspan="5" class="p-6 text-center text-gray-500">No Record</td></tr><?php endif; ?>
                <?php foreach ($rows as $c): ?>
                    <tr class="border-t border-gray-100 dark:border-gray-800">
                        <td class="p-3 font-semibold"><?= esc($c['card_no']) ?></td><td class="p-3"><?= esc($c['card_id']) ?></td>
                        <td class="p-3"><span class="text-xs px-2 py-0.5 rounded font-semibold <?= $c['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' ?>"><?= ucfirst(esc($c['status'])) ?></span></td>
                        <td class="p-3"><?= esc($c['holder'] ?? '-') ?></td>
                        <td class="p-3"><button type="button" @click='edit = <?= json_encode(['id' => (int) $c['id'], 'card_id' => $c['card_id'], 'card_no' => $c['card_no'], 'status' => $c['status']], JSON_HEX_APOS | JSON_HEX_QUOT) ?>' class="text-primary"><span class="material-symbols-outlined text-[20px]">edit</span></button></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <form method="post" action="<?= base_url('twp/cards/rates') ?>" class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 space-y-3 h-fit">
            <?= csrf_field() ?>
            <h2 class="font-bold uppercase text-sm text-primary">TWP Rates</h2>
            <div><label class="block text-sm mb-1">Per worker (RM)</label><input type="number" step="0.01" min="0" name="rate_worker" value="<?= number_format($rates['worker'], 2, '.', '') ?>" <?= $superadmin ? '' : 'disabled' ?> class="<?= $input ?>"></div>
            <div><label class="block text-sm mb-1">Per vehicle (RM)</label><input type="number" step="0.01" min="0" name="rate_vehicle" value="<?= number_format($rates['vehicle'], 2, '.', '') ?>" <?= $superadmin ? '' : 'disabled' ?> class="<?= $input ?>"></div>
            <?php if ($superadmin): ?><button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Save Rates</button>
            <?php else: ?><p class="text-xs text-gray-500">Rates are set by the superadmin.</p><?php endif; ?>
            <?php $eff = $effective ?? null; if ($eff && in_array('payment_type', $eff['source'] ?? [], true)): ?>
                <div class="rounded bg-blue-50 dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-300 p-2">
                    Config &gt; Payment Type is set, so new requests use:
                    per worker RM <?= number_format($eff['worker'], 2) ?><?= $eff['source']['worker'] === 'payment_type' ? ' (STAFF)' : '' ?>,
                    per vehicle RM <?= number_format($eff['vehicle'], 2) ?><?= $eff['source']['vehicle'] === 'payment_type' ? ' (LORRY)' : '' ?>.
                    The rates above apply only to a type with no active Payment Type.
                </div>
            <?php else: ?><p class="text-xs text-gray-500">Set per-client amounts in Config &gt; Payment Type (STAFF = per worker, LORRY = per vehicle); they replace these rates.</p><?php endif; ?>
        </form>
    </div>

    <div x-show="edit" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="edit = null">
        <form method="post" action="<?= base_url('twp/cards/save') ?>" class="bg-white dark:bg-gray-900 rounded-lg w-full max-w-md p-5 space-y-3">
            <?= csrf_field() ?>
            <input type="hidden" name="id" :value="edit && edit.id">
            <h3 class="font-bold text-lg">Temporary Work Pass Card Details</h3>
            <div><label class="block text-sm mb-1">Temporary Work Pass No *</label><input name="card_no" required :value="edit && edit.card_no" class="<?= $input ?>"></div>
            <div><label class="block text-sm mb-1">Card ID *</label><input name="card_id" required :value="edit && edit.card_id" placeholder="Tap the card on the reader" class="<?= $input ?>"></div>
            <div><label class="block text-sm mb-1">Status</label><select name="status" class="<?= $input ?>"><option value="active" :selected="edit && edit.status === 'active'">Active</option><option value="inactive" :selected="edit && edit.status === 'inactive'">Inactive</option></select></div>
            <div class="flex justify-end gap-2"><button type="button" @click="edit = null" class="px-4 py-2 rounded border text-sm">Back</button><button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Save</button></div>
        </form>
    </div>
</div>

<!-- KPK print-section: blank TWP card, front then back with the signature (artwork from Config > Card Template Management) -->
<div id="twpPrint" class="hidden print:block">
    <div style="width:54mm;height:86mm;position:relative;"><img src="<?= esc($templates['front']) ?>" style="width:100%;height:100%;position:absolute;object-fit:cover;"></div>
    <div style="break-after:page"></div>
    <div style="width:54mm;height:86mm;position:relative;">
        <img src="<?= esc($templates['back']) ?>" style="width:100%;height:100%;position:absolute;">
        <img src="<?= base_url('assets/images/template/sign.png') ?>" style="width:65%;height:8mm;left:8%;top:68mm;position:absolute;">
    </div>
</div>
<style>@media print { @page { size: 54mm 86mm; margin: 0; } body * { visibility: hidden; } #twpPrint, #twpPrint * { visibility: visible; } #twpPrint { position: absolute; left: 0; top: 0; } }</style>

<?= view('staffs/_layout_bottom') ?>
