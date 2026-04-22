<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>New Invitation - SafeG</title>
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

    <!-- Blacklist dropdown function-->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden transition-colors duration-200">
    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col p-4 hidden md:flex h-full overflow-hidden">
        <div class="flex flex-col gap-8 flex-1 min-h-0">
            <div class="flex items-center gap-3 px-2">
                <div class="bg-center bg-no-repeat bg-cover rounded-lg size-10 bg-primary/10 flex items-center justify-center text-primary" data-alt="SafeG Logo abstract blue square">
                    <span class="material-symbols-outlined text-3xl">shield_person</span>
                </div>
                <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">SafeG</h1>
            </div>
            <nav class="flex flex-col gap-2 overflow-y-auto pr-1 custom-scrollbar">
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('dashboard') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">dashboard</span>
                    <p class="text-sm font-medium">Dashboard</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary group transition-colors" href="<?= base_url('invitations') ?>">
                    <span class="material-symbols-outlined text-[22px] font-medium fill-1 group-hover:scale-110 transition-transform">mail</span>
                    <p class="text-sm font-semibold">Invitations</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('requests') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">assignment</span>
                    <p class="text-sm font-medium">Request List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('staffs') ?>">
                    <span class="material-symbols-outlined text-[22px] font-medium fill-1 group-hover:scale-110 transition-transform">badge</span>
                    <p class="text-sm font-medium">Staff Pass List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('visitors') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">group</span>
                    <p class="text-sm font-medium">Visitors List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('workflow') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">account_tree</span>
                    <p class="text-sm font-medium">Visitor Workflow</p>
                </a>
                <div x-data="{ openBlacklist: <?= str_contains($current, 'blacklist') ? 'true' : 'false' ?>, openIndividual: <?= str_contains($current, 'blacklist') ? 'true' : 'false' ?> }">
    
                    <!-- Blacklist Parent Button -->
                    <button 
                        type="button"
                        @click="openBlacklist = !openBlacklist"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg <?= str_contains($current, 'blacklist') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary' ?> transition-colors group"
                    >
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">person_cancel</span>
                            <p class="text-sm font-medium">Blacklist</p>
                        </div>
                        <span 
                            class="material-symbols-outlined text-[18px] transition-transform duration-200"
                            :class="openBlacklist ? 'rotate-180' : ''"
                        >expand_more</span>
                    </button>

                    <!-- Blacklist Dropdown -->
                    <div 
                        x-show="openBlacklist" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="ml-4 mt-1 flex flex-col gap-1"
                    >
                        <!-- Individual Submenu Button -->
                        <button
                            type="button"
                            @click="openIndividual = !openIndividual"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-lg <?= str_contains($current, 'blacklist') ? 'text-primary bg-primary/5' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary' ?> transition-colors group"
                        >
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[18px] group-hover:scale-110 transition-transform">person</span>
                                <p class="text-sm font-medium">Individual</p>
                            </div>
                            <span 
                                class="material-symbols-outlined text-[16px] transition-transform duration-200"
                                :class="openIndividual ? 'rotate-180' : ''"
                            >expand_more</span>
                        </button>

                        <!-- Request List & Closed List Links -->
                        <div 
                            x-show="openIndividual"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="ml-4 mt-1 flex flex-col gap-1"
                        >   
                            <!-- Request List -->
                            <a href="<?= base_url('blacklist/blacklistrequest') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                            <?= $current == 'blacklist/blacklistrequest' 
                                ? 'bg-primary/10 text-primary font-medium' 
                                : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $current == 'blacklist/blacklistrequest' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                                Request List
                            </a>

                            <!-- Closed List -->
                            <a href="<?= base_url('blacklist/closedlist') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                            <?= $current == 'blacklist/closedlist' 
                                ? 'bg-primary/10 text-primary font-medium' 
                                : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $current == 'blacklist/closedlist' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                                Closed List
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Report Dropdown -->
                <div x-data="{ openReport: <?= str_contains($current, 'report') ? 'true' : 'false' ?> }">
                    <button type="button" @click="openReport = !openReport"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg <?= str_contains($current, 'report') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary' ?> transition-colors group">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">description</span>
                            <p class="text-sm font-medium">Report</p>
                        </div>
                        <span class="material-symbols-outlined text-[18px] transition-transform duration-200" :class="openReport ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="openReport"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="ml-4 mt-1 flex flex-col gap-1">
                        <a href="<?= base_url('report/access') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'report/access' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $current == 'report/access' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            Access Report
                        </a>
                        <a href="<?= base_url('report/visitor') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'report/visitor' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $current == 'report/visitor' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            Visitor Report
                        </a>
                        <a href="<?= base_url('report/chronology') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'report/chronology') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'report/chronology') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            Visitor Chronology
                        </a>
                        <a href="<?= base_url('report/bydoor') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'report/bydoor') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'report/bydoor') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            Visitor Info By Door
                        </a>
                    </div>
                </div>

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
                        New Visitor Invitation
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Please complete the form below to register new visitors.</p>
                </div>
            </div>

