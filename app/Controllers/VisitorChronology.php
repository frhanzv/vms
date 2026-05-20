<?php

namespace App\Controllers;

use App\Models\SubLocationModel;

class VisitorChronology extends BaseController
{
    protected $subLocationModel;

    public function __construct()
    {
        $this->subLocationModel = new SubLocationModel();
    }

    public function index()
    {
        return view('reports/access_chronology', [
            'pageTitle'    => 'Visitor Details - SafeG',
            'subLocations' => $this->subLocationModel->getAllActive(),
        ]);
    }

    public function generate()
    {
        $fromRaw       = $this->request->getPost('from_datetime');
        $toRaw         = $this->request->getPost('to_datetime');
        $subLocationId = $this->request->getPost('sub_location_id');
        $invitationId  = $this->request->getPost('invitation_id');
        $searchBy      = $this->request->getPost('search_by');
        $searchTerm    = trim((string) $this->request->getPost('search_term'));

        $normalizedSearchTerm = $this->normalizeIdentitySearchTerm($searchTerm);

        if (empty($fromRaw) || empty($toRaw)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Date range is required.']);
        }

        $fromTs = strtotime(str_replace('T', ' ', $fromRaw));
        $toTs   = strtotime(str_replace('T', ' ', $toRaw));
        if ($fromTs === false || $toTs === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid date or time.']);
        }
        $fromDatetime = date('Y-m-d H:i:s', $fromTs);
        $toDatetime   = date('Y-m-d H:i:s', $toTs);

        $hasInvitation = !empty($invitationId);
        $hasSearch     = $searchTerm !== '';

        if (! $hasInvitation && ! $hasSearch) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enter IC number or visitor name.']);
        }

        if ($hasSearch && $normalizedSearchTerm === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Enter a valid IC or visitor name.']);
        }

        $db = db_connect();

        // Resolve lane IDs from sub-location's parent location
        $laneIds      = [];
        $locationName = 'All Locations';
        if ($subLocationId !== null && $subLocationId !== '') {
            $subLoc = $this->subLocationModel->find((int) $subLocationId);
            if ($subLoc) {
                $locationName = $subLoc['name'];
                $laneRows     = $db->query(
                    "SELECT id FROM lanes WHERE location_id = ? AND status = 'active'",
                    [(int) $subLoc['location_id']]
                )->getResultArray();
                $laneIds = array_column($laneRows, 'id');
            } else {
                $locationName = 'Unknown Location';
            }
        }

        // 1. Grouped Visitor Summary
        $whereGrouped  = ['i.id IS NOT NULL'];
        $joinGrouped   = ['vcl.invitation_id = i.id', 'vcl.scanned_at >= ?', 'vcl.scanned_at <= ?'];
        $paramsGrouped = [$fromDatetime, $toDatetime];

        if (!empty($laneIds)) {
            $laneInList    = implode(',', array_map('intval', $laneIds));
            $joinGrouped[] = "vcl.lane_id IN ({$laneInList})";
        }

        $identitySqlExpr = $searchBy === 'name'
            ? "LOWER(REPLACE(REPLACE(REPLACE(TRIM(COALESCE(i.full_name,'')), '-', ''), ' ', ''), '_', ''))"
            : "LOWER(REPLACE(REPLACE(REPLACE(TRIM(COALESCE(i.ic_passport,'')), '-', ''), ' ', ''), '_', ''))";

        if ($hasInvitation) {
            $whereGrouped[] = 'i.id = ?';
            $paramsGrouped[] = (int)$invitationId;
        } else {
            $whereGrouped[] = "{$identitySqlExpr} = ?";
            $paramsGrouped[] = strtolower($normalizedSearchTerm);
        }

