<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="<?= csrf_hash() ?>"/>
    <title><?= esc($pageTitle ?? 'Staff Pass - SafeG') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#137fec",
                        secondary: "#3b82f6",
                        success: "#10b981",
                        "background-light": "#f6f7f8",
                        "background-dark": "#111827",
                        "card-light": "#ffffff",
                        "card-dark": "#1f2937",
                    },
                    fontFamily: { display: ["Montserrat", "sans-serif"], sans: ["Montserrat", "sans-serif"] },
                    borderRadius: { DEFAULT: "0.375rem" },
                },
            },
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden transition-colors duration-200">

    <?= view('partials/sidebar') ?>

    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8 bg-background-light dark:bg-background-dark">
        <div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-7xl">

            <?php
            // Pipeline tabs — same order as KPK: list -> process -> printing -> issuance -> closed
            $staffTabs = [
                'staffs'               => ['Staff List', 'badge'],
                'staffs/process-list'  => ['Process', 'manage_accounts'],
                'staffs/printing-list' => ['Printing', 'print'],
                'staffs/issuance-list' => ['Issuance', 'outbox'],
                'staffs/closed-list'   => ['Closed', 'task_alt'],
            ];
            $activeTab = $activeTab ?? '';
            ?>
            <nav class="no-print flex flex-wrap gap-1 mb-6 border-b border-gray-200 dark:border-gray-700 -mt-2">
                <?php foreach ($staffTabs as $path => [$label, $icon]): ?>
                <a href="<?= base_url($path) ?>"
                   class="flex items-center gap-1.5 px-3 py-2 text-xs font-semibold uppercase tracking-wide border-b-2 -mb-px transition-colors <?= $activeTab === $path ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-primary' ?>">
                    <span class="material-symbols-outlined text-[18px]"><?= $icon ?></span><?= $label ?>
                </a>
                <?php endforeach; ?>
            </nav>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-4 flex items-center gap-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 text-sm rounded-lg px-4 py-3">
                    <span class="material-symbols-outlined text-[20px] flex-shrink-0">check_circle</span>
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 flex items-start gap-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 text-sm rounded-lg px-4 py-3">
                    <span class="material-symbols-outlined text-[20px] flex-shrink-0 mt-0.5">error</span>
                    <div>
                        <?php foreach (explode("\n", (string) session()->getFlashdata('error')) as $line): ?>
                            <p><?= esc($line) ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
