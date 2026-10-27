<?php

namespace App\Libraries;

use App\Controllers\GateMonitoring;
use CodeIgniter\Database\BaseConnection;

/**
 * Numbers for the Vehicle dashboard (KPK Dashboard > Vehicle).
 *
 * VMS records gate movements per person, not per vehicle, so a "vehicle movement" here is a gate
 * transaction that carries a plate number: vendor / visitor visits (plate on the record) and staff
 * visits (the staff member's latest issued vehicle). Rejected taps are not counted. Reads only.
 */
class VehicleDashboardStats
{
    public static function collect(BaseConnection $db, int $clientId, int $page, int $perPage, string $search = ''): array
    {
        $out = [
            'registered' => ['staff' => 0, 'vendor' => 0],
            'gate'       => ['in' => 0, 'out' => 0],
            'months'     => [],
            'rows'       => [],
            'total'      => 0,
            'page'       => max(1, $page),
            'perPage'    => $perPage,
            'search'     => $search,
            'available'  => true,
        ];

        // ---- registered today (KPK: vehicles whose application date is today)
        if ($db->tableExists('vehicles')) {
            $b = $db->table('vehicles')->select('SUM(staff_id IS NOT NULL) AS s, SUM(staff_id IS NULL) AS v', false)->where('DATE(created_at) = CURDATE()', null, false);
            if ($clientId > 0 && $db->fieldExists('client_id', 'vehicles')) {
                $b->where(VehicleClientApprovals::scopeSql($db, $clientId, $db->prefixTable('vehicles')), null, false); // clients owning one of its locations
            }
            if ($db->fieldExists('soft_delete', 'vehicles')) {
                $b->groupStart()->where('soft_delete', 0)->orWhere('soft_delete IS NULL', null, false)->groupEnd();
            }
            $r = $b->get()->getRowArray() ?: [];
            $out['registered'] = ['staff' => (int) ($r['s'] ?? 0), 'vendor' => (int) ($r['v'] ?? 0)];
        }

        // ---- gate movements that carry a plate
        $union = (new GateMonitoring())->unionSql($clientId, true);
        $base  = "FROM ({$union}) tx WHERE tx.plate_no <> '' AND tx.status <> 'Rejected'";

        $g = $db->query("SELECT SUM(DATE(tx.time_in) = CURDATE()) AS gi, SUM(tx.time_out IS NOT NULL AND DATE(tx.time_out) = CURDATE()) AS go {$base}")->getRowArray() ?: [];
        $out['gate'] = ['in' => (int) ($g['gi'] ?? 0), 'out' => (int) ($g['go'] ?? 0)];

        // ---- entering per month, last 12 months, Staff vs Vendor pass
        $labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $labels[date('Y-m', strtotime("first day of -{$i} month"))] = strtoupper(date('M', strtotime("first day of -{$i} month")));
        }
        $from = array_key_first($labels) . '-01';
        $rows = $db->query("SELECT DATE_FORMAT(tx.time_in, '%Y-%m') AS ym, tx.pass_type, COUNT(*) AS n {$base} AND tx.time_in >= ? GROUP BY ym, tx.pass_type", [$from])->getResultArray();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['ym']][$r['pass_type']] = (int) $r['n'];
        }
        foreach ($labels as $ym => $label) {
            $out['months'][] = ['month' => $label, 'staff' => $map[$ym]['STAFF'] ?? 0, 'vendor' => $map[$ym]['VENDORPASS'] ?? 0];
        }

        // ---- duration of stay (completed visits)
        $done = $base . ' AND tx.time_out IS NOT NULL';

        $typeExpr = '';
        if ($db->tableExists('vehicles') && $db->tableExists('vehicle_types') && $db->fieldExists('vehicle_type_id', 'vehicles')) {
            $typeExpr = "(SELECT t.name FROM vehicles v JOIN vehicle_types t ON t.id = v.vehicle_type_id
                            WHERE UPPER(REPLACE(v.plate_no, ' ', '')) = UPPER(REPLACE(tx.plate_no, ' ', '')) ORDER BY v.id DESC LIMIT 1)";
        }
        $typeSel = $typeExpr !== '' ? ", {$typeExpr} AS vehicle_type" : '';

        // search: vehicle number, type of vehicle, driver type
        $binds = [];
        $search = trim($search);
        if ($search !== '') {
            $like  = '%' . $db->escapeLikeString($search) . '%';
            $conds = ['tx.plate_no LIKE ?', "REPLACE(tx.pass_type, 'VENDORPASS', 'VENDOR PASS') LIKE ?"];
            $binds = [$like, $like];
            if ($typeExpr !== '') {
                $conds[] = "{$typeExpr} LIKE ?";
                $binds[] = $like;
            }
            $done .= ' AND (' . implode(' OR ', $conds) . ')';
        }

        $out['total'] = (int) ($db->query("SELECT COUNT(*) AS c {$done}", $binds)->getRowArray()['c'] ?? 0);
        $offset = (max(1, $page) - 1) * $perPage;
        $out['rows'] = $db->query(
            "SELECT tx.plate_no, tx.pass_type, tx.time_in, tx.time_out, ROUND(TIMESTAMPDIFF(SECOND, tx.time_in, tx.time_out) / 3600, 2) AS duration {$typeSel}
             {$done} ORDER BY tx.time_in DESC LIMIT {$perPage} OFFSET {$offset}",
            $binds
        )->getResultArray();

        return $out;
    }
}
