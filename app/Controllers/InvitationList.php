<?php

namespace App\Controllers;

use CodeIgniter\Email\Email;
use App\Models\InvitationModel;
use App\Models\InvitationScheduleModel;
use App\Models\InvitationVisitorModel;
use App\Models\VisitReasonModel;
use App\Models\LocationModel;
use App\Models\CompanyModel;
use App\Models\VisitorTypeModel;

class InvitationList extends BaseController
{
    protected $invitationModel;
    protected $scheduleModel;
    protected $visitorModel;
    protected $visitReasonModel;
    protected $locationModel;
    protected $companyModel;
    protected $visitorTypeModel;

    public function __construct()
    {
        $this->invitationModel = new InvitationModel();
        $this->scheduleModel = new InvitationScheduleModel();
        $this->visitorModel = new InvitationVisitorModel();
        $this->visitReasonModel = new VisitReasonModel();
        $this->locationModel = new LocationModel();
        $this->companyModel = new CompanyModel();
        $this->visitorTypeModel = new VisitorTypeModel();
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
            'submitted' => $this->invitationModel->where('status', 'Submitted')->countAllResults(),
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

        $visitorTypes = $this->invitationsSupportVisitorType()
            ? $this->visitorTypeModel->orderBy('name', 'ASC')->findAll()
            : [];
        
        // Get locations from database  
        $locations = $this->locationModel->findAll();
        
        // Get active companies from database
        $companyModel = new \App\Models\CompanyModel();
        $companies = $companyModel->where('status', 'active')->findAll();

        // Get current user information from session
        $currentUser = session()->get();
        
        // Debug: log session data
        log_message('info', 'Session data in create invitation: ' . print_r($currentUser, true));
        
        $data = [
            'pageTitle' => 'Create Invitation - SafeG',
            'visitReasons' => $visitReasons,
            'visitorTypes' => $visitorTypes,
            'locations' => $locations,
            'companies' => $companies,
            'staff_id' => $currentUser['staff_id'] ?? $currentUser['user_id'] ?? 'STAFF001',  // Try user_id as fallback
            'contact_no' => $currentUser['contact'] ?? $currentUser['phone'] ?? '+60123456789'  // Try phone as fallback
        ];

        return view('invitations/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'reason' => 'required',
            'schedules' => 'required',
            'schedules.*.date_from' => 'required',
            'schedules.*.date_to' => 'required',
        ];

        $visitorTypeCount = $this->invitationsSupportVisitorType()
            ? $this->visitorTypeModel->countAllResults()
            : 0;
        if ($visitorTypeCount > 0) {
            $rules['visitor_type_id'] = 'required|integer';
        }

        if (!$validation->withRequest($this->request)->setRules($rules)->run()) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }

        $rawVisitors = $this->request->getPost('visitors');
        if (! is_array($rawVisitors)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', ['visitors' => 'Visitor details are required.']);
        }

        $visitors = [];
        foreach ($rawVisitors as $row) {
            if (! is_array($row)) {
                continue;
            }
            $fullName = trim((string) ($row['full_name'] ?? $row['name'] ?? ''));
            if ($fullName === '') {
                continue;
            }
            $visitors[] = [
                'full_name' => $fullName,
                'contact' => trim((string) ($row['contact'] ?? '')),
                'visitor_email' => trim((string) ($row['visitor_email'] ?? $row['email'] ?? '')),
            ];
        }

