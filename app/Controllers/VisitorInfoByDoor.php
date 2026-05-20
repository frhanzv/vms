<?php

namespace App\Controllers;

use App\Models\SubLocationModel;

class VisitorInfoByDoor extends BaseController
{
    protected $subLocationModel;

    public function __construct()
    {
        $this->subLocationModel = new SubLocationModel();
    }

    public function index()
    {
        return view('reports/visitor_info_by_door', [
            'pageTitle'    => 'Visitor Info By Door - SafeG',
            'subLocations' => $this->subLocationModel->getAllActive(),
        ]);
    }

    public function generate()
    {
        $subLocationId = $this->request->getPost('sub_location_id');
        $fromDate      = $this->request->getPost('from_date');
        $toDate        = $this->request->getPost('to_date');

        if (empty($subLocationId) || empty($fromDate) || empty($toDate)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select a Sub Location and Date Range.',
            ]);
        }

        if ($fromDate > $toDate) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'From Date cannot be later than To Date.',
            ]);
        }

        $subLoc = $this->subLocationModel->find((int) $subLocationId);
        if (! $subLoc) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sub location not found.',
            ]);
        }

        $db = \Config\Database::connect();

        // Resolve lane IDs for the RFID path (lanes sharing the sub_location's parent location)
        $laneRows = $db->query(
            "SELECT id FROM lanes WHERE location_id = ? AND status = 'active'",
            [(int) $subLoc['location_id']]
        )->getResultArray();
        $laneIds = array_column($laneRows, 'id');

        // Build the WHERE clause to match either:
        //   RFID scans: vcl.lane_id belongs to lanes at this sub_location's parent location
        //   QR scans:   vcl.sub_location_id is the selected sub_location directly
        $doorCondition = 'vcl.sub_location_id = ' . (int) $subLocationId;
        if (!empty($laneIds)) {
            $lanePlaceholders = implode(',', array_map('intval', $laneIds));
            $doorCondition = "(vcl.lane_id IN ({$lanePlaceholders}) OR vcl.sub_location_id = " . (int) $subLocationId . ")";
        }

        $sql = "SELECT
                    i.id              AS invitation_id,
                    i.full_name       AS visitor_name,
                    i.contact         AS contact_no,
                    i.staff_id        AS staff_no,
                    i.invited_by      AS person_visited,
                    i.company         AS company,
                    i.ic_passport     AS ic_passport,
                    i.reason          AS reason,
                    i.status          AS visit_status,
                    DATE(i.created_at) AS invitation_date,
                    vcl.scanned_at    AS checkin_time
                FROM visitor_card_logs vcl
                JOIN invitations i ON i.id = vcl.invitation_id
                WHERE vcl.action IN ('checkin', 'door_checkin')
                  AND DATE(vcl.scanned_at) >= ?
                  AND DATE(vcl.scanned_at) <= ?
                  AND {$doorCondition}
                ORDER BY vcl.scanned_at ASC";

        $rows = $db->query($sql, [$fromDate, $toDate])->getResultArray();
        $locName = strtoupper($subLoc['name']);

        $records = [];
        foreach ($rows as $row) {
            $timeStr   = $row['checkin_time'] ? date('M j, Y g:i A', strtotime($row['checkin_time'])) : '-';

            $records[] = [
                'visitor_name'   => $row['visitor_name'] ?? 'Unknown',
                'contact_no'     => $row['contact_no'] ?: 'N/A',
                'person_visited' => $row['person_visited'] ?: 'N/A',
                'checkin_time'   => $timeStr,
                'location_name'  => $locName,

                // Fields for Details Modal
                'full_name'      => $row['visitor_name'] ?? 'Unknown',
                'ic_passport'    => $row['ic_passport'] ?: 'NULL',
                'company'        => $row['company'] ?: 'N/A',
                'reason'         => $row['reason'] ?: 'N/A',
                'date'           => $row['invitation_date'] ? date('M j, Y', strtotime($row['invitation_date'])) : '-',
                'status'         => $row['visit_status'] ?: 'Pending',
                'location'       => $locName,
            ];
        }

        return $this->response->setJSON([
            'success'         => true,
            'total_visitors'  => count($records),
            'visitors'        => $records,
            'location_text'   => $locName,
            'date_range_text' => date('Y-m-d', strtotime($fromDate)) . ' to ' . date('Y-m-d', strtotime($toDate)),
            'last_updated'    => date('M j, Y g:i A'),
        ]);
    }
}