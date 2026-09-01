<?php
$lp = $loginPageSettings ?? [];
$pageTitle = $lp['page_title'] ?? 'GXO - Visitor Management System';
$backgroundImage = trim((string) ($lp['background_image_url'] ?? ''));
if ($backgroundImage === '') {
    $backgroundImage = base_url('assets/images/gxo/login-building.jpg');
}
$backgroundImageCss = str_replace(['\\', '"', "\n", "\r"], ['\\\\', '\"', '', ''], $backgroundImage);

$heading = $lp['heading'] ?? 'Sign In';
$subheading = $lp['subheading'] ?? 'Welcome back! Please enter your details.';
$usernameLabel = $lp['username_label'] ?? 'Username';
$usernamePlaceholder = $lp['username_placeholder'] ?? 'Enter your username';
$passwordLabel = $lp['password_label'] ?? 'Password';
$passwordPlaceholder = $lp['password_placeholder'] ?? 'Enter your password';
$rememberText = $lp['remember_text'] ?? 'Remember me';
$forgotText = $lp['forgot_password_text'] ?? 'Forgot Password?';
$loginButtonText = $lp['login_button_text'] ?? 'Sign In';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <style>
        :root {
            --gxo-orange: #ff3d0b;
            --gxo-dark: #111827;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Montserrat', sans-serif;
            color: var(--gxo-dark);
            background: #eef3f8;
        }

        .login-shell {
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .login-shell::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(90deg, rgba(255,255,255,.54) 0%, rgba(255,255,255,.36) 34%, rgba(255,255,255,.14) 63%, rgba(255,255,255,.04) 100%),
                url("<?= $backgroundImageCss ?>");
            background-size: cover;
            background-position: center;
            transform: scale(1.01);
        }

        .login-shell::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 70% 14%, rgba(255,255,255,.34), transparent 32%);
            pointer-events: none;
        }

        .brand-mark {
            color: var(--gxo-orange);
            letter-spacing: -0.08em;
            line-height: .86;
            text-shadow: 0 10px 28px rgba(255, 61, 11, .15);
        }

        .orange-rule {
            width: 52px;
            height: 3px;
            border-radius: 999px;
            background: var(--gxo-orange);
        }

        .glass-badge {
            background: rgba(20, 24, 31, .58);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,.22);
            box-shadow: 0 18px 44px rgba(15, 23, 42, .18);
        }

        .signin-card {
            background: rgba(255,255,255,.96);
            border: 1px solid rgba(255,255,255,.86);
            box-shadow: 0 28px 80px rgba(15, 23, 42, .20);
        }

        .field-wrap {
            background: #eef4ff;
            border: 1px solid #dbe4f3;
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .field-wrap:focus-within {
            background: #fff;
            border-color: var(--gxo-orange);
            box-shadow: 0 0 0 4px rgba(255, 61, 11, .10);
        }

        .field-wrap input,
        .field-wrap input:focus {
            background: transparent;
            border: 0;
            outline: 0;
            box-shadow: none;
        }

        @media (max-width: 1023px) {
            .login-shell {
                overflow-y: auto;
            }

            .login-shell::before {
                background-image:
                    linear-gradient(180deg, rgba(255,255,255,.64), rgba(255,255,255,.38)),
                    url("<?= $backgroundImageCss ?>");
            }
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <main class="relative z-10 grid min-h-screen grid-cols-1 gap-8 px-6 py-8 lg:grid-cols-[1fr_540px] lg:gap-12 lg:px-12 xl:px-16">
            <section class="flex min-h-[280px] flex-col justify-between lg:min-h-full">
                <div class="max-w-xl pt-4 lg:pt-6">
                    <div class="brand-mark text-[70px] font-black sm:text-[88px] lg:text-[100px]">GXO</div>
                    <h1 class="mt-5 text-3xl font-bold tracking-[-0.04em] text-slate-950 sm:text-4xl">Visitor Management System</h1>
                    <p class="mt-2 text-xl font-medium text-slate-900">by SafeG</p>
                    <div class="orange-rule mt-7"></div>
                    <p class="mt-7 max-w-sm text-lg font-medium leading-8 text-slate-800">
                        Secure. Smart. Seamless.<br>
                        Managing visitors and access<br class="hidden sm:block">
                        with efficiency and safety.
                    </p>
                </div>

                <div class="glass-badge mb-0 hidden w-fit items-center gap-4 rounded-xl px-5 py-4 text-white lg:flex">
                    <div class="grid h-12 w-12 place-items-center rounded-lg border border-white/45">
                        <span class="material-symbols-outlined text-3xl">verified_user</span>
                    </div>
                    <div>
                        <p class="text-base font-bold">Your security is our priority</p>
                        <p class="mt-1 text-sm font-medium text-white/90">All access is monitored and logged</p>
                    </div>
                </div>
            </section>

            <section class="flex items-center justify-center lg:justify-end">
                <div class="signin-card flex min-h-[680px] w-full max-w-[540px] flex-col rounded-[30px] px-7 py-9 sm:px-10 lg:px-12">
                    <div class="text-center">
                        <div class="brand-mark text-[54px] font-black sm:text-[64px]">GXO</div>
                        <p class="mt-3 text-xl font-bold tracking-[-0.03em] text-slate-950 sm:text-2xl">Logistics (Malaysia) Sdn Bhd</p>
                    </div>

                    <div class="mt-14 text-center">
                        <h2 class="text-3xl font-bold tracking-[-0.04em] text-slate-950"><?= esc($heading) ?></h2>
                        <p class="mt-3 text-base font-medium text-slate-500"><?= esc($subheading) ?></p>
                    </div>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                            <?= esc(session()->getFlashdata('error')) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                            <?= esc(session()->getFlashdata('success')) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('auth/attemptLogin') ?>" class="mt-9 space-y-6">
                        <?= csrf_field() ?>

                        <div>
                            <label for="username" class="mb-3 block text-sm font-bold text-slate-950"><?= esc($usernameLabel) ?></label>
                            <div class="field-wrap flex h-14 items-center gap-4 rounded-lg px-4">
                                <span class="material-symbols-outlined text-slate-700">mail</span>
                                <input id="username" name="username" type="text" autocomplete="username" required value="<?= esc(old('username'), 'attr') ?>" placeholder="<?= esc($usernamePlaceholder, 'attr') ?>" class="h-full w-full border-0 p-0 text-base font-semibold text-slate-950 placeholder:text-slate-400">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="mb-3 block text-sm font-bold text-slate-950"><?= esc($passwordLabel) ?></label>
                            <div class="field-wrap flex h-14 items-center gap-4 rounded-lg px-4">
                                <span class="material-symbols-outlined text-slate-700">lock</span>
                                <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="<?= esc($passwordPlaceholder, 'attr') ?>" class="h-full w-full border-0 p-0 text-base font-semibold text-slate-950 placeholder:text-slate-400">
                                <button type="button" id="togglePassword" class="grid h-9 w-9 shrink-0 place-items-center rounded-full text-slate-700 transition hover:bg-white/70" aria-label="Show password">
                                    <span class="material-symbols-outlined text-[22px]">visibility</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-4 text-sm">
                            <label class="flex items-center gap-3 font-medium text-slate-700">
                                <input type="checkbox" name="remember" value="1" class="h-5 w-5 rounded border-slate-300 text-[#ff3d0b] focus:ring-[#ff3d0b]">
                                <span><?= esc($rememberText) ?></span>
                            </label>
                            <a href="<?= base_url('forgot-password') ?>" class="font-semibold text-[#ff3d0b] hover:text-[#d93108]"><?= esc($forgotText) ?></a>
                        </div>

                        <button type="submit" class="flex h-14 w-full items-center justify-center gap-3 rounded-lg bg-[#ff3d0b] text-base font-bold text-white shadow-lg shadow-orange-500/25 transition hover:bg-[#e73508] active:scale-[.99]">
                            <span class="material-symbols-outlined text-[22px]">login</span>
                            <?= esc($loginButtonText) ?>
                        </button>
                    </form>

                    <div class="mt-3 text-right text-sm font-semibold text-slate-500">V1.1</div>

                    <div class="mt-auto pt-10 text-right">
                        <p class="text-sm font-semibold text-slate-600">Powered by</p>
                        <div class="mt-2 flex items-end justify-end gap-1">
                            <span class="text-xl font-semibold text-[#1f8cff]">Safe</span>
                            <span class="text-5xl font-black leading-none text-[#2f7fd7]">G</span>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        togglePassword?.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            togglePassword.querySelector('.material-symbols-outlined').textContent = isPassword ? 'visibility_off' : 'visibility';
        });
    </script>
</body>
</html>
