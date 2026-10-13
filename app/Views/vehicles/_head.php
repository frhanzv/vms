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
