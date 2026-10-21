<?php
/**
 * Coloured summary cards (same look as the Invitation List).
 * $cards = [['label' => 'Pending', 'value' => 12, 'sub' => 'Awaiting approval', 'color' => 'orange', 'icon' => 'pending_actions'], ...]
 * colour: indigo | orange | blue | green | red
 */
$palette = [
    'indigo' => ['border-l-indigo-500', 'text-gray-400', 'bg-indigo-50 dark:bg-indigo-900/20', 'text-indigo-600 dark:text-indigo-400'],
    'orange' => ['border-l-orange-500', 'text-orange-500', 'bg-orange-50 dark:bg-orange-900/20', 'text-orange-600 dark:text-orange-400'],
    'blue'   => ['border-l-blue-500', 'text-blue-500', 'bg-blue-50 dark:bg-blue-900/20', 'text-blue-600 dark:text-blue-400'],
    'green'  => ['border-l-green-500', 'text-green-600', 'bg-green-50 dark:bg-green-900/20', 'text-green-600 dark:text-green-400'],
    'red'    => ['border-l-red-500', 'text-red-500', 'bg-red-50 dark:bg-red-900/20', 'text-red-600 dark:text-red-400'],
];
$n   = count($cards);
$cols = $n >= 5 ? 'md:grid-cols-5' : ($n === 4 ? 'md:grid-cols-4' : 'md:grid-cols-3');
?>
<div class="grid grid-cols-1 <?= $cols ?> gap-4 mb-8">
    <?php foreach ($cards as $c): [$bd, $subc, $icbg, $icfg] = $palette[$c['color']] ?? $palette['indigo']; ?>
    <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border-l-4 <?= $bd ?> shadow-sm border-t border-r border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide"><?= esc($c['label']) ?></p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><?= number_format((int) $c['value']) ?></p>
            <p class="text-[10px] <?= $subc ?> mt-1 font-medium"><?= esc($c['sub'] ?? '') ?></p>
        </div>
        <div class="p-3 <?= $icbg ?> rounded-full <?= $icfg ?>">
            <span class="material-symbols-outlined text-2xl"><?= esc($c['icon'] ?? 'info') ?></span>
        </div>
    </div>
    <?php endforeach; ?>
</div>
