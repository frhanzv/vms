<?php

namespace App\Controllers;

use App\Libraries\VehicleRules;
use App\Models\VendorLocationModel;

/**
 * Daily Pass: a one-day (or short visit) vehicle pass. Draft -> Pending -> Approved / Rejected.
 * Built but switched off - routes and sidebar only appear when VEHICLE_DAILY_PASS=true in .env.
 * Pages are gated by the Role > Vehicle keys my_vehicle_daily_pass, daily_pass_request_list,
 * daily_pass_reject_list and daily_pass_closed_list.
 */
class VehicleDailyPass extends BaseController
{
    private const PER_PAGE = 15;

    /** stage => [title, role key] */
    private const STAGES = [
        'mine'    => ['My Daily Pass', 'my_vehicle_daily_pass'],
        'request' => ['Daily Pass Request List', 'daily_pass_request_list'],
        'reject'  => ['Daily Pass Reject List', 'daily_pass_reject_list'],
        'closed'  => ['Daily Pass List', 'daily_pass_closed_list'],
    ];

    private function init(): void
    {
        helper(['role', 'feature', 'access', 'form', 'vendor_company']);
    }

    private function userName(): string
    {
        return (string) (session()->get('full_name') ?: session()->get('username'));
    }

    private function back(string $kind, string $msg, string $to = 'vehicle-daily')
    {
        return redirect()->to(base_url($to))->with($kind, $msg);
    }

    private function may(string $stage): bool
    {
        return VehicleRules::dailyEnabled() && has_access('vehicle', self::STAGES[$stage][1]);
    }

    private function scope($b)
    {
        if (! is_platform_superadmin()) {
            $b->where('d.client_id', current_client_id());
        }
        if (is_vendor_admin()) {
            $co = current_vendor_company();
            $b->where('d.vendor_company_id', (int) ($co['id'] ?? 0));
        }

        return $b;
    }

    private function find(int $id): ?array
    {
        $b = \Config\Database::connect()->table('vehicle_daily_passes d')->select('d.*, t.name AS type_name')->join('vehicle_types t', 't.id = d.vehicle_type_id', 'left')->where('d.id', $id);

        return $this->scope($b)->get()->getRowArray() ?: null;
    }

    public function index()
    {
        $this->init();
        $slug = ['mine' => 'mine', 'request' => 'request-list', 'reject' => 'reject-list', 'closed' => 'list'];
        foreach ($slug as $stage => $path) {
            if ($this->may($stage)) {
                return redirect()->to(base_url('vehicle-daily/' . $path));
            }
        }

        return $this->back('error', 'You are not allowed to view this list.', 'dashboard');
    }

    public function listing(string $stage = 'mine')
    {
        $this->init();
        $stage = isset(self::STAGES[$stage]) ? $stage : 'mine';
        if (! $this->may($stage)) {
            return $this->back('error', 'You are not allowed to view this list.', 'dashboard');
        }
        $db     = \Config\Database::connect();
        $search = trim((string) $this->request->getGet('search'));
        $page   = max(1, (int) $this->request->getGet('page'));

        $b = $db->table('vehicle_daily_passes d')->select('d.*, t.name AS type_name')->join('vehicle_types t', 't.id = d.vehicle_type_id', 'left');
        $this->scope($b);
        if ($stage === 'request') {
            $b->where('d.status', 'Pending');
        } elseif ($stage === 'reject') {
            $b->where('d.status', 'Rejected');
        } elseif ($stage === 'closed') {
            $b->where('d.status', 'Approved');
        }
        if ($search !== '') {
            $b->groupStart()->like('d.plate_no', VehicleRules::normalizePlate($search))->orLike('d.driver_name', $search)->orLike('d.company_name', $search)->orLike('d.app_no', $search)->groupEnd();
        }
        $total = (clone $b)->countAllResults(false);
        $rows  = $b->orderBy('d.id', 'DESC')->limit(self::PER_PAGE, ($page - 1) * self::PER_PAGE)->get()->getResultArray();

        $tabs = [];
        foreach (self::STAGES as $k => [$label]) {
            if ($this->may($k)) {
                $tabs[$k] = $label;
            }
        }

        return view('vehicles/daily_list', [
            'pageTitle' => self::STAGES[$stage][0] . ' - SafeG', 'title' => self::STAGES[$stage][0], 'stage' => $stage, 'tabs' => $tabs, 'rows' => $rows,
            'search' => $search, 'page' => $page, 'pages' => max(1, (int) ceil($total / self::PER_PAGE)), 'total' => $total, 'canCreate' => $this->may('mine'),
        ]);
    }

