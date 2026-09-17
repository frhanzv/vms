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
    $introLine = $intro_line ?? ('You have been invited to visit ' . ($company ?? '') . '. Please complete your registration by clicking the button below.');
    $buttonText = $template['button_text'] ?? 'Complete Registration';
    $notesTitle = $template['notes_title'] ?? 'Important Notes';
    $notesItems = $notes_items ?? [];
    $footerText = $template['footer_text'] ?? 'This is an automated message from SafeG Visitor Management System';
    $crudColors = $custom_colors ?? [];
    $primaryColor = $crudColors['primary_color'] ?? ($template['primary_color'] ?? '#137fec');
    $contentBgColor = $crudColors['content_bg_color'] ?? ($template['content_bg_color'] ?? '#f8f9fa');
    $textColor = $crudColors['text_color'] ?? ($template['text_color'] ?? '#333333');
    $detailFields = array_merge([
        'company' => true,
        'location' => true,
        'reason' => true,
        'invited_by' => true,
        'schedule' => true,
        'host_contact' => false,
        'visitor_type' => false,
    ], $detail_fields ?? []);
    ?>
    <style>
        body { margin: 0; padding: 0; background: #ffffff; font-family: 'Montserrat', Arial, sans-serif; line-height: 1.6; color: <?= esc($textColor) ?>; }
        .container { max-width: 600px; margin: 0 auto; padding: 18px 12px; }
        .email-shell { background: <?= esc($contentBgColor) ?>; border-radius: 4px; overflow: hidden; }
        .header { background: <?= esc($primaryColor) ?>; color: white; padding: 26px 24px 28px; text-align: center; }
        .content { padding: 32px 30px 18px; font-size: 13px; }
        .btn { background: <?= esc($primaryColor) ?>; color: white !important; padding: 14px 28px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 0; font-weight: 700; }
        .info-box { background: white; padding: 24px 22px; border-radius: 8px; margin: 22px 0 28px; box-shadow: none; }
        .footer { text-align: center; color: #777; margin-top: 28px; font-size: 11px; }
        .raw-link { color: <?= esc($primaryColor) ?>; word-break: break-all; }
        .header-logo { max-height: 42px; max-width: 160px; display: block; margin: 0 auto 10px; border: 0; }
        .brand-fallback { font-size: 18px; font-weight: 700; line-height: 1.2; margin-bottom: 10px; }
        .header-title { margin: 0; font-size: 17px; font-weight: 700; line-height: 1.3; }
        .details-title { margin: 0 0 18px; font-size: 16px; line-height: 1.3; }
        .detail-line { margin: 0 0 10px; }
        .notes-list { margin: 0 0 0 22px; padding: 0; font-size: 12px; line-height: 1.7; }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-shell">
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
                <img src="<?= esc($logoSrc) ?>" alt="<?= esc($brandName) ?> Logo" class="header-logo">
            <?php endif; ?>
            <?php if ($logoSrc === ''): ?>
                <div class="brand-fallback">🛡️ <?= esc($brandName) ?></div>
            <?php endif; ?>
            <h2 class="header-title"><?= esc($headerTitle) ?></h2>
        </div>
        
        <div class="content">
            <?php if (!empty($custom_body_html)): ?>
                <div><?= $custom_body_html ?></div>
            <?php else: ?>
                <p style="margin: 0 0 18px;">Dear <?= esc($visitor_name) ?>,</p>
                <p style="margin: 0 0 18px;"><?= esc($introLine) ?></p>
                <p style="margin: 0 0 20px;"><a href="<?= esc($registration_link) ?>" class="raw-link"><?= esc($registration_link) ?></a></p>
                <p style="margin: 0 0 20px;">Thank you.</p>
            <?php endif; ?>
            
            <div class="info-box">
                <h3 class="details-title">Visit Details:</h3>
                <?php if ($detailFields['invited_by']): ?><p class="detail-line"><strong>Invited By:</strong> <?= esc($invited_by ?: 'Not specified') ?></p><?php endif; ?>
                <?php if ($detailFields['host_contact'] || !empty($host_contact)): ?><p class="detail-line"><strong>Contact No. of Host:</strong> <?= esc($host_contact ?: 'Not specified') ?></p><?php endif; ?>
                <?php if ($detailFields['visitor_type'] || !empty($visitor_type)): ?><p class="detail-line"><strong>Visitor Type:</strong> <?= esc($visitor_type ?: 'Not specified') ?></p><?php endif; ?>
                <?php if ($detailFields['reason']): ?><p class="detail-line"><strong>Purpose:</strong> <?= esc($reason ?: 'Not specified') ?></p><?php endif; ?>
                <?php if ($detailFields['company']): ?><p class="detail-line"><strong>Company:</strong> <?= esc($company ?: 'Not specified') ?></p><?php endif; ?>
                <?php if ($detailFields['location']): ?><p class="detail-line"><strong>Location:</strong> <?= esc($location ?: 'Not specified') ?></p><?php endif; ?>
                <?php if (!empty($other_reason)): ?>
                <p class="detail-line"><strong>Additional Details:</strong> <?= esc($other_reason) ?></p>
                <?php endif; ?>
                
                <?php if ($detailFields['schedule'] && !empty($schedules)): ?>
                <p class="detail-line" style="margin-top: 16px;"><strong>Visit Schedule(s):</strong></p>
                <?php foreach ($schedules as $schedule): ?>
                <?php
                    $fromRaw = (string) ($schedule['date_from'] ?? '');
                    $toRaw   = (string) ($schedule['date_to'] ?? '');
                    $fromTs  = strtotime($fromRaw);
                    $toTs    = strtotime($toRaw);
                    $fromDisp = $fromTs ? date('d/m/Y H:i', $fromTs) : $fromRaw;
                    $toDisp   = $toTs ? date('d/m/Y H:i', $toTs) : $toRaw;
                ?>
                <p class="detail-line">📅 <strong>From:</strong> <?= esc($fromDisp) ?>
                   <strong>To:</strong> <?= esc($toDisp) ?></p>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div style="text-align: center; margin-bottom: 30px;">
                <a href="<?= esc($registration_link) ?>" class="btn"><?= esc($buttonText) ?></a>
            </div>
            
            <p style="margin: 0 0 8px;"><strong><?= esc($notesTitle) ?>:</strong></p>
            <ul class="notes-list">
                <?php foreach ($notesItems as $note): ?>
                <li><?= esc($note) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        </div>
        
        <div class="footer">
            <p><?= esc($footerText) ?></p>
            <p>© <?= date('Y') ?> <?= esc($brandName) ?>. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
