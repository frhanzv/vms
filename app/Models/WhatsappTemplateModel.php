<?php

namespace App\Models;

use CodeIgniter\Model;

class WhatsappTemplateModel extends Model
{
    protected $table            = 'whatsapp_templates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['company_id', 'notification_type', 'template_name', 'language_code'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Returns all templates for a company, keyed by notification_type.
     * Missing types return empty template_name with default language.
     */
    public function getForCompany(int $companyId): array
    {
        $rows = $this->where('company_id', $companyId)->findAll();
        $stored = array_column($rows, null, 'notification_type');

        $result = [];
        foreach (ClientNotificationSettingModel::allTypes() as $type => $label) {
            $result[$type] = [
                'template_name' => $stored[$type]['template_name'] ?? '',
                'language_code' => $stored[$type]['language_code'] ?? 'en_US',
            ];
        }

        return $result;
    }

    /**
     * Returns a single template for a company and notification type.
     * Returns null if not configured.
     */
    public function getTemplate(int $companyId, string $notificationType): ?array
    {
        $row = $this->where('company_id', $companyId)
                    ->where('notification_type', $notificationType)
                    ->first();

        if ($row === null || empty($row['template_name'])) {
            return null;
        }

        return $row;
    }

    /**
     * Upserts WhatsApp templates for a company.
     * $templates format: ['invitation_sent' => ['template_name' => '...', 'language_code' => 'en_US'], ...]
     */
    public function saveForCompany(int $companyId, array $templates): void
    {
        foreach ($templates as $type => $data) {
            $existing = $this->where('company_id', $companyId)
                             ->where('notification_type', $type)
                             ->first();

            $payload = [
                'company_id'        => $companyId,
                'notification_type' => $type,
                'template_name'     => $data['template_name'] ?? '',
                'language_code'     => $data['language_code'] ?? 'en_US',
            ];

            if ($existing) {
                $this->update($existing['id'], $payload);
            } else {
                $this->insert($payload);
            }
        }
    }
}
