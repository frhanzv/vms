<?php

namespace App\Services\Channels;

use App\Libraries\InvitationEmailSender;

class EmailChannel
{
    public function send(int $invitationId, string $notificationType): bool
    {
        $sender = new InvitationEmailSender();

        switch ($notificationType) {
            case 'invitation_sent':
                return $sender->send($invitationId);
            case 'request_approved':
                return $sender->sendApproval($invitationId);
            default:
                return false;
        }
    }
}
