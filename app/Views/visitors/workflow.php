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
            <div class="px-4 md:px-10 py-6 max-w-[1440px] mx-auto w-full">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-5 pb-6 border-b border-border-light dark:border-border-dark mb-6">
                    <div class="flex min-w-72 flex-col gap-2">
                        <h1 class="text-text-main-light dark:text-white text-3xl md:text-4xl font-black leading-tight tracking-[-0.033em]">Visitor Workflows</h1>
                        <p class="text-text-secondary-light dark:text-[#92adc9] text-base font-normal leading-normal max-w-2xl">Manage invitation process sequence for visitors.</p>
                    </div>
                    <a href="<?= base_url('workflow/create') ?>" class="flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-lg shadow-lg shadow-primary/20 transition-all">
                        <span class="material-symbols-outlined text-[20px]">edit</span>
                        <span class="text-sm font-bold leading-normal tracking-[0.015em]">Edit Sequence</span>
                    </a>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="mb-5 rounded-lg border border-green-200 bg-green-50 text-green-700 px-4 py-3 text-sm">
                        <?= esc((string) session()->getFlashdata('success')) ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">
                        <?= esc((string) session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="flex flex-col gap-1 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-border-light dark:border-[#324d67] shadow-sm">
                        <p class="text-text-secondary-light dark:text-[#92adc9] text-sm font-medium">Active Workflows</p>
                        <p class="text-text-main-light dark:text-white text-2xl font-bold leading-tight mt-1"><?= esc((string) $stats['active_workflows']) ?></p>
                        <p class="text-[#0bda5b] text-xs font-medium"><?= esc((string) $stats['active_change']) ?></p>
                    </div>
                    <div class="flex flex-col gap-1 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-border-light dark:border-[#324d67] shadow-sm">
                        <p class="text-text-secondary-light dark:text-[#92adc9] text-sm font-medium">Total Visitors</p>
                        <p class="text-text-main-light dark:text-white text-2xl font-bold leading-tight mt-1"><?= esc((string) $stats['total_visitors']) ?></p>
                        <p class="text-[#0bda5b] text-xs font-medium"><?= esc((string) $stats['visitors_change']) ?></p>
                    </div>
                    <div class="flex flex-col gap-1 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-border-light dark:border-[#324d67] shadow-sm">
                        <p class="text-text-secondary-light dark:text-[#92adc9] text-sm font-medium">Avg. Process Time</p>
                        <p class="text-text-main-light dark:text-white text-2xl font-bold leading-tight mt-1"><?= esc((string) $stats['avg_time']) ?></p>
                        <p class="text-[#0bda5b] text-xs font-medium"><?= esc((string) $stats['time_change']) ?></p>
                    </div>
                    <div class="flex flex-col gap-1 rounded-xl p-5 bg-surface-light dark:bg-surface-dark border border-border-light dark:border-[#324d67] shadow-sm">
                        <p class="text-text-secondary-light dark:text-[#92adc9] text-sm font-medium">Security Alerts</p>
                        <p class="text-text-main-light dark:text-white text-2xl font-bold leading-tight mt-1"><?= esc((string) $stats['alerts']) ?></p>
                        <p class="text-text-secondary-light dark:text-[#92adc9] text-xs font-medium"><?= esc((string) $stats['alerts_text']) ?></p>
                    </div>
                </div>

                <div class="flex flex-1 flex-col bg-surface-light dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark shadow-sm overflow-hidden">
                    <div class="grid grid-cols-12 gap-4 p-4 border-b border-border-light dark:border-border-dark bg-gray-50 dark:bg-[#1a242f] text-xs font-semibold text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider">
                        <div class="col-span-4">Workflow Name</div>
                        <div class="col-span-3">Trigger</div>
                        <div class="col-span-1">Order</div>
                        <div class="col-span-2">Status</div>
                        <div class="col-span-2">Source</div>
                    </div>
                    <div class="flex flex-col">
                        <?php foreach ($workflows as $workflow): ?>
                            <div class="grid grid-cols-12 gap-4 p-4 border-b border-border-light dark:border-border-dark items-center">
                                <div class="col-span-4 flex items-center gap-3">
                                    <div class="size-10 rounded bg-primary/10 flex items-center justify-center text-primary shrink-0">
                                        <span class="material-symbols-outlined"><?= esc((string) $workflow['icon']) ?></span>
                                    </div>
                                    <div>
                                        <p class="text-text-main-light dark:text-white font-medium text-sm"><?= esc((string) $workflow['name']) ?></p>
                                        <p class="text-text-secondary-light dark:text-text-secondary-dark text-xs"><?= esc((string) $workflow['route']) ?></p>
                                    </div>
                                </div>
                                <div class="col-span-3 text-sm"><?= esc((string) $workflow['trigger']) ?></div>
                                <div class="col-span-1"><span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 dark:bg-[#233648] text-xs font-medium"><?= esc((string) ($workflow['step_order'] ?? '')) ?></span></div>
                                <div class="col-span-2"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-500/10 text-green-600 dark:text-green-400 text-xs font-bold border border-green-500/20"><span class="size-1.5 rounded-full bg-green-500"></span><?= esc((string) $workflow['status']) ?></span></div>
                                <div class="col-span-2 text-sm text-text-secondary-light dark:text-text-secondary-dark"><?= esc((string) ($workflow['modified'] ?? '')) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
