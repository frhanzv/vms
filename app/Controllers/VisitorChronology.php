<?php

namespace App\Controllers;

use App\Models\LocationModel;

class VisitorChronology extends BaseController
{
    protected $locationModel;

    public function __construct()
    {
        $this->locationModel = new LocationModel();
    }

    public function index()
    {
        $locations = $this->locationModel->orderBy('branch', 'ASC')->orderBy('location_access', 'ASC')->findAll();

        return view('reports/access_chronology', [
            'pageTitle'   => 'Visitor Details - SafeG',
            'locations'   => $locations,
        ]);
    }

    public function generate()
    {
        $fromRaw = $this->request->getPost('from_datetime');
        $toRaw   = $this->request->getPost('to_datetime');
        $locationId   = $this->request->getPost('location_id');
        $invitationId = $this->request->getPost('invitation_id');
        $searchBy     = $this->request->getPost('search_by');
        $searchTerm   = trim((string) $this->request->getPost('search_term'));

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

        if ($locationId !== null && $locationId !== '') {
            $location = $this->locationModel->find((int)$locationId);
            $locationName = $location ? trim($location['branch'] . ' - ' . $location['location_access']) : 'Unknown';
        } else {
            $locationName = 'All locations';
        }

        $db = db_connect();

        // 1. Grouped Visitor Summary (for the main table - Image 4)
        $whereGrouped = ['i.id IS NOT NULL'];
        $paramsGrouped = [];

        if ($hasInvitation) {
            $whereGrouped[] = 'i.id = ?';
            $paramsGrouped[] = (int)$invitationId;
        } elseif ($searchBy === 'staff') {
            $whereGrouped[] = 'i.staff_id = ?';
            $paramsGrouped[] = $searchTerm;
        } else {
            $whereGrouped[] = 'i.ic_passport = ?';
            $paramsGrouped[] = $searchTerm;
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
                // If the last scan was recent or if we want to check something specific...
                // Usually logic is based on checkin/checkout action field if exists.
                // For now, if there is a 'visit_to' (last scan), we consider them Checked Out or OUT.
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
                'search_type'     => 'Auto Detect' // Placeholder as in Image 3
            ];
        }

        // 2. Full Chronology (for the "Chrono" action - Image 5)
        $whereChron = ['vcl.scanned_at >= ?', 'vcl.scanned_at <= ?', 'i.id IS NOT NULL'];
        $paramsChron = [$fromDatetime, $toDatetime];
        
        if ($locationId !== null && $locationId !== '') {
            $whereChron[] = 'la.location_id = ?';
            $paramsChron[] = $locationId;
        }

        if ($hasInvitation) {
            $whereChron[] = 'i.id = ?';
            $paramsChron[] = (int)$invitationId;
        } elseif ($searchBy === 'staff') {
            $whereChron[] = 'i.staff_id = ?';
            $paramsChron[] = $searchTerm;
        } else {
            $whereChron[] = 'i.ic_passport = ?';
            $paramsChron[] = $searchTerm;
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
}
