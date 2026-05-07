<?php

namespace App\Controllers;

use App\Models\LaneModel;

class VisitorChronology extends BaseController
{
    protected $laneModel;

    public function __construct()
    {
        $this->laneModel = new LaneModel();
    }

    public function index()
    {
        return view('reports/access_chronology', [
            'pageTitle' => 'Visitor Details - SafeG',
            'lanes'     => $this->laneModel->where('status', 'active')->orderBy('lane', 'ASC')->findAll(),
        ]);
    }

    public function generate()
    {
        $fromRaw      = $this->request->getPost('from_datetime');
        $toRaw        = $this->request->getPost('to_datetime');
        $laneId       = $this->request->getPost('lane_id');
        $invitationId = $this->request->getPost('invitation_id');
        $searchBy     = $this->request->getPost('search_by');
        $searchTerm   = trim((string) $this->request->getPost('search_term'));

        // Normalize Search Term (Strip dashes for IC/Staff searches)
        $normalizedSearchTerm = str_replace('-', '', $searchTerm);

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
            return $this->response->setJSON(['success' => false, 'message' => 'Enter IC number or staff number.']);
        }

        if ($laneId !== null && $laneId !== '') {
            $lane = $this->laneModel->find((int)$laneId);
            $locationName = $lane ? $lane['lane'] : 'Unknown Lane';
        } else {
            $locationName = 'All Lanes';
        }

        $db = db_connect();

        // 1. Grouped Visitor Summary
        $whereGrouped = ['i.id IS NOT NULL'];
        $paramsGrouped = [];

