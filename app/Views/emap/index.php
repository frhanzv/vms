<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: { sans: ["Montserrat", "sans-serif"] },
                },
            },
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="<?= base_url('assets/emap/emap.css') ?>"/>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white overflow-hidden">
<div class="flex h-screen w-full">
    <?= view('partials/sidebar') ?>
    <main class="flex-1 h-screen min-w-0 overflow-hidden bg-background-light dark:bg-background-dark">
        <div
            id="emap-root"
            class="h-full"
            data-bootstrap-url="<?= esc(base_url('api/e-map/bootstrap')) ?>"
            data-live-url="<?= esc(base_url('api/e-map/live')) ?>"
            data-map-url-template="<?= esc(base_url('api/e-map/maps/__MAP_ID__')) ?>"
            data-can-design="<?= $canDesign ? '1' : '0' ?>"
            data-can-publish="<?= $canPublish ? '1' : '0' ?>"
        >
            <div class="h-full flex items-center justify-center text-slate-500">
                <div class="text-center">
                    <span class="material-symbols-outlined text-4xl animate-pulse">map</span>
                    <p class="mt-2 text-sm font-semibold">Loading E-Map…</p>
                </div>
            </div>
        </div>
    </main>
</div>
<script type="module" src="<?= base_url('assets/emap/emap.js') ?>"></script>
</body>
</html>
