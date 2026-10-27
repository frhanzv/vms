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
        $b  = $db->table('vehicles v')->select('v.*, t.name AS type_name, t.category AS type_category, t.req_pass AS type_req_pass')->join('vehicle_types t', 't.id = v.vehicle_type_id', 'left')->where('v.id', $id);

        return $this->notDeleted($this->scope($b))->get()->getRowArray() ?: null;
    }

    /** A soft-deleted pass (KPK "remove") disappears from every list and page. */
    private function notDeleted($builder, string $alias = 'v')
    {
        if (\Config\Database::connect()->fieldExists('soft_delete', 'vehicles')) {
            $builder->groupStart()->where($alias . '.soft_delete', 0)->orWhere($alias . '.soft_delete IS NULL', null, false)->groupEnd();
        }

        return $builder;
    }

    // ------------------------------------------------------------------ drivers (vendor passes linked to a vehicle)

    /** Vendor passes (drivers) the user may link: active card, not suspended / terminated, inside the user's client and (vendor account) company. */
    private function driverQuery()
    {
        $b = \Config\Database::connect()->table('vendors')->select('id, full_name, ic_no, passport_no, vendor_company_name, card_status, status')
            ->where('card_status', 'Active')->whereNotIn('status', ['suspended', 'Suspended', 'terminated', 'Terminated']);
        if (! is_platform_superadmin()) {
            $b->where('company_id', current_client_id());
        }
        apply_vendor_company_scope($b);

        return $b;
    }

    /** JSON for the driver picker: search by name, IC or passport. */
    public function driverLookup()
    {
        $this->init();
        if (! ($this->allowed('public', 'create') || $this->allowed('cnc', 'create') || $this->allowed('public', 'closed') || $this->allowed('cnc', 'closed'))) {
            return $this->response->setJSON([]);
        }
        $q = trim((string) $this->request->getGet('q'));
        if (mb_strlen($q) < 2) {
            return $this->response->setJSON([]);
        }
        $b = $this->driverQuery()->groupStart()->like('full_name', $q)->orLike('ic_no', $q)->orLike('passport_no', $q)->groupEnd();

        return $this->response->setJSON($b->orderBy('full_name')->limit(10)->get()->getResultArray());
    }

    /** Drivers linked to a pass, with the vendor pass details. */
    private function drivers(int $vehicleId): array
    {
        $db = \Config\Database::connect();
        if (! $vehicleId || ! $db->tableExists('vehicle_drivers')) {
            return [];
        }

        return $db->table('vehicle_drivers d')->select('d.id AS link_id, d.vendor_id, d.hod, x.full_name, x.ic_no, x.passport_no, x.card_status, x.status AS pass_status, x.vendor_company_name')
            ->join('vendors x', 'x.id = d.vendor_id', 'left')->where('d.vehicle_id', $vehicleId)->orderBy('d.id')->get()->getResultArray();
    }

    /** Validates the posted driver ids against the user's scope. Returns [ids, error|null]. */
    private function postedDrivers(): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', (array) $this->request->getPost('driver_id')))));
        if (! $ids) {
            return [[], null];
        }
        $ok = array_column($this->driverQuery()->whereIn('id', $ids)->get()->getResultArray(), 'id');
        if (count($ok) !== count($ids)) {
            return [[], 'A selected driver pass is not active (or not yours). Choose the driver again.'];
        }

        return [$ids, null];
    }

    private function saveDrivers($db, int $vehicleId, array $ids): void
    {
        if (! $db->tableExists('vehicle_drivers')) {
            return;
        }
        $db->table('vehicle_drivers')->where('vehicle_id', $vehicleId)->delete();
        foreach ($ids as $vid) {
            $db->table('vehicle_drivers')->insert(['vehicle_id' => $vehicleId, 'vendor_id' => (int) $vid, 'hod' => null, 'created_at' => date('Y-m-d H:i:s')]);
        }
    }

    /** KPK sticker history: the new sticker becomes active, the previous one inactive. */
    private function recordSticker($db, array $v, array $f, string $by): void
    {
        if (! $db->tableExists('vehicle_stickers')) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $db->table('vehicle_stickers')->where('vehicle_id', (int) $v['id'])->where('status', 'active')->update(['status' => 'inactive', 'updated_at' => $now]);
        $db->table('vehicle_stickers')->insert([
            'vehicle_id' => (int) $v['id'], 'sticker_year' => $f['sticker_year'] ?? $v['sticker_year'] ?? null, 'serial_no' => $f['sticker_serial'] ?? null,
            'receipt_no' => $f['receipt_no'] ?? $v['receipt_no'] ?? null, 'payment_type' => $f['payment_type'] ?? $v['payment_type'] ?? null,
            'issue_date' => $f['issue_date'] ?? null, 'pass_expiry' => $f['pass_expiry'] ?? $v['pass_expiry'] ?? null,
            'collector_ic' => $f['collector_ic'] ?? null, 'collector_name' => $f['collector_name'] ?? null,
            'road_tax_expiry' => $v['road_tax_expiry'] ?? null, 'insurance_expiry' => $v['insurance_expiry'] ?? null,
            'status' => 'active', 'issued_by' => $by, 'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    private function closeStickers($db, int $vehicleId): void
    {
        if ($db->tableExists('vehicle_stickers')) {
            $db->table('vehicle_stickers')->where('vehicle_id', $vehicleId)->where('status', 'active')->update(['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s')]);
        }
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
        $appFor = trim((string) $this->request->getGet('app_for'));      // Individual / Company / Staff
        $typeId = (int) $this->request->getGet('type_id');               // vehicle type
        $appTyp = trim((string) $this->request->getGet('type_of_app'));  // New / Renewal
        $sort   = trim((string) $this->request->getGet('sort'));
        $per    = (int) $this->request->getGet('per_page');
        $per    = in_array($per, [10, 20, 30, 40], true) ? $per : self::PER_PAGE;

        $b = $db->table('vehicles v')->select('v.*, t.name AS type_name')->join('vehicle_types t', 't.id = v.vehicle_type_id', 'left');
        $this->scope($b);
        $b->where('v.vehicle_group', $group);
        $this->notDeleted($b);
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
            $filterable = array_merge(VehicleRules::STATUSES, ['Expired']);
            if ($status === 'Expired') {
                $b->where('v.status', 'Issued')->where('v.pass_expiry <', date('Y-m-d'));
            } elseif ($status !== '' && in_array($status, $filterable, true)) {
                $b->where('v.status', $status);
            }
        }
        if (in_array($appFor, ['Individual', 'Company', 'Staff'], true)) {
            $b->where('v.owner_type', $appFor);
        }
        if ($typeId > 0) {
            $b->where('v.vehicle_type_id', $typeId);
        }
        if (in_array($appTyp, ['New', 'Renewal'], true)) {
            $b->where('v.type_of_app', $appTyp);
        }
        if ($search !== '') {
            $b->groupStart()->like('v.plate_no', VehicleRules::normalizePlate($search))->orLike('v.owner_name', $search)->orLike('v.owner_ic', $search)
                ->orLike('v.company_name', $search)->orLike('v.department', $search)->orLike('v.staff_no', $search)
                ->orLike('v.app_no', $search)->orLike('v.sticker_serial', $search)->orLike('v.receipt_no', $search);
            // KPK also finds a vehicle by its driver's name or IC / passport
            if ($db->tableExists('vehicle_drivers')) {
                $b->orWhere('v.id IN (SELECT d.vehicle_id FROM ' . $db->prefixTable('vehicle_drivers') . ' d JOIN ' . $db->prefixTable('vendors') . ' x ON x.id = d.vendor_id WHERE x.full_name LIKE ' . $db->escape('%' . $db->escapeLikeString($search) . '%') . ' OR x.ic_no LIKE ' . $db->escape('%' . $db->escapeLikeString($search) . '%') . ' OR x.passport_no LIKE ' . $db->escape('%' . $db->escapeLikeString($search) . '%') . ')', null, false);
            }
            $b->groupEnd();
        }
        $sorts = ['app_asc' => ['v.app_no', 'ASC'], 'app_desc' => ['v.app_no', 'DESC'], 'reg_asc' => ['v.plate_no', 'ASC'], 'reg_desc' => ['v.plate_no', 'DESC'],
            'date_asc' => ['v.id', 'ASC'], 'date_desc' => ['v.id', 'DESC'], 'name_asc' => ['v.owner_name', 'ASC'], 'name_desc' => ['v.owner_name', 'DESC'],
            'company_asc' => ['v.company_name', 'ASC'], 'company_desc' => ['v.company_name', 'DESC'], 'collect_asc' => ['v.collect_date', 'ASC'], 'collect_desc' => ['v.collect_date', 'DESC']];
        [$sortCol, $sortDir] = $sorts[$sort] ?? $sorts['date_desc'];
        $total = (clone $b)->countAllResults(false);
        $rows  = $b->orderBy($sortCol, $sortDir)->limit($per, ($page - 1) * $per)->get()->getResultArray();
        if ($stage === 'request') {   // row Edit / Delete icons: only where the pass screen would allow them too
            foreach ($rows as &$rw) {
                $rw['_can_approve'] = $this->may($rw, 'request');
                $rw['_can_edit']   = $this->canEdit($rw);
                $rw['_can_delete'] = $this->canDelete($rw);
            }
            unset($rw);
        } elseif ($stage === 'reviewed') {   // KPK Approved list: an Approved pass can still be rejected
            foreach ($rows as &$rw) {
                $rw['_can_reject'] = $rw['status'] === 'Approved' && $this->may($rw, 'approved');
            }
            unset($rw);
        }

        // summary cards: counts of the whole list (ignoring search and filters), only the statuses this page can hold
        $statCards = [];
        if (in_array($stage, ['all', 'staff', 'closed'], true)) {
            $today = $db->escape(date('Y-m-d'));
            $sb    = $db->table('vehicles v');
            $this->scope($sb);
            $sb->where('v.vehicle_group', $group);
            $this->notDeleted($sb);
            if ($stage === 'staff') {
                $sb->where('v.pass_for', 'staff');
            }
            if ($stage === 'closed') {
                $sb->whereIn('v.status', ['Issued', 'Terminated']);
                $sr = $sb->select("COUNT(*) AS total, SUM(v.status = 'Issued' AND (v.pass_expiry IS NULL OR v.pass_expiry >= {$today})) AS active, "
                    . "SUM(v.status = 'Issued' AND v.pass_expiry < {$today}) AS expired, SUM(v.status = 'Terminated') AS term_n", false)->get()->getRowArray() ?: [];
                $statCards = [
                    ['label' => 'Total Closed', 'value' => (int) ($sr['total'] ?? 0), 'sub' => 'All closed passes', 'color' => 'indigo', 'icon' => 'directions_car'],
                    ['label' => 'Active', 'value' => (int) ($sr['active'] ?? 0), 'sub' => 'Issued and valid', 'color' => 'green', 'icon' => 'check_circle'],
                    ['label' => 'Expired', 'value' => (int) ($sr['expired'] ?? 0), 'sub' => 'Pass expiry passed', 'color' => 'orange', 'icon' => 'event_busy'],
                    ['label' => 'Terminated', 'value' => (int) ($sr['term_n'] ?? 0), 'sub' => 'Pass ended', 'color' => 'red', 'icon' => 'cancel'],
                ];
            } else {
                $sr = $sb->select("COUNT(*) AS total, SUM(v.status = 'Pending') AS pending, SUM(v.status = 'Approved') AS approved, SUM(v.status = 'Issued') AS issued, SUM(v.status = 'Rejected') AS rejected", false)->get()->getRowArray() ?: [];
                $statCards = [
                    ['label' => 'Total Passes', 'value' => (int) ($sr['total'] ?? 0), 'sub' => 'All vehicle pass records', 'color' => 'indigo', 'icon' => 'directions_car'],
                    ['label' => 'Pending', 'value' => (int) ($sr['pending'] ?? 0), 'sub' => 'Awaiting approval', 'color' => 'orange', 'icon' => 'pending_actions'],
                    ['label' => 'Approved', 'value' => (int) ($sr['approved'] ?? 0), 'sub' => 'Awaiting issue', 'color' => 'blue', 'icon' => 'task_alt'],
                    ['label' => 'Issued', 'value' => (int) ($sr['issued'] ?? 0), 'sub' => 'Active passes', 'color' => 'green', 'icon' => 'check_circle'],
                    ['label' => 'Rejected', 'value' => (int) ($sr['rejected'] ?? 0), 'sub' => 'Declined requests', 'color' => 'red', 'icon' => 'cancel'],
                ];
            }
        }

        return view('vehicles/list', [
            'pageTitle' => $title . ' - ' . VehicleRules::GROUPS[$group] . ' - SafeG', 'title' => VehicleRules::GROUPS[$group] . ' - ' . $title, 'group' => $group, 'stage' => $stage, 'rows' => $rows,
            'search' => $search, 'status' => $status, 'page' => $page, 'pages' => max(1, (int) ceil($total / $per)), 'total' => $total,
            'appFor' => $appFor, 'typeId' => $typeId, 'appTyp' => $appTyp, 'sort' => $sort, 'per' => $per, 'types' => $this->types($group),
            'filterable' => $filterable, 'statCards' => $statCards, 'rejectReasons' => in_array($stage, ['request', 'reviewed'], true) ? $this->rejectReasons() : [],
            'canCreate' => $this->allowed($group, 'create'),
            'canColumns' => ! is_vendor_admin() && has_access('vehicle', 'show_hide_columns'),
            'canDirect' => $stage === 'closed' && ! is_vendor_admin() && $this->allowed($group, 'closed') && has_access('vehicle', 'new_issued_pass'),
            'canStaffRequest' => $group === 'public' && ! is_vendor_admin() && $this->allowed($group, 'staff_request'),
        ]);
    }

    /** Show/Hide Columns button: the same per-client settings as Config > Page Settings (client_list_columns). */
    public function saveColumns()
    {
        $this->init();
        helper('list_columns');
        $list = (string) $this->request->getPost('list');
        $stage = str_starts_with($list, 'vehicle_') ? substr($list, 8) : '';
        $back = (string) $this->request->getPost('back');
        $back = preg_match('#^vehicles/(public|cnc)(/[a-z-]+)?$#', $back) ? $back : 'vehicles/public';
        if (! isset(VehicleRules::LIST_PAGES[$stage]) || ! isset(list_column_registry()[$list])) {
            return $this->back('error', 'Unknown list.', $back);
        }
        if (is_vendor_admin() || ! has_access('vehicle', 'show_hide_columns')) {
            return $this->back('error', 'Columns are set by the client administrator.', $back);
        }
        $clientId = (int) current_client_id();
        if ($clientId <= 0) {
            return $this->back('error', 'Columns are set per client - use Config > Page Settings and choose a client.', $back);
        }
        $posted = $this->request->getPost('columns');
        $posted = is_array($posted) ? $posted : [];
        $db     = \Config\Database::connect();
        $now    = date('Y-m-d H:i:s');
        foreach (VehicleRules::listColumns($stage) as $key => $def) {
            if (! empty($def['locked'])) {
                continue;
            }
            $where = ['client_id' => $clientId, 'list_key' => $list, 'column_key' => $key];
            $data  = ['is_visible' => array_key_exists($key, $posted) ? 1 : 0, 'updated_at' => $now];
            if ($db->table('client_list_columns')->where($where)->countAllResults() > 0) {
                $db->table('client_list_columns')->where($where)->update($data);
            } else {
                $db->table('client_list_columns')->insert($where + $data + ['created_at' => $now]);
            }
        }

        return $this->back('success', 'Columns updated.', $back);
    }

    // ------------------------------------------------------------------ form

    /** The requester side: the owner of a Draft / Rejected / Pending pass, by the Role > Vehicle "my pass" keys. */
    private function ownerMay(array $v): bool
    {
        return in_array($v['status'], ['Draft', 'Rejected', 'Pending'], true)
            && $this->allowed($this->group($v['vehicle_group'] ?? 'public'), ($v['pass_for'] ?? 'vendor') === 'staff' && ! is_vendor_admin() ? 'staff_request' : 'create');
    }

    /** KPK lets the approver correct the details on the Request list (Pending) and Approved list (Approved) pages. */
    private function approverMay(array $v): bool
    {
        return ($v['status'] === 'Pending' && $this->may($v, 'request')) || ($v['status'] === 'Approved' && $this->may($v, 'approved'));
    }

    private function canEdit(array $v): bool
    {
        return $this->ownerMay($v) || $this->approverMay($v);
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

    /** KPK "New Vehicle Entry Pass" (direct close): an admin records a pass that is already issued. */
    public function directClose(string $group = 'public', string $kind = 'vendor')
    {
        $this->init();
        $group = $this->group($group);
        if (is_vendor_admin() || ! $this->allowed($group, 'closed') || ! has_access('vehicle', 'new_issued_pass')) {
            return $this->back('error', 'You are not allowed to add an issued pass directly.', 'dashboard');
        }
        $kind = ($kind === 'staff' && $group === 'public') ? 'staff' : 'vendor';

        return $this->form(['vehicle_group' => $group, 'pass_for' => $kind, 'direct' => 1]);
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
        $new = array_diff_key($v, array_flip(['id', 'app_no', 'app_date', 'status', 'reject_reason', 'payment_status', 'payment_ref', 'payment_type', 'receipt_no', 'sticker_serial', 'sticker_year', 'pass_expiry', 'issued_at', 'issued_by', 'collector_name', 'approved_by', 'approved_at', 'created_at', 'updated_at', 'collect_date', 'issue_date', 'collector_ic', 'terminated_at']));
        $new['renewed_from'] = (int) $v['id'];
        $new['type_of_app']  = 'Renewal';
        $new['driver_rows']  = $this->drivers((int) $v['id']);

        return $this->form($new);
    }

    private function renewError(array $v): ?string
    {
        $group = $this->group($v['vehicle_group'] ?? 'public');
        if (! $this->canEdit(array_merge($v, ['status' => 'Draft']))) {
            return 'You are not allowed to request a vehicle pass here.';
        }
        $v['renewal_pending'] = \Config\Database::connect()->table('vehicles')->where('renewed_from', (int) $v['id'])->whereIn('status', ['Draft', 'Pending', 'Approved'])->where('soft_delete', 0)->countAllResults() > 0;

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
            'group' => $group, 'passFor' => $v['pass_for'] ?? 'vendor', 'ownCompany' => $co, 'stickerYears' => VehicleRules::stickerYears(),
            'allTypes' => $this->types($group), 'asApprover' => ! empty($v['id']) && ! $this->ownerMay($v) && $this->approverMay($v),
            'driverRows' => ! empty($v['driver_rows']) ? $v['driver_rows'] : (! empty($v['id']) ? $this->drivers((int) $v['id']) : []),
            'direct' => ! empty($v['direct']), 'ledger' => false,
            'staffRow' => ! empty($v['staff_id']) ? \Config\Database::connect()->table('staff')->where('id', (int) $v['staff_id'])->get()->getRowArray() : null,
        ]);
    }

    /** Staff picker for New Staff Request: finds staff by staff no / name / IC. */
    public function staffLookup()
    {
        $this->init();
        if (is_vendor_admin() || ! ($this->allowed('public', 'staff_request') || $this->allowed('public', 'closed'))) {
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

        $p          = $this->request->getPost();
        $asApprover = $cur && ! $this->ownerMay($cur) && $this->approverMay($cur); // approver fixing details: status never changes
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

        $direct = ! $id && ! empty($p['direct']);
        if ($direct && (is_vendor_admin() || ! $this->allowed($group, 'closed') || ! has_access('vehicle', 'new_issued_pass'))) {
            return $this->back('error', 'You are not allowed to add an issued pass directly.', 'dashboard');
        }
        $submit = ($p['action'] ?? 'submit') === 'submit' || $asApprover || $direct;
        $errors = [];
        $get    = static fn(string $k): string => trim((string) ($p[$k] ?? ''));
        // Dynamic Form Fields > Vehicle Pass Request: a switched-off field is not asked, not checked and keeps its saved value
        $on     = static fn(string $k): bool => VehicleRules::fieldOn($k, $kind === 'staff');
        $req    = static fn(string $k): bool => VehicleRules::fieldRequired($k, $kind === 'staff');
        $keep   = static fn(string $k): string => $on($k) ? $get($k) : trim((string) ($cur[$k] ?? ''));

        $type = null;
        foreach ($this->types($group) as $t) {
            if ((int) $t['id'] === (int) ($p['vehicle_type_id'] ?? 0)) {
                $type = $t;
            }
        }
        $plate = VehicleRules::normalizePlate($get('plate_no'));
        $year  = $on('sticker_year') && in_array($get('sticker_year'), VehicleRules::stickerYears(), true) ? $get('sticker_year') : ($cur['sticker_year'] ?? VehicleRules::stickerYears()[0]);
        $locs  = array_values(array_filter(array_map('trim', (array) ($p['location_access'] ?? []))));
        if (! $on('location_access')) {
            $locs = array_values(array_filter(explode(',', (string) ($cur['location_access'] ?? ''))));
        }
        $valid = array_keys((new VendorLocationModel())->getOptionsForUser(true));
        $locs  = array_values(array_intersect($locs, $valid));

        // who owns the vehicle
        $co    = current_vendor_company();
        // KPK's request form has no owner fields: the owner is the logged-in account, "Application For" (Individual / Company) says which.
        $appFor    = $on('owner_type') && in_array($get('owner_type'), ['Individual', 'Company'], true) ? $get('owner_type') : (in_array($cur['owner_type'] ?? '', ['Individual', 'Company'], true) ? $cur['owner_type'] : 'Individual');
        $acctName  = (string) (session()->get('full_name') ?: session()->get('username'));
        $clientRow = $db->tableExists('clients') && function_exists('current_client_id') ? $db->table('clients')->select('name')->where('id', (int) current_client_id())->get()->getRowArray() : null;
        $coName    = $co ? (string) $co['name'] : (string) (($cur['company_name'] ?? '') ?: ($clientRow['name'] ?? ''));
        $owner = ['owner_type' => $appFor, 'owner_name' => $appFor === 'Company' ? $coName : ($cur['owner_name'] ?? $acctName), 'owner_ic' => (string) ($cur['owner_ic'] ?? ''),
                  'company_name' => $appFor === 'Company' ? $coName : '', 'contact_no' => (string) ($cur['contact_no'] ?? ''), 'staff_id' => null];
        if ($kind === 'staff') {
            $st = $db->table('staff')->where('id', (int) ($p['staff_id'] ?? ($cur['staff_id'] ?? 0)))->get()->getRowArray();
            if (! $st) {
                $errors[] = 'Find and choose the staff member first.';
            } else {
                $owner = ['owner_type' => 'Staff', 'owner_name' => (string) $st['full_name'], 'owner_ic' => (string) ($st['ic_passport'] ?? ''),
                          'company_name' => '', 'contact_no' => (string) ($cur['contact_no'] ?? ''), 'staff_id' => (int) $st['id'], 'staff_no' => (string) ($st['staff_no'] ?? ''), 'department' => (string) ($st['department'] ?? '')];
            }
        } elseif ($group === 'cnc' || $co) {
            $owner['owner_type'] = 'Company'; // Cargo | Non-Cargo and vendor accounts are company vehicles
            if ($co) {
                $owner['company_name'] = (string) $co['name'];
            }
        }

        $operation = $on('vehicle_operation') ? (in_array($get('vehicle_operation'), VehicleRules::OPERATIONS, true) ? $get('vehicle_operation') : null) : ($cur['vehicle_operation'] ?? null);
        if ($group === 'cnc' && $on('vehicle_operation') && ! $operation) {
            $errors[] = 'Choose the vehicle operation.';
        }
        if ($plate === '') {
            $errors[] = 'Plate number is required.';
        }
        if (! $type) {
            $errors[] = 'Choose the vehicle type.';
        }
        if ($submit) {
            // KPK: Colour and Brand always; Insurance on Public and Cargo / Non-Cargo; Road Tax on Cargo / Non-Cargo (and when Config makes the others mandatory)
            foreach (['road_tax_expiry' => 'Road tax expiry', 'insurance_expiry' => 'Insurance expiry', 'colour' => 'Colour', 'brand' => 'Brand', 'year_made' => 'Year made'] as $k => $label) {
                $need = $req($k);
                if (in_array($k, ['road_tax_expiry', 'insurance_expiry'], true)) {
                    $need = $kind === 'staff' ? false : ($group === 'cnc' ? $on($k) : $req($k)); // KPK: Staff neither, Cargo / Non-Cargo both, Public per Config
                }
                if ($need && $get($k) === '') {
                    $errors[] = "$label is required.";
                }
            }
            if (! $direct && $req('documents') && $this->docCount($id, $p) < 1) {
                $errors[] = 'Upload at least one document (vehicle grant / road tax / insurance).';
            }
            if ($on('tnc') && ! $asApprover && ! $direct && empty($p['tnc'])) {
                $errors[] = 'Please accept the terms and conditions.';
            }
            if ($kind !== 'staff' && $req('location_access') && $locs === []) {
                $errors[] = 'Choose at least one Location Access.';
            }
            if ($type) {
                foreach (VehicleRules::TYPE_FIELDS as $col => [$field, $label]) {
                    if ((int) ($type[$col] ?? 0) === 1 && $get($field) === '') {
                        $errors[] = "$label is required for {$type['name']}.";
                    }
                }
            }
        }
        $ignore = array_filter([$id, $origId]);
        $coId   = $co ? (int) $co['id'] : (int) ($cur['vendor_company_id'] ?? ($orig['vendor_company_id'] ?? 0));
        if ($plate !== '' && $type && ($h = VehicleRules::plateHolder($db, $plate, $ignore, $year, $coId ?: null))) {
            $errors[] = VehicleRules::plateMessage($plate, $h);
        }
        if ($type && $group === 'cnc' && ($w = VehicleRules::weightError($type, $get))) {
            $errors[] = $w;
        }
        [$driverIds, $driverErr] = $this->postedDrivers();
        if ($driverErr) {
            $errors[] = $driverErr;
        }
        if ($submit && ! $asApprover && $type) {
            // KPK limits: staff max vehicles, company max vehicles (car / motorbike / van types only)
            $staffRowId = $kind === 'staff' ? (int) ($owner['staff_id'] ?? 0) : null;
            if ($lim = VehicleRules::limitError($db, (string) ($type['category'] ?? ''), $kind === 'staff' ? null : $coId, $staffRowId, $id)) {
                $errors[] = $lim;
            }
            // year-end window: a renewal or a resubmitted (rejected) pass must be for next year's sticker
            if (($origId || ($cur && $cur['status'] === 'Rejected')) && ($ye = VehicleRules::yearEndError($year))) {
                $errors[] = $ye;
            }
        }
        $stickerF = [];
        if ($direct && ! $errors) {
            [$serr, $stickerF] = $this->stickerFields($db, ['payment_required' => $type ? (int) $type['payment_required'] : 0, 'payment_status' => 'Pending', 'vendor_company_id' => null], 0);
            if ($serr) {
                $errors[] = $serr;
            }
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
            'staff_no' => $owner['staff_no'] ?? null,
            'owner_type' => $owner['owner_type'] ?: 'Individual', 'owner_name' => $owner['owner_name'], 'owner_ic' => $owner['owner_ic'] ?: null,
            'company_name' => $owner['company_name'] ?: null, 'contact_no' => $owner['contact_no'] ?: null, 'staff_id' => $owner['staff_id'],
            'vehicle_type_id' => $type ? (int) $type['id'] : null, 'plate_no' => $plate,
            'brand' => $keep('brand') ?: null, 'year_made' => $keep('year_made') ?: null, 'colour' => $keep('colour') ?: null,
            'road_tax_expiry' => $this->toDate($keep('road_tax_expiry')), 'insurance_expiry' => $this->toDate($keep('insurance_expiry')),
            'location_access' => implode(',', $locs), 'remark' => $keep('remark') ?: null, 'sticker_year' => $year,
            'vehicle_operation' => $group === 'cnc' ? $operation : null,
            'department' => ($on('department') ? $get('department') : '') ?: ($owner['department'] ?? '') ?: ($cur['department'] ?? null), 'hod' => ($on('hod') ? $get('hod') : '') ?: ($cur['hod'] ?? null),
            'payment_required' => $type ? (int) $type['payment_required'] : 0, 'updated_at' => $now,
        ];
        foreach (VehicleRules::TYPE_FIELDS as [$field, $label]) {
            $data[$field] = $get($field) ?: null;
        }
        foreach (['truck_head_number', 'trailer_number'] as $f) {
            $data[$f] = $data[$f] !== null ? VehicleRules::stripSpaces($data[$f]) : null;
        }
        $data['payment_status'] = $data['payment_required'] ? ($cur['payment_status'] ?? 'Pending') : 'None';

        $db->transStart();
        if ($id) {
            $from = $cur['status'];
            if ($submit && ! $asApprover && $from !== 'Pending') {
                $data['status'] = 'Pending';
                $data['reject_reason'] = null;
            }
            $db->table('vehicles')->where('id', $id)->update($data);
            if ($submit && ! $asApprover && $from !== 'Pending') {
                $this->log($id, $from, 'Pending', 'Submitted');
            } elseif ($asApprover) {
                $this->log($id, $from, $from, 'Details corrected by ' . $this->userName());
            }
        } else {
            $data += [
                'client_id' => $clientId ?: null, 'app_no' => $this->nextAppNo($db), 'app_date' => date('Y-m-d'), 'type_of_app' => $origId ? 'Renewal' : 'New',
                'vehicle_group' => $group, 'pass_for' => $kind, 'vendor_company_id' => $co ? (int) $co['id'] : ($orig['vendor_company_id'] ?? null),
                'created_by_user' => (int) session()->get('user_id') ?: null, 'renewed_from' => $origId ?: null,
                'status' => $submit ? 'Pending' : 'Draft', 'requested_by' => $this->userName(), 'created_at' => $now,
            ];
            if ($direct) {
                $cd = $get('collect_date') !== '' ? $this->toDate($get('collect_date')) : null;
                $data = array_merge($data, array_intersect_key($stickerF, array_flip(['sticker_serial', 'issue_date', 'collector_ic', 'collector_name', 'receipt_no', 'payment_type', 'payment_status'])), [
                    'status' => 'Issued', 'collect_date' => $cd, 'pass_expiry' => VehicleRules::expiryFor($year, $kind === 'staff'),
                    'issued_at' => $now, 'issued_by' => $this->userName(), 'approved_by' => $this->userName(), 'approved_at' => $now,
                ]);
            }
            $data['soft_delete'] = 0;
            $db->table('vehicles')->insert($data);
            $id = (int) $db->insertID();
            $this->log($id, null, $data['status'], $direct ? 'Added as an issued pass (direct close). Sticker ' . ($data['sticker_serial'] ?? '') : (($origId ? 'Renewal of ' . $orig['app_no'] . '. ' : '') . ($submit ? 'Submitted' : 'Saved as draft')));
            if ($direct) {
                $this->recordSticker($db, array_merge($data, ['id' => $id]), array_merge($stickerF, ['pass_expiry' => $data['pass_expiry'], 'sticker_year' => $year]), $this->userName());
            }
        }
        $this->saveDrivers($db, $id, $driverIds);
        $this->saveFiles($db, $id);
        $db->transComplete();

        return $this->back('success', $asApprover ? 'Details updated.' : ($direct ? 'Issued vehicle pass added.' : ($submit ? 'Vehicle pass request submitted.' : 'Saved as draft.')), 'vehicles/view/' . $id);
    }

    /** Documents the pass will have after this save: kept existing files plus valid new uploads. */
    private function docCount(?int $id, array $p): int
    {
        $db   = \Config\Database::connect();
        $kept = 0;
        if ($id) {
            $b = $db->table('vehicle_files')->where('vehicle_id', $id);
            if (! empty($p['remove_file'])) {
                $b->whereNotIn('id', array_map('intval', (array) $p['remove_file']));
            }
            $kept = $b->countAllResults();
        }
        $new = 0;
        foreach ((array) ($this->request->getFiles()['documents'] ?? []) as $f) {
            if ($f && $f->isValid() && ! $f->hasMoved()) {
                $new++;
            }
        }

        return $kept + $new;
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

        return view('vehicles/view', [
            'pageTitle' => 'Vehicle Pass ' . $v['app_no'] . ' - SafeG', 'v' => $v, 'group' => $g,
            'files' => $db->table('vehicle_files')->where('vehicle_id', $v['id'])->get()->getResultArray(),
            'logs' => $db->table('vehicle_status_logs')->where('vehicle_id', $v['id'])->orderBy('id', 'DESC')->get()->getResultArray(),
            'drivers' => $this->drivers((int) $v['id']),
            'stickers' => $db->tableExists('vehicle_stickers') ? $db->table('vehicle_stickers')->where('vehicle_id', $v['id'])->orderBy('id', 'DESC')->get()->getResultArray() : [],
            'reissues' => $db->tableExists('vehicle_reissues') ? $db->table('vehicle_reissues')->where('vehicle_id', $v['id'])->orderBy('id', 'DESC')->get()->getResultArray() : [],
            'labels' => (new VendorLocationModel())->getAllLabelMap(), 'typeFields' => VehicleRules::TYPE_FIELDS,
            'locationOptions' => (new VendorLocationModel())->getOptionsForUser(true), 'types' => $this->types($g), 'ledger' => $this->ledgerCode($v) !== '',
            'rejectReasons' => $this->rejectReasons(),
            'listUrl' => $this->listUrl($g, ($v['pass_for'] ?? '') === 'staff' ? 'staff' : 'all'),
            'can' => [
                'edit'      => $this->canEdit($v),
                'approve'   => $this->may($v, 'request'),
                'issue'     => $this->may($v, 'approved'),
                'reject_approved' => $v['status'] === 'Approved' && $this->may($v, 'approved'),
                'terminate' => $this->may($v, 'closed') && has_access('vehicle', 'terminate_pass'),
                'delete'    => $this->canDelete($v),
                'submit'    => $own && in_array($v['status'], ['Draft', 'Rejected'], true),
                'renew'     => $own && $this->renewError($v) === null,
                'closedTools' => $this->may($v, 'closed') && $v['status'] === 'Issued' && has_access('vehicle', 'sticker_tools'),
                'edit_approver' => $this->approverMay($v) && ! $this->ownerMay($v),
            ],
            'renewMsg'  => $own ? $this->renewError($v) : null,
            'payOnline' => VehiclePayment::enabled(),
        ]);
    }

    private function rejectReasons(): array
    {
        try {
            return (new \App\Models\RejectReasonModel())->select('id, reason')->where('status', 'active')->orderBy('reason', 'ASC')->findAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function canDelete(array $v): bool
    {
        return ($v['status'] === 'Rejected' && $this->may($v, 'delete_rejected'))
            || ($v['status'] === 'Draft' && ($this->may($v, 'create') || ($v['pass_for'] ?? '') === 'staff'))
            || (in_array($v['status'], ['Issued', 'Terminated'], true) && $this->may($v, 'delete_closed'));
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
        $page = ['submit' => 'create', 'approve' => 'request', 'reject' => $v['status'] === 'Approved' ? 'approved' : 'request', 'issue' => 'approved', 'terminate' => 'closed'][$action] ?? null;
        $ok   = $page && $this->may($v, $page) && ($action !== 'terminate' || has_access('vehicle', 'terminate_pass'));
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

        $autoIssue = false;
        if ($action === 'submit') {
            $t = $db->table('vehicle_types')->where('id', $v['vehicle_type_id'])->get()->getRowArray() ?: [];
            if ($h = VehicleRules::plateHolder($db, (string) $v['plate_no'], array_filter([(int) $v['id'], (int) ($v['renewed_from'] ?? 0)]), null, (int) ($v['vendor_company_id'] ?? 0) ?: null)) {
                return $this->back('error', VehicleRules::plateMessage((string) $v['plate_no'], $h), $back);
            }
            $isStaff = ($v['pass_for'] ?? '') === 'staff';
            if ($lim = VehicleRules::limitError($db, (string) ($t['category'] ?? ''), $isStaff ? null : (int) ($v['vendor_company_id'] ?? 0), $isStaff ? (int) $v['staff_id'] : null, (int) $v['id'])) {
                return $this->back('error', $lim, $back);
            }
            if (($v['status'] === 'Rejected' || ! empty($v['renewed_from'])) && ($ye = VehicleRules::yearEndError((string) $v['sticker_year']))) {
                return $this->back('error', $ye, $back);
            }
            $upd['reject_reason'] = null;
        } elseif ($action === 'approve') {
            if (trim((string) $v['location_access']) === '' && ($v['owner_type'] ?? '') !== 'Staff') {
                return $this->back('error', 'The pass has no Location Access.', $back);
            }
            $cd = trim((string) $this->request->getPost('collect_date'));
            if ($cd !== '' && ! $this->toDate($cd)) {
                return $this->back('error', 'Use a valid collection date (dd/mm/yyyy).', $back);
            }
            $upd += ['approved_by' => $this->userName(), 'approved_at' => date('Y-m-d H:i:s'), 'collect_date' => $cd !== '' ? $this->toDate($cd) : null];
            // KPK: a Cargo | Non-Cargo type that needs no payment is issued the moment it is approved; so is every pass when direct close is switched on
            $payReq    = (int) ($v['payment_required'] ?? 0) === 1;
            $autoIssue = ($g === 'cnc' && ! $payReq) || VehicleRules::approveIssues();
            if ($autoIssue && $payReq && ($v['payment_status'] ?? '') !== 'Paid') {
                $autoIssue = false; // a payable type still goes through the receipt step
            }
            if ($autoIssue) {
                $upd['status']       = 'Issued';
                $upd['pass_expiry']  = VehicleRules::expiryFor((string) ($v['sticker_year'] ?? ''), ($v['pass_for'] ?? '') === 'staff');
                $upd['issue_date']   = date('Y-m-d');
                $upd['issued_at']    = date('Y-m-d H:i:s');
                $upd['issued_by']    = $this->userName();
                $to                  = 'Issued';
            }
        } elseif ($action === 'reject') {
            $reason = '';
            if ((int) $this->request->getPost('reject_reason_id') > 0) {
                try {
                    $rr     = (new \App\Models\RejectReasonModel())->find((int) $this->request->getPost('reject_reason_id'));
                    $reason = trim((string) ($rr['reason'] ?? ''));
                } catch (\Throwable $e) {
                    $reason = '';
                }
            }
            if ($reason === '') {   // KPK: a reason from the list is required; the remark is extra
                return $this->back('error', 'Please choose a reason for rejecting.', $back);
            }
            $upd['reject_reason'] = trim($reason . ($reason !== '' && $note !== '' ? ' - ' : '') . $note);
            $note = $upd['reject_reason'];
        } elseif ($action === 'issue') {
            if ($err = $this->issueFields($db, $v, $upd)) {
                return $this->back('error', $err, $back);
            }
        }

        $db->transStart();
        $db->table('vehicles')->where('id', (int) $id)->update($upd);
        $this->log((int) $id, (string) $v['status'], $to, $note !== '' ? $note : ($action === 'issue' ? 'Sticker ' . $upd['sticker_serial'] : ($autoIssue ? 'Approved and issued (no payment step)' : null)));
        $issued = $action === 'issue' || $autoIssue;
        if ($issued) {
            $this->recordSticker($db, array_merge($v, $upd, ['id' => (int) $id]), $upd, $this->userName());
        }
        if ($action === 'terminate') {
            $this->closeStickers($db, (int) $id);
            $db->table('vehicles')->where('id', (int) $id)->update(['terminated_at' => date('Y-m-d H:i:s')]);
        }
        // a renewal that gets its sticker replaces the old pass
        if ($issued && ! empty($v['renewed_from'])) {
            $old = $db->table('vehicles')->where('id', (int) $v['renewed_from'])->get()->getRowArray();
            if ($old && $old['status'] === 'Issued') {
                $db->table('vehicles')->where('id', $old['id'])->update(['status' => 'Terminated', 'terminated_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                $this->closeStickers($db, (int) $old['id']);
                $this->log((int) $old['id'], 'Issued', 'Terminated', 'Replaced by renewal ' . $v['app_no']);
            }
        }
        $db->transComplete();

        // KPK: the applicant is emailed when the pass is approved or rejected
        if (($action === 'approve' || $action === 'reject') && $db->transStatus()) {
            \App\Libraries\VehicleMailer::decision(array_merge($v, $upd), $action === 'approve' ? 'APPROVED' : 'REJECTED', $action === 'reject' ? (string) $upd['reject_reason'] : $note);
        }

        $msg = ['submit' =>'Submitted for approval.', 'approve' => $autoIssue ? 'Vehicle pass approved and issued.' : 'Vehicle pass approved. It is now waiting for sticker issuance.', 'reject' => 'Vehicle pass rejected.',
            'issue' => 'Sticker issued.', 'terminate' => 'Vehicle pass terminated.'][$action];
        $dest = $back;
        if ($action === 'reject' && $v['status'] === 'Approved') {
            $dest = $this->listUrl($g, 'reviewed');
        } elseif ($action === 'approve' || $action === 'reject') {
            $dest = $this->listUrl($g, 'request');
        } elseif ($action === 'issue') {
            $dest = $this->listUrl($g, 'closed');
        }

        return $this->back('success', $msg, $dest);
    }

    /**
     * Closed List tools (KPK): reissue a sticker (Damage / Lost / Stolen), change serial no, change receipt no,
     * change vehicle type, change location access. They never change the pass status.
     */
    public function adjust($id, string $what)
    {
        $this->init();
        $db   = \Config\Database::connect();
        $v    = $this->find((int) $id);
        $back = 'vehicles/view/' . (int) $id;
        if (! $v) {
            return $this->back('error', 'Vehicle pass not found.');
        }
        if (! $this->may($v, 'closed') || $v['status'] !== 'Issued' || ! has_access('vehicle', 'sticker_tools')) {
            return $this->back('error', 'You cannot change this pass. Only an issued pass can be changed from the Closed List.', $back);
        }
        $g   = fn(string $k) => trim((string) $this->request->getPost($k));
        $now = date('Y-m-d H:i:s');
        $upd = ['updated_at' => $now];
        $ok  = '';
        $log = '';

        if ($what === 'reissue') {
            $reason = $g('reissue_reason');
            if (! in_array($reason, VehicleRules::REISSUE_REASONS, true)) {
                return $this->back('error', 'Choose the reason for the reissue.', $back);
            }
            foreach ($this->drivers((int) $v['id']) as $dr) {
                if (($dr['card_status'] ?? '') !== 'Active') {
                    return $this->back('error', 'There are driver passes not yet issued (or no longer active): ' . ($dr['full_name'] ?? '') . '.', $back);
                }
            }
            [$err, $f] = $this->stickerFields($db, array_merge($v, ['payment_status' => 'Pending']), (int) $v['id']);
            if ($err) {
                return $this->back('error', $err, $back);
            }
            $db->transStart();
            $db->table('vehicle_reissues')->insert([
                'vehicle_id' => (int) $v['id'], 'reason' => $reason, 'old_serial' => $v['sticker_serial'], 'new_serial' => $f['sticker_serial'],
                'issue_date' => $f['issue_date'], 'collector_ic' => $f['collector_ic'], 'payment_type' => $f['payment_type'] ?? null, 'receipt_no' => $f['receipt_no'] ?? null,
                'remark' => $g('remark') ?: null, 'done_by' => $this->userName(), 'created_at' => $now,
            ]);
            $upd += array_intersect_key($f, array_flip(['sticker_serial', 'issue_date', 'collector_ic', 'collector_name', 'receipt_no', 'payment_type']));
            $db->table('vehicles')->where('id', (int) $v['id'])->update($upd);
            $this->recordSticker($db, array_merge($v, $upd, ['id' => (int) $v['id']]), $f, $this->userName());
            $this->saveFiles($db, (int) $v['id']);
            $this->log((int) $v['id'], 'Issued', 'Issued', "Sticker reissued ({$reason}): {$v['sticker_serial']} -> {$f['sticker_serial']}");
            $db->transComplete();

            return $this->back('success', 'Reissue sticker completed.', $back);
        }
        if ($what === 'change-serial') {
            $serial = $g('sticker_serial');
            if ($serial === '') {
                return $this->back('error', 'Enter the serial number.', $back);
            }
            if ($db->table('vehicles')->where('sticker_serial', $serial)->where('id !=', (int) $v['id'])->countAllResults() > 0) {
                return $this->back('error', "Fail to update. Duplicated serial number '{$serial}' found.", $back);
            }
            $upd['sticker_serial'] = $serial;
            $ok = 'Serial number updated.';
            $log = "Serial number changed: {$v['sticker_serial']} -> {$serial}";
        } elseif ($what === 'change-receipt') {
            $type = strtoupper($g('payment_type') ?: 'CASH');
            if (! in_array($type, VehicleRules::PAYMENT_TYPES, true) || ($type === 'LEDGER' && $this->ledgerCode($v) === '')) {
                return $this->back('error', 'Choose a valid payment type.', $back);
            }
            if ($type !== 'LEDGER' && $g('receipt_no') === '') {
                return $this->back('error', 'Enter the receipt number.', $back);
            }
            $upd += ['payment_type' => $type, 'receipt_no' => $type === 'LEDGER' ? null : $g('receipt_no'), 'payment_status' => 'Paid'];
            $ok  = 'Receipt updated.';
            $log = "Payment changed: {$type} " . ($type === 'LEDGER' ? '' : $g('receipt_no'));
        } elseif ($what === 'change-type') {
            $t = null;
            foreach ($this->types($this->vGroup($v)) as $row) {
                if ((int) $row['id'] === (int) $this->request->getPost('vehicle_type_id')) {
                    $t = $row;
                }
            }
            if (! $t) {
                return $this->back('error', 'Choose the vehicle type.', $back);
            }
            $upd['vehicle_type_id'] = (int) $t['id'];
            $ok  = 'Vehicle type updated.';
            $log = 'Vehicle type changed to ' . $t['name'];
        } elseif ($what === 'edit-info') {
            // KPK "Update vehicle information" after issue: year made, colour, brand, road tax, insurance, type (+ Cargo | Non-Cargo details)
            $t = null;
            foreach ($this->types($this->vGroup($v)) as $row) {
                if ((int) $row['id'] === (int) ($this->request->getPost('vehicle_type_id') ?: $v['vehicle_type_id'])) {
                    $t = $row;
                }
            }
            if (! $t) {
                return $this->back('error', 'Choose the vehicle type.', $back);
            }
            foreach (['colour' => 'Colour', 'brand' => 'Brand', 'insurance_expiry' => 'Insurance expiry'] as $k => $label) {
                if (VehicleRules::fieldRequired($k, ($v['pass_for'] ?? '') === 'staff') && $g($k) === '') {
                    return $this->back('error', "{$label} is required.", $back);
                }
            }
            foreach (['road_tax_expiry', 'insurance_expiry'] as $d) {
                if ($g($d) !== '' && ! $this->validDate($g($d))) {
                    return $this->back('error', 'Use a valid date (dd/mm/yyyy) for ' . str_replace('_', ' ', $d) . '.', $back);
                }
            }
            if ($this->vGroup($v) === 'cnc' && ($w = VehicleRules::weightError($t, $g))) {
                return $this->back('error', $w, $back);
            }
            $upd += ['vehicle_type_id' => (int) $t['id'], 'year_made' => $g('year_made') ?: null, 'colour' => $g('colour'), 'brand' => $g('brand'),
                'road_tax_expiry' => $this->toDate($g('road_tax_expiry')), 'insurance_expiry' => $this->toDate($g('insurance_expiry'))];
            if ($this->vGroup($v) === 'cnc') {
                // a field the type does not need is cleared, as KPK does
                foreach (VehicleRules::TYPE_FIELDS as $col => [$field, $label]) {
                    $val = (int) ($t[$col] ?? 0) === 1 ? $g($field) : '';
                    if ((int) ($t[$col] ?? 0) === 1 && $val === '') {
                        return $this->back('error', "{$label} is required for {$t['name']}.", $back);
                    }
                    $upd[$field] = in_array($field, ['truck_head_number', 'trailer_number'], true) ? VehicleRules::stripSpaces($val) : $val;
                }
                if (in_array($g('vehicle_operation'), VehicleRules::OPERATIONS, true)) {
                    $upd['vehicle_operation'] = $g('vehicle_operation');
                }
            }
            $ok  = 'Vehicle information updated.';
            $log = 'Vehicle information updated';
        } elseif ($what === 'change-locations') {
            $locs = array_values(array_intersect(array_filter(array_map('trim', (array) $this->request->getPost('location_access'))), array_keys((new VendorLocationModel())->getOptionsForUser(true))));
            if ($locs === []) {
                return $this->back('error', 'Choose at least one Location Access.', $back);
            }
            $upd['location_access'] = implode(',', $locs);
            $ok  = 'Location access updated.';
            $log = 'Location access changed';
        } else {
            return $this->back('error', 'Unknown action.', $back);
        }

        $db->transStart();
        $db->table('vehicles')->where('id', (int) $v['id'])->update($upd);
        if ($db->tableExists('vehicle_stickers') && array_intersect_key($upd, array_flip(['sticker_serial', 'receipt_no', 'payment_type', 'road_tax_expiry', 'insurance_expiry']))) {
            $st = ['updated_at' => $now];
            foreach (['sticker_serial' => 'serial_no', 'receipt_no' => 'receipt_no', 'payment_type' => 'payment_type', 'road_tax_expiry' => 'road_tax_expiry', 'insurance_expiry' => 'insurance_expiry'] as $from => $to) {
                if (array_key_exists($from, $upd)) {
                    $st[$to] = $upd[$from];
                }
            }
            $db->table('vehicle_stickers')->where('vehicle_id', (int) $v['id'])->where('status', 'active')->update($st);
        }
        $this->log((int) $v['id'], 'Issued', 'Issued', $log);
        $db->transComplete();

        return $this->back('success', $ok, $back);
    }

    /** Does the vehicle's company have a ledger account (then it may pay by LEDGER, no receipt)? */
    private function ledgerCode(array $v): string
    {
        $db = \Config\Database::connect();
        if (empty($v['vendor_company_id']) || ! $db->fieldExists('ledger_acc_no', 'companies')) {
            return '';
        }
        $c = $db->table('companies')->select('ledger_acc_no')->where('id', (int) $v['vendor_company_id'])->get()->getRowArray();

        return trim((string) ($c['ledger_acc_no'] ?? ''));
    }

    /**
     * Shared by Issue and Reissue: serial, date of issuance, collector IC, payment type, receipt.
     * Returns [error|null, fields].
     */
    private function stickerFields($db, array $v, int $ignoreId): array
    {
        $g      = fn(string $k) => trim((string) $this->request->getPost($k));
        $serial = $g('sticker_serial');
        $issued = $g('issue_date') === '' ? date('Y-m-d') : $this->toDate($g('issue_date'));
        if ($serial === '') {
            return ['Enter the sticker serial number.', []];
        }
        if (! $issued) {
            return ['Enter a valid date of issuance (dd/mm/yyyy).', []];
        }
        if ($g('collector_ic') === '') {
            return ["Enter the collector's IC / passport number.", []];
        }
        if ($db->table('vehicles')->where('sticker_serial', $serial)->where('id !=', $ignoreId)->countAllResults() > 0
            || $db->table('vehicle_reissues')->where('new_serial', $serial)->countAllResults() > 0) {
            return ["Duplicated serial number found: '{$serial}' is already used.", []];
        }
        $f = ['sticker_serial' => $serial, 'issue_date' => $issued, 'collector_ic' => $g('collector_ic'), 'collector_name' => $g('collector_name') ?: null];
        if ((int) $v['payment_required'] === 1 && $v['payment_status'] !== 'Paid') {
            $type = strtoupper($g('payment_type') ?: 'CASH');
            if (! in_array($type, VehicleRules::PAYMENT_TYPES, true)) {
                return ['Choose the payment type.', []];
            }
            if ($type === 'LEDGER' && $this->ledgerCode($v) === '') {
                return ['This company has no ledger account, so LEDGER cannot be used.', []];
            }
            $receipt = $g('receipt_no');
            if ($type !== 'LEDGER' && $receipt === '') {
                return ['This vehicle type needs payment. Pay online, or enter the receipt number.', []];
            }
            $f += ['receipt_no' => $type === 'LEDGER' ? null : $receipt, 'payment_type' => $type, 'payment_status' => 'Paid'];
        }

        return [null, $f];
    }

    /** Validates the issuance form and fills $upd. Returns an error text or null. */
    private function issueFields($db, array $v, array &$upd): ?string
    {
        [$err, $f] = $this->stickerFields($db, $v, (int) $v['id']);
        if ($err) {
            return $err;
        }
        $g      = fn(string $k) => trim((string) $this->request->getPost($k));
        $expiry = $g('pass_expiry') !== '' ? $this->toDate($g('pass_expiry')) : (($v['sticker_year'] ?: date('Y')) . '-12-31');
        if (! $expiry || $expiry < date('Y-m-d')) {
            return 'Enter a pass expiry date that is today or later.';
        }
        $upd += $f + ['sticker_year' => $v['sticker_year'] ?: substr($expiry, 0, 4), 'pass_expiry' => $expiry, 'issued_at' => date('Y-m-d H:i:s'), 'issued_by' => $this->userName()];

        return null;
    }

    /**
     * KPK removes a pass softly: it keeps its record (and files) but leaves every list, its sticker goes
     * inactive and its plate becomes free again.
     */
    private function delete(array $v)
    {
        $to = $this->listUrl($this->vGroup($v), ($v['pass_for'] ?? '') === 'staff' ? 'staff' : 'all');
        if (! $this->canDelete($v)) {
            return $this->back('error', 'You cannot delete this vehicle pass.', 'vehicles/view/' . (int) $v['id']);
        }
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        $db->transStart();
        $this->closeStickers($db, (int) $v['id']);
        $db->table('vehicles')->where('id', (int) $v['id'])->update(['soft_delete' => 1, 'deleted_at' => $now, 'updated_at' => $now]);
        $this->log((int) $v['id'], (string) $v['status'], (string) $v['status'], 'Vehicle application removed by ' . $this->userName());
        $db->transComplete();

        return $this->back('success', 'Vehicle pass deleted.', $to);
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
