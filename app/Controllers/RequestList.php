<?php

namespace App\Controllers;

use App\Models\InvitationModel;
use App\Models\InvitationScheduleModel;
use App\Models\VisitorLicenseModel;
use App\Models\VisitorEquipmentModel;
use App\Models\ClientFeatureModel;
use App\Libraries\InvitationProcessFlowService;
use App\Services\InvitationApprovalService;

class RequestList extends BaseController
{
    protected $invitationModel;
    protected $scheduleModel;
    protected $licenseModel;
    protected $equipmentModel;
    protected $flowService;
    protected InvitationApprovalService $approvalService;

    public function __construct()
    {
        $this->invitationModel = new InvitationModel();
        $this->scheduleModel = new InvitationScheduleModel();
        $this->licenseModel = new VisitorLicenseModel();
        $this->equipmentModel = new VisitorEquipmentModel();
        $this->flowService = new InvitationProcessFlowService();
        $this->approvalService = new InvitationApprovalService();
    }

    /** Vehicle categories / types offered on the visitor form (KPK "Edit Vehicle Info"). */
    public const VEHICLE_TYPES = [
        'CAR'             => ['SEDAN' => 'Sedan', 'HATCHBACK' => 'Hatchback', 'SUV' => 'SUV', 'COUPE' => 'Coupe'],
        'MOTORCYCLE'      => ['SCOOTER' => 'Scooter', 'SPORT_BIKE' => 'Sport bike', 'CRUISER' => 'Cruiser'],
        'TRUCK'           => ['PICKUP' => 'Pickup', 'LORRY' => 'Lorry', 'TRAILER' => 'Trailer'],
        'BUS'             => ['MINI_BUS' => 'Mini Bus', 'COACH' => 'Coach', 'SCHOOL_BUS' => 'School Bus'],
        'VAN'             => ['CARGO_VAN' => 'Cargo Van', 'PASSENGER_VAN' => 'Passenger Van', 'MINIVAN' => 'Minivan'],
        'HEAVY_MACHINERY' => ['EXCAVATOR' => 'Excavator', 'BULLDOZER' => 'Bulldozer', 'CRANE' => 'Crane', 'FORKLIFT' => 'Forklift'],
    ];

    /** Platform superadmin picks the client to look at; everyone else sees their own. [clientId, clients]. */
    private function viewClient(): array
    {
        helper(['role', 'feature']);
        $viewClientId = current_client_id();
        $viewClients  = [];
        if (is_platform_superadmin()) {
            $viewClients  = (new \App\Models\ClientModel())->select('id,name')->orderBy('name')->findAll();
            $viewClientId = (int) ($this->request->getGet('view_client_id') ?: $viewClientId ?: ($viewClients[0]['id'] ?? 0));
            if (! in_array($viewClientId, array_map('intval', array_column($viewClients, 'id')), true)) {
                $viewClientId = (int) ($viewClients[0]['id'] ?? 0);
            }
        }

        return [(int) $viewClientId, $viewClients];
    }

    /** Submitted requests this user may approve: same rules as the queue (client, host, workflow steps, no kiosk walk-ins). */
    private function submittedQuery(int $viewClientId)
    {
        $requiresBriefing = false;
        $requiresFacial   = false;
        foreach ($this->flowService->getOrderedSteps(true) as $step) {
            if ($step['key'] === 'approval') {
                break;
            }
            $requiresBriefing = $requiresBriefing || in_array($step['key'], ['security_briefing', 'video'], true);
            $requiresFacial   = $requiresFacial || in_array($step['key'], ['facial_verification', 'take_photo'], true);
        }
        $query = $this->invitationModel->where('status', 'Submitted');
        $this->applyClientRequestScope($query, $viewClientId);
        $this->applyHostRequestScope($query);
        $this->applyRequestWorkflowFilters($query, $requiresBriefing, $requiresFacial);
        $this->excludeKioskWalkIns($query);

        return $query;
    }

