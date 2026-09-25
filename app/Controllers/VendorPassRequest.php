<?php

namespace App\Controllers;

class VendorPassRequest extends BaseController
{
    public function index()
    {
        helper('feature');

        $countryModel = new \App\Models\CountryModel();
        $countries    = $countryModel->where('status', 'Active')->orderBy('name', 'ASC')->findAll();

        $data = [
            'pageTitle' => 'Vendor Pass Request - SafeG',
            'countries' => $countries,
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

        $formData = $this->collectFormData($appNo);

        // Same duplicate check pattern as StaffPassRequest::store() — IC/Passport must be unique.
        $icOrPassport = $formData['ic_no'] ?: $formData['passport_no'];
        $column       = $formData['ic_no'] ? 'ic_no' : 'passport_no';
        if ($icOrPassport && $db->table('vendors')->where($column, $icOrPassport)->countAllResults() > 0) {
            return redirect()->back()->withInput()
                ->with('error', "A vendor pass record with IC/Passport '{$icOrPassport}' already exists.");
        }

        $formData['company_id'] = current_company_id();
        $formData['created_at'] = date('Y-m-d H:i:s');

        $this->handleUploads($formData);

        $db->table('vendors')->insert($formData);

        return redirect()->to(base_url('vendors'))
            ->with('success', 'Vendor pass request submitted successfully.');
    }

    public function view($id)
    {
        helper('privacy');
        $db     = \Config\Database::connect();
        $vendor = $db->table('vendors')->where('id', (int) $id)->get()->getRowArray();

        if (!$vendor) {
            return redirect()->to(base_url('vendors'))->with('error', 'Vendor pass record not found.');
        }

        return view('vendors/vendorpassrequest_detail', ['vendor' => $vendor]);
    }

    public function edit($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')) {
            return redirect()->to(base_url('vendors'))->with('error', 'You are not allowed to edit vendor pass records.');
        }

        $db     = \Config\Database::connect();
        $vendor = $db->table('vendors')->where('id', (int) $id)->get()->getRowArray();

        if (!$vendor) {
            return redirect()->to(base_url('vendors'))->with('error', 'Vendor pass record not found.');
        }

        $countryModel = new \App\Models\CountryModel();
        $countries    = $countryModel->where('status', 'Active')->orderBy('name', 'ASC')->findAll();

        return view('vendors/vendorpassrequest', [
            'pageTitle'  => 'Edit Vendor Pass - SafeG',
            'countries'  => $countries,
            'vendor'     => $vendor,
            'formAction' => 'vendors/vendorpassrequest/update/' . (int) $id,
            'isEdit'     => true,
        ]);
    }

    public function update($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')) {
            return $this->response->setStatusCode(403, 'You are not allowed to edit vendor pass records.');
        }

        $db = \Config\Database::connect();

        $appNo = trim($this->request->getPost('app_no') ?? '');
        if (empty($appNo)) {
            $existing = $db->table('vendors')->where('id', (int) $id)->select('app_no')->get()->getRow();
            $appNo    = $existing?->app_no ?? '';
        }

        $formData = $this->collectFormData($appNo);

        $icOrPassport = $formData['ic_no'] ?: $formData['passport_no'];
        $column       = $formData['ic_no'] ? 'ic_no' : 'passport_no';
        if ($icOrPassport && $db->table('vendors')->where($column, $icOrPassport)->where('id !=', (int) $id)->countAllResults() > 0) {
            return redirect()->back()->withInput()
                ->with('error', "A vendor pass record with IC/Passport '{$icOrPassport}' already exists.");
        }

        $this->handleUploads($formData);

        $db->table('vendors')->where('id', (int) $id)->update($formData);

        return redirect()->to(base_url('vendors'))
            ->with('success', 'Vendor pass record updated successfully.');
    }

    /**
     * Pulls every posted field into one array, matching the vendors table columns.
     * Shared by store() and update() so both stay in sync.
     */
    private function collectFormData(string $appNo): array
    {
        $r = fn(string $key) => $this->request->getPost($key);

        return [
            'app_no'                        => $appNo,

            // Application Info
            'date_of_application'           => $r('date_of_application'),
            'type_of_application'           => $r('type_of_application'),
            'sub_type'                      => $r('sub_type'),

            // Vendor's company (SSM)
            'vendor_company_reg_id'         => $r('vendor_company_reg_id'),
            'vendor_company_name'           => $r('vendor_company_name'),

            // Personal Details
            'full_name'                     => $r('full_name'),
            'ic_no'                         => $r('ic_no'),
            'passport_no'                   => $r('passport_no'),
            'dob'                           => $r('dob') ?: null,
            'sex'                           => $r('sex'),
            'resident'                      => $r('resident'),
            'contact_no'                    => $r('contact_no'),
            'email'                         => $r('email'),
            'staff_no'                      => $r('staff_no'),
            'designation'                   => $r('designation'),

            // Address
            'address_1'                     => $r('address_1'),
            'address_2'                     => $r('address_2'),
            'address_3'                     => $r('address_3'),
            'postcode'                      => $r('postcode'),

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
            'status'                        => $r('status') ?: 'Pending',
            'remark'                        => $r('remark'),
        ];
    }

    /**
     * Same upload pattern as StaffPassRequest — moves files into writable/uploads
     * and stores the generated filename on $formData by reference.
     */
    private function handleUploads(array &$formData): void
    {
        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $photo->move('uploads/vendor_photos', $newName);
            $formData['photo'] = $newName;
        }

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
    }
}
