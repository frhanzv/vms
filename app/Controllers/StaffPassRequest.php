<?php

namespace App\Controllers;

/**
 * Staff Pass Request — KPK doStaffVendorPass / saveAsDraftStaffVendorPass /
 * renewVendorPassStaff on VMS's staff table.
 *
 *  - Submit      -> status Pending (goes to Staff List for approval).
 *  - Save Draft  -> status Draft, no duplicate / mandatory checks (KPK SAVED).
 *  - Duplicate   -> another live pass with the same IC / passport blocks it.
 *  - Edit        -> while Draft / Pending / Rejected (admins: any time).
 *  - Renew       -> only from a closed pass (card issued). Name and IC are
 *                   locked, everything else can be updated, licences are
 *                   replaced, status goes back to Pending as RENEWAL.
 */
class StaffPassRequest extends StaffPassBase
{
    private const FALLBACK_DEPARTMENTS = ['EPIC', 'HR', 'FINANCE', 'OPERATIONS', 'IT', 'MAINTENANCE'];

    public function index()
    {
        return view('staffs/staffpassrequest', $this->formData([
            'pageTitle' => 'Staff Pass Request - SafeG',
        ]));
    }

    public function store()
    {
        $isDraft = (bool) $this->request->getPost('save_as_draft');
        $data    = $this->collect($isDraft);

        if (! $isDraft && ($missing = $this->missingRequired($data))) {
            return redirect()->back()->withInput()->with('error', 'Please complete the following mandatory field(s): ' . implode(', ', $missing) . '.');
        }
        if (! $isDraft && $this->duplicateIcExists((string) $data['ic_passport'])) {
            return redirect()->back()->withInput()->with('error', "Another staff pass exists for IC / Passport '{$data['ic_passport']}'.");
        }

        // Location Access is mandatory: it decides which client(s) receive the pass for approval.
        $data['location_access'] = $this->resolveLocationCsv($this->request->getPost('location_access'));
        if (! $isDraft && $data['location_access'] === '') {
            return redirect()->back()->withInput()->with('error', 'Please choose at least one Location Access. It decides which client(s) receive this pass for approval.');
        }

        helper('feature');
        $db = $this->db();
        $data['app_no']     = $this->nextAppNo();
        $data['company_id'] = current_company_id() ?: null;
        $data['is_active']  = 1;
        $data['card_status'] = 'Inactive';
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->handleUploads($data);

        $db->table('staff')->insert($this->onlyColumns($data));
        $id = (int) $db->insertID();
        $this->saveLicenses($id, false);
        $this->syncApprovals($id, (string) $data['location_access'], $isDraft);
        $this->logAction(['id' => $id, 'status' => null], $isDraft ? 'draft' : 'submit', $data['status']);

        return redirect()->to(base_url('staffs'))
            ->with('success', $isDraft ? 'Staff pass request saved as draft.' : 'Staff pass request submitted successfully.');
    }

    public function view($id)
    {
        helper(['privacy', 'access']);
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return redirect()->to(base_url('staffs'))->with('error', 'Staff record not found.');
        }

