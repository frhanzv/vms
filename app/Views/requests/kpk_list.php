<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php
// KPK visitor-request-list: table + search / Sort By / items per page; a row opens the request details.
$input = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm';
$qs    = static fn(array $over = []) => base_url('requests') . '?' . http_build_query(array_filter(array_merge(
    ['q' => $q, 'sort' => $sort, 'per_page' => $perPage, 'view_client_id' => $viewClients ? $viewClientId : null], $over
), static fn($v) => $v !== null && $v !== ''));
?>
<div class="-m-2 space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold uppercase text-gray-800 dark:text-white">Request List</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Visitor requests waiting for approval</p>
        </div>
        <a href="<?= base_url('requests/queue') . ($viewClients ? '?view_client_id=' . (int) $viewClientId : '') ?>" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-1 hover:bg-gray-50 dark:hover:bg-gray-800">
            <span class="material-symbols-outlined text-[18px]">view_sidebar</span> Queue View
        </a>
    </div>

    <?php foreach (['success' => 'green', 'error' => 'red'] as $k => $c): if ($m = session()->getFlashdata($k)): ?>
        <div class="rounded-lg border border-<?= $c ?>-200 bg-<?= $c ?>-50 px-4 py-3 text-sm text-<?= $c ?>-700"><?= esc($m) ?></div>
    <?php endif; endforeach; ?>

    <form method="get" class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 flex flex-wrap items-end gap-3">
        <?php if ($viewClients): ?>
        <div><label class="block text-xs text-gray-500 mb-1">Client</label>
            <select name="view_client_id" class="<?= $input ?>"><?php foreach ($viewClients as $c): ?><option value="<?= (int) $c['id'] ?>" <?= (int) $c['id'] === (int) $viewClientId ? 'selected' : '' ?>><?= esc($c['name']) ?></option><?php endforeach; ?></select></div>
        <?php endif; ?>
        <div class="flex-1 min-w-[220px]"><label class="block text-xs text-gray-500 mb-1">Search</label>
            <div class="flex"><input name="q" value="<?= esc($q, 'attr') ?>" placeholder="IC / Passport / Full Name / Company" class="<?= $input ?> w-full rounded-r-none">
                <button class="px-4 rounded-r-lg bg-primary text-white"><span class="material-symbols-outlined text-[20px] align-middle">search</span></button></div></div>
        <div><label class="block text-xs text-gray-500 mb-1">Sort By</label>
            <select name="sort" onchange="this.form.submit()" class="<?= $input ?>"><option value="dt-d" <?= $sort === 'dt-d' ? 'selected' : '' ?>>Date Time DESC</option><option value="dt-a" <?= $sort === 'dt-a' ? 'selected' : '' ?>>Date Time ASC</option></select></div>
        <input type="hidden" name="per_page" value="<?= (int) $perPage ?>">
    </form>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                <tr><th class="p-3">No</th><th class="p-3">Date</th><th class="p-3">Full Name</th><th class="p-3">Status</th><th class="p-3">Company Visiting</th><th class="p-3">Reason</th></tr>
            </thead>
            <tbody>
            <?php if (! $rows): ?><tr><td colspan="6" class="p-8 text-center text-gray-500">No Record</td></tr><?php endif; ?>
            <?php foreach ($rows as $r): ?>
                <tr class="border-t border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 cursor-pointer" onclick="location.href='<?= base_url('requests/view/' . $r['id']) ?>'">
                    <td class="p-3"><?= $r['no'] ?></td>
                    <td class="p-3 whitespace-nowrap"><?= esc($r['date']) ?></td>
                    <td class="p-3"><a href="<?= base_url('requests/view/' . $r['id']) ?>" class="font-semibold text-primary hover:underline"><?= esc($r['name']) ?></a><?php if ($r['ic'] !== ''): ?><div class="text-xs text-gray-500"><?= esc($r['ic']) ?></div><?php endif; ?></td>
                    <td class="p-3"><span class="text-xs px-2 py-0.5 rounded font-semibold bg-amber-100 text-amber-700"><?= esc($r['status'] === 'Submitted' ? 'Pending' : $r['status']) ?></span></td>
                    <td class="p-3"><?= $r['visiting'] ? implode('<br>', array_map('esc', $r['visiting'])) : '-' ?></td>
                    <td class="p-3"><?= esc($r['reason']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-gray-100 dark:border-gray-800 text-sm">
            <form method="get" class="flex items-center gap-2">
                <?php foreach (['q' => $q, 'sort' => $sort] + ($viewClients ? ['view_client_id' => $viewClientId] : []) as $k => $v): ?><input type="hidden" name="<?= $k ?>" value="<?= esc((string) $v, 'attr') ?>"><?php endforeach; ?>
                <select name="per_page" onchange="this.form.submit()" class="<?= $input ?> py-1"><?php foreach ([10, 20, 30, 40] as $n): ?><option value="<?= $n ?>" <?= $perPage === $n ? 'selected' : '' ?>><?= $n ?> items per page</option><?php endforeach; ?></select>
                <span class="text-gray-500"><?= $total ?> record<?= $total === 1 ? '' : 's' ?></span>
            </form>
            <div class="flex items-center gap-1">
                <a href="<?= $page > 1 ? $qs(['page' => $page - 1]) : '#' ?>" class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 <?= $page > 1 ? '' : 'opacity-40 pointer-events-none' ?>">&lsaquo;</a>
                <span class="px-3 py-1">Page <?= $page ?> of <?= $pages ?></span>
                <a href="<?= $page < $pages ? $qs(['page' => $page + 1]) : '#' ?>" class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 <?= $page < $pages ? '' : 'opacity-40 pointer-events-none' ?>">&rsaquo;</a>
            </div>
        </div>
    </div>
</div>

<?= view('staffs/_layout_bottom') ?>
