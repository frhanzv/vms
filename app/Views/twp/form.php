<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php
$o     = static fn($k, $d = '') => esc((string) ($old[$k] ?? $d));
$input = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm focus:ring-primary focus:border-primary';
$label = 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1';
$req   = '<span class="text-red-500">*</span>';
$workers = array_values($old['workers'] ?? [['resident' => 'LOCAL']]);
$chosen  = (array) ($old['locations'] ?? []);
?>
<form method="post" action="<?= base_url('twp/requests/save') ?>" class="-m-2 space-y-4"
      x-data="{ resident: '<?= $o('resident', 'LOCAL') ?>', workers: <?= esc(json_encode($workers), 'attr') ?>, rate: <?= (float) $rates['worker'] ?>, vrate: <?= (float) $rates['vehicle'] ?>,
                get total() { return this.workers.filter(w => (w.name || '').trim()).reduce((s, w) => s + this.rate + ((w.vehicle_no || '').trim() ? this.vrate : 0), 0) } }">
    <?= csrf_field() ?>
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold uppercase tracking-wide">New Temporary Work Pass</h1>
        <a href="<?= base_url('twp/requests') ?>" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-semibold">Back</a>
    </div>

    <?php if ($errors): ?>
        <div class="px-4 py-3 rounded border bg-red-50 text-red-700 border-red-200 text-sm"><ul class="list-disc ml-5"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 space-y-3">
            <h2 class="font-bold uppercase text-sm text-primary">Applicant Info</h2>
            <div><label class="<?= $label ?>">Company <?= $req ?></label><input name="company_name" value="<?= $o('company_name') ?>" required class="<?= $input ?>"></div>
            <div><label class="<?= $label ?>">Company Registration No</label><input name="company_reg_id" value="<?= $o('company_reg_id') ?>" class="<?= $input ?>"></div>
            <div><label class="<?= $label ?>">Resident <?= $req ?></label>
                <select name="resident" x-model="resident" class="<?= $input ?>"><option>LOCAL</option><option>FOREIGNER</option></select></div>
            <div x-show="resident === 'LOCAL'"><label class="<?= $label ?>">IC Number <?= $req ?></label><input name="ic_no" value="<?= $o('ic_no') ?>" class="<?= $input ?>"></div>
            <div x-show="resident === 'FOREIGNER'"><label class="<?= $label ?>">Passport Number <?= $req ?></label><input name="passport_no" value="<?= $o('passport_no') ?>" class="<?= $input ?>"></div>
            <div><label class="<?= $label ?>">Applicant Name <?= $req ?></label><input name="full_name" value="<?= $o('full_name') ?>" required class="<?= $input ?>"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="<?= $label ?>">Contact Number <?= $req ?></label><input name="contact_no" value="<?= $o('contact_no') ?>" required class="<?= $input ?>"></div>
                <div><label class="<?= $label ?>">Email Address</label><input type="email" name="email" value="<?= $o('email') ?>" class="<?= $input ?>"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 space-y-3">
            <h2 class="font-bold uppercase text-sm text-primary">Application Info</h2>
            <div><label class="<?= $label ?>">Type Of Work <?= $req ?></label><input name="type_of_work" value="<?= $o('type_of_work') ?>" required placeholder="e.g. Maintenance, Cleaning, Installation" class="<?= $input ?>"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="<?= $label ?>">Date and Time In <?= $req ?></label><input type="datetime-local" name="date_from" value="<?= $o('date_from') ?>" required class="<?= $input ?>"></div>
                <div><label class="<?= $label ?>">Date and Time Out <?= $req ?></label><input type="datetime-local" name="date_to" value="<?= $o('date_to') ?>" required class="<?= $input ?>"></div>
            </div>
            <div><label class="<?= $label ?>">Location Of Work <?= $req ?></label>
                <div class="grid grid-cols-2 gap-1 max-h-40 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg p-2">
                    <?php if (! $locations): ?><span class="text-xs text-gray-500">No locations set up yet.</span><?php endif; ?>
                    <?php foreach ($locations as $l): ?><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="locations[]" value="<?= esc($l) ?>" <?= in_array($l, $chosen, true) ? 'checked' : '' ?>> <?= esc($l) ?></label><?php endforeach; ?>
                </div></div>
            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-3 text-sm">
                <div class="font-semibold text-primary uppercase text-xs mb-1">Payment Info</div>
                <div>Per worker: RM <?= number_format($rates['worker'], 2) ?> &middot; per vehicle: RM <?= number_format($rates['vehicle'], 2) ?></div>
                <div class="font-bold">Total: RM <span x-text="total.toFixed(2)"></span></div>
                <div class="text-xs text-gray-500">Pay at the counter (receipt) or online by FPX from the request page after submitting.</div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 space-y-3">
        <div class="flex items-center justify-between"><h2 class="font-bold uppercase text-sm text-primary">Workers</h2>
            <button type="button" @click="workers.push({ resident: 'LOCAL' })" class="px-3 py-1.5 rounded bg-primary text-white text-xs font-semibold">+ Add Worker</button></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs uppercase text-gray-500"><tr><th class="p-2 text-left">Name *</th><th class="p-2 text-left">Resident</th><th class="p-2 text-left">IC / Passport No *</th><th class="p-2 text-left">Contact No</th><th class="p-2 text-left">Vehicle Number</th><th></th></tr></thead>
                <tbody>
                <template x-for="(w, i) in workers" :key="i">
                    <tr>
                        <td class="p-1"><input :name="`workers[${i}][name]`" x-model="w.name" class="<?= $input ?>"></td>
                        <td class="p-1"><select :name="`workers[${i}][resident]`" x-model="w.resident" class="<?= $input ?>"><option>LOCAL</option><option>FOREIGNER</option></select></td>
                        <td class="p-1"><input :name="`workers[${i}][id_no]`" x-model="w.id_no" class="<?= $input ?>"></td>
                        <td class="p-1"><input :name="`workers[${i}][contact_no]`" x-model="w.contact_no" class="<?= $input ?>"></td>
                        <td class="p-1"><input :name="`workers[${i}][vehicle_no]`" x-model="w.vehicle_no" class="<?= $input ?> uppercase"></td>
                        <td class="p-1"><button type="button" @click="workers.splice(i, 1)" x-show="workers.length > 1" class="text-red-600"><span class="material-symbols-outlined">delete</span></button></td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end"><button class="px-5 py-2 rounded bg-primary text-white text-sm font-semibold">Submit Request</button></div>
</form>

<?= view('staffs/_layout_bottom') ?>
