<?php

if (! function_exists('company_logo_url')) {
    /**
     * Resolve the uploaded company logo URL for sidebar branding.
     */
    function company_logo_url(): ?string
    {
        static $resolved = false;
        static $url = null;

        if ($resolved) {
            return $url;
        }

        $resolved = true;
        $model = model(\App\Models\SettingModel::class);
        $path = trim((string) $model->getSetting('company_logo'));

        if ($path === '') {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            $url = $path;
        } else {
            $url = base_url(ltrim($path, '/'));
        }

        return $url;
    }
}

if (! function_exists('company_brand_name')) {
    /**
     * Sidebar brand label — uses login brand name when configured.
     */
    function company_brand_name(): string
    {
        static $resolved = false;
        static $name = 'SafeG';

        if ($resolved) {
            return $name;
        }

        $resolved = true;
        $model = model(\App\Models\SettingModel::class);
        $stored = $model->getSetting('login_brand_name');

        if ($stored !== null && trim((string) $stored) !== '') {
            $name = (string) $stored;
        }

        return $name;
    }
}
