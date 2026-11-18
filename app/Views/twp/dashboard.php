<?php
/**
 * KPK dashboard-twp, in the VMS dashboard look (same page frame, tiles and charts as the Vendor dashboard).
 * Data: Controllers\Twp::dashboard().
 */
helper(['dashboard_chart']);
$dt      = static fn($v) => $v ? date('d M Y, H:i', strtotime((string) $v)) : '-';
$report  = static fn(string $tab = '') => base_url('twp/report') . '?' . http_build_query(array_filter(['tab' => $tab, 'date_from' => $from, 'date_to' => $to]));
$inPrem  = (int) ($sum['in_premise'] ?? 0);
$near    = (int) ($sum['near_window'] ?? 0);
$oow     = (int) ($sum['out_window'] ?? 0);
$period  = $from === $to ? date('d M Y', strtotime($from)) : date('d M Y', strtotime($from)) . ' → ' . date('d M Y', strtotime($to));
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
                <h2 class="text-lg font-bold leading-tight text-slate-900 dark:text-white">Temporary Work Pass Dashboard</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Today, <?= esc(date('l, d F Y')) ?></p>
            </div>
            <?php helper('access'); if (has_access('twp', 'request_list')): ?>
            <a href="<?= base_url('twp/requests') ?>" class="flex h-10 items-center gap-2 rounded-lg bg-primary px-4 text-sm font-bold text-white shadow-sm transition-colors hover:bg-primary-dark">
                <span class="material-symbols-outlined text-[20px]">list_alt</span>Request List
            </a>
            <?php endif; ?>
        </header>

        <div class="viz-root flex-1 overflow-y-auto p-8">
            <div class="mx-auto flex max-w-7xl flex-col gap-6">

            <!-- Filters -->
            <form method="get" class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-surface-light px-4 py-3 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                <span class="material-symbols-outlined text-[20px] text-slate-400">tune</span>
                <input type="date" name="date_from" aria-label="From" value="<?= esc($from, 'attr') ?>" class="h-9 rounded-full border-slate-200 bg-slate-50 px-4 text-sm dark:border-slate-600 dark:bg-slate-800"/>
                <span class="text-xs text-slate-400">to</span>
                <input type="date" name="date_to" aria-label="To" value="<?= esc($to, 'attr') ?>" class="h-9 rounded-full border-slate-200 bg-slate-50 px-4 text-sm dark:border-slate-600 dark:bg-slate-800"/>
                <button type="submit" class="h-9 rounded-full bg-primary px-5 text-sm font-bold text-white hover:bg-primary-dark">Update Now</button>
                <a href="<?= base_url('twp/dashboard') ?>" class="px-2 text-sm font-semibold text-slate-500 hover:text-primary">Reset</a>
                <span class="ml-auto text-xs text-slate-400"><?= esc($period) ?> &middot; As at <?= esc(date('H:i')) ?></span>
            </form>

            <!-- Movement in the period -->
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <?= dash_stat('Total Check In', (int) ($sum['checked_in'] ?? 0), 'Workers who entered', 'login', 'good', $report()) ?>
                <?= dash_stat('Total Check Out', (int) ($sum['checked_out'] ?? 0), 'Workers who left', 'logout', 'info', $report()) ?>
                <?= dash_stat('Denied Entry', (int) ($sum['denied'] ?? 0), 'Taps refused at the gate', 'block', 'bad') ?>
                <?= dash_stat('Average Time In Location', $sum['avg_hours'] !== null ? $sum['avg_hours'] . ' hrs' : '-', 'Of workers who checked out', 'timer', 'neutral') ?>
            </section>

            <!-- Right now -->
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <?= dash_stat('In Premise', $inPrem, 'Inside right now', 'groups', 'info', $report('premise')) ?>
                <?= dash_stat('Near Out Of Window', $near, 'Pass ends within ' . \App\Libraries\TwpGate::NEAR_MINUTES . ' min', 'schedule', 'warn', $report('premise')) ?>
                <?= dash_stat('Out Of Window', $oow, 'Still inside after the pass ended', 'warning', 'bad', $report('oow')) ?>
            </section>

            <!-- Charts -->
            <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-surface-light p-6 shadow-sm dark:border-slate-700 dark:bg-surface-dark lg:col-span-2">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Check In <?= $oneDay ? 'By Hour' : 'By Day' ?></h3>
                    <p class="mb-4 text-xs text-slate-500 dark:text-slate-400"><?= esc($period) ?></p>
                    <?= dash_columns($trend['labels'], $trend['values'], 'check-ins', 'Temporary work pass check-ins ' . ($oneDay ? 'by hour' : 'by day')) ?>
                </div>
                <div class="rounded-xl border border-slate-200 bg-surface-light p-6 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">In Premise Status</h3>
                    <p class="mb-4 text-xs text-slate-500 dark:text-slate-400"><?= $inPrem ?> worker<?= $inPrem === 1 ? '' : 's' ?> inside now</p>
                    <?php
                    // fixed colours per status (the shared stacked bar colours by position, so a lone red status would show blue)
                    $parts = [['Within pass time', max(0, $inPrem - $near - $oow), 'bg-emerald-500'], ['Near out of window', $near, 'bg-amber-500'], ['Out of window', $oow, 'bg-red-500']];
                    ?>
                    <?php if ($inPrem === 0): ?>
                        <p class="text-sm text-slate-500 dark:text-slate-400">No workers inside right now.</p>
                    <?php else: ?>
                        <div class="flex h-3 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700" role="img" aria-label="Workers inside by pass status">
                            <?php foreach ($parts as [$l, $n, $c]): if ($n > 0): ?><div class="<?= $c ?>" style="width: <?= round($n / $inPrem * 100, 1) ?>%" title="<?= esc($l, 'attr') ?>: <?= $n ?>"></div><?php endif; endforeach; ?>
                        </div>
                        <ul class="mt-5 space-y-3">
                            <?php foreach ($parts as [$l, $n, $c]): ?>
                            <li class="flex items-center gap-2 text-sm">
                                <span class="size-2.5 shrink-0 rounded-full <?= $c ?>"></span>
                                <span class="flex-1 text-slate-600 dark:text-slate-300"><?= esc($l) ?></span>
                                <strong class="text-slate-900 dark:text-white"><?= $n ?></strong>
                                <span class="w-10 text-right text-xs text-slate-400"><?= round($n / $inPrem * 100) ?>%</span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- By location -->
                <div class="rounded-xl border border-slate-200 bg-surface-light p-6 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">By Location</h3>
                    <p class="mb-4 text-xs text-slate-500 dark:text-slate-400">Workers inside now, per location</p>
                    <?= dash_hbars(array_map(static fn($l) => [
                        'label' => $l['location'] ?: 'No location', 'value' => (int) $l['in_premise'],
                        'note'  => (int) $l['checked_in'] . ' in · ' . (int) $l['checked_out'] . ' out · ' . (int) $l['out_window'] . ' out of window',
                    ], $byLoc), 'inside', 'Workers inside per location') ?>
                </div>

                <!-- Currently in premise -->
                <div class="flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-surface-light shadow-sm dark:border-slate-700 dark:bg-surface-dark lg:col-span-2">
                    <div class="flex items-center justify-between px-6 pt-6 pb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Currently In Premise</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Newest check-in first</p>
                        </div>
                        <a href="<?= esc($report('premise'), 'attr') ?>" class="text-sm font-semibold text-primary hover:underline">View all</a>
                    </div>
                    <?php if (! $inside): ?>
                        <div class="flex flex-col items-center justify-center px-6 pb-10 pt-4 text-center">
                            <span class="material-symbols-outlined mb-2 text-4xl text-slate-300">person_off</span>
                            <p class="text-sm text-slate-500 dark:text-slate-400">No temporary workers inside right now.</p>
                        </div>
                    <?php else: ?>
                    <div class="max-h-[26rem] overflow-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="sticky top-0 bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                <tr><th class="px-6 py-3 font-semibold">Worker</th><th class="px-4 py-3 font-semibold">Location</th><th class="px-4 py-3 font-semibold">Time In / Pass Ends</th><th class="px-6 py-3 font-semibold">Status</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <?php foreach ($inside as $x):
                                $end    = $x['date_to'] ? strtotime((string) $x['date_to']) : null;
                                $isOow  = $end !== null && $end < time();
                                $isNear = ! $isOow && $end !== null && $end < time() + \App\Libraries\TwpGate::NEAR_MINUTES * 60;
                                $init   = strtoupper(implode('', array_map(static fn($w) => mb_substr($w, 0, 1), array_slice(preg_split('/\s+/', trim((string) $x['name'])), 0, 2))));
                            ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary"><?= esc($init ?: '?') ?></span>
                                            <div class="min-w-0">
                                                <p class="truncate font-semibold text-slate-900 dark:text-white"><?= esc($x['name']) ?></p>
                                                <p class="truncate text-xs text-slate-500 dark:text-slate-400"><?= esc($x['ic']) ?> · <?= esc($x['company_name']) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-slate-700 dark:text-slate-200"><?= esc($x['location'] ?: '-') ?></p>
                                        <p class="max-w-[12rem] truncate text-xs text-slate-500 dark:text-slate-400" title="<?= esc($x['in_lane'], 'attr') ?>"><?= esc($x['in_lane'] ?: '-') ?></p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <p class="text-slate-700 dark:text-slate-200"><?= $dt($x['check_in_time']) ?></p>
                                        <p class="text-xs <?= $isOow ? 'font-semibold text-red-600' : 'text-slate-500 dark:text-slate-400' ?>">Ends <?= $dt($x['date_to']) ?></p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3">
                                        <?php if ($isOow): ?>
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-700 dark:text-slate-200"><span class="material-symbols-outlined text-[16px] text-red-600">warning</span>Out of window</span>
                                        <?php elseif ($isNear): ?>
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-700 dark:text-slate-200"><span class="material-symbols-outlined text-[16px] text-amber-600">schedule</span>Near out of window</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-700 dark:text-slate-200"><span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>Within pass time</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            </div>
        </div>
    </main>
</div>
<?= view('partials/dashboard_viz_assets', ['part' => 'script']) ?>
</body>
</html>
