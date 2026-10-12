<?php
/** Vendor emails (invitation / registration reminder). Edited in Config > Email Template. */
$brandName   = $brand_name ?? 'SafeG';
$primary     = $primary_color ?? '#137fec';
$contentBg   = $content_bg_color ?? '#f8f9fa';
$textColor   = $text_color ?? '#333333';
$logo        = trim((string) ($logo_src ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($header_title ?? 'SafeG') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; line-height: 1.6; color: <?= esc($textColor) ?>; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: <?= esc($primary) ?>; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: <?= esc($contentBg) ?>; padding: 30px; border-radius: 0 0 8px 8px; }
        .btn { background: <?= esc($primary) ?>; color: white !important; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
        .footer { text-align: center; color: #666; margin-top: 30px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <?php if ($logo !== ''): ?>
                <img src="<?= esc($logo) ?>" alt="<?= esc($brandName) ?> Logo" style="max-height: 80px; display: block; margin: 0 auto 10px;">
            <?php endif; ?>
            <h1 style="margin-top: 0; font-size: 24px;">🛡️ <?= esc($brandName) ?></h1>
            <h2><?= esc($header_title ?? '') ?></h2>
        </div>
        <div class="content">
            <div><?= $body_html ?? '' ?></div>
            <?php if (! empty($button_url)): ?>
                <p style="text-align:center;"><a class="btn" href="<?= htmlspecialchars($button_url, ENT_QUOTES) ?>"><?= esc($button_text ?? 'Open') ?></a></p>
            <?php endif; ?>
        </div>
        <div class="footer"><?= esc($footer_text ?? 'This is an automated message from SafeG Visitor Management System') ?></div>
    </div>
</body>
</html>
