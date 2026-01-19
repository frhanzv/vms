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
                        secondary: "#3b82f6",
                        success: "#10b981",
                        "background-light": "#f6f7f8",
                        "background-dark": "#111827",
                        "card-light": "#ffffff",
                        "card-dark": "#1f2937",
                        "nav-active": "#e0efff",
                        "nav-text": "#344767",
                        "nav-icon": "#3b82f6",
                    },
                    fontFamily: {
                        display: ["Montserrat", "sans-serif"],
                        sans: ["Montserrat", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.375rem",
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden transition-colors duration-200">
    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col justify-between p-4 hidden md:flex h-full">
        <div class="flex flex-col gap-8">
            <div class="flex items-center gap-3 px-2">
                <div class="bg-center bg-no-repeat bg-cover rounded-lg size-10 bg-primary/10 flex items-center justify-center text-primary" data-alt="SafeG Logo abstract blue square">
                    <span class="material-symbols-outlined text-3xl">shield_person</span>
                </div>
                <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">SafeG</h1>
            </div>
            <nav class="flex flex-col gap-2">
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('dashboard') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">dashboard</span>
                    <p class="text-sm font-medium">Dashboard</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('invitations') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">mail</span>
                    <p class="text-sm font-medium">Invitations</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('requests') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">assignment</span>
                    <p class="text-sm font-medium">Request List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary group transition-colors" href="<?= base_url('visitors') ?>">
                    <span class="material-symbols-outlined text-[22px] font-medium fill-1 group-hover:scale-110 transition-transform">group</span>
                    <p class="text-sm font-semibold">Visitors List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('logbook') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">menu_book</span>
                    <p class="text-sm font-medium">Visitor Logbook</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('workflow') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">account_tree</span>
                    <p class="text-sm font-medium">Visitor Workflow</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('config') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">tune</span>
                    <p class="text-sm font-medium">Config</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('settings') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">settings</span>
                    <p class="text-sm font-medium">Settings</p>
                </a>
            </nav>
        </div>
        <div class="border-t border-slate-200 dark:border-slate-700 pt-4 px-2">
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shadow-sm ring-2 ring-white dark:ring-slate-900">
                    <?= strtoupper(substr(session()->get('full_name') ?? 'U', 0, 2)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white truncate"><?= esc(session()->get('full_name') ?? 'User') ?></p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate"><?= esc(ucfirst(session()->get('role') ?? 'User')) ?></p>
                </div>
                <a href="<?= base_url('auth/logout') ?>" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <span class="material-symbols-outlined text-xl">logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8 bg-background-light dark:bg-background-dark">
        <div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-7xl">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase">
                        Visitor Pass Request
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Submit a new visitor pass request</p>
                </div>
            </div>

            <form action="<?= base_url('visitor-pass-request/store') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- Application Info Section -->
                <section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Application Info</h3>
                        <span class="material-symbols-outlined text-slate-400">corporate_fare</span>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col gap-2 mb-4">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Company Visiting <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <?php 
                                $locations = [
                                    'ADMIN B', 'ADMIN D', 'AMPANG KL', 'ANNEXE BUILDING',
                                    'CFS', 'COMMON WAREHOUSE', 'EAST WHARF', 'EPIC SOLAR',
                                    'KPK GATE', 'KSB PHASE 2 GATE', 'KTSB K.TRG', 'KUALA TERENGGANU',
                                    'LCB', 'OPERATION PHASE 1', 'PHASE 1', 'PHASE 3',
                                    'PHASE 4', 'SUKMA SAMUDERA', 'TELUK KALONG', 'WH27',
                                    'WHSET WHARF', 'WORKSHOP MAINTENANCE', 'WORKSHOP PHASE 2'
                                ];
                                foreach ($locations as $location): 
                                ?>
                                <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-primary hover:bg-primary/5 cursor-pointer transition-all">
                                    <input type="checkbox" name="company_visiting[]" value="<?= $location ?>" class="rounded text-primary focus:ring-primary focus:ring-offset-0 w-4 h-4"/>
                                    <span class="text-xs font-medium text-slate-700 dark:text-slate-300"><?= $location ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Date of Visit Section -->
                <section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden mt-8">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Date of Visit</h3>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="addDateVisit()" class="text-green-600 hover:text-green-700 transition-colors p-1.5 rounded-full hover:bg-green-50 dark:hover:bg-green-900/20" title="Add Date">
                                <span class="material-symbols-outlined text-2xl">add_circle</span>
                            </button>
                            <button type="button" onclick="removeDateVisit()" class="text-red-600 hover:text-red-700 transition-colors p-1.5 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove Date">
                                <span class="material-symbols-outlined text-2xl">remove_circle</span>
                            </button>
                        </div>
                    </div>
                    <div id="dateVisitContainer" class="p-6 flex flex-col gap-6">
                        <div class="date-visit-item grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Date From <span class="text-red-500">*</span></label>
                                <input name="dates[0][date_from]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="date" required/>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Date To <span class="text-red-500">*</span></label>
                                <input name="dates[0][date_to]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="date" required/>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Time From <span class="text-red-500">*</span></label>
                                <input name="dates[0][time_from]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="time" required/>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Time To <span class="text-red-500">*</span></label>
                                <input name="dates[0][time_to]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="time" required/>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Person Section -->
                <section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden mt-8">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Person</h3>
                        <button type="button" class="bg-success hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold uppercase shadow transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">credit_card</span>
                            Read MyKad
                        </button>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Resident <span class="text-red-500">*</span></label>
                            <select name="resident" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" required>
                                <option value="">Select...</option>
                                <option value="LOCAL">LOCAL</option>
                                <option value="FOREIGN">FOREIGN</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">IC Number <span class="text-red-500">*</span></label>
                            <input name="ic_number" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" placeholder="Enter IC / Passport Number" type="text" required/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Date of Birth <span class="text-red-500">*</span></label>
                            <input name="date_of_birth" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="date" required/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Sex <span class="text-red-500">*</span></label>
                            <select name="sex" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" required>
                                <option value="">Select...</option>
                                <option value="MALE">MALE</option>
                                <option value="FEMALE">FEMALE</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Full Name <span class="text-red-500">*</span></label>
                            <input name="full_name" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" placeholder="Full name as per ID" type="text" required/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Contact Number <span class="text-red-500">*</span></label>
                            <input name="contact_number" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" placeholder="+60 1x-xxx xxxx" type="tel" required/>
                        </div>
                        <div class="md:col-span-2 flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Email Address <span class="text-red-500">*</span></label>
                            <input name="email" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" placeholder="visitor@example.com" type="email" required/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Address 1</label>
                            <input name="address_1" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Address 2</label>
                            <input name="address_2" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text"/>
                        </div>
                        <div class="md:col-span-2 flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Address 3</label>
                            <input name="address_3" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Country</label>
                            <select name="country" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white">
                                <option value="MALAYSIA">MALAYSIA</option>
                                <option value="OTHER">OTHER</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">State</label>
                            <select name="state" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white">
                                <option value="">SELECT</option>
                                <option value="JOHOR">JOHOR</option>
                                <option value="KEDAH">KEDAH</option>
                                <option value="KELANTAN">KELANTAN</option>
                                <option value="MELAKA">MELAKA</option>
                                <option value="NEGERI SEMBILAN">NEGERI SEMBILAN</option>
                                <option value="PAHANG">PAHANG</option>
                                <option value="PENANG">PENANG</option>
                                <option value="PERAK">PERAK</option>
                                <option value="PERLIS">PERLIS</option>
                                <option value="SABAH">SABAH</option>
                                <option value="SARAWAK">SARAWAK</option>
                                <option value="SELANGOR">SELANGOR</option>
                                <option value="TERENGGANU">TERENGGANU</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">City</label>
                            <input name="city" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Postal Code</label>
                            <input name="postal_code" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Category</label>
                            <select name="category" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white">
                                <option value="PUBLIC">PUBLIC</option>
                                <option value="PRIVATE">PRIVATE</option>
                                <option value="GOVERNMENT">GOVERNMENT</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Type Of Vehicle</label>
                            <select name="vehicle_type" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white">
                                <option value="">SELECT</option>
                                <option value="CAR">CAR</option>
                                <option value="MOTORCYCLE">MOTORCYCLE</option>
                                <option value="VAN">VAN</option>
                                <option value="TRUCK">TRUCK</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Vehicle Registration Number</label>
                            <input name="vehicle_registration" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" placeholder="e.g. ABC 1234" type="text"/>
                        </div>
                    </div>
                </section>

                <!-- Driving License Section -->
                <section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden mt-8">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Driving License</h3>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="addLicense()" class="text-green-600 hover:text-green-700 transition-colors p-1.5 rounded-full hover:bg-green-50 dark:hover:bg-green-900/20" title="Add License">
                                <span class="material-symbols-outlined text-2xl">add_circle</span>
                            </button>
                            <button type="button" onclick="removeLicense()" class="text-red-600 hover:text-red-700 transition-colors p-1.5 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove License">
                                <span class="material-symbols-outlined text-2xl">remove_circle</span>
                            </button>
                        </div>
                    </div>
                    <div id="licenseContainer" class="p-6 flex flex-col gap-4">
                        <div class="license-item flex items-start gap-4">
                            <span class="license-number text-sm font-bold text-slate-600 dark:text-slate-400 mt-3">1.</span>
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">License Class</label>
                                    <select name="licenses[0][class]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white">
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
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">License Expiry <span class="text-red-500">*</span></label>
                                    <input name="licenses[0][expiry]" placeholder="DD/MM/YYYY" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="date"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Company Section -->
                <section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden mt-8">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Company</h3>
                        <span class="material-symbols-outlined text-slate-400">business</span>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Company Registration ID</label>
                            <input name="company_reg_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Company Name</label>
                            <input name="company_name" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Staff ID Of Person Visited</label>
                            <input name="staff_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Name Of Person Visited <span class="text-red-500">*</span></label>
                            <input name="person_visited" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text" required/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Contact No Of Person Visited <span class="text-red-500">*</span></label>
                            <input name="contact_person_visited" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" placeholder="+60 1x-xxx xxxx" type="tel" required/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Reason <span class="text-red-500">*</span></label>
                            <select name="reason" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" required>
                                <option value="">SELECT</option>
                                <option value="MEETING">MEETING</option>
                                <option value="DELIVERY">DELIVERY</option>
                                <option value="MAINTENANCE">MAINTENANCE</option>
                                <option value="SITE VISIT">SITE VISIT</option>
                                <option value="CATERING">CATERING</option>
                                <option value="OTHER">OTHER</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- Asset/Equipment Details Section -->
                <section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden mt-8">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Asset/Equipment Details</h3>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="addEquipment()" class="text-green-600 hover:text-green-700 transition-colors p-1.5 rounded-full hover:bg-green-50 dark:hover:bg-green-900/20" title="Add Equipment">
                                <span class="material-symbols-outlined text-2xl">add_circle</span>
                            </button>
                            <button type="button" onclick="removeEquipment()" class="text-red-600 hover:text-red-700 transition-colors p-1.5 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove Equipment">
                                <span class="material-symbols-outlined text-2xl">remove_circle</span>
                            </button>
                        </div>
                    </div>
                    <div id="equipmentContainer" class="p-6 flex flex-col gap-6">
                        <div class="equipment-item">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="equipment-number text-sm font-bold text-slate-600 dark:text-slate-400">1.</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Category</label>
                                    <select name="equipment[0][category]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white">
                                        <option value="">SELECT</option>
                                        <option value="TOOLS">TOOLS</option>
                                        <option value="ELECTRONICS">ELECTRONICS</option>
                                        <option value="MACHINERY">MACHINERY</option>
                                        <option value="VEHICLE">VEHICLE</option>
                                        <option value="OTHER">OTHER</option>
                                    </select>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Size</label>
                                    <select name="equipment[0][size]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white">
                                        <option value="">SELECT</option>
                                        <option value="SMALL">SMALL</option>
                                        <option value="MEDIUM">MEDIUM</option>
                                        <option value="LARGE">LARGE</option>
                                        <option value="EXTRA LARGE">EXTRA LARGE</option>
                                    </select>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Transportation Method</label>
                                    <select name="equipment[0][transport]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white">
                                        <option value="">SELECT</option>
                                        <option value="HAND CARRY">HAND CARRY</option>
                                        <option value="VEHICLE">VEHICLE</option>
                                        <option value="TRUCK">TRUCK</option>
                                        <option value="FORKLIFT">FORKLIFT</option>
                                    </select>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Purpose</label>
                                    <input name="equipment[0][purpose]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text"/>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Type of Equipment</label>
                                    <input name="equipment[0][type]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text"/>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Voltage Use</label>
                                    <input name="equipment[0][voltage]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" placeholder="e.g. 240V" type="text"/>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Quantity</label>
                                    <input name="equipment[0][quantity]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="number" min="1"/>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Serial Number</label>
                                    <input name="equipment[0][serial]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Upload Section -->
                <section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden mt-8">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Upload Documents</h3>
                        <span class="material-symbols-outlined text-slate-400">upload_file</span>
                    </div>
                    <div class="p-6">
                        <div class="border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-lg p-8 text-center hover:border-primary hover:bg-primary/5 transition-all cursor-pointer">
                            <input type="file" name="documents[]" multiple class="hidden" id="fileUpload"/>
                            <label for="fileUpload" class="cursor-pointer flex flex-col items-center gap-3">
                                <span class="material-symbols-outlined text-5xl text-slate-400">cloud_upload</span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Click or drag file to upload</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Other Documents (PDF, JPG, PNG - Max 10MB)</p>
                                </div>
                            </label>
                        </div>
                        <div id="fileList" class="mt-4 space-y-2"></div>
                    </div>
                </section>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-4 py-6 border-t border-slate-200 dark:border-slate-800 mt-8">
                    <button type="button" onclick="window.history.back()" class="px-6 py-3 rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-8 py-3 rounded-lg bg-primary text-white font-bold hover:bg-blue-600 shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2">
                        <span>Submit Request</span>
                        <span class="material-symbols-outlined text-sm">send</span>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Date Visit dynamic addition
        let dateVisitCount = 1;
        function addDateVisit() {
            const container = document.getElementById('dateVisitContainer');
            const html = `
                <div class="date-visit-item grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Date From <span class="text-red-500">*</span></label>
                        <input name="dates[${dateVisitCount}][date_from]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="date" required/>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Date To <span class="text-red-500">*</span></label>
                        <input name="dates[${dateVisitCount}][date_to]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="date" required/>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Time From <span class="text-red-500">*</span></label>
                        <input name="dates[${dateVisitCount}][time_from]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="time" required/>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Time To <span class="text-red-500">*</span></label>
                        <input name="dates[${dateVisitCount}][time_to]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="time" required/>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            dateVisitCount++;
        }

        function removeDateVisit() {
            const container = document.getElementById('dateVisitContainer');
            const items = container.querySelectorAll('.date-visit-item');
            if (items.length > 1) {
                items[items.length - 1].remove();
                dateVisitCount--;
            }
        }

        // License dynamic addition
        let licenseCount = 1;
        function addLicense() {
            const container = document.getElementById('licenseContainer');
            const html = `
                <div class="license-item flex items-start gap-4">
                    <span class="license-number text-sm font-bold text-slate-600 dark:text-slate-400 mt-3">${licenseCount + 1}.</span>
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">License Class</label>
                            <select name="licenses[${licenseCount}][class]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white">
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
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">License Expiry <span class="text-red-500">*</span></label>
                            <input name="licenses[${licenseCount}][expiry]" placeholder="DD/MM/YYYY" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="date"/>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            licenseCount++;
            updateLicenseNumbers();
        }

        function removeLicense() {
            const container = document.getElementById('licenseContainer');
            const items = container.querySelectorAll('.license-item');
            if (items.length > 1) {
                items[items.length - 1].remove();
                licenseCount--;
                updateLicenseNumbers();
            }
        }

        function updateLicenseNumbers() {
            const numbers = document.querySelectorAll('.license-number');
            numbers.forEach((num, index) => {
                num.textContent = `${index + 1}.`;
            });
        }

        // Equipment dynamic addition
        let equipmentCount = 1;
        function addEquipment() {
            const container = document.getElementById('equipmentContainer');
            const html = `
                <div class="equipment-item">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="equipment-number text-sm font-bold text-slate-600 dark:text-slate-400">${equipmentCount + 1}.</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Category</label>
                            <select name="equipment[${equipmentCount}][category]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white">
                                <option value="">SELECT</option>
                                <option value="TOOLS">TOOLS</option>
                                <option value="ELECTRONICS">ELECTRONICS</option>
                                <option value="MACHINERY">MACHINERY</option>
                                <option value="VEHICLE">VEHICLE</option>
                                <option value="OTHER">OTHER</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Size</label>
                            <select name="equipment[${equipmentCount}][size]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white">
                                <option value="">SELECT</option>
                                <option value="SMALL">SMALL</option>
                                <option value="MEDIUM">MEDIUM</option>
                                <option value="LARGE">LARGE</option>
                                <option value="EXTRA LARGE">EXTRA LARGE</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Transportation Method</label>
                            <select name="equipment[${equipmentCount}][transport]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white">
                                <option value="">SELECT</option>
                                <option value="HAND CARRY">HAND CARRY</option>
                                <option value="VEHICLE">VEHICLE</option>
                                <option value="TRUCK">TRUCK</option>
                                <option value="FORKLIFT">FORKLIFT</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Purpose</label>
                            <input name="equipment[${equipmentCount}][purpose]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Type of Equipment</label>
                            <input name="equipment[${equipmentCount}][type]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Voltage Use</label>
                            <input name="equipment[${equipmentCount}][voltage]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" placeholder="e.g. 240V" type="text"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Quantity</label>
                            <input name="equipment[${equipmentCount}][quantity]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="number" min="1"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Serial Number</label>
                            <input name="equipment[${equipmentCount}][serial]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="text"/>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            equipmentCount++;
            updateEquipmentNumbers();
        }

        function removeEquipment() {
            const container = document.getElementById('equipmentContainer');
            const items = container.querySelectorAll('.equipment-item');
            if (items.length > 1) {
                items[items.length - 1].remove();
                equipmentCount--;
                updateEquipmentNumbers();
            }
        }

        function updateEquipmentNumbers() {
            const numbers = document.querySelectorAll('.equipment-number');
            numbers.forEach((num, index) => {
                num.textContent = `${index + 1}.`;
            });
        }

        // File upload preview
        document.getElementById('fileUpload').addEventListener('change', function(e) {
            const fileList = document.getElementById('fileList');
            fileList.innerHTML = '';
            
            Array.from(e.target.files).forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700';
                fileItem.innerHTML = `
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">description</span>
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">${file.name}</p>
                            <p class="text-xs text-slate-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="text-slate-400 hover:text-red-500 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                `;
                fileList.appendChild(fileItem);
            });
        });

        function removeFile(index) {
            const input = document.getElementById('fileUpload');
            const dt = new DataTransfer();
            const files = Array.from(input.files);
            
            files.forEach((file, i) => {
                if (i !== index) dt.items.add(file);
            });
            
            input.files = dt.files;
            input.dispatchEvent(new Event('change'));
        }
    </script>
</body>
</html>
