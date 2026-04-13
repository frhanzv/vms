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
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('logbook') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">menu_book</span>
                    <p class="text-sm font-medium">Visitor Logbook</p>
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
                            <p class="text-sm font-medium">REPORT</p>
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
                            ACCESS REPORT
                        </a>
                        <a href="<?= base_url('report/visitor') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'report/visitor' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $current == 'report/visitor' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            VISITOR REPORT
                        </a>
                        <a href="<?= base_url('report/chronology') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'report/chronology') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'report/chronology') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            VISITOR CHRONOLOGY
                        </a>
                        <a href="<?= base_url('report/bydoor') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'report/bydoor') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'report/bydoor') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            VISITOR INFO BY DOOR
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

<!-- Visit Context Section -->
<section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
<div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
<h3 class="text-lg font-bold text-slate-900 dark:text-white">Visit Context</h3>
<span class="material-symbols-outlined text-slate-400">info</span>
</div>
<div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
<!-- Staff ID (Auto-filled, Read-only) -->
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
Staff ID
<span class="text-xs font-normal text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">Auto-filled</span>
</label>
<div class="relative">
<input name="staff_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white cursor-not-allowed" value="<?= esc($staff_id ?? '') ?>" type="text" readonly/>
<span class="material-symbols-outlined absolute right-3 top-3 text-slate-400 text-[20px]">lock</span>
</div>
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

<!-- Contact No Of Person Visited (Auto-filled, Read-only) -->
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
Contact No Of Person Visited
<span class="text-xs font-normal text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">Auto-filled</span>
</label>
<div class="relative">
<input name="contact_person" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white cursor-not-allowed" value="<?= esc($contact_no ?? '') ?>" type="tel" readonly/>
<span class="material-symbols-outlined absolute right-3 top-3 text-slate-400 text-[20px]">lock</span>
</div>
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

<!-- Visitor Details Section -->
<section class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden mt-8">
<div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
<h3 class="text-lg font-bold text-slate-900 dark:text-white">Primary Visitor Details</h3>
<span class="material-symbols-outlined text-slate-400">person</span>
</div>
<div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Full Name <span class="text-red-500">*</span></label>
<input name="full_name" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" placeholder="Full name as per ID" type="text" required/>
</div>
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Contact Number <span class="text-red-500">*</span></label>
<input name="contact" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" placeholder="+60 1x-xxx xxxx" type="tel" required/>
</div>
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Email Address <span class="text-red-500">*</span></label>
<input name="visitor_email" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm focus:border-primary focus:ring-primary dark:text-white" placeholder="visitor@example.com" type="email" required/>
</div>
</div>
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

// Dynamic visitor addition
let visitorCount = 1;
document.getElementById('add-visitor').addEventListener('click', function() {
    const container = document.getElementById('visitors-container');
    visitorCount++;
    
    const visitorHTML = `
        <div class="visitor-item relative p-5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/20 group hover:border-primary/30 transition-colors">
            <div class="absolute right-4 top-4">
                <button type="button" class="remove-visitor text-slate-400 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove Visitor">
                    <span class="material-symbols-outlined text-[20px]">delete</span>
                </button>
            </div>
            <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="visitor-number flex items-center justify-center size-6 rounded-full bg-primary/10 text-primary text-xs">${visitorCount}</span>
                Visitor Information
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Full Name</label>
                    <input name="visitors[${visitorCount-1}][name]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-primary focus:ring-primary dark:text-white" placeholder="Full name as per ID" type="text" required/>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Contact Number</label>
                    <input name="visitors[${visitorCount-1}][contact]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-primary focus:ring-primary dark:text-white" placeholder="+60 1x-xxx xxxx" type="tel" required/>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Email Address</label>
                    <input name="visitors[${visitorCount-1}][email]" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-primary focus:ring-primary dark:text-white" placeholder="visitor@example.com" type="email" required/>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', visitorHTML);
    document.getElementById('visitor-count').value = visitorCount;
    updateVisitorNumbers();
});

// Remove visitor
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-visitor')) {
        const visitorItems = document.querySelectorAll('.visitor-item');
        if (visitorItems.length > 1) {
            e.target.closest('.visitor-item').remove();
            visitorCount--;
            document.getElementById('visitor-count').value = visitorCount;
            updateVisitorNumbers();
        }
    }
});

// Update visitor numbers
function updateVisitorNumbers() {
    const visitorItems = document.querySelectorAll('.visitor-item');
    visitorItems.forEach((item, index) => {
        const numberSpan = item.querySelector('.visitor-number');
        if (numberSpan) {
            numberSpan.textContent = index + 1;
        }
        // Update input names
        const inputs = item.querySelectorAll('input');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name && name.startsWith('visitors[')) {
                const fieldName = name.split('][')[1].replace(']', '');
                input.setAttribute('name', `visitors[${index}][${fieldName}]`);
            }
        });
    });
}

// Dynamic schedule addition
let scheduleCount = 1;
document.getElementById('add-schedule').addEventListener('click', function() {
    const container = document.getElementById('schedule-container');
    scheduleCount++;
    
    const scheduleHTML = `
        <div class="schedule-item flex items-end gap-4 p-4 border border-slate-200 dark:border-slate-700 rounded-lg">
            <div class="flex-1 space-y-2">
                <label class="block text-sm font-semibold text-slate-600 dark:text-slate-400">Date From <span class="text-red-500">*</span></label>
                <input name="schedules[${scheduleCount-1}][date_from]" class="w-full h-12 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none" placeholder="dd/mm/yyyy --:-- --" type="datetime-local" required/>
            </div>
            <div class="flex-1 space-y-2">
                <label class="block text-sm font-semibold text-slate-600 dark:text-slate-400">Date To <span class="text-red-500">*</span></label>
                <input name="schedules[${scheduleCount-1}][date_to]" class="w-full h-12 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none" placeholder="dd/mm/yyyy --:-- --" type="datetime-local" required/>
            </div>
            <div class="flex items-center pb-2">
                <button type="button" class="remove-schedule text-slate-400 hover:text-red-500 transition-colors p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove Slot">
                    <span class="material-symbols-outlined">remove_circle_outline</span>
                </button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', scheduleHTML);
});

// Remove schedule slot
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-schedule')) {
        const scheduleItems = document.querySelectorAll('.schedule-item');
        if (scheduleItems.length > 1) {
            e.target.closest('.schedule-item').remove();
            scheduleCount--;
        }
    }
});

</script>

</body>
</html>