    /**
     * KPK visitor-request-list: a table (No, Date, Full Name, Status, Company Visiting, Reason) with search,
     * Sort By and items per page. A row opens the details page.
     */
    public function index()
    {
        // Approval e-mails link to requests?request_id=N: open that request's details
        if ((int) $this->request->getGet('request_id') > 0) {
            return redirect()->to(base_url('requests/view/' . (int) $this->request->getGet('request_id')));
        }
        [$viewClientId, $viewClients] = $this->viewClient();
        $q       = trim((string) $this->request->getGet('q'));
        $sort    = $this->request->getGet('sort') === 'dt-a' ? 'ASC' : 'DESC';
        $perPage = in_array((int) $this->request->getGet('per_page'), [10, 20, 30, 40], true) ? (int) $this->request->getGet('per_page') : 10;
        $page    = max(1, (int) $this->request->getGet('page'));

        $query = $this->submittedQuery($viewClientId);
        if ($q !== '') {
            $query->groupStart()->like('full_name', $q)->orLike('ic_passport', $q)->orLike('contact', $q)->orLike('company', $q)->groupEnd();
        }
        $total = $query->countAllResults(false);
        $rows  = $query->orderBy('created_at', $sort)->findAll($perPage, ($page - 1) * $perPage);

        $list = [];
        foreach ($rows as $i => $r) {
            $reason = (string) ($r['reason'] ?? '');
            $list[] = [
                'id'       => (int) $r['id'],
                'no'       => ($page - 1) * $perPage + $i + 1,
                'date'     => date('d/m/Y', strtotime((string) $r['created_at'])),
                'name'     => $r['full_name'],
                'ic'       => $r['ic_passport'] ?? '',
                'status'   => $r['status'],
                'visiting' => array_values(array_filter(array_map('trim', explode(',', (string) ($r['location'] ?? ''))))),
                'reason'   => in_array($reason, ['', 'OTHER'], true) ? (($r['other_reason'] ?? '') ?: 'OTHER') : $reason,
            ];
        }

        return view('requests/kpk_list', [
            'pageTitle'    => 'Visitor Request List - SafeG',
            'rows'         => $list,
            'total'        => $total,
            'page'         => $page,
            'perPage'      => $perPage,
            'pages'        => max(1, (int) ceil($total / $perPage)),
            'q'            => $q,
            'sort'         => $sort === 'ASC' ? 'dt-a' : 'dt-d',
            'viewClients'  => $viewClients,
            'viewClientId' => $viewClientId,
        ]);
    }

    /** The invitation if this user may see it here (client + host rules), else null. */
    private function scopedInvitation(int $id): ?array
    {
        [$viewClientId] = $this->viewClient();
        $query = $this->invitationModel->where('id', $id);
        $this->applyClientRequestScope($query, $viewClientId);
        $inv = $query->first();

        return $inv && $this->hostCanAccessInvitation($inv) ? $inv : null;
    }

