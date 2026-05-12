<?php

namespace App\Controllers;

use App\Models\InvitationModel;
use App\Models\InvitationScheduleModel;
use App\Models\VisitReasonModel;
use App\Models\LocationModel;
use App\Models\CompanyModel;
use App\Models\VisitorTypeModel;
use App\Models\ClientFormFieldModel;
use App\Libraries\InvitationEmailSender;

class InvitationList extends BaseController
{
    protected $invitationModel;
    protected $scheduleModel;
    protected $visitReasonModel;
    protected $locationModel;
    protected $companyModel;
    protected $visitorTypeModel;
    protected $clientFormFieldModel;
    protected InvitationEmailSender $invitationEmailSender;

    public function __construct()
    {
        $this->invitationModel      = new InvitationModel();
        $this->scheduleModel        = new InvitationScheduleModel();
        $this->visitReasonModel     = new VisitReasonModel();
        $this->locationModel        = new LocationModel();
        $this->companyModel         = new CompanyModel();
        $this->visitorTypeModel     = new VisitorTypeModel();
        $this->clientFormFieldModel = new ClientFormFieldModel();
        $this->invitationEmailSender = new InvitationEmailSender();
    }

    private function getInvitationFormConfig(): array
    {
        $companyId = (int) session()->get('company_id');
        if (! $companyId) {
            return [];
        }
        $config = [];
        foreach ($this->clientFormFieldModel->getForCompanyForm($companyId, 'invitation') as $f) {
            $config[$f['field_key']] = (bool) $f['is_enabled'];
        }
        return $config;
    }

    public function index()
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        if (! in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $filters = $this->parseInvitationFilters();
        $result  = $this->buildInvitationsResult($filters, $page, $perPage);

        $stats = [
            'total'     => $this->invitationModel->countAll(),
            'pending'   => $this->invitationModel->where('status', 'Pending')->countAllResults(),
            'submitted' => $this->invitationModel->where('status', 'Submitted')->countAllResults(),
            'approved'  => $this->invitationModel->where('status', 'Approved')->countAllResults(),
            'rejected'  => $this->invitationModel->where('status', 'Rejected')->countAllResults(),
        ];

        $db = \Config\Database::connect();
        $reasonList = array_column(
            $db->query("SELECT DISTINCT reason FROM invitations WHERE reason IS NOT NULL AND reason != '' ORDER BY reason ASC")->getResultArray(),
            'reason'
        );
        $locationList = array_column(
            $db->query("SELECT DISTINCT location FROM invitations WHERE location IS NOT NULL AND location != '' ORDER BY location ASC")->getResultArray(),
            'location'
        );

        return view('invitations/list', [
            'pageTitle'    => 'Invitation List - SafeG',
            'stats'        => $stats,
            'invitations'  => $result['invitations'],
            'filters'      => array_merge($filters, ['per_page' => $perPage]),
            'reasonList'   => $reasonList,
            'locationList' => $locationList,
            'pagination'   => $result['pagination'],
        ]);
    }

    public function data()
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        if (! in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $filters = $this->parseInvitationFilters();
        $result  = $this->buildInvitationsResult($filters, $page, $perPage);

        return $this->response->setJSON([
            'success'     => true,
            'invitations' => $result['invitations'],
            'pagination'  => $result['pagination'],
        ]);
    }

    private function parseInvitationFilters(): array
    {
        return [
            'search'    => trim((string) ($this->request->getGet('search') ?? '')),
            'status'    => trim((string) ($this->request->getGet('status') ?? '')),
            'reason'    => trim((string) ($this->request->getGet('reason') ?? '')),
            'location'  => trim((string) ($this->request->getGet('location') ?? '')),
            'date_from' => trim((string) ($this->request->getGet('date_from') ?? '')),
            'date_to'   => trim((string) ($this->request->getGet('date_to') ?? '')),
            'sort'      => trim((string) ($this->request->getGet('sort') ?? 'date_desc')),
        ];
    }

