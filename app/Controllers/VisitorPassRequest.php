<?php

namespace App\Controllers;

class VisitorPassRequest extends BaseController
{
    public function index()
    {
        $data = [
            'pageTitle' => 'Visitor Pass Request - SafeG'
        ];

        return view('visitors/request', $data);
    }

    public function store()
    {
        // Handle form submission
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'company_visiting' => 'required',
            'date_from' => 'required',
            'date_to' => 'required',
            'time_from' => 'required',
            'time_to' => 'required',
            'resident' => 'required',
            'ic_number' => 'required',
            'full_name' => 'required',
            'contact_number' => 'required',
            'email' => 'required|valid_email',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Process form data (to be implemented with database)
        return redirect()->to(base_url('visitors'))->with('success', 'Visitor pass request submitted successfully');
    }
}
