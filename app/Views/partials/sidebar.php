<?php
helper(['access', 'navigation', 'branding', 'feature']);
$current     = app_route_path();
$isDashboard = ($current === '' || $current === 'dashboard');
$isEmap      = str_starts_with($current, 'e-map');
$isStaff     = str_contains($current, 'staffs') || str_contains($current, 'staff-pass-request');
$isVendor    = str_contains($current, 'vendors') || str_contains($current, 'vendorpassrequest');
$isWorkflow  = str_contains($current, 'workflow');
$isConfig    = str_contains($current, 'config');
$isSettings  = str_contains($current, 'settings');

$requestListAvailable = is_platform_superadmin() || ! client_feature_enabled('auto_approve_after_workflow');
$hasVisitorPassAccess = has_access('visitor_pass_list', 'invitations') || ($requestListAvailable && has_access('visitor_pass_list', 'request_list')) || has_access('visitor_pass_list', 'visitors_list');
$hasBlacklistAccess   = has_access('blacklist', 'request_list') || has_access('blacklist', 'closed_list') || has_access('blacklist', 'individual_request_list') || has_access('blacklist', 'individual_closed_list') || has_access('blacklist', 'company_request_list') || has_access('blacklist', 'company_closed_list');
$hasReportAccess      = has_access('report', 'access_report') || has_access('report', 'visitor_report') || has_access('report', 'visitor_chronology') || has_access('report', 'visitor_info_by_door') || has_access('report', 'gate_in_out') || has_access('report', 'out_window_list') || has_access('report', 'port_pass_monthly') || has_access('report', 'port_pass_summary') || has_access('report', 'company_permit_ageing') || has_access('report', 'company_permit_monthly') || has_access('report', 'vehicle_sticker_summary') || has_access('report', 'blacklist_report') || has_access('report', 'attendance_report') || has_access('report', 'monitoring_report')  || has_access('report', 'vendor_report');
$hasConfigAccess      = has_access('config', 'view') || has_access('config', 'alert_priority') || has_access('config', 'api_management') || has_access('config', 'general_settings') || has_access('config', 'application_settings') || has_access('config', 'role_management') || has_access('config', 'user_management') || has_access('config', 'company') || (is_client_superadmin() && current_client_id() > 0);
?>

