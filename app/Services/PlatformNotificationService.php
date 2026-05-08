<?php

namespace App\Services;

use App\Models\UserModel;

/**
 * Sends platform-level alert emails to all active superadmins (e.g. Ahmad / Bytespace).
 * Not for per-company visitor events — only for system-wide concerns.
 */
class PlatformNotificationService
{
    protected UserModel $userModel;
    protected \Config\Email $emailConfig;

    public function __construct()
    {
        $this->userModel   = new UserModel();
        $this->emailConfig = config('Email');
    }

    /**
     * Notify all superadmins about a platform-level event.
     *
     * @param string $subject  Email subject line
     * @param string $body     Plain-text or HTML body
     * @param array<string, mixed> $context  Optional extra data logged alongside the alert
     */
    public function notify(string $subject, string $body, array $context = []): void
    {
        $superadmins = $this->userModel->getSuperAdmins();

        if (empty($superadmins)) {
            log_message('warning', 'PlatformNotificationService: no active superadmins found to notify.');
            return;
        }

        if (! empty($context)) {
            log_message('info', 'PlatformNotificationService: ' . $subject . ' | context: ' . json_encode($context));
        }

        foreach ($superadmins as $admin) {
            $this->sendEmail($admin['email'], $admin['full_name'], $subject, $body);
        }
    }

    /**
     * Shorthand: notify about a failed notification delivery.
     */
    public function notifyDeliveryFailure(int $invitationId, string $channel, string $notificationType): void
    {
        $subject = "[SafeG Alert] Notification delivery failed";
        $body    = "A notification could not be delivered.\n\n"
                 . "Invitation ID : {$invitationId}\n"
                 . "Channel       : {$channel}\n"
                 . "Type          : {$notificationType}\n\n"
                 . "Please check the application logs for details.";

        $this->notify($subject, $body, [
            'invitation_id'     => $invitationId,
            'channel'           => $channel,
            'notification_type' => $notificationType,
        ]);
    }

    /**
     * Shorthand: notify when a new company is created.
     */
    public function notifyNewCompany(int $companyId, string $companyName): void
    {
        $subject = "[SafeG] New company registered: {$companyName}";
        $body    = "A new client company has been created on the SafeG platform.\n\n"
                 . "Company ID   : {$companyId}\n"
                 . "Company Name : {$companyName}";

        $this->notify($subject, $body, [
            'company_id'   => $companyId,
            'company_name' => $companyName,
        ]);
    }

    /**
     * Shorthand: notify when a WhatsApp credential check fails (e.g. 401 from Meta API).
     */
    public function notifyCredentialFailure(int $companyId, string $channel): void
    {
        $subject = "[SafeG Alert] Messaging credential failure — {$channel}";
        $body    = "A messaging credential check failed for a client company.\n\n"
                 . "Company ID : {$companyId}\n"
                 . "Channel    : {$channel}\n\n"
                 . "The client's access token may be expired or revoked. "
                 . "Please advise them to update their credentials in the Config panel.";

        $this->notify($subject, $body, [
            'company_id' => $companyId,
            'channel'    => $channel,
        ]);
    }

    protected function sendEmail(string $toEmail, string $toName, string $subject, string $body): void
    {
        try {
            $email = \Config\Services::email();
            $email->initialize([
                'protocol'    => $this->emailConfig->protocol,
                'SMTPHost'    => $this->emailConfig->SMTPHost,
                'SMTPUser'    => $this->emailConfig->SMTPUser,
                'SMTPPass'    => $this->emailConfig->SMTPPass,
                'SMTPPort'    => $this->emailConfig->SMTPPort,
                'SMTPCrypto'  => $this->emailConfig->SMTPCrypto,
                'SMTPTimeout' => $this->emailConfig->SMTPTimeout,
                'charset'     => $this->emailConfig->charset,
                'newline'     => $this->emailConfig->newline,
                'CRLF'        => $this->emailConfig->CRLF,
            ]);
            $email->setMailType('text');
            $email->setFrom($this->emailConfig->fromEmail, $this->emailConfig->fromName);
            $email->setTo($toEmail, $toName);
            $email->setSubject($subject);
            $email->setMessage($body);
            $email->send();

            log_message('info', "PlatformNotificationService: alert sent to {$toEmail} — {$subject}");
        } catch (\Exception $e) {
            log_message('error', "PlatformNotificationService: failed to send to {$toEmail} — " . $e->getMessage());
        }
    }
}
