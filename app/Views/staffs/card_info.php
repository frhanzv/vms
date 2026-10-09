<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => ['process' => 'staffs/process-list', 'issuance' => 'staffs/issuance-list', 'closed' => 'staffs/closed-list'][$stage] ?? 'staffs']) ?>
<?php
$s   = $staff;
$id  = (int) $s['id'];
$val = fn(string $k) => esc((string) ($s[$k] ?? ''), 'attr');
$dt  = fn(?string $v) => $v && ! str_starts_with($v, '0000') ? date('d/m/Y', strtotime($v)) : '-';
$stageLabel = [
    'request'  => ['Request / Approval', 'bg-amber-50 text-amber-700'],
    'process'  => ['Process — waiting for card print', 'bg-blue-50 text-blue-700'],
    'issuance' => ['Issuance — printed, not collected', 'bg-indigo-50 text-indigo-700'],
    'closed'   => ['Closed — card issued', 'bg-emerald-50 text-emerald-700'],
][$stage];
$input = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2 text-sm disabled:bg-gray-100 dark:disabled:bg-gray-900 disabled:text-gray-500';
$label = 'block text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1';
$card  = 'rounded-lg border border-gray-200 dark:border-gray-700 p-5';
$dis   = $canEdit ? '' : 'disabled';
$selectedLocations = array_map('strval', $selectedLocations);
?>

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-6">
                <div>
                    <a href="javascript:history.back()" class="text-xs text-gray-500 hover:text-primary flex items-center gap-1 mb-2"><span class="material-icons text-sm">arrow_back</span>Back</a>
                    <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white"><?= esc($s['full_name'] ?: '-') ?></h1>
                    <p class="text-xs text-gray-500 mt-1">
                        App No <strong><?= esc($s['app_no'] ?: '-') ?></strong> ·
                        <?= esc(strtoupper((string) ($s['type_of_application'] ?: 'NEW'))) ?> ·
                        Pass status <strong><?= esc($s['status'] ?: '-') ?></strong>
                        <?php if ((int) ($s['is_active'] ?? 1) !== 1): ?> · <span class="text-red-500 font-semibold">Inactive employee</span><?php endif; ?>
                    </p>
                </div>
                <span class="px-3 py-1.5 rounded-full text-xs font-bold <?= $stageLabel[1] ?>"><?= $stageLabel[0] ?></span>
            </div>

            <?php if ($s['status'] === 'Suspended'): ?>
            <div class="mb-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-lg px-4 py-3">
                Suspended<?= ! empty($s['suspension_period']) ? ' until ' . esc($dt($s['suspension_period'])) : '' ?> — <?= esc($s['suspended_reason'] ?? '') ?>
            </div>
            <?php endif; ?>

            <!-- Action bar -->
            <div class="flex flex-wrap gap-2 mb-6">
                <?php if ($canPrint): ?>
                    <?php if ($stage === 'process'): ?>
                    <button onclick="staffPrintOne(<?= $id ?>)" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-1"><span class="material-icons text-sm">print</span>Print Card</button>
                    <?php else: ?>
                    <button onclick="openModal('reprintModal')" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-1"><span class="material-icons text-sm">print</span>Reprint Card</button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($canActivate): ?>
                <button onclick="act('activate', 'Activate this card?')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded text-sm font-medium">Activate Card</button>
                <?php endif; ?>
                <?php if ($canTerminate): ?>
                <button onclick="act('terminate', 'Terminate this card? The physical card will be released back to the pool.')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-medium">Terminate Card</button>
                <?php endif; ?>
                <?php if ($canSuspend): ?>
                <button onclick="openModal('suspendModal')" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded text-sm font-medium">Suspend</button>
                <?php endif; ?>
                <?php if ($canUnsuspend): ?>
                <button onclick="act('unsuspend', 'Lift the suspension and re-activate the card?')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded text-sm font-medium">Unsuspend</button>
                <?php endif; ?>
                <?php if ($canRenew): ?>
                <a href="<?= base_url('staffs/renew/' . $id) ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-1"><span class="material-icons text-sm">autorenew</span>Renew</a>
                <?php endif; ?>
                <?php if ($canReject): ?>
                <button onclick="openModal('rejectModal')" class="border border-red-300 text-red-600 hover:bg-red-50 px-4 py-2 rounded text-sm font-medium">Reject</button>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Photo + card -->
                <div class="space-y-6">
                    <div class="<?= $card ?>">
                        <h2 class="text-sm font-bold uppercase mb-4">Photo</h2>
                        <div class="flex flex-col items-center gap-3">
                            <?php if ($photoUrl): ?>
                            <img id="photoPreview" src="<?= esc($photoUrl, 'attr') ?>" class="w-36 h-44 object-cover rounded border border-gray-200" alt=""/>
                            <?php else: ?>
                            <div id="photoPreview" class="w-36 h-44 rounded border-2 border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400 text-xs"><span class="material-symbols-outlined text-4xl">person</span>No photo yet</div>
                            <?php endif; ?>
                            <?php if ($canUploadPhoto): ?>
                            <div class="flex gap-2">
                                <label class="cursor-pointer border border-gray-300 dark:border-gray-600 px-3 py-1.5 rounded text-xs font-semibold hover:bg-gray-50 dark:hover:bg-gray-800">
                                    Upload<input type="file" accept="image/png,image/jpeg" class="hidden" onchange="uploadPhoto(this.files[0])"/>
                                </label>
                                <button type="button" onclick="openCamera()" class="border border-gray-300 dark:border-gray-600 px-3 py-1.5 rounded text-xs font-semibold hover:bg-gray-50 dark:hover:bg-gray-800">Take Photo</button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="<?= $card ?>">
                        <h2 class="text-sm font-bold uppercase mb-4">Card Info</h2>
                        <dl class="text-xs space-y-2">
                            <div class="flex justify-between"><dt class="text-gray-500">Card Serial</dt><dd class="font-mono font-semibold"><?= esc($s['receipt_no'] ?: '-') ?></dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Card Status</dt><dd class="font-semibold"><?= esc($s['card_status'] ?: 'Inactive') ?></dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Card Expiry</dt><dd><?= esc($dt($s['card_expiry'] ?? null)) ?></dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">RFID Card</dt><dd class="font-mono"><?= esc($boundCard['card_id'] ?? '-') ?></dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Collected By</dt><dd><?= esc($s['collector_name'] ?? '-') ?: '-' ?></dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Issued</dt><dd><?= ! empty($s['issued_at']) ? esc(date('d/m/Y H:i', strtotime($s['issued_at']))) . ' · ' . esc($s['issued_by'] ?? '') : '-' ?></dd></div>
                            <?php if (! empty($s['terminated_at'])): ?>
                            <div class="flex justify-between"><dt class="text-gray-500">Terminated</dt><dd class="text-red-500"><?= esc(date('d/m/Y H:i', strtotime($s['terminated_at']))) ?> · <?= esc($s['terminated_by'] ?? '') ?></dd></div>
                            <?php endif; ?>
                        </dl>
                        <?php if ($canBindCard): ?>
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <label class="<?= $label ?>">Bind RFID Card</label>
                            <div class="flex gap-2">
                                <input id="cardEpc" placeholder="Tap card or type EPC" class="<?= $input ?>"/>
                                <button onclick="bindCard(this)" class="bg-gray-700 hover:bg-gray-800 text-white px-3 rounded text-xs font-semibold">Bind</button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Details -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="<?= $card ?>">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-bold uppercase">Staff Details</h2>
                            <?php if ($canEdit): ?>
                            <button onclick="saveDetails(this)" class="bg-primary hover:bg-blue-700 text-white px-4 py-1.5 rounded text-xs font-semibold">Update</button>
                            <?php endif; ?>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="detailsForm">
                            <div><span class="<?= $label ?>">Full Name</span><input class="<?= $input ?>" value="<?= $val('full_name') ?>" disabled/></div>
                            <div><span class="<?= $label ?>">IC / Passport</span><input class="<?= $input ?>" value="<?= esc($icPassport, 'attr') ?>" disabled/></div>
                            <div>
                                <span class="<?= $label ?>">Staff No</span>
                                <div class="flex gap-2">
                                    <input class="<?= $input ?>" value="<?= $val('staff_no') ?>" disabled/>
                                    <?php if ($canChangeStaffNo): ?>
                                    <button type="button" onclick="changeStaffNo()" class="text-primary text-xs font-semibold whitespace-nowrap">Change</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div><span class="<?= $label ?>">Resident</span><input class="<?= $input ?>" value="<?= $val('resident') ?>" disabled/></div>
                            <div><span class="<?= $label ?>">Date of Birth</span><input class="<?= $input ?>" value="<?= esc($dt($s['date_of_birth'] ?? null), 'attr') ?>" disabled/></div>
                            <div><span class="<?= $label ?>">Sex</span><input class="<?= $input ?>" value="<?= $val('sex') ?>" disabled/></div>

                            <div><label class="<?= $label ?>">Name On Staff Pass</label><input data-f="name_on_staff_pass" class="<?= $input ?>" value="<?= $val('name_on_staff_pass') ?>" <?= $dis ?>/></div>
                            <div><label class="<?= $label ?>">Contact Number</label><input data-f="contact_number" class="<?= $input ?>" value="<?= $val('contact_number') ?>" <?= $dis ?>/></div>
                            <div><label class="<?= $label ?>">Email</label><input data-f="email" type="email" class="<?= $input ?>" value="<?= $val('email') ?>" <?= $dis ?>/></div>
                            <div><label class="<?= $label ?>">Designation</label><input data-f="designation" class="<?= $input ?>" value="<?= $val('designation') ?>" <?= $dis ?>/></div>
                            <div>
                                <label class="<?= $label ?>">Department</label>
                                <select data-f="department" class="<?= $input ?>" <?= $dis ?>>
                                    <option value="">-</option>
                                    <?php $deps = $departments; if (! empty($s['department']) && ! in_array($s['department'], $deps, true)) { $deps[] = $s['department']; } ?>
                                    <?php foreach ($deps as $d): ?>
                                    <option value="<?= esc($d, 'attr') ?>" <?= ($s['department'] ?? '') === $d ? 'selected' : '' ?>><?= esc($d) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div><label class="<?= $label ?>">Sub Type</label><input data-f="sub_type" class="<?= $input ?>" value="<?= $val('sub_type') ?>" <?= $dis ?>/></div>
                            <div>
                                <label class="<?= $label ?>">Approving Branch</label>
                                <select data-f="access_branch" class="<?= $input ?>" <?= $dis ?>>
                                    <?php foreach (['' => '-', 'KSB' => 'KSB', 'KPK' => 'KPK', 'BOTH' => 'Both'] as $v => $l): ?>
                                    <option value="<?= $v ?>" <?= ($s['access_branch'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div><label class="<?= $label ?>">Card Expiry</label><input data-f="card_expiry" type="date" class="<?= $input ?>" value="<?= $val('card_expiry') ?>" <?= $dis ?>/></div>
                            <div><label class="<?= $label ?>">Visa / Permit Expiry</label><input data-f="visa_expiry" type="date" class="<?= $input ?>" value="<?= $val('visa_expiry') ?>" <?= $dis ?>/></div>
                            <div class="md:col-span-3"><label class="<?= $label ?>">Address</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                    <input data-f="address_1" class="<?= $input ?>" value="<?= $val('address_1') ?>" placeholder="Address 1" <?= $dis ?>/>
                                    <input data-f="address_2" class="<?= $input ?>" value="<?= $val('address_2') ?>" placeholder="Address 2" <?= $dis ?>/>
                                    <input data-f="address_3" class="<?= $input ?>" value="<?= $val('address_3') ?>" placeholder="Address 3" <?= $dis ?>/>
                                    <input data-f="postal_code" class="<?= $input ?>" value="<?= $val('postal_code') ?>" placeholder="Postcode" <?= $dis ?>/>
                                    <input data-f="city" class="<?= $input ?>" value="<?= $val('city') ?>" placeholder="City" <?= $dis ?>/>
                                    <input data-f="state" class="<?= $input ?>" value="<?= $val('state') ?>" placeholder="State" <?= $dis ?>/>
                                </div>
                            </div>
                            <div class="md:col-span-3"><label class="<?= $label ?>">Remark</label><textarea data-f="remark" rows="2" class="<?= $input ?>" <?= $dis ?>><?= esc($s['remark'] ?? '') ?></textarea></div>
                        </div>
                    </div>

                    <!-- Location access -->
                    <div class="<?= $card ?>">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-bold uppercase">Location Access</h2>
                            <?php if ($canEditLocation && $locationGroups): ?>
                            <button onclick="saveLocations(this)" class="bg-primary hover:bg-blue-700 text-white px-4 py-1.5 rounded text-xs font-semibold">Save Location Access</button>
                            <?php endif; ?>
                        </div>
                        <?php if (! $locationGroups): ?>
                            <p class="text-xs text-gray-500">No locations configured. Current value: <?= esc($s['location_access'] ?: '-') ?></p>
                        <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($locationGroups as $branch => $entries): ?>
                            <div>
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2"><?= esc($branch) ?></p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <?php foreach ($entries as $e): ?>
                                    <div class="flex items-center justify-between border border-gray-100 dark:border-gray-700 rounded px-3 py-2 text-xs">
                                        <span><?= esc($e['label']) ?></span>
                                        <span class="flex gap-3">
                                            <?php foreach (['in' => 'IN', 'out' => 'OUT'] as $d => $dl): if (! $e[$d]) continue; ?>
                                            <label class="flex items-center gap-1"><input type="checkbox" class="loc-check rounded" value="<?= esc($e[$d], 'attr') ?>" <?= in_array($e[$d], $selectedLocations, true) ? 'checked' : '' ?> <?= $canEditLocation ? '' : 'disabled' ?>/><?= $dl ?></label>
                                            <?php endforeach; ?>
                                        </span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Driving licences -->
                    <div class="<?= $card ?>">
                        <h2 class="text-sm font-bold uppercase mb-4">Driving License</h2>
                        <table class="w-full text-xs mb-3">
                            <thead><tr class="text-left text-gray-500 uppercase"><th class="py-1">Class</th><th class="py-1">Expiry</th><th></th></tr></thead>
                            <tbody>
                            <?php if (! $licenses): ?>
                                <tr><td colspan="3" class="py-2 text-gray-400">No licenses.</td></tr>
                            <?php else: foreach ($licenses as $l): ?>
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    <td class="py-2 font-semibold"><?= esc($l['license_class'] ?? '-') ?></td>
                                    <td class="py-2"><?= esc($dt($l['license_expiry'] ?? null)) ?></td>
                                    <td class="py-2 text-right"><?php if ($canAddLicense): ?><button onclick="removeLicense(<?= (int) $l['id'] ?>, this)" class="text-red-500 hover:underline">Remove</button><?php endif; ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                        <?php if ($canAddLicense): ?>
                        <div class="flex flex-wrap gap-2 items-end">
                            <div><label class="<?= $label ?>">Class</label>
                                <select id="licClass" class="<?= $input ?>">
                                    <?php foreach (['A', 'A1', 'B', 'B1', 'B2', 'C', 'D', 'DA', 'E', 'E1', 'E2', 'F', 'G', 'H', 'I', 'M', 'GDL'] as $c): ?><option><?= $c ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div><label class="<?= $label ?>">Expiry</label><input id="licExpiry" type="date" class="<?= $input ?>"/></div>
                            <button onclick="addLicense(this)" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded text-xs font-semibold">Add License</button>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- History -->
                    <div class="<?= $card ?>">
                        <h2 class="text-sm font-bold uppercase mb-4">Print History</h2>
                        <table class="w-full text-xs">
                            <thead><tr class="text-left text-gray-500 uppercase"><th class="py-1">Date</th><th class="py-1">Serial</th><th class="py-1">Type</th><th class="py-1">Reason</th><th class="py-1">By</th></tr></thead>
                            <tbody>
                            <?php if (! $printLogs): ?><tr><td colspan="5" class="py-2 text-gray-400">Not printed yet.</td></tr><?php endif; ?>
                            <?php foreach ($printLogs as $p): ?>
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    <td class="py-2"><?= esc(date('d/m/Y H:i', strtotime($p['printed_at']))) ?></td>
                                    <td class="py-2 font-mono"><?= esc($p['receipt_no']) ?></td>
                                    <td class="py-2"><?= ! empty($p['is_reprint']) ? 'Reprint' : 'First print' ?></td>
                                    <td class="py-2"><?= esc($p['reason'] ?? '-') ?: '-' ?></td>
                                    <td class="py-2"><?= esc($p['printed_by'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="<?= $card ?>">
                        <h2 class="text-sm font-bold uppercase mb-4">Activity</h2>
                        <ul class="text-xs space-y-2">
                            <?php if (! $statusLogs): ?><li class="text-gray-400">No activity recorded.</li><?php endif; ?>
                            <?php foreach ($statusLogs as $log): ?>
                            <li class="flex gap-3 border-t border-gray-100 dark:border-gray-700 pt-2">
                                <span class="text-gray-400 whitespace-nowrap"><?= esc(date('d/m/Y H:i', strtotime($log['created_at']))) ?></span>
                                <span><strong><?= esc(ucwords(str_replace('_', ' ', $log['action']))) ?></strong>
                                    <?= $log['to_status'] ? '→ ' . esc($log['to_status']) : '' ?>
                                    <?= $log['reject_reason'] ? ' · ' . esc($log['reject_reason']) : '' ?>
                                    <?= $log['remark'] ? ' · ' . esc($log['remark']) : '' ?>
                                    <span class="text-gray-400">— <?= esc($log['acted_by'] ?? '') ?></span></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

<?= view('staffs/_layout_bottom') ?>
<?= view('staffs/_card_print') ?>

    <!-- Reprint -->
    <div id="reprintModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-sm w-full p-6">
            <h3 class="text-lg font-bold mb-3">Reprint Card</h3>
            <label class="<?= $label ?>">Reason</label>
            <select id="reprintReason" class="<?= $input ?> mb-3">
                <option value="Lost card">Lost card</option>
                <option value="Damaged card">Damaged card</option>
                <option value="Details changed">Details changed</option>
                <option value="Other">Other</option>
            </select>
            <p class="text-xs text-gray-500 mb-4">Staff reprints don't need a payment receipt. The card keeps its serial number.</p>
            <div class="flex justify-end gap-2">
                <button onclick="closeModal('reprintModal')" class="px-4 py-2 rounded border text-sm">Cancel</button>
                <button onclick="staffPrintOne(<?= $id ?>, document.getElementById('reprintReason').value)" class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Reprint</button>
            </div>
        </div>
    </div>

    <!-- Suspend -->
    <div id="suspendModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-sm w-full p-6">
            <h3 class="text-lg font-bold mb-3">Suspend Staff Pass</h3>
            <label class="<?= $label ?>">Reason <span class="text-red-500">*</span></label>
            <textarea id="suspendReason" rows="2" class="<?= $input ?> mb-3"></textarea>
            <label class="<?= $label ?>">Suspended Until (optional)</label>
            <input id="suspendUntil" type="date" class="<?= $input ?> mb-4"/>
            <div class="flex justify-end gap-2">
                <button onclick="closeModal('suspendModal')" class="px-4 py-2 rounded border text-sm">Cancel</button>
                <button onclick="submitSuspend(this)" class="px-4 py-2 rounded bg-amber-500 text-white text-sm font-semibold">Suspend</button>
            </div>
        </div>
    </div>

    <!-- Reject -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-sm w-full p-6">
            <h3 class="text-lg font-bold mb-3">Reject Staff Pass</h3>
            <label class="<?= $label ?>">Reason</label>
            <select id="rejectReasonId" class="<?= $input ?> mb-3">
                <option value="">-- Select a reason --</option>
                <?php foreach ($rejectReasons as $r): ?><option value="<?= (int) $r['id'] ?>"><?= esc($r['reason']) ?></option><?php endforeach; ?>
            </select>
            <label class="<?= $label ?>">Remark</label>
            <textarea id="rejectRemark" rows="2" class="<?= $input ?> mb-4"></textarea>
            <div class="flex justify-end gap-2">
                <button onclick="closeModal('rejectModal')" class="px-4 py-2 rounded border text-sm">Cancel</button>
                <button onclick="submitReject(this)" class="px-4 py-2 rounded bg-red-600 text-white text-sm font-semibold">Reject</button>
            </div>
        </div>
    </div>

    <!-- Camera -->
    <div id="cameraModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-md w-full p-4">
            <video id="camVideo" autoplay playsinline class="w-full rounded bg-black"></video>
            <canvas id="camCanvas" class="hidden"></canvas>
            <div class="flex justify-end gap-2 mt-3">
                <button onclick="closeCamera()" class="px-4 py-2 rounded border text-sm">Cancel</button>
                <button onclick="capturePhoto(this)" class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Capture &amp; Save</button>
            </div>
        </div>
    </div>

    <script>
        const BASE = '<?= base_url('staffs/card-info') ?>';
        const STAFF_ID = <?= $id ?>;
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
        function act(action, question, payload) {
            if (question && !confirm(question)) return;
            staffPostReload(BASE + '/' + action + '/' + STAFF_ID, payload || {});
        }
        function saveDetails(btn) {
            const data = {};
            document.querySelectorAll('#detailsForm [data-f]').forEach(el => data[el.dataset.f] = el.value);
            staffPostReload(BASE + '/update/' + STAFF_ID, data, btn);
        }
        function saveLocations(btn) {
            const locations = Array.from(document.querySelectorAll('.loc-check:checked')).map(c => c.value);
            staffPostReload(BASE + '/location-access/' + STAFF_ID, { locations }, btn);
        }
        function addLicense(btn) {
            staffPostReload(BASE + '/add-license/' + STAFF_ID, {
                license_class: document.getElementById('licClass').value,
                license_expiry: document.getElementById('licExpiry').value,
            }, btn);
        }
        function removeLicense(licId, btn) {
            if (!confirm('Remove this license?')) return;
            staffPostReload(BASE + '/delete-license/' + STAFF_ID + '/' + licId, {}, btn);
        }
        function bindCard(btn) {
            staffPostReload(BASE + '/read-card/' + STAFF_ID, { card_epc: document.getElementById('cardEpc').value.trim() }, btn);
        }
        function submitSuspend(btn) {
            staffPostReload(BASE + '/suspend/' + STAFF_ID, {
                reason: document.getElementById('suspendReason').value.trim(),
                until: document.getElementById('suspendUntil').value,
            }, btn);
        }
        function submitReject(btn) {
            staffPostReload(BASE + '/reject/' + STAFF_ID, {
                reject_reason_id: document.getElementById('rejectReasonId').value,
                remark: document.getElementById('rejectRemark').value.trim(),
            }, btn);
        }
        function changeStaffNo() {
            const no = prompt('New Staff No:');
            if (no === null || !no.trim()) return;
            staffPostReload('<?= base_url('staffs/change-staff-no/') ?>' + STAFF_ID, { staff_no: no.trim() });
        }
        function uploadPhoto(file) {
            if (!file) return;
            const fd = new FormData();
            fd.append('photo', file);
            staffPostReload(BASE + '/upload-photo/' + STAFF_ID, fd);
        }
        let camStream = null;
        function openCamera() {
            navigator.mediaDevices.getUserMedia({ video: true }).then(stream => {
                camStream = stream;
                document.getElementById('camVideo').srcObject = stream;
                openModal('cameraModal');
            }).catch(() => alert('Could not open the camera. Check the browser permission, or use Upload instead.'));
        }
        function closeCamera() {
            if (camStream) camStream.getTracks().forEach(t => t.stop());
            camStream = null;
            closeModal('cameraModal');
        }
        function capturePhoto(btn) {
            const video = document.getElementById('camVideo');
            const canvas = document.getElementById('camCanvas');
            canvas.width = video.videoWidth; canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
            closeCamera();
            staffPostReload(BASE + '/upload-photo/' + STAFF_ID, { photo_data: dataUrl }, btn);
        }
    </script>
</body>
</html>
