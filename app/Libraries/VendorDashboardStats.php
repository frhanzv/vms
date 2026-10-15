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
 * lists): Draft, Pending, Rejected, Process (approved, no receipt no. - this is also
 * the Printing List), Issuance (receipt no. set), Closed (everything in the Closed
 * List: card active or terminated, plus suspended).
 */
if (! function_exists('vendor_client_scope_sql')) {
    require_once __DIR__ . '/../Helpers/vendor_client_scope_helper.php';
}

class VendorDashboardStats
{
    public const STAGES = ['Draft', 'Pending', 'Rejected', 'Process', 'Issuance', 'Closed'];

    /**
     * @param array{location?:string,from?:string,to?:string} $filters
     *        location = location code (slug of locations.location_access), from/to = Y-m-d (application / check-in date range)
     */
    public static function collect(BaseConnection $db, ?int $clientId, ?string $regNo, bool $locked = false, ?string $today = null, array $filters = []): array
    {
        $today = $today ?: date('Y-m-d');
        $in30  = date('Y-m-d', strtotime($today . ' +30 days'));

        $isDate = static fn($d) => is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && strtotime($d) !== false;
        $from   = $isDate($filters['from'] ?? null) ? $filters['from'] : date('Y-m-d', strtotime($today . ' -30 days'));
        $to     = $isDate($filters['to'] ?? null) ? $filters['to'] : $today;
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }
        $locCode = trim((string) ($filters['location'] ?? ''));

        $scope = static function ($b) use ($clientId, $regNo, $locked, $locCode, $db) {
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
            if ($locCode !== '') {
                // location_access is a comma separated list of location codes (slug of locations.location_access)
                $col = "REPLACE(" . $db->prefixTable('vendors') . ".location_access, ' ', '')";
                $b->where('(' . $col . ' = ' . $db->escape($locCode)
                    . ' OR ' . $col . ' LIKE ' . $db->escape($locCode . ',%')
                    . ' OR ' . $col . ' LIKE ' . $db->escape('%,' . $locCode)
                    . ' OR ' . $col . ' LIKE ' . $db->escape('%,' . $locCode . ',%') . ')', null, false);
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

        // ---- KPK-style summary -----------------------------------------------
        $inRange = static fn($b) => $b->where('vendors.created_at >=', $from . ' 00:00:00')->where('vendors.created_at <=', $to . ' 23:59:59');

        $newApplications = (int) $inRange($scope($db->table('vendors')))->countAllResults();
        $approved        = (int) $inRange($scope($db->table('vendors')))->where("LOWER(status) = 'approved'", null, false)->countAllResults();
        $issuance        = (int) $inRange($scope($db->table('vendors')))->where("LOWER(status) = 'approved'", null, false)
            ->where("TRIM(COALESCE(receipt_no, '')) <> ''", null, false)->countAllResults();

        $activeCompanies = (int) ($activeBase()->select('COUNT(DISTINCT vendor_company_reg_id) AS c', false)
            ->where("TRIM(COALESCE(vendor_company_reg_id, '')) <> ''", null, false)->get()->getRowArray()['c'] ?? 0);
        $activePasses = (int) $activeBase()->countAllResults();

        // ---- By location -----------------------------------------------------
        $locLabels = [];
        try {
            foreach ($db->table('locations')->select('location_access')->where('status', 'active')->orderBy('location_access', 'ASC')->get()->getResultArray() as $l) {
                $code = \App\Models\VendorLocationModel::slugifyCode((string) $l['location_access']);
                if ($code !== '' && ! isset($locLabels[$code])) {
                    $locLabels[$code] = (string) $l['location_access'];
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'VendorDashboardStats locations: ' . $e->getMessage());
        }
        $locCounts = [];
        $lr = $scope($db->table('vendors')->select('location_access, COUNT(*) AS c', false))
            ->groupBy('location_access')->get()->getResultArray();
        foreach ($lr as $r) {
            foreach (array_filter(array_map('trim', explode(',', (string) ($r['location_access'] ?? '')))) as $code) {
                $locCounts[$code] = ($locCounts[$code] ?? 0) + (int) $r['c'];
            }
        }
        arsort($locCounts);
        $byLocation = [];
        foreach ($locCounts as $code => $n) {
            $byLocation[] = ['code' => $code, 'label' => $locLabels[$code] ?? $code, 'c' => $n];
        }

        // ---- Check IN / OUT / out of window (gate visits) ---------------------
        $gate = ['checkIn' => 0, 'checkOut' => 0, 'outOfWindow' => 0, 'nearOutOfWindow' => 0, 'available' => false];
        try {
            if ($db->tableExists('vendor_visits')) {
                $visits = static fn() => $scope($db->table('vendor_visits')->join('vendors', 'vendors.id = vendor_visits.vendor_id'));
                $gate['available'] = true;
                $gate['checkIn'] = (int) $visits()->where('vendor_visits.check_in_time >=', $from . ' 00:00:00')
                    ->where('vendor_visits.check_in_time <=', $to . ' 23:59:59')->countAllResults();
                $gate['checkOut'] = (int) $visits()->where('vendor_visits.check_out_time >=', $from . ' 00:00:00')
                    ->where('vendor_visits.check_out_time <=', $to . ' 23:59:59')->countAllResults();
                $onSite = static fn() => $visits()->select('COUNT(DISTINCT ' . $db->prefixTable('vendor_visits') . '.vendor_id) AS c', false)
                    ->where('vendor_visits.check_in_time IS NOT NULL')->where('vendor_visits.check_out_time IS NULL');
                $gate['outOfWindow'] = (int) ($onSite()->where('vendors.pass_expiry IS NOT NULL')
                    ->where('vendors.pass_expiry <', $today)->get()->getRowArray()['c'] ?? 0);
                $gate['nearOutOfWindow'] = (int) ($onSite()->where('vendors.pass_expiry >=', $today)
                    ->where('vendors.pass_expiry <=', date('Y-m-d', strtotime($today . ' +7 days')))->get()->getRowArray()['c'] ?? 0);
            }
        } catch (\Throwable $e) {
            log_message('error', 'VendorDashboardStats gate: ' . $e->getMessage());
        }

        return [
            'summary' => [
                'newApplications' => $newApplications,
                'approved'        => $approved,
                'issuance'        => $issuance,
                'expired'         => $expired,
                'expiring'        => $expiring,
                'activePasses'    => $activePasses,
                'activeCompanies' => $activeCompanies,
                'avgPerCompany'   => $activeCompanies > 0 ? round($activePasses / $activeCompanies, 1) : 0,
                'asAt'            => date('d M Y, g:i A'),
            ],
            'byLocation'    => $byLocation,
            'locationList'  => $locLabels,
            'gate'          => $gate,
            'filters'       => ['location' => $locCode, 'from' => $from, 'to' => $to],
            'total'         => $total,
            'stages'        => $stages,
            'activePasses'  => $activePasses,
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

        // Same rules as the lists: Closed List = card_status Active / Terminated;
        // Process List and Printing List = the same set (approved, no receipt no. yet);
        // Issuance List = approved, receipt no. set, card not active yet.
        if ($card === 'terminated' || in_array($status, ['suspended', 'terminated'], true)) {
            return 'Closed';
        }
        if ($card === 'active')     return 'Closed';
        if ($status === 'draft')    return 'Draft';
        if ($status === 'rejected') return 'Rejected';
        if ($status === 'approved') {
            if (trim((string) ($r['receipt_no'] ?? '')) !== '') return 'Issuance';
            return 'Process';
        }
        return 'Pending';
    }
}
