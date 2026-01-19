<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $pageTitle ?? 'Visitor Registration - SafeG' ?></title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "primary-hover": "#0f6ac6",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1a2632",
                        "text-main": "#0d141b",
                        "text-sub": "#4c739a",
                        "border-color": "#e7edf3",
                    },
                    fontFamily: {
                        "sans": ["Montserrat", "sans-serif"],
                        "display": ["Montserrat", "sans-serif"],
                        "brand": ["Montserrat", "sans-serif"],
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
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
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main dark:text-white font-brand antialiased min-h-screen flex flex-col">
    <header class="sticky top-0 z-50 w-full bg-surface-light/95 dark:bg-surface-dark/95 backdrop-blur border-b border-border-color dark:border-gray-800">
        <div class="max-w-[960px] mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-end">
            <div class="hidden sm:flex items-center gap-4 text-sm font-medium text-text-sub dark:text-gray-400">
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">help</span> Help</span>
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">language</span> English</span>
            </div>
        </div>
    </header>

    <main class="flex-1 w-full max-w-[960px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex justify-between items-end mb-2">
                <span class="text-sm font-semibold text-primary font-brand uppercase tracking-wider">Step 1 of 3</span>
                <span class="text-xs text-text-sub dark:text-gray-400">Registration Details</span>
            </div>
            <div class="h-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-primary w-1/3 rounded-full shadow-[0_0_10px_rgba(19,127,236,0.5)]"></div>
            </div>
        </div>

        <!-- Page Header -->
        <div class="mb-8 space-y-2">
            <h1 class="text-3xl sm:text-4xl font-black text-text-main dark:text-white font-brand tracking-tight">Visitor Registration</h1>
            <p class="text-text-sub dark:text-gray-400 text-lg max-w-2xl font-brand">
                Please complete your details for secure entry verification at SafeG.
            </p>
        </div>

        <form action="<?= base_url('visitor-registration/submit') ?>" class="space-y-8" method="POST" id="registrationForm">
            <?= csrf_field() ?>

            <!-- Visit Information -->
            <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                    <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">business</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Visit Information</h2>
                        <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Where and when are you visiting?</p>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Company Visiting <span class="text-red-500">*</span></label>
                        <div class="bg-background-light dark:bg-background-dark p-4 rounded-lg border border-border-color dark:border-gray-700">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="ADMIN B" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">ADMIN B</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="ADMIN D" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">ADMIN D</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="AMPANG KL" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">AMPANG KL</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="ANNEXE BUILDING" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">ANNEXE BUILDING</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="CFS" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">CFS</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="COMMON WAREHOUSE" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">COMMON WAREHOUSE</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="EAST WHARF" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">EAST WHARF</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="EPIC SOLAR" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">EPIC SOLAR</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="KPK GATE" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">KPK GATE</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="KSB PHASE 2 GATE" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">KSB PHASE 2 GATE</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="KTSB K.TRG" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">KTSB K.TRG</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="KUALA TERENGGANU" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">KUALA TERENGGANU</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="LCB" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">LCB</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="OPERATION PHASE 1" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">OPERATION PHASE 1</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="PHASE 1" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">PHASE 1</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="PHASE 3" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">PHASE 3</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="PHASE 4" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">PHASE 4</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="SUKMA SAMUDERA" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">SUKMA SAMUDERA</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="TELUK KALONG" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">TELUK KALONG</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="WH27" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">WH27</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="WHSET WHARF" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">WHSET WHARF</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="WORKSHOP MAINTENANCE" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">WORKSHOP MAINTENANCE</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="WORKSHOP PHASE 2" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm">WORKSHOP PHASE 2</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date From</label>
                            <div class="relative">
                                <input name="date_from" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="dd/mm/yyyy hh:mm" type="text" value="17/12/2025 07:00"/>
                                <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub pointer-events-none">calendar_month</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date To</label>
                            <div class="relative">
                                <input name="date_to" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="dd/mm/yyyy hh:mm" type="text" value="17/12/2025 19:00"/>
                                <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub pointer-events-none">event</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Details of Visit -->
            <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                    <div class="size-10 rounded-full bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <span class="material-symbols-outlined">badge</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Details of Visit</h2>
                        <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Host and purpose details.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Staff ID Of Person Visited</label>
                        <input name="staff_id" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Contact No Of Person Visited</label>
                        <input name="host_contact" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="012956492"/>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Name Of Company Visited</label>
                        <input name="company_visited" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="DOWELL SCHLUMBERGER (M) SDN. BHD."/>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Reason</label>
                        <input name="visit_reason" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="OTHER"/>
                    </div>
                </div>
            </section>

            <!-- Person Details -->
            <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-full bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center text-teal-600 dark:text-teal-400">
                            <span class="material-symbols-outlined">person</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Person Details</h2>
                            <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Your personal identification and contact info.</p>
                        </div>
                    </div>
                    <button type="button" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold uppercase shadow transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">credit_card</span>
                        READ MYKAD
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Resident</label>
                        <input name="resident_type" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="LOCAL"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">IC Number</label>
                        <input name="ic_number" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="830319115313"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Full Name</label>
                        <input name="full_name" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="AISAMUDDIN BIN YAHAYA"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Birthday</label>
                        <input name="birthday" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="19/03/1983"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Sex</label>
                        <select name="sex" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-10 appearance-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none cursor-pointer font-brand">
                            <option value="">Select Sex</option>
                            <option selected value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Contact Number</label>
                        <input name="contact_number" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="tel" value="0196588662"/>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Email</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input name="email" class="flex-1 h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="email" value="AISAMUDDIN.YAHAYA@PETRONAS.COM.MY"/>
                            <button class="h-12 px-6 bg-primary hover:bg-primary-hover text-white font-medium rounded-lg transition-colors whitespace-nowrap shadow-sm font-brand" type="button">
                                Update Email
                            </button>
                        </div>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 1</label>
                        <input name="address1" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="NO 33 JLN KS 1/3, KOTA SULTAN AHMAD SHAH,"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 2</label>
                        <input name="address2" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 3</label>
                        <input name="address3" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">State</label>
                        <input name="state" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="PAHANG"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">City</label>
                        <input name="city" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="KUANTAN"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Postal Code</label>
                        <input name="postal_code" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="25200"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Country</label>
                        <input name="country" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="Malaysia"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Vehicle Category</label>
                        <input name="vehicle_category" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="VAN"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Vehicle Type</label>
                        <input name="vehicle_type" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="VAN"/>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Vehicle Registration Number</label>
                        <input name="vehicle_registration" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="JVT7006"/>
                    </div>
                </div>
            </section>

            <!-- Company Details -->
            <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                    <div class="size-10 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400">
                        <span class="material-symbols-outlined">apartment</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Company Details</h2>
                        <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Your company information.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Company Registration ID</label>
                        <input name="company_reg_id" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="49020-A"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Company Name</label>
                        <input name="company_name" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text" value="DOWELL SCHLUMBERGER (M) SDN. BHD."/>
                    </div>
                </div>
            </section>

            <!-- Asset/Equipment Details Section -->
            <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 overflow-hidden">
                <div class="p-6 border-b border-border-color dark:border-gray-800 flex justify-between items-center bg-background-light dark:bg-background-dark">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-full bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <span class="material-symbols-outlined">inventory_2</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Asset/Equipment Details</h2>
                            <p class="text-sm text-text-sub dark:text-gray-400 font-brand">List all equipment you'll be bringing.</p>
                        </div>
                    </div>
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
                    <div class="text-center py-8 text-text-sub dark:text-gray-400 font-brand">
                        <span class="material-symbols-outlined text-5xl mb-3 block text-gray-300 dark:text-gray-600">inventory_2</span>
                        <p class="text-sm">No equipment added yet. Click <span class="text-primary font-semibold">+</span> to add equipment.</p>
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
                        <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Required for identity verification.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-3">
                        <p class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Government ID <span class="text-red-500">*</span></p>
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
                        <p class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Invitation Letter (Optional)</p>
                        <div class="group relative flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                            <input name="invitation_letter" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" type="file"/>
                            <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                <span class="material-symbols-outlined text-4xl text-gray-400 group-hover:text-primary mb-3 transition-colors">upload_file</span>
                                <p class="mb-1 text-sm text-text-main dark:text-gray-300 font-brand"><span class="font-semibold text-primary">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-text-sub dark:text-gray-500 font-brand">PDF or Images</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Profile Photo -->
            <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                    <div class="size-10 rounded-full bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center text-teal-600 dark:text-teal-400">
                        <span class="material-symbols-outlined">photo_camera</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Profile Photo</h2>
                        <p class="text-sm text-text-sub dark:text-gray-400 font-brand">This will be used for your visitor badge.</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-8">
                    <div class="relative group">
                        <div class="size-32 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden border-4 border-white dark:border-gray-700 shadow-lg">
                            <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600">account_circle</span>
                        </div>
                        <button class="absolute bottom-0 right-0 p-2 bg-white dark:bg-gray-700 rounded-full shadow-md text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary transition-colors border border-gray-200 dark:border-gray-600" type="button">
                            <span class="material-symbols-outlined text-xl">edit</span>
                        </button>
                    </div>
                    <div class="flex-1 space-y-4 text-center sm:text-left">
                        <div>
                            <h3 class="font-medium text-text-main dark:text-white font-brand">Upload or Capture</h3>
                            <p class="text-sm text-text-sub dark:text-gray-400 mt-1 font-brand">Please ensure your face is clearly visible. No sunglasses or hats.</p>
                        </div>
                        <div class="flex flex-wrap justify-center sm:justify-start gap-3">
                            <button class="px-5 py-2.5 rounded-lg bg-primary hover:bg-primary-hover text-white font-medium text-sm flex items-center gap-2 transition-all shadow-lg shadow-primary/25 font-brand" type="button">
                                <span class="material-symbols-outlined text-lg">upload</span>
                                Upload Photo
                            </button>
                            <button class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-text-main dark:text-white font-medium text-sm flex items-center gap-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all font-brand" type="button">
                                <span class="material-symbols-outlined text-lg">camera_alt</span>
                                Take Photo
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sticky Footer with Actions -->
            <div class="sticky bottom-0 z-40 -mx-4 sm:-mx-6 lg:-mx-8 p-4 sm:px-6 lg:px-8 bg-surface-light/95 dark:bg-surface-dark/95 border-t border-border-color dark:border-gray-800 backdrop-blur flex flex-col sm:flex-row items-center justify-between gap-4 mt-8">
                <div class="flex items-center gap-2 text-xs text-text-sub dark:text-gray-500 font-brand">
                    <span class="material-symbols-outlined text-base">lock</span>
                    <span>Your data is securely encrypted with 256-bit SSL.</span>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button class="flex-1 sm:flex-none px-6 py-3 rounded-lg border border-transparent text-text-sub dark:text-gray-400 font-medium hover:text-text-main dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors font-brand" type="button">
                        Cancel
                    </button>
                    <button class="flex-1 sm:flex-none px-8 py-3 rounded-lg bg-primary hover:bg-primary-hover text-white font-bold shadow-lg shadow-primary/30 flex items-center justify-center gap-2 transition-all transform hover:scale-[1.02] font-brand" type="submit">
                        Submit Registration
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                </div>
            </div>
        </form>
    </main>

    <script>
        // Equipment dynamic addition
        let equipmentCount = 0;
        
        function addEquipment() {
            const container = document.getElementById('equipmentContainer');
            
            // Remove empty state message if it exists
            const emptyState = container.querySelector('.text-center');
            if (emptyState) {
                emptyState.remove();
            }
            
            const html = `
                <div class="equipment-item">
                    <div class="flex items-center justify-between mb-4">
                        <span class="equipment-number text-sm font-bold text-text-main dark:text-text-sub font-brand">${equipmentCount + 1}.</span>
                        <button type="button" onclick="removeSpecificEquipment(this)" class="text-red-600 hover:text-red-700 transition-colors p-1.5 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove this equipment">
                            <span class="material-symbols-outlined text-xl">delete</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Category</label>
                            <select name="equipment[${equipmentCount}][category]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT</option>
                                <option value="TOOLS">TOOLS</option>
                                <option value="ELECTRONICS">ELECTRONICS</option>
                                <option value="MACHINERY">MACHINERY</option>
                                <option value="VEHICLE">VEHICLE</option>
                                <option value="OTHER">OTHER</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Size</label>
                            <select name="equipment[${equipmentCount}][size]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT</option>
                                <option value="SMALL">SMALL</option>
                                <option value="MEDIUM">MEDIUM</option>
                                <option value="LARGE">LARGE</option>
                                <option value="EXTRA LARGE">EXTRA LARGE</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Transportation Method</label>
                            <select name="equipment[${equipmentCount}][transport]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT</option>
                                <option value="HAND CARRY">HAND CARRY</option>
                                <option value="VEHICLE">VEHICLE</option>
                                <option value="TRUCK">TRUCK</option>
                                <option value="FORKLIFT">FORKLIFT</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Purpose</label>
                            <input name="equipment[${equipmentCount}][purpose]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Type of Equipment</label>
                            <input name="equipment[${equipmentCount}][type]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Voltage Use</label>
                            <input name="equipment[${equipmentCount}][voltage]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="e.g. 240V" type="text"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Quantity</label>
                            <input name="equipment[${equipmentCount}][quantity]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="number" min="1"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-text-main dark:text-gray-200 font-brand">Serial Number</label>
                            <input name="equipment[${equipmentCount}][serial]" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
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
            if (items.length > 0) {
                items[items.length - 1].remove();
                updateEquipmentNumbers();
                
                // Show empty state if no items left
                if (container.querySelectorAll('.equipment-item').length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-8 text-text-sub dark:text-gray-400 font-brand">
                            <span class="material-symbols-outlined text-5xl mb-3 block text-gray-300 dark:text-gray-600">inventory_2</span>
                            <p class="text-sm">No equipment added yet. Click <span class="text-primary font-semibold">+</span> to add equipment.</p>
                        </div>
                    `;
                    equipmentCount = 0;
                }
            }
        }

        function removeSpecificEquipment(button) {
            const container = document.getElementById('equipmentContainer');
            const equipmentItem = button.closest('.equipment-item');
            equipmentItem.remove();
            updateEquipmentNumbers();
            
            // Show empty state if no items left
            if (container.querySelectorAll('.equipment-item').length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-text-sub dark:text-gray-400 font-brand">
                        <span class="material-symbols-outlined text-5xl mb-3 block text-gray-300 dark:text-gray-600">inventory_2</span>
                        <p class="text-sm">No equipment added yet. Click <span class="text-primary font-semibold">+</span> to add equipment.</p>
                    </div>
                `;
                equipmentCount = 0;
            }
        }

        function updateEquipmentNumbers() {
            const numbers = document.querySelectorAll('.equipment-number');
            numbers.forEach((num, index) => {
                num.textContent = `${index + 1}.`;
            });
        }

        // Form submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Form validation and submission logic here
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Registration submitted successfully!');
                    // Redirect or show success message
                } else {
                    alert('Error: ' + (data.message || 'Please check your inputs'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    </script>
</body>
</html>