<form action="<?= base_url('invitations/store') ?>" method="post">
<?= csrf_field() ?>

<!-- Display Success/Error Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg mb-6">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <span class="font-medium"><?= session()->getFlashdata('success') ?></span>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg mb-6">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-red-600">error</span>
            <span class="font-medium"><?= session()->getFlashdata('error') ?></span>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg mb-6">
        <div class="flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-red-600">error</span>
            <span class="font-medium">Please fix the following errors:</span>
        </div>
        <ul class="list-disc list-inside ml-6 space-y-1">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Visitor count + history (reference: new invitation header row) -->
<section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden mb-6">
<div class="p-6 flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-4">
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="visitor-count">Number of visitors</label>
<input id="visitor-count" type="number" min="1" max="30" value="1" class="w-28 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white"/>
<p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs">Sets how many visitor rows appear below. Changing this adds or removes rows from the bottom.</p>
</div>
<button type="button" id="btn-invitation-history" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-primary text-white text-sm font-bold hover:bg-blue-600 shadow-sm shrink-0">
<span class="material-symbols-outlined text-[20px]">history</span>
History
</button>
</div>
</section>

<!-- Visit Context Section -->
<section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
<div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
<h3 class="text-lg font-bold text-slate-900 dark:text-white">Visit Context</h3>
<span class="material-symbols-outlined text-slate-400">info</span>
</div>
<div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
<!-- Staff ID (defaults from account; editable e.g. when using a manager login) -->
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Staff ID <span class="text-red-500">*</span></label>
<input name="staff_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" value="<?= esc(old('staff_id', $staff_id ?? '')) ?>" type="text" maxlength="50" required/>
</div>

<!-- Visitor Type -->
<div class="flex flex-col gap-2 md:col-span-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Visitor Type <?php if (! empty($visitorTypes)): ?><span class="text-red-500">*</span><?php endif; ?></label>
<?php if (! empty($visitorTypes)): ?>
<select name="visitor_type_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" required>
<option value="">Select visitor type...</option>
<?php foreach ($visitorTypes as $vt): ?>
<option value="<?= (int) $vt['id'] ?>"><?= esc($vt['name']) ?></option>
<?php endforeach; ?>
</select>
<p class="text-xs text-slate-500 dark:text-slate-400">Configured under System Configuration → Visitor Type Management. Path is stored for routing reference.</p>
<?php else: ?>
<input type="hidden" name="visitor_type_id" value=""/>
<p class="text-sm text-amber-700 dark:text-amber-400 rounded-lg border border-amber-200 dark:border-amber-900/50 bg-amber-50 dark:bg-amber-900/20 px-4 py-3">No visitor types are configured yet. Add them under <strong>Config</strong> → Visitor Type Management, then refresh this page.</p>
<?php endif; ?>
</div>

<!-- Name of Company Visited -->
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Name of Company Visited</label>
<select name="company_visited" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" required>
<option value="">Select company...</option>
<?php if (isset($companies) && !empty($companies)): ?>
    <?php foreach ($companies as $company): ?>
        <option value="<?= esc($company['name']) ?>"><?= esc($company['name']) ?></option>
    <?php endforeach; ?>
<?php endif; ?>
</select>
</div>

<!-- Contact No Of Person Visited (defaults from account; editable) -->
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Contact No Of Person Visited <span class="text-red-500">*</span></label>
<input name="contact_person" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" value="<?= esc(old('contact_person', $contact_no ?? '')) ?>" type="tel" maxlength="20" required/>
</div>

<!-- Link Expiry -->
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Link Expiry</label>
<div class="relative">
<input name="link_expiry" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" type="date" required/>
</div>
</div>