        $sqlGrouped = "SELECT 
                        i.id AS invitation_id,
                        i.full_name AS visitor_name,
                        i.ic_passport AS ic_no,
                        i.company AS visitor_company,
                        i.contact AS contact_no,
                        i.reason AS visit_reason,
                        i.staff_id,
                        i.invited_by AS person_visited,
                        i.updated_at,
                        MIN(vcl.scanned_at) AS visit_from,
                        MAX(vcl.scanned_at) AS visit_to,
                        COALESCE(
                            (
                                SELECT COALESCE(sl_via_lane.name, sl_via_direct.name)
                                FROM visitor_card_logs vcl_last
                                LEFT JOIN lanes la_last               ON la_last.id               = vcl_last.lane_id
                                LEFT JOIN sub_locations sl_via_lane   ON sl_via_lane.location_id   = la_last.location_id
                                LEFT JOIN sub_locations sl_via_direct ON sl_via_direct.id           = vcl_last.sub_location_id
                                WHERE vcl_last.invitation_id = i.id
                                  AND vcl_last.action != 'assigned'
                                  AND COALESCE(sl_via_lane.name, sl_via_direct.name) IS NOT NULL
                                ORDER BY vcl_last.scanned_at DESC, vcl_last.id DESC
                                LIMIT 1
                            ),
                            NULLIF(i.location, ''),
                            'N/A'
                        ) AS i_location,
                        (SELECT MIN(iv.check_in_time) FROM invitation_visitors iv WHERE iv.invitation_id = i.id) AS reg_check_in,
                        (SELECT MAX(iv.check_out_time) FROM invitation_visitors iv WHERE iv.invitation_id = i.id) AS reg_check_out,
                        (SELECT MAX(s.date_to) FROM invitation_schedules s WHERE s.invitation_id = i.id) AS schedule_end
                       FROM invitations i
                       LEFT JOIN visitor_card_logs vcl ON " . implode(' AND ', $joinGrouped) . "
                       LEFT JOIN lanes la ON la.id = vcl.lane_id
                       LEFT JOIN sub_locations sl ON sl.location_id = la.location_id
                       WHERE " . implode(' AND ', $whereGrouped) . "
                       GROUP BY i.id";
        
        $visitorData = $db->query($sqlGrouped, $paramsGrouped)->getResultArray();
        
        $formattedVisitors = [];
        foreach ($visitorData as $v) {
            $scanFrom = $v['visit_from'] ?? null;
            $scanTo   = $v['visit_to'] ?? null;
            $regIn    = $v['reg_check_in'] ?? null;
            $regOut   = $v['reg_check_out'] ?? null;
            $sameScan = $scanFrom && $scanTo && strtotime((string) $scanFrom) === strtotime((string) $scanTo);

            // Match dashboard / visitor list: invitation_visitors is authoritative for on-site vs checked out.
            if ($regIn && ! $regOut) {
                $status = 'Checked In';
            } elseif ($regOut) {
                $status = 'Checked Out';
            } elseif ($scanFrom && $sameScan) {
                $status = 'Checked In';
            } elseif ($scanFrom && $scanTo && ! $sameScan) {
                $status = 'Checked Out';
            } elseif ($scanFrom) {
                $status = 'Checked In';
            } else {
                $status = '-';
            }

            $visitFromDisp = $scanFrom
                ? date('M j, Y g:i A', strtotime((string) $scanFrom))
                : ($regIn ? date('M j, Y g:i A', strtotime((string) $regIn)) : '-');

            if ($regOut) {
                $visitToDisp = date('M j, Y g:i A', strtotime((string) $regOut));
            } elseif ($status === 'Checked In') {
                $visitToDisp = $scanTo ? date('M j, Y g:i A', strtotime((string) $scanTo)) : '—';
            } else {
                $visitToDisp = $scanTo ? date('M j, Y g:i A', strtotime((string) $scanTo)) : '-';
            }

            $duration = '-';
            $isOverstay = false;
            $overstaySeconds = 0;
            
            $schedEnd = $v['schedule_end'] ? strtotime((string) $v['schedule_end']) : null;
            $nowTs = time();

            if ($regIn) {
                $endTs = $regOut ? strtotime((string) $regOut) : $nowTs;
                $diff  = max(0, $endTs - strtotime((string) $regIn));
                
                // Dashboard logic: if currently on-site and past schedule, show overstay relative to schedule end
                if (!$regOut && $schedEnd && $nowTs > $schedEnd) {
                    $isOverstay = true;
                    $overstaySeconds = $nowTs - $schedEnd;
                    $duration = '+' . $this->formatDuration($overstaySeconds);
                } else {
                    $duration = $this->formatDuration($diff) . ($regOut ? '' : ' (ongoing)');
                }
            } elseif ($scanFrom && $scanTo && ! $sameScan) {
                $diff = max(0, strtotime((string) $scanTo) - strtotime((string) $scanFrom));
                $duration = $this->formatDuration($diff);
            } elseif ($scanFrom && $sameScan && $status === 'Checked In') {
                if ($schedEnd && $nowTs > $schedEnd) {
                    $isOverstay = true;
                    $overstaySeconds = $nowTs - $schedEnd;
                    $duration = '+' . $this->formatDuration($overstaySeconds);
                } else {
                    $diff = max(0, $nowTs - strtotime((string) $scanFrom));
                    $duration = $this->formatDuration($diff) . ' (ongoing)';
                }
            }

            $formattedVisitors[] = [
                'invitation_id'   => $v['invitation_id'],
                'visitor_name'    => $v['visitor_name'] ?? 'N/A',
                'ic_no'           => $v['ic_no'] ?? 'N/A',
                'visitor_company' => $v['visitor_company'] ?? 'N/A',
                'contact_no'      => $v['contact_no'] ?? 'N/A',
                'visit_reason'    => $v['visit_reason'] ?? 'N/A',
                'staff_id'        => $v['staff_id'] ?? 'N/A',
                'person_visited'  => $v['person_visited'] ?? 'N/A',
                'visit_from'      => $visitFromDisp,
                'visit_to'        => $visitToDisp,
                'visit_duration'  => $duration,
                'status'          => $status,
                'location'        => $v['i_location'] ?? 'N/A',
                'last_updated'    => date('M j, Y g:i A', strtotime($v['updated_at'] ?? 'now')),
                'search_type'     => 'Auto Detect'
            ];
        }

