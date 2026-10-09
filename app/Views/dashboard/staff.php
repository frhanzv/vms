<?php
/**
 * Staff dashboard. Data comes from App\Libraries\StaffDashboardStats via
 * Controllers\DashboardStaff.
 */
helper(['dashboard_chart', 'dashboard_cards']);
$staffUrl = static fn(string $search = ''): string => base_url('staffs') . ($search !== '' ? '?search=' . rawurlencode($search) : '');
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
                <h2 class="text-lg font-bold leading-tight text-slate-900 dark:text-white">Staff Dashboard</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Today, <?= esc($currentDate) ?></p>
            </div>
            <div class="flex items-center gap-3">
            <?= view('partials/dashboard_customize', ['dash' => 'staff']) ?>
            <?php if (! empty($canAddStaff)): ?>
            <a href="<?= base_url('staffs/staffpassrequest') ?>" class="flex h-10 items-center gap-2 rounded-lg bg-primary px-4 text-sm font-bold text-white shadow-sm transition-colors hover:bg-primary-dark">
                <span class="material-symbols-outlined text-[20px]">add</span>New Staff Pass
            </a>
            <?php endif; ?>
            </div>
        </header>

        <div class="viz-root flex-1 overflow-y-auto p-8">
            <?php if ($msg = session()->getFlashdata('error')): ?>
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><?= esc($msg) ?></div>
            <?php endif; ?>

            <?= view('partials/dashboard_tabs', ['active' => 'staff']) ?>

            <?php if (! dash_card_any('staff')): ?>
                <p class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">No cards are switched on. Use <strong>Customize</strong> to show some.</p>
            <?php endif; ?>

            <!-- KPIs -->
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Key numbers">
                <?php if (dash_card('staff', 'kpi_total')): ?>
                <?= dash_stat('Total staff', $total, $active . ' active', 'badge', 'info', $staffUrl()) ?>
                <?php endif; ?>
                <?php if (dash_card('staff', 'kpi_cards_active')): ?>
                <?= dash_stat('Active pass cards', $cardsActive, $withoutCard . ' staff without a card', 'credit_card', 'good') ?>
                <?php endif; ?>
                <?php if (dash_card('staff', 'kpi_cards_expiring')): ?>
                <?= dash_stat('Cards expiring ≤ 30 days', $cardsExpiring, $cardsExpired . ' already expired', 'event_upcoming', $cardsExpiring > 0 ? 'warn' : 'neutral') ?>
                <?php endif; ?>
                <?php if (dash_card('staff', 'kpi_docs_expiring')): ?>
                <?= dash_stat('Documents expiring ≤ 30 days', $docsExpiring, 'Licences, permits and similar', 'description', $docsExpiring > 0 ? 'warn' : 'neutral') ?>
                <?php endif; ?>
            </section>

            <!-- Charts -->
            <section class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
                <?php if (dash_card('staff', 'chart_status')): ?>
                <div class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="mb-4 text-sm font-bold">Staff by status</h3>
                    <?= dash_stacked([
                        ['label' => 'Active',    'value' => $active],
                        ['label' => 'Suspended', 'value' => $suspended],
                        ['label' => 'Inactive / other', 'value' => $inactive],
                    ], 'Staff by status') ?>
                </div>
                <?php endif; ?>

                <?php if (dash_card('staff', 'chart_card_health')): ?>
                <div class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="mb-4 text-sm font-bold">Pass card health</h3>
                    <?= dash_hbars([
                        ['label' => 'Active cards',        'value' => $cardsActive],
                        ['label' => 'Expiring ≤ 30 days',  'value' => $cardsExpiring],
                        ['label' => 'Expired',             'value' => $cardsExpired],
                        ['label' => 'Staff without card',  'value' => $withoutCard],
                    ], 'staff', 'Pass card health') ?>
                </div>
                <?php endif; ?>

                <?php if (dash_card('staff', 'chart_monthly')): ?>
                <div class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="mb-1 text-sm font-bold">New staff per month</h3>
                    <p class="mb-2 text-xs text-slate-500 dark:text-slate-400">Last 6 months</p>
                    <?= dash_columns($monthLabels, $monthValues, 'new staff', 'New staff per month') ?>
                </div>
                <?php endif; ?>

                <?php if (dash_card('staff', 'chart_departments')): ?>
                <div class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="mb-4 text-sm font-bold">Top departments</h3>
                    <?php
                    $deptItems = [];
                    foreach ($departments as $d) {
                        $deptItems[] = ['label' => $d['label'], 'value' => (int) $d['c']];
                    }
                    echo dash_hbars($deptItems, 'staff', 'Staff by department');
                    ?>
                </div>
                <?php endif; ?>
            </section>

            <!-- Tables -->
            <section class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
                <?php if (dash_card('staff', 'list_attention')): ?>
                <div class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="mb-3 text-sm font-bold">Needs attention</h3>
                    <?php if (empty($needsAttention)): ?>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Nobody needs attention.</p>
                    <?php else: ?>
                    <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php foreach ($needsAttention as $r): ?>
                        <li class="flex items-center justify-between gap-3 py-2">
                            <a class="min-w-0 truncate text-sm font-semibold hover:underline" href="<?= esc($staffUrl((string) $r['staff_no']), 'attr') ?>"><?= esc($r['full_name']) ?>
                                <span class="block truncate text-xs font-normal text-slate-500"><?= esc($r['staff_no']) ?><?= ! empty($r['next_action']) ? ' · ' . esc($r['next_action']) : '' ?></span></a>
                            <?= dash_status_pill((string) $r['status']) ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (dash_card('staff', 'list_expiring')): ?>
                <div class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="mb-3 text-sm font-bold">Cards expiring soon</h3>
                    <?php if (empty($expiringCards)): ?>
                        <p class="text-sm text-slate-500 dark:text-slate-400">No cards expire in the next 30 days.</p>
                    <?php else: ?>
                    <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php foreach ($expiringCards as $r): ?>
                        <li class="flex items-center justify-between gap-3 py-2">
                            <a class="min-w-0 truncate text-sm font-semibold hover:underline" href="<?= esc($staffUrl((string) $r['staff_no']), 'attr') ?>"><?= esc($r['full_name']) ?>
                                <span class="block truncate text-xs font-normal text-slate-500"><?= esc($r['staff_no']) ?> · <?= esc($r['expiry_date']) ?></span></a>
                            <span class="shrink-0 text-xs font-bold text-amber-700 dark:text-amber-400"><?= (int) $r['days_left'] === 0 ? 'Today' : (int) $r['days_left'] . ' d' ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (dash_card('staff', 'list_recent')): ?>
                <div class="rounded-xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="mb-3 text-sm font-bold">Recent registrations</h3>
                    <?php if (empty($recent)): ?>
                        <p class="text-sm text-slate-500 dark:text-slate-400">No staff yet.</p>
                    <?php else: ?>
                    <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php foreach ($recent as $r): ?>
                        <li class="flex items-center justify-between gap-3 py-2">
                            <a class="min-w-0 truncate text-sm font-semibold hover:underline" href="<?= esc($staffUrl((string) $r['staff_no']), 'attr') ?>"><?= esc($r['full_name']) ?>
                                <span class="block truncate text-xs font-normal text-slate-500"><?= esc($r['staff_no']) ?><?= ! empty($r['department']) ? ' · ' . esc($r['department']) : '' ?></span></a>
                            <?= dash_status_pill((string) ($r['status'] ?? '')) ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
</div>
<?= view('partials/dashboard_viz_assets', ['part' => 'script']) ?>
</body>
</html>
