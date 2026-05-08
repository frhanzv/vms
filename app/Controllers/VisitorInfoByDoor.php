<?php

namespace App\Controllers;

use App\Models\LaneModel;

class VisitorInfoByDoor extends BaseController
{
    protected $laneModel;

    public function __construct()
    {
        $this->laneModel = new LaneModel();
    }

    public function index()
    {
        return view('reports/visitor_info_by_door', [
            'pageTitle' => 'Visitor Info By Door - SafeG',
            'lanes' => $this->laneModel->where('status', 'active')->orderBy('lane', 'ASC')->findAll(),
        ]);
    }

    public function generate()
    {
        $laneId = $this->request->getPost('lane_id');
        $fromDate = $this->request->getPost('from_date');
        $toDate = $this->request->getPost('to_date');

        if (empty($laneId) || empty($fromDate) || empty($toDate)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select a Lane and Date Range.',
            ]);
        }

        if ($fromDate > $toDate) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'From Date cannot be later than To Date.',
            ]);
        }

        $db = \Config\Database::connect();
        
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
            la.id AS lane_id, 
            la.lane AS location_name
        ');
        $builder->join('invitations i', 'i.id = vcl.invitation_id');
        $builder->join('lanes la', 'la.id = vcl.lane_id');
        
        $builder->where('vcl.action', 'checkin');
        $builder->where("DATE(vcl.scanned_at) >=", $fromDate);
        $builder->where("DATE(vcl.scanned_at) <=", $toDate);
        $builder->where('la.id', $laneId);
        
        $builder->orderBy('vcl.scanned_at', 'ASC');
        
        $rows = $builder->get()->getResultArray();
        
        $locRec = $this->laneModel->find($laneId);
        $locName = $locRec ? $locRec['id'] . '.' . strtoupper($locRec['lane']) : 'UNKNOWN';

        $records = [];
        foreach ($rows as $row) {
            $timeStr = $row['checkin_time'] ? date('d M Y h:i A', strtotime($row['checkin_time'])) : '-';
            
            $records[] = [
                'visitor_name'    => $row['visitor_name'] ?? 'Unknown',
                'contact_no'      => $row['contact_no'] ?: 'N/A',
                'staff_no'        => $row['staff_no'] ?: 'null',
                'person_visited'  => $row['person_visited'] ?: 'N/A',
                'checkin_time'    => $timeStr,
                'location_name'   => $locName,

                // Fields for Details Modal
                'full_name'       => $row['visitor_name'] ?? 'Unknown',
                'ic_passport'     => $row['ic_passport'] ?: 'NULL',
                'company'         => $row['company'] ?: 'N/A',
                'reason'          => $row['reason'] ?: 'N/A',
                'date'            => $row['invitation_date'] ? date('d/m/Y', strtotime($row['invitation_date'])) : '-',
                'status'          => $row['visit_status'] ?: 'Pending',
                'location'        => $locName
            ];
        }

        return $this->response->setJSON([
            'success'       => true,
            'total_visitors'=> count($records),
            'visitors'      => $records,
            'location_text' => $locName,
            'date_range_text' => date('Y-m-d', strtotime($fromDate)) . ' to ' . date('Y-m-d', strtotime($toDate)),
            'last_updated'  => date('d M Y h:i A')
        ]);
    }
}
