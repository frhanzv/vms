<?php

namespace App\Controllers;

/**
 * Monitoring > Monitoring (KPK "monitoring/transaction-list").
 *
 * One live page for every gate movement of the three pass kinds VMS records:
 *   STAFF      staff_visits      (+ denied taps in staff_card_logs = "Rejected")
 *   VENDORPASS vendor_visits     (lane from vendor_card_logs)
 *   VISITOR    invitation_visitors (lane from visitor_card_logs)
 *
 * It only reads those tables - nothing here writes gate data. Same client scope as the reports.
 * Needs the Role > Monitoring > Monitoring permission.
 */
class GateMonitoring extends BaseController
{
    private const OUT_OF_WINDOW_HOURS = 24;
    private const MAX_ROWS            = 5000;

    private const SORTS = [
        'la-z' => 'location ASC', 'lz-a' => 'location DESC',
        'ln-a' => 'in_lane ASC',  'ln-d' => 'in_lane DESC',
        'dt-a' => 'time_in ASC',  'dt-d' => 'time_in DESC',
    ];

    private function db()
    {
        return \Config\Database::connect();
    }

    private function allowed(): bool
    {
        helper('access');

        return has_access('monitoring', 'monitoring');
    }

    private function json(array $payload, int $code = 200)
    {
        return $this->response->setStatusCode($code)->setJSON($payload);
    }

    private function has(string $table, string $col): bool
    {
        static $cache = [];
        $k = $table . '.' . $col;

        return $cache[$k] ??= ($this->db()->tableExists($table) && $this->db()->fieldExists($col, $table));
    }

    /** The client whose data is shown: the platform admin may pick one (0 = all), everyone else sees its own. */
    private function clientId(): int
    {
        helper(['role', 'feature']);
        if (is_platform_superadmin()) {
            return (int) $this->request->getGet('client_id');
        }

        return (int) current_client_id();
    }

    private function clients(): array
    {
        helper('role');
        if (! is_platform_superadmin() || ! $this->db()->tableExists('clients')) {
            return [];
        }

        return $this->db()->table('clients')->select('id, name')->where('status', 'active')->orderBy('name')->get()->getResultArray();
    }

    // ------------------------------------------------------------------ page

    public function index()
    {
        if (! $this->allowed()) {
            return redirect()->to(base_url('dashboard'))->with('error', 'You are not allowed to open Monitoring.');
        }
        helper('role');

        return view('monitoring/transaction_list', [
            'pageTitle' => 'Monitoring - SafeG',
            'clients'   => $this->clients(),
            'oowHours'  => self::OUT_OF_WINDOW_HOURS,
        ]);
    }

    // ------------------------------------------------------------------ data

