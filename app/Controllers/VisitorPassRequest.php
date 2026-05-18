<?php

namespace App\Controllers;

use App\Models\ClientFormFieldModel;

class VisitorPassRequest extends BaseController
{
    public function index()
    {
        $companyId            = (int) (session()->get('company_id') ?? 0);
        $clientFormFieldModel = new ClientFormFieldModel();
        $rows                 = $clientFormFieldModel->getForCompanyForm($companyId, 'visitor_pass_request');

        $fieldSettings = [];
        foreach ($rows as $f) {
            $fieldSettings[$f['field_key']] = (bool) $f['is_enabled'];
        }

        $data = [
            'pageTitle'     => 'Visitor Pass Request - SafeG',
            'fieldSettings' => $fieldSettings,
        ];

        return view('visitors/request', $data);
    }

    public function store()
    {
        $companyId            = (int) (session()->get('company_id') ?? 0);
        $clientFormFieldModel = new ClientFormFieldModel();
        $rows                 = $clientFormFieldModel->getForCompanyForm($companyId, 'visitor_pass_request');

        $fieldSettings = [];
        foreach ($rows as $f) {
            $fieldSettings[$f['field_key']] = (bool) $f['is_enabled'];
        }

        $rules = [];

        if ($fieldSettings['person_details'] ?? true) {
            $rules['ic_number']      = 'required';
            $rules['resident']       = 'required';
            $rules['full_name']      = 'required';
            $rules['contact_number'] = 'required';
            $rules['email']          = 'required|valid_email';
        }

        if ($fieldSettings['company_visiting'] ?? true) {
            $rules['company_visiting'] = 'required';
        }

        $validation = \Config\Services::validation();
        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        return redirect()->to(base_url('visitors'))->with('success', 'Visitor pass request submitted successfully');
    }
}
