<?php

if (!function_exists('_migrate_legacy_access')) {
    function _migrate_legacy_access(array &$data)
    {
        if (isset($data['dashboard']['view']) && !isset($data['dashboard']['main_menu'])) {
            $data['dashboard']['main_menu'] = $data['dashboard']['view'];
        }
        if (isset($data['blacklist']['request_list']) && !isset($data['blacklist']['individual_request_list'])) {
            $data['blacklist']['individual_request_list'] = $data['blacklist']['request_list'];
        }
        if (isset($data['blacklist']['closed_list']) && !isset($data['blacklist']['individual_closed_list'])) {
            $data['blacklist']['individual_closed_list'] = $data['blacklist']['closed_list'];
        }
    }
}

if (!function_exists('has_access')) {
    /**
     * Check if the currently logged-in user has access to a specific module and action.
     * If a role has no permissions configured yet (empty access column), defaults to full access.
     * 
     * @param string $module The module key (e.g., 'dashboard', 'settings', 'visitor_pass_list')
     * @param string $action The action key (e.g., 'view', 'create', 'invitations')
     * @return bool
     */
    function has_access($module, $action = 'view')
    {
        static $loadedData = null;
        static $grantAll = false;

        $roleName = session()->get('role');
        if (!$roleName) {
            return false;
        }

        helper('role');
        if (is_platform_superadmin($roleName)) {
            return true;
        }

        if ($loadedData !== null) {
            if ($grantAll) {
                return true;
            }
            return isset($loadedData[$module][$action]) && $loadedData[$module][$action] === true;
        }

        $roleKey = strtolower(str_replace([' ', '_', '-'], '', $roleName));
        $cached = session()->get('role_access_cache');
        $cacheTTL = 60;

        $cacheValid = is_array($cached)
            && isset($cached['_role']) && $cached['_role'] === $roleKey
            && isset($cached['_time']) && (time() - $cached['_time']) < $cacheTTL;

        if ($cacheValid) {
            $loadedData = $cached['_data'] ?? [];
            $grantAll = !empty($cached['_grant_all']);
            if ($grantAll) {
                return true;
            }
            _migrate_legacy_access($loadedData);
            return isset($loadedData[$module][$action]) && $loadedData[$module][$action] === true;
        }

        // Clear any old-format cache
        session()->remove('role_access_data');

        // Load from DB
        $roleModel = new \App\Models\RoleModel();
        $role = $roleModel->where('name', $roleName)->first();

        if (!$role) {
            $allRoles = $roleModel->findAll();
            foreach ($allRoles as $r) {
                $normalizedDb = strtolower(str_replace([' ', '_', '-'], '', $r['name']));
                if ($normalizedDb === $roleKey) {
                    $role = $r;
                    break;
                }
            }
        }

        if ($role && !empty($role['access'])) {
            $decoded = json_decode($role['access'], true);
            if (!empty($decoded)) {
                $loadedData = $decoded;
                $grantAll = false;
                _migrate_legacy_access($loadedData);
            } else {
                $loadedData = [];
                $grantAll = true;
            }
        } else {
            $loadedData = [];
            $grantAll = true;
        }

        session()->set('role_access_cache', [
            '_role' => $roleKey,
            '_data' => $loadedData,
            '_grant_all' => $grantAll,
            '_time' => time(),
        ]);

        if ($grantAll) {
            return true;
        }

        return isset($loadedData[$module][$action]) && $loadedData[$module][$action] === true;
    }
}
