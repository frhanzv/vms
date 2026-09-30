<?php

namespace App\Controllers;

/**
 * The Process List "detail view" — this is the real workhorse KPK page.
 * KPK's process-list.component.ts (~1,800 lines) is really one giant detail
 * panel per vendor pass with a long list of buttons: view/update details,
 * reject with a reason, upload or take a live photo, urine test history,
 * read/bind an RFID card, issue + finish (activate) the card, view card
 * details, print, and view reprint history.
 *
 * This controller ports that same button set. Design is VMS's own — the
 * PROCESS (what each button actually does) is what's matched to KPK.
 */
class VendorProcessDetail extends BaseController
{
    private function loadScopedVendor($id): ?array
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('vendors')->where('id', (int) $id);
        if (! is_platform_superadmin()) {
            $builder->where('company_id', current_company_id());
        }
        return $builder->get()->getRowArray() ?: null;
    }

    /** view() — KPK's viewDetails(). Renders the full detail panel. */
    public function view($id)
    {
        helper(['access', 'privacy', 'role']);
        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return redirect()->to(base_url('vendors/process-list'))->with('error', 'Record not found.');
        }

        $db = \Config\Database::connect();

        $urineTests = $db->table('vendor_urine_tests')
            ->where('vendor_id', (int) $id)
            ->orderBy('test_date', 'DESC')
            ->get()->getResultArray();

        $printLogs = $db->table('vendor_card_print_logs')
            ->where('vendor_id', (int) $id)
            ->orderBy('printed_at', 'DESC')
            ->get()->getResultArray();

        $card = null;
        if ($vendor['card_id']) {
            $card = $db->table('visitor_cards')->where('id', (int) $vendor['card_id'])->get()->getRowArray();
        }

        $licenses = $db->table('vendor_driving_licenses')->where('vendor_id', (int) $id)->orderBy('id', 'DESC')->get()->getResultArray();

        $rejectReasons = [];
        try {
            $rejectReasons = (new \App\Models\RejectReasonModel())->findAll();
        } catch (\Throwable $e) {
            // reject_reasons config not seeded yet — reject still works, just with a free-text fallback.
        }

        return view('vendors/process_detail', [
            'pageTitle'       => 'Vendor Process Detail - SafeG',
            'vendor'          => $vendor,
            'icPassport'      => mask_ic_passport($vendor['ic_no'] ?: ($vendor['passport_no'] ?? ''), 'N/A'),
            'urineTests'      => $urineTests,
            'printLogs'       => $printLogs,
            'boundCard'       => $card,
            'licenses'        => $licenses,
            'locationOptions' => VendorPassRequest::LOCATION_OPTIONS,
            'stateOptions'    => VendorPassRequest::STATE_OPTIONS,
            'selectedLocations' => array_filter(explode(',', (string) ($vendor['location_access'] ?? ''))),
            'rejectReasons'   => $rejectReasons,
            'canEdit'         => has_access('vendor_pass_list', 'edit'),
        ]);
    }

    /** update() — KPK's doUpdate()/doSaveCardDetails(). Saves the editable fields. */
    public function update($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }

        $body = $this->request->getJSON(true) ?? [];
        // Mirrors the full field set on the request form (Application Info,
        // Company, Person) so a mistake spotted here can be fixed directly,
        // per the real KPK "Update" button on this page.
        $allowed = [
            'vendor_company_name', 'full_name', 'name_on_vendor_pass', 'contact_no', 'email',
            'designation', 'remark', 'pass_expiry', 'card_type', 'type_of_registration', 'payment',
            'resident', 'in_out_bound', 'staff_no', 'address_1', 'address_2', 'address_3',
            'country', 'state', 'city', 'postcode', 'vehicle_registration',
        ];
        $update = [];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $body)) {
                $value = trim((string) $body[$field]);
                $update[$field] = $value !== '' ? $value : null;
            }
        }
        if (isset($body['location_access']) && is_array($body['location_access'])) {
            $codes = array_values(array_intersect($body['location_access'], array_keys(VendorPassRequest::LOCATION_OPTIONS)));
            $update['location_access'] = ! empty($codes) ? implode(',', $codes) : null;
        }
        if (empty($update)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nothing to update.']);
        }
        if (isset($update['card_type']) && ! in_array($update['card_type'], ['Permanent', 'Temporary'], true)) {
            unset($update['card_type']);
        }

        \Config\Database::connect()->table('vendors')->where('id', (int) $id)->update($update);

        return $this->response->setJSON(['success' => true, 'message' => 'Details saved.']);
    }

    /** reject() — KPK's doReject()/confirmActionReject(). */
    public function reject($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }

        $body     = $this->request->getJSON(true) ?? [];
        $reasonId = $body['reject_reason_id'] ?? null;
        $remark   = trim((string) ($body['remark'] ?? ''));

        $reasonText = null;
        if ($reasonId) {
            $reason = \Config\Database::connect()->table('reject_reasons')->where('id', (int) $reasonId)->get()->getRowArray();
            $reasonText = $reason['reason'] ?? $reason['name'] ?? null;
        }
        if (! $reasonText && $remark === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Please select a reason or enter a remark.']);
        }

        \Config\Database::connect()->table('vendors')->where('id', (int) $id)->update([
            'status'        => 'Rejected',
            'next_action'   => null,
            'reject_reason' => $reasonText ?: $remark,
            'remark'        => $remark !== '' ? $remark : ($vendor['remark'] ?? null),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Vendor pass rejected.']);
    }

    /**
     * uploadPhoto() — KPK's launchUploadPhoto()/doUploadPhoto()/usePhoto().
     * Accepts either a normal file upload OR a base64 data URL from a live
     * camera capture (rotation/retake is handled client-side on a canvas
     * before it ever reaches here — the server just stores the final image).
     */
    public function uploadPhoto($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }

        $newName = null;

        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/facial_photos', $newName);
        } else {
            $dataUrl = $this->request->getPost('photo_data') ?? ($this->request->getJSON(true)['photo_data'] ?? null);
            if ($dataUrl && preg_match('/^data:image\/(png|jpe?g);base64,/', $dataUrl, $m)) {
                $ext  = $m[1] === 'jpg' ? 'jpeg' : $m[1];
                $data = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1));
                if ($data !== false) {
                    $newName = bin2hex(random_bytes(16)) . '.' . $ext;
                    $dir = WRITEPATH . '../public/uploads/facial_photos';
                    if (! is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    file_put_contents($dir . '/' . $newName, $data);
                }
            }
        }

        if (! $newName) {
            return $this->response->setJSON(['success' => false, 'message' => 'No photo received.']);
        }

        \Config\Database::connect()->table('vendors')->where('id', (int) $id)->update(['facial_photo' => $newName]);

        return $this->response->setJSON([
            'success'   => true,
            'message'   => 'Photo saved.',
            'photo_url' => base_url('uploads/facial_photos/' . $newName),
        ]);
    }

    /** urineTests() — KPK's viewUrineHistoryDetails(). */
    public function urineTests($id)
    {
        $db   = \Config\Database::connect();
        $rows = $db->table('vendor_urine_tests')->where('vendor_id', (int) $id)->orderBy('test_date', 'DESC')->get()->getResultArray();
        return $this->response->setJSON(['success' => true, 'tests' => $rows]);
    }

    /** addUrineTest() — records a new test result. */
    public function addUrineTest($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }

        $testDate = trim((string) ($this->request->getPost('test_date') ?? ''));
        $result   = trim((string) ($this->request->getPost('result') ?? ''));
        $remark   = trim((string) ($this->request->getPost('remark') ?? ''));

        if (! in_array($result, ['Negative', 'Positive'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please choose a result (Negative/Positive).']);
        }

        $attachment = null;
        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $attachment = $file->getRandomName();
            $file->move('uploads/urine_tests', $attachment);
        }

        \Config\Database::connect()->table('vendor_urine_tests')->insert([
            'vendor_id'  => (int) $id,
            'test_date'  => $testDate !== '' ? $testDate : date('Y-m-d'),
            'result'     => $result,
            'remark'     => $remark !== '' ? $remark : null,
            'attachment' => $attachment,
            'created_by' => (string) (session()->get('full_name') ?: session()->get('username')),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Urine test result recorded.']);
    }

    /**
     * readCard() — KPK's readCard(). Binds a physical RFID card to this
     * vendor pass. Reuses the exact same shared-card-pool pattern as
     * VisitorList::bindCard() (find-or-create by EPC, lock the row, check
     * it's available, release any card previously bound to this vendor)
     * rather than inventing a separate card system just for vendors.
     */
    public function readCard($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }

        $body = $this->request->getJSON(true) ?? [];
        $epc  = trim((string) ($body['card_epc'] ?? ''));
        if ($epc === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Please tap or enter a card EPC.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $card = $db->table('visitor_cards')->where('card_id', $epc)->get()->getRowArray();
        if (! $card) {
            $newId = $db->table('visitor_cards')->insert([
                'card_id'    => $epc,
                'serial_no'  => 'AUTO-' . $epc,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $card = $db->table('visitor_cards')->where('id', $db->insertID())->get()->getRowArray();
        }

        $locked = $db->query('SELECT * FROM visitor_cards WHERE id = ? FOR UPDATE', [$card['id']])->getRowArray();
        if (! $locked || $locked['status'] !== 'active') {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'This card is not available (status: ' . ($locked['status'] ?? 'unknown') . ').']);
        }

        if ($vendor['card_id'] && (int) $vendor['card_id'] !== (int) $locked['id']) {
            $db->table('visitor_cards')->where('id', (int) $vendor['card_id'])->where('status !=', 'lost')->update(['status' => 'active']);
        }

        $db->table('visitor_cards')->where('id', (int) $locked['id'])->update(['status' => 'in_use']);
        $db->table('vendors')->where('id', (int) $id)->update(['card_id' => (int) $locked['id']]);

        $db->transComplete();
        if (! $db->transStatus()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Could not bind the card — please try again.']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Card ' . $epc . ' bound to this pass.']);
    }

    /** printLogs() — KPK's viewReprintHistoryDetails(). */
    public function printLogs($id)
    {
        $db   = \Config\Database::connect();
        $rows = $db->table('vendor_card_print_logs')->where('vendor_id', (int) $id)->orderBy('printed_at', 'DESC')->get()->getResultArray();
        return $this->response->setJSON(['success' => true, 'logs' => $rows]);
    }
}
