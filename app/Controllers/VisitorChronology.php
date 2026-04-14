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

        $hasInvitation = $invitationId !== null && $invitationId !== '';
        $hasSearch     = $searchTerm !== '';

        if (! $hasInvitation && ! $hasSearch) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enter IC number or staff number, or open this page from a report.']);
        }

        $locationId = ($locationId === null || $locationId === '') ? null : (int) $locationId;

        if ($locationId !== null) {
            $location = $this->locationModel->find($locationId);
            if (! $location) {
                return $this->response->setJSON(['success' => false, 'message' => 'Location not found.']);
            }
            $locationName = trim(($location['branch'] ?? '') . ' - ' . ($location['location_access'] ?? ''));
        } else {
            $locationName = 'All locations';
        }

        $db = db_connect();

        $where  = ['vcl.scanned_at >= ?', 'vcl.scanned_at <= ?', 'i.id IS NOT NULL'];
        $params = [$fromDatetime, $toDatetime];

        if ($locationId !== null) {
            $where[]  = 'la.location_id = ?';
            $params[] = $locationId;
        }

        if ($hasInvitation) {
            $where[]  = 'i.id = ?';
            $params[] = (int) $invitationId;
        } elseif ($searchBy === 'staff') {
            $where[]  = 'i.staff_id = ?';
            $params[] = $searchTerm;
        } else {
            $where[]  = 'i.ic_passport = ?';
            $params[] = $searchTerm;
        }

        $sql = 'SELECT i.id AS invitation_id,
                       i.full_name AS visitor_name,
                       i.contact AS contact_no,
                       i.ic_passport AS ic_no,
                       i.invited_by AS person_visited,
                       i.company AS visitor_company,
                       i.vehicle_registration AS vehicle_no,
                       i.reason AS visit_reason,
                       vcl.scanned_at AS access_time,
                       la.id AS lane_id,
                       la.lane AS lane_name,
                       loc.branch,
                       loc.location_access
                FROM visitor_card_logs vcl
                LEFT JOIN lanes la ON la.id = vcl.lane_id
                LEFT JOIN locations loc ON loc.id = la.location_id
                LEFT JOIN invitations i ON i.id = vcl.invitation_id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY vcl.scanned_at ASC';

        $chronologyData = $db->query($sql, $params)->getResultArray();

        $formatted = [];
        foreach ($chronologyData as $row) {
            $laneId   = isset($row['lane_id']) ? (int) $row['lane_id'] : 0;
            $laneName = (string) ($row['lane_name'] ?? '');
            $laneLbl = $laneId > 0 && $laneName !== '' ? $laneId . '. ' . $laneName : ($laneName !== '' ? $laneName : '—');
            $parts = array_filter([(string) ($row['branch'] ?? ''), (string) ($row['location_access'] ?? '')]);
            $site     = implode(' — ', $parts);
            $locDet   = $site !== '' ? $laneLbl . ' · ' . $site : $laneLbl;

            $formatted[] = [
                'invitation_id'   => $row['invitation_id'],
                'visitor_name'    => $row['visitor_name'] ?? 'N/A',
                'contact_no'      => $row['contact_no'] ?? 'N/A',
                'ic_no'           => $row['ic_no'] ?? 'N/A',
                'person_visited'  => $row['person_visited'] ?? 'N/A',
                'visitor_company' => $row['visitor_company'] ?? 'N/A',
                'vehicle_no'      => $row['vehicle_no'] ?? '-',
                'visit_reason'    => $row['visit_reason'] ?? 'N/A',
                'access_time'     => ! empty($row['access_time']) ? date('d/m/Y H:i', strtotime($row['access_time'])) : '-',
                'location_detail' => $locDet,
            ];
        }

        return $this->response->setJSON([
            'success'       => true,
            'total_records' => count($formatted),
            'location_name' => $locationName !== '' ? $locationName : 'Unknown',
            'from_datetime' => date('d M Y, h:i A', strtotime($fromDatetime)),
            'to_datetime'   => date('d M Y, h:i A', strtotime($toDatetime)),
            'chronology'    => $formatted,
        ]);
    }
}
