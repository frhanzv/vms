<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * Same per-client approval as VendorClientApprovals, for staff passes.
 * Staff pick gates from `locations` (location_access text, owned by one
 * client via locations.client_id) and rows live in staff_client_approvals.
 */
class StaffClientApprovals extends VendorClientApprovals
{
    protected const TABLE = 'staff_client_approvals';
    protected const KEY   = 'staff_id';

    protected static function ownerRows(BaseConnection $db, array $codes): array
    {
        return $db->table('locations')->select('client_id')->whereIn('location_access', $codes)
            ->where('client_id IS NOT NULL', null, false)->get()->getResultArray();
    }
}
