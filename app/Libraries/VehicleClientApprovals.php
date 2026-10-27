<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * Same per-client sharing and approval as VendorClientApprovals, for vehicle passes.
 * A vehicle stores its Location Access as location codes (slug of locations.location_access),
 * like a vendor pass, so the owner lookup is the vendor one. Rows live in vehicle_client_approvals.
 */
class VehicleClientApprovals extends VendorClientApprovals
{
    protected const TABLE = 'vehicle_client_approvals';
    protected const KEY   = 'vehicle_id';

    /** False until the 2026-10-27 migration has run: the pass then keeps the old one-client rule. */
    public static function ready(BaseConnection $db): bool
    {
        static $ready = null;

        return $ready ??= $db->tableExists(static::TABLE);
    }

    /**
     * SQL condition (no leading AND) for "client $clientId may see this vehicle" on table/alias $t:
     * it owns one of the pass's locations, or nobody owns any and the pass was made under this client.
     */
    public static function scopeSql(BaseConnection $db, int $clientId, string $t = 'v'): string
    {
        $c = (int) $clientId;
        if (! static::ready($db)) {
            return "{$t}.client_id = {$c}";
        }

        return "(EXISTS (SELECT 1 FROM vehicle_client_approvals vca WHERE vca.vehicle_id = {$t}.id AND vca.client_id = {$c})"
            . " OR ({$t}.client_id = {$c} AND NOT EXISTS (SELECT 1 FROM vehicle_client_approvals vca2 WHERE vca2.vehicle_id = {$t}.id)))";
    }
}
