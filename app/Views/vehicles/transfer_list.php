<?php
echo view('vehicles/_head', ['pageTitle' => $pageTitle]);
$in = 'border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded px-3 py-2 text-sm';
?>
<div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-4">
        <h1 class="text-xl md:text-2xl font-bold tracking-tight uppercase">Transfer Ownership</h1>
        <a href="<?= base_url('vehicle-transfer/new') ?>" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-1.5 shadow self-start">
            <span class="material-icons text-[18px]">add</span> Transfer a Vehicle
        </a>
    </div>
    <form method="get" class="flex gap-2 mb-5">
        <input name="search" value="<?= esc($search) ?>" placeholder="Plate / Transfer No / Company" class="<?= $in ?> flex-1"/>
        <button class="bg-primary text-white rounded px-4 text-sm font-medium">Search</button>
    </form>
    <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700 mb-4">
        <table class="w-full min-w-max text-left text-xs">
            <thead><tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase tracking-wide">
                <?php foreach (['Transfer No', 'Date', 'Plate', 'From Company', 'To Company', 'Done By', ''] as $h): ?><th class="p-3 border-b dark:border-gray-600"><?= $h ?></th><?php endforeach; ?>
            </tr></thead>
            <tbody>
            <?php if (! $rows): ?><tr><td colspan="7" class="p-8 text-center text-gray-500">No ownership transfer found.</td></tr>
            <?php else: foreach ($rows as $r): ?>
                <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="p-3 font-medium"><?= esc($r['transfer_no']) ?></td>
                    <td class="p-3"><?= $r['created_at'] ? date('d/m/Y', strtotime($r['created_at'])) : '-' ?></td>
                    <td class="p-3 font-semibold"><?= esc($r['plate_no']) ?></td>
                    <td class="p-3"><?= esc($r['from_company'] ?: $r['from_owner_name']) ?></td>
                    <td class="p-3"><?= esc($r['to_company'] ?: $r['to_owner_name']) ?></td>
                    <td class="p-3"><?= esc($r['requested_by']) ?></td>
                    <td class="p-3"><a href="<?= base_url('vehicles/view/' . $r['vehicle_id']) ?>" class="text-primary font-semibold hover:underline">Vehicle</a></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <div class="flex items-center justify-between text-xs text-gray-500">
        <span><?= (int) $total ?> record(s)</span>
        <?php if ($pages > 1): $q = http_build_query(['search' => $search]); ?>
        <div class="flex gap-1"><?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?<?= $q ?>&page=<?= $i ?>" class="px-2.5 py-1 rounded <?= $i === $page ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-gray-700' ?>"><?= $i ?></a><?php endfor; ?></div>
        <?php endif; ?>
    </div>
</div>
<?= view('vehicles/_foot') ?>
