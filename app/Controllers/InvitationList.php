<?php

namespace App\Controllers;

use App\Models\InvitationModel;
use App\Models\InvitationScheduleModel;
use App\Models\VisitReasonModel;
use App\Models\LocationModel;
use App\Models\CompanyModel;
use App\Models\VisitorTypeModel;
use App\Libraries\InvitationEmailSender;

class InvitationList extends BaseController
{
    protected $invitationModel;
    protected $scheduleModel;
    protected $visitReasonModel;
    protected $locationModel;
    protected $companyModel;
    protected $visitorTypeModel;
    protected InvitationEmailSender $invitationEmailSender;

    public function __construct()
    {
        $this->invitationModel = new InvitationModel();
        $this->scheduleModel = new InvitationScheduleModel();
        $this->visitReasonModel = new VisitReasonModel();
        $this->locationModel = new LocationModel();
        $this->companyModel = new CompanyModel();
        $this->visitorTypeModel = new VisitorTypeModel();
        $this->invitationEmailSender = new InvitationEmailSender();
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
            
            $linkExpiry = $invitation['link_expiry'] ?? null;
            $invitations[] = [
                'id' => $invitation['id'],  // Add the ID for resending emails
                'no' => ($page - 1) * 10 + $index + 1,
                'date' => $schedule ? date('d/m/Y', strtotime($schedule['date_from'])) : '-',
                'visit_from' => $schedule ? date('d/m/Y H:i', strtotime((string) $schedule['date_from'])) : '-',
                'visit_to' => $schedule ? date('d/m/Y H:i', strtotime((string) $schedule['date_to'])) : '-',
                'full_name' => $invitation['full_name'],
                'ic_passport' => $invitation['ic_passport'],
                'contact' => $invitation['contact'],
                'visitor_email' => $invitation['visitor_email'] ?? '',
                'vehicle_reg' => $invitation['vehicle_registration'] ?: '',
                'location' => $invitation['location'] ?: '-',
                'company' => $invitation['company'] ?: '-',
                'invited_by' => $invitation['invited_by'] ?: '-',
                'status' => $invitation['status'],
                'reason' => $invitation['reason'] === 'OTHER' ? ($invitation['other_reason'] ?: 'OTHER') : $invitation['reason'],
                'link_expiry' => $linkExpiry ? date('d/m/Y', strtotime((string) $linkExpiry)) : '-',
                'visitor_count' => 1,
                'registration_link' => base_url('visitor-registration?token=' . base64_encode((string) $invitation['id'])),
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

    /**
     * JSON list for invitation history modal.
     * Uses the same scope as the invitation list (all invitation records), so older rows
     * without registration_source / with different invited_by still appear.
     */
    public function historyRows()
    {
        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = min(50, max(5, (int) ($this->request->getGet('per_page') ?? 10)));
        $search = trim((string) $this->request->getGet('search'));
        $sort = (string) ($this->request->getGet('sort') ?? 'created_at');
        $order = strtoupper((string) ($this->request->getGet('order') ?? 'DESC'));
        if (! in_array($order, ['ASC', 'DESC'], true)) {
            $order = 'DESC';
        }
        $allowedSort = ['created_at', 'link_expiry', 'full_name', 'status'];
        if (! in_array($sort, $allowedSort, true)) {
            $sort = 'created_at';
        }

        $builder = $this->invitationModel->builder();

        if ($search !== '') {
            $builder->groupStart()
                ->like('full_name', $search)
                ->orLike('company', $search)
                ->orLike('company_visited', $search)
                ->groupEnd();
        }

        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $perPage;
        $rows = $builder->orderBy($sort, $order)->limit($perPage, $offset)->get()->getResultArray();

        $ids = array_column($rows, 'id');
        $firstScheduleDate = [];
        if ($ids !== []) {
            $schedules = $this->scheduleModel->whereIn('invitation_id', $ids)
                ->orderBy('date_from', 'ASC')
                ->findAll();
            foreach ($schedules as $sch) {
                $iid = (int) $sch['invitation_id'];
                if (! isset($firstScheduleDate[$iid])) {
                    $firstScheduleDate[$iid] = $sch['date_from'];
                }
            }
        }

        $today = strtotime('today');
        $out = [];
        foreach ($rows as $i => $row) {
            $id = (int) $row['id'];
            $le = $row['link_expiry'] ?? null;
            $leTs = $le ? strtotime($le . ' 23:59:59') : false;
            $out[] = [
                'id' => $id,
                'no' => $offset + $i + 1,
                'date' => isset($firstScheduleDate[$id])
                    ? date('d/m/Y', strtotime((string) $firstScheduleDate[$id]))
                    : (! empty($row['created_at']) ? date('d/m/Y', strtotime((string) $row['created_at'])) : '-'),
                'full_name' => $row['full_name'] ?? '',
                'link_expiry' => $le ? date('d/m/Y', strtotime((string) $le)) : '-',
                'link_expired' => $leTs !== false && $leTs < $today,
                'status' => $row['status'] ?? '',
                'company' => $row['company_visited'] ?: ($row['company'] ?? ''),
                'invited_by' => $row['invited_by'] ?? '',
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $out,
            'pagination' => [
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($total / $perPage)),
                'total' => $total,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * JSON payload to autofill the create-invitation form from a past invitation.
     */
    public function historyForForm($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid invitation']);
        }

        $row = $this->invitationModel->find($id);
        if (! $row) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invitation not found']);
        }

        $schedules = $this->scheduleModel->where('invitation_id', $id)->orderBy('date_from', 'ASC')->findAll();
        $schedOut = [];
        foreach ($schedules as $sch) {
            $schedOut[] = [
                'date_from' => $this->formatForDatetimeLocal($sch['date_from'] ?? null),
                'date_to' => $this->formatForDatetimeLocal($sch['date_to'] ?? null),
            ];
        }

        $visitorTypeId = isset($row['visitor_type_id']) ? (int) $row['visitor_type_id'] : null;

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'staff_id' => (string) ($row['staff_id'] ?? ''),
                'visitor_type_id' => $visitorTypeId > 0 ? $visitorTypeId : null,
                'company_visited' => (string) ($row['company_visited'] ?? $row['company'] ?? ''),
                'contact_person' => (string) ($row['host_contact'] ?? ''),
                'link_expiry' => $this->formatForDateInput($row['link_expiry'] ?? null),
                'reason' => (string) ($row['reason'] ?? ''),
                'other_reason' => (string) ($row['other_reason'] ?? ''),
                'location' => (string) ($row['location'] ?? ''),
                'allow_sub_invites' => ! empty($row['allow_sub_invites']),
                'visitors' => [[
                    'full_name' => (string) ($row['full_name'] ?? ''),
                    'contact' => (string) ($row['contact'] ?? ''),
                    'visitor_email' => (string) ($row['visitor_email'] ?? ''),
                ]],
                'schedules' => $schedOut,
            ],
        ]);
    }

    private function formatForDatetimeLocal($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        $ts = strtotime((string) $value);

        return $ts ? date('Y-m-d\TH:i', $ts) : '';
    }

    private function formatForDateInput($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        $ts = strtotime((string) $value);

        return $ts ? date('Y-m-d', $ts) : '';
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'reason' => 'required',
            'schedules' => 'required',
            'schedules.*.date_from' => 'required',
            'schedules.*.date_to' => 'required',
            'staff_id' => 'required|max_length[50]',
            'contact_person' => 'required|max_length[20]',
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
                'staff_id' => trim((string) $this->request->getPost('staff_id')),
                'company_visited' => $this->request->getPost('company_visited'),
                'host_contact' => trim((string) $this->request->getPost('contact_person')),
                'registration_source' => 'Invitation',
                'allow_sub_invites' => $this->request->getPost('allow_sub_invites') ? 1 : 0,
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
                if ($this->invitationEmailSender->send($invitationId)) {
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
     * Resend invitation email
     */
    public function resend($id)
    {
        if ($this->invitationEmailSender->send((int) $id)) {
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