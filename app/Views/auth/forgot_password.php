<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle ?? 'Forgot Password - SafeG') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
                        "display": ["Manrope", "sans-serif"],
                        "montserrat": ["Montserrat", "sans-serif"],
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white antialiased">
<div class="flex min-h-screen w-full items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="flex items-center gap-3 mb-8 justify-center">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary text-white shadow-lg shadow-primary/30">
                <span class="material-symbols-outlined text-2xl">shield_person</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">SafeG</h1>
        </div>

        <div class="bg-white dark:bg-[#1a2632] border border-slate-200 dark:border-slate-700 rounded-xl p-6 sm:p-8">
            <h2 class="text-2xl font-bold tracking-tight mb-2">Forgot Password</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Enter your Staff ID or IC Number and we'll email a new password to the address on file.</p>

            <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <p class="text-sm text-red-800 dark:text-red-200"><?= esc(session()->getFlashdata('error')) ?></p>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <p class="text-sm text-green-800 dark:text-green-200"><?= esc(session()->getFlashdata('success')) ?></p>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('forgot-password') ?>" method="post" class="flex flex-col gap-5">
                <?= csrf_field() ?>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold" for="identifier">Staff ID or IC Number</label>
                    <input class="form-input rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#101922] h-12 px-4 text-base" id="identifier" name="identifier" type="text" value="<?= old('identifier') ?>" required/>
                </div>
                <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-primary h-14 px-4 text-base font-bold text-white shadow-md shadow-primary/20 hover:bg-blue-600 transition-all">
                    Reset Password
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
            <a class="font-semibold text-primary hover:underline" href="<?= base_url('login') ?>">Back to Login</a>
        </p>
    </div>
</div>
</body>
</html>
