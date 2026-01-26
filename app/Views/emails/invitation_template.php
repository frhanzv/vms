<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Invitation - SafeG</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #137fec; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
        .btn { background: #137fec; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
        .info-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .footer { text-align: center; color: #666; margin-top: 30px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛡️ SafeG</h1>
            <h2>Visitor Invitation</h2>
        </div>
        
        <div class="content">
            <p>Dear <strong><?= esc($visitor_name) ?></strong>,</p>
            
            <p>You have been invited to visit <strong><?= esc($company) ?></strong>. Please complete your registration by clicking the button below.</p>
            
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
            
            <div style="text-align: center;">
                <a href="<?= $registration_link ?>" class="btn">Complete Registration</a>
            </div>
            
            <p><strong>Important Notes:</strong></p>
            <ul>
                <li>Please complete your registration before your visit</li>
                <li>Bring a valid ID (IC/Passport) for verification</li>
                <li>This invitation expires on: <strong><?= date('d/m/Y', strtotime($link_expiry)) ?></strong></li>
                <li>Contact security if you have any questions</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>This is an automated message from SafeG Visitor Management System</p>
            <p>© <?= date('Y') ?> SafeG. All rights reserved.</p>
        </div>
    </div>
</body>
</html>