    /** All gate movements as one UNION: same columns whatever the pass kind. */
    public function unionSql(int $clientId, bool $staffPlate = false): string
    {
        $db = $this->db();
        helper(['client_visibility', 'vendor_client_scope']);
        $parts = [];

        // ---- STAFF
        if ($db->tableExists('staff_visits') && $db->tableExists('staff')) {
            $scope = $clientId > 0 && $db->tableExists('staff_client_approvals') ? ' AND ' . visibility_staff_sql($clientId, 's') : '';
            $staffPlateSql = $staffPlate && $this->has('vehicles', 'staff_id') && $this->has('vehicles', 'plate_no')
                ? "COALESCE((SELECT sveh.plate_no FROM vehicles sveh WHERE sveh.staff_id = s.id AND sveh.status = 'Issued' ORDER BY sveh.id DESC LIMIT 1), '')" : "''";
            $photo = $this->has('staff', 'photo') ? "IF(COALESCE(s.photo,'') <> '', CONCAT('uploads/staff_photos/', s.photo), '')" : "''";
            $parts[] = "SELECT 'staff' AS kind, s.id AS ref_id, s.full_name AS name, COALESCE(s.ic_passport,'') AS ic, 'STAFF' AS pass_type,
                    COALESCE(loc_i.location_access, loc_o.location_access, '') AS location, COALESCE(li.lane,'') AS in_lane, COALESCE(lo.lane,'') AS out_lane,
                    COALESCE(vc.card_id,'') AS card_no, {$staffPlateSql} AS plate_no, sv.check_in_time AS time_in, sv.check_out_time AS time_out,
                    IF(sv.check_out_time IS NULL, 'Gate In', 'Gate Out') AS status, {$photo} AS photo, CONCAT('staff-v-', sv.id) AS tx_key
                FROM staff_visits sv
                JOIN staff s ON s.id = sv.staff_id
                LEFT JOIN lanes li ON li.id = sv.check_in_lane_id LEFT JOIN locations loc_i ON loc_i.id = li.location_id
                LEFT JOIN lanes lo ON lo.id = sv.check_out_lane_id LEFT JOIN locations loc_o ON loc_o.id = lo.location_id
                LEFT JOIN visitor_cards vc ON vc.id = sv.card_id
                WHERE sv.check_in_time IS NOT NULL" . $scope;

            if ($db->tableExists('staff_card_logs')) {
                $parts[] = "SELECT 'staff', COALESCE(s.id, 0), COALESCE(s.full_name, 'UNKNOWN CARD'), COALESCE(s.ic_passport,''), 'STAFF',
                        COALESCE(loc.location_access,''), COALESCE(l.lane,''), '', COALESCE(vc.card_id,''), '', sl.scanned_at, NULL, 'Rejected', '', CONCAT('staff-d-', sl.id)
                    FROM staff_card_logs sl
                    LEFT JOIN staff s ON s.id = sl.staff_id
                    LEFT JOIN lanes l ON l.id = sl.lane_id LEFT JOIN locations loc ON loc.id = l.location_id
                    LEFT JOIN visitor_cards vc ON vc.id = sl.card_id
                    WHERE sl.action = 'denied'" . ($scope !== '' ? ' AND (s.id IS NULL OR ' . substr($scope, 5) . ')' : '');
            }
        }

        // ---- VENDOR PASS
        if ($db->tableExists('vendor_visits') && $db->tableExists('vendors')) {
            $scope = $clientId > 0 && $db->tableExists('vendor_client_approvals') ? ' AND ' . vendor_client_scope_sql($clientId, 'v') : '';
            $photo = $this->has('vendors', 'facial_photo')
                ? "IF(COALESCE(v.facial_photo,'') <> '', CONCAT('uploads/facial_photos/', v.facial_photo), '')"
                : ($this->has('vendors', 'photo') ? "IF(COALESCE(v.photo,'') <> '', CONCAT('uploads/vendor_photos/', v.photo), '')" : "''");
            // plate: on the vendor record when there is one, otherwise the vehicle this vendor pass drives
            $plate = $this->has('vendors', 'vehicle_registration') ? "COALESCE(v.vehicle_registration,'')"
                : ($db->tableExists('vehicle_drivers') && $this->has('vehicles', 'plate_no')
                    ? "COALESCE((SELECT vh.plate_no FROM vehicle_drivers vd JOIN vehicles vh ON vh.id = vd.vehicle_id WHERE vd.vendor_id = v.id ORDER BY vd.id DESC LIMIT 1), '')" : "''");
            $lane  = static fn(string $action) => "(SELECT x.lane_id FROM vendor_card_logs x WHERE x.vendor_id = vv.vendor_id AND x.action = '{$action}' AND x.scanned_at = " . ($action === 'checkin' ? 'vv.check_in_time' : 'vv.check_out_time') . ' LIMIT 1)';
            $hasLogs = $db->tableExists('vendor_card_logs');
            $joinIn  = $hasLogs ? 'LEFT JOIN lanes li ON li.id = ' . $lane('checkin') . ' LEFT JOIN locations loc_i ON loc_i.id = li.location_id' : 'LEFT JOIN lanes li ON 1=0 LEFT JOIN locations loc_i ON 1=0';
            $joinOut = $hasLogs ? 'LEFT JOIN lanes lo ON lo.id = ' . $lane('checkout') . ' LEFT JOIN locations loc_o ON loc_o.id = lo.location_id' : 'LEFT JOIN lanes lo ON 1=0 LEFT JOIN locations loc_o ON 1=0';
            $parts[] = "SELECT 'vendor', v.id, v.full_name, COALESCE(NULLIF(v.ic_no,''), NULLIF(v.passport_no,''), ''), 'VENDORPASS',
                    COALESCE(loc_i.location_access, loc_o.location_access, ''), COALESCE(li.lane,''), COALESCE(lo.lane,''),
                    COALESCE(vc.card_id,''), {$plate}, vv.check_in_time, vv.check_out_time,
                    IF(vv.check_out_time IS NULL, 'Gate In', 'Gate Out'), {$photo}, CONCAT('vendor-v-', vv.id)
                FROM vendor_visits vv
                JOIN vendors v ON v.id = vv.vendor_id
                {$joinIn} {$joinOut}
                LEFT JOIN visitor_cards vc ON vc.id = vv.vendor_card_id
                WHERE vv.check_in_time IS NOT NULL" . $scope;
        }

        // ---- VISITOR
        if ($db->tableExists('invitation_visitors') && $db->tableExists('invitations')) {
            $scope = $clientId > 0 ? ' AND ' . visibility_invitation_sql($clientId, 'i') : '';
            $plate = $this->has('invitation_visitors', 'vehicle_registration') ? "COALESCE(iv.vehicle_registration,'')" : "''";
            $hasLogs = $db->tableExists('visitor_card_logs');
            $lane  = static fn(string $action) => "(SELECT x.lane_id FROM visitor_card_logs x WHERE x.invitation_id = i.id AND x.action = '{$action}' ORDER BY x.scanned_at DESC LIMIT 1)";
            $joinIn  = $hasLogs ? 'LEFT JOIN lanes li ON li.id = ' . $lane('checkin') . ' LEFT JOIN locations loc_i ON loc_i.id = li.location_id' : 'LEFT JOIN lanes li ON 1=0 LEFT JOIN locations loc_i ON 1=0';
            $joinOut = $hasLogs ? 'LEFT JOIN lanes lo ON lo.id = ' . $lane('checkout') . ' LEFT JOIN locations loc_o ON loc_o.id = lo.location_id' : 'LEFT JOIN lanes lo ON 1=0 LEFT JOIN locations loc_o ON 1=0';
            $parts[] = "SELECT 'visitor', i.id, COALESCE(NULLIF(iv.full_name,''), i.full_name), COALESCE(NULLIF(iv.ic_passport,''), i.ic_passport, ''), 'VISITOR',
                    COALESCE(loc_i.location_access, loc_o.location_access, ''), COALESCE(li.lane,''), COALESCE(lo.lane,''),
                    COALESCE(vc.card_id,''), {$plate}, iv.check_in_time, iv.check_out_time,
                    IF(iv.check_out_time IS NULL, 'Gate In', 'Gate Out'), '', CONCAT('visitor-v-', iv.id)
                FROM invitation_visitors iv
                JOIN invitations i ON i.id = iv.invitation_id
                {$joinIn} {$joinOut}
                LEFT JOIN visitor_cards vc ON vc.id = iv.visitor_card_id
                WHERE iv.check_in_time IS NOT NULL" . $scope;
        }

        return $parts ? implode(' UNION ALL ', $parts) : "SELECT 'x' AS kind, 0 AS ref_id, '' AS name, '' AS ic, '' AS pass_type, '' AS location, '' AS in_lane, '' AS out_lane, '' AS card_no, '' AS plate_no, NULL AS time_in, NULL AS time_out, '' AS status, '' AS photo, '' AS tx_key FROM DUAL WHERE 1=0";
    }

