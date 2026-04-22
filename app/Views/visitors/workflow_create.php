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
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Drag and reorder steps, then save.</p>
                </div>
                <a href="<?= base_url('workflow') ?>" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-800">Back</a>
            </div>

            <form action="<?= base_url('workflow/save') ?>" method="post" id="workflowForm">
                <?= csrf_field() ?>
                <input type="hidden" name="steps_json" id="stepsJson" value="">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                        Drag to reorder
                    </div>
                    <ul id="workflowStepList" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($steps as $index => $step): ?>
                            <li class="step-item flex items-center justify-between px-4 py-4 bg-white dark:bg-gray-900" draggable="true" data-step-key="<?= esc((string) $step['key']) ?>">
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
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" id="resetOrderBtn" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-800">Reset</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-primary hover:bg-blue-700 text-white text-sm font-semibold">Save Sequence</button>
                </div>
            </form>
        </div>
    </main>
    <script>
        (function () {
            const list = document.getElementById('workflowStepList');
            const jsonInput = document.getElementById('stepsJson');
            const resetBtn = document.getElementById('resetOrderBtn');
            const originalOrder = Array.from(list.querySelectorAll('.step-item')).map((el) => el.dataset.stepKey);
            let draggingItem = null;

            function updateNumbersAndPayload() {
                const items = Array.from(list.querySelectorAll('.step-item'));
                items.forEach((item, idx) => {
                    const noEl = item.querySelector('.step-no');
                    if (noEl) noEl.textContent = String(idx + 1);
                });
                jsonInput.value = JSON.stringify(items.map((item) => item.dataset.stepKey));
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

            Array.from(list.querySelectorAll('.step-item')).forEach((item) => {
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
            });

            resetBtn.addEventListener('click', () => rebuildFromOrder(originalOrder));
            updateNumbersAndPayload();
        })();
    </script>
</body>
</html>