<aside class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col p-4 hidden md:flex h-full overflow-hidden">
    <div class="flex flex-col gap-8 flex-1 min-h-0">
        <div class="flex items-center gap-3 px-2">
            <?php $sidebarLogoUrl = company_logo_url(); ?>
            <?php if ($sidebarLogoUrl): ?>
            <div class="rounded-lg size-10 flex-shrink-0 overflow-hidden bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                <img src="<?= esc($sidebarLogoUrl) ?>" alt="<?= esc(company_brand_name()) ?> logo" class="size-10 object-contain" />
            </div>
            <?php else: ?>
            <div class="bg-center bg-no-repeat bg-cover rounded-lg size-10 bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-3xl">shield_person</span>
            </div>
            <?php endif; ?>
            <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white"><?= esc(company_brand_name()) ?></h1>
        </div>
        <nav class="flex flex-col gap-2 overflow-y-auto pr-1 custom-scrollbar">

            <!-- Dashboard (Visitor / Staff / Vendor — only the ones this user may open) -->
            <?php
            helper('dashboard_nav');
            $dashTabs   = dashboard_tabs();
            $dashActive = $current === 'dashboard/staff' ? 'staff' : ($current === 'dashboard/vendor' ? 'vendor' : (($current === '' || $current === 'dashboard') ? 'visitor' : ''));
            ?>
            <?php if (count($dashTabs) === 1): $t = $dashTabs[0]; $on = $dashActive === $t['key']; ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $on ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= esc($t['url'], 'attr') ?>">
                <span class="material-symbols-outlined text-[22px] <?= $on ? 'font-medium fill-1' : '' ?> group-hover:scale-110 transition-transform">dashboard</span>
                <p class="text-sm <?= $on ? 'font-semibold' : 'font-medium' ?>">Dashboard</p>
            </a>
            <?php elseif (count($dashTabs) > 1): ?>
            <details class="group/dash" <?= $dashActive !== '' ? 'open' : '' ?>>
                <summary class="flex cursor-pointer list-none items-center gap-3 px-3 py-2.5 rounded-lg <?= $dashActive !== '' ? 'text-primary' : 'text-slate-600 dark:text-slate-400' ?> hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[22px]">dashboard</span>
                    <p class="flex-1 text-sm font-medium">Dashboard</p>
                    <span class="material-symbols-outlined text-[18px] transition-transform group-open/dash:rotate-180">expand_more</span>
                </summary>
                <div class="mt-1 ml-5 flex flex-col gap-1 border-l border-slate-200 pl-3 dark:border-slate-700">
                    <?php foreach ($dashTabs as $t): $on = $dashActive === $t['key']; ?>
                    <a class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm <?= $on ? 'bg-primary/10 font-semibold text-primary' : 'font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors" href="<?= esc($t['url'], 'attr') ?>">
                        <span class="material-symbols-outlined text-[18px]"><?= esc($t['icon']) ?></span><?= esc($t['label']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </details>
            <?php endif; ?>

            <!-- E-Map -->
            <?php
            helper('role');
            $emapRole = normalize_role_slug((string) session()->get('role'));
            $hasEmapAccess = in_array($emapRole, ['superadmin', 'clientsuperadmin', 'admin', 'officer'], true);
            ?>
            <?php if ($hasEmapAccess && client_feature_enabled('emap')): ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isEmap ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('e-map') ?>">
                <span class="material-symbols-outlined text-[22px] <?= $isEmap ? 'font-medium fill-1' : '' ?> group-hover:scale-110 transition-transform">map</span>
                <p class="text-sm <?= $isEmap ? 'font-semibold' : 'font-medium' ?>">E-Map</p>
            </a>
            <?php endif; ?>

            <!-- Visitor Pass List -->
            <?php if ($hasVisitorPassAccess): ?>
            <div x-data="{ openVisitorPass: <?= (str_contains($current, 'invitations') || str_contains($current, 'requests') || str_contains($current, 'visitors')) ? 'true' : 'false' ?> }">
                <button type="button" @click="openVisitorPass = !openVisitorPass"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg <?= (str_contains($current, 'invitations') || str_contains($current, 'requests') || str_contains($current, 'visitors')) ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary' ?> transition-colors group">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">badge</span>
                        <p class="text-sm font-medium">Visitor Pass List</p>
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-200" :class="openVisitorPass ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="openVisitorPass"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="ml-4 mt-1 flex flex-col gap-1">
                    <?php if (client_feature_enabled('invitations') && has_access('visitor_pass_list', 'invitations')): ?>
                    <a href="<?= base_url('invitations') ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'invitations') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'invitations') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                        Invitations
                    </a>
                    <?php endif; ?>
                    <?php if ($requestListAvailable && has_access('visitor_pass_list', 'request_list')): ?>
                    <a href="<?= base_url('requests') ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'requests') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'requests') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                        Request List
                    </a>
                    <?php endif; ?>
                    <?php if (has_access('visitor_pass_list', 'visitors_list')): ?>
                    <a href="<?= base_url('visitors') ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'visitors') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'visitors') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                        Visitors List
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Staff Pass List -->
            <?php if (client_feature_enabled('staff_pass') && has_access('staff_pass_list', 'view')): ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isStaff ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('staffs') ?>">
                <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">badge</span>
                <p class="text-sm <?= $isStaff ? 'font-semibold' : 'font-medium' ?>">Staff Pass List</p>
            </a>
            <?php endif; ?>

            <!-- Vendor Pass List -->
            <?php if (client_feature_enabled('vendor_pass') && has_access('vendor_pass_list', 'view')): ?>
            <div x-data="{ openVendor: <?= $isVendor ? 'true' : 'false' ?> }">
                <button type="button" @click="openVendor = !openVendor"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg <?= $isVendor ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary' ?> transition-colors group">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">local_shipping</span>
                        <p class="text-sm font-medium">Vendor Pass List</p>
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-200" :class="openVendor ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="openVendor"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="ml-4 mt-1 flex flex-col gap-1">
                    <a href="<?= base_url('vendors') ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'vendors' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $current == 'vendors' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                        Pass List
                    </a>
                    <?php helper('vendor_company'); if (! is_vendor_admin()): // staff-only pipeline pages ?>
                    <a href="<?= base_url('vendors/process-list') ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'vendors/process-list' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $current == 'vendors/process-list' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                        Process List
                    </a>
                    <a href="<?= base_url('vendors/printing-list') ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'vendors/printing-list' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $current == 'vendors/printing-list' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                        Printing List
                    </a>
                    <a href="<?= base_url('vendors/issuance-list') ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'vendors/issuance-list' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $current == 'vendors/issuance-list' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                        Issuance List
                    </a>
                    <a href="<?= base_url('vendors/closed-list') ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'vendors/closed-list' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $current == 'vendors/closed-list' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                        Closed List
                    </a>
                    <?php endif; ?>
                    <?php if (has_access('vendor_pass_list', 'manage_locations')): ?>
                    <a href="<?= base_url('vendors/locations') ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'vendors/locations' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $current == 'vendors/locations' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                        Locations
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Visitor Workflow -->
            <?php if (has_access('visitor_workflow', 'view')): ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isWorkflow ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('workflow') ?>">
                <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">account_tree</span>
                <p class="text-sm <?= $isWorkflow ? 'font-semibold' : 'font-medium' ?>">Visitor Workflow</p>
            </a>
            <?php endif; ?>

            <!-- Blacklist -->
            <?php if ($hasBlacklistAccess && client_feature_enabled('blacklist')): ?>
            <div x-data="{ openBlacklist: <?= str_contains($current, 'blacklist') ? 'true' : 'false' ?>, openIndividual: <?= str_contains($current, 'blacklist') ? 'true' : 'false' ?> }">
                <button type="button" @click="openBlacklist = !openBlacklist"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg <?= str_contains($current, 'blacklist') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary' ?> transition-colors group">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">person_cancel</span>
                        <p class="text-sm font-medium">Blacklist</p>
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-200" :class="openBlacklist ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="openBlacklist"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="ml-4 mt-1 flex flex-col gap-1">
                    <button type="button" @click="openIndividual = !openIndividual"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg <?= str_contains($current, 'blacklist') ? 'text-primary bg-primary/5' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary' ?> transition-colors group">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[18px] group-hover:scale-110 transition-transform">person</span>
                            <p class="text-sm font-medium">Individual</p>
                        </div>
                        <span class="material-symbols-outlined text-[16px] transition-transform duration-200" :class="openIndividual ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="openIndividual"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="ml-4 mt-1 flex flex-col gap-1">
                        <?php if (has_access('blacklist', 'individual_request_list') || has_access('blacklist', 'request_list')): ?>
                        <a href="<?= base_url('blacklist/blacklistrequest') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'blacklist/blacklistrequest' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $current == 'blacklist/blacklistrequest' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            Request List
                        </a>
                        <?php endif; ?>
                        <?php if (has_access('blacklist', 'individual_closed_list') || has_access('blacklist', 'closed_list')): ?>
                        <a href="<?= base_url('blacklist/closedlist') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'blacklist/closedlist' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $current == 'blacklist/closedlist' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                            Closed List
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Report -->
            <?php if ($hasReportAccess): ?>
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
                    <?php if (has_access('report', 'access_report')): ?>
                    <a href="<?= base_url('report/access') ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'report/access' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $current == 'report/access' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                        Access Report
                    </a>
                    <?php endif; ?>
                    <?php if (has_access('report', 'visitor_report')): ?>
                    <a href="<?= base_url('report/visitor') ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'report/visitor' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $current == 'report/visitor' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                        Visitor Report
                    </a>
                    <?php endif; ?>
                    <?php if (has_access('report', 'visitor_chronology')): ?>
                    <a href="<?= base_url('report/chronology') ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'report/chronology') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'report/chronology') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                        Visitor Chronology
                    </a>
                    <?php endif; ?>
                    <?php if (has_access('report', 'visitor_info_by_door')): ?>
                    <a href="<?= base_url('report/bydoor') ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= str_contains($current, 'report/bydoor') ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= str_contains($current, 'report/bydoor') ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                        Visitor Info By Door
                    </a>
                    <?php endif; ?>

                    <?php if (has_access('report', 'vendor_report')): ?>
                    <a href="<?= base_url('report/vendor') ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $current == 'report/vendor' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $current == 'report/vendor' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                        Vendor Report
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Config -->
            <?php if ($hasConfigAccess): ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isConfig ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('config') ?>">
                <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">tune</span>
                <p class="text-sm <?= $isConfig ? 'font-semibold' : 'font-medium' ?>">Config</p>
            </a>
            <?php endif; ?>

            <!-- List Columns (which columns a client can see) -->
            <?php if (is_platform_superadmin() || is_client_superadmin()): $isListCols = str_contains($current, 'config/list-columns'); ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isListCols ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('config/list-columns') ?>">
                <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">view_column</span>
                <p class="text-sm <?= $isListCols ? 'font-semibold' : 'font-medium' ?>">List Columns</p>
            </a>
            <?php endif; ?>

            <!-- Dashboard Cards (which cards a client's dashboards have) -->
            <?php if (is_platform_superadmin() || is_client_superadmin()): $isDashCards = str_contains($current, 'config/dashboard-cards'); ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isDashCards ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('config/dashboard-cards') ?>">
                <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">widgets</span>
                <p class="text-sm <?= $isDashCards ? 'font-semibold' : 'font-medium' ?>">Dashboard Cards</p>
            </a>
            <?php endif; ?>

            <!-- Settings — all roles -->
            <?php if (has_access('settings', 'view')): ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isSettings ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('settings') ?>">
                <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">settings</span>
                <p class="text-sm <?= $isSettings ? 'font-semibold' : 'font-medium' ?>">Settings</p>
            </a>
            <?php endif; ?>

        </nav>
    </div>
    <div class="border-t border-slate-200 dark:border-slate-700 pt-4 px-2">
        <div class="flex items-center gap-3">
            <div class="size-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shadow-sm ring-2 ring-white dark:ring-slate-900">
                <?= strtoupper(substr(session()->get('full_name') ?? 'U', 0, 2)) ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-900 dark:text-white truncate"><?= esc(session()->get('full_name') ?? 'User') ?></p>
                <p class="text-xs text-slate-500 dark:text-slate-400 truncate"><?= esc(role_display_name(session()->get('role'))) ?></p>
                <?php
                $sidebarClient = current_client_name();
                if ($sidebarClient): ?>
                    <p class="text-xs text-slate-400 dark:text-slate-500 truncate" title="<?= esc($sidebarClient) ?>"><?= esc($sidebarClient) ?></p>
                <?php elseif (is_platform_superadmin() && current_client_id() === 0): ?>
                    <p class="text-xs text-slate-400 dark:text-slate-500 truncate">Platform</p>
                <?php endif; ?>
            </div>
            <a href="<?= base_url('auth/logout') ?>" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined text-xl">logout</span>
            </a>
        </div>
    </div>
</aside>