<!-- Reason for Visit -->
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Reason for Visit</label>
<select name="reason" id="visit-reason" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" required>
<option value="">Select reason...</option>
<?php if (isset($visitReasons) && !empty($visitReasons)): ?>
    <?php foreach ($visitReasons as $reason): ?>
        <option value="<?= esc($reason['reason']) ?>"><?= esc($reason['reason']) ?></option>
    <?php endforeach; ?>
<?php endif; ?>
<option value="OTHER">OTHER</option>
</select>
</div>

<!-- Other Reason -->
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Other Reason</label>
<input name="other_reason" id="other-reason" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white placeholder:text-slate-400" placeholder="Please specify if 'OTHER' selected" type="text" disabled/>
</div>
<!-- Location -->
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Location</label>
<select name="location" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white">
<option value="">Select location...</option>
<?php if (isset($locations) && !empty($locations)): ?>
    <?php foreach ($locations as $location): ?>
        <option value="<?= esc($location['location_access']) ?>"><?= esc($location['branch']) ?> - <?= esc($location['location_access']) ?></option>
    <?php endforeach; ?>
<?php endif; ?>
</select>
</div></div>
</section>

<!-- Visitor Details Section (multiple rows, same pattern as Visit Schedule) -->
<section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden mt-8">
<div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
<h3 class="text-lg font-bold text-slate-900 dark:text-white">Visitor Details</h3>
<div class="flex items-center gap-2">
<button type="button" id="add-visitor" class="text-primary hover:text-primary/80 flex items-center gap-1 text-sm font-semibold" title="Add visitor">
<span class="material-symbols-outlined text-[20px]">person_add</span>
Add Visitor
</button>
<button type="button" id="remove-last-visitor" class="text-slate-500 hover:text-red-500 flex items-center gap-1 text-sm font-semibold" title="Remove last visitor">
<span class="material-symbols-outlined text-[20px]">person_remove</span>
</button>
</div>
</div>
<div class="px-6 pt-2 pb-2 border-b border-slate-200 dark:border-slate-800">
<label class="flex items-start gap-3 cursor-pointer group">
<input type="checkbox" name="allow_sub_invites" value="1" class="mt-1 rounded border-slate-300 text-primary focus:ring-primary" <?= old('allow_sub_invites') ? 'checked' : '' ?>/>
<span class="text-sm text-slate-700 dark:text-slate-300">
<span class="font-semibold block group-hover:text-primary transition-colors">Multiple invitation per mail</span>
<span class="text-xs text-slate-500 dark:text-slate-400 font-normal block mt-1">When enabled, each invitee can open their registration link and send invitations to additional guests using the same visit details and schedule. Sub-invitations cannot chain further.</span>
</span>
</label>
</div>
<div id="visitors-container" class="p-6 flex flex-col gap-4">
<div class="visitor-item flex flex-col lg:flex-row lg:items-end gap-4 p-4 border border-slate-200 dark:border-slate-700 rounded-lg">
<div class="flex-1 space-y-2">
<label class="block text-sm font-semibold text-slate-600 dark:text-slate-400">Full Name <span class="text-red-500">*</span></label>
<input name="visitors[0][full_name]" class="w-full h-12 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none" placeholder="Full name as per ID" type="text" required/>
</div>
<div class="flex-1 space-y-2">
<label class="block text-sm font-semibold text-slate-600 dark:text-slate-400">Contact Number <span class="text-red-500">*</span></label>
<input name="visitors[0][contact]" class="w-full h-12 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none" placeholder="+60 1x-xxx xxxx" type="tel" required/>
</div>
<div class="flex-1 space-y-2">
<label class="block text-sm font-semibold text-slate-600 dark:text-slate-400">Email Address <span class="text-red-500">*</span></label>
<input name="visitors[0][visitor_email]" class="w-full h-12 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none" placeholder="visitor@example.com" type="email" required/>
</div>
<div class="flex items-center pb-2 lg:pb-0">
<button type="button" class="remove-visitor text-slate-400 hover:text-red-500 transition-colors p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20 opacity-50 pointer-events-none" title="Remove visitor" aria-disabled="true">
<span class="material-symbols-outlined">remove_circle_outline</span>
</button>
</div>
</div>
</div>
<p class="px-6 pb-6 text-xs text-slate-500 dark:text-slate-400 -mt-2">Each visitor receives a separate invitation and registration link. Empty rows are ignored.</p>
</section>

