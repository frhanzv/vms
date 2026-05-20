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

        // Resolve all active lane IDs that belong to the same parent location
        $laneRows = $db->query(
            "SELECT id FROM lanes WHERE location_id = ? AND status = 'active'",
            [(int) $subLoc['location_id']]
        )->getResultArray();
        $laneIds = array_column($laneRows, 'id');

        if (empty($laneIds)) {
            return $this->response->setJSON([
                'success'         => true,
                'total_visitors'  => 0,
                'visitors'        => [],
                'location_text'   => strtoupper($subLoc['name']),
                'date_range_text' => date('Y-m-d', strtotime($fromDate)) . ' to ' . date('Y-m-d', strtotime($toDate)),
                'last_updated'    => date('M j, Y g:i A'),
            ]);
        }

        $lanePlaceholders = implode(',', array_fill(0, count($laneIds), '?'));

        $builder = $db->table('visitor_card_logs vcl');
        $builder->select('
            i.id AS invitation_id, 
            i.full_name AS visitor_name, 
            i.contact AS contact_no, 
            i.staff_id as staff_no, 
            i.invited_by as person_visited,
            i.company as company,
            i.ic_passport as ic_passport,
            i.reason as reason,
            i.status as visit_status,
            DATE(i.created_at) as invitation_date,
            vcl.scanned_at AS checkin_time, 
            sl.id AS sub_location_id, 
            sl.name AS location_name
        ');
        $builder->join('invitations i', 'i.id = vcl.invitation_id');
        $builder->join('lanes la', 'la.id = vcl.lane_id');
        $builder->join('sub_locations sl', 'sl.location_id = la.location_id AND sl.id = ' . (int) $subLocationId);

        $builder->where('vcl.action', 'checkin');
        $builder->where("DATE(vcl.scanned_at) >=", $fromDate);
        $builder->where("DATE(vcl.scanned_at) <=", $toDate);
        $builder->whereIn('la.id', $laneIds);

        $builder->orderBy('vcl.scanned_at', 'ASC');

        $rows    = $builder->get()->getResultArray();
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