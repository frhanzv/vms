<?php

/**
 * Who may see a vendor pass (staff side).
 *
 * One product is shared by several clients in the same building. A pass is
 * seen by every client that owns one of its selected locations (a row in
 * vendor_client_approvals). A pass whose locations no client owns yet falls
 * back to the old rule: it belongs to the client in vendors.company_id.
 * Platform superadmin sees everything. A vendor company account is scoped by
 * its own company elsewhere (apply_vendor_company_scope) — not by client.
 */

if (! function_exists('vendor_client_scope_sql')) {
    /** SQL condition (no leading AND) for one client id; 'vendors' is the table name. */
    function vendor_client_scope_sql(int $clientId, string $t = 'vendors'): string
    {
        $clientId = (int) $clientId;

        return "(EXISTS (SELECT 1 FROM vendor_client_approvals vca WHERE vca.vendor_id = {$t}.id AND vca.client_id = {$clientId})"
            . " OR ({$t}.company_id = {$clientId} AND NOT EXISTS (SELECT 1 FROM vendor_client_approvals vca2 WHERE vca2.vendor_id = {$t}.id)))";
    }
}

if (! function_exists('vendor_client_scope')) {
    /** Apply the staff-side client rule to a vendors query builder. */
    function vendor_client_scope($builder, string $t = 'vendors'): void
    {
        helper(['feature', 'role']);
        helper('vendor_company');

        if (is_platform_superadmin() || is_vendor_admin()) {
            return;
        }

        $builder->where(vendor_client_scope_sql((int) current_company_id(), $t), null, false);
    }
}
