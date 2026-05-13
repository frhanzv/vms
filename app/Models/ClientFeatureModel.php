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
    protected $allowedFields    = ['company_id', 'feature_key', 'is_enabled'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Canonical list of all toggleable SafeG features.
    // Absence in client_features = enabled (default on).
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
        ];
    }

    /**
     * Returns all features for a company with their enabled state.
     * Features with no DB record default to enabled (1).
     */
    public function getForCompany(int $companyId): array
    {
        $rows = $this->where('company_id', $companyId)->findAll();
        $stored = array_column($rows, 'is_enabled', 'feature_key');

        $result = [];
        foreach (self::allFeatures() as $key => $label) {
            $result[] = [
                'feature_key' => $key,
                'label'       => $label,
                'is_enabled'  => isset($stored[$key]) ? (int) $stored[$key] : 1,
            ];
        }
        return $result;
    }

    /**
     * Upserts feature flags for a company.
     * Only writes rows that are disabled (0) or already exist.
     */
    public function saveForCompany(int $companyId, array $features): void
    {
        foreach ($features as $key => $enabled) {
            $enabled = $enabled ? 1 : 0;
            $existing = $this->where('company_id', $companyId)
                             ->where('feature_key', $key)
                             ->first();

            if ($existing) {
                $this->update($existing['id'], ['is_enabled' => $enabled]);
            } elseif ($enabled === 0) {
                // Only create a record when disabling; absence means enabled
                $this->insert([
                    'company_id'  => $companyId,
                    'feature_key' => $key,
                    'is_enabled'  => 0,
                ]);
            }
        }
    }

    /**
     * Check if a single feature is enabled for a company.
     * Returns true when no record exists (default on).
     */
    public function isEnabled(int $companyId, string $featureKey): bool
    {
        $row = $this->where('company_id', $companyId)
                    ->where('feature_key', $featureKey)
                    ->first();

        return $row === null || (bool) $row['is_enabled'];
    }
}
