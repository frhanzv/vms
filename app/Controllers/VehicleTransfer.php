<?php

namespace App\Controllers;

use App\Libraries\VehicleRules;

/**
 * Transfer Ownership (KPK behaviour): a company looks up another company's vehicle by plate number and
 * takes it over straight away - no approval step. The pass keeps its sticker and expiry; only the
 * company changes. A non-vendor user (admin / officer) picks the receiving company.
 * An expired pass cannot be taken over: the plate is free, so a new request is made instead.
 * Every transfer is logged in vehicle_transfers (status "Completed").
 */
class VehicleTransfer extends BaseController
{
    private const PER_PAGE = 15;
    private const KEY      = 'transfer_ownership';

    private function init(): void
    {
        helper(['role', 'feature', 'access', 'form', 'vendor_company']);
    }

    private function userName(): string
    {
        return (string) (session()->get('full_name') ?: session()->get('username'));
    }

    private function back(string $kind, string $msg, string $to = 'vehicle-transfer')
    {
        return redirect()->to(base_url($to))->with($kind, $msg);
    }

    private function allowed(): bool
    {
        return has_access('vehicle', self::KEY);
    }

    private function ownCompanyId(): int
    {
        $co = current_vendor_company();

        return (int) ($co['id'] ?? 0);
    }

    /** Transfer log, limited to the client; a vendor account only sees transfers its company gave or received. */
    public function index()
    {
        $this->init();
        if (! $this->allowed()) {
            return $this->back('error', 'You are not allowed to view this list.', 'dashboard');
        }
        $db     = \Config\Database::connect();
        $search = trim((string) $this->request->getGet('search'));
        $page   = max(1, (int) $this->request->getGet('page'));

        $b = $db->table('vehicle_transfers t')->select('t.*, v.plate_no, v.vehicle_group')->join('vehicles v', 'v.id = t.vehicle_id');
        if (! is_platform_superadmin()) {
            $b->where('v.client_id', current_client_id());
        }
        if (is_vendor_admin()) {
            $c = $this->ownCompanyId();
            $b->groupStart()->where('t.from_company_id', $c)->orWhere('t.to_company_id', $c)->groupEnd();
        }
        if ($search !== '') {
            $b->groupStart()->like('v.plate_no', VehicleRules::normalizePlate($search))->orLike('t.transfer_no', $search)
                ->orLike('t.from_company', $search)->orLike('t.to_company', $search)->groupEnd();
        }
        $total = (clone $b)->countAllResults(false);
        $rows  = $b->orderBy('t.id', 'DESC')->limit(self::PER_PAGE, ($page - 1) * self::PER_PAGE)->get()->getResultArray();

        return view('vehicles/transfer_list', [
            'pageTitle' => 'Transfer Ownership - SafeG', 'rows' => $rows, 'search' => $search, 'page' => $page,
            'pages' => max(1, (int) ceil($total / self::PER_PAGE)), 'total' => $total,
        ]);
    }

    /** Companies a non-vendor user can hand a vehicle to. */
    private function companyOptions(): array
    {
        if (is_vendor_admin()) {
            return [];
        }

        return \Config\Database::connect()->table('companies')->select('id, name')->where('status', 'active')->orderBy('name')->get()->getResultArray();
    }

    /**
     * Search by plate: finds the live pass holding that plate that belongs to a company other than the
     * receiving one. Returns [row|null, message|null].
     */
    private function lookup(string $plate): array
    {
        $plate = VehicleRules::normalizePlate($plate);
        if ($plate === '') {
            return [null, null];
        }
        $db = \Config\Database::connect();
        // plate stored as typed (spaces / dashes) still matches the normalised search
        $norm = "UPPER(REPLACE(REPLACE(REPLACE(REPLACE(v.plate_no, ' ', ''), '-', ''), '.', ''), '/', ''))";
        $b    = $db->table('vehicles v')
            ->select('v.*, t.name AS type_name, c.name AS co_name, c.registration_no AS co_reg_no')
            ->join('vehicle_types t', 't.id = v.vehicle_type_id', 'left')
            ->join('companies c', 'c.id = v.vendor_company_id', 'left')
            ->where($norm . ' = ' . $db->escape($plate), null, false)
            ->where('v.status', 'Issued')
            ->where('v.vendor_company_id IS NOT NULL', null, false);
        if (! is_platform_superadmin()) {
            $b->where('v.client_id', current_client_id());
        }
        if (is_vendor_admin()) {
            $b->where('v.vendor_company_id !=', $this->ownCompanyId());
        }
        if ($db->fieldExists('soft_delete', 'vehicles')) {
            $b->groupStart()->where('v.soft_delete', 0)->orWhere('v.soft_delete IS NULL', null, false)->groupEnd();
        }
        $v = $b->orderBy('v.id', 'DESC')->get()->getRowArray();
        if (! $v) {
            return [null, "No vehicle with plate number {$plate} was found under another company."];
        }
        $v['expired'] = ! empty($v['pass_expiry']) && $v['pass_expiry'] < date('Y-m-d');

        return [$v, null];
    }

