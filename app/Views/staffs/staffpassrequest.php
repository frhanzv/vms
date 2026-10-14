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
        /* Hide native calendar picker icon */
        input[type="datetime-local"]::-webkit-calendar-picker-indicator,
        input[type="date"]::-webkit-calendar-picker-indicator {
            display: none;
            -webkit-appearance: none;
            opacity: 0;
            pointer-events: none;
        }
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

                <form action="<?= base_url($formAction ?? 'staffs/staffpassrequest/store') ?>" method="post" enctype="multipart/form-data" class="space-y-8">
                    <?= csrf_field() ?>
                    <?php if (session()->getFlashdata('error')): ?>
                    <div class="flex items-start gap-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 text-sm rounded-lg px-4 py-3">
                        <span class="material-symbols-outlined text-[20px]">error</span>
                        <span><?= esc(session()->getFlashdata('error')) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (($staff['status'] ?? '') === 'Rejected' && ! empty($staff['reject_reason'])): ?>
                    <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-lg px-4 py-3">
                        This request was rejected: <strong><?= esc($staff['reject_reason']) ?></strong>. Fix it and submit again to send it back for approval.
                    </div>
                    <?php endif; ?>
                    <?php
                        $isRenew = ! empty($isRenew);
                        $lock    = $isRenew ? 'readonly tabindex="-1" style="background:#f1f5f9"' : '';
                        $s    = $staff ?? [];
                        $v    = fn($f, $db = null) => esc(old($f, $s[$db ?? $f] ?? ''));
                        $sel  = fn($f, $val) => ($s[$f] ?? '') === $val ? 'selected' : '';
                        // Config > Dynamic Form Fields > Staff Pass Request > "Mandatory": red * and the browser's required check.
                        $required = $required ?? [];
                        $req      = fn(string $k) => ($required[$k] ?? false) ? ' <span class="text-red-500">*</span>' : '';
                        $reqAttr  = fn(string $k) => ($required[$k] ?? false) ? 'required' : '';
                    ?>

                    <!-- Application Information -->
                    <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                            <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">business</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Application Info</h2>
                            </div>
                        </div>
                        <?php $fs = $fieldSettings ?? []; ?>
                        <div class="space-y-6">

                            <!-- Row 1: Date of Application, Type of Application, Designation, Resident -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date Of Application</label>
                                    <input name="date_of_application" value="<?= isset($isEdit) && ! $isRenew ? esc($s['date_of_application'] ?? '') : date('d/m/Y') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                                </div>
                                <?php if ($fs['type_of_application'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Type Of Application<?= $req('type_of_application') ?></label>
                                    <?php if ($isRenew): ?>
                                    <input <?= $reqAttr('type_of_application') ?> type="hidden" name="type_of_application" value="RENEWAL"/>
                                    <input value="RENEWAL" class="w-full h-12 rounded-lg border-border-color bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" readonly/>
                                    <?php else: ?>
                                    <select name="type_of_application" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                        <option value="NEW" <?= $sel('type_of_application','NEW') ?>>NEW</option>
                                        <option value="RENEWAL" <?= $sel('type_of_application','RENEWAL') ?>>RENEWAL</option>
                                        <option value="REPLACEMENT" <?= $sel('type_of_application','REPLACEMENT') ?>>REPLACEMENT</option>
                                    </select>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['designation'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Designation<?= $req('designation') ?></label>
                                    <select <?= $reqAttr('designation') ?> name="designation" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                        <option value="">SELECT</option>
                                        <option value="PEN. LORI" <?= $sel('designation','PEN. LORI') ?>>PEN. LORI</option>
                                        <option value="OPERATOR" <?= $sel('designation','OPERATOR') ?>>OPERATOR</option>
                                        <option value="SUPERVISOR" <?= $sel('designation','SUPERVISOR') ?>>SUPERVISOR</option>
                                        <option value="ENGINEER" <?= $sel('designation','ENGINEER') ?>>ENGINEER</option>
                                        <option value="MANAGER" <?= $sel('designation','MANAGER') ?>>MANAGER</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['resident'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Resident<?= $req('resident') ?></label>
                                    <select <?= $reqAttr('resident') ?> name="resident" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                        <option value="LOCAL" <?= $sel('resident','LOCAL') ?>>LOCAL</option>
                                        <option value="FOREIGN" <?= $sel('resident','FOREIGN') ?>>FOREIGN</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Row 2: Sub Type -->
                            <?php if ($fs['sub_type'] ?? true): ?>
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Sub Type<?= $req('sub_type') ?></label>
                                    <select <?= $reqAttr('sub_type') ?> name="sub_type" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                        <option value="PERMANENT" <?= $sel('sub_type','PERMANENT') ?>>PERMANENT</option>
                                        <option value="TEMPORARY" <?= $sel('sub_type','TEMPORARY') ?>>TEMPORARY</option>
                                        <option value="CONTRACT" <?= $sel('sub_type','CONTRACT') ?>>CONTRACT</option>
                                        <option value="FAMILY PASS" <?= $sel('sub_type','FAMILY PASS') ?>>FAMILY PASS</option>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Location Access — same dropdown as the Contractor/Vendor Request -->
                            <?php if ($fs['location_access'] ?? true): ?>
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
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Location Access<?= $req('location_access') ?></label>
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
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Location Access<?= $req('location_access') ?></label>

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

                    <!-- Date of Visit -->
                    

                    <!-- Details of Visit -->
                    
                    <!-- Staff Details -->
                    <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600 dark:text-green-400">
                                    <span class="material-symbols-outlined">person</span>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Staff Details</h2>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-6">

                            <!-- Read MyKad Button -->
                            <?php if (($mykadOcrEnabled ?? true) && ! $isRenew): ?>
                            <div>
                                <button type="button" id="staff-mykad-btn" class="bg-success hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold uppercase shadow transition-all font-brand flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base">credit_card</span>
                                    Read MyKad
                                </button>
                            </div>
                            <?php endif; ?>

                            <!-- Row 1: IC Number, Date of Birth -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <?php if ($fs['ic_number'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">IC / Passport Number<?= $req('ic_number') ?></label>
                                    <input <?= $reqAttr('ic_number') ?> name="ic_number" value="<?= $v('ic_number', 'ic_passport') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="Enter IC / Passport Number" type="text" <?= $lock ?>/>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['date_of_birth'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date Of Birth<?= $req('date_of_birth') ?></label>
                                    <input <?= $reqAttr('date_of_birth') ?> name="date_of_birth" value="<?= $v('date_of_birth') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="DD/MM/YYYY" type="date" <?= $lock ?>/>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Row 2: Sex, Full Name, Name On Staff Pass -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <?php if ($fs['sex'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Sex<?= $req('sex') ?></label>
                                    <select <?= $reqAttr('sex') ?> name="sex" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                        <option value="MALE" <?= $sel('sex','MALE') ?>>MALE</option>
                                        <option value="FEMALE" <?= $sel('sex','FEMALE') ?>>FEMALE</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['full_name'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Full Name<?= $req('full_name') ?></label>
                                    <input <?= $reqAttr('full_name') ?> name="full_name" value="<?= $v('full_name') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="Full name as per ID" type="text" <?= $lock ?>/>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['name_on_staff_pass'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Name On Staff Pass<?= $req('name_on_staff_pass') ?></label>
                                    <input <?= $reqAttr('name_on_staff_pass') ?> name="name_on_staff_pass" value="<?= $v('name_on_staff_pass') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Row 3: Staff No, Contact Number, Email Address -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <?php if ($fs['staff_no'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Staff No<?= $req('staff_no') ?></label>
                                    <input <?= $reqAttr('staff_no') ?> name="staff_no" value="<?= $v('staff_no') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" <?= $lock ?>/>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['contact_number'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Contact Number<?= $req('contact_number') ?></label>
                                    <input <?= $reqAttr('contact_number') ?> name="contact_number" value="<?= $v('contact_number') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="+60 1x-xxx xxxx" type="tel"/>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['email'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Email Address<?= $req('email') ?></label>
                                    <input <?= $reqAttr('email') ?> name="email" value="<?= $v('email') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="staff@example.com" type="email"/>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Row 4: Department -->
                            <?php if ($fs['department'] ?? true): ?>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Department<?= $req('department') ?></label>
                                    <select <?= $reqAttr('department') ?> name="department" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                        <option value="">SELECT</option>
                                        <?php $deps = $departments ?? []; if (! empty($s['department']) && ! in_array($s['department'], $deps, true)) { $deps[] = $s['department']; } ?>
                                        <?php foreach ($deps as $dep): ?>
                                        <option value="<?= esc($dep, 'attr') ?>" <?= (old('department', $s['department'] ?? '')) === $dep ? 'selected' : '' ?>><?= esc($dep) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Row 5: Address 1, Address 2 -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <?php if ($fs['address_1'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 1<?= $req('address_1') ?></label>
                                    <input <?= $reqAttr('address_1') ?> name="address_1" value="<?= $v('address_1') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['address_2'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 2<?= $req('address_2') ?></label>
                                    <input <?= $reqAttr('address_2') ?> name="address_2" value="<?= $v('address_2') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Row 6: Address 3 -->
                            <?php if ($fs['address_3'] ?? true): ?>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 3<?= $req('address_3') ?></label>
                                <input <?= $reqAttr('address_3') ?> name="address_3" value="<?= $v('address_3') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                            </div>
                            <?php endif; ?>

                            <!-- Row 7: City, State -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <?php if ($fs['city'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">City<?= $req('city') ?></label>
                                    <select <?= $reqAttr('city') ?> name="city" id="staff-city" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" disabled>
                                        <option value="">SELECT STATE FIRST</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['state'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">State<?= $req('state') ?></label>
                                    <select <?= $reqAttr('state') ?> name="state" id="staff-state" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" disabled>
                                        <option value="">SELECT COUNTRY FIRST</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Row 8: Postal Code, Country -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <?php if ($fs['postal_code'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Postal Code<?= $req('postal_code') ?></label>
                                    <input <?= $reqAttr('postal_code') ?> name="postal_code" value="<?= $v('postal_code') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                                </div>
                                <?php endif; ?>
                                <?php if ($fs['country'] ?? true): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Country<?= $req('country') ?></label>
                                    <select <?= $reqAttr('country') ?> name="country" id="staff-country" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                        <option value="">SELECT</option>
                                        <?php
                                            $savedCountry = $s['country'] ?? '';
                                            $defaultCountry = $savedCountry ?: 'MALAYSIA';
                                        ?>
                                        <?php foreach ($countries ?? [] as $c): ?>
                                        <option value="<?= esc($c['name']) ?>" data-id="<?= (int) $c['id'] ?>"
                                            <?= strtoupper($c['name']) === strtoupper($defaultCountry) ? 'selected' : '' ?>>
                                            <?= esc($c['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </section>

                    <!-- Approval, Photo & Remark (KPK staff form: branch office, photo, remark) -->
                    <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                            <div class="size-10 rounded-full bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600">
                                <span class="material-symbols-outlined">verified_user</span>
                            </div>
                            <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Approval &amp; Photo</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <?php if ($fs['access_branch'] ?? true): ?>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Approving Branch</label>
                                <select name="access_branch" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand">
                                    <?php foreach (['' => 'Any approver', 'KSB' => 'KSB', 'KPK' => 'KPK', 'BOTH' => 'Both (KSB + KPK)'] as $bv => $bl): ?>
                                    <option value="<?= $bv ?>" <?= old('access_branch', $s['access_branch'] ?? '') === $bv ? 'selected' : '' ?>><?= $bl ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Visa / Work Permit Expiry <span class="text-xs text-text-sub">(foreigner)</span></label>
                                <input name="visa_expiry" type="date" value="<?= $v('visa_expiry') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand"/>
                            </div>
                            <?php if ($fs['photo_upload'] ?? true): ?>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Passport Photo <span class="text-xs text-text-sub">(needed before the card is printed)</span></label>
                                <?php if (! empty($s['photo'])): ?>
                                <img src="<?= base_url('uploads/staff_photos/' . esc($s['photo'], 'url')) ?>" alt="" class="w-16 h-20 object-cover rounded border mb-2"/>
                                <?php endif; ?>
                                <input name="photo" type="file" accept="image/png,image/jpeg" class="block w-full text-sm"/>
                            </div>
                            <?php endif; ?>
                            <?php if ($fs['remark'] ?? true): ?>
                            <div class="space-y-2 md:col-span-3">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Remark</label>
                                <textarea name="remark" rows="2" class="w-full rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 py-2 outline-none font-brand"><?= $v('remark') ?></textarea>
                            </div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <!-- Driving License Section -->
                    <?php if ($fs['driving_license'] ?? true): ?>
                    <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 mt-8">
                        <div class="p-6 sm:p-8">
                            <div class="flex items-center justify-between mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400">
                                        <span class="material-symbols-outlined">cards_stack</span>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Driving License</h2>
                                        <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Optional driving license information.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                <button type="button" onclick="addLicense()" class="text-green-600 hover:text-green-700 transition-colors p-1.5 rounded-full hover:bg-green-50 dark:hover:bg-green-900/20" title="Add License">
                                    <span class="material-symbols-outlined text-2xl">add_circle</span>
                                </button>
                                <button type="button" onclick="removeLicense()" class="text-red-600 hover:text-red-700 transition-colors p-1.5 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove License">
                                    <span class="material-symbols-outlined text-2xl">remove_circle</span>
                                </button>
                                </div>
                            </div>
                            <?php if (! empty($licenses)): ?>
                            <div class="mb-4 text-sm">
                                <p class="font-medium text-text-main dark:text-gray-200 mb-2"><?= $isRenew ? 'Current licenses (replaced if you add new ones below)' : 'Saved licenses' ?></p>
                                <ul class="space-y-1">
                                    <?php foreach ($licenses as $lic): ?>
                                    <li class="text-text-sub dark:text-gray-400">Class <strong><?= esc($lic['license_class'] ?? '-') ?></strong> — expires <?= ! empty($lic['license_expiry']) ? esc(date('d/m/Y', strtotime($lic['license_expiry']))) : '-' ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                            <div id="licenseContainer" class="flex flex-col gap-4">
                                <div class="text-center py-8 text-text-sub dark:text-gray-400">
                                    <span class="material-symbols-outlined text-5xl mb-3 block text-gray-300 dark:text-gray-600">badge</span>
                                    <p class="text-sm">No licenses added yet. Click <span class="text-primary font-semibold">+</span> to add driving license.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php endif; ?>

                    <!-- CSP Details -->
                    <?php if ($fs['csp_number'] ?? false): ?>
                    <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                            <div class="size-10 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400">
                                <span class="material-symbols-outlined">apartment</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">CSP</h2>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-text-main dark:text-gray-200 font-brand">CSP Number<?= $req('csp_number') ?></label>
                                <input <?= $reqAttr('csp_number') ?> name="company_reg_id" value="<?= $v('company_reg_id', 'csp_number') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-text-main dark:text-gray-200 font-brand">Expiry Date</label>
                                <div class="relative">
                                    <input name="csp_expiry_date" value="<?= $v('csp_expiry_date') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="DD/MM/YYYY" type="date"/>
                                    <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub cursor-pointer" onclick="this.previousElementSibling.showPicker()">calendar_month</span>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php endif; ?>

                    <!-- E-Vetting -->
                    <?php if ($fs['evetting'] ?? false): ?>
                    <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                            <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <span class="material-symbols-outlined">verified_user</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">E-Vetting</h2>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">
                                    Date of Application <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        name="evetting_date_of_application"
                                        value="<?= $v('evetting_date_of_application') ?>"
                                        class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand"
                                        placeholder="DD/MM/YYYY"
                                        type="date"
                                    />
                                    <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub cursor-pointer" onclick="this.previousElementSibling.showPicker()">calendar_month</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">
                                    Date of Result
                                </label>
                                <div class="relative">
                                    <input
                                        name="evetting_date_of_result"
                                        value="<?= $v('evetting_date_of_result') ?>"
                                        class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand"
                                        placeholder="DD/MM/YYYY"
                                        type="date"
                                    />
                                    <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub cursor-pointer" onclick="this.previousElementSibling.showPicker()">calendar_month</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">
                                    Result
                                </label>
                                <select
                                    name="evetting_result"
                                    class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand appearance-none cursor-pointer"
                                >
                                    <option value="In Progress" <?= ($s['evetting_result'] ?? 'In Progress') === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="Pass" <?= ($s['evetting_result'] ?? '') === 'Pass' ? 'selected' : '' ?>>Pass</option>
                                    <option value="Fail" <?= ($s['evetting_result'] ?? '') === 'Fail' ? 'selected' : '' ?>>Fail</option>
                                </select>
                            </div>
                        </div>
                    </section>
                    <?php endif; ?>
                    
                    
                    <!-- Document Upload -->
                    <?php if ($fs['document_upload'] ?? true): ?>
                    <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-md border border-border-color dark:border-gray-800 p-6 sm:p-8 mt-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                            <div class="size-10 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <span class="material-symbols-outlined">folder_open</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold font-brand text-text-main dark:text-white">Document Upload</h2>
                                <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Required for identity verification.</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-3">
                                <p class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">IC / MyKad <span class="text-red-500">*</span></p>
                                <div class="group relative flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                                    <input name="government_id" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" type="file"/>
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                        <span class="material-symbols-outlined text-4xl text-gray-400 group-hover:text-primary mb-3 transition-colors">id_card</span>
                                        <p class="mb-1 text-sm text-text-main dark:text-gray-300 font-brand"><span class="font-semibold text-primary">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-text-sub dark:text-gray-500 font-brand">SVG, PNG, JPG or PDF (MAX. 5MB)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col gap-3">
                                <p class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Other Documents (Optional)</p>
                                <div class="group relative flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                                    <input name="invitation_letter[]" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" type="file" multiple/>
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                        <span class="material-symbols-outlined text-4xl text-gray-400 group-hover:text-primary mb-3 transition-colors">upload_file</span>
                                        <p class="mb-1 text-sm text-text-main dark:text-gray-300 font-brand"><span class="font-semibold text-primary">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-text-sub dark:text-gray-500 font-brand">PDF or Images</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php endif; ?>

                    <!-- Profile Photo -->

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-4 py-6 border-t border-border-color dark:border-gray-800 mt-8">
                        <button type="button" onclick="window.history.back()" class="px-6 py-3 rounded-lg border border-border-color dark:border-gray-700 text-text-main dark:text-gray-300 font-bold hover:bg-background-light dark:hover:bg-gray-800 transition-all font-brand">
                            Cancel
                        </button>
                        <?php if (! $isRenew && in_array($s['status'] ?? 'Draft', ['Draft', ''], true)): ?>
                        <button type="submit" name="save_as_draft" value="1" formnovalidate class="px-6 py-3 rounded-lg border border-primary text-primary font-bold hover:bg-blue-50 dark:hover:bg-gray-800 transition-all font-brand">
                            Save as Draft
                        </button>
                        <?php endif; ?>
                        <button type="submit" class="px-8 py-3 rounded-lg bg-primary text-white font-bold hover:bg-primary-hover shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2 font-brand">
                            <span><?= $isRenew ? 'Submit Renewal' : (isset($isEdit) && ($s['status'] ?? '') !== 'Draft' ? 'Save Changes' : 'Submit Request') ?></span>
                            <span class="material-symbols-outlined text-sm"><?= isset($isEdit) ? 'save' : 'send' ?></span>
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

// License dynamic addition
        let licenseCount = 0;
        function addLicense() {
            const container = document.getElementById('licenseContainer');
            
            // Remove empty state message if it exists
            const emptyState = container.querySelector('.text-center');
            if (emptyState) {
                emptyState.remove();
            }
            
            const items = container.querySelectorAll('.license-item');
            const newIndex = items.length;
            
            const html = `
                <div class="license-item bg-background-light dark:bg-background-dark/50 rounded-lg p-4 border border-border-color dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-text-main dark:text-white font-brand">License ${newIndex + 1}</h4>
                        <button type="button" onclick="removeSpecificLicense(this)" class="text-red-600 hover:text-red-700 transition-colors p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Delete this license">
                            <span class="material-symbols-outlined text-xl">delete</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">License Class</label>
                            <select name="licenses[${licenseCount}][class]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT</option>
                                <option value="B">B</option>
                                <option value="B2">B2</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="DA">DA</option>
                                <option value="E">E</option>
                                <option value="E1">E1</option>
                                <option value="E2">E2</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">License Expiry <span class="text-red-500">*</span></label>
                            <input name="licenses[${licenseCount}][expiry]" placeholder="DD/MM/YYYY" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="date"/>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            licenseCount++;
        }


// Function for removing license
        function removeLicense() {
            const container = document.getElementById('licenseContainer');
            const items = container.querySelectorAll('.license-item');
            if (items.length > 0) {
                items[items.length - 1].remove();
                updateLicenseNumbers();
                
                // Show empty state if no items left
                if (container.querySelectorAll('.license-item').length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-8 text-text-sub dark:text-gray-400">
                            <span class="material-symbols-outlined text-5xl mb-3 block text-gray-300 dark:text-gray-600">badge</span>
                            <p class="text-sm">No licenses added yet. Click <span class="text-primary font-semibold">+</span> to add driving license.</p>
                        </div>
                    `;
                    licenseCount = 0;
                }
            }
        }

        function removeSpecificLicense(button) {
            const container = document.getElementById('licenseContainer');
            const item = button.closest('.license-item');
            item.remove();
            
            updateLicenseNumbers();
            
            // Show empty state if no items left
            const items = container.querySelectorAll('.license-item');
            if (items.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-text-sub dark:text-gray-400">
                        <span class="material-symbols-outlined text-5xl mb-3 block text-gray-300 dark:text-gray-600">badge</span>
                        <p class="text-sm">No licenses added yet. Click <span class="text-primary font-semibold">+</span> to add driving license.</p>
                    </div>
                `;
                licenseCount = 0;
            }
        }

        function updateLicenseNumbers() {
            const container = document.getElementById('licenseContainer');
            const items = container.querySelectorAll('.license-item');
            items.forEach((item, index) => {
                const header = item.querySelector('h4');
                if (header) {
                    header.textContent = `License ${index + 1}`;
                }
            });
        }

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

                    const parent = this.closest('.group');
                    let preview = parent.querySelector('.file-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.className = 'file-preview absolute inset-0 bg-green-50 dark:bg-green-900/20 rounded-xl border-2 border-green-500 flex flex-col items-center justify-center p-4 overflow-y-auto';
                        parent.appendChild(preview);
                    }

                    const fileList = files.map(f =>
                        `<p class="text-xs text-green-700 dark:text-green-400 truncate max-w-full">&#10003; ${f.name}</p>`
                    ).join('');

                    preview.innerHTML = `
                        <div class="text-center w-full">
                            <span class="material-symbols-outlined text-3xl text-green-600 mb-1">check_circle</span>
                            <p class="text-sm font-semibold text-green-700 dark:text-green-400 mb-1">${files.length} file${files.length > 1 ? 's' : ''} selected</p>
                            <div class="text-left px-2 mb-2">${fileList}</div>
                            <button type="button" class="mt-1 text-xs text-red-600 hover:text-red-800" onclick="this.closest('.file-preview').remove(); document.querySelector('input[name=\\'invitation_letter[]\\']').value='';">Clear All</button>
                        </div>
                    `;
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