    private function validDate(?string $d): ?string
    {
        return $d && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && strtotime($d) ? $d : null;
    }

    /** WHERE (on the outer tx alias) from the page filters. */
    private function where(): string
    {
        $db = $this->db();
        $g  = fn(string $k) => trim((string) $this->request->getGet($k));
        $w  = ['1=1'];

        $from = $this->validDate($g('from'));
        $to   = $this->validDate($g('to'));
        if ($from) {
            $w[] = 'tx.time_in >= ' . $db->escape($from . ' 00:00:00');
        }
        if ($to) {
            $w[] = 'tx.time_in <= ' . $db->escape($to . ' 23:59:59');
        }
        if ($g('tab') === 'reject') {
            $w[] = "tx.status = 'Rejected'";
        } else {
            $w[] = "tx.status <> 'Rejected'";
        }
        $status = $g('status');
        if (in_array($status, ['Gate In', 'Gate Out', 'Rejected'], true)) {
            $w[] = 'tx.status = ' . $db->escape($status);
        }
        if ($g('oow') === '1') {
            $w[] = "tx.status = 'Gate In' AND tx.time_in <= " . $db->escape(date('Y-m-d H:i:s', time() - self::OUT_OF_WINDOW_HOURS * 3600));
        }
        $type = $g('type');
        if (in_array($type, ['VENDORPASS', 'STAFF', 'VISITOR'], true)) {
            $w[] = 'tx.pass_type = ' . $db->escape($type);
        }
        if (($q = $g('q')) !== '') {
            $like = $db->escape('%' . $db->escapeLikeString($q) . '%');
            $w[]  = "(tx.name LIKE {$like} OR tx.ic LIKE {$like} OR tx.location LIKE {$like} OR tx.in_lane LIKE {$like} OR tx.out_lane LIKE {$like} OR tx.card_no LIKE {$like} OR tx.plate_no LIKE {$like})";
        }

        return implode(' AND ', $w);
    }

