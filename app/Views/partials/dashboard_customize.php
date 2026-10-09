<?php
/**
 * "Customize" popover for a dashboard — the user hides/shows cards for
 * themselves (only cards the client allows are listed).
 * Usage: <?= view('partials/dashboard_customize', ['dash' => 'staff']) ?>
 */
helper('dashboard_cards');
$def   = dash_card_registry()[$dash] ?? null;
$mine  = dash_card_choices('user', (int) session()->get('user_id'), $dash);
$byGrp = [];
foreach (($def['cards'] ?? []) as $key => $card) {
    if (dash_card_allowed($dash, $key)) {
        $byGrp[$card['group']][$key] = $card['label'];
    }
}
if (! $def || ! $byGrp) {
    return;
}
?>
<details class="relative">
    <summary class="flex h-10 cursor-pointer list-none items-center gap-1.5 rounded-lg border border-slate-200 bg-surface-light px-4 text-sm font-medium text-slate-600 shadow-sm hover:bg-slate-50 dark:border-slate-700 dark:bg-surface-dark dark:text-slate-300 dark:hover:bg-slate-800">
        <span class="material-symbols-outlined text-[20px]">dashboard_customize</span>
        <span class="hidden sm:inline">Customize</span>
    </summary>
    <form method="post" action="<?= base_url('dashboard/cards/mine') ?>" class="absolute right-0 z-30 mt-2 max-h-[70vh] w-80 overflow-y-auto rounded-xl border border-slate-200 bg-surface-light p-4 shadow-xl dark:border-slate-700 dark:bg-surface-dark">
        <?= csrf_field() ?>
        <input type="hidden" name="dashboard" value="<?= esc($dash, 'attr') ?>"/>
        <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">Cards on this dashboard</p>
        <?php foreach ($byGrp as $group => $items): ?>
        <p class="mb-1 mt-3 text-[11px] font-bold uppercase text-slate-400"><?= esc($group) ?></p>
        <?php foreach ($items as $key => $label): $id = 'mc_' . $dash . '_' . $key; ?>
        <label for="<?= esc($id, 'attr') ?>" class="flex cursor-pointer items-center gap-2.5 py-1.5 text-sm">
            <input id="<?= esc($id, 'attr') ?>" type="checkbox" name="cards[<?= esc($dash, 'attr') ?>][]" value="<?= esc($key, 'attr') ?>" <?= ($mine[$key] ?? true) ? 'checked' : '' ?> class="size-4 accent-primary"/>
            <?= esc($label) ?>
        </label>
        <?php endforeach; endforeach; ?>
        <div class="mt-4 flex items-center gap-2">
            <button type="submit" class="h-9 rounded-lg bg-primary px-4 text-sm font-semibold text-white">Save</button>
            <button type="submit" name="reset" value="1" class="h-9 rounded-lg px-3 text-sm font-medium text-slate-500 hover:underline">Show all</button>
        </div>
    </form>
</details>
