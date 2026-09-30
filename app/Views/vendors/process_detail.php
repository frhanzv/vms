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
                <div class="mt-4">
                    <label class="block text-xs font-semibold mb-2">Location Access</label>
                    <div class="flex flex-wrap gap-4">
                        <?php foreach ($locationOptions as $code => $labelText): ?>
                        <label class="flex items-center gap-1.5 text-sm">
                            <input type="checkbox" class="f_location_access" value="<?= esc($code) ?>" <?= in_array($code, $selectedLocations ?? [], true) ? 'checked' : '' ?> <?= ($canEdit ?? false) ? '' : 'disabled' ?>/>
                            <?= esc($labelText) ?>
                        </label>
                        <?php endforeach; ?>
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
                        <?php if ($canEdit ?? false): ?>
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

            <?php if ($canEdit ?? false): ?>
            <div class="flex justify-end gap-2">
                <a href="<?= base_url('vendors/process-list') ?>" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold flex items-center">Back</a>
                <button onclick="location.reload()" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold">Refresh</button>
                <button onclick="saveDetails()" class="h-9 px-4 rounded-lg bg-primary text-white text-sm font-semibold">Update</button>
                <button onclick="openReject()" class="h-9 px-4 rounded-lg bg-red-600 text-white text-sm font-semibold">Reject</button>
                <label class="h-9 px-4 rounded-lg bg-emerald-600 text-white text-sm font-semibold flex items-center gap-1.5 cursor-pointer">
                    Upload Photo
                    <input type="file" accept="image/*" class="hidden" onchange="uploadPhotoFile(this.files[0])"/>
                </label>
            </div>
            <?php endif; ?>

            <!-- Card / RFID -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-bold uppercase mb-4">Physical Card</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    <?php if ($boundCard): ?>
                        Bound to card <span class="font-mono font-semibold"><?= esc($boundCard['card_id']) ?></span> — status: <?= esc($boundCard['status']) ?>
                    <?php else: ?>
                        No physical card bound yet.
                    <?php endif; ?>
                </p>
                <?php if ($canEdit ?? false): ?>
                <div class="flex gap-2 items-center flex-wrap">
                    <input id="cardEpcInput" placeholder="Tap RFID card or type EPC, then press Enter" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm w-80" onkeydown="if(event.key==='Enter'){readCard();}"/>
                    <button onclick="readCard()" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold">Bind Card</button>
                    <span class="text-[11px] text-gray-400">Most USB/RFID readers act like a keyboard — tap a card while this box is focused.</span>
                </div>
                <?php endif; ?>
            </div>

            <!--
                Urine Test History was removed from this page for now — the
                supervisor said that tracking is for a later phase. The
                backing table/controller/routes are still in the codebase
                (harmless if unused) so this can be switched back on later
                without rebuilding it from scratch.
            -->

            <!-- Card / Print -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold uppercase">Card Printing</h2>
                    <?php if ($canEdit ?? false): ?>
                    <div class="flex gap-2">
                        <a href="<?= base_url('vendors/card-info/view/' . $vendor['id']) ?>" class="h-8 px-3 rounded-lg border border-gray-300 dark:border-gray-600 text-xs font-semibold flex items-center">Card Details</a>
                        <button onclick="printCard()" class="h-8 px-3 rounded-lg bg-primary text-white text-xs font-semibold">Print</button>
                        <button onclick="finishAndIssue()" class="h-8 px-3 rounded-lg bg-emerald-600 text-white text-xs font-semibold">Finish (Issue Card)</button>
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

        </div>
    </main>

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

    <div id="print-section"></div>

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
            const locationAccess = Array.from(document.querySelectorAll('.f_location_access:checked')).map(el => el.value);
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
                .then(d => { if (d.success) document.getElementById('currentPhoto').src = d.photo_url; else alert(d.message); })
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
                .then(d => { if (d.success) { document.getElementById('currentPhoto').src = d.photo_url; closeCamera(); } else alert(d.message); })
                .catch(netErr);
        }

        // --- RFID card ---
        function readCard() {
            const epc = document.getElementById('cardEpcInput').value.trim();
            if (!epc) return;
            postJson('<?= base_url('vendors/process-list/detail/read-card/') ?>' + VENDOR_ID, { card_epc: epc })
                .then(d => { alert(d.message); if (d.success) location.reload(); })
                .catch(netErr);
        }


        // --- Card print ---
        function cardMarkup(card) {
            const typeClass = String(card.card_type).toLowerCase() === 'permanent' ? 'background:linear-gradient(160deg,#0f3d91,#137fec,#1e2a5e);' : 'background:linear-gradient(160deg,#92400e,#f59e0b,#78350f);';
            return `<div style="width:54mm;height:85.6mm;position:relative;overflow:hidden;border-radius:3mm;color:#fff;font-family:'Montserrat',sans-serif;${typeClass}">
                <div style="padding:4mm;display:flex;flex-direction:column;align-items:center;height:100%;box-sizing:border-box;">
                    <div style="font-size:2.6mm;letter-spacing:0.5mm;opacity:.85;margin-bottom:2mm;">SAFEG VENDOR PASS</div>
                    <img src="${card.photo_url}" style="width:22mm;height:22mm;object-fit:cover;border-radius:2mm;border:0.5mm solid rgba(255,255,255,.8);" />
                    <div style="margin-top:3mm;font-size:3.6mm;font-weight:700;text-align:center;">${card.full_name}</div>
                    <div style="font-size:2.8mm;opacity:.9;margin-top:1mm;font-family:'Courier New',monospace;">${card.ic_no}</div>
                    <div style="font-size:2.4mm;opacity:.85;margin-top:1mm;text-align:center;">${card.company}</div>
                    <div style="margin-top:auto;width:100%;text-align:center;font-size:2.6mm;font-weight:700;text-transform:uppercase;">${card.card_type}</div>
                    <div style="font-size:2.4mm;font-family:'Courier New',monospace;margin-top:0.5mm;">Valid Until: ${card.valid_until}</div>
                    <div style="font-size:2.2mm;opacity:.75;margin-top:0.5mm;font-family:'Courier New',monospace;">${card.receipt_no}</div>
                </div>
            </div>`;
        }
        function fetchCard() {
            return postJson('<?= base_url('vendors/printing-list/generate-serial/') ?>' + VENDOR_ID, {});
        }
        function printCard() {
            fetchCard().then(d => {
                if (!d.success) { alert(d.message); return; }
                document.getElementById('print-section').innerHTML = cardMarkup(d.card);
                setTimeout(() => { window.print(); location.reload(); }, 150);
            }).catch(netErr);
        }

        // --- Finish / Issue ---
        function finishAndIssue() {
            const name = prompt("Collector's full name:");
            if (!name) return;
            const ic = prompt("Collector's IC / Passport No:");
            if (!ic) return;
            postJson('<?= base_url('vendors/issuance-list/issue/') ?>' + VENDOR_ID, { collector_name: name, collector_ic_passport: ic })
                .then(d => { alert(d.message); if (d.success) location.href = '<?= base_url('vendors/closed-list') ?>'; })
                .catch(netErr);
        }
    </script>
</body>
</html>
