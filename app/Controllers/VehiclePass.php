<?php

namespace App\Controllers;

use App\Libraries\VehiclePayment;
use App\Libraries\VehicleRules;
use App\Models\VendorLocationModel;

/**
 * Vehicle pass (annual): Request -> Pending -> Approved -> Issued (sticker) -> Terminated.
 * Stage pages are gated by the existing Config > Role "Vehicle" access keys.
 */
class VehiclePass extends BaseController
{
    private const PER_PAGE = 15;

    /** stage => title. Which Role > Vehicle key opens each page is in VehicleRules::KEYS. */
    private const STAGES = [
        'all'      => 'My Vehicle Pass',
        'staff'    => 'Staff Vehicle Pass',
        'request'  => 'Request List',
        'reviewed' => 'Approved / Reject List',
        'closed'   => 'Closed List',
    ];

    private function init(): void
    {
        helper(['role', 'feature', 'access', 'form', 'vendor_company']);
    }

    private function group(?string $g): string
    {
        return isset(VehicleRules::GROUPS[$g ?? '']) ? (string) $g : 'public';
    }

    /** Does the user hold the Role > Vehicle key for this page of the group? (reviewed = approved or reject key) */
    private function allowed(string $group, string $page): bool
    {
        $keys = VehicleRules::KEYS[$group];
        if ($page === 'reviewed') {
            return has_access('vehicle', $keys['approved']) || has_access('vehicle', $keys['reject']);
        }

        return isset($keys[$page]) && has_access('vehicle', $keys[$page]);
    }

    private function listUrl(string $group, string $page = 'all'): string
    {
        return 'vehicles/' . $group . ['all' => '', 'staff' => '/staff', 'request' => '/request-list', 'reviewed' => '/reviewed-list', 'closed' => '/closed-list'][$page];
    }

    private function scope($builder, string $alias = 'v')
    {
        if (! is_platform_superadmin()) {
            $builder->where($alias . '.client_id', current_client_id());
        }
        // a vendor account only ever sees the passes of its own company
        if (is_vendor_admin()) {
            $co = current_vendor_company();
            $builder->where($alias . '.vendor_company_id', (int) ($co['id'] ?? 0));
        }

        return $builder;
    }

    private function find(int $id): ?array
    {
        $db = \Config\Database::connect();
        $b  = $db->table('vehicles v')->select('v.*, t.name AS type_name')->join('vehicle_types t', 't.id = v.vehicle_type_id', 'left')->where('v.id', $id);

        return $this->scope($b)->get()->getRowArray() ?: null;
    }

    private function userName(): string
    {
        return (string) (session()->get('full_name') ?: session()->get('username'));
    }

