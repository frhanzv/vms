<?php
/**
 * Vendor dashboard. Data comes from App\Libraries\VendorDashboardStats via
 * Controllers\DashboardVendor.
 */
helper(['dashboard_chart', 'dashboard_cards']);
$passUrl = static fn(string $status = ''): string => base_url('vendors') . ($status !== '' ? '?status=' . rawurlencode($status) : '');
$viewUrl = static fn($id): string => base_url('vendorpassrequest/view/' . (int) $id);
$st = $stages;
?>
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
            theme: { extend: {
                colors: {
                    "primary": "#137fec", "primary-dark": "#0f66be",
                    "background-light": "#f6f7f8", "background-dark": "#101922",
                    "surface-light": "#ffffff", "surface-dark": "#1a2634",
                },
                fontFamily: { "display": ["Montserrat", "sans-serif"], "sans": ["Montserrat", "sans-serif"] },
                borderRadius: {"DEFAULT": "0.375rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
            } },
        }
    </script>
    <style>body { font-family: 'Montserrat', sans-serif; }</style>
    <?= view('partials/dashboard_viz_assets', ['part' => 'head']) ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white overflow-hidden">
<div class="flex h-screen w-full">
    <?= view('partials/sidebar') ?>

    <main class="relative flex h-screen flex-1 flex-col overflow-hidden">
        <header class="z-10 flex min-h-[5rem] flex-shrink-0 items-center justify-between border-b border-slate-200 bg-surface-light px-8 py-3 dark:border-slate-800 dark:bg-surface-dark">
            <div class="flex flex-col justify-center">
                <h2 class="text-lg font-bold leading-tight text-slate-900 dark:text-white">Vendor Dashboard<?= $isVendorAccount && $companyName !== '' ? ' · ' . esc($companyName) : '' ?></h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Today, <?= esc($currentDate) ?></p>
            </div>
            <div class="flex items-center gap-3">
            <?= view('partials/dashboard_customize', ['dash' => 'vendor']) ?>
            <?php if (! empty($canAdd)): ?>
                <?php if ($isVendorAccount): ?>
                <a href="<?= base_url('vendors/import') ?>" class="flex h-10 items-center gap-2 rounded-lg border border-primary bg-surface-light px-4 text-sm font-bold text-primary shadow-sm hover:bg-slate-50 dark:bg-surface-dark dark:hover:bg-slate-800">
                    <span class="material-symbols-outlined text-[20px]">upload_file</span>Import
                </a>
                <?php endif; ?>
                <?php helper('vendor_company'); if (! is_vendor_admin() || \App\Libraries\VendorWorkflow::enabled(\Config\Database::connect(), (int) current_company_id(), 'pass_request')): ?><a href="<?= base_url('vendors/vendorpassrequest') ?>" class="flex h-10 items-center gap-2 rounded-lg bg-primary px-4 text-sm font-bold text-white shadow-sm transition-colors hover:bg-primary-dark">
                    <span class="material-symbols-outlined text-[20px]">add</span>New Request
                </a><?php endif; ?>
            <?php endif; ?>
            </div>
        </header>

        <div class="viz-root flex-1 overflow-y-auto p-8">
            <?php if ($msg = session()->getFlashdata('error')): ?>
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><?= esc($msg) ?></div>
            <?php endif; ?>

            <?php if (! empty($locked)): ?>
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">
                    Your account is not linked to an active company, so there is nothing to show yet. Please contact the administrator.
                </div>
            <?php else: ?>

            <?php if (! dash_card_any('vendor')): ?>
                <p class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">No cards are switched on. Use <strong>Customize</strong> to show some.</p>
            <?php endif; ?>

            <div class="mx-auto flex max-w-7xl flex-col gap-6">

            <!-- Filters -->
            <form method="get" style="order: 0" class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-surface-light px-4 py-3 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                <span class="material-symbols-outlined text-[20px] text-slate-400">tune</span>
                <?php if (! empty($locationList)): ?>
                <select name="location" aria-label="Location" class="h-9 rounded-full border-slate-200 bg-slate-50 px-4 text-sm font-medium dark:border-slate-600 dark:bg-slate-800">
                    <option value="">All locations</option>
                    <?php foreach ($locationList as $code => $label): ?>
                    <option value="<?= esc($code, 'attr') ?>" <?= ($filters['location'] ?? '') === (string) $code ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
                <input type="date" name="from" aria-label="From" value="<?= esc($filters['from'], 'attr') ?>" class="h-9 rounded-full border-slate-200 bg-slate-50 px-4 text-sm dark:border-slate-600 dark:bg-slate-800"/>
                <span class="text-xs text-slate-400">to</span>
                <input type="date" name="to" aria-label="To" value="<?= esc($filters['to'], 'attr') ?>" class="h-9 rounded-full border-slate-200 bg-slate-50 px-4 text-sm dark:border-slate-600 dark:bg-slate-800"/>
                <button type="submit" class="h-9 rounded-full bg-primary px-5 text-sm font-bold text-white hover:bg-primary-dark">Apply</button>
                <a href="<?= base_url('dashboard/vendor') ?>" class="px-2 text-sm font-semibold text-slate-500 hover:text-primary">Reset</a>
                <span class="ml-auto text-xs text-slate-400">As at <?= esc($summary['asAt']) ?></span>
            </form>

            <?php if (dash_card('vendor', 'summary')): ?>
            <!-- Summary hero -->
            <section style="order: <?= dash_card_pos('vendor', 'summary') ?>" aria-label="Summary"
                     class="overflow-hidden rounded-2xl bg-gradient-to-br from-primary via-blue-600 to-indigo-700 text-white shadow-lg">
                <div class="grid gap-6 p-6 lg:grid-cols-5 lg:p-8">
                    <div class="lg:col-span-2">
                        <p class="text-xs font-bold uppercase tracking-widest text-white/70">Total Active Pass</p>
                        <p class="mt-2 text-6xl font-black leading-none"><?= dash_fmt($summary['activePasses']) ?></p>
                        <p class="mt-3 text-sm text-white/80">across <strong><?= dash_fmt($summary['activeCompanies']) ?></strong> companies &middot; avg <strong><?= esc((string) $summary['avgPerCompany']) ?></strong> per company</p>
                    </div>
                    <div class="grid grid-cols-3 gap-3 lg:col-span-3">
                        <?php foreach ([
                            ['New Application', $summary['newApplications'], 'note_add'],
                            ['Approved', $summary['approved'], 'check_circle'],
                            ['Pass Issuance', $summary['issuance'], 'receipt_long'],
                        ] as [$lbl, $val, $ico]): ?>
                        <div class="rounded-xl bg-white/15 p-4 backdrop-blur-sm">
                            <span class="material-symbols-outlined text-[22px] text-white/80"><?= $ico ?></span>
                            <p class="mt-2 text-3xl font-black"><?= dash_fmt($val) ?></p>
                            <p class="mt-1 text-xs font-semibold text-white/80"><?= esc($lbl) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 border-t border-white/20 bg-black/10 px-6 py-3 text-sm lg:px-8">
                    <span class="inline-flex items-center gap-2 rounded-full bg-red-500/90 px-3 py-1 font-semibold"><span class="material-symbols-outlined text-[18px]">event_busy</span><?= dash_fmt($summary['expired']) ?> Expired</span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-amber-400 px-3 py-1 font-semibold text-amber-950"><span class="material-symbols-outlined text-[18px]">event_upcoming</span><?= dash_fmt($summary['expiring']) ?> Expiring in 30 days</span>
                    <span class="ml-auto text-xs text-white/70 self-center"><?= esc($filters['from']) ?> &rarr; <?= esc($filters['to']) ?></span>
                </div>
            </section>
            <?php endif; ?>

            <?php if (dash_card('vendor', 'gate_summary') && ! empty($gate['available'])): ?>
            <!-- Gate activity -->
            <section style="order: <?= dash_card_pos('vendor', 'gate_summary') ?>" aria-label="Gate activity" class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <?php foreach ([
                    ['Total Check IN',       $gate['checkIn'],         'login',         'emerald'],
                    ['Total Check Out',      $gate['checkOut'],        'logout',        'sky'],
                    ['Near Out Of Window',   $gate['nearOutOfWindow'], 'hourglass_top', 'amber'],
                    ['Out Of Window',        $gate['outOfWindow'],     'warning',       'red'],
                ] as [$lbl, $val, $ico, $c]):
                    $cls = [
                        'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800',
                        'sky'     => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-900/20 dark:text-sky-300 dark:border-sky-800',
                        'amber'   => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-800',
                        'red'     => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-800',
                    ][$c]; ?>
                <div class="flex items-center gap-4 rounded-2xl border p-4 <?= $cls ?>">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-white/70 dark:bg-white/10"><span class="material-symbols-outlined text-[26px]"><?= $ico ?></span></span>
                    <div><p class="text-3xl font-black leading-none"><?= dash_fmt($val) ?></p><p class="mt-1 text-xs font-semibold"><?= esc($lbl) ?></p></div>
                </div>
                <?php endforeach; ?>
            </section>
            <?php endif; ?>

            <!-- Charts -->
            <section class="grid grid-cols-1 gap-6 xl:grid-cols-2" style="order: 20">
                <?php if (dash_card('vendor', 'chart_location')): ?>
                <div class="rounded-2xl border border-slate-200 bg-surface-light p-6 shadow-sm dark:border-slate-700 dark:bg-surface-dark" style="order: <?= dash_card_pos('vendor', 'chart_location') ?>">
                    <h3 class="text-base font-bold">By Location</h3>
                    <p class="mb-4 text-xs text-slate-500 dark:text-slate-400">Passes by location access</p>
                    <?php
                    $li = [];
                    foreach ($byLocation as $l) {
                        $li[] = ['label' => $l['label'], 'value' => (int) $l['c']];
                    }
                    echo $li ? dash_hbars($li, 'passes', 'Passes by location') : '<p class="text-sm text-slate-500">No location data yet.</p>';
                    ?>
                </div>
                <?php endif; ?>
                <?php if (dash_card('vendor', 'chart_pipeline')): ?>
                <div>
                <div class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="mb-1 text-sm font-bold">Pass pipeline</h3>
                    <p class="mb-3 text-xs text-slate-500 dark:text-slate-400">Where every application is right now</p>
                    <?php
                    $stageLinks = $isVendorAccount ? [] : [
                        'Process'  => base_url('vendors/process-list'),
                        'Issuance' => base_url('vendors/issuance-list'),
                        'Closed'   => base_url('vendors/closed-list'),
                    ];
                    $tones = ['Draft' => 1, 'Pending' => 1, 'Rejected' => 1, 'Process' => 2, 'Issuance' => 4, 'Closed' => 1];
                    $items = [];
                    foreach (\App\Libraries\VendorDashboardStats::STAGES as $s) {
                        $items[] = ['label' => $s, 'value' => $st[$s], 'tone' => $tones[$s], 'href' => $stageLinks[$s] ?? null];
                    }
                    echo dash_hbars($items, 'applications', 'Applications by pipeline stage');
                    ?>
                </div>
                </div>
                <?php endif; ?>


            </section>

            <!-- Everything from the previous dashboard -->
            <details style="order: 30" class="group rounded-2xl border border-slate-200 bg-surface-light shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                <summary class="flex cursor-pointer list-none items-center justify-between px-6 py-4 text-sm font-bold">
                    <span class="flex items-center gap-2"><span class="material-symbols-outlined text-primary">dashboard_customize</span>More charts &amp; lists</span>
                    <span class="material-symbols-outlined transition-transform group-open:rotate-180">expand_more</span>
                </summary>
                <div class="border-t border-slate-100 p-6 dark:border-slate-800">
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Key numbers">
                <?php if (dash_card('vendor', 'kpi_total')): ?>
                <div class="[&>*]:h-full" style="order: <?= dash_card_pos('vendor', 'kpi_total') ?>">
                <?= dash_stat('Total applications', $total, 'All time', 'groups', 'info', $passUrl()) ?>
                </div>
                <?php endif; ?>
                <?php if (dash_card('vendor', 'kpi_active')): ?>
                <div class="[&>*]:h-full" style="order: <?= dash_card_pos('vendor', 'kpi_active') ?>">
                <?= dash_stat('Active passes', $activePasses, $expired . ' expired', 'badge', 'good', $passUrl('Approved')) ?>
                </div>
                <?php endif; ?>
                <?php if (dash_card('vendor', 'kpi_expiring')): ?>
                <div class="[&>*]:h-full" style="order: <?= dash_card_pos('vendor', 'kpi_expiring') ?>">
                <?= dash_stat('Expiring ≤ 30 days', $expiring, 'Active passes', 'event_upcoming', $expiring > 0 ? 'warn' : 'neutral') ?>
                </div>
                <?php endif; ?>
                <?php if (dash_card('vendor', 'kpi_awaiting')): ?>
                <div class="[&>*]:h-full" style="order: <?= dash_card_pos('vendor', 'kpi_awaiting') ?>">
                <?= dash_stat($isVendorAccount ? 'Awaiting KPK' : 'Awaiting approval', $st['Pending'], $isVendorAccount ? ($st['Rejected'] . ' rejected · ' . $st['Draft'] . ' draft') : ($st['Rejected'] . ' rejected'), 'schedule', $st['Pending'] > 0 ? 'warn' : 'neutral', $passUrl('Pending')) ?>
                </div>
                <?php endif; ?>
            </section>

            <section class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
                <?php if (dash_card('vendor', 'chart_monthly')): ?>
                <div class="[&>*]:h-full" style="order: <?= dash_card_pos('vendor', 'chart_monthly') ?>">
                <div class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="mb-1 text-sm font-bold">Applications per month</h3>
                    <p class="mb-2 text-xs text-slate-500 dark:text-slate-400">Last 6 months</p>
                    <?= dash_columns($monthLabels, $monthValues, 'applications', 'Applications per month') ?>
                </div>
                </div>
                <?php endif; ?>

                <?php if (dash_card('vendor', 'chart_worker_type')): ?>
                <div class="[&>*]:h-full" style="order: <?= dash_card_pos('vendor', 'chart_worker_type') ?>">
                <div class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="mb-4 text-sm font-bold">Worker type</h3>
                    <?php
                    $seg = [];
                    foreach (array_slice($workerTypes, 0, 3) as $w) {
                        $seg[] = ['label' => $w['label'], 'value' => (int) $w['c']];
                    }
                    echo dash_stacked($seg, 'Applications by worker type');
                    ?>
                </div>
                </div>
                <?php endif; ?>

                <?php if (! $isVendorAccount): ?>
                <?php if (dash_card('vendor', 'chart_companies')): ?>
                <div class="[&>*]:h-full" style="order: <?= dash_card_pos('vendor', 'chart_companies') ?>">
                <div class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="mb-1 text-sm font-bold">Top vendor companies</h3>
                    <p class="mb-3 text-xs text-slate-500 dark:text-slate-400">By number of applications</p>
                    <?php
                    $ci = [];
                    foreach ($companies as $c) {
                        $ci[] = ['label' => $c['label'], 'value' => (int) $c['c'], 'href' => base_url('vendors') . '?search=' . rawurlencode($c['label']),
                                 'note' => (int) $c['pending'] . ' pending'];
                    }
                    echo dash_hbars($ci, 'applications', 'Applications by vendor company');
                    ?>
                </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </section>

            <section class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
                <?php
                $lists = [
                    [$isVendorAccount ? 'Needs your attention' : 'Waiting for approval', $needsAction, $isVendorAccount ? 'Nothing to fix or submit.' : 'No pending applications.', 'status', 'list_action'],
                    ['Passes expiring soon', $expiringList, 'No passes expire in the next 30 days.', 'expiry', 'list_expiring'],
                    ['Recent applications', $recent, 'No applications yet.', 'status', 'list_recent'],
                ];
                foreach ($lists as [$title, $data, $empty, $mode, $cardKey]):
                    if (! dash_card('vendor', $cardKey)) { continue; } ?>
                <div style="order: <?= dash_card_pos('vendor', $cardKey) ?>" class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="mb-3 text-sm font-bold"><?= esc($title) ?></h3>
                    <?php if (empty($data)): ?>
                        <p class="text-sm text-slate-500 dark:text-slate-400"><?= esc($empty) ?></p>
                    <?php else: ?>
                    <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php foreach ($data as $r): ?>
                        <li class="flex items-center justify-between gap-3 py-2">
                            <a class="min-w-0 truncate text-sm font-semibold hover:underline" href="<?= esc($viewUrl($r['id']), 'attr') ?>"><?= esc($r['full_name']) ?>
                                <span class="block truncate text-xs font-normal text-slate-500"><?= esc($r['app_no'] ?? '') ?><?= ! $isVendorAccount && ! empty($r['vendor_company_name']) ? ' · ' . esc($r['vendor_company_name']) : '' ?></span></a>
                            <?php if ($mode === 'expiry'): ?>
                                <span class="shrink-0 text-xs font-bold text-amber-700 dark:text-amber-400"><?= (int) $r['days_left'] === 0 ? 'Today' : (int) $r['days_left'] . ' d' ?></span>
                            <?php else: ?>
                                <?= dash_status_pill((string) ($r['status'] ?? '')) ?>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </section>
                </div>
            </details>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<?= view('partials/dashboard_viz_assets', ['part' => 'script']) ?>
<?php if (dash_card('vendor', 'assistant')): ?><?= view('partials/analytics_assistant', ['examples' => ['How many vendors are registered?', 'Which vendors are on-site right now?', 'Show vendor passes expiring this month.']]) ?><?php endif; ?>
</body>
</html>
