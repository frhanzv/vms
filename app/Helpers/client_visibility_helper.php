<?php

/**
 * Who sees which Staff / Visitor record when several clients share one product.
 *
 * A gate / "Location Access" row (table `locations`) belongs to ONE client
 * (locations.client_id). A record is visible to client X when
 *   - it was created under X (client_id = X), OR
 *   - one of its locations is owned by X, OR
 *   - legacy: it has no client and none of its locations is owned by anyone.
 * (Invitations only; staff passes use per-client approval rows, see below.)
 * The platform superadmin sees everything.
 *
 * Staff: see visibility_staff_sql (per-client approval rows).
 * Invitations store a location (invitations.location = locations.location_access, comma list for public registration);
 * staff store a comma list (staff.location_access = "Gate A IN,Gate B OUT").
 */

if (! function_exists('visibility_invitation_sql')) {
    /** SQL condition for table/alias $t (an `invitations` row). The location may be one code or a comma list. */
    function visibility_invitation_sql(int $clientId, string $t = 'invitations'): string
    {
        $c   = (int) $clientId;
        $csv = "CONCAT(',', COALESCE({$t}.location, ''), ',')";
        $hit = "INSTR({$csv}, CONCAT(',', vl.location_access, ',')) > 0";

        return "({$t}.client_id = {$c}"
            . " OR EXISTS (SELECT 1 FROM locations vl WHERE vl.client_id = {$c} AND {$hit})"
            . " OR ({$t}.client_id IS NULL AND NOT EXISTS (SELECT 1 FROM locations vl WHERE vl.client_id IS NOT NULL AND {$hit})))";
    }
}

if (! function_exists('visibility_staff_sql')) {
    /**
     * SQL condition for table/alias $t (a `staff` row). Staff passes are approved per
     * client like vendor passes: seen by every client with a row in
     * staff_client_approvals (= owns one of its gates); with no rows (draft, or gates
     * nobody owns yet) it falls back to staff.company_id, and rows with no company
     * stay visible to everyone as before.
     */
    function visibility_staff_sql(int $clientId, string $t = 'staff'): string
    {
        $c = (int) $clientId;

        return "(EXISTS (SELECT 1 FROM staff_client_approvals sca WHERE sca.staff_id = {$t}.id AND sca.client_id = {$c})"
            . " OR (NOT EXISTS (SELECT 1 FROM staff_client_approvals sca2 WHERE sca2.staff_id = {$t}.id)"
            . " AND ({$t}.company_id = {$c} OR {$t}.company_id IS NULL)))";
    }
}

if (! function_exists('visibility_scope_applies')) {
    /** False for the platform superadmin (sees all) and for vendor company accounts. */
    function visibility_scope_applies(): bool
    {
        helper(['feature', 'role']);
        if (is_platform_superadmin()) {
            return false;
        }
        helper('vendor_company');

        return ! (function_exists('is_vendor_admin') && is_vendor_admin()) && (int) current_client_id() > 0;
    }
}

if (! function_exists('visibility_scope_invitations')) {
    /** Restrict an invitations (or joined) query builder to what this client may see. */
    function visibility_scope_invitations($builder, string $t = 'invitations'): void
    {
        if (visibility_scope_applies()) {
            $builder->where(visibility_invitation_sql((int) current_client_id(), $t), null, false);
        }
    }
}

if (! function_exists('visibility_scope_staff')) {
    function visibility_scope_staff($builder, string $t = 'staff'): void
    {
        if (visibility_scope_applies()) {
            $builder->where(visibility_staff_sql((int) current_client_id(), $t), null, false);
        }
    }
}

if (! function_exists('visibility_location_clients')) {
    /**
     * Client ids whose gate locations the current user may pick from (own client
     * + site-group siblings). Null = no restriction (platform superadmin).
     *
     * @return list<int>|null
     */
    function visibility_location_clients(): ?array
    {
        return \App\Models\VendorLocationModel::visibleClientIds();
    }
}

if (! function_exists('visibility_filter_locations')) {
    /**
     * Keep only the `locations` rows this user may pick: owned by own client or a
     * site-group sibling, or owned by nobody yet.
     *
     * @param  list<array> $rows rows of the `locations` table
     * @return list<array>
     */
    function visibility_filter_locations(array $rows): array
    {
        $ids = visibility_location_clients();
        if ($ids === null) {
            return $rows;
        }

        return array_values(array_filter($rows, static fn($l) => empty($l['client_id']) || in_array((int) $l['client_id'], $ids, true)));
    }
}

if (! function_exists('visibility_invitation_allowed')) {
    /** May the current user open/act on this invitation (client + gate visibility)? */
    function visibility_invitation_allowed(int $invitationId): bool
    {
        if (! visibility_scope_applies()) {
            return true;
        }

        return \Config\Database::connect()->table('invitations')->where('id', $invitationId)
            ->where(visibility_invitation_sql((int) current_client_id(), 'invitations'), null, false)->countAllResults() > 0;
    }
}

if (! function_exists('visibility_staff_allowed')) {
    function visibility_staff_allowed(int $staffId): bool
    {
        if (! visibility_scope_applies()) {
            return true;
        }

        return \Config\Database::connect()->table('staff')->where('id', $staffId)
            ->where(visibility_staff_sql((int) current_client_id(), 'staff'), null, false)->countAllResults() > 0;
    }
}
