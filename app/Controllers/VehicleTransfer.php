<?php

namespace App\Controllers;

use App\Libraries\VehicleRules;

/**
 * Transfer Ownership: change the registered owner of an ISSUED vehicle pass.
 * A vendor account (or staff) files the request; a non-vendor user holding the same Role > Vehicle
 * "Transfer Ownership" key approves it, which rewrites the owner on the pass and logs the change.
 * The sticker and expiry stay as they are.
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

    /** vehicles.* joined, limited to the user's client (and the vendor company for a vendor account) */
    private function scopeVehicle($b, string $alias = 'v')
    {
        if (! is_platform_superadmin()) {
            $b->where($alias . '.client_id', current_client_id());
        }
        if (is_vendor_admin()) {
            $co = current_vendor_company();
            $b->where($alias . '.vendor_company_id', (int) ($co['id'] ?? 0));
        }

        return $b;
    }

    private function findTransfer(int $id): ?array
    {
        $b = \Config\Database::connect()->table('vehicle_transfers t')
            ->select('t.*, v.plate_no, v.app_no AS pass_no, v.status AS pass_status, v.vehicle_group, v.pass_for, v.sticker_serial, v.pass_expiry')
            ->join('vehicles v', 'v.id = t.vehicle_id')->where('t.id', $id);
        $this->scopeVehicle($b);

        return $b->get()->getRowArray() ?: null;
    }

    public function index()
    {
        $this->init();
        if (! $this->allowed()) {
            return $this->back('error', 'You are not allowed to view this list.', 'dashboard');
        }
        $db     = \Config\Database::connect();
        $status = trim((string) $this->request->getGet('status'));
        $search = trim((string) $this->request->getGet('search'));
        $page   = max(1, (int) $this->request->getGet('page'));

        $b = $db->table('vehicle_transfers t')->select('t.*, v.plate_no, v.vehicle_group')->join('vehicles v', 'v.id = t.vehicle_id');
        $this->scopeVehicle($b);
        if (in_array($status, ['Pending', 'Approved', 'Rejected'], true)) {
            $b->where('t.status', $status);
        }
        if ($search !== '') {
            $b->groupStart()->like('v.plate_no', VehicleRules::normalizePlate($search))->orLike('t.transfer_no', $search)
                ->orLike('t.from_owner_name', $search)->orLike('t.to_owner_name', $search)->groupEnd();
        }
        $total = (clone $b)->countAllResults(false);
        $rows  = $b->orderBy('t.id', 'DESC')->limit(self::PER_PAGE, ($page - 1) * self::PER_PAGE)->get()->getResultArray();

        return view('vehicles/transfer_list', [
            'pageTitle' => 'Transfer Ownership - SafeG', 'rows' => $rows, 'status' => $status, 'search' => $search, 'page' => $page,
            'pages' => max(1, (int) ceil($total / self::PER_PAGE)), 'total' => $total,
        ]);
    }

    /** Issued passes that can be transferred (no transfer already waiting). */
    private function candidates(): array
    {
        $db = \Config\Database::connect();
        $b  = $db->table('vehicles v')->select('v.id, v.plate_no, v.app_no, v.owner_name, v.vehicle_group')->where('v.status', 'Issued')
            ->where('v.id NOT IN (SELECT vehicle_id FROM ' . $db->prefixTable('vehicle_transfers') . " WHERE status = 'Pending')", null, false);
        $this->scopeVehicle($b);

        return $b->orderBy('v.plate_no')->get()->getResultArray();
    }

    public function create($vehicleId = 0)
    {
        $this->init();
        if (! $this->allowed()) {
            return $this->back('error', 'You are not allowed to transfer ownership.', 'dashboard');
        }

        return view('vehicles/transfer_form', [
            'pageTitle' => 'New Ownership Transfer - SafeG', 'cands' => $this->candidates(), 'pick' => (int) $vehicleId, 'old' => session()->getFlashdata('old') ?: [],
        ]);
    }

    public function store()
    {
        $this->init();
        if (! $this->allowed()) {
            return $this->back('error', 'You are not allowed to transfer ownership.', 'dashboard');
        }
        $db = \Config\Database::connect();
        $p  = $this->request->getPost();
        $g  = static fn(string $k): string => trim((string) ($p[$k] ?? ''));

        $b = $db->table('vehicles v')->select('v.*')->where('v.id', (int) ($p['vehicle_id'] ?? 0));
        $v = $this->scopeVehicle($b)->get()->getRowArray();
        $errors = [];
        if (! $v) {
            $errors[] = 'Choose the vehicle pass to transfer.';
        } elseif ($v['status'] !== 'Issued') {
            $errors[] = 'Only an issued vehicle pass can be transferred.';
        } elseif ($db->table('vehicle_transfers')->where('vehicle_id', $v['id'])->where('status', 'Pending')->countAllResults() > 0) {
            $errors[] = 'This vehicle already has a transfer waiting for approval.';
        }
        $type = in_array($g('to_owner_type'), ['Individual', 'Company'], true) ? $g('to_owner_type') : '';
        if ($type === '') {
            $errors[] = 'Choose the new owner type.';
        }
        if ($g('to_owner_name') === '') {
            $errors[] = 'New owner name is required.';
        }
        if ($type === 'Company' && $g('to_company') === '') {
            $errors[] = 'New company name is required.';
        }
        if ($g('to_contact_no') === '') {
            $errors[] = 'New owner contact number is required.';
        }
        // a Cargo | Non-Cargo or vendor-account pass always belongs to a company
        if ($v && $type === 'Individual' && ($v['vehicle_group'] === 'cnc' || is_vendor_admin())) {
            $errors[] = 'This vehicle must be transferred to a company.';
        }
        if ($errors) {
            return redirect()->back()->withInput()->with('error', implode("\n", $errors))->with('old', $p);
        }

        $now = date('Y-m-d H:i:s');
        $no  = 'VT-' . date('Ymd');
        $n   = $db->table('vehicle_transfers')->like('transfer_no', $no, 'after')->countAllResults() + 1;
        do {
            $tno = $no . '-' . str_pad((string) $n++, 3, '0', STR_PAD_LEFT);
        } while ($db->table('vehicle_transfers')->where('transfer_no', $tno)->countAllResults() > 0);

        $db->table('vehicle_transfers')->insert([
            'client_id' => $v['client_id'], 'vehicle_id' => $v['id'], 'transfer_no' => $tno,
            'from_owner_type' => $v['owner_type'], 'from_owner_name' => $v['owner_name'], 'from_owner_ic' => $v['owner_ic'], 'from_company' => $v['company_name'],
            'to_owner_type' => $type, 'to_owner_name' => $g('to_owner_name'), 'to_owner_ic' => $g('to_owner_ic') ?: null,
            'to_company' => $type === 'Company' ? $g('to_company') : null, 'to_contact_no' => $g('to_contact_no'),
            'reason' => $g('reason') ?: null, 'status' => 'Pending', 'requested_by' => $this->userName(), 'created_at' => $now, 'updated_at' => $now,
        ]);

        return $this->back('success', "Transfer request {$tno} submitted for approval.", 'vehicle-transfer/view/' . $db->insertID());
    }

    public function view($id)
    {
        $this->init();
        if (! $this->allowed()) {
            return $this->back('error', 'You are not allowed to view this.', 'dashboard');
        }
        $t = $this->findTransfer((int) $id);
        if (! $t) {
            return $this->back('error', 'Transfer request not found.');
        }

        return view('vehicles/transfer_view', [
            'pageTitle' => 'Transfer ' . $t['transfer_no'] . ' - SafeG', 't' => $t,
            'canDecide' => $t['status'] === 'Pending' && ! is_vendor_admin(),
        ]);
    }

    public function decide($id, string $action)
    {
        $this->init();
        $to = 'vehicle-transfer/view/' . (int) $id;
        if (! $this->allowed() || is_vendor_admin()) {
            return $this->back('error', 'You are not allowed to do that.', $to);
        }
        $t = $this->findTransfer((int) $id);
        if (! $t) {
            return $this->back('error', 'Transfer request not found.');
        }
        if ($t['status'] !== 'Pending') {
            return $this->back('error', 'This request has already been ' . strtolower($t['status']) . '.', $to);
        }
        $db   = \Config\Database::connect();
        $now  = date('Y-m-d H:i:s');
        $note = trim((string) $this->request->getPost('note'));

        if ($action === 'reject') {
            if ($note === '') {
                return $this->back('error', 'Please give a reason for rejecting.', $to);
            }
            $db->table('vehicle_transfers')->where('id', $t['id'])->update(['status' => 'Rejected', 'reject_reason' => $note, 'decided_by' => $this->userName(), 'decided_at' => $now, 'updated_at' => $now]);

            return $this->back('success', 'Transfer rejected.', $to);
        }
        if ($t['pass_status'] !== 'Issued') {
            return $this->back('error', 'The vehicle pass is no longer issued, so it cannot be transferred.', $to);
        }

        $db->transStart();
        $db->table('vehicles')->where('id', $t['vehicle_id'])->update([
            'owner_type' => $t['to_owner_type'], 'owner_name' => $t['to_owner_name'], 'owner_ic' => $t['to_owner_ic'],
            'company_name' => $t['to_company'], 'contact_no' => $t['to_contact_no'], 'staff_id' => null, 'updated_at' => $now,
        ]);
        $db->table('vehicle_transfers')->where('id', $t['id'])->update(['status' => 'Approved', 'decided_by' => $this->userName(), 'decided_at' => $now, 'updated_at' => $now]);
        $db->table('vehicle_status_logs')->insert([
            'vehicle_id' => $t['vehicle_id'], 'from_status' => 'Issued', 'to_status' => 'Issued',
            'note' => "Ownership transferred ({$t['transfer_no']}): {$t['from_owner_name']} -> {$t['to_owner_name']}", 'done_by' => $this->userName(), 'created_at' => $now,
        ]);
        $db->transComplete();

        return $this->back('success', 'Transfer approved. The vehicle pass now belongs to the new owner.', $to);
    }
}
