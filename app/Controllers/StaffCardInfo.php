<?php

namespace App\Controllers;

/**
 * One detail screen per staff pass — KPK's process-list detail panel and
 * closed-list "Card Details" panel, which VMS's vendor module splits into
 * VendorProcessDetail + VendorCardInfo. For staff it is a single page whose
 * buttons change with the pass stage:
 *
 *   Process (approved, not printed): update details, reject, photo
 *       (upload / live camera), Staff No, licences, location access, print.
 *   Issuance (printed):              reprint, bind RFID card, card expiry.
 *   Closed (issued):                 reprint (lost card — no receipt needed
 *       for staff), activate / terminate card, suspend / unsuspend,
 *       change staff no, renew.
 */
class StaffCardInfo extends StaffPassBase
{
    private const EDITABLE = [
        'name_on_staff_pass', 'contact_number', 'email', 'designation', 'department', 'sub_type',
        'address_1', 'address_2', 'address_3', 'postal_code', 'city', 'state', 'country',
        'visa_expiry', 'card_expiry', 'remark', 'access_branch',
    ];

    public function view($id)
    {
        helper(['access', 'privacy', 'role', 'feature']);
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return redirect()->to(base_url('staffs'))->with('error', 'Record not found.');
        }

        $boundCard = ! empty($staff['card_id'])
            ? $this->db()->table('visitor_cards')->where('id', (int) $staff['card_id'])->get()->getRowArray()
            : null;

        $stage = $this->stage($staff);
        $edit  = $this->canEdit();