    private function buildInvitationsResult(array $filters, int $page, int $perPage): array
    {
        $sortMap = [
            'date_desc' => ['created_at', 'DESC'],
            'date_asc'  => ['created_at', 'ASC'],
            'name_asc'  => ['full_name',  'ASC'],
            'name_desc' => ['full_name',  'DESC'],
        ];
        [$sortField, $sortOrder] = $sortMap[$filters['sort']] ?? $sortMap['date_desc'];

        $builder = $this->invitationModel->builder();
        $this->applyInvitationFilters($builder, $filters);

        $total  = $builder->countAllResults(false);
        $offset = ($page - 1) * $perPage;
        $rows   = $builder->orderBy($sortField, $sortOrder)->limit($perPage, $offset)->get()->getResultArray();

        $ids = array_map('intval', array_column($rows, 'id'));
        $firstScheduleByInvitation = [];
        if ($ids !== []) {
            $schedules = $this->scheduleModel->whereIn('invitation_id', $ids)->orderBy('date_from', 'ASC')->findAll();
            foreach ($schedules as $sch) {
                $iid = (int) $sch['invitation_id'];
                if (! isset($firstScheduleByInvitation[$iid])) {
                    $firstScheduleByInvitation[$iid] = $sch;
                }
            }
        }

        $invitations = [];
        foreach ($rows as $index => $row) {
            $schedule   = $firstScheduleByInvitation[(int) $row['id']] ?? null;
            $linkExpiry = $row['link_expiry'] ?? null;
            $invitations[] = [
                'id'                => $row['id'],
                'no'                => ($page - 1) * $perPage + $index + 1,
                'date'              => $schedule ? date('d/m/Y', strtotime($schedule['date_from'])) : (! empty($row['created_at']) ? date('d/m/Y', strtotime((string) $row['created_at'])) : '-'),
                'visit_from'        => $schedule ? date('d/m/Y H:i', strtotime((string) $schedule['date_from'])) : '-',
                'visit_to'          => $schedule ? date('d/m/Y H:i', strtotime((string) $schedule['date_to'])) : '-',
                'full_name'         => $row['full_name'],
                'ic_passport'       => $row['ic_passport'],
                'contact'           => $row['contact'],
                'visitor_email'     => $row['visitor_email'] ?? '',
                'vehicle_reg'       => $row['vehicle_registration'] ?: '',
                'location'          => $row['location'] ?: '-',
                'company'           => $row['company'] ?: '-',
                'invited_by'        => $row['invited_by'] ?: '-',
                'status'            => $row['status'],
                'reason'            => $row['reason'] === 'OTHER' ? ($row['other_reason'] ?: 'OTHER') : $row['reason'],
                'link_expiry'       => $linkExpiry ? date('d/m/Y', strtotime((string) $linkExpiry)) : '-',
                'visitor_count'     => 1,
                'registration_link' => base_url('visitor-registration?token=' . base64_encode((string) $row['id'])),
            ];
        }

        return [
            'invitations' => $invitations,
            'pagination'  => [
                'current_page' => $page,
                'last_page'    => max(1, (int) ceil($total / $perPage)),
                'total'        => $total,
                'per_page'     => $perPage,
            ],
        ];
    }

    private function applyInvitationFilters($builder, array $filters): void
    {
        if ($filters['search'] !== '') {
            $builder->groupStart()
                ->like('full_name', $filters['search'])
                ->orLike('ic_passport', $filters['search'])
                ->orLike('contact', $filters['search'])
                ->orLike('company', $filters['search'])
                ->orLike('vehicle_registration', $filters['search'])
                ->orLike('location', $filters['search'])
                ->orLike('invited_by', $filters['search'])
                ->orLike('status', $filters['search'])
                ->orLike('reason', $filters['search'])
                ->orLike('other_reason', $filters['search'])
                ->orLike('visitor_email', $filters['search'])
                ->orLike('staff_id', $filters['search'])
                ->orLike('host_contact', $filters['search'])
                ->groupEnd();
        }

        $allowedStatuses = ['Pending', 'Submitted', 'Approved', 'Rejected'];
        if ($filters['status'] !== '' && in_array($filters['status'], $allowedStatuses, true)) {
            $builder->where('status', $filters['status']);
        }

        if ($filters['reason'] !== '') {
            $builder->where('reason', $filters['reason']);
        }

        if ($filters['location'] !== '') {
            $builder->where('location', $filters['location']);
        }

        if ($filters['date_from'] !== '') {
            $builder->where('DATE(created_at) >=', $filters['date_from']);
        }

        if ($filters['date_to'] !== '') {
            $builder->where('DATE(created_at) <=', $filters['date_to']);
        }
    }

