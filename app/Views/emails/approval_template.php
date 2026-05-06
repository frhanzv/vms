<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Invitation - SafeG</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php
    $template = $template ?? [];
    $brandName = $template['brand_name'] ?? 'SafeG';
    $headerTitle = $template['header_title'] ?? 'Visitor Invitation';
    $introLine = $intro_line ?? ('Your visitor request to ' . ($company ?? '') . ' has been approved. Please use the QR pass below for check-in.');
    $buttonText = $template['button_text'] ?? 'Complete Registration';
    $notesTitle = $template['notes_title'] ?? 'Important Notes';
    $notesItems = $notes_items ?? [];
    $footerText = $template['footer_text'] ?? 'This is an automated message from SafeG Visitor Management System';
    $crudColors = $custom_colors ?? [];
    $primaryColor = $crudColors['primary_color'] ?? ($template['primary_color'] ?? '#137fec');
    $contentBgColor = $crudColors['content_bg_color'] ?? ($template['content_bg_color'] ?? '#f8f9fa');
    $textColor = $crudColors['text_color'] ?? ($template['text_color'] ?? '#333333');
    ?>
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; line-height: 1.6; color: <?= esc($textColor) ?>; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: <?= esc($primaryColor) ?>; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: <?= esc($contentBgColor) ?>; padding: 30px; border-radius: 0 0 8px 8px; }
        .btn { background: <?= esc($primaryColor) ?>; color: white !important; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
        .info-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .footer { text-align: center; color: #666; margin-top: 30px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <?php 
            $logoSrc = '';
            if (!empty($custom_logo_cid)) {
                $logoSrc = 'cid:' . $custom_logo_cid;
            } elseif (!empty($custom_logo)) {
                $logoSrc = strpos($custom_logo, 'http') === 0 ? $custom_logo : base_url($custom_logo);
            }
            if ($logoSrc !== ''):
            ?>
                <img src="<?= esc($logoSrc) ?>" alt="<?= esc($brandName) ?> Logo" style="max-height: 80px; display: block; margin: 0 auto; margin-bottom: 10px;">
            <?php endif; ?>
            <h1 style="margin-top: 0; font-size: 24px;">🛡️ <?= esc($brandName) ?></h1>
            <h2><?= esc($headerTitle) ?></h2>
        </div>
        
        <div class="content">
            <?php if (!empty($custom_body_html)): ?>
                <div><?= $custom_body_html ?></div>
            <?php else: ?>
                <p><?= esc($introLine) ?></p>
            <?php endif; ?>
            
            <div class="info-box">
                <h3>Visit Details:</h3>
                <p><strong>Company:</strong> <?= esc($company) ?></p>
                <p><strong>Location:</strong> <?= esc($location) ?></p>
                <p><strong>Purpose:</strong> <?= esc($reason) ?></p>
                <?php if (!empty($other_reason)): ?>
                <p><strong>Additional Details:</strong> <?= esc($other_reason) ?></p>
                <?php endif; ?>
                <p><strong>Invited By:</strong> <?= esc($invited_by) ?></p>
                
                <?php if (!empty($schedules)): ?>
                <h4>Visit Schedule(s):</h4>
                <?php foreach ($schedules as $schedule): ?>
                <p>📅 <strong>From:</strong> <?= date('d/m/Y H:i', strtotime($schedule['date_from'])) ?>
                   <strong>To:</strong> <?= date('d/m/Y H:i', strtotime($schedule['date_to'])) ?></p>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <?php
                $qrImgSrc = '';
                if (!empty($qr_code_image_url)) {
                    $qrImgSrc = $qr_code_image_url;
                } elseif (!empty($qr_code_cid)) {
                    $qrImgSrc = 'cid:' . $qr_code_cid;
                } elseif (!empty($qr_code_base64)) {
                    $qrImgSrc = strpos($qr_code_base64, 'data:image') === 0
                        ? $qr_code_base64
                        : ('data:image/png;base64,' . $qr_code_base64);
                }
            ?>
            <?php if (!empty($qrImgSrc)): ?>
            <div style="text-align: center; margin: 30px 0;">
                <p style="margin-bottom: 10px; font-weight: bold;">Your Digital Pass</p>
                <img src="<?= esc($qrImgSrc) ?>" alt="Visitor QR Code" style="max-width: 200px; border: 1px solid #ddd; padding: 10px; border-radius: 8px; background: white;">
                <?php if (!empty($qr_code_text)): ?>
                    <p style="font-size: 12px; color: #666; margin-top: 6px; margin-bottom: 0;">Pass ID: <?= esc($qr_code_text) ?></p>
                <?php endif; ?>
                <p style="font-size: 12px; color: #666; margin-top: 10px;">Please present this QR code during check-in.</p>
            </div>
            <?php endif; ?>
            
            <p><strong><?= esc($notesTitle) ?>:</strong></p>
            <ul>
                <?php foreach ($notesItems as $note): ?>
                <li><?= esc($note) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="footer">
            <p><?= esc($footerText) ?></p>
            <p>© <?= date('Y') ?> <?= esc($brandName) ?>. All rights reserved.</p>
        </div>
    </div>
</body>
</html>