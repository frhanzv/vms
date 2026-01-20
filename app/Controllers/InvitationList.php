<?php

namespace App\Controllers;

use CodeIgniter\Email\Email;
use App\Models\InvitationModel;
use App\Models\InvitationScheduleModel;
use App\Models\InvitationVisitorModel;
use App\Models\VisitReasonModel;
use App\Models\LocationModel;
use App\Models\CompanyModel;

class InvitationList extends BaseController
{
    protected $invitationModel;
    protected $scheduleModel;
    protected $visitorModel;
    protected $visitReasonModel;
    protected $locationModel;
    protected $companyModel;

    public function __construct()
    {
        $this->invitationModel = new InvitationModel();
        $this->scheduleModel = new InvitationScheduleModel();
        $this->visitorModel = new InvitationVisitorModel();
        $this->visitReasonModel = new VisitReasonModel();
        $this->locationModel = new LocationModel();
        $this->companyModel = new CompanyModel();
    }

    public function index()
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $search = $this->request->getGet('search') ?? '';
        $status = $this->request->getGet('status') ?? '';
        
        // Get invitations with pagination
        $result = $this->invitationModel->getInvitationsWithPagination($page, 10, $search);
        
        // Format the data for display
        $invitations = [];
        foreach ($result['data'] as $index => $invitation) {
            // Get the first schedule for display
            $schedule = $this->scheduleModel->where('invitation_id', $invitation['id'])
                                          ->orderBy('date_from', 'ASC')
                                          ->first();
            
            $invitations[] = [
                'id' => $invitation['id'],  // Add the ID for resending emails
                'no' => ($page - 1) * 10 + $index + 1,
                'date' => $schedule ? date('d/m/Y', strtotime($schedule['date_from'])) : '-',
                'full_name' => $invitation['full_name'],
                'ic_passport' => $invitation['ic_passport'],
                'contact' => $invitation['contact'],
                'vehicle_reg' => $invitation['vehicle_registration'] ?: '',
                'location' => $invitation['location'] ?: '-',
                'company' => $invitation['company'] ?: '-',
                'invited_by' => $invitation['invited_by'] ?: '-',
                'status' => $invitation['status'],
                'reason' => $invitation['reason'] === 'OTHER' ? ($invitation['other_reason'] ?: 'OTHER') : $invitation['reason']
            ];
        }
        
        // Calculate stats
        $stats = [
            'total' => $result['total'],
            'pending' => $this->invitationModel->where('status', 'Pending')->countAllResults(),
            'approved' => $this->invitationModel->where('status', 'Approved')->countAllResults(),
            'rejected' => $this->invitationModel->where('status', 'Rejected')->countAllResults()
        ];

        $data = [
            'pageTitle' => 'Invitation List - SafeG',
            'stats' => $stats,
            'invitations' => $invitations,
            'pagination' => [
                'current_page' => $result['current_page'],
                'last_page' => $result['last_page'],
                'total' => $result['total']
            ]
        ];