        // 2. Full Chronology
        $whereChron  = ['vcl.scanned_at >= ?', 'vcl.scanned_at <= ?', 'i.id IS NOT NULL'];
        $paramsChron = [$fromDatetime, $toDatetime];

        if (!empty($laneIds)) {
            $laneInList   = implode(',', array_map('intval', $laneIds));
            $whereChron[] = "vcl.lane_id IN ({$laneInList})";
        }

        if ($hasInvitation) {
            $whereChron[] = 'i.id = ?';
            $paramsChron[] = (int)$invitationId;
        } else {
            $whereChron[] = "{$identitySqlExpr} = ?";
            $paramsChron[] = strtolower($normalizedSearchTerm);
        }

        $sqlChron = 'SELECT i.id AS invitation_id,
                       i.full_name AS visitor_name,
                       vcl.scanned_at AS access_time,
                       COALESCE(sl_lane.name, sl_direct.name) AS lane_name,
                       loc.branch,
                       loc.location_access
                FROM visitor_card_logs vcl
                LEFT JOIN lanes la                ON la.id              = vcl.lane_id
                LEFT JOIN sub_locations sl_lane   ON sl_lane.location_id   = la.location_id
                LEFT JOIN sub_locations sl_direct ON sl_direct.id = vcl.sub_location_id
                LEFT JOIN locations loc           ON loc.id = COALESCE(la.location_id, sl_direct.location_id)
                LEFT JOIN invitations i ON i.id = vcl.invitation_id
                WHERE ' . implode(' AND ', $whereChron) . '
                  AND vcl.action != \'assigned\'
                ORDER BY vcl.scanned_at ASC';

        $chronologyData = $db->query($sqlChron, $paramsChron)->getResultArray();

        $formattedChron = [];
        foreach ($chronologyData as $row) {
            $formattedChron[] = [
                'visitor_name'    => $row['visitor_name'] ?? 'N/A',
                'access_time'     => ! empty($row['access_time']) ? date('M j, Y g:i A', strtotime($row['access_time'])) : '-',
                'location_detail' => ($row['lane_name'] ?? 'N/A'),
            ];
        }

