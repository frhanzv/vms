<?php
echo view('vehicles/_head', ['pageTitle' => $pageTitle]);
$badge = ['Draft' => 'bg-gray-100 text-gray-700', 'Pending' => 'bg-amber-100 text-amber-800', 'Approved' => 'bg-emerald-100 text-emerald-800', 'Rejected' => 'bg-red-100 text-red-800'];
$in = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm';
$slug = ['mine' => 'mine', 'request' => 'request-list', 'reject' => 'reject-list', 'closed' => 'list'];
?>
<div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-4">
        <h1 class="text-xl md:text-2xl font-bold tracking-tight uppercase"><?= esc($title) ?></h1>
        <?php if ($canCreate): ?>
        <a href="<?= base_url('vehicle-daily/request') ?>" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-1.5 shadow self-start"><span class="material-icons text-[18px]">add</span> Request</a>
        <?php endif; ?>
    </div>
    <div class="flex flex-wrap gap-2 mb-5 border-b border-gray-200 dark:border-gray-700 pb-3">
        <?php foreach ($tabs as $k => $label): ?><a href="<?= base_url('vehicle-daily/' . $slug[$k]) ?>" class="px-3 py-1.5 rounded-full text-xs font-semibold <?= $k === $stage ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200' ?>"><?= $label ?></a><?php endforeach; ?>
    </div>
    <form method="get" class="flex gap-2 mb-5">
        <input name="search" value="<?= esc($search) ?>" placeholder="Plate / Driver / Company / App No" class="<?= $in ?> flex-1"/>
        <button class="bg-primary text-white rounded px-4 text-sm font-medium">Filter</button>
    </form>
    <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-4">
        <table class="w-full min-w-max text-left text-xs">
            <thead><tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase tracking-wide">
                <?php foreach (['App No', 'Date', 'Plate', 'Driver', 'Company', 'Visit', 'Status', ''] as $h): ?><th class="p-3 border-b dark:border-gray-600"><?= $h ?></th><?php endforeach; ?>
            </tr></thead>
            <tbody>
            <?php if (! $rows): ?><tr><td colspan="8" class="p-8 text-center text-gray-500">No daily pass found.</td></tr>
            <?php else: foreach ($rows as $r): ?>
                <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="p-3 font-medium"><?= esc($r['app_no']) ?></td>
                    <td class="p-3"><?= $r['app_date'] ? date('d/m/Y', strtotime($r['app_date'])) : '-' ?></td>
                    <td class="p-3 font-semibold"><?= esc($r['plate_no']) ?></td>
                    <td class="p-3"><?= esc($r['driver_name'] ?: '-') ?></td>
                    <td class="p-3"><?= esc($r['company_name'] ?: '-') ?></td>
                    <td class="p-3"><?= $r['visit_from'] ? date('d/m/Y', strtotime($r['visit_from'])) : '-' ?> - <?= $r['visit_to'] ? date('d/m/Y', strtotime($r['visit_to'])) : '-' ?></td>
                    <td class="p-3"><span class="px-2 py-0.5 rounded-full font-semibold <?= $badge[$r['status']] ?? '' ?>"><?= esc($r['status']) ?></span></td>
                    <td class="p-3"><a href="<?= base_url('vehicle-daily/view/' . $r['id']) ?>" class="text-primary font-semibold hover:underline">Details</a></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <div class="flex items-center justify-between text-xs text-gray-500">
        <span><?= (int) $total ?> record(s)</span>
        <?php if ($pages > 1): $q = http_build_query(['search' => $search]); ?>
        <div class="flex gap-1"><?php for ($i = 1; $i <= $pages; $i++): ?><a href="?<?= $q ?>&page=<?= $i ?>" class="px-2.5 py-1 rounded <?= $i === $page ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-gray-700' ?>"><?= $i ?></a><?php endfor; ?></div>
        <?php endif; ?>
    </div>
</div>
<?= view('vehicles/_foot') ?>
