<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
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
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#111827",
                        "card-light": "#ffffff",
                        "card-dark": "#1f2937",
                    },
                    fontFamily: {
                        sans: ["Montserrat", "sans-serif"],
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden">
    <?= view('reports/partials/report_sidebar', ['current' => $current]) ?>

    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8 bg-background-light dark:bg-background-dark">
        <div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-7xl">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">
                        Kiosk Settings
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Manage mobile kiosk feature flags and visitor form fields
                    </p>
                </div>
                <a href="<?= base_url('config') ?>"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Back to Config
                </a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl">check_circle</span>
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('config/saveKioskSettings') ?>">
                <?= csrf_field() ?>

                <!-- Feature Flags -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center gap-4">
                        <div class="p-2 bg-primary/10 rounded-lg">
                            <span class="material-symbols-outlined text-primary text-xl">toggle_on</span>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-800 dark:text-white">Feature Flags</h2>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Enable or disable kiosk features</p>
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-slate-800/50 overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-4 py-3 w-12">No</th>
                                    <th class="px-4 py-3">Feature</th>
                                    <th class="px-4 py-3">Description</th>
                                    <th class="px-4 py-3 text-center w-28">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 dark:text-slate-300">
                                <?php
                                $features = [
                                    ['Walk In', 'Allow walk-in visitor registration', 'kiosk_walk_in'],
                                    ['Invitation', 'Allow invitation-based visitor check-in', 'kiosk_invitation'],
                                    ['Collect Card', 'Allow visitor card collection flow', 'kiosk_collect_card'],
                                    ['VVIP', 'Allow VVIP visitor flow', 'kiosk_vvip'],
                                ];
                                foreach ($features as $i => [$label, $desc, $key]):
                                    $checked = ($config[$key] ?? 'true') === 'true';
                                ?>
                                <tr class="border-b border-gray-100 dark:border-slate-700/50">
                                    <td class="px-4 py-3"><?= $i + 1 ?></td>
                                    <td class="px-4 py-3 font-semibold"><?= esc($label) ?></td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-slate-400"><?= esc($desc) ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="hidden" name="<?= esc($key) ?>" value="false">
                                            <input type="checkbox" name="<?= esc($key) ?>" value="true"
                                                   class="sr-only peer" <?= $checked ? 'checked' : '' ?>>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                                        </label>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Visitor Fields -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center gap-4">
                        <div class="p-2 bg-emerald-500/10 rounded-lg">
                            <span class="material-symbols-outlined text-emerald-600 text-xl">person</span>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-800 dark:text-white">Visitor Fields</h2>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Configure which fields to show on the kiosk registration form</p>
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-slate-800/50 overflow-x-auto">
                        <?php
                        $fields = json_decode($config['kiosk_visitor_fields'] ?? '{}', true) ?: [];
                        $fieldLabels = [
                            'cardholder_name' => 'Cardholder Name',
                            'ic_number'       => 'IC / Passport Number',
                            'contact_number'  => 'Contact Number',
                            'company_name'    => 'Company Name',
                            'email'           => 'Email',
                            'vehicle_reg_no'  => 'Vehicle Registration No',
                            'address'         => 'Address',
                            'country'         => 'Country (Foreigner only)',
                            'date_of_birth'   => 'Date of Birth',
                            'postal_code'     => 'Postal Code',
                            'state'           => 'State',
                            'city'            => 'City',
                        ];
                        ?>
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-4 py-3 w-12">No</th>
                                    <th class="px-4 py-3">Field</th>
                                    <th class="px-4 py-3 text-center w-28">Show</th>
                                    <th class="px-4 py-3 text-center w-28">Required</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 dark:text-slate-300">
                                <?php $i = 1; foreach ($fieldLabels as $key => $label):
                                    $show     = $fields[$key]['show']     ?? true;
                                    $required = $fields[$key]['required'] ?? false;
                                ?>
                                <tr class="border-b border-gray-100 dark:border-slate-700/50">
                                    <td class="px-4 py-3"><?= $i++ ?></td>
                                    <td class="px-4 py-3 font-semibold"><?= esc($label) ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="visitor_fields[<?= esc($key) ?>][show]" value="true"
                                                   class="sr-only peer" <?= $show ? 'checked' : '' ?>>
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                                        </label>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="visitor_fields[<?= esc($key) ?>][required]" value="true"
                                                   class="sr-only peer" <?= $required ? 'checked' : '' ?>>
                                            <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-emerald-500"></div>
                                        </label>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium">
                    <span class="material-symbols-outlined text-base">save</span>
                    Save Settings
                </button>
            </form>
        </div>
    </main>
</body>
</html>
