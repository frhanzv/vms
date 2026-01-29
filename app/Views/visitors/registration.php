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
<body class="bg-background-light dark:bg-background-dark text-text-main dark:text-white font-brand antialiased min-h-screen flex flex-col">
    <header class="sticky top-0 z-50 w-full bg-surface-light/95 dark:bg-surface-dark/95 backdrop-blur border-b border-border-color dark:border-gray-800">
        <div class="max-w-[960px] mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gradient-to-br from-primary to-blue-600 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-xl">shield_person</span>
                </div>
                <span class="text-xl font-bold text-text-main dark:text-white">SafeG</span>
            </div>
            <div class="hidden sm:flex items-center gap-4 text-sm font-medium text-text-sub dark:text-gray-400">
                <button type="button" onclick="showHelp()" class="flex items-center gap-1 hover:text-primary transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">help</span> Help
                </button>
                <button type="button" onclick="showLanguageMenu()" class="flex items-center gap-1 hover:text-primary transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">language</span> <span id="currentLang">English</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Help Modal -->
    <div id="helpModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full animate-scale-in">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl text-primary">help</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Need Help?</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4 text-gray-700 dark:text-gray-300">
                    <p class="font-semibold">Registration Assistance:</p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-lg text-primary mt-0.5">check_circle</span>
                            <span>Fill in all required fields accurately</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-lg text-primary mt-0.5">check_circle</span>
                            <span>Upload a clear photo of your MyKad for OCR</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-lg text-primary mt-0.5">check_circle</span>
                            <span>Ensure contact information is correct</span>
                        </li>
                    </ul>
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="font-semibold mb-2">Contact Support:</p>
                        <p class="text-sm">📞 Helpline: +60 3-XXXX XXXX</p>
                        <p class="text-sm">✉️ Email: support@safeg.com</p>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeHelpModal()" class="w-full px-4 py-3 bg-primary hover:bg-primary-hover text-white font-semibold rounded-xl transition-colors">
                    Got it, thanks!
                </button>
            </div>
        </div>
    </div>

    <!-- Language Modal -->
    <div id="languageModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full animate-scale-in">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl text-primary">language</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Select Language</h3>
                </div>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                <div class="space-y-2">
                    <button onclick="changeLanguage('en')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="en">
                        <span class="font-medium">🇬🇧 English</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('ms')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="ms">
                        <span class="font-medium">🇲🇾 Bahasa Malaysia</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('zh-CN')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="zh-CN">
                        <span class="font-medium">🇨🇳 中文 (简体)</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('zh-TW')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="zh-TW">
                        <span class="font-medium">🇹🇼 繁體中文</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('ta')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="ta">
                        <span class="font-medium">🇮🇳 தமிழ் (Tamil)</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('hi')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="hi">
                        <span class="font-medium">🇮🇳 हिन्दी (Hindi)</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('ja')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="ja">
                        <span class="font-medium">🇯🇵 日本語 (Japanese)</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('ko')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="ko">
                        <span class="font-medium">🇰🇷 한국어 (Korean)</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('th')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="th">
                        <span class="font-medium">🇹🇭 ภาษาไทย (Thai)</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('vi')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="vi">
                        <span class="font-medium">🇻🇳 Tiếng Việt (Vietnamese)</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('id')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="id">
                        <span class="font-medium">🇮🇩 Bahasa Indonesia</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeLanguageModal()" class="w-full px-4 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold rounded-xl transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes scale-in {
            from {
                transform: scale(0.9);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        .animate-scale-in {
            animation: scale-in 0.2s ease-out;
        }
    </style>

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
            <h1 class="text-3xl sm:text-4xl font-black text-text-main dark:text-white font-brand tracking-tight" data-translate="Visitor Registration">Visitor Registration</h1>
            <p class="text-text-sub dark:text-gray-400 text-lg max-w-2xl font-brand" data-translate="Please complete your details for secure entry verification at SafeG.">
                Please complete your details for secure entry verification at SafeG.
            </p>
        </div>

        <form action="<?= base_url('visitor-registration/submit') ?>" class="space-y-8" method="POST" id="registrationForm">
            <?= csrf_field() ?>
            <?php if (isset($token) && $token): ?>
                <input type="hidden" name="token" value="<?= esc($token) ?>">
            <?php endif; ?>

                <!-- Visit Information -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">business</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold font-brand text-text-main dark:text-white" data-translate="Visit Information">Visit Information</h2>
                            <p class="text-sm text-text-sub dark:text-gray-400 font-brand" data-translate="Where and when are you visiting?">Where and when are you visiting?</p>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand"><span data-translate="Company Visiting">Company Visiting</span> <span class="text-red-500">*</span></label>
                            <div class="bg-background-light dark:bg-background-dark p-4 rounded-lg border border-border-color dark:border-gray-700">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <?php 
                                // Use dynamic locations from database if available, otherwise fallback to hardcoded
                                if (isset($locations) && is_array($locations) && count($locations) > 0):
                                    foreach ($locations as $location): 
                                        // Display as "Branch - Location Access" to match invitation format
                                        $locationName = trim(($location['branch'] ?? '') . ' - ' . ($location['location_access'] ?? ''));
                                        $locationAccess = trim($location['location_access'] ?? '');
                                        
                                        // Check if selected - compare both full name and just location_access
                                        $isChecked = false;
                                        if (isset($selectedLocations) && is_array($selectedLocations)) {
                                            foreach ($selectedLocations as $selected) {
                                                $selected = trim($selected);
                                                // Match either full format or just location access
                                                if (strcasecmp($selected, $locationName) === 0 || strcasecmp($selected, $locationAccess) === 0) {
                                                    $isChecked = true;
                                                    break;
                                                }
                                            }
                                        }
                                ?>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="<?= esc($locationName) ?>" <?= $isChecked ? 'checked' : '' ?> class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm"><?= esc($locationName) ?></span>
                                </label>
                                <?php 
                                    endforeach;
                                else:
                                    // Fallback to hardcoded locations
                                    $fallbackLocations = [
                                        'ADMIN B', 'ADMIN D', 'AMPANG KL', 'ANNEXE BUILDING',
                                        'CFS', 'COMMON WAREHOUSE', 'EAST WHARF', 'EPIC SOLAR',
                                        'KPK GATE', 'KSB PHASE 2 GATE', 'KTSB K.TRG', 'KUALA TERENGGANU',
                                        'LCB', 'OPERATION PHASE 1', 'PHASE 1', 'PHASE 3',
                                        'PHASE 4', 'SUKMA SAMUDERA', 'TELUK KALONG', 'WH27',
                                        'WHSET WHARF', 'WORKSHOP MAINTENANCE', 'WORKSHOP PHASE 2'
                                    ];
                                    foreach ($fallbackLocations as $location): 
                                ?>
                                <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-transparent hover:border-primary/20 hover:bg-white dark:hover:bg-gray-800 transition-all">
                                    <input name="company_visiting[]" value="<?= $location ?>" class="form-checkbox rounded text-primary border-gray-300 focus:ring-primary h-5 w-5" type="checkbox"/>
                                    <span class="text-text-main dark:text-white font-medium font-brand text-sm"><?= $location ?></span>
                                </label>
                                <?php 
                                    endforeach;
                                endif;
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Date of Visit -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 overflow-hidden">
                    <div class="flex items-center justify-between px-6 sm:px-8 py-4 border-b border-border-color dark:border-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <span class="material-symbols-outlined">event</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Date of Visit</h2>
                                <p class="text-sm text-text-sub dark:text-gray-400 font-brand">When will the visit occur?</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="addDateVisit()" class="text-green-600 hover:text-green-700 transition-colors p-1.5 rounded-full hover:bg-green-50 dark:hover:bg-green-900/20" title="Add Date">
                                <span class="material-symbols-outlined text-2xl">add_circle</span>
                            </button>
                            <button type="button" onclick="removeDateVisit()" class="text-red-600 hover:text-red-700 transition-colors p-1.5 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove Date">
                                <span class="material-symbols-outlined text-2xl">remove_circle</span>
                            </button>
                        </div>
                    </div>
                    <div id="dateVisitContainer" class="p-6 sm:p-8 flex flex-col gap-6">
                        <div class="date-visit-item bg-background-light dark:bg-background-dark/50 rounded-lg p-4 border border-border-color dark:border-gray-700">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-text-main dark:text-white font-brand">Date Visit 1</h4>
                                <button type="button" onclick="removeSpecificDateVisit(this)" class="text-red-600 hover:text-red-700 transition-colors p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Delete this date visit">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date From <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input name="dates[0][date_from]" id="dateFrom0" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="dd/mm/yyyy hh:mm" type="datetime-local" value="<?= isset($schedules[0]) ? date('Y-m-d\TH:i', strtotime($schedules[0]['date_from'])) : '' ?>" required/>
                                        <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub cursor-pointer" onclick="document.getElementById('dateFrom0').showPicker()">calendar_month</span>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date To <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input name="dates[0][date_to]" id="dateTo0" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="dd/mm/yyyy hh:mm" type="datetime-local" value="<?= isset($schedules[0]) ? date('Y-m-d\TH:i', strtotime($schedules[0]['date_to'])) : '' ?>" required/>
                                        <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub cursor-pointer" onclick="document.getElementById('dateTo0').showPicker()">event</span>
                                    </div>
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
                            <h2 class="text-lg font-bold font-brand text-text-main dark:text-white" data-translate="Details of Visit">Details of Visit</h2>
                            <p class="text-sm text-text-sub dark:text-gray-400 font-brand" data-translate="Host and purpose details.">Host and purpose details.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand" data-translate="Staff ID Of Person Visited">Staff ID Of Person Visited</label>
                            <input name="staff_id" value="<?= esc($invitation['staff_id'] ?? '') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand" data-translate="Contact No Of Person Visited">Contact No Of Person Visited</label>
                            <input name="host_contact" value="<?= esc($invitation['host_contact'] ?? '') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand" data-translate="Name Of Company Visited">Name Of Company Visited</label>
                            <input name="company_visited" value="<?= esc($invitation['company_visited'] ?? '') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand" data-translate="Reason">Reason</label>
                            <input name="visit_reason" value="<?= esc($invitation['reason'] ?? '') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                    </div>
                </section>

                <!-- Person Details -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600 dark:text-green-400">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold font-brand text-text-main dark:text-white" data-translate="Person Details">Person Details</h2>
                                <p class="text-sm text-text-sub dark:text-gray-400 font-brand" data-translate="Visitor identification information.">Visitor identification information.</p>
                            </div>
                        </div>
                        <div>
                            <button type="button" onclick="openMyKadModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold uppercase shadow-lg transition-all flex items-center gap-2 font-brand">
                                <span class="material-symbols-outlined text-lg">credit_card</span>
                                Read MyKad
                            </button>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Resident <span class="text-red-500">*</span></label>
                            <select name="resident" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" required>
                                <option value="">Select...</option>
                                <option value="LOCAL">LOCAL</option>
                                <option value="FOREIGN">FOREIGN</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">IC Number <span class="text-red-500">*</span></label>
                            <input name="ic_number" value="<?= esc($invitation['ic_passport'] ?? '') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="Enter IC / Passport Number" type="text" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date of Birth <span class="text-red-500">*</span></label>
                            <input name="date_of_birth" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="date" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Sex <span class="text-red-500">*</span></label>
                            <select name="sex" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" required>
                                <option value="">Select...</option>
                                <option value="MALE">MALE</option>
                                <option value="FEMALE">FEMALE</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Full Name <span class="text-red-500">*</span></label>
                            <input name="full_name" value="<?= esc($invitation['full_name'] ?? '') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="Full name as per ID" type="text" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Contact Number <span class="text-red-500">*</span></label>
                            <input name="contact_number" value="<?= esc($invitation['contact'] ?? '') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="+60 1x-xxx xxxx" type="tel" required/>
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Email Address <span class="text-red-500">*</span></label>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <input id="visitorEmail" name="email" value="<?= esc($invitation['visitor_email'] ?? '') ?>" class="flex-1 h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="visitor@example.com" type="email" required/>
                                <button id="updateEmailBtn" class="h-12 px-6 bg-primary hover:bg-primary-hover text-white font-medium rounded-lg transition-colors whitespace-nowrap shadow-sm font-brand" type="button">
                                    Update Email
                                </button>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 1</label>
                            <input name="address_1" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 2</label>
                            <input name="address_2" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Address 3</label>
                            <input name="address_3" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">City</label>
                            <select name="city" id="citySelect" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT</option>
                                <?php if (isset($cities) && is_array($cities)): ?>
                                    <?php foreach ($cities as $city): ?>
                                        <option value="<?= esc($city['id']) ?>" data-state-id="<?= esc($city['state_id']) ?>"><?= esc($city['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">State</label>
                            <select name="state" id="stateSelect" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT</option>
                                <?php if (isset($states) && is_array($states)): ?>
                                    <?php foreach ($states as $state): ?>
                                        <option value="<?= esc($state['id']) ?>" data-country-id="<?= esc($state['country_id']) ?>"><?= esc($state['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Postal Code</label>
                            <input name="postal_code" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Country</label>
                            <select name="country" id="countrySelect" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT</option>
                                <?php if (isset($countries) && is_array($countries)): ?>
                                    <?php foreach ($countries as $country): ?>
                                        <option value="<?= esc($country['id']) ?>"><?= esc($country['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Category</label>
                            <select name="category" id="vehicleCategory" onchange="updateVehicleType()" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT</option>
                                <option value="CAR">Car</option>
                                <option value="MOTORCYCLE">Motorcycle</option>
                                <option value="TRUCK">Truck</option>
                                <option value="BUS">Bus</option>
                                <option value="VAN">Van</option>
                                <option value="HEAVY_MACHINERY">Heavy Machinery</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Type Of Vehicle</label>
                            <select name="vehicle_type" id="vehicleType" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand">
                                <option value="">SELECT CATEGORY FIRST</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Vehicle Registration Number</label>
                            <input name="vehicle_registration" value="<?= esc($invitation['vehicle_registration'] ?? '') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="e.g. ABC 1234" type="text"/>
                        </div>
                    </div>
                </section>

                <!-- Driving License Section -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 mt-8">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400">
                                    <span class="material-symbols-outlined">badge</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Driving License</h2>
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
                        <div id="licenseContainer" class="flex flex-col gap-4">
                            <div class="text-center py-8 text-text-sub dark:text-gray-400">
                                <span class="material-symbols-outlined text-5xl mb-3 block text-gray-300 dark:text-gray-600">badge</span>
                                <p class="text-sm">No licenses added yet. Click <span class="text-primary font-semibold">+</span> to add driving license.</p>
                            </div>
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
                            <input name="company_reg_id" value="<?= esc($companyRegistrationId ?? '') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Company Name</label>
                            <input name="company_name" value="<?= esc($invitation['company'] ?? '') ?>" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" type="text"/>
                        </div>
                    </div>
                </section>
                </section>

                <!-- Asset/Equipment Details Section -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 mt-8">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-full bg-cyan-50 dark:bg-cyan-900/20 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                                    <span class="material-symbols-outlined">inventory_2</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold font-brand text-text-main dark:text-white">Asset/Equipment Details</h2>
                                    <p class="text-sm text-text-sub dark:text-gray-400 font-brand">Equipment and assets being brought in.</p>
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
                        <div id="equipmentContainer" class="flex flex-col gap-6">
                            <div class="text-center py-8 text-text-sub dark:text-gray-400">
                                <span class="material-symbols-outlined text-5xl mb-3 block text-gray-300 dark:text-gray-600">inventory_2</span>
                                <p class="text-sm">No equipment added yet. Click <span class="text-primary font-semibold">+</span> to add equipment.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Document Upload -->
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8 mt-8">
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
                <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8 mt-8">
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
                            <div id="profilePhotoPreview" class="size-32 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden border-4 border-white dark:border-gray-700 shadow-lg">
                                <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600">account_circle</span>
                            </div>
                            <button id="editProfilePhoto" class="absolute bottom-0 right-0 p-2 bg-white dark:bg-gray-700 rounded-full shadow-md text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary transition-colors border border-gray-200 dark:border-gray-600" type="button">
                                <span class="material-symbols-outlined text-xl">edit</span>
                            </button>
                            <input type="file" id="profilePhotoInput" name="profile_photo" accept="image/*" class="hidden">
                        </div>
                        <div class="flex-1 space-y-4 text-center sm:text-left">
                            <div>
                                <h3 class="font-medium text-text-main dark:text-white font-brand">Upload or Capture</h3>
                                <p class="text-sm text-text-sub dark:text-gray-400 mt-1 font-brand">Please ensure your face is clearly visible. No sunglasses or hats.</p>
                            </div>
                            <div class="flex flex-wrap justify-center sm:justify-start gap-3">
                                <button id="uploadPhotoBtn" class="px-5 py-2.5 rounded-lg bg-primary hover:bg-primary-hover text-white font-medium text-sm flex items-center gap-2 transition-all shadow-lg shadow-primary/25 font-brand" type="button">
                                    <span class="material-symbols-outlined text-lg">upload</span>
                                    Upload Photo
                                </button>
                                <button id="takePhotoBtn" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-text-main dark:text-white font-medium text-sm flex items-center gap-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all font-brand" type="button">
                                    <span class="material-symbols-outlined text-lg">camera_alt</span>
                                    Take Photo
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

            <!-- Sticky Footer with Actions -->
            <div class="sticky bottom-4 z-40 mt-8 mx-auto max-w-4xl">
                <div class="bg-surface-light/95 dark:bg-surface-dark/95 border border-border-color dark:border-gray-800 rounded-2xl shadow-2xl backdrop-blur-sm p-4 sm:px-6 sm:py-5">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2 text-xs text-text-sub dark:text-gray-500 font-brand">
                            <span class="material-symbols-outlined text-base">lock</span>
                            <span>Your data is securely encrypted with 256-bit SSL.</span>
                        </div>
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <button class="flex-1 sm:flex-none px-6 py-3 rounded-xl border border-border-color dark:border-gray-700 text-text-main dark:text-gray-300 font-semibold hover:bg-background-light dark:hover:bg-gray-800 transition-all font-brand" type="button" onclick="window.history.back()">
                                Cancel
                            </button>
                            <button class="flex-1 sm:flex-none px-8 py-3 rounded-xl bg-primary hover:bg-primary-hover text-white font-bold shadow-lg shadow-primary/30 flex items-center justify-center gap-2 transition-all transform hover:scale-[1.02] font-brand" type="submit">
                                Next
                                <span class="material-symbols-outlined">arrow_forward</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- MyKad Upload Modal -->
        <div id="mykadModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" onclick="closeMyKadModal()"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-surface-light dark:bg-surface-dark rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-border-color dark:border-gray-700">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-border-color dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">credit_card</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-text-main dark:text-white font-brand">Read MyKad</h3>
                                <p class="text-sm text-text-sub dark:text-gray-400">Upload your IC card image</p>
                            </div>
                        </div>
                        <button type="button" onclick="closeMyKadModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-6">
                        <!-- Drag and Drop Area -->
                        <div id="dropZone" class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-primary hover:bg-primary/5 transition-all cursor-pointer">
                            <input type="file" id="mykadFileInput" accept="image/*" class="hidden" onchange="handleMyKadUpload(event)"/>
                            
                            <div class="space-y-4">
                                <div class="w-16 h-16 mx-auto rounded-full bg-primary/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-4xl text-primary">upload_file</span>
                                </div>
                                
                                <div>
                                    <p class="text-base font-semibold text-text-main dark:text-white mb-1">
                                        Drop your MyKad image here
                                    </p>
                                    <p class="text-sm text-text-sub dark:text-gray-400">
                                        or <button type="button" onclick="document.getElementById('mykadFileInput').click()" class="text-primary hover:text-primary-hover font-semibold underline">browse files</button>
                                    </p>
                                </div>

                                <div class="flex items-center justify-center gap-2 text-xs text-text-sub dark:text-gray-500">
                                    <span class="material-symbols-outlined text-sm">info</span>
                                    <span>Supports JPG, PNG (Max 5MB)</span>
                                </div>
                            </div>

                            <!-- Drag overlay -->
                            <div id="dragOverlay" class="hidden absolute inset-0 bg-primary/10 border-2 border-primary rounded-xl flex items-center justify-center">
                                <div class="text-center">
                                    <span class="material-symbols-outlined text-5xl text-primary mb-2">cloud_upload</span>
                                    <p class="text-primary font-semibold">Drop to upload</p>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Area -->
                        <div id="imagePreview" class="hidden mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                            <div class="flex items-center gap-3">
                                <img id="previewImage" src="" alt="Preview" class="w-20 h-20 object-cover rounded-lg"/>
                                <div class="flex-1">
                                    <p id="fileName" class="text-sm font-medium text-text-main dark:text-white"></p>
                                    <p id="fileSize" class="text-xs text-text-sub dark:text-gray-400"></p>
                                </div>
                                <button type="button" onclick="clearPreview()" class="text-red-500 hover:text-red-600">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeMyKadModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="button" id="processButton" onclick="processMyKadImage()" disabled class="px-5 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">auto_fix_high</span>
                            Process MyKad
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Camera Modal for Profile Photo -->
        <div id="cameraModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" onclick="closeCameraModal()"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-surface-light dark:bg-surface-dark rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-border-color dark:border-gray-700">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-border-color dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-teal-600 dark:text-teal-400">photo_camera</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-text-main dark:text-white font-brand">Take Photo</h3>
                                <p class="text-sm text-text-sub dark:text-gray-400">Capture your profile photo</p>
                            </div>
                        </div>
                        <button type="button" onclick="closeCameraModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-6">
                        <div class="relative bg-gray-900 rounded-xl overflow-hidden">
                            <video id="cameraStream" autoplay playsinline class="w-full h-96 object-cover"></video>
                            <canvas id="cameraCanvas" class="hidden"></canvas>
                            
                            <!-- Camera preview overlay -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="w-64 h-64 border-4 border-white/50 rounded-full"></div>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-center gap-2 text-sm text-text-sub dark:text-gray-400">
                            <span class="material-symbols-outlined text-base">info</span>
                            <span>Position your face within the circle</span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-center gap-3">
                        <button type="button" onclick="closeCameraModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="button" id="captureButton" onclick="capturePhoto()" class="px-6 py-2.5 text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-lg transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">camera</span>
                            Capture Photo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Profile Photo functionality
        let cameraStream = null;
        let profilePhotoBlob = null;

        // Upload Photo button
        document.getElementById('uploadPhotoBtn').addEventListener('click', function() {
            document.getElementById('profilePhotoInput').click();
        });

        // Edit button
        document.getElementById('editProfilePhoto').addEventListener('click', function() {
            document.getElementById('profilePhotoInput').click();
        });

        // Handle file selection
        document.getElementById('profilePhotoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file');
                    return;
                }

                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB');
                    return;
                }

                // Preview the image
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('profilePhotoPreview');
                    preview.innerHTML = `<img src="${event.target.result}" alt="Profile" class="w-full h-full object-cover">`;
                    
                    // Store blob for upload
                    fetch(event.target.result)
                        .then(res => res.blob())
                        .then(blob => {
                            profilePhotoBlob = blob;
                        });
                };
                reader.readAsDataURL(file);
            }
        });

        // Take Photo button
        document.getElementById('takePhotoBtn').addEventListener('click', function() {
            openCameraModal();
        });

        // Handle Government ID file upload preview
        document.querySelector('input[name="government_id"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB');
                    this.value = '';
                    return;
                }

                // Show file name preview
                const parent = this.closest('.group');
                let preview = parent.querySelector('.file-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.className = 'file-preview absolute inset-0 bg-green-50 dark:bg-green-900/20 rounded-xl border-2 border-green-500 flex items-center justify-center p-4';
                    parent.appendChild(preview);
                }
                
                preview.innerHTML = `
                    <div class="text-center">
                        <span class="material-symbols-outlined text-4xl text-green-600 mb-2">check_circle</span>
                        <p class="text-sm font-semibold text-green-700 dark:text-green-400 font-brand">${file.name}</p>
                        <p class="text-xs text-green-600 dark:text-green-500 mt-1 font-brand">${(file.size / 1024).toFixed(1)} KB</p>
                        <button type="button" class="mt-2 text-xs text-red-600 hover:text-red-800 font-brand" onclick="clearFileInput(this, 'government_id')">Remove</button>
                    </div>
                `;
            }
        });

        // Handle Invitation Letter file upload preview
        document.querySelector('input[name="invitation_letter"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB');
                    this.value = '';
                    return;
                }

                // Show file name preview
                const parent = this.closest('.group');
                let preview = parent.querySelector('.file-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.className = 'file-preview absolute inset-0 bg-green-50 dark:bg-green-900/20 rounded-xl border-2 border-green-500 flex items-center justify-center p-4';
                    parent.appendChild(preview);
                }
                
                preview.innerHTML = `
                    <div class="text-center">
                        <span class="material-symbols-outlined text-4xl text-green-600 mb-2">check_circle</span>
                        <p class="text-sm font-semibold text-green-700 dark:text-green-400 font-brand">${file.name}</p>
                        <p class="text-xs text-green-600 dark:text-green-500 mt-1 font-brand">${(file.size / 1024).toFixed(1)} KB</p>
                        <button type="button" class="mt-2 text-xs text-red-600 hover:text-red-800 font-brand" onclick="clearFileInput(this, 'invitation_letter')">Remove</button>
                    </div>
                `;
            }
        });

        // Function to clear file input
        window.clearFileInput = function(button, inputName) {
            const input = document.querySelector(`input[name="${inputName}"]`);
            input.value = '';
            const preview = button.closest('.file-preview');
            if (preview) {
                preview.remove();
            }
        };

        // Open camera modal
        function openCameraModal() {
            document.getElementById('cameraModal').classList.remove('hidden');
            startCamera();
        }

        // Close camera modal
        function closeCameraModal() {
            stopCamera();
            document.getElementById('cameraModal').classList.add('hidden');
        }

        // Start camera
        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'user',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    } 
                });
                cameraStream = stream;
                document.getElementById('cameraStream').srcObject = stream;
            } catch (error) {
                console.error('Camera access error:', error);
                alert('Unable to access camera. Please check permissions or use Upload Photo instead.');
                closeCameraModal();
            }
        }

        // Stop camera
        function stopCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
        }

        // Capture photo
        function capturePhoto() {
            const video = document.getElementById('cameraStream');
            const canvas = document.getElementById('cameraCanvas');
            const context = canvas.getContext('2d');

            // Set canvas size to video size
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            // Draw video frame to canvas
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert to blob and display
            canvas.toBlob(function(blob) {
                profilePhotoBlob = blob;
                
                // Create preview URL
                const url = URL.createObjectURL(blob);
                const preview = document.getElementById('profilePhotoPreview');
                preview.innerHTML = `<img src="${url}" alt="Profile" class="w-full h-full object-cover">`;
                
                // Create file for input
                const file = new File([blob], 'profile_photo.jpg', { type: 'image/jpeg' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                document.getElementById('profilePhotoInput').files = dataTransfer.files;
                
                closeCameraModal();
            }, 'image/jpeg', 0.9);
        }

        // Date Visit dynamic addition
        let dateVisitCount = 1;
        function addDateVisit() {
            const container = document.getElementById('dateVisitContainer');
            const items = container.querySelectorAll('.date-visit-item');
            const newIndex = items.length;
            
            const html = `
                <div class="date-visit-item bg-background-light dark:bg-background-dark/50 rounded-lg p-4 border border-border-color dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-text-main dark:text-white font-brand">Date Visit ${newIndex + 1}</h4>
                        <button type="button" onclick="removeSpecificDateVisit(this)" class="text-red-600 hover:text-red-700 transition-colors p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Delete this date visit">
                            <span class="material-symbols-outlined text-xl">delete</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date From <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input name="dates[${dateVisitCount}][date_from]" id="dateFrom${dateVisitCount}" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="dd/mm/yyyy hh:mm" type="datetime-local" required/>
                                <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub cursor-pointer" onclick="document.getElementById('dateFrom${dateVisitCount}').showPicker()">calendar_month</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-text-main dark:text-gray-200 font-brand">Date To <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input name="dates[${dateVisitCount}][date_to]" id="dateTo${dateVisitCount}" class="w-full h-12 rounded-lg border-border-color dark:border-gray-700 bg-background-light dark:bg-background-dark text-text-main dark:text-white px-4 pr-12 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-brand" placeholder="dd/mm/yyyy hh:mm" type="datetime-local" required/>
                                <span class="material-symbols-outlined absolute right-4 top-3 text-text-sub cursor-pointer" onclick="document.getElementById('dateTo${dateVisitCount}').showPicker()">event</span>
                            </div>
                        </div>
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
                updateDateVisitNumbers();
            }
        }

        function removeSpecificDateVisit(button) {
            const container = document.getElementById('dateVisitContainer');
            const items = container.querySelectorAll('.date-visit-item');
            
            if (items.length > 1) {
                const item = button.closest('.date-visit-item');
                item.remove();
                updateDateVisitNumbers();
            } else {
                alert('At least one date visit entry is required.');
            }
        }

        function updateDateVisitNumbers() {
            const container = document.getElementById('dateVisitContainer');
            const items = container.querySelectorAll('.date-visit-item');
            items.forEach((item, index) => {
                const header = item.querySelector('h4');
                if (header) {
                    header.textContent = `Date Visit ${index + 1}`;
                }
            });
        }

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

        // Vehicle type mapping
        const vehicleTypes = {
            'CAR': [
                { value: 'SEDAN', label: 'Sedan' },
                { value: 'HATCHBACK', label: 'Hatchback' },
                { value: 'SUV', label: 'SUV' },
                { value: 'COUPE', label: 'Coupe' }
            ],
            'TRUCK': [
                { value: 'PICKUP', label: 'Pickup' },
                { value: 'LORRY', label: 'Lorry' },
                { value: 'TRAILER', label: 'Trailer' }
            ],
            'MOTORCYCLE': [
                { value: 'SCOOTER', label: 'Scooter' },
                { value: 'SPORT_BIKE', label: 'Sport bike' },
                { value: 'CRUISER', label: 'Cruiser' }
            ],
            'BUS': [
                { value: 'MINI_BUS', label: 'Mini Bus' },
                { value: 'COACH', label: 'Coach' },
                { value: 'SCHOOL_BUS', label: 'School Bus' }
            ],
            'VAN': [
                { value: 'CARGO_VAN', label: 'Cargo Van' },
                { value: 'PASSENGER_VAN', label: 'Passenger Van' },
                { value: 'MINIVAN', label: 'Minivan' }
            ],
            'HEAVY_MACHINERY': [
                { value: 'EXCAVATOR', label: 'Excavator' },
                { value: 'BULLDOZER', label: 'Bulldozer' },
                { value: 'CRANE', label: 'Crane' },
                { value: 'FORKLIFT', label: 'Forklift' }
            ]
        };

        function updateVehicleType() {
            const category = document.getElementById('vehicleCategory').value;
            const vehicleTypeSelect = document.getElementById('vehicleType');
            
            // Clear existing options
            vehicleTypeSelect.innerHTML = '';
            
            if (!category) {
                vehicleTypeSelect.innerHTML = '<option value="">SELECT CATEGORY FIRST</option>';
                return;
            }
            
            // Add default option
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'SELECT TYPE';
            vehicleTypeSelect.appendChild(defaultOption);
            
            // Add category-specific options
            if (vehicleTypes[category]) {
                vehicleTypes[category].forEach(type => {
                    const option = document.createElement('option');
                    option.value = type.value;
                    option.textContent = type.label;
                    vehicleTypeSelect.appendChild(option);
                });
            }
        }

        // Cascading dropdown: Bidirectional Country <-> State <-> City
        const allStates = Array.from(document.querySelectorAll('#stateSelect option')).slice(1); // Skip first "SELECT" option
        const allCities = Array.from(document.querySelectorAll('#citySelect option')).slice(1); // Skip first "SELECT" option
        
        // Filter states based on selected country (top-down)
        document.getElementById('countrySelect').addEventListener('change', function() {
            const countryId = this.value;
            const stateSelect = document.getElementById('stateSelect');
            const citySelect = document.getElementById('citySelect');
            
            // Reset state and city
            stateSelect.innerHTML = '<option value="">SELECT</option>';
            citySelect.innerHTML = '<option value="">SELECT STATE FIRST</option>';
            
            if (!countryId) {
                return;
            }
            
            // Filter and add states for selected country
            allStates.forEach(option => {
                if (option.dataset.countryId === countryId) {
                    stateSelect.appendChild(option.cloneNode(true));
                }
            });
        });
        
        // Filter cities based on selected state (top-down)
        // AND auto-select country when state is selected (bottom-up)
        document.getElementById('stateSelect').addEventListener('change', function() {
            const stateId = this.value;
            const citySelect = document.getElementById('citySelect');
            const countrySelect = document.getElementById('countrySelect');
            
            // Reset city
            citySelect.innerHTML = '<option value="">SELECT</option>';
            
            if (!stateId) {
                citySelect.innerHTML = '<option value="">SELECT STATE FIRST</option>';
                return;
            }
            
            // Auto-select country based on selected state (bottom-up)
            const selectedStateOption = allStates.find(opt => opt.value === stateId);
            if (selectedStateOption) {
                const countryId = selectedStateOption.dataset.countryId;
                if (countryId) {
                    countrySelect.value = countryId;
                }
            }
            
            // Filter and add cities for selected state
            allCities.forEach(option => {
                if (option.dataset.stateId === stateId) {
                    citySelect.appendChild(option.cloneNode(true));
                }
            });
        });
        
        // Auto-select state and country when city is selected (bottom-up)
        document.getElementById('citySelect').addEventListener('change', function() {
            const cityId = this.value;
            
            if (!cityId) {
                return;
            }
            
            // Find the selected city option from allCities
            const selectedCityOption = allCities.find(opt => opt.value === cityId);
            if (selectedCityOption) {
                const stateId = selectedCityOption.dataset.stateId;
                
                if (stateId) {
                    const stateSelect = document.getElementById('stateSelect');
                    const countrySelect = document.getElementById('countrySelect');
                    
                    // Find the state from allStates
                    const selectedStateOption = allStates.find(opt => opt.value === stateId);
                    
                    if (selectedStateOption) {
                        const countryId = selectedStateOption.dataset.countryId;
                        
                        // First select country
                        if (countryId) {
                            countrySelect.value = countryId;
                            // Trigger country change to populate states
                            countrySelect.dispatchEvent(new Event('change'));
                            
                            // Then select state (after a small delay to let states populate)
                            setTimeout(() => {
                                stateSelect.value = stateId;
                                // Trigger state change to populate cities
                                stateSelect.dispatchEvent(new Event('change'));
                                
                                // Restore city selection
                                setTimeout(() => {
                                    document.getElementById('citySelect').value = cityId;
                                }, 50);
                            }, 50);
                        }
                    }
                }
            }
        });

        // Update Email Handler
        document.getElementById('updateEmailBtn').addEventListener('click', async function() {
            const emailInput = document.getElementById('visitorEmail');
            const newEmail = emailInput.value.trim();
            
            if (!newEmail) {
                alert('Please enter an email address');
                emailInput.focus();
                return;
            }

            // Basic email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(newEmail)) {
                alert('Please enter a valid email address');
                emailInput.focus();
                return;
            }

            // Confirm update
            if (!confirm('Are you sure you want to update your email address to: ' + newEmail + '?')) {
                return;
            }

            const updateBtn = this;
            const originalText = updateBtn.innerHTML;
            updateBtn.disabled = true;
            updateBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">progress_activity</span> Updating...';

            try {
                const formData = new FormData();
                formData.append('email', newEmail);
                formData.append('token', '<?= base64_encode($invitationId ?? '') ?>');

                const response = await fetch('<?= base_url('visitor-registration/updateEmail') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert('Email updated successfully!');
                    emailInput.value = result.email;
                } else {
                    alert('Failed to update email: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Email update error:', error);
                alert('An error occurred while updating email. Please try again.');
            } finally {
                updateBtn.disabled = false;
                updateBtn.innerHTML = originalText;
            }
        });

        // Form submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate required fields
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalidField = null;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            // Validate company visiting checkboxes
            const companyCheckboxes = this.querySelectorAll('input[name="company_visiting[]"]:checked');
            if (companyCheckboxes.length === 0) {
                isValid = false;
                alert('Please select at least one company to visit');
                return;
            }

            // Validate date visits
            const dateVisits = this.querySelectorAll('.date-visit-item');
            if (dateVisits.length === 0) {
                isValid = false;
                alert('Please add at least one visit date');
                return;
            }

            if (!isValid) {
                alert('Please fill in all required fields');
                if (firstInvalidField) {
                    firstInvalidField.focus();
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }
            
            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="material-symbols-outlined animate-spin">progress_activity</span> Submitting...';
            
            // Form validation and submission logic here
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Registration submitted successfully!');
                    // Redirect to security briefing
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    alert('Error: ' + (data.message || 'Please check your inputs'));
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                    
                    // Display validation errors if any
                    if (data.errors) {
                        let errorMsg = 'Validation errors:\n';
                        for (let field in data.errors) {
                            errorMsg += '- ' + data.errors[field] + '\n';
                        }
                        alert(errorMsg);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });

        // MyKad Modal Functions
        let selectedFile = null;

        function openMyKadModal() {
            document.getElementById('mykadModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeMyKadModal() {
            document.getElementById('mykadModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            clearPreview();
        }

        function clearPreview() {
            selectedFile = null;
            document.getElementById('imagePreview').classList.add('hidden');
            document.getElementById('mykadFileInput').value = '';
            document.getElementById('processButton').disabled = true;
        }

        // Drag and Drop Handlers
        const dropZone = document.getElementById('dropZone');
        const dragOverlay = document.getElementById('dragOverlay');

        dropZone.addEventListener('click', () => {
            document.getElementById('mykadFileInput').click();
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dragOverlay.classList.remove('hidden');
        });

        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dragOverlay.classList.add('hidden');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dragOverlay.classList.add('hidden');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelect(files[0]);
            }
        });

        function handleFileSelect(file) {
            if (!file.type.startsWith('image/')) {
                alert('Please upload an image file');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                return;
            }

            selectedFile = file;

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('previewImage').src = e.target.result;
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(2) + ' KB';
                document.getElementById('imagePreview').classList.remove('hidden');
                document.getElementById('processButton').disabled = false;
            };
            reader.readAsDataURL(file);
        }

        // MyKad OCR Upload and Processing
        function handleMyKadUpload(event) {
            const file = event.target.files[0];
            if (file) {
                handleFileSelect(file);
            }
        }

        async function processMyKadImage() {
            if (!selectedFile) return;

            const processButton = document.getElementById('processButton');
            const originalText = processButton.innerHTML;
            processButton.disabled = true;
            processButton.innerHTML = '<span class="material-symbols-outlined animate-spin">progress_activity</span> Processing...';

            try {
                const formData = new FormData();
                formData.append('mykad_image', selectedFile);

                const response = await fetch('<?= base_url('visitor-registration/processMyKad') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const result = await response.json();
                console.log('MyKad Response:', result);

                if (result.success && result.data) {
                    const data = result.data;
                    console.log('Extracted Data:', data);
                    
                    // Show raw OCR text and quality for debugging
                    if (data.raw_ocr_text) {
                        console.log('Raw OCR Text:', data.raw_ocr_text);
                        console.log('OCR Quality:', data.ocr_quality || 'unknown');
                    }
                    
                    let fieldsUpdated = 0;
                    let qualityMessage = '';
                    
                    if (data.ocr_quality === 'poor' || data.ocr_quality === 'failed') {
                        qualityMessage = '\n\n⚠️ Image quality detected as POOR. For better results:\n- Ensure good lighting\n- Keep MyKad flat (not tilted)\n- Focus clearly on text\n- Take photo from directly above';
                    }
                    
                    // Fill IC Number
                    if (data.ic_number) {
                        const icField = document.querySelector('input[name="ic_number"]');
                        if (icField) {
                            icField.value = data.ic_number;
                            console.log('IC filled:', data.ic_number);
                            fieldsUpdated++;
                        }
                    }
                    
                    // Fill Name
                    if (data.name) {
                        const nameField = document.querySelector('input[name="full_name"]');
                        if (nameField) {
                            nameField.value = data.name;
                            console.log('Name filled:', data.name);
                            fieldsUpdated++;
                        }
                    }
                    
                    // Fill Date of Birth
                    if (data.date_of_birth) {
                        const dobField = document.querySelector('input[name="date_of_birth"]');
                        if (dobField) {
                            dobField.value = data.date_of_birth;
                            console.log('DOB filled:', data.date_of_birth);
                            fieldsUpdated++;
                        }
                    }
                    
                    // Fill Sex
                    if (data.sex) {
                        const sexField = document.querySelector('select[name="sex"]');
                        if (sexField) {
                            sexField.value = data.sex.toUpperCase();
                            console.log('Sex filled:', data.sex);
                            fieldsUpdated++;
                        }
                    }
                    
                    // Fill Address
                    if (data.address) {
                        const addressParts = data.address.split('\n').filter(a => a.trim());
                        if (addressParts[0]) {
                            const addr1 = document.querySelector('input[name="address_1"]');
                            if (addr1) {
                                addr1.value = addressParts[0];
                                fieldsUpdated++;
                            }
                        }
                        if (addressParts[1]) {
                            const addr2 = document.querySelector('input[name="address_2"]');
                            if (addr2) addr2.value = addressParts[1];
                        }
                        if (addressParts[2]) {
                            const addr3 = document.querySelector('input[name="address_3"]');
                            if (addr3) addr3.value = addressParts[2];
                        }
                        console.log('Address filled:', addressParts);
                    }
                    
                    // Fill Postcode
                    if (data.postcode) {
                        const postcodeField = document.querySelector('input[name="postal_code"]');
                        if (postcodeField) {
                            postcodeField.value = data.postcode;
                            console.log('Postcode filled:', data.postcode);
                            fieldsUpdated++;
                        }
                    }
                    
                    // Fill State first (needed to load cities)
                    if (data.state) {
                        const stateField = document.querySelector('select[name="state"]');
                        if (stateField) {
                            // Try to find matching option by text content
                            const options = Array.from(stateField.options);
                            const matchingOption = options.find(opt => opt.text.toUpperCase().includes(data.state.toUpperCase()));
                            if (matchingOption) {
                                stateField.value = matchingOption.value;
                                console.log('State filled:', data.state);
                                fieldsUpdated++;
                                
                                // Trigger change event to load cities and wait for it
                                stateField.dispatchEvent(new Event('change'));
                                
                                // Wait for cities to load, then set city
                                if (data.city) {
                                    setTimeout(() => {
                                        const cityField = document.querySelector('select[name="city"]');
                                        if (cityField) {
                                            const cityOptions = Array.from(cityField.options);
                                            
                                            // Try exact match first
                                            let matchingCityOption = cityOptions.find(opt => opt.text.toUpperCase() === data.city.toUpperCase());
                                            
                                            // Try partial match (OCR might have extra chars)
                                            if (!matchingCityOption) {
                                                matchingCityOption = cityOptions.find(opt => 
                                                    opt.text.toUpperCase().includes(data.city.toUpperCase()) || 
                                                    data.city.toUpperCase().includes(opt.text.toUpperCase())
                                                );
                                            }
                                            
                                            // Try fuzzy match (remove last few chars for OCR errors)
                                            if (!matchingCityOption && data.city.length > 5) {
                                                const cityPrefix = data.city.substring(0, data.city.length - 2).toUpperCase();
                                                matchingCityOption = cityOptions.find(opt => 
                                                    opt.text.toUpperCase().startsWith(cityPrefix) ||
                                                    cityPrefix.startsWith(opt.text.toUpperCase())
                                                );
                                            }
                                            
                                            if (matchingCityOption) {
                                                cityField.value = matchingCityOption.value;
                                                console.log('City filled (after state load):', data.city, '→', matchingCityOption.text);
                                                fieldsUpdated++;
                                            } else {
                                                console.warn('City not found in dropdown after state load:', data.city);
                                            }
                                        }
                                    }, 1000); // Wait 1 second for cities to load
                                }
                            }
                        }
                    }
                    
                    // Fill Country (Malaysia for local IC)
                    if (data.country) {
                        const countryField = document.querySelector('select[name="country"]');
                        if (countryField) {
                            const options = Array.from(countryField.options);
                            const matchingOption = options.find(opt => opt.text.toUpperCase().includes(data.country.toUpperCase()));
                            if (matchingOption) {
                                countryField.value = matchingOption.value;
                                console.log('Country filled:', data.country);
                                fieldsUpdated++;
                            }
                        }
                    }
                    
                    // Set Resident to LOCAL
                    const residentField = document.querySelector('select[name="resident"]');
                    if (residentField) {
                        residentField.value = 'LOCAL';
                        console.log('Resident set to LOCAL');
                    }
                    
                    closeMyKadModal();
                    
                    if (fieldsUpdated > 0) {
                        alert(`MyKad data extracted! ${fieldsUpdated} field(s) filled. Please verify and complete any missing information.${qualityMessage}`);
                    } else {
                        alert('Could not extract data from the image. The image quality is too poor or the angle is not clear. Please take a clearer photo:\n\n✓ Good lighting\n✓ MyKad flat on surface\n✓ Photo from directly above\n✓ All text in focus\n\nOr enter details manually.');
                    }
                } else {
                    console.error('MyKad extraction failed:', result);
                    closeMyKadModal();
                    alert('Could not read MyKad image. Please ensure:\n- Image is clear and well-lit\n- MyKad is flat and not tilted\n- All text is visible and in focus\n\nYou can enter details manually.');
                }
            } catch (error) {
                console.error('MyKad processing error:', error);
                alert('An error occurred. Please enter manually.');
            } finally {
                processButton.disabled = false;
                processButton.innerHTML = originalText;
            }
        }

        // Help and Language functions
        function showHelp() {
            document.getElementById('helpModal').classList.remove('hidden');
        }

        function closeHelpModal() {
            document.getElementById('helpModal').classList.add('hidden');
        }

        function showLanguageMenu() {
            const currentLang = localStorage.getItem('selectedLanguage') || 'en';
            document.querySelectorAll('.language-option').forEach(btn => {
                const checkIcon = btn.querySelector('.material-symbols-outlined');
                if (btn.dataset.lang === currentLang) {
                    checkIcon.classList.remove('hidden');
                    btn.classList.add('bg-blue-50', 'dark:bg-blue-900/20');
                } else {
                    checkIcon.classList.add('hidden');
                    btn.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
                }
            });
            document.getElementById('languageModal').classList.remove('hidden');
        }

        function closeLanguageModal() {
            document.getElementById('languageModal').classList.add('hidden');
        }

        function changeLanguage(langCode) {
            localStorage.setItem('selectedLanguage', langCode);
            const langNames = {
                'en': 'English', 'ms': 'Bahasa Malaysia', 'zh-CN': '中文', 'zh-TW': '繁體中文',
                'ta': 'தமிழ்', 'hi': 'हिन्दी', 'ja': '日本語', 'ko': '한국어',
                'th': 'ภาษาไทย', 'vi': 'Tiếng Việt', 'id': 'Bahasa Indonesia'
            };
            
            // Update language display in header
            document.getElementById('currentLang').textContent = langNames[langCode];
            
            // Translate page content
            translatePage(langCode);
            
            closeLanguageModal();
            const notification = document.createElement('div');
            notification.className = 'fixed top-20 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 animate-slide-in';
            notification.textContent = `Language changed to ${langNames[langCode]}`;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

        // Translation function
        function translatePage(lang) {
            const originalTexts = {
                'Visitor Registration': 'Visitor Registration',
                'Please complete your details for secure entry verification at SafeG.': 'Please complete your details for secure entry verification at SafeG.',
                'Visit Information': 'Visit Information',
                'Where and when are you visiting?': 'Where and when are you visiting?',
                'Company Visiting': 'Company Visiting',
                'Details of Visit': 'Details of Visit',
                'Host and purpose details.': 'Host and purpose details.',
                'Staff ID Of Person Visited': 'Staff ID Of Person Visited',
                'Contact No Of Person Visited': 'Contact No Of Person Visited',
                'Name Of Company Visited': 'Name Of Company Visited',
                'Reason': 'Reason',
                'Person Details': 'Person Details',
                'Visitor identification information.': 'Visitor identification information.'
            };

            const translations = {
                'ms': {
                    'Visitor Registration': 'Pendaftaran Pelawat',
                    'Please complete your details for secure entry verification at SafeG.': 'Sila lengkapkan butiran anda untuk pengesahan kemasukan selamat di SafeG.',
                    'Visit Information': 'Maklumat Lawatan',
                    'Where and when are you visiting?': 'Ke mana dan bila anda melawat?',
                    'Company Visiting': 'Syarikat Yang Dilawati',
                    'Details of Visit': 'Butiran Lawatan',
                    'Host and purpose details.': 'Butiran tuan rumah dan tujuan.',
                    'Staff ID Of Person Visited': 'ID Kakitangan Yang Dilawati',
                    'Contact No Of Person Visited': 'No. Perhubungan Yang Dilawati',
                    'Name Of Company Visited': 'Nama Syarikat Yang Dilawati',
                    'Reason': 'Sebab',
                    'Person Details': 'Butiran Diri',
                    'Visitor identification information.': 'Maklumat pengenalan pelawat.'
                },
                'zh-CN': {
                    'Visitor Registration': '访客登记',
                    'Please complete your details for secure entry verification at SafeG.': '请填写您的详细信息以进行SafeG的安全入境验证。',
                    'Visit Information': '访问信息',
                    'Where and when are you visiting?': '您要访问哪里和什么时候？',
                    'Company Visiting': '访问公司',
                    'Details of Visit': '访问详情',
                    'Host and purpose details.': '主人和目的详情。',
                    'Staff ID Of Person Visited': '被访者员工编号',
                    'Contact No Of Person Visited': '被访者联系电话',
                    'Name Of Company Visited': '被访公司名称',
                    'Reason': '原因',
                    'Person Details': '个人详情',
                    'Visitor identification information.': '访客身份信息。'
                },
                'zh-TW': {
                    'Visitor Registration': '訪客登記',
                    'Please complete your details for secure entry verification at SafeG.': '請填寫您的詳細資訊以進行SafeG的安全入境驗證。',
                    'Visit Information': '訪問資訊',
                    'Where and when are you visiting?': '您要訪問哪裡和什麼時候？',
                    'Company Visiting': '訪問公司',
                    'Details of Visit': '訪問詳情',
                    'Host and purpose details.': '主人和目的詳情。',
                    'Staff ID Of Person Visited': '被訪者員工編號',
                    'Contact No Of Person Visited': '被訪者聯絡電話',
                    'Name Of Company Visited': '被訪公司名稱',
                    'Reason': '原因',
                    'Person Details': '個人詳情',
                    'Visitor identification information.': '訪客身份資訊。'
                },
                'ta': {
                    'Visitor Registration': 'பார்வையாளர் பதிவு',
                    'Please complete your details for secure entry verification at SafeG.': 'SafeG இல் பாதுகாப்பான நுழைவு சரிபார்ப்புக்கு உங்கள் விவரங்களை முடிக்கவும்.',
                    'Visit Information': 'வருகை தகவல்',
                    'Where and when are you visiting?': 'நீங்கள் எங்கு மற்றும் எப்போது வருகிறீர்கள்?',
                    'Company Visiting': 'வருகை நிறுவனம்',
                    'Details of Visit': 'வருகை விவரங்கள்',
                    'Host and purpose details.': 'புரவலன் மற்றும் நோக்கம் விவரங்கள்.',
                    'Staff ID Of Person Visited': 'சந்திக்கப்பட்ட நபரின் ஊழியர் அடையாள எண்',
                    'Contact No Of Person Visited': 'சந்திக்கப்பட்ட நபரின் தொடர்பு எண்',
                    'Name Of Company Visited': 'சந்திக்கப்பட்ட நிறுவனத்தின் பெயர்',
                    'Reason': 'காரணம்',
                    'Person Details': 'நபர் விவரங்கள்',
                    'Visitor identification information.': 'பார்வையாளர் அடையாள தகவல்.'
                },
                'hi': {
                    'Visitor Registration': 'आगंतुक पंजीकरण',
                    'Please complete your details for secure entry verification at SafeG.': 'SafeG में सुरक्षित प्रवेश सत्यापन के लिए कृपया अपना विवरण पूरा करें।',
                    'Visit Information': 'यात्रा जानकारी',
                    'Where and when are you visiting?': 'आप कहाँ और कब जा रहे हैं?',
                    'Company Visiting': 'कंपनी का दौरा',
                    'Details of Visit': 'यात्रा का विवरण',
                    'Host and purpose details.': 'मेजबान और उद्देश्य विवरण।',
                    'Staff ID Of Person Visited': 'मिले व्यक्ति की स्टाफ आईडी',
                    'Contact No Of Person Visited': 'मिले व्यक्ति का संपर्क नंबर',
                    'Name Of Company Visited': 'मिली कंपनी का नाम',
                    'Reason': 'कारण',
                    'Person Details': 'व्यक्ति विवरण',
                    'Visitor identification information.': 'आगंतुक पहचान जानकारी।'
                },
                'ja': {
                    'Visitor Registration': '訪問者登録',
                    'Please complete your details for secure entry verification at SafeG.': 'SafeGでの安全な入場確認のため、詳細を記入してください。',
                    'Visit Information': '訪問情報',
                    'Where and when are you visiting?': 'どこへいつ訪問しますか？',
                    'Company Visiting': '訪問先企業',
                    'Details of Visit': '訪問詳細',
                    'Host and purpose details.': 'ホストと目的の詳細。',
                    'Staff ID Of Person Visited': '訪問先担当者のスタッフID',
                    'Contact No Of Person Visited': '訪問先担当者の連絡先',
                    'Name Of Company Visited': '訪問先企業名',
                    'Reason': '理由',
                    'Person Details': '個人詳細',
                    'Visitor identification information.': '訪問者識別情報。'
                },
                'ko': {
                    'Visitor Registration': '방문자 등록',
                    'Please complete your details for secure entry verification at SafeG.': 'SafeG의 안전한 입장 확인을 위해 세부 정보를 작성해 주세요.',
                    'Visit Information': '방문 정보',
                    'Where and when are you visiting?': '어디를 언제 방문하십니까?',
                    'Company Visiting': '방문 회사',
                    'Details of Visit': '방문 세부정보',
                    'Host and purpose details.': '호스트 및 목적 세부정보.',
                    'Staff ID Of Person Visited': '방문 대상자 직원 ID',
                    'Contact No Of Person Visited': '방문 대상자 연락처',
                    'Name Of Company Visited': '방문 회사명',
                    'Reason': '이유',
                    'Person Details': '개인 정보',
                    'Visitor identification information.': '방문자 신원 정보.'
                },
                'th': {
                    'Visitor Registration': 'การลงทะเบียนผู้เยี่ยมชม',
                    'Please complete your details for secure entry verification at SafeG.': 'โปรดกรอกรายละเอียดของคุณเพื่อการยืนยันการเข้าที่ปลอดภัยที่ SafeG',
                    'Visit Information': 'ข้อมูลการเยี่ยมชม',
                    'Where and when are you visiting?': 'คุณจะเยี่ยมชมที่ไหนและเมื่อไหร่?',
                    'Company Visiting': 'บริษัทที่จะเยี่ยม',
                    'Details of Visit': 'รายละเอียดการเยี่ยมชม',
                    'Host and purpose details.': 'รายละเอียดเจ้าภาพและวัตถุประสงค์',
                    'Staff ID Of Person Visited': 'รหัสพนักงานของผู้ที่เยี่ยมชม',
                    'Contact No Of Person Visited': 'เบอร์ติดต่อของผู้ที่เยี่ยมชม',
                    'Name Of Company Visited': 'ชื่อบริษัทที่เยี่ยมชม',
                    'Reason': 'เหตุผล',
                    'Person Details': 'รายละเอียดบุคคล',
                    'Visitor identification information.': 'ข้อมูลระบุตัวตนผู้เยี่ยมชม'
                },
                'vi': {
                    'Visitor Registration': 'Đăng ký khách',
                    'Please complete your details for secure entry verification at SafeG.': 'Vui lòng hoàn thành thông tin của bạn để xác minh ra vào an toàn tại SafeG.',
                    'Visit Information': 'Thông tin chuyến thăm',
                    'Where and when are you visiting?': 'Bạn đang thăm đâu và khi nào?',
                    'Company Visiting': 'Công ty đến thăm',
                    'Details of Visit': 'Chi tiết chuyến thăm',
                    'Host and purpose details.': 'Chi tiết chủ nhà và mục đích.',
                    'Staff ID Of Person Visited': 'ID nhân viên người được thăm',
                    'Contact No Of Person Visited': 'Số liên lạc người được thăm',
                    'Name Of Company Visited': 'Tên công ty được thăm',
                    'Reason': 'Lý do',
                    'Person Details': 'Chi tiết cá nhân',
                    'Visitor identification information.': 'Thông tin nhận dạng khách.'
                },
                'id': {
                    'Visitor Registration': 'Pendaftaran Pengunjung',
                    'Please complete your details for secure entry verification at SafeG.': 'Silakan lengkapi detail Anda untuk verifikasi masuk aman di SafeG.',
                    'Visit Information': 'Informasi Kunjungan',
                    'Where and when are you visiting?': 'Ke mana dan kapan Anda berkunjung?',
                    'Company Visiting': 'Perusahaan yang Dikunjungi',
                    'Details of Visit': 'Detail Kunjungan',
                    'Host and purpose details.': 'Detail tuan rumah dan tujuan.',
                    'Staff ID Of Person Visited': 'ID Staf Orang yang Dikunjungi',
                    'Contact No Of Person Visited': 'No. Kontak Orang yang Dikunjungi',
                    'Name Of Company Visited': 'Nama Perusahaan yang Dikunjungi',
                    'Reason': 'Alasan',
                    'Person Details': 'Detail Pribadi',
                    'Visitor identification information.': 'Informasi identifikasi pengunjung.'
                }
            };

            const trans = lang === 'en' ? originalTexts : translations[lang];
            if (!trans) return;

            document.querySelectorAll('[data-translate]').forEach(el => {
                const key = el.getAttribute('data-translate');
                if (trans[key]) {
                    if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                        el.placeholder = trans[key];
                    } else {
                        el.textContent = trans[key];
                    }
                }
            });
        }

        // Load saved language on page load
        window.addEventListener('DOMContentLoaded', function() {
            const savedLang = localStorage.getItem('selectedLanguage');
            if (savedLang && savedLang !== 'en') {
                changeLanguage(savedLang);
            }
        });

        // Close modals on outside click
        document.getElementById('helpModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeHelpModal();
        });
        document.getElementById('languageModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeLanguageModal();
        });
    </script>
</body>
</html>
