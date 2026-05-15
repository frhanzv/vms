<?php

namespace App\Controllers;

use App\Models\InvitationModel;
use App\Models\InvitationScheduleModel;
use App\Models\InvitationVisitorModel;
use App\Models\VisitorCardModel;
use App\Models\VisitorTypeModel;

class VisitorList extends BaseController
{
    protected $invitationModel;
    protected $invitationVisitorModel;
    protected $visitorCardModel;
    protected $invitationScheduleModel;

    public function __construct()
    {
        $this->invitationModel = new InvitationModel();
        $this->invitationVisitorModel = new InvitationVisitorModel();
        $this->visitorCardModel = new VisitorCardModel();
        $this->invitationScheduleModel = new InvitationScheduleModel();
    }

    public function index()
    {
        // Get all approved invitations with card details
        $db = \Config\Database::connect();
        $builder = $db->table('invitation_visitors iv');
        $baseSelect = 'iv.*, 
                          iv.version as iv_version,
                          i.full_name as visitor_name, 
                          i.ic_passport as visitor_ic_passport,
                          i.contact as visitor_contact,
                          i.company as visitor_company,
                          i.invited_by as host_name,
                          i.reason as visit_purpose,
                          i.vehicle_registration as vehicle_reg,
                          i.location,
                          i.profile_photo_path AS invitation_profile_photo_path,
                          i.facial_verification_image AS invitation_facial_verification_image,
                          i.registration_source,
                          i.created_at as invitation_created_at,
                          i.version as invitation_version,
                          sch.id as schedule_id,
                          sch.date_from as sch_date_from,
                          sch.date_to as sch_date_to,
                          vc.id as visitor_card_table_id,
                          vc.card_id as card_epc,
                          vc.status as card_status';
        if ($this->invitationsSupportVisitorType()) {
            $builder->select($baseSelect . ',
                          i.visitor_type_id,
                          vt.name as visitor_type_name');
            $builder->join('invitations i', 'i.id = iv.invitation_id');
            $builder->join('visitor_types vt', 'vt.id = i.visitor_type_id', 'left');
        } else {
            $builder->select($baseSelect);
            $builder->join('invitations i', 'i.id = iv.invitation_id');
        }
        $builder->join(
            '(SELECT invitation_id, MIN(id) AS id FROM invitation_schedules GROUP BY invitation_id) sch_pick',
            'sch_pick.invitation_id = i.id',
            'left'
        );
        $builder->join('invitation_schedules sch', 'sch.id = sch_pick.id', 'left');
        $builder->join('visitor_cards vc', 'vc.id = iv.visitor_card_id', 'left');
        $builder->where('i.status', 'Approved');

        // Optional free-text filter (used by the search box and the Read MyKad → IC auto-search).
        // Matches against full name, IC/passport (also normalized to strip dashes/spaces so a
        // scanned "020314-03-0050" still matches a stored "020314030050"), vehicle reg, and the
        // bound visitor card EPC (Visitor Pass No).
        $searchTerm = trim((string) ($this->request->getGet('search') ?? ''));
        if ($searchTerm !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $searchTerm) . '%';
            $normalized = preg_replace('/[\s\-]/', '', $searchTerm);
            if ($normalized === null) {
                $normalized = $searchTerm;
            }
            $likeNormalized = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $normalized) . '%';

            $builder->groupStart()
                ->like('i.full_name', $searchTerm, 'both', null, true)
                ->orLike('i.ic_passport', $searchTerm, 'both', null, true)
                ->orLike('i.vehicle_registration', $searchTerm, 'both', null, true)
                ->orLike('vc.card_id', $searchTerm, 'both', null, true);
            if ($normalized !== '' && $normalized !== $searchTerm) {
                $builder->orWhere(
                    "REPLACE(REPLACE(i.ic_passport, '-', ''), ' ', '') LIKE",
                    $likeNormalized,
                    false
                );
            }
            $builder->groupEnd();
        }

        $builder->orderBy('i.created_at', 'DESC');

