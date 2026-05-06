<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Approval - SafeG') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#137fec",
                        "primary-hover": "#0f66be",
                        "background-light": "#f6f7fb",
                        "background-dark": "#101922",
                    },
                    fontFamily: { sans: ["Montserrat", "sans-serif"] },
                },
            },
        };
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark dark:text-white font-sans min-h-screen flex flex-col">
    <header class="border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900">
        <div class="max-w-lg mx-auto px-4 py-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-2xl">verified</span>
            <span class="text-lg font-bold text-slate-900 dark:text-white">SafeG</span>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm p-8 text-center space-y-4">
            <div class="mx-auto size-16 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-4xl">how_to_vote</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Approval &amp; check-in</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                Please wait for reception or your host to approve this visit. When you are cleared, continue to receive your entry QR or pass.
            </p>
            <?php if (! empty($next_after_approval_url)): ?>
                <a href="<?= esc($next_after_approval_url) ?>"
                   class="inline-flex items-center justify-center gap-2 w-full mt-4 px-4 py-3 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary-hover transition-colors">
                    <span class="material-symbols-outlined text-xl">qr_code_2</span>
                    Continue
                </a>
            <?php else: ?>
                <p class="text-xs text-slate-500 dark:text-slate-500 mt-2">Invitation link incomplete — open this step from your invitation email.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
