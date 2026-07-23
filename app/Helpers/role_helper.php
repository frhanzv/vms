<?php

if (!function_exists('normalize_role_slug')) {
    /**
     * Map a role display name or slug to the canonical slug stored in users.role.
     */
    function normalize_role_slug(?string $role): string
    {
        if ($role === null || $role === '') {
            return '';
        }

        $key = strtolower(str_replace([' ', '_', '-'], '', trim($role)));

        $map = [
            'superadmin'       => 'superadmin',
            'clientsuperadmin' => 'clientsuperadmin',
            'admin'            => 'admin',
            'siteadmin'        => 'admin',
            'officer'          => 'officer',
            'securityofficer'  => 'officer',
            'host'             => 'host',
            'receptionist'     => 'receptionist',
            'viewer'           => 'viewer',
        ];

        return $map[$key] ?? $key;
    }
}

if (!function_exists('role_display_name')) {
    /**
     * Human-readable label for a role slug.
     */
    function role_display_name(?string $role): string
    {
        $slug = normalize_role_slug($role);

        $labels = [
            'superadmin'       => 'Superadmin',
            'clientsuperadmin' => 'Client Superadmin',
            'admin'            => 'Admin',
            'officer'          => 'Officer',
            'host'             => 'Host',
            'receptionist'     => 'Receptionist',
            'viewer'           => 'Viewer',
        ];

        return $labels[$slug] ?? ucfirst($slug);
    }
}

if (!function_exists('role_matches')) {
    /**
     * Compare session/user role values regardless of display name vs slug formatting.
     */
    function role_matches(?string $role, string $expectedSlug): bool
    {
        return normalize_role_slug($role) === normalize_role_slug($expectedSlug);
    }
}

if (!function_exists('is_platform_superadmin')) {
    function is_platform_superadmin(?string $role = null): bool
    {
        $role ??= session()->get('role');
        return role_matches($role, 'superadmin');
    }
}