    private function types(): array
    {
        $b = \Config\Database::connect()->table('vehicle_types')->where('status', 'active')->orderBy('name');
        if (! is_platform_superadmin()) {
            $b->groupStart()->where('client_id', current_client_id())->orWhere('client_id IS NULL', null, false)->groupEnd();
        }

        return $b->get()->getResultArray();
    }

    private function form(array $d)
    {
        return view('vehicles/daily_form', [
            'pageTitle' => (empty($d['id']) ? 'New' : 'Edit') . ' Daily Pass - SafeG', 'd' => $d, 'types' => $this->types(),
            'locationOptions' => (new VendorLocationModel())->getOptionsForUser(true), 'old' => session()->getFlashdata('old') ?: [], 'ownCompany' => current_vendor_company(),
        ]);
    }

    public function create()
    {
        $this->init();
        if (! $this->may('mine')) {
            return $this->back('error', 'You are not allowed to request a daily pass.', 'dashboard');
        }

        return $this->form([]);
    }

    public function edit($id)
    {
        $this->init();
        $d = $this->find((int) $id);
        if (! $d || ! $this->may('mine') || ! in_array($d['status'], ['Draft', 'Rejected'], true)) {
            return $this->back('error', 'This daily pass can no longer be edited.');
        }

        return $this->form($d);
    }

    public function store()
    {
        return $this->save(null);
    }

    public function update($id)
    {
        return $this->save((int) $id);
    }

    private function toDate(string $s): ?string
    {
        foreach (['d/m/Y', 'Y-m-d'] as $f) {
            $dt = \DateTime::createFromFormat($f, $s);
            if ($dt && $dt->format($f) === $s) {
                return $dt->format('Y-m-d');
            }
        }

        return null;
    }