        return view('staffs/staffpassrequest_detail', [
            'staff'          => $staff,
            'fieldSettings'  => $this->fieldSettings(),
            'locationGroups' => $this->getLocationGroups(),
            'licenses'       => $this->licenses((int) $id),
            'statusLogs'     => $this->statusLogs((int) $id),
            'photoUrl'       => $this->photoUrl($staff),
        ]);
    }

    public function edit($id)
    {
        if (! $this->canEdit()) {
            return redirect()->to(base_url('staffs'))->with('error', 'You are not allowed to edit staff records.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return redirect()->to(base_url('staffs'))->with('error', 'Staff record not found.');
        }
        if ($blocked = $this->editBlock($staff)) {
            return redirect()->to(base_url('staffs'))->with('error', $blocked);
        }

        return view('staffs/staffpassrequest', $this->formData([
            'pageTitle'  => 'Edit Staff - SafeG',
            'staff'      => $staff,
            'licenses'   => $this->licenses((int) $id),
            'formAction' => 'staffpassrequest/update/' . (int) $id,
            'isEdit'     => true,
        ]));
    }

    public function update($id)
    {
        if (! $this->canEdit()) {
            return redirect()->to(base_url('staffs'))->with('error', 'You are not allowed to edit staff records.');
        }
        $current = $this->loadScopedStaff((int) $id);
        if (! $current) {
            return redirect()->to(base_url('staffs'))->with('error', 'Staff record not found.');
        }
        if ($blocked = $this->editBlock($current)) {
            return redirect()->to(base_url('staffs'))->with('error', $blocked);
        }

        $isDraft = (bool) $this->request->getPost('save_as_draft');
        $data    = $this->collect($isDraft);

        // Editing a processed pass (admins only) never changes its workflow status.
        if (! in_array($current['status'], ['Draft', 'Pending', 'Rejected', null, ''], true)) {
            unset($data['status']);
        } elseif ($current['status'] === 'Rejected' && ! $isDraft) {
            $data['status']        = 'Pending'; // KPK: resubmitting a rejected pass sends it back for approval
            $data['reject_reason'] = null;
        }

        $data['location_access'] = $this->resolveLocationCsv($this->request->getPost('location_access'), (string) ($current['location_access'] ?? ''));
        if (! $isDraft && $data['location_access'] === '') {
            return redirect()->back()->withInput()->with('error', 'Please choose at least one Location Access. It decides which client(s) receive this pass for approval.');
        }

        if (! $isDraft && ($missing = $this->missingRequired($data))) {
            return redirect()->back()->withInput()->with('error', 'Please complete the following mandatory field(s): ' . implode(', ', $missing) . '.');
        }
        if (! $isDraft && $this->duplicateIcExists((string) $data['ic_passport'], (int) $id)) {
            return redirect()->back()->withInput()->with('error', "Another staff pass exists for IC / Passport '{$data['ic_passport']}'.");
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->handleUploads($data);
        $this->db()->table('staff')->where('id', (int) $id)->update($this->onlyColumns($data));
        $this->saveLicenses((int) $id, false);
        $this->syncApprovals((int) $id, (string) ($data['location_access'] ?? $current['location_access'] ?? ''), $isDraft);
        // A rejected pass that is edited and resubmitted starts a fresh round for every client.
        if (! $isDraft && ($current['status'] ?? '') === 'Rejected' && ($data['status'] ?? '') === 'Pending' && $this->approvalsReady()) {
            \App\Libraries\StaffClientApprovals::resetAll($this->db(), (int) $id);
        }
        $this->logAction($current, 'edit', $data['status'] ?? $current['status']);

        return redirect()->to(base_url('staffs'))
            ->with('success', $isDraft ? 'Staff pass request saved as draft.' : 'Staff record updated successfully.');
    }

    // ------------------------------------------------------------------
    //  Renew (KPK renew-port-pass-staff)
    // ------------------------------------------------------------------

    public function renew($id)
    {
        if (! $this->canEdit()) {
            return redirect()->to(base_url('staffs/closed-list'))->with('error', 'You are not allowed to renew staff passes.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return redirect()->to(base_url('staffs/closed-list'))->with('error', 'Staff record not found.');
        }
        if ($blocked = $this->renewBlock($staff)) {
            return redirect()->to(base_url('staffs/closed-list'))->with('error', $blocked);
        }

        return view('staffs/staffpassrequest', $this->formData([
            'pageTitle'  => 'Renew Staff Pass - SafeG',
            'staff'      => $staff,
            'licenses'   => $this->licenses((int) $id),
            'formAction' => 'staffs/renew/' . (int) $id,
            'isEdit'     => true,
            'isRenew'    => true,
        ]));
    }

    public function renewStore($id)
    {
        if (! $this->canEdit()) {
            return redirect()->to(base_url('staffs/closed-list'))->with('error', 'You are not allowed to renew staff passes.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return redirect()->to(base_url('staffs/closed-list'))->with('error', 'Staff record not found.');
        }
        if ($blocked = $this->renewBlock($staff)) {
            return redirect()->to(base_url('staffs/closed-list'))->with('error', $blocked);
        }

        $data = $this->collect(false);
        $data['location_access'] = $this->resolveLocationCsv($this->request->getPost('location_access'), (string) ($staff['location_access'] ?? ''));
        if ($data['location_access'] === '') {
            return redirect()->back()->withInput()->with('error', 'Please choose at least one Location Access.');
        }
        // KPK renewPortPassStaff keeps the identity fields as they are.
        unset($data['full_name'], $data['ic_passport'], $data['staff_no'], $data['resident'], $data['date_of_birth']);
        $data['type_of_application'] = 'RENEWAL';
        $data['date_of_application'] = date('d/m/Y');
        $data['status']              = 'Pending';
        $data['next_action']         = null;
        $data['reject_reason']       = null;
        $data['renewed_at']          = date('Y-m-d H:i:s');
        $data['updated_at']          = date('Y-m-d H:i:s');

        if ($missing = $this->missingRequired($data + $staff)) {
            return redirect()->back()->withInput()->with('error', 'Please complete the following mandatory field(s): ' . implode(', ', $missing) . '.');
        }

        $this->handleUploads($data);

        // Only the first renewal wins (KPK: "already been renewed by someone else").
        $db = $this->db();
        $db->table('staff')->where('id', (int) $id)->where('status', $staff['status'])->update($this->onlyColumns($data));
        if ($db->affectedRows() === 0) {
            return redirect()->to(base_url('staffs/closed-list'))->with('error', 'Fail to renew. This pass has already been renewed by someone else. Please refresh.');
        }
        $this->saveLicenses((int) $id, true);
        $this->syncApprovals((int) $id, (string) $data['location_access']);
        if ($this->approvalsReady()) {
            \App\Libraries\StaffClientApprovals::resetAll($db, (int) $id); // a renewal needs every client's approval again
        }
        $this->logAction($staff, 'renew', 'Pending');

        return redirect()->to(base_url('staffs'))->with('success', 'Staff pass renewal submitted — it is now Pending approval.');
    }

    private function renewBlock(array $staff): ?string
    {
        $closed = in_array($staff['card_status'] ?? '', ['Active', 'Terminated'], true);
        if (! $closed || ($staff['status'] ?? '') !== 'Approved') {
            return 'Only a closed staff pass (card already issued) can be renewed.';
        }
        return null;
    }

    private function editBlock(array $staff): ?string
    {
        helper('role');
        if (is_platform_superadmin() || is_client_superadmin()) {
            return null;
        }
        if (! in_array((string) ($staff['status'] ?? ''), ['', 'Draft', 'Pending', 'Rejected'], true)) {
            return 'This staff pass has already been processed and can no longer be edited here. Use Process List or Card Info instead.';
        }
        return null;
    }

    // ------------------------------------------------------------------
    //  Form plumbing
    // ------------------------------------------------------------------

    private function formData(array $extra): array
    {
        helper('feature');
        $countries = (new \App\Models\CountryModel())->where('status', 'Active')->orderBy('name', 'ASC')->findAll();

        return $extra + [
            'fieldSettings'   => $this->fieldSettings(),
            'countries'       => $countries,
            'locationGroups'  => $this->getLocationGroups(),
            'departments'     => $this->departmentOptions(),
            'mykadOcrEnabled' => client_feature_enabled('mykad_ocr'),
            'licenses'        => [],
        ];
    }

    private function fieldSettings(): array
    {
        helper('feature');
        $rows = (new \App\Models\ClientFormFieldModel())->getForCompanyForm(current_company_id(), self::FORM);
        $out  = [];
        foreach ($rows as $f) {
            $out[$f['field_key']] = (bool) $f['is_enabled'];
        }
        return $out;
    }

    private function departmentOptions(): array
    {
        try {
            $rows = $this->db()->table('departments')->select('name')->where('status', 'Active')->orderBy('name', 'ASC')->get()->getResultArray();
            $names = array_values(array_filter(array_map(fn($r) => trim((string) $r['name']), $rows)));
            if ($names) {
                return $names;
            }
        } catch (\Throwable $e) {
            // departments table not there — fall back to the built-in list
        }
        return self::FALLBACK_DEPARTMENTS;
    }

    private function collect(bool $isDraft): array
    {
        $r = fn(string $key) => ($v = $this->request->getPost($key)) === null ? null : (is_string($v) ? trim($v) : $v);
        $locations = (array) ($this->request->getPost('location_access') ?? []);
        $branch    = strtoupper((string) $r('access_branch'));

        return [
            'date_of_application'          => $r('date_of_application') ?: date('d/m/Y'),
            'type_of_application'          => $r('type_of_application') ?: 'NEW',
            'designation'                  => $r('designation'),
            'resident'                     => $r('resident'),
            'sub_type'                     => $r('sub_type'),
            'access_branch'                => in_array($branch, ['KSB', 'KPK', 'BOTH'], true) ? $branch : null,
            'location_access'              => $locations ? implode(',', array_map('strval', $locations)) : null,
            'ic_passport'                  => $r('ic_number'),
            'visa_expiry'                  => $r('visa_expiry') ?: null,
            'date_of_birth'                => $r('date_of_birth') ?: null,
            'sex'                          => $r('sex'),
            'full_name'                    => $r('full_name'),
            'name_on_staff_pass'           => $r('name_on_staff_pass'),
            'staff_no'                     => $r('staff_no'),
            'contact_number'               => $r('contact_number'),
            'email'                        => $r('email'),
            'department'                   => $r('department'),
            'address_1'                    => $r('address_1'),
            'address_2'                    => $r('address_2'),
            'address_3'                    => $r('address_3'),
            'country'                      => $r('country'),
            'state'                        => $r('state'),
            'city'                         => $r('city'),
            'postal_code'                  => $r('postal_code'),
            'csp_number'                   => $r('company_reg_id'),
            'csp_expiry_date'              => $r('csp_expiry_date') ?: null,
            'evetting_date_of_application' => $r('evetting_date_of_application') ?: null,
            'evetting_date_of_result'      => $r('evetting_date_of_result') ?: null,
            'evetting_result'              => $r('evetting_result'),
            'remark'                       => $r('remark'),
            'status'                       => $isDraft ? 'Draft' : 'Pending',
        ];
    }

    /** Mandatory on submit (KPK form validation); drafts skip this. */
    private function missingRequired(array $data): array
    {
        $fs     = $this->fieldSettings();
        $checks = [
            'full_name'   => ['Full Name', true],
            'ic_passport' => ['IC / Passport Number', $fs['ic_number'] ?? true],
            'resident'    => ['Resident', $fs['resident'] ?? true],
            'department'  => ['Department', $fs['department'] ?? true],
            'email'       => ['Email Address', $fs['email'] ?? true],
        ];
        $missing = [];
        foreach ($checks as $field => [$label, $enabled]) {
            if ($enabled && trim((string) ($data[$field] ?? '')) === '') {
                $missing[] = $label;
            }
        }
        return $missing;
    }

    private function nextAppNo(): string
    {
        $tag     = 'SP-' . date('Ymd');
        $db      = $this->db();
        $counter = (int) $db->table('staff')->like('app_no', $tag . '-', 'after')->countAllResults() + 1;
        while ($db->table('staff')->where('app_no', $tag . '-' . str_pad((string) $counter, 3, '0', STR_PAD_LEFT))->countAllResults() > 0) {
            $counter++;
        }
        return $tag . '-' . str_pad((string) $counter, 3, '0', STR_PAD_LEFT);
    }

    private function handleUploads(array &$data): void
    {
        $gov = $this->request->getFile('government_id');
        if ($gov && $gov->isValid() && ! $gov->hasMoved()) {
            $name = $gov->getRandomName();
            $gov->move(FCPATH . 'uploads/government_ids', $name);
            $data['government_id'] = $name;
        }

        $paths = [];
        foreach ((array) $this->request->getFileMultiple('invitation_letter') as $doc) {
            if ($doc && $doc->isValid() && ! $doc->hasMoved()) {
                $name = $doc->getRandomName();
                $doc->move(FCPATH . 'uploads/other_docs', $name);
                $paths[] = $name;
            }
        }
        if ($paths) {
            $data['other_doc'] = json_encode($paths);
        }

        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid() && ! $photo->hasMoved()) {
            if ($name = $this->storePhoto()) {
                $data['photo'] = $name;
            }
        }
    }

    /**
     * The form posts licenses[n][class] / licenses[n][expiry]. On renew KPK
     * replaces the licence list; on create/edit rows are only added.
     */
    private function saveLicenses(int $staffId, bool $replace): void
    {
        $db = $this->db();
        if (! $db->tableExists('staff_driving_licenses')) {
            return;
        }
        $rows = [];
        foreach ((array) ($this->request->getPost('licenses') ?? []) as $lic) {
            $class  = trim((string) ($lic['class'] ?? ''));
            $expiry = trim((string) ($lic['expiry'] ?? ''));
            if ($class === '' && $expiry === '') {
                continue;
            }
            $rows[] = [
                'staff_id'       => $staffId,
                'license_class'  => $class !== '' ? $class : null,
                'license_expiry' => $expiry !== '' ? $expiry : null,
                'created_at'     => date('Y-m-d H:i:s'),
            ];
        }
        if ($replace && $rows) {
            $db->table('staff_driving_licenses')->where('staff_id', $staffId)->delete();
        }
        if ($rows) {
            $db->table('staff_driving_licenses')->insertBatch($rows);
        }
    }
}
