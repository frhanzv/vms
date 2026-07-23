<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientNotificationSettingModel extends Model
{
    protected $table            = 'client_notification_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['client_id', 'channel', 'notification_type', 'enabled'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public static function allTypes(): array
    {
        return [
            'invitation_sent'        => 'Visitor Invitation',
            'request_approved'       => 'Request Approved',
            'request_rejected'       => 'Request Rejected',
            'registration_submitted' => 'Registration Submitted',
            'reminder'               => 'Reminder',
            'check_in'               => 'Visitor Check-In',
            'check_out'              => 'Visitor Check-Out',
            'blacklist_flagged'      => 'Blacklist Flagged',
        ];
    }

    public static function allChannels(): array
    {
        return ['email', 'whatsapp', 'telegram'];
    }

    /**
     * Returns full settings matrix for a company.
     * Email defaults to enabled, whatsapp/telegram default to disabled.
     */
    public function getForCompany(int $companyId): array
    {
        $rows = $this->where('client_id', $companyId)->findAll();

        $stored = [];
        foreach ($rows as $row) {
            $stored[$row['channel']][$row['notification_type']] = (int) $row['enabled'];
        }

        $result = [];
        foreach (self::allChannels() as $channel) {
            foreach (self::allTypes() as $type => $label) {
                $default = $channel === 'email' ? 1 : 0;
                $result[$channel][$type] = $stored[$channel][$type] ?? $default;
            }
        }

        return $result;
    }

    /**
     * Check if a specific notification is enabled for a company and channel.
     * Email defaults to enabled, other channels default to disabled.
     */
    public function isEnabled(int $companyId, string $channel, string $notificationType): bool
    {
        $row = $this->where('client_id', $companyId)
                    ->where('channel', $channel)
                    ->where('notification_type', $notificationType)
                    ->first();

        if ($row !== null) {
            return (bool) $row['enabled'];
        }

        return $channel === 'email';
    }

    /**
     * Upserts notification settings for a company.
     * $settings format: ['email' => ['invitation_sent' => 1, ...], 'whatsapp' => [...]]
     */
    public function saveForCompany(int $companyId, array $settings): void
    {
        foreach ($settings as $channel => $types) {
            foreach ($types as $type => $enabled) {
                $existing = $this->where('client_id', $companyId)
                                 ->where('channel', $channel)
                                 ->where('notification_type', $type)
                                 ->first();

                $payload = [
                    'client_id'        => $companyId,
                    'channel'           => $channel,
                    'notification_type' => $type,
                    'enabled'           => $enabled ? 1 : 0,
                ];

                if ($existing) {
                    $this->update($existing['id'], $payload);
                } else {
                    $this->insert($payload);
                }
            }
        }
    }
}
