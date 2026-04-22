<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BlacklistRequestModel;
use App\Models\InvitationModel;

class BlacklistEntry extends BaseController
{
    protected $blacklistModel;
    protected $invitationModel;

    public function __construct()
    {
        $this->blacklistModel  = new BlacklistRequestModel();
        $this->invitationModel = new InvitationModel();
    }

    /**
     * Show the Entry search page
     */
    public function index()
    {
        return view('blacklist/entry', [
            'pageTitle' => 'Blacklist Entry',
        ]);
    }

    /**
     * AJAX: Search invitations by IC/Passport
     * Returns JSON result
     */
    public function search()
    {
        $ic = trim($this->request->getGet('ic') ?? '');

        if ($ic === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please enter an IC / Passport number.',
            ]);
        }

        $db      = \Config\Database::connect();
        $results = $db->table('invitations')
            ->like('ic_passport', $ic)
            ->orLike('full_name', $ic)
            ->limit(20)
            ->get()
            ->getResultArray();

        if (empty($results)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No records found.',
            ]);
        }

        $data = [];
        foreach ($results as $row) {
            // Auto-detect type: check staff table by ic_passport
            $staff = $db->table('staff')
                ->where('ic_passport', $row['ic_passport'])
                ->get()
                ->getRowArray();

            $data[] = [
                'id'          => $row['id'],
                'full_name'   => $row['full_name'],
                'ic_passport' => $row['ic_passport'] ?? '',
                'contact'     => $row['contact'] ?? '',
                'company'     => $row['company'] ?? '',
                'date_of_birth' => $row['date_of_birth'] ?? '',
                'sex'         => $row['sex'] ?? '',
                'type'        => $staff ? 'Staff' : 'Visitor',
                'staff_no'    => $staff ? ($staff['staff_no'] ?? '') : '',
                'designation' => $staff ? ($staff['designation'] ?? '') : '',
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * Show the pre-filled blacklist entry form for a specific invitation
     */
    public function proceed()
    {
        $invitationId = (int) $this->request->getGet('invitation_id');

        if ($invitationId <= 0) {
            return redirect()->to(base_url('blacklist/entry'))
                ->with('error', 'Invalid invitation selected.');
        }

        $db         = \Config\Database::connect();
        $invitation = $db->table('invitations')
            ->where('id', $invitationId)
            ->get()
            ->getRowArray();

        if (!$invitation) {
            return redirect()->to(base_url('blacklist/entry'))
                ->with('error', 'Record not found.');
        }

        // Auto-detect type
        $staff = $db->table('staff')
            ->where('ic_passport', $invitation['ic_passport'])
            ->get()
            ->getRowArray();

        $type        = $staff ? 'Staff' : 'Visitor';
        $staffNo     = $staff ? ($staff['staff_no'] ?? '') : '';
        $designation = $staff ? ($staff['designation'] ?? '') : '';

        // Resolve country name from countries table
        $countryName = '—';
        if (!empty($invitation['country'])) {
            $countryRow = $db->table('countries')
                ->where('id', $invitation['country'])
                ->get()
                ->getRowArray();
            $countryName = $countryRow ? $countryRow['name'] : $invitation['country'];
        }

        // Load blacklist reasons
        $reasons = $db->table('blacklistreason')
            ->where('status', 'Active')
            ->get()
            ->getResultArray();

        return view('blacklist/entry_form', [
            'pageTitle'    => 'Blacklist Entry',
            'invitation'   => $invitation,
            'type'         => $type,
            'staff_no'     => $staffNo,
            'designation'  => $designation,
            'country_name' => $countryName,
            'reasons'      => $reasons,
        ]);
    }

    /**
     * Store the blacklist entry
     */
    public function store()
    {
        $ic          = $this->request->getPost('ic_passport_no');
        $name        = $this->request->getPost('name');
        $type        = $this->request->getPost('type');
        $staffId     = $this->request->getPost('staff_id');
        $blacklistDate = $this->request->getPost('blacklist_date');
        $reason      = $this->request->getPost('reason');

        // Check for duplicate active blacklist
        if ($this->blacklistModel->isDuplicate($ic)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This person is already blacklisted.');
        }

        $data = [
            'ic_passport_no' => $ic,
            'name'           => $name,
            'type'           => $type,
            'staff_id'       => $staffId ?: null,
            'blacklist_date' => $blacklistDate,
            'created_date'   => date('Y-m-d'),
            'reason'         => $reason,
            'status'         => 'closed',
        ];

        $result = $this->blacklistModel->insert($data);

        if (!$result) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save blacklist entry. Please try again.');
        }

        return redirect()->to(base_url('blacklist/closedlist'))
            ->with('success', $name . ' has been successfully blacklisted.');
    }
}