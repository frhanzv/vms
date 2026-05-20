<?php

namespace App\Controllers;

use App\Models\SubLocationModel;

class AccessReport extends BaseController
{
    protected $subLocationModel;

    public function __construct()
    {
        $this->subLocationModel = new SubLocationModel();
    }

    public function index()
    {
        $subLocations = $this->subLocationModel->getAllActive();

        $data = [
            'pageTitle'    => 'Access Report - SafeG',
            'subLocations' => $subLocations,
        ];

        return view('reports/access_report', $data);
    }

    public function generate()
    {
        $fromDatetime   = $this->request->getPost('from_datetime');
        $toDatetime     = $this->request->getPost('to_datetime');
        $subLocationIds = $this->request->getPost('sub_location_ids');  // array from multi-select

        // Normalise: accept single sub_location_id for backward compat
        if (empty($subLocationIds)) {
            $single         = $this->request->getPost('sub_location_id');
            $subLocationIds = $single ? [$single] : [];
        }

        if (empty($fromDatetime) || empty($toDatetime) || empty($subLocationIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'All fields are required.']);
        }

        // Sanitise to integers
        $subLocationIds = array_values(array_filter(array_map('intval', (array) $subLocationIds)));
        if (empty($subLocationIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid location selection.']);
        }

        $db = \Config\Database::connect();

        // Fetch selected sub-locations
        $subLocations = $this->subLocationModel->whereIn('id', $subLocationIds)->findAll();
        if (empty($subLocations)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Location not found.']);
        }
        $locationName = count($subLocations) === 1
            ? $subLocations[0]['id'] . '. ' . $subLocations[0]['name']
            : count($subLocations) . ' Locations';

        // Resolve the parent location_ids for the selected sub-locations,
        // then get all lane IDs that share those location_ids.
        $parentLocationIds  = array_values(array_unique(array_column($subLocations, 'location_id')));
        $locPlaceholders    = implode(',', array_fill(0, count($parentLocationIds), '?'));
        $laneRows           = $db->query(
            "SELECT id FROM lanes WHERE location_id IN ({$locPlaceholders}) AND status = 'active'",
            $parentLocationIds
        )->getResultArray();
        $laneIds = array_column($laneRows, 'id');

        // Build IN placeholders for lane IDs (may be empty if no lanes found)
        $lanePlaceholders = !empty($laneIds)
            ? implode(',', array_fill(0, count($laneIds), '?'))
            : 'NULL';

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
                    COALESCE(
                        (
                            SELECT COALESCE(sl_via_lane.name, sl_via_direct.name)
                            FROM visitor_card_logs vcl_last
                            LEFT JOIN lanes la_last       ON la_last.id       = vcl_last.lane_id
                            LEFT JOIN sub_locations sl_via_lane   ON sl_via_lane.location_id   = la_last.location_id
                            LEFT JOIN sub_locations sl_via_direct ON sl_via_direct.id = vcl_last.sub_location_id
                            WHERE vcl_last.invitation_id = i.id
                              AND vcl_last.action != 'assigned'
                              AND COALESCE(sl_via_lane.name, sl_via_direct.name) IS NOT NULL
                            ORDER BY vcl_last.scanned_at DESC, vcl_last.id DESC
                            LIMIT 1
                        ),
                        NULLIF(i.location, ''),
                        'N/A'
                    ) AS location_name,
                    CASE 
                        WHEN COUNT(vcl.id) > 0 THEN COUNT(vcl.id)
                        WHEN iv.check_in_time IS NOT NULL THEN 1
                        ELSE 0
                    END AS total_access
                FROM invitation_visitors iv
                JOIN invitations i ON i.id = iv.invitation_id
                LEFT JOIN visitor_card_logs vcl ON vcl.invitation_id = i.id
                LEFT JOIN lanes la ON la.id = vcl.lane_id
                LEFT JOIN sub_locations sl ON sl.location_id = la.location_id
                WHERE (
                    ((la.id IN ({$lanePlaceholders}) OR vcl.lane_id IS NULL) AND vcl.scanned_at >= ? AND vcl.scanned_at <= ?)
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
            'success'        => true,
            'total_visitors' => count($visitors),
            'location_name'  => $locationName,
            'from_datetime'  => date('M j, Y g:i A', strtotime($fromDatetime)),
            'to_datetime'    => date('M j, Y g:i A', strtotime($toDatetime)),
            'visitors'       => $visitors,
        ]);
    }

    /**
     * Per-invitation access events for the movement history modal.
     */
    public function movementHistory()
    {
        $fromDatetime   = $this->request->getPost('from_datetime');
        $toDatetime     = $this->request->getPost('to_datetime');
        $subLocationIds = $this->request->getPost('sub_location_ids');
        $invitationId   = $this->request->getPost('invitation_id');

        // Backward compat
        if (empty($subLocationIds)) {
            $single         = $this->request->getPost('sub_location_id');
            $subLocationIds = $single ? [$single] : [];
        }

        $subLocationIds = array_values(array_filter(array_map('intval', (array) $subLocationIds)));

        if (empty($fromDatetime) || empty($toDatetime) || empty($subLocationIds) || empty($invitationId)) {
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

        // Resolve lane IDs from sub-location parent locations
        $subLocations      = $this->subLocationModel->whereIn('id', $subLocationIds)->findAll();
        $parentLocationIds = array_values(array_unique(array_column($subLocations, 'location_id')));
        $locPlaceholders   = implode(',', array_fill(0, count($parentLocationIds), '?'));
        $laneRows          = $db->query(
            "SELECT id FROM lanes WHERE location_id IN ({$locPlaceholders}) AND status = 'active'",
            $parentLocationIds
        )->getResultArray();
        $laneIds          = array_column($laneRows, 'id');
        $lanePlaceholders = !empty($laneIds)
            ? implode(',', array_fill(0, count($laneIds), '?'))
            : 'NULL';

        $sql = "SELECT
                    scanned_at, action, lane_id, lane_name,
                    location_id, branch, location_access
                FROM (
                    SELECT
                        vcl.scanned_at, vcl.action,
                        COALESCE(sl_lane.id, sl_direct.id) AS lane_id,
                        COALESCE(sl_lane.name, sl_direct.name) AS lane_name,
                        COALESCE(la.location_id, sl_direct.location_id) AS location_id,
                        loc.branch, loc.location_access
                    FROM visitor_card_logs vcl
                    LEFT JOIN lanes la ON la.id = vcl.lane_id
                    LEFT JOIN sub_locations sl_lane ON sl_lane.location_id = la.location_id
                    LEFT JOIN sub_locations sl_direct ON sl_direct.id = vcl.sub_location_id
                    LEFT JOIN locations loc ON loc.id = COALESCE(la.location_id, sl_direct.location_id)
                    WHERE vcl.invitation_id = ?
                      AND vcl.action != 'assigned'
                      AND (la.id IN ({$lanePlaceholders}) OR vcl.lane_id IS NULL)
                      AND vcl.scanned_at >= ?
                      AND vcl.scanned_at <= ?

                    UNION ALL

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
            [(int) $invitationId], $laneIds, [$fromDatetime, $toDatetime],
            [(int) $invitationId], [$fromDatetime, $toDatetime],
            [(int) $invitationId], [$fromDatetime, $toDatetime]
        );
        $rows = $db->query($sql, $params)->getResultArray();

        $movements = [];
        foreach ($rows as $row) {
            $laneName = (string) ($row['lane_name'] ?? '');
            $locationDisplay = $laneName !== '' ? $laneName : '—';

            $action    = strtolower((string) ($row['action'] ?? 'checkin'));
            $typeLabels = [
                'checkin'       => 'Checkin',
                'checkout'      => 'Checkout',
                'door_access'   => 'Door Access',
                'door_checkin'  => 'In',
                'door_checkout' => 'Out',
            ];
            $typeLabel = $typeLabels[$action] ?? ucfirst(str_replace('_', ' ', $action));

            $currentLocation  = $action === 'checkout' ? 'Out' : $locationDisplay;
            $locationAccessed = $locationDisplay;

            $movements[] = [
                'date_time'         => ! empty($row['scanned_at'])
                    ? date('M j, Y g:i A', strtotime($row['scanned_at']))
                    : '—',
                'current_location'  => $currentLocation,
                'location_accessed' => $locationAccessed,
                'access'            => 'Yes',
                'access_granted'    => true,
                'reason'            => '—',
                'type'              => $typeLabel,
                'action'            => 'Allowed',
                'action_allowed'    => true,
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