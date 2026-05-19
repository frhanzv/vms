<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Staff Pass Request - Detail</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                        "border-color": "#e7edf3",
                    },
                    fontFamily: {
                        display: ["Montserrat", "sans-serif"],
                        sans: ["Montserrat", "sans-serif"],
                        brand: ["Montserrat", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.375rem",
                    },
                },
            },
        };
    </script>
    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cfdbe7; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #4c739a; }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased min-h-screen">
    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8">
        <div class="max-w-[960px] mx-auto">

            <!-- Page Header -->
            <div class="mb-8 space-y-2">
                <div class="flex items-center gap-3">
                    <button onclick="window.history.back()" class="text-text-sub dark:text-gray-400 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </button>
                    <h1 class="text-3xl sm:text-4xl font-black text-text-main dark:text-white font-brand tracking-tight">Staff Pass Request</h1>
                </div>
                <p class="text-sm text-text-sub dark:text-gray-400 font-brand pl-9">
                    Application No: <span class="font-semibold text-text-main dark:text-white"><?= esc($staff['app_no']) ?></span>
                </p>
            </div>

            <div class="space-y-8">

                <!-- Application Information -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">business</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Application Info</h2>
                        </div>
                    </div>
                    <div class="space-y-6">

                        <!-- Row 1 -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Application Number</label>
                                <input value="<?= esc($staff['app_no']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date Of Application</label>
                                <input value="<?= esc($staff['date_of_application']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Type Of Application</label>
                                <input value="<?= esc($staff['type_of_application']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Designation</label>
                                <input value="<?= esc($staff['designation']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Resident</label>
                                <input value="<?= esc($staff['resident']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Sub Type</label>
                                <input value="<?= esc($staff['sub_type']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2 sm:col-span-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Remark</label>
                                <input value="<?= esc($staff['remark']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                        </div>

                        <!-- Location Access -->
                        <?php
                        $locationGroups = [
                            'Changing Rooms' => [
                                ['label' => 'Changing Room 1',                   'in' => 'CHANGING ROOM 1 IN',                    'out' => 'CHANGING ROOM 1 OUT'],
                                ['label' => 'Changing Room 2',                   'in' => 'CHANGING ROOM 2 IN',                    'out' => 'CHANGING ROOM 2 OUT'],
                            ],
                            'Production' => [
                                ['label' => 'Production 1 - Clean Room 10K',     'in' => 'PRODUCTION 1 - CLEAN ROOM 10K IN',      'out' => 'PRODUCTION 1 - CLEAN ROOM 10K OUT'],
                                ['label' => 'Production 2 - Clean Room 1K',      'in' => 'PRODUCTION 2 - CLEAN ROOM 1K IN',       'out' => 'PRODUCTION 2 - CLEAN ROOM 1K OUT'],
                                ['label' => 'Production 3',                       'in' => 'PRODUCTION 3 IN',                       'out' => 'PRODUCTION 3 OUT'],
                                ['label' => 'Production 4',                       'in' => 'PRODUCTION 4 IN',                       'out' => 'PRODUCTION 4 OUT'],
                                ['label' => 'Production 5 - Work In Progress',    'in' => 'PRODUCTION 5 - WORK IN PROGRESS IN',    'out' => 'PRODUCTION 5 - WORN IN PROGRESS OUT'],
                                ['label' => 'Production Office',                  'in' => 'PRODUCTION OFFICE IN',                  'out' => 'PRODUCTION OFFICE OUT'],
                                ['label' => 'Production WIP',                     'in' => 'PRODUCTION WIP IN',                     'out' => 'PRODUCTION WIP OUT'],
                            ],
                            'Rooms' => [
                                ['label' => 'CMM Room',                           'in' => 'CMM ROOM IN',                           'out' => 'CMM ROOM OUT'],
                                ['label' => 'Jitter Bug Room',                    'in' => 'JITTER BUG ROOM IN',                    'out' => 'JITTER BUG ROOM OUT'],
                                ['label' => 'Polishing Room',                     'in' => 'POLISHING ROOM IN',                     'out' => 'POLISHING ROOM OUT'],
                                ['label' => 'Polishing/Deburing Room',            'in' => 'POLISHING/DEBURING ROOM IN',            'out' => 'POLISHING/DEBURING ROOM OUT'],
                                ['label' => 'QA Room',                            'in' => 'QA ROOM IN',                            'out' => 'QA ROOM OUT'],
                                ['label' => 'Robotic Jitter Bug Room',            'in' => 'ROBOTIC JITTER BUG ROOM IN',            'out' => 'ROBOTIC JITTER BUG ROOM OUT'],
                                ['label' => 'Robotic Welding Room',               'in' => 'ROBOTIC WELDING ROOM IN',               'out' => 'ROBOTIC WELDING ROOM OUT'],
                                ['label' => 'Tools Room',                         'in' => 'TOOLS ROOM IN',                         'out' => 'TOOLS ROOM OUT'],
                                ['label' => 'Ultra Sonic Room',                   'in' => 'ULTRA SONIC ROOM IN',                   'out' => 'ULTRA SONIC ROOM OUT'],
                            ],
                            'Areas & Others' => [
                                ['label' => 'Chemical Waste',                     'in' => 'CHEMICAL WASTE IN',                     'out' => 'CHEMICAL WASTE OUT'],
                                ['label' => 'Finished Good Area 1',               'in' => 'FINISHED GOODS AREA 1 IN',              'out' => 'FINISHED GOOD AREA 1 OUT'],
                                ['label' => 'Finished Good Area 2',               'in' => 'FINISHED GOOD AREA 2 IN',               'out' => 'FINISHED GOOD AREA 2 OUT'],
                                ['label' => 'Maintenance Department',             'in' => 'MAINTENANCE DEPARTMENT IN',             'out' => 'MAINTENANCE DEPARTMENT OUT'],
                                ['label' => 'Packaging Area',                     'in' => 'PACKAGING AREA IN',                     'out' => 'PACKAGING AREA OUT'],
                                ['label' => 'Raw Material Area',                  'in' => 'RAW MATERIAL AREA IN',                  'out' => 'RAW MATERIAL OUT'],
                                ['label' => 'Schedule Waste',                     'in' => 'SCHEDULE WASTE IN',                     'out' => 'SCHEDULE WASTE OUT'],
                                ['label' => 'Toilet',                             'in' => 'TOILET IN',                             'out' => 'TOILET OUT'],
                                ['label' => 'Utility',                            'in' => 'UTILITY IN',                            'out' => 'UTILITY OUT'],
                                ['label' => 'Water Treatment Area',               'in' => 'WATER TREATMENT AREA IN',               'out' => 'WATER TREATMENT AREA OUT'],
                            ],
                        ];

                        $savedLocations = [];
                        if (!empty($staff['location_access'])) {
                            $decoded = json_decode($staff['location_access'], true);
                            $savedLocations = is_array($decoded) ? $decoded : explode(',', $staff['location_access']);
                            $savedLocations = array_map('trim', $savedLocations);
                        }
                        ?>
                        <div class="space-y-3" x-data="{ search: '' }">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Location Access</label>
                            </div>

                            <!-- Search -->
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-2.5 text-text-sub text-[20px] pointer-events-none">search</span>
                                <input x-model="search" type="text" placeholder="Search locations..." class="w-full h-10 rounded-lg border border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white pl-10 pr-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand text-sm"/>
                            </div>

                            <!-- Column header -->
                            <div class="flex items-center justify-end pr-4 gap-4">
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 font-brand w-8 text-center">IN</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-rose-500 dark:text-rose-400 font-brand w-8 text-center">OUT</span>
                            </div>

                            <div class="rounded-xl border border-border-color dark:border-gray-700 overflow-hidden divide-y divide-border-color dark:divide-gray-700">
                                <?php $groupIndex = 0; foreach ($locationGroups as $groupName => $locations): $groupId = 'grp-' . $groupIndex++; ?>
                                <div>
                                    <!-- Group header -->
                                    <div class="bg-gray-50 dark:bg-gray-800/60 px-4 py-2.5">
                                        <span class="text-xs font-bold uppercase tracking-wider text-text-sub dark:text-gray-400 font-brand"><?= $groupName ?></span>
                                    </div>
                                    <!-- Location rows -->
                                    <?php foreach ($locations as $loc):
                                        $inChecked  = in_array($loc['in'],  $savedLocations);
                                        $outChecked = in_array($loc['out'], $savedLocations);
                                    ?>
                                    <div x-show="search === '' || '<?= strtolower($loc['label']) ?>'.includes(search.toLowerCase())"
                                         class="flex items-center justify-between px-4 py-3 transition-colors border-t border-border-color dark:border-gray-700
                                         <?= ($inChecked || $outChecked) ? 'bg-blue-50/50 dark:bg-blue-900/10' : 'hover:bg-gray-50 dark:hover:bg-gray-800/40' ?>">
                                        <span class="text-sm font-medium font-brand flex-1 pr-4 <?= ($inChecked || $outChecked) ? 'text-primary dark:text-blue-400' : 'text-text-main dark:text-gray-100' ?>"><?= $loc['label'] ?></span>
                                        <div class="flex items-center gap-4 flex-shrink-0">
                                            <div class="flex items-center justify-center w-8">
                                                <input value="<?= $loc['in'] ?>" class="form-checkbox rounded text-primary border-2 border-gray-300 dark:border-gray-600 h-5 w-5 cursor-not-allowed opacity-80" type="checkbox" <?= $inChecked ? 'checked' : '' ?> disabled/>
                                            </div>
                                            <div class="flex items-center justify-center w-8">
                                                <input value="<?= $loc['out'] ?>" class="form-checkbox rounded text-primary border-2 border-gray-300 dark:border-gray-600 h-5 w-5 cursor-not-allowed opacity-80" type="checkbox" <?= $outChecked ? 'checked' : '' ?> disabled/>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- Person -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600 dark:text-green-400">
                            <span class="material-symbols-outlined">person</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Person</h2>
                        </div>
                    </div>
                    <div class="space-y-6">

                        <!-- Row 1: IC, DOB -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">IC Number</label>
                                <input value="<?= esc($staff['ic_passport']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Birthday</label>
                                <input value="<?= esc($staff['date_of_birth']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                        </div>

                        <!-- Row 2: Sex, Full Name, Name On Staff Pass -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Sex</label>
                                <input value="<?= esc($staff['sex']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Full Name</label>
                                <input value="<?= esc($staff['full_name']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Name On Staff Pass</label>
                                <input value="<?= esc($staff['name_on_staff_pass']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                        </div>

                        <!-- Row 3: Staff No, Contact, Email, Department -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Staff No</label>
                                <input value="<?= esc($staff['staff_no']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Contact Number</label>
                                <input value="<?= esc($staff['contact_number']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Email</label>
                                <input value="<?= esc($staff['email']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Department</label>
                                <input value="<?= esc($staff['department']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                        </div>

                        <!-- Row 4: Address -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 1</label>
                                <input value="<?= esc($staff['address_1']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 2</label>
                                <input value="<?= esc($staff['address_2']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 3</label>
                                <input value="<?= esc($staff['address_3']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                        </div>

                        <!-- Row 5: Country, State, City, Postal Code -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Country</label>
                                <input value="<?= esc($staff['country']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">State</label>
                                <input value="<?= esc($staff['state']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">City</label>
                                <input value="<?= esc($staff['city']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Postal Code</label>
                                <input value="<?= esc($staff['postal_code']) ?>" class="w-full h-12 rounded-lg border border-border-color dark:border-gray-700 bg-gray-100 dark:bg-background-dark text-text-main dark:text-white px-4 outline-none font-brand" type="text" readonly/>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- Document Upload -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <span class="material-symbols-outlined">folder_open</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Document Upload</h2>
                            <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Uploaded identity documents.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-3">
                            <p class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">IC / MyKad</p>
                            <?php if (!empty($staff['government_id'])): ?>
                                <a href="<?= base_url('uploads/' . $staff['government_id']) ?>" target="_blank"
                                   class="flex items-center gap-3 p-4 rounded-xl border border-border-color dark:border-gray-700 hover:border-primary/40 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all">
                                    <span class="material-symbols-outlined text-3xl text-indigo-500">id_card</span>
                                    <div>
                                        <p class="text-sm font-semibold text-text-main dark:text-white font-brand"><?= esc(basename($staff['government_id'])) ?></p>
                                        <p class="text-xs text-primary font-brand">Click to view</p>
                                    </div>
                                </a>
                            <?php else: ?>
                                <div class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl">
                                    <span class="material-symbols-outlined text-3xl text-gray-300 dark:text-gray-600 mb-1">id_card</span>
                                    <p class="text-xs text-text-sub dark:text-gray-500 font-brand">No file uploaded</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-col gap-3">
                            <p class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Other Documents</p>
                            <?php if (!empty($staff['other_doc'])): ?>
                                <a href="<?= base_url('uploads/' . $staff['other_doc']) ?>" target="_blank"
                                   class="flex items-center gap-3 p-4 rounded-xl border border-border-color dark:border-gray-700 hover:border-primary/40 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all">
                                    <span class="material-symbols-outlined text-3xl text-indigo-500">upload_file</span>
                                    <div>
                                        <p class="text-sm font-semibold text-text-main dark:text-white font-brand"><?= esc(basename($staff['other_doc'])) ?></p>
                                        <p class="text-xs text-primary font-brand">Click to view</p>
                                    </div>
                                </a>
                            <?php else: ?>
                                <div class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl">
                                    <span class="material-symbols-outlined text-3xl text-gray-300 dark:text-gray-600 mb-1">upload_file</span>
                                    <p class="text-xs text-text-sub dark:text-gray-500 font-brand">No file uploaded</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 py-6 border-t border-border-color dark:border-gray-800">
                    <button type="button" onclick="window.history.back()" class="px-6 py-3 rounded-lg border border-border-color dark:border-gray-700 text-text-main dark:text-gray-300 font-bold hover:bg-background-light dark:hover:bg-gray-800 transition-all font-brand">
                        Back
                    </button>
                </div>

            </div>
        </div>
    </main>
</body>
</html>