        if ($visitors === []) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', ['visitors' => 'Add at least one visitor with a full name, contact, and email.']);
        }

        $visitorValidation = \Config\Services::validation();
        foreach ($visitors as $i => $visitorRow) {
            if (! $visitorValidation->setRules([
                'contact' => 'required|max_length[20]',
                'visitor_email' => 'required|valid_email|max_length[255]',
            ])->run($visitorRow)) {
                return redirect()->back()
                               ->withInput()
                               ->with('errors', array_merge(
                                   ['visitors' => 'Visitor #' . ($i + 1) . ' has invalid contact or email.'],
                                   $visitorValidation->getErrors()
                               ));
            }
        }

        $schedules = $this->request->getPost('schedules');
        if (! is_array($schedules) || $schedules === []) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', ['schedules' => 'At least one visit schedule is required.']);
        }

        $vtPost = $this->request->getPost('visitor_type_id');
        $visitorTypeId = ($vtPost !== null && $vtPost !== '') ? (int) $vtPost : null;
        if ($visitorTypeCount > 0) {
            if ($visitorTypeId === null || ! $this->visitorTypeModel->find($visitorTypeId)) {
                return redirect()->back()
                               ->withInput()
                               ->with('errors', ['visitor_type_id' => 'Please select a valid visitor type.']);
            }
        } else {
            $visitorTypeId = null;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $currentUser = session()->get('full_name') ?? session()->get('user_name') ?? 'System';

            log_message('info', 'Invitation POST data: ' . print_r($this->request->getPost(), true));

            $shared = [
                'ic_passport' => null,
                'company' => $this->request->getPost('company_visited'),
                'vehicle_registration' => null,
                'location' => $this->request->getPost('location'),
                'invited_by' => $currentUser,
                'reason' => $this->request->getPost('reason'),
                'other_reason' => $this->request->getPost('other_reason'),
                'link_expiry' => $this->request->getPost('link_expiry'),
                'status' => 'Pending',
                'staff_id' => $this->request->getPost('staff_id'),
                'company_visited' => $this->request->getPost('company_visited'),
                'host_contact' => $this->request->getPost('contact_person'),
                'registration_source' => 'Invitation',
            ];
            if ($this->invitationsSupportVisitorType()) {
                $shared['visitor_type_id'] = $visitorTypeId;
            }

            $createdIds = [];

            foreach ($visitors as $visitorRow) {
                $invitationData = array_merge($shared, [
                    'full_name' => $visitorRow['full_name'],
                    'contact' => $visitorRow['contact'],
                    'visitor_email' => $visitorRow['visitor_email'],
                ]);

                log_message('info', 'Prepared invitation data: ' . print_r($invitationData, true));

                $invitationId = $this->invitationModel->insert($invitationData);

                if (! $invitationId) {
                    $errors = $this->invitationModel->errors();
                    $dbError = \Config\Database::connect()->error();
                    $errorMsg = 'Failed to create invitation';
                    if ($errors !== []) {
                        $errorMsg .= ': ' . implode(', ', $errors);
                    }
                    if ($dbError['code'] !== 0) {
                        $errorMsg .= ' (DB Error: ' . $dbError['message'] . ')';
                    }
                    throw new \Exception($errorMsg);
                }

                $createdIds[] = (int) $invitationId;

                foreach ($schedules as $schedule) {
                    if (! empty($schedule['date_from']) && ! empty($schedule['date_to'])) {
                        $this->scheduleModel->insert([
                            'invitation_id' => $invitationId,
                            'date_from' => $schedule['date_from'],
                            'date_to' => $schedule['date_to'],
                        ]);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            $n = count($createdIds);
            $emailsSent = 0;
            foreach ($createdIds as $invitationId) {
                if ($this->sendInvitationEmail($invitationId)) {
                    $emailsSent++;
                } else {
                    log_message('warning', 'Invitation created but email sending failed for invitation ID: ' . $invitationId);
                }
            }

            if ($emailsSent === $n) {
                $msg = $n === 1
                    ? 'Invitation created successfully and email sent to visitor.'
                    : $n . ' invitations created successfully and email sent to each visitor.';

                return redirect()->to(base_url('invitations'))->with('success', $msg);
            }

            if ($emailsSent > 0) {
                return redirect()->to(base_url('invitations'))
                               ->with('warning', $n . ' invitation(s) created; email sent to ' . $emailsSent . ' of ' . $n . '. Resend from the invitation list for any that failed.');
            }

            return redirect()->to(base_url('invitations'))
                           ->with('warning', $n . ' invitation(s) created but email could not be sent. You can resend from the invitation list.');
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

        $invitation['visitor_type_name'] = '';
        if ($this->invitationsSupportVisitorType() && ! empty($invitation['visitor_type_id'])) {
            $vt = $this->visitorTypeModel->find((int) $invitation['visitor_type_id']);
            $invitation['visitor_type_name'] = $vt ? $vt['name'] : '';
        }

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