    public function export()
    {
        $filters = [
            'search'    => trim((string) ($this->request->getGet('search') ?? '')),
            'status'    => trim((string) ($this->request->getGet('status') ?? '')),
            'reason'    => trim((string) ($this->request->getGet('reason') ?? '')),
            'location'  => trim((string) ($this->request->getGet('location') ?? '')),
            'date_from' => trim((string) ($this->request->getGet('date_from') ?? '')),
            'date_to'   => trim((string) ($this->request->getGet('date_to') ?? '')),
            'sort'      => trim((string) ($this->request->getGet('sort') ?? 'date_desc')),
            'per_page'  => 10,
        ];

        $sortMap = [
            'date_desc' => ['created_at', 'DESC'],
            'date_asc'  => ['created_at', 'ASC'],
            'name_asc'  => ['full_name',  'ASC'],
            'name_desc' => ['full_name',  'DESC'],
        ];
        [$sortField, $sortOrder] = $sortMap[$filters['sort']] ?? $sortMap['date_desc'];

        $builder = $this->invitationModel->builder();
        $this->applyInvitationFilters($builder, $filters);

        $rows = $builder->orderBy($sortField, $sortOrder)->get()->getResultArray();
        $ids = array_map('intval', array_column($rows, 'id'));

        $firstScheduleByInvitation = [];
        if ($ids !== []) {
            $schedules = $this->scheduleModel
                ->whereIn('invitation_id', $ids)
                ->orderBy('date_from', 'ASC')
                ->findAll();

            foreach ($schedules as $schedule) {
                $invitationId = (int) $schedule['invitation_id'];
                if (! isset($firstScheduleByInvitation[$invitationId])) {
                    $firstScheduleByInvitation[$invitationId] = $schedule;
                }
            }
        }

        $handle = fopen('php://temp', 'w+');
        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, [
            'No',
            'Invitation Date',
            'Visit From',
            'Visit To',
            'Full Name',
            'IC/Passport',
            'Contact',
            'Visitor Email',
            'Company',
            'Vehicle Registration',
            'Location',
            'Invited By',
            'Status',
            'Reason',
            'Link Expiry',
            'Created At',
        ]);

