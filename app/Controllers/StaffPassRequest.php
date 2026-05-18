<?php

namespace App\Controllers;

class StaffPassRequest extends BaseController
{
    public function index()
    {
        $userModel  = new \App\Models\UserModel();
        $user       = $userModel->find(session()->get('user_id'));
        $companyId  = (int) ($user['company_id'] ?? 0);

        $clientFormFieldModel = new \App\Models\ClientFormFieldModel();
        $rows = $clientFormFieldModel->getForCompanyForm($companyId, 'staff_pass_request');

        $fieldSettings = [];
        foreach ($rows as $f) {
            $fieldSettings[$f['field_key']] = (bool) $f['is_enabled'];
        }

        $countryModel = new \App\Models\CountryModel();
        $countries    = $countryModel->where('status', 'Active')->orderBy('name', 'ASC')->findAll();

        $data = [
            'pageTitle'     => 'Staff Pass Request - SafeG',
            'fieldSettings' => $fieldSettings,
            'countries'     => $countries,
        ];

        return view('staffs/staffpassrequest', $data);
    }

    public function store()
    {
        $db = \Config\Database::connect();

        $formData = [
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

        $db->table('staff')->insert($formData);

        return redirect()->to(base_url('staffs'))
            ->with('success', 'Staff pass request submitted successfully.');
    }

    public function view($id) {
        $staffModel = new \App\Models\StaffModel();
        $data['staff'] = $staffModel->find($id);
        return view('staffs/staffpassrequest_detail', $data);
    }

    public function edit($id)
    {
        $staffModel = new \App\Models\StaffModel();
        $staff = $staffModel->find($id);
        if (!$staff) {
            return redirect()->to(base_url('staffs'))->with('error', 'Staff record not found.');
        }

        $userModel  = new \App\Models\UserModel();
        $user       = $userModel->find(session()->get('user_id'));
        $companyId  = (int) ($user['company_id'] ?? 0);

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
            'staff'         => $staff,
            'formAction'    => 'staffpassrequest/update/' . (int) $id,
            'isEdit'        => true,
        ]);
    }

    public function update($id)
    {
        $db = \Config\Database::connect();

        $formData = [
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

        $db->table('staff')->where('id', (int) $id)->update($formData);

        return redirect()->to(base_url('staffs'))
            ->with('success', 'Staff record updated successfully.');
    }
}