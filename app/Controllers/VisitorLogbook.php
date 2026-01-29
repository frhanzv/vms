<?php

namespace App\Controllers;

use App\Models\InvitationVisitorModel;
use App\Models\InvitationModel;
use App\Models\VisitorCardLogModel;

class VisitorLogbook extends BaseController
{
    protected $invitationVisitorModel;
    protected $invitationModel;
    protected $cardLogModel;

    public function __construct()
    {
        $this->invitationVisitorModel = new InvitationVisitorModel();
        $this->invitationModel = new InvitationModel();
        $this->cardLogModel = new VisitorCardLogModel();
    }

    public function index()
    {
        // Get filter parameters
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $search = $this->request->getGet('search') ?? '';

        // Get visitor logbook data
        $db = \Config\Database::connect();
        
        // Build query without subquery in JOIN
        $sql = "SELECT iv.*, 
                       i.full_name as visitor_name, 
                       i.invited_by as host_name,
                       i.company as visitor_company,
                       i.created_at as visit_date,
                       vc.card_id as card_epc,
                       (SELECT l.lane FROM visitor_card_logs vcl2 
                        LEFT JOIN lanes l ON l.id = vcl2.lane_id 
                        WHERE vcl2.invitation_id = iv.invitation_id 
                        ORDER BY vcl2.scanned_at DESC LIMIT 1) as lane_name
                FROM invitation_visitors iv
                LEFT JOIN invitations i ON i.id = iv.invitation_id
                LEFT JOIN visitor_cards vc ON vc.id = iv.visitor_card_id
                WHERE i.status = ?
                AND iv.check_in_time IS NOT NULL
                AND DATE(i.created_at) >= ?
                AND DATE(i.created_at) <= ?";
        
        $params = ['Approved', $startDate, $endDate];
        
        // Apply search filter
        if (!empty($search)) {
            $sql .= " AND (i.full_name LIKE ? OR i.company LIKE ? OR i.invited_by LIKE ? OR vc.card_id LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        $sql .= " ORDER BY iv.check_in_time DESC";
        
        $results = $db->query($sql, $params)->getResultArray();

        // Format records
        $records = [];
        foreach ($results as $row) {
            $status = 'Pending';
            $statusClass = 'yellow';

            if ($row['check_in_time']) {
                if ($row['check_out_time']) {
                    $status = 'Checked Out';
                    $statusClass = 'slate';
                } else {
                    $status = 'Checked In';
                    $statusClass = 'green';
                }
            }

            // Calculate duration if checked out
            $duration = null;
            if ($row['check_in_time'] && $row['check_out_time']) {
                $checkIn = strtotime($row['check_in_time']);
                $checkOut = strtotime($row['check_out_time']);
                $durationSeconds = $checkOut - $checkIn;
                $hours = floor($durationSeconds / 3600);
                $minutes = floor(($durationSeconds % 3600) / 60);
                $duration = sprintf('%dh %dm', $hours, $minutes);
            }

            $records[] = [
                'id' => $row['id'],
                'visitor_name' => $row['visitor_name'] ?? 'N/A',
                'company' => $row['visitor_company'] ?? 'N/A',
                'host' => $row['host_name'] ?? 'N/A',
                'visit_date' => date('M d, Y', strtotime($row['visit_date'])),
                'checkin' => $row['check_in_time'] ? date('M d, h:i A', strtotime($row['check_in_time'])) : '-',
                'checkout' => $row['check_out_time'] ? date('M d, h:i A', strtotime($row['check_out_time'])) : '-',
                'duration' => $duration,
                'card_epc' => $row['card_epc'] ?? '-',
                'lane' => $row['lane_name'] ?? '-',
                'status' => $status,
                'status_class' => $statusClass
            ];
        }

        // Get statistics
        $stats = [
            'total_today' => $this->invitationModel
                ->where('status', 'Approved')
                ->where('DATE(created_at)', date('Y-m-d'))
                ->countAllResults(),
            'checked_in' => $db->table('invitation_visitors')
                ->where('check_in_time IS NOT NULL')
                ->where('check_out_time IS NULL')
                ->countAllResults(),
            'checked_out_today' => $db->table('invitation_visitors iv')
                ->join('invitations i', 'i.id = iv.invitation_id')
                ->where('DATE(iv.check_out_time)', date('Y-m-d'))
                ->countAllResults()
        ];

        $data = [
            'pageTitle' => 'Visitor Logbook - SafeG',
            'records' => $records,
            'stats' => $stats,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'search' => $search
            ]
        ];

        return view('visitors/logbook', $data);
    }
}