<!-- Visit Schedule Section -->
<section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden mt-8">
<div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
<h3 class="text-lg font-bold text-slate-900 dark:text-white">Visit Schedule</h3>
<button type="button" id="add-schedule" class="text-primary hover:text-primary/80 flex items-center gap-1 text-sm font-semibold">
<span class="material-symbols-outlined text-[20px]">calendar_add_on</span>
Add Date Slot
</button>
</div>
<div id="schedule-container" class="p-6 flex flex-col gap-4">
<div class="schedule-item flex items-end gap-4 p-4 border border-slate-200 dark:border-slate-700 rounded-lg">
<div class="flex-1 space-y-2">
<label class="block text-sm font-semibold text-slate-600 dark:text-slate-400">Date From <span class="text-red-500">*</span></label>
<input name="schedules[0][date_from]" class="w-full h-12 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none" placeholder="dd/mm/yyyy --:-- --" type="datetime-local" required/>
</div>
<div class="flex-1 space-y-2">
<label class="block text-sm font-semibold text-slate-600 dark:text-slate-400">Date To <span class="text-red-500">*</span></label>
<input name="schedules[0][date_to]" class="w-full h-12 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none" placeholder="dd/mm/yyyy --:-- --" type="datetime-local" required/>
</div>
<div class="flex items-center pb-2">
<button type="button" class="remove-schedule text-slate-400 hover:text-red-500 transition-colors p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove Slot">
<span class="material-symbols-outlined">remove_circle_outline</span>
</button>
</div>
</div>
</div>
</section>

<!-- Form Actions -->
<div class="flex items-center justify-end gap-4 py-6 border-t border-slate-200 dark:border-slate-800 mt-8">
<button type="button" onclick="window.history.back()" class="px-6 py-3 rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
Back
</button>
<button type="submit" class="px-8 py-3 rounded-lg bg-primary text-white font-bold hover:bg-blue-600 shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2">
<span>Send Invitation</span>
<span class="material-symbols-outlined text-sm">send</span>
</button>
</div>
</form>
        </div>
    </main>

<!-- Invitation history modal -->
<div id="invitation-history-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" aria-hidden="true">
<div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-5xl max-h-[90vh] flex flex-col">
<div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between shrink-0">
<h2 class="text-lg font-bold text-slate-900 dark:text-white uppercase tracking-tight">Invitation history</h2>
<button type="button" id="invitation-history-close" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Close">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-col lg:flex-row gap-3 lg:items-center shrink-0">
<input type="search" id="history-search" class="flex-1 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm dark:text-white" placeholder="Full name / company name"/>
<button type="button" id="history-search-btn" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold shrink-0">
<span class="material-symbols-outlined text-[20px]">search</span>
Search
</button>
<div class="flex items-center gap-2 lg:ml-auto">
<label class="text-xs font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">Sort by</label>
<select id="history-sort" class="rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm dark:text-white">
<option value="created_at|DESC">Newest first</option>
<option value="created_at|ASC">Oldest first</option>
<option value="link_expiry|ASC">Link expiry (soonest)</option>
<option value="full_name|ASC">Name A–Z</option>
<option value="status|ASC">Status A–Z</option>
</select>
</div>
<div class="flex items-center gap-2">
<label class="text-xs font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">Per page</label>
<select id="history-per-page" class="rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm dark:text-white">
<option value="10">10</option>
<option value="20">20</option>
<option value="30">30</option>
</select>
</div>
</div>
<div class="overflow-auto flex-1 min-h-0 px-6 py-2">
<table class="w-full text-sm text-left">
<thead class="text-xs uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700 sticky top-0 bg-white dark:bg-slate-900 z-10">
<tr>
<th class="py-3 pr-2">No</th>
<th class="py-3 pr-2">Date</th>
<th class="py-3 pr-2">Full name</th>
<th class="py-3 pr-2">Link expiry</th>
<th class="py-3 pr-2">Status</th>
<th class="py-3 pr-2">Company</th>
<th class="py-3">Created by</th>
</tr>
</thead>
<tbody id="history-table-body" class="divide-y divide-slate-100 dark:divide-slate-800"></tbody>
</table>
<p id="history-empty" class="hidden py-8 text-center text-slate-500 dark:text-slate-400 text-sm">No invitations found.</p>
<p id="history-loading" class="hidden py-8 text-center text-slate-500 text-sm">Loading…</p>
<p id="history-error" class="hidden py-8 text-center text-red-600 text-sm"></p>
</div>
<div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 shrink-0">
<div id="history-pagination" class="flex flex-wrap items-center gap-2 text-sm text-slate-600 dark:text-slate-300"></div>
<p class="text-xs text-slate-500 dark:text-slate-400">Click a row to load that invitation into the form (you can still edit before sending).</p>
</div>
</div>
</div>

