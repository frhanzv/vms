<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "primary-hover": "#0f6bd0",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                        "surface-dark": "#1c2630",
                        "surface-light": "#ffffff",
                        "border-dark": "#2d3b4b",
                        "border-light": "#e5e7eb",
                        "text-main-dark": "#ffffff",
                        "text-secondary-dark": "#92adc9",
                        "text-main-light": "#111827",
                        "text-secondary-light": "#6b7280",
                    },
                    fontFamily: {
                        "display": ["Montserrat", "sans-serif"],
                        "sans": ["Montserrat", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main-light dark:text-text-main-dark font-display antialiased overflow-hidden">
<div class="flex h-screen w-full flex-col">
    <div class="flex flex-1 overflow-hidden">
        <?= view('partials/sidebar') ?>

        <main class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark custom-scrollbar">
<?php
$active   = array_values(array_filter($steps, fn($s) => $s['is_active']));
$inactive = array_values(array_filter($steps, fn($s) => ! $s['is_active']));
$staffN   = count(array_filter($steps, fn($s) => $s['owner'] !== 'Vendor'));
$vendorN  = count(array_filter($steps, fn($s) => $s['owner'] !== 'Staff'));
$customN  = count(array_filter($steps, fn($s) => $s['is_custom']));
$row = function (array $w, bool $on) { ob_start(); ?>
                                <div class="grid grid-cols-12 gap-4 p-4 items-center hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                                    <div class="col-span-4 flex items-center gap-3">
                                        <div class="size-10 rounded <?= $on ? 'bg-primary/10 text-primary' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' ?> flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined"><?= esc($w['icon']) ?></span>
                                        </div>
                                        <div>
                                            <p class="text-text-main-light dark:text-white font-medium text-sm flex items-center gap-2"><?= esc($w['name']) ?></p>
                                            <p class="text-text-secondary-light dark:text-text-secondary-dark text-xs"><?= esc($w['owner']) ?></p>
                                        </div>
                                    </div>
                                    <div class="col-span-3 text-sm <?= $on ? 'text-gray-600 dark:text-gray-300' : 'text-gray-500 dark:text-gray-400' ?>"><?= esc($w['trigger']) ?></div>
                                    <div class="col-span-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 dark:bg-[#233648] text-xs font-medium <?= $on ? '' : 'text-gray-500' ?>"><?= $on ? esc((string) $w['order']) : '-' ?></span>
                                    </div>
                                    <div class="col-span-2">
                                        <?php if ($on): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-500/10 text-green-600 dark:text-green-400 text-xs font-bold border border-green-500/20"><span class="size-1.5 rounded-full bg-green-500"></span>Active</span>
                                        <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-500/10 text-gray-600 dark:text-gray-400 text-xs font-bold border border-gray-500/20"><span class="size-1.5 rounded-full bg-gray-500"></span>Inactive</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-span-2 flex items-center justify-start gap-2">
                                        <button onclick='openEditModal(<?= esc(json_encode(['key' => $w['key'], 'name' => $w['name'], 'trigger' => $w['trigger'], 'owner' => $w['owner'], 'custom' => $w['is_custom']]), 'attr') ?>)' class="p-1.5 text-gray-500 hover:text-primary hover:bg-primary/10 rounded transition-colors" title="Edit">
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </button>
                                        <?php if ($w['locked']): ?>
                                        <span class="p-1.5 text-gray-300 dark:text-gray-600 cursor-not-allowed" title="Required step, always on"><span class="material-symbols-outlined text-[20px]">toggle_on</span></span>
                                        <?php else: ?>
                                        <button onclick="toggleWorkflow('<?= esc($w['key'], 'js') ?>')" class="p-1.5 text-gray-500 <?= $on ? 'hover:text-orange-500 hover:bg-orange-500/10' : 'hover:text-green-500 hover:bg-green-500/10' ?> rounded transition-colors" title="<?= $on ? 'Set Inactive' : 'Set Active' ?>">
                                            <span class="material-symbols-outlined text-[20px]"><?= $on ? 'toggle_on' : 'toggle_off' ?></span>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($w['is_custom'] && $w['own']): ?>
                                        <button onclick="openDeleteModal('<?= esc($w['key'], 'js') ?>')" class="p-1.5 text-gray-500 hover:text-red-500 hover:bg-red-500/10 rounded transition-colors" title="Delete">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                        <?php else: ?>
                                        <span class="p-1.5 text-gray-300 dark:text-gray-600 cursor-not-allowed" title="<?= $w['is_custom'] ? 'Added by the platform default, turn it off instead' : 'Built-in step, cannot be deleted' ?>"><span class="material-symbols-outlined text-[20px]">delete</span></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
<?php return ob_get_clean(); };
$tableHead = <<<'HTML'
                    <div class="grid grid-cols-12 gap-4 p-4 border-b border-border-light dark:border-border-dark bg-gray-50 dark:bg-[#1a242f] text-xs font-semibold text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider">
                        <div class="col-span-4">Workflow Name</div>
                        <div class="col-span-3">Trigger</div>
                        <div class="col-span-1">Order</div>
                        <div class="col-span-2">Status</div>
                        <div class="col-span-2">Actions</div>
                    </div>
HTML;
?>
            <div class="px-4 md:px-10 py-6 max-w-[1440px] mx-auto w-full">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-5 pb-6 border-b border-border-light dark:border-border-dark mb-6">
                    <div class="flex min-w-72 flex-col gap-2">
                        <h1 class="text-text-main-light dark:text-white text-3xl md:text-4xl font-black leading-tight tracking-[-0.033em]">Vendor Workflows</h1>
                        <p class="text-text-secondary-light dark:text-[#92adc9] text-base font-normal leading-normal max-w-2xl">Manage the vendor pass process sequence, from company setup to the finished card.
                            <?= $platform ? 'You are editing the default used by every client without its own workflow.' : 'You are editing your own client\'s workflow.' ?></p>
                    </div>
                    <a href="<?= base_url('vendor-workflow/create') ?>" class="flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-lg shadow-lg shadow-primary/20 transition-all">
                        <span class="material-symbols-outlined text-[20px]">edit</span>
                        <span class="text-sm font-bold leading-normal tracking-[0.015em]">Edit Sequence</span>
                    </a>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="mb-5 rounded-lg border border-green-200 bg-green-50 text-green-700 px-4 py-3 text-sm"><?= esc((string) session()->getFlashdata('success')) ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm"><?= esc((string) session()->getFlashdata('error')) ?></div>
                <?php endif; ?>
                <?php if (! $tableOk): ?>
                    <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 text-amber-800 px-4 py-3 text-sm">Run <code>php spark migrate</code> first, so changes can be saved.</div>
                <?php endif; ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="flex flex-col gap-1 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-border-light dark:border-[#324d67] shadow-sm">
                        <p class="text-text-secondary-light dark:text-[#92adc9] text-sm font-medium">Active Workflows</p>
                        <p class="text-text-main-light dark:text-white text-2xl font-bold leading-tight mt-1"><?= count($active) ?></p>
                        <p class="text-[#0bda5b] text-xs font-medium">of <?= count($steps) ?> steps enabled</p>
                    </div>
                    <div class="flex flex-col gap-1 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-border-light dark:border-[#324d67] shadow-sm">
                        <p class="text-text-secondary-light dark:text-[#92adc9] text-sm font-medium">Done By Staff</p>
                        <p class="text-text-main-light dark:text-white text-2xl font-bold leading-tight mt-1"><?= $staffN ?></p>
                        <p class="text-[#0bda5b] text-xs font-medium">Steps that need your team</p>
                    </div>
                    <div class="flex flex-col gap-1 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-border-light dark:border-[#324d67] shadow-sm">
                        <p class="text-text-secondary-light dark:text-[#92adc9] text-sm font-medium">Done By Vendor</p>
                        <p class="text-text-main-light dark:text-white text-2xl font-bold leading-tight mt-1"><?= $vendorN ?></p>
                        <p class="text-[#0bda5b] text-xs font-medium">Staff can step in for them</p>
                    </div>
                    <div class="flex flex-col gap-1 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-border-light dark:border-[#324d67] shadow-sm">
                        <p class="text-text-secondary-light dark:text-[#92adc9] text-sm font-medium">Custom Workflows</p>
                        <p class="text-text-main-light dark:text-white text-2xl font-bold leading-tight mt-1"><?= $customN ?></p>
                        <p class="text-text-secondary-light dark:text-[#92adc9] text-xs font-medium">Add them on Edit Sequence</p>
                    </div>
                </div>

                <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Active Workflows</h2>
                <div class="flex flex-col bg-surface-light dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark shadow-sm overflow-hidden mb-8">
                    <?= $tableHead ?>
                    <div class="flex flex-col divide-y divide-border-light dark:divide-border-dark">
                        <?php if (empty($active)): ?><div class="p-8 text-center text-gray-500 dark:text-gray-400">No active workflows.</div><?php endif; ?>
                        <?php foreach ($active as $w) { echo $row($w, true); } ?>
                    </div>
                </div>

                <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4 mt-8">Inactive Workflows</h2>
                <div class="flex flex-col bg-surface-light dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark shadow-sm overflow-hidden opacity-80">
                    <?= $tableHead ?>
                    <div class="flex flex-col divide-y divide-border-light dark:divide-border-dark">
                        <?php if (empty($inactive)): ?><div class="p-8 text-center text-gray-500 dark:text-gray-400">No inactive workflows.</div><?php endif; ?>
                        <?php foreach ($inactive as $w) { echo $row($w, false); } ?>
                    </div>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-xs text-gray-500 max-w-3xl">The order is how the steps are listed for the team. The vendor and staff pages still follow the real flow. Steps you add are for tracking and do not block anything. Required steps stay on and cannot be deleted. Turning off <strong>Account Verification</strong> activates vendor accounts as soon as they register. Card Processing covers the photo, card details and printing on the Process List.</p>
                    <form method="post" action="<?= base_url('vendor-workflow/reset') ?>" onsubmit="return confirm('Reset all steps to the default? Custom steps you added will be removed.')"><?= csrf_field() ?>
                        <button class="text-sm text-gray-500 hover:text-red-600 underline">Reset to default</button></form>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-200 dark:border-slate-700 w-full max-w-md mx-4 overflow-hidden transform transition-all">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Edit Workflow</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="editForm" method="post" action="">
            <?= csrf_field() ?>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Workflow Name <span class="text-red-500">*</span></label>
                    <input type="text" id="editWorkflowName" name="step_name" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Trigger</label>
                    <input type="text" id="editTriggerEvent" name="trigger_event" placeholder="e.g. Staff approve the request" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                    <p class="text-xs text-gray-500 mt-1">Describe what happens in this step.</p>
                </div>
                <div id="editOwnerWrap">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Done by</label>
                    <select id="editOwner" name="owner" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                        <?php foreach (\App\Libraries\VendorWorkflow::OWNERS as $o): ?><option><?= esc($o) ?></option><?php endforeach; ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Only custom workflows can change who does them.</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-hover rounded-lg transition-colors shadow-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-200 dark:border-slate-700 w-full max-w-md mx-4 overflow-hidden transform transition-all">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-slate-700 bg-red-50 dark:bg-red-900/20">
            <h3 class="text-lg font-bold text-red-600 dark:text-red-400 flex items-center gap-2">
                <span class="material-symbols-outlined">warning</span> Delete Workflow
            </h3>
            <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="deleteForm" method="post" action="">
            <?= csrf_field() ?>
            <div class="p-6">
                <p class="text-gray-700 dark:text-gray-300">Are you sure you want to delete this workflow? This action cannot be undone.</p>
            </div>
            <div class="flex justify-end gap-3 p-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm">Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
    function submitPost(url) {
        const f = document.createElement('form'); f.method = 'post'; f.action = url;
        f.innerHTML = '<?= csrf_field() ?>'; document.body.appendChild(f); f.submit();
    }
    function toggleWorkflow(key) { submitPost('<?= base_url('vendor-workflow/toggle/') ?>' + encodeURIComponent(key)); }
    function openEditModal(w) {
        document.getElementById('editWorkflowName').value = w.name;
        document.getElementById('editTriggerEvent').value = w.trigger;
        const sel = document.getElementById('editOwner'); sel.value = w.owner; sel.disabled = !w.custom;
        document.getElementById('editOwnerWrap').style.display = w.custom ? '' : 'none';
        document.getElementById('editForm').action = '<?= base_url('vendor-workflow/edit/') ?>' + encodeURIComponent(w.key);
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
    function openDeleteModal(key) {
        document.getElementById('deleteForm').action = '<?= base_url('vendor-workflow/delete/') ?>' + encodeURIComponent(key);
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    function closeDeleteModal() { document.getElementById('deleteModal').classList.add('hidden'); }
</script>

</body>
</html>
