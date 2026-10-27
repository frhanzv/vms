<?php

namespace App\Controllers;

use CodeIgniter\Database\BaseConnection;

/**
 * Pop-up behind every summary card on the Vendor and Vehicle dashboards (the Visitor
 * dashboard has its own pop-ups). GET dashboard/cardDetail?dash=vendor|vehicle&card=<key>
 * returns the rows the card's number is made of, so the number and the list always agree.
 *
 * Scope rules are the same as the numbers: App\Libraries\VendorDashboardStats (vendor) and
 * App\Libraries\VehicleDashboardStats (vehicle).
 */
class DashboardDetail extends BaseController
{
    private const LIMIT = 2000;

    public function data()
    {
        helper(['dashboard_nav', 'access', 'feature', 'role', 'vendor_company']);

        $dash = (string) $this->request->getGet('dash');
        $card = (string) $this->request->getGet('card');

        if (! in_array($dash, ['vendor', 'vehicle'], true) || ! dashboard_tab_allowed($dash)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Not allowed.']);
        }

        try {
            $db  = \Config\Database::connect();
            $out = $dash === 'vendor' ? $this->vendor($db, $card) : $this->vehicle($db, $card);
        } catch (\Throwable $e) {
            log_message('error', 'DashboardDetail ' . $dash . '/' . $card . ': ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Could not load the details.']);
        }

