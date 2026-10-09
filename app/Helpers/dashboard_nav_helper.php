<?php

/**
 * Which dashboards the logged-in user may open: Visitor, Staff, Vendor.
 * One source of truth for the tab bar, the sidebar and the controllers.
 *
 *  - Visitor / Staff / Vendor tabs for staff-side roles need the existing
 *    "Dashboard" menu permission plus the module's own permission and the
 *    client's feature toggle.
 *  - A vendor company account (role vendor_admin) has no staff-side dashboard
 *    permission at all; it gets exactly one dashboard: its own Vendor one.
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

        if ($staffSide && client_feature_enabled('staff_pass') && has_access('staff_pass_list', 'view')) {
            $tabs[] = ['key' => 'staff', 'label' => 'Staff', 'url' => base_url('dashboard/staff'), 'icon' => 'badge'];
        }

        if (($staffSide || is_vendor_admin()) && client_feature_enabled('vendor_pass') && has_access('vendor_pass_list', 'view')) {
            $tabs[] = ['key' => 'vendor', 'label' => 'Vendor', 'url' => base_url('dashboard/vendor'), 'icon' => 'local_shipping'];
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