        if ($hasInvitation) {
            $whereGrouped[] = 'i.id = ?';
            $paramsGrouped[] = (int)$invitationId;
        } elseif ($searchBy === 'staff') {
            $whereGrouped[] = "REPLACE(i.staff_id, '-', '') = ?";
            $paramsGrouped[] = $normalizedSearchTerm;
        } else {
            $whereGrouped[] = "REPLACE(i.ic_passport, '-', '') = ?";
            $paramsGrouped[] = $normalizedSearchTerm;
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
                        MAX(vcl.scanned_at) AS visit_to
                       FROM invitations i
                       LEFT JOIN visitor_card_logs vcl ON vcl.invitation_id = i.id
                       WHERE " . implode(' AND ', $whereGrouped) . "
                       GROUP BY i.id";
        
        $visitorData = $db->query($sqlGrouped, $paramsGrouped)->getResultArray();
        
        $formattedVisitors = [];
        foreach ($visitorData as $v) {
            $status = 'OUT';
            if ($v['visit_from'] && !$v['visit_to']) {
                $status = 'Checked In';
            } elseif ($v['visit_from'] && $v['visit_to']) {
                $status = 'Checked Out';
            }

            $duration = '-';
            if ($v['visit_from'] && $v['visit_to']) {
                $diff = strtotime($v['visit_to']) - strtotime($v['visit_from']);
                $hours = floor($diff / 3600);
                $mins = floor(($diff % 3600) / 60);
                $secs = $diff % 60;
                $duration = "{$hours} hour {$mins} minutes {$secs} seconds";
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
                'visit_from'      => $v['visit_from'] ? date('M j, Y g:i A', strtotime($v['visit_from'])) : '-',
                'visit_to'        => $v['visit_to'] ? date('M j, Y g:i A', strtotime($v['visit_to'])) : '-',
                'visit_duration'  => $duration,
                'status'          => $status,
                'last_updated'    => date('n/j/Y, g:i:s A', strtotime($v['updated_at'] ?? 'now')),
                'search_type'     => 'Auto Detect'
            ];
        }

        // 2. Full Chronology
        $whereChron = ['vcl.scanned_at >= ?', 'vcl.scanned_at <= ?', 'i.id IS NOT NULL'];
        $paramsChron = [$fromDatetime, $toDatetime];
        
        if ($laneId !== null && $laneId !== '') {
            $whereChron[] = 'vcl.lane_id = ?';
            $paramsChron[] = $laneId;
        }

        if ($hasInvitation) {
            $whereChron[] = 'i.id = ?';
            $paramsChron[] = (int)$invitationId;
        } elseif ($searchBy === 'staff') {
            $whereChron[] = "REPLACE(i.staff_id, '-', '') = ?";
            $paramsChron[] = $normalizedSearchTerm;
        } else {
            $whereChron[] = "REPLACE(i.ic_passport, '-', '') = ?";
            $paramsChron[] = $normalizedSearchTerm;
        }

        $sqlChron = 'SELECT i.id AS invitation_id,
                       i.full_name AS visitor_name,
                       vcl.scanned_at AS access_time,
                       la.lane AS lane_name,
                       loc.branch,
                       loc.location_access
                FROM visitor_card_logs vcl
                LEFT JOIN lanes la ON la.id = vcl.lane_id
                LEFT JOIN locations loc ON loc.id = la.location_id
                LEFT JOIN invitations i ON i.id = vcl.invitation_id
                WHERE ' . implode(' AND ', $whereChron) . '
                ORDER BY vcl.scanned_at ASC';

        $chronologyData = $db->query($sqlChron, $paramsChron)->getResultArray();

        $formattedChron = [];
        foreach ($chronologyData as $row) {
            $formattedChron[] = [
                'visitor_name'    => $row['visitor_name'] ?? 'N/A',
                'access_time'     => ! empty($row['access_time']) ? date('d/m/Y H:i', strtotime($row['access_time'])) : '-',
                'location_detail' => ($row['lane_name'] ?? '') . ' · ' . ($row['branch'] ?? '') . ' — ' . ($row['location_access'] ?? ''),
            ];
        }

        return $this->response->setJSON([
            'success'       => true,
            'location_name' => $locationName,
            'from_datetime' => date('d M Y, h:i A', strtotime($fromDatetime)),
            'to_datetime'   => date('d M Y, h:i A', strtotime($toDatetime)),
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
                    MAX(vcl.scanned_at) AS visit_to
                FROM invitations i
                LEFT JOIN visitor_card_logs vcl ON vcl.invitation_id = i.id
                WHERE i.id = ?
                GROUP BY i.id";
        
        $visitor = $db->query($sql, [(int)$id])->getRowArray();
        
        if (!$visitor) {
            return "Visitor not found.";
        }

        // Calculate duration and status
        $duration = '-';
        if ($visitor['visit_from'] && $visitor['visit_to']) {
            $diff = strtotime($visitor['visit_to']) - strtotime($visitor['visit_from']);
            $hours = floor($diff / 3600);
            $mins = floor(($diff % 3600) / 60);
            $secs = $diff % 60;
            $duration = "{$hours} hour {$mins} minutes {$secs} seconds";
        }

        $statusText = 'Currently OUT of Building';
        if ($visitor['visit_from'] && !$visitor['visit_to']) {
             $statusText = 'Currently IN Building';
        }

        $data = [
            'visitor' => $visitor,
            'visit_duration' => $duration,
            'status_text' => $statusText,
            'generated_at' => date('n/j/Y, g:i:s A')
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
        $sql = "SELECT vcl.*, la.lane AS lane_name, loc.branch, loc.location_access
                FROM visitor_card_logs vcl
                LEFT JOIN lanes la ON la.id = vcl.lane_id
                LEFT JOIN locations loc ON loc.id = la.location_id
                WHERE vcl.invitation_id = ?
                ORDER BY vcl.scanned_at ASC";
        
        $logs = $db->query($sql, [(int)$invitationId])->getResultArray();

        if (empty($logs)) {
            return $this->response->setJSON([
                'success' => true,
                'summary' => [
                    'full_name' => $invitation['full_name'],
                    'ic_no' => $invitation['ic_passport'],
                    'status' => 'NO_LOGS',
                    'total_time' => '0s',
                    'total_visits' => 0,
                    'total_scans' => 0,
                    'last_seen' => '-'
                ],
                'dates' => []
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
                    'display_date' => date('d-M-Y', strtotime($dateStr)),
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
            $exitTime = $nextLog ? date('h:i:s A', strtotime($nextLog['scanned_at'])) : '-';
            $durationStr = $this->formatDuration($durationSeconds);

            $dates[$dateStr]['movements'][] = [
                'movement_index' => count($dates[$dateStr]['movements']) + 1,
                'from' => ($currentLog['lane_name'] ?? 'Unknown Road'),
                'to'   => ($nextLog ? ($nextLog['lane_name'] ?? 'Unknown Road') : 'STILL AT SITE'),
                'entry_time' => date('h:i:s A', strtotime($currentLog['scanned_at'])),
                'exit_time'  => $exitTime,
                'time_spent' => ($nextLog ? $durationStr : '-'),
                'status'     => 'GRANTED' // Action is always granted in logs unless denied logs exist
            ];
        }

        // Final summary
        $lastLog = end($logs);
        $status = (strpos(strtolower($lastLog['lane_name'] ?? ''), 'out') !== false) ? 'OUT_BUILDING' : 'IN_BUILDING';
        
        return $this->response->setJSON([
            'success' => true,
            'summary' => [
                'full_name' => $invitation['full_name'],
                'ic_no' => $invitation['ic_passport'],
                'status' => $status,
                'total_time' => $this->formatDuration($totalSeconds),
                'total_visits' => count($uniqueDays),
                'total_scans' => $totalScans,
                'last_seen' => ($lastLog['lane_name'] ?? '-')
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

        $sql = "SELECT vcl.*, la.lane AS lane_name, loc.branch, loc.location_access
                FROM visitor_card_logs vcl
                LEFT JOIN lanes la ON la.id = vcl.lane_id
                LEFT JOIN locations loc ON loc.id = la.location_id
                WHERE vcl.invitation_id = ?
                ORDER BY vcl.scanned_at ASC";
        
        $logs = $db->query($sql, [$invitationId])->getResultArray();

        $dates = [];
        $totalSeconds = 0;
        $uniqueDays = [];
        $totalScans = count($logs);
        
        foreach ($logs as $log) {
            $dateStr = date('Y-m-d', strtotime($log['scanned_at']));
            if (!isset($dates[$dateStr])) {
                $dates[$dateStr] = [
                    'display_date' => date('d-M-Y', strtotime($dateStr)),
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

            $exitTime = $nextLog ? date('H:i:s', strtotime($nextLog['scanned_at'])) : '-';
            $durationStr = $this->formatDuration($durationSeconds);

            $dates[$dateStr]['movements'][] = [
                'movement_index' => count($dates[$dateStr]['movements']) + 1,
                'from' => ($currentLog['lane_name'] ?? 'Unknown Road'),
                'to'   => ($nextLog ? ($nextLog['lane_name'] ?? 'Unknown Road') : 'STILL AT SITE'),
                'entry_time' => date('H:i:s', strtotime($currentLog['scanned_at'])),
                'exit_time'  => $exitTime,
                'time_spent' => ($nextLog ? $durationStr : '-'),
                'status'     => 'GRANTED'
            ];
        }

        $sqlCheck = "SELECT MIN(vcl.scanned_at) AS visit_from, MAX(vcl.scanned_at) AS visit_to FROM visitor_card_logs vcl WHERE vcl.invitation_id = ?";
        $vData = $db->query($sqlCheck, [$invitationId])->getRowArray();
        $realStatus = 'OUT';
        if ($vData['visit_from'] && !$vData['visit_to']) {
            $realStatus = 'CHECKED IN';
        } elseif ($vData['visit_from'] && $vData['visit_to']) {
            $realStatus = 'CHECKED OUT';
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

        return view('reports/visitor_chronology_print', ['data' => $data, 'generated_at' => date('n/j/Y, g:i:s A')]);
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
