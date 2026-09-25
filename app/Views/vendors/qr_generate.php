<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: "#137fec" }, fontFamily: { sans: ["Montserrat", "sans-serif"] } } } };
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 h-screen flex">

    <?= view('partials/sidebar') ?>

    <main class="flex-1 overflow-y-auto p-4 md:p-8 flex items-center justify-center">
        <div class="max-w-sm w-full bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 text-center">
            <h1 class="text-lg font-bold text-gray-800 dark:text-white mb-1">Vendor Pass QR</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4"><?= esc($vendor['full_name'] ?? '') ?> — <?= esc($vendor['app_no'] ?? '') ?></p>

            <div class="bg-white p-3 rounded-lg inline-block border border-gray-200">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=<?= urlencode($publicUrl) ?>" alt="Vendor Pass QR Code" width="220" height="220"/>
            </div>

            <p class="text-xs text-gray-400 mt-4 break-all"><?= esc($publicUrl) ?></p>

            <div class="flex gap-2 mt-5">
                <a href="<?= base_url('vendors') ?>" class="flex-1 h-10 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700">
                    Back
                </a>
                <a href="<?= esc($publicUrl) ?>" target="_blank" class="flex-1 h-10 rounded-lg bg-primary text-white text-sm font-medium flex items-center justify-center hover:bg-blue-700">
                    Open Link
                </a>
            </div>
        </div>
    </main>
</body>
</html>
