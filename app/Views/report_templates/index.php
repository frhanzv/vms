<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php $fmt = static fn($v) => $v ? date('d/m/Y H:i', strtotime((string) $v)) : '-'; ?>
<div class="-m-2 space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold uppercase tracking-wide">Report Templates</h1>
            <p class="text-sm text-gray-500">Save the sections and columns you need, then preview, download or email them, or let them go out on a schedule.</p>
        </div>
        <a href="<?= base_url('report/templates/create') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-primary text-white text-sm font-semibold hover:opacity-90">
            <span class="material-symbols-outlined text-[18px]">add_chart</span> New Template</a>
    </div>

    <?= view('report_templates/_flash') ?>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                <tr><th class="p-3">Template</th><th class="p-3">Sections</th><th class="p-3">Schedule</th><th class="p-3">Format</th><th class="p-3">Recipients</th><th class="p-3">Status</th><th class="p-3">Last Generated</th><th class="p-3">Actions</th></tr>
            </thead>
            <tbody>
            <?php if (! $rows): ?>
                <tr><td colspan="8" class="p-6 text-center text-gray-500">No report templates yet. Click <b>New Template</b> to build one.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $r): $secs = json_decode((string) $r['sections'], true) ?: []; $mails = json_decode((string) $r['recipients'], true) ?: []; ?>
                <tr class="border-t border-gray-100 dark:border-gray-800">
                    <td class="p-3"><div class="font-semibold"><?= esc($r['name']) ?></div><div class="text-xs text-gray-500 max-w-xs truncate"><?= esc($r['description'] ?? '') ?></div></td>
                    <td class="p-3 text-xs"><?= esc(implode(', ', array_column($secs, 'label'))) ?></td>
                    <td class="p-3"><?= esc($rb->scheduleLabel($r)) ?></td>
                    <td class="p-3"><?= esc(\App\Libraries\ReportBuilder::FORMATS[$r['format']] ?? $r['format']) ?></td>
                    <td class="p-3 text-xs"><?= $mails ? esc(implode(', ', $mails)) : '-' ?></td>
                    <td class="p-3"><span class="text-xs px-2 py-0.5 rounded font-semibold <?= $r['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' ?>"><?= $r['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                    <td class="p-3"><?= $fmt($r['last_generated_at']) ?></td>
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            <a href="<?= base_url('report/templates/' . $r['id']) ?>" title="Preview" class="text-primary"><span class="material-symbols-outlined text-[20px]">visibility</span></a>
                            <a href="<?= base_url('report/templates/' . $r['id'] . '/export') ?>" title="Download" class="text-primary"><span class="material-symbols-outlined text-[20px]">file_download</span></a>
                            <a href="<?= base_url('report/templates/' . $r['id'] . '/edit') ?>" title="Edit" class="text-primary"><span class="material-symbols-outlined text-[20px]">edit</span></a>
                            <form method="post" action="<?= base_url('report/templates/' . $r['id'] . '/delete') ?>" onsubmit="return confirm('Delete this report template?')">
                                <?= csrf_field() ?><button type="submit" title="Delete" class="text-red-600"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= view('staffs/_layout_bottom') ?>
