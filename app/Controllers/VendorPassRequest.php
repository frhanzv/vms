<?php

namespace App\Controllers;

use App\Models\VendorLocationModel;

/**
 * Vendor Pass Request — rebuilt field-for-field against the real KPK
 * "Contractor/Vendor Request" form (new-port-pass-request), per instruction:
 * "follow all the things that they have inside here, dont leave a single
 * things." Worker Type (Permanent/Temporary — KPK's real physical card
 * type) is now chosen HERE at intake, not assigned later in Process List —
 * that's the other explicit instruction this rebuild follows.
 */
class VendorPassRequest extends BaseController
{
    /** Malaysian states — KPK's real form drives this from a location API we don't have; a fixed list covers the same field faithfully enough. */
    public const STATE_OPTIONS = [
        'Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang',
        'Perak', 'Perlis', 'Pulau Pinang', 'Sabah', 'Sarawak', 'Selangor',
        'Terengganu', 'W.P. Kuala Lumpur', 'W.P. Labuan', 'W.P. Putrajaya',
    ];

    public function index()
    {
        helper('feature');

        $countryModel = new \App\Models\CountryModel();
        $countries    = $countryModel->where('status', 'Active')->orderBy('name', 'ASC')->findAll();

        $data = [
            'pageTitle'       => 'Vendor Pass Request - SafeG',
            'countries'       => $countries,
            'fields'          => $this->vendorFieldToggles(),
            'required'        => $this->vendorFieldRequired(),
            'locationOptions' => (new VendorLocationModel())->getOptionsForUser(true),
            'stateOptions'    => self::STATE_OPTIONS,
            'lockedCompany'   => $this->vendorAccountCompany(),
            'designations'    => $this->designationOptions(),
        ];

        return view('vendors/vendorpassrequest', $data);
    }

    public function store()
    {
        helper('feature');
        $db = \Config\Database::connect();

        $appNo = trim($this->request->getPost('app_no') ?? '');
        if (empty($appNo)) {
            $batchTag = 'VP-' . date('Ymd');
            $counter  = 1;
            while ($db->table('vendors')->where('app_no', $batchTag . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT))->countAllResults() > 0) {
                $counter++;
            }
            $appNo = $batchTag . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
        }

        $isDraft  = (bool) $this->request->getPost('save_as_draft');
        $formData = $this->collectFormData($appNo, $isDraft);

        helper('vendor_company');
        if (is_vendor_admin() && ! current_vendor_company()) {
            return redirect()->back()->withInput()
                ->with('error', 'Your account is not linked to an active company. Please contact the administrator.');
        }

        if (! $isDraft) {
            $missing = $this->validateRequiredFields();
            if ($missing) {
                return redirect()->back()->withInput()
                    ->with('error', "Please complete the following mandatory field(s): " . implode(', ', $missing) . '.');
            }
        }

        // Same duplicate check pattern as StaffPassRequest::store() — IC/Passport must be unique.
        // Skipped for drafts, which are commonly saved with fields still missing.
        $icOrPassport = $formData['ic_no'] ?: $formData['passport_no'];
        $column       = $formData['ic_no'] ? 'ic_no' : 'passport_no';
        if (! $isDraft && $icOrPassport && $db->table('vendors')->where($column, $icOrPassport)->countAllResults() > 0) {
            return redirect()->back()->withInput()
                ->with('error', "A vendor pass record with IC/Passport '{$icOrPassport}' already exists.");
        }

        // Location Access is mandatory: it decides which client(s) see and approve the pass.
        if (! $isDraft && trim((string) $formData['location_access']) === '') {
            return redirect()->back()->withInput()
                ->with('error', 'Please choose at least one Location Access. It decides which client(s) receive this pass for approval.');
        }

        $formData['company_id'] = current_company_id();
        $formData['created_at'] = date('Y-m-d H:i:s');

        $this->handleUploads($formData);

        $db->table('vendors')->insert($formData);
        $vendorId = $db->insertID();

