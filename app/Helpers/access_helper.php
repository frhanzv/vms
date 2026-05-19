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
     * 
     * @param string $module The module key (e.g., 'dashboard', 'settings', 'visitor_pass_list')
     * @param string $action The action key (e.g., 'view', 'create', 'invitations')
     * @return bool
     */
    function has_access($module, $action = 'view')
    {
        static $loadedData = null;

        $roleName = session()->get('role');
        if (!$roleName) {
            return false;
        }

        if ($loadedData !== null) {
            return isset($loadedData[$module][$action]) && $loadedData[$module][$action] === true;
        }

        $roleKey = strtolower(str_replace([' ', '_', '-'], '', $roleName));
        $cached = session()->get('role_access_cache');
        $cacheTTL = 60; // seconds — cache expires after this, forces fresh DB read

        $cacheValid = is_array($cached)
            && isset($cached['_role']) && $cached['_role'] === $roleKey
            && isset($cached['_time']) && (time() - $cached['_time']) < $cacheTTL;

        if ($cacheValid) {
            $loadedData = $cached['_data'] ?? [];
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
            $loadedData = json_decode($role['access'], true) ?? [];
            _migrate_legacy_access($loadedData);
        } else {
            $loadedData = [];
        }

        session()->set('role_access_cache', [
            '_role' => $roleKey,
            '_data' => $loadedData,
            '_time' => time(),
        ]);

        return isset($loadedData[$module][$action]) && $loadedData[$module][$action] === true;
    }
}
