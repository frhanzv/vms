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

<body class="bg-background-light dark:bg-background-dark min-h-screen">
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
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Location Access</label>
                            <div class="p-4 rounded-lg border border-border-color dark:border-gray-700">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <?php
                                $locations = [
                                    'CHANGING ROOM 1 IN',           'CHANGING ROOM 1 OUT',          'CHANGING ROOM 2 IN',               'CHANGING ROOM 2 OUT',
                                    'CHEMICAL WASTE IN',             'CHEMICAL WASTE OUT',           'CMM ROOM IN',                      'CMM ROOM OUT',
                                    'FINISHED GOOD AREA 1 OUT',      'FINISHED GOOD AREA 2 IN',      'FINISHED GOOD AREA 2 OUT',         'FINISHED GOODS AREA 1 IN',
                                    'JITTER BUG ROOM IN',            'JITTER BUG ROOM OUT',          'MAINTENANCE DEPARTMENT IN',        'MAINTENANCE DEPARTMENT OUT',
                                    'PACKAGING AREA IN',             'PACKAGING AREA OUT',           'POLISHING ROOM IN',                'POLISHING ROOM OUT',
                                    'POLISHING/DEBURING ROOM IN',    'POLISHING/DEBURING ROOM OUT',  'PRODUCTION 1 - CLEAN ROOM 10K IN','PRODUCTION 1 - CLEAN ROOM 10K OUT',
                                    'PRODUCTION 2 - CLEAN ROOM 1K IN','PRODUCTION 2 - CLEAN ROOM 1K OUT','PRODUCTION 3 IN',             'PRODUCTION 3 OUT',
                                    'PRODUCTION 4 IN',               'PRODUCTION 4 OUT',             'PRODUCTION 5 - WORK IN PROGRESS IN','PRODUCTION 5 - WORN IN PROGRESS OUT',
                                    'PRODUCTION OFFICE IN',          'PRODUCTION OFFICE OUT',        'PRODUCTION WIP IN',                'PRODUCTION WIP OUT',
                                    'QA ROOM IN',                    'QA ROOM OUT',                  'RAW MATERIAL AREA IN',             'RAW MATERIAL OUT',
                                    'ROBOTIC JITTER BUG ROOM IN',    'ROBOTIC JITTER BUG ROOM OUT',  'ROBOTIC WELDING ROOM IN',          'ROBOTIC WELDING ROOM OUT',
                                    'SCHEDULE WASTE IN',             'SCHEDULE WASTE OUT',           'TOILET IN',                        'TOILET OUT',
                                    'TOOLS ROOM IN',                 'TOOLS ROOM OUT',               'ULTRA SONIC ROOM IN',              'ULTRA SONIC ROOM OUT',
                                    'UTILITY IN',                    'UTILITY OUT',                  'WATER TREATMENT AREA IN',          'WATER TREATMENT AREA OUT',
                                ];

                                $savedLocations = [];
                                if (!empty($staff['location_access'])) {
                                    $decoded = json_decode($staff['location_access'], true);
                                    $savedLocations = is_array($decoded) ? $decoded : explode(',', $staff['location_access']);
                                    $savedLocations = array_map('trim', $savedLocations);
                                }

                                foreach ($locations as $location):
                                    $checked = in_array($location, $savedLocations);
                                ?>
                                <label class="flex items-center gap-2 p-3 rounded-lg border <?= $checked ? 'border-primary/30 bg-blue-50 dark:bg-blue-900/10' : 'border-transparent' ?>">
                                    <input value="<?= $location ?>" class="form-checkbox rounded text-primary border-2 border-gray-500 h-5 w-5" type="checkbox" <?= $checked ? 'checked' : '' ?> disabled/>
                                    <span class="font-medium font-brand text-sm <?= $checked ? 'text-primary dark:text-blue-400' : 'text-text-main dark:text-white' ?>"><?= $location ?></span>
                                </label>
                                <?php endforeach; ?>
                                </div>
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
                                <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Name On Vendor Pass</label>
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