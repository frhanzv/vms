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

    /** Summary / gate cards that follow KPK's getVendorForDashboard rules (shared with the card pop-ups). */
    public const KPK_CARDS = ['summary_new', 'summary_renewed', 'summary_approved', 'summary_issuance', 'summary_expired',
        'summary_expiring', 'summary_active', 'gate_in', 'gate_out', 'gate_near', 'gate_out_window'];

    /**
     * The period the summary is for (KPK): no dates picked = today, and "expired" means expired yesterday.
     * With dates, "expired" runs from the first date to the last one, but never past yesterday.
     *
     * @return array{from:string,to:string,ranged:bool,expFrom:string,expTo:string,today:string}
     */
    public static function period(array $filters, ?string $today = null): array
    {
        $today     = $today ?: date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime($today . ' -1 day'));
        $isDate    = static fn($d) => is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && strtotime($d) !== false;
        $ranged    = $isDate($filters['from'] ?? null) && $isDate($filters['to'] ?? null);
        $from      = $ranged ? $filters['from'] : $today;
        $to        = $ranged ? $filters['to'] : $today;
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        return [
            'from' => $from, 'to' => $to, 'ranged' => $ranged, 'today' => $today,
            'expFrom' => $ranged ? $from : $yesterday,
            'expTo'   => $ranged ? min($to, $yesterday) : $yesterday,
        ];
    }

    /**
     * Narrows a vendors query (already scoped) to the records behind one KPK summary card. Gate cards
     * expect vendor_visits joined to vendors. Returns false for a card that isn't one of KPK_CARDS.
     */
    public static function applyCard($b, string $card, array $p): bool
    {
        $range  = static fn($b) => $b->where('vendors.created_at >=', $p['from'] . ' 00:00:00')->where('vendors.created_at <=', $p['to'] . ' 23:59:59');
        $issued = "LOWER(COALESCE(vendors.card_status, '')) IN ('active','terminated')"; // KPK: the pass has a card
        $noCard = "LOWER(COALESCE(vendors.card_status, '')) NOT IN ('active','terminated')";
        $now    = date('Y-m-d H:i:s');
        $day    = date('Y-m-d H:i:s', strtotime($now . ' -1 day'));

        switch ($card) {
            case 'summary_new':        // typeOfApp = new
                $range($b)->where("UPPER(COALESCE(vendors.type_of_application, 'NEW')) <> 'RENEWAL'", null, false);
                break;
            case 'summary_renewed':    // typeOfApp = renewal
                $range($b)->where("UPPER(COALESCE(vendors.type_of_application, '')) = 'RENEWAL'", null, false);
                break;
            case 'summary_approved':   // approved, still in process (verify / receipt / training)
                $range($b)->where("LOWER(vendors.status) = 'approved'", null, false)
                    ->where("TRIM(COALESCE(vendors.receipt_no, '')) = ''", null, false)->where($noCard, null, false);
                break;
            case 'summary_issuance':   // approved, waiting for collection
                $range($b)->where("LOWER(vendors.status) = 'approved'", null, false)
                    ->where("TRIM(COALESCE(vendors.receipt_no, '')) <> ''", null, false)->where($noCard, null, false);
                break;
            case 'summary_expired':
                $b->where($issued, null, false)->where('vendors.pass_expiry >=', $p['expFrom'])->where('vendors.pass_expiry <=', $p['expTo']);
                break;
            case 'summary_expiring':
                $b->where($issued, null, false)->where('vendors.pass_expiry >=', $p['today'])
                    ->where('vendors.pass_expiry <=', date('Y-m-d', strtotime($p['today'] . ' +30 days')));
                break;
            case 'summary_active':
                $b->where("LOWER(vendors.card_status) = 'active'", null, false)
                    ->where("LOWER(COALESCE(vendors.status, '')) NOT IN ('suspended','terminated')", null, false)
                    ->groupStart()->where('vendors.pass_expiry IS NULL', null, false)->orWhere('vendors.pass_expiry >', $p['today'])->groupEnd();
                if ($p['ranged']) {
                    $range($b);
                }
                break;
            case 'gate_in':
                $b->where('vendor_visits.check_in_time >=', $p['from'] . ' 00:00:00')->where('vendor_visits.check_in_time <=', $p['to'] . ' 23:59:59');
                break;
            case 'gate_out':
                $b->where('vendor_visits.check_out_time >=', $p['from'] . ' 00:00:00')->where('vendor_visits.check_out_time <=', $p['to'] . ' 23:59:59');
                break;
            case 'gate_near':          // came in before today, still inside, less than 24 hours so far
                $b->where('vendor_visits.check_out_time IS NULL', null, false)->where('vendor_visits.check_in_time <', $p['today'] . ' 00:00:00')
                    ->where('vendor_visits.check_in_time >', $day);
                break;
            case 'gate_out_window':    // still inside 24 hours or more after coming in
                $b->where('vendor_visits.check_out_time IS NULL', null, false)->where('vendor_visits.check_in_time IS NOT NULL', null, false)
                    ->where('vendor_visits.check_in_time <=', $day);
                break;
            default:
                return false;
        }

        return true;
    }

    /** KPK progress bar: ratio as a whole percent (0 when the base is 0). */
    public static function pct(int $part, int $base): int
    {
        return $base > 0 ? (int) round($part / $base * 100) : 0;
    }

    /**
     * @param array{location?:string,from?:string,to?:string} $filters
     *        location = location code (slug of locations.location_access), from/to = Y-m-d (application / check-in date range)
     */
    public static function collect(BaseConnection $db, ?int $clientId, ?string $regNo, bool $locked = false, ?string $today = null, array $filters = []): array
    {
        $today = $today ?: date('Y-m-d');
        $in30  = date('Y-m-d', strtotime($today . ' +30 days'));

        $period  = self::period($filters, $today);
        $from    = $period['from'];
        $to      = $period['to'];
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

        // ---- KPK summary (getVendorForDashboard) -----------------------------
        $count = static function (string $card) use ($db, $scope, $period): int {
            $b = $scope($db->table('vendors'));
            self::applyCard($b, $card, $period);

            return (int) $b->countAllResults();
        };
        $newApplications = $count('summary_new');
        $renewed         = $count('summary_renewed');
        $approved        = $count('summary_approved');
        $issuance        = $count('summary_issuance');
        $kpkExpired      = $count('summary_expired');
        $kpkExpiring     = $count('summary_expiring');
        $kpkActive       = $count('summary_active');

        // KPK counts registered companies (company permits), not companies that happen to hold a pass
        $activeCompanies = 0;
        if (! $locked && $db->tableExists('companies')) {
            $cb = $db->table('companies')->where('status', 'active');
            if ($regNo !== null) {
                $cb->where('registration_no', $regNo);
            } elseif ($clientId !== null && $db->fieldExists('client_id', 'companies')) {
                $cb->groupStart()->where('client_id', $clientId)->orWhere('client_id IS NULL', null, false)->groupEnd();
            }
            $activeCompanies = (int) $cb->countAllResults();
        }
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
                $visits = static function (string $card) use ($db, $scope, $period): int {
                    $b = $scope($db->table('vendor_visits')->join('vendors', 'vendors.id = vendor_visits.vendor_id'));
                    self::applyCard($b, $card, $period);

                    return (int) $b->countAllResults();
                };
                $gate['available']       = true;
                $gate['checkIn']         = $visits('gate_in');
                $gate['checkOut']        = $visits('gate_out');
                $gate['nearOutOfWindow'] = $visits('gate_near');
                $gate['outOfWindow']     = $visits('gate_out_window');
            }
        } catch (\Throwable $e) {
            log_message('error', 'VendorDashboardStats gate: ' . $e->getMessage());
        }

        return [
            'summary' => [
                'newApplications' => $newApplications,
                'approved'        => $approved,
                'issuance'        => $issuance,
                'expired'         => $kpkExpired,
                'expiring'        => $kpkExpiring,
                'renewed'         => $renewed,
                'activePasses'    => $kpkActive,
                'activeCompanies' => $activeCompanies,
                'avgPerCompany'   => $activeCompanies > 0 ? round($kpkActive / $activeCompanies, 2) : 0,
                // KPK progress bars (same ratios as dashboard-vendorpass.component.ts)
                'pct' => [
                    'approved' => self::pct($approved, $newApplications),
                    'issuance' => self::pct($issuance, $newApplications),
                    'expiring' => self::pct($kpkExpiring, $kpkExpired),
                    'renewed'  => self::pct($renewed, $kpkExpired),
                    'companies' => self::pct($kpkActive, $activeCompanies),
                ],
                'asAt'            => date('d M Y, g:i A'),
            ],
            'byLocation'    => $byLocation,
            'locationList'  => $locLabels,
            'gate'          => $gate,
            'filters'       => ['location' => $locCode, 'from' => $period['ranged'] ? $from : '', 'to' => $period['ranged'] ? $to : '', 'ranged' => $period['ranged'], 'periodFrom' => $from, 'periodTo' => $to],
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
