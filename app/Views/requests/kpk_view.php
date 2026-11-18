<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php
// KPK visitor-request-list details: Application Info, Date Of Visit, Details Of Visit, Person, Driving License,
// Company, Asset/Equipment Details, Upload; Back / Edit Vehicle Info / Approve / Reject.
$v     = static fn($x) => ($x === null || $x === '') ? '-' : esc((string) $x);
$d     = static fn($x, $f = 'd/m/Y') => $x ? date($f, strtotime((string) $x)) : '-';
$card  = 'bg-white dark:bg-gray-900 rounded-lg shadow p-5';
$h     = 'text-sm font-bold uppercase text-primary mb-3';
$field = static fn(string $label, string $value) => '<div><p class="text-xs text-gray-500 dark:text-gray-400">' . esc($label) . '</p><p class="text-sm font-medium text-gray-800 dark:text-gray-100 break-words">' . $value . '</p></div>';
$reason = in_array((string) ($inv['reason'] ?? ''), ['', 'OTHER'], true) ? (($inv['other_reason'] ?? '') ?: 'OTHER') : $inv['reason'];
$input  = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm';
?>
<div class="-m-2 space-y-4" x-data="{ modal: null, busy: false, remark: '', cat: <?= esc(json_encode((string) ($inv['vehicle_category'] ?? '')), 'attr') ?>, type: <?= esc(json_encode((string) ($inv['vehicle_type'] ?? '')), 'attr') ?>, types: <?= esc(json_encode($vehicleTypes), 'attr') ?> }">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="<?= base_url('requests') ?>" class="text-xs text-primary hover:underline flex items-center gap-1 mb-1"><span class="material-symbols-outlined text-sm">arrow_back</span> Back to Request List</a>
            <h1 class="text-2xl font-bold uppercase text-gray-800 dark:text-white"><?= esc($inv['full_name']) ?></h1>
            <p class="text-sm text-gray-500"><?= $v($visitorType ?: 'Visitor') ?></p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold <?= ['Submitted' => 'bg-amber-100 text-amber-700', 'Approved' => 'bg-green-100 text-green-700', 'Rejected' => 'bg-red-100 text-red-700'][$inv['status']] ?? 'bg-gray-100 text-gray-700' ?>"><?= esc($inv['status'] === 'Submitted' ? 'Pending' : $inv['status']) ?></span>
    </div>

    <?php foreach (['success' => 'green', 'error' => 'red'] as $k => $c): if ($m = session()->getFlashdata($k)): ?>
        <div class="rounded-lg border border-<?= $c ?>-200 bg-<?= $c ?>-50 px-4 py-3 text-sm text-<?= $c ?>-700"><?= esc($m) ?></div>
    <?php endif; endforeach; ?>

    <div class="<?= $card ?>">
        <h2 class="<?= $h ?>">Application Info</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?= $field('Date Of Application', $d($inv['created_at'])) ?>
            <?= $field('Application Number', $v('VR-' . str_pad((string) $inv['id'], 6, '0', STR_PAD_LEFT))) ?>
            <?= $field('Requested By', $v($inv['invited_by'] ?? '')) ?>
            <?= $field('Registration', $v($inv['registration_source'] ?? '')) ?>
        </div>
        <div class="mt-4"><p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Company Visiting</p>
            <div class="flex flex-wrap gap-2"><?php foreach (array_filter(array_map('trim', explode(',', (string) ($inv['location'] ?? '')))) as $loc): ?><span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded bg-primary/10 text-primary font-semibold"><span class="material-symbols-outlined text-[14px]">check_box</span><?= esc($loc) ?></span><?php endforeach; ?></div></div>
    </div>

    <div class="<?= $card ?>">
        <h2 class="<?= $h ?>">Date Of Visit</h2>
        <?php if (! $schedules): ?><p class="text-sm text-gray-500">-</p><?php endif; ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <?php foreach ($schedules as $i => $s): ?>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                    <p class="text-xs font-bold text-gray-500 mb-2">No <?= $i + 1 ?></p>
                    <div class="grid grid-cols-2 gap-3"><?= $field('Date From', $d($s['date_from'], 'd/m/Y H:i')) ?><?= $field('Date To', $d($s['date_to'], 'd/m/Y H:i')) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="<?= $card ?>">
        <h2 class="<?= $h ?>">Details Of Visit</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <?= $field('Location Visited', $v($inv['location_visited'] ?? '')) ?>
            <?= $field('Staff Id Of Person Visited', $v($inv['staff_id'] ?? '')) ?>
            <?= $field('Name Of Company Visited', $v($inv['company_visited'] ?? '')) ?>
            <?= $field('Contact No Of Person Visited', $v($inv['host_contact'] ?? '')) ?>
            <?= $field('Reason', $v($reason)) ?>
        </div>
    </div>

    <div class="<?= $card ?>">
        <h2 class="<?= $h ?>">Person</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?= $field('Resident', $v($inv['resident'] ?? '')) ?>
            <?= $field('IC / Passport Number', $v($inv['ic_passport'] ?? '')) ?>
            <?= $field('Full Name', $v($inv['full_name'])) ?>
            <?= $field('Birthday', $d($inv['date_of_birth'] ?? null)) ?>
            <?= $field('Sex', $v($inv['sex'] ?? '')) ?>
            <?= $field('Contact Number', $v($inv['contact'] ?? '')) ?>
            <?= $field('Email', $v($inv['visitor_email'] ?? '')) ?>
            <?= $field('Address', $v($inv['address'] ?? '')) ?>
            <?= $field('Country', $v($inv['country'] ?? '')) ?>
            <?= $field('State', $v($inv['state'] ?? '')) ?>
            <?= $field('City', $v($inv['city'] ?? '')) ?>
            <?= $field('Postal Code', $v($inv['postcode'] ?? '')) ?>
            <?= $field('Vehicle Category', $v(str_replace('_', ' ', (string) ($inv['vehicle_category'] ?? '')))) ?>
            <?= $field('Vehicle Type', $v(str_replace('_', ' ', (string) ($inv['vehicle_type'] ?? '')))) ?>
            <?= $field('Vehicle Registration Number', $v($inv['vehicle_registration'] ?? '')) ?>
        </div>
    </div>

    <?php if ($licenses): ?>
    <div class="<?= $card ?>">
        <h2 class="<?= $h ?>">Driving License</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <?php foreach ($licenses as $i => $l): ?>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                    <p class="text-xs font-bold text-gray-500 mb-2">No <?= $i + 1 ?></p>
                    <div class="grid grid-cols-2 gap-3"><?= $field('License Class', $v($l['license_class'] ?? '')) ?><?= $field('License Expiry', $d($l['expiry_date'] ?? null)) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="<?= $card ?>">
        <h2 class="<?= $h ?>">Company</h2>
        <div class="grid grid-cols-2 gap-4"><?= $field('Company Registration ID', $v($inv['registration_no'] ?? '')) ?><?= $field('Company Name', $v($inv['company'] ?? '')) ?></div>
    </div>

    <?php if ($equipment): ?>
    <div class="<?= $card ?>">
        <h2 class="<?= $h ?>">Asset/Equipment Details</h2>
        <div class="space-y-3">
            <?php foreach ($equipment as $i => $e): ?>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                    <?= $field('Category', $v($e['category'] ?? '')) ?><?= $field('Size', $v($e['size'] ?? '')) ?><?= $field('Transportation Method', $v($e['transport'] ?? '')) ?><?= $field('Purpose', $v($e['purpose'] ?? '')) ?>
                    <?= $field('Type of Equipment', $v($e['equipment_type'] ?? '')) ?><?= $field('Voltage Use', $v($e['voltage'] ?? '')) ?><?= $field('Quantity', $v($e['quantity'] ?? '')) ?><?= $field('Serial Number', $v($e['serial_number'] ?? '')) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="<?= $card ?>">
        <h2 class="<?= $h ?>">Upload</h2>
        <?php if (! $uploads): ?><p class="text-sm text-gray-500">No document uploaded.</p><?php endif; ?>
        <div class="flex flex-wrap gap-3">
            <?php foreach ($uploads as $label => $urls): foreach ($urls as $n => $url): ?>
                <a href="<?= esc($url, 'attr') ?>" target="_blank" rel="noopener" class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-sm hover:border-primary">
                    <span class="material-symbols-outlined text-primary">description</span><?= esc($label) ?><?= count($urls) > 1 ? ' ' . ($n + 1) : '' ?>
                    <span class="material-symbols-outlined text-[16px] text-gray-400">download</span>
                </a>
            <?php endforeach; endforeach; ?>
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-2 pb-4">
        <a href="<?= base_url('requests') ?>" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold">Back</a>
        <?php if ($canDecide): ?>
            <button type="button" @click="modal = 'vehicle'" class="px-4 py-2 rounded-lg border border-primary text-primary text-sm font-semibold">Edit Vehicle Info</button>
            <button type="button" @click="modal = 'approve'" class="px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-semibold">Approve</button>
            <button type="button" @click="modal = 'reject'; remark = ''" class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold">Reject</button>
        <?php endif; ?>
    </div>

    <?php if ($canDecide): ?>
    <!-- Approve / Reject confirmation (KPK approveModal / rejectModal) -->
    <div x-show="modal === 'approve' || modal === 'reject'" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="modal = null">
        <div class="bg-white dark:bg-gray-900 rounded-lg w-full max-w-md p-5 space-y-3">
            <h3 class="font-bold text-lg" x-text="modal === 'approve' ? 'Approve Confirmation' : 'Reject Confirmation'"></h3>
            <p class="text-sm text-gray-600 dark:text-gray-300" x-text="modal === 'approve' ? 'Approve this visitor request?' : 'Reject this visitor request?'"></p>
            <div x-show="modal === 'reject'"><label class="block text-sm mb-1">Remark <span class="text-red-500">*</span></label><textarea x-model="remark" rows="3" class="<?= $input ?>"></textarea></div>
            <div class="flex justify-end gap-2">
                <button type="button" @click="modal = null" class="px-4 py-2 rounded border text-sm">Cancel</button>
                <button type="button" :disabled="busy || (modal === 'reject' && ! remark.trim())" class="px-4 py-2 rounded text-white text-sm font-semibold disabled:opacity-50" :class="modal === 'approve' ? 'bg-green-600' : 'bg-red-600'"
                    @click="busy = true; fetch('<?= base_url('requests') ?>/' + modal, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ id: <?= (int) $inv['id'] ?>, reason: remark }) })
                        .then(r => r.json()).then(d => { alert(d.message || (d.success ? 'Done.' : 'Failed.')); if (d.success) location.href = '<?= base_url('requests') ?>'; }).finally(() => busy = false)"
                    x-text="busy ? 'Saving...' : (modal === 'approve' ? 'Approve' : 'Reject')"></button>
            </div>
        </div>
    </div>

    <!-- Edit Vehicle Info (KPK changeVinInfo) -->
    <div x-show="modal === 'vehicle'" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="modal = null">
        <form method="post" action="<?= base_url('requests/vehicle/' . (int) $inv['id']) ?>" class="bg-white dark:bg-gray-900 rounded-lg w-full max-w-md p-5 space-y-3">
            <?= csrf_field() ?>
            <h3 class="font-bold text-lg">Edit Vehicle Info</h3>
            <div><label class="block text-sm mb-1">Category</label>
                <select name="vehicle_category" x-model="cat" @change="type = ''" class="<?= $input ?>"><option value="">SELECT</option><template x-for="c in Object.keys(types)" :key="c"><option :value="c" x-text="c.replace('_', ' ')"></option></template></select></div>
            <div><label class="block text-sm mb-1">Type Of Vehicle</label>
                <select name="vehicle_type" x-model="type" class="<?= $input ?>"><option value="" x-text="cat ? 'SELECT' : 'SELECT CATEGORY FIRST'"></option><template x-for="(label, key) in (types[cat] || {})" :key="key"><option :value="key" x-text="label" :selected="key === type"></option></template></select></div>
            <div class="flex justify-end gap-2"><button type="button" @click="modal = null" class="px-4 py-2 rounded border text-sm">Cancel</button><button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Save</button></div>
        </form>
    </div>
    <?php endif; ?>
</div>

<?= view('staffs/_layout_bottom') ?>
