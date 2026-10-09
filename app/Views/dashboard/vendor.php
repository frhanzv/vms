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
                <a href="<?= base_url('vendors/vendorpassrequest') ?>" class="flex h-10 items-center gap-2 rounded-lg bg-primary px-4 text-sm font-bold text-white shadow-sm transition-colors hover:bg-primary-dark">
                    <span class="material-symbols-outlined text-[20px]">add</span>New Request
                </a>
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

            <?= view('partials/dashboard_tabs', ['active' => 'vendor']) ?>

            <?php if (! dash_card_any('vendor')): ?>
                <p class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">No cards are switched on. Use <strong>Customize</strong> to show some.</p>
            <?php endif; ?>

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Key numbers">
                <?php if (dash_card('vendor', 'kpi_total')): ?>
                <?= dash_stat('Total applications', $total, 'All time', 'groups', 'info', $passUrl()) ?>
                <?php endif; ?>
                <?php if (dash_card('vendor', 'kpi_active')): ?>
                <?= dash_stat('Active passes', $activePasses, $expired . ' expired', 'badge', 'good', $passUrl('Approved')) ?>
                <?php endif; ?>
                <?php if (dash_card('vendor', 'kpi_expiring')): ?>
                <?= dash_stat('Expiring ≤ 30 days', $expiring, 'Active passes', 'event_upcoming', $expiring > 0 ? 'warn' : 'neutral') ?>
                <?php endif; ?>
                <?php if (dash_card('vendor', 'kpi_awaiting')): ?>
                <?= dash_stat($isVendorAccount ? 'Awaiting KPK' : 'Awaiting approval', $st['Pending'], $isVendorAccount ? ($st['Rejected'] . ' rejected · ' . $st['Draft'] . ' draft') : ($st['Rejected'] . ' rejected'), 'schedule', $st['Pending'] > 0 ? 'warn' : 'neutral', $passUrl('Pending')) ?>
                <?php endif; ?>
            </section>

            <section class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
                <?php if (dash_card('vendor', 'chart_pipeline')): ?>
                <div class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="mb-1 text-sm font-bold">Pass pipeline</h3>
                    <p class="mb-3 text-xs text-slate-500 dark:text-slate-400">Where every application is right now</p>
                    <?php
                    $stageLinks = $isVendorAccount ? [] : [
                        'Process'  => base_url('vendors/process-list'),
                        'Printing' => base_url('vendors/printing-list'),
                        'Issuance' => base_url('vendors/issuance-list'),
                        'Active'   => base_url('vendors/closed-list'),
                    ];
                    $tones = ['Draft' => 1, 'Pending' => 1, 'Rejected' => 1, 'Process' => 2, 'Printing' => 3, 'Issuance' => 4, 'Active' => 4, 'Closed' => 1];
                    $items = [];
                    foreach (\App\Libraries\VendorDashboardStats::STAGES as $s) {
                        $items[] = ['label' => $s, 'value' => $st[$s], 'tone' => $tones[$s], 'href' => $stageLinks[$s] ?? null];
                    }
                    echo dash_hbars($items, 'applications', 'Applications by pipeline stage');
                    ?>
                </div>
                <?php endif; ?>

                <?php if (dash_card('vendor', 'chart_monthly')): ?>
                <div class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="mb-1 text-sm font-bold">Applications per month</h3>
                    <p class="mb-2 text-xs text-slate-500 dark:text-slate-400">Last 6 months</p>
                    <?= dash_columns($monthLabels, $monthValues, 'applications', 'Applications per month') ?>
                </div>
                <?php endif; ?>

                <?php if (dash_card('vendor', 'chart_worker_type')): ?>
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
                <?php endif; ?>

                <?php if (! $isVendorAccount): ?>
                <?php if (dash_card('vendor', 'chart_companies')): ?>
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
                <div class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
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
            <?php endif; ?>
        </div>
    </main>
</div>
<?= view('partials/dashboard_viz_assets', ['part' => 'script']) ?>
</body>
</html>
