<?php

namespace App\Controllers;

class StaffPassRequest extends BaseController
{
    public function index()
    {
        helper('feature');
        $companyId  = current_company_id();

        $clientFormFieldModel = new \App\Models\ClientFormFieldModel();
        $rows = $clientFormFieldModel->getForCompanyForm($companyId, 'staff_pass_request');

        $fieldSettings = [];
        foreach ($rows as $f) {
            $fieldSettings[$f['field_key']] = (bool) $f['is_enabled'];
        }

        $countryModel = new \App\Models\CountryModel();
        $countries    = $countryModel->where('status', 'Active')->orderBy('name', 'ASC')->findAll();
        $locationGroups = $this->getLocationGroups();

        $data = [
            'pageTitle'     => 'Staff Pass Request - SafeG',
            'fieldSettings' => $fieldSettings,
            'countries'     => $countries,
            'locationGroups' => $locationGroups,
            'mykadOcrEnabled' => client_feature_enabled('mykad_ocr'),
        ];

        return view('staffs/staffpassrequest', $data);
    }

    public function store()
    {
        $db = \Config\Database::connect();

        $appNo = trim($this->request->getPost('app_no') ?? '');
        if (empty($appNo)) {
            $batchTag = 'APP-' . date('Ymd');
            $counter = 1;
            while ($db->table('staff')->where('app_no', $batchTag . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT))->countAllResults() > 0) {
                $counter++;
            }
            $appNo = $batchTag . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
        }

        $formData = [
            'app_no'                        => $appNo,
            // Application Info
            'date_of_application'           => $this->request->getPost('date_of_application'),
            'type_of_application'           => $this->request->getPost('type_of_application'),
            'designation'                   => $this->request->getPost('designation'),
            'resident'                      => $this->request->getPost('resident'),
            'sub_type'                      => $this->request->getPost('sub_type'),
            'location_access'               => $this->request->getPost('location_access')
                                                ? implode(',', $this->request->getPost('location_access'))
                                                : null,

            // Staff Details
            'ic_passport'                   => $this->request->getPost('ic_number'),
            'date_of_birth'                 => $this->request->getPost('date_of_birth'),
            'sex'                           => $this->request->getPost('sex'),
            'full_name'                     => $this->request->getPost('full_name'),
            'name_on_staff_pass'            => $this->request->getPost('name_on_staff_pass'),
            'staff_no'                      => $this->request->getPost('staff_no'),
            'contact_number'                => $this->request->getPost('contact_number'),
            'email'                         => $this->request->getPost('email'),
            'department'                    => $this->request->getPost('department'),
            'address_1'                     => $this->request->getPost('address_1'),
            'address_2'                     => $this->request->getPost('address_2'),
            'address_3'                     => $this->request->getPost('address_3'),
            'country'                       => $this->request->getPost('country'),
            'state'                         => $this->request->getPost('state'),
            'city'                          => $this->request->getPost('city'),
            'postal_code'                   => $this->request->getPost('postal_code'),

            // CSP
            'csp_number'                    => $this->request->getPost('company_reg_id'),
            'csp_expiry_date'               => $this->request->getPost('csp_expiry_date') ?: null,

            // E-Vetting
            'evetting_date_of_application'  => $this->request->getPost('evetting_date_of_application') ?: null,
            'evetting_date_of_result'       => $this->request->getPost('evetting_date_of_result') ?: null,
            'evetting_result'               => $this->request->getPost('evetting_result'),

            'created_at'                    => date('Y-m-d H:i:s'),
        ];

        // Government ID upload
        $governmentId = $this->request->getFile('government_id');
        if ($governmentId && $governmentId->isValid() && !$governmentId->hasMoved()) {
            $newName = $governmentId->getRandomName();
            $governmentId->move('uploads/government_ids', $newName);
            $formData['government_id'] = $newName;
        }

        // Other document upload (multiple)
        $otherDocs = $this->request->getFileMultiple('invitation_letter');
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

        $icPassport = $formData['ic_passport'] ?? null;
        if ($icPassport && $db->table('staff')->where('ic_passport', $icPassport)->countAllResults() > 0) {
            return redirect()->back()->withInput()
                ->with('error', "A staff record with IC/Passport '{$icPassport}' already exists.");
        }

        $db->table('staff')->insert($formData);

