<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * Numbers for the Vendor dashboard. A pure class (takes the DB connection and
 * the scope) so it can be tested without a web request.
 *
 * Scope:
 *   $clientId  - tenant filter on vendors.company_id (null = all clients,
 *                platform superadmin only)
 *   $regNo     - vendor-account filter on vendors.vendor_company_reg_id
 *                (null = no company filter, i.e. staff side)
 *   $locked    - true when a vendor account has no usable company: every
 *                number is zero.
 *
 * Pipeline stages (same rules as the Process / Printing / Issuance / Closed
 * lists): Draft, Pending, Rejected, Process (approved, no card type yet),
 * Printing (card type set), Issuance (receipt no. set), Active (card active),
 * Closed (suspended / terminated). Precedence: closed > active > issuance >
 * printing > process.
 */
if (! function_exists('vendor_client_scope_sql')) {
    require_once __DIR__ . '/../Helpers/vendor_client_scope_helper.php';
}

class VendorDashboardStats
{
    public const STAGES = ['Draft', 'Pending', 'Rejected', 'Process', 'Printing', 'Issuance', 'Active', 'Closed'];

    public static function collect(BaseConnection $db, ?int $clientId, ?string $regNo, bool $locked = false, ?string $today = null): array
    {
        $today = $today ?: date('Y-m-d');
        $in30  = date('Y-m-d', strtotime($today . ' +30 days'));

        $scope = static function ($b) use ($clientId, $regNo, $locked) {
            if ($locked) {
                $b->where('1 = 0', null, false);
                return $b;
            }
            if ($clientId !== null) {
                // Shared product: a pass counts for every client owning one of its locations.
                $b->where(vendor_client_scope_sql($clientId, 'vendors'), null, false);
            }
            if ($regNo !== null) {
                $b->where('vendor_company_reg_id', $regNo);
            }
            return $b;
        };

        // ---- Pipeline --------------------------------------------------------
        $rows = $scope($db->table('vendors')
            ->select('status, card_status, card_type, receipt_no, COUNT(*) AS c', false))
            ->groupBy('status, card_status, card_type, receipt_no')->get()->getResultArray();

        $stages = array_fill_keys(self::STAGES, 0);
        $total  = 0;
        foreach ($rows as $r) {
            $stages[self::stage($r)] += (int) $r['c'];
            $total += (int) $r['c'];
        }

        // ---- Active passes: expiring / expired -------------------------------
        $activeBase = static fn() => $scope($db->table('vendors'))
            ->where("LOWER(card_status) = 'active'", null, false)
            ->where("LOWER(status) NOT IN ('suspended','terminated')", null, false);

        $expiring = (int) $activeBase()->where('pass_expiry >=', $today)->where('pass_expiry <=', $in30)->countAllResults();
        $expired  = (int) $activeBase()->where('pass_expiry IS NOT NULL', null, false)->where('pass_expiry <', $today)->countAllResults();

        // ---- Applications per month (last 6) ---------------------------------
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[date('Y-m', strtotime(date('Y-m-01', strtotime($today)) . " -$i months"))] = date('M y', strtotime(date('Y-m-01', strtotime($today)) . " -$i months"));
        }
        $perMonth = array_fill_keys(array_keys($months), 0);
        $mRows = $scope($db->table('vendors')
            ->select("DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c", false))
            ->where('created_at >=', array_key_first($months) . '-01')
            ->groupBy('ym')->get()->getResultArray();
        foreach ($mRows as $r) {
            if (isset($perMonth[$r['ym']])) {
                $perMonth[$r['ym']] = (int) $r['c'];
            }
        }

        // ---- Worker type -----------------------------------------------------
        $typeRows = $scope($db->table('vendors')
            ->select("COALESCE(NULLIF(TRIM(card_type), ''), 'Not set') AS label, COUNT(*) AS c", false))
            ->groupBy('label')->orderBy('c', 'DESC')->get()->getResultArray();

        // ---- By company (staff side only) ------------------------------------
        $companies = [];
        if ($regNo === null && ! $locked) {
            $companies = $scope($db->table('vendors')
                ->select("COALESCE(NULLIF(TRIM(vendor_company_name), ''), 'Not set') AS label, COUNT(*) AS c, "
                    . "SUM(CASE WHEN LOWER(status) = 'pending' THEN 1 ELSE 0 END) AS pending", false))
                ->groupBy('label')->orderBy('c', 'DESC')->orderBy('label', 'ASC')->limit(8)->get()->getResultArray();
        }

        // ---- Tables ----------------------------------------------------------
        $actionStatuses = $regNo === null ? ['Pending'] : ['Rejected', 'Draft'];
        $needsAction = $scope($db->table('vendors')
            ->select('id, full_name, app_no, vendor_company_name, status, created_at'))
            ->whereIn('status', $actionStatuses)
            ->orderBy('id', 'DESC')->limit(8)->get()->getResultArray();

        $expiringList = $activeBase()
            ->select('id, full_name, app_no, vendor_company_name, pass_expiry')
            ->where('pass_expiry >=', $today)->where('pass_expiry <=', $in30)
            ->orderBy('pass_expiry', 'ASC')->limit(8)->get()->getResultArray();
        foreach ($expiringList as &$row) {
            $row['days_left'] = (int) floor((strtotime($row['pass_expiry']) - strtotime($today)) / 86400);
        }
        unset($row);

        $recent = $scope($db->table('vendors')
            ->select('id, full_name, app_no, vendor_company_name, status, created_at'))
            ->orderBy('id', 'DESC')->limit(8)->get()->getResultArray();

        return [
            'total'         => $total,
            'stages'        => $stages,
            'activePasses'  => $stages['Active'],
            'expiring'      => $expiring,
            'expired'       => $expired,
            'monthLabels'   => array_values($months),
            'monthValues'   => array_values($perMonth),
            'workerTypes'   => $typeRows,
            'companies'     => $companies,
            'needsAction'   => $needsAction,
            'expiringList'  => $expiringList,
            'recent'        => $recent,
        ];
    }

    /** @param array<string,mixed> $r */
    public static function stage(array $r): string
    {
        $status = strtolower(trim((string) ($r['status'] ?? '')));
        $card   = strtolower(trim((string) ($r['card_status'] ?? '')));

        if (in_array($status, ['suspended', 'terminated'], true)) {
            return 'Closed';
        }
        if ($status === 'draft')    return 'Draft';
        if ($status === 'rejected') return 'Rejected';
        if ($status === 'approved') {
            if ($card === 'active')                                   return 'Active';
            if (trim((string) ($r['receipt_no'] ?? '')) !== '')       return 'Issuance';
            if (trim((string) ($r['card_type'] ?? '')) !== '')        return 'Printing';
            return 'Process';
        }
        return 'Pending';
    }
}
