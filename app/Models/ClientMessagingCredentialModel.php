<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientMessagingCredentialModel extends Model
{
    protected $table            = 'client_messaging_credentials';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['company_id', 'channel', 'phone_number_id', 'access_token', 'is_active'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public static function allChannels(): array
    {
        return ['whatsapp', 'telegram'];
    }

    public function getForCompany(int $companyId, string $channel): ?array
    {
        return $this->where('company_id', $companyId)
                    ->where('channel', $channel)
                    ->first();
    }

    public function saveCredentials(int $companyId, string $channel, array $data): void
    {
        $existing = $this->where('company_id', $companyId)
                         ->where('channel', $channel)
                         ->first();

        $payload = array_merge(['company_id' => $companyId, 'channel' => $channel], $data);

        if ($existing) {
            $this->update($existing['id'], $payload);
        } else {
            $this->insert($payload);
        }
    }

    public function isActive(int $companyId, string $channel): bool
    {
        $row = $this->where('company_id', $companyId)
                    ->where('channel', $channel)
                    ->first();

        return $row !== null && (bool) $row['is_active'];
    }
}
