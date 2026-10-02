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
    <style>
        @media print {
            body * { visibility: hidden; }
            #print-section, #print-section * { visibility: visible; }
            #print-section { position: absolute; top: 0; left: 0; }
            .card-page { break-after: page; }
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans text-gray-800 dark:text-gray-200 min-h-screen flex">
    <?= view('partials/sidebar') ?>
    <main class="flex-1 overflow-y-auto p-4 md:p-8">
        <div class="max-w-5xl mx-auto space-y-6">

            <div class="flex items-center justify-between">
                <div>
                    <a href="javascript:history.back()" class="text-xs text-primary hover:underline flex items-center gap-1 mb-1">
                        <span class="material-symbols-outlined text-sm">arrow_back</span> Back
                    </a>
                    <h1 class="text-xl font-bold uppercase">Vendor Pass Card Info</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= esc($vendor['full_name'] ?? '') ?> &middot; <?= esc($vendor['app_no'] ?? 'N/A') ?></p>
                </div>
                <?php
                    $statusColor = [
                        'Active'     => 'bg-emerald-100 text-emerald-700',
                        'Inactive'   => 'bg-gray-200 text-gray-600',
                        'Terminated' => 'bg-red-100 text-red-700',
                    ][$vendor['card_status'] ?? 'Inactive'] ?? 'bg-gray-200 text-gray-600';
                ?>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $statusColor ?>">Card: <?= esc($vendor['card_status'] ?? 'Inactive') ?></span>
            </div>

            <!-- Card Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-bold uppercase mb-4">Card Info</h2>
                <div class="flex items-start gap-6 flex-wrap mb-4">
                    <img id="currentPhoto" src="<?= $vendor['facial_photo'] ? base_url('uploads/facial_photos/' . $vendor['facial_photo']) : ($vendor['photo'] ? base_url('uploads/vendor_photos/' . $vendor['photo']) : base_url('assets/images/avatar-placeholder.png')) ?>"
                        class="w-28 h-28 object-cover rounded-lg border border-gray-200 dark:border-gray-700"/>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm flex-1 min-w-[280px]">
                        <div>
                            <label class="block text-xs font-semibold mb-1">Serial No</label>
                            <input value="<?= esc($vendor['receipt_no'] ?? '-') ?>" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded px-3 py-2 text-sm text-gray-500 font-mono" disabled/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1">Card ID</label>
                            <input value="<?= esc($boundCard['card_id'] ?? '-') ?>" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded px-3 py-2 text-sm text-gray-500 font-mono" disabled/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1">Card Expiry</label>
                            <input id="f_pass_expiry" type="date" value="<?= esc($vendor['pass_expiry'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1">Collector's IC / Passport No</label>
                            <input id="f_collector_ic" value="<?= esc($vendor['collector_ic_passport'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1">Collector's Name</label>
                            <input id="f_collector_name" value="<?= esc($vendor['collector_name'] ?? '') ?>" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" <?= ($canEdit ?? false) ? '' : 'disabled' ?>/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1">Collected Date</label>
                            <input value="<?= $vendor['issued_at'] ? esc(date('d/m/Y h:i A', strtotime($vendor['issued_at']))) : '-' ?>" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded px-3 py-2 text-sm text-gray-500" disabled/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1">Issued By</label>
                            <input value="<?= esc($vendor['issued_by'] ?? '-') ?>" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded px-3 py-2 text-sm text-gray-500" disabled/>
                        </div>
                        <?php if (! empty($vendor['terminated_at'])): ?>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold mb-1">Terminated</label>
                            <input value="<?= esc(date('d/m/Y h:i A', strtotime($vendor['terminated_at']))) ?> by <?= esc($vendor['terminated_by'] ?? '-') ?>" class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded px-3 py-2 text-sm text-red-500" disabled/>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Location Access -->
                <div class="mt-2 mb-4">
                    <label class="block text-xs font-semibold mb-2">Location Access</label>
                    <div class="flex flex-wrap gap-4">
                        <?php foreach ($locationOptions as $code => $label): ?>
                        <label class="flex items-center gap-1.5 text-sm">
                            <input type="checkbox" class="loc-checkbox" value="<?= esc($code) ?>" <?= in_array($code, $selectedLocations, true) ? 'checked' : '' ?> disabled/>
                            <?= esc($label) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if ($canEdit ?? false): ?>
                <div class="flex flex-wrap justify-end gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <?php if ($canAddLicense ?? false): ?>
                    <button onclick="openLicenseModal()" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold">Add License</button>
                    <?php endif; ?>
                    <?php if ($canEditLocation ?? false): ?>
                    <button onclick="openLocationModal()" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold">Edit Location Access</button>
                    <?php endif; ?>
                    <?php if ($canUploadPhoto ?? false): ?>
                    <label class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold flex items-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-base">upload</span> Upload Photo
                        <input type="file" accept="image/*" class="hidden" onchange="uploadPhotoFile(this.files[0])"/>
                    </label>
                    <?php endif; ?>
                    <a href="<?= base_url('vendors/qr/' . $vendor['id']) ?>" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold flex items-center">QR Code</a>
                    <button onclick="reprintCard()" class="h-9 px-4 rounded-lg bg-gray-600 text-white text-sm font-semibold">Reprint</button>
                    <?php if (($vendor['card_status'] ?? '') !== 'Active' && ($canActivate ?? false)): ?>
                    <button onclick="activateCard()" class="h-9 px-4 rounded-lg bg-emerald-600 text-white text-sm font-semibold">Activate Card</button>
                    <?php endif; ?>
                    <?php if (($vendor['card_status'] ?? '') !== 'Terminated' && ($canTerminate ?? false)): ?>
                    <button onclick="terminateCard()" class="h-9 px-4 rounded-lg bg-red-600 text-white text-sm font-semibold">Card Terminate</button>
                    <?php endif; ?>
                    <button onclick="saveCardInfo()" class="h-9 px-4 rounded-lg bg-primary text-white text-sm font-semibold">Update</button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Driving License -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-bold uppercase mb-4">Driving License</h2>
                <?php if (empty($licenses)): ?>
                <p class="text-sm text-gray-500 text-center py-4">No Record</p>
                <?php else: ?>
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase">
                            <th class="p-2 border-b">No</th><th class="p-2 border-b">License Class</th><th class="p-2 border-b">License Expiry</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($licenses as $i => $lic): ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="p-2"><?= $i + 1 ?></td>
                            <td class="p-2"><?= esc($lic['license_class']) ?></td>
                            <td class="p-2"><?= $lic['license_expiry'] ? esc(date('d/m/Y', strtotime($lic['license_expiry']))) : '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

            <!-- Reprint History -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-bold uppercase mb-4">Reprint History</h2>
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

    <!-- Add License Modal -->
    <div id="licenseModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-sm p-6">
            <h2 class="text-base font-bold mb-3">Add Driving License</h2>
            <label class="block text-xs font-semibold mb-1">License Class</label>
            <input id="licClass" placeholder="e.g. D" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm mb-3"/>
            <label class="block text-xs font-semibold mb-1">License Expiry</label>
            <input id="licExpiry" type="date" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm mb-4"/>
            <div class="flex justify-end gap-2">
                <button onclick="closeLicenseModal()" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm">Cancel</button>
                <button onclick="submitLicense()" class="h-9 px-4 rounded-lg bg-primary text-white text-sm font-semibold">Save</button>
            </div>
        </div>
    </div>

    <!-- Location Access Modal -->
    <div id="locationModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-sm p-6">
            <h2 class="text-base font-bold mb-3">Edit Location Access</h2>
            <div class="flex flex-col gap-2 mb-4">
                <?php foreach ($locationOptions as $code => $label): ?>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" class="loc-modal-checkbox" value="<?= esc($code) ?>" <?= in_array($code, $selectedLocations, true) ? 'checked' : '' ?>/>
                    <?= esc($label) ?>
                </label>
                <?php endforeach; ?>
            </div>
            <div class="flex justify-end gap-2">
                <button onclick="closeLocationModal()" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm">Cancel</button>
                <button onclick="submitLocationAccess()" class="h-9 px-4 rounded-lg bg-primary text-white text-sm font-semibold">Save</button>
            </div>
        </div>
    </div>

    <div id="print-section"></div>

    <script>
        const VENDOR_ID = <?= (int) $vendor['id'] ?>;
        const CSRF = { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' };

        function postJson(url, body) {
            return fetch(url, { method: 'POST', headers: CSRF, body: JSON.stringify(body || {}) })
                .then(r => { if (!r.ok) throw new Error('http_' + r.status); return r.json(); });
        }
        function netErr(err) {
            if (String(err.message).startsWith('http_')) {
                alert('Something went wrong on the server. If this keeps happening, check that all migrations have been run and the route is registered.');
            } else {
                alert('Could not reach the server. Please check your connection and try again.');
            }
        }

        // --- Add License ---
        function openLicenseModal() { document.getElementById('licenseModal').classList.remove('hidden'); }
        function closeLicenseModal() { document.getElementById('licenseModal').classList.add('hidden'); }
        function submitLicense() {
            const license_class = document.getElementById('licClass').value.trim();
            const license_expiry = document.getElementById('licExpiry').value;
            if (!license_class || !license_expiry) { alert('Please fill in both fields.'); return; }
            postJson('<?= base_url('vendors/card-info/add-license/') ?>' + VENDOR_ID, { license_class, license_expiry })
                .then(d => { alert(d.message); if (d.success) location.reload(); })
                .catch(netErr);
        }

        // --- Edit Location Access ---
        function openLocationModal() { document.getElementById('locationModal').classList.remove('hidden'); }
        function closeLocationModal() { document.getElementById('locationModal').classList.add('hidden'); }
        function submitLocationAccess() {
            const locations = Array.from(document.querySelectorAll('.loc-modal-checkbox:checked')).map(cb => cb.value);
            postJson('<?= base_url('vendors/card-info/update-location-access/') ?>' + VENDOR_ID, { locations })
                .then(d => { alert(d.message); if (d.success) location.reload(); })
                .catch(netErr);
        }

        // --- Upload Photo ---
        function uploadPhotoFile(file) {
            if (!file) return;
            const fd = new FormData();
            fd.append('photo', file);
            fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            fetch('<?= base_url('vendors/card-info/upload-photo/') ?>' + VENDOR_ID, { method: 'POST', body: fd })
                .then(r => { if (!r.ok) throw new Error('http_' + r.status); return r.json(); })
                .then(d => { if (d.success) { document.getElementById('currentPhoto').src = d.photo_url; } else { alert(d.message); } })
                .catch(netErr);
        }

        // --- Activate / Terminate ---
        function activateCard() {
            if (!confirm('Activate this card?')) return;
            postJson('<?= base_url('vendors/card-info/activate/') ?>' + VENDOR_ID, {})
                .then(d => { alert(d.message); if (d.success) location.reload(); })
                .catch(netErr);
        }
        function terminateCard() {
            if (!confirm('Terminate this card? The physical card will be released back to the pool.')) return;
            postJson('<?= base_url('vendors/card-info/terminate/') ?>' + VENDOR_ID, {})
                .then(d => { alert(d.message); if (d.success) location.reload(); })
                .catch(netErr);
        }

        // --- Update ---
        function saveCardInfo() {
            postJson('<?= base_url('vendors/card-info/update/') ?>' + VENDOR_ID, {
                pass_expiry: document.getElementById('f_pass_expiry').value,
                collector_name: document.getElementById('f_collector_name').value,
                collector_ic_passport: document.getElementById('f_collector_ic').value,
            }).then(d => { alert(d.message); if (d.success) location.reload(); }).catch(netErr);
        }

        // --- Reprint (front + back, same design as Printing List) ---
        const TERMS_TEXT = [
            "This pass remains the property of the issuing company and must be returned upon request or on expiry.",
            "This pass must be worn visibly at all times while on the premises.",
            "Loss of this pass must be reported to security immediately.",
            "This pass is not transferable and may only be used by the person named on it.",
        ];
        function cardMarkup(card) {
            const typeClass = String(card.card_type).toLowerCase() === 'permanent' ? 'background:linear-gradient(160deg,#0f3d91,#137fec,#1e2a5e);' : 'background:linear-gradient(160deg,#92400e,#f59e0b,#78350f);';
            return `<div class="card-page" style="width:54mm;height:85.6mm;position:relative;overflow:hidden;border-radius:3mm;color:#fff;font-family:'Montserrat',sans-serif;${typeClass}">
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
            </div>` + cardBackMarkup(card);
        }
        function cardBackMarkup(card) {
            const terms = TERMS_TEXT.map((t, i) => `<li style="margin-bottom:1.5mm;">${i + 1}. ${t}</li>`).join('');
            return `<div class="card-page" style="width:54mm;height:85.6mm;position:relative;overflow:hidden;border-radius:3mm;background:#fff;color:#1f2937;border:0.3mm solid #d1d5db;font-family:'Montserrat',sans-serif;">
                <div style="padding:4mm;display:flex;flex-direction:column;height:100%;box-sizing:border-box;">
                    <div style="font-size:2.6mm;font-weight:700;text-align:center;margin-bottom:2mm;text-transform:uppercase;">Terms &amp; Conditions</div>
                    <ol style="list-style:none;padding:0;margin:0;font-size:2.2mm;line-height:1.3;">${terms}</ol>
                    <div style="margin-top:auto;text-align:center;font-size:2.2mm;">
                        <div style="font-size:2mm;opacity:.7;">${card.company}</div>
                        <div style="border-top:0.2mm solid #9ca3af;width:80%;margin:6mm auto 1mm;"></div>
                        <div style="font-size:2mm;">Authorized Signature</div>
                    </div>
                </div>
            </div>`;
        }
        function reprintCard() {
            postJson('<?= base_url('vendors/printing-list/generate-serial/') ?>' + VENDOR_ID, {}).then(d => {
                if (!d.success) { alert(d.message); return; }
                document.getElementById('print-section').innerHTML = cardMarkup(d.card);
                setTimeout(() => { window.print(); location.reload(); }, 150);
            }).catch(netErr);
        }
    </script>
</body>
</html>
