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
                        "primary-hover": "#0f6ac6",        // ← ADD THIS
                        secondary: "#3b82f6",
                        success: "#10b981",
                        "background-light": "#f6f7f8",
                        "background-dark": "#111827",
                        "card-light": "#ffffff",
                        "card-dark": "#1f2937",
                        "nav-active": "#e0efff",
                        "nav-text": "#344767",
                        "nav-icon": "#3b82f6",
                        "surface-light": "#ffffff",         // ← ADD THIS
                        "surface-dark": "#1a2632",          // ← ADD THIS
                        "text-main": "#0d141b",             // ← ADD THIS
                        "text-sub": "#4c739a",              // ← ADD THIS
                        "border-color": "#c4d0dc",          // ← ADD THIS
                    },
                    fontFamily: {
                        display: ["Montserrat", "sans-serif"],
                        sans: ["Montserrat", "sans-serif"],
                        brand: ["Montserrat", "sans-serif"], // ← ADD THIS
                    },
                    borderRadius: {
                        DEFAULT: "0.375rem",
                    },
                },
            },
        };
    </script>

    <!-- Blacklist dropdown function-->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cfdbe7;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #4c739a;
        }
        input[type="date"]::-webkit-calendar-picker-indicator { opacity: 1; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden transition-colors duration-200">

    <!-- Sidebar -->
    <?= view("partials/sidebar") ?>


    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8">
        <div class="max-w-[960px] mx-auto">
            <!-- Page Header -->
                <div class="mb-8 space-y-2">
                    <h1 class="text-3xl sm:text-4xl font-black text-text-main dark:text-white font-brand tracking-tight"><?= ! empty($isRenew) ? 'Renew Staff Pass' : (isset($isEdit) ? 'Edit Staff' : 'Staff Pass Request') ?></h1>
                    <?php if (! empty($isRenew)): ?>
                    <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Name, IC / passport and Staff No. stay as they are. The renewal goes back to Pending for approval; once approved a new card is printed and issued.</p>
                    <?php endif; ?>
                </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-6 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 text-sm">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>
            <?php if (($staff['status'] ?? '') === 'Rejected' && ! empty($staff['reject_reason'])): ?>
                <div class="mb-6 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 text-sm">
                    This request was rejected: <strong><?= esc($staff['reject_reason']) ?></strong>. Fix it and submit again to send it back for approval.
                </div>
            <?php endif; ?>

            <form action="<?= base_url($formAction ?? 'staffs/staffpassrequest/store') ?>" method="post" enctype="multipart/form-data" class="space-y-8">
                <?= csrf_field() ?>
                <?php
                    $isRenew = ! empty($isRenew);
                    $s   = $staff ?? [];
                    $v   = fn($f, $db = null) => esc(old($f, $s[$db ?? $f] ?? ''));
                    $sel = fn($f, $val) => ($s[$f] ?? '') === $val ? 'selected' : '';
                    $inputClass = 'w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand';
                    $labelClass = 'block text-sm font-medium text-text-main dark:text-gray-200 font-brand';
                    // Renewal keeps the person's identity: these fields are read-only.
                    $lock      = $isRenew ? 'readonly tabindex="-1"' : '';
                    $lockClass = $isRenew ? ' bg-gray-100 dark:bg-gray-800 cursor-not-allowed' : '';

                    // Config > Dynamic Form Fields > Staff Pass Request: shown / hidden ...
                    $fs = $fieldSettings ?? [];
                    $on = fn(string $key, bool $default = true) => (bool) ($fs[$key] ?? $default);
                    // ... and "Mandatory": red * and the browser's required check.
                    $required = $required ?? [];
                    $req      = fn(string $k) => ($required[$k] ?? false) ? ' <span class="text-red-500">*</span>' : '';
                    $reqAttr  = fn(string $k) => ($required[$k] ?? false) ? 'required' : '';

                    $existingLicenses = $licenses ?? [];
                ?>

                <!-- Application Info -->
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
                                <input name="date_of_application" value="<?= isset($isEdit) && ! $isRenew ? esc($s['date_of_application'] ?? '') : date('d/m/Y') ?>" class="<?= $inputClass ?> bg-gray-100 dark:bg-background-dark" type="text" readonly/>
                            </div>
                            <?php if ($on('type_of_application')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Type Of Application<?= $req('type_of_application') ?></label>
                                <?php if ($isRenew): ?>
                                <input type="hidden" name="type_of_application" value="RENEWAL"/>
                                <input value="RENEWAL" class="<?= $inputClass ?> bg-gray-100 dark:bg-gray-800 cursor-not-allowed" type="text" readonly/>
                                <?php else: ?>
                                <select name="type_of_application" class="<?= $inputClass ?>" <?= $reqAttr('type_of_application') ?>>
                                    <option value="NEW" <?= $sel('type_of_application', 'NEW') ?>>NEW</option>
                                    <option value="RENEWAL" <?= $sel('type_of_application', 'RENEWAL') ?>>RENEWAL</option>
                                    <option value="REPLACEMENT" <?= $sel('type_of_application', 'REPLACEMENT') ?>>REPLACEMENT</option>
                                </select>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('access_branch')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Approving Branch</label>
                                <select name="access_branch" class="<?= $inputClass ?>">
                                    <?php foreach (['' => '-- Select --', 'KSB' => 'KSB', 'KPK' => 'KPK', 'BOTH' => 'Both (KSB + KPK)'] as $bv => $bl): ?>
                                    <option value="<?= $bv ?>" <?= old('access_branch', $s['access_branch'] ?? '') === $bv ? 'selected' : '' ?>><?= $bl ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('designation')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Designation<?= $req('designation') ?></label>
                                <?php $desig = (string) old('designation', $s['designation'] ?? ''); $designations = ['PEN. LORI', 'OPERATOR', 'SUPERVISOR', 'ENGINEER', 'MANAGER']; ?>
                                <select name="designation" class="<?= $inputClass ?>" <?= $reqAttr('designation') ?>>
                                    <option value="">-- Select --</option>
                                    <?php foreach ($designations as $dn): ?>
                                    <option value="<?= esc($dn) ?>" <?= $desig === $dn ? 'selected' : '' ?>><?= esc($dn) ?></option>
                                    <?php endforeach; ?>
                                    <?php if ($desig !== '' && ! in_array($desig, $designations, true)): ?>
                                    <option value="<?= esc($desig) ?>" selected><?= esc($desig) ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php if ($on('resident')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Resident<?= $req('resident') ?></label>
                                <select name="resident" class="<?= $inputClass ?>" <?= $reqAttr('resident') ?>>
                                    <option value="LOCAL" <?= $sel('resident', 'LOCAL') ?>>LOCAL</option>
                                    <option value="FOREIGN" <?= $sel('resident', 'FOREIGN') ?>>FOREIGN</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('sub_type')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Sub Type<?= $req('sub_type') ?></label>
                                <?php $subType = (string) old('sub_type', $s['sub_type'] ?? (isset($isEdit) ? '' : 'PERMANENT')); ?>
                                <select name="sub_type" class="<?= $inputClass ?>" <?= $reqAttr('sub_type') ?>>
                                    <option value="">-- Select --</option>
                                    <?php foreach (['PERMANENT', 'TEMPORARY', 'CONTRACT', 'FAMILY PASS'] as $st): ?>
                                    <option value="<?= $st ?>" <?= $subType === $st ? 'selected' : '' ?>><?= $st ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Visa / Work Permit Expiry <span class="text-xs text-text-sub">(foreigner)</span></label>
                                <input name="visa_expiry" value="<?= $v('visa_expiry') ?>" class="<?= $inputClass ?>" type="date"/>
                            </div>
                        </div>

                        <!-- Location Access — same dropdown as the Contractor/Vendor Request -->
                        <?php if ($on('location_access')): ?>
                        <?php
                            // Every IN / OUT gate value from Location Access Management, A-Z.
                            $locLabels = [];
                            foreach (($locationGroups ?? []) as $groupLocations) {
                                foreach ($groupLocations as $loc) {
                                    foreach (['in', 'out'] as $dir) {
                                        if (! empty($loc[$dir])) {
                                            $locLabels[$loc[$dir]] = $loc[$dir];
                                        }
                                    }
                                }
                            }
                            uksort($locLabels, 'strnatcasecmp');
                            $selectedLocations = array_values(array_filter(array_map('trim', explode(',', (string) ($s['location_access'] ?? '')))));
                        ?>
                        <?php if (! empty($lockLocations)): ?>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Location Access<?= $req('location_access') ?></label>
                            <div class="w-full min-h-[3rem] flex flex-wrap items-center gap-1.5 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2 text-sm cursor-not-allowed">
                                <?php if ($selectedLocations === []): ?><span class="text-text-sub">-</span><?php endif; ?>
                                <?php foreach ($selectedLocations as $code): ?>
                                <span class="inline-flex items-center rounded-full bg-primary/10 text-primary px-2.5 py-0.5 text-xs font-medium"><?= esc($code) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <p class="text-xs text-text-sub dark:text-gray-400 font-brand">Changing Location Access on an approved pass needs the Manage Locations permission.</p>
                        </div>
                        <?php else: ?>
                        <div class="space-y-2 relative"
                             x-data="{ open: false, q: '', selected: <?= esc(json_encode(array_values(array_intersect($selectedLocations, array_keys($locLabels)))), 'attr') ?>, labels: <?= esc(json_encode((object) $locLabels), 'attr') ?>,
                                       get codes() { return Object.keys(this.labels); },
                                       get shown() { const q = this.q.toLowerCase(); return this.codes.filter(c => this.labels[c].toLowerCase().includes(q)); },
                                       get allOn() { return this.codes.length > 0 && this.selected.length === this.codes.length; },
                                       toggleAll() { this.selected = this.allOn ? [] : [...this.codes]; },
                                       remove(c) { this.selected = this.selected.filter(x => x !== c); } }"
                             @click.outside="open = false" @keydown.escape.window="open = false">
                            <label class="<?= $labelClass ?>">Location Access<?= $req('location_access') ?></label>

                            <template x-for="c in selected" :key="c"><input type="hidden" name="location_access[]" :value="c"></template>

                            <button type="button" @click="open = !open"
                                class="w-full min-h-[3rem] flex items-center justify-between gap-2 rounded-lg border border-border-color dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-left text-sm focus:ring-2 focus:ring-primary/30">
                                <span class="flex flex-wrap gap-1.5 items-center">
                                    <span x-show="selected.length === 0" class="text-text-sub">-- Select location(s) --</span>
                                    <template x-for="c in selected" :key="c">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-primary/10 text-primary px-2.5 py-0.5 text-xs font-medium">
                                            <span x-text="labels[c] || c"></span>
                                            <span class="cursor-pointer font-bold leading-none" @click.stop="remove(c)" title="Remove">&times;</span>
                                        </span>
                                    </template>
                                </span>
                                <span class="material-symbols-outlined text-text-sub transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                            </button>

                            <div x-show="open" x-transition.opacity
                                 class="absolute z-30 mt-1 w-full rounded-lg border border-border-color dark:border-gray-700 bg-white dark:bg-gray-900 shadow-lg">
                                <div class="p-2 border-b border-border-color dark:border-gray-700">
                                    <input type="text" x-model="q" placeholder="Search location..."
                                        class="w-full rounded-md border border-border-color dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm"/>
                                </div>
                                <label class="flex items-center gap-2 px-3 py-2 text-sm font-semibold border-b border-border-color dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <input type="checkbox" :checked="allOn" @change="toggleAll()"/>
                                    Select All <span class="text-text-sub font-normal" x-text="'(' + selected.length + '/' + codes.length + ')'"></span>
                                </label>
                                <div class="max-h-64 overflow-y-auto py-1">
                                    <template x-for="c in shown" :key="c">
                                        <label class="flex items-center gap-2 px-3 py-1.5 text-sm cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <input type="checkbox" :value="c" x-model="selected"/>
                                            <span x-text="labels[c]"></span>
                                        </label>
                                    </template>
                                    <p x-show="shown.length === 0" class="px-3 py-2 text-sm text-text-sub">No location found.</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Person -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">badge</span>
                            </div>
                            <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Person</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <?php if (($mykadOcrEnabled ?? true) && ! $isRenew): ?>
                            <button type="button" id="staff-mykad-btn" class="h-10 px-4 rounded-lg border border-primary text-primary hover:bg-primary/10 text-sm font-brand font-semibold">
                                Read MyKad
                            </button>
                            <?php endif; ?>
                            <?php if ($on('document_upload')): ?>
                            <button type="button" onclick="document.getElementById('governmentIdInput').click()" class="h-10 px-4 rounded-lg bg-primary hover:bg-primary-hover text-white text-sm font-brand font-semibold">
                                Upload IC
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <?php if ($on('ic_number')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">IC / Passport Number<?= $req('ic_number') ?></label>
                                <input name="ic_number" value="<?= $v('ic_number', 'ic_passport') ?>" class="<?= $inputClass . $lockClass ?>" type="text" maxlength="50" <?= $reqAttr('ic_number') ?> <?= $lock ?>/>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('date_of_birth')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Date Of Birth<?= $req('date_of_birth') ?></label>
                                <input name="date_of_birth" value="<?= $v('date_of_birth') ?>" class="<?= $inputClass . $lockClass ?>" type="date" <?= $reqAttr('date_of_birth') ?> <?= $lock ?>/>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('sex')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Sex<?= $req('sex') ?></label>
                                <select name="sex" class="<?= $inputClass ?>" <?= $reqAttr('sex') ?>>
                                    <option value="MALE" <?= $sel('sex', 'MALE') ?>>MALE</option>
                                    <option value="FEMALE" <?= $sel('sex', 'FEMALE') ?>>FEMALE</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('staff_no')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Staff No<?= $req('staff_no') ?></label>
                                <input name="staff_no" value="<?= $v('staff_no') ?>" class="<?= $inputClass . $lockClass ?>" type="text" maxlength="50" <?= $reqAttr('staff_no') ?> <?= $lock ?>/>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php if ($on('full_name')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Full Name<?= $req('full_name') ?></label>
                                <input name="full_name" value="<?= $v('full_name') ?>" class="<?= $inputClass . $lockClass ?>" type="text" maxlength="100" <?= $reqAttr('full_name') ?> <?= $lock ?>/>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('name_on_staff_pass')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Name On Staff Pass<?= $req('name_on_staff_pass') ?></label>
                                <input name="name_on_staff_pass" value="<?= $v('name_on_staff_pass') ?>" class="<?= $inputClass ?>" type="text" maxlength="100" <?= $reqAttr('name_on_staff_pass') ?>/>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('department')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Department<?= $req('department') ?></label>
                                <?php $deps = $departments ?? []; $dept = (string) old('department', $s['department'] ?? ''); if ($dept !== '' && ! in_array($dept, $deps, true)) { $deps[] = $dept; } ?>
                                <select name="department" class="<?= $inputClass ?>" <?= $reqAttr('department') ?>>
                                    <option value="">-- Select --</option>
                                    <?php foreach ($deps as $dep): ?>
                                    <option value="<?= esc($dep, 'attr') ?>" <?= $dept === $dep ? 'selected' : '' ?>><?= esc($dep) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($on('contact_number') || $on('email')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <?php if ($on('contact_number')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Contact Number<?= $req('contact_number') ?></label>
                                <input name="contact_number" value="<?= $v('contact_number') ?>" class="<?= $inputClass ?>" type="tel" maxlength="30" <?= $reqAttr('contact_number') ?>/>
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
                        <?php if ($on('address_1') || $on('address_2') || $on('address_3')): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <?php foreach (['address_1' => 'Address 1', 'address_2' => 'Address 2', 'address_3' => 'Address 3'] as $ak => $al): ?>
                            <?php if ($on($ak)): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>"><?= $al ?><?= $req($ak) ?></label>
                                <input name="<?= $ak ?>" value="<?= $v($ak) ?>" class="<?= $inputClass ?>" type="text" maxlength="150" <?= $reqAttr($ak) ?>/>
                            </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <?php if ($on('country')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Country<?= $req('country') ?></label>
                                <select name="country" id="staff-country" class="<?= $inputClass ?>" <?= $reqAttr('country') ?>>
                                    <option value="">-- Select --</option>
                                    <?php $defaultCountry = ($s['country'] ?? '') ?: 'MALAYSIA'; ?>
                                    <?php foreach ($countries ?? [] as $c): ?>
                                    <option value="<?= esc($c['name']) ?>" data-id="<?= (int) $c['id'] ?>" <?= strtoupper($c['name']) === strtoupper($defaultCountry) ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('state')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">State<?= $req('state') ?></label>
                                <select name="state" id="staff-state" class="<?= $inputClass ?>" <?= $reqAttr('state') ?> disabled>
                                    <option value="">SELECT COUNTRY FIRST</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('city')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">City<?= $req('city') ?></label>
                                <select name="city" id="staff-city" class="<?= $inputClass ?>" <?= $reqAttr('city') ?> disabled>
                                    <option value="">SELECT STATE FIRST</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <?php if ($on('postal_code')): ?>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">Postal Code<?= $req('postal_code') ?></label>
                                <input name="postal_code" value="<?= $v('postal_code') ?>" class="<?= $inputClass ?>" type="text" maxlength="10" <?= $reqAttr('postal_code') ?>/>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($on('remark')): ?>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Remark</label>
                            <textarea name="remark" rows="2" class="w-full rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand"><?= $v('remark') ?></textarea>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <?php if ($on('driving_license')): ?>
                <!-- Driving License (saved rows are shown read-only; only new rows are posted) -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8"
                    x-data="{ rows: [<?php foreach ($existingLicenses as $lic): ?>{ class: <?= esc(json_encode((string) ($lic['license_class'] ?? '')), 'attr') ?>, expiry: <?= esc(json_encode((string) ($lic['license_expiry'] ?? '')), 'attr') ?>, saved: true }, <?php endforeach; ?>] }">
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
                            <button type="button" @click="const i = rows.map(r => r.saved).lastIndexOf(false); if (i > -1) rows.splice(i, 1)" class="size-9 rounded-full border border-border-color dark:border-gray-700 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-800">
                                <span class="material-symbols-outlined text-[20px]">remove</span>
                            </button>
                        </div>
                    </div>
                    <?php if ($isRenew && $existingLicenses): ?>
                    <p class="text-xs text-text-sub mb-4">Adding a new license replaces the current ones when the renewal is submitted.</p>
                    <?php endif; ?>
                    <p x-show="rows.length === 0" class="text-sm text-text-sub text-center py-4">No Record</p>
                    <template x-for="(row, i) in rows" :key="i">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-4">
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">License Class</label>
                                <template x-if="row.saved">
                                    <input :value="row.class" class="<?= $inputClass ?> bg-gray-100 dark:bg-background-dark" type="text" readonly/>
                                </template>
                                <template x-if="! row.saved">
                                    <select :name="'licenses[' + i + '][class]'" x-model="row.class" class="<?= $inputClass ?>">
                                        <option value="">-- Select --</option>
                                        <?php foreach (\App\Libraries\LicenseClasses::options() as $lc): ?>
                                        <option value="<?= esc($lc) ?>"><?= esc($lc) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </template>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">License Expiry</label>
                                <input :name="row.saved ? null : 'licenses[' + i + '][expiry]'" x-model="row.expiry" class="<?= $inputClass ?>" type="date" :readonly="row.saved" :class="row.saved ? 'bg-gray-100 dark:bg-background-dark' : ''"/>
                            </div>
                        </div>
                    </template>
                </section>
                <?php endif; ?>

                <?php if ($on('csp_number', false) || $on('evetting', false)): ?>
                <!-- CSP & E-Vetting -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">verified_user</span>
                        </div>
                        <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">CSP &amp; E-Vetting</h2>
                    </div>
                    <div class="space-y-6">
                        <?php if ($on('csp_number', false)): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">CSP Number<?= $req('csp_number') ?></label>
                                <input name="company_reg_id" value="<?= $v('company_reg_id', 'csp_number') ?>" class="<?= $inputClass ?>" type="text" maxlength="50" <?= $reqAttr('csp_number') ?>/>
                            </div>
                            <div class="space-y-2">
                                <label class="<?= $labelClass ?>">CSP Expiry Date</label>
                                <input name="csp_expiry_date" value="<?= $v('csp_expiry_date') ?>" class="<?= $inputClass ?>" type="date"/>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('evetting', false)): ?>
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
                                <?php $evr = (string) ($s['evetting_result'] ?? 'In Progress'); ?>
                                <select name="evetting_result" class="<?= $inputClass ?>">
                                    <?php foreach (['In Progress', 'Pass', 'Fail'] as $er): ?>
                                    <option value="<?= $er ?>" <?= $evr === $er ? 'selected' : '' ?>><?= $er ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ($on('photo_upload') || $on('document_upload')): ?>
                <!-- Upload -->
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
                            <input id="governmentIdInput" name="government_id" class="<?= $inputClass ?> pt-2.5" type="file" accept="image/*,application/pdf"/>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('photo_upload')): ?>
                        <div class="space-y-2">
                            <label class="<?= $labelClass ?>">Passport Photo <span class="text-xs text-text-sub">(needed before the card is printed)</span></label>
                            <label for="passportPhotoInput" class="flex flex-col items-center justify-center h-32 rounded-lg border-2 border-dashed border-border-color dark:border-gray-700 text-text-sub text-sm cursor-pointer hover:border-primary transition-colors">
                                <?php if (! empty($s['photo'])): ?>
                                <img src="<?= base_url('uploads/staff_photos/' . esc($s['photo'], 'url')) ?>" alt="" class="h-16 w-12 object-cover rounded border mb-1"/>
                                <?php else: ?>
                                <span class="material-symbols-outlined text-2xl mb-1">cloud_upload</span>
                                <?php endif; ?>
                                <span data-photo-name><?= ! empty($s['photo']) ? 'Click to replace the photo' : 'Click or drag files to upload' ?></span>
                                <input id="passportPhotoInput" name="photo" class="hidden" type="file" accept="image/png,image/jpeg" onchange="this.parentElement.querySelector('[data-photo-name]').textContent = this.files[0] ? this.files[0].name : 'Click or drag files to upload'"/>
                            </label>
                        </div>
                        <?php endif; ?>
                        <?php if ($on('document_upload')): ?>
                        <div class="space-y-2 sm:col-span-2">
                            <label class="<?= $labelClass ?>">Other Documents</label>
                            <input name="invitation_letter[]" class="<?= $inputClass ?> pt-2.5" type="file" multiple/>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pb-8">
                    <a href="<?= base_url('staffs') ?>" class="h-12 px-6 rounded-lg border border-border-color dark:border-gray-700 text-text-main dark:text-gray-200 font-brand font-medium flex items-center hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        Cancel
                    </a>
                    <?php if (! $isRenew && in_array($s['status'] ?? 'Draft', ['Draft', ''], true)): ?>
                    <button type="submit" name="save_as_draft" value="1" formnovalidate class="h-12 px-6 rounded-lg border border-border-color dark:border-gray-700 text-text-main dark:text-gray-200 font-brand font-semibold flex items-center gap-2 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        Save As Draft
                    </button>
                    <?php endif; ?>
                    <button type="submit" class="h-12 px-6 rounded-lg bg-primary hover:bg-primary-hover text-white font-brand font-semibold flex items-center gap-2 shadow transition-colors">
                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                        <?= $isRenew ? 'Submit Renewal' : (isset($isEdit) && ($s['status'] ?? '') !== 'Draft' ? 'Save Changes' : 'Submit') ?>
                    </button>
                </div>
            </form>
        </div>
    </main>

<!-- Read MyKad Modal -->
<div id="staff-mykad-modal" class="hidden fixed inset-0 z-[110] overflow-y-auto" aria-hidden="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" id="staff-mykad-backdrop"></div>
        <div class="relative bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-2xl">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white font-brand">Read MyKad</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-brand">Upload your IC card image to auto-fill the form</p>
                </div>
                <button type="button" id="staff-mykad-close" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <input type="file" id="staff-mykad-file" accept="image/*" class="hidden"/>
                <div id="staff-mykad-dropzone" class="border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-xl p-8 text-center cursor-pointer hover:border-primary transition-colors">
                    <span class="material-symbols-outlined text-4xl text-slate-400 mb-3 block">id_card</span>
                    <p class="text-slate-700 dark:text-slate-200 font-semibold font-brand">Drop your MyKad image here</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-brand">or <button type="button" id="staff-mykad-browse" class="text-primary underline font-semibold">browse files</button></p>
                    <p class="text-xs text-slate-400 mt-2 font-brand">Supports JPG, PNG (Max 5MB)</p>
                </div>
                <div id="staff-mykad-selected" class="hidden rounded-lg border border-slate-200 dark:border-slate-700 p-3 bg-slate-50 dark:bg-slate-800/40 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p id="staff-mykad-filename" class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate font-brand"></p>
                        <p id="staff-mykad-filesize" class="text-xs text-slate-500 dark:text-slate-400 font-brand"></p>
                    </div>
                    <button type="button" id="staff-mykad-clear" class="text-red-500 hover:text-red-700 text-sm font-semibold font-brand">Remove</button>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <button type="button" id="staff-mykad-cancel" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-brand">Cancel</button>
                <button type="button" id="staff-mykad-process" class="px-5 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed font-brand" disabled>Process MyKad</button>
            </div>
        </div>
    </div>
</div>

</body>

<script>

        function autoDetectGenderFromIC(icValue) {
            const ic = icValue.replace(/[-\s]/g, '');
            const residentField = document.querySelector('select[name="resident"]');

            if (/^\d{12}$/.test(ic)) {
                if (residentField) residentField.value = 'LOCAL';

                const lastDigit = parseInt(ic.slice(-1));
                const sexField = document.querySelector('select[name="sex"]');
                if (sexField) sexField.value = lastDigit % 2 !== 0 ? 'MALE' : 'FEMALE';

                const yy = parseInt(ic.substring(0, 2));
                const mm = ic.substring(2, 4);
                const dd = ic.substring(4, 6);
                const currentYY = new Date().getFullYear() % 100;
                const fullYear = yy > currentYY ? 1900 + yy : 2000 + yy;
                const dobField = document.querySelector('input[name="date_of_birth"]');
                if (dobField) dobField.value = `${fullYear}-${mm}-${dd}`;

            } else if (/[a-zA-Z]/.test(ic) || ic.length > 12) {
                if (residentField) residentField.value = 'FOREIGN';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const icField = document.querySelector('input[name="ic_number"]');
            if (icField) {
                icField.addEventListener('input', function() {
                    autoDetectGenderFromIC(this.value);
                });
            }

            const additionalDocsInput = document.querySelector('input[name="invitation_letter[]"]');
            if (additionalDocsInput) {
                additionalDocsInput.addEventListener('change', function(e) {
                    const files = Array.from(e.target.files);
                    if (!files.length) return;

                    const oversized = files.filter(f => f.size > 5 * 1024 * 1024);
                    if (oversized.length) {
                        alert('Each file must be less than 5MB.');
                        this.value = '';
                        return;
                    }

                });
            }
        });
// ── Country / State / City cascade ───────────────────────────────────────────

const staffCountryEl = document.getElementById('staff-country');
const staffStateEl   = document.getElementById('staff-state');
const staffCityEl    = document.getElementById('staff-city');

function staffLoadStates(countryId, preselectState, preselectCity) {
    if (!staffStateEl) return;
    staffStateEl.innerHTML = '<option value="">Loading…</option>';
    staffStateEl.disabled  = true;
    if (staffCityEl) { staffCityEl.innerHTML = '<option value="">SELECT STATE FIRST</option>'; staffCityEl.disabled = true; }
    if (!countryId) {
        staffStateEl.innerHTML = '<option value="">SELECT COUNTRY FIRST</option>';
        return;
    }
    fetch(`<?= base_url('api/admin/state/country/') ?>${countryId}`)
        .then(r => r.json())
        .then(res => {
            staffStateEl.disabled = false;
            if (res.status !== 'success' || !res.data.length) {
                staffStateEl.innerHTML = '<option value="">No states found</option>';
                return;
            }
            staffStateEl.innerHTML = '<option value="">SELECT</option>' +
                res.data.map(s => `<option value="${s.name}" data-id="${s.id}">${s.name}</option>`).join('');
            if (preselectState) {
                for (const opt of staffStateEl.options) {
                    if (opt.value.toUpperCase() === preselectState.toUpperCase()) {
                        opt.selected = true;
                        staffLoadCities(opt.dataset.id, preselectCity);
                        break;
                    }
                }
            }
        })
        .catch(() => { staffStateEl.disabled = false; staffStateEl.innerHTML = '<option value="">Failed to load</option>'; });
}

function staffLoadCities(stateId, preselectCity) {
    if (!staffCityEl) return;
    staffCityEl.innerHTML = '<option value="">Loading…</option>';
    staffCityEl.disabled  = true;
    if (!stateId) {
        staffCityEl.innerHTML = '<option value="">SELECT STATE FIRST</option>';
        return;
    }
    fetch(`<?= base_url('api/admin/city/state/') ?>${stateId}`)
        .then(r => r.json())
        .then(res => {
            staffCityEl.disabled = false;
            if (res.status !== 'success' || !res.data.length) {
                staffCityEl.innerHTML = '<option value="">No cities found</option>';
                return;
            }
            staffCityEl.innerHTML = '<option value="">SELECT</option>' +
                res.data.map(c => `<option value="${c.name}">${c.name}</option>`).join('');
            if (preselectCity) {
                for (const opt of staffCityEl.options) {
                    if (opt.value.toUpperCase() === preselectCity.toUpperCase()) {
                        opt.selected = true;
                        break;
                    }
                }
            }
        })
        .catch(() => { staffCityEl.disabled = false; staffCityEl.innerHTML = '<option value="">Failed to load</option>'; });
}

staffCountryEl?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    staffLoadStates(opt?.dataset?.id || '');
});

staffStateEl?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    staffLoadCities(opt?.dataset?.id || '');
});

// Trigger on page load for pre-selected country; pass state/city for edit pre-fill
(function() {
    const preselected = staffCountryEl?.querySelector('option[selected]');
    if (preselected?.dataset?.id) {
        staffLoadStates(
            preselected.dataset.id,
            '<?= esc($s['state'] ?? '') ?>',
            '<?= esc($s['city'] ?? '') ?>'
        );
    }
})();

// ── Read MyKad ────────────────────────────────────────────────────────────────

let staffMyKadFile = null;

function formatMyKadFileSize(bytes) {
    if (!bytes || bytes <= 0) return '0 B';
    const units = ['B','KB','MB','GB'];
    const e = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
    return (bytes / Math.pow(1024, e)).toFixed(1) + ' ' + units[e];
}

function setStaffMyKadFile(file) {
    staffMyKadFile = file || null;
    const selected   = document.getElementById('staff-mykad-selected');
    const processBtn = document.getElementById('staff-mykad-process');
    const filename   = document.getElementById('staff-mykad-filename');
    const filesize   = document.getElementById('staff-mykad-filesize');
    if (!selected || !processBtn || !filename || !filesize) return;
    if (staffMyKadFile) {
        filename.textContent = staffMyKadFile.name || 'Selected file';
        filesize.textContent = formatMyKadFileSize(staffMyKadFile.size || 0);
        selected.classList.remove('hidden');
        processBtn.disabled = false;
    } else {
        selected.classList.add('hidden');
        processBtn.disabled = true;
    }
}

function validateStaffMyKadFile(file) {
    if (!file) return false;
    if (!/^image\//.test(file.type || '')) {
        alert('Please select an image file (JPG or PNG).');
        return false;
    }
    if ((file.size || 0) > 5 * 1024 * 1024) {
        alert('File is too large. Maximum size is 5MB.');
        return false;
    }
    return true;
}

function openStaffMyKadModal() {
    const modal = document.getElementById('staff-mykad-modal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
}

function closeStaffMyKadModal() {
    const modal     = document.getElementById('staff-mykad-modal');
    const fileInput = document.getElementById('staff-mykad-file');
    if (modal) { modal.classList.add('hidden'); modal.setAttribute('aria-hidden', 'true'); }
    if (fileInput) fileInput.value = '';
    setStaffMyKadFile(null);
}

document.getElementById('staff-mykad-btn')?.addEventListener('click', openStaffMyKadModal);
document.getElementById('staff-mykad-close')?.addEventListener('click', closeStaffMyKadModal);
document.getElementById('staff-mykad-cancel')?.addEventListener('click', closeStaffMyKadModal);
document.getElementById('staff-mykad-backdrop')?.addEventListener('click', closeStaffMyKadModal);

document.getElementById('staff-mykad-browse')?.addEventListener('click', function() {
    document.getElementById('staff-mykad-file')?.click();
});

document.getElementById('staff-mykad-file')?.addEventListener('change', function(e) {
    const file = e.target?.files?.[0];
    if (!file) { setStaffMyKadFile(null); return; }
    if (!validateStaffMyKadFile(file)) { this.value = ''; setStaffMyKadFile(null); return; }
    setStaffMyKadFile(file);
});

document.getElementById('staff-mykad-clear')?.addEventListener('click', function() {
    const fileInput = document.getElementById('staff-mykad-file');
    if (fileInput) fileInput.value = '';
    setStaffMyKadFile(null);
});

const staffMykadDropzone = document.getElementById('staff-mykad-dropzone');
staffMykadDropzone?.addEventListener('dragover',  function(e) { e.preventDefault(); e.stopPropagation(); this.classList.add('border-primary'); });
staffMykadDropzone?.addEventListener('dragleave', function(e) { e.preventDefault(); e.stopPropagation(); this.classList.remove('border-primary'); });
staffMykadDropzone?.addEventListener('drop', function(e) {
    e.preventDefault(); e.stopPropagation();
    this.classList.remove('border-primary');
    const file = e.dataTransfer?.files?.[0];
    if (!file) return;
    if (!validateStaffMyKadFile(file)) { setStaffMyKadFile(null); return; }
    setStaffMyKadFile(file);
});

document.getElementById('staff-mykad-process')?.addEventListener('click', async function() {
    if (!staffMyKadFile) { alert('Please select an image first.'); return; }
    const processBtn = this;
    const originalText = processBtn.textContent;
    processBtn.disabled = true;
    processBtn.textContent = 'Processing…';
    try {
        const fd = new FormData();
        fd.append('mykad_image', staffMyKadFile);
        const response = await fetch('<?= base_url('visitor-registration/processMyKad') ?>', {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const result = await response.json();
        if (!result?.success || !result?.data) {
            alert(result?.message || 'Could not read MyKad image.');
            return;
        }
        const d = result.data;
        const setVal = (name, val) => {
            if (!val) return;
            const el = document.querySelector(`[name="${name}"]`);
            if (!el) return;
            if (el.tagName === 'SELECT') {
                const upper = String(val).toUpperCase();
                for (const opt of el.options) {
                    if (opt.value.toUpperCase() === upper || opt.text.toUpperCase() === upper) {
                        el.value = opt.value;
                        break;
                    }
                }
            } else {
                el.value = String(val);
            }
        };
        setVal('ic_number',        d.ic_number);
        setVal('full_name',        d.name);
        setVal('name_on_staff_pass', d.name);
        setVal('date_of_birth',    d.date_of_birth);
        setVal('sex',              d.sex);
        setVal('address_1',   d.address);
        setVal('postal_code', d.postcode);
        // State/city are cascade-driven — trigger load from the currently selected country
        if (d.state) {
            const cOpt = staffCountryEl?.options[staffCountryEl?.selectedIndex];
            if (cOpt?.dataset?.id) staffLoadStates(cOpt.dataset.id, d.state, d.city);
        }
        closeStaffMyKadModal();
        alert('MyKad processed. Please verify and complete any missing fields before submitting.');
    } catch (err) {
        alert('Could not read MyKad image. Please try again with a clearer image.');
    } finally {
        processBtn.disabled = false;
        processBtn.textContent = originalText;
    }
});

</script>
</html>
