<?php
echo view('vehicles/_head', ['pageTitle' => $pageTitle]);
$isEdit = ! empty($d['id']);
$val = static fn(string $k) => esc($old[$k] ?? ($d[$k] ?? ''));
$dt  = static fn(string $k) => esc($old[$k] ?? (! empty($d[$k]) ? date('d/m/Y', strtotime($d[$k])) : ''));
$selLocs = array_values(array_filter((array) ($old['location_access'] ?? explode(',', (string) ($d['location_access'] ?? '')))));
$in  = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm';
$lb  = 'block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1';
?>
<div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= base_url('vehicle-daily') ?>" class="text-gray-500 hover:text-primary"><span class="material-symbols-outlined">arrow_back</span></a>
        <h1 class="text-xl md:text-2xl font-bold tracking-tight uppercase"><?= $isEdit ? 'Edit Daily Pass ' . esc($d['app_no']) : 'New Daily Pass Request' ?></h1>
    </div>
    <form method="post" action="<?= base_url($isEdit ? 'vehicle-daily/update/' . $d['id'] : 'vehicle-daily/store') ?>"
          x-data="{ open: false, q: '', selected: <?= esc(json_encode($selLocs), 'attr') ?>, labels: <?= esc(json_encode(array_map('strval', $locationOptions)), 'attr') ?>,
                    get codes() { return Object.keys(this.labels); },
                    get shown() { const q = this.q.toLowerCase(); return this.codes.filter(c => this.labels[c].toLowerCase().includes(q)); },
                    get allOn() { return this.codes.length > 0 && this.selected.length === this.codes.length; },
                    toggleAll() { this.selected = this.allOn ? [] : [...this.codes]; }, remove(c) { this.selected = this.selected.filter(x => x !== c); } }">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div><label class="<?= $lb ?>">Company *</label>
                <?php if (! empty($ownCompany)): ?><input value="<?= esc($ownCompany['name']) ?>" disabled class="<?= $in ?> bg-gray-100"/>
                <?php else: ?><input name="company_name" value="<?= $val('company_name') ?>" class="<?= $in ?>"/><?php endif; ?></div>
            <div><label class="<?= $lb ?>">Driver Name *</label><input name="driver_name" value="<?= $val('driver_name') ?>" class="<?= $in ?>"/></div>
            <div><label class="<?= $lb ?>">Driver IC / Passport</label><input name="driver_ic" value="<?= $val('driver_ic') ?>" class="<?= $in ?>"/></div>
            <div><label class="<?= $lb ?>">Contact No. *</label><input name="contact_no" value="<?= $val('contact_no') ?>" class="<?= $in ?>"/></div>
            <div><label class="<?= $lb ?>">Vehicle Type</label>
                <select name="vehicle_type_id" class="<?= $in ?>"><option value="">-- Select --</option>
                    <?php foreach ($types as $t): ?><option value="<?= $t['id'] ?>" <?= (string) ($old['vehicle_type_id'] ?? $d['vehicle_type_id'] ?? '') === (string) $t['id'] ? 'selected' : '' ?>><?= esc($t['name']) ?></option><?php endforeach; ?></select></div>
            <div><label class="<?= $lb ?>">Plate Number *</label><input name="plate_no" value="<?= $val('plate_no') ?>" class="<?= $in ?> uppercase"/></div>
            <div class="md:col-span-3"><label class="<?= $lb ?>">Purpose of Visit *</label><input name="purpose" value="<?= $val('purpose') ?>" class="<?= $in ?>"/></div>
            <div><label class="<?= $lb ?>">Visit From *</label><input name="visit_from" value="<?= $dt('visit_from') ?>" class="flatpickr <?= $in ?>" placeholder="dd/mm/yyyy"/></div>
            <div><label class="<?= $lb ?>">Visit To *</label><input name="visit_to" value="<?= $dt('visit_to') ?>" class="flatpickr <?= $in ?>" placeholder="dd/mm/yyyy"/></div>
        </div>
        <h2 class="text-sm font-bold uppercase text-gray-700 dark:text-gray-200 mb-3">Location Access *</h2>
        <div class="relative mb-6 max-w-xl" @click.outside="open = false">
            <template x-for="c in selected" :key="c"><input type="hidden" name="location_access[]" :value="c"></template>
            <button type="button" @click="open = !open" class="w-full min-h-[3rem] flex items-center justify-between gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-left text-sm">
                <span class="flex flex-wrap gap-1.5 items-center">
                    <span x-show="selected.length === 0" class="text-gray-400">-- Select location(s) --</span>
                    <template x-for="c in selected" :key="c"><span class="inline-flex items-center gap-1 rounded-full bg-primary/10 text-primary px-2.5 py-0.5 text-xs font-medium"><span x-text="labels[c] || c"></span><span class="cursor-pointer font-bold" @click.stop="remove(c)">&times;</span></span></template>
                </span>
                <span class="material-symbols-outlined text-gray-400">expand_more</span>
            </button>
            <div x-show="open" x-cloak class="absolute z-30 mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-lg">
                <div class="p-2 border-b dark:border-gray-600"><input type="text" x-model="q" placeholder="Search location..." class="<?= $in ?>"/></div>
                <label class="flex items-center gap-2 px-3 py-2 text-sm font-semibold border-b dark:border-gray-600 cursor-pointer"><input type="checkbox" :checked="allOn" @change="toggleAll()"/> Select All</label>
                <div class="max-h-64 overflow-y-auto py-1"><template x-for="c in shown" :key="c"><label class="flex items-center gap-2 px-3 py-1.5 text-sm cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700"><input type="checkbox" :value="c" x-model="selected"/><span x-text="labels[c]"></span></label></template></div>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="<?= base_url('vehicle-daily') ?>" class="px-4 py-2 rounded border border-gray-300 text-sm">Cancel</a>
            <button name="action" value="draft" class="px-4 py-2 rounded border border-gray-300 text-sm font-medium">Save as Draft</button>
            <button name="action" value="submit" class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Submit</button>
        </div>
    </form>
</div>
<?= view('vehicles/_foot') ?>