        return view('invitations/list', $data);
    }

    public function create()
    {
        // Get visit reasons from database
        $visitReasons = $this->visitReasonModel->findAll();
        
        // Get locations from database  
        $locations = $this->locationModel->findAll();
        
        // Get active companies from database
        $companyModel = new \App\Models\CompanyModel();
        $companies = $companyModel->where('status', 'active')->findAll();

        // Get current user information from session
        $currentUser = session()->get();
        
        $data = [
            'pageTitle' => 'Create Invitation - SafeG',
            'visitReasons' => $visitReasons,
            'locations' => $locations,
            'companies' => $companies,
            'staff_id' => $currentUser['staff_id'] ?? 'STAFF001',  // Use staff_id from session
            'contact_no' => $currentUser['contact'] ?? '+60123456789'  // Default contact if not available
        ];

        return view('invitations/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        // Validate admin-provided fields including basic visitor details
        $rules = [
            'full_name' => 'required|max_length[255]',
            'contact' => 'required|max_length[20]', 
            'visitor_email' => 'required|valid_email|max_length[255]',
            'reason' => 'required',
            'schedules.*.date_from' => 'required',
            'schedules.*.date_to' => 'required'
        ];

        if (!$validation->withRequest($this->request)->setRules($rules)->run()) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get current user from session
            $currentUser = session()->get('full_name') ?? session()->get('user_name') ?? 'System';
            
            // Prepare main invitation data (admin provides basic visitor details)
            $invitationData = [
                'full_name' => $this->request->getPost('full_name'),  // Admin provides
                'ic_passport' => null,  // Visitor will fill later
                'contact' => $this->request->getPost('contact'),  // Admin provides
                'visitor_email' => $this->request->getPost('visitor_email'),  // Admin provides visitor's email
                'company' => $this->request->getPost('company_visited'),  // From admin selection
                'vehicle_registration' => null,  // Visitor will fill later
                'location' => $this->request->getPost('location'),
                'invited_by' => $currentUser,
                'reason' => $this->request->getPost('reason'),
                'other_reason' => $this->request->getPost('other_reason'),
                'link_expiry' => $this->request->getPost('link_expiry'),
                'status' => 'Pending'
            ];

            // Insert main invitation
            $invitationId = $this->invitationModel->insert($invitationData);

            if (!$invitationId) {
                // Get detailed error information
                $errors = $this->invitationModel->errors();
                $dbError = \Config\Database::connect()->error();
                
                $errorMsg = 'Failed to create invitation';
                if (!empty($errors)) {
                    $errorMsg .= ': ' . implode(', ', $errors);
                }
                if ($dbError['code'] !== 0) {
                    $errorMsg .= ' (DB Error: ' . $dbError['message'] . ')';
                }
                
                throw new \Exception($errorMsg);
            }

            // Insert schedules
            $schedules = $this->request->getPost('schedules');
            if ($schedules) {
                foreach ($schedules as $schedule) {
                    if (!empty($schedule['date_from']) && !empty($schedule['date_to'])) {
                        $this->scheduleModel->insert([
                            'invitation_id' => $invitationId,
                            'date_from' => $schedule['date_from'],
                            'date_to' => $schedule['date_to']
                        ]);
                    }
                }
            }

            // Send invitation email to visitor
            if ($this->sendInvitationEmail($invitationId)) {
                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Database transaction failed');
                }

                return redirect()->to(base_url('invitations'))
                               ->with('success', 'Invitation created successfully and email sent to visitor.');
            } else {
                // Email failed but continue with invitation creation
                log_message('warning', 'Invitation created but email sending failed for invitation ID: ' . $invitationId);
                
                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Database transaction failed');
                }

                return redirect()->to(base_url('invitations'))
                               ->with('warning', 'Invitation created successfully but email could not be sent. You can resend it from the invitation list.');
            }

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Invitation creation failed: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create invitation: ' . $e->getMessage());
        }
    }

    /**
     * Send invitation email to visitor
     */
    private function sendInvitationEmail($invitationId)
    {
        try {
            // Get invitation details with related data
            $invitation = $this->getInvitationDetails($invitationId);
            
            if (!$invitation) {
                log_message('error', 'Invitation not found for ID: ' . $invitationId);
                return false;
            }

            // Check if visitor email is available
            if (empty($invitation['visitor_email'])) {
                log_message('warning', 'No visitor email found for invitation ID: ' . $invitationId);
                return false;
            }

            $email = new Email();
            
            // Generate registration link with invitation token
            $registrationLink = base_url('visitor-registration?token=' . base64_encode($invitationId));
            
            // Prepare email data
            $emailData = [
                'visitor_name' => $invitation['full_name'],
                'company' => $invitation['company_name'],
                'location' => $invitation['location_name'],
                'reason' => $invitation['reason_name'],
                'other_reason' => $invitation['other_reason'],
                'invited_by' => $invitation['invited_by'],
                'schedules' => $invitation['schedules'],
                'registration_link' => $registrationLink,
                'link_expiry' => $invitation['link_expiry']
            ];
            
            // Render email template
            $message = view('emails/invitation_template', $emailData);
            
            $email->setFrom('noreply@safeg.com', 'SafeG VMS');
            $email->setTo($invitation['visitor_email']);
            $email->setSubject('Visitor Invitation - Complete Your Registration');
            $email->setMessage($message);
            
            // Always try to send emails (remove development mode restriction)
            $result = $email->send();
            
            // Log for debugging
            if ($result) {
                log_message('info', 'Email sent successfully to: ' . $invitation['visitor_email']);
            } else {
                log_message('error', 'Email sending failed to: ' . $invitation['visitor_email']);
                log_message('error', 'Email error: ' . $email->printDebugger());
            }
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', 'Email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get invitation details with related data for email
     */
    private function getInvitationDetails($invitationId)
    {
        // Get invitation with related data
        $invitation = $this->invitationModel->find($invitationId);
        if (!$invitation) {
            return null;
        }

        // Get company name
        $company = $this->companyModel->find($invitation['company']);
        $invitation['company_name'] = $company ? $company['name'] : $invitation['company'];

        // Get location name
        $location = $this->locationModel->find($invitation['location']);
        $invitation['location_name'] = $location ? $location['name'] : $invitation['location'];

        // Get reason name
        $reason = $this->visitReasonModel->find($invitation['reason']);
        $invitation['reason_name'] = $reason ? $reason['reason'] : $invitation['reason'];

        // Get schedules
        $schedules = $this->scheduleModel->where('invitation_id', $invitationId)->findAll();
        $invitation['schedules'] = $schedules;

        // Visitor email should be stored in the invitation record
        $invitation['visitor_email'] = $invitation['visitor_email'] ?? $invitation['contact'] . '@example.com';

        return $invitation;
    }

    /**
     * Resend invitation email
     */
    public function resend($id)
    {
        if ($this->sendInvitationEmail($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Invitation email sent successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to send invitation email'
            ]);
        }
    }
}