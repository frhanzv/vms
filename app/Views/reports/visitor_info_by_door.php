<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Montserrat", "sans-serif"],
                        "sans": ["Montserrat", "sans-serif"]
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased overflow-hidden">
<div class="flex h-screen w-full flex-col">
    <div class="flex flex-1 overflow-hidden">
        <?= view('reports/partials/report_sidebar', ['current' => $current]) ?>
        <main class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark custom-scrollbar p-6 lg:p-10">
            <div class="mx-auto max-w-5xl flex flex-col gap-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-primary mb-1">Reports</p>
                    <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Visitor Info By Door</h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Use this report to analyse visitors by entrance or lane once filters are connected.</p>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-10 text-center">
                    <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600 mb-4 block">door_front</span>
                    <p class="text-slate-600 dark:text-slate-300 font-semibold">Placeholder report</p>
                    <p class="text-sm text-slate-500 mt-2">Implement <code class="text-xs bg-slate-100 dark:bg-slate-800 px-1 rounded">VisitorInfoByDoor::generate</code> when door-level rules are ready.</p>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
