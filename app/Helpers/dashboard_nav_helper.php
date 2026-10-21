<?php

/**
 * Which dashboards the logged-in user may open: Visitor, Vendor and Vehicle.
 * One source of truth for the tab bar, the sidebar and the controllers.
 *
 *  - Visitor / Staff / Vendor tabs for staff-side roles need the existing
 *    "Dashboard" menu permission plus the module's own permission and the
 *    client's feature toggle.
 *  - A vendor company account (role vendor_admin) gets its own Vendor
 *    dashboard only when its role has Dashboard > Vendor Pass ticked.
 */

if (! function_exists('dashboard_tabs')) {
    /** @return list<array{key:string,label:string,url:string,icon:string}> */
    function dashboard_tabs(): array
    {
        static $tabs = null;
        if ($tabs !== null) {
            return $tabs;
        }

        helper(['access', 'feature', 'vendor_company']);

        $tabs      = [];
        $staffSide = has_access('dashboard', 'main_menu');

        if ($staffSide) {
            $tabs[] = ['key' => 'visitor', 'label' => 'Visitor', 'url' => base_url('dashboard'), 'icon' => 'groups'];
        }

        // No Staff dashboard: staff passes are managed from the Staff Pass List only.
        // (dashboard/staff now redirects to the main dashboard because the tab is not allowed.)

        // The Vendor dashboard follows the role's Dashboard > Vendor Pass tick,
        // for staff roles and vendor company accounts alike.
        if (($staffSide || is_vendor_admin()) && client_feature_enabled('vendor_pass')
            && has_access('dashboard', 'vendor_pass') && has_access('vendor_pass_list', 'view')) {
            $tabs[] = ['key' => 'vendor', 'label' => 'Vendor', 'url' => base_url('dashboard/vendor'), 'icon' => 'local_shipping'];
        }

        // Vehicle dashboard: Dashboard > Vehicle plus the client's vehicle pass feature.
        if ($staffSide && client_feature_enabled('vehicle_pass') && has_access('dashboard', 'vehicle')) {
            $tabs[] = ['key' => 'vehicle', 'label' => 'Vehicle', 'url' => base_url('dashboard/vehicle'), 'icon' => 'directions_car'];
        }

        return $tabs;
    }
}

if (! function_exists('dashboard_tab_allowed')) {
    function dashboard_tab_allowed(string $key): bool
    {
        foreach (dashboard_tabs() as $tab) {
            if ($tab['key'] === $key) {
                return true;
            }
        }

        return false;
    }
}
