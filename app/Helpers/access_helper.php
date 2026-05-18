<?php

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
        $roleName = session()->get('role');
        if (!$roleName) {
            return false;
        }

        // Cache the access permissions in the session to avoid DB query on every function call
        $accessData = session()->get('role_access_data');
        
        if ($accessData === null) {
            $roleModel = new \App\Models\RoleModel();
            
            // Try to match exact or lowercase since roles might be 'Super Admin' or 'superadmin'
            $role = $roleModel->where('name', $roleName)->orWhere('LOWER(name)', strtolower($roleName))->first();
            
            if ($role && !empty($role['access'])) {
                $accessData = json_decode($role['access'], true) ?? [];
            } else {
                $accessData = [];
            }
            
            session()->set('role_access_data', $accessData);
        }

        return isset($accessData[$module][$action]) && $accessData[$module][$action] === true;
    }
}
