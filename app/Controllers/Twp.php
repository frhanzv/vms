<?php

namespace App\Controllers;

use App\Libraries\TwpGate;
use App\Libraries\VehiclePayment;

/**
 * KPK Temporary Work Pass (TWP), ported from VendorPassServiceImpl (insertTemporaryWorkPass,
 * doApproveTempWorkPass, doRejectWorkerPass, convertWorkRequest) and the Angular pages
 * Request List / Pass List / TWP Card / Dashboard TWP / TWP reports.
 *
 * The bank (FPX) part stays in Java: "Pay Online" goes through App\Libraries\VehiclePayment,
 * the same bridge the vehicle module uses. Counter payments are recorded here by receipt no.
 */
class Twp extends BaseController
{
    private const STATUSES = ['Pending', 'Approved', 'Rejected'];

    private function db()
    {
        return \Config\Database::connect();
    }

    /** 0 = platform superadmin (every client), otherwise the user's client. */
    private function clientId(): int
    {
        helper(['role', 'feature']);

        return is_platform_superadmin() ? 0 : (int) current_client_id();
    }

    private function scoped(string $alias = 'r'): string
    {
        $c = $this->clientId();

        return $c > 0 ? "{$alias}.company_id = {$c}" : '1=1';
    }

    private function find(int $id): ?array
    {
        return $this->db()->query("SELECT r.* FROM twp_requests r WHERE r.id = ? AND {$this->scoped()}", [$id])->getRowArray() ?: null;
    }

    private function back(string $to, string $type, string $msg)
    {
        return redirect()->to(base_url($to))->with($type, $msg);
    }

    /** The Location Access choices this client owns (KPK selectedLocation). */
    private function locations(): array
    {
        $b = $this->db()->table('locations')->select('location_access')->where('location_access IS NOT NULL', null, false)->orderBy('location_access');
        if (($c = $this->clientId()) > 0 && $this->db()->fieldExists('client_id', 'locations')) {
            $b->groupStart()->where('client_id', $c)->orWhere('client_id IS NULL', null, false)->groupEnd();
        }

        return array_values(array_unique(array_filter(array_column($b->get()->getResultArray(), 'location_access'))));
    }

    /** KPK payment type rates: STAFF per worker, LORRY per worker with a vehicle. */
    private function rates(): array
    {
        $rows = $this->db()->table('settings')->whereIn('setting_key', ['twp_rate_worker', 'twp_rate_vehicle'])->get()->getResultArray();
        $map  = array_column($rows, 'setting_value', 'setting_key');

        return ['worker' => (float) ($map['twp_rate_worker'] ?? 0), 'vehicle' => (float) ($map['twp_rate_vehicle'] ?? 0)];
    }

    // ------------------------------------------------------------------ Request List

    public function requests()
    {
        $q      = trim((string) $this->request->getGet('q'));
        $status = in_array($this->request->getGet('status'), self::STATUSES, true) ? $this->request->getGet('status') : '';
        $sort   = ['cn-a' => 'r.company_name ASC', 'cn-d' => 'r.company_name DESC', 'dt-a' => 'r.created_at ASC'][$this->request->getGet('sort')] ?? 'r.created_at DESC';
        $where  = [$this->scoped()];
        $bind   = [];
        if ($q !== '') {
            $where[] = '(r.app_no LIKE ? OR r.company_name LIKE ? OR r.full_name LIKE ? OR r.ic_no LIKE ? OR r.passport_no LIKE ?)';
            $bind    = array_fill(0, 5, "%{$q}%");
        }
        if ($status !== '') {
            $where[] = 'r.status = ?';
            $bind[]  = $status;
        }
        $rows = $this->db()->query('SELECT r.*, (SELECT COUNT(*) FROM twp_workers w WHERE w.request_id = r.id) AS workers FROM twp_requests r WHERE '
            . implode(' AND ', $where) . " ORDER BY {$sort} LIMIT 500", $bind)->getResultArray();

        return view('twp/requests', ['pageTitle' => 'TWP Request List - SafeG', 'rows' => $rows, 'q' => $q, 'status' => $status, 'sort' => $this->request->getGet('sort') ?? '']);
    }

    public function create()
    {
        return view('twp/form', ['pageTitle' => 'New Temporary Work Pass - SafeG', 'locations' => $this->locations(), 'rates' => $this->rates(), 'old' => [], 'errors' => []]);
    }