        foreach ($rows as $index => $row) {
            $invitationId = (int) $row['id'];
            $schedule = $firstScheduleByInvitation[$invitationId] ?? null;
            $dateFrom = $schedule['date_from'] ?? null;
            $dateTo = $schedule['date_to'] ?? null;
            $reason = ($row['reason'] ?? '') === 'OTHER'
                ? ($row['other_reason'] ?? 'OTHER')
                : ($row['reason'] ?? '');

            fputcsv($handle, [
                $index + 1,
                $dateFrom ? date('d/m/Y', strtotime((string) $dateFrom)) : '-',
                $dateFrom ? date('d/m/Y H:i', strtotime((string) $dateFrom)) : '-',
                $dateTo ? date('d/m/Y H:i', strtotime((string) $dateTo)) : '-',
                $row['full_name'] ?? '',
                $row['ic_passport'] ?? '',
                $row['contact'] ?? '',
                $row['visitor_email'] ?? '',
                $row['company'] ?? '',
                $row['vehicle_registration'] ?? '',
                $row['location'] ?? '',
                $row['invited_by'] ?? '',
                $row['status'] ?? '',
                $reason,
                ! empty($row['link_expiry']) ? date('d/m/Y', strtotime((string) $row['link_expiry'])) : '',
                ! empty($row['created_at']) ? date('d/m/Y H:i:s', strtotime((string) $row['created_at'])) : '',
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="invitations-' . date('Y-m-d-His') . '.csv"')
            ->setBody((string) $csvContent);
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
            'pageTitle'    => 'Create Invitation - SafeG',
            'visitReasons' => $visitReasons,
            'visitorTypes' => $visitorTypes,
            'locations'    => $locations,
            'companies'    => $companies,
            'staff_id'     => $currentUser['staff_id'] ?? $currentUser['user_id'] ?? 'STAFF001',
            'contact_no'   => $currentUser['contact'] ?? $currentUser['phone'] ?? '+60123456789',
            'formConfig'   => $this->getInvitationFormConfig(),
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
        $invConfig  = $this->getInvitationFormConfig();
        $isEnabled  = static fn(string $k) => ! isset($invConfig[$k]) || $invConfig[$k];

        $validation = \Config\Services::validation();

        $rules = [
            'reason'         => 'required',
            'schedules'      => 'required',
            'schedules.*.date_from' => 'required',
            'schedules.*.date_to'   => 'required',
            'staff_id'       => 'required|max_length[50]',
            'contact_person' => 'required|max_length[20]',
            'link_expiry'    => 'required',
        ];
        if ($isEnabled('reason'))         { $rules['reason']         = 'required'; }
        if ($isEnabled('staff_id'))       { $rules['staff_id']       = 'required|max_length[50]'; }
        if ($isEnabled('contact_person')) { $rules['contact_person'] = 'required|max_length[20]'; }

        $visitorTypeCount = $this->invitationsSupportVisitorType()
            ? $this->visitorTypeModel->countAllResults()
            : 0;
        if ($visitorTypeCount > 0 && $isEnabled('visitor_type')) {
            $rules['visitor_type_id'] = 'required|integer';
        }

        if (! $validation->withRequest($this->request)->setRules($rules)->run()) {
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
            $email    = trim((string) ($row['visitor_email'] ?? $row['email'] ?? ''));
            // Use full_name as row identifier when enabled, email otherwise
            if ($isEnabled('visitor_full_name') ? ($fullName === '') : ($email === '')) {
                continue;
            }
            $visitors[] = [
                'full_name'     => $fullName,
                'contact'       => trim((string) ($row['contact'] ?? '')),
                'visitor_email' => $email,
            ];
        }

        if ($visitors === []) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', ['visitors' => 'Add at least one visitor with a full name, contact, and email.']);
        }

        $perVisitorRules = [];
        if ($isEnabled('visitor_contact')) { $perVisitorRules['contact']       = 'required|max_length[20]'; }
        if ($isEnabled('visitor_email'))   { $perVisitorRules['visitor_email'] = 'required|valid_email|max_length[255]'; }

        if (! empty($perVisitorRules)) {
            $visitorValidation = \Config\Services::validation();
            foreach ($visitors as $i => $visitorRow) {
                if (! $visitorValidation->setRules($perVisitorRules)->run($visitorRow)) {
                    return redirect()->back()
                                   ->withInput()
                                   ->with('errors', array_merge(
                                       ['visitors' => 'Visitor #' . ($i + 1) . ' has invalid contact or email.'],
                                       $visitorValidation->getErrors()
                                   ));
                }
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
                'ic_passport'         => null,
                'vehicle_registration' => null,
                'invited_by'          => $currentUser,
                'status'              => 'Pending',
                'registration_source' => 'Invitation',
                'company'             => $isEnabled('company_visited') ? $this->request->getPost('company_visited') : null,
                'company_visited'     => $isEnabled('company_visited') ? $this->request->getPost('company_visited') : null,
                'location'            => $isEnabled('location')        ? $this->request->getPost('location')        : null,
                'reason'              => $isEnabled('reason')          ? $this->request->getPost('reason')          : null,
                'other_reason'        => $isEnabled('reason')          ? $this->request->getPost('other_reason')    : null,
                'link_expiry'         => $isEnabled('link_expiry')     ? $this->request->getPost('link_expiry')     : null,
                'staff_id'            => $isEnabled('staff_id')        ? trim((string) $this->request->getPost('staff_id'))       : '',
                'host_contact'        => $isEnabled('contact_person')  ? trim((string) $this->request->getPost('contact_person')) : '',
                'allow_sub_invites'   => ($isEnabled('allow_sub_invites') && $this->request->getPost('allow_sub_invites')) ? 1 : 0,
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
            $notificationService = new \App\Services\NotificationService();
            foreach ($createdIds as $invitationId) {
                if ($notificationService->dispatch($invitationId, 'invitation_sent')) {
                    $emailsSent++;
                } else {
                    log_message('warning', 'Invitation created but notification sending failed for invitation ID: ' . $invitationId);
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
        if ((new \App\Services\NotificationService())->dispatch((int) $id, 'invitation_sent')) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Invitation sent successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to send invitation'
            ]);
        }
    }
}