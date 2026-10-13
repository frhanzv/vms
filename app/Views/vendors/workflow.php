<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = { darkMode: "class", theme: { extend: {
            colors: { primary: "#137fec", "background-light": "#f6f7f8", "background-dark": "#111827", "card-light": "#ffffff", "card-dark": "#1f2937" },
            fontFamily: { sans: ["Montserrat", "sans-serif"] } } } };
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important}</style>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden">
    <?= view('partials/sidebar') ?>
    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8">
        <div class="mx-auto max-w-7xl">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span><span><?= esc(session()->getFlashdata('success')) ?></span>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">
                <?php foreach (explode("\n", (string) session()->getFlashdata('error')) as $line): ?><p><?= esc($line) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

<?php
$active   = array_values(array_filter($steps, fn($s) => $s['is_active']));
$inactive = array_values(array_filter($steps, fn($s) => ! $s['is_active']));
$ownerCls = ['Staff' => 'bg-blue-50 text-blue-700', 'Vendor' => 'bg-amber-50 text-amber-700', 'Vendor / Staff' => 'bg-purple-50 text-purple-700'];
$table = function (array $rows, bool $on) use ($ownerCls) { ob_start(); ?>
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <table class="w-full text-left text-sm">
            <thead><tr class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                <th class="p-4">Workflow Name</th><th class="p-4">Done By</th><th class="p-4">What Happens</th><th class="p-4">Order</th><th class="p-4">Status</th><th class="p-4">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $s): ?>
                <tr class="border-b last:border-0 border-gray-100 dark:border-gray-700">
                    <td class="p-4"><div class="flex items-center gap-3">
                        <span class="size-10 rounded-lg flex items-center justify-center <?= $on ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-gray-400' ?>"><span class="material-symbols-outlined"><?= $s['icon'] ?></span></span>
                        <div><p class="font-semibold"><?= esc($s['name']) ?></p><p class="text-xs text-gray-500"><?= esc($s['key']) ?></p></div></div></td>
                    <td class="p-4"><span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $ownerCls[$s['owner']] ?? '' ?>"><?= esc($s['owner']) ?></span></td>
                    <td class="p-4 text-gray-600 dark:text-gray-300 max-w-xs"><?= esc($s['trigger']) ?></td>
                    <td class="p-4"><span class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-xs font-bold"><?= $s['order'] ?></span></td>
                    <td class="p-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $on ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' ?>"><?= $on ? 'Active' : 'Inactive' ?></span>
                        <?php if ($s['locked']): ?><span class="ml-1 text-[11px] text-gray-400" title="Required step">required</span><?php endif; ?>
                        <?php if (! $s['locked'] && ! $s['enforced']): ?><span class="ml-1 text-[11px] text-gray-400" title="Shown only for now">label only</span><?php endif; ?>
                    </td>
                    <td class="p-4"><div class="flex items-center gap-3">
                        <button type="button" @click='edit = <?= esc(json_encode(['key' => $s['key'], 'name' => $s['name'], 'trigger' => $s['trigger'], 'owner' => $s['owner'], 'custom' => $s['is_custom']]), 'attr') ?>' class="text-gray-500 hover:text-primary" title="Edit"><span class="material-symbols-outlined text-[20px]">edit</span></button>
                        <?php if (! $s['locked']): ?>
                        <form method="post" action="<?= base_url('vendor-workflow/toggle/' . $s['key']) ?>"><?= csrf_field() ?>
                            <button class="text-gray-500 hover:text-primary" title="<?= $on ? 'Turn off' : 'Turn on' ?>"><span class="material-symbols-outlined text-[24px]"><?= $on ? 'toggle_on' : 'toggle_off' ?></span></button></form>
                        <?php endif; ?>
                        <?php if ($s['is_custom'] && $s['own']): ?>
                        <form method="post" action="<?= base_url('vendor-workflow/delete/' . $s['key']) ?>" onsubmit="return confirm('Delete this step?')"><?= csrf_field() ?>
                            <button class="text-gray-500 hover:text-red-600" title="Delete"><span class="material-symbols-outlined text-[20px]">delete</span></button></form>
                        <?php endif; ?>
                    </div></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php return ob_get_clean(); };