    public function save()
    {
        $p   = $this->request->getPost();
        $db  = $this->db();
        $res = ($p['resident'] ?? '') === 'FOREIGNER' ? 'FOREIGNER' : 'LOCAL';
        $err = [];
        foreach (['company_name' => 'Company', 'full_name' => 'Applicant Name', 'contact_no' => 'Contact Number', 'type_of_work' => 'Type Of Work', 'date_from' => 'Date and Time In', 'date_to' => 'Date and Time Out'] as $k => $l) {
            if (trim((string) ($p[$k] ?? '')) === '') {
                $err[] = "{$l} is required.";
            }
        }
        $idNo = trim((string) ($res === 'FOREIGNER' ? ($p['passport_no'] ?? '') : ($p['ic_no'] ?? '')));
        if ($idNo === '') {
            $err[] = ($res === 'FOREIGNER' ? 'Passport Number' : 'IC Number') . ' is required.';
        }
        $from = strtotime((string) ($p['date_from'] ?? ''));
        $to   = strtotime((string) ($p['date_to'] ?? ''));
        if ($from && $to && $to <= $from) {
            $err[] = 'Date and Time Out must be after Date and Time In.';
        }
        $locs = array_values(array_intersect($this->locations(), (array) ($p['locations'] ?? [])));
        if (! $locs) {
            $err[] = 'Choose at least one Location Of Work.';
        }

        $workers = [];
        foreach ((array) ($p['workers'] ?? []) as $w) {
            $name = trim((string) ($w['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $wr  = ($w['resident'] ?? '') === 'FOREIGNER' ? 'FOREIGNER' : 'LOCAL';
            $wid = trim((string) ($w['id_no'] ?? ''));
            if ($wid === '') {
                $err[] = "Worker {$name}: " . ($wr === 'FOREIGNER' ? 'Passport Number' : 'IC Number') . ' is required.';
            }
            $workers[] = ['name' => mb_substr($name, 0, 200), 'resident' => $wr, 'ic_no' => $wr === 'LOCAL' ? $wid : null, 'passport_no' => $wr === 'FOREIGNER' ? $wid : null,
                'contact_no' => trim((string) ($w['contact_no'] ?? '')) ?: null, 'vehicle_no' => strtoupper(trim((string) ($w['vehicle_no'] ?? ''))) ?: null];
        }
        if (! $workers) {
            $err[] = 'Add at least one worker.';
        }
        // KPK: the applicant and every worker are checked against the blacklist before saving
        if (! $err && TwpGate::blacklisted($db, $idNo, ...array_map(static fn($w) => $w['ic_no'] ?? $w['passport_no'], $workers))) {
            $err[] = 'IC No or Passport No Blacklisted / Suspended.';
        }
        if ($err) {
            return view('twp/form', ['pageTitle' => 'New Temporary Work Pass - SafeG', 'locations' => $this->locations(), 'rates' => $this->rates(), 'old' => $p, 'errors' => $err]);
        }

        $rates  = $this->rates();
        $amount = $rates['worker'] * count($workers) + $rates['vehicle'] * count(array_filter(array_column($workers, 'vehicle_no')));
        $now    = date('Y-m-d H:i:s');
        $c      = $this->clientId();
        $db->transStart();
        $db->table('twp_requests')->insert([
            'company_id' => $c > 0 ? $c : null, 'app_date' => $now, 'requested_by' => (int) session()->get('user_id') ?: null,
            'company_name' => mb_substr(trim($p['company_name']), 0, 200), 'company_reg_id' => trim((string) ($p['company_reg_id'] ?? '')) ?: null,
            'resident' => $res, 'ic_no' => $res === 'LOCAL' ? $idNo : null, 'passport_no' => $res === 'FOREIGNER' ? $idNo : null,
            'full_name' => mb_substr(trim($p['full_name']), 0, 200), 'contact_no' => trim($p['contact_no']), 'email' => trim((string) ($p['email'] ?? '')) ?: null,
            'sub_type' => 'TEMPORARY WORK PERMIT', 'type_of_work' => mb_substr(trim($p['type_of_work']), 0, 150), 'location_access' => implode(',', $locs),
            'date_from' => date('Y-m-d H:i:s', $from), 'date_to' => date('Y-m-d H:i:s', $to),
            'amount' => $amount, 'payment_status' => $amount > 0 ? 'Pending' : 'None', 'payment_type' => $amount > 0 ? null : 'NONE',
            'status' => 'Pending', 'created_at' => $now, 'updated_at' => $now,
        ]);
        $id = (int) $db->insertID();
        // KPK appNo = <company>-WP-<running number>
        $db->table('twp_requests')->where('id', $id)->update(['app_no' => 'WP-' . date('ymd') . '-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT)]);
        foreach ($workers as $w) {
            $db->table('twp_workers')->insert($w + ['request_id' => $id, 'status' => 'Pending', 'created_at' => $now, 'updated_at' => $now]);
        }
        $db->transComplete();

        return $this->back("twp/requests/{$id}", 'success', 'Temporary work pass request submitted.');
    }

    public function show($id)
    {
        $r = $this->find((int) $id);
        if (! $r) {
            return $this->back('twp/requests', 'error', 'Request not found.');
        }
        $db = $this->db();

        return view('twp/view', [
            'pageTitle' => $r['app_no'] . ' - SafeG', 'r' => $r, 'onlinePay' => VehiclePayment::enabled(),
            'workers'   => $db->query('SELECT w.*, c.card_no FROM twp_workers w LEFT JOIN twp_cards c ON c.id = w.card_id WHERE w.request_id = ? ORDER BY w.id', [$r['id']])->getResultArray(),
            'by'        => static fn($uid) => $uid ? ($db->table('users')->select('username')->where('id', (int) $uid)->get()->getRowArray()['username'] ?? '-') : '-',
            'reasons'   => $db->tableExists('reject_reasons') ? array_column($db->table('reject_reasons')->select('reason')->get()->getResultArray(), 'reason') : [],
        ]);
    }

    /** KPK doApproveTempWorkPass + convertWorkRequest: every worker becomes an Approved pass with its own number and QR. */
    public function approve($id)
    {
        $r = $this->find((int) $id);
        if (! $r || ! in_array($r['status'], ['Pending', 'Rejected'], true)) {
            return $this->back('twp/requests', 'error', 'Only a Pending or Rejected request can be approved.');
        }
        $db  = $this->db();
        $now = date('Y-m-d H:i:s');
        $db->transStart();
        $db->table('twp_requests')->where('id', $r['id'])->update(['status' => 'Approved', 'remark' => trim((string) $this->request->getPost('remark')) ?: null,
            'reject_reason' => null, 'reviewed_by' => (int) session()->get('user_id') ?: null, 'reviewed_at' => $now, 'updated_at' => $now]);
        foreach ($db->table('twp_workers')->where('request_id', $r['id'])->orderBy('id')->get()->getResultArray() as $i => $w) {
            $db->table('twp_workers')->where('id', $w['id'])->update(['status' => 'Approved', 'updated_at' => $now,
                'pass_no' => $w['pass_no'] ?: $r['app_no'] . '-' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'qr_string' => $w['qr_string'] ?: $w['id'] . strtoupper(bin2hex(random_bytes(8)))]);
        }
        $db->transComplete();

        return $this->back("twp/requests/{$r['id']}", 'success', 'Request approved. The workers are now in the Pass List.');
    }

    /** KPK doRejectWorkerPass (allowed from Pending / Rejected / Approved). Issued cards are taken back. */
    public function reject($id)
    {
        $r      = $this->find((int) $id);
        $reason = trim((string) $this->request->getPost('reject_reason'));
        if (! $r) {
            return $this->back('twp/requests', 'error', 'Request not found.');
        }
        if ($reason === '') {
            return $this->back("twp/requests/{$r['id']}", 'error', 'Give a reject reason.');
        }
        $db  = $this->db();
        $now = date('Y-m-d H:i:s');
        $db->table('twp_requests')->where('id', $r['id'])->update(['status' => 'Rejected', 'reject_reason' => mb_substr($reason, 0, 255),
            'remark' => trim((string) $this->request->getPost('remark')) ?: null, 'reviewed_by' => (int) session()->get('user_id') ?: null, 'reviewed_at' => $now, 'updated_at' => $now]);
        $db->query("UPDATE twp_workers SET status = 'Rejected', card_status = IF(card_status = 'Issued', 'Returned', card_status),
            returned_at = IF(card_status = 'Issued', ?, returned_at), updated_at = ? WHERE request_id = ?", [$now, $now, $r['id']]);

        return $this->back("twp/requests/{$r['id']}", 'success', 'Request rejected.');
    }

    /** Counter payment: the officer records the official receipt (KPK Official Receipt). */
    public function payment($id)
    {
        $r = $this->find((int) $id);
        if (! $r) {
            return $this->back('twp/requests', 'error', 'Request not found.');
        }
        $receipt = trim((string) $this->request->getPost('receipt_no'));
        $type    = in_array($this->request->getPost('payment_type'), ['CASH', 'CHEQUE', 'CREDIT CARD', 'BANK TRANSFER'], true) ? $this->request->getPost('payment_type') : 'CASH';
        if ($receipt === '') {
            return $this->back("twp/requests/{$r['id']}", 'error', 'Receipt No is required.');
        }
        $this->db()->table('twp_requests')->where('id', $r['id'])->update(['receipt_no' => mb_substr($receipt, 0, 80), 'payment_type' => $type,
            'payment_ref' => trim((string) $this->request->getPost('payment_ref')) ?: null, 'payment_status' => 'Paid', 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->back("twp/requests/{$r['id']}", 'success', 'Payment recorded.');
    }

    /** Online payment: handed to the Java FPX service (bank logic is not in PHP). */
    public function payOnline($id)
    {
        $r = $this->find((int) $id);
        if (! $r || $r['payment_status'] !== 'Pending') {
            return $this->back('twp/requests', 'error', 'Nothing to pay for this request.');
        }
        $res = VehiclePayment::start(['app_no' => $r['app_no'], 'amount' => $r['amount'], 'owner_name' => $r['full_name']], base_url("twp/requests/{$r['id']}/payment-return"));
        if (empty($res['ok']) || empty($res['redirect_url'])) {
            return $this->back("twp/requests/{$r['id']}", 'error', $res['message'] ?? 'Online payment could not start.');
        }
        $this->db()->table('twp_requests')->where('id', $r['id'])->update(['payment_ref' => $res['ref'] ?? null, 'payment_type' => 'FPX', 'updated_at' => date('Y-m-d H:i:s')]);

        return redirect()->to($res['redirect_url']);
    }

    public function paymentReturn($id)
    {
        $r = $this->find((int) $id);
        if (! $r || empty($r['payment_ref'])) {
            return $this->back('twp/requests', 'error', 'Payment not found.');
        }
        $res = VehiclePayment::status($r['payment_ref']);
        if (! empty($res['ok']) && ! empty($res['paid'])) {
            $this->db()->table('twp_requests')->where('id', $r['id'])->update(['payment_status' => 'Paid', 'receipt_no' => $res['receipt_no'] ?? $r['receipt_no'],
                'payment_type' => $res['payment_type'] ?? 'FPX', 'updated_at' => date('Y-m-d H:i:s')]);

            return $this->back("twp/requests/{$r['id']}", 'success', 'Payment received.');
        }

        return $this->back("twp/requests/{$r['id']}", 'error', $res['message'] ?? 'Payment not completed.');
    }

    // ------------------------------------------------------------------ Pass List

    public function passes()
    {
        $q    = trim((string) $this->request->getGet('q'));
        $bind = [];
        $sql  = "SELECT w.*, r.app_no, r.company_name, r.date_from, r.date_to, r.location_access, r.type_of_work, c.card_no, c.card_id AS card_epc
                FROM twp_workers w JOIN twp_requests r ON r.id = w.request_id
                LEFT JOIN twp_cards c ON c.id = w.card_id WHERE w.status = 'Approved' AND r.status = 'Approved' AND {$this->scoped()}";
        if ($q !== '') {
            $sql .= ' AND (w.name LIKE ? OR w.ic_no LIKE ? OR w.passport_no LIKE ? OR w.pass_no LIKE ? OR r.company_name LIKE ? OR c.card_no LIKE ?)';
            $bind = array_fill(0, 6, "%{$q}%");
        }
        $rows = $this->db()->query($sql . ' ORDER BY w.id DESC LIMIT 500', $bind)->getResultArray();

        return view('twp/passes', ['pageTitle' => 'TWP Pass List - SafeG', 'rows' => $rows, 'q' => $q, 'freeCards' => $this->freeCards()]);
    }

    private function freeCards(): array
    {
        $c = $this->clientId();

        return $this->db()->query("SELECT id, card_no, card_id FROM twp_cards c WHERE status = 'active'" . ($c > 0 ? " AND (c.company_id = {$c} OR c.company_id IS NULL)" : '')
            . " AND NOT EXISTS (SELECT 1 FROM twp_workers w WHERE w.card_id = c.id AND w.card_status = 'Issued') ORDER BY card_no")->getResultArray();
    }

    private function worker(int $id): ?array
    {
        return $this->db()->query("SELECT w.* FROM twp_workers w JOIN twp_requests r ON r.id = w.request_id WHERE w.id = ? AND r.status = 'Approved' AND {$this->scoped()}", [$id])->getRowArray() ?: null;
    }

    /** KPK "Read Card / Scan Card": bind a free TWP card (picked or scanned by EPC) to this worker. */
    public function issueCard($id)
    {
        $w = $this->worker((int) $id);
        if (! $w || $w['card_status'] === 'Issued') {
            return $this->back('twp/passes', 'error', 'This pass already has a card or was not found.');
        }
        $epc  = trim((string) $this->request->getPost('card_epc'));
        $pick = (int) $this->request->getPost('card_id');
        $free = array_column($this->freeCards(), null, 'id');
        if ($epc !== '') {
            $pick = (int) (array_values(array_filter($free, static fn($c) => strcasecmp($c['card_id'], $epc) === 0))[0]['id'] ?? 0);
        }
        if (! isset($free[$pick])) {
            return $this->back('twp/passes', 'error', 'That card is not a free, active TWP card.');
        }
        $this->db()->table('twp_workers')->where('id', $w['id'])->update(['card_id' => $pick, 'card_status' => 'Issued', 'issued_at' => date('Y-m-d H:i:s'),
            'issued_by' => (int) session()->get('user_id') ?: null, 'returned_at' => null, 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->back('twp/passes', 'success', 'Card ' . $free[$pick]['card_no'] . ' issued to ' . $w['name'] . '.');
    }

    public function returnCard($id)
    {
        $w = $this->worker((int) $id);
        if (! $w || $w['card_status'] !== 'Issued') {
            return $this->back('twp/passes', 'error', 'No issued card to return.');
        }
        $this->db()->table('twp_workers')->where('id', $w['id'])->update(['card_status' => 'Returned', 'returned_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->back('twp/passes', 'success', 'Card returned.');
    }

    // ------------------------------------------------------------------ TWP Card config (+ rates)

    public function cards()
    {
        $c    = $this->clientId();
        $rows = $this->db()->query("SELECT c.*, (SELECT w.name FROM twp_workers w WHERE w.card_id = c.id AND w.card_status = 'Issued' LIMIT 1) AS holder
                FROM twp_cards c" . ($c > 0 ? " WHERE c.company_id = {$c} OR c.company_id IS NULL" : '') . ' ORDER BY c.card_no')->getResultArray();

        return view('twp/cards', ['pageTitle' => 'Temporary Work Pass Card - SafeG', 'rows' => $rows, 'rates' => $this->rates(), 'superadmin' => $c === 0]);
    }

    public function saveCard()
    {
        $id   = (int) $this->request->getPost('id');
        $epc  = trim((string) $this->request->getPost('card_id'));
        $no   = trim((string) $this->request->getPost('card_no'));
        $db   = $this->db();
        $c    = $this->clientId();
        if ($epc === '' || $no === '') {
            return $this->back('twp/cards', 'error', 'Card ID and Temporary Work Pass No are required.');
        }
        if ($db->table('twp_cards')->where('card_id', $epc)->where('id !=', $id)->countAllResults() > 0
            || ($db->tableExists('visitor_cards') && $db->table('visitor_cards')->where('card_id', $epc)->countAllResults() > 0)) {
            return $this->back('twp/cards', 'error', 'This Card ID is already registered.');
        }
        $data = ['card_id' => mb_substr($epc, 0, 100), 'card_no' => mb_substr($no, 0, 80), 'status' => $this->request->getPost('status') === 'inactive' ? 'inactive' : 'active', 'updated_at' => date('Y-m-d H:i:s')];
        if ($id) {
            $own = $db->table('twp_cards')->where('id', $id);
            $c > 0 && $own->where('company_id', $c);
            $own->update($data);
        } else {
            $db->table('twp_cards')->insert($data + ['company_id' => $c > 0 ? $c : null, 'created_by' => (int) session()->get('user_id') ?: null, 'created_at' => $data['updated_at']]);
        }

        return $this->back('twp/cards', 'success', 'TWP card saved.');
    }

    /** STAFF / LORRY rates are global (KPK payment type list), so only the platform superadmin sets them. */
    public function saveRates()
    {
        if ($this->clientId() !== 0) {
            return $this->back('twp/cards', 'error', 'Only the superadmin can change the TWP rates.');
        }
        $db = $this->db();
        foreach (['twp_rate_worker' => 'rate_worker', 'twp_rate_vehicle' => 'rate_vehicle'] as $key => $field) {
            $val = number_format(max(0, (float) $this->request->getPost($field)), 2, '.', '');
            $db->table('settings')->where('setting_key', $key)->countAllResults() > 0
                ? $db->table('settings')->where('setting_key', $key)->update(['setting_value' => $val, 'updated_at' => date('Y-m-d H:i:s')])
                : $db->table('settings')->insert(['setting_key' => $key, 'setting_value' => $val, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        }

        return $this->back('twp/cards', 'success', 'TWP rates saved.');
    }

    // ------------------------------------------------------------------ Dashboard + reports

    /** Visits joined to their worker / request / lanes, scoped to this client. */
    private function visitSql(): string
    {
        return "SELECT v.*, w.name, COALESCE(NULLIF(w.ic_no,''), w.passport_no, '') AS ic, w.contact_no, w.vehicle_no, w.pass_no, r.company_name, r.app_no, r.date_from, r.date_to, r.created_at AS reg_date,
                COALESCE(loc_i.location_access, '') AS location, COALESCE(li.lane, '') AS in_lane, COALESCE(lo.lane, '') AS out_lane,
                ROUND(TIMESTAMPDIFF(MINUTE, v.check_in_time, COALESCE(v.check_out_time, NOW())) / 60, 2) AS hours
            FROM twp_visits v JOIN twp_workers w ON w.id = v.worker_id JOIN twp_requests r ON r.id = w.request_id
            LEFT JOIN lanes li ON li.id = v.check_in_lane_id LEFT JOIN locations loc_i ON loc_i.id = li.location_id
            LEFT JOIN lanes lo ON lo.id = v.check_out_lane_id
            WHERE {$this->scoped()}";
    }

    private function range(): array
    {
        $ok   = static fn($d) => is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) ? $d : null;
        $from = $ok($this->request->getGet('date_from')) ?? date('Y-m-d');
        $to   = $ok($this->request->getGet('date_to')) ?? date('Y-m-d');

        return [$from, $to];
    }

    public function dashboard()
    {
        $db         = $this->db();
        [$from, $to] = $this->range();
        $f          = $db->escape($from . ' 00:00:00');
        $t          = $db->escape($to . ' 23:59:59');
        $base       = $this->visitSql();
        $near       = TwpGate::NEAR_MINUTES;
        $sum        = $db->query("SELECT
                SUM(x.check_in_time BETWEEN {$f} AND {$t}) AS checked_in,
                SUM(x.check_out_time BETWEEN {$f} AND {$t}) AS checked_out,
                SUM(x.check_out_time IS NULL) AS in_premise,
                SUM(x.check_out_time IS NULL AND x.date_to < NOW()) AS out_window,
                SUM(x.check_out_time IS NULL AND x.date_to >= NOW() AND x.date_to < NOW() + INTERVAL {$near} MINUTE) AS near_window,
                ROUND(AVG(IF(x.check_out_time BETWEEN {$f} AND {$t}, x.hours, NULL)), 2) AS avg_hours
            FROM ({$base}) x")->getRowArray();
        $sum['denied'] = (int) $db->query("SELECT COUNT(*) n FROM twp_card_logs l LEFT JOIN twp_workers w ON w.id = l.worker_id LEFT JOIN twp_requests r ON r.id = w.request_id
                WHERE l.action = 'denied' AND l.scanned_at BETWEEN {$f} AND {$t} AND (r.id IS NULL OR {$this->scoped()})")->getRow()->n;
        $byLoc = $db->query("SELECT x.location, SUM(x.check_in_time BETWEEN {$f} AND {$t}) AS checked_in, SUM(x.check_out_time BETWEEN {$f} AND {$t}) AS checked_out,
                SUM(x.check_out_time IS NULL) AS in_premise, SUM(x.check_out_time IS NULL AND x.date_to < NOW()) AS out_window
            FROM ({$base}) x GROUP BY x.location ORDER BY x.location")->getResultArray();
        $inside = $db->query("SELECT x.* FROM ({$base}) x WHERE x.check_out_time IS NULL ORDER BY x.check_in_time DESC LIMIT 200")->getResultArray();

        return view('twp/dashboard', ['pageTitle' => 'TWP Dashboard - SafeG', 'sum' => $sum, 'byLoc' => $byLoc, 'inside' => $inside, 'from' => $from, 'to' => $to]);
    }

    /** KPK TWP / TWP In Premise / TWP Out Of Window reports (tab), with CSV export. */
    public function report()
    {
        $db   = $this->db();
        $tab  = in_array($this->request->getGet('tab'), ['premise', 'oow'], true) ? $this->request->getGet('tab') : 'twp';
        $q    = trim((string) $this->request->getGet('q'));
        $co   = trim((string) $this->request->getGet('company'));
        [$from, $to] = $this->range();
        $where = [];
        $bind  = [];
        if ($tab === 'twp') {
            $where[] = 'x.check_in_time BETWEEN ? AND ?';
            array_push($bind, $from . ' 00:00:00', $to . ' 23:59:59');
        } elseif ($tab === 'premise') {
            $where[] = 'x.check_out_time IS NULL';
        } else {
            // out of window: still inside after the pass ended, or left after it ended
            $where[] = '((x.check_out_time IS NULL AND x.date_to < NOW()) OR x.check_out_time > x.date_to)';
            $where[] = 'x.check_in_time BETWEEN ? AND ?';
            array_push($bind, $from . ' 00:00:00', $to . ' 23:59:59');
        }
        if ($q !== '') {
            $where[] = '(x.name LIKE ? OR x.ic LIKE ? OR x.company_name LIKE ?)';
            array_push($bind, "%{$q}%", "%{$q}%", "%{$q}%");
        }
        if ($co !== '') {
            $where[] = 'x.company_name LIKE ?';
            $bind[]  = "%{$co}%";
        }
        $sort = ['n-a' => 'x.name ASC', 'n-d' => 'x.name DESC', 'dt-a' => 'x.check_in_time ASC'][$this->request->getGet('sort')] ?? 'x.check_in_time DESC';
        $rows = $db->query("SELECT x.* FROM ({$this->visitSql()}) x WHERE " . implode(' AND ', $where) . " ORDER BY {$sort} LIMIT 5000", $bind)->getResultArray();

        if ($this->request->getGet('export')) {
            $fh = fopen('php://temp', 'w+');
            fwrite($fh, "\xEF\xBB\xBF");
            fputcsv($fh, ['No', 'Full Name', 'IC No / Passport No', 'Company Name', 'Contact No', 'Pass No', 'Location', 'Registration Date', 'Time In', 'Time Out', 'Duration (hrs)', 'Pass Valid To']);
            foreach ($rows as $i => $r) {
                fputcsv($fh, [$i + 1, $r['name'], $r['ic'], $r['company_name'], $r['contact_no'], $r['pass_no'], $r['location'], $r['reg_date'], $r['check_in_time'], $r['check_out_time'], $r['hours'], $r['date_to']]);
            }
            rewind($fh);
            $csv = stream_get_contents($fh);
            fclose($fh);

            return $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8')
                ->setHeader('Content-Disposition', 'attachment; filename="twp-' . $tab . '-' . date('Ymd-His') . '.csv"')->setBody($csv);
        }

        return view('twp/report', ['pageTitle' => 'TWP Report - SafeG', 'rows' => $rows, 'tab' => $tab, 'q' => $q, 'company' => $co, 'from' => $from, 'to' => $to,
            'sort' => $this->request->getGet('sort') ?? '', 'manhours' => round(array_sum(array_column($rows, 'hours')), 2)]);
    }
}
