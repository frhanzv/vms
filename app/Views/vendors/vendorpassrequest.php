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
                    <?= isset($isEdit) ? 'Edit Vendor Pass' : 'Vendor Pass Request' ?>
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
                ?>

                <!-- Application Information -->
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
                            <?php if ($on('sub_type')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Sub Type</label>
                                <input name="sub_type" value="<?= $v('sub_type') ?>" class="<?= $inputClass ?>" type="text" placeholder="e.g. Contractor, Supplier"/>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('resident')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Resident</label>
                                <select name="resident" class="<?= $inputClass ?>">
                                    <option value="Malaysian" <?= $sel('resident', 'Malaysian') ?>>Malaysian</option>
                                    <option value="Non-Malaysian" <?= $sel('resident', 'Non-Malaysian') ?>>Non-Malaysian</option>
                                </select>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <?php if ($on('vendor_company')): ?>
                <!-- Vendor Company -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">business</span>
                        </div>
                        <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Vendor Company</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Company SSM No / Reg ID</label>
                            <input name="vendor_company_reg_id" value="<?= $v('vendor_company_reg_id') ?>" class="<?= $inputClass ?>" type="text" maxlength="100"/>
                        </div>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Company Name</label>
                            <input name="vendor_company_name" value="<?= $v('vendor_company_name') ?>" class="<?= $inputClass ?>" type="text" maxlength="255"/>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Personal Details -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">badge</span>
                        </div>
                        <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Personal Details</h2>
                    </div>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Full Name</label>
                                <input name="full_name" value="<?= $v('full_name') ?>" class="<?= $inputClass ?>" type="text" maxlength="100" required/>
                            </div>
                            <?php if ($on('staff_no')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Staff No. (at vendor company)</label>
                                <input name="staff_no" value="<?= $v('staff_no') ?>" class="<?= $inputClass ?>" type="text" maxlength="50"/>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($on('ic_passport') || $on('date_of_birth') || $on('sex')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <?php if ($on('ic_passport')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">IC Number</label>
                                <input name="ic_no" value="<?= $v('ic_no') ?>" class="<?= $inputClass ?>" type="text" maxlength="50"/>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Passport Number</label>
                                <input name="passport_no" value="<?= $v('passport_no') ?>" class="<?= $inputClass ?>" type="text" maxlength="16"/>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('date_of_birth')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Date Of Birth</label>
                                <input name="dob" value="<?= $v('dob') ?>" class="<?= $inputClass ?>" type="date"/>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('sex')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Sex</label>
                                <select name="sex" class="<?= $inputClass ?>">
                                    <option value="Male" <?= $sel('sex', 'Male') ?>>Male</option>
                                    <option value="Female" <?= $sel('sex', 'Female') ?>>Female</option>
                                </select>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('designation') || $on('contact_number') || $on('email')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php if ($on('designation')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Designation</label>
                                <input name="designation" value="<?= $v('designation') ?>" class="<?= $inputClass ?>" type="text" maxlength="50"/>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('contact_number')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Contact Number</label>
                                <input name="contact_no" value="<?= $v('contact_no') ?>" class="<?= $inputClass ?>" type="tel" maxlength="30"/>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('email')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Email</label>
                                <input name="email" value="<?= $v('email') ?>" class="<?= $inputClass ?>" type="email" maxlength="100"/>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <?php if ($on('address')): ?>
                <!-- Address -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">home</span>
                        </div>
                        <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Address</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Address Line 1</label>
                            <input name="address_1" value="<?= $v('address_1') ?>" class="<?= $inputClass ?>" type="text" maxlength="150"/>
                        </div>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Address Line 2</label>
                            <input name="address_2" value="<?= $v('address_2') ?>" class="<?= $inputClass ?>" type="text" maxlength="150"/>
                        </div>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Address Line 3</label>
                            <input name="address_3" value="<?= $v('address_3') ?>" class="<?= $inputClass ?>" type="text" maxlength="150"/>
                        </div>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Postcode</label>
                            <input name="postcode" value="<?= $v('postcode') ?>" class="<?= $inputClass ?>" type="text" maxlength="10"/>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ($on('visit_details')): ?>
                <!-- Visit Details -->
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
                <!-- CSP & E-Vetting -->
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
                                <label class="<?= $labelClass ?>">CSP Number</label>
                                <input name="csp_number" value="<?= $v('csp_number') ?>" class="<?= $inputClass ?>" type="text" maxlength="50"/>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">CSP Expiry Date</label>
                                <input name="csp_expiry_date" value="<?= $v('csp_expiry_date') ?>" class="<?= $inputClass ?>" type="date"/>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('evetting')): ?>
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

                <!-- Pass & Documents -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">badge</span>
                        </div>
                        <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Pass &amp; Documents</h2>
                    </div>
                    <div class="space-y-6">
                        <?php if ($on('pass_expiry') || $on('remark')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <?php if ($on('pass_expiry')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Pass Expiry</label>
                                <input name="pass_expiry" value="<?= $v('pass_expiry') ?>" class="<?= $inputClass ?>" type="date"/>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('remark')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Remark</label>
                                <input name="remark" value="<?= $v('remark') ?>" class="<?= $inputClass ?>" type="text"/>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('photo_upload') || $on('document_upload')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <?php if ($on('photo_upload')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Photo</label>
                                <input name="photo" class="<?= $inputClass ?> pt-2.5" type="file" accept="image/*"/>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('document_upload')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Government ID (MyKad/Passport scan)</label>
                                <input name="government_id" class="<?= $inputClass ?> pt-2.5" type="file"/>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Other Documents</label>
                                <input name="other_doc[]" class="<?= $inputClass ?> pt-2.5" type="file" multiple/>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pb-8">
                    <a href="<?= base_url('vendors') ?>" class="h-12 px-6 rounded-lg border border-border-color dark:border-gray-700 text-text-main dark:text-gray-200 font-brand font-medium flex items-center hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="h-12 px-6 rounded-lg bg-primary hover:bg-primary-hover text-white font-brand font-semibold flex items-center gap-2 shadow transition-colors">
                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                        <?= isset($isEdit) ? 'Save Changes' : 'Submit Request' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