        if ($out === null) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Unknown card.']);
        }

        return $this->response->setJSON($out);
    }

    // ------------------------------------------------------------------ vendor

    private function vendor(BaseConnection $db, string $card): ?array
    {
        helper('vendor_client_scope');

        $isVendorAccount = is_vendor_admin();
        $clientId        = is_platform_superadmin() ? null : (int) current_company_id();
        $regNo           = null;
        $locked          = false;
        if ($isVendorAccount) {
            $company = current_vendor_company();
            $regNo   = trim((string) ($company['registration_no'] ?? ''));
            $locked  = ($regNo === '');
        }

        $today  = date('Y-m-d');
        $in30   = date('Y-m-d', strtotime($today . ' +30 days'));
        $period = \App\Libraries\VendorDashboardStats::period(['from' => $this->request->getGet('from'), 'to' => $this->request->getGet('to')], $today);
        $from   = $period['from'];
        $to     = $period['to'];
        $locCode = trim((string) $this->request->getGet('location'));

        $scope = static function ($b) use ($clientId, $regNo, $locked, $locCode, $db) {
            if ($locked) {
                return $b->where('1 = 0', null, false);
            }
            if ($clientId !== null) {
                $b->where(vendor_client_scope_sql($clientId, 'vendors'), null, false);
            }
            if ($regNo !== null) {
                $b->where('vendors.vendor_company_reg_id', $regNo);
            }
            if ($locCode !== '') {
                $col = "REPLACE(" . $db->prefixTable('vendors') . ".location_access, ' ', '')";
                $b->where('(' . $col . ' = ' . $db->escape($locCode)
                    . ' OR ' . $col . ' LIKE ' . $db->escape($locCode . ',%')
                    . ' OR ' . $col . ' LIKE ' . $db->escape('%,' . $locCode)
                    . ' OR ' . $col . ' LIKE ' . $db->escape('%,' . $locCode . ',%') . ')', null, false);
            }
            return $b;
        };
        $active = static fn() => $scope($db->table('vendors'))
            ->where("LOWER(vendors.card_status) = 'active'", null, false)
            ->where("LOWER(vendors.status) NOT IN ('suspended','terminated')", null, false);

        $cols = 'vendors.id, vendors.full_name, vendors.app_no, vendors.vendor_company_name, vendors.card_type, vendors.status, vendors.card_status, vendors.pass_expiry, vendors.created_at';
        $title = '';
        $sub   = '';
        $q     = null;
        $order = 'vendors.id DESC';

        switch ($card) {
            case 'kpi_total':
                $title = 'Total applications'; $sub = 'Every vendor pass application';
                $q = $scope($db->table('vendors'));
                break;
            case 'kpi_active':
                $title = 'Active passes'; $sub = 'Passes with an active card';
                $q = $active();
                break;
            case 'kpi_expiring':
                $title = 'Expiring in 30 days'; $sub = 'Active passes that expire by ' . date('d M Y', strtotime($in30));
                $q = $active()->where('vendors.pass_expiry >=', $today)->where('vendors.pass_expiry <=', $in30);
                $order = 'vendors.pass_expiry ASC';
                break;
            // KPK summary tiles: same rules as the numbers (VendorDashboardStats::applyCard)
            case 'summary_active':
                $title = 'Total Active Pass'; $sub = 'Active card, not expired' . ($period['ranged'] ? ', applied ' . $from . ' to ' . $to : '');
                break;
            case 'summary_expiring':
                $title = 'Expiring in 30 Days'; $sub = 'Passes that expire by ' . date('d M Y', strtotime($in30));
                $order = 'vendors.pass_expiry ASC';
                break;
            case 'summary_expired':
                $title = 'Expired'; $sub = 'Passes that expired ' . ($period['expFrom'] === $period['expTo'] ? 'on ' . $period['expFrom'] : $period['expFrom'] . ' to ' . $period['expTo']);
                $order = 'vendors.pass_expiry DESC';
                break;
            case 'summary_renewed':
                $title = 'Total Pass Renewed'; $sub = 'Renewal applications, ' . $from . ' to ' . $to;
                break;
            case 'kpi_awaiting':
                $title = $isVendorAccount ? 'Awaiting KPK' : 'Awaiting approval'; $sub = 'Applications waiting for a decision';
                $q = $scope($db->table('vendors'))
                    ->where("LOWER(COALESCE(vendors.status,'')) NOT IN ('draft','rejected','approved','suspended','terminated')", null, false)
                    ->where("LOWER(COALESCE(vendors.card_status,'')) NOT IN ('active','terminated')", null, false);
                break;
            case 'summary_new':
                return $this->vendorKpk($db, $scope, $card, $period, ['New Application', 'New applications, ' . $from . ' to ' . $to]);
            case 'summary_approved':
                $title = 'Approved'; $sub = 'Approved, still in process, ' . $from . ' to ' . $to;
                break;
            case 'summary_issuance':
                $title = 'Pass Issuance'; $sub = 'Approved and waiting for collection, ' . $from . ' to ' . $to;
                break;
            case 'gate_in':
            case 'gate_out':
            case 'gate_near':
            case 'gate_out_window':
                return $this->vendorKpk($db, $scope, $card, $period, [
                    'gate_in'         => ['Total Check IN', 'Vendor check-ins, ' . $from . ' to ' . $to],
                    'gate_out'        => ['Total Check Out', 'Vendor check-outs, ' . $from . ' to ' . $to],
                    'gate_near'       => ['Near Out Of Window', 'Came in before today, still inside, less than 24 hours'],
                    'gate_out_window' => ['Out Of Window', 'Still inside 24 hours or more after coming in'],
                ][$card]);
            default:
                return null;
        }
        if ($q === null) {   // a KPK summary tile
            $q = $scope($db->table('vendors'));
            \App\Libraries\VendorDashboardStats::applyCard($q, $card, $period);
        }

        $rows = $q->select($cols, false)->orderBy($order, '', false)->limit(self::LIMIT + 1)->get()->getResultArray();
        $trunc = count($rows) > self::LIMIT;
        $rows  = array_slice($rows, 0, self::LIMIT);

        $out = [];
        $urls = [];
        foreach ($rows as $r) {
            $out[]  = [
                trim((string) $r['full_name']) ?: '-',
                (string) ($r['app_no'] ?? '') ?: '-',
                (string) ($r['vendor_company_name'] ?? '') ?: '-',
                (string) ($r['card_type'] ?? '') ?: '-',
                (string) ($r['status'] ?? '') ?: '-',
                $this->date($r['pass_expiry'] ?? null, 'd M Y'),
                $this->date($r['created_at'] ?? null, 'd M Y H:i'),
            ];
            $urls[] = base_url('vendorpassrequest/view/' . (int) $r['id']);
        }

        return [
            'title' => $title, 'subtitle' => $sub, 'icon' => 'local_shipping',
            'noun' => 'records',
            'columns' => ['Name', 'App No', 'Company', 'Card Type', 'Status', 'Pass Expiry', 'Applied'],
            'rows' => $out, 'urls' => $urls, 'truncated' => $trunc,
            'link' => ['label' => 'Open vendor pass list', 'url' => base_url('vendors')],
        ];
    }

    /**
     * KPK's five dashboard pop-ups (New Application, Near Out Of Window, Out Of Window, Check In, Check Out)
     * share one table: App Date, Full Name, IC No / Passport No, Company Name, Location, Time In, Time Out.
     */
    private function vendorKpk(BaseConnection $db, \Closure $scope, string $card, array $period, array $title): array
    {
        $rows  = [];
        $trunc = false;
        $sel   = 'vendors.id, vendors.full_name, vendors.ic_no, vendors.passport_no, vendors.vendor_company_name, vendors.location_access, '
               . 'vendors.created_at AS app_date';
        if ($card === 'summary_new') {
            $b = $scope($db->table('vendors'));
            if ($db->tableExists('vendor_visits')) {   // last gate visit of each applicant
                $last = '(SELECT %s FROM ' . $db->prefixTable('vendor_visits') . ' lv WHERE lv.vendor_id = vendors.id ORDER BY lv.check_in_time DESC LIMIT 1)';
                $sel .= ', ' . sprintf($last, 'lv.check_in_time') . ' AS time_in, ' . sprintf($last, 'lv.check_out_time') . ' AS time_out';
            } else {
                $sel .= ', NULL AS time_in, NULL AS time_out';
            }
            $order = 'vendors.id DESC';
        } elseif ($db->tableExists('vendor_visits')) {
            $b     = $scope($db->table('vendor_visits')->join('vendors', 'vendors.id = vendor_visits.vendor_id'));
            $sel  .= ', vendor_visits.check_in_time AS time_in, vendor_visits.check_out_time AS time_out';
            $order = $card === 'gate_out' ? 'vendor_visits.check_out_time DESC' : 'vendor_visits.check_in_time DESC';
        }
        if (isset($b)) {
            \App\Libraries\VendorDashboardStats::applyCard($b, $card, $period);
            $rows  = $b->select($sel, false)->orderBy($order, '', false)->limit(self::LIMIT + 1)->get()->getResultArray();
            $trunc = count($rows) > self::LIMIT;
            $rows  = array_slice($rows, 0, self::LIMIT);
        }

        $labels = [];
        try {
            $labels = (new \App\Models\VendorLocationModel())->getAllLabelMap();
        } catch (\Throwable $e) {
            $labels = [];
        }
        $out  = [];
        $urls = [];
        foreach ($rows as $r) {
            $locs = array_filter(array_map('trim', explode(',', (string) ($r['location_access'] ?? ''))));
            $out[] = [
                $this->date($r['app_date'] ?? null, 'd M Y'),
                trim((string) $r['full_name']) ?: '-',
                (string) (($r['ic_no'] ?? '') ?: ($r['passport_no'] ?? '')) ?: '-',
                (string) ($r['vendor_company_name'] ?? '') ?: '-',
                $locs ? implode(', ', array_map(static fn($c) => $labels[$c] ?? $c, $locs)) : '-',
                $this->date($r['time_in'] ?? null, 'd M Y H:i'),
                $this->date($r['time_out'] ?? null, 'd M Y H:i'),
            ];
            $urls[] = base_url('vendorpassrequest/view/' . (int) $r['id']);
        }

        return [
            'title' => $title[0], 'subtitle' => $title[1], 'icon' => 'local_shipping',
            'noun' => 'records',
            'columns' => ['App Date', 'Full Name', 'IC No / Passport No', 'Company Name', 'Location', 'Time In', 'Time Out'],
            'rows' => $out, 'urls' => $urls, 'truncated' => $trunc, 'link' => null,
        ];
    }

    // ----------------------------------------------------------------- vehicle

    private function vehicle(BaseConnection $db, string $card): ?array
    {
        $clientId = is_platform_superadmin() ? (int) $this->request->getGet('client_id') : (int) current_client_id();

        if (in_array($card, ['reg_staff', 'reg_vendor'], true)) {
            $staff = $card === 'reg_staff';
            $rows  = [];
            if ($db->tableExists('vehicles')) {
                $b = $db->table('vehicles')->where('DATE(vehicles.created_at) = CURDATE()', null, false);
                $b->where($staff ? 'vehicles.staff_id IS NOT NULL' : 'vehicles.staff_id IS NULL', null, false);
                if ($clientId > 0 && $db->fieldExists('client_id', 'vehicles')) {
                    $b->where(\App\Libraries\VehicleClientApprovals::scopeSql($db, $clientId, $db->prefixTable('vehicles')), null, false);
                }
                if ($db->fieldExists('soft_delete', 'vehicles')) {
                    $b->groupStart()->where('vehicles.soft_delete', 0)->orWhere('vehicles.soft_delete IS NULL', null, false)->groupEnd();
                }
                $sel = 'vehicles.id, vehicles.plate_no, vehicles.brand, vehicles.status, vehicles.created_at, vehicles.app_no, vehicles.owner_name, vehicles.company_name';
                $b->select($sel . ($db->tableExists('vehicle_types') ? ', (SELECT t.name FROM vehicle_types t WHERE t.id = vehicles.vehicle_type_id) AS vtype' : ''), false);
                $rows = $b->orderBy('vehicles.id', 'DESC')->limit(self::LIMIT + 1)->get()->getResultArray();
            }
            $trunc = count($rows) > self::LIMIT;
            $rows  = array_slice($rows, 0, self::LIMIT);
            $out = [];
            $urls = [];
            foreach ($rows as $r) {
                $out[] = [
                    (string) ($r['plate_no'] ?? '') ?: '-',
                    (string) ($r['vtype'] ?? '') ?: '-',
                    (string) ($r['brand'] ?? '') ?: '-',
                    $staff ? ((string) ($r['owner_name'] ?? '') ?: '-') : ((string) ($r['company_name'] ?? '') ?: ((string) ($r['owner_name'] ?? '') ?: '-')),
                    (string) ($r['app_no'] ?? '') ?: '-',
                    (string) ($r['status'] ?? '') ?: '-',
                    $this->date($r['created_at'] ?? null, 'd M Y H:i'),
                ];
                $urls[] = base_url('vehicles/view/' . (int) $r['id']);
            }

            return [
                'title' => $staff ? 'Staff Vehicle Registered' : 'Vendor Pass Vehicle Registered',
                'subtitle' => 'Vehicles applied for today', 'icon' => $staff ? 'badge' : 'local_shipping',
                'noun' => 'vehicles',
                'columns' => ['Vehicle Number', 'Type of Vehicle', 'Brand', $staff ? 'Staff' : 'Company', 'App No', 'Status', 'Applied'],
                'rows' => $out, 'urls' => $urls, 'truncated' => $trunc, 'link' => null,
            ];
        }

        if (in_array($card, ['gate_in', 'gate_out'], true)) {
            $union = (new GateMonitoring())->unionSql($clientId, true);
            $typeSel = "''";
            if ($db->tableExists('vehicles') && $db->tableExists('vehicle_types') && $db->fieldExists('vehicle_type_id', 'vehicles')) {
                $typeSel = "(SELECT t.name FROM vehicles v JOIN vehicle_types t ON t.id = v.vehicle_type_id
                              WHERE UPPER(REPLACE(v.plate_no, ' ', '')) = UPPER(REPLACE(tx.plate_no, ' ', '')) ORDER BY v.id DESC LIMIT 1)";
            }
            $when = $card === 'gate_in'
                ? 'DATE(tx.time_in) = CURDATE()'
                : 'tx.time_out IS NOT NULL AND DATE(tx.time_out) = CURDATE()';
            $rows = $db->query(
                "SELECT tx.plate_no, tx.name, tx.pass_type, tx.time_in, tx.time_out, tx.location, {$typeSel} AS vehicle_type
                 FROM ({$union}) tx WHERE tx.plate_no <> '' AND tx.status <> 'Rejected' AND {$when}
                 ORDER BY " . ($card === 'gate_in' ? 'tx.time_in' : 'tx.time_out') . ' DESC LIMIT ' . (self::LIMIT + 1)
            )->getResultArray();
            $trunc = count($rows) > self::LIMIT;
            $rows  = array_slice($rows, 0, self::LIMIT);
            $out = [];
            foreach ($rows as $r) {
                $out[] = [
                    (string) $r['plate_no'] ?: '-',
                    (string) ($r['vehicle_type'] ?? '') ?: '-',
                    $r['pass_type'] === 'VENDORPASS' ? 'VENDOR PASS' : ((string) $r['pass_type'] ?: '-'),
                    (string) ($r['name'] ?? '') ?: '-',
                    (string) ($r['location'] ?? '') ?: '-',
                    $this->date($r['time_in'] ?? null, 'd M Y H:i'),
                    $this->date($r['time_out'] ?? null, 'd M Y H:i'),
                ];
            }

            return [
                'title' => $card === 'gate_in' ? 'Gate In' : 'Gate Out',
                'subtitle' => $card === 'gate_in' ? 'Vehicles that entered today' : 'Vehicles that left today',
                'icon' => $card === 'gate_in' ? 'login' : 'logout',
                'noun' => 'vehicles',
                'columns' => ['Vehicle Number', 'Type of Vehicle', 'Driver Type', 'Name', 'Location', 'Time In', 'Time Out'],
                'rows' => $out, 'urls' => [], 'truncated' => $trunc, 'link' => null,
            ];
        }

        return null;
    }

    private function date($v, string $fmt): string
    {
        $v = trim((string) $v);

        return ($v === '' || str_starts_with($v, '0000') || strtotime($v) === false) ? '-' : date($fmt, strtotime($v));
    }
}
