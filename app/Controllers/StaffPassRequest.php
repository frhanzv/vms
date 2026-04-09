<?php

namespace App\Controllers;

class StaffPassRequest extends BaseController
{
    public function index()
    {
        $data = [
            'pageTitle' => 'Staff Pass Request - SafeG',
        ];

        return view('staffs/staffpassrequest', $data);
    }

    public function store()
    {
        // Collect form data
        $formData = [
            // Application Info
            'location_access'   => $this->request->getPost('company_visiting'),

            // Person Details
            'resident'          => $this->request->getPost('resident'),
            'ic_number'         => $this->request->getPost('ic_number'),
            'date_of_birth'     => $this->request->getPost('date_of_birth'),
            'sex'               => $this->request->getPost('sex'),
            'full_name'         => $this->request->getPost('full_name'),
            'contact_number'    => $this->request->getPost('contact_number'),
            'email'             => $this->request->getPost('email'),
            'address_1'         => $this->request->getPost('address_1'),
            'address_2'         => $this->request->getPost('address_2'),
            'address_3'         => $this->request->getPost('address_3'),
            'city'              => $this->request->getPost('city'),
            'state'             => $this->request->getPost('state'),
            'postal_code'       => $this->request->getPost('postal_code'),
            'country'           => $this->request->getPost('country'),
            'category'          => $this->request->getPost('category'),
            'vehicle_type'      => $this->request->getPost('vehicle_type'),
            'vehicle_reg'       => $this->request->getPost('vehicle_registration'),

            // Company Details
            'company_reg_id'    => $this->request->getPost('company_reg_id'),
            'company_name'      => $this->request->getPost('company_name'),
        ];

        // Handle file uploads
        $governmentId = $this->request->getFile('government_id');
        $otherDoc     = $this->request->getFile('invitation_letter');

        // if ($governmentId && $governmentId->isValid()) {
        //     $governmentId->move(WRITEPATH . 'uploads/government_ids');
        //     $formData['government_id'] = $governmentId->getName();
        // }

        // if ($otherDoc && $otherDoc->isValid()) {
        //     $otherDoc->move(WRITEPATH . 'uploads/other_docs');
        //     $formData['other_doc'] = $otherDoc->getName();
        // }

        // Save to database
        // $model = new \App\Models\StaffPassRequestModel();
        // $model->insert($formData);

        // Redirect after successful submission
        // return redirect()->to(base_url('staffs'))->with('success', 'Staff pass request submitted successfully.');

        return redirect()->to(base_url('staffs'));
    }
}