    private function save(?int $id)
    {
        $this->init();
        if (! $this->may('mine')) {
            return $this->back('error', 'You are not allowed to request a daily pass.', 'dashboard');
        }
        $db  = \Config\Database::connect();
        $cur = $id ? $this->find($id) : null;
        if ($id && (! $cur || ! in_array($cur['status'], ['Draft', 'Rejected'], true))) {
            return $this->back('error', 'This daily pass can no longer be edited.');
        }
        $p      = $this->request->getPost();
        $get    = static fn(string $k): string => trim((string) ($p[$k] ?? ''));
        $submit = ($p['action'] ?? 'submit') === 'submit';
        $co     = current_vendor_company();
        $plate  = VehicleRules::normalizePlate($get('plate_no'));
        $locs   = array_values(array_intersect(array_filter(array_map('trim', (array) ($p['location_access'] ?? []))), array_keys((new VendorLocationModel())->getOptionsForUser(true))));
        $company = $co ? (string) $co['name'] : $get('company_name');
        $appFor  = ($p['app_for'] ?? 'Individual') === 'Staff' ? 'Staff' : 'Individual';
        if ($co) {
            $appFor = 'Individual'; // a vendor account always applies for its own company
        }
        $road    = $this->toDate($get('road_tax_expiry'));
        $ins     = $this->toDate($get('insurance_expiry'));

        // KPK: one or more visit dates, each a single day or a date range
        $visits = [];
        $vf = (array) ($p['vd_from'] ?? []);
        $vt = (array) ($p['vd_to'] ?? []);
        $badDate = false;
        foreach ($vf as $i => $f) {
            $f = trim((string) $f);
            $t = trim((string) ($vt[$i] ?? ''));
            if ($f === '' && $t === '') {
                continue;
            }
            $fd = $this->toDate($f);
            $td = $t === '' ? $fd : $this->toDate($t);
            if (! $fd || ! $td || $td < $fd) {
                $badDate = true;
                continue;
            }
            $visits[] = ['from' => $fd, 'to' => $td];
        }
        usort($visits, static fn($x, $y) => strcmp($x['from'], $y['from']));
        $from = $visits ? $visits[0]['from'] : null;
        $to   = $visits ? max(array_column($visits, 'to')) : null;

        $errors = [];
        if ($plate === '') {
            $errors[] = 'Plate number is required.';
        }
        if ($submit) {
            foreach (['driver_name' => $appFor === 'Staff' ? 'Staff name' : 'Name', 'contact_no' => 'Contact number', 'purpose' => 'Purpose', 'brand' => 'Brand', 'colour' => 'Colour', 'year_made' => 'Year made'] as $k => $label) {
                if ($get($k) === '') {
                    $errors[] = "$label is required.";
                }
            }
            if ($appFor === 'Staff') {
                foreach (['staff_no' => 'Staff ID', 'department' => 'Department', 'hod' => 'HOD'] as $k => $label) {
                    if ($get($k) === '') {
                        $errors[] = "$label is required.";
                    }
                }
            } elseif ($company === '') {
                $errors[] = 'Company name is required.';
            }
            if (! $road) {
                $errors[] = 'Road tax expiry date is required (dd/mm/yyyy).';
            }
            if (! $ins) {
                $errors[] = 'Insurance expiry date is required (dd/mm/yyyy).';
            }
            if (! $visits || $badDate) {
                $errors[] = 'Add at least one valid visit date (dd/mm/yyyy); the end date cannot be before the start date.';
            } elseif ($visits[0]['from'] < date('Y-m-d')) {
                $errors[] = 'Visit dates cannot be in the past.';
            }
            if ($locs === []) {
                $errors[] = 'Choose at least one Location Access.';
            }
            if (empty($p['agree_terms'])) {
                $errors[] = 'Please agree to the terms and conditions.';
            }
        }
        if ($errors) {
            return redirect()->back()->withInput()->with('error', implode("\n", $errors))->with('old', $p);
        }

        $now  = date('Y-m-d H:i:s');
        $data = [
            'company_name' => $company ?: null, 'driver_name' => $get('driver_name') ?: null, 'driver_ic' => $get('driver_ic') ?: null, 'contact_no' => $get('contact_no') ?: null,
            'vehicle_type_id' => (int) ($p['vehicle_type_id'] ?? 0) ?: null, 'plate_no' => $plate, 'purpose' => $get('purpose') ?: null,
            'visit_from' => $from, 'visit_to' => $to, 'location_access' => implode(',', $locs), 'updated_at' => $now,
            'app_for' => $appFor, 'staff_no' => $appFor === 'Staff' ? ($get('staff_no') ?: null) : null, 'department' => $appFor === 'Staff' ? ($get('department') ?: null) : null,
            'hod' => $appFor === 'Staff' ? ($get('hod') ?: null) : null, 'year_made' => $get('year_made') ?: null, 'colour' => $get('colour') ?: null, 'brand' => $get('brand') ?: null,
            'road_tax_expiry' => $road, 'insurance_expiry' => $ins, 'visit_dates' => $visits ? json_encode($visits) : null, 'remark' => $get('remark') ?: null,
        ];
        if ($appFor === 'Staff') {
            $data['company_name'] = null;
        }
        if ($submit) {
            $data['status'] = 'Pending';
            $data['reject_reason'] = null;
        }
        if ($id) {
            $db->table('vehicle_daily_passes')->where('id', $id)->update($data);
        } else {
            $tag = 'DP-' . date('Ymd');
            $n   = $db->table('vehicle_daily_passes')->like('app_no', $tag, 'after')->countAllResults() + 1;
            do {
                $no = $tag . '-' . str_pad((string) $n++, 3, '0', STR_PAD_LEFT);
            } while ($db->table('vehicle_daily_passes')->where('app_no', $no)->countAllResults() > 0);
            $data += [
                'client_id' => is_platform_superadmin() ? null : current_client_id(), 'app_no' => $no, 'app_date' => date('Y-m-d'),
                'vendor_company_id' => $co ? (int) $co['id'] : null, 'status' => $submit ? 'Pending' : 'Draft',
                'requested_by' => $this->userName(), 'created_by_user' => (int) session()->get('user_id') ?: null, 'created_at' => $now,
            ];
            $db->table('vehicle_daily_passes')->insert($data);
            $id = (int) $db->insertID();
        }

        return $this->back('success', $submit ? 'Daily pass request submitted.' : 'Saved as draft.', 'vehicle-daily/view/' . $id);
    }

