<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
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
                    fontFamily: {
                        "display": ["Montserrat", "sans-serif"],
                        "sans": ["Montserrat", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.375rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        /* DataTables — security alerts modal (match visitor report) */
        #dash-modal .dataTables_wrapper .dataTables_length select {
            appearance: none;
            background-color: white;
            border: 1px solid #d1d5db;
            color: #374151;
            padding: 0.375rem 2rem 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            outline: none;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%236b7280'%3e%3cpath d='M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 1.25em;
        }
        #dash-modal .dataTables_wrapper .dataTables_length label { margin: 0; color: transparent; }
        #dash-modal .dataTables_wrapper .dataTables_filter label {
            font-size: 1rem;
            color: #334155;
            display: flex;
            align-items: center;
        }
        #dash-modal .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #cbd5e1;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            outline: none;
            min-width: 200px;
            margin-left: 0.5rem;
        }
        #dash-modal .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #137fec;
            box-shadow: 0 0 0 2px rgba(19,127,236,0.15);
        }
        #dash-modal .dataTables_wrapper .dataTables_info,
        #dash-modal .dataTables_wrapper .dataTables_length,
        #dash-modal .dataTables_wrapper .dataTables_filter {
            font-size: 0.8rem;
            color: #64748b;
        }
        #dash-modal .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.25rem !important;
            border: 1px solid #e2e8f0 !important;
            font-size: 0.8rem;
            padding: 0.3rem 0.85rem !important;
            margin: 0 0.15rem;
            background: white !important;
            color: #64748b !important;
        }
        #dash-modal .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        #dash-modal .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #137fec !important;
            color: white !important;
            border: none !important;
        }
        #dash-modal .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e0effe !important;
            color: #137fec !important;
            border: none !important;
        }
        #dash-modal table.dataTable thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 0.85rem 1rem;
            white-space: nowrap;
            overflow: visible;
        }
        #dash-modal table.dataTable tbody td {
            font-size: 0.82rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        #dash-modal table.dataTable tbody tr:hover td { background: #f0f7ff; }
        #dash-modal table.dataTable { border-collapse: collapse !important; width: 100% !important; }
        #dash-modal .dash-alerts-dt-scroll {
            min-height: 16rem;
        }
        @media (min-width: 768px) {
            #dash-modal .dash-alerts-dt-scroll { min-height: 22rem; }
        }
        body > .dash-alerts-filter-dropdown {
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.18);
        }
        #analytics-assistant-panel {
            transition: width .2s ease, height .2s ease, inset .2s ease, border-radius .2s ease, transform .2s ease;
        }
        #analytics-assistant-panel.analytics-assistant-expanded {
            inset: 0.75rem !important;
            width: auto !important;
            height: auto !important;
            max-width: none !important;
            max-height: none !important;
            border-radius: 0.75rem !important;
        }
        #analytics-assistant-panel.analytics-assistant-minimized {
            transform: translateY(calc(100% + 2rem));
            pointer-events: none;
        }
        @media (max-width: 640px) {
            #analytics-assistant-launcher {
                left: 1rem;
                right: 1rem;
                width: auto;
                justify-content: center;
            }
            #analytics-assistant-panel {
                left: 0.75rem !important;
                right: 0.75rem !important;
                bottom: 0.75rem !important;
                width: auto !important;
                height: min(78vh, 620px) !important;
            }
        }
        /* Widget edit mode */
        .widget-drag-handle { cursor: grab; display: none; }
        body.widget-edit-mode .widget-drag-handle { display: flex; }
        body.widget-edit-mode .widget-hide-btn { display: flex !important; }
        body.widget-edit-mode .widget-card-content { pointer-events: none; }
        .widget-hide-btn { display: none; }
        .sortable-ghost { opacity: 0.35; }
        .sortable-chosen { cursor: grabbing; }
        /* Height chain: grid cell → wrapper → content → card */
        .widget-wrapper { display: flex; flex-direction: column; }
        .widget-card-content { flex: 1 1 0%; min-height: 0; display: flex; flex-direction: column; }
        .widget-card-content > * { flex: 1 1 0%; min-height: 0; }
        /* Customize drawer */
        #widget-drawer { transform: translateX(100%); transition: transform 0.3s ease; }
        #widget-drawer.open { transform: translateX(0); }
    </style>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- Blacklist dropdown function-->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white overflow-hidden">
