<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#111827",
                        "card-light": "#ffffff",
                        "card-dark": "#1f2937"
                    },
                    fontFamily: {
                        sans: ["Montserrat", "sans-serif"]
                    }
                }
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden">
    <?= view('partials/sidebar') ?>
    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8">
<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-5xl">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Edit Vendor Process Sequence</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Drag to reorder active steps, then click <strong>Save Sequence</strong>. <strong>Undo changes</strong> only reverts the current page state before save. Use <strong>+ Add Workflow</strong> to add your own step.</p>
        </div>
        <a href="<?= base_url('vendor-workflow') ?>" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-800">Back</a>
    </div>
    <form action="<?= base_url('vendor-workflow/save') ?>" method="post" id="seqForm"><?= csrf_field() ?>
        <input type="hidden" name="steps_json" id="stepsJson"/>
        <input type="hidden" name="custom_steps_json" id="customJson"/>
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 flex items-center justify-between gap-3">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">Drag to reorder active steps</span>
                <button type="button" onclick="openAdd()" class="px-5 py-2 rounded border border-primary text-primary text-sm font-bold hover:bg-primary hover:text-white transition-colors">+ Add Workflow</button>
            </div>
            <ul id="seqList" class="divide-y divide-gray-200 dark:divide-gray-700">
            <?php foreach ($steps as $i => $s): ?>
                <li class="step-item flex items-center justify-between px-4 py-4 bg-white dark:bg-gray-900" draggable="true" data-key="<?= esc($s['key']) ?>">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-gray-400 cursor-grab">drag_indicator</span>
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold step-no"><?= $i + 1 ?></span>
                        <div><p class="text-sm font-semibold"><?= esc($s['name']) ?></p><p class="text-xs text-gray-500"><?= esc($s['owner']) ?> &bull; <?= esc($s['trigger']) ?></p></div>
                    </div>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </form>
    <div class="mt-6 flex justify-end gap-2">
        <button type="button" id="undoBtn" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-800">Undo changes</button>
        <button type="submit" form="seqForm" class="px-4 py-2 rounded-lg bg-primary hover:bg-blue-700 text-white text-sm font-semibold">Save Sequence</button>
    </div>
</div>
        </main>

<div id="addModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700"><h3 class="text-lg font-bold">Add Custom Workflow</h3>
            <button type="button" onclick="closeAdd()" class="text-gray-400 hover:text-gray-600"><span class="material-symbols-outlined">close</span></button></div>
        <form id="addForm" onsubmit="submitAdd(event)">
            <div class="p-6 space-y-4">
                <div><label class="block text-sm font-medium mb-1">Workflow Name <span class="text-red-500">*</span></label>
                    <input id="addName" required maxlength="100" placeholder="e.g. Safety Briefing" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 outline-none focus:ring-2 focus:ring-primary"/></div>
                <div><label class="block text-sm font-medium mb-1">Done by</label>
                    <select id="addOwner" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900">
                        <?php foreach (\App\Libraries\VendorWorkflow::OWNERS as $o): ?><option><?= esc($o) ?></option><?php endforeach; ?></select></div>
                <div><label class="block text-sm font-medium mb-1">What happens</label>
                    <input id="addTrigger" maxlength="255" placeholder="e.g. Vendor staff attend the safety briefing" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 outline-none focus:ring-2 focus:ring-primary"/></div>
                <p class="text-xs text-gray-500">A custom step is for tracking and showing the team the full process. It does not block the vendor or staff pages.</p>
            </div>
            <div class="flex justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeAdd()" class="px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Cancel</button>
                <button class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg">Add Workflow</button></div>
        </form>
    </div>
</div>

<script>
const list = document.getElementById('seqList'), stepsJson = document.getElementById('stepsJson'), customJson = document.getElementById('customJson');
const original = [...list.querySelectorAll('.step-item')].map(e => e.dataset.key);
const added = {}; let dragging = null, seed = 0;
const esc = t => String(t).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
function sync() {
    const items = [...list.querySelectorAll('.step-item')];
    items.forEach((it, i) => it.querySelector('.step-no').textContent = i + 1);
    stepsJson.value = JSON.stringify(items.map(i => i.dataset.key));
    customJson.value = JSON.stringify(items.filter(i => added[i.dataset.key]).map(i => added[i.dataset.key]));
}
function bind(it) {
    it.addEventListener('dragstart', () => { dragging = it; it.classList.add('opacity-60'); });
    it.addEventListener('dragend', () => { it.classList.remove('opacity-60'); dragging = null; sync(); });
    it.addEventListener('dragover', e => {
        e.preventDefault(); if (!dragging || dragging === it) return;
        const r = it.getBoundingClientRect();
        list.insertBefore(dragging, e.clientY < r.top + r.height / 2 ? it : it.nextSibling);
    });
}
list.querySelectorAll('.step-item').forEach(bind);
document.getElementById('undoBtn').addEventListener('click', () => {
    const map = {}; list.querySelectorAll('.step-item').forEach(i => map[i.dataset.key] = i);
    list.innerHTML = '';
    original.forEach(k => map[k] && list.appendChild(map[k]));
    Object.keys(added).forEach(k => delete added[k]);
    sync();
});
function openAdd() { document.getElementById('addModal').classList.remove('hidden'); document.getElementById('addName').focus(); }
function closeAdd() { document.getElementById('addModal').classList.add('hidden'); document.getElementById('addForm').reset(); }
function submitAdd(e) {
    e.preventDefault();
    const label = document.getElementById('addName').value.trim(); if (!label) return;
    const owner = document.getElementById('addOwner').value, trig = document.getElementById('addTrigger').value.trim();
    const key = 'new_' + (++seed);
    added[key] = { key, label, owner, trigger_event: trig };
    const li = document.createElement('li');
    li.className = 'step-item flex items-center justify-between px-4 py-4 bg-white dark:bg-gray-900'; li.draggable = true; li.dataset.key = key;
    li.innerHTML = '<div class="flex items-center gap-3"><span class="material-symbols-outlined text-gray-400 cursor-grab">drag_indicator</span><span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold step-no">0</span><div><p class="text-sm font-semibold">' + esc(label) + ' <span class="ml-1 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-bold">NEW</span></p><p class="text-xs text-gray-500">' + esc(owner) + ' &bull; ' + esc(trig || 'Custom step') + '</p></div></div>';
    bind(li); list.appendChild(li); closeAdd(); sync();
}
sync();
</script>
</body>
</html>
