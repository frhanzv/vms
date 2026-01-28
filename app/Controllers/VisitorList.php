<?php

namespace App\Controllers;

use App\Models\InvitationModel;

class VisitorList extends BaseController
{
    protected $invitationModel;

    public function __construct()
    {
        $this->invitationModel = new InvitationModel();
    }

    public function index()
    {
        // Get all approved invitations
        $approvedInvitations = $this->invitationModel
            ->where('status', 'Approved')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Count statistics
        $totalApproved = count($approvedInvitations);
        $checkedIn = $this->invitationModel
            ->where('status', 'Approved')
            ->where('checked_in_at IS NOT NULL')
            ->countAllResults();
        $pending = $this->invitationModel
            ->where('status', 'Pending')
            ->orWhere('status', 'Submitted')
            ->countAllResults();

        // Format data for the view
        $visitors = [];
        foreach ($approvedInvitations as $index => $invitation) {
            $visitors[] = [
                'no' => $index + 1,
                'date' => date('d/m/Y', strtotime($invitation['created_at'])),
                'full_name' => $invitation['full_name'],
                'ic_passport' => $invitation['ic_passport'],
                'contact' => $invitation['contact'],
                'vehicle_reg' => $invitation['vehicle_registration'],
                'location' => $invitation['location'] ?? 'N/A',
                'type' => 'Invitation',
                'card_status' => $invitation['checked_in_at'] ? 'Active' : null,
                'pass_no' => $invitation['id'], // Using invitation ID as pass number
                'reason' => strtoupper($invitation['reason'])
            ];
        }

        $data = [
            'pageTitle' => 'Visitor List - SafeG',
            'stats' => [
                'total' => $totalApproved,
                'checkedIn' => $checkedIn,
                'pending' => $pending
            ],
            'visitors' => $visitors
        ];

        return view('visitors/list', $data);
    }
}
