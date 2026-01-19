<?php

namespace App\Controllers;

class VisitorRegistration extends BaseController
{
    public function index()
    {
        $data = [
            'pageTitle' => 'Visitor Registration - SafeG'
        ];

        return view('visitors/registration', $data);
    }

    public function submit()
    {
        // Handle form submission
        $validationRules = [
            'ic_number' => 'required',
            'full_name' => 'required|min_length[3]',
            'contact_number' => 'required',
            'email' => 'required|valid_email',
            'company_name' => 'required',
        ];

        if (!$this->validate($validationRules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Process the registration
        // Save to database, send notifications, etc.

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Registration submitted successfully'
        ]);
    }
}
