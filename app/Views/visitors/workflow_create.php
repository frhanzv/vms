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
        <div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-5xl">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Invitation Process Sequence</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Drag to reorder active workflows, then click <strong>Save Sequence</strong>. <strong>Undo changes</strong> only reverts the current page state before save. Use <strong>+ Add Workflow</strong> to create custom workflows.</p>
                </div>
                <a href="<?= base_url('workflow') ?>" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-800">Back</a>
            </div>

            <form action="<?= base_url('workflow/save') ?>" method="post" id="workflowForm">
                <?= csrf_field() ?>
                <input type="hidden" name="steps_json" id="stepsJson" value="">
                <input type="hidden" name="custom_steps_json" id="customStepsJson" value="">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 flex items-center justify-between gap-3">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">Drag to reorder active steps</span>
                        <button type="button" id="addWorkflowBtn" onclick="openAddModal()" class="px-5 py-2 rounded border border-primary text-primary text-sm font-bold hover:bg-primary hover:text-white transition-colors shadow-sm">
                            + Add Workflow
                        </button>
                    </div>
                    <ul id="workflowStepList" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($steps as $index => $step): ?>
                            <li class="step-item flex items-center justify-between px-4 py-4 bg-white dark:bg-gray-900" draggable="true" data-step-key="<?= esc((string) $step['key']) ?>" data-is-custom="<?= ! empty($step['is_custom']) ? '1' : '0' ?>" data-step-label="<?= esc((string) $step['label']) ?>" data-step-route="<?= esc((string) $step['route']) ?>">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-gray-400 cursor-grab">drag_indicator</span>
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold step-no"><?= $index + 1 ?></span>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white"><?= esc((string) $step['label']) ?></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= esc((string) $step['route']) ?></p>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </form>

                <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
                    <div class="flex justify-end gap-2 order-1 sm:order-2">
                        <button type="button" id="resetOrderBtn" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-800">Undo changes</button>
                        <button type="submit" form="workflowForm" class="px-4 py-2 rounded-lg bg-primary hover:bg-blue-700 text-white text-sm font-semibold">Save Sequence</button>
                    </div>
                </div>
        </div>
    </main>

    <!-- Add Custom Workflow Modal -->
    <div id="addModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-200 dark:border-slate-700 w-full max-w-md mx-4 overflow-hidden transform transition-all">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Add Custom Workflow</h3>
                <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="addForm" onsubmit="submitAddModal(event)">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Workflow Name <span class="text-red-500">*</span></label>
                        <input type="text" id="addWorkflowName" required placeholder="e.g. Document Check" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Trigger</label>
                        <input type="text" id="addTriggerEvent" placeholder="e.g. On Arrival, Custom, Manual Trigger" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to use the default system trigger.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Route / Path</label>
                        <input type="text" id="addWorkflowRoute" placeholder="e.g. security/document-check" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to use the system default route.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-hover rounded-lg transition-colors shadow-sm">Add Workflow</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.getElementById('addWorkflowName').focus();
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            document.getElementById('addForm').reset();
        }
        
        // Expose submit to global scope for the modal
        function submitAddModal(e) {
            e.preventDefault();
            if (window._submitAddWorkflow) window._submitAddWorkflow();
        }

        (function () {
            const list = document.getElementById('workflowStepList');
            const jsonInput = document.getElementById('stepsJson');
            const customStepsInput = document.getElementById('customStepsJson');
            const resetBtn = document.getElementById('resetOrderBtn');
            const addWorkflowBtn = document.getElementById('addWorkflowBtn');
            const originalOrder = Array.from(list.querySelectorAll('.step-item')).map((el) => el.dataset.stepKey);
            const allSteps = <?= json_encode($allSteps ?? []) ?>;
            const customCounterSeed = Date.now();
            let customCounter = 0;
            let draggingItem = null;

            const nameInput = document.getElementById('addWorkflowName');
            const routeInput = document.getElementById('addWorkflowRoute');
            const triggerInput = document.getElementById('addTriggerEvent');

            nameInput.addEventListener('input', (e) => {
                const val = e.target.value.trim().toLowerCase();
                if (!val) return;
                
                const match = allSteps.find(s => s.label.toLowerCase() === val);
                if (match) {
                    routeInput.value = match.db_route || match.route || '';
                    triggerInput.value = match.trigger_event || '';
                }
            });

            function escapeHtml(text) {
                return String(text)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function updateNumbersAndPayload() {
                const items = Array.from(list.querySelectorAll('.step-item'));
                const customSteps = [];
                items.forEach((item, idx) => {
                    const noEl = item.querySelector('.step-no');
                    if (noEl) noEl.textContent = String(idx + 1);
                    if (item.dataset.isCustom === '1') {
                        customSteps.push({
                            key: item.dataset.stepKey,
                            label: item.dataset.stepLabel || '',
                            route: item.dataset.stepRoute || '',
                            api_link: item.dataset.apiLink || '',
                            trigger_event: item.dataset.triggerEvent || '',
                        });
                    }
                });
                jsonInput.value = JSON.stringify(items.map((item) => item.dataset.stepKey));
                customStepsInput.value = JSON.stringify(customSteps);
            }

            function rebuildFromOrder(order) {
                const map = new Map();
                Array.from(list.querySelectorAll('.step-item')).forEach((item) => map.set(item.dataset.stepKey, item));
                list.innerHTML = '';
                order.forEach((key) => {
                    if (map.has(key)) list.appendChild(map.get(key));
                });
                updateNumbersAndPayload();
            }

            function bindStepEvents(item) {
                item.addEventListener('dragstart', () => {
                    draggingItem = item;
                    item.classList.add('opacity-60');
                });
                item.addEventListener('dragend', () => {
                    item.classList.remove('opacity-60');
                    draggingItem = null;
                    updateNumbersAndPayload();
                });
                item.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    if (!draggingItem || draggingItem === item) return;
                    const rect = item.getBoundingClientRect();
                    const midpoint = rect.top + rect.height / 2;
                    if (event.clientY < midpoint) {
                        list.insertBefore(draggingItem, item);
                    } else {
                        list.insertBefore(draggingItem, item.nextSibling);
                    }
                });
            }

            Array.from(list.querySelectorAll('.step-item')).forEach(bindStepEvents);

            function createCustomStepElement(stepKey, label, route, apiLink, triggerEvent, isCustom = '1') {
                const item = document.createElement('li');
                item.className = 'step-item flex items-center justify-between px-4 py-4 bg-white dark:bg-gray-900';
                item.setAttribute('draggable', 'true');
                item.dataset.stepKey = stepKey;
                item.dataset.isCustom = isCustom;
                item.dataset.stepLabel = label;
                item.dataset.stepRoute = route;
                item.dataset.apiLink = apiLink;
                item.dataset.triggerEvent = triggerEvent;
                
                let apiBadge = apiLink ? `<span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[10px] font-bold border border-blue-500/20" title="Webhook attached: ${escapeHtml(apiLink)}">API</span>` : '';
                
                item.innerHTML = `
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-gray-400 cursor-grab">drag_indicator</span>
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold step-no">0</span>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                ${escapeHtml(label)}
                                ${apiBadge}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(triggerEvent || 'Custom Trigger')} • ${escapeHtml(route)}</p>
                        </div>
                    </div>
                `;
                bindStepEvents(item);
                return item;
            }

            window._submitAddWorkflow = function() {
                const label = document.getElementById('addWorkflowName').value.trim();
                let route = document.getElementById('addWorkflowRoute').value.trim();
                const triggerEvent = document.getElementById('addTriggerEvent').value.trim();

                if (!label) return;
                
                const match = allSteps.find(s => s.label.toLowerCase() === label.toLowerCase());
                
                let stepKey;
                let isCustom = '1';
                
                if (match) {
                    stepKey = match.key;
                    isCustom = match.is_custom ? '1' : '0';
                    const items = Array.from(list.querySelectorAll('.step-item'));
                    if (items.some(item => item.dataset.stepKey === stepKey)) {
                        alert('This workflow is already in the sequence.');
                        return;
                    }
                } else {
                    if (!route) {
                        route = '#';
                    }
                    stepKey = `custom_${customCounterSeed}_${customCounter++}`;
                }
                
                const item = createCustomStepElement(stepKey, label, route, '', triggerEvent, isCustom);
                list.appendChild(item);
                updateNumbersAndPayload();
                closeAddModal();
            };

            resetBtn.addEventListener('click', () => rebuildFromOrder(originalOrder));
            updateNumbersAndPayload();

        })();
    </script>
</body>
</html>