<script>
// Enable/Disable "Other Reason" field based on "Reason for Visit" selection
const visitReasonSelect = document.getElementById('visit-reason');
const otherReasonInput = document.getElementById('other-reason');

visitReasonSelect.addEventListener('change', function() {
    if (this.value === 'OTHER') {
        otherReasonInput.disabled = false;
        otherReasonInput.required = true;
        otherReasonInput.classList.remove('bg-slate-50', 'dark:bg-slate-800/50', 'cursor-not-allowed');
    } else {
        otherReasonInput.disabled = true;
        otherReasonInput.required = false;
        otherReasonInput.value = '';
        otherReasonInput.classList.add('bg-slate-50', 'dark:bg-slate-800/50');
    }
});

// Dynamic visitor rows (same indexing approach as schedules)
let visitorRowCount = 1;

function visitorFieldTemplate(index, field) {
    const base = 'w-full h-12 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none';
    if (field === 'full_name') {
        return `<input name="visitors[${index}][full_name]" class="${base}" placeholder="Full name as per ID" type="text" required/>`;
    }
    if (field === 'contact') {
        return `<input name="visitors[${index}][contact]" class="${base}" placeholder="+60 1x-xxx xxxx" type="tel" required/>`;
    }
    return `<input name="visitors[${index}][visitor_email]" class="${base}" placeholder="visitor@example.com" type="email" required/>`;
}

function updateVisitorRemoveButtons() {
    const items = document.querySelectorAll('#visitors-container .visitor-item');
    items.forEach((item, i) => {
        const btn = item.querySelector('.remove-visitor');
        if (!btn) return;
        const onlyOne = items.length <= 1;
        btn.classList.toggle('opacity-50', onlyOne);
        btn.classList.toggle('pointer-events-none', onlyOne);
        btn.toggleAttribute('aria-disabled', onlyOne);
    });
}

function reindexVisitorRows() {
    const items = document.querySelectorAll('#visitors-container .visitor-item');
    items.forEach((item, index) => {
        const inputs = item.querySelectorAll('input');
        inputs.forEach(input => {
            const n = input.getAttribute('name');
            if (!n || !n.startsWith('visitors[')) return;
            const m = n.match(/^visitors\[\d+\]\[(full_name|contact|visitor_email)\]$/);
            if (m) {
                input.setAttribute('name', 'visitors[' + index + '][' + m[1] + ']');
            }
        });
    });
    visitorRowCount = items.length;
    updateVisitorRemoveButtons();
}

document.getElementById('add-visitor').addEventListener('click', function() {
    const container = document.getElementById('visitors-container');
    const idx = container.querySelectorAll('.visitor-item').length;
    const row = document.createElement('div');
    row.className = 'visitor-item flex flex-col lg:flex-row lg:items-end gap-4 p-4 border border-slate-200 dark:border-slate-700 rounded-lg';
    row.innerHTML = `
        <div class="flex-1 space-y-2">
            <label class="block text-sm font-semibold text-slate-600 dark:text-slate-400">Full Name <span class="text-red-500">*</span></label>
            ${visitorFieldTemplate(idx, 'full_name')}
        </div>
        <div class="flex-1 space-y-2">
            <label class="block text-sm font-semibold text-slate-600 dark:text-slate-400">Contact Number <span class="text-red-500">*</span></label>
            ${visitorFieldTemplate(idx, 'contact')}
        </div>
        <div class="flex-1 space-y-2">
            <label class="block text-sm font-semibold text-slate-600 dark:text-slate-400">Email Address <span class="text-red-500">*</span></label>
            ${visitorFieldTemplate(idx, 'visitor_email')}
        </div>
        <div class="flex items-center pb-2 lg:pb-0">
            <button type="button" class="remove-visitor text-slate-400 hover:text-red-500 transition-colors p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove visitor">
                <span class="material-symbols-outlined">remove_circle_outline</span>
            </button>
        </div>
    `;
    container.appendChild(row);
    reindexVisitorRows();
});