        $this->saveDrivingLicenses($vendorId);
        \App\Libraries\VendorClientApprovals::sync($db, (int) $vendorId, $isDraft ? '' : (string) $formData['location_access']);

        return redirect()->to(base_url('vendors'))
            ->with('success', $isDraft ? 'Vendor pass request saved as draft.' : 'Vendor pass request submitted successfully.');
    }

    public function view($id)
    {
        helper('privacy');
        $db     = \Config\Database::connect();
        $vendor = $this->loadScopedVendor((int) $id);

        if (!$vendor) {
            return redirect()->to(base_url('vendors'))->with('error', 'Vendor pass record not found.');
        }

        $licenses = $db->table('vendor_driving_licenses')->where('vendor_id', (int) $id)->orderBy('id', 'DESC')->get()->getResultArray();

        return view('vendors/vendorpassrequest_detail', [
            'vendor'          => $vendor,
            'fields'          => $this->vendorFieldToggles(),
            'required'        => $this->vendorFieldRequired(),
            'licenses'        => $licenses,
            'locationOptions' => (new VendorLocationModel())->getOptionsForUser(true),
        ]);
    }

    public function edit($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')) {
            return redirect()->to(base_url('vendors'))->with('error', 'You are not allowed to edit vendor pass records.');
        }

        $db     = \Config\Database::connect();
        $vendor = $this->loadScopedVendor((int) $id);

        if (!$vendor) {
            return redirect()->to(base_url('vendors'))->with('error', 'Vendor pass record not found.');
        }

        if ($blocked = $this->vendorAccountEditBlock($vendor)) {
            return redirect()->to(base_url('vendors'))->with('error', $blocked);
        }

        $countryModel = new \App\Models\CountryModel();
        $countries    = $countryModel->where('status', 'Active')->orderBy('name', 'ASC')->findAll();

        $licenses = $db->table('vendor_driving_licenses')->where('vendor_id', (int) $id)->orderBy('id', 'DESC')->get()->getResultArray();

        return view('vendors/vendorpassrequest', [
            'pageTitle'       => 'Edit Vendor Pass - SafeG',
            'countries'       => $countries,
            'vendor'          => $vendor,
            'formAction'      => 'vendors/vendorpassrequest/update/' . (int) $id,
            'isEdit'          => true,
            'fields'          => $this->vendorFieldToggles(),
            'required'        => $this->vendorFieldRequired(),
            'licenses'        => $licenses,
            'locationOptions' => (new VendorLocationModel())->getOptionsForUser(true),
            'stateOptions'    => self::STATE_OPTIONS,
            'lockedCompany'   => $this->vendorAccountCompany(),
            'designations'    => $this->designationOptions(),
        ]);
    }

    public function update($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')) {
            return $this->response->setStatusCode(403, 'You are not allowed to edit vendor pass records.');
        }

        $db = \Config\Database::connect();

        $current = $this->loadScopedVendor((int) $id);
        if (! $current) {
            return $this->response->setStatusCode(404, 'Vendor pass record not found.');
        }
        if ($blocked = $this->vendorAccountEditBlock($current)) {
            return redirect()->to(base_url('vendors'))->with('error', $blocked);
        }

        $appNo = trim($this->request->getPost('app_no') ?? '');
        if (empty($appNo)) {
            $appNo = (string) ($current['app_no'] ?? '');
        }

        $isDraft  = (bool) $this->request->getPost('save_as_draft');
        $formData = $this->collectFormData($appNo, $isDraft);

        if (! $isDraft) {
            $missing = $this->validateRequiredFields();
            if ($missing) {
                return redirect()->back()->withInput()
                    ->with('error', "Please complete the following mandatory field(s): " . implode(', ', $missing) . '.');
            }
        }

        // Keep locations of other clients that this user cannot see in the picker
        // (so editing a pass never silently drops another client's location).
        $visible = array_keys((new VendorLocationModel())->getOptionsForUser(true));
        $kept    = array_values(array_filter(array_map('trim', explode(',', (string) ($current['location_access'] ?? ''))), static fn($c) => $c !== '' && ! in_array($c, $visible, true)));
        if ($kept) {
            $formData['location_access'] = implode(',', array_unique(array_merge(array_filter(explode(',', (string) $formData['location_access'])), $kept)));
        }
        if (! $isDraft && trim((string) $formData['location_access']) === '') {
            return redirect()->back()->withInput()
                ->with('error', 'Please choose at least one Location Access. It decides which client(s) receive this pass for approval.');
        }

        $icOrPassport = $formData['ic_no'] ?: $formData['passport_no'];
        $column       = $formData['ic_no'] ? 'ic_no' : 'passport_no';
        if (! $isDraft && $icOrPassport && $db->table('vendors')->where($column, $icOrPassport)->where('id !=', (int) $id)->countAllResults() > 0) {
            return redirect()->back()->withInput()
                ->with('error', "A vendor pass record with IC/Passport '{$icOrPassport}' already exists.");
        }

        $this->handleUploads($formData);

        $db->table('vendors')->where('id', (int) $id)->update($formData);

        // Editing only ever ADDS new license rows here — matches Card Info's
        // "Add License" (also add-only); removing a license goes through
        // that page once it's needed.
        $this->saveDrivingLicenses((int) $id);

        \App\Libraries\VendorClientApprovals::sync($db, (int) $id, $isDraft ? '' : (string) $formData['location_access']);
        // A rejected pass that is edited and resubmitted starts a fresh round for every client.
        if (! $isDraft && ($current['status'] ?? '') === 'Rejected' && ($formData['status'] ?? '') === 'Pending') {
            \App\Libraries\VendorClientApprovals::resetAll($db, (int) $id);
        }

        return redirect()->to(base_url('vendors'))
            ->with('success', $isDraft ? 'Vendor pass request saved as draft.' : 'Vendor pass record updated successfully.');
    }

    /**
     * Pulls every posted field into one array, matching the vendors table columns.
     * Shared by store() and update() so both stay in sync.
     */
    private function collectFormData(string $appNo, bool $isDraft = false): array
    {
        $r = fn(string $key) => $this->request->getPost($key);

        $locations = (array) ($this->request->getPost('location_access') ?? []);
        $locations = array_values(array_intersect($locations, array_keys((new VendorLocationModel())->getOptionsForUser(true))));

        return $this->lockForVendorAccount([
            'app_no'                        => $appNo,

            // Application Info
            'date_of_application'           => $r('date_of_application'),
            'type_of_application'           => $r('type_of_application'),
            'sub_type'                      => $r('sub_type'),
            'type_of_registration'          => $r('type_of_registration'),
            'payment'                       => $r('payment'),
            'resident'                      => $r('resident'),
            'card_type'                     => $r('card_type') ?: null, // "Worker Type" on the form — Permanent/Temporary
            'location_access'               => implode(',', $locations),

            // Vendor's company (SSM)
            'vendor_company_reg_id'         => $r('vendor_company_reg_id'),
            'vendor_company_name'           => $r('vendor_company_name'),

            // Personal Details
            'in_out_bound'                  => $r('in_out_bound'),
            'full_name'                     => $r('full_name'),
            'name_on_vendor_pass'           => $r('name_on_vendor_pass'),
            'ic_no'                         => $r('ic_no'),
            'passport_no'                   => $r('passport_no'),
            'dob'                           => $r('dob') ?: null,
            'sex'                           => $r('sex'),
            'contact_no'                    => $r('contact_no'),
            'email'                         => $r('email'),
            'staff_no'                      => $r('staff_no'),
            'designation'                   => $r('designation'),

            // Address
            'address_1'                     => $r('address_1'),
            'address_2'                     => $r('address_2'),
            'address_3'                     => $r('address_3'),
            'country'                       => $r('country') ?: 'Malaysia',
            'state'                         => $r('state'),
            'city'                          => $r('city'),
            'postcode'                      => $r('postcode'),
            'vehicle_registration'          => $r('vehicle_registration'),

            // Visit Details
            'name_of_person_visited'        => $r('name_of_person_visited'),
            'contact_no_of_person_visited'  => $r('contact_no_of_person_visited'),
            'location_visited'              => $r('location_visited'),

            // CSP
            'csp_number'                    => $r('csp_number'),
            'csp_expiry_date'               => $r('csp_expiry_date') ?: null,

            // E-Vetting
            'evetting_date_of_application'  => $r('evetting_date_of_application') ?: null,
            'evetting_date_of_result'       => $r('evetting_date_of_result') ?: null,
            'evetting_result'               => $r('evetting_result'),

            // Pass
            'pass_expiry'                   => $r('pass_expiry') ?: null,
            'status'                        => $isDraft ? 'Draft' : ($r('status') ?: 'Pending'),
            'remark'                        => $r('remark'),
        ], $isDraft);
    }

    // ---------------------------------------------------------------------
    // Vendor company accounts (role vendor_admin)
    // ---------------------------------------------------------------------

    /** The logged-in vendor account's own company, or null for everyone else. */
    private function vendorAccountCompany(): ?array
    {
        helper('vendor_company');
        if (is_vendor_admin()) {
            return current_vendor_company();
        }

        // Admin / superadmin: their own company record, shown read-only like KPK.
        $companyId = (int) ((new \App\Models\UserModel())->find((int) session()->get('user_id'))['company_id'] ?? 0);
        return $companyId > 0 ? ((new \App\Models\CompanyModel())->find($companyId) ?: null) : null;
    }

    /** Active designation names for the Designation drop-down (Config > Designation). */
    private function designationOptions(): array
    {
        try {
            $rows = \Config\Database::connect()->table('designations')
                ->select('name')->whereIn('status', ['active', 'Active'])->orderBy('name', 'ASC')->get()->getResultArray();
            return array_column($rows, 'name');
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Loads one vendors row the current user is allowed to see: same client
     * (unless platform superadmin) and, for a vendor company account, only
     * its own company's records.
     */
    private function loadScopedVendor(int $id): ?array
    {
        helper(['feature', 'vendor_company']);

        $builder = \Config\Database::connect()->table('vendors')->where('id', $id);
        helper('vendor_client_scope');
        vendor_client_scope($builder);
        apply_vendor_company_scope($builder);

        return $builder->get()->getRowArray() ?: null;
    }

    /**
     * A vendor company can only change a request while it is still theirs to
     * change; once KPK staff have approved it / started processing it, it is
     * locked. Returns an error message, or null when editing is fine.
     */
    private function vendorAccountEditBlock(array $vendor): ?string
    {
        helper('vendor_company');
        if (! is_vendor_admin()) {
            return null;
        }

        if (! in_array((string) ($vendor['status'] ?? ''), ['Draft', 'Pending', 'Rejected'], true)) {
            return 'This request can no longer be edited because it has already been processed.';
        }

        return null;
    }

    /**
     * For a vendor company account: stamp its registered company on the
     * record (ignoring whatever was posted), force the status to Draft or
     * Pending (they can never approve their own request) and drop the
     * KPK-side e-vetting fields. Everyone else's data passes through.
     */
    private function lockForVendorAccount(array $formData, bool $isDraft): array
    {
        helper('vendor_company');

        // Visit Details and Pass (expiry / remark) are filled in on the Process List,
        // not on the request form, so a request never overwrites what was set there.
        foreach (['name_of_person_visited', 'contact_no_of_person_visited', 'location_visited', 'pass_expiry', 'remark'] as $key) {
            if ($this->request->getPost($key) === null) {
                unset($formData[$key]);
            }
        }

        if (! is_vendor_admin()) {
            // Admin accounts: the company on the form is the account's own company.
            if ($own = $this->vendorAccountCompany()) {
                $formData['vendor_company_reg_id'] = $own['registration_no'];
                $formData['vendor_company_name']   = $own['name'];
            }
            return $formData;
        }

        $company = current_vendor_company();
        if ($company) {
            $formData['vendor_company_reg_id'] = $company['registration_no'];
            $formData['vendor_company_name']   = $company['name'];
        }

        $formData['status'] = $isDraft ? 'Draft' : 'Pending';

        foreach (['evetting_date_of_application', 'evetting_date_of_result', 'evetting_result'] as $key) {
            unset($formData[$key]);
        }

        return $formData;
    }

    /**
     * Driving License rows, posted as parallel arrays (license_class[],
     * license_expiry[]) from the repeatable +/- section on the form —
     * inserted into the same vendor_driving_licenses table the Card Info
     * page's "Add License" uses, so both stay consistent.
     */
    private function saveDrivingLicenses(int $vendorId): void
    {
        $classes  = (array) ($this->request->getPost('license_class') ?? []);
        $expiries = (array) ($this->request->getPost('license_expiry') ?? []);
        if (empty($classes)) {
            return;
        }

        $db   = \Config\Database::connect();
        $rows = [];
        foreach ($classes as $i => $class) {
            $class  = trim((string) $class);
            $expiry = trim((string) ($expiries[$i] ?? ''));
            if ($class === '' && $expiry === '') {
                continue;
            }
            $rows[] = [
                'vendor_id'      => $vendorId,
                'license_class'  => $class !== '' ? $class : null,
                'license_expiry' => $expiry !== '' ? $expiry : null,
                'created_at'     => date('Y-m-d H:i:s'),
            ];
        }
        if (! empty($rows)) {
            $db->table('vendor_driving_licenses')->insertBatch($rows);
        }
    }

    /**
     * Same upload pattern as StaffPassRequest — moves files into writable/uploads
     * and stores the generated filename on $formData by reference.
     */
    private function handleUploads(array &$formData): void
    {
        // "Passport Photo" in KPK's Upload section.
        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $photo->move('uploads/vendor_photos', $newName);
            $formData['photo'] = $newName;
        }

        // Shared by both the "Upload IC" quick button (Person section) and
        // the "Photostat IC / Passport" slot in KPK's Upload section — same
        // underlying field either way, so whichever one the person used is
        // the one that gets saved.
        $governmentId = $this->request->getFile('government_id');
        if ($governmentId && $governmentId->isValid() && !$governmentId->hasMoved()) {
            $newName = $governmentId->getRandomName();
            $governmentId->move('uploads/government_ids', $newName);
            $formData['government_id'] = $newName;
        }

        $otherDocs     = $this->request->getFileMultiple('other_doc');
        $otherDocPaths = [];
        if ($otherDocs) {
            foreach ($otherDocs as $doc) {
                if ($doc->isValid() && !$doc->hasMoved()) {
                    $newName = $doc->getRandomName();
                    $doc->move('uploads/other_docs', $newName);
                    $otherDocPaths[] = $newName;
                }
            }
        }
        if (!empty($otherDocPaths)) {
            $formData['other_doc'] = json_encode($otherDocPaths);
        }

        foreach (['mysejahtera_cert' => 'uploads/mysejahtera', 'mysejahtera_cert_2' => 'uploads/mysejahtera', 'facial_photo' => 'uploads/facial_photos'] as $field => $dir) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($dir, $newName);
                $formData[$field] = $newName;
            }
        }
    }

    /**
     * field_key => is_enabled map for the current company, from the
     * 'vendor_pass_request' form type registered in ClientFormFieldModel.
     * Absence of a saved row means enabled — same default as everywhere else.
     */
    private function vendorFieldToggles(): array
    {
        helper('feature');
        $model  = new \App\Models\ClientFormFieldModel();
        $rows   = $model->getForCompanyForm(current_company_id(), 'vendor_pass_request');

        $toggles = [];
        foreach ($rows as $row) {
            $toggles[$row['field_key']] = (bool) $row['is_enabled'];
        }
        return $toggles;
    }

    /**
     * field_key => is_required map, for fields marked 'requirable' in
     * ClientFormFieldModel::vendorPassFields(). Passed to the view so the
     * form can render the red "*" and the HTML `required` attribute
     * dynamically, per Config > Dynamic Form Fields > Vendor Pass Request.
     */
    private function vendorFieldRequired(): array
    {
        $model = new \App\Models\ClientFormFieldModel();
        $rows  = $model->getForCompanyForm(current_company_id(), 'vendor_pass_request');

        $required = [];
        foreach ($rows as $row) {
            if (! empty($row['requirable'])) {
                $required[$row['field_key']] = (bool) $row['is_required'];
            }
        }
        $required['location_access'] = true; // always mandatory — it decides which client approves
        return $required;
    }

    /**
     * Maps each requirable field_key to the POST field name(s) that satisfy
     * it. A key is "filled" when at least one of its mapped POST names is
     * non-empty (location_access[] is checked as a non-empty array).
     * Section-type keys (e.g. 'address') are satisfied if any one of their
     * sub-fields is filled — mirrors how the rest of this form treats a
     * section as one configurable unit.
     */
    private function requiredFieldPostKeys(): array
    {
        return [
            'type_of_application'  => ['type_of_application'],
            'type_of_registration' => ['type_of_registration'],
            'payment'              => ['payment'],
            'sub_type'             => ['sub_type'],
            'resident'             => ['resident'],
            'card_type'            => ['card_type'],
            'location_access'      => ['location_access'],
            'in_out_bound'         => ['in_out_bound'],
            'vendor_company'       => ['vendor_company_reg_id', 'vendor_company_name'],
            'staff_no'             => ['staff_no'],
            'full_name'            => ['full_name'],
            'name_on_vendor_pass'  => ['name_on_vendor_pass'],
            'ic_passport'          => ['ic_no', 'passport_no'],
            'date_of_birth'        => ['date_of_birth'],
            'sex'                  => ['sex'],
            'designation'          => ['designation'],
            'contact_number'       => ['contact_number'],
            'email'                => ['email'],
            'vehicle_registration' => ['vehicle_registration'],
            'address'              => ['address_line1', 'address_line2', 'address_line3', 'country', 'state', 'city', 'postal_code'],
            'csp_number'           => ['csp_number'],
            'pass_expiry'          => ['pass_expiry'],
            'remark'               => ['remark'],
        ];
    }

    /**
     * Checks every field the current company has marked mandatory (Config >
     * Dynamic Form Fields > Vendor Pass Request > "Mandatory") and returns
     * the human labels of any that were left empty. A field that is
     * currently disabled is skipped — a hidden field can't be mandatory.
     */
    private function validateRequiredFields(): array
    {
        $required = $this->vendorFieldRequired();
        if (empty($required)) {
            return [];
        }

        $toggles   = $this->vendorFieldToggles();
        $postKeys  = $this->requiredFieldPostKeys();
        $labels    = array_column(\App\Models\ClientFormFieldModel::vendorPassFields(), 'label', 'field_key');
        $missing   = [];

        foreach ($required as $key => $isRequired) {
            if (! $isRequired) {
                continue;
            }
            if (isset($toggles[$key]) && ! $toggles[$key]) {
                continue; // disabled fields can't be mandatory
            }
            if (in_array($key, ['pass_expiry', 'remark'], true)) {
                continue; // filled in on the Process List
            }

            $names  = $postKeys[$key] ?? [];
            $filled = false;
            foreach ($names as $name) {
                $value = $this->request->getPost($name);
                if (is_array($value) ? ! empty($value) : trim((string) $value) !== '') {
                    $filled = true;
                    break;
                }
            }

            if (! $filled && $names) {
                $missing[] = $labels[$key] ?? $key;
            }
        }

        return $missing;
    }
}
