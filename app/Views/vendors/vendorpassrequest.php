<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#137fec",
                        "primary-hover": "#0f6ac6",
                        secondary: "#3b82f6",
                        success: "#10b981",
                        "background-light": "#f6f7f8",
                        "background-dark": "#111827",
                        "card-light": "#ffffff",
                        "card-dark": "#1f2937",
                        "nav-active": "#e0efff",
                        "nav-text": "#344767",
                        "nav-icon": "#3b82f6",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1a2632",
                        "text-main": "#0d141b",
                        "text-sub": "#4c739a",
                        "border-color": "#c4d0dc",
                    },
                    fontFamily: {
                        display: ["Montserrat", "sans-serif"],
                        sans: ["Montserrat", "sans-serif"],
                        brand: ["Montserrat", "sans-serif"],
                    },
                    borderRadius: { DEFAULT: "0.375rem" },
                },
            },
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cfdbe7; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #4c739a; }
        input[type="date"]::-webkit-calendar-picker-indicator { opacity: 1; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden transition-colors duration-200">

    <?= view("partials/sidebar") ?>

    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8">
        <div class="max-w-[960px] mx-auto">

            <div class="mb-8 space-y-2">
                <h1 class="text-3xl sm:text-4xl font-black text-text-main dark:text-white font-brand tracking-tight">
                    <?= isset($isEdit) ? 'Edit Vendor Pass' : 'Contractor/Vendor Request' ?>
                </h1>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-6 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 text-sm">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url($formAction ?? 'vendors/vendorpassrequest/store') ?>" method="post" enctype="multipart/form-data" class="space-y-8">
                <?= csrf_field() ?>
                <?php
                    $s   = $vendor ?? [];
                    $v   = fn($f) => esc(old($f, $s[$f] ?? ''));
                    $sel = fn($f, $val) => ($s[$f] ?? '') === $val ? 'selected' : '';
                    $inputClass = 'w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand';
                    $labelClass = 'block text-sm font-medium text-text-main dark:text-gray-200 font-brand';

                    // Config-driven field/section toggles — set by Config > Dynamic Form Fields >
                    // Vendor Pass Request. Absence of a key means enabled (same default as everywhere else).
                    $on = fn(string $key) => $fields[$key] ?? true;

                    // Config-driven "Mandatory" toggle for the same fields. $req() returns the
                    // red asterisk markup, $reqAttr() returns the HTML `required` attribute —
                    // both read from the same per-company setting the server validates against.
                    $required = $required ?? [];
                    $req      = fn(string $key) => ($required[$key] ?? false) ? ' <span class="text-red-500">*</span>' : '';
                    $reqAttr  = fn(string $key) => ($required[$key] ?? false) ? 'required' : '';

                    $selectedLocations = ! empty($s['location_access']) ? explode(',', $s['location_access']) : [];
                    $existingLicenses  = $licenses ?? [];
                ?>

                <!-- Application Info (matches KPK's real request form field-for-field) -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">assignment</span>
                        </div>
                        <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Application Info</h2>
                    </div>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Date Of Application</label>
                                <input name="date_of_application" value="<?= isset($isEdit) ? esc($s['date_of_application'] ?? '') : date('d/m/Y') ?>" class="<?= $inputClass ?> bg-gray-100 dark:bg-background-dark" type="text" readonly/>
                            </div>
                            <?php if ($on('type_of_application')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Type Of Application</label>
                                <select name="type_of_application" class="<?= $inputClass ?>">
                                    <option value="NEW" <?= $sel('type_of_application', 'NEW') ?>>NEW</option>
                                    <option value="RENEWAL" <?= $sel('type_of_application', 'RENEWAL') ?>>RENEWAL</option>
                                    <option value="REPLACEMENT" <?= $sel('type_of_application', 'REPLACEMENT') ?>>REPLACEMENT</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('type_of_registration')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Type Of Registration<?= $req('type_of_registration') ?></label>
                                <select name="type_of_registration" class="<?= $inputClass ?>" <?= $reqAttr('type_of_registration') ?>>
                                    <option value="">-- Select --</option>
                                    <option value="TENANT" <?= $sel('type_of_registration', 'TENANT') ?>>TENANT</option>
                                    <option value="NON-TENANT" <?= $sel('type_of_registration', 'NON-TENANT') ?>>NON-TENANT</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Designation<?= $req('designation') ?></label>
                                <input name="designation" value="<?= $v('designation') ?>" class="<?= $inputClass ?>" type="text" maxlength="50" <?= $reqAttr('designation') ?>/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php if ($on('payment')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Payment<?= $req('payment') ?></label>
                                <select name="payment" class="<?= $inputClass ?>" <?= $reqAttr('payment') ?>>
                                    <option value="NONE" <?= $sel('payment', 'NONE') ?>>NONE</option>
                                    <option value="ONLINE" <?= $sel('payment', 'ONLINE') ?>>ONLINE</option>
                                    <option value="CASH" <?= $sel('payment', 'CASH') ?>>CASH</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('resident')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Resident<?= $req('resident') ?></label>
                                <select name="resident" class="<?= $inputClass ?>" <?= $reqAttr('resident') ?>>
                                    <option value="Malaysian" <?= $sel('resident', 'Malaysian') ?>>Malaysian</option>
                                    <option value="Non-Malaysian" <?= $sel('resident', 'Non-Malaysian') ?>>Non-Malaysian</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('card_type')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Worker Type<?= $req('card_type') ?></label>
                                <select name="card_type" class="<?= $inputClass ?>" <?= $reqAttr('card_type') ?>>
                                    <option value="">-- Select --</option>
                                    <option value="Permanent" <?= $sel('card_type', 'Permanent') ?>>Permanent</option>
                                    <option value="Temporary" <?= $sel('card_type', 'Temporary') ?>>Temporary (Contract)</option>
                                </select>
                                <p class="text-xs text-text-sub">This decides the pass/card type printed for this person — it's no longer chosen later in Process List.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($on('location_access')): ?>
                        <div class="space-y-2" x-data="{ selected: <?= json_encode($selectedLocations) ?> }">
                            <div class="flex items-center gap-3">
                                <label class="<?= $labelClass ?>">Location Access<?= $req('location_access') ?></label>
                                <label class="flex items-center gap-1.5 text-sm text-text-sub">
                                    <input type="checkbox" @change="selected = $event.target.checked ? <?= json_encode(array_keys($locationOptions)) ?> : []"
                                        :checked="selected.length === <?= count($locationOptions) ?>"/>
                                    Select All
                                </label>
                            </div>
                            <div class="flex flex-wrap gap-6 pt-1">
                                <?php foreach ($locationOptions as $code => $label): ?>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="location_access[]" value="<?= esc($code) ?>" x-model="selected"/>
                                    <?= esc($label) ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <?php if ($on('vendor_company')): ?>
                <!-- Company -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">business</span>
                        </div>
                        <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Company</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Company Registration ID<?= $req('vendor_company') ?></label>
                            <?php if (! empty($lockedCompany)): ?>
                            <input name="vendor_company_reg_id" value="<?= esc($lockedCompany['registration_no']) ?>" class="<?= $inputClass ?> bg-gray-100 dark:bg-gray-800 cursor-not-allowed" type="text" maxlength="100" readonly/>
                            <?php else: ?>
                            <input name="vendor_company_reg_id" value="<?= $v('vendor_company_reg_id') ?>" class="<?= $inputClass ?>" type="text" maxlength="100"/>
                            <?php endif; ?>
                        </div>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Company Name<?= $req('vendor_company') ?></label>
                            <?php if (! empty($lockedCompany)): ?>
                            <input name="vendor_company_name" value="<?= esc($lockedCompany['name']) ?>" class="<?= $inputClass ?> bg-gray-100 dark:bg-gray-800 cursor-not-allowed" type="text" maxlength="255" readonly/>
                            <?php else: ?>
                            <input name="vendor_company_name" value="<?= $v('vendor_company_name') ?>" class="<?= $inputClass ?>" type="text" maxlength="255"/>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Person -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">badge</span>
                            </div>
                            <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Person</h2>
                        </div>
                        <button type="button" onclick="document.getElementById('governmentIdInput').click()" class="h-10 px-4 rounded-lg bg-primary hover:bg-primary-hover text-white text-sm font-brand font-semibold">
                            Upload IC
                        </button>
                    </div>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <?php if ($on('in_out_bound')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">In/ Out Bound<?= $req('in_out_bound') ?></label>
                                <select name="in_out_bound" class="<?= $inputClass ?>" <?= $reqAttr('in_out_bound') ?>>
                                    <option value="INSIDE" <?= $sel('in_out_bound', 'INSIDE') ?>>INSIDE</option>
                                    <option value="OUTSIDE" <?= $sel('in_out_bound', 'OUTSIDE') ?>>OUTSIDE</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('ic_passport')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">IC Number<?= $req('ic_passport') ?></label>
                                <input name="ic_no" value="<?= $v('ic_no') ?>" class="<?= $inputClass ?>" type="text" maxlength="50"/>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Passport Number<?= $req('ic_passport') ?></label>
                                <input name="passport_no" value="<?= $v('passport_no') ?>" class="<?= $inputClass ?>" type="text" maxlength="16"/>
                            </div>
                            <?php if ($required['ic_passport'] ?? false): ?>
                            <p class="text-xs text-text-sub sm:col-span-2 -mt-4">Either IC Number or Passport Number is required.</p>
                            <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($on('date_of_birth')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Date Of Birth<?= $req('date_of_birth') ?></label>
                                <input name="dob" value="<?= $v('dob') ?>" class="<?= $inputClass ?>" type="date" <?= $reqAttr('date_of_birth') ?>/>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php if ($on('sex')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Sex<?= $req('sex') ?></label>
                                <select name="sex" class="<?= $inputClass ?>" <?= $reqAttr('sex') ?>>
                                    <option value="Male" <?= $sel('sex', 'Male') ?>>Male</option>
                                    <option value="Female" <?= $sel('sex', 'Female') ?>>Female</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('full_name')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Full Name<?= $req('full_name') ?></label>
                                <input name="full_name" value="<?= $v('full_name') ?>" class="<?= $inputClass ?>" type="text" maxlength="100" <?= $reqAttr('full_name') ?>/>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('name_on_vendor_pass')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Name On Vendor Pass<?= $req('name_on_vendor_pass') ?></label>
                                <input name="name_on_vendor_pass" value="<?= $v('name_on_vendor_pass') ?>" class="<?= $inputClass ?>" type="text" maxlength="100" <?= $reqAttr('name_on_vendor_pass') ?>/>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($on('contact_number') || $on('email')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <?php if ($on('contact_number')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Contact Number<?= $req('contact_number') ?></label>
                                <input name="contact_no" value="<?= $v('contact_no') ?>" class="<?= $inputClass ?>" type="tel" maxlength="30" <?= $reqAttr('contact_number') ?>/>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('email')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Email Address<?= $req('email') ?></label>
                                <input name="email" value="<?= $v('email') ?>" class="<?= $inputClass ?>" type="email" maxlength="100" <?= $reqAttr('email') ?>/>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('address')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Address 1<?= $req('address') ?></label>
                                <input name="address_1" value="<?= $v('address_1') ?>" class="<?= $inputClass ?>" type="text" maxlength="150"/>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Address 2</label>
                                <input name="address_2" value="<?= $v('address_2') ?>" class="<?= $inputClass ?>" type="text" maxlength="150"/>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Address 3</label>
                                <input name="address_3" value="<?= $v('address_3') ?>" class="<?= $inputClass ?>" type="text" maxlength="150"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Country</label>
                                <select name="country" class="<?= $inputClass ?> bg-gray-100 dark:bg-background-dark" disabled>
                                    <option selected>MALAYSIA</option>
                                </select>
                                <input type="hidden" name="country" value="Malaysia"/>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">State</label>
                                <select name="state" class="<?= $inputClass ?>">
                                    <option value="">SELECT</option>
                                    <?php foreach ($stateOptions ?? [] as $state): ?>
                                    <option value="<?= esc($state) ?>" <?= $sel('state', $state) ?>><?= esc(strtoupper($state)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">City</label>
                                <input name="city" value="<?= $v('city') ?>" class="<?= $inputClass ?>" type="text" maxlength="50" placeholder="SELECT"/>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Postal Code</label>
                                <input name="postcode" value="<?= $v('postcode') ?>" class="<?= $inputClass ?>" type="text" maxlength="10"/>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <?php if ($on('vehicle_registration')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Vehicle Registration Number<?= $req('vehicle_registration') ?></label>
                                <input name="vehicle_registration" value="<?= $v('vehicle_registration') ?>" class="<?= $inputClass ?>" type="text" maxlength="20" <?= $reqAttr('vehicle_registration') ?>/>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('staff_no')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Staff No. (at vendor company)<?= $req('staff_no') ?></label>
                                <input name="staff_no" value="<?= $v('staff_no') ?>" class="<?= $inputClass ?>" type="text" maxlength="50" <?= $reqAttr('staff_no') ?>/>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <!-- Driving License -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8"
                    x-data="{ rows: [<?php foreach ($existingLicenses as $lic): ?>{ class: <?= json_encode($lic['license_class'] ?? '') ?>, expiry: <?= json_encode($lic['license_expiry'] ?? '') ?>, saved: true }, <?php endforeach; ?>] }">
                    <div class="flex items-center justify-between gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">directions_car</span>
                            </div>
                            <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Driving License</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="rows.push({ class: '', expiry: '', saved: false })" class="size-9 rounded-full border border-border-color dark:border-gray-700 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-800">
                                <span class="material-symbols-outlined text-[20px]">add</span>
                            </button>
                            <button type="button" @click="if (rows.length) rows.pop()" class="size-9 rounded-full border border-border-color dark:border-gray-700 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-800">
                                <span class="material-symbols-outlined text-[20px]">remove</span>
                            </button>
                        </div>
                    </div>
                    <p x-show="rows.length === 0" class="text-sm text-text-sub text-center py-4">No Record</p>
                    <template x-for="(row, i) in rows" :key="i">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-4">
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">License Class</label>
                                <input :name="'license_class[' + i + ']'" x-model="row.class" class="<?= $inputClass ?>" type="text" placeholder="e.g. D" :readonly="row.saved" :class="row.saved ? 'bg-gray-100 dark:bg-background-dark' : ''"/>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">License Expiry</label>
                                <input :name="'license_expiry[' + i + ']'" x-model="row.expiry" class="<?= $inputClass ?>" type="date" :readonly="row.saved" :class="row.saved ? 'bg-gray-100 dark:bg-background-dark' : ''"/>
                            </div>
                        </div>
                    </template>
                </section>

                <?php if ($on('visit_details')): ?>
                <!-- Visit Details (VMS addition, kept beyond KPK's own request form) -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">location_on</span>
                        </div>
                        <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Visit Details</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Name Of Person Visited</label>
                            <input name="name_of_person_visited" value="<?= $v('name_of_person_visited') ?>" class="<?= $inputClass ?>" type="text" maxlength="100"/>
                        </div>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Contact No. Of Person Visited</label>
                            <input name="contact_no_of_person_visited" value="<?= $v('contact_no_of_person_visited') ?>" class="<?= $inputClass ?>" type="tel" maxlength="14"/>
                        </div>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Location Visited</label>
                            <input name="location_visited" value="<?= $v('location_visited') ?>" class="<?= $inputClass ?>" type="text" maxlength="100"/>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ($on('csp_number') || $on('evetting')): ?>
                <!-- CSP & E-Vetting (VMS addition, kept beyond KPK's own request form) -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">verified_user</span>
                        </div>
                        <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">CSP &amp; E-Vetting</h2>
                    </div>
                    <div class="space-y-6">
                        <?php if ($on('csp_number')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">CSP Number<?= $req('csp_number') ?></label>
                                <input name="csp_number" value="<?= $v('csp_number') ?>" class="<?= $inputClass ?>" type="text" maxlength="50" <?= $reqAttr('csp_number') ?>/>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">CSP Expiry Date</label>
                                <input name="csp_expiry_date" value="<?= $v('csp_expiry_date') ?>" class="<?= $inputClass ?>" type="date"/>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('evetting') && empty($lockedCompany)): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">E-Vetting Date Of Application</label>
                                <input name="evetting_date_of_application" value="<?= $v('evetting_date_of_application') ?>" class="<?= $inputClass ?>" type="date"/>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">E-Vetting Date Of Result</label>
                                <input name="evetting_date_of_result" value="<?= $v('evetting_date_of_result') ?>" class="<?= $inputClass ?>" type="date"/>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">E-Vetting Result</label>
                                <input name="evetting_result" value="<?= $v('evetting_result') ?>" class="<?= $inputClass ?>" type="text" maxlength="20"/>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ($on('pass_expiry') || $on('remark')): ?>
                <!-- Pass -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">badge</span>
                        </div>
                        <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Pass</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <?php if ($on('pass_expiry')): ?>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Pass Expiry<?= $req('pass_expiry') ?></label>
                            <input name="pass_expiry" value="<?= $v('pass_expiry') ?>" class="<?= $inputClass ?>" type="date" <?= $reqAttr('pass_expiry') ?>/>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('remark')): ?>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Remark<?= $req('remark') ?></label>
                            <input name="remark" value="<?= $v('remark') ?>" class="<?= $inputClass ?>" type="text" <?= $reqAttr('remark') ?>/>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Upload -->
                <?php if ($on('photo_upload') || $on('document_upload')): ?>
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">upload_file</span>
                        </div>
                        <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Upload</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <?php if ($on('document_upload')): ?>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Photostat IC / Passport</label>
                            <input id="governmentIdInput" name="government_id" class="<?= $inputClass ?> pt-2.5" type="file"/>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('photo_upload')): ?>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Passport Photo</label>
                            <label for="passportPhotoInput" class="flex flex-col items-center justify-center h-32 rounded-lg border-2 border-dashed border-border-color dark:border-gray-700 text-text-sub text-sm cursor-pointer hover:border-primary transition-colors">
                                <span class="material-symbols-outlined text-2xl mb-1">cloud_upload</span>
                                Click or drag files to upload
                                <input id="passportPhotoInput" name="photo" class="hidden" type="file" accept="image/*" onchange="this.previousElementSibling.nextSibling.textContent = this.files[0] ? this.files[0].name : 'Click or drag files to upload'"/>
                            </label>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('document_upload')): ?>
                        <div class="space-y-2 sm:col-span-2">
                            <label class="<?= $labelClass ?>">Other Documents</label>
                            <input name="other_doc[]" class="<?= $inputClass ?> pt-2.5" type="file" multiple/>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ($on('additional_verification')): ?>
                <!-- Additional Verification (VMS addition, kept beyond KPK's own request form) -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">verified</span>
                        </div>
                        <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Additional Verification</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">MySejahtera Certificate</label>
                            <input name="mysejahtera_cert" class="<?= $inputClass ?> pt-2.5" type="file" accept="image/*,application/pdf"/>
                        </div>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">MySejahtera Certificate (2nd Dose)</label>
                            <input name="mysejahtera_cert_2" class="<?= $inputClass ?> pt-2.5" type="file" accept="image/*,application/pdf"/>
                        </div>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Facial Identity Photo</label>
                            <input name="facial_photo" class="<?= $inputClass ?> pt-2.5" type="file" accept="image/*"/>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pb-8">
                    <a href="<?= base_url('vendors') ?>" class="h-12 px-6 rounded-lg border border-border-color dark:border-gray-700 text-text-main dark:text-gray-200 font-brand font-medium flex items-center hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" name="save_as_draft" value="1" formnovalidate class="h-12 px-6 rounded-lg border border-border-color dark:border-gray-700 text-text-main dark:text-gray-200 font-brand font-semibold flex items-center gap-2 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        Save As Draft
                    </button>
                    <button type="submit" class="h-12 px-6 rounded-lg bg-primary hover:bg-primary-hover text-white font-brand font-semibold flex items-center gap-2 shadow transition-colors">
                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                        <?= isset($isEdit) ? 'Save Changes' : 'Submit' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