    private function log(int $id, ?string $from, string $to, ?string $note = null): void
    {
        \Config\Database::connect()->table('vehicle_status_logs')->insert([
            'vehicle_id' => $id, 'from_status' => $from, 'to_status' => $to, 'note' => $note,
            'done_by' => $this->userName(), 'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function back(string $kind, string $msg, string $to = 'vehicles/public')
    {
        return redirect()->to(base_url($to))->with($kind, $msg);
    }

    /** Active vehicle types this client may use (its own + shared), for one group. */
    private function types(string $group = 'public'): array
    {
        $b = \Config\Database::connect()->table('vehicle_types')->where('status', 'active')->orderBy('name');
        if (\Config\Database::connect()->fieldExists('vehicle_group', 'vehicle_types')) {
            $b->groupStart()->where('vehicle_group', $group)->orWhere('vehicle_group', 'both')->orWhere('vehicle_group IS NULL', null, false)->groupEnd();
        }
        if (! is_platform_superadmin()) {
            $b->groupStart()->where('client_id', current_client_id())->orWhere('client_id IS NULL', null, false)->groupEnd();
        }

        return $b->get()->getResultArray();
    }

    // ------------------------------------------------------------------ lists

    public function index()
    {
        return redirect()->to(base_url('vehicles/public'));
    }

    public function listing(string $group = 'public', string $stage = 'all')
    {
        $this->init();
        $group = $this->group($group);
        $stage = isset(self::STAGES[$stage]) ? $stage : 'all';
        if ($stage === 'staff' && $group !== 'public') {
            $stage = 'all';
        }
        $title = self::STAGES[$stage];
        if (! $this->allowed($group, $stage)) {
            return $this->back('error', 'You are not allowed to view this list.', 'dashboard');
        }

        $db     = \Config\Database::connect();
        $search = trim((string) $this->request->getGet('search'));
        $status = trim((string) $this->request->getGet('status'));
        $page   = max(1, (int) $this->request->getGet('page'));

        $b = $db->table('vehicles v')->select('v.*, t.name AS type_name')->join('vehicle_types t', 't.id = v.vehicle_type_id', 'left');
        $this->scope($b);
        $b->where('v.vehicle_group', $group);
        $filterable = null; // statuses the user may filter by on this page
        if ($stage === 'request') {
            $b->where('v.status', 'Pending');
        } elseif ($stage === 'closed') {
            $b->whereIn('v.status', ['Issued', 'Terminated']);
        } elseif ($stage === 'reviewed') {
            $filterable = ['Approved', 'Rejected'];
            $b->whereIn('v.status', $status !== '' && in_array($status, $filterable, true) ? [$status] : $filterable);
        } else {
            if ($stage === 'staff') {
                $b->where('v.pass_for', 'staff');
            }
            $filterable = VehicleRules::STATUSES;
            if ($status !== '' && in_array($status, $filterable, true)) {
                $b->where('v.status', $status);
            }
        }
        if ($search !== '') {
            $b->groupStart()->like('v.plate_no', VehicleRules::normalizePlate($search))->orLike('v.owner_name', $search)
                ->orLike('v.company_name', $search)->orLike('v.app_no', $search)->orLike('v.sticker_serial', $search)->orLike('v.receipt_no', $search)->groupEnd();
        }
        $total = (clone $b)->countAllResults(false);
        $rows  = $b->orderBy('v.id', 'DESC')->limit(self::PER_PAGE, ($page - 1) * self::PER_PAGE)->get()->getResultArray();

        return view('vehicles/list', [
            'pageTitle' => $title . ' - ' . VehicleRules::GROUPS[$group] . ' - SafeG', 'title' => VehicleRules::GROUPS[$group] . ' - ' . $title, 'group' => $group, 'stage' => $stage, 'rows' => $rows,
            'search' => $search, 'status' => $status, 'page' => $page, 'pages' => max(1, (int) ceil($total / self::PER_PAGE)), 'total' => $total,
            'filterable' => $filterable,
            'canCreate' => $this->allowed($group, 'create'),
            'canStaffRequest' => $group === 'public' && ! is_vendor_admin() && $this->allowed($group, 'staff_request'),
        ]);
    }

    // ------------------------------------------------------------------ form

    /** A vendor account can only work with its own company's passes; staff are limited by their Role keys. */
    private function canEdit(array $v): bool
    {
        return in_array($v['status'], ['Draft', 'Rejected', 'Pending'], true) && $this->allowed($this->group($v['vehicle_group'] ?? 'public'), ($v['pass_for'] ?? 'vendor') === 'staff' && ! is_vendor_admin() ? 'staff_request' : 'create');
    }

    public function create(string $group = 'public', string $kind = 'vendor')
    {
        $this->init();
        $group = $this->group($group);
        $kind  = ($kind === 'staff' && $group === 'public' && ! is_vendor_admin()) ? 'staff' : 'vendor';
        if (! $this->allowed($group, $kind === 'staff' ? 'staff_request' : 'create')) {
            return $this->back('error', 'You are not allowed to request a vehicle pass here.', 'dashboard');
        }

        return $this->form(['vehicle_group' => $group, 'pass_for' => $kind]);
    }

    public function edit($id)
    {
        $this->init();
        $v = $this->find((int) $id);
        if (! $v) {
            return $this->back('error', 'Vehicle pass not found.');
        }
        if (! $this->canEdit($v)) {
            return $this->back('error', 'This vehicle pass can no longer be edited.', 'vehicles/view/' . (int) $id);
        }

        return $this->form($v);
    }

    /** Renewal: a new application pre-filled from an issued pass that is close to (or past) its expiry. */
    public function renew($id)
    {
        $this->init();
        $v = $this->find((int) $id);
        if (! $v) {
            return $this->back('error', 'Vehicle pass not found.');
        }
        $err = $this->renewError($v);
        if ($err) {
            return $this->back('error', $err, 'vehicles/view/' . (int) $id);
        }
        $new = array_diff_key($v, array_flip(['id', 'app_no', 'app_date', 'status', 'reject_reason', 'payment_status', 'payment_ref', 'payment_type', 'receipt_no', 'sticker_serial', 'sticker_year', 'pass_expiry', 'issued_at', 'issued_by', 'collector_name', 'approved_by', 'approved_at', 'created_at', 'updated_at']));
        $new['renewed_from'] = (int) $v['id'];
        $new['type_of_app']  = 'Renewal';

        return $this->form($new);
    }

    private function renewError(array $v): ?string
    {
        $group = $this->group($v['vehicle_group'] ?? 'public');
        if (! $this->canEdit(array_merge($v, ['status' => 'Draft']))) {
            return 'You are not allowed to request a vehicle pass here.';
        }
        $v['renewal_pending'] = \Config\Database::connect()->table('vehicles')->where('renewed_from', (int) $v['id'])->whereIn('status', ['Draft', 'Pending', 'Approved'])->countAllResults() > 0;

        return VehicleRules::renewBlock($v);
    }

    private function form(array $v)
    {
        $files = ! empty($v['id']) ? \Config\Database::connect()->table('vehicle_files')->where('vehicle_id', $v['id'])->get()->getResultArray() : [];
        $group = $this->group($v['vehicle_group'] ?? 'public');
        $co    = current_vendor_company();

        return view('vehicles/form', [
            'pageTitle' => (! empty($v['id']) ? 'Edit' : (! empty($v['renewed_from']) ? 'Renew' : 'New')) . ' Vehicle Pass - SafeG', 'v' => $v, 'types' => $this->types($group),
            'locationOptions' => (new VendorLocationModel())->getOptionsForUser(true), 'files' => $files,
            'typeFields' => VehicleRules::TYPE_FIELDS, 'old' => session()->getFlashdata('old') ?: [],
            'group' => $group, 'passFor' => $v['pass_for'] ?? 'vendor', 'ownCompany' => $co,
            'staffRow' => ! empty($v['staff_id']) ? \Config\Database::connect()->table('staff')->where('id', (int) $v['staff_id'])->get()->getRowArray() : null,
        ]);
    }

    /** Staff picker for New Staff Request: finds staff by staff no / name / IC. */
    public function staffLookup()
    {
        $this->init();
        if (is_vendor_admin() || ! $this->allowed('public', 'staff_request')) {
            return $this->response->setJSON([]);
        }
        $q = trim((string) $this->request->getGet('q'));
        if (mb_strlen($q) < 2) {
            return $this->response->setJSON([]);
        }
        $db = \Config\Database::connect();
        $b  = $db->table('staff')->select('id, staff_no, full_name, ic_passport, contact_number')
            ->groupStart()->like('staff_no', $q)->orLike('full_name', $q)->orLike('ic_passport', $q)->groupEnd();
        if (! is_platform_superadmin() && $db->fieldExists('company_id', 'staff')) {
            $b->groupStart()->where('company_id', current_client_id())->orWhere('company_id IS NULL', null, false)->groupEnd();
        }

        return $this->response->setJSON($b->orderBy('full_name')->limit(10)->get()->getResultArray());
    }

    public function store()
    {
        return $this->save(null);
    }

    public function update($id)
    {
        return $this->save((int) $id);
    }

    private function save(?int $id)
    {
        $this->init();
        $db  = \Config\Database::connect();
        $cur = $id ? $this->find($id) : null;
        if ($id && (! $cur || ! $this->canEdit($cur))) {
            return $this->back('error', 'This vehicle pass can no longer be edited.');
        }

        $p      = $this->request->getPost();
        $group  = $this->group($cur['vehicle_group'] ?? ($p['vehicle_group'] ?? 'public'));
        $kind   = $cur['pass_for'] ?? (($p['pass_for'] ?? '') === 'staff' && $group === 'public' && ! is_vendor_admin() ? 'staff' : 'vendor');
        $orig   = null;
        $origId = $id ? (int) ($cur['renewed_from'] ?? 0) : (int) ($p['renewed_from'] ?? 0);
        if ($origId > 0) {
            $orig = $this->find($origId);
            if (! $orig || ($e = $this->renewError($orig)) && ! $id) {
                return $this->back('error', ($orig ? $e : 'The pass to renew was not found.'), 'vehicles/view/' . $origId);
            }
            $group = $this->group($orig['vehicle_group']);
            $kind  = $orig['pass_for'];
        }
        if (! $id && ! $this->allowed($group, $kind === 'staff' ? 'staff_request' : 'create')) {
            return $this->back('error', 'You are not allowed to request a vehicle pass here.', 'dashboard');
        }

        $submit = ($p['action'] ?? 'submit') === 'submit';
        $errors = [];
        $get    = static fn(string $k): string => trim((string) ($p[$k] ?? ''));

        $type = null;
        foreach ($this->types($group) as $t) {
            if ((int) $t['id'] === (int) ($p['vehicle_type_id'] ?? 0)) {
                $type = $t;
            }
        }
        $plate = VehicleRules::normalizePlate($get('plate_no'));
        $locs  = array_values(array_filter(array_map('trim', (array) ($p['location_access'] ?? []))));
        $valid = array_keys((new VendorLocationModel())->getOptionsForUser(true));
        $locs  = array_values(array_intersect($locs, $valid));

        // who owns the vehicle
        $co    = current_vendor_company();
        $owner = ['owner_type' => $get('owner_type'), 'owner_name' => $get('owner_name'), 'owner_ic' => $get('owner_ic'), 'company_name' => $get('company_name'), 'contact_no' => $get('contact_no'), 'staff_id' => null];
        if ($kind === 'staff') {
            $st = $db->table('staff')->where('id', (int) ($p['staff_id'] ?? ($cur['staff_id'] ?? 0)))->get()->getRowArray();
            if (! $st) {
                $errors[] = 'Find and choose the staff member first.';
            } else {
                $owner = ['owner_type' => 'Staff', 'owner_name' => (string) $st['full_name'], 'owner_ic' => (string) ($st['ic_passport'] ?? ''),
                          'company_name' => '', 'contact_no' => $get('contact_no') ?: (string) ($st['contact_number'] ?? ''), 'staff_id' => (int) $st['id']];
            }
        } elseif ($group === 'cnc' || $co) {
            $owner['owner_type'] = 'Company'; // Cargo | Non-Cargo and vendor accounts are company vehicles
            if ($co) {
                $owner['company_name'] = (string) $co['name'];
            }
        }

        if ($plate === '') {
            $errors[] = 'Plate number is required.';
        }
        if (! $type) {
            $errors[] = 'Choose the vehicle type.';
        }
        if ($submit) {
            foreach (['owner_name' => 'Owner name', 'contact_no' => 'Contact number'] as $k => $label) {
                if ($owner[$k] === '') {
                    $errors[] = "$label is required.";
                }
            }
            foreach (['road_tax_expiry' => 'Road tax expiry', 'insurance_expiry' => 'Insurance expiry'] as $k => $label) {
                if ($get($k) === '') {
                    $errors[] = "$label is required.";
                }
            }
            if (! in_array($owner['owner_type'], ['Individual', 'Company', 'Staff'], true)) {
                $errors[] = 'Choose the owner type.';
            }
            if ($owner['owner_type'] === 'Company' && $owner['company_name'] === '') {
                $errors[] = 'Company name is required for a company vehicle.';
            }
            if ($locs === []) {
                $errors[] = 'Choose at least one Location Access.';
            }
            if ($type) {
                foreach (VehicleRules::TYPE_FIELDS as $col => [$field, $label]) {
                    if ((int) $type[$col] === 1 && $get($field) === '') {
                        $errors[] = "$label is required for {$type['name']}.";
                    }
                }
            }
        }
        $ignore = array_filter([$id, $origId]);
        if ($plate !== '' && $type && (int) $type['duplicate_plate_ok'] !== 1 && ($h = VehicleRules::plateHolder($db, $plate, $ignore))) {
            $errors[] = VehicleRules::plateMessage($plate, $h);
        }
        foreach (['road_tax_expiry', 'insurance_expiry'] as $d) {
            if ($get($d) !== '' && ! $this->validDate($get($d))) {
                $errors[] = 'Use a valid date (dd/mm/yyyy or yyyy-mm-dd) for ' . str_replace('_', ' ', $d) . '.';
            }
        }

        // client that owns the vehicle: the user's client, or for the platform admin the owner of the chosen location
        $clientId = is_platform_superadmin() ? $this->clientOfLocations($locs) : current_client_id();

        if ($errors) {
            return redirect()->back()->withInput()->with('error', implode("\n", $errors))->with('old', $p);
        }

        $now  = date('Y-m-d H:i:s');
        $data = [
            'owner_type' => $owner['owner_type'] ?: 'Individual', 'owner_name' => $owner['owner_name'], 'owner_ic' => $owner['owner_ic'] ?: null,
            'company_name' => $owner['company_name'] ?: null, 'contact_no' => $owner['contact_no'] ?: null, 'staff_id' => $owner['staff_id'],
            'vehicle_type_id' => $type ? (int) $type['id'] : null, 'plate_no' => $plate,
            'brand' => $get('brand') ?: null, 'year_made' => $get('year_made') ?: null, 'colour' => $get('colour') ?: null,
            'road_tax_expiry' => $this->toDate($get('road_tax_expiry')), 'insurance_expiry' => $this->toDate($get('insurance_expiry')),
            'location_access' => implode(',', $locs), 'remark' => $get('remark') ?: null,
            'payment_required' => $type ? (int) $type['payment_required'] : 0, 'updated_at' => $now,
        ];
        foreach (VehicleRules::TYPE_FIELDS as [$field, $label]) {
            $data[$field] = $get($field) ?: null;
        }
        $data['payment_status'] = $data['payment_required'] ? ($cur['payment_status'] ?? 'Pending') : 'None';

        $db->transStart();
        if ($id) {
            $from = $cur['status'];
            if ($submit && $from !== 'Pending') {
                $data['status'] = 'Pending';
                $data['reject_reason'] = null;
            }
            $db->table('vehicles')->where('id', $id)->update($data);
            if ($submit && $from !== 'Pending') {
                $this->log($id, $from, 'Pending', 'Submitted');
            }
        } else {
            $data += [
                'client_id' => $clientId ?: null, 'app_no' => $this->nextAppNo($db), 'app_date' => date('Y-m-d'), 'type_of_app' => $origId ? 'Renewal' : 'New',
                'vehicle_group' => $group, 'pass_for' => $kind, 'vendor_company_id' => $co ? (int) $co['id'] : ($orig['vendor_company_id'] ?? null),
                'created_by_user' => (int) session()->get('user_id') ?: null, 'renewed_from' => $origId ?: null,
                'status' => $submit ? 'Pending' : 'Draft', 'requested_by' => $this->userName(), 'created_at' => $now,
            ];
            $db->table('vehicles')->insert($data);
            $id = (int) $db->insertID();
            $this->log($id, null, $data['status'], ($origId ? 'Renewal of ' . $orig['app_no'] . '. ' : '') . ($submit ? 'Submitted' : 'Saved as draft'));
        }
        $this->saveFiles($db, $id);
        $db->transComplete();

        return $this->back('success', $submit ? 'Vehicle pass request submitted.' : 'Saved as draft.', 'vehicles/view/' . $id);
    }

    private function saveFiles($db, int $id): void
    {
        $dir = FCPATH . 'uploads/vehicle_files';
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        foreach ((array) ($this->request->getFileMultiple('documents') ?: []) as $f) {
            if (! $f || ! $f->isValid() || $f->hasMoved() || $f->getSize() > 5 * 1024 * 1024
                || ! in_array(strtolower($f->getExtension()), ['jpg', 'jpeg', 'png', 'pdf'], true)) {
                continue;
            }
            $name = $f->getRandomName();
            $f->move($dir, $name);
            $db->table('vehicle_files')->insert([
                'vehicle_id' => $id, 'label' => 'Document', 'file_path' => 'uploads/vehicle_files/' . $name,
                'original_name' => mb_substr($f->getClientName(), 0, 250), 'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        foreach ((array) $this->request->getPost('remove_file') as $fid) {
            $row = $db->table('vehicle_files')->where('id', (int) $fid)->where('vehicle_id', $id)->get()->getRowArray();
            if ($row) {
                @unlink(FCPATH . $row['file_path']);
                $db->table('vehicle_files')->where('id', (int) $fid)->delete();
            }
        }
    }

    private function nextAppNo($db): string
    {
        $tag = 'VH-' . date('Ymd');
        $n   = $db->table('vehicles')->like('app_no', $tag, 'after')->countAllResults() + 1;
        do {
            $no = $tag . '-' . str_pad((string) $n++, 3, '0', STR_PAD_LEFT);
        } while ($db->table('vehicles')->where('app_no', $no)->countAllResults() > 0);

        return $no;
    }

    private function validDate(string $s): bool
    {
        return $this->toDate($s) !== null;
    }

    private function toDate(string $s): ?string
    {
        $s = trim($s);
        if ($s === '') {
            return null;
        }
        foreach (['d/m/Y', 'Y-m-d', 'd-m-Y'] as $f) {
            $d = \DateTime::createFromFormat($f, $s);
            if ($d && $d->format($f) === $s) {
                return $d->format('Y-m-d');
            }
        }

        return null;
    }

    private function clientOfLocations(array $codes): int
    {
        if ($codes === []) {
            return 0;
        }
        foreach (\Config\Database::connect()->table('locations')->select('location_access, client_id')->where('client_id IS NOT NULL', null, false)->get()->getResultArray() as $r) {
            if (in_array(VendorLocationModel::slugifyCode((string) $r['location_access']), $codes, true)) {
                return (int) $r['client_id'];
            }
        }

        return 0;
    }

    // ------------------------------------------------------------------ detail

    private function vGroup(array $v): string
    {
        return $this->group($v['vehicle_group'] ?? 'public');
    }

    /** Role > Vehicle key for one action on a pass, taken from the pass' own group. */
    private function keyFor(array $v, string $page): string
    {
        return VehicleRules::KEYS[$this->vGroup($v)][$page] ?? '';
    }

    private function may(array $v, string $page): bool
    {
        $k = $this->keyFor($v, $page);

        return $k !== '' && has_access('vehicle', $k);
    }

    public function view($id)
    {
        $this->init();
        $v = $this->find((int) $id);
        if (! $v) {
            return $this->back('error', 'Vehicle pass not found.');
        }
        $db = \Config\Database::connect();
        $g  = $this->vGroup($v);
        // staff passes are created from the staff pages, so the "create" key is the staff request one
        $own = ($v['pass_for'] ?? 'vendor') === 'staff'
            ? (has_access('vehicle', VehicleRules::KEYS['public']['staff_request']) || has_access('vehicle', VehicleRules::KEYS['public']['staff']))
            : $this->may($v, 'create');

        $transferOpen = $db->tableExists('vehicle_transfers')
            && $db->table('vehicle_transfers')->where('vehicle_id', $v['id'])->where('status', 'Pending')->countAllResults() > 0;

        return view('vehicles/view', [
            'pageTitle' => 'Vehicle Pass ' . $v['app_no'] . ' - SafeG', 'v' => $v, 'group' => $g,
            'files' => $db->table('vehicle_files')->where('vehicle_id', $v['id'])->get()->getResultArray(),
            'logs' => $db->table('vehicle_status_logs')->where('vehicle_id', $v['id'])->orderBy('id', 'DESC')->get()->getResultArray(),
            'labels' => (new VendorLocationModel())->getAllLabelMap(), 'typeFields' => VehicleRules::TYPE_FIELDS,
            'listUrl' => $this->listUrl($g, ($v['pass_for'] ?? '') === 'staff' ? 'staff' : 'all'),
            'can' => [
                'edit'      => $own && in_array($v['status'], ['Draft', 'Rejected', 'Pending'], true),
                'approve'   => $this->may($v, 'request'),
                'issue'     => $this->may($v, 'approved'),
                'terminate' => $this->may($v, 'closed'),
                'delete'    => $this->canDelete($v),
                'submit'    => $own && in_array($v['status'], ['Draft', 'Rejected'], true),
                'renew'     => $own && $this->renewError($v) === null,
                'transfer'  => $v['status'] === 'Issued' && ! $transferOpen && has_access('vehicle', 'transfer_ownership'),
            ],
            'renewMsg'  => $own ? $this->renewError($v) : null,
            'payOnline' => VehiclePayment::enabled(),
        ]);
    }

    private function canDelete(array $v): bool
    {
        return ($v['status'] === 'Rejected' && $this->may($v, 'delete_rejected'))
            || ($v['status'] === 'Draft' && ($this->may($v, 'create') || ($v['pass_for'] ?? '') === 'staff'))
            || ($v['status'] === 'Terminated' && $this->may($v, 'delete_closed'));
    }

    // ------------------------------------------------------------------ actions

    public function action($id, string $action)
    {
        $this->init();
        $db = \Config\Database::connect();
        $v  = $this->find((int) $id);
        if (! $v) {
            return $this->back('error', 'Vehicle pass not found.');
        }
        $back = 'vehicles/view/' . (int) $id;
        $g    = $this->vGroup($v);

        if ($action === 'delete') {
            return $this->delete($v);
        }
        $page = ['submit' => 'create', 'approve' => 'request', 'reject' => 'request', 'issue' => 'approved', 'terminate' => 'closed'][$action] ?? null;
        $ok   = $page && $this->may($v, $page);
        if ($action === 'submit' && ! $ok && ($v['pass_for'] ?? '') === 'staff') {
            $ok = has_access('vehicle', VehicleRules::KEYS['public']['staff_request']) || has_access('vehicle', VehicleRules::KEYS['public']['staff']);
        }
        if (! $ok) {
            return $this->back('error', 'You are not allowed to do that.', $back);
        }
        $to = VehicleRules::next((string) $v['status'], $action);
        if (! $to) {
            return $this->back('error', "This pass is {$v['status']} - it cannot be {$action}d from here. Refresh the page.", $back);
        }

        $upd  = ['status' => $to, 'updated_at' => date('Y-m-d H:i:s')];
        $note = trim((string) $this->request->getPost('note'));

        if ($action === 'submit') {
            if ($h = (VehicleRules::plateHolder($db, (string) $v['plate_no'], array_filter([(int) $v['id'], (int) ($v['renewed_from'] ?? 0)])))) {
                $t = $db->table('vehicle_types')->where('id', $v['vehicle_type_id'])->get()->getRowArray();
                if (! $t || (int) $t['duplicate_plate_ok'] !== 1) {
                    return $this->back('error', VehicleRules::plateMessage((string) $v['plate_no'], $h), $back);
                }
            }
            $upd['reject_reason'] = null;
        } elseif ($action === 'approve') {
            if (trim((string) $v['location_access']) === '') {
                return $this->back('error', 'The pass has no Location Access.', $back);
            }
            $upd += ['approved_by' => $this->userName(), 'approved_at' => date('Y-m-d H:i:s')];
        } elseif ($action === 'reject') {
            if ($note === '') {
                return $this->back('error', 'Please give a reason for rejecting.', $back);
            }
            $upd['reject_reason'] = $note;
        } elseif ($action === 'issue') {
            if ($err = $this->issueFields($db, $v, $upd)) {
                return $this->back('error', $err, $back);
            }
        }

        $db->transStart();
        $db->table('vehicles')->where('id', (int) $id)->update($upd);
        $this->log((int) $id, (string) $v['status'], $to, $note !== '' ? $note : ($action === 'issue' ? 'Sticker ' . $upd['sticker_serial'] : null));
        // a renewal that gets its sticker replaces the old pass
        if ($action === 'issue' && ! empty($v['renewed_from'])) {
            $old = $db->table('vehicles')->where('id', (int) $v['renewed_from'])->get()->getRowArray();
            if ($old && $old['status'] === 'Issued') {
                $db->table('vehicles')->where('id', $old['id'])->update(['status' => 'Terminated', 'updated_at' => date('Y-m-d H:i:s')]);
                $this->log((int) $old['id'], 'Issued', 'Terminated', 'Replaced by renewal ' . $v['app_no']);
            }
        }
        $db->transComplete();

        $msg = ['submit' => 'Submitted for approval.', 'approve' => 'Vehicle pass approved. It is now waiting for sticker issuance.', 'reject' => 'Vehicle pass rejected.',
            'issue' => 'Sticker issued.', 'terminate' => 'Vehicle pass terminated.'][$action];
        $dest = $back;
        if ($action === 'approve' || $action === 'reject') {
            $dest = $this->listUrl($g, 'request');
        } elseif ($action === 'issue') {
            $dest = $this->listUrl($g, 'closed');
        }

        return $this->back('success', $msg, $dest);
    }

    /** Validates the issuance form and fills $upd. Returns an error text or null. */
    private function issueFields($db, array $v, array &$upd): ?string
    {
        $g      = fn(string $k) => trim((string) $this->request->getPost($k));
        $serial = $g('sticker_serial');
        $expiry = $this->toDate($g('pass_expiry'));
        if ($serial === '') {
            return 'Enter the sticker serial number.';
        }
        if (! $expiry || $expiry < date('Y-m-d')) {
            return 'Enter a pass expiry date that is today or later.';
        }
        if ($db->table('vehicles')->where('sticker_serial', $serial)->where('id !=', (int) $v['id'])->countAllResults() > 0) {
            return "Sticker serial number '{$serial}' is already used.";
        }
        if ((int) $v['payment_required'] === 1 && $v['payment_status'] !== 'Paid') {
            $receipt = $g('receipt_no');
            if ($receipt === '') {
                return 'This vehicle type needs payment. Pay online, or enter the receipt number.';
            }
            $upd += ['receipt_no' => $receipt, 'payment_type' => $g('payment_type') ?: 'Manual', 'payment_status' => 'Paid'];
        }
        $upd += [
            'sticker_serial' => $serial, 'sticker_year' => $g('sticker_year') ?: substr($expiry, 0, 4), 'pass_expiry' => $expiry,
            'collector_name' => $g('collector_name') ?: null, 'issued_at' => date('Y-m-d H:i:s'), 'issued_by' => $this->userName(),
        ];

        return null;
    }

    private function delete(array $v)
    {
        if (! $this->canDelete($v)) {
            return $this->back('error', 'You cannot delete this vehicle pass.', 'vehicles/view/' . (int) $v['id']);
        }
        $db = \Config\Database::connect();
        foreach ($db->table('vehicle_files')->where('vehicle_id', $v['id'])->get()->getResultArray() as $f) {
            @unlink(FCPATH . $f['file_path']);
        }
        $db->table('vehicle_files')->where('vehicle_id', $v['id'])->delete();
        $db->table('vehicle_status_logs')->where('vehicle_id', $v['id'])->delete();
        $db->table('vehicles')->where('id', $v['id'])->delete();

        return $this->back('success', 'Vehicle pass deleted.', $this->listUrl($this->vGroup($v), ($v['pass_for'] ?? '') === 'staff' ? 'staff' : 'all'));
    }

    // ------------------------------------------------------------------ payment (external service)

    public function pay($id)
    {
        $this->init();
        $v = $this->find((int) $id);
        if (! $v || $v['status'] !== 'Approved' || (int) $v['payment_required'] !== 1) {
            return $this->back('error', 'Payment is not needed for this pass.');
        }
        $res = VehiclePayment::start($v, base_url('vehicles/payment-return/' . (int) $id));
        if (empty($res['ok']) || empty($res['redirect_url'])) {
            return $this->back('error', $res['message'] ?? 'Could not start the payment.', 'vehicles/view/' . (int) $id);
        }
        \Config\Database::connect()->table('vehicles')->where('id', (int) $id)->update(['payment_status' => 'Pending', 'payment_ref' => $res['ref'] ?? $v['app_no']]);

        return redirect()->to($res['redirect_url']);
    }

    public function paymentReturn($id)
    {
        $this->init();
        $v = $this->find((int) $id);
        if (! $v) {
            return $this->back('error', 'Vehicle pass not found.');
        }
        $res = VehiclePayment::status((string) ($v['payment_ref'] ?: $v['app_no']));
        $db  = \Config\Database::connect();
        if (! empty($res['ok']) && ! empty($res['paid'])) {
            $db->table('vehicles')->where('id', (int) $id)->update([
                'payment_status' => 'Paid', 'receipt_no' => $res['receipt_no'] ?? null, 'payment_type' => $res['payment_type'] ?? 'Online', 'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $this->log((int) $id, $v['status'], $v['status'], 'Payment received ' . ($res['receipt_no'] ?? ''));

            return $this->back('success', 'Payment received.', 'vehicles/view/' . (int) $id);
        }
        $db->table('vehicles')->where('id', (int) $id)->update(['payment_status' => 'Failed']);

        return $this->back('error', 'Payment was not completed. ' . ($res['message'] ?? ''), 'vehicles/view/' . (int) $id);
    }
}
