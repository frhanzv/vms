<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Vendor Pass - Detail</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#137fec", "background-light": "#f6f7f8", "background-dark": "#111827",
                        "surface-light": "#ffffff", "surface-dark": "#1a2632",
                        "text-main": "#0d141b", "text-sub": "#4c739a", "border-color": "#e7edf3",
                    },
                    fontFamily: { display: ["Montserrat", "sans-serif"], sans: ["Montserrat", "sans-serif"], brand: ["Montserrat", "sans-serif"] },
                    borderRadius: { DEFAULT: "0.375rem" },
                },
            },
        };
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased min-h-screen">
    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8">
        <div class="max-w-[960px] mx-auto">

            <?php
                $f = fn($field) => esc($vendor[$field] ?? '');
                $ro = 'w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand';
                $label = 'block text-sm font-medium text-text-main dark:text-gray-200 font-brand';
                $badgeClass = [
                    'Pending'   => 'bg-amber-50 text-amber-700',
                    'Approved'  => 'bg-emerald-50 text-emerald-700',
                    'Rejected'  => 'bg-red-50 text-red-700',
                    'Suspended' => 'bg-gray-100 text-gray-700',
                ][$vendor['status'] ?? 'Pending'] ?? 'bg-gray-100 text-gray-700';
                $on = fn(string $key) => $fields[$key] ?? true;
            ?>

            <!-- Page Header -->
            <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <button onclick="window.history.back()" class="text-text-sub dark:text-gray-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined">arrow_back</span>
                        </button>
                        <h1 class="text-3xl sm:text-4xl font-black text-text-main dark:text-white font-brand tracking-tight">Vendor Pass</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass ?>"><?= $f('status') ?: 'Pending' ?></span>
                    </div>
                    <p class="text-sm text-text-sub dark:text-gray-400 font-brand pl-9">
                        Application No: <span class="font-semibold text-text-main dark:text-white"><?= $f('app_no') ?></span>
                    </p>
                </div>
                <div class="flex gap-2">
                    <?php if (($vendor['status'] ?? '') === 'Approved'): ?>
                    <a href="<?= base_url('vendors/qr/' . $vendor['id']) ?>" class="h-10 px-4 rounded-lg border border-border-color dark:border-gray-700 text-sm font-medium flex items-center gap-1.5 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <span class="material-symbols-outlined text-[18px]">qr_code_2</span> QR Pass
                    </a>
                    <?php endif; ?>
                    <a href="<?= base_url('vendorpassrequest/edit/' . $vendor['id']) ?>" class="h-10 px-4 rounded-lg bg-primary text-white text-sm font-medium flex items-center gap-1.5 hover:bg-blue-700">
                        <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                    </a>
                </div>
            </div>

            <div class="space-y-8">

                <!-- Application Information -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary"><span class="material-symbols-outlined">assignment</span></div>
                        <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Application Info</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="space-y-2"><label class="<?= $label ?>">Application Number</label><input value="<?= $f('app_no') ?>" class="<?= $ro ?>" readonly/></div>
                        <div class="space-y-2"><label class="<?= $label ?>">Date Of Application</label><input value="<?= $f('date_of_application') ?>" class="<?= $ro ?>" readonly/></div>
                        <?php if ($on('type_of_application')): ?><div class="space-y-2"><label class="<?= $label ?>">Type Of Application</label><input value="<?= $f('type_of_application') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        <?php if ($on('type_of_registration')): ?><div class="space-y-2"><label class="<?= $label ?>">Type Of Registration</label><input value="<?= $f('type_of_registration') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        <?php if ($on('designation')): ?><div class="space-y-2"><label class="<?= $label ?>">Designation</label><input value="<?= $f('designation') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        <?php if ($on('payment')): ?><div class="space-y-2"><label class="<?= $label ?>">Payment</label><input value="<?= $f('payment') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        <?php if ($on('resident')): ?><div class="space-y-2"><label class="<?= $label ?>">Resident</label><input value="<?= $f('resident') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        <?php if ($on('sub_type')): ?><div class="space-y-2"><label class="<?= $label ?>">Sub Type</label><input value="<?= $f('sub_type') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        <div class="space-y-2"><label class="<?= $label ?>">Worker Type</label><input value="<?= $f('card_type') ?>" class="<?= $ro ?>" readonly/></div>
                    </div>
                    <?php if ($on('remark')): ?>
                    <div class="mt-6 space-y-2">
                        <label class="<?= $label ?>">Remark</label>
                        <input value="<?= $f('remark') ?>" class="<?= $ro ?>" readonly/>
                    </div>
                    <?php endif; ?>
                    <?php if ($on('location_access')):
                        $selectedLocations = array_filter(explode(',', (string) ($vendor['location_access'] ?? '')));
                        $locationLabelMap  = \App\Controllers\VendorPassRequest::LOCATION_OPTIONS;
                    ?>
                    <div class="mt-6 space-y-2">
                        <label class="<?= $label ?>">Location Access</label>
                        <?php if (empty($selectedLocations)): ?>
                            <p class="text-sm text-text-sub dark:text-gray-400">No Record</p>
                        <?php else: ?>
                            <div class="flex flex-wrap gap-x-6 gap-y-2">
                                <?php foreach ($locationLabelMap as $code => $labelText): ?>
                                <label class="flex items-center gap-2 text-sm text-text-main dark:text-gray-200">
                                    <input type="checkbox" disabled <?= in_array($code, $selectedLocations, true) ? 'checked' : '' ?> class="rounded border-border-color"/>
                                    <?= esc($labelText) ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </section>

                <?php if ($on('vendor_company')): ?>
                <!-- Vendor Company -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary"><span class="material-symbols-outlined">business</span></div>
                        <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Vendor Company</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-2"><label class="<?= $label ?>">Company SSM No / Reg ID</label><input value="<?= $f('vendor_company_reg_id') ?>" class="<?= $ro ?>" readonly/></div>
                        <div class="space-y-2"><label class="<?= $label ?>">Company Name</label><input value="<?= $f('vendor_company_name') ?>" class="<?= $ro ?>" readonly/></div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Applicant Details (personal info + contact + address — KPK calls this section "Person") -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary"><span class="material-symbols-outlined">badge</span></div>
                        <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Applicant Details</h2>
                    </div>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2"><label class="<?= $label ?>">Full Name</label><input value="<?= $f('full_name') ?>" class="<?= $ro ?>" readonly/></div>
                            <div class="space-y-2"><label class="<?= $label ?>">Name On Vendor Pass</label><input value="<?= $f('name_on_vendor_pass') ?>" class="<?= $ro ?>" readonly/></div>
                            <?php if ($on('staff_no')): ?><div class="space-y-2"><label class="<?= $label ?>">Staff No. (at vendor company)</label><input value="<?= $f('staff_no') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('in_out_bound')): ?><div class="space-y-2"><label class="<?= $label ?>">In/Out Bound</label><input value="<?= $f('in_out_bound') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        </div>
                        <?php if ($on('ic_passport') || $on('date_of_birth') || $on('sex')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <?php if ($on('ic_passport')): ?>
                            <div class="space-y-2"><label class="<?= $label ?>">IC Number</label><input value="<?= esc(mask_ic_passport($vendor['ic_no'] ?? '', '')) ?>" class="<?= $ro ?>" readonly/></div>
                            <div class="space-y-2"><label class="<?= $label ?>">Passport Number</label><input value="<?= esc(mask_ic_passport($vendor['passport_no'] ?? '', '')) ?>" class="<?= $ro ?>" readonly/></div>
                            <?php endif; ?>
                            <?php if ($on('date_of_birth')): ?><div class="space-y-2"><label class="<?= $label ?>">Date Of Birth</label><input value="<?= $f('dob') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('sex')): ?><div class="space-y-2"><label class="<?= $label ?>">Sex</label><input value="<?= $f('sex') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('contact_number') || $on('email') || $on('vehicle_registration')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php if ($on('contact_number')): ?><div class="space-y-2"><label class="<?= $label ?>">Contact Number</label><input value="<?= $f('contact_no') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('email')): ?><div class="space-y-2"><label class="<?= $label ?>">Email</label><input value="<?= $f('email') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('vehicle_registration')): ?><div class="space-y-2"><label class="<?= $label ?>">Vehicle Registration Number</label><input value="<?= $f('vehicle_registration') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('address')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="space-y-2"><label class="<?= $label ?>">Address 1</label><input value="<?= $f('address_1') ?>" class="<?= $ro ?>" readonly/></div>
                            <div class="space-y-2"><label class="<?= $label ?>">Address 2</label><input value="<?= $f('address_2') ?>" class="<?= $ro ?>" readonly/></div>
                            <div class="space-y-2"><label class="<?= $label ?>">Address 3</label><input value="<?= $f('address_3') ?>" class="<?= $ro ?>" readonly/></div>
                            <div class="space-y-2"><label class="<?= $label ?>">Country</label><input value="<?= $f('country') ?: 'Malaysia' ?>" class="<?= $ro ?>" readonly/></div>
                            <div class="space-y-2"><label class="<?= $label ?>">State</label><input value="<?= $f('state') ?>" class="<?= $ro ?>" readonly/></div>
                            <div class="space-y-2"><label class="<?= $label ?>">City</label><input value="<?= $f('city') ?>" class="<?= $ro ?>" readonly/></div>
                            <div class="space-y-2"><label class="<?= $label ?>">Postal Code</label><input value="<?= $f('postcode') ?>" class="<?= $ro ?>" readonly/></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Driving License — always shown, "No Record" when empty, same as KPK's Card Info page -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary"><span class="material-symbols-outlined">directions_car</span></div>
                        <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Driving License</h2>
                    </div>
                    <?php if (empty($licenses)): ?>
                        <p class="text-sm text-text-sub dark:text-gray-400 text-center py-2">No Record</p>
                    <?php else: ?>
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase">
                                <th class="p-2 border-b">Class</th><th class="p-2 border-b">Expiry</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($licenses as $lic): ?>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="p-2"><?= esc($lic['license_class']) ?></td>
                                <td class="p-2"><?= esc($lic['license_expiry']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </section>

                <?php if ($on('visit_details')): ?>
                <!-- Visit Details -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary"><span class="material-symbols-outlined">location_on</span></div>
                        <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Visit Details</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="space-y-2"><label class="<?= $label ?>">Name Of Person Visited</label><input value="<?= $f('name_of_person_visited') ?>" class="<?= $ro ?>" readonly/></div>
                        <div class="space-y-2"><label class="<?= $label ?>">Contact No. Of Person Visited</label><input value="<?= $f('contact_no_of_person_visited') ?>" class="<?= $ro ?>" readonly/></div>
                        <div class="space-y-2"><label class="<?= $label ?>">Location Visited</label><input value="<?= $f('location_visited') ?>" class="<?= $ro ?>" readonly/></div>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ($on('csp_number') || $on('evetting')): ?>
                <!-- CSP & E-Vetting -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary"><span class="material-symbols-outlined">verified_user</span></div>
                        <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">CSP &amp; E-Vetting</h2>
                    </div>
                    <div class="space-y-6">
                        <?php if ($on('csp_number')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2"><label class="<?= $label ?>">CSP Number</label><input value="<?= $f('csp_number') ?>" class="<?= $ro ?>" readonly/></div>
                            <div class="space-y-2"><label class="<?= $label ?>">CSP Expiry Date</label><input value="<?= $f('csp_expiry_date') ?>" class="<?= $ro ?>" readonly/></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('evetting')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div class="space-y-2"><label class="<?= $label ?>">E-Vetting Date Of Application</label><input value="<?= $f('evetting_date_of_application') ?>" class="<?= $ro ?>" readonly/></div>
                            <div class="space-y-2"><label class="<?= $label ?>">E-Vetting Date Of Result</label><input value="<?= $f('evetting_date_of_result') ?>" class="<?= $ro ?>" readonly/></div>
                            <div class="space-y-2"><label class="<?= $label ?>">E-Vetting Result</label><input value="<?= $f('evetting_result') ?>" class="<?= $ro ?>" readonly/></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ($on('pass_expiry')): ?>
                <!-- Pass -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary"><span class="material-symbols-outlined">badge</span></div>
                        <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Pass</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-2"><label class="<?= $label ?>">Pass Expiry</label><input value="<?= $f('pass_expiry') ?>" class="<?= $ro ?>" readonly/></div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Upload — same idea as KPK's Upload section: whatever documents
                     were attached at intake, shown as links, "No Record" if none. -->
                <?php
                    $uploadLinks = [];
                    if (!empty($vendor['photo'])) {
                        $uploadLinks[] = ['label' => $vendor['photo'], 'url' => base_url('uploads/vendor_photos/' . $vendor['photo'])];
                    }
                    if (!empty($vendor['government_id'])) {
                        $uploadLinks[] = ['label' => $vendor['government_id'], 'url' => base_url('uploads/government_ids/' . $vendor['government_id'])];
                    }
                    if (!empty($vendor['other_doc'])) {
                        $otherDocs = json_decode((string) $vendor['other_doc'], true);
                        foreach ((array) $otherDocs as $doc) {
                            if ($doc) {
                                $uploadLinks[] = ['label' => $doc, 'url' => base_url('uploads/other_docs/' . $doc)];
                            }
                        }
                    }
                ?>
                <?php if ($on('document_upload') || $on('photo_upload')): ?>
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary"><span class="material-symbols-outlined">attach_file</span></div>
                        <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Upload</h2>
                    </div>
                    <?php if (empty($uploadLinks)): ?>
                        <p class="text-sm text-text-sub dark:text-gray-400">No Record</p>
                    <?php else: ?>
                        <ul class="list-disc pl-5 space-y-1">
                            <?php foreach ($uploadLinks as $up): ?>
                            <li><a href="<?= $up['url'] ?>" target="_blank" class="text-primary hover:underline text-sm"><?= esc($up['label']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>
                <?php endif; ?>

                <div class="flex justify-start">
                    <button onclick="window.history.back()" class="h-10 px-6 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold">Back</button>
                </div>

            </div>
        </div>
    </main>
</body>
</html>