document.getElementById('remove-last-visitor').addEventListener('click', function() {
    const container = document.getElementById('visitors-container');
    const items = container.querySelectorAll('.visitor-item');
    if (items.length > 1) {
        items[items.length - 1].remove();
        reindexVisitorRows();
    }
});

document.addEventListener('click', function(e) {
    const rm = e.target.closest('.remove-visitor');
    if (!rm || rm.getAttribute('aria-disabled') === 'true') return;
    const row = rm.closest('.visitor-item');
    if (!row) return;
    const items = document.querySelectorAll('#visitors-container .visitor-item');
    if (items.length > 1) {
        row.remove();
        reindexVisitorRows();
    }
});

updateVisitorRemoveButtons();

function scheduleRowTemplate(index) {
    return `
        <div class="schedule-item flex items-end gap-4 p-4 border border-slate-200 dark:border-slate-700 rounded-lg">
            <div class="flex-1 space-y-2">
                <label class="block text-sm font-semibold text-slate-600 dark:text-slate-400">Date From <span class="text-red-500">*</span></label>
                <input name="schedules[${index}][date_from]" class="w-full h-12 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none" placeholder="dd/mm/yyyy --:-- --" type="datetime-local" required/>
            </div>
            <div class="flex-1 space-y-2">
                <label class="block text-sm font-semibold text-slate-600 dark:text-slate-400">Date To <span class="text-red-500">*</span></label>
                <input name="schedules[${index}][date_to]" class="w-full h-12 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none" placeholder="dd/mm/yyyy --:-- --" type="datetime-local" required/>
            </div>
            <div class="flex items-center pb-2">
                <button type="button" class="remove-schedule text-slate-400 hover:text-red-500 transition-colors p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove Slot">
                    <span class="material-symbols-outlined">remove_circle_outline</span>
                </button>
            </div>
        </div>`;
}

function reindexScheduleRows() {
    const items = document.querySelectorAll('#schedule-container .schedule-item');
    items.forEach((item, index) => {
        item.querySelectorAll('input[type="datetime-local"]').forEach((input) => {
            const n = input.getAttribute('name');
            if (!n || !n.startsWith('schedules[')) return;
            const m = n.match(/^schedules\[\d+\]\[(date_from|date_to)\]$/);
            if (m) input.setAttribute('name', 'schedules[' + index + '][' + m[1] + ']');
        });
    });
}

document.getElementById('add-schedule').addEventListener('click', function() {
    const container = document.getElementById('schedule-container');
    const idx = container.querySelectorAll('.schedule-item').length;
    container.insertAdjacentHTML('beforeend', scheduleRowTemplate(idx));
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-schedule')) {
        const scheduleItems = document.querySelectorAll('.schedule-item');
        if (scheduleItems.length > 1) {
            e.target.closest('.schedule-item').remove();
            reindexScheduleRows();
        }
    }
});

function setVisitorSectionCount(target) {
    const n = Math.min(30, Math.max(1, parseInt(String(target), 10) || 1));
    const container = document.getElementById('visitors-container');
    let items = container.querySelectorAll('.visitor-item');
    while (items.length < n) {
        document.getElementById('add-visitor').click();
        items = container.querySelectorAll('.visitor-item');
    }
    while (items.length > n) {
        if (items.length <= 1) break;
        items[items.length - 1].remove();
        items = container.querySelectorAll('.visitor-item');
    }
    reindexVisitorRows();
    const vc = document.getElementById('visitor-count');
    if (vc && parseInt(vc.value, 10) !== n) vc.value = String(n);
}

const visitorCountInput = document.getElementById('visitor-count');
if (visitorCountInput) {
    visitorCountInput.addEventListener('change', function() {
        setVisitorSectionCount(this.value);
    });
}

const INVITATION_HISTORY_LIST_URL = <?= json_encode(base_url('invitations/history-rows')) ?>;
const INVITATION_HISTORY_FORM_BASE = <?= json_encode(base_url('invitations/history-for-form')) ?>;

