<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * Numbers behind the Staff dashboard. Kept out of the controller so the
 * queries can be exercised on their own.
 *
 * Reads the same tables the Staff List page reads (`staff`, `staff_cards`).
 * With a $clientId the figures follow the Staff List rule for shared products:
 * staff created under that client OR holding one of its gates (see
 * client_visibility_helper). Null = everything (platform superadmin).
 */
class StaffDashboardStats
{
    /** @return array<string,mixed> */
    public static function collect(BaseConnection $db, ?string $today = null, ?int $clientId = null): array
    {
        if (! function_exists('visibility_staff_sql')) {
            require_once __DIR__ . '/../Helpers/client_visibility_helper.php';
        }
        $visT = $clientId ? visibility_staff_sql($clientId, 'staff') : '1 = 1'; // table `staff`
        $visS = $clientId ? visibility_staff_sql($clientId, 's') : '1 = 1';     // alias `s`

        $today = $today ?? date('Y-m-d');
        $in30  = date('Y-m-d', strtotime($today . ' +30 days'));

        // ---- Headline numbers -------------------------------------------------
        $total = (int) $db->table('staff')->where($visT, null, false)->countAllResults();

        $statusRows = $db->query(
            "SELECT COALESCE(NULLIF(TRIM(status), ''), 'Unknown') AS label, COUNT(*) AS c FROM staff WHERE {$visT} GROUP BY label"
        )->getResultArray();
        $statusCount = [];
        foreach ($statusRows as $r) {
            $statusCount[strtolower($r['label'])] = (int) $r['c'];
        }
        $active    = $statusCount['active'] ?? 0;
        $suspended = $statusCount['suspended'] ?? 0;
        $inactive  = max(0, $total - $active - $suspended);

        $cardBase = static function () use ($db, $visS) {
            return $db->table('staff_cards c')
                ->join('staff s', 's.id = c.staff_id')
                ->where($visS, null, false)
                ->where("LOWER(c.status) = 'active'", null, false);
        };
        $cardsActive   = (int) $cardBase()->select('COUNT(DISTINCT c.staff_id) AS n', false)->get()->getRow()->n;
        $cardsExpiring = (int) $cardBase()->select('COUNT(DISTINCT c.staff_id) AS n', false)
            ->where('c.expiry_date >=', $today)->where('c.expiry_date <=', $in30)->get()->getRow()->n;
        $cardsExpired  = (int) $cardBase()->select('COUNT(DISTINCT c.staff_id) AS n', false)
            ->where('c.expiry_date <', $today)->get()->getRow()->n;
        $withAnyCard   = (int) $db->table('staff_cards c')->select('COUNT(DISTINCT c.staff_id) AS n', false)->join('staff s', 's.id = c.staff_id')->where($visS, null, false)->get()->getRow()->n;
        $withoutCard   = max(0, $total - $withAnyCard);

        // Documents (CSP / visa / licence) that run out in the next 30 days.
        $docCols = array_values(array_filter(
            ['csp_expiry_date', 'visa_expiry', 'license_expiry'],
            static fn($col) => $db->fieldExists($col, 'staff')
        ));
        $docsExpiring = 0;
        if ($docCols !== []) {
            $q = $db->table('staff')->where($visT, null, false)->groupStart();
            foreach ($docCols as $i => $col) {
                $i === 0 ? $q->groupStart() : $q->orGroupStart();
                $q->where($col . ' >=', $today)->where($col . ' <=', $in30)->groupEnd();
            }
            $docsExpiring = (int) $q->groupEnd()->countAllResults();
        }

        // ---- New staff per month (last 6) ------------------------------------
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $ts = strtotime('first day of -' . $i . ' month', strtotime($today));
            $months[date('Y-m', $ts)] = date('M y', $ts);
        }
        $monthRows = $db->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c
             FROM staff WHERE created_at >= ? AND {$visT} GROUP BY ym",
            [date('Y-m-01 00:00:00', strtotime('first day of -5 month', strtotime($today)))]
        )->getResultArray();
        $perMonth = array_fill_keys(array_keys($months), 0);
        foreach ($monthRows as $r) {
            if (isset($perMonth[$r['ym']])) {
                $perMonth[$r['ym']] = (int) $r['c'];
            }
        }

        // ---- By department (top 6) -------------------------------------------
        $deptRows = $db->query(
            "SELECT COALESCE(NULLIF(TRIM(department), ''), 'Not set') AS label, COUNT(*) AS c
             FROM staff WHERE {$visT} GROUP BY label ORDER BY c DESC, label ASC LIMIT 6"
        )->getResultArray();

        // ---- Tables ----------------------------------------------------------
        $needsAttention = $db->table('staff')
            ->select('id, full_name, staff_no, status, next_action, suspension_period')
            ->where($visT, null, false)
            ->where("LOWER(status) != 'active'", null, false)
            ->where('status IS NOT NULL', null, false)
            ->orderBy('id', 'DESC')->limit(8)->get()->getResultArray();

        $expiringCards = $db->table('staff_cards c')
            ->select('s.id, s.full_name, s.staff_no, c.expiry_date')
            ->join('staff s', 's.id = c.staff_id')
            ->where($visS, null, false)
            ->where("LOWER(c.status) = 'active'", null, false)
            ->where('c.expiry_date >=', $today)->where('c.expiry_date <=', $in30)
            ->orderBy('c.expiry_date', 'ASC')->limit(8)->get()->getResultArray();
        foreach ($expiringCards as &$row) {
            $row['days_left'] = (int) floor((strtotime($row['expiry_date']) - strtotime($today)) / 86400);
        }
        unset($row);

        $recent = $db->table('staff')
            ->select('id, full_name, staff_no, department, status, created_at')
            ->where($visT, null, false)
            ->orderBy('id', 'DESC')->limit(8)->get()->getResultArray();

        return [
            'total'          => $total,
            'active'         => $active,
            'suspended'      => $suspended,
            'inactive'       => $inactive,
            'cardsActive'    => $cardsActive,
            'cardsExpiring'  => $cardsExpiring,
            'cardsExpired'   => $cardsExpired,
            'withoutCard'    => $withoutCard,
            'docsExpiring'   => $docsExpiring,
            'monthLabels'    => array_values($months),
            'monthValues'    => array_values($perMonth),
            'departments'    => $deptRows,
            'needsAttention' => $needsAttention,
            'expiringCards'  => $expiringCards,
            'recent'         => $recent,
        ];
    }
}
