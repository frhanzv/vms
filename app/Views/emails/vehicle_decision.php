<?php
/**
 * Vehicle pass approved / rejected email (KPK "vehicleApprovalAndReject"): same fields and wording, VMS look.
 *
 * @var string $decision 'APPROVED' or 'REJECTED'
 * @var array  $rows     label => value
 */
$approved = $decision === 'APPROVED';
$color    = $approved ? '#059669' : '#dc2626';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Pass - SafeG</title>
</head>
<body style="margin:0; background:#f1f5f9; font-family:Montserrat, Arial, sans-serif; color:#333333; line-height:1.6;">
    <div style="max-width:600px; margin:0 auto; padding:20px;">
        <div style="background:#137fec; color:#ffffff; padding:20px; text-align:center; border-radius:8px 8px 0 0;">
            <h2 style="margin:0; font-size:20px;">We have <?= esc(strtolower($decision)) ?> your Vehicle Pass.</h2>
        </div>
        <div style="background:#f8f9fa; padding:30px; border-radius:0 0 8px 8px;">
            <p style="margin-top:0;">Dear Sir/Madam,</p>
            <p>Your Application has been <strong style="color:<?= $color ?>;"><?= esc($decision) ?></strong>.</p>
            <p>Details as follow:</p>
            <table style="width:100%; background:#ffffff; border-radius:8px; border-collapse:collapse; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                <?php foreach ($rows as $label => $value): ?>
                <tr>
                    <td style="padding:8px 16px; font-size:13px; font-weight:600; width:45%; border-bottom:1px solid #eef2f7;"><?= esc($label) ?></td>
                    <td style="padding:8px 16px; font-size:13px; border-bottom:1px solid #eef2f7;"><?= esc($value !== '' ? $value : '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php if ($approved): ?>
            <p>Please bring this approval slip during collection time.</p>
            <?php endif; ?>
            <p>Application progress status is accessible at <a href="<?= esc($siteUrl, 'attr') ?>" style="color:#137fec;"><?= esc($siteUrl) ?></a></p>
            <p style="margin-bottom:0;">Regards,<br><?= esc($brand) ?></p>
        </div>
        <p style="text-align:center; color:#666666; font-size:12px; margin-top:20px;">This is an automated message from SafeG Visitor Management System</p>
    </div>
</body>
</html>
