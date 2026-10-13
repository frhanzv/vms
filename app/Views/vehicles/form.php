<?php
echo view('vehicles/_head', ['pageTitle' => $pageTitle]);
$isEdit = ! empty($v['id']);
$val = static fn(string $k) => esc($old[$k] ?? ($v[$k] ?? ''));
$dt  = static fn(string $k) => esc($old[$k] ?? (! empty($v[$k]) ? date('d/m/Y', strtotime($v[$k])) : ''));
$selLocs = array_values(array_filter((array) ($old['location_access'] ?? explode(',', (string) ($v['location_access'] ?? '')))));
$in  = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm';
$lb  = 'block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1';
$typeMeta = [];
foreach ($types as $t) { $typeMeta[$t['id']] = array_map('intval', array_intersect_key($t, $typeFields)); }
?>
<div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= base_url('vehicles/' . $group . ($passFor === 'staff' ? '/staff' : '')) ?>" class="text-gray-500 hover:text-primary"><span class="material-symbols-outlined">arrow_back</span></a>
        <h1 class="text-xl md:text-2xl font-bold tracking-tight uppercase"><?= $isEdit ? 'Edit Vehicle Pass ' . esc($v['app_no']) : (! empty($v['renewed_from']) ? 'Renew Vehicle Pass' : ($passFor === 'staff' ? 'New Staff Vehicle Request' : 'New Vehicle Pass Request')) ?>
            <span class="text-xs normal-case font-medium text-gray-500">- <?= esc(\App\Libraries\VehicleRules::GROUPS[$group]) ?></span></h1>
    </div>

    <form method="post" enctype="multipart/form-data" action="<?= base_url($isEdit ? 'vehicles/update/' . $v['id'] : 'vehicles/store') ?>"
          x-data="{ type: '<?= $val('vehicle_type_id') ?>', meta: <?= esc(json_encode($typeMeta), 'attr') ?>, owner: '<?= $val('owner_type') === 'Company' ? 'Company' : 'Individual' ?>',
                    show(c) { return this.type && this.meta[this.type] && this.meta[this.type][c] === 1; },
                    open: false, q: '', selected: <?= esc(json_encode($selLocs), 'attr') ?>, labels: <?= esc(json_encode(array_map('strval', $locationOptions)), 'attr') ?>,
                    get codes() { return Object.keys(this.labels); },
                    get shown() { const q = this.q.toLowerCase(); return this.codes.filter(c => this.labels[c].toLowerCase().includes(q)); },
                    get allOn() { return this.codes.length > 0 && this.selected.length === this.codes.length; },
                    toggleAll() { this.selected = this.allOn ? [] : [...this.codes]; },
                    remove(c) { this.selected = this.selected.filter(x => x !== c); } }">
        <?= csrf_field() ?>

        <input type="hidden" name="vehicle_group" value="<?= esc($group) ?>"/>
        <input type="hidden" name="pass_for" value="<?= esc($passFor) ?>"/>
        <?php if (! empty($v['renewed_from'])): ?><input type="hidden" name="renewed_from" value="<?= (int) $v['renewed_from'] ?>"/>
        <div class="mb-5 rounded border border-blue-200 bg-blue-50 text-blue-800 px-4 py-3 text-sm">Renewal of the current pass. When the new sticker is issued the old pass is closed automatically.</div><?php endif; ?>

        <h2 class="text-sm font-bold uppercase text-gray-700 dark:text-gray-200 mb-3">Owner</h2>
        <?php if ($passFor === 'staff'): ?>
        <div class="mb-6" x-data="{ q: '', res: [], sel: <?= esc(json_encode($staffRow ? ['id' => (int) $staffRow['id'], 'staff_no' => $staffRow['staff_no'], 'full_name' => $staffRow['full_name'], 'ic_passport' => $staffRow['ic_passport'] ?? ''] : null), 'attr') ?>,
              async find() { if (this.q.length < 2) { this.res = []; return; } const r = await fetch('<?= base_url('vehicles/public/staff-lookup') ?>?q=' + encodeURIComponent(this.q)); this.res = await r.json(); } }">
            <input type="hidden" name="staff_id" :value="sel ? sel.id : ''"/>
            <label class="<?= $lb ?>">Staff * (search by Staff No, name or IC)</label>
            <div class="relative max-w-xl">
                <input type="text" x-model="q" @input.debounce.300ms="find()" placeholder="Type to search staff..." class="<?= $in ?>" <?= $isEdit ? 'readonly' : '' ?>/>
                <div x-show="res.length && !sel" x-cloak class="absolute z-20 w-full mt-1 rounded border bg-white dark:bg-gray-800 shadow max-h-56 overflow-y-auto">
                    <template x-for="r in res" :key="r.id"><button type="button" class="block w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                        @click="sel = r; res = []; q = ''"><span class="font-semibold" x-text="r.full_name"></span> <span class="text-xs text-gray-500" x-text="r.staff_no + ' / ' + (r.ic_passport || '')"></span></button></template>
                </div>
            </div>
            <div x-show="sel" x-cloak class="mt-2 inline-flex items-center gap-2 rounded-full bg-primary/10 text-primary px-3 py-1 text-sm">
                <span x-text="sel ? sel.full_name + ' (' + sel.staff_no + ')' : ''"></span>
                <?php if (! $isEdit): ?><button type="button" @click="sel = null" class="font-bold">&times;</button><?php endif; ?>
            </div>
            <div class="mt-4 max-w-xs"><label class="<?= $lb ?>">Contact No.</label><input name="contact_no" value="<?= $val('contact_no') ?>" class="<?= $in ?>"/></div>
        </div>
        <?php else: $lockCo = ! empty($ownCompany) || $group === 'cnc'; ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div><label class="<?= $lb ?>">Owner Type *</label>
                <?php if ($lockCo): ?><input type="hidden" name="owner_type" value="Company"/><input value="Company" disabled class="<?= $in ?> bg-gray-100"/>
                <?php else: ?><select name="owner_type" x-model="owner" class="<?= $in ?>"><?php foreach (['Individual', 'Company'] as $o): ?><option><?= $o ?></option><?php endforeach; ?></select><?php endif; ?></div>
            <div><label class="<?= $lb ?>">Owner Name *</label><input name="owner_name" value="<?= $val('owner_name') ?>" class="<?= $in ?>"/></div>
            <div><label class="<?= $lb ?>">IC / Passport</label><input name="owner_ic" value="<?= $val('owner_ic') ?>" class="<?= $in ?>"/></div>
            <div <?= $lockCo ? '' : 'x-show="owner === \'Company\'"' ?>><label class="<?= $lb ?>">Company Name *</label>
                <?php if (! empty($ownCompany)): ?><input type="hidden" name="company_name" value="<?= esc($ownCompany['name']) ?>"/><input value="<?= esc($ownCompany['name']) ?>" disabled class="<?= $in ?> bg-gray-100"/>
                <?php else: ?><input name="company_name" value="<?= $val('company_name') ?>" class="<?= $in ?>"/><?php endif; ?></div>
            <div><label class="<?= $lb ?>">Contact No. *</label><input name="contact_no" value="<?= $val('contact_no') ?>" class="<?= $in ?>"/></div>
        </div>
        <?php endif; ?>

        <h2 class="text-sm font-bold uppercase text-gray-700 dark:text-gray-200 mb-3">Vehicle</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div><label class="<?= $lb ?>">Vehicle Type *</label>
                <select name="vehicle_type_id" x-model="type" class="<?= $in ?>">
                    <option value="">-- Select --</option>
                    <?php foreach ($types as $t): ?><option value="<?= $t['id'] ?>"><?= esc($t['name']) ?></option><?php endforeach; ?>
                </select></div>
            <div><label class="<?= $lb ?>">Plate Number *</label><input name="plate_no" value="<?= $val('plate_no') ?>" class="<?= $in ?> uppercase" placeholder="e.g. ABC1234"/></div>
            <div><label class="<?= $lb ?>">Brand / Model</label><input name="brand" value="<?= $val('brand') ?>" class="<?= $in ?>"/></div>
            <div><label class="<?= $lb ?>">Year Made</label><input name="year_made" maxlength="4" value="<?= $val('year_made') ?>" class="<?= $in ?>"/></div>
            <div><label class="<?= $lb ?>">Colour</label><input name="colour" value="<?= $val('colour') ?>" class="<?= $in ?>"/></div>
            <div><label class="<?= $lb ?>">Road Tax Expiry *</label><input name="road_tax_expiry" value="<?= $dt('road_tax_expiry') ?>" class="flatpickr <?= $in ?>" placeholder="dd/mm/yyyy"/></div>
            <div><label class="<?= $lb ?>">Insurance Expiry *</label><input name="insurance_expiry" value="<?= $dt('insurance_expiry') ?>" class="flatpickr <?= $in ?>" placeholder="dd/mm/yyyy"/></div>
            <?php foreach ($typeFields as $col => [$field, $label]): ?>
            <div x-show="show('<?= $col ?>')" x-cloak><label class="<?= $lb ?>"><?= $label ?> *</label><input name="<?= $field ?>" value="<?= $val($field) ?>" class="<?= $in ?>"/></div>
            <?php endforeach; ?>
        </div>

        <h2 class="text-sm font-bold uppercase text-gray-700 dark:text-gray-200 mb-3">Location Access *</h2>
        <div class="relative mb-6 max-w-xl" @click.outside="open = false" @keydown.escape.window="open = false">
            <template x-for="c in selected" :key="c"><input type="hidden" name="location_access[]" :value="c"></template>
            <button type="button" @click="open = !open" class="w-full min-h-[3rem] flex items-center justify-between gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-left text-sm">
                <span class="flex flex-wrap gap-1.5 items-center">
                    <span x-show="selected.length === 0" class="text-gray-400">-- Select location(s) --</span>
                    <template x-for="c in selected" :key="c">
                        <span class="inline-flex items-center gap-1 rounded-full bg-primary/10 text-primary px-2.5 py-0.5 text-xs font-medium"><span x-text="labels[c] || c"></span>
                            <span class="cursor-pointer font-bold" @click.stop="remove(c)">&times;</span></span>
                    </template>
                </span>
                <span class="material-symbols-outlined text-gray-400" :class="open ? 'rotate-180' : ''">expand_more</span>
            </button>
            <div x-show="open" x-cloak class="absolute z-30 mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-lg">
                <div class="p-2 border-b dark:border-gray-600"><input type="text" x-model="q" placeholder="Search location..." class="<?= $in ?>"/></div>
                <label class="flex items-center gap-2 px-3 py-2 text-sm font-semibold border-b dark:border-gray-600 cursor-pointer"><input type="checkbox" :checked="allOn" @change="toggleAll()"/> Select All</label>
                <div class="max-h-64 overflow-y-auto py-1">
                    <template x-for="c in shown" :key="c"><label class="flex items-center gap-2 px-3 py-1.5 text-sm cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700"><input type="checkbox" :value="c" x-model="selected"/><span x-text="labels[c]"></span></label></template>
                    <p x-show="shown.length === 0" class="px-3 py-2 text-sm text-gray-500">No location found.</p>
                </div>
            </div>
        </div>

        <h2 class="text-sm font-bold uppercase text-gray-700 dark:text-gray-200 mb-3">Documents</h2>
        <div class="mb-6">
            <?php foreach ($files as $f): ?>
            <label class="flex items-center gap-2 text-sm mb-1"><input type="checkbox" name="remove_file[]" value="<?= $f['id'] ?>"/>
                Remove <a href="<?= base_url($f['file_path']) ?>" target="_blank" class="text-primary underline"><?= esc($f['original_name']) ?></a></label>
            <?php endforeach; ?>
            <input type="file" name="documents[]" multiple accept=".jpg,.jpeg,.png,.pdf" class="<?= $in ?> max-w-xl"/>
            <p class="text-xs text-gray-500 mt-1">Vehicle grant / road tax / insurance (JPG, PNG or PDF, max 5 MB each).</p>
        </div>

        <div class="mb-6"><label class="<?= $lb ?>">Remark</label><textarea name="remark" rows="2" class="<?= $in ?>"><?= $val('remark') ?></textarea></div>

        <div class="flex justify-end gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="<?= base_url('vehicles/' . $group . ($passFor === 'staff' ? '/staff' : '')) ?>" class="px-4 py-2 rounded border border-gray-300 text-sm">Cancel</a>
            <button name="action" value="draft" class="px-4 py-2 rounded border border-gray-300 text-sm font-medium">Save as Draft</button>
            <button name="action" value="submit" class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Submit</button>
        </div>
    </form>
</div>
<?= view('vehicles/_foot') ?>
