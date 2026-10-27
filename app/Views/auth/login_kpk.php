<?php
/**
 * KPK login (Safe-G "pages/login"): padlock background, ACMS logo, underline inputs, navy Sign In,
 * EPIC + KSB logos top right, SafeG logo at the bottom, contact details bottom left.
 * Markup and styles follow angular/src/app/pages/content-pages/login in safeg_kpk.
 */
$lp = $loginPageSettings ?? [];
$pageTitle = $lp['page_title'] ?? 'SafeG';
$backgroundImage = trim((string) ($lp['background_image_url'] ?? '')) ?: base_url('assets/images/safeg/login_bg.png');
$backgroundImageCss = str_replace(['\\', '"', "\n", "\r"], ['\\\\', '\"', '', ''], $backgroundImage);
$img = static fn(string $f): string => base_url('assets/images/safeg/' . $f);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; height: 100%; }
        body { font-family: 'Montserrat', sans-serif; color: #ebeff7; }

        #login {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            background-image: url("<?= $backgroundImageCss ?>");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: 50% 50%;
        }

        .banner-container {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            max-width: 380px;
            padding: 0 16px;
            transform: translate(-50%, -50%);
        }

        .logo { display: block; width: 100%; max-width: 320px; margin-bottom: 1rem; }

        .error { margin: 0 0 .75rem; font-size: 12px; color: #ff8a8a; text-align: center; }
        .notice { margin: 0 0 .75rem; font-size: 12px; color: #9ef0c4; text-align: center; }

        .field {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
            padding: 1rem 0;
            border: 0;
            border-bottom: 1px solid #ebeff7;
            border-radius: 0;
            background: transparent;
            color: #ebeff7;
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            font-weight: 300;
            outline: none;
        }
        .field::placeholder { color: #ebeff7; opacity: 1; }
        .field:focus { border-bottom-color: #ffffff; }
        .field:-webkit-autofill,
        .field:-webkit-autofill:focus {
            -webkit-text-fill-color: #ebeff7;
            -webkit-box-shadow: 0 0 0 1000px transparent inset;
            transition: background-color 9999s ease-out 0s;
        }

        .link { font-size: 11px; color: #ebeff7; text-decoration: none; cursor: pointer; }
        .link:hover { font-weight: 700; }

        .sign-in {
            width: 100%;
            margin-top: 3rem;
            padding: 10px 0;
            border: none;
            border-radius: 12px;
            background: #1C2158;
            color: #ebeff7;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .2);
            transition: all .3s ease;
        }
        .sign-in:hover { background: #242a6b; box-shadow: 0 6px 12px rgba(0, 0, 0, .3); transform: translateY(-2px); }
        .sign-in:active { background: #181b4c; box-shadow: 0 3px 6px rgba(0, 0, 0, .25); transform: translateY(1px); }

        .below { display: flex; justify-content: flex-end; margin-top: .5rem; text-align: right; }
        .version { margin-top: .25rem; font-size: 13px; color: #000000; }

        .top-right-images { position: absolute; top: 10px; right: 10px; display: flex; align-items: flex-start; }
        .top-image1 { height: 25px; width: auto; margin-left: 5px; }
        .top-image2 { width: 40px; height: auto; margin-top: -3px; margin-left: 5px; }

        .bottom-left-text { position: absolute; bottom: 25px; left: 30px; line-height: 1.5; }
        .bottom-left-text p { margin: 0; }
        .bottom-left-text .bolder { font-size: 10px; font-weight: 800; color: #ffffff; }
        .bottom-left-text .slim-text { font-size: 9px; font-weight: 300; color: #e6e4e4; }

        .bottom-right-logo { position: absolute; bottom: 1px; left: 67%; width: 250px; max-height: 200px; transform: translateX(-50%); }

        @media (max-width: 768px) {
            .version { color: #ffffff; }
            .bottom-right-logo { left: 50%; width: 160px; }
            .bottom-left-text { left: 16px; bottom: 120px; }
        }
    </style>
</head>
<body>
<section id="login">
    <div class="top-right-images">
        <img src="<?= $img('epic_logo.png') ?>" alt="EPIC" class="top-image1">
        <img src="<?= $img('ksb_small_logo.png') ?>" alt="KSB" class="top-image2">
    </div>

    <div class="banner-container">
        <form method="post" action="<?= base_url('auth/attemptLogin') ?>">
            <?= csrf_field() ?>
            <img src="<?= $img('login_logo1.png') ?>" alt="ACMS - Access Control Management System" class="logo">

            <?php if ($msg = session()->getFlashdata('error')): ?>
                <p class="error" role="alert"><?= esc($msg) ?></p>
            <?php endif; ?>
            <?php if ($msg = session()->getFlashdata('success')): ?>
                <p class="notice"><?= esc($msg) ?></p>
            <?php endif; ?>

            <input type="text" name="username" class="field" placeholder="USERNAME" aria-label="Username" autocomplete="username" required value="<?= esc(old('username'), 'attr') ?>">
            <input type="password" name="password" class="field" placeholder="PASSWORD" aria-label="Password" autocomplete="current-password" required style="margin-bottom: .25rem">

            <a href="<?= base_url('forgot-password') ?>" class="link">RECOVERY PASSWORD</a>

            <button type="submit" class="sign-in">Sign In</button>

            <div class="below">
                <div>
                    <a href="<?= base_url('register') ?>" class="link" style="font-size: 13px">Register</a>
                    <div class="version">Version 2.0.3</div>
                </div>
            </div>
        </form>
    </div>

    <img src="<?= $img('safeg_logo3.png') ?>" alt="SafeG" class="bottom-right-logo">

    <div class="bottom-left-text">
        <p class="bolder">Contact Information:</p>
        <p class="slim-text">Counter Pass: +6098602312</p>
        <p class="slim-text">Email: passksb@epicgroup.com.my</p>
    </div>
</section>
</body>
</html>