let historyState = { page: 1, search: '', sort: 'created_at', order: 'DESC', perPage: 10 };

function setSelectValue(selectEl, value) {
    if (!selectEl) return;
    const v = String(value ?? '');
    if (v === '') {
        selectEl.selectedIndex = 0;
        return;
    }
    for (let i = 0; i < selectEl.options.length; i++) {
        if (selectEl.options[i].value === v) {
            selectEl.selectedIndex = i;
            return;
        }
    }
    for (let i = 0; i < selectEl.options.length; i++) {
        if (String(selectEl.options[i].value).toLowerCase() === v.toLowerCase()) {
            selectEl.selectedIndex = i;
            return;
        }
    }
}

function rebuildSchedulesFromData(schedules) {
    const container = document.getElementById('schedule-container');
    const list = Array.isArray(schedules) && schedules.length ? schedules : [{ date_from: '', date_to: '' }];
    container.innerHTML = '';
    list.forEach((row, index) => {
        container.insertAdjacentHTML('beforeend', scheduleRowTemplate(index));
        const items = container.querySelectorAll('.schedule-item');
        const last = items[items.length - 1];
        const fromIn = last.querySelector('input[name="schedules[' + index + '][date_from]"]');
        const toIn = last.querySelector('input[name="schedules[' + index + '][date_to]"]');
        if (fromIn) fromIn.value = row.date_from || '';
        if (toIn) toIn.value = row.date_to || '';
    });
    reindexScheduleRows();
}

function applyInvitationHistoryPayload(d) {
    const staff = document.querySelector('input[name="staff_id"]');
    if (staff) staff.value = d.staff_id || '';
    setSelectValue(document.querySelector('select[name="visitor_type_id"]'), d.visitor_type_id != null ? String(d.visitor_type_id) : '');
    setSelectValue(document.querySelector('select[name="company_visited"]'), d.company_visited || '');
    const contactPerson = document.querySelector('input[name="contact_person"]');
    if (contactPerson) contactPerson.value = d.contact_person || '';
    // From history: leave blank — host sets fresh expiry, reason, location, and visit dates
    const linkExp = document.querySelector('input[name="link_expiry"]');
    if (linkExp) linkExp.value = '';
    setSelectValue(document.getElementById('visit-reason'), '');
    visitReasonSelect.dispatchEvent(new Event('change'));
    setSelectValue(document.querySelector('select[name="location"]'), '');
    const allowSub = document.querySelector('input[name="allow_sub_invites"]');
    if (allowSub) allowSub.checked = !!d.allow_sub_invites;

    const visitors = Array.isArray(d.visitors) && d.visitors.length ? d.visitors : [{ full_name: '', contact: '', visitor_email: '' }];
    setVisitorSectionCount(visitors.length);
    const rows = document.querySelectorAll('#visitors-container .visitor-item');
    visitors.forEach((v, i) => {
        const row = rows[i];
        if (!row) return;
        const fn = row.querySelector('input[name*="[full_name]"]');
        const co = row.querySelector('input[name*="[contact]"]');
        const em = row.querySelector('input[name*="[visitor_email]"]');
        if (fn) fn.value = v.full_name || '';
        if (co) co.value = v.contact || '';
        if (em) em.value = v.visitor_email || '';
    });

    rebuildSchedulesFromData([{ date_from: '', date_to: '' }]);
}

