<?php

if (!function_exists('client_feature_enabled')) {
    function client_feature_enabled(string $featureKey): bool
    {
        if (is_platform_superadmin(session()->get('role'))) {
            return true;
        }

        $companyId = (int) (
            session()->get('company_id')
            ?: ((new \App\Models\UserModel())->find((int) session()->get('user_id'))['company_id'] ?? 0)
        );

        return (new \App\Models\ClientFeatureModel())->isEnabled($companyId, $featureKey);
    }
}
