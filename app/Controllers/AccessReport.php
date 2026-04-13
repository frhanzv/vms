<?php

namespace App\Controllers;

use App\Models\LocationModel;

class AccessReport extends BaseController
{
    protected $locationModel;

    public function __construct()
    {
        $this->locationModel = new LocationModel();
    }

    public function index()
    {
        $locations = $this->locationModel->getAllActive();

        $data = [
            'pageTitle' => 'Access Report - SafeG',
            'locations' => $locations,
        ];

        return view('reports/access_report', $data);
    }

    public function generate()
    {
        $fromDatetime = $this->request->getPost('from_datetime');
        $toDatetime   = $this->request->getPost('to_datetime');
        $locationId   = $this->request->getPost('location_id');

        if (empty($fromDatetime) || empty($toDatetime) || empty($locationId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'All fields are required.']);
        }

        $db = \Config\Database::connect();

        // Get location name
        $location = $this->locationModel->find($locationId);
        if (!$location) {
            return $this->response->setJSON(['success' => false, 'message' => 'Location not found.']);
        }
        $locationName = $location['branch'] . ' - ' . $location['location_access'];

        // Query: visitors at this location within the time range
        // Join visitor_card_logs -> lanes -> locations to filter by location
        // Then join invitation_visitors and invitations for visitor details
        $sql = "SELECT 
                    i.id               AS invitation_id,
                    i.full_name        AS visitor_name,
                    i.contact          AS contact_no,
                    i.ic_passport      AS ic_no,
                    i.invited_by       AS person_visited,
                    i.staff_id         AS staff_id,
                    i.company          AS visitor_company,
                    i.vehicle_registration AS vehicle_no,
                    i.reason           AS visit_reason,
                    MIN(vcl.scanned_at) AS first_access,
                    MAX(vcl.scanned_at) AS last_access,
                    COUNT(vcl.id)      AS total_access
                FROM visitor_card_logs vcl
                LEFT JOIN lanes la ON la.id = vcl.lane_id
                LEFT JOIN invitations i ON i.id = vcl.invitation_id
                WHERE la.location_id = ?
                  AND vcl.scanned_at >= ?
                  AND vcl.scanned_at <= ?
                  AND i.id IS NOT NULL
                GROUP BY 
                    i.id,
                    i.full_name,
                    i.contact,
                    i.ic_passport,
                    i.invited_by,
                    i.staff_id,
                    i.company,
                    i.vehicle_registration,
                    i.reason
                ORDER BY first_access ASC";

        $rows = $db->query($sql, [$locationId, $fromDatetime, $toDatetime])->getResultArray();

        $visitors = [];
        foreach ($rows as $row) {
            $visitors[] = [
                'invitation_id'   => (int) ($row['invitation_id'] ?? 0),
                'visitor_name'    => $row['visitor_name']    ?? 'N/A',
                'contact_no'      => $row['contact_no']      ?? 'N/A',
                'ic_no'           => $row['ic_no']           ?? 'N/A',
                'person_visited'  => $row['person_visited']  ?? 'N/A',
                'staff_id'        => $row['staff_id']        ?? 'N/A',
                'visitor_company' => $row['visitor_company'] ?? 'N/A',
                'vehicle_no'      => $row['vehicle_no']      ?? '-',
                'visit_reason'    => $row['visit_reason']    ?? 'N/A',
                'first_access'    => $row['first_access']    ? date('d/m/Y H:i', strtotime($row['first_access'])) : '-',
                'last_access'     => $row['last_access']     ? date('d/m/Y H:i', strtotime($row['last_access']))  : '-',
                'total_access'    => (int) $row['total_access'],
            ];
        }

        return $this->response->setJSON([
            'success'       => true,
            'total_visitors'=> count($visitors),
            'location_name' => $locationName,
            'from_datetime' => date('d M Y, h:i A', strtotime($fromDatetime)),
            'to_datetime'   => date('d M Y, h:i A', strtotime($toDatetime)),
            'visitors'      => $visitors,
        ]);
    }

    /**
     * Per-invitation access events for the movement history modal (same filters as the report).
     */
    public function movementHistory()
    {
        $fromDatetime = $this->request->getPost('from_datetime');
        $toDatetime   = $this->request->getPost('to_datetime');
        $locationId   = $this->request->getPost('location_id');
        $invitationId = $this->request->getPost('invitation_id');

        if (empty($fromDatetime) || empty($toDatetime) || empty($locationId) || empty($invitationId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'All fields are required.']);
        }

        $db = \Config\Database::connect();

        $inv = $db->table('invitations')->where('id', (int) $invitationId)->get()->getRowArray();
        if (! $inv) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invitation not found.']);
        }

        $staffRef = $inv['staff_id'] ?? '';
        if ($staffRef === '' || $staffRef === 'N/A') {
            $staffRef = $inv['ic_passport'] ?? '';
        }
        if ($staffRef === '' || $staffRef === 'N/A') {
            $staffRef = (string) ($inv['full_name'] ?? 'Visitor');
        }

        $sql = 'SELECT vcl.scanned_at, vcl.action,
                       la.id AS lane_id, la.lane AS lane_name,
                       loc.id AS location_id, loc.branch, loc.location_access
                FROM visitor_card_logs vcl
                INNER JOIN lanes la ON la.id = vcl.lane_id
                INNER JOIN locations loc ON loc.id = la.location_id
                WHERE vcl.invitation_id = ?
                  AND la.location_id = ?
                  AND vcl.scanned_at >= ?
                  AND vcl.scanned_at <= ?
                ORDER BY vcl.scanned_at ASC';

        $rows = $db->query($sql, [(int) $invitationId, (int) $locationId, $fromDatetime, $toDatetime])->getResultArray();

        $movements = [];
        foreach ($rows as $row) {
            $laneId = isset($row['lane_id']) ? (int) $row['lane_id'] : 0;
            $laneName = (string) ($row['lane_name'] ?? '');
            $laneLabel = $laneId > 0 && $laneName !== ''
                ? $laneId . '. ' . $laneName
                : ($laneName !== '' ? $laneName : '—');
            $siteParts = array_filter([(string) ($row['branch'] ?? ''), (string) ($row['location_access'] ?? '')]);
            $siteLabel = implode(' — ', $siteParts);
            $locationDisplay = $siteLabel !== ''
                ? $laneLabel . ' · ' . $siteLabel
                : $laneLabel;

            $action = strtolower((string) ($row['action'] ?? 'checkin'));
            $typeLabel = $action === 'checkout' ? 'Checkout' : 'Checkin';

            $movements[] = [
                'date_time' => ! empty($row['scanned_at'])
                    ? date('d M Y h:i:s A', strtotime($row['scanned_at']))
                    : '—',
                'location'        => $locationDisplay,
                'access'          => 'Yes',
                'access_granted'  => true,
                'reason'          => '—',
                'type'            => $typeLabel,
                'action'          => 'Allowed',
                'action_allowed'  => true,
            ];
        }

        return $this->response->setJSON([
            'success'      => true,
            'staff_no'     => $staffRef,
            'visitor_name' => $inv['full_name'] ?? '',
            'movements'    => $movements,
        ]);
    }
}