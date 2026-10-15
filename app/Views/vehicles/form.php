<?php
echo view('vehicles/_head', ['pageTitle' => $pageTitle]);
$isEdit = ! empty($v['id']);
$val = static fn(string $k) => esc($old[$k] ?? ($v[$k] ?? ''));
$dt  = static fn(string $k) => esc($old[$k] ?? (! empty($v[$k]) ? date('d/m/Y', strtotime($v[$k])) : ''));
$selLocs = array_values(array_filter((array) ($old['location_access'] ?? explode(',', (string) ($v['location_access'] ?? '')))));
$in  = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm';
$lb  = 'block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1';
$typeMeta = [];
foreach ($types as $t) { $typeMeta[$t['id']] = array_map('intval', array_intersect_key($t, $typeFields)); $typeMeta[$t['id']]['req_pass'] = (int) ($t['req_pass'] ?? 0); }
$direct = ! empty($direct);
$driverRows = $driverRows ?? [];
$typeList = array_map(static fn($t) => ['id' => (string) $t['id'], 'name' => $t['name'], 'operation' => $t['operation'] ?? ''], $types);
$ops = \App\Libraries\VehicleRules::OPERATIONS;
$yearNow = (int) date('Y');
// Config > Dynamic Form Fields > Vehicle Pass Request
$on = static fn(string $k): bool => \App\Libraries\VehicleRules::fieldOn($k);
$st = static fn(string $k): string => \App\Libraries\VehicleRules::fieldRequired($k) ? ' *' : '';
?>
<div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= base_url('vehicles/' . $group . ($passFor === 'staff' ? '/staff' : '')) ?>" class="text-gray-500 hover:text-primary"><span class="material-symbols-outlined">arrow_back</span></a>
        <h1 class="text-xl md:text-2xl font-bold tracking-tight uppercase"><?= $isEdit ? 'Edit Vehicle Pass ' . esc($v['app_no']) : ($direct ? 'New Vehicle Entry Pass (already issued)' : (! empty($v['renewed_from']) ? 'Renew Vehicle Pass' : ($passFor === 'staff' ? 'New Staff Vehicle Request' : 'New Vehicle Pass Request'))) ?>
            <span class="text-xs normal-case font-medium text-gray-500">- <?= esc(\App\Libraries\VehicleRules::GROUPS[$group]) ?></span></h1>
    </div>

    <form method="post" enctype="multipart/form-data" action="<?= base_url($isEdit ? 'vehicles/update/' . $v['id'] : 'vehicles/store') ?>"
          x-data="{ type: '<?= $val('vehicle_type_id') ?>', op: '<?= $val('vehicle_operation') ?: ($group === 'cnc' ? $ops[0] : '') ?>', typeList: <?= esc(json_encode($typeList), 'attr') ?>,
                    get typesShown() { return this.typeList.filter(t => !this.op || !t.operation || t.operation === this.op); },
                    meta: <?= esc(json_encode($typeMeta), 'attr') ?>, owner: '<?= $val('owner_type') === 'Company' ? 'Company' : 'Individual' ?>',
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
        <?php if ($direct): ?><input type="hidden" name="direct" value="1"/>
        <div class="mb-5 rounded border border-amber-200 bg-amber-50 text-amber-800 px-4 py-3 text-sm">This records a pass that is already issued: it skips the request and approval steps and goes straight to the Closed List.</div><?php endif; ?>
        <?php if (! empty($v['renewed_from'])): ?><input type="hidden" name="renewed_from" value="<?= (int) $v['renewed_from'] ?>"/>
        <div class="mb-5 rounded border border-blue-200 bg-blue-50 text-blue-800 px-4 py-3 text-sm">Renewal of the current pass. When the new sticker is issued the old pass is closed automatically.</div><?php endif; ?>

        <h2 class="text-sm font-bold uppercase text-gray-700 dark:text-gray-200 mb-3">Application Info</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div><label class="<?= $lb ?>">Date Of Application</label><input value="<?= esc(! empty($v['app_date']) ? date('d/m/Y', strtotime($v['app_date'])) : date('d/m/Y')) ?>" disabled class="<?= $in ?> bg-gray-100"/></div>
            <div><label class="<?= $lb ?>">Type Of Application</label><input value="<?= ! empty($v['renewed_from']) || ($v['type_of_app'] ?? '') === 'Renewal' ? 'Renewal' : 'New' ?>" disabled class="<?= $in ?> bg-gray-100"/></div>
            <div><label class="<?= $lb ?>">Sticker Year *</label>
                <select name="sticker_year" class="<?= $in ?>"><?php $sy = (string) ($old['sticker_year'] ?? $v['sticker_year'] ?? ''); foreach ($stickerYears as $y): ?><option <?= $sy === $y ? 'selected' : '' ?>><?= $y ?></option><?php endforeach; ?></select></div>
            <?php if ($group === 'cnc'): ?>
            <div><label class="<?= $lb ?>">Vehicle Operation *</label>
                <select name="vehicle_operation" x-model="op" @change="if (!typesShown.find(t => t.id === type)) type = ''" class="<?= $in ?>"><?php foreach ($ops as $o): ?><option><?= $o ?></option><?php endforeach; ?></select></div>
            <?php endif; ?>
        </div>

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
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 max-w-4xl">
                <?php if ($on('department')): ?><div><label class="<?= $lb ?>">Department<?= $st('department') ?></label><input name="department" value="<?= $val('department') ?: esc($staffRow['department'] ?? '') ?>" class="<?= $in ?>"/></div><?php endif; ?>
                <?php if ($on('hod')): ?><div><label class="<?= $lb ?>">HOD<?= $st('hod') ?></label><input name="hod" value="<?= $val('hod') ?>" class="<?= $in ?>"/></div><?php endif; ?>
                <?php if ($on('contact_no')): ?><div><label class="<?= $lb ?>">Contact No.<?= $st('contact_no') ?></label><input name="contact_no" value="<?= $val('contact_no') ?>" class="<?= $in ?>"/></div><?php endif; ?>
            </div>
        </div>
        <?php else: $lockCo = ! empty($ownCompany) || $group === 'cnc'; ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div><label class="<?= $lb ?>">Owner Type *</label>
                <?php if ($lockCo): ?><input type="hidden" name="owner_type" value="Company"/><input value="Company" disabled class="<?= $in ?> bg-gray-100"/>
                <?php else: ?><select name="owner_type" x-model="owner" class="<?= $in ?>"><?php foreach (['Individual', 'Company'] as $o): ?><option><?= $o ?></option><?php endforeach; ?></select><?php endif; ?></div>
            <div><label class="<?= $lb ?>">Owner Name *</label><input name="owner_name" value="<?= $val('owner_name') ?>" class="<?= $in ?>"/></div>
            <?php if ($on('owner_ic')): ?><div><label class="<?= $lb ?>">IC / Passport<?= $st('owner_ic') ?></label><input name="owner_ic" value="<?= $val('owner_ic') ?>" class="<?= $in ?>"/></div><?php endif; ?>
            <div <?= $lockCo ? '' : 'x-show="owner === \'Company\'"' ?>><label class="<?= $lb ?>">Company Name *</label>
                <?php if (! empty($ownCompany)): ?><input type="hidden" name="company_name" value="<?= esc($ownCompany['name']) ?>"/><input value="<?= esc($ownCompany['name']) ?>" disabled class="<?= $in ?> bg-gray-100"/>
                <?php else: ?><input name="company_name" value="<?= $val('company_name') ?>" class="<?= $in ?>"/><?php endif; ?></div>
            <?php if ($on('contact_no')): ?><div><label class="<?= $lb ?>">Contact No.<?= $st('contact_no') ?></label><input name="contact_no" value="<?= $val('contact_no') ?>" class="<?= $in ?>"/></div><?php endif; ?>
        </div>
        <?php endif; ?>

        <h2 class="text-sm font-bold uppercase text-gray-700 dark:text-gray-200 mb-3">Vehicle</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div><label class="<?= $lb ?>">Vehicle Type *</label>
                <select name="vehicle_type_id" x-model="type" class="<?= $in ?>">
                    <option value="">-- Select --</option>
                    <template x-for="t in typesShown" :key="t.id"><option :value="t.id" :selected="t.id === type" x-text="t.name"></option></template>
                </select></div>
            <div><label class="<?= $lb ?>">Plate Number *</label><input name="plate_no" value="<?= $val('plate_no') ?>" class="<?= $in ?> uppercase" placeholder="e.g. ABC1234"/></div>
            <?php if ($on('brand')): ?><div><label class="<?= $lb ?>">Brand<?= $st('brand') ?></label><input name="brand" value="<?= $val('brand') ?>" class="<?= $in ?>"/></div><?php endif; ?>
            <?php if ($on('year_made')): ?><div><label class="<?= $lb ?>">Year Made<?= $st('year_made') ?></label>
                <select name="year_made" class="<?= $in ?>"><?php $ym = (string) ($old['year_made'] ?? $v['year_made'] ?? ''); for ($y = $yearNow; $y > $yearNow - 60; $y--): ?><option <?= $ym === (string) $y ? 'selected' : '' ?>><?= $y ?></option><?php endfor; ?></select></div><?php endif; ?>
            <?php if ($on('colour')): ?><div><label class="<?= $lb ?>">Colour<?= $st('colour') ?></label><input name="colour" value="<?= $val('colour') ?>" class="<?= $in ?>"/></div><?php endif; ?>
            <?php if ($on('road_tax_expiry')): ?><div><label class="<?= $lb ?>">Road Tax Expiry<?= $st('road_tax_expiry') ?></label><input name="road_tax_expiry" value="<?= $dt('road_tax_expiry') ?>" class="flatpickr <?= $in ?>" placeholder="dd/mm/yyyy"/></div><?php endif; ?>
            <?php if ($on('insurance_expiry')): ?><div><label class="<?= $lb ?>">Insurance Expiry<?= $st('insurance_expiry') ?></label><input name="insurance_expiry" value="<?= $dt('insurance_expiry') ?>" class="flatpickr <?= $in ?>" placeholder="dd/mm/yyyy"/></div><?php endif; ?>
            <?php foreach ($typeFields as $col => [$field, $label]): ?>
            <div x-show="show('<?= $col ?>')" x-cloak><label class="<?= $lb ?>"><?= $label ?> *</label>
                <?php if ($field === 'trailer_type'): ?><select name="<?= $field ?>" class="<?= $in ?>"><?php foreach (\App\Libraries\VehicleRules::TRAILER_TYPES as $tv => $tl): ?><option value="<?= $tv ?>" <?= (string) ($old[$field] ?? $v[$field] ?? '20') === (string) $tv ? 'selected' : '' ?>><?= $tl ?></option><?php endforeach; ?></select>
                <?php else: ?><input name="<?= $field ?>" value="<?= $val($field) ?>" class="<?= $in ?>"/><?php endif; ?></div>
            <?php endforeach; ?>
        </div>

        <?php if ($passFor !== 'staff'): ?>
        <div x-show="show('req_pass')" x-cloak class="mb-6" x-data="{ q: '', res: [], sel: <?= esc(json_encode(array_map(static fn($d) => ['id' => (int) $d['vendor_id'], 'full_name' => (string) $d['full_name'], 'ic' => (string) ($d['ic_no'] ?: $d['passport_no'])], $driverRows)), 'attr') ?>,
              async find() { if (this.q.length < 2) { this.res = []; return; } const r = await fetch('<?= base_url('vehicles/drivers') ?>?q=' + encodeURIComponent(this.q)); this.res = await r.json(); },
              add(r) { if (!this.sel.find(x => x.id === +r.id)) this.sel.push({ id: +r.id, full_name: r.full_name, ic: r.ic_no || r.passport_no || '' }); this.res = []; this.q = ''; } }">
            <h2 class="text-sm font-bold uppercase text-gray-700 dark:text-gray-200 mb-3">Driver (Vendor Pass) *</h2>
            <template x-for="d in sel" :key="d.id"><input type="hidden" name="driver_id[]" :value="d.id"></template>
            <div class="relative max-w-xl">
                <input type="text" x-model="q" @input.debounce.300ms="find()" placeholder="Search driver by name or IC / passport..." class="<?= $in ?>"/>
                <div x-show="res.length" x-cloak class="absolute z-20 w-full mt-1 rounded border bg-white dark:bg-gray-800 shadow max-h-56 overflow-y-auto">
                    <template x-for="r in res" :key="r.id"><button type="button" class="block w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700" @click="add(r)">
                        <span class="font-semibold" x-text="r.full_name"></span> <span class="text-xs text-gray-500" x-text="(r.ic_no || r.passport_no || '') + ' / ' + (r.vendor_company_name || '')"></span></button></template>
                </div>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
                <template x-for="d in sel" :key="d.id"><span class="inline-flex items-center gap-2 rounded-full bg-primary/10 text-primary px-3 py-1 text-sm"><span x-text="d.full_name + ' (' + d.ic + ')'"></span>
                    <button type="button" class="font-bold" @click="sel = sel.filter(x => x.id !== d.id)">&times;</button></span></template>
            </div>
            <p class="text-xs text-gray-500 mt-1">Only drivers with an active vendor pass can be linked. Reissuing the sticker later needs every linked pass to be active.</p>
        </div>
        <?php endif; ?>

        <?php if ($on('location_access')): ?>
        <h2 class="text-sm font-bold uppercase text-gray-700 dark:text-gray-200 mb-3">Location Access<?= $st('location_access') ?></h2>
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

        <?php endif; ?>

        <?php if ($on('documents')): ?>
        <h2 class="text-sm font-bold uppercase text-gray-700 dark:text-gray-200 mb-3">Upload Documents<?= $direct ? '' : $st('documents') ?></h2>
        <div class="mb-6">
            <?php foreach ($files as $f): ?>
            <label class="flex items-center gap-2 text-sm mb-1"><input type="checkbox" name="remove_file[]" value="<?= $f['id'] ?>"/>
                Remove <a href="<?= base_url($f['file_path']) ?>" target="_blank" class="text-primary underline"><?= esc($f['original_name']) ?></a></label>
            <?php endforeach; ?>
            <input type="file" name="documents[]" multiple accept=".jpg,.jpeg,.png,.pdf" class="<?= $in ?> max-w-xl"/>
            <p class="text-xs text-gray-500 mt-1">Vehicle grant / road tax / insurance (JPG, PNG or PDF, max 5 MB each).</p>
        </div>
        <?php endif; ?>

        <?php if ($on('remark')): ?><div class="mb-6"><label class="<?= $lb ?>">Remark<?= $st('remark') ?></label><textarea name="remark" rows="2" class="<?= $in ?>"><?= $val('remark') ?></textarea></div><?php endif; ?>

        <?php if ($direct): ?>
        <h2 class="text-sm font-bold uppercase text-gray-700 dark:text-gray-200 mb-3">Sticker Issuance</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6" x-data="{ pay: 'CASH' }">
            <div><label class="<?= $lb ?>">Collection Date</label><input name="collect_date" value="<?= esc($old['collect_date'] ?? date('d/m/Y')) ?>" class="flatpickr <?= $in ?>" placeholder="dd/mm/yyyy"/></div>
            <div><label class="<?= $lb ?>">Sticker Serial Number *</label><input name="sticker_serial" value="<?= esc($old['sticker_serial'] ?? '') ?>" class="<?= $in ?>"/></div>
            <div><label class="<?= $lb ?>">Date Of Issuance *</label><input name="issue_date" value="<?= esc($old['issue_date'] ?? date('d/m/Y')) ?>" class="flatpickr <?= $in ?>" placeholder="dd/mm/yyyy"/></div>
            <div><label class="<?= $lb ?>">Collector's IC No / Passport No *</label><input name="collector_ic" value="<?= esc($old['collector_ic'] ?? '') ?>" class="<?= $in ?>"/></div>
            <div><label class="<?= $lb ?>">Collected By</label><input name="collector_name" value="<?= esc($old['collector_name'] ?? '') ?>" class="<?= $in ?>"/></div>
            <div><label class="<?= $lb ?>">Payment Type</label><select name="payment_type" x-model="pay" class="<?= $in ?>"><option>CASH</option></select></div>
            <div><label class="<?= $lb ?>">Receipt No. (when the type needs payment)</label><input name="receipt_no" value="<?= esc($old['receipt_no'] ?? '') ?>" class="<?= $in ?>"/></div>
        </div>
        <?php endif; ?>

        <?php if (empty($asApprover) && ! $direct && $on('tnc')): ?>
        <label class="flex items-start gap-2 text-sm mb-6 max-w-3xl"><input type="checkbox" name="tnc" value="1" class="mt-1"/>
            <span>I declare that the information given is true, the vehicle is registered to the owner above, and I agree to follow the port's vehicle sticker terms and conditions. The sticker remains the property of the port and must be returned on request.</span></label>
        <?php endif; ?>

        <div class="flex justify-end gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="<?= base_url('vehicles/' . $group . ($passFor === 'staff' ? '/staff' : '')) ?>" class="px-4 py-2 rounded border border-gray-300 text-sm">Cancel</a>
            <?php if (empty($asApprover) && ! $direct): ?><button name="action" value="draft" class="px-4 py-2 rounded border border-gray-300 text-sm font-medium">Save as Draft</button><?php endif; ?>
            <button name="action" value="submit" class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold"><?= ! empty($asApprover) ? 'Save Changes' : ($direct ? 'Save Issued Pass' : 'Submit') ?></button>
        </div>
    </form>
</div>
<?= view('vehicles/_foot') ?>