        // Debug: Log the query
        $sql = $builder->getCompiledSelect(false);
        log_message('debug', 'Visitor List Query: ' . $sql);

        $results = $builder->get()->getResultArray();
        
        // Debug: Log results count
        log_message('debug', 'Visitor List Results Count: ' . count($results));
        
        // Also check total approved invitations
        $totalInvitations = $db->table('invitations')->where('status', 'Approved')->countAllResults();
        log_message('debug', 'Total Approved Invitations: ' . $totalInvitations);
        
        // Check invitation_visitors records
        $totalIV = $db->table('invitation_visitors')->countAllResults();
        log_message('debug', 'Total invitation_visitors Records: ' . $totalIV);

        // Count statistics
        $totalApproved = count($results);
        $checkedIn = 0;
        $withCard = 0;
        
        foreach ($results as $row) {
            if ($row['check_in_time']) {
                $checkedIn++;
            }
            if ($row['visitor_card_id']) {
                $withCard++;
            }
        }

        $pending = $this->invitationModel
            ->whereIn('status', ['pending', 'submitted'])
            ->countAllResults();

        // Format data for the view
        $visitors = [];
        foreach ($results as $index => $row) {
            $cardStatusBadge = '-';
            if ($row['visitor_card_id']) {
                if ($row['card_status'] === 'in_use') {
                    $cardStatusBadge = 'In Use';
                } elseif ($row['card_status'] === 'active') {
                    $cardStatusBadge = 'Active';
                } else {
                    $cardStatusBadge = ucfirst($row['card_status']);
                }
            }

            $dateSrc = ! empty($row['sch_date_from'])
                ? $row['sch_date_from']
                : ($row['invitation_created_at'] ?? $row['created_at']);
            $visitDateIso = date('Y-m-d', strtotime($dateSrc));

            $profilePath = $row['invitation_profile_photo_path'] ?? null;
            $facialPath  = $row['invitation_facial_verification_image'] ?? null;

            $visitors[] = [
                'id' => $row['id'],
                'invitation_id' => $row['invitation_id'],
                'schedule_id' => isset($row['schedule_id']) ? (int) $row['schedule_id'] : null,
                'no' => $index + 1,
                'date' => date('M j, Y', strtotime($dateSrc)),
                'full_name' => $row['visitor_name'],
                'ic_passport' => $row['visitor_ic_passport'] ?? '',
                'contact' => $row['visitor_contact'] ?? '',
                'company' => $row['visitor_company'] ?? '',
                'host' => $row['host_name'] ?? '',
                'invited_by' => $row['host_name'] ?? '',
                'visit_purpose' => $row['visit_purpose'] ?? '',
                'reason' => $row['visit_purpose'] ?? '',
                'vehicle_reg' => $row['vehicle_reg'] ?? '',
                'location' => $row['location'] ?? '',
                'type' => $row['registration_source'] ?? 'Walk-In',
                'visitor_type' => ! empty($row['visitor_type_name'] ?? '') ? $row['visitor_type_name'] : '-',
                'visitor_type_id' => isset($row['visitor_type_id']) ? $row['visitor_type_id'] : null,
                'pass_no' => $row['card_epc'] ?? '',
                'card_epc' => $row['card_epc'] ?? null,
                'card_id' => $row['visitor_card_id'],
                'visitor_card_table_id' => $row['visitor_card_table_id'] ?? null,
                'card_status' => $cardStatusBadge,
                'card_status_raw' => $row['card_status'] ?? null,
                'checked_in' => $row['check_in_time'] ? true : false,
                'check_in_time' => $row['check_in_time'],
                'check_out_time' => $row['check_out_time'],
                'visit_date_iso' => $visitDateIso,
                'date_from' => $row['sch_date_from'] ?? '',
                'date_to' => $row['sch_date_to'] ?? '',
                'iv_version' => (int) ($row['iv_version'] ?? 1),
                'invitation_version' => (int) ($row['invitation_version'] ?? 1),
                'profile_photo_path' => $profilePath,
                'facial_verification_image' => $facialPath,
                'profile_photo_url' => $this->invitationUploadPublicUrl($profilePath),
                'facial_verification_url' => $this->invitationUploadPublicUrl($facialPath),
            ];
        }