        return $this->response->setJSON([
            'success'       => true,
            'location_name' => $locationName,
            'from_datetime' => date('M j, Y g:i A', strtotime($fromDatetime)),
            'to_datetime'   => date('M j, Y g:i A', strtotime($toDatetime)),
            'visitors'      => $formattedVisitors,
            'chronology'    => $formattedChron,
        ]);
    }

    public function details($id)
    {
        $db = db_connect();
        
        $sql = "SELECT 
                    i.*,
                    MIN(vcl.scanned_at) AS visit_from,
                    MAX(vcl.scanned_at) AS visit_to,
                    (SELECT MIN(iv.check_in_time) FROM invitation_visitors iv WHERE iv.invitation_id = i.id) AS reg_check_in,
                    (SELECT MAX(iv.check_out_time) FROM invitation_visitors iv WHERE iv.invitation_id = i.id) AS reg_check_out
                FROM invitations i
                LEFT JOIN visitor_card_logs vcl ON vcl.invitation_id = i.id
                WHERE i.id = ?
                GROUP BY i.id";
        
        $visitor = $db->query($sql, [(int)$id])->getRowArray();
        
        if (!$visitor) {
            return "Visitor not found.";
        }

        $ivRow = [
            'check_in_time'  => $visitor['reg_check_in'] ?? null,
            'check_out_time' => $visitor['reg_check_out'] ?? null,
        ];

        $duration = '-';
        if ($ivRow['check_in_time']) {
            $secs = $this->secondsFromRegistrationWindow($ivRow);
            $duration = $this->formatDuration($secs) . ($ivRow['check_out_time'] ? '' : ' (ongoing)');
        } else {
            $scanFrom = $visitor['visit_from'] ?? null;
            $scanTo   = $visitor['visit_to'] ?? null;
            $sameScan = $scanFrom && $scanTo && strtotime((string) $scanFrom) === strtotime((string) $scanTo);
            if ($scanFrom && $scanTo && ! $sameScan) {
                $diff = max(0, strtotime((string) $scanTo) - strtotime((string) $scanFrom));
                $duration = $this->formatDuration($diff);
            } elseif ($scanFrom && $sameScan) {
                $duration = $this->formatDuration(max(0, time() - strtotime((string) $scanFrom))) . ' (ongoing)';
            }
        }

        $statusText = 'Currently OUT of Building';
        if ($ivRow['check_in_time'] && ! $ivRow['check_out_time']) {
            $statusText = 'Currently IN Building';
        } elseif ($ivRow['check_out_time']) {
            $statusText = 'Checked out (registration)';
        } elseif (($visitor['visit_from'] ?? null) && ($visitor['visit_to'] ?? null)
            && strtotime((string) $visitor['visit_from']) !== strtotime((string) $visitor['visit_to'])) {
            $statusText = 'Currently OUT of Building';
        } elseif ($visitor['visit_from'] ?? null) {
            $statusText = 'Currently IN Building';
        }

        $data = [
            'visitor' => $visitor,
            'visit_duration' => $duration,
            'status_text' => $statusText,
            'generated_at' => date('M j, Y g:i A')
        ];

        return view('reports/visitor_details_print', $data);
    }

    /**
     * Get detailed movement history and statistics for a specific visitor (Invitation)
     */
    public function movementTimeline()
    {
        $invitationId = $this->request->getPost('invitation_id');
        if (empty($invitationId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invitation ID is required.']);
        }

        $db = db_connect();

        // 1. Get Invitation Details
        $invitation = $db->table('invitations')->where('id', (int)$invitationId)->get()->getRowArray();
        if (!$invitation) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }

        // 2. Get All Visitor Card Logs for this invitation
        $sql = "SELECT vcl.*,
                    COALESCE(sl_lane.name, sl_direct.name) AS lane_name,
                    loc.branch, loc.location_access
                FROM visitor_card_logs vcl
                LEFT JOIN lanes la           ON la.id            = vcl.lane_id
                LEFT JOIN sub_locations sl_lane   ON sl_lane.location_id   = la.location_id
                LEFT JOIN sub_locations sl_direct ON sl_direct.id = vcl.sub_location_id
                LEFT JOIN locations loc      ON loc.id = COALESCE(la.location_id, sl_direct.location_id)
                WHERE vcl.invitation_id = ?
                  AND vcl.action != 'assigned'
                ORDER BY vcl.scanned_at ASC";
        
        $logs = $db->query($sql, [(int)$invitationId])->getResultArray();

        $ivRow = $db->query(
            'SELECT 
                MIN(iv.check_in_time) AS check_in_time, 
                MAX(iv.check_out_time) AS check_out_time,
                (SELECT MAX(date_to) FROM invitation_schedules s WHERE s.invitation_id = iv.invitation_id) AS schedule_end
             FROM invitation_visitors iv 
             WHERE iv.invitation_id = ?
             GROUP BY iv.invitation_id',
            [(int) $invitationId]
        )->getRowArray() ?: ['check_in_time' => null, 'check_out_time' => null, 'schedule_end' => null];

        if (empty($logs)) {
            $totalSeconds = $this->secondsFromRegistrationWindow($ivRow);
            $status       = $this->registrationStatusLabel($ivRow);
            if ($status === 'UNKNOWN') {
                $status = 'NO_LOGS';
            }

            // Calculate overstay for summary if currently on site
            $schedEnd = !empty($ivRow['schedule_end']) ? strtotime((string) $ivRow['schedule_end']) : null;
            $nowTs = time();
            $displayTotalTime = $this->formatDuration($totalSeconds);
            $isOverstay = false;

            if (empty($ivRow['check_out_time']) && !empty($ivRow['check_in_time']) && $schedEnd && $nowTs > $schedEnd) {
                $isOverstay = true;
                $overstaySecs = $nowTs - $schedEnd;
                $displayTotalTime = '+' . $this->formatDuration($overstaySecs);
            }

            return $this->response->setJSON([
                'success' => true,
                'summary' => [
                    'full_name'    => $invitation['full_name'] ?? 'N/A',
                    'ic_no'        => $invitation['ic_passport'] ?? 'N/A',
                    'status'       => $status,
                    'total_time'   => $displayTotalTime,
                    'total_visits' => (!empty($ivRow['check_in_time']) ? 1 : 0),
                    'total_scans'  => 0,
                    'location'     => $invitation['location'] ?? 'N/A',
                    'is_overstay'  => $isOverstay,
                    'last_seen'    => '-',
                ],
                'dates' => (!empty($ivRow['check_in_time']) ? [
                    [
                        'display_date' => date('M j, Y', strtotime($ivRow['check_in_time'])),
                        'logs_count' => 0,
                        'movements' => [
                            [
                                'movement_index' => 1,
                                'from' => 'Manual Registration',
                                'to'   => ($ivRow['check_out_time'] ? 'Manual Checkout' : 'STILL AT SITE'),
                                'entry_time' => date('g:i A', strtotime($ivRow['check_in_time'])),
                                'exit_time'  => ($ivRow['check_out_time'] ? date('g:i A', strtotime($ivRow['check_out_time'])) : '-'),
                                'time_spent' => ($ivRow['check_out_time'] ? $this->formatDuration(strtotime($ivRow['check_out_time']) - strtotime($ivRow['check_in_time'])) : '-'),
                                'status'     => 'GRANTED'
                            ]
                        ]
                    ]
                ] : []),
            ]);
        }

        // 3. Process Logs into Movements and Stats
        $dates = [];
        $totalSeconds = 0;
        $uniqueDays = [];
        $totalScans = count($logs);
        
        // Group logs by date
        foreach ($logs as $log) {
            $dateStr = date('Y-m-d', strtotime($log['scanned_at']));
            if (!isset($dates[$dateStr])) {
                $dates[$dateStr] = [
                    'display_date' => date('M j, Y', strtotime($dateStr)),
                    'logs_count' => 0,
                    'movements' => []
                ];
                $uniqueDays[$dateStr] = true;
            }
        }

        // Generate Movements
        // Logic: Movement i = Log[i] to Log[i+1]
        for ($i = 0; $i < count($logs); $i++) {
            $currentLog = $logs[$i];
            $dateStr = date('Y-m-d', strtotime($currentLog['scanned_at']));
            $dates[$dateStr]['logs_count']++;

            $nextLog = ($i + 1 < count($logs)) ? $logs[$i+1] : null;
            
            // Stats calculation: Only add to total time if next scan exists and is on the same day OR we just add anyway if it's a continuous visit
            $durationSeconds = 0;
            if ($nextLog) {
                $durationSeconds = strtotime($nextLog['scanned_at']) - strtotime($currentLog['scanned_at']);
                // Don't count overnight or very long gaps as a single movement if they are more than 12 hours? 
                // For simplicity, we just count them.
                $totalSeconds += $durationSeconds;
            }

            // Create Movement object
            $exitTime = $nextLog ? date('g:i A', strtotime($nextLog['scanned_at'])) : '-';
            $durationStr = $this->formatDuration($durationSeconds);

            $actionLabels = [
                'checkin'       => 'Check In',
                'checkout'      => 'Check Out',
                'door_checkin'  => 'In',
                'door_checkout' => 'Out',
                'door_access'   => 'Access',
            ];
            $fromAction = strtolower((string) ($currentLog['action'] ?? ''));
            $toAction   = $nextLog ? strtolower((string) ($nextLog['action'] ?? '')) : '';

            $dates[$dateStr]['movements'][] = [
                'movement_index' => count($dates[$dateStr]['movements']) + 1,
                'from'        => ($currentLog['lane_name'] ?? '—'),
                'from_action' => $actionLabels[$fromAction] ?? '',
                'to'          => ($nextLog ? ($nextLog['lane_name'] ?? '—') : 'STILL AT SITE'),
                'to_action'   => $nextLog ? ($actionLabels[$toAction] ?? '') : '',
                'entry_time'  => date('g:i A', strtotime($currentLog['scanned_at'])),
                'exit_time'   => $exitTime,
                'time_spent'  => ($nextLog ? $durationStr : '-'),
                'status'      => 'GRANTED'
            ];
        }

        if (! empty($ivRow['check_out_time'])) {
            $lastIdx     = count($logs) - 1;
            $lastDateStr = date('Y-m-d', strtotime($logs[$lastIdx]['scanned_at']));
            if (isset($dates[$lastDateStr]['movements'])) {
                $movements   = &$dates[$lastDateStr]['movements'];
                $lastMIdx    = count($movements) - 1;
                $checkoutTs  = strtotime((string) $ivRow['check_out_time']);
                $entryTs     = strtotime($logs[$lastIdx]['scanned_at']);
                if ($lastMIdx >= 0 && ($movements[$lastMIdx]['to'] ?? '') === 'STILL AT SITE' && $checkoutTs >= $entryTs) {
                    $dur = $checkoutTs - $entryTs;
                    $movements[$lastMIdx]['to']         = 'Recorded checkout';
                    $movements[$lastMIdx]['exit_time']  = date('g:i A', $checkoutTs);
                    $movements[$lastMIdx]['time_spent'] = $this->formatDuration($dur);
                }
            }
        }

        // Final summary: prefer registration record (same source as dashboard on-site / overstay).
        $lastLog = end($logs);
        $status = $this->registrationStatusLabel($ivRow);
        if ($status === 'UNKNOWN') {
            $status = (strpos(strtolower($lastLog['lane_name'] ?? ''), 'out') !== false) ? 'OUT_BUILDING' : 'IN_BUILDING';
        }

        // Calculate overstay logic (matching dashboard and report)
        $schedEnd = !empty($ivRow['schedule_end']) ? strtotime((string) $ivRow['schedule_end']) : null;
        $nowTs = time();
        $isOverstay = false;
        
        $durationSeconds = $this->secondsFromRegistrationWindow($ivRow);
        $totalSeconds = max($totalSeconds, $durationSeconds);
        $displayTotalTime = $this->formatDuration($totalSeconds);

        if (empty($ivRow['check_out_time']) && !empty($ivRow['check_in_time']) && $schedEnd && $nowTs > $schedEnd) {
            $isOverstay = true;
            $overDiff = $nowTs - $schedEnd;
            $displayTotalTime = '+' . $this->formatDuration($overDiff);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'summary' => [
                'full_name'    => $invitation['full_name'] ?? 'N/A',
                'ic_no'        => $invitation['ic_passport'] ?? 'N/A',
                'status'       => $status,
                'total_time'   => $displayTotalTime,
                'total_visits' => count($dates),
                'total_scans'  => count($logs),
                'location'     => $invitation['location'] ?? 'N/A',
                'is_overstay'  => $isOverstay,
                'last_seen'    => ($lastLog['lane_name'] ?? '-')
            ],
            'dates' => array_values($dates)
        ]);
    }

    public function chronologyPrint($id)
    {
        $invitationId = (int)$id;
        if (empty($invitationId)) {
            return "Invitation ID is required.";
        }

        $db = db_connect();

        $invitation = $db->table('invitations')->where('id', $invitationId)->get()->getRowArray();
        if (!$invitation) {
            return "Record not found.";
        }

        $sql = "SELECT vcl.*,
                    COALESCE(sl_lane.name, sl_direct.name) AS lane_name,
                    loc.branch, loc.location_access
                FROM visitor_card_logs vcl
                LEFT JOIN lanes la           ON la.id            = vcl.lane_id
                LEFT JOIN sub_locations sl_lane   ON sl_lane.location_id   = la.location_id
                LEFT JOIN sub_locations sl_direct ON sl_direct.id = vcl.sub_location_id
                LEFT JOIN locations loc      ON loc.id = COALESCE(la.location_id, sl_direct.location_id)
                WHERE vcl.invitation_id = ?
                  AND vcl.action != 'assigned'
                ORDER BY vcl.scanned_at ASC";

        $logs = $db->query($sql, [$invitationId])->getResultArray();

        $ivRow = $db->query(
            'SELECT MIN(check_in_time) AS check_in_time, MAX(check_out_time) AS check_out_time
             FROM invitation_visitors WHERE invitation_id = ?',
            [$invitationId]
        )->getRowArray() ?: ['check_in_time' => null, 'check_out_time' => null];

        $dates = [];
        $totalSeconds = 0;
        $uniqueDays = [];
        $totalScans = count($logs);
        
        foreach ($logs as $log) {
            $dateStr = date('Y-m-d', strtotime($log['scanned_at']));
            if (!isset($dates[$dateStr])) {
                $dates[$dateStr] = [
                    'display_date' => date('M j, Y', strtotime($dateStr)),
                    'logs_count' => 0,
                    'movements' => []
                ];
                $uniqueDays[$dateStr] = true;
            }
        }

        for ($i = 0; $i < count($logs); $i++) {
            $currentLog = $logs[$i];
            $dateStr = date('Y-m-d', strtotime($currentLog['scanned_at']));
            $dates[$dateStr]['logs_count']++;

            $nextLog = ($i + 1 < count($logs)) ? $logs[$i+1] : null;
            
            $durationSeconds = 0;
            if ($nextLog) {
                $durationSeconds = strtotime($nextLog['scanned_at']) - strtotime($currentLog['scanned_at']);
                $totalSeconds += $durationSeconds;
            }

            $exitTime = $nextLog ? date('g:i A', strtotime($nextLog['scanned_at'])) : '-';
            $durationStr = $this->formatDuration($durationSeconds);

            $dates[$dateStr]['movements'][] = [
                'movement_index' => count($dates[$dateStr]['movements']) + 1,
                'from' => ($currentLog['lane_name'] ?? '—'),
                'to'   => ($nextLog ? ($nextLog['lane_name'] ?? '—') : 'STILL AT SITE'),
                'entry_time' => date('g:i A', strtotime($currentLog['scanned_at'])),
                'exit_time'  => $exitTime,
                'time_spent' => ($nextLog ? $durationStr : '-'),
                'status'     => 'GRANTED'
            ];
        }

        if (! empty($logs) && ! empty($ivRow['check_out_time'])) {
            $lastIdx     = count($logs) - 1;
            $lastDateStr = date('Y-m-d', strtotime($logs[$lastIdx]['scanned_at']));
            if (isset($dates[$lastDateStr]['movements'])) {
                $movements  = &$dates[$lastDateStr]['movements'];
                $lastMIdx   = count($movements) - 1;
                $checkoutTs = strtotime((string) $ivRow['check_out_time']);
                $entryTs    = strtotime($logs[$lastIdx]['scanned_at']);
                if ($lastMIdx >= 0 && ($movements[$lastMIdx]['to'] ?? '') === 'STILL AT SITE' && $checkoutTs >= $entryTs) {
                    $dur = $checkoutTs - $entryTs;
                    $movements[$lastMIdx]['to']         = 'Recorded checkout';
                    $movements[$lastMIdx]['exit_time']  = date('g:i A', $checkoutTs);
                    $movements[$lastMIdx]['time_spent'] = $this->formatDuration($dur);
                }
            }
        }

        $totalSeconds = max($totalSeconds, $this->secondsFromRegistrationWindow($ivRow));

        $realStatus = $this->registrationStatusLabel($ivRow);
        if ($realStatus === 'UNKNOWN') {
            $sqlCheck = 'SELECT MIN(vcl.scanned_at) AS visit_from, MAX(vcl.scanned_at) AS visit_to FROM visitor_card_logs vcl WHERE vcl.invitation_id = ?';
            $vData      = $db->query($sqlCheck, [$invitationId])->getRowArray();
            $sameScan   = ! empty($vData['visit_from']) && ! empty($vData['visit_to'])
                && strtotime((string) $vData['visit_from']) === strtotime((string) $vData['visit_to']);
            if (! empty($vData['visit_from']) && $sameScan) {
                $realStatus = 'CHECKED IN';
            } elseif (! empty($vData['visit_from']) && ! empty($vData['visit_to']) && ! $sameScan) {
                $realStatus = 'CHECKED OUT';
            } else {
                $realStatus = '-';
            }
        }

        $data = [
            'summary' => [
                'full_name' => $invitation['full_name'],
                'ic_no' => $invitation['ic_passport'],
                'status' => $realStatus,
                'total_time' => $this->formatDuration($totalSeconds),
                'total_visits' => count($uniqueDays),
                'total_scans' => $totalScans,
            ],
            'dates' => array_values($dates)
        ];

        return view('reports/visitor_chronology_print', ['data' => $data, 'generated_at' => date('M j, Y g:i A')]);
    }

    /**
     * Normalize IC / staff input so values match whether stored or typed with or without "-", spaces, etc.
     */
    private function normalizeIdentitySearchTerm(string $raw): string
    {
        return strtolower(preg_replace('/[\s\-_]+/u', '', trim($raw)));
    }

    /**
     * Stay length from invitation_visitors check-in/out (aligned with dashboard on-site logic).
     */
    private function secondsFromRegistrationWindow(array $ivRow): int
    {
        if (empty($ivRow['check_in_time'])) {
            return 0;
        }
        $start = strtotime((string) $ivRow['check_in_time']);
        $end   = ! empty($ivRow['check_out_time'])
            ? strtotime((string) $ivRow['check_out_time'])
            : time();

        return max(0, $end - $start);
    }

    private function registrationStatusLabel(array $ivRow): string
    {
        if (! empty($ivRow['check_out_time'])) {
            return 'CHECKED OUT';
        }
        if (! empty($ivRow['check_in_time'])) {
            return 'CHECKED IN';
        }

        return 'UNKNOWN';
    }

    private function formatDuration($seconds)
    {
        if ($seconds <= 0) return '0s';
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds % 60;
        
        $res = [];
        if ($h > 0) $res[] = "{$h}h";
        if ($m > 0 || $h > 0) $res[] = "{$m}m";
        $res[] = "{$s}s";
        
        return implode(' ', $res);
    }
}