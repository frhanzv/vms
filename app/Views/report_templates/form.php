<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<?php
use App\Libraries\ReportBuilder;

$chosen = $rb->sections($tpl);
$cfg    = json_decode((string) ($tpl['schedule_config'] ?? '{}'), true) ?: [];
$mails  = implode(', ', json_decode((string) ($tpl['recipients'] ?? '[]'), true) ?: []);
$input  = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm focus:ring-primary focus:border-primary';
$label  = 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1';
$modules = [];
foreach ($rb->catalog() as $key => $s) {
    $modules[$s['module']][$key] = $s;
}
?>
<form method="post" action="<?= base_url('report/templates/save') ?>" class="-m-2 space-y-4"
      x-data="{ frequency: '<?= esc($tpl['frequency'], 'js') ?>', layout: '<?= esc($tpl['report_layout'], 'js') ?>' }">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int) $tpl['id'] ?>">
    <input type="hidden" name="report_layout" :value="layout">

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold uppercase tracking-wide"><?= $tpl['id'] ? 'Edit Template' : 'New Template' ?></h1>
        <a href="<?= base_url('report/templates') ?>" class="inline-flex items-center gap-1 px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-sm font-semibold hover:bg-gray-100 dark:hover:bg-gray-800">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Back to Reports</a>
    </div>

    <?php if ($errors): ?>
        <div class="px-4 py-3 rounded border bg-red-50 text-red-700 border-red-200 text-sm"><b>Please check the form:</b>
            <ul class="list-disc ml-5"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-lg shadow p-4 space-y-3">
            <h2 class="font-bold uppercase text-sm text-primary">Template Details</h2>
            <div><label class="<?= $label ?>">Template Name <span class="text-red-500">*</span></label>
                <input name="name" required maxlength="150" value="<?= esc($tpl['name']) ?>" placeholder="Daily Gate In / Out Report" class="<?= $input ?>"></div>
            <div><label class="<?= $label ?>">Description</label>
                <textarea name="description" rows="2" placeholder="Optional notes for this report template" class="<?= $input ?>"><?= esc($tpl['description'] ?? '') ?></textarea></div>
            <div><label class="<?= $label ?>">Email Recipients</label>
                <textarea name="recipient_text" rows="2" placeholder="manager@company.com, security@company.com" class="<?= $input ?>"><?= esc($mails) ?></textarea>
                <span class="text-xs text-gray-500">Add multiple emails separated by commas, semicolons or new lines.</span></div>
            <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" <?= ! empty($tpl['is_active']) ? 'checked' : '' ?>> Active</label>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 space-y-3">
            <h2 class="font-bold uppercase text-sm text-primary">Schedule &amp; Format</h2>
            <div><label class="<?= $label ?>">Frequency</label>
                <select name="frequency" x-model="frequency" class="<?= $input ?>">
                    <?php foreach (ReportBuilder::FREQUENCIES as $v => $l): ?><option value="<?= $v ?>"><?= $l ?></option><?php endforeach; ?>
                </select></div>
            <div x-show="frequency !== 'manual'"><label class="<?= $label ?>">Time</label>
                <input type="time" name="schedule_time" value="<?= esc($cfg['time'] ?? '08:00') ?>" class="<?= $input ?>"></div>
            <div x-show="['monthly','yearly'].includes(frequency)"><label class="<?= $label ?>">Week</label>
                <select name="schedule_week" class="<?= $input ?>">
                    <?php foreach (ReportBuilder::WEEKS as $w): ?><option value="<?= $w ?>" <?= ($cfg['week'] ?? '') === $w ? 'selected' : '' ?>><?= ucfirst($w) ?></option><?php endforeach; ?>
                </select></div>
            <div x-show="['weekly','monthly','yearly'].includes(frequency)"><label class="<?= $label ?>">Day</label>
                <select name="schedule_weekday" class="<?= $input ?>">
                    <?php foreach (ReportBuilder::WEEKDAYS as $d): ?><option value="<?= $d ?>" <?= ($cfg['weekday'] ?? '') === $d ? 'selected' : '' ?>><?= ucfirst($d) ?></option><?php endforeach; ?>
                </select></div>
            <div x-show="frequency === 'yearly'"><label class="<?= $label ?>">Month</label>
                <select name="schedule_month" class="<?= $input ?>">
                    <?php for ($m = 1; $m <= 12; $m++): ?><option value="<?= $m ?>" <?= (int) ($cfg['month'] ?? 1) === $m ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option><?php endfor; ?>
                </select></div>
            <p x-show="frequency !== 'manual'" class="text-xs text-gray-500">Scheduled reports cover the previous day / week / month / year and are emailed to the recipients.</p>
            <div><label class="<?= $label ?>">Format</label>
                <select name="format" class="<?= $input ?>">
                    <?php foreach (ReportBuilder::FORMATS as $v => $l): ?><option value="<?= $v ?>" <?= $tpl['format'] === $v ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?>
                </select>
                <span class="text-xs text-gray-500">Excel + PDF downloads as a ZIP and sends both files as email attachments.</span></div>
            <div><label class="<?= $label ?>">Report Layout</label>
                <div class="flex gap-2">
                    <button type="button" @click="layout = 'sectioned'" :class="layout === 'sectioned' ? 'bg-primary text-white border-primary' : 'border-gray-300 dark:border-gray-600'" class="flex-1 px-3 py-2 rounded border text-sm font-semibold inline-flex items-center justify-center gap-1"><span class="material-symbols-outlined text-[18px]">view_agenda</span> Sectioned</button>
                    <button type="button" @click="layout = 'combined'" :class="layout === 'combined' ? 'bg-primary text-white border-primary' : 'border-gray-300 dark:border-gray-600'" class="flex-1 px-3 py-2 rounded border text-sm font-semibold inline-flex items-center justify-center gap-1"><span class="material-symbols-outlined text-[18px]">table_chart</span> Combined</button>
                </div>
                <span class="text-xs text-gray-500">Choose sectioned or combined tables for PDF. Excel always uses a separate worksheet for each section.</span></div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 space-y-4">
        <h2 class="font-bold uppercase text-sm text-primary">Sections</h2>
        <?php foreach ($modules as $module => $secs): ?>
            <div>
                <div class="text-xs font-bold uppercase text-gray-500 mb-2"><?= esc($module) ?></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <?php foreach ($secs as $key => $s): $on = isset($chosen[$key]); $cols = $chosen[$key] ?? array_keys($s['columns']); ?>
                    <div x-data="{ on: <?= $on ? 'true' : 'false' ?> }" class="rounded-lg border p-3" :class="on ? 'border-primary bg-primary/5' : 'border-gray-200 dark:border-gray-700'">
                        <label class="flex items-start gap-2 cursor-pointer">
                            <input type="checkbox" name="sections[<?= $key ?>][enabled]" value="1" x-model="on" class="mt-0.5">
                            <span><span class="font-semibold text-sm"><?= esc($s['label']) ?></span><span class="block text-xs text-gray-500"><?= esc($s['description']) ?></span></span>
                        </label>
                        <div x-show="on" class="mt-3 grid grid-cols-2 gap-x-3 gap-y-1">
                            <?php foreach ($s['columns'] as $ck => $cl): ?>
                                <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="sections[<?= $key ?>][columns][<?= $ck ?>]" value="1" <?= in_array($ck, $cols, true) ? 'checked' : '' ?>> <?= esc($cl) ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded bg-primary text-white text-sm font-semibold hover:opacity-90"><span class="material-symbols-outlined text-[18px]">save</span> Save Template</button>
    </div>
</form>

<?= view('staffs/_layout_bottom') ?>
