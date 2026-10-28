<?php
$tabs = ['twp/requests' => 'Request List', 'twp/passes' => 'Pass List', 'twp/dashboard' => 'Dashboard', 'twp/report' => 'Reports', 'twp/cards' => 'TWP Card'];
$here = trim(service('uri')->getPath(), '/');
?>
<div class="flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-xl font-bold uppercase tracking-wide">Temporary Work Pass</h1>
    <nav class="flex flex-wrap gap-1 bg-white dark:bg-gray-900 rounded-lg shadow p-1">
        <?php foreach ($tabs as $url => $label): $on = str_contains($here, $url); ?>
            <a href="<?= base_url($url) ?>" class="px-3 py-1.5 rounded text-sm font-semibold <?= $on ? 'bg-primary text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' ?>"><?= $label ?></a>
        <?php endforeach; ?>
    </nav>
</div>
<?= view('report_templates/_flash') ?>
