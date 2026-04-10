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
}