    /** KPK request details: Application Info, Date / Details Of Visit, Person, Driving License, Company, Assets, Upload; Approve / Reject. */
    public function view($id)
    {
        $inv = $this->scopedInvitation((int) $id);
        if (! $inv) {
            return redirect()->to(base_url('requests'))->with('error', 'Request not found.');
        }
        $db   = \Config\Database::connect();
        $docs = static function ($raw): array {
            if (! $raw) {
                return [];
            }
            $list = json_decode((string) $raw, true);

            return array_map(static fn($p) => base_url('uploads/' . $p), is_array($list) ? $list : [$raw]);
        };

        return view('requests/kpk_view', [
            'pageTitle'    => 'Visitor Request - SafeG',
            'inv'          => $inv,
            'schedules'    => (new \App\Models\InvitationScheduleModel())->where('invitation_id', (int) $id)->orderBy('date_from')->findAll(),
            'equipment'    => $this->equipmentModel->where('invitation_id', (int) $id)->findAll(),
            'licenses'     => $db->tableExists('visitor_licenses') ? $db->table('visitor_licenses')->where('invitation_id', (int) $id)->get()->getResultArray() : [],
            'visitorType'  => ! empty($inv['visitor_type_id']) && $db->tableExists('visitor_types') ? (string) ($db->table('visitor_types')->where('id', (int) $inv['visitor_type_id'])->get()->getRow('name') ?? '') : '',
            'uploads'      => array_filter([
                'Government ID'     => $docs($inv['government_id_path'] ?? ''),
                'Invitation Letter' => $docs($inv['invitation_letter_path'] ?? ''),
                'Profile Photo'     => $docs($inv['profile_photo_path'] ?? ''),
            ]),
            'canDecide'    => ($inv['status'] ?? '') === 'Submitted',
            'vehicleTypes' => self::VEHICLE_TYPES,
        ]);
    }