        return redirect()->to(base_url('staffs'))
            ->with('success', 'Staff pass request submitted successfully.');
    }

    public function view($id) {
        $staffModel = new \App\Models\StaffModel();
        $staff = $staffModel->find($id);
        if (!$staff) {
            return redirect()->to(base_url('staffs'))->with('error', 'Staff record not found.');
        }

        helper('feature');
        $clientFormFieldModel = new \App\Models\ClientFormFieldModel();
        $rows = $clientFormFieldModel->getForCompanyForm(current_company_id(), 'staff_pass_request');

        $fieldSettings = [];
        foreach ($rows as $field) {
            $fieldSettings[$field['field_key']] = (bool) $field['is_enabled'];
        }

        return view('staffs/staffpassrequest_detail', [
            'staff'          => $staff,
            'fieldSettings'  => $fieldSettings,
            'locationGroups' => $this->getLocationGroups(),
        ]);
    }

    public function edit($id)
    {
        $staffModel = new \App\Models\StaffModel();
        $staff = $staffModel->find($id);
        if (!$staff) {
            return redirect()->to(base_url('staffs'))->with('error', 'Staff record not found.');
        }

        helper('feature');
        $companyId  = current_company_id();

        $clientFormFieldModel = new \App\Models\ClientFormFieldModel();
        $rows = $clientFormFieldModel->getForCompanyForm($companyId, 'staff_pass_request');

        $fieldSettings = [];
        foreach ($rows as $f) {
            $fieldSettings[$f['field_key']] = (bool) $f['is_enabled'];
        }

        $countryModel = new \App\Models\CountryModel();
        $countries    = $countryModel->where('status', 'Active')->orderBy('name', 'ASC')->findAll();

        return view('staffs/staffpassrequest', [
            'pageTitle'     => 'Edit Staff - SafeG',
            'fieldSettings' => $fieldSettings,
            'countries'     => $countries,
            'locationGroups' => $this->getLocationGroups(),
            'mykadOcrEnabled' => client_feature_enabled('mykad_ocr'),
            'staff'         => $staff,
            'formAction'    => 'staffpassrequest/update/' . (int) $id,
            'isEdit'        => true,
        ]);
    }

    /**
     * Build the IN/OUT selector directly from active Location Access Management rows.
     */
    private function getLocationGroups(): array
    {
        $locations = (new \App\Models\LocationModel())->getAllActive();
        $groups = [];

        foreach ($locations as $location) {
            $access = trim((string) ($location['location_access'] ?? ''));
            if ($access === '') {
                continue;
            }

            $branch = trim((string) ($location['branch'] ?? '')) ?: 'Locations';
            $direction = null;
            $label = $access;
            if (preg_match('/\s+(IN|OUT)$/i', $access, $matches)) {
                $direction = strtolower($matches[1]);
                $label = trim(substr($access, 0, -strlen($matches[0])));
            }

            $key = strtolower($label);
            if (!isset($groups[$branch][$key])) {
                $groups[$branch][$key] = ['label' => $label, 'in' => null, 'out' => null];
            }

            if ($direction !== null) {
                $groups[$branch][$key][$direction] = $access;
            } else {
                // A management row without an IN/OUT suffix remains selectable.
                $groups[$branch][$key]['in'] = $access;
            }
        }

        foreach ($groups as $branch => $entries) {
            $groups[$branch] = array_values($entries);
        }

        return $groups;
    }

    public function update($id)
    {
        $db = \Config\Database::connect();

        $appNo = trim($this->request->getPost('app_no') ?? '');
        if (empty($appNo)) {
            $existing = $db->table('staff')->where('id', (int) $id)->select('app_no')->get()->getRow();
            $appNo = $existing?->app_no ?? '';
        }

        $formData = [
            'app_no'                        => $appNo,
            'date_of_application'           => $this->request->getPost('date_of_application'),
            'type_of_application'           => $this->request->getPost('type_of_application'),
            'designation'                   => $this->request->getPost('designation'),
            'resident'                      => $this->request->getPost('resident'),
            'sub_type'                      => $this->request->getPost('sub_type'),
            'location_access'               => $this->request->getPost('location_access')
                                                ? implode(',', $this->request->getPost('location_access'))
                                                : null,
            'ic_passport'                   => $this->request->getPost('ic_number'),
            'date_of_birth'                 => $this->request->getPost('date_of_birth'),
            'sex'                           => $this->request->getPost('sex'),
            'full_name'                     => $this->request->getPost('full_name'),
            'name_on_staff_pass'            => $this->request->getPost('name_on_staff_pass'),
            'staff_no'                      => $this->request->getPost('staff_no'),
            'contact_number'                => $this->request->getPost('contact_number'),
            'email'                         => $this->request->getPost('email'),
            'department'                    => $this->request->getPost('department'),
            'address_1'                     => $this->request->getPost('address_1'),
            'address_2'                     => $this->request->getPost('address_2'),
            'address_3'                     => $this->request->getPost('address_3'),
            'country'                       => $this->request->getPost('country'),
            'state'                         => $this->request->getPost('state'),
            'city'                          => $this->request->getPost('city'),
            'postal_code'                   => $this->request->getPost('postal_code'),
            'csp_number'                    => $this->request->getPost('company_reg_id'),
            'csp_expiry_date'               => $this->request->getPost('csp_expiry_date') ?: null,
            'evetting_date_of_application'  => $this->request->getPost('evetting_date_of_application') ?: null,
            'evetting_date_of_result'       => $this->request->getPost('evetting_date_of_result') ?: null,
            'evetting_result'               => $this->request->getPost('evetting_result'),
        ];

        $governmentId = $this->request->getFile('government_id');
        if ($governmentId && $governmentId->isValid() && !$governmentId->hasMoved()) {
            $newName = $governmentId->getRandomName();
            $governmentId->move('uploads/government_ids', $newName);
            $formData['government_id'] = $newName;
        }

        $otherDocs = $this->request->getFileMultiple('invitation_letter');
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

        $icPassport = $formData['ic_passport'] ?? null;
        if ($icPassport && $db->table('staff')->where('ic_passport', $icPassport)->where('id !=', (int) $id)->countAllResults() > 0) {
            return redirect()->back()->withInput()
                ->with('error', "A staff record with IC/Passport '{$icPassport}' already exists.");
        }

        $db->table('staff')->where('id', (int) $id)->update($formData);

        return redirect()->to(base_url('staffs'))
            ->with('success', 'Staff record updated successfully.');
    }
}
