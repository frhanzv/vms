<?php

namespace App\Controllers;

use App\Models\LaneModel;

class AccessReport extends BaseController
{
    protected $laneModel;

    public function __construct()
    {
        $this->laneModel = new LaneModel();
    }

    public function index()
    {
        $lanes = $this->laneModel->where('status', 'active')->orderBy('id', 'ASC')->findAll();

        $data = [
            'pageTitle' => 'Access Report - SafeG',
            'lanes' => $lanes,
        ];

        return view('reports/access_report', $data);
    }

    public function generate()
    {
        $fromDatetime = $this->request->getPost('from_datetime');
        $toDatetime   = $this->request->getPost('to_datetime');
        $laneIds  = $this->request->getPost('lane_ids');  // array from multi-select

        // Normalise: accept single location_id for backward compat
        if (empty($laneIds)) {
            $single = $this->request->getPost('lane_id');
            $laneIds = $single ? [$single] : [];
        }

        if (empty($fromDatetime) || empty($toDatetime) || empty($laneIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'All fields are required.']);
        }

        // Sanitise to integers
        $laneIds = array_values(array_filter(array_map('intval', (array) $laneIds)));
        if (empty($laneIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid location selection.']);
        }

        $db = \Config\Database::connect();

        // Build location name label
        $lanes    = $this->laneModel->whereIn('id', $laneIds)->findAll();
        if (empty($lanes)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Location not found.']);
        }
        $locationName = count($lanes) === 1
            ? $lanes[0]['id'] . '. ' . $lanes[0]['lane']
            : count($lanes) . ' Locations';

        // Build IN placeholders
        $placeholders = implode(',', array_fill(0, count($laneIds), '?'));

        // We want to show visitors who have physical scan logs AND those who only have manual check-ins
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
                    i.location         AS location,
                    CASE 
                        WHEN MIN(vcl.scanned_at) IS NULL THEN iv.check_in_time 
                        WHEN iv.check_in_time IS NULL THEN MIN(vcl.scanned_at)
                        ELSE LEAST(MIN(vcl.scanned_at), iv.check_in_time)
                    END AS first_access,
                    CASE 
                        WHEN MAX(vcl.scanned_at) IS NULL THEN COALESCE(iv.check_out_time, iv.check_in_time)
                        WHEN COALESCE(iv.check_out_time, iv.check_in_time) IS NULL THEN MAX(vcl.scanned_at)
                        ELSE GREATEST(MAX(vcl.scanned_at), COALESCE(iv.check_out_time, iv.check_in_time))
                    END AS last_access,
                    COALESCE(NULLIF(i.location, ''), MIN(la.lane), 'N/A') AS location_name,
                    CASE 
                        WHEN COUNT(vcl.id) > 0 THEN COUNT(vcl.id)
                        WHEN iv.check_in_time IS NOT NULL THEN 1
                        ELSE 0
                    END AS total_access
                FROM invitation_visitors iv
                JOIN invitations i ON i.id = iv.invitation_id
                LEFT JOIN visitor_card_logs vcl ON vcl.invitation_id = i.id
                LEFT JOIN lanes la ON la.id = vcl.lane_id
                WHERE (
                    ((la.id IN ({$placeholders}) OR vcl.lane_id IS NULL) AND vcl.scanned_at >= ? AND vcl.scanned_at <= ?)
                    OR
                    (vcl.id IS NULL AND iv.check_in_time >= ? AND iv.check_in_time <= ?)
                )
                GROUP BY 
                    i.id,
                    i.full_name,
                    i.contact,
                    i.ic_passport,
                    i.invited_by,
                    i.staff_id,
                    i.company,
                    i.vehicle_registration,
                    i.reason,
                    i.location,
                    iv.check_in_time,
                    iv.check_out_time
                ORDER BY first_access DESC";

        // Double up parameters for the OR condition
        $params = array_merge($laneIds, [$fromDatetime, $toDatetime], [$fromDatetime, $toDatetime]);
        $rows   = $db->query($sql, $params)->getResultArray();

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
                'first_access'    => $row['first_access']    ? date('M j, Y g:i A', strtotime($row['first_access'])) : '-',
                'last_access'     => $row['last_access']     ? date('M j, Y g:i A', strtotime($row['last_access']))  : '-',
                'total_access'    => (int) $row['total_access'],
                'location_name'   => $row['location_name']   ?? 'N/A',
            ];
        }

        return $this->response->setJSON([
            'success'       => true,
            'total_visitors'=> count($visitors),
            'location_name' => $locationName,
            'from_datetime' => date('M j, Y g:i A', strtotime($fromDatetime)),
            'to_datetime'   => date('M j, Y g:i A', strtotime($toDatetime)),
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
        $laneIds  = $this->request->getPost('lane_ids');
        $invitationId = $this->request->getPost('invitation_id');

        // Backward compat: accept single lane_id
        if (empty($laneIds)) {
            $single = $this->request->getPost('lane_id');
            $laneIds = $single ? [$single] : [];
        }

        $laneIds = array_values(array_filter(array_map('intval', (array) $laneIds)));

        if (empty($fromDatetime) || empty($toDatetime) || empty($laneIds) || empty($invitationId)) {
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

        $placeholders = implode(',', array_fill(0, count($laneIds), '?'));
        
        // Include both physical scan logs and registration times (check-in/out)
        $sql = "SELECT 
                    scanned_at, action, lane_id, lane_name, 
                    location_id, branch, location_access
                FROM (
                    -- Physical logs and manual logs in visitor_card_logs
                    SELECT 
                        vcl.scanned_at, vcl.action,
                        la.id AS lane_id, la.lane AS lane_name,
                        loc.id AS location_id, loc.branch, loc.location_access
                    FROM visitor_card_logs vcl
                    LEFT JOIN lanes la ON la.id = vcl.lane_id
                    LEFT JOIN locations loc ON loc.id = la.location_id
                    WHERE vcl.invitation_id = ?
                      AND (la.id IN ({$placeholders}) OR vcl.lane_id IS NULL)
                      AND vcl.scanned_at >= ?
                      AND vcl.scanned_at <= ?

                    UNION ALL

                    -- Fallback to registration check-in if not in logs
                    SELECT 
                        iv.check_in_time AS scanned_at, 'checkin' AS action,
                        NULL AS lane_id, i.location AS lane_name,
                        NULL AS location_id, i.location AS branch, i.location AS location_access
                    FROM invitation_visitors iv
                    JOIN invitations i ON i.id = iv.invitation_id
                    WHERE iv.invitation_id = ?
                      AND iv.check_in_time >= ?
                      AND iv.check_in_time <= ?
                      AND NOT EXISTS (
                          SELECT 1 FROM visitor_card_logs vcl2 
                          WHERE vcl2.invitation_id = iv.invitation_id 
                          AND vcl2.action = 'checkin' 
                          AND vcl2.scanned_at = iv.check_in_time
                      )

                    UNION ALL

                    -- Fallback to registration check-out if not in logs
                    SELECT 
                        iv.check_out_time AS scanned_at, 'checkout' AS action,
                        NULL AS lane_id, i.location AS lane_name,
                        NULL AS location_id, i.location AS branch, i.location AS location_access
                    FROM invitation_visitors iv
                    JOIN invitations i ON i.id = iv.invitation_id
                    WHERE iv.invitation_id = ?
                      AND iv.check_out_time >= ?
                      AND iv.check_out_time <= ?
                      AND NOT EXISTS (
                          SELECT 1 FROM visitor_card_logs vcl2 
                          WHERE vcl2.invitation_id = iv.invitation_id 
                          AND vcl2.action = 'checkout' 
                          AND vcl2.scanned_at = iv.check_out_time
                      )
                ) combined
                ORDER BY scanned_at ASC";

        $params = array_merge(
            [(int) $invitationId], $laneIds, [$fromDatetime, $toDatetime], // part 1
            [(int) $invitationId], [$fromDatetime, $toDatetime],           // part 2
            [(int) $invitationId], [$fromDatetime, $toDatetime]            // part 3
        );
        $rows   = $db->query($sql, $params)->getResultArray();

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
            
            $currentLocation = $action === 'checkout' ? 'Out' : $laneLabel;
            $locationAccessed = $laneLabel;

            $movements[] = [
                'date_time' => ! empty($row['scanned_at'])
                    ? date('M j, Y g:i A', strtotime($row['scanned_at']))
                    : '—',
                'current_location'  => $currentLocation,
                'location_accessed' => $locationAccessed,
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