        // Get available visitor cards for binding
        $availableCards = $this->visitorCardModel
            ->where('status', 'active')
            ->orderBy('card_id', 'ASC')
            ->findAll();

        $visitorTypes = [];
        if ($this->invitationsSupportVisitorType()) {
            $visitorTypes = (new VisitorTypeModel())->orderBy('name', 'ASC')->findAll();
        }

        $data = [
            'pageTitle' => 'Visitor Past List - SafeG',
            'stats' => [
                'total' => $totalApproved,
                'checkedIn' => $checkedIn,
                'withCard' => $withCard,
                'pending' => $pending
            ],
            'visitors' => $visitors,
            'availableCards' => $availableCards,
            'visitorTypes' => $visitorTypes,
            'showVisitorTypes' => $this->invitationsSupportVisitorType(),
            'searchTerm' => $searchTerm,
        ];

        return view('visitors/list', $data);
    }

    public function export()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('invitation_visitors iv');
        $baseSelect = 'iv.*, 
                          i.full_name as visitor_name, 
                          i.ic_passport as visitor_ic_passport,
                          i.contact as visitor_contact,
                          i.company as visitor_company,
                          i.invited_by as host_name,
                          i.reason as visit_purpose,
                          i.vehicle_registration as vehicle_reg,
                          i.location,
                          i.registration_source,
                          i.created_at as invitation_created_at,
                          sch.date_from as sch_date_from,
                          sch.date_to as sch_date_to,
                          vc.card_id as card_epc,
                          vc.status as card_status';

        if ($this->invitationsSupportVisitorType()) {
            $builder->select($baseSelect . ',
                          vt.name as visitor_type_name');
            $builder->join('invitations i', 'i.id = iv.invitation_id');
            $builder->join('visitor_types vt', 'vt.id = i.visitor_type_id', 'left');
        } else {
            $builder->select($baseSelect);
            $builder->join('invitations i', 'i.id = iv.invitation_id');
        }

        $builder->join(
            '(SELECT invitation_id, MIN(id) AS id FROM invitation_schedules GROUP BY invitation_id) sch_pick',
            'sch_pick.invitation_id = i.id',
            'left'
        );
        $builder->join('invitation_schedules sch', 'sch.id = sch_pick.id', 'left');
        $builder->join('visitor_cards vc', 'vc.id = iv.visitor_card_id', 'left');
        $builder->where('i.status', 'Approved');
        $builder->orderBy('i.created_at', 'DESC');

        $rows = $builder->get()->getResultArray();

        $handle = fopen('php://temp', 'w+');
        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, [
            'No',
            'Date',
            'Full Name',
            'IC/Passport',
            'Contact',
            'Company',
            'Vehicle Registration',
            'Location',
            'Visitor Type',
            'Type',
            'Card Status',
            'Visitor Pass No',
            'Reason',
            'Check In Time',
            'Check Out Time',
        ]);

        foreach ($rows as $index => $row) {
            $dateSrc = ! empty($row['sch_date_from'])
                ? $row['sch_date_from']
                : ($row['invitation_created_at'] ?? $row['created_at'] ?? null);

            $cardStatus = '-';
            if (! empty($row['card_status'])) {
                if ($row['card_status'] === 'in_use') {
                    $cardStatus = 'In Use';
                } elseif ($row['card_status'] === 'active') {
                    $cardStatus = 'Active';
                } else {
                    $cardStatus = ucfirst((string) $row['card_status']);
                }
            }

            fputcsv($handle, [
                $index + 1,
                $dateSrc ? date('M j, Y', strtotime((string) $dateSrc)) : '',
                $row['visitor_name'] ?? '',
                $row['visitor_ic_passport'] ?? '',
                $row['visitor_contact'] ?? '',
                $row['visitor_company'] ?? '',
                $row['vehicle_reg'] ?? '',
                $row['location'] ?? '',
                $row['visitor_type_name'] ?? '-',
                $row['registration_source'] ?? 'Walk-In',
                $cardStatus,
                $row['card_epc'] ?? '',
                $row['visit_purpose'] ?? '',
                ! empty($row['check_in_time']) ? date('M j, Y g:i A', strtotime((string) $row['check_in_time'])) : '',
                ! empty($row['check_out_time']) ? date('M j, Y g:i A', strtotime((string) $row['check_out_time'])) : '',
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="visitors-' . date('Y-m-d-His') . '.csv"')
            ->setBody((string) $csvContent);
    }

    /**
     * Update invitation visitor record and related invitation / schedule / card fields.
     * Uses optimistic locking via version column to prevent lost updates.
     */
    public function updateVisitor()
    {
        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $invitationVisitorId = (int) ($payload['invitation_visitor_id'] ?? 0);
        if ($invitationVisitorId < 1) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid visitor ID']);
        }

        $iv = $this->invitationVisitorModel->find($invitationVisitorId);
        if (! $iv) {
            return $this->response->setJSON(['success' => false, 'message' => 'Visitor record not found']);
        }

        $invitation = $this->invitationModel->find($iv['invitation_id']);
        if (! $invitation || $invitation['status'] !== 'Approved') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invitation not found or not approved']);
        }

        $fullName = trim((string) ($payload['full_name'] ?? ''));
        $icPassport = trim((string) ($payload['ic_passport'] ?? ''));
        $contact = trim((string) ($payload['contact'] ?? ''));
        $reason = trim((string) ($payload['reason'] ?? ''));

        if ($fullName === '' || $contact === '' || $reason === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Full name, contact, and reason are required']);
        }

        $vehicleReg = trim((string) ($payload['vehicle_reg'] ?? ''));
        $location = trim((string) ($payload['location'] ?? ''));
        $company = trim((string) ($payload['company'] ?? ''));
        $invitedBy = trim((string) ($payload['invited_by'] ?? ''));
        $visitDateIso = trim((string) ($payload['visit_date_iso'] ?? ''));

        // Version from the client (what they last read)
        $invitationVersion = (int) ($payload['invitation_version'] ?? $invitation['version'] ?? 1);
        $ivVersion = (int) ($payload['iv_version'] ?? $iv['version'] ?? 1);

        $invitationUpdate = [
            'full_name' => $fullName,
            'ic_passport' => $icPassport,
            'contact' => $contact,
            'vehicle_registration' => $vehicleReg !== '' ? $vehicleReg : null,
            'location' => $location !== '' ? $location : null,
            'company' => $company !== '' ? $company : null,
            'invited_by' => $invitedBy !== '' ? $invitedBy : null,
            'reason' => $reason,
        ];

        if ($this->invitationsSupportVisitorType()) {
            $vtId = $payload['visitor_type_id'] ?? null;
            if ($vtId === '' || $vtId === null) {
                $invitationUpdate['visitor_type_id'] = null;
            } else {
                $invitationUpdate['visitor_type_id'] = (int) $vtId;
            }
        }

        try {
            $this->invitationModel->skipValidation(true);
            $this->invitationModel->updateWithLock($invitation['id'], $invitationUpdate, $invitationVersion, 'invitation');
        } catch (\App\Exceptions\ConcurrencyException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This visitor record has been modified by another user. Please refresh and try again.'
            ]);
        }

        $checkIn = $this->normalizeDateTimeInput($payload['check_in_time'] ?? null);
        $checkOut = $this->normalizeDateTimeInput($payload['check_out_time'] ?? null);

        // Detect manual changes for logging
        $db = \Config\Database::connect();
        if ($checkIn && (!$iv['check_in_time'] || strtotime($checkIn) !== strtotime($iv['check_in_time']))) {
            $db->table('visitor_card_logs')->insert([
                'visitor_card_id' => $iv['visitor_card_id'] ?? null,
                'invitation_id'   => $iv['invitation_id'],
                'action'          => 'checkin',
                'lane_id'         => null,
                'scan_source'     => 'manual_edit',
                'scanned_at'      => $checkIn,
                'created_at'      => date('Y-m-d H:i:s'),
            ]);
        }
        if ($checkOut && (!$iv['check_out_time'] || strtotime($checkOut) !== strtotime($iv['check_out_time']))) {
            $db->table('visitor_card_logs')->insert([
                'visitor_card_id' => $iv['visitor_card_id'] ?? null,
                'invitation_id'   => $iv['invitation_id'],
                'action'          => 'checkout',
                'lane_id'         => null,
                'scan_source'     => 'manual_edit',
                'scanned_at'      => $checkOut,
                'created_at'      => date('Y-m-d H:i:s'),
            ]);
        }

        $ivUpdate = [
            'full_name' => $fullName,
            'ic_passport' => $icPassport,
            'contact' => $contact,
            'vehicle_registration' => $vehicleReg !== '' ? $vehicleReg : null,
            'company' => $company !== '' ? $company : null,
            'check_in_time' => $checkIn,
            'check_out_time' => $checkOut,
        ];

        try {
            $this->invitationVisitorModel->skipValidation(true);
            $this->invitationVisitorModel->updateWithLock($invitationVisitorId, $ivUpdate, $ivVersion, 'visitor');
        } catch (\App\Exceptions\ConcurrencyException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This visitor record has been modified by another user. Please refresh and try again.'
            ]);
        }

        if ($visitDateIso !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $visitDateIso)) {
            $scheduleId = isset($payload['schedule_id']) ? (int) $payload['schedule_id'] : 0;
            $from = $visitDateIso . ' 00:00:00';
            $to = $visitDateIso . ' 23:59:59';
            $this->invitationScheduleModel->skipValidation(true);
            if ($scheduleId > 0) {
                $existing = $this->invitationScheduleModel->find($scheduleId);
                if ($existing && (int) $existing['invitation_id'] === (int) $invitation['id']) {
                    $this->invitationScheduleModel->update($scheduleId, [
                        'date_from' => $from,
                        'date_to' => $to,
                    ]);
                }
            } else {
                $this->invitationScheduleModel->insert([
                    'invitation_id' => $invitation['id'],
                    'date_from' => $from,
                    'date_to' => $to,
                ]);
            }
        }

        $cardTableId = isset($iv['visitor_card_id']) ? (int) $iv['visitor_card_id'] : 0;
        if ($cardTableId > 0) {
            $newEpc = trim((string) ($payload['pass_no'] ?? ''));
            $newCardStatus = trim((string) ($payload['card_status_raw'] ?? ''));
            $allowedCardStatus = ['active', 'in_use', 'lost', 'inactive'];
            $cardPatch = [];
            if ($newEpc !== '') {
                $cardPatch['card_id'] = $newEpc;
            }
            if ($newCardStatus !== '' && in_array($newCardStatus, $allowedCardStatus, true)) {
                $cardPatch['status'] = $newCardStatus;
            }
            if ($cardPatch !== []) {
                $this->visitorCardModel->skipValidation(true);
                $this->visitorCardModel->update($cardTableId, $cardPatch);
            }
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Visitor updated successfully']);
    }

    public function updateVisitDate()
    {
        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $invitationId = (int) ($payload['invitation_id'] ?? 0);
        $scheduleId = (int) ($payload['schedule_id'] ?? 0);
        
        $dateFrom = trim((string) ($payload['date_from'] ?? ''));
        $dateTo = trim((string) ($payload['date_to'] ?? ''));

        if ($invitationId < 1 || $dateFrom === '' || $dateTo === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required fields']);
        }

        $dateFrom = date('Y-m-d H:i:s', strtotime($dateFrom));
        $dateTo = date('Y-m-d H:i:s', strtotime($dateTo));

        $this->invitationScheduleModel->skipValidation(true);
        if ($scheduleId > 0) {
            $existing = $this->invitationScheduleModel->find($scheduleId);
            if ($existing && (int) $existing['invitation_id'] === $invitationId) {
                $this->invitationScheduleModel->update($scheduleId, [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Schedule record not found']);
            }
        } else {
            $this->invitationScheduleModel->insert([
                'invitation_id' => $invitationId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ]);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Visit date updated successfully']);
    }

    private function normalizeDateTimeInput($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $s = trim((string) $value);
        if ($s === '') {
            return null;
        }
        $ts = strtotime($s);

        return $ts ? date('Y-m-d H:i:s', $ts) : null;
    }

    /**
     * Absolute URL for invitation files served under /uploads/{folder}/{file}
     * (FileServer + public/uploads). Keeps visitor list photos correct regardless of JS base URL.
     */
    private function invitationUploadPublicUrl(?string $relativePath): ?string
    {
        if ($relativePath === null || $relativePath === '') {
            return null;
        }
        $relativePath = str_replace('\\', '/', trim($relativePath));
        if ($relativePath === '') {
            return null;
        }

        return base_url('uploads/' . ltrim($relativePath, '/'));
    }

    /**
     * Bind visitor card to invitation visitor.
     * Uses a transaction to atomically check availability and assign.
     */
    public function bindCard()
    {
        $json = $this->request->getJSON();
        $invitationVisitorId = $json->invitation_visitor_id ?? null;
        $cardId = $json->card_id ?? null;
        $newCardEpc = $json->new_card_epc ?? null;

        if (!$invitationVisitorId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid visitor ID'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // If new EPC is provided, create a new card
            if ($newCardEpc) {
                $existingCard = $this->visitorCardModel->where('card_id', $newCardEpc)->first();
                
                if ($existingCard) {
                    $cardId = $existingCard['id'];
                    
                    if ($existingCard['status'] !== 'active') {
                        $db->transRollback();
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'This card exists but is not available (status: ' . $existingCard['status'] . ')'
                        ]);
                    }
                } else {
                    $newCardId = $this->visitorCardModel->insert([
                        'card_id' => $newCardEpc,
                        'status' => 'active',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    if (!$newCardId) {
                        $db->transRollback();
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Failed to create new card'
                        ]);
                    }
                    
                    $cardId = $newCardId;
                }
            }

            if (!$cardId) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please select a card or enter a new EPC number'
                ]);
            }

            // Lock the card row to prevent concurrent binding
            $card = $db->query(
                'SELECT * FROM visitor_cards WHERE id = ? FOR UPDATE',
                [$cardId]
            )->getRowArray();

            if (!$card || $card['status'] !== 'active') {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Card is not available (it may have just been assigned to someone else)'
                ]);
            }

            // Check visitor hasn't already got a card bound (by another concurrent request)
            $iv = $db->query(
                'SELECT * FROM invitation_visitors WHERE id = ? FOR UPDATE',
                [$invitationVisitorId]
            )->getRowArray();

            if (!$iv) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Visitor record not found'
                ]);
            }

            if ($iv['visitor_card_id'] && (int)$iv['visitor_card_id'] !== (int)$cardId) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'A card has already been assigned to this visitor by another user. Please refresh.'
                ]);
            }

            // Check card isn't assigned to another visitor today
            $existingAssignment = $db->table('invitation_visitors iv')
                ->join('invitations i', 'i.id = iv.invitation_id')
                ->where('iv.visitor_card_id', $cardId)
                ->where('iv.id !=', $invitationVisitorId)
                ->where('iv.check_out_time IS NULL')
                ->where('DATE(i.created_at)', date('Y-m-d'))
                ->get()
                ->getRowArray();

            if ($existingAssignment) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Card is currently assigned to another visitor'
                ]);
            }

            $this->invitationVisitorModel->update($invitationVisitorId, [
                'visitor_card_id' => $cardId
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to bind card due to a database error'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Card bound successfully',
                'card' => $card
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'bindCard error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Unbind visitor card from invitation visitor.
     * Uses atomic check to prevent double-unbind.
     *
     * @return array{success: bool, message: string}
     */
    protected function performUnbindCard(int $invitationVisitorId): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $invitationVisitor = $db->query(
                'SELECT * FROM invitation_visitors WHERE id = ? FOR UPDATE',
                [$invitationVisitorId]
            )->getRowArray();

            if (! $invitationVisitor || ! $invitationVisitor['visitor_card_id']) {
                $db->transRollback();

                return [
                    'success' => false,
                    'message' => 'No card is currently assigned to this visitor',
                ];
            }

            $cardId = $invitationVisitor['visitor_card_id'];

            $this->invitationVisitorModel->update($invitationVisitorId, [
                'visitor_card_id' => null,
            ]);

            $this->visitorCardModel->update($cardId, [
                'status' => 'active',
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Failed to unbind card due to a database error',
                ];
            }

            return [
                'success' => true,
                'message' => 'Card unbound successfully',
            ];
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'unbindCard error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Unbind visitor card from invitation visitor.
     * Uses atomic check to prevent double-unbind.
     */
    public function unbindCard()
    {
        $invitationVisitorId = $this->request->getPost('invitation_visitor_id');
        if (! $invitationVisitorId) {
            $json = $this->request->getJSON(true);
            if (is_array($json)) {
                $invitationVisitorId = $json['invitation_visitor_id'] ?? null;
            }
        }

        if (! $invitationVisitorId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request parameter',
            ]);
        }

        $result = $this->performUnbindCard((int) $invitationVisitorId);

        return $this->response->setJSON($result);
    }

    /**
     * Return cards for multiple invitation_visitors (batch unbind).
     */
    public function batchUnbindCards()
    {
        $json = $this->request->getJSON(true);
        $ids = is_array($json) ? ($json['invitation_visitor_ids'] ?? []) : [];

        if (! is_array($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn ($id) => $id > 0)));

        if ($ids === []) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No visitors selected',
            ]);
        }

        $returned = 0;
        $failed = [];

        foreach ($ids as $id) {
            $r = $this->performUnbindCard($id);
            if ($r['success']) {
                $returned++;
            } else {
                $failed[] = [
                    'invitation_visitor_id' => $id,
                    'message' => $r['message'],
                ];
            }
        }

        if ($returned === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $failed[0]['message'] ?? 'Could not return cards for selected visitors',
                'returned_count' => 0,
                'failed' => $failed,
            ]);
        }

        $msg = $returned === 1
            ? '1 card returned successfully'
            : sprintf('%d cards returned successfully', $returned);

        if ($failed !== []) {
            $msg .= sprintf(' (%d skipped)', count($failed));
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $msg,
            'returned_count' => $returned,
            'failed' => $failed,
        ]);
    }

    /**
     * Generate QR Code for a specific visitor
     */
    public function generateQr($invitationId)
    {
        $invitation = $this->invitationModel->find((int) $invitationId);
        $passId     = 'VIS-' . (int) $invitationId;
        $qrCodeData = $passId;

        if (is_array($invitation)) {
            $icPassport = trim((string) ($invitation['ic_passport'] ?? ''));
            if ($icPassport !== '') {
                $qrCodeData = $icPassport;
            }
        }

        $options = new \chillerlan\QRCode\QROptions([
            'outputInterface' => \chillerlan\QRCode\Output\QRGdImagePNG::class,
            'eccLevel'        => \chillerlan\QRCode\Common\EccLevel::L,
            'scale'           => 5,
            'outputBase64'    => false,
        ]);
        try {
            $qrcode = new \chillerlan\QRCode\QRCode($options);
            $output = $qrcode->render($qrCodeData);
            return $this->response->setHeader('Content-Type', 'image/png')->setBody($output);
        } catch (\Exception $e) {
            log_message('error', 'generateQr failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500);
        }
    }
}
