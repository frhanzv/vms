<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle ?? 'Blacklist Entry') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "primary-dark": "#0f66be",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1a2634",
                    },
                    fontFamily: { "sans": ["Montserrat", "sans-serif"] },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .field-readonly {
            @apply w-full h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none cursor-not-allowed;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white overflow-hidden">
<div class="flex h-screen w-full">

    <!-- Sidebar -->
    <?= view('partials/sidebar') ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <div class="flex-1 overflow-y-auto p-6 md:p-8 no-scrollbar">

            <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('blacklist/entry/store') ?>" method="POST">
                <?= csrf_field() ?>

                <!-- Hidden fields -->
                <input type="hidden" name="ic_passport_no" value="<?= esc($invitation['ic_passport'] ?? '') ?>"/>
                <input type="hidden" name="name"           value="<?= esc($invitation['full_name'] ?? '') ?>"/>
                <input type="hidden" name="type"           value="<?= esc($type) ?>"/>
                <input type="hidden" name="staff_id"       value="<?= esc($staff_no) ?>"/>

                <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-8">

                    <!-- Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                        <h2 class="text-lg font-semibold text-gray-700 uppercase tracking-tight">Blacklist Entry</h2>
                        <a href="<?= base_url('blacklist/entry') ?>" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[24px]">close</span>
                        </a>
                    </div>

                    <!-- SUBMISSION Section -->
                    <div>
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">Submission</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            <div>
                                <label class="block text-xs text-slate-400 mb-1.5 font-medium">
                                    Date of Blacklist <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="blacklist_date"
                                    value="<?= date('Y-m-d') ?>"
                                    required
                                    class="w-full h-10 px-4 text-sm bg-white border border-slate-300 rounded-lg text-slate-700 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"/>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-400 mb-1.5 font-medium">Type of Blacklist</label>
                                <input type="text" value="BLACKLIST" readonly
                                    class="w-full h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-600 focus:outline-none cursor-not-allowed"/>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-400 mb-1.5 font-medium">
                                    Reason <span class="text-red-500">*</span>
                                </label>
                                <select name="reason" required
                                    class="w-full h-10 px-4 text-sm bg-white border border-slate-300 rounded-lg text-slate-700 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 appearance-none cursor-pointer">
                                    <option value="">— Select Reason —</option>
                                    <?php foreach ($reasons as $r): ?>
                                        <option value="<?= esc($r['reason']) ?>"><?= esc($r['reason']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                    </div>

                    <!-- PERSON Section -->
                    <div>
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">Person</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            <div>
                                <label class="block text-xs text-slate-400 mb-1.5 font-medium">IC / Passport No</label>
                                <input type="text" value="<?= esc($invitation['ic_passport'] ?? '—') ?>" readonly
                                    class="w-full h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none cursor-not-allowed"/>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-400 mb-1.5 font-medium">Name</label>
                                <input type="text" value="<?= esc($invitation['full_name'] ?? '—') ?>" readonly
                                    class="w-full h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none cursor-not-allowed"/>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-400 mb-1.5 font-medium">Gender</label>
                                <input type="text" value="<?= esc($invitation['sex'] ?? '—') ?>" readonly
                                    class="w-full h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none cursor-not-allowed"/>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-400 mb-1.5 font-medium">Birthday</label>
                                <input type="text" value="<?= esc($invitation['date_of_birth'] ?? '—') ?>" readonly
                                    class="w-full h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none cursor-not-allowed"/>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-400 mb-1.5 font-medium">Country</label>
                                <input type="text" value="<?= esc($country_name) ?>" readonly
                                    class="w-full h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none cursor-not-allowed"/>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-400 mb-1.5 font-medium">Company Name</label>
                                <input type="text" value="<?= esc($invitation['company'] ?? '—') ?>" readonly
                                    class="w-full h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none cursor-not-allowed"/>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-400 mb-1.5 font-medium">Type</label>
                                <input type="text" value="<?= esc($type) ?>" readonly
                                    class="w-full h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none cursor-not-allowed"/>
                            </div>

                            <?php if ($type === 'Staff'): ?>
                            <div>
                                <label class="block text-xs text-slate-400 mb-1.5 font-medium">Staff No</label>
                                <input type="text" value="<?= esc($staff_no) ?>" readonly
                                    class="w-full h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none cursor-not-allowed"/>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-400 mb-1.5 font-medium">Designation</label>
                                <input type="text" value="<?= esc($designation) ?>" readonly
                                    class="w-full h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none cursor-not-allowed"/>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <a href="<?= base_url('blacklist/entry') ?>"
                            class="px-5 py-2.5 rounded-lg bg-amber-400 hover:bg-amber-500 text-white text-sm font-semibold transition-colors">
                            Back
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary-dark text-white text-sm font-semibold transition-colors shadow-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            Submit
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </main>
</div>
</body>
</html>