    /** KPK "Edit Vehicle Info" (Category, Type Of Vehicle) before the request is decided. */
    public function updateVehicle($id)
    {
        $inv = $this->scopedInvitation((int) $id);
        if (! $inv || ($inv['status'] ?? '') !== 'Submitted') {
            return redirect()->to(base_url('requests'))->with('error', 'Only a submitted request can be changed.');
        }
        $cat  = strtoupper(trim((string) $this->request->getPost('vehicle_category')));
        $type = strtoupper(trim((string) $this->request->getPost('vehicle_type')));
        if ($cat !== '' && ! isset(self::VEHICLE_TYPES[$cat])) {
            return redirect()->back()->with('error', 'Please choose a valid vehicle category.');
        }
        if ($type !== '' && ($cat === '' || ! isset(self::VEHICLE_TYPES[$cat][$type]))) {
            return redirect()->back()->with('error', 'Please choose a vehicle type for that category.');
        }
        \Config\Database::connect()->table('invitations')->where('id', (int) $id)->update([
            'vehicle_category' => $cat ?: null, 'vehicle_type' => $type ?: null, 'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('requests/view/' . (int) $id))->with('success', 'Vehicle info updated.');
    }

    /** The earlier VMS queue view (queue on the left, details on the right), still at requests/queue. */
    public function queue()
    {
        helper(['role', 'feature']);
        $viewService = new \App\Services\RequestViewService();
        $viewClientId = current_client_id();
        $viewClients = [];
        if (is_platform_superadmin()) {
            $viewClients = (new \App\Models\ClientModel())->select('id,name')->orderBy('name')->findAll();
            $viewClientId = (int)($this->request->getGet('view_client_id') ?: $viewClientId ?: ($viewClients[0]['id'] ?? 0));
            if (! in_array($viewClientId, array_map('intval', array_column($viewClients, 'id')), true)) {
                return $this->response->setStatusCode(400)->setBody('Invalid client');
            }
        }
        $viewClient = $viewClientId > 0 ? (new \App\Models\ClientModel())->find($viewClientId) : null;
        $activeSteps = $this->flowService->getOrderedSteps(true);
        $requiresBriefing = false;
        $requiresFacial = false;

        foreach ($activeSteps as $step) {
            if ($step['key'] === 'approval') {
                break;
            }
            if ($step['key'] === 'security_briefing' || $step['key'] === 'video') {
                $requiresBriefing = true;
            }
            if ($step['key'] === 'facial_verification' || $step['key'] === 'take_photo') {
                $requiresFacial = true;
            }
        }

        // Load submitted requests in batches (avoid loading entire queue into memory).
        $queueLimit = 50;
        $query = $this->invitationModel->where('status', 'Submitted');
        $this->applyClientRequestScope($query, $viewClientId);
        $this->applyHostRequestScope($query);
        $this->applyRequestWorkflowFilters($query, $requiresBriefing, $requiresFacial);
        $this->excludeKioskWalkIns($query);

        $submittedRequests = $query->orderBy('created_at', 'DESC')->findAll($queueLimit);

        $invitationIds = array_column($submittedRequests, 'id');
        $schedulesByInvitation = $this->loadEarliestSchedulesByInvitation($invitationIds);

        // Format queue requests
        $queueRequests = [];
        foreach ($submittedRequests as $request) {
            $schedule = $schedulesByInvitation[$request['id']] ?? null;
            $timeUntilVisit = $this->formatTimeUntilVisit($schedule);

            $queueRequests[] = [
                'id' => $request['id'],
                'name' => $request['full_name'],
                'company' => $request['company'] ?? 'N/A',
                'host' => $request['invited_by'] ?? '',
                'time' => $timeUntilVisit,
                'photo' => $request['profile_photo_path'] ? base_url('uploads/' . $request['profile_photo_path']) : '',
                'initials' => strtoupper(substr($request['full_name'], 0, 2)),
                'status' => 'normal',
                'is_flagged' => false
            ];
        }

        $selectedRequestId = (int)($this->request->getGet('request_id') ?? 0);

        // Get selected request as current. Fallback to first request in queue.
        $currentRequest = null;
        if (count($submittedRequests) > 0) {
            $first = $submittedRequests[0];
            if ($selectedRequestId > 0) {
                foreach ($submittedRequests as $submittedRequest) {
                    if ((int)$submittedRequest['id'] === $selectedRequestId) {
                        $first = $submittedRequest;
                        break;
                    }
                }
            }
            $schedule = $schedulesByInvitation[$first['id']] ?? null;
            $hostDetails = $this->resolveHostDetails($first, $viewClientId);
            $visitedCompany = $this->resolveVisitedCompany($first, $viewClient);

            // Get equipment
            $equipment = $this->equipmentModel
                ->where('invitation_id', $first['id'])
                ->findAll();

            $assets = [];
            foreach ($equipment as $item) {
                $assets[] = [
                    'type' => 'inventory_2',
                    'name' => ($item['equipment_type'] ?? 'Equipment') . 
                            ($item['serial_number'] ? ' (Serial: ' . $item['serial_number'] . ')' : '')
                ];
            }

            $currentRequest = [
                'raw_id' => (int)$first['id'],
                'id' => 'VIS-' . $first['id'],
                'name' => $first['full_name'],
                'company' => $first['company'] ?? 'N/A',
                'host' => $hostDetails['name'],
                'arrival' => $schedule ? date('h:i A - d/m/Y', strtotime($schedule['date_from'])) : 'N/A',
                'purpose' => $first['reason'] ?? 'N/A',
                'photo' => $first['profile_photo_path'] ? base_url('uploads/' . $first['profile_photo_path']) : '',
                'government_id_image' => $first['government_id_path'] ? base_url('uploads/' . $first['government_id_path']) : '',
                'invitation_letter' => (function($raw) {
                    if (!$raw) return [];
                    $decoded = json_decode($raw, true);
                    if (is_array($decoded)) {
                        return array_map(fn($p) => base_url('uploads/' . $p), $decoded);
                    }
                    return [base_url('uploads/' . $raw)];
                })($first['invitation_letter_path'] ?? ''),
                'access_zones' => explode(', ', $first['location'] ?? ''),
                'assets' => $assets,
                'notes' => '',
                'watchlist_status' => 'cleared',
                'ai_match' => $first['facial_verified_at'] ? 95 : 0,
                'ic_passport' => $first['ic_passport'] ?? 'N/A',
                'contact' => $first['contact'] ?? 'N/A',
                'email' => $first['visitor_email'] ?? 'N/A',
                'vehicle' => $first['vehicle_registration'] ?? 'N/A',
                'staff_id' => $first['staff_id'] ?? 'N/A',
                'host_contact' => $hostDetails['contact'],
                'company_visited' => $visitedCompany
            ];
        }

        // Calculate stats
        $pendingQuery = (new InvitationModel())->where('status', 'Submitted');
        $this->applyClientRequestScope($pendingQuery, $viewClientId);
        $this->applyHostRequestScope($pendingQuery);
        $this->applyRequestWorkflowFilters($pendingQuery, $requiresBriefing, $requiresFacial);
        $this->excludeKioskWalkIns($pendingQuery);

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $todayPendingQuery = (new InvitationModel())
            ->where('status', 'Submitted')
            ->where('DATE(created_at)', $today);
        $yesterdayPendingQuery = (new InvitationModel())
            ->where('status', 'Submitted')
            ->where('DATE(created_at)', $yesterday);
        foreach ([$todayPendingQuery, $yesterdayPendingQuery] as $trendQuery) {
            $this->applyClientRequestScope($trendQuery, $viewClientId);
            $this->applyHostRequestScope($trendQuery);
            $this->applyRequestWorkflowFilters($trendQuery, $requiresBriefing, $requiresFacial);
            $this->excludeKioskWalkIns($trendQuery);
        }
        $todayPending = $todayPendingQuery->countAllResults();
        $yesterdayPending = $yesterdayPendingQuery->countAllResults();
        $pendingChangePercent = $yesterdayPending > 0
            ? (int) round((($todayPending - $yesterdayPending) / $yesterdayPending) * 100)
            : ($todayPending > 0 ? 100 : 0);

        $expectedQuery = (new InvitationModel())
            ->distinct()
            ->select('invitations.id')
            ->join('invitation_schedules schedules', 'schedules.invitation_id = invitations.id', 'inner')
            ->where('invitations.status', 'Approved')
            ->where('DATE(schedules.date_from) <=', $today)
            ->where('DATE(schedules.date_to) >=', $today);
        $rejectedQuery = (new InvitationModel())->where('status', 'Rejected');
        $this->applyClientRequestScope($expectedQuery, $viewClientId);
        $this->applyClientRequestScope($rejectedQuery, $viewClientId);
        $this->applyHostRequestScope($expectedQuery);
        $this->applyHostRequestScope($rejectedQuery);

        $stats = [
            'pending' => $pendingQuery->countAllResults(),
            'pending_change_percent' => $pendingChangePercent,
            // No persisted review flag currently exists; do not label every
            // submitted request as flagged.
            'flagged' => 0,
            'expected' => $expectedQuery->countAllResults(),
            'rejected' => $rejectedQuery->countAllResults(),
        ];

        $data = [
            'pageTitle' => 'Request List - SafeG',
            'canEditRequestView' => $viewClientId > 0 && \App\Services\RequestViewService::canEdit((string)session()->get('role')),
            'requestViewClientId' => $viewClientId,
            'requestViewClientName' => $viewClient['name'] ?? 'this client',
            'requestViewClients' => $viewClients,
            'requestViewSettings' => $viewService->get($viewClientId),
            'stats' => $stats,
            'currentRequest' => $currentRequest,
            'queueRequests' => $queueRequests,
            'queueTotal' => $stats['pending'],
            'queueLimit' => $queueLimit,
        ];

        return view('requests/list', $data);
    }

    public function saveViewSettings()
    {
        helper(['role', 'feature']);
        if (! \App\Services\RequestViewService::canEdit((string)session()->get('role'))) {
            return $this->response->setStatusCode(403)->setJSON(['success'=>false,'message'=>'Only admins can edit this view.']);
        }
        $input = $this->request->getJSON(true) ?? [];
        $clientId = (int)($input['client_id'] ?? 0);
        if ($clientId <= 0 || (! is_platform_superadmin() && $clientId !== current_client_id())) {
            return $this->response->setStatusCode(403)->setJSON(['success'=>false,'message'=>'You can only edit your own client view.']);
        }
        if (! (new \App\Models\ClientModel())->find($clientId)) {
            return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'Client not found.']);
        }
        $sections = $input['sections'] ?? null;
        if (! is_array($sections) || array_diff(array_keys($sections), array_keys(\App\Services\RequestViewService::SECTIONS))
            || count($sections) !== count(\App\Services\RequestViewService::SECTIONS)
            || count(array_filter($sections, 'is_bool')) !== count($sections)) {
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'message'=>'Invalid view settings.']);
        }
        $saved = (new \App\Services\RequestViewService())->save($clientId, $sections);
        return $this->response->setStatusCode($saved ? 200 : 500)->setJSON(['success'=>$saved,'message'=>$saved?'View saved.':'Unable to save view.']);
    }
    private function applyRequestWorkflowFilters($query, bool $requiresBriefing, bool $requiresFacial): void
    {
        if (! $requiresBriefing && ! $requiresFacial) {
            return;
        }

        $query->groupStart()
            ->where('registration_source', 'kiosk')
            ->orGroupStart();

        if ($requiresBriefing) {
            $query->where('video_watched', 1);
        }
        if ($requiresFacial) {
            $query->where('facial_verified_at IS NOT NULL', null, false);
        }

        $query->groupEnd()
            ->groupEnd();
    }

    private function applyClientRequestScope($query, int $clientId): void
    {
        if ($clientId > 0) {
            // Shared product: requests created under this client OR held at one of its gates.
            helper('client_visibility');
            $query->where(visibility_invitation_sql($clientId, 'invitations'), null, false);
        }
    }

    private function excludeKioskWalkIns($query): void
    {
        $autoApprovalClientIds = (new ClientFeatureModel())
            ->where('feature_key', 'auto_approve_after_workflow')
            ->where('is_enabled', 1)
            ->findColumn('client_id');

        if (empty($autoApprovalClientIds)) {
            return;
        }

        $query->groupStart()
            ->where('registration_source !=', 'kiosk')
            ->orWhere('registration_source IS NULL', null, false)
            ->orWhere('client_id IS NULL', null, false)
            ->orWhereNotIn('client_id', array_map('intval', $autoApprovalClientIds))
            ->groupEnd();
    }

    private function applyHostRequestScope($query): void
    {
        helper('role');
        if (! role_matches(session()->get('role'), 'host')) {
            return;
        }

        $refs = $this->currentHostRefs();
        if ($refs === []) {
            $query->where('1 = 0', null, false);
            return;
        }

        $query->groupStart();
        foreach ($refs as $idx => $ref) {
            if ($idx === 0) {
                $query->where('staff_id', $ref)->orWhere('invited_by', $ref);
            } else {
                $query->orWhere('staff_id', $ref)->orWhere('invited_by', $ref);
            }
        }
        $query->groupEnd();
    }

    private function currentHostRefs(): array
    {
        helper('role');
        if (! role_matches(session()->get('role'), 'host')) {
            return [];
        }

        return array_values(array_unique(array_filter([
            trim((string) session()->get('staff_id')),
            trim((string) session()->get('username')),
            trim((string) session()->get('full_name')),
            trim((string) session()->get('email')),
        ], static fn($v) => $v !== '')));
    }

    private function hostCanAccessInvitation(array $invitation): bool
    {
        helper('client_visibility');
        if (! empty($invitation['id']) && ! visibility_invitation_allowed((int) $invitation['id'])) {
            return false;
        }

        $refs = $this->currentHostRefs();
        if ($refs === []) {
            return true;
        }

        return in_array((string) ($invitation['staff_id'] ?? ''), $refs, true)
            || in_array((string) ($invitation['invited_by'] ?? ''), $refs, true);
    }

    /**
     * @param int[] $invitationIds
     * @return array<int, array>
     */
    private function loadEarliestSchedulesByInvitation(array $invitationIds): array
    {
        if ($invitationIds === []) {
            return [];
        }

        $schedules = $this->scheduleModel
            ->whereIn('invitation_id', $invitationIds)
            ->orderBy('date_from', 'ASC')
            ->findAll();

        $map = [];
        foreach ($schedules as $schedule) {
            $invitationId = (int) $schedule['invitation_id'];
            if (! isset($map[$invitationId])) {
                $map[$invitationId] = $schedule;
            }
        }

        return $map;
    }

    private function formatTimeUntilVisit(?array $schedule): string
    {
        if (! $schedule) {
            return '';
        }

        $visitTime = strtotime($schedule['date_from']);
        $diff = $visitTime - time();

        if ($diff <= 0) {
            return 'Now';
        }

        $hours = (int) floor($diff / 3600);
        $minutes = (int) floor(($diff % 3600) / 60);

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . 'm';
    }

    public function approve()
    {
        $json = $this->request->getJSON();
        $id = isset($json->id) ? (int) $json->id : 0;

        if ($id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request ID',
            ]);
        }

        $result = $this->approveInvitationById($id);

        return $this->response->setJSON($result);
    }

    /**
     * Approve multiple submitted requests in one action.
     */
    public function batchApprove()
    {
        $payload = $this->request->getJSON(true);
        $rawIds = $payload['ids'] ?? [];
        if (! is_array($rawIds) || $rawIds === []) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No requests selected',
            ]);
        }

        $ids = array_values(array_unique(array_filter(
            array_map(static fn ($v) => (int) $v, $rawIds),
            static fn ($id) => $id > 0
        )));

        if ($ids === []) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No valid request IDs',
            ]);
        }

        $approved = 0;
        $failed = [];
        foreach ($ids as $id) {
            $result = $this->approveInvitationById($id);
            if (! empty($result['success'])) {
                $approved++;
            } else {
                $failed[] = [
                    'id' => $id,
                    'message' => $result['message'] ?? 'Failed',
                ];
            }
        }

        $msg = $approved === 1
            ? '1 request approved successfully.'
            : $approved . ' requests approved successfully.';
        if ($failed !== []) {
            $msg .= ' ' . count($failed) . ' could not be approved (already processed or invalid).';
        }

        return $this->response->setJSON([
            'success' => $approved > 0,
            'approved' => $approved,
            'failed' => $failed,
            'message' => $msg,
        ]);
    }

    /**
     * @return array{success:bool,message:string}
     */
    private function approveInvitationById(int $id): array
    {
        $invitation = $this->invitationModel->find($id);
        if (! $invitation) {
            return ['success' => false, 'message' => 'Invitation not found'];
        }
        if (! $this->hostCanAccessInvitation($invitation)) {
            return ['success' => false, 'message' => 'You can only approve your own requests'];
        }

        return $this->approvalService->approve($id);
    }

    public function reject()
    {
        $json = $this->request->getJSON();
        $id = $json->id ?? null;
        $reason = $json->reason ?? '';

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request ID'
            ]);
        }

        try {
            $invitation = $this->invitationModel->find($id);

            if (!$invitation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invitation not found'
                ]);
            }

            if (! $this->hostCanAccessInvitation($invitation)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'You can only reject your own requests'
                ]);
            }

            if ($invitation['status'] === 'Rejected') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This request has already been rejected'
                ]);
            }

            if ($invitation['status'] === 'Approved') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This request has already been approved and cannot be rejected'
                ]);
            }

            if ($invitation['status'] !== 'Submitted') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Only submitted requests can be rejected (current status: ' . $invitation['status'] . ')'
                ]);
            }

            // Atomic update: only succeeds if status is still 'Submitted'
            $db = \Config\Database::connect();
            $db->table('invitations')
                ->where('id', $id)
                ->where('status', 'Submitted')
                ->update([
                    'status' => 'Rejected',
                    'other_reason' => $reason,
                    'version' => ($invitation['version'] ?? 1) + 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if ($db->affectedRows() === 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This request has already been processed by another user. Please refresh the page.'
                ]);
            }

            (new \App\Services\NotificationService())->dispatch($id, 'request_rejected');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Request rejected successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function pastVisits()
    {
        $json = $this->request->getJSON();
        $icPassport = $json->ic_passport ?? null;

        if (!$icPassport) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid IC/Passport number'
            ]);
        }

        try {
            // Fetch past visits for this visitor (exclude current pending/submitted ones)
            $visits = $this->invitationModel
                ->where('ic_passport', $icPassport)
                ->whereIn('status', ['Approved', 'Rejected'])
                ->orderBy('created_at', 'DESC')
                ->limit(10);
            $this->applyHostRequestScope($visits);
            $visits = $visits->findAll();

            $formattedVisits = [];
            foreach ($visits as $visit) {
                // Get schedule for visit date
                $schedule = $this->scheduleModel
                    ->where('invitation_id', $visit['id'])
                    ->first();

                $formattedVisits[] = [
                    'id' => $visit['id'],
                    'visit_date' => $schedule ? date('d M Y', strtotime($schedule['visit_date'])) : 'N/A',
                    'status' => $visit['status'],
                    'purpose' => $visit['purpose_of_visit'],
                    'host_name' => $visit['host_name'],
                    'company_visited' => $visit['company_visited'],
                    'checked_in_at' => $visit['checked_in_at'] ? date('d M Y H:i', strtotime($visit['checked_in_at'])) : null,
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'visits' => $formattedVisits
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Resolve kiosk host identifiers (for example, a staff number) to the
     * person's display name and contact number.
     */
    private function resolveHostDetails(array $invitation, int $fallbackClientId): array
    {
        $rawName = trim((string)($invitation['invited_by'] ?? ''));
        $staffId = trim((string)($invitation['staff_id'] ?? ''));
        $reference = $staffId !== '' ? $staffId : $rawName;
        $name = $rawName;
        $contact = trim((string)($invitation['host_contact'] ?? ''));

        if ($reference !== '') {
            $clientId = (int)($invitation['client_id'] ?? $fallbackClientId);
            $userQuery = (new \App\Models\UserModel())
                ->select('full_name, contact_no')
                ->where('is_active', 1)
                ->groupStart()
                    ->where('staff_id', $reference)
                    ->orWhere('username', $reference)
                    ->orWhere('full_name', $reference)
                ->groupEnd();

            if ($clientId > 0) {
                $userQuery->where('client_id', $clientId);
            }

            $hostUser = $userQuery->first();
            if ($hostUser) {
                $name = trim((string)($hostUser['full_name'] ?? '')) ?: $name;
                $contact = trim((string)($hostUser['contact_no'] ?? '')) ?: $contact;
            } else {
                $staff = (new \App\Models\StaffModel())
                    ->select('full_name, contact_number')
                    ->where('staff_no', $reference)
                    ->first();
                if ($staff) {
                    $name = trim((string)($staff['full_name'] ?? '')) ?: $name;
                    $contact = trim((string)($staff['contact_number'] ?? '')) ?: $contact;
                }
            }
        }

        return [
            'name' => $name !== '' ? $name : 'N/A',
            'contact' => $contact !== '' ? $contact : 'N/A',
        ];
    }

    private function resolveVisitedCompany(array $invitation, ?array $viewClient): string
    {
        $invitationClientId = (int)($invitation['client_id'] ?? 0);
        if ($invitationClientId > 0
            && (!$viewClient || (int)($viewClient['id'] ?? 0) !== $invitationClientId)) {
            $viewClient = (new \App\Models\ClientModel())->find($invitationClientId);
        }

        $company = trim((string)($viewClient['name'] ?? ''));
        if ($company === '') {
            $company = trim((string)($invitation['company_visited'] ?? ''));
        }

        return $company !== '' ? $company : 'N/A';
    }
}
