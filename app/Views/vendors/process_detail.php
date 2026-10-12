<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title><?= esc($pageTitle) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: "#137fec" }, fontFamily: { sans: ["Montserrat","sans-serif"] } } } };</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important}</style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans text-gray-800 dark:text-gray-200 min-h-screen flex">
    <?= view('partials/sidebar') ?>
    <main class="flex-1 overflow-y-auto p-4 md:p-8">
        <div class="max-w-6xl mx-auto space-y-6">

            <?php $reg = $vendor['type_of_registration'] ?? ''; ?>

            <div class="flex items-center justify-between">
                <div>
                    <a href="<?= base_url('vendors/process-list') ?>" class="text-xs text-primary hover:underline flex items-center gap-1 mb-1">
                        <span class="material-symbols-outlined text-sm">arrow_back</span> Back to Process List
                    </a>
                    <h1 class="text-xl font-bold uppercase">Vendor Pass Info<?= $reg ? ' - ' . esc($reg) : '' ?></h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= esc($vendor['app_no'] ?? 'N/A') ?> &middot; <?= esc($vendor['vendor_company_name'] ?? '') ?></p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary"><?= esc($vendor['status']) ?></span>
            </div>

            <!-- Application Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-bold uppercase mb-4">Application Info</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div><label class="block text-xs font-semibold mb-1">Application Number</label><input value="<?= esc($vendor['app_no'] ?? '') ?>" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded px-3 py-2 text-sm text-gray-500" disabled/></div>
                    <div><label class="block text-xs font-semibold mb-1">Date Of Application</label><input value="<?= esc($vendor['date_of_application'] ?? '') ?>" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded px-3 py-2 text-sm text-gray-500" disabled/></div>
                    <div><label class="block text-xs font-semibold mb-1">Type Of Application</label><input value="<?= esc($vendor['type_of_application'] ?? '') ?>" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded px-3 py-2 text-sm text-gray-500" disabled/></div>
                    <div><label class="block text-xs font-semibold mb-1">Type Of Registration</label><input id="f_type_of_registration" value="<?= esc($vendor['type_of_registration'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                    <div><label class="block text-xs font-semibold mb-1">Designation</label><input id="f_designation" value="<?= esc($vendor['designation'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                    <div><label class="block text-xs font-semibold mb-1">Payment</label><input id="f_payment" value="<?= esc($vendor['payment'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                    <div><label class="block text-xs font-semibold mb-1">Resident</label><input id="f_resident" value="<?= esc($vendor['resident'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Worker Type</label>
                        <select id="f_card_type" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>>
                            <option value="" <?= empty($vendor['card_type']) ? 'selected' : '' ?>>Not assigned</option>
                            <option value="Permanent" <?= $vendor['card_type'] === 'Permanent' ? 'selected' : '' ?>>Permanent</option>
                            <option value="Temporary" <?= $vendor['card_type'] === 'Temporary' ? 'selected' : '' ?>>Temporary</option>
                        </select>
                    </div>
                    <div><label class="block text-xs font-semibold mb-1">Pass Expiry</label><input id="f_pass_expiry" type="date" value="<?= esc($vendor['pass_expiry'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                    <div class="md:col-span-4">
                        <label class="block text-xs font-semibold mb-1">Remark</label>
                        <textarea id="f_remark" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>><?= esc($vendor['remark'] ?? '') ?></textarea>
                    </div>
                </div>
                <?php
                    $locSel    = array_values(array_filter(array_map('trim', (array) ($selectedLocations ?? []))));
                    $locLabels = array_map('strval', $locationOptions);
                ?>
                <div class="mt-4 relative"
                     x-data="{ open: false, q: '', selected: <?= esc(json_encode($locSel), 'attr') ?>, labels: <?= esc(json_encode($locLabels), 'attr') ?>, editable: <?= ($canEdit ?? false) ? 'true' : 'false' ?>,
                               get codes() { return Object.keys(this.labels); },
                               get shown() { const q = this.q.toLowerCase(); return this.codes.filter(c => this.labels[c].toLowerCase().includes(q)); },
                               get allOn() { return this.codes.length > 0 && this.selected.length === this.codes.length; },
                               toggleAll() { this.selected = this.allOn ? [] : [...this.codes]; },
                               remove(c) { if (this.editable) this.selected = this.selected.filter(x => x !== c); },
                               init() { window.getLocationAccess = () => this.selected; } }"
                     @click.outside="open = false" @keydown.escape.window="open = false">
                    <label class="block text-xs font-semibold mb-2">Location Access</label>
                    <button type="button" @click="editable && (open = !open)" :class="editable ? 'cursor-pointer' : 'cursor-default bg-gray-50 dark:bg-gray-900'"
                        class="w-full min-h-[2.75rem] flex items-center justify-between gap-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-900 px-3 py-2 text-left text-sm">
                        <span class="flex flex-wrap gap-1.5 items-center">
                            <span x-show="selected.length === 0" class="text-gray-400" x-text="editable ? '-- Select location(s) --' : 'No location access.'"></span>
                            <template x-for="c in selected" :key="c">
                                <span class="inline-flex items-center gap-1 rounded-full bg-primary/10 text-primary px-2.5 py-0.5 text-xs font-medium">
                                    <span x-text="labels[c] || c"></span>
                                    <span x-show="editable" class="cursor-pointer font-bold leading-none" @click.stop="remove(c)" title="Remove">&times;</span>
                                </span>
                            </template>
                        </span>
                        <span x-show="editable" class="material-symbols-outlined text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-cloak class="absolute z-30 mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 shadow-lg">
                        <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                            <input type="text" x-model="q" placeholder="Search location..." class="w-full rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 px-3 py-1.5 text-sm"/>
                        </div>
                        <label class="flex items-center gap-2 px-3 py-2 text-sm font-semibold border-b border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                            <input type="checkbox" :checked="allOn" @change="toggleAll()"/>
                            Select All <span class="text-gray-400 font-normal" x-text="'(' + selected.length + '/' + codes.length + ')'"></span>
                        </label>
                        <div class="max-h-64 overflow-y-auto py-1">
                            <template x-for="c in shown" :key="c">
                                <label class="flex items-center gap-2 px-3 py-1.5 text-sm cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <input type="checkbox" :value="c" x-model="selected"/>
                                    <span x-text="labels[c]"></span>
                                </label>
                            </template>
                            <p x-show="shown.length === 0" class="px-3 py-2 text-sm text-gray-500">No location found.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-bold uppercase mb-4">Company</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><label class="block text-xs font-semibold mb-1">Company Registration ID</label><input value="<?= esc($vendor['vendor_company_reg_id'] ?? '') ?>" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded px-3 py-2 text-sm text-gray-500" disabled/></div>
                    <div><label class="block text-xs font-semibold mb-1">Company Name</label><input id="f_vendor_company_name" value="<?= esc($vendor['vendor_company_name'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                </div>
            </div>

            <!-- Person -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-bold uppercase mb-4">Person</h2>
                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm flex-1">
                        <div><label class="block text-xs font-semibold mb-1">In/Out Bound</label><input id="f_in_out_bound" value="<?= esc($vendor['in_out_bound'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                        <div><label class="block text-xs font-semibold mb-1">IC Number</label><input value="<?= esc($icPassport) ?>" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded px-3 py-2 text-sm text-gray-500" disabled/></div>
                        <div><label class="block text-xs font-semibold mb-1">Full Name</label><input id="f_full_name" value="<?= esc($vendor['full_name'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                        <div><label class="block text-xs font-semibold mb-1">Name On Vendor Pass</label><input id="f_name_on_vendor_pass" value="<?= esc($vendor['name_on_vendor_pass'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                        <div><label class="block text-xs font-semibold mb-1">Birthday</label><input value="<?= esc($vendor['dob'] ?? '') ?>" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded px-3 py-2 text-sm text-gray-500" disabled/></div>
                        <div><label class="block text-xs font-semibold mb-1">Sex</label><input value="<?= esc($vendor['sex'] ?? '') ?>" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded px-3 py-2 text-sm text-gray-500" disabled/></div>
                        <div><label class="block text-xs font-semibold mb-1">Contact Number</label><input id="f_contact_no" value="<?= esc($vendor['contact_no'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                        <div><label class="block text-xs font-semibold mb-1">Email</label><input id="f_email" value="<?= esc($vendor['email'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                        <div><label class="block text-xs font-semibold mb-1">Staff No.</label><input id="f_staff_no" value="<?= esc($vendor['staff_no'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                        <div><label class="block text-xs font-semibold mb-1">Address 1</label><input id="f_address_1" value="<?= esc($vendor['address_1'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                        <div><label class="block text-xs font-semibold mb-1">Address 2</label><input id="f_address_2" value="<?= esc($vendor['address_2'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                        <div><label class="block text-xs font-semibold mb-1">Address 3</label><input id="f_address_3" value="<?= esc($vendor['address_3'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                        <div><label class="block text-xs font-semibold mb-1">Country</label><input id="f_country" value="<?= esc($vendor['country'] ?: 'Malaysia') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                        <div>
                            <label class="block text-xs font-semibold mb-1">State</label>
                            <select id="f_state" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>>
                                <option value="">Select...</option>
                                <?php foreach ($stateOptions as $st): ?>
                                <option value="<?= esc($st) ?>" <?= ($vendor['state'] ?? '') === $st ? 'selected' : '' ?>><?= esc($st) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div><label class="block text-xs font-semibold mb-1">City</label><input id="f_city" value="<?= esc($vendor['city'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                        <div><label class="block text-xs font-semibold mb-1">Postal Code</label><input id="f_postcode" value="<?= esc($vendor['postcode'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                        <div><label class="block text-xs font-semibold mb-1">Vehicle Registration Number</label><input id="f_vehicle_registration" value="<?= esc($vendor['vehicle_registration'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/></div>
                    </div>
                    <div class="flex flex-col items-center gap-2 w-full lg:w-44">
                        <img id="currentPhoto" src="<?= $vendor['facial_photo'] ? base_url('uploads/facial_photos/' . $vendor['facial_photo']) : ($vendor['photo'] ? base_url('uploads/vendor_photos/' . $vendor['photo']) : base_url('assets/images/avatar-placeholder.png')) ?>"
                            class="w-full lg:w-40 h-40 object-cover rounded-lg border border-gray-200 dark:border-gray-700"/>
                        <?php if ($canUploadPhoto ?? false): ?>
                        <div class="flex gap-2">
                            <label class="h-9 w-9 rounded-lg border border-gray-300 dark:border-gray-600 flex items-center justify-center cursor-pointer" title="Upload from file">
                                <span class="material-symbols-outlined text-base">upload</span>
                                <input type="file" accept="image/*" class="hidden" onchange="uploadPhotoFile(this.files[0])"/>
                            </label>
                            <button onclick="openCamera()" class="h-9 w-9 rounded-lg border border-gray-300 dark:border-gray-600 flex items-center justify-center" title="Take Photo">
                                <span class="material-symbols-outlined text-base">photo_camera</span>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Driving License -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-bold uppercase mb-4">Driving License</h2>
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase">
                            <th class="p-2 border-b">Class</th><th class="p-2 border-b">Expiry</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($licenses)): ?>
                        <tr><td colspan="2" class="p-4 text-center text-gray-500">No Record</td></tr>
                        <?php else: foreach ($licenses as $lic): ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="p-2"><?= esc($lic['license_class']) ?></td>
                            <td class="p-2"><?= esc($lic['license_expiry']) ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (($canEdit ?? false) || ($canReject ?? false) || ($canUploadPhoto ?? false)): ?>
            <div class="flex justify-end gap-2">
                <a href="<?= base_url('vendors/process-list') ?>" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold flex items-center">Back</a>
                <button onclick="location.reload()" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold">Refresh</button>
                <?php if ($canEdit ?? false): ?>
                <button onclick="saveDetails()" class="h-9 px-4 rounded-lg bg-primary text-white text-sm font-semibold">Update</button>
                <?php endif; ?>
                <?php if ($canReject ?? false): ?>
                <button onclick="openReject()" class="h-9 px-4 rounded-lg bg-red-600 text-white text-sm font-semibold">Reject</button>
                <?php endif; ?>
                <?php if ($canUploadPhoto ?? false): ?>
                <label class="h-9 px-4 rounded-lg bg-emerald-600 text-white text-sm font-semibold flex items-center gap-1.5 cursor-pointer">
                    Upload Photo
                    <input type="file" accept="image/*" class="hidden" onchange="uploadPhotoFile(this.files[0])"/>
                </label>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Card / RFID -->
            <?php if ($showRfidSection ?? true): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-bold uppercase mb-4">Physical Card</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    <?php if ($boundCard): ?>
                        Bound to card <span class="font-mono font-semibold"><?= esc($boundCard['card_id']) ?></span> — status: <?= esc($boundCard['status']) ?>
                    <?php else: ?>
                        No physical card bound yet.
                    <?php endif; ?>
                </p>
                <p class="text-[11px] text-gray-400">The card is linked when you press <strong>Finish</strong> and scan it.</p>
            </div>
            <?php endif; ?>

            <!--
                Urine Test History was removed from this page for now — the
                supervisor said that tracking is for a later phase. The
                backing table/controller/routes are still in the codebase
                (harmless if unused) so this can be switched back on later
                without rebuilding it from scratch.
            -->

            <!-- Card / Print -->
            <?php if ($showPrintingSection ?? true): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold uppercase">Card Printing</h2>
                    <?php if ($canEdit ?? false): ?>
                    <?php
                    // Steps in order: 1 card photo -> 2 Card Details (expiry saved) -> 3 Print -> 4 Finish.
                    // Vendor Workflow: a step switched off in Vendor Workflow no longer blocks the next one.
                    $stepPhoto   = ! ($needPhoto ?? true) || ! empty($vendor['facial_photo']) || ! empty($vendor['photo']);
                    $stepDetails = $stepPhoto && ! empty($vendor['pass_expiry']);
                    $stepPrinted = $stepDetails && (! ($needPrint ?? true) || ! empty($printLogs));
                    $lock = static fn(bool $ok) => $ok ? '' : ' opacity-50 cursor-not-allowed';
                    ?>
                    <div class="flex gap-2">
                        <button onclick="stepGo(<?= $stepPhoto ? 'true' : 'false' ?>, 'Upload the card photo first.', openCardDetails)" class="h-8 px-3 rounded-lg bg-indigo-600 text-white text-xs font-semibold<?= $lock($stepPhoto) ?>" title="<?= $stepPhoto ? '' : 'Upload the card photo first' ?>">Card Details</button>
                        <button onclick="stepGo(<?= $stepDetails ? 'true' : 'false' ?>, '<?= $stepPhoto ? 'Save the expiry date in Card Details first.' : 'Upload the card photo first.' ?>', printCard)" class="h-8 px-3 rounded-lg bg-primary text-white text-xs font-semibold<?= $lock($stepDetails) ?>" title="<?= $stepDetails ? '' : 'Complete the earlier steps first' ?>">Print</button>
                        <button onclick="stepGo(<?= $stepPrinted ? 'true' : 'false' ?>, '<?= $stepDetails ? 'Print the card first.' : ($stepPhoto ? 'Save the expiry date in Card Details first.' : 'Upload the card photo first.') ?>', openScanCard)" class="h-8 px-3 rounded-lg bg-emerald-600 text-white text-xs font-semibold<?= $lock($stepPrinted) ?>" title="<?= $stepPrinted ? '' : 'Complete the earlier steps first' ?>">Finish</button>
                    </div>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Receipt No: <span class="font-mono"><?= esc($vendor['receipt_no'] ?? '-') ?></span> &middot; Card Status: <?= esc($vendor['card_status'] ?? '-') ?></p>

                <h3 class="text-xs font-bold uppercase mt-4 mb-2 text-gray-500">Reprint History</h3>
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase">
                            <th class="p-2 border-b">Date</th><th class="p-2 border-b">Receipt No</th><th class="p-2 border-b">Reprint?</th><th class="p-2 border-b">By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($printLogs)): ?>
                        <tr><td colspan="4" class="p-4 text-center text-gray-500">Not printed yet.</td></tr>
                        <?php else: foreach ($printLogs as $l): ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="p-2"><?= esc($l['printed_at']) ?></td>
                            <td class="p-2 font-mono"><?= esc($l['receipt_no']) ?></td>
                            <td class="p-2"><?= $l['is_reprint'] ? 'Yes' : 'No' ?></td>
                            <td class="p-2"><?= esc($l['printed_by'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            </div>

        </div>
    </main>

    <!-- Card Details Modal: expiry date of the card -->
    <div id="cardDetailsModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-bold uppercase">Card Details</h3>
                <button onclick="closeCardDetails()" class="h-8 w-8 rounded-full bg-gray-700 text-white">&times;</button>
            </div>
            <div class="p-4">
                <label class="block text-sm mb-1">Expiry Date <span class="text-red-500">(*)</span></label>
                <input id="cd_expiry" type="date" value="<?= esc($vendor['pass_expiry'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm"/>
            </div>
            <div class="flex justify-end p-4 border-t dark:border-gray-700">
                <button onclick="saveCardDetails()" class="h-9 px-5 rounded-lg bg-indigo-600 text-white text-sm font-semibold">Save</button>
            </div>
        </div>
    </div>

    <!-- Scan Card Modal: Finish = link the real card and end the Process List -->
    <div id="scanCardModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-bold">Scan Card</h3>
                <button onclick="closeScanCard()" class="h-7 w-7 border border-gray-400 text-sm">&times;</button>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label class="block text-sm mb-1"><span class="text-red-500">*</span>Pass No</label>
                    <input id="sc_pass_no" value="<?= esc($passNoDefault ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm"/>
                </div>
                <div>
                    <label class="block text-sm mb-1"><span class="text-red-500">*</span>Card ID</label>
                    <input id="sc_card_id" autocomplete="off" placeholder="Tap the card on the reader" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" onkeydown="if(event.key==='Enter'){finishReadCard();}"/>
                </div>
            </div>
            <div class="flex justify-end p-4 border-t dark:border-gray-700">
                <button onclick="finishReadCard()" class="h-9 px-5 rounded-lg bg-emerald-500 text-white text-sm font-semibold">Read Card</button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-sm p-6">
            <h2 class="text-base font-bold mb-3">Reject Vendor Pass</h2>
            <label class="block text-xs font-semibold mb-1">Reason</label>
            <select id="rejectReasonId" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm mb-3">
                <option value="">Select a reason...</option>
                <?php foreach ($rejectReasons as $r): ?>
                <option value="<?= $r['id'] ?>"><?= esc($r['reason'] ?? $r['name'] ?? '') ?></option>
                <?php endforeach; ?>
            </select>
            <label class="block text-xs font-semibold mb-1">Remark (optional)</label>
            <textarea id="rejectRemark" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm mb-4"></textarea>
            <div class="flex justify-end gap-2">
                <button onclick="closeReject()" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm">Cancel</button>
                <button onclick="submitReject()" class="h-9 px-4 rounded-lg bg-red-600 text-white text-sm font-semibold">Confirm Reject</button>
            </div>
        </div>
    </div>

    <!-- Camera Modal -->
    <div id="cameraModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6 text-center">
            <h2 class="text-base font-bold mb-3">Take Photo</h2>
            <video id="cameraVideo" autoplay playsinline class="w-full rounded-lg bg-black mb-3" style="max-height:320px;"></video>
            <canvas id="cameraCanvas" class="hidden"></canvas>
            <img id="cameraPreview" class="hidden w-full rounded-lg mb-3" style="max-height:320px; object-fit:contain; margin-left:auto; margin-right:auto;"/>
            <div id="cameraLiveBtns" class="flex justify-center gap-2">
                <button onclick="closeCamera()" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm">Cancel</button>
                <button onclick="rotatePreview()" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm hidden" id="rotateBtn">Rotate</button>
                <button onclick="takeSnapshot()" id="snapBtn" class="h-9 px-4 rounded-lg bg-primary text-white text-sm font-semibold">Capture</button>
            </div>
            <div id="cameraPreviewBtns" class="justify-center gap-2 hidden">
                <button onclick="retakePhoto()" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm">Retake</button>
                <button onclick="rotatePreview()" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm">Rotate</button>
                <button onclick="usePhoto()" class="h-9 px-4 rounded-lg bg-primary text-white text-sm font-semibold">Use Photo</button>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/vendor-card-print.js') ?>"></script>

    <script>
        const VENDOR_ID = <?= (int) $vendor['id'] ?>;
        const CSRF = { 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' };

        function postJson(url, body) {
            return fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', ...CSRF }, body: JSON.stringify(body || {}) })
                .then(r => { if (!r.ok) throw new Error('http_' + r.status); return r.json(); });
        }
        function postForm(url, formData) {
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            return fetch(url, { method: 'POST', body: formData }).then(r => { if (!r.ok) throw new Error('http_' + r.status); return r.json(); });
        }
        function netErr() { alert('Could not reach the server. Please check your connection and try again.'); }

        // --- Save details ---
        function val(id) { const el = document.getElementById(id); return el ? el.value : ''; }
        function saveDetails() {
            const locationAccess = typeof window.getLocationAccess === 'function' ? Array.from(window.getLocationAccess()) : [];
            postJson('<?= base_url('vendors/process-list/detail/update/') ?>' + VENDOR_ID, {
                vendor_company_name: val('f_vendor_company_name'),
                full_name: val('f_full_name'),
                name_on_vendor_pass: val('f_name_on_vendor_pass'),
                contact_no: val('f_contact_no'),
                email: val('f_email'),
                designation: val('f_designation'),
                pass_expiry: val('f_pass_expiry'),
                card_type: val('f_card_type'),
                remark: val('f_remark'),
                type_of_registration: val('f_type_of_registration'),
                payment: val('f_payment'),
                resident: val('f_resident'),
                in_out_bound: val('f_in_out_bound'),
                staff_no: val('f_staff_no'),
                address_1: val('f_address_1'),
                address_2: val('f_address_2'),
                address_3: val('f_address_3'),
                country: val('f_country'),
                state: val('f_state'),
                city: val('f_city'),
                postcode: val('f_postcode'),
                vehicle_registration: val('f_vehicle_registration'),
                location_access: locationAccess,
            }).then(d => alert(d.message)).catch(netErr);
        }

        // --- Reject ---
        function openReject() { document.getElementById('rejectModal').classList.remove('hidden'); }
        function closeReject() { document.getElementById('rejectModal').classList.add('hidden'); }
        function submitReject() {
            postJson('<?= base_url('vendors/process-list/detail/reject/') ?>' + VENDOR_ID, {
                reject_reason_id: document.getElementById('rejectReasonId').value,
                remark: document.getElementById('rejectRemark').value,
            }).then(d => { alert(d.message); if (d.success) location.href = '<?= base_url('vendors/process-list') ?>'; }).catch(netErr);
        }

        // --- Photo upload (file) ---
        function uploadPhotoFile(file) {
            if (!file) return;
            const fd = new FormData();
            fd.append('photo', file);
            postForm('<?= base_url('vendors/process-list/detail/upload-photo/') ?>' + VENDOR_ID, fd)
                .then(d => { if (d.success) { document.getElementById('currentPhoto').src = d.photo_url; location.reload(); } else alert(d.message); })
                .catch(netErr);
        }

        // --- Live camera capture ---
        let cameraStream = null, capturedDataUrl = null, rotation = 0;
        function openCamera() {
            document.getElementById('cameraModal').classList.remove('hidden');
            document.getElementById('cameraPreview').classList.add('hidden');
            document.getElementById('cameraPreviewBtns').classList.add('hidden');
            document.getElementById('cameraPreviewBtns').classList.remove('flex');
            document.getElementById('cameraLiveBtns').classList.remove('hidden');
            const video = document.getElementById('cameraVideo');
            video.classList.remove('hidden');
            navigator.mediaDevices.getUserMedia({ video: true }).then(stream => {
                cameraStream = stream;
                video.srcObject = stream;
            }).catch(() => { alert('Could not access the camera. You can still use "Upload from file" instead.'); closeCamera(); });
        }
        function closeCamera() {
            if (cameraStream) { cameraStream.getTracks().forEach(t => t.stop()); cameraStream = null; }
            document.getElementById('cameraModal').classList.add('hidden');
        }
        function takeSnapshot() {
            const video = document.getElementById('cameraVideo');
            const canvas = document.getElementById('cameraCanvas');
            canvas.width = video.videoWidth; canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            capturedDataUrl = canvas.toDataURL('image/jpeg');
            rotation = 0;
            showPreview();
        }
        function showPreview() {
            const img = document.getElementById('cameraPreview');
            img.src = capturedDataUrl;
            img.style.transform = 'rotate(' + rotation + 'deg)';
            img.classList.remove('hidden');
            document.getElementById('cameraVideo').classList.add('hidden');
            document.getElementById('cameraLiveBtns').classList.add('hidden');
            document.getElementById('cameraPreviewBtns').classList.remove('hidden');
            document.getElementById('cameraPreviewBtns').classList.add('flex');
        }
        function retakePhoto() {
            capturedDataUrl = null;
            document.getElementById('cameraPreview').classList.add('hidden');
            document.getElementById('cameraVideo').classList.remove('hidden');
            document.getElementById('cameraPreviewBtns').classList.add('hidden');
            document.getElementById('cameraLiveBtns').classList.remove('hidden');
        }
        function rotatePreview() {
            rotation = (rotation + 90) % 360;
            const canvas = document.getElementById('cameraCanvas');
            const img = new Image();
            img.onload = () => {
                const rad = rotation * Math.PI / 180;
                const swap = rotation % 180 !== 0;
                canvas.width = swap ? img.height : img.width;
                canvas.height = swap ? img.width : img.height;
                const ctx = canvas.getContext('2d');
                ctx.save();
                ctx.translate(canvas.width / 2, canvas.height / 2);
                ctx.rotate(rad);
                ctx.drawImage(img, -img.width / 2, -img.height / 2);
                ctx.restore();
                capturedDataUrl = canvas.toDataURL('image/jpeg');
                rotation = 0;
                document.getElementById('cameraPreview').src = capturedDataUrl;
                document.getElementById('cameraPreview').style.transform = '';
            };
            img.src = capturedDataUrl;
        }
        function usePhoto() {
            postJson('<?= base_url('vendors/process-list/detail/upload-photo/') ?>' + VENDOR_ID, { photo_data: capturedDataUrl })
                .then(d => { if (d.success) { document.getElementById('currentPhoto').src = d.photo_url; closeCamera(); location.reload(); } else alert(d.message); })
                .catch(netErr);
        }

        // A step button that is not ready yet looks faded and explains what to do first.
        function stepGo(ready, message, action) {
            if (!ready) { alert(message); return; }
            action();
        }

        // --- Card Details (expiry date) ---
        function openCardDetails() { document.getElementById('cardDetailsModal').classList.remove('hidden'); }
        function closeCardDetails() { document.getElementById('cardDetailsModal').classList.add('hidden'); }
        function saveCardDetails() {
            const d = document.getElementById('cd_expiry').value;
            if (!d) { alert('Expiry Date is required.'); return; }
            postJson('<?= base_url('vendors/process-list/detail/update/') ?>' + VENDOR_ID, { pass_expiry: d })
                .then(r => { alert(r.message); if (r.success) location.reload(); })
                .catch(netErr);
        }

        // --- Finish: scan the real card, link it and end the Process List ---
        function openScanCard() {
            document.getElementById('scanCardModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('sc_card_id').focus(), 50);
        }
        function closeScanCard() { document.getElementById('scanCardModal').classList.add('hidden'); }
        function finishReadCard() {
            const epc = document.getElementById('sc_card_id').value.trim();
            const passNo = document.getElementById('sc_pass_no').value.trim();
            if (!passNo || !epc) { alert('Pass No and Card ID are required.'); return; }
            postJson('<?= base_url('vendors/process-list/detail/read-card/') ?>' + VENDOR_ID, { card_epc: epc, pass_no: passNo, finish: true })
                .then(d => {
                    alert(d.message);
                    if (d.success) location.href = '<?= base_url('vendors/process-list') ?>';
                })
                .catch(netErr);
        }


        // --- Card print (front + back, cards only - see assets/js/vendor-card-print.js) ---
        function fetchCard() {
            return postJson('<?= base_url('vendors/printing-list/generate-serial/') ?>' + VENDOR_ID, { defer: true });
        }
        function printCard() {
            fetchCard().then(d => {
                if (!d.success) { alert(d.message); return; }
                VendorCardPrint.print([d.card], () => location.reload());
            }).catch(netErr);
        }

    </script>
</body>
</html>