?>
<div x-data="{ edit: null }">
    <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 pb-6 border-b border-gray-200 dark:border-gray-700 mb-6">
        <div>
            <h1 class="text-3xl md:text-4xl font-black tracking-tight">Vendor Workflows</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 max-w-2xl">The steps of a vendor pass, from company setup to the finished card, and who does each one.
                <?= $platform ? 'You are editing the default used by every client without its own workflow.' : 'You are editing your own client\'s workflow.' ?></p>
        </div>
        <div class="flex items-center gap-2">
            <form method="post" action="<?= base_url('vendor-workflow/reset') ?>" onsubmit="return confirm('Reset all steps to the default? Custom steps you added will be removed.')"><?= csrf_field() ?>
                <button class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium">Reset to default</button></form>
            <a href="<?= base_url('vendor-workflow/create') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-blue-700"><span class="material-symbols-outlined text-[18px]">edit</span>Edit Sequence</a>
        </div>
    </div>

    <?php if (! $tableOk): ?><div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 text-amber-800 px-4 py-3 text-sm">Run <code>php spark migrate</code> first, so changes can be saved.</div><?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5"><p class="text-sm text-gray-500">Active Steps</p><p class="text-3xl font-bold mt-1"><?= count($active) ?></p><p class="text-xs text-emerald-600 mt-1">of <?= count($steps) ?> steps</p></div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5"><p class="text-sm text-gray-500">Done By Staff</p><p class="text-3xl font-bold mt-1"><?= count(array_filter($steps, fn($s) => $s['owner'] !== 'Vendor')) ?></p><p class="text-xs text-gray-500 mt-1">steps need your team</p></div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5"><p class="text-sm text-gray-500">Done By Vendor</p><p class="text-3xl font-bold mt-1"><?= count(array_filter($steps, fn($s) => $s['owner'] !== 'Staff')) ?></p><p class="text-xs text-gray-500 mt-1">staff can step in for them</p></div>
    </div>

    <h2 class="text-xl font-bold mb-3">Active Workflows</h2>
    <div class="mb-8"><?= $table($active, true) ?></div>
    <?php if ($inactive): ?>
    <h2 class="text-xl font-bold mb-3">Inactive Workflows</h2>
    <div class="mb-8"><?= $table($inactive, false) ?></div>
    <?php endif; ?>
    <p class="text-xs text-gray-500">Use <strong>Edit Sequence</strong> to reorder the steps or add your own. The order is how the steps are listed and shown to the team; the vendor and staff pages themselves still follow the real flow (a card cannot be printed before it is approved). Steps you add are for tracking and do not block anything. Required steps cannot be turned off or deleted. Turning off <strong>Account Verification</strong> activates vendor accounts as soon as they register; turning off <strong>Card Photo</strong> or <strong>Card Printing</strong> stops the Process List from asking for them.</p>

    <div x-show="edit" x-cloak class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
        <form method="post" :action="'<?= base_url('vendor-workflow/edit') ?>/' + (edit ? edit.key : '')" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-md p-6 space-y-3"><?= csrf_field() ?>
            <h3 class="text-lg font-bold">Edit Step</h3>
            <div><label class="block text-xs font-semibold mb-1">Name</label><input name="step_name" :value="edit && edit.name" maxlength="100" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded px-3 py-2 text-sm"/></div>
            <div x-show="edit && edit.custom"><label class="block text-xs font-semibold mb-1">Done by</label>
                <select name="owner" :disabled="!(edit && edit.custom)" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded px-3 py-2 text-sm">
                    <?php foreach (\App\Libraries\VendorWorkflow::OWNERS as $o): ?><option value="<?= esc($o) ?>" :selected="edit && edit.owner === '<?= esc($o) ?>'"><?= esc($o) ?></option><?php endforeach; ?>
                </select></div>
            <div><label class="block text-xs font-semibold mb-1">What happens</label><textarea name="trigger_event" rows="3" maxlength="255" x-text="edit && edit.trigger" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded px-3 py-2 text-sm"></textarea></div>
            <div class="flex justify-end gap-2 pt-2"><button type="button" @click="edit = null" class="px-4 py-2 rounded border border-gray-300 text-sm">Cancel</button><button class="px-4 py-2 rounded bg-primary text-white text-sm font-semibold">Save</button></div>
        </form>
    </div>
</div>
        </div>
    </main>
</body>
</html>