    private function order(): string
    {
        return self::SORTS[trim((string) $this->request->getGet('sort'))] ?? 'time_in DESC';
    }

    public function data()
    {
        if (! $this->allowed()) {
            return $this->json(['success' => false, 'message' => 'Not allowed.'], 403);
        }
        try {
            $db     = $this->db();
            $cid    = $this->clientId();
            $union  = $this->unionSql($cid);
            $page   = max(1, (int) $this->request->getGet('page'));
            $per    = in_array((int) $this->request->getGet('per_page'), [10, 20, 30, 40], true) ? (int) $this->request->getGet('per_page') : 10;
            $where  = $this->where();

            $total = (int) ($db->query("SELECT COUNT(*) c FROM ({$union}) tx WHERE {$where}")->getRow()->c ?? 0);
            $pages = max(1, (int) ceil($total / $per));
            $page  = min($page, $pages);
            $rows  = $db->query("SELECT tx.* FROM ({$union}) tx WHERE {$where} ORDER BY tx.{$this->order()}, tx.ref_id DESC LIMIT {$per} OFFSET " . (($page - 1) * $per))->getResultArray();

            return $this->json([
                'success' => true, 'rows' => $rows, 'total' => $total, 'pages' => $pages, 'page' => $page, 'per_page' => $per,
                'stats' => $this->stats($union), 'charts' => $this->charts($union, (string) $this->request->getGet('lane')),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'GateMonitoring::data ' . $e->getMessage());

            return $this->json(['success' => false, 'message' => 'Could not load monitoring data. Run "php spark migrate" and try again.']);
        }
    }

    private function stats(string $union): array
    {
        $db     = $this->db();
        $cutoff = $db->escape(date('Y-m-d H:i:s', time() - self::OUT_OF_WINDOW_HOURS * 3600));
        $out    = ['visitor' => 0, 'staff' => 0, 'vendor' => 0, 'denied' => 0, 'out_of_window' => 0, 'in_visitor' => 0, 'in_staff' => 0, 'in_vendor' => 0, 'in_all' => 0];
        $map    = ['VISITOR' => 'visitor', 'STAFF' => 'staff', 'VENDORPASS' => 'vendor'];
        $res    = $db->query("SELECT pass_type,
                SUM(status <> 'Rejected' AND DATE(time_in) = CURDATE()) AS today,
                SUM(status = 'Rejected' AND DATE(time_in) = CURDATE()) AS denied,
                SUM(status = 'Gate In') AS inprem,
                SUM(status = 'Gate In' AND time_in <= {$cutoff}) AS oow
            FROM ({$union}) tx GROUP BY pass_type")->getResultArray();
        foreach ($res as $r) {
            $k = $map[$r['pass_type']] ?? null;
            if ($k === null) {
                continue;
            }
            $out[$k]               = (int) $r['today'];
            $out['in_' . $k]       = (int) $r['inprem'];
            $out['denied']        += (int) $r['denied'];
            $out['out_of_window'] += (int) $r['oow'];
            $out['in_all']        += (int) $r['inprem'];
        }

        return $out;
    }

    /** Chart series for the right-hand panel, worked out from the last 90 days of movements. */
    private function charts(string $union, string $laneFilter): array
    {
        $db   = $this->db();
        $rows = $db->query("SELECT pass_type, in_lane, out_lane, time_in, time_out FROM ({$union}) tx WHERE tx.status <> 'Rejected' AND tx.time_in >= " . $db->escape(date('Y-m-d 00:00:00', strtotime('-90 days'))) . ' LIMIT 40000')->getResultArray();

        $today   = date('Y-m-d');
        $perLane = [];
        $lanes   = [];
        $hourIn  = array_fill(0, 24, 0);
        $hourOut = array_fill(0, 24, 0);
        $dowIn   = array_fill(1, 7, 0);
        $dowOut  = array_fill(1, 7, 0);
        $monthly = ['VISITOR' => [], 'VENDORPASS' => [], 'STAFF' => []];
        $daily   = ['VISITOR' => [], 'VENDORPASS' => [], 'STAFF' => []];
        $cut30   = date('Y-m-d', strtotime('-30 days'));
        $cut84   = date('Y-m-d', strtotime('-84 days'));

        foreach ($rows as $r) {
            $ti = $r['time_in'];
            $to = $r['time_out'];
            foreach ([['in', $ti, $r['in_lane']], ['out', $to, $r['out_lane']]] as [$dir, $t, $lane]) {
                if (! $t) {
                    continue;
                }
                $d    = substr($t, 0, 10);
                $lane = $lane !== '' ? $lane : '-';
                $lanes[$lane] = true;
                if ($d === $today) {
                    $perLane[$lane][$dir] = ($perLane[$lane][$dir] ?? 0) + 1;
                }
                if ($d >= $cut30 && ($laneFilter === '' || $laneFilter === $lane)) {
                    $h = (int) substr($t, 11, 2);
                    $dir === 'in' ? $hourIn[$h]++ : $hourOut[$h]++;
                }
                if ($d >= $cut84) {
                    $w = (int) date('N', strtotime($d));
                    $dir === 'in' ? $dowIn[$w]++ : $dowOut[$w]++;
                }
            }
            $pt = $r['pass_type'];
            if (isset($monthly[$pt])) {
                $m = substr($ti, 0, 7);
                $monthly[$pt][$m] = ($monthly[$pt][$m] ?? 0) + 1;
                $d = substr($ti, 0, 10);
                $daily[$pt][$d] = ($daily[$pt][$d] ?? 0) + 1;
            }
        }

        ksort($perLane);
        $laneNames = array_keys($lanes);
        sort($laneNames);
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = date('Y-m', strtotime("first day of -{$i} month"));
        }
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $days[] = date('Y-m-d', strtotime("-{$i} day"));
        }
        $series = [];
        foreach (['VISITOR', 'VENDORPASS', 'STAFF'] as $pt) {
            $series[$pt] = [
                'month' => array_map(static fn($m) => $monthly[$pt][$m] ?? 0, $months),
                'week'  => array_map(static fn($d) => $daily[$pt][$d] ?? 0, $days),
            ];
        }

        return [
            'lanes'      => $laneNames,
            'per_lane'   => ['labels' => array_keys($perLane), 'in' => array_map(static fn($v) => $v['in'] ?? 0, array_values($perLane)), 'out' => array_map(static fn($v) => $v['out'] ?? 0, array_values($perLane))],
            'per_hour'   => ['in' => array_map(static fn($v) => round($v / 30, 2), $hourIn), 'out' => array_map(static fn($v) => round($v / 30, 2), $hourOut)],
            'per_day'    => ['labels' => ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'], 'in' => array_map(static fn($v) => round($v / 12, 1), array_values($dowIn)), 'out' => array_map(static fn($v) => round($v / 12, 1), array_values($dowOut))],
            'total'      => ['months' => $months, 'days' => $days, 'series' => $series],
        ];
    }

    /** Picture + recent movements of one person (search box or the magnifier on a row). */
    public function person()
    {
        if (! $this->allowed()) {
            return $this->json(['success' => false, 'message' => 'Not allowed.'], 403);
        }
        $q = trim((string) $this->request->getGet('q'));
        if ($q === '') {
            return $this->json(['success' => false, 'message' => 'Type an IC / passport / name.']);
        }
        try {
            $db    = $this->db();
            $union = $this->unionSql($this->clientId());
            $like  = $db->escape('%' . $db->escapeLikeString($q) . '%');
            $exact = $db->escape($q);
            $first = $db->query("SELECT * FROM ({$union}) tx WHERE tx.ic = {$exact} OR tx.name LIKE {$like} OR tx.ic LIKE {$like} ORDER BY (tx.ic = {$exact}) DESC, tx.time_in DESC LIMIT 1")->getRowArray();
            if (! $first) {
                return $this->json(['success' => true, 'found' => false, 'message' => 'No record found.']);
            }
            $rows = $db->query("SELECT location, time_in, time_out, status, plate_no FROM ({$union}) tx WHERE tx.kind = " . $db->escape($first['kind']) . ' AND tx.ref_id = ' . (int) $first['ref_id'] . ' ORDER BY tx.time_in DESC LIMIT 20')->getResultArray();

            return $this->json([
                'success' => true, 'found' => true,
                'person'  => ['name' => $first['name'], 'ic' => $first['ic'], 'type' => $first['pass_type'], 'plate_no' => $first['plate_no'], 'photo_url' => $first['photo'] !== '' ? base_url($first['photo']) : ''],
                'rows'    => $rows,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'GateMonitoring::person ' . $e->getMessage());

            return $this->json(['success' => false, 'message' => 'Could not look that person up.']);
        }
    }

    /** Transaction Info (KPK monitoring/transaction-info/:id): everything about one gate movement. */
    public function info(string $key = '')
    {
        if (! $this->allowed()) {
            return redirect()->to(base_url('dashboard'))->with('error', 'You are not allowed to open Monitoring.');
        }
        if (! preg_match('/^(staff|vendor|visitor)-(v|d)-\d+$/', $key)) {
            return redirect()->to(base_url('monitoring'))->with('error', 'Transaction not found.');
        }
        $db  = $this->db();
        $row = $db->query('SELECT tx.* FROM (' . $this->unionSql($this->clientId()) . ') tx WHERE tx.tx_key = ' . $db->escape($key) . ' LIMIT 1')->getRowArray();
        if (! $row) {
            return redirect()->to(base_url('monitoring'))->with('error', 'Transaction not found.');
        }

        $vehicle = null;
        $plate   = strtoupper(preg_replace('/\s+/', '', (string) $row['plate_no']));
        if ($plate !== '' && $db->tableExists('vehicles') && $db->fieldExists('plate_no', 'vehicles')) {
            $b = $db->table('vehicles v')->select('v.*')->where("UPPER(REPLACE(v.plate_no, ' ', '')) = " . $db->escape($plate), null, false)->orderBy('v.id', 'DESC');
            if ($db->tableExists('vehicle_types') && $db->fieldExists('vehicle_type_id', 'vehicles')) {
                $b->select('vt.name AS vehicle_type_name')->join('vehicle_types vt', 'vt.id = v.vehicle_type_id', 'left');
            }
            $vehicle = $b->get(1)->getRowArray() ?: null;
        }

        return view('monitoring/transaction_info', ['pageTitle' => 'Transaction Info - SafeG', 'tx' => $row, 'vehicle' => $vehicle]);
    }

    public function export()
    {
        if (! $this->allowed()) {
            return redirect()->to(base_url('dashboard'));
        }
        $db    = $this->db();
        $union = $this->unionSql($this->clientId());
        $rows  = $db->query("SELECT tx.* FROM ({$union}) tx WHERE {$this->where()} ORDER BY tx.{$this->order()} LIMIT " . self::MAX_ROWS)->getResultArray();

        $fh = fopen('php://temp', 'w+');
        fwrite($fh, "\xEF\xBB\xBF");
        fputcsv($fh, ['No', 'Name', 'Type Of Vendor', 'IC/Passport No', 'Location', 'In Lane No', 'Out Lane No', 'Card ID', 'Plate No', 'Time In', 'Time Out', 'Status']);
        foreach ($rows as $i => $r) {
            fputcsv($fh, [$i + 1, $r['name'], $r['pass_type'], $r['ic'], $r['location'], $r['in_lane'], $r['out_lane'], $r['card_no'], $r['plate_no'], $r['time_in'], $r['time_out'], strtoupper($r['status'])]);
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="monitoring-' . date('Ymd-His') . '.csv"')->setBody($csv);
    }
}