<div class="flex h-screen w-full">
    <!-- Sidebar -->
    <?= view("partials/sidebar") ?>


    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <!-- Header -->
        <header class="flex-shrink-0 border-b border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark px-8 py-3 flex items-center justify-between z-10 min-h-[5rem]">
            <button class="md:hidden p-2 text-slate-600 dark:text-slate-400">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <div class="flex flex-col justify-center">
                <h2 class="text-slate-900 dark:text-white text-lg font-bold leading-tight">Host Dashboard</h2>
                <p class="text-slate-500 dark:text-slate-400 text-xs">Today, <?= $currentDate ?></p>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative hidden sm:block">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input class="h-10 pl-10 pr-4 text-sm bg-slate-100 dark:bg-slate-800 border-none rounded-full focus:ring-2 focus:ring-primary text-slate-900 dark:text-white placeholder-slate-400 w-64" placeholder="Search visitors..." type="text"/>
                </div>
                <div class="flex items-center gap-2">
                    <select class="text-sm bg-transparent font-medium text-slate-600 dark:text-slate-400 border-none focus:ring-0 cursor-pointer">
                        <option value="en">EN</option>
                        <option value="ms">MS</option>
                    </select>
                </div>
                <button class="flex items-center justify-center size-10 rounded-full text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors relative">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border border-white dark:border-slate-900"></span>
                </button>
                <button id="widget-customize-btn" onclick="openWidgetDrawer()" class="flex items-center gap-1.5 h-10 px-4 bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <span class="material-symbols-outlined text-[20px]">dashboard_customize</span>
                    <span class="hidden sm:inline">Customize</span>
                </button>
                <div class="flex flex-col gap-2 items-end">
                    <a href="<?= base_url('invitations/create') ?>" class="flex items-center justify-center h-10 px-4 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-lg shadow-sm transition-colors gap-2 w-full sm:w-48">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span class="hidden sm:inline">New Invitation</span>
                    </a>
                    <a href="<?= base_url('visitor-pass-request') ?>" class="flex items-center justify-center h-10 px-4 bg-surface-light dark:bg-surface-dark border border-primary text-primary hover:bg-slate-50 dark:hover:bg-slate-800 text-sm font-bold rounded-lg shadow-sm transition-colors gap-2 w-full sm:w-48">
                        <span class="material-symbols-outlined text-[20px]">person_add</span>
                        <span class="hidden sm:inline">Walk-in Visitor</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 pb-20 no-scrollbar">
            <!-- Welcome Section -->
            <div class="flex flex-col gap-2 mb-6">
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Welcome back, <?= esc(session()->get('full_name') ?? $userName) ?></h1>
                <p class="text-slate-500 dark:text-slate-400 text-base">Here's what's happening at your facility today.</p>
            </div>

            <?php if (!empty($criticalAlerts)): ?>
            <script type="application/json" id="critical-alerts-json"><?= json_encode($criticalAlerts, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?></script>
            <?php endif; ?>

            <div id="ack-access-denied-prompt" class="hidden"></div>

            <!-- Widgets Grid -->
            <div id="widgets-container" class="grid grid-cols-2 xl:grid-cols-4 gap-6">

            <?php
            $colSpans = \App\Models\DashboardWidgetPreferenceModel::$colSpans;
            foreach ($widgetPreferences as $wp):
                $wid     = $wp['id'];
                $visible = $wp['visible'] ?? true;
                $span    = $colSpans[$wid] ?? 'col-span-1';
            ?>
            <div data-widget-id="<?= $wid ?>" class="widget-wrapper <?= $span ?><?= $visible ? '' : ' hidden' ?>">
                <!-- drag handle + hide button (only visible in edit mode) -->
                <div class="widget-drag-handle items-center justify-between bg-slate-100 dark:bg-slate-800 rounded-t-xl px-3 py-1.5 border border-b-0 border-slate-200 dark:border-slate-700 select-none">
                    <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                        <span class="material-symbols-outlined text-[18px]">drag_indicator</span>
                        <span class="text-xs font-medium"><?= esc($wp['label']) ?></span>
                    </div>
                    <button onclick="hideWidget('<?= $wid ?>')" class="widget-hide-btn items-center gap-1 text-xs text-red-500 hover:text-red-700 transition-colors px-2 py-0.5 rounded hover:bg-red-50 dark:hover:bg-red-900/20">
                        <span class="material-symbols-outlined text-[16px]">visibility_off</span>
                    </button>
                </div>
                <div class="widget-card-content">
                <?php if ($wid === 'critical-alert'): ?>
                    <!-- Critical Security Alert -->
                    <div id="critical-alert-section">
                        <div id="critical-alert-active-wrapper" class="<?= !empty($criticalAlerts) ? '' : 'hidden' ?> bg-gradient-to-r from-red-900 via-red-800 to-red-900 rounded-xl p-5 border border-red-700 shadow-lg relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-red-700/20 rounded-full -mr-10 -mt-10"></div>
                            <div id="critical-alert-card-content" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10"></div>
                        </div>
                        <div id="critical-alert-all-clear-wrapper" class="<?= !empty($criticalAlerts) ? 'hidden' : '' ?> bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 rounded-xl p-5 border border-slate-600 shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-slate-600/20 rounded-full -mr-10 -mt-10"></div>
                            <div class="flex items-center gap-4 relative z-10">
                                <div class="size-10 rounded-lg bg-green-700/30 flex items-center justify-center text-green-300 flex-shrink-0"><span class="material-symbols-outlined fill-1">verified_user</span></div>
                                <div class="flex-1"><h3 class="text-white font-bold text-base">Critical Security Alert</h3><p class="text-slate-300 text-sm mt-0.5">No critical security alerts at this time. All clear.</p></div>
                                <button type="button" onclick="openModal('activeAlerts')" class="flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-bold rounded-lg transition-colors backdrop-blur-sm flex-shrink-0"><span class="material-symbols-outlined text-[18px]">shield</span> View All Alerts</button>
                            </div>
                        </div>
                    </div>
                <?php elseif ($wid === 'access-denied'): ?>
                    <!-- Access Denied Widget -->
                    <div onclick="openModal('accessDenied')" class="bg-gradient-to-br from-red-800 to-red-900 rounded-xl p-6 border border-red-700 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow cursor-pointer h-full">
                        <div class="absolute top-3 right-3"><span class="px-2 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full">Access Denied</span></div>
                        <p class="text-4xl font-black text-white mb-1" id="dash-widget-access-denied"><?= $accessDeniedCount ?></p>
                        <p class="text-red-100 font-semibold text-sm">Access Denied Incidents</p>
                        <p class="text-red-300 text-xs mt-1">Acknowledged — last 24 hours</p>
                        <div class="flex items-center gap-1 mt-3 text-red-200 text-xs font-medium group-hover:text-white transition-colors"><span class="material-symbols-outlined text-[14px]">arrow_forward</span> View all incidents</div>
                    </div>
                <?php elseif ($wid === 'overstay-alerts'): ?>
                    <!-- Overstay Alerts Widget -->
                    <div onclick="openModal('overstay')" class="bg-gradient-to-br from-amber-700 to-amber-800 rounded-xl p-6 border border-amber-600 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow cursor-pointer h-full">
                        <div class="absolute top-3 right-3"><span class="px-2 py-0.5 bg-amber-500 text-white text-[10px] font-bold rounded-full">Overstay</span></div>
                        <p class="text-4xl font-black text-white mb-1" id="dash-widget-overstay"><?= $overstayCount ?></p>
                        <p class="text-amber-100 font-semibold text-sm">Visitor Overstay Alerts</p>
                        <p class="text-amber-300 text-xs mt-1">Active alerts</p>
                        <div class="flex items-center gap-1 mt-3 text-amber-200 text-xs font-medium group-hover:text-white transition-colors"><span class="material-symbols-outlined text-[14px]">arrow_forward</span> View all alerts</div>
                    </div>
                <?php elseif ($wid === 'stat-expected'): ?>
                    <!-- Expected Today -->
                    <div onclick="openModal('expectedToday')" class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-4 relative overflow-hidden group cursor-pointer hover:border-indigo-300 dark:hover:border-indigo-800 hover:ring-1 hover:ring-indigo-100 dark:hover:ring-indigo-900/50 transition-all h-full">
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><span class="material-symbols-outlined text-6xl text-slate-900 dark:text-white">calendar_today</span></div>
                        <div class="size-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400"><span class="material-symbols-outlined">calendar_month</span></div>
                        <div>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Expected Today</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-3xl font-bold text-slate-900 dark:text-white"><?= $stats['expectedToday'] ?></p>
                                <?php if ($trend != 0): ?><span class="text-xs <?= $trend > 0 ? 'text-green-600' : 'text-red-600' ?> font-medium flex items-center <?= $trend > 0 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' ?> px-1.5 py-0.5 rounded"><span class="material-symbols-outlined text-[14px] mr-0.5"><?= $trend > 0 ? 'trending_up' : 'trending_down' ?></span> <?= ($trend > 0 ? '+' : '') . $trend ?></span><?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php elseif ($wid === 'stat-onsite'): ?>
                    <!-- Currently On-Site Stat -->
                    <div onclick="openModal('onSite')" class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-4 relative overflow-hidden group cursor-pointer hover:border-primary/50 dark:hover:border-primary/50 hover:ring-1 hover:ring-primary/10 transition-all h-full">
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><span class="material-symbols-outlined text-6xl text-primary">group</span></div>
                        <div class="size-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary"><span class="material-symbols-outlined fill-1">check_circle</span></div>
                        <div>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Currently On-Site</p>
                            <div class="flex items-baseline gap-2"><p class="text-3xl font-bold text-slate-900 dark:text-white"><?= $stats['currentlyOnSite'] ?></p><span class="text-sm text-slate-400 font-normal">visitors</span></div>
                        </div>
                    </div>
                <?php elseif ($wid === 'stat-checkedout'): ?>
                    <!-- Checked Out Stat -->
                    <div onclick="openModal('checkedOut')" class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-4 relative overflow-hidden group cursor-pointer hover:border-slate-400 dark:hover:border-slate-600 hover:ring-1 hover:ring-slate-200 dark:hover:ring-slate-700/50 transition-all h-full">
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><span class="material-symbols-outlined text-6xl text-slate-900 dark:text-white">logout</span></div>
                        <div class="size-10 rounded-lg bg-slate-100 dark:bg-slate-700/50 flex items-center justify-center text-slate-600 dark:text-slate-400"><span class="material-symbols-outlined">logout</span></div>
                        <div><p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Checked Out</p><p class="text-3xl font-bold text-slate-900 dark:text-white"><?= $stats['checkedOut'] ?></p></div>
                    </div>
                <?php elseif ($wid === 'stat-alerts'): ?>
                    <!-- Security Alerts Stat -->
                    <div onclick="openModal('activeAlerts')" class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col gap-4 relative overflow-hidden group cursor-pointer hover:border-red-300 dark:hover:border-red-800 transition-colors h-full">
                        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><span class="material-symbols-outlined text-6xl text-slate-900 dark:text-white">shield</span></div>
                        <div class="size-10 rounded-lg bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400"><span class="material-symbols-outlined fill-1">shield</span></div>
                        <div><p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Total Security Alerts</p><p class="text-3xl font-bold text-slate-900 dark:text-white" id="dash-widget-active-alerts"><?= $activeSecurityAlertCount ?></p></div>
                    </div>
                <?php elseif ($wid === 'upcoming-appointments'): ?>
                    <!-- Upcoming Appointments -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col h-full">
                        <div class="flex items-center justify-between mb-4"><div class="flex items-center gap-2"><span class="material-symbols-outlined text-indigo-500 fill-1">event_upcoming</span><h3 class="text-base font-bold text-slate-900 dark:text-white">Upcoming Appointments</h3></div><button onclick="openModal('upcomingAppts')" class="text-xs text-indigo-500 hover:text-indigo-700 font-medium transition-colors">View All</button></div>
                        <?php if (empty($upcomingAppointments)): ?>
                        <div class="flex flex-col items-center justify-center py-8 text-center"><p class="text-4xl font-black text-slate-300 dark:text-slate-600 mb-2">0</p><p class="text-sm text-slate-400 italic">No upcoming appointments</p></div>
                        <?php else: ?>
                        <div class="flex flex-col gap-3 max-h-[200px] overflow-y-auto pr-1">
                            <?php foreach ($upcomingAppointments as $appt): ?>
                            <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <div class="size-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 flex-shrink-0 mt-0.5"><span class="material-symbols-outlined text-[18px]">schedule</span></div>
                                <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-slate-900 dark:text-white truncate"><?= esc($appt['visitor_name']) ?></p><p class="text-xs text-slate-500"><?= esc($appt['time']) ?> - <?= esc($appt['date']) ?></p><p class="text-xs text-slate-400">Host: <?= esc($appt['host_name']) ?></p></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php elseif ($wid === 'today-appointments'): ?>
                    <!-- Today's Appointments -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col h-full">
                        <div class="flex items-center justify-between mb-4"><div class="flex items-center gap-2"><span class="material-symbols-outlined text-emerald-500 fill-1">today</span><h3 class="text-base font-bold text-slate-900 dark:text-white">Today's Appointments</h3><span class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] px-2 py-0.5 rounded-full font-bold"><?= count($todayAppointments) ?></span></div><button onclick="openModal('todayAppts')" class="text-xs text-emerald-500 hover:text-emerald-700 font-medium transition-colors">View All</button></div>
                        <?php if (empty($todayAppointments)): ?>
                        <p class="text-sm text-slate-400 italic text-center py-4">No appointments today</p>
                        <?php else: ?>
                        <div class="flex flex-col gap-2.5 max-h-[250px] overflow-y-auto pr-1">
                            <?php foreach ($todayAppointments as $ta): ?>
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 dark:border-slate-700 hover:border-emerald-200 dark:hover:border-emerald-800 transition-colors">
                                <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-slate-900 dark:text-white truncate"><?= esc($ta['visitor_name']) ?></p><p class="text-xs text-slate-500"><?= esc($ta['time']) ?> - <?= esc($ta['end_time']) ?></p><p class="text-xs text-slate-400">Host: <?= esc($ta['host_name']) ?></p></div>
                                <?php $apptStatusColor = match($ta['status']) { 'In Progress' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'Completed' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400', default => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }; ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold flex-shrink-0 <?= $apptStatusColor ?>"><?= esc($ta['status']) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php elseif ($wid === 'occupancy-chart'): ?>
                    <!-- Visitor Occupancy Chart -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col h-full">
                        <div class="flex justify-between items-start mb-6">
                            <div><h3 class="text-base font-bold text-slate-900 dark:text-white">Visitor Occupancy</h3><p class="text-sm text-slate-500 dark:text-slate-400">Real-time capacity tracking</p></div>
                            <div class="text-right"><p class="text-2xl font-bold text-primary"><?= $stats['currentlyOnSite'] ?></p><p class="text-xs text-slate-400">Capacity</p></div>
                        </div>
                        <div class="grid grid-cols-12 gap-2 items-end px-2 flex-1" style="min-height:8rem;">
                            <?php foreach ($occupancyChart as $bar): ?>
                            <div class="flex flex-col items-center gap-2 h-full justify-end group cursor-pointer">
                                <div class="relative w-full max-w-[40px] bg-indigo-50 dark:bg-slate-800 rounded-t-sm h-full flex items-end overflow-hidden">
                                    <div class="w-full <?= $bar['isPeak'] ? 'bg-primary' : 'bg-indigo-200 dark:bg-indigo-900' ?> transition-all duration-500<?= $bar['isPeak'] ? ' relative' : '' ?>" style="height: <?= max($bar['percentage'], 2) ?>%">
                                        <?php if ($bar['isPeak']): ?><div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10"><?= $bar['label'] ?> (Peak: <?= $bar['count'] ?>)</div><?php endif; ?>
                                    </div>
                                </div>
                                <span class="text-xs <?= $bar['isPeak'] ? 'font-bold text-primary' : 'font-medium text-slate-500' ?>"><?= $bar['label'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php elseif ($wid === 'recent-activity'): ?>
                    <!-- Recent Activity -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col h-full">
                        <div class="flex justify-between items-center mb-4">
                            <div><h3 class="text-base font-bold text-slate-900 dark:text-white">Recent Activity</h3><p class="text-xs text-slate-400 mt-0.5">All system events from the last 7 days</p></div>
                            <button onclick="openModal('recentActivity')" class="text-xs font-medium text-primary hover:text-primary-dark">View All</button>
                        </div>
                        <div class="flex flex-wrap gap-x-3 gap-y-1 mb-4">
                            <span class="flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400"><span class="size-2 rounded-full bg-amber-400 inline-block"></span>Created</span>
                            <span class="flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400"><span class="size-2 rounded-full bg-green-500 inline-block"></span>Approved</span>
                            <span class="flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400"><span class="size-2 rounded-full bg-red-500 inline-block"></span>Rejected</span>
                            <span class="flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400"><span class="size-2 rounded-full bg-blue-500 inline-block"></span>Door Access</span>
                            <span class="flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400"><span class="size-2 rounded-full bg-slate-400 inline-block"></span>Check-out</span>
                        </div>
                        <div class="flex flex-col gap-0 overflow-y-auto max-h-[340px] pr-1 custom-scrollbar divide-y divide-slate-100 dark:divide-slate-700/50">
                            <?php if (empty($recentActivity)): ?>
                            <p class="text-sm text-slate-400 text-center py-6">No recent activity in the last 7 days.</p>
                            <?php else: ?>
                            <?php foreach ($recentActivity as $activity): ?>
                            <div class="flex gap-3 items-start py-3">
                                <div class="size-8 rounded-full <?= $activity['iconBg'] ?> flex items-center justify-center flex-shrink-0 mt-0.5"><span class="material-symbols-outlined text-[17px] <?= $activity['iconColor'] ?>"><?= $activity['icon'] ?></span></div>
                                <div class="flex flex-col flex-1 min-w-0"><p class="text-sm text-slate-900 dark:text-white leading-snug"><?= $activity['description'] ?>.</p><p class="text-xs text-slate-400 mt-0.5"><?= $activity['time'] ?> • <?= $activity['location'] ?></p></div>
                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full flex-shrink-0 mt-1 <?php echo match($activity['type']) { 'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', 'check_in' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'check_out' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400', 'door_access' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'security_alert' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }; ?>"><?= esc($activity['label']) ?></span>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php elseif ($wid === 'visitors-table'): ?>
                    <!-- Visitors Table -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <!-- Tabs -->
                <div class="border-b border-slate-200 dark:border-slate-700 px-6 pt-2">
                    <div class="flex gap-8 overflow-x-auto no-scrollbar">
                        <button type="button" data-visitor-tab="all" class="flex items-center gap-2 border-b-[3px] border-primary text-primary pb-3 pt-2 cursor-pointer group whitespace-nowrap">
                            <p class="text-sm font-bold">All Visitors</p>
                            <span data-tab-count class="bg-primary/10 text-primary text-[10px] px-1.5 py-0.5 rounded-full font-bold"><?= $tabCounts['all'] ?></span>
                        </button>
                        <button type="button" data-visitor-tab="prearrival" class="flex items-center gap-2 border-b-[3px] border-transparent hover:border-slate-300 dark:hover:border-slate-600 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 pb-3 pt-2 cursor-pointer transition-all whitespace-nowrap">
                            <p class="text-sm font-medium">Pre-Arrival</p>
                            <span data-tab-count class="bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] px-1.5 py-0.5 rounded-full font-bold"><?= $tabCounts['preArrival'] ?></span>
                        </button>
                        <button type="button" data-visitor-tab="checkedin" class="flex items-center gap-2 border-b-[3px] border-transparent hover:border-slate-300 dark:hover:border-slate-600 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 pb-3 pt-2 cursor-pointer transition-all whitespace-nowrap">
                            <p class="text-sm font-medium">Checked In</p>
                            <span data-tab-count class="bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] px-1.5 py-0.5 rounded-full font-bold"><?= $tabCounts['checkedIn'] ?></span>
                        </button>
                        <button type="button" data-visitor-tab="checkedout" class="flex items-center gap-2 border-b-[3px] border-transparent hover:border-slate-300 dark:hover:border-slate-600 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 pb-3 pt-2 cursor-pointer transition-all whitespace-nowrap">
                            <p class="text-sm font-medium">Checked Out</p>
                            <span data-tab-count class="bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] px-1.5 py-0.5 rounded-full font-bold"><?= $tabCounts['checkedOut'] ?></span>
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="p-4 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50 dark:bg-slate-800/20 flex-wrap">
                    <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto">
                        <div class="relative w-full sm:w-auto flex-1 sm:flex-initial">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">filter_list</span>
                            <select id="visitor-company-filter" onchange="applyVisitorFilters()" class="pl-9 pr-8 py-2 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300 w-full sm:w-48 appearance-none cursor-pointer">
                                <option value="">Filter by Company</option>
                                <?php foreach ($companyList as $company): ?>
                                <option value="<?= esc($company) ?>"><?= esc($company) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex-1 sm:flex-initial">
                                <input id="visitor-date-from" onchange="applyVisitorFilters()" class="py-2 px-3 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300 w-full sm:w-auto" placeholder="Start Date" type="datetime-local"/>
                            </div>
                            <span class="text-slate-400 hidden sm:inline">-</span>
                            <div class="relative flex-1 sm:flex-initial">
                                <input id="visitor-date-to" onchange="applyVisitorFilters()" class="py-2 px-3 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300 w-full sm:w-auto" placeholder="End Date" type="datetime-local"/>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto justify-end">
                        <button onclick="exportVisitors()" class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            Export
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Visitor Name</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Host</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="visitors-table-body" class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-surface-dark">
                            <tr id="no-data-row" <?= !empty($visitors) ? 'style="display:none"' : '' ?>>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400 dark:text-slate-500">No data</td>
                            </tr>
                            <?php foreach ($visitors as $visitor): ?>
                            <?php
                                $statusToken = 'prearrival';
                                if ($visitor['status'] === 'On-Site') {
                                    $statusToken = 'checkedin';
                                } elseif ($visitor['status'] === 'Checked Out') {
                                    $statusToken = 'checkedout';
                                }
                            ?>
                            <tr data-visitor-status="<?= $statusToken ?>" data-invitation-id="<?= $visitor['id'] ?>" data-company="<?= esc(strtolower($visitor['company'])) ?>" data-date="<?= esc($visitor['date_raw']) ?>" class="visitor-row group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <?php if ($visitor['hasImage']): ?>
                                        <div class="size-9 rounded-full bg-cover bg-center" style="background-image: url('<?= $visitor['image'] ?>');"></div>
                                        <?php else: ?>
                                        <div class="size-9 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xs font-bold">
                                            <?= $visitor['initials'] ?>
                                        </div>
                                        <?php endif; ?>
                                        <div>
                                            <p class="text-sm font-medium text-slate-900 dark:text-white <?= $visitor['status'] === 'Checked Out' ? 'opacity-60' : '' ?>"><?= esc($visitor['name']) ?></p>
                                            <p class="text-xs text-slate-500"><?= esc($visitor['contact']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300"><?= esc($visitor['company']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300"><?= esc($visitor['time']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300 uppercase"><?= esc($visitor['host']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusClasses = [
                                        'green' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-800 bg-green-500',
                                        'amber' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800 bg-amber-500',
                                        'slate' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700 bg-slate-400'
                                    ];
                                    $classes = explode(' ', $statusClasses[$visitor['statusClass']]);
                                    ?>
                                    <span data-status-badge class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium <?= $classes[0] ?> <?= $classes[1] ?> <?= $classes[2] ?> <?= $classes[3] ?> border <?= $classes[4] ?> <?= $classes[5] ?>">
                                        <span class="size-1.5 rounded-full <?= $classes[6] ?>"></span>
                                        <?= esc($visitor['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <?php if ($visitor['status'] === 'On-Site'): ?>
                                    <button onclick="dashboardViewVisitor(this)" class="text-slate-400 hover:text-primary transition-colors p-1" title="View Details">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>
                                    <button onclick="dashboardCheckOut(this)" class="text-slate-400 hover:text-red-600 transition-colors p-1" title="Check Out">
                                        <span class="material-symbols-outlined text-[20px]">logout</span>
                                    </button>
                                    <?php elseif ($visitor['status'] === 'Pre-Arrival'): ?>
                                    <button onclick="dashboardCheckIn(this)" class="text-slate-400 hover:text-green-600 transition-colors p-1" title="Check In">
                                        <span class="material-symbols-outlined text-[20px]">login</span>
                                    </button>
                                    <button onclick="dashboardEditVisitor(this)" class="text-slate-400 hover:text-primary transition-colors p-1" title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    <?php else: ?>
                                    <button onclick="dashboardViewVisitor(this)" class="text-slate-400 hover:text-primary transition-colors p-1" title="View History">
                                        <span class="material-symbols-outlined text-[20px]">history</span>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-surface-dark px-6 py-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400">Showing <span id="visitors-showing-range" class="font-bold">1-<?= count($visitors) ?></span> of <span id="visitors-total-count" class="font-bold"><?= $tabCounts['all'] ?></span> visitors</p>
                    <div class="flex gap-2">
                        <button class="px-3 py-1 text-xs border border-slate-200 dark:border-slate-700 rounded hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 disabled:opacity-50" disabled>Previous</button>
                        <button class="px-3 py-1 text-xs border border-slate-200 dark:border-slate-700 rounded hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400">Next</button>
                    </div>
                </div>
            </div><!-- /visitors-table outer -->
                <?php elseif ($wid === 'onsite-table'): ?>
                    <!-- Currently On-Site Table -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between p-6 pb-4">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary fill-1">group</span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Currently On-Site</h3>
                                <span class="bg-primary/10 text-primary text-[10px] px-2 py-0.5 rounded-full font-bold"><?= $onSiteVisitorCount ?></span>
                            </div>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[16px]">search</span>
                                <input class="h-8 pl-8 pr-3 text-xs bg-slate-100 dark:bg-slate-800 border-none rounded-full focus:ring-1 focus:ring-primary text-slate-900 dark:text-white placeholder-slate-400 w-40" placeholder="Search visitors..." type="text" id="onsite-search"/>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse" id="onsite-table">
                                <thead>
                                    <tr class="border-y border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Visitor Name</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">IC Number</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date & Time</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Last Door Entry</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Host</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-surface-dark">
                                    <?php if (empty($onSiteVisitors)): ?>
                                    <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-slate-400">No visitors currently on-site</td></tr>
                                    <?php else: ?>
                                    <?php foreach ($onSiteVisitors as $ov): ?>
                                    <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-6 py-3 whitespace-nowrap"><div class="flex items-center gap-2"><div class="size-7 rounded-full bg-primary/10 flex items-center justify-center text-primary text-[10px] font-bold"><?= strtoupper(substr($ov['name'], 0, 2)) ?></div><span class="text-sm font-medium text-slate-900 dark:text-white"><?= esc($ov['name']) ?></span></div></td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300"><?= esc($ov['ic_number']) ?></td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300"><?= esc($ov['check_in_time']) ?></td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300"><?= esc($ov['last_door_entry']) ?></td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300 uppercase"><?= esc($ov['host']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php elseif ($wid === 'traffic-analytics'): ?>
                    <!-- Visitor Traffic Analytics -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                            <div class="flex items-center gap-2"><span class="material-symbols-outlined text-primary fill-1">bar_chart</span><h3 class="text-base font-bold text-slate-900 dark:text-white">Visitor Traffic Analytics</h3></div>
                            <div class="flex items-center gap-3 flex-wrap">
                                <div class="flex items-center gap-2"><label class="text-xs font-medium text-slate-500">From</label><input type="date" id="traffic-from" value="<?= date('Y-m-d') ?>" class="h-9 px-3 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300"/></div>
                                <div class="flex items-center gap-2"><label class="text-xs font-medium text-slate-500">To</label><input type="date" id="traffic-to" value="<?= date('Y-m-d') ?>" class="h-9 px-3 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300"/></div>
                                <button onclick="updateTrafficGraph()" class="flex items-center gap-1.5 h-9 px-4 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-lg transition-colors"><span class="material-symbols-outlined text-[18px]">bar_chart</span> Update Graph</button>
                            </div>
                        </div>
                        <div class="relative">
                            <div class="flex items-center gap-1.5 mb-3 pl-8"><div class="w-3 h-3 bg-primary rounded-sm"></div><span class="text-xs text-slate-500 font-medium">Scans</span></div>
                            <div class="flex h-48">
                                <div class="flex flex-col justify-between text-[10px] text-slate-400 font-medium py-1 pr-2 text-right w-8 flex-shrink-0" id="traffic-y-axis"></div>
                                <div class="flex-1 flex items-end gap-1 border-b border-l border-slate-200 dark:border-slate-700 pb-1 relative" id="traffic-chart">
                                    <?php foreach ($trafficHours as $th): ?>
                                    <div class="flex-1 flex flex-col items-center justify-end h-full gap-1 group cursor-pointer traffic-bar-container" data-count="<?= $th['count'] ?>">
                                        <div class="w-full max-w-[28px] mx-auto bg-primary/80 hover:bg-primary rounded-t transition-all duration-300 relative" style="height: 0%"><div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[9px] py-0.5 px-1.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10"><?= $th['count'] ?></div></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="flex gap-1 pl-8 mt-1" id="traffic-x-labels">
                                <?php foreach ($trafficHours as $th): ?>
                                <div class="flex-1 text-center text-[9px] text-slate-400 font-medium truncate"><?= $th['label'] ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php elseif ($wid === 'poster-banner'): ?>
                    <!-- Poster Banner -->
                    <?php $posterImage = $wp['image'] ?? null; ?>
                    <div id="poster-banner-content" class="rounded-xl overflow-hidden w-full" style="height:18rem;">
                        <?php if ($posterImage): ?>
                        <img src="<?= esc($posterImage) ?>" alt="Poster Banner" class="w-full h-full object-cover block">
                        <?php else: ?>
                        <div class="w-full h-full flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/30 text-slate-400">
                            <span class="material-symbols-outlined text-6xl mb-3 opacity-40">image</span>
                            <p class="text-sm font-medium">No poster uploaded</p>
                            <p class="text-xs mt-1 opacity-70">Open Customize → upload an image</p>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                </div><!-- /widget-card-content -->
            </div><!-- /widget-wrapper -->
            <?php endforeach; ?>

            </div><!-- /widgets-container -->

        </div><!-- /dashboard-content -->
    </main>
</div>

<!-- Drawer tab: stays at screen right edge; moves left when drawer is open -->
<button id="widget-drawer-tab" onclick="toggleDrawerCollapse()"
    class="fixed top-1/2 -translate-y-1/2 z-[98] hidden flex-col items-center justify-center w-9 h-16 bg-primary hover:bg-primary-dark text-white rounded-l-xl shadow-lg"
    style="right:0; transition: right 0.3s cubic-bezier(.4,0,.2,1);"
    title="Toggle panel">
    <span id="widget-drawer-tab-icon" class="material-symbols-outlined text-[22px]">chevron_left</span>
</button>

<!-- Widget Customize Drawer -->
<aside id="widget-drawer" class="fixed top-0 right-0 h-full w-80 bg-surface-light dark:bg-surface-dark border-l border-slate-200 dark:border-slate-700 shadow-2xl z-[97] flex flex-col">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-700 flex-shrink-0">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">dashboard_customize</span>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Customize Dashboard</h2>
        </div>
        <button onclick="resetWidgets()" class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors mr-1" title="Reset to default">
            <span class="material-symbols-outlined text-[16px]">restart_alt</span>Reset
        </button>
    </div>
    <div class="px-5 py-3 bg-primary/5 border-b border-primary/20 flex-shrink-0">
        <div class="flex items-center gap-2 text-primary text-xs font-medium">
            <span class="material-symbols-outlined text-[16px]">drag_indicator</span>
            Drag the handle on each widget to reorder
        </div>
        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs mt-1">
            <span class="material-symbols-outlined text-[16px]">visibility_off</span>
            Click the eye button on a widget to hide it
        </div>
    </div>
    <div id="drawer-widget-list" class="flex-1 overflow-y-auto px-4 py-3 space-y-2"></div>
    <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700 flex-shrink-0 space-y-2">
        <button onclick="doneWidgetCustomize()" class="w-full flex items-center justify-center gap-2 h-10 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-lg transition-colors">
            <span class="material-symbols-outlined text-[18px]">check</span>Done
        </button>
        <p id="drawer-save-feedback" class="text-center text-xs text-green-600 hidden">Layout saved!</p>
    </div>
</aside>

<!-- Analytics Assistant -->
<button id="analytics-assistant-launcher" type="button" onclick="openAnalyticsAssistant()" class="fixed bottom-6 right-6 z-[90] inline-flex items-center gap-3 rounded-full bg-primary hover:bg-primary-dark text-white px-5 py-3 shadow-2xl transition-all">
    <span class="size-8 rounded-full bg-white/15 flex items-center justify-center">
        <span class="material-symbols-outlined text-[20px]">monitoring</span>
    </span>
    <span class="text-sm font-bold">Analytics Assistant</span>
    <span class="size-3 rounded-full bg-white ring-4 ring-white/20"></span>
</button>

<section id="analytics-assistant-panel" class="hidden fixed bottom-6 right-6 z-[95] w-[min(420px,calc(100vw-2rem))] h-[min(640px,calc(100vh-2rem))] bg-surface-light dark:bg-surface-dark text-slate-900 dark:text-white rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col">
    <header class="h-16 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-4 flex-shrink-0">
        <div class="flex items-center gap-3 min-w-0">
            <span class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-[20px]">monitoring</span>
            </span>
            <div class="min-w-0">
                <p class="text-sm font-bold leading-tight truncate text-slate-900 dark:text-white">Analytics Assistant</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-tight">Ask about VMS database records</p>
            </div>
        </div>
        <div class="flex items-center gap-1">
            <button type="button" onclick="minimizeAnalyticsAssistant()" class="size-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors" title="Minimize">
                <span class="material-symbols-outlined text-[18px]">remove</span>
            </button>
            <button type="button" onclick="toggleAnalyticsAssistantSize()" class="size-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors" title="Widen">
                <span id="analytics-assistant-size-icon" class="material-symbols-outlined text-[18px]">fullscreen</span>
            </button>
            <button type="button" onclick="closeAnalyticsAssistant()" class="size-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors" title="Close">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
    </header>

    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-start justify-between gap-4 flex-shrink-0">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Analytics Assistant</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Ask read-only questions across the VMS database.</p>
        </div>
        <span id="analytics-assistant-status" class="inline-flex items-center gap-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 border border-amber-100 dark:border-amber-800 px-3 py-2 text-xs font-semibold whitespace-nowrap">
            <span class="size-2 rounded-full bg-amber-400"></span>
            Ready
        </span>
    </div>

    <div class="flex-1 min-h-0 flex bg-slate-50 dark:bg-slate-950">
        <button id="analytics-history-toggle" type="button" onclick="toggleAnalyticsHistory()" class="hidden sm:flex w-9 bg-primary text-white items-center justify-center [writing-mode:vertical-rl] text-xs font-bold gap-2" title="Chat History">
            <span id="analytics-history-toggle-icon" class="material-symbols-outlined text-[18px] rotate-90">keyboard_double_arrow_right</span>
            Chat History
        </button>
        <aside id="analytics-history-panel" class="hidden w-72 flex-shrink-0 border-r border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 overflow-hidden">
            <div class="h-full flex flex-col">
                <div class="p-4 border-b border-slate-200 dark:border-slate-700 space-y-3">
                    <button type="button" onclick="newAnalyticsChat()" class="w-full h-10 rounded-lg bg-primary hover:bg-primary-dark text-white text-sm font-bold flex items-center justify-center gap-2 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        New Chat
                    </button>
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">Chat History</h4>
                            <p class="text-xs text-slate-500 mt-0.5">This session only</p>
                        </div>
                        <button type="button" onclick="clearAnalyticsHistory()" class="text-[11px] font-semibold text-slate-400 hover:text-primary transition-colors">Clear</button>
                    </div>
                </div>
                <div id="analytics-history-list" class="flex-1 overflow-y-auto p-3 space-y-2">
                    <div id="analytics-history-empty" class="hidden rounded-lg border border-dashed border-slate-200 dark:border-slate-700 p-3 text-xs text-slate-400">
                        No chats yet.
                    </div>
                </div>
                <div id="analytics-history-menu" class="hidden fixed z-[10080] w-40 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-xl p-1">
                    <button type="button" onclick="renameAnalyticsChatFromMenu()" class="w-full flex items-center gap-2 px-3 py-2 rounded-md text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                        <span class="material-symbols-outlined text-[18px]">edit_square</span>
                        Rename
                    </button>
                    <button type="button" onclick="deleteAnalyticsChatFromMenu()" class="w-full flex items-center gap-2 px-3 py-2 rounded-md text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                        Delete
                    </button>
                </div>
            </div>
        </aside>
        <div class="flex-1 min-w-0 min-h-0 flex flex-col">
            <div id="analytics-assistant-messages" class="flex-1 overflow-y-auto p-5 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="size-9 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[20px]">monitoring</span>
                    </div>
                    <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 text-sm text-slate-600 dark:text-slate-300 max-w-[90%] shadow-sm">
                        <p class="font-bold text-slate-900 dark:text-white mb-2">Hello! I&apos;m your Analytics Assistant</p>
                        <p>Ask me anything about VMS database records. Examples:</p>
                        <ul class="list-disc pl-5 mt-3 space-y-1 text-slate-500 dark:text-slate-400">
                            <li>Who is currently on-site?</li>
                            <li>Which visitors are overstaying?</li>
                            <li>How many visitors are expected today?</li>
                            <li>Show active security alerts.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <form id="analytics-assistant-form" class="border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 flex gap-3 flex-shrink-0" onsubmit="sendAnalyticsAssistantMessage(event)">
                <input id="analytics-assistant-input" type="text" autocomplete="off" maxlength="1200" placeholder="Ask anything about your data..." class="flex-1 min-w-0 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <button id="analytics-assistant-send" type="submit" class="rounded-lg bg-primary hover:bg-primary-dark text-white px-5 py-3 text-sm font-bold flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                    Send
                    <span class="material-symbols-outlined text-[18px]">send</span>
                </button>
            </form>
            <p class="px-4 pb-4 -mt-2 bg-white dark:bg-slate-900 text-[11px] text-slate-400 flex-shrink-0">
                Smart AI can query VMS records in read-only mode.
            </p>
        </div>
    </div>
</section>

<!-- Dashboard Drill-Down Modal -->
<div id="dash-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="absolute inset-4 md:inset-y-6 md:inset-x-auto md:left-1/2 md:-translate-x-1/2 md:w-full md:max-w-6xl bg-surface-light dark:bg-surface-dark rounded-2xl shadow-2xl flex flex-col max-h-[min(92vh,920px)] min-h-0 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex-shrink-0">
            <div class="flex items-center gap-3">
                <button id="modal-back-btn" onclick="modalGoBack()" class="hidden size-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors" title="Back">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </button>
                <div id="modal-icon" class="size-9 rounded-lg flex items-center justify-center"></div>
                <div>
                    <h3 id="modal-title" class="text-base font-bold text-slate-900 dark:text-white"></h3>
                    <p id="modal-subtitle" class="text-xs text-slate-500"></p>
                </div>
            </div>
            <button onclick="closeModal()" class="size-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div id="modal-body" class="flex-1 min-h-0 overflow-y-auto p-6">
            <div class="flex items-center justify-center py-12">
                <div class="flex items-center gap-3 text-slate-400">
                    <svg class="animate-spin size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span class="text-sm font-medium">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
const BASE = '<?= base_url() ?>';
const UPCOMING_APPTS = <?= json_encode($upcomingAppointments) ?>;
const TODAY_APPTS = <?= json_encode($todayAppointments) ?>;
function esc(s) { const d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }
let criticalAlertQueue = [];
let analyticsChats = [];
let activeAnalyticsChatId = '';
let analyticsHistoryMenuChatId = '';

function openAnalyticsAssistant() {
    const panel = document.getElementById('analytics-assistant-panel');
    const launcher = document.getElementById('analytics-assistant-launcher');
    panel.classList.remove('hidden', 'analytics-assistant-minimized');
    launcher.classList.add('hidden');
    setTimeout(() => {
        scrollAnalyticsToBottom();
        document.getElementById('analytics-assistant-input')?.focus();
    }, 80);
}

function minimizeAnalyticsAssistant() {
    const panel = document.getElementById('analytics-assistant-panel');
    const launcher = document.getElementById('analytics-assistant-launcher');
    panel.classList.add('analytics-assistant-minimized');
    launcher.classList.remove('hidden');
    setTimeout(() => panel.classList.add('hidden'), 180);
}

function closeAnalyticsAssistant() {
    const panel = document.getElementById('analytics-assistant-panel');
    const launcher = document.getElementById('analytics-assistant-launcher');
    panel.classList.add('hidden');
    panel.classList.remove('analytics-assistant-expanded', 'analytics-assistant-minimized');
    document.getElementById('analytics-assistant-size-icon').textContent = 'fullscreen';
    launcher.classList.remove('hidden');
}

function toggleAnalyticsAssistantSize() {
    const panel = document.getElementById('analytics-assistant-panel');
    const icon = document.getElementById('analytics-assistant-size-icon');
    const expanded = panel.classList.toggle('analytics-assistant-expanded');
    icon.textContent = expanded ? 'fullscreen_exit' : 'fullscreen';
}

function postAssistantAction(url, data = {}) {
    const body = new URLSearchParams();
    Object.keys(data).forEach(key => body.append(key, data[key]));
    return fetch(BASE + url, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: body.toString()
    }).then(r => r.json());
}

function loadAnalyticsChats() {
    return fetch(BASE + '/dashboard/assistantHistory', {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(d => {
        analyticsChats = d.success && Array.isArray(d.chats) ? d.chats : [];
        activeAnalyticsChatId = analyticsChats[0]?.id || '';
        if (!activeAnalyticsChatId) createAnalyticsChat(false);
        renderActiveAnalyticsChat();
        renderAnalyticsHistory();
    })
    .catch(() => {
        if (!activeAnalyticsChatId) createAnalyticsChat(false);
        renderActiveAnalyticsChat();
        renderAnalyticsHistory();
    });
}

function analyticsWelcomeHtml() {
    return '<div class="flex items-start gap-3">'
        + '<div class="size-9 rounded-full bg-primary flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-[20px]">monitoring</span></div>'
        + '<div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 text-sm text-slate-600 dark:text-slate-300 max-w-[90%] shadow-sm">'
        + '<p class="font-bold text-slate-900 dark:text-white mb-2">Hello! I&apos;m your Analytics Assistant</p>'
        + '<p>Ask me anything about VMS database records. Examples:</p>'
        + '<ul class="list-disc pl-5 mt-3 space-y-1 text-slate-500 dark:text-slate-400">'
        + '<li>Who is currently on-site?</li>'
        + '<li>Which visitors are overstaying?</li>'
        + '<li>How many visitors are expected today?</li>'
        + '<li>Show active security alerts.</li>'
        + '</ul></div></div>';
}

function ensureActiveAnalyticsChat() {
    if (activeAnalyticsChatId && analyticsChats.some(chat => chat.id === activeAnalyticsChatId)) return;
    createAnalyticsChat(false);
}

function createAnalyticsChat(render = true) {
    const now = new Date();
    const chat = {
        id: 'chat-' + now.getTime() + '-' + Math.random().toString(16).slice(2),
        title: 'New Chat',
        date: now.toLocaleDateString('en-US', {month: 'short', day: 'numeric'}),
        messages: []
    };
    analyticsChats.unshift(chat);
    activeAnalyticsChatId = chat.id;
    if (render) {
        renderActiveAnalyticsChat();
        renderAnalyticsHistory();
    }
}

function activeAnalyticsChat() {
    ensureActiveAnalyticsChat();
    return analyticsChats.find(chat => chat.id === activeAnalyticsChatId);
}

function newAnalyticsChat() {
    closeAnalyticsHistoryMenu();
    postAssistantAction('/dashboard/assistantChatCreate').then(d => {
        if (d.success && d.chat) {
            analyticsChats.unshift(d.chat);
            activeAnalyticsChatId = d.chat.id;
        } else {
            createAnalyticsChat(false);
        }
        renderActiveAnalyticsChat();
        renderAnalyticsHistory();
        document.getElementById('analytics-assistant-input')?.focus();
    });
}

function toggleAnalyticsHistory() {
    const panel = document.getElementById('analytics-history-panel');
    const icon = document.getElementById('analytics-history-toggle-icon');
    const isOpening = panel.classList.contains('hidden');
    panel.classList.toggle('hidden', !isOpening);
    icon.textContent = isOpening ? 'keyboard_double_arrow_left' : 'keyboard_double_arrow_right';
    if (isOpening) renderAnalyticsHistory();
    else closeAnalyticsHistoryMenu();
}

function updateActiveChatTitle(question) {
    const chat = activeAnalyticsChat();
    if (!chat || (chat.title !== 'New Chat' && chat.messages.filter(m => m.role === 'user').length > 1)) return;
    chat.title = question.length > 34 ? question.slice(0, 31) + '...' : question;
    renderAnalyticsHistory();
}

function renderAnalyticsHistory() {
    const list = document.getElementById('analytics-history-list');
    const empty = document.getElementById('analytics-history-empty');
    if (!list) return;

    list.querySelectorAll('[data-history-entry]').forEach(el => el.remove());
    if (empty) empty.classList.toggle('hidden', analyticsChats.length > 0);

    analyticsChats.forEach((chat) => {
        const item = document.createElement('div');
        item.dataset.historyEntry = '1';
        item.className = 'group relative rounded-lg border transition-colors '
            + (chat.id === activeAnalyticsChatId
                ? 'border-primary/30 bg-blue-50 dark:bg-slate-800'
                : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:border-primary/40');
        item.innerHTML = '<button type="button" class="w-full text-left p-3 pr-10" onclick="switchAnalyticsChat(\'' + esc(chat.id) + '\')">'
            + '<p class="text-xs font-bold text-slate-900 dark:text-white truncate">' + esc(chat.title) + '</p>'
            + '<p class="text-[11px] text-slate-400 mt-1">' + esc(chat.date) + '</p>'
            + '</button>'
            + '<button type="button" class="absolute top-2 right-2 size-7 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-white dark:hover:bg-slate-700" onclick="openAnalyticsHistoryMenu(event, \'' + esc(chat.id) + '\')" title="Chat options">'
            + '<span class="material-symbols-outlined text-[18px]">more_vert</span>'
            + '</button>';
        list.appendChild(item);
    });
}

function clearAnalyticsHistory() {
    closeAnalyticsHistoryMenu();
    if (!confirm('Are you sure you want to clear all chat history? This action cannot be undone.')) return;
    postAssistantAction('/dashboard/assistantChatClear').finally(() => {
        analyticsChats = [];
        activeAnalyticsChatId = '';
        createAnalyticsChat(true);
    });
}

function switchAnalyticsChat(chatId) {
    if (!analyticsChats.some(chat => chat.id === chatId)) return;
    activeAnalyticsChatId = chatId;
    closeAnalyticsHistoryMenu();
    renderActiveAnalyticsChat();
    renderAnalyticsHistory();
    scrollAnalyticsToBottom();
}

function openAnalyticsHistoryMenu(event, chatId) {
    event.stopPropagation();
    const menu = document.getElementById('analytics-history-menu');
    const rect = event.currentTarget.getBoundingClientRect();
    analyticsHistoryMenuChatId = chatId;
    menu.style.left = Math.min(rect.right + 4, window.innerWidth - 176) + 'px';
    menu.style.top = rect.top + 'px';
    menu.classList.remove('hidden');
}

function closeAnalyticsHistoryMenu() {
    const menu = document.getElementById('analytics-history-menu');
    if (menu) menu.classList.add('hidden');
    analyticsHistoryMenuChatId = '';
}

function renameAnalyticsChatFromMenu() {
    const chat = analyticsChats.find(item => item.id === analyticsHistoryMenuChatId);
    if (!chat) return;
    const next = prompt('Rename chat', chat.title);
    if (next === null) return;
    const title = next.trim();
    if (title) {
        chat.title = title.length > 40 ? title.slice(0, 37) + '...' : title;
        postAssistantAction('/dashboard/assistantChatRename', {chat_id: chat.id, title: chat.title});
    }
    closeAnalyticsHistoryMenu();
    renderAnalyticsHistory();
}

function deleteAnalyticsChatFromMenu() {
    const chatId = analyticsHistoryMenuChatId;
    if (!chatId) return;
    postAssistantAction('/dashboard/assistantChatDelete', {chat_id: chatId});
    analyticsChats = analyticsChats.filter(chat => chat.id !== chatId);
    closeAnalyticsHistoryMenu();
    if (activeAnalyticsChatId === chatId) {
        activeAnalyticsChatId = analyticsChats[0]?.id || '';
        if (!activeAnalyticsChatId) createAnalyticsChat(false);
        renderActiveAnalyticsChat();
    }
    renderAnalyticsHistory();
}

document.addEventListener('click', function (event) {
    const target = event.target;
    if (target && target.closest && (target.closest('#analytics-history-menu') || target.closest('[onclick^="openAnalyticsHistoryMenu"]'))) {
        return;
    }
    closeAnalyticsHistoryMenu();
});

function scrollAnalyticsToBottom() {
    const wrap = document.getElementById('analytics-assistant-messages');
    if (!wrap) return;
    requestAnimationFrame(() => requestAnimationFrame(() => { wrap.scrollTop = wrap.scrollHeight; }));
}

function renderActiveAnalyticsChat() {
    const wrap = document.getElementById('analytics-assistant-messages');
    const chat = activeAnalyticsChat();
    wrap.innerHTML = analyticsWelcomeHtml();
    chat.messages.forEach(message => renderAssistantMessage(message.role, message.text, false));
    scrollAnalyticsToBottom();
}

function renderAssistantMessage(role, text, isLoading = false) {
    const wrap = document.getElementById('analytics-assistant-messages');
    const row = document.createElement('div');
    const rowId = 'assistant-row-' + Date.now() + '-' + Math.random().toString(16).slice(2);
    row.dataset.assistantRowId = rowId;
    row.className = role === 'user' ? 'flex items-start justify-end gap-3' : 'flex items-start gap-3';

    const bubble = document.createElement('div');
    bubble.className = role === 'user'
        ? 'rounded-lg bg-primary text-white p-4 text-sm max-w-[88%] shadow-sm'
        : 'rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 text-sm text-slate-600 dark:text-slate-300 max-w-[90%] shadow-sm';
    bubble.innerHTML = isLoading
        ? '<span class="inline-flex items-center gap-2 text-slate-500 dark:text-slate-400"><span class="size-2 rounded-full bg-primary animate-pulse"></span>Thinking...</span>'
        : assistantFormatText(text);

    if (role === 'user') {
        row.appendChild(bubble);
    } else {
        const avatar = document.createElement('div');
        avatar.className = 'size-9 rounded-full bg-primary flex items-center justify-center flex-shrink-0';
        avatar.innerHTML = '<span class="material-symbols-outlined text-[20px]">monitoring</span>';
        row.appendChild(avatar);
        row.appendChild(bubble);
    }

    if (isLoading) row.dataset.loading = '1';
    wrap.appendChild(row);
    scrollAnalyticsToBottom();
    return {row, rowId};
}

function appendAssistantMessage(role, text, isLoading = false) {
    const rendered = renderAssistantMessage(role, text, isLoading);
    if (!isLoading) {
        activeAnalyticsChat().messages.push({role, text});
    }
    return rendered;
}

function assistantInlineFormat(text) {
    return esc(text).replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
}

function assistantIsMarkdownTableStart(lines, index) {
    if (index + 1 >= lines.length) return false;
    const header = lines[index] || '';
    const separator = lines[index + 1] || '';
    return /^\s*\|.*\|\s*$/.test(header)
        && /^\s*\|?\s*:?-{3,}:?\s*(\|\s*:?-{3,}:?\s*)+\|?\s*$/.test(separator);
}

function assistantRenderMarkdownTable(tableLines) {
    const rows = tableLines.map(line => line.trim().replace(/^\|/, '').replace(/\|$/, '').split('|').map(cell => assistantInlineFormat(cell.trim())));
    const headers = rows[0] || [];
    const bodyRows = rows.slice(2);
    let html = '<div class="my-2 overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">';
    html += '<table class="min-w-full text-left text-xs">';
    html += '<thead class="bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200"><tr>';
    headers.forEach(header => { html += '<th class="px-3 py-2 font-bold">' + header + '</th>'; });
    html += '</tr></thead><tbody class="divide-y divide-slate-100 dark:divide-slate-700">';
    bodyRows.forEach(row => {
        html += '<tr>';
        headers.forEach((_, idx) => { html += '<td class="px-3 py-2 align-top">' + (row[idx] || '') + '</td>'; });
        html += '</tr>';
    });
    html += '</tbody></table></div>';
    return html;
}

function assistantFormatText(text) {
    const lines = String(text || '').split(/\r?\n/);
    const parts = [];

    for (let i = 0; i < lines.length; i++) {
        if (assistantIsMarkdownTableStart(lines, i)) {
            const tableLines = [lines[i], lines[i + 1]];
            i += 2;
            while (i < lines.length && /^\s*\|.*\|\s*$/.test(lines[i])) {
                tableLines.push(lines[i]);
                i++;
            }
            i--;
            parts.push(assistantRenderMarkdownTable(tableLines));
            continue;
        }

        parts.push(assistantInlineFormat(lines[i]));
    }

    return parts.join('<br>');
}

function setAssistantBusy(isBusy) {
    const input = document.getElementById('analytics-assistant-input');
    const send = document.getElementById('analytics-assistant-send');
    const status = document.getElementById('analytics-assistant-status');
    input.disabled = isBusy;
    send.disabled = isBusy;
    status.className = isBusy
        ? 'inline-flex items-center gap-2 rounded-lg bg-primary/10 text-primary border border-primary/20 px-3 py-2 text-xs font-semibold whitespace-nowrap'
        : 'inline-flex items-center gap-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 border border-amber-100 dark:border-amber-800 px-3 py-2 text-xs font-semibold whitespace-nowrap';
    status.innerHTML = isBusy
        ? '<span class="size-2 rounded-full bg-primary animate-pulse"></span> Thinking'
        : '<span class="size-2 rounded-full bg-amber-400"></span> Ready';
}

function sendAnalyticsAssistantMessage(event) {
    event.preventDefault();
    const input = document.getElementById('analytics-assistant-input');
    const message = input.value.trim();
    if (!message) return;

    const chat = activeAnalyticsChat();
    const history = chat.messages.slice(-10).map(item => ({
        role: item.role,
        content: item.text
    }));

    appendAssistantMessage('user', message);
    updateActiveChatTitle(message);
    input.value = '';
    setAssistantBusy(true);
    const loading = appendAssistantMessage('assistant', '', true);

    fetch(BASE + '/dashboard/assistantAsk', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'message=' + encodeURIComponent(message) + '&chat_id=' + encodeURIComponent(chat.id) + '&history=' + encodeURIComponent(JSON.stringify(history))
    })
    .then(r => r.json())
    .then(d => {
        loading.row.remove();
        if (!d.success) {
            if (d.chat_id) activeAnalyticsChatId = String(d.chat_id);
            appendAssistantMessage('assistant', d.message || 'Analytics Assistant is unavailable.');
            return;
        }
        if (d.chat_id) {
            chat.id = String(d.chat_id);
            activeAnalyticsChatId = String(d.chat_id);
        }
        if (d.title) {
            chat.title = d.title;
            renderAnalyticsHistory();
        }
        appendAssistantMessage('assistant', d.answer || 'No answer was returned.');
    })
    .catch(() => {
        loading.row.remove();
        appendAssistantMessage('assistant', 'I could not reach the Analytics Assistant service. Please try again.');
    })
    .finally(() => {
        setAssistantBusy(false);
        input.focus();
    });
}

function parseCriticalAlertsJson() {
    const el = document.getElementById('critical-alerts-json');
    if (!el) return;
    try {
        const raw = JSON.parse(el.textContent || '[]');
        criticalAlertQueue = Array.isArray(raw) ? raw : [];
    } catch (e) {
        criticalAlertQueue = [];
    }
    el.remove();
}

function buildCriticalAlertCardHtml(a) {
    const sev = esc((String(a.severity || '')).charAt(0).toUpperCase() + String(a.severity || '').slice(1));
    const n = criticalAlertQueue.length;
    const queueLine = n > 1
        ? '<p class="text-red-300/90 text-xs font-medium mt-2">' + esc('1 of ' + n + ' — acknowledge to review the next alert') + '</p>'
        : '';
    let inner = '<div class="flex items-start gap-4 flex-1 min-w-0">';
    inner += '<div class="size-10 rounded-lg bg-red-700/50 flex items-center justify-center text-red-200 flex-shrink-0 mt-0.5"><span class="material-symbols-outlined fill-1">warning</span></div>';
    inner += '<div class="flex-1 min-w-0"><div class="flex items-center gap-3 mb-1.5 flex-wrap">';
    inner += '<h3 class="text-white font-bold text-base">Critical Security Alert</h3>';
    inner += '<span class="px-2 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full uppercase tracking-wider">' + sev + '</span></div>';
    inner += '<div class="grid grid-cols-1 sm:grid-cols-3 gap-x-8 gap-y-1 text-sm mb-2">';
    inner += '<div><span class="text-red-300 font-medium">INCIDENT TYPE</span><p class="text-white font-semibold break-words">' + esc(a.incident_type) + '</p></div>';
    inner += '<div><span class="text-red-300 font-medium">LOCATION</span><p class="text-white font-semibold break-words">' + esc(a.location) + '</p></div>';
    inner += '<div><span class="text-red-300 font-medium">TIME</span><p class="text-white font-semibold break-words">' + esc(a.time) + '</p></div></div>';
    if (a.description) inner += '<p class="text-red-200 text-sm break-words">' + esc(a.description) + '</p>';
    inner += queueLine + '</div></div>';
    inner += '<div class="flex items-center gap-2 flex-shrink-0">';
    inner += '<button type="button" onclick="openAlertFromBanner(' + Number(a.id) + ')" class="flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-500 border border-red-500 text-white text-sm font-bold rounded-lg transition-colors">';
    inner += '<span class="material-symbols-outlined text-[18px]">visibility</span> View Incident</button>';
    inner += '<button type="button" onclick="acknowledgeAlert(' + Number(a.id) + ')" class="flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-bold rounded-lg transition-colors backdrop-blur-sm">';
    inner += '<span class="material-symbols-outlined text-[18px]">check</span> Acknowledge</button></div>';
    return inner;
}

function renderCriticalAlertCard() {
    const content = document.getElementById('critical-alert-card-content');
    const activeW = document.getElementById('critical-alert-active-wrapper');
    const clearW = document.getElementById('critical-alert-all-clear-wrapper');
    if (!content || !activeW || !clearW) return;
    if (criticalAlertQueue.length === 0) {
        activeW.classList.add('hidden');
        clearW.classList.remove('hidden');
        content.innerHTML = '';
        return;
    }
    clearW.classList.add('hidden');
    activeW.classList.remove('hidden');
    content.innerHTML = buildCriticalAlertCardHtml(criticalAlertQueue[0]);
}

function applySecurityWidgetPayload(d) {
    if (!d || d.success === false) return;
    if (Array.isArray(d.criticalAlerts)) {
        criticalAlertQueue = d.criticalAlerts;
        renderCriticalAlertCard();
    }
    const setText = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = String(v); };
    if (d.accessDeniedCount !== undefined) setText('dash-widget-access-denied', d.accessDeniedCount);
    if (d.overstayCount !== undefined) setText('dash-widget-overstay', d.overstayCount);
    if (d.activeSecurityAlertCount !== undefined) setText('dash-widget-active-alerts', d.activeSecurityAlertCount);
}

function refreshDashboardWidgets() {
    return fetch(BASE + '/dashboard/widgetSnapshot', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(d => { applySecurityWidgetPayload(d); })
        .catch(() => {});
}

function initCriticalAlerts() {
    parseCriticalAlertsJson();
    renderCriticalAlertCard();
}

function clearAckAccessDeniedPrompt() {
    const host = document.getElementById('ack-access-denied-prompt');
    if (!host) return;
    host.className = 'hidden';
    host.innerHTML = '';
}

function showAckAccessDeniedPrompt() {
    const host = document.getElementById('ack-access-denied-prompt');
    if (!host) return;
    host.className = 'rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/80 px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3';
    host.innerHTML = '<p class="text-sm text-slate-700 dark:text-slate-300"><span class="font-semibold text-slate-900 dark:text-white">Alert acknowledged.</span> Open Access Denied Incidents when you are ready.</p>'
        + '<div class="flex flex-wrap items-center gap-2 shrink-0">'
        + '<button type="button" class="px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">Dismiss</button>'
        + '<button type="button" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-primary hover:bg-primary-dark text-white transition-colors">View Access Denied Incidents</button>'
        + '</div>';
    const btns = host.querySelectorAll('button');
    btns[0].addEventListener('click', clearAckAccessDeniedPrompt);
    btns[1].addEventListener('click', function () {
        clearAckAccessDeniedPrompt();
        openModal('accessDenied');
    });
}

function acknowledgeAlert(id) {
    fetch(BASE + '/dashboard/acknowledgeAlert', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'alert_id=' + id
    }).then(r => r.json()).then(d => {
        if (!d.success) return;
        showAckAccessDeniedPrompt();
        applySecurityWidgetPayload(d);
    });
}

function updateTrafficGraph() {
    const f = document.getElementById('traffic-from').value, t = document.getElementById('traffic-to').value;
    fetch(BASE + '/dashboard/trafficData?from=' + f + '&to=' + t).then(r => r.json()).then(d => {
        if (!d.success) return;
        const c = document.getElementById('traffic-chart'), l = document.getElementById('traffic-x-labels');
        c.querySelectorAll('.traffic-bar-container').forEach(e => e.remove()); l.innerHTML = '';
        const rawMax = Math.max(0, ...d.data.map(x => x.count));
        const m = getNiceScale(rawMax);
        d.data.forEach(x => {
            const p = (x.count / m) * 100, cn = document.createElement('div');
            cn.className = 'flex-1 flex flex-col items-center justify-end h-full gap-1 group cursor-pointer traffic-bar-container';
            cn.dataset.count = x.count;
            cn.innerHTML = '<div class="w-full max-w-[28px] mx-auto bg-primary/80 hover:bg-primary rounded-t transition-all duration-300 relative" style="height:' + p + '%"><div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[9px] py-0.5 px-1.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">' + x.count + '</div></div>';
            c.appendChild(cn);
            const lb = document.createElement('div');
            lb.className = 'flex-1 text-center text-[9px] text-slate-400 font-medium truncate';
            lb.textContent = x.label; l.appendChild(lb);
        });
        renderYAxis(m);
    });
}

document.getElementById('onsite-search')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#onsite-table tbody tr').forEach(r => { r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none'; });
});

function initVisitorStatusTabs() {
    const tabButtons = document.querySelectorAll('[data-visitor-tab]');
    const rows = Array.from(document.querySelectorAll('#visitors-table-body .visitor-row'));
    const showingRange = document.getElementById('visitors-showing-range');
    const totalCount = document.getElementById('visitors-total-count');
    if (!tabButtons.length || !showingRange || !totalCount) return;

    let currentTab = 'all';

    const updateTabStyles = (activeTab) => {
        tabButtons.forEach((btn) => {
            const isActive = btn.dataset.visitorTab === activeTab;
            const label = btn.querySelector('p');
            const badge = btn.querySelector('[data-tab-count]');

            btn.classList.toggle('border-primary', isActive);
            btn.classList.toggle('text-primary', isActive);
            btn.classList.toggle('border-transparent', !isActive);
            btn.classList.toggle('text-slate-500', !isActive);
            btn.classList.toggle('dark:text-slate-400', !isActive);

            if (label) {
                label.classList.toggle('font-bold', isActive);
                label.classList.toggle('font-medium', !isActive);
            }

            if (badge) {
                badge.classList.toggle('bg-primary/10', isActive);
                badge.classList.toggle('text-primary', isActive);
                badge.classList.toggle('bg-slate-100', !isActive);
                badge.classList.toggle('dark:bg-slate-800', !isActive);
                badge.classList.toggle('text-slate-500', !isActive);
            }
        });
    };

    const applyFilter = (activeTab) => {
        if (activeTab !== undefined) currentTab = activeTab;

        const companyVal = (document.getElementById('visitor-company-filter')?.value || '').toLowerCase();
        const dateFrom = document.getElementById('visitor-date-from')?.value || '';
        const dateTo = document.getElementById('visitor-date-to')?.value || '';

        const tabTotal = currentTab === 'all'
            ? rows.length
            : rows.filter((row) => row.dataset.visitorStatus === currentTab).length;

        let visibleCount = 0;
        rows.forEach((row) => {
            const tabMatch = currentTab === 'all' || row.dataset.visitorStatus === currentTab;
            const companyMatch = !companyVal || row.dataset.company === companyVal;
            let dateMatch = true;
            if (dateFrom || dateTo) {
                const rowDate = new Date(row.dataset.date);
                if (dateFrom && rowDate < new Date(dateFrom)) dateMatch = false;
                if (dateTo && rowDate > new Date(dateTo)) dateMatch = false;
            }
            const shouldShow = tabMatch && companyMatch && dateMatch;
            row.style.display = shouldShow ? '' : 'none';
            if (shouldShow) visibleCount++;
        });

        const noDataRow = document.getElementById('no-data-row');
        if (noDataRow) noDataRow.style.display = visibleCount === 0 ? '' : 'none';

        showingRange.textContent = visibleCount > 0 ? `1-${visibleCount}` : '0-0';
        totalCount.textContent = String(tabTotal);
        updateTabStyles(currentTab);
    };

    window.applyVisitorFilters = () => applyFilter();

    tabButtons.forEach((btn) => {
        btn.addEventListener('click', () => applyFilter(btn.dataset.visitorTab || 'all'));
    });
}

function exportVisitors() {
    const rows = Array.from(document.querySelectorAll('#visitors-table-body .visitor-row'))
        .filter(r => r.style.display !== 'none');
    const headers = ['Visitor Name', 'Contact', 'Company', 'Date & Time', 'Host', 'Status'];
    const csvRows = [headers.join(',')];
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const name = cells[0]?.querySelectorAll('p')[0]?.textContent?.trim() || '';
        const contact = cells[0]?.querySelectorAll('p')[1]?.textContent?.trim() || '';
        const company = cells[1]?.textContent?.trim() || '';
        const time = cells[2]?.textContent?.trim() || '';
        const host = cells[3]?.textContent?.trim() || '';
        const status = cells[4]?.querySelector('[data-status-badge]')?.textContent?.trim().replace(/\s+/g, ' ') || '';
        csvRows.push([name, contact, company, time, host, status].map(v => `"${v.replace(/"/g, '""')}"`).join(','));
    });
    const blob = new Blob([csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'visitors_' + new Date().toISOString().slice(0, 10) + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(a.href);
}

function getNiceScale(rawMax) {
    if (rawMax <= 4) return 4;
    const rough = rawMax / 4;
    const exp = Math.floor(Math.log10(rough));
    const pow = Math.pow(10, exp);
    const norm = rough / pow;
    let step = norm <= 1 ? pow : norm <= 2 ? 2 * pow : norm <= 5 ? 5 * pow : 10 * pow;
    while (step * 4 < rawMax) step *= 2;
    return step * 4;
}

function renderYAxis(scale) {
    const y = document.getElementById('traffic-y-axis'); if (!y) return; y.innerHTML = '';
    const step = scale / 4;
    for (let i = 4; i >= 0; i--) { const s = document.createElement('span'); s.textContent = step * i; y.appendChild(s); }
}

document.addEventListener('DOMContentLoaded', function () {
    loadAnalyticsChats();
    initCriticalAlerts();
    initVisitorStatusTabs();
    const bars = document.querySelectorAll('.traffic-bar-container'); let rawMx = 0;
    bars.forEach(b => { const c = parseInt(b.dataset.count || 0); if (c > rawMx) rawMx = c; });
    const mx = getNiceScale(rawMx); renderYAxis(mx);
    setTimeout(() => { bars.forEach(b => { const bar = b.querySelector('div'), c = parseInt(b.dataset.count || 0), p = (c / mx) * 100; if (bar) bar.style.height = Math.max(p, c > 0 ? 3 : 0) + '%'; }); }, 100);
});

/* ========== MODAL SYSTEM ========== */
const modalEl = document.getElementById('dash-modal');
const modalTitle = document.getElementById('modal-title');
const modalSubtitle = document.getElementById('modal-subtitle');
const modalIcon = document.getElementById('modal-icon');
const modalBody = document.getElementById('modal-body');
const LOADER = '<div class="flex items-center justify-center py-12"><div class="flex items-center gap-3 text-slate-400"><svg class="animate-spin size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span class="text-sm font-medium">Loading...</span></div></div>';
const EMPTY = (msg) => '<div class="flex flex-col items-center justify-center py-12 text-center"><span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600 mb-3">inbox</span><p class="text-slate-400 font-medium">' + msg + '</p></div>';

function destroyDashActiveAlertsTable() {
    if (typeof $ !== 'undefined') {
        try {
            $('.dash-alerts-filter-dropdown').each(function () {
                var $dd = $(this);
                $dd.addClass('hidden');
                var w = $dd.data('anchorWrapper');
                if (w && w.length) $dd.appendTo(w);
            });
            $('body > .dash-alerts-filter-dropdown').remove();
        } catch (e1) {}
        try {
            $(window).off('resize.dashAlertFilterScroll');
            $('#modal-body').off('scroll.dashAlertFilter');
        } catch (e2) {}
    }
    var t = document.getElementById('dash-active-alerts-table');
    if (typeof $ !== 'undefined' && $.fn && $.fn.DataTable && t && $.fn.DataTable.isDataTable(t)) {
        $(t).DataTable().destroy();
    }
    if (typeof $ !== 'undefined' && $.fn && $.fn.DataTable) {
        $('.dash-filterable-table').each(function () {
            if ($.fn.DataTable.isDataTable(this)) {
                $(this).DataTable().destroy();
            }
        });
    }
}

function cellTextForCheckboxFilter(raw) {
    if (raw === null || raw === undefined) return '-';
    var $cell = raw && raw.jquery
        ? raw.clone()
        : (raw && raw.nodeType ? $(raw).clone() : $('<div>').html(String(raw)));
    var primaryText = $cell.find('p.font-medium').first().text().trim();
    var t = primaryText || $cell.text().trim();
    if (!t || t === 'NULL' || t === 'null') return '-';
    return t;
}

(function installVmsCheckboxColumnFilterSearchOnce() {
    if (window.__vmsDtCheckboxColumnFilterSearchInstalled) return;
    window.__vmsDtCheckboxColumnFilterSearchInstalled = true;
    if (typeof $ === 'undefined' || !$.fn.dataTable || !$.fn.dataTable.ext) return;
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (!settings.oInit || !settings.oInit._checkboxColumnFilter) return true;
        var cf = settings._colCheckboxFilters;
        if (!cf) return true;
        for (var key in cf) {
            if (!Object.prototype.hasOwnProperty.call(cf, key)) continue;
            var allowed = cf[key];
            if (!(allowed instanceof Set)) continue;
            if (allowed.size === 0) return false;
            var colIdx = parseInt(key, 10);
            var rowMeta = settings.aoData && settings.aoData[dataIndex] ? settings.aoData[dataIndex] : null;
            var cellNode = rowMeta && rowMeta.anCells ? rowMeta.anCells[colIdx] : null;
            var cellText = cellTextForCheckboxFilter(cellNode || data[colIdx]);
            if (!allowed.has(cellText)) return false;
        }
        return true;
    });
})();

function refreshActiveAlertsSummaryLine() {
    var el = document.getElementById('active-alerts-summary');
    if (!el || typeof $ === 'undefined' || !$.fn.DataTable) return;
    var t = document.getElementById('dash-active-alerts-table');
    if (!t || !$.fn.DataTable.isDataTable(t)) return;
    var api = $(t).DataTable();
    var n = api.rows({ search: 'applied' }).count();
    var active = 0;
    api.rows({ search: 'applied' }).every(function () {
        var node = this.node();
        if (node && node.getAttribute('data-active') === '1') active++;
    });
    el.innerHTML = n + ' total alert' + (n !== 1 ? 's' : '') + ' &mdash; <span class="text-red-600 font-semibold">' + active + ' active</span>';
}

function closeAllDashAlertFilterDropdowns() {
    if (typeof $ === 'undefined') return;
    $('.dash-alerts-filter-dropdown').each(function () {
        var $dd = $(this);
        if (!$dd.hasClass('hidden')) {
            $dd.addClass('hidden');
            var w = $dd.data('anchorWrapper');
            if (w && w.length) $dd.appendTo(w);
        }
    });
    $('body > .dash-alerts-filter-dropdown').each(function () {
        var $dd = $(this);
        var w = $dd.data('anchorWrapper');
        if (w && w.length) $dd.addClass('hidden').appendTo(w);
    });
    $(window).off('resize.dashAlertFilterScroll');
    $('#modal-body').off('scroll.dashAlertFilter');
}

function positionDashAlertFilterDropdown($dropdown, $icon) {
    var el = $icon[0];
    if (!el || !el.getBoundingClientRect) return;
    var r = el.getBoundingClientRect();
    var pad = 8;
    var minW = Math.min(360, Math.max(200, r.width, 220));
    var vw = window.innerWidth;
    var left = Math.min(Math.max(pad, r.left), vw - minW - pad);
    var maxH = Math.min(window.innerHeight * 0.58, 480);
    var spaceBelow = window.innerHeight - r.bottom - pad;
    var top = r.bottom + 6;
    if (spaceBelow < 200 && r.top > spaceBelow + 40) {
        top = Math.max(pad, r.top - maxH - 6);
    }
    $dropdown.css({
        position: 'fixed',
        left: left + 'px',
        top: top + 'px',
        minWidth: minW + 'px',
        maxHeight: maxH + 'px',
        zIndex: 10050,
        overflowY: 'auto'
    });
}

function repositionOpenDashAlertFilters() {
    if (typeof $ === 'undefined') return;
    $('.dash-alerts-filter-dropdown:not(.hidden)').each(function () {
        var $dd = $(this);
        var $icon = $dd.data('anchorIcon');
        if ($icon && $icon.length) positionDashAlertFilterDropdown($dd, $icon);
    });
}

function initDashFilterableDataTable(tableSelector, config) {
    config = config || {};
    if (typeof $ === 'undefined' || !$.fn.DataTable) return;
    var $t = $(tableSelector);
    if (!$t.length) return;
    if ($.fn.DataTable.isDataTable($t[0])) $t.DataTable().destroy();

    $t.DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50], ['10 ITEMS PER PAGE', '25 ITEMS PER PAGE', '50 ITEMS PER PAGE']],
        ordering: true,
        responsive: false,
        _checkboxColumnFilter: true,
        dom: '<"flex justify-end items-center mb-4 mt-1"f><"dash-alerts-dt-scroll overflow-x-auto"t><"flex flex-col md:flex-row justify-between items-center gap-4 mt-4"p<"ml-auto"l>>',
        language: {
            search: config.searchLabel || 'Search:',
            searchPlaceholder: '',
            lengthMenu: '_MENU_',
            paginate: { previous: '&laquo;', next: '&raquo;' }
        },
        autoWidth: false,
        initComplete: function () {
            var api = this.api();
            var st = api.settings()[0];
            st._colCheckboxFilters = {};
            api.columns().every(function () { this.search(''); });
            api.draw(false);

            var syncingFilterOptions = false;

            function uniqueOptionsIgnoringColumnSearch(colIdx) {
                if (!st._colCheckboxFilters) st._colCheckboxFilters = {};
                var cf = st._colCheckboxFilters;
                var had = Object.prototype.hasOwnProperty.call(cf, colIdx);
                var saved = had ? cf[colIdx] : undefined;
                delete st._colCheckboxFilters[colIdx];
                api.draw(false);
                var opts = [];
                var seen = {};
                api.rows({ search: 'applied' }).every(function () {
                    var rowData = this.data();
                    var textVal = cellTextForCheckboxFilter(rowData[colIdx]);
                    if (textVal && textVal !== '-' && textVal !== 'View' && textVal !== 'NULL' && textVal !== 'null' && !seen[textVal]) {
                        seen[textVal] = true;
                        opts.push(textVal);
                    }
                });
                opts.sort();
                if (had) st._colCheckboxFilters[colIdx] = saved;
                api.draw(false);
                return opts;
            }

            function syncOtherColumnFilterDropdowns(sourceColIdx) {
                api.columns().every(function () {
                    var col2 = this;
                    var idx2 = col2.index();
                    if (idx2 === sourceColIdx) return;
                    var $wrap2 = $(col2.header()).find('.dt-filter-wrapper').first();
                    var dd2 = $wrap2.length ? $wrap2.data('filterDropdownEl') : null;
                    if (!dd2 || !dd2.length) return;
                    var prev = {};
                    dd2.find('.filter-item input').each(function () {
                        var k = cellTextForCheckboxFilter($(this).val());
                        prev[k] = $(this).prop('checked');
                    });
                    var newOpts = uniqueOptionsIgnoringColumnSearch(idx2);
                    dd2.find('.filter-item').remove();
                    var applyFn2 = dd2.data('applyFilter');
                    var newItemCbs = [];
                    newOpts.forEach(function (val) {
                        var itemLabel = $('<label class="filter-item flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer text-slate-600 capitalize"></label>');
                        itemLabel.attr('data-filter-text', val.toLowerCase());
                        var itemCb = $('<input type="checkbox" checked value="' + String(val).replace(/"/g, '&quot;') + '" class="form-checkbox h-4 w-4 text-[#535dec] accent-[#535dec] rounded border-slate-300 cursor-pointer">');
                        itemLabel.append(itemCb).append('<span class="select-none">' + val + '</span>');
                        dd2.append(itemLabel);
                        itemCb.prop('checked', Object.prototype.hasOwnProperty.call(prev, val) ? prev[val] : true);
                        if (applyFn2) itemCb.on('change', applyFn2);
                        newItemCbs.push(itemCb);
                    });
                    dd2.data('itemCbs', newItemCbs);
                    if (applyFn2) applyFn2();
                });
            }

            api.columns().every(function () {
                var column = this;
                var header = $(column.header());
                var headerText = header.clone().children().remove().end().text().trim().toUpperCase();
                if (headerText === 'NO' || headerText === 'NO.' || headerText === '#' || headerText === 'ACTIONS') return;

                header.find('.dt-filter-wrapper').remove();

                var wrapper = $('<div class="dt-filter-wrapper inline-block relative ml-1 align-middle" onclick="event.stopPropagation()"></div>');
                var icon = $('<span class="material-symbols-outlined text-[16px] text-slate-300 hover:text-[#535dec] transition-colors cursor-pointer" style="vertical-align: middle;">filter_alt</span>');
                var dropdown = $('<div class="filter-dropdown dash-alerts-filter-dropdown hidden fixed z-[10050] bg-white border border-slate-200 rounded shadow-xl p-2 text-left text-sm" style="min-width: 200px; font-weight: normal; box-sizing: border-box;"></div>');

                wrapper.append(icon).append(dropdown);
                header.append(wrapper);
                wrapper.data('filterDropdownEl', dropdown);

                var searchInput = $('<input type="text" placeholder="Search in this column..." class="w-full mb-2 border border-slate-200 rounded px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-primary/20">');
                dropdown.append(searchInput);

                var options = [];
                column.data().unique().sort().each(function (d) {
                    var textVal = cellTextForCheckboxFilter(d);
                    if (textVal && textVal !== '-' && textVal !== 'View' && textVal !== 'NULL' && textVal !== 'null') options.push(textVal);
                });

                var allLabel = $('<label class="flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer font-semibold text-slate-700 capitalize mb-1"></label>');
                var allCb = $('<input type="checkbox" checked class="form-checkbox h-4 w-4 text-[#535dec] accent-[#535dec] rounded border-slate-300 cursor-pointer">');
                allLabel.append(allCb).append('<span class="select-none">All</span>');
                dropdown.append(allLabel);

                var removeAllLabel = $('<label class="flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer font-semibold text-slate-700 capitalize mb-1"></label>');
                var removeAllCb = $('<input type="checkbox" class="form-checkbox h-4 w-4 text-red-500 accent-red-500 rounded border-slate-300 cursor-pointer">');
                removeAllLabel.append(removeAllCb).append('<span class="select-none">Remove All</span>');
                dropdown.append(removeAllLabel);
                dropdown.append('<hr class="my-1 border-slate-200">');

                var itemCbs = [];
                options.forEach(function (val) {
                    var itemLabel = $('<label class="filter-item flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer text-slate-600 capitalize"></label>');
                    itemLabel.attr('data-filter-text', val.toLowerCase());
                    var itemCb = $('<input type="checkbox" checked value="' + String(val).replace(/"/g, '&quot;') + '" class="form-checkbox h-4 w-4 text-[#535dec] accent-[#535dec] rounded border-slate-300 cursor-pointer">');
                    itemLabel.append(itemCb).append('<span class="select-none">' + val + '</span>');
                    dropdown.append(itemLabel);
                    itemCbs.push(itemCb);
                });
                dropdown.data('itemCbs', itemCbs);

                searchInput.on('input', function () {
                    var q = $(this).val().toLowerCase();
                    dropdown.find('.filter-item').each(function () {
                        var text = ($(this).attr('data-filter-text') || '');
                        $(this).toggle(text.includes(q));
                    });
                });

                icon.on('click', function (e) {
                    e.stopPropagation();
                    var opening = dropdown.hasClass('hidden');
                    $('.dash-alerts-filter-dropdown:not(.hidden)').each(function () {
                        var o = $(this);
                        if (o[0] !== dropdown[0]) {
                            o.addClass('hidden');
                            var w0 = o.data('anchorWrapper');
                            if (w0 && w0.length) o.appendTo(w0);
                        }
                    });
                    if (opening) {
                        dropdown.data('anchorIcon', icon);
                        dropdown.data('anchorWrapper', wrapper);
                        dropdown.appendTo('body').removeClass('hidden');
                        positionDashAlertFilterDropdown(dropdown, icon);
                        $(window).off('resize.dashAlertFilterScroll').on('resize.dashAlertFilterScroll', repositionOpenDashAlertFilters);
                        $('#modal-body').off('scroll.dashAlertFilter').on('scroll.dashAlertFilter', repositionOpenDashAlertFilters);
                    } else {
                        dropdown.addClass('hidden');
                        dropdown.appendTo(wrapper);
                        if (!$('.dash-alerts-filter-dropdown:not(.hidden)').length) {
                            $(window).off('resize.dashAlertFilterScroll');
                            $('#modal-body').off('scroll.dashAlertFilter');
                        }
                    }
                });

                function applyFilter() {
                    var cbs = dropdown.data('itemCbs') || itemCbs;
                    var colIdx = column.index();
                    if (!st._colCheckboxFilters) st._colCheckboxFilters = {};
                    var checkedCount = 0;
                    var allowedSet = new Set();
                    cbs.forEach(function (cb) {
                        if (cb.prop('checked')) {
                            checkedCount++;
                            allowedSet.add(cellTextForCheckboxFilter(cb.val()));
                        }
                    });
                    var optCount = cbs.length;
                    var allChecked = optCount > 0 && checkedCount === optCount;
                    var noneChecked = checkedCount === 0;
                    allCb.prop('checked', allChecked);
                    removeAllCb.prop('checked', noneChecked);
                    allCb.prop('indeterminate', false);
                    removeAllCb.prop('indeterminate', false);
                    if (optCount === 0) {
                        icon.removeClass('text-[#535dec] text-red-500').addClass('text-slate-300');
                        delete st._colCheckboxFilters[colIdx];
                    } else if (checkedCount > 0 && checkedCount < optCount) {
                        icon.removeClass('text-slate-300 text-red-500').addClass('text-[#535dec]');
                        st._colCheckboxFilters[colIdx] = allowedSet;
                    } else if (checkedCount === 0) {
                        icon.removeClass('text-slate-300 text-[#535dec]').addClass('text-red-500');
                        st._colCheckboxFilters[colIdx] = new Set();
                    } else {
                        icon.removeClass('text-[#535dec] text-red-500').addClass('text-slate-300');
                        delete st._colCheckboxFilters[colIdx];
                    }
                    column.search('', false, false);
                    api.draw(false);
                    if (!syncingFilterOptions) {
                        syncingFilterOptions = true;
                        try { syncOtherColumnFilterDropdowns(colIdx); } finally { syncingFilterOptions = false; }
                    }
                    if (typeof config.onDraw === 'function') config.onDraw();
                    if (!dropdown.hasClass('hidden') && dropdown.parent()[0] === document.body) {
                        requestAnimationFrame(function () { positionDashAlertFilterDropdown(dropdown, icon); });
                    }
                }
                dropdown.data('applyFilter', applyFilter);

                allCb.on('change', function () {
                    var isChecked = $(this).prop('checked');
                    removeAllCb.prop('checked', false);
                    (dropdown.data('itemCbs') || itemCbs).forEach(function (cb) { cb.prop('checked', isChecked); });
                    applyFilter();
                });

                removeAllCb.on('change', function () {
                    var isChecked = $(this).prop('checked');
                    var cbs = dropdown.data('itemCbs') || itemCbs;
                    if (isChecked) {
                        allCb.prop('checked', false);
                        cbs.forEach(function (cb) { cb.prop('checked', false); });
                    } else {
                        allCb.prop('checked', true);
                        cbs.forEach(function (cb) { cb.prop('checked', true); });
                    }
                    applyFilter();
                });

                itemCbs.forEach(function (cb) { cb.on('change', applyFilter); });
            });

            $(document).off('click.dashAlertDtFilter').on('click.dashAlertDtFilter', function (e) {
                var $tg = $(e.target);
                if ($tg.closest('.dt-filter-wrapper').length || $tg.closest('.dash-alerts-filter-dropdown').length) return;
                closeAllDashAlertFilterDropdowns();
            });

            api.on('draw', function () {
                if (typeof config.onDraw === 'function') config.onDraw();
                repositionOpenDashAlertFilters();
            });
        }
    });
}

function initActiveAlertsDataTable() {
    initDashFilterableDataTable('#dash-active-alerts-table', {
        searchLabel: 'Search alerts:',
        onDraw: refreshActiveAlertsSummaryLine
    });
}

function initDashModalDataTables(type) {
    if (type === 'activeAlerts' || typeof $ === 'undefined' || !$.fn.DataTable) return;
    $('.dash-filterable-table').each(function () {
        initDashFilterableDataTable(this, { searchLabel: 'Search:' });
    });
}

function fmtTime(dt) { if (!dt) return 'N/A'; const d = new Date(dt.replace(' ', 'T')); return d.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit', hour12: true}); }
function fmtDateTime(dt) { if (!dt) return 'N/A'; const d = new Date(dt.replace(' ', 'T')); return d.toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) + ' ' + d.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit', hour12: true}); }
function initials(n) { return (n || 'NA').substring(0, 2).toUpperCase(); }
function sevBadge(s) { const c = {critical:'bg-red-100 text-red-700',high:'bg-orange-100 text-orange-700',medium:'bg-amber-100 text-amber-700',low:'bg-slate-100 text-slate-600'}; return '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold ' + (c[s] || c.low) + '">' + esc((s||'').charAt(0).toUpperCase()+(s||'').slice(1)) + '</span>'; }

function openModal(type) {
    closeAllDashAlertFilterDropdowns();
    destroyDashActiveAlertsTable();
    if (typeof $ !== 'undefined') {
        try { $(document).off('click.dashAlertDtFilter'); } catch (e) {}
    }
    modalBody.innerHTML = LOADER;
    modalEl.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    document.getElementById('modal-back-btn').classList.add('hidden');
    modalBackTarget = null;
    const cfg = modalConfigs[type];
    if (!cfg) return;
    modalTitle.textContent = cfg.title;
    modalSubtitle.textContent = cfg.subtitle;
    modalIcon.className = 'size-9 rounded-lg flex items-center justify-center ' + cfg.iconCls;
    modalIcon.innerHTML = '<span class="material-symbols-outlined fill-1">' + cfg.icon + '</span>';
    if (cfg.localData !== undefined) {
        cfg.render(cfg.localData);
        initDashModalDataTables(type);
    } else {
        let fetchUrl = BASE + cfg.url;
        fetch(fetchUrl).then(r => r.json()).then(d => {
            cfg.render(d);
            initDashModalDataTables(type);
        }).catch(() => { modalBody.innerHTML = EMPTY('Failed to load data'); });
    }
}

function closeModal() {
    closeAllDashAlertFilterDropdowns();
    destroyDashActiveAlertsTable();
    if (typeof $ !== 'undefined') {
        try { $(document).off('click.dashAlertDtFilter'); } catch (e) {}
    }
    modalEl.classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('modal-back-btn').classList.add('hidden');
}

let modalBackTarget = null;
function modalGoBack() {
    document.getElementById('modal-back-btn').classList.add('hidden');
    if (modalBackTarget) openModal(modalBackTarget);
}

/* ── Dashboard visitor row actions ─────────────────────────────────────── */
function getRowInvitationId(btn) {
    return btn.closest('tr').dataset.invitationId;
}

function dashboardCheckIn(btn) {
    const invId = getRowInvitationId(btn);
    if (!invId) return;
    btn.disabled = true;
    btn.querySelector('span').textContent = 'hourglass_empty';
    fetch('<?= site_url('dashboard/quickCheckIn') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: 'invitation_id=' + encodeURIComponent(invId)
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) {
            btn.disabled = false;
            btn.querySelector('span').textContent = 'login';
            alert(d.message || 'Check-in failed');
            return;
        }
        // Update row status badge and swap action buttons without full reload
        const row = btn.closest('tr');
        row.dataset.visitorStatus = 'checkedin';
        const statusCell = row.querySelector('[data-status-badge]');
        if (statusCell) {
            statusCell.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
            statusCell.innerHTML = '<span class="size-1.5 rounded-full bg-green-500 inline-block"></span>On-Site';
        }
        const actionCell = row.querySelector('td:last-child');
        if (actionCell) {
            actionCell.innerHTML =
                '<button onclick="dashboardViewVisitor(this)" class="text-slate-400 hover:text-primary transition-colors p-1" title="View Details"><span class="material-symbols-outlined text-[20px]">visibility</span></button>' +
                '<button onclick="dashboardCheckOut(this)" class="text-slate-400 hover:text-red-600 transition-colors p-1" title="Check Out"><span class="material-symbols-outlined text-[20px]">logout</span></button>';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.querySelector('span').textContent = 'login';
        alert('Network error, please try again');
    });
}

function dashboardCheckOut(btn) {
    // Placeholder — will be implemented with full check-out flow later
    alert('Check-out flow coming soon.');
}

function dashboardViewVisitor(btn) {
    window.location.href = '<?= site_url('invitations') ?>';
}

function dashboardEditVisitor(btn) {
    window.location.href = '<?= site_url('invitations') ?>';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

function buildTable(headers, rows) {
    if (!rows.length) return '';
    let h = '<table class="dash-filterable-table display w-full text-left border-collapse" style="width:100%"><thead><tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">';
    headers.forEach(th => { h += '<th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">' + th + '</th>'; });
    h += '</tr></thead><tbody class="divide-y divide-slate-200 dark:divide-slate-700">';
    rows.forEach(tr => { h += '<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">'; tr.forEach(td => { h += '<td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap">' + td + '</td>'; }); h += '</tr>'; });
    h += '</tbody></table>';
    return '<div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">' + h + '</div>';
}

function ackFromModal(id, btn) {
    btn.disabled = true; btn.innerHTML = '<svg class="animate-spin size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
    fetch(BASE + '/dashboard/acknowledgeAlert', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}, body: 'alert_id=' + id })
    .then(r => r.json()).then(d => {
        if (!d.success) { btn.disabled = false; btn.textContent = 'Acknowledge'; return; }
        var table = document.getElementById('dash-active-alerts-table');
        if (table && typeof $ !== 'undefined' && $.fn.DataTable && $.fn.DataTable.isDataTable(table)) {
            var dt = $(table).DataTable();
            dt.row($(btn).closest('tr')).remove().draw(false);
            refreshActiveAlertsSummaryLine();
            if (dt.rows().count() === 0) {
                destroyDashActiveAlertsTable();
                if (typeof $ !== 'undefined') {
                    try { $(document).off('click.dashAlertDtFilter'); } catch (e2) {}
                }
                modalBody.innerHTML = '<p id="active-alerts-summary" class="text-sm text-slate-500 mb-4">0 total alerts</p>' + EMPTY('No security alerts');
            }
            applySecurityWidgetPayload(d);
            return;
        }
        const row = btn.closest('tr'); if (row) { row.style.transition = 'opacity .3s'; row.style.opacity = '0'; setTimeout(() => row.remove(), 300); }
        applySecurityWidgetPayload(d);
    });
}

const modalConfigs = {
    accessDenied: {
        title: 'Access Denied Incidents', subtitle: 'Acknowledged — last 24 hours',
        icon: 'gpp_bad', iconCls: 'bg-red-100 text-red-600',
        url: '/dashboard/accessDeniedData',
        render(d) {
            if (!d.success || !d.data.length) { modalBody.innerHTML = EMPTY('No acknowledged access denied incidents in the last 24 hours'); return; }
            const rows = d.data.map(a => [
                '<div class="flex items-center gap-2"><span class="material-symbols-outlined text-red-500 text-[16px]">block</span>' + esc(a.incident_type) + '</div>',
                sevBadge(a.severity),
                '<div class="flex items-center gap-2"><div class="size-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 text-[10px] font-bold">' + initials(a.visitor_name || 'Unknown') + '</div><div><p class="font-medium text-slate-900 dark:text-white">' + esc(a.visitor_name || 'Unknown') + '</p></div></div>',
                esc(a.location || 'N/A'),
                fmtDateTime(a.created_at),
                a.is_acknowledged == 1
                    ? '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700"><span class="material-symbols-outlined text-[12px]">check_circle</span>Acknowledged</span>'
                    : '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700"><span class="material-symbols-outlined text-[12px]">error</span>Pending</span>',
            ]);
            modalBody.innerHTML = '<p class="text-sm text-slate-500 mb-4">' + d.data.length + ' incident' + (d.data.length !== 1 ? 's' : '') + ' found</p>' + buildTable(['Incident', 'Severity', 'Visitor', 'Location', 'Date & Time', 'Status'], rows);
        }
    },
    overstay: {
        title: 'Visitor Overstay Alerts', subtitle: 'Visitors past scheduled window',
        icon: 'timer_off', iconCls: 'bg-amber-100 text-amber-600',
        url: '/dashboard/overstayData',
        render(d) {
            if (!d.success) { modalBody.innerHTML = EMPTY('Failed to load data'); return; }
            let html = '';
            if (d.physicalOverstays && d.physicalOverstays.length) {
                const rows = d.physicalOverstays.map(v => {
                    const endTs = new Date(v.schedule_end.replace(' ', 'T')).getTime();
                    const overMin = Math.max(0, Math.floor((Date.now() - endTs) / 60000));
                    const overH = Math.floor(overMin / 60), overM = overMin % 60;
                    const overLabel = overH > 0 ? overH + 'h ' + overM + 'm' : overM + 'm';
                    const nowLabel = new Date().toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit', hour12: true});
                    return [
                        '<div class="flex items-center gap-2"><div class="size-7 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 text-[10px] font-bold">' + initials(v.visitor_name) + '</div><div><p class="font-medium text-slate-900 dark:text-white">' + esc(v.visitor_name) + '</p></div></div>',
                        esc(v.contact_no || 'N/A'), esc(v.ic_no || 'N/A'), esc(v.location), fmtDateTime(v.check_in_time), fmtDateTime(v.schedule_end), nowLabel,
                        '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700"><span class="material-symbols-outlined text-[12px]">schedule</span>+' + overLabel + '</span>',
                        esc(v.host_name)
                    ];
                });
                html += buildTable(['Visitor Name', 'Contact No', 'IC No', 'Location', 'Check-in Time', 'Expected End', 'Current Time', 'Overstay Duration', 'Host'], rows);
            }
            if (!html) html = EMPTY('No visitor overstay alerts at this time');
            modalBody.innerHTML = html;
        }
    },
    alertDetail: {
        title: 'Security Alert Detail', subtitle: 'Incident information',
        icon: 'warning', iconCls: 'bg-red-100 text-red-600',
        url: '',
        render(d) {
            if (!d.success || !d.data) { modalBody.innerHTML = EMPTY('Alert not found'); return; }
            const a = d.data;
            let html = '<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">';
            const fields = [['Incident Type', a.incident_type], ['Severity', null], ['Location', a.location || 'N/A'], ['Time', fmtDateTime(a.created_at)], ['Visitor Name', a.visitor_name || 'Unknown'], ['Status', null]];
            fields.forEach(([label, val]) => {
                html += '<div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50"><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">' + label + '</p>';
                if (label === 'Severity') html += sevBadge(a.severity);
                else if (label === 'Status') html += a.is_acknowledged == 1 ? '<span class="inline-flex items-center gap-1 text-sm font-medium text-green-600"><span class="material-symbols-outlined text-[16px] fill-1">check_circle</span>Acknowledged</span>' : '<span class="inline-flex items-center gap-1 text-sm font-medium text-red-600"><span class="material-symbols-outlined text-[16px] fill-1">error</span>Pending</span>';
                else html += '<p class="text-sm font-medium text-slate-900 dark:text-white">' + esc(val) + '</p>';
                html += '</div>';
            });
            html += '</div>';
            if (a.description) html += '<div class="mt-4 p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50"><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Description</p><p class="text-sm text-slate-700 dark:text-slate-300">' + esc(a.description) + '</p></div>';
            if (a.is_acknowledged == 1 && a.acknowledged_by_name) html += '<div class="mt-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800"><p class="text-sm text-green-700 dark:text-green-400 font-medium">Acknowledged by ' + esc(a.acknowledged_by_name) + ' on ' + fmtDateTime(a.acknowledged_at) + '</p></div>';
            if (a.is_acknowledged == 0) html += '<div class="mt-4 flex justify-end"><button onclick="ackFromDetail(' + a.id + ', this)" class="flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-lg transition-colors"><span class="material-symbols-outlined text-[18px]">check</span>Acknowledge</button></div>';
            modalBody.innerHTML = html;
        }
    },
    expectedToday: {
        title: 'Expected Today', subtitle: new Date().toLocaleDateString('en-US', {month: 'long', day: 'numeric', year: 'numeric'}),
        icon: 'calendar_month', iconCls: 'bg-indigo-100 text-indigo-600',
        url: '/dashboard/expectedTodayData',
        render(d) {
            if (!d.success || !d.data.length) { modalBody.innerHTML = EMPTY('No visitors expected today'); return; }
            const rows = d.data.map((v, i) => {
                return [
                    '<span class="text-slate-500 font-medium">' + (i + 1) + '</span>',
                    '<div class="flex items-center gap-2"><div class="size-7 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-[10px] font-bold">' + initials(v.full_name) + '</div><div><p class="font-medium text-slate-900 dark:text-white">' + esc(v.full_name || 'N/A') + '</p></div></div>',
                    fmtTime(v.date_from),
                    esc(v.contact_no || 'N/A'),
                    esc(v.ic_no || 'N/A'),
                    esc(v.host_name || 'N/A')
                ];
            });
            modalBody.innerHTML = '<p class="text-sm text-slate-500 mb-4">' + d.data.length + ' visitor' + (d.data.length !== 1 ? 's' : '') + ' expected</p>' + buildTable(['#', 'Visitor Name', 'Appointment Time', 'Contact No', 'IC No', 'Host'], rows);
        }
    },
    onSite: {
        title: 'Currently On-Site', subtitle: 'Visitors currently on premises',
        icon: 'group', iconCls: 'bg-primary/10 text-primary',
        url: '/dashboard/onSiteData',
        render(d) {
            if (!d.success || !d.data.length) { modalBody.innerHTML = EMPTY('No visitors currently on-site'); return; }
            const rows = d.data.map((v, i) => {
                const nm = v.visitor_name || v.visitor_email || '-';
                const avatarEl = '<div class="size-7 rounded-full bg-primary/10 flex items-center justify-center text-primary text-[10px] font-bold relative overflow-hidden">'
                    + initials(nm)
                    + (v.profile_photo_path ? '<img src="' + BASE + 'uploads/' + esc(v.profile_photo_path) + '" class="absolute inset-0 size-7 object-cover" alt="" onerror="this.remove()">' : '')
                    + '</div>';
                return [
                    '<span class="text-slate-500 font-medium">' + (i + 1) + '</span>',
                    '<div class="flex items-center gap-2">' + avatarEl + '<div><p class="font-medium text-slate-900 dark:text-white">' + esc(nm) + '</p><p class="text-[11px] text-slate-400">' + esc(v.contact || '') + '</p></div></div>',
                    esc(v.location || 'N/A'),
                    fmtDateTime(v.check_in_time),
                    esc(String(v.host_name || 'N/A').toUpperCase()),
                    esc(v.staff_no || 'N/A')
                ];
            });
            modalBody.innerHTML = '<p class="text-sm text-slate-500 mb-4">' + d.data.length + ' visitor' + (d.data.length !== 1 ? 's' : '') + ' on-site</p>' + buildTable(['#', 'Visitor Name', 'Location', 'Check-in Time', 'Host', 'Staff No'], rows);
        }
    },
    checkedOut: {
        title: 'Checked Out Today', subtitle: new Date().toLocaleDateString('en-US', {month: 'long', day: 'numeric', year: 'numeric'}),
        icon: 'logout', iconCls: 'bg-slate-200 text-slate-600',
        url: '/dashboard/checkedOutData',
        render(d) {
            if (!d.success || !d.data.length) { modalBody.innerHTML = EMPTY('No visitors checked out today'); return; }
            const rows = d.data.map((v, i) => {
                return [
                    '<span class="text-slate-500 font-medium">' + (i + 1) + '</span>',
                    '<div class="flex items-center gap-2"><div class="size-7 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 text-[10px] font-bold">' + initials(v.visitor_name) + '</div><div><p class="font-medium text-slate-900 dark:text-white">' + esc(v.visitor_name || 'N/A') + '</p></div></div>',
                    esc(v.contact_no || 'N/A'),
                    esc(v.ic_no || 'N/A'),
                    fmtDateTime(v.check_out_time),
                    esc(v.host_name || 'N/A')
                ];
            });
            modalBody.innerHTML = '<p class="text-sm text-slate-500 mb-4">' + d.data.length + ' visitor' + (d.data.length !== 1 ? 's' : '') + ' checked out</p>' + buildTable(['#', 'Visitor Name', 'Contact No', 'IC No', 'Check Out Time', 'Host'], rows);
        }
    },
    recentActivity: {
        title: 'Recent Activity', subtitle: 'All system events — last 30 days',
        icon: 'history', iconCls: 'bg-primary/10 text-primary',
        url: '/dashboard/recentActivityData',
        render(d) {
            if (!d.success || !d.data.length) { modalBody.innerHTML = EMPTY('No activity in the last 30 days'); return; }
            const typeIcon = {
                created:        { icon: 'add_circle',   cls: 'text-amber-500'  },
                approved:       { icon: 'check_circle', cls: 'text-green-500'  },
                rejected:       { icon: 'cancel',       cls: 'text-red-500'    },
                check_in:       { icon: 'login',        cls: 'text-green-500'  },
                check_out:      { icon: 'logout',       cls: 'text-slate-400'  },
                door_access:    { icon: 'sensor_door',  cls: 'text-blue-500'   },
                security_alert: { icon: 'warning',      cls: 'text-red-500'    },
            };
            const typeBadge = {
                created:        'bg-amber-100 text-amber-700',
                approved:       'bg-green-100 text-green-700',
                rejected:       'bg-red-100 text-red-700',
                check_in:       'bg-green-100 text-green-700',
                check_out:      'bg-slate-100 text-slate-600',
                door_access:    'bg-blue-100 text-blue-700',
                security_alert: 'bg-red-100 text-red-700',
            };
            const rows = d.data.map(a => {
                const ti = typeIcon[a.type] || { icon: 'info', cls: 'text-slate-400' };
                const badgeCls = typeBadge[a.type] || 'bg-slate-100 text-slate-600';
                let desc = '';
                if (a.type === 'created')        desc = 'Invitation created for <strong>' + esc(a.name) + '</strong>';
                else if (a.type === 'approved')  desc = 'Invitation approved for <strong>' + esc(a.name) + '</strong>';
                else if (a.type === 'rejected')  desc = 'Invitation rejected for <strong>' + esc(a.name) + '</strong>';
                else if (a.type === 'check_in')  desc = '<strong>' + esc(a.name) + '</strong> checked in';
                else if (a.type === 'check_out') desc = '<strong>' + esc(a.name) + '</strong> checked out';
                else if (a.type === 'door_access') desc = '<strong>' + esc(a.name) + '</strong> ' + (a.extra === 'checkin' ? 'entered via ' : 'exited via ') + esc(a.actor);
                else desc = 'Alert: <strong>' + esc(a.extra) + '</strong>';
                return [
                    '<div class="flex items-center gap-2"><span class="material-symbols-outlined text-[16px] ' + ti.cls + '">' + ti.icon + '</span><span class="text-xs font-medium px-1.5 py-0.5 rounded-full ' + badgeCls + '">' + esc(a.label) + '</span></div>',
                    '<span class="text-sm">' + desc + '</span>',
                    esc(a.actor),
                    '<span class="text-xs text-slate-400" title="' + esc(a.time) + '">' + esc(a.time_ago) + '</span>',
                ];
            });
            modalBody.innerHTML = '<p class="text-sm text-slate-500 mb-4">' + d.data.length + ' event' + (d.data.length !== 1 ? 's' : '') + ' found</p>' + buildTable(['Type', 'Description', 'By / Location', 'When'], rows);
        }
    },
    activeAlerts: {
        title: 'Total Security Alerts', subtitle: 'All alerts — active and acknowledged',
        icon: 'shield', iconCls: 'bg-red-100 text-red-600',
        url: '/dashboard/activeAlertsData',
        render(d) {
            if (!d.success) {
                modalBody.innerHTML = EMPTY('Failed to load data');
                return;
            }
            if (!d.data.length) {
                modalBody.innerHTML = '<p id="active-alerts-summary" class="text-sm text-slate-500 mb-4">0 total alerts</p>' + EMPTY('No security alerts');
                return;
            }

            const active = d.data.filter(a => a.is_acknowledged != 1).length;
            let tbody = '';
            d.data.forEach((a, i) => {
                const ackBadge = a.is_acknowledged == 1
                    ? '<span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700">Acknowledged</span>'
                    : '<span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">Active</span>';
                const actions = a.is_acknowledged == 1
                    ? '<button type="button" onclick="openAlertDetail(' + a.id + ', \'activeAlerts\')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold rounded transition-colors">View</button>'
                    : '<div class="flex gap-1"><button type="button" onclick="openAlertDetail(' + a.id + ', \'activeAlerts\')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold rounded transition-colors">View</button><button type="button" onclick="ackFromModal(' + a.id + ', this)" class="px-2 py-1 bg-primary hover:bg-primary-dark text-white text-[10px] font-bold rounded transition-colors">Acknowledge</button></div>';
                const isActive = a.is_acknowledged != 1 ? '1' : '0';
                const incidentCell = '<div class="flex items-center gap-2"><span class="material-symbols-outlined text-red-500 text-[16px] fill-1">warning</span>' + esc(a.incident_type) + '</div>';
                tbody += '<tr data-active="' + isActive + '">'
                    + '<td class="text-slate-500 font-medium">' + (i + 1) + '</td>'
                    + '<td>' + incidentCell + '</td>'
                    + '<td>' + sevBadge(a.severity) + '</td>'
                    + '<td><div class="flex items-center gap-2"><div class="size-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 text-[10px] font-bold">' + initials(a.visitor_name || 'Unknown') + '</div><div><p class="font-medium text-slate-900 dark:text-white">' + esc(a.visitor_name || 'Unknown') + '</p></div></div></td>'
                    + '<td>' + esc(a.contact_no || 'N/A') + '</td>'
                    + '<td>' + esc(a.ic_no || 'N/A') + '</td>'
                    + '<td>' + esc(a.location || 'N/A') + '</td>'
                    + '<td>' + fmtDateTime(a.created_at) + '</td>'
                    + '<td>' + esc(a.host_name || 'N/A') + '</td>'
                    + '<td>' + ackBadge + '</td>'
                    + '<td>' + actions + '</td>'
                    + '</tr>';
            });

            modalBody.innerHTML = ''
                + '<p id="active-alerts-summary" class="text-sm text-slate-500 mb-4">' + d.data.length + ' total alert' + (d.data.length !== 1 ? 's' : '') + ' &mdash; <span class="text-red-600 font-semibold">' + active + ' active</span></p>'
                + '<div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 p-3 sm:p-5">'
                + '<div class="overflow-x-auto custom-scrollbar pb-1">'
                + '<table id="dash-active-alerts-table" class="w-full whitespace-nowrap display" style="width:100%">'
                + '<thead><tr>'
                + '<th>#</th><th>INCIDENT</th><th>SEVERITY</th><th>VISITOR</th><th>CONTACT NO</th><th>IC NO</th><th>LOCATION</th><th>DATE &amp; TIME</th><th>HOST</th><th>STATUS</th><th>ACTIONS</th>'
                + '</tr></thead><tbody>' + tbody + '</tbody></table>'
                + '</div></div>';

            initActiveAlertsDataTable();
        }
    },
    upcomingAppts: {
        title: 'Upcoming Appointments', subtitle: 'Scheduled visits after now',
        icon: 'event_upcoming', iconCls: 'bg-indigo-100 text-indigo-600',
        localData: UPCOMING_APPTS,
        render(data) {
            if (!data.length) { modalBody.innerHTML = EMPTY('No upcoming appointments'); return; }
            const rows = data.map(a => [
                '<div class="flex items-center gap-2"><div class="size-7 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-[10px] font-bold">' + initials(a.visitor_name) + '</div>' + esc(a.visitor_name) + '</div>',
                esc(a.host_name), esc(a.date), esc(a.time), esc(a.reason || 'Visit')
            ]);
            modalBody.innerHTML = '<p class="text-sm text-slate-500 mb-4">' + data.length + ' upcoming appointment' + (data.length !== 1 ? 's' : '') + '</p>' + buildTable(['Visitor', 'Host', 'Date', 'Time', 'Reason'], rows);
        }
    },
    todayAppts: {
        title: "Today's Appointments", subtitle: new Date().toLocaleDateString('en-US', {month: 'long', day: 'numeric', year: 'numeric'}),
        icon: 'today', iconCls: 'bg-emerald-100 text-emerald-600',
        localData: TODAY_APPTS,
        render(data) {
            if (!data.length) { modalBody.innerHTML = EMPTY('No appointments today'); return; }
            const statusBadge = s => {
                const cls = s === 'In Progress' ? 'bg-green-100 text-green-700' : s === 'Completed' ? 'bg-slate-100 text-slate-600' : 'bg-amber-100 text-amber-700';
                return '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold ' + cls + '">' + esc(s) + '</span>';
            };
            const rows = data.map(a => [
                '<div class="flex items-center gap-2"><div class="size-7 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-[10px] font-bold">' + initials(a.visitor_name) + '</div>' + esc(a.visitor_name) + '</div>',
                esc(a.host_name), esc(a.time) + ' – ' + esc(a.end_time), esc(a.reason || 'Visit'), statusBadge(a.status)
            ]);
            modalBody.innerHTML = '<p class="text-sm text-slate-500 mb-4">' + data.length + ' appointment' + (data.length !== 1 ? 's' : '') + ' today</p>' + buildTable(['Visitor', 'Host', 'Date & Time', 'Reason', 'Status'], rows);
        }
    }
};

function openAlertDetail(id, backTarget) {
    closeAllDashAlertFilterDropdowns();
    destroyDashActiveAlertsTable();
    if (typeof $ !== 'undefined') {
        try { $(document).off('click.dashAlertDtFilter'); } catch (e) {}
    }
    modalBody.innerHTML = LOADER;
    modalEl.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    modalTitle.textContent = 'Security Alert #' + id;
    modalSubtitle.textContent = 'Incident detail';
    modalIcon.className = 'size-9 rounded-lg flex items-center justify-center bg-red-100 text-red-600';
    modalIcon.innerHTML = '<span class="material-symbols-outlined fill-1">warning</span>';
    if (backTarget) {
        modalBackTarget = backTarget;
        document.getElementById('modal-back-btn').classList.remove('hidden');
    }
    fetch(BASE + '/dashboard/alertDetailData/' + id).then(r => r.json()).then(d => modalConfigs.alertDetail.render(d)).catch(() => { modalBody.innerHTML = EMPTY('Failed to load alert'); });
}

function openAlertFromBanner(id) {
    openAlertDetail(id);
    modalEl.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function ackFromDetail(id, btn) {
    btn.disabled = true; btn.textContent = 'Acknowledging...';
    fetch(BASE + '/dashboard/acknowledgeAlert', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}, body: 'alert_id=' + id })
    .then(r => r.json()).then(d => {
        openAlertDetail(id);
        if (d.success) applySecurityWidgetPayload(d);
    });
}

// ── Widget Customization ──────────────────────────────────────────────────────
let widgetPrefs = <?= json_encode($widgetPreferences) ?>;
let sortableInstance = null;
let drawerCollapsed  = false;

function openWidgetDrawer() {
    drawerCollapsed = false;
    const drawer = document.getElementById('widget-drawer');
    const tab    = document.getElementById('widget-drawer-tab');
    drawer.style.transform = '';
    drawer.classList.add('open');
    // Show the collapse tab beside the drawer
    tab.style.display = 'flex';
    tab.style.right   = '320px';
    document.getElementById('widget-drawer-tab-icon').textContent = 'chevron_right';
    document.getElementById('analytics-assistant-launcher').style.display = 'none';
    document.body.classList.add('widget-edit-mode');
    if (!sortableInstance) {
        sortableInstance = new Sortable(document.getElementById('widgets-container'), {
            animation: 150,
            handle: '.widget-drag-handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: renderDrawerList,
        });
    }
    renderDrawerList();
}

function toggleDrawerCollapse() {
    drawerCollapsed = !drawerCollapsed;
    const drawer = document.getElementById('widget-drawer');
    const tab    = document.getElementById('widget-drawer-tab');
    const icon   = document.getElementById('widget-drawer-tab-icon');
    if (drawerCollapsed) {
        drawer.style.transform = 'translateX(100%)';
        tab.style.right        = '0px';
        icon.textContent       = 'chevron_left';
    } else {
        drawer.style.transform = 'translateX(0)';
        tab.style.right        = '320px';
        icon.textContent       = 'chevron_right';
    }
}

function closeWidgetDrawer() {
    drawerCollapsed = false;
    const drawer = document.getElementById('widget-drawer');
    const tab    = document.getElementById('widget-drawer-tab');
    drawer.style.transform = '';
    drawer.classList.remove('open');
    tab.style.display = 'none';
    document.getElementById('analytics-assistant-launcher').style.display = '';
    document.body.classList.remove('widget-edit-mode');
}

function doneWidgetCustomize() {
    const configs = collectCurrentPrefs();
    const body    = new URLSearchParams({ widgets: JSON.stringify(configs) });
    fetch(BASE + '/dashboard/saveWidgetPreferences', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: body.toString(),
    }).then(r => r.json()).then(d => {
        if (d.success) widgetPrefs = configs;
        closeWidgetDrawer();
    });
}