function openHistoryModal() {
    const modal = document.getElementById('invitation-history-modal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    historyState.page = 1;
    loadHistoryPage();
}

function closeHistoryModal() {
    const modal = document.getElementById('invitation-history-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
}

async function loadHistoryPage() {
    const tbody = document.getElementById('history-table-body');
    const empty = document.getElementById('history-empty');
    const loading = document.getElementById('history-loading');
    const errEl = document.getElementById('history-error');
    const pag = document.getElementById('history-pagination');
    if (!tbody) return;
    errEl.classList.add('hidden');
    empty.classList.add('hidden');
    loading.classList.remove('hidden');
    tbody.innerHTML = '';

    const params = new URLSearchParams({
        page: String(historyState.page),
        per_page: String(historyState.perPage),
        search: historyState.search,
        sort: historyState.sort,
        order: historyState.order,
    });
    try {
        const res = await fetch(INVITATION_HISTORY_LIST_URL + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const json = await res.json();
        loading.classList.add('hidden');
        if (!json.success) {
            errEl.textContent = json.message || 'Failed to load history';
            errEl.classList.remove('hidden');
            return;
        }
        if (!json.data || json.data.length === 0) {
            empty.classList.remove('hidden');
        } else {
            json.data.forEach((row) => {
                const tr = document.createElement('tr');
                tr.className = 'cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors';
                tr.dataset.id = String(row.id);
                const expClass = row.link_expired ? 'text-red-600 font-semibold' : '';
                tr.innerHTML =
                    '<td class="py-2 pr-2">' + row.no + '</td>' +
                    '<td class="py-2 pr-2">' + escapeHtml(row.date) + '</td>' +
                    '<td class="py-2 pr-2">' + escapeHtml(row.full_name) + '</td>' +
                    '<td class="py-2 pr-2 ' + expClass + '">' + escapeHtml(row.link_expiry) + '</td>' +
                    '<td class="py-2 pr-2">' + escapeHtml(row.status) + '</td>' +
                    '<td class="py-2 pr-2">' + escapeHtml(row.company) + '</td>' +
                    '<td class="py-2">' + escapeHtml(row.invited_by) + '</td>';
                tbody.appendChild(tr);
            });
        }
        renderHistoryPagination(json.pagination || {});
    } catch (e) {
        loading.classList.add('hidden');
        errEl.textContent = 'Network error loading history.';
        errEl.classList.remove('hidden');
    }
}

function escapeHtml(s) {
    const t = document.createElement('div');
    t.textContent = s;
    return t.innerHTML;
}

function renderHistoryPagination(p) {
    const pag = document.getElementById('history-pagination');
    if (!pag) return;
    const cur = p.current_page || 1;
    const last = p.last_page || 1;
    const total = p.total || 0;
    pag.innerHTML = '';
    if (last <= 1) {
        pag.textContent = total ? (total + ' record(s)') : '';
        return;
    }
    const addBtn = (label, page, disabled) => {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'px-2 py-1 rounded border border-slate-300 dark:border-slate-600 text-sm ' + (disabled ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-100 dark:hover:bg-slate-800');
        b.textContent = label;
        if (!disabled) b.addEventListener('click', () => { historyState.page = page; loadHistoryPage(); });
        pag.appendChild(b);
    };
    addBtn('«', 1, cur <= 1);
    addBtn('‹', cur - 1, cur <= 1);
    const span = document.createElement('span');
    span.className = 'px-2 text-sm';
    span.textContent = 'Page ' + cur + ' / ' + last;
    pag.appendChild(span);
    addBtn('›', cur + 1, cur >= last);
    addBtn('»', last, cur >= last);
}

document.getElementById('btn-invitation-history')?.addEventListener('click', openHistoryModal);
document.getElementById('invitation-history-close')?.addEventListener('click', closeHistoryModal);
document.getElementById('invitation-history-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeHistoryModal();
});

document.getElementById('history-search-btn')?.addEventListener('click', function() {
    historyState.search = (document.getElementById('history-search')?.value || '').trim();
    historyState.page = 1;
    loadHistoryPage();
});
document.getElementById('history-search')?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        historyState.search = this.value.trim();
        historyState.page = 1;
        loadHistoryPage();
    }
});

document.getElementById('history-sort')?.addEventListener('change', function() {
    const parts = this.value.split('|');
    historyState.sort = parts[0] || 'created_at';
    historyState.order = parts[1] || 'DESC';
    historyState.page = 1;
    loadHistoryPage();
});

document.getElementById('history-per-page')?.addEventListener('change', function() {
    historyState.perPage = parseInt(this.value, 10) || 10;
    historyState.page = 1;
    loadHistoryPage();
});

document.getElementById('history-table-body')?.addEventListener('click', async function(e) {
    const tr = e.target.closest('tr[data-id]');
    if (!tr) return;
    const id = tr.dataset.id;
    try {
        const res = await fetch(INVITATION_HISTORY_FORM_BASE + '/' + encodeURIComponent(id), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const json = await res.json();
        if (!json.success || !json.data) {
            alert(json.message || 'Could not load invitation');
            return;
        }
        applyInvitationHistoryPayload(json.data);
        closeHistoryModal();
    } catch (err) {
        alert('Could not load invitation');
    }
});

</script>

</body>
</html>