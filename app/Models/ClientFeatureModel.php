<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientFeatureModel extends Model
{
    protected $table            = 'client_features';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['client_id', 'feature_key', 'is_enabled'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Canonical list of all toggleable SafeG features.
    public static function allFeatures(): array
    {
        return [
            'blacklist'           => 'Blacklist Management',
            'invitations'         => 'Invitations / Pre-registration',
            'workflows'           => 'Approval Workflows',
            'staff_pass'          => 'Staff Pass',
            'visitor_card'        => 'Visitor Card Issuance',
            'security_alerts'     => 'Security Alerts',
            'device_management'   => 'Device Management',
            'company_visited'     => 'Company Visited Field (disable to use Visitor Type)',
            'mykad_ocr'           => 'Read MyKad / MyKad OCR',
            'auto_approve_after_workflow' => 'Auto-Approve After Video / Questionnaire',
        ];
    }

    /**
     * Existing features remain enabled by default for backward compatibility.
     * Opt-in workflow behaviour must be explicitly enabled per client.
     */
    public static function defaultEnabled(string $featureKey): bool
    {
        return $featureKey !== 'auto_approve_after_workflow';
    }

    /**
     * Returns all features for a client with their configured or declared default state.
     */
    public function getForClient(int $clientId): array
    {
        $rows = $this->where('client_id', $clientId)->findAll();
        $stored = array_column($rows, 'is_enabled', 'feature_key');

        $result = [];
        foreach (self::allFeatures() as $key => $label) {
            $result[] = [
                'feature_key' => $key,
                'label'       => $label,
                'is_enabled'  => isset($stored[$key])
                    ? (int) $stored[$key]
                    : (int) self::defaultEnabled($key),
            ];
        }
        return $result;
    }

    /** @deprecated Use getForClient() */
    public function getForCompany(int $companyId): array
    {
        return $this->getForClient($companyId);
    }

    public function saveForClient(int $clientId, array $features): void
    {
        foreach ($features as $key => $enabled) {
            $enabled = $enabled ? 1 : 0;
            $existing = $this->where('client_id', $clientId)
                             ->where('feature_key', $key)
                             ->first();

            if ($existing) {
                $this->update($existing['id'], ['is_enabled' => $enabled]);
            } elseif ($enabled !== (int) self::defaultEnabled($key)) {
                $this->insert([
                    'client_id'   => $clientId,
                    'feature_key' => $key,
                    'is_enabled'  => 0,
                ]);
            }
        }
    }

    /** @deprecated Use saveForClient() */
    public function saveForCompany(int $companyId, array $features): void
    {
        $this->saveForClient($companyId, $features);
    }

    public function isEnabled(int $clientId, string $featureKey): bool
    {
        $row = $this->where('client_id', $clientId)
                    ->where('feature_key', $featureKey)
                    ->first();

        return $row === null
            ? self::defaultEnabled($featureKey)
            : (bool) $row['is_enabled'];
    }
}
