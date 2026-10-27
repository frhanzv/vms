<?php
/** Vehicle dashboard. Data comes from App\Libraries\VehicleDashboardStats via Controllers\DashboardVehicle. */
helper(['dashboard_chart', 'dashboard_cards']);
$reg   = $registered['staff'] + $registered['vendor'];
$gates = $gate['in'] + $gate['out'];
$pages = max(1, (int) ceil($total / max(1, $perPage)));
$qs    = static fn(array $o): string => base_url('dashboard/vehicle') . '?' . http_build_query(array_merge(['per_page' => $perPage], ($search ?? '') !== '' ? ['search' => $search] : [], $o));
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
                <h2 class="text-lg font-bold leading-tight text-slate-900 dark:text-white">Vehicle Dashboard</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Today, <?= esc($currentDate) ?></p>
            </div>
            <?= view('partials/dashboard_customize', ['dash' => 'vehicle']) ?>
        </header>

        
        <div class="viz-root flex-1 overflow-y-auto p-8">
            <?php if ($msg = session()->getFlashdata('error')): ?>
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><?= esc($msg) ?></div>
            <?php endif; ?>

            <?php if (! dash_card_any('vehicle')): ?>
                <p class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">No cards are switched on. Use <strong>Customize</strong> to show some.</p>
            <?php endif; ?>

            <div class="mx-auto flex max-w-7xl flex-col gap-6">

            <!-- Today -->
            <?php if (dash_card('vehicle', 'kpi_today')): ?>
            <section style="order: <?= dash_card_pos('vehicle', 'kpi_today') ?>" aria-label="Registered vehicles and gate activity today" class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <?= dash_stat('Staff Vehicle Registered', $registered['staff'], 'today', 'badge', 'info', null, 'reg_staff') ?>
                <?= dash_stat('Vendor Pass Vehicle Registered', $registered['vendor'], 'today', 'local_shipping', 'info', null, 'reg_vendor') ?>
                <?= dash_stat('Gate In', $gate['in'], 'vehicles today', 'login', 'good', null, 'gate_in') ?>
                <?= dash_stat('Gate Out', $gate['out'], 'vehicles today', 'logout', 'neutral', null, 'gate_out') ?>
            </section>
            <?php endif; ?>

            <?php if (dash_card('vehicle', 'chart_share')): ?>
            <section style="order: <?= dash_card_pos('vehicle', 'chart_share') ?>" class="rounded-2xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                <h3 class="mb-1 text-sm font-bold uppercase tracking-wide">Total of Registered Vehicle And Gate In Out</h3>
                <p class="mb-3 text-xs text-slate-500">Share of today's registrations and of today's gate movements.</p>
                <div class="grid gap-6 md:grid-cols-2">
                    <div><p class="mb-2 text-xs font-semibold text-slate-500">Registered today</p>
                        <?= dash_stacked([['label' => 'Staff', 'value' => $registered['staff']], ['label' => 'Vendor Pass', 'value' => $registered['vendor']]], 'Registered vehicles today') ?>
                        <?php if ($reg === 0): ?><p class="text-sm text-slate-500">No vehicles registered today.</p><?php endif; ?></div>
                    <div><p class="mb-2 text-xs font-semibold text-slate-500">Gate movements today</p>
                        <?= dash_stacked([['label' => 'Gate In', 'value' => $gate['in']], ['label' => 'Gate Out', 'value' => $gate['out']]], 'Gate in and out today') ?>
                        <?php if ($gates === 0): ?><p class="text-sm text-slate-500">No vehicle gate movements today.</p><?php endif; ?></div>
                </div>
            </section>
            <?php endif; ?>

            <!-- Entering by month -->
            <?php if (dash_card('vehicle', 'chart_monthly')): ?>
            <section style="order: <?= dash_card_pos('vehicle', 'chart_monthly') ?>" class="rounded-2xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wide">Vehicle Entering</h3>
                <div class="relative h-72"><canvas id="chMonth" role="img" aria-label="Vehicles entering per month, staff and vendor pass"></canvas></div>
                <details class="mt-3 text-xs text-slate-500"><summary class="cursor-pointer">Table view</summary>
                    <table class="mt-2 w-full text-left"><thead><tr><th class="p-1">Month</th><th class="p-1">Staff Vehicle</th><th class="p-1">Vendor Pass Vehicle</th></tr></thead><tbody>
                    <?php foreach ($months as $m): ?><tr><td class="p-1"><?= esc($m['month']) ?></td><td class="p-1"><?= (int) $m['staff'] ?></td><td class="p-1"><?= (int) $m['vendor'] ?></td></tr><?php endforeach; ?>
                    </tbody></table></details>
            </section>
            <?php endif; ?>

            <!-- Duration -->
            <?php if (dash_card('vehicle', 'list_duration')): ?>
            <section style="order: <?= dash_card_pos('vehicle', 'list_duration') ?>" class="rounded-2xl border border-slate-200 bg-surface-light p-5 shadow-sm dark:border-slate-700 dark:bg-surface-dark">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-sm font-bold uppercase tracking-wide">Vehicle Duration</h3>
                    <form method="get" action="<?= esc(base_url('dashboard/vehicle'), 'attr') ?>" class="flex items-center gap-2">
                        <input type="hidden" name="per_page" value="<?= (int) $perPage ?>">
                        <input type="text" name="search" value="<?= esc($search ?? '', 'attr') ?>" maxlength="60" placeholder="Search vehicle no., type, driver type"
                               class="h-9 w-64 rounded border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-800">
                        <button type="submit" class="h-9 rounded bg-primary px-3 text-sm font-semibold text-white hover:opacity-90">Search</button>
                        <?php if (($search ?? '') !== ''): ?><a href="<?= esc(base_url('dashboard/vehicle') . '?per_page=' . (int) $perPage, 'attr') ?>" class="h-9 rounded border px-3 text-sm leading-9 hover:bg-slate-100 dark:hover:bg-slate-800">Clear</a><?php endif; ?>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead><tr class="border-b border-slate-200 text-xs font-bold uppercase dark:border-slate-700">
                            <th class="p-3">No</th><th class="p-3">Vehicle Number</th><th class="p-3">Type of Vehicle</th><th class="p-3">Driver Type</th>
                            <th class="p-3">Time In</th><th class="p-3">Time Out</th><th class="p-3">Duration of Stay (hr)</th></tr></thead>
                        <tbody>
                        <?php if (! $rows): ?><tr><td colspan="7" class="p-6 text-center text-slate-500"><?= ($search ?? '') !== '' ? 'No vehicle visits match your search.' : 'No completed vehicle visits yet.' ?></td></tr><?php endif; ?>
                        <?php foreach ($rows as $i => $r): ?>
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <td class="p-3"><?= ($page - 1) * $perPage + $i + 1 ?></td>
                            <td class="p-3 font-semibold"><?= esc($r['plate_no']) ?></td>
                            <td class="p-3"><?= esc($r['vehicle_type'] ?? '-') ?: '-' ?></td>
                            <td class="p-3"><?= $r['pass_type'] === 'VENDORPASS' ? 'VENDOR PASS' : esc($r['pass_type']) ?></td>
                            <td class="p-3"><?= esc(date('d-m-Y H:i', strtotime($r['time_in']))) ?></td>
                            <td class="p-3"><?= esc(date('d-m-Y H:i', strtotime($r['time_out']))) ?></td>
                            <td class="p-3"><?= esc((string) $r['duration']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm">
                    <div class="flex items-center gap-1">
                        <?php if ($page > 1): ?><a class="rounded border px-3 py-1 hover:bg-slate-100 dark:hover:bg-slate-800" href="<?= esc($qs(['page' => $page - 1]), 'attr') ?>">Prev</a><?php endif; ?>
                        <span class="px-2">Page <?= (int) $page ?> of <?= (int) $pages ?> &middot; <?= (int) $total ?> records</span>
                        <?php if ($page < $pages): ?><a class="rounded border px-3 py-1 hover:bg-slate-100 dark:hover:bg-slate-800" href="<?= esc($qs(['page' => $page + 1]), 'attr') ?>">Next</a><?php endif; ?>
                    </div>
                    <form method="get"><input type="hidden" name="search" value="<?= esc($search ?? '', 'attr') ?>"><select name="per_page" onchange="this.form.submit()" class="h-9 rounded border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-800">
                        <?php foreach ([10, 20, 30, 40] as $n): ?><option value="<?= $n ?>" <?= $perPage === $n ? 'selected' : '' ?>><?= $n ?> items per page</option><?php endforeach; ?>
                    </select></form>
                </div>
            </section>
            <?php endif; ?>

            </div>
        </div>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    const data = <?= json_encode($months) ?>;
    const css = getComputedStyle(document.querySelector('.viz-root'));
    const c1 = css.getPropertyValue('--v-s1').trim(), c2 = css.getPropertyValue('--v-s2').trim(), grid = css.getPropertyValue('--v-grid').trim(), ink = css.getPropertyValue('--v-ink2').trim();
    const ds = (label, key, color, dash) => ({ label, data: data.map(d => d[key]), borderColor: color, backgroundColor: color, borderWidth: 2, borderDash: dash, pointRadius: 4, pointBackgroundColor: color, tension: 0 });
    const cv = document.getElementById('chMonth');
    if (!cv) { return; }
    new Chart(cv, {
        type: 'line',
        data: { labels: data.map(d => d.month), datasets: [ds('Staff Vehicle', 'staff', c1, []), ds('Vendor Pass Vehicle', 'vendor', c2, [6, 4])] },
        options: {
            responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top', align: 'end', labels: { color: ink, usePointStyle: true } } },
            scales: {
                x: { grid: { color: grid }, ticks: { color: ink }, title: { display: true, text: 'Month', color: ink } },
                y: { beginAtZero: true, grid: { color: grid }, ticks: { color: ink, precision: 0 }, title: { display: true, text: 'Number of Vehicle', color: ink } }
            }
        }
    });
})();
</script>
<?= view('partials/dashboard_viz_assets', ['part' => 'script']) ?>
<?= view('partials/dash_detail_modal', ['dash' => 'vehicle']) ?>
<?php if (dash_card('vehicle', 'assistant')): ?><?= view('partials/analytics_assistant', ['examples' => ['How many vehicles are registered?', 'Which vehicles are currently inside?', 'Show vehicles with expired road tax.']]) ?><?php endif; ?>
</body>
</html>