        return view('staffs/card_info', [
            'pageTitle'         => 'Staff Pass Details - SafeG',
            'staff'             => $staff,
            'stage'             => $stage,
            'icPassport'        => mask_ic_passport($staff['ic_passport'] ?? '', 'N/A'),
            'photoUrl'          => $this->photoUrl($staff),
            'licenses'          => $this->licenses((int) $id),
            'printLogs'         => $this->printLogs((int) $id),
            'statusLogs'        => $this->statusLogs((int) $id),
            'boundCard'         => $boundCard,
            'locationGroups'    => $this->getLocationGroups(),
            'selectedLocations' => array_filter(explode(',', (string) ($staff['location_access'] ?? ''))),
            'rejectReasons'     => $this->rejectReasons(),
            'departments'       => $this->departmentNames(),
            'canEdit'           => $edit && $this->cfg('process_update_button'),
            'canReject'         => $edit && $this->cfg('process_reject_button') && $stage === 'process',
            'canUploadPhoto'    => $edit && $this->cfg('process_upload_photo_button'),
            'canPrint'          => $edit && $this->cfg('printing_generate_button') && $staff['status'] === 'Approved' && in_array($stage, ['process', 'issuance', 'closed'], true),
            'canBindCard'       => $edit && $this->cfg('process_rfid_section'),
            'canAddLicense'     => $edit && $this->cfg('card_info_add_license_button'),
            'canEditLocation'   => $edit && $this->cfg('card_info_edit_location_button'),
            'canActivate'       => $edit && $this->cfg('card_info_activate_button') && $stage === 'closed' && $staff['card_status'] !== 'Active' && $staff['status'] === 'Approved',
            'canTerminate'      => $edit && $this->cfg('card_info_terminate_button') && in_array($stage, ['closed', 'issuance'], true) && $staff['card_status'] !== 'Terminated',
            'canSuspend'        => $edit && $this->cfg('suspend_button') && $stage === 'closed' && $staff['status'] === 'Approved',
            'canUnsuspend'      => $edit && $this->cfg('suspend_button') && $staff['status'] === 'Suspended',
            'canChangeStaffNo'  => $edit,
            'canRenew'          => $edit && $this->cfg('renew_button') && $stage === 'closed' && $staff['status'] === 'Approved',
        ]);
    }

    /** Which pipeline list this pass currently sits on. */
    private function stage(array $s): string
    {
        $cardOut = in_array($s['card_status'] ?? '', ['Active', 'Terminated'], true);
        if ($s['status'] === 'Suspended' || $cardOut) {
            return 'closed'; // includes a renewal that is still pending approval
        }
        if ($s['status'] !== 'Approved') {
            return 'request';
        }
        return empty($s['receipt_no']) ? 'process' : 'issuance';
    }

    private function departmentNames(): array
    {
        try {
            $rows = $this->db()->table('departments')->select('name')->where('status', 'Active')->orderBy('name')->get()->getResultArray();
            $names = array_values(array_filter(array_column($rows, 'name')));
            if ($names) {
                return $names;
            }
        } catch (\Throwable $e) {
        }
        return ['EPIC', 'HR', 'FINANCE', 'OPERATIONS', 'IT', 'MAINTENANCE'];
    }

    /** KPK doUpdate() / doSaveCardDetails(). */
    public function update($id)
    {
        if (! $this->canEdit() || ! $this->cfg('process_update_button')) {
            return $this->fail('Not allowed.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return $this->fail('Record not found.');
        }

        $in     = $this->input();
        $update = [];
        foreach (self::EDITABLE as $field) {
            if (array_key_exists($field, $in)) {
                $value = trim((string) $in[$field]);
                $update[$field] = $value !== '' ? $value : null;
            }
        }
        if (array_key_exists('access_branch', $update) && ! in_array($update['access_branch'], ['KSB', 'KPK', 'BOTH', null], true)) {
            unset($update['access_branch']);
        }
        foreach (['card_expiry', 'visa_expiry'] as $dateField) {
            if (! empty($update[$dateField]) && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $update[$dateField])) {
                return $this->fail('Please enter a valid date.');
            }
        }
        if (empty($update)) {
            return $this->fail('Nothing to update.');
        }
        $update['updated_at'] = date('Y-m-d H:i:s');

        $this->db()->table('staff')->where('id', (int) $id)->update($this->onlyColumns($update));
        $this->logAction($staff, 'update_details', $staff['status']);

        return $this->ok('Details saved.');
    }

    /** KPK doReject() from the process panel. */
    public function reject($id)
    {
        if (! $this->canEdit() || ! $this->cfg('process_reject_button')) {
            return $this->fail('Not allowed.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return $this->fail('Record not found.');
        }
        if ($this->stage($staff) !== 'process') {
            return $this->fail('Only a pass that has not been printed yet can be rejected here.');
        }
        $in       = $this->input();
        $remark   = trim((string) ($in['remark'] ?? ''));
        $reasonId = (int) ($in['reject_reason_id'] ?? 0);
        $reason   = null;
        if ($reasonId > 0) {
            $row    = $this->db()->table('reject_reasons')->where('id', $reasonId)->get()->getRowArray();
            $reason = $row['reason'] ?? null;
        }
        if (! $reason && $remark === '') {
            return $this->fail('Please select a reason or enter a remark.');
        }

        $this->db()->table('staff')->where('id', (int) $id)->update($this->onlyColumns([
            'status'        => 'Rejected',
            'next_action'   => null,
            'reject_reason' => $reason ?: $remark,
            'remark'        => $remark !== '' ? $remark : ($staff['remark'] ?? null),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]));
        $this->logAction($staff, 'reject', 'Rejected', null, $remark, $reason ?: $remark);

        return $this->ok('Staff pass rejected.');
    }

    /** KPK launchUploadPhoto()/doUploadPhoto()/usePhoto() — file or live camera capture. */
    public function uploadPhoto($id)
    {
        if (! $this->canEdit() || ! $this->cfg('process_upload_photo_button')) {
            return $this->fail('Not allowed.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return $this->fail('Record not found.');
        }
        $name = $this->storePhoto();
        if (! $name) {
            return $this->fail('No photo received (JPG or PNG only).');
        }
        $this->db()->table('staff')->where('id', (int) $id)->update($this->onlyColumns(['photo' => $name, 'updated_at' => date('Y-m-d H:i:s')]));

        return $this->ok('Photo saved.', ['photo_url' => base_url('uploads/staff_photos/' . $name)]);
    }

    /** KPK readCard(): bind a physical RFID card from the shared visitor_cards pool. */
    public function readCard($id)
    {
        if (! $this->canEdit() || ! $this->cfg('process_rfid_section')) {
            return $this->fail('Not allowed.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return $this->fail('Record not found.');
        }
        $epc = trim((string) ($this->input()['card_epc'] ?? ''));
        if ($epc === '') {
            return $this->fail('Please tap or enter a card EPC.');
        }

        $db = $this->db();
        $db->transStart();
        $card = $db->table('visitor_cards')->where('card_id', $epc)->get()->getRowArray();
        if (! $card) {
            $db->table('visitor_cards')->insert([
                'card_id'    => $epc,
                'serial_no'  => 'AUTO-' . $epc,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $card = $db->table('visitor_cards')->where('id', $db->insertID())->get()->getRowArray();
        }
        $locked = $db->query('SELECT * FROM visitor_cards WHERE id = ? FOR UPDATE', [$card['id']])->getRowArray();
        if (! $locked || ($locked['status'] !== 'active' && (int) $locked['id'] !== (int) ($staff['card_id'] ?? 0))) {
            $db->transRollback();
            return $this->fail('This card is not available (status: ' . ($locked['status'] ?? 'unknown') . ').');
        }
        if (! empty($staff['card_id']) && (int) $staff['card_id'] !== (int) $locked['id']) {
            $db->table('visitor_cards')->where('id', (int) $staff['card_id'])->where('status !=', 'lost')->update(['status' => 'active']);
        }
        $db->table('visitor_cards')->where('id', (int) $locked['id'])->update(['status' => 'in_use']);
        $db->table('staff')->where('id', (int) $id)->update($this->onlyColumns(['card_id' => (int) $locked['id'], 'updated_at' => date('Y-m-d H:i:s')]));
        $db->transComplete();

        if (! $db->transStatus()) {
            return $this->fail('Could not bind the card — please try again.');
        }
        $this->logAction($staff, 'bind_card', $staff['status'], null, 'Card ' . $epc);

        return $this->ok('Card ' . $epc . ' bound to this pass.');
    }

    public function addLicense($id)
    {
        if (! $this->canEdit() || ! $this->cfg('card_info_add_license_button')) {
            return $this->fail('Not allowed.');
        }
        if (! $this->loadScopedStaff((int) $id)) {
            return $this->fail('Record not found.');
        }
        $in     = $this->input();
        $class  = trim((string) ($in['license_class'] ?? ''));
        $expiry = trim((string) ($in['license_expiry'] ?? ''));
        if ($class === '' || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiry)) {
            return $this->fail('Please fill in the license class and expiry.');
        }
        $this->db()->table('staff_driving_licenses')->insert([
            'staff_id'       => (int) $id,
            'license_class'  => $class,
            'license_expiry' => $expiry,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        return $this->ok('Driving license added.');
    }

    public function deleteLicense($id, $licenseId)
    {
        if (! $this->canEdit() || ! $this->cfg('card_info_add_license_button')) {
            return $this->fail('Not allowed.');
        }
        if (! $this->loadScopedStaff((int) $id)) {
            return $this->fail('Record not found.');
        }
        $this->db()->table('staff_driving_licenses')->where('id', (int) $licenseId)->where('staff_id', (int) $id)->delete();

        return $this->ok('Driving license removed.');
    }

    /** KPK editLocationAccess(). */
    public function updateLocationAccess($id)
    {
        if (! $this->canEdit() || ! $this->cfg('card_info_edit_location_button')) {
            return $this->fail('Not allowed.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return $this->fail('Record not found.');
        }
        $csv = $this->resolveLocationCsv($this->input()['locations'] ?? [], (string) ($staff['location_access'] ?? ''));
        if ($csv === '') {
            return $this->fail('Location Access is mandatory — choose at least one.');
        }
        $this->db()->table('staff')->where('id', (int) $id)->update($this->onlyColumns([
            'location_access' => $csv,
            'updated_at'      => date('Y-m-d H:i:s'),
        ]));
        $this->syncApprovals((int) $id, $csv);
        $this->logAction($staff, 'location_access', $staff['status'], null, $csv);

        return $this->ok('Location access updated.');
    }

    /** KPK activateCard() — only once a card has been printed. */
    public function activate($id)
    {
        if (! $this->canEdit() || ! $this->cfg('card_info_activate_button')) {
            return $this->fail('Not allowed.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return $this->fail('Record not found.');
        }
        if (empty($staff['receipt_no']) && ($staff['card_status'] ?? '') !== 'Terminated') {
            return $this->fail('This card has not been printed yet — print it from Printing List first.');
        }
        if ($staff['status'] !== 'Approved') {
            return $this->fail('Only an approved pass can have its card activated (current status: ' . $staff['status'] . ').');
        }
        $this->db()->table('staff')->where('id', (int) $id)->update($this->onlyColumns([
            'card_status'   => 'Active',
            'terminated_at' => null,
            'terminated_by' => null,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]));
        $this->logAction($staff, 'activate_card', $staff['status']);

        return $this->ok('Card activated.');
    }

    /** KPK terminateCard() — releases the bound RFID card back to the pool. */
    public function terminate($id)
    {
        if (! $this->canEdit() || ! $this->cfg('card_info_terminate_button')) {
            return $this->fail('Not allowed.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return $this->fail('Record not found.');
        }
        $remark = trim((string) ($this->input()['remark'] ?? ''));

        $db = $this->db();
        $db->transStart();
        $db->table('staff')->where('id', (int) $id)->update($this->onlyColumns([
            'card_status'   => 'Terminated',
            'card_id'       => null,
            'terminated_at' => date('Y-m-d H:i:s'),
            'terminated_by' => $this->actor(),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]));
        if (! empty($staff['card_id'])) {
            $db->table('visitor_cards')->where('id', (int) $staff['card_id'])->where('status !=', 'lost')->update(['status' => 'active']);
        }
        $db->transComplete();
        if (! $db->transStatus()) {
            return $this->fail('Could not terminate the card — please try again.');
        }
        $this->logAction($staff, 'terminate_card', $staff['status'], null, $remark);

        return $this->ok('Card terminated and the physical card released back to the pool.');
    }

    /** KPK suspend-list: suspend a closed pass for a period with a reason. */
    public function suspend($id)
    {
        if (! $this->canEdit() || ! $this->cfg('suspend_button')) {
            return $this->fail('Not allowed.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return $this->fail('Record not found.');
        }
        if ($staff['status'] !== 'Approved' || $this->stage($staff) !== 'closed') {
            return $this->fail('Only an issued, approved pass can be suspended.');
        }
        $in     = $this->input();
        $reason = trim((string) ($in['reason'] ?? ''));
        $until  = trim((string) ($in['until'] ?? ''));
        if ($reason === '') {
            return $this->fail('Please enter the reason for suspension.');
        }
        if ($until !== '' && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $until)) {
            return $this->fail('Please enter a valid suspension end date.');
        }

        $this->db()->table('staff')->where('id', (int) $id)->where('status', 'Approved')->update($this->onlyColumns([
            'status'            => 'Suspended',
            'card_status'       => 'Inactive',
            'suspension_period' => $until !== '' ? $until : null,
            'suspended_reason'  => $reason,
            'updated_at'        => date('Y-m-d H:i:s'),
        ]));
        $this->logAction($staff, 'suspend', 'Suspended', null, $reason . ($until ? ' (until ' . $until . ')' : ''));

        return $this->ok('Staff pass suspended.');
    }

    public function unsuspend($id)
    {
        if (! $this->canEdit() || ! $this->cfg('suspend_button')) {
            return $this->fail('Not allowed.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return $this->fail('Record not found.');
        }
        if ($staff['status'] !== 'Suspended') {
            return $this->fail('This pass is not suspended.');
        }
        $this->db()->table('staff')->where('id', (int) $id)->where('status', 'Suspended')->update($this->onlyColumns([
            'status'            => 'Approved',
            'card_status'       => 'Active',
            'suspension_period' => null,
            'suspended_reason'  => null,
            'updated_at'        => date('Y-m-d H:i:s'),
        ]));
        $this->logAction($staff, 'unsuspend', 'Approved', null, trim((string) ($this->input()['remark'] ?? '')));

        return $this->ok('Suspension lifted — card active again.');
    }
}
