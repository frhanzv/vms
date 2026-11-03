<?php
/**
 * TWP page header, same look as the Vendor List: title, Show/Hide Columns (when $listKey is a
 * list_column_registry() list), the page's own buttons ($actions HTML), the summary cards ($cards) and flash.
 */
helper(['list_columns', 'feature']);
$listKey ??= null;
$cols    = $listKey ? (list_column_registry()[$listKey]['columns'] ?? []) : [];
?>
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <h1 class="text-xl md:text-2xl font-bold tracking-tight text-gray-800 dark:text-white uppercase"><?= esc($title) ?></h1>
    <div class="flex flex-wrap gap-2">
        <?php if ($cols): ?>
            <button type="button" onclick="document.getElementById('twpColumnsModal').classList.remove('hidden')"
                class="bg-[#535dec] hover:bg-[#4853e0] text-white px-4 py-2 rounded text-sm font-medium flex items-center shadow transition-colors">
                <span class="material-icons text-sm mr-1">visibility</span> Show/Hide Columns
            </button>
        <?php endif; ?>
        <?= $actions ?? '' ?>
    </div>
</div>
<?php if (! empty($cards)): ?><?= view('partials/stat_cards', ['cards' => $cards]) ?><?php endif; ?>
<?= view('report_templates/_flash') ?>

<?php if ($cols): ?>
<div id="twpColumnsModal" class="hidden fixed inset-0 z-[120] flex items-center justify-center p-4">
    <div onclick="document.getElementById('twpColumnsModal').classList.add('hidden')" class="absolute inset-0 bg-slate-900/55 dark:bg-black/65 cursor-pointer"></div>
    <form method="POST" action="<?= base_url('twp/columns') ?>" class="relative flex w-full max-w-[600px] flex-col rounded-xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
        <?= csrf_field() ?>
        <input type="hidden" name="list" value="<?= esc($listKey) ?>"/>
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-700">
            <h2 class="text-lg font-bold tracking-tight text-[#3b5998] dark:text-white">Show/Hide Columns</h2>
            <button type="button" onclick="document.getElementById('twpColumnsModal').classList.add('hidden')" class="text-slate-300 hover:text-slate-500"><span class="material-icons text-[20px]">close</span></button>
        </div>
        <div class="px-6 py-5 overflow-y-auto max-h-[60vh]">
            <div class="mb-5 flex items-center gap-2">
                <input type="checkbox" id="selectAllTwpColumns" onchange="document.querySelectorAll('.twp-column-toggle:not(:disabled)').forEach(c => c.checked = this.checked)" class="rounded border-slate-300 text-[#535dec] focus:ring-[#535dec] h-4 w-4 cursor-pointer">
                <label for="selectAllTwpColumns" class="text-sm font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Select All Columns</label>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-4">
                <?php foreach ($cols as $ckey => $cdef): $locked = ! empty($cdef['locked']); ?>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="twp_col_<?= esc($ckey) ?>" <?= $locked ? 'disabled' : 'name="columns[' . esc($ckey) . ']" value="1"' ?>
                           class="twp-column-toggle rounded border-slate-300 text-[#535dec] focus:ring-[#535dec] h-4 w-4 <?= $locked ? 'opacity-60' : 'cursor-pointer' ?>" <?= list_col($listKey, $ckey) ? 'checked' : '' ?>>
                    <label for="twp_col_<?= esc($ckey) ?>" class="text-sm text-slate-600 dark:text-slate-300 uppercase <?= $locked ? '' : 'cursor-pointer' ?>">
                        <?= esc($cdef['label']) ?><?= $locked ? ' <span class="normal-case text-xs text-slate-400">(always shown)</span>' : '' ?>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="flex justify-end gap-3 border-t-2 border-slate-100 px-6 py-4 dark:border-slate-700 rounded-b-xl">
            <button type="button" onclick="document.getElementById('twpColumnsModal').classList.add('hidden')" class="rounded-md border border-slate-400 bg-slate-500 hover:bg-slate-600 px-5 py-2 text-sm font-semibold text-white">Close</button>
            <button type="submit" class="rounded-md bg-[#535dec] hover:bg-[#4853e0] px-5 py-2 text-sm font-semibold text-white shadow-md">Apply Changes</button>
        </div>
    </form>
</div>
<?php endif; ?>
