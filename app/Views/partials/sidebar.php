<?php
helper(['access', 'navigation']);
$current     = app_route_path();
$isDashboard = ($current === '' || $current === 'dashboard');
$isStaff     = str_contains($current, 'staffs') || str_contains($current, 'staff-pass-request');
$isWorkflow  = str_contains($current, 'workflow');
$isConfig    = str_contains($current, 'config');
$isSettings  = str_contains($current, 'settings');

$hasVisitorPassAccess = has_access('visitor_pass_list', 'invitations') || has_access('visitor_pass_list', 'request_list') || has_access('visitor_pass_list', 'visitors_list');
$hasBlacklistAccess   = has_access('blacklist', 'request_list') || has_access('blacklist', 'closed_list') || has_access('blacklist', 'individual_request_list') || has_access('blacklist', 'individual_closed_list') || has_access('blacklist', 'company_request_list') || has_access('blacklist', 'company_closed_list');
$hasReportAccess      = has_access('report', 'access_report') || has_access('report', 'visitor_report') || has_access('report', 'visitor_chronology') || has_access('report', 'visitor_info_by_door') || has_access('report', 'gate_in_out') || has_access('report', 'out_window_list') || has_access('report', 'port_pass_monthly') || has_access('report', 'port_pass_summary') || has_access('report', 'company_permit_ageing') || has_access('report', 'company_permit_monthly') || has_access('report', 'vehicle_sticker_summary') || has_access('report', 'blacklist_report') || has_access('report', 'attendance_report') || has_access('report', 'monitoring_report');
$hasConfigAccess      = has_access('config', 'view') || has_access('config', 'alert_priority') || has_access('config', 'api_management') || has_access('config', 'general_settings') || has_access('config', 'application_settings') || has_access('config', 'role_management') || has_access('config', 'user_management') || has_access('config', 'company');
?>

<aside class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col p-4 hidden md:flex h-full overflow-hidden">
    <div class="flex flex-col gap-8 flex-1 min-h-0">
        <div class="flex items-center gap-3 px-2">
            <div class="bg-center bg-no-repeat bg-cover rounded-lg size-10 bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-3xl">shield_person</span>
            </div>
            <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">SafeG</h1>
        </div>
        <nav class="flex flex-col gap-2 overflow-y-auto pr-1 custom-scrollbar">

            <!-- Dashboard -->
            <?php if (has_access('dashboard', 'main_menu')): ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isDashboard ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('dashboard') ?>">
                <span class="material-symbols-outlined text-[22px] <?= $isDashboard ? 'font-medium fill-1' : '' ?> group-hover:scale-110 transition-transform">dashboard</span>
                <p class="text-sm <?= $isDashboard ? 'font-semibold' : 'font-medium' ?>">Dashboard</p>
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
                    <?php if (has_access('visitor_pass_list', 'request_list')): ?>
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

            <!-- Visitor Workflow -->
            <?php if (has_access('visitor_workflow', 'view')): ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isWorkflow ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white' ?> transition-colors group" href="<?= base_url('workflow') ?>">
                <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">account_tree</span>
                <p class="text-sm <?= $isWorkflow ? 'font-semibold' : 'font-medium' ?>">Visitor Workflow</p>
            </a>
            <?php endif; ?>

            <!-- Blacklist -->
            <?php
                $_blCompanyId = (int) (
                    session()->get('company_id')
                    ?: ((new \App\Models\UserModel())->find((int) session()->get('user_id'))['company_id'] ?? 0)
                );
                $_blEnabled = session()->get('role') === 'superadmin'
                    || (new \App\Models\ClientFeatureModel())->isEnabled($_blCompanyId, 'blacklist');
            ?>
            <?php if ($hasBlacklistAccess && $_blEnabled): ?>
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
                <p class="text-xs text-slate-500 dark:text-slate-400 truncate"><?= esc(ucfirst(session()->get('role') ?? 'User')) ?></p>
            </div>
            <a href="<?= base_url('auth/logout') ?>" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined text-xl">logout</span>
            </a>
        </div>
    </div>
</aside>