function renderDrawerList() {
    const container = document.getElementById('widgets-container');
    const wrappers  = Array.from(container.querySelectorAll('[data-widget-id]'));
    const list      = document.getElementById('drawer-widget-list');
    list.innerHTML  = '';
    wrappers.forEach((el, idx) => {
        const wid      = el.dataset.widgetId;
        const pref     = widgetPrefs.find(p => p.id === wid) || { id: wid, label: wid, visible: true };
        const visible  = !el.classList.contains('hidden');
        const isFirst  = idx === 0;
        const isLast   = idx === wrappers.length - 1;
        const isPoster = wid === 'poster-banner';
        const curImg   = isPoster ? (pref.image || '') : '';
        const item     = document.createElement('div');
        item.className = 'flex flex-col gap-1.5 p-2 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700';
        item.innerHTML = `
            <div class="flex items-center gap-2">
                <div class="flex flex-col gap-0.5 flex-shrink-0">
                    <button onclick="moveWidget('${wid}',-1)" ${isFirst ? 'disabled' : ''}
                        class="flex items-center justify-center size-6 rounded transition-colors ${isFirst ? 'text-slate-300 dark:text-slate-600 cursor-not-allowed' : 'text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-primary'}">
                        <span class="material-symbols-outlined text-[16px]">keyboard_arrow_up</span>
                    </button>
                    <button onclick="moveWidget('${wid}',1)" ${isLast ? 'disabled' : ''}
                        class="flex items-center justify-center size-6 rounded transition-colors ${isLast ? 'text-slate-300 dark:text-slate-600 cursor-not-allowed' : 'text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-primary'}">
                        <span class="material-symbols-outlined text-[16px]">keyboard_arrow_down</span>
                    </button>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 flex-1 truncate">${pref.label || wid}</span>
                <button onclick="toggleWidgetVisibility('${wid}')" title="${visible ? 'Hide' : 'Show'}"
                    class="flex items-center justify-center size-7 rounded-md transition-colors flex-shrink-0 ${visible ? 'text-primary hover:bg-primary/10' : 'text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'}">
                    <span class="material-symbols-outlined text-[18px]">${visible ? 'visibility' : 'visibility_off'}</span>
                </button>
            </div>
            ${isPoster ? `
            <div class="flex items-center gap-2 pl-8">
                <input type="file" id="poster-upload-input" accept="image/*" class="hidden" onchange="handlePosterUpload(this)">
                <button onclick="document.getElementById('poster-upload-input').click()"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 hover:bg-primary/20 text-primary text-xs font-medium rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[16px]">upload</span>
                    ${curImg ? 'Replace Image' : 'Upload Image'}
                </button>
                <span id="poster-upload-status" class="text-[11px] text-slate-400"></span>
            </div>
            ${curImg ? `<div class="rounded-lg overflow-hidden ml-8 h-16" style="max-width:calc(100% - 2rem);"><img src="${curImg}" class="w-full h-full object-cover block"></div>` : ''}
            ` : ''}`;
        list.appendChild(item);
    });
}

