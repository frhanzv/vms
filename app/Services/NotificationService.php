<?php

namespace App\Services;

use App\Models\InvitationModel;
use App\Models\ClientNotificationSettingModel;
use App\Services\Channels\EmailChannel;
use App\Services\Channels\WhatsAppChannel;
use App\Services\Channels\TelegramChannel;

class NotificationService
{
    protected InvitationModel $invitationModel;
    protected ClientNotificationSettingModel $settingModel;

    public function __construct()
    {
        $this->invitationModel = new InvitationModel();
        $this->settingModel    = new ClientNotificationSettingModel();
    }

    /**
     * Dispatches a notification to all enabled channels for the invitation's company.
     * Returns the email channel result (or true if email is disabled) so callers can
     * continue tracking email success the same way they did before.
     */
    public function dispatch(int $invitationId, string $notificationType): bool
    {
        $invitation = $this->invitationModel->find($invitationId);
        if (! $invitation) {
            log_message('error', "NotificationService: invitation {$invitationId} not found");
            return false;
        }

        $companyId = (int) $invitation['company'];

        $channels = [
            'email'    => new EmailChannel(),
            'whatsapp' => new WhatsAppChannel(),
            'telegram' => new TelegramChannel(),
        ];

        $emailResult = true;

        foreach ($channels as $channel => $handler) {
            if (! $this->settingModel->isEnabled($companyId, $channel, $notificationType)) {
                continue;
            }

            $result = $handler->send($invitationId, $notificationType);

            if ($channel === 'email') {
                $emailResult = $result;
            }
        }

        return $emailResult;
    }
}
