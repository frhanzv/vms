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

if (!function_exists('is_client_superadmin')) {
    function is_client_superadmin(?string $role = null): bool
    {
        $role ??= session()->get('role');
        return role_matches($role, 'clientsuperadmin');
    }
}

if (!function_exists('roles_blocked_for_client_user_management')) {
    /** Roles a Client Super Admin must never assign or manage. */
    function roles_blocked_for_client_user_management(): array
    {
        return ['superadmin', 'clientsuperadmin'];
    }
}

if (!function_exists('user_management_client_scope')) {
    /**
     * Client tenant scope for user management. 0 = platform superadmin (all clients).
     */
    function user_management_client_scope(): int
    {
        if (is_platform_superadmin()) {
            return 0;
        }

        if (is_client_superadmin()) {
            helper('feature');
            return current_client_id();
        }

        return 0;
    }
}

if (!function_exists('is_client_scoped_user_manager')) {
    function is_client_scoped_user_manager(?string $role = null): bool
    {
        $role ??= session()->get('role');

        return is_client_superadmin($role) && !is_platform_superadmin($role);
    }
}

if (!function_exists('can_assign_role_in_user_management')) {
    function can_assign_role_in_user_management(string $targetRoleSlug, ?string $managerRole = null): bool
    {
        $managerRole ??= session()->get('role');
        $target = normalize_role_slug($targetRoleSlug);

        if (is_platform_superadmin($managerRole)) {
            return true;
        }

        if (is_client_superadmin($managerRole)) {
            return ! in_array($target, roles_blocked_for_client_user_management(), true);
        }

        return false;
    }
}

if (!function_exists('can_manage_target_user')) {
    function can_manage_target_user(array $targetUser, ?string $managerRole = null): bool
    {
        helper('feature');
        $managerRole ??= session()->get('role');

        if (is_platform_superadmin($managerRole)) {
            return true;
        }

        if (! is_client_superadmin($managerRole)) {
            return false;
        }

        $scope = user_management_client_scope();
        if ($scope <= 0) {
            return false;
        }

        $targetRole = normalize_role_slug($targetUser['role'] ?? '');
        if (in_array($targetRole, roles_blocked_for_client_user_management(), true)) {
            return false;
        }

        return (int) ($targetUser['client_id'] ?? 0) === $scope;
    }
}

if (!function_exists('can_manage_client_company_config')) {
    /**
     * Client Features / Dynamic Form Fields admin — platform superadmin only.
     */
    function can_manage_client_company_config(?string $role = null): bool
    {
        helper('role');
        $role ??= session()->get('role');

        return is_platform_superadmin($role);
    }
}

if (!function_exists('scoped_client_company_id')) {
    /**
     * Platform superadmin manages all clients. Other roles are not scoped here.
     */
    function scoped_client_company_id(): int
    {
        return 0;
    }
}

if (!function_exists('assert_client_company_config_access')) {
    function client_company_config_allowed(int $companyId): bool
    {
        helper(['role', 'feature']);

        if (!can_manage_client_company_config()) {
            return false;
        }

        $scope = scoped_client_company_id();

        return $scope === 0 || $scope === $companyId;
    }
}

if (!function_exists('find_role_by_slug')) {
    /**
     * Find a Role Management record by users.role slug (normalized match).
     */
    function find_role_by_slug(?string $roleSlug): ?array
    {
        if ($roleSlug === null || $roleSlug === '') {
            return null;
        }

        $needle = normalize_role_slug($roleSlug);
        if ($needle === '') {
            return null;
        }

        static $index = null;
        if ($index === null) {
            $index = [];
            foreach ((new \App\Models\RoleModel())->findAll() as $row) {
                $index[normalize_role_slug($row['name'])] = $row;
            }
        }

        return $index[$needle] ?? null;
    }
}

if (!function_exists('role_management_name')) {
    /**
     * Label shown in UI — prefers the Role Management name when configured.
     */
    function role_management_name(?string $roleSlug): string
    {
        $role = find_role_by_slug($roleSlug);
        return $role['name'] ?? role_display_name($roleSlug);
    }
}
