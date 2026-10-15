<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Staff Pass - Detail</title>
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
                // Same layout as the Vendor Pass detail page (vendors/vendorpassrequest_detail).
                $f     = fn($field) => esc($staff[$field] ?? '');
                $date  = fn($field) => ! empty($staff[$field]) && strtotime((string) $staff[$field]) ? esc(date('d/m/Y', strtotime((string) $staff[$field]))) : $f($field);
                $ro    = 'w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand';
                $label = 'block text-sm font-medium text-text-main dark:text-gray-200 font-brand';
                $status = ($staff['status'] ?? '') ?: 'Pending';
                $badgeClass = [
                    'Draft'     => 'bg-slate-100 text-slate-600',
                    'Pending'   => 'bg-amber-50 text-amber-700',
                    'Approved'  => 'bg-emerald-50 text-emerald-700',
                    'Rejected'  => 'bg-red-50 text-red-700',
                    'Suspended' => 'bg-gray-100 text-gray-700',
                ][$status] ?? 'bg-gray-100 text-gray-700';
                $fs = $fieldSettings ?? [];
                $on = fn(string $key, bool $default = true) => (bool) ($fs[$key] ?? $default);
            ?>

            <!-- Page Header -->
            <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <button onclick="window.history.back()" class="text-text-sub dark:text-gray-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined">arrow_back</span>
                        </button>
                        <h1 class="text-3xl sm:text-4xl font-black text-text-main dark:text-white font-brand tracking-tight">Staff Pass</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass ?>"><?= esc($status) ?></span>
                    </div>
                    <p class="text-sm text-text-sub dark:text-gray-400 font-brand pl-9">
                        Application No: <span class="font-semibold text-text-main dark:text-white"><?= $f('app_no') ?></span>
                    </p>
                </div>
                <?php if (! empty($canEdit)): ?>
                <div class="flex gap-2">
                    <a href="<?= base_url('staffpassrequest/edit/' . (int) $staff['id']) ?>" class="h-10 px-4 rounded-lg bg-primary text-white text-sm font-medium flex items-center gap-1.5 hover:bg-blue-700">
                        <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($status === 'Rejected' && ! empty($staff['reject_reason'])): ?>
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">Rejected: <?= $f('reject_reason') ?></div>
            <?php endif; ?>

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
                        <?php if ($on('access_branch')): ?><div class="space-y-2"><label class="<?= $label ?>">Approving Branch</label><input value="<?= $f('access_branch') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        <?php if ($on('designation')): ?><div class="space-y-2"><label class="<?= $label ?>">Designation</label><input value="<?= $f('designation') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        <?php if ($on('resident')): ?><div class="space-y-2"><label class="<?= $label ?>">Resident</label><input value="<?= $f('resident') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        <?php if ($on('sub_type')): ?><div class="space-y-2"><label class="<?= $label ?>">Sub Type</label><input value="<?= $f('sub_type') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        <div class="space-y-2"><label class="<?= $label ?>">Visa / Work Permit Expiry</label><input value="<?= $date('visa_expiry') ?>" class="<?= $ro ?>" readonly/></div>
                    </div>
                    <?php if ($on('remark')): ?>
                    <div class="mt-6 space-y-2">
                        <label class="<?= $label ?>">Remark</label>
                        <input value="<?= $f('remark') ?>" class="<?= $ro ?>" readonly/>
                    </div>
                    <?php endif; ?>
                    <?php if ($on('location_access')):
                        $selectedLocations = array_values(array_filter(array_map('trim', explode(',', (string) ($staff['location_access'] ?? '')))));
                    ?>
                    <div class="mt-6 space-y-2">
                        <label class="<?= $label ?>">Location Access</label>
                        <?php if (empty($selectedLocations)): ?>
                            <p class="text-sm text-text-sub dark:text-gray-400">No Record</p>
                        <?php else: ?>
                            <?php // only the gates this pass has access to ?>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($selectedLocations as $code): ?>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800 px-3 py-1 text-sm font-medium">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                    <?= esc($code) ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </section>

                <!-- Applicant Details -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary"><span class="material-symbols-outlined">badge</span></div>
                        <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Applicant Details</h2>
                    </div>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2"><label class="<?= $label ?>">Full Name</label><input value="<?= $f('full_name') ?>" class="<?= $ro ?>" readonly/></div>
                            <?php if ($on('name_on_staff_pass')): ?><div class="space-y-2"><label class="<?= $label ?>">Name On Staff Pass</label><input value="<?= $f('name_on_staff_pass') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('staff_no')): ?><div class="space-y-2"><label class="<?= $label ?>">Staff No</label><input value="<?= $f('staff_no') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('department')): ?><div class="space-y-2"><label class="<?= $label ?>">Department</label><input value="<?= $f('department') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        </div>
                        <?php if ($on('ic_number') || $on('date_of_birth') || $on('sex')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php if ($on('ic_number')): ?><div class="space-y-2"><label class="<?= $label ?>">IC / Passport Number</label><input value="<?= esc(mask_ic_passport((string) ($staff['ic_passport'] ?? ''), '')) ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('date_of_birth')): ?><div class="space-y-2"><label class="<?= $label ?>">Date Of Birth</label><input value="<?= $date('date_of_birth') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('sex')): ?><div class="space-y-2"><label class="<?= $label ?>">Sex</label><input value="<?= $f('sex') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('contact_number') || $on('email')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <?php if ($on('contact_number')): ?><div class="space-y-2"><label class="<?= $label ?>">Contact Number</label><input value="<?= $f('contact_number') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('email')): ?><div class="space-y-2"><label class="<?= $label ?>">Email</label><input value="<?= $f('email') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php if ($on('address_1')): ?><div class="space-y-2"><label class="<?= $label ?>">Address 1</label><input value="<?= $f('address_1') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('address_2')): ?><div class="space-y-2"><label class="<?= $label ?>">Address 2</label><input value="<?= $f('address_2') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('address_3')): ?><div class="space-y-2"><label class="<?= $label ?>">Address 3</label><input value="<?= $f('address_3') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('country')): ?><div class="space-y-2"><label class="<?= $label ?>">Country</label><input value="<?= $f('country') ?: 'MALAYSIA' ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('state')): ?><div class="space-y-2"><label class="<?= $label ?>">State</label><input value="<?= $f('state') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('city')): ?><div class="space-y-2"><label class="<?= $label ?>">City</label><input value="<?= $f('city') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                            <?php if ($on('postal_code')): ?><div class="space-y-2"><label class="<?= $label ?>">Postal Code</label><input value="<?= $f('postal_code') ?>" class="<?= $ro ?>" readonly/></div><?php endif; ?>
                        </div>
                    </div>
                </section>

                <?php if ($on('driving_license')): ?>
                <!-- Driving License — always shown, "No Record" when empty -->
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
                                <td class="p-2"><?= esc($lic['license_class'] ?? '-') ?></td>
                                <td class="p-2"><?= ! empty($lic['license_expiry']) ? esc(date('d/m/Y', strtotime((string) $lic['license_expiry']))) : '-' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </section>
                <?php endif; ?>

                <?php if ($on('csp_number', false) || $on('evetting', false)): ?>
                <!-- CSP & E-Vetting -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary"><span class="material-symbols-outlined">verified_user</span></div>
                        <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">CSP &amp; E-Vetting</h2>
                    </div>
                    <div class="space-y-6">
                        <?php if ($on('csp_number', false)): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2"><label class="<?= $label ?>">CSP Number</label><input value="<?= $f('csp_number') ?>" class="<?= $ro ?>" readonly/></div>
                            <div class="space-y-2"><label class="<?= $label ?>">CSP Expiry Date</label><input value="<?= $date('csp_expiry_date') ?>" class="<?= $ro ?>" readonly/></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('evetting', false)): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div class="space-y-2"><label class="<?= $label ?>">E-Vetting Date Of Application</label><input value="<?= $date('evetting_date_of_application') ?>" class="<?= $ro ?>" readonly/></div>
                            <div class="space-y-2"><label class="<?= $label ?>">E-Vetting Date Of Result</label><input value="<?= $date('evetting_date_of_result') ?>" class="<?= $ro ?>" readonly/></div>
                            <div class="space-y-2"><label class="<?= $label ?>">E-Vetting Result</label><input value="<?= $f('evetting_result') ?>" class="<?= $ro ?>" readonly/></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Upload — whatever was attached at intake, shown as links, "No Record" if none. -->
                <?php
                    $uploadLinks = [];
                    if (! empty($staff['photo'])) {
                        $uploadLinks[] = ['label' => 'Passport Photo — ' . basename((string) $staff['photo']), 'url' => base_url('uploads/staff_photos/' . basename((string) $staff['photo']))];
                    }
                    if (! empty($staff['government_id'])) {
                        $uploadLinks[] = ['label' => 'IC / Passport — ' . basename((string) $staff['government_id']), 'url' => base_url('uploads/government_ids/' . basename((string) $staff['government_id']))];
                    }
                    $otherDocs = json_decode((string) ($staff['other_doc'] ?? ''), true);
                    if (! is_array($otherDocs)) {
                        $otherDocs = ! empty($staff['other_doc']) ? [$staff['other_doc']] : [];
                    }
                    foreach ($otherDocs as $doc) {
                        if ($doc) {
                            $uploadLinks[] = ['label' => basename((string) $doc), 'url' => base_url('uploads/other_docs/' . basename((string) $doc))];
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
