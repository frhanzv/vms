<?php

namespace App\Services\Channels;

class TelegramChannel
{
    public function send(int $invitationId, string $notificationType): bool
    {
        // Telegram visitor notifications are not yet implemented.
        return false;
    }
}
