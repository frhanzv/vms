<?php
/**
 * Pagination bar. Expects $pagination (current_page, last_page, total, per_page)
 * and $baseUrl; keeps every other query-string parameter as it is.
 */
$curPage  = (int) ($pagination['current_page'] ?? 1);
$lastPage = (int) ($pagination['last_page'] ?? 1);
$pgTotal  = (int) ($pagination['total'] ?? 0);
$pgPer    = (int) ($pagination['per_page'] ?? 10);
$query    = $_GET;
$pageUrl  = function (int $pg) use ($query, $baseUrl): string {
    $q = $query;
    unset($q['page']);
    if ($pg > 1) {
        $q['page'] = $pg;
    }
    $qs = http_build_query($q);
    return $baseUrl . ($qs ? '?' . $qs : '');
};
$numbers = [];
if ($lastPage <= 7) {
    $numbers = range(1, $lastPage);
} else {
    $numbers[] = 1;
    if ($curPage > 3) $numbers[] = '...';
    for ($i = max(2, $curPage - 1); $i <= min($lastPage - 1, $curPage + 1); $i++) $numbers[] = $i;
    if ($curPage < $lastPage - 2) $numbers[] = '...';
    $numbers[] = $lastPage;
}
$first = $pgTotal === 0 ? 0 : ($curPage - 1) * $pgPer + 1;
$last  = min($curPage * $pgPer, $pgTotal);
$btn   = 'w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded';
?>
<div class="no-print flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-medium text-gray-500 dark:text-gray-400">
    <div class="flex items-center gap-1">
        <?php if ($curPage > 1): ?>
            <a href="<?= esc($pageUrl($curPage - 1), 'attr') ?>" class="<?= $btn ?> hover:bg-gray-50 dark:hover:bg-gray-800">«</a>
        <?php else: ?>
            <span class="<?= $btn ?> opacity-40 cursor-not-allowed">«</span>
        <?php endif; ?>
        <?php foreach ($numbers as $pn): ?>
            <?php if ($pn === '...'): ?>
                <span class="w-8 h-8 flex items-center justify-center">...</span>
            <?php elseif ($pn === $curPage): ?>
                <span class="w-8 h-8 flex items-center justify-center bg-primary text-white rounded shadow-sm"><?= $pn ?></span>
            <?php else: ?>
                <a href="<?= esc($pageUrl((int) $pn), 'attr') ?>" class="<?= $btn ?> hover:bg-gray-50 dark:hover:bg-gray-800"><?= $pn ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($curPage < $lastPage): ?>
            <a href="<?= esc($pageUrl($curPage + 1), 'attr') ?>" class="<?= $btn ?> hover:bg-gray-50 dark:hover:bg-gray-800">»</a>
        <?php else: ?>
            <span class="<?= $btn ?> opacity-40 cursor-not-allowed">»</span>
        <?php endif; ?>
    </div>
    <div class="flex items-center gap-3">
        <span class="text-gray-400">Showing <?= number_format($first) ?>–<?= number_format($last) ?> of <?= number_format($pgTotal) ?></span>
        <?php if (! empty($showPerPage)): ?>
        <select data-per-page class="appearance-none bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 py-1.5 pl-3 pr-8 rounded text-xs font-medium cursor-pointer">
            <?php foreach ([10, 25, 50] as $pp): ?>
                <option value="<?= $pp ?>" <?= $pgPer === $pp ? 'selected' : '' ?>><?= $pp ?> ITEMS PER PAGE</option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
    </div>
</div>
