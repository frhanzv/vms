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
        $invitationModel = new \App\Models\InvitationModel();
        $scheduleModel   = new \App\Models\InvitationScheduleModel();
        $licenseModel    = new \App\Models\VisitorLicenseModel();
        $equipmentModel  = new \App\Models\VisitorEquipmentModel();

        $companyId = (int) (session()->get('company_id') ?? 0);

        // Basic validation
        $rules = [
            'ic_number'      => 'required',
            'resident'       => 'required',
            'full_name'      => 'required',
            'contact_number' => 'required',
            'email'          => 'required|valid_email',
            'visit_reason'   => 'required',
            'dates'          => 'required',
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Handle File Upload for Government ID
        $governmentIdPath = null;
        $govIdFile = $this->request->getFile('government_id');
        if ($govIdFile && $govIdFile->isValid() && !$govIdFile->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/government_id';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $newName = $govIdFile->getRandomName();
            $govIdFile->move($uploadPath, $newName);
            $governmentIdPath = 'uploads/government_id/' . $newName;
        }

        // Handle File Upload for Invitation Letter
        $invitationLetterPath = null;
        $letterFiles = $this->request->getFileMultiple('invitation_letter');
        if ($letterFiles) {
            foreach ($letterFiles as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $uploadPath = FCPATH . 'uploads/invitation_letters';
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    $newName = $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    $invitationLetterPath = 'uploads/invitation_letters/' . $newName;
                    break; 
                }
            }
        }

        // Concatenate address
        $address = implode(', ', array_filter([
            $this->request->getPost('address_1'),
            $this->request->getPost('address_2'),
            $this->request->getPost('address_3')
        ]));

        // Prepare Invitation Data
        $invitationData = [
            'full_name'            => $this->request->getPost('full_name'),
            'ic_passport'          => $this->request->getPost('ic_number'),
            'contact'              => $this->request->getPost('contact_number'),
            'visitor_email'        => $this->request->getPost('email'),
            'resident'             => $this->request->getPost('resident'),
            'date_of_birth'        => $this->request->getPost('date_of_birth'),
            'sex'                  => $this->request->getPost('sex'),
            'address'              => $address,
            'city'                 => $this->request->getPost('city'),
            'state'                => $this->request->getPost('state'),
            'postcode'             => $this->request->getPost('postal_code'),
            'country'              => $this->request->getPost('country'),
            'vehicle_registration' => $this->request->getPost('vehicle_registration'),
            'vehicle_category'     => $this->request->getPost('category'),
            'vehicle_type'         => $this->request->getPost('vehicle_type'),
            'company_visited'      => $this->request->getPost('company_visited'),
            'host_contact'         => $this->request->getPost('host_contact'),
            'staff_id'             => $this->request->getPost('staff_id'),
            'reason'               => $this->request->getPost('visit_reason'),
            'company'              => $this->request->getPost('company_name'),
            'registration_no'      => $this->request->getPost('company_reg_id'),
            'government_id_path'   => $governmentIdPath,
            'invitation_letter_path' => $invitationLetterPath,
            'status'               => 'Submitted',
            'registration_source'  => 'Visitor Pass Request',
            // Auto-bypass security steps for requests if needed to show in list immediately
            'video_watched'        => 1, 
            'facial_verified_at'   => date('Y-m-d H:i:s'),
        ];

        // Save Invitation
        $invitationId = $invitationModel->insert($invitationData);

        if (!$invitationId) {
            return redirect()->back()->withInput()->with('errors', $invitationModel->errors());
        }

        // Save Schedules
        $dates = $this->request->getPost('dates');
        if ($dates && is_array($dates)) {
            foreach ($dates as $date) {
                if (!empty($date['date_from']) && !empty($date['date_to'])) {
                    $scheduleModel->insert([
                        'invitation_id' => $invitationId,
                        'date_from'     => $date['date_from'],
                        'date_to'       => $date['date_to'],
                    ]);
                }
            }
        }

        // Save Licenses
        $licenses = $this->request->getPost('licenses');
        if ($licenses && is_array($licenses)) {
            foreach ($licenses as $license) {
                if (!empty($license['class'])) {
                    $licenseModel->insert([
                        'invitation_id'  => $invitationId,
                        'license_class'  => $license['class'],
                        'expiry_date'    => $license['expiry'],
                    ]);
                }
            }
        }

        // Save Equipment
        $equipments = $this->request->getPost('equipment');
        if ($equipments && is_array($equipments)) {
            foreach ($equipments as $eq) {
                if (!empty($eq['category'])) {
                    $equipmentModel->insert([
                        'invitation_id'  => $invitationId,
                        'category'       => $eq['category'],
                        'size'           => $eq['size'],
                        'transport'      => $eq['transport'],
                        'purpose'        => $eq['purpose'],
                        'equipment_type' => $eq['type'],
                        'voltage'        => $eq['voltage'],
                        'quantity'       => $eq['quantity'],
                        'serial_number'  => $eq['serial'],
                    ]);
                }
            }
        }

        return redirect()->to(base_url('visitors'))->with('success', 'Visitor pass request submitted successfully');
    }
}
