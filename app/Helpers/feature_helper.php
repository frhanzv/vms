<?php

if (!function_exists('current_client_id')) {
    /**
     * Resolve the active VMS client (tenant) for feature / form-field scoping.
     * This is NOT the invitation visitor company list.
     */
    function current_client_id(): int
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }

        $clientId = (int) session()->get('client_id');
        if ($clientId > 0) {
            return $cached = $clientId;
        }

        // Legacy session key from before clients table existed.
        $legacyCompanyId = (int) session()->get('company_id');
        if ($legacyCompanyId > 0) {
            return $cached = $legacyCompanyId;
        }

        $userId = (int) session()->get('user_id');
        if ($userId > 0) {
            $user = (new \App\Models\UserModel())->find($userId);
            if ($user) {
                $clientId = (int) ($user['client_id'] ?? 0);
                if ($clientId > 0) {
                    return $cached = $clientId;
                }
            }
        }

        return $cached = 0;
    }
}

if (!function_exists('current_company_id')) {
    /** @deprecated Use current_client_id() — company list is for invitations only. */
    function current_company_id(): int
    {
        return current_client_id();
    }
}

if (!function_exists('current_client_name')) {
    function current_client_name(): ?string
    {
        static $cachedName = null;
        static $cachedForId = null;

        $clientId = current_client_id();
        if ($clientId <= 0) {
            return null;
        }

        if ($cachedForId === $clientId) {
            return $cachedName;
        }

        $client = (new \App\Models\ClientModel())->find($clientId);
        $cachedForId = $clientId;
        $cachedName = $client['name'] ?? null;

        return $cachedName;
    }
}

if (!function_exists('current_company_name')) {
    /** @deprecated Use current_client_name() */
    function current_company_name(): ?string
    {
        return current_client_name();
    }
}

if (!function_exists('client_feature_enabled')) {
    function client_feature_enabled(string $featureKey): bool
    {
        $clientId = current_client_id();

        if (is_platform_superadmin()) {
            return true;
        }

        return (new \App\Models\ClientFeatureModel())->isEnabled($clientId, $featureKey);
    }
}

if (!function_exists('client_form_field_enabled')) {
    function client_form_field_enabled(string $formType, string $fieldKey): bool
    {
        $clientId = current_client_id();
        if ($clientId === 0) {
            return true;
        }

        return (new \App\Models\ClientFormFieldModel())->isEnabled($clientId, $formType, $fieldKey);
    }
}

if (!function_exists('client_form_config')) {
    /**
     * @return array<string, bool> field_key => is_enabled
     */
    function client_form_config(string $formType): array
    {
        $clientId = current_client_id();
        if ($clientId === 0) {
            return [];
        }

        $config = [];
        foreach ((new \App\Models\ClientFormFieldModel())->getForCompanyForm($clientId, $formType) as $field) {
            $config[$field['field_key']] = (bool) $field['is_enabled'];
        }

        return $config;
    }
}