    public function create($vehicleId = 0)
    {
        $this->init();
        if (! $this->allowed()) {
            return $this->back('error', 'You are not allowed to transfer ownership.', 'dashboard');
        }
        $plate = trim((string) $this->request->getGet('plate'));
        [$v, $msg] = $this->lookup($plate);

        return view('vehicles/transfer_form', [
            'pageTitle' => 'Transfer Ownership - SafeG', 'plate' => $plate, 'v' => $v, 'notFound' => $plate !== '' ? $msg : null,
            'companies' => $this->companyOptions(), 'ownCompany' => current_vendor_company(),
        ]);
    }

    /** Immediate takeover. */
    public function store()
    {
        $this->init();
        if (! $this->allowed()) {
            return $this->back('error', 'You are not allowed to transfer ownership.', 'dashboard');
        }
        $db   = \Config\Database::connect();
        $id   = (int) $this->request->getPost('vehicle_id');
        $self = 'vehicle-transfer/new';

        $to = null;
        if (is_vendor_admin()) {
            $to = current_vendor_company();
        } else {
            $to = $db->table('companies')->where('id', (int) $this->request->getPost('to_company_id'))->where('status', 'active')->get()->getRowArray();
        }
        if (! $to) {
            return $this->back('error', is_vendor_admin() ? 'Your company could not be found.' : 'Choose the company that takes over the vehicle.', $self);
        }

        $v = $db->table('vehicles')->where('id', $id)->get()->getRowArray();
        if (! $v || (! is_platform_superadmin() && (int) $v['client_id'] !== (int) current_client_id())) {
            return $this->back('error', 'Vehicle not found.', $self);
        }
        $back = $self . '?plate=' . urlencode((string) $v['plate_no']);
        if ($v['status'] !== 'Issued') {
            return $this->back('error', 'VEHICLE_ALREADY_CHANGED_OWNER: this vehicle pass is no longer active, please search again.', $back);
        }
        if (! empty($v['pass_expiry']) && $v['pass_expiry'] < date('Y-m-d')) {
            return $this->back('error', 'This pass has expired. Please submit a new vehicle pass request for the plate instead.', $back);
        }
        if ((int) $v['vendor_company_id'] === (int) $to['id']) {
            return $this->back('error', 'The vehicle already belongs to ' . $to['name'] . '.', $back);
        }
        // race guard: the owner shown on the search page must still be the owner now
        $shown = (int) $this->request->getPost('from_company_id');
        if ($shown !== (int) $v['vendor_company_id']) {
            return $this->back('error', 'VEHICLE_ALREADY_CHANGED_OWNER: the vehicle has just changed owner. Please search again.', $back);
        }

        $now = date('Y-m-d H:i:s');
        $no  = 'VT-' . date('Ymd');
        $n   = $db->table('vehicle_transfers')->like('transfer_no', $no, 'after')->countAllResults() + 1;
        do {
            $tno = $no . '-' . str_pad((string) $n++, 3, '0', STR_PAD_LEFT);
        } while ($db->table('vehicle_transfers')->where('transfer_no', $tno)->countAllResults() > 0);

        $db->transStart();
        // update only if the owner is still the one shown (guards two companies grabbing at once)
        $db->table('vehicles')->where('id', $id)->where('vendor_company_id', (int) $v['vendor_company_id'])->update([
            'vendor_company_id' => (int) $to['id'], 'owner_type' => 'Company', 'owner_name' => $to['name'], 'owner_ic' => null,
            'company_name' => $to['name'], 'staff_id' => null, 'updated_at' => $now,
        ]);
        $changed = $db->affectedRows() > 0;
        if ($changed) {
            $db->table('vehicle_transfers')->insert([
                'client_id' => $v['client_id'], 'vehicle_id' => $id, 'transfer_no' => $tno,
                'from_owner_type' => $v['owner_type'], 'from_owner_name' => $v['owner_name'], 'from_owner_ic' => $v['owner_ic'], 'from_company' => $v['company_name'], 'from_company_id' => $v['vendor_company_id'],
                'to_owner_type' => 'Company', 'to_owner_name' => $to['name'], 'to_company' => $to['name'], 'to_company_id' => (int) $to['id'],
                'status' => 'Completed', 'requested_by' => $this->userName(), 'decided_by' => $this->userName(), 'decided_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $db->table('vehicle_status_logs')->insert([
                'vehicle_id' => $id, 'from_status' => 'Issued', 'to_status' => 'Issued',
                'note' => "Ownership transferred ({$tno}): {$v['company_name']} -> {$to['name']}", 'done_by' => $this->userName(), 'created_at' => $now,
            ]);
        }
        $db->transComplete();

        if (! $changed) {
            return $this->back('error', 'VEHICLE_ALREADY_CHANGED_OWNER: the vehicle has just changed owner. Please search again.', $back);
        }

        return $this->back('success', "Vehicle {$v['plate_no']} now belongs to {$to['name']} ({$tno}).", 'vehicle-transfer');
    }
}
