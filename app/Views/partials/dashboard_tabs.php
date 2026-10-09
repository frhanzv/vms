<?php
/**
 * Visitor | Staff | Vendor switcher. Only the dashboards this user may open are
 * shown, and nothing is rendered when there is just one (nothing to switch).
 * Usage: <?= view('partials/dashboard_tabs', ['active' => 'staff']) ?>
 */
helper('dashboard_nav');
$tabs   = dashboard_tabs();
$active = $active ?? '';
if (count($tabs) < 2) {
    return;
}
?>
<nav class="mb-6 inline-flex gap-1 rounded-xl border border-slate-200 bg-surface-light p-1 shadow-sm dark:border-slate-700 dark:bg-surface-dark" aria-label="Dashboards">
    <?php foreach ($tabs as $tab): $isActive = $tab['key'] === $active; ?>
    <a href="<?= esc($tab['url'], 'attr') ?>"
       class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors <?= $isActive ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' ?>"
       <?= $isActive ? 'aria-current="page"' : '' ?>>
        <span class="material-symbols-outlined text-[20px]"><?= esc($tab['icon']) ?></span>
        <?= esc($tab['label']) ?>
    </a>
    <?php endforeach; ?>
</nav>