    public function view($id)
    {
        $this->init();
        $d = $this->find((int) $id);
        if (! $d || ! VehicleRules::dailyEnabled()) {
            return $this->back('error', 'Daily pass not found.');
        }
        $own = $this->may('mine');

        return view('vehicles/daily_view', [
            'pageTitle' => 'Daily Pass ' . $d['app_no'] . ' - SafeG', 'd' => $d, 'labels' => (new VendorLocationModel())->getAllLabelMap(),
            'can' => [
                'edit' => $own && in_array($d['status'], ['Draft', 'Rejected'], true),
                'submit' => $own && in_array($d['status'], ['Draft', 'Rejected'], true),
                'decide' => $d['status'] === 'Pending' && $this->may('request'),
                'delete' => $own && $d['status'] === 'Draft',
            ],
        ]);
    }

    public function action($id, string $action)
    {
        $this->init();
        $d    = $this->find((int) $id);
        $back = 'vehicle-daily/view/' . (int) $id;
        if (! $d || ! VehicleRules::dailyEnabled()) {
            return $this->back('error', 'Daily pass not found.');
        }
        $db   = \Config\Database::connect();
        $note = trim((string) $this->request->getPost('note'));
        $now  = date('Y-m-d H:i:s');

        if (in_array($action, ['submit', 'delete'], true)) {
            if (! $this->may('mine')) {
                return $this->back('error', 'You are not allowed to do that.', $back);
            }
            if ($action === 'delete') {
                if ($d['status'] !== 'Draft') {
                    return $this->back('error', 'Only a draft can be deleted.', $back);
                }
                $db->table('vehicle_daily_passes')->where('id', $d['id'])->delete();

                return $this->back('success', 'Daily pass deleted.');
            }
            if (! in_array($d['status'], ['Draft', 'Rejected'], true)) {
                return $this->back('error', 'This daily pass was already submitted.', $back);
            }
            foreach (['driver_name', 'contact_no', 'purpose', 'visit_from', 'visit_to', 'location_access', 'brand', 'colour', 'year_made', 'road_tax_expiry', 'insurance_expiry'] as $k) {
                if (trim((string) $d[$k]) === '') {
                    return $this->back('error', 'Complete the daily pass (Edit) before submitting.', $back);
                }
            }
            if (($d['app_for'] ?? 'Individual') === 'Staff' ? trim((string) $d['staff_no']) === '' : trim((string) $d['company_name']) === '') {
                return $this->back('error', 'Complete the daily pass (Edit) before submitting.', $back);
            }
            $db->table('vehicle_daily_passes')->where('id', $d['id'])->update(['status' => 'Pending', 'reject_reason' => null, 'updated_at' => $now]);

            return $this->back('success', 'Daily pass request submitted.', $back);
        }

        if (! in_array($action, ['approve', 'reject'], true) || ! $this->may('request')) {
            return $this->back('error', 'You are not allowed to do that.', $back);
        }
        if ($d['status'] !== 'Pending') {
            return $this->back('error', 'This daily pass is no longer waiting for approval.', $back);
        }
        if ($action === 'reject') {
            if ($note === '') {
                return $this->back('error', 'Please give a reason for rejecting.', $back);
            }
            $db->table('vehicle_daily_passes')->where('id', $d['id'])->update(['status' => 'Rejected', 'reject_reason' => $note, 'updated_at' => $now]);

            return $this->back('success', 'Daily pass rejected.', 'vehicle-daily/request-list');
        }
        $db->table('vehicle_daily_passes')->where('id', $d['id'])->update(['status' => 'Approved', 'approved_by' => $this->userName(), 'approved_at' => $now, 'updated_at' => $now]);

        return $this->back('success', 'Daily pass approved.', 'vehicle-daily/request-list');
    }
}