function moveWidget(wid, direction) {
    const container = document.getElementById('widgets-container');
    const wrappers  = Array.from(container.querySelectorAll('[data-widget-id]'));
    const idx       = wrappers.findIndex(el => el.dataset.widgetId === wid);
    if (idx < 0) return;
    const targetIdx = idx + direction;
    if (targetIdx < 0 || targetIdx >= wrappers.length) return;
    const el     = wrappers[idx];
    const target = wrappers[targetIdx];
    el.style.opacity    = '0.4';
    el.style.transition = 'opacity 0.12s';
    setTimeout(() => {
        direction === -1 ? container.insertBefore(el, target) : container.insertBefore(target, el);
        el.style.opacity = '1';
        renderDrawerList();
    }, 120);
}

function toggleWidgetVisibility(wid) {
    const el = document.querySelector(`[data-widget-id="${wid}"]`);
    if (!el) return;
    el.classList.toggle('hidden');
    renderDrawerList();
}

function hideWidget(wid) {
    const el = document.querySelector(`[data-widget-id="${wid}"]`);
    if (el) el.classList.add('hidden');
    renderDrawerList();
}

function handlePosterUpload(input) {
    const file = input.files[0];
    if (!file) return;
    const status = document.getElementById('poster-upload-status');
    status.textContent = 'Uploading…';
    const fd = new FormData();
    fd.append('image', file);
    fetch(BASE + '/dashboard/uploadPosterImage', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd,
    }).then(r => r.json()).then(d => {
        if (d.success) {
            const pi = widgetPrefs.findIndex(p => p.id === 'poster-banner');
            if (pi >= 0) widgetPrefs[pi].image = d.url;
            else widgetPrefs.push({ id: 'poster-banner', label: 'Poster Banner', visible: true, position: 99, image: d.url });
            // Update the live widget on dashboard
            const content = document.getElementById('poster-banner-content');
            if (content) content.innerHTML = `<img src="${d.url}" alt="Poster Banner" class="w-full h-full object-cover block">`;
            status.textContent = 'Uploaded!';
            setTimeout(() => { status.textContent = ''; renderDrawerList(); }, 1500);
        } else {
            status.textContent = d.message || 'Failed';
        }
    }).catch(() => { status.textContent = 'Error'; });
}

function collectCurrentPrefs() {
    const container = document.getElementById('widgets-container');
    return Array.from(container.querySelectorAll('[data-widget-id]')).map((el, idx) => {
        const wid  = el.dataset.widgetId;
        const pref = widgetPrefs.find(p => p.id === wid) || {};
        const base = { id: wid, label: pref.label || wid, visible: !el.classList.contains('hidden'), position: idx };
        if (wid === 'poster-banner') base.image = pref.image || null;
        return base;
    });
}

function resetWidgets() {
    if (!confirm('Reset dashboard to default layout?')) return;
    fetch(BASE + '/dashboard/saveWidgetPreferences', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams({ widgets: JSON.stringify([]) }).toString(),
    }).then(() => location.reload());
}

// Apply saved visibility on load
(function applyInitialVisibility() {
    widgetPrefs.forEach(pref => {
        const el = document.querySelector(`[data-widget-id="${pref.id}"]`);
        if (!el) return;
        if (!pref.visible) el.classList.add('hidden');
        else el.classList.remove('hidden');
    });
})();
</script>
</body>
</html>
