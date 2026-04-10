<?php

namespace App\Controllers;

class VisitorReport extends BaseController
{
    public function index()
    {
        $data = [
            'pageTitle' => 'Visitor Report - SafeG',
        ];

        return view('reports/visitor_report', $data);
    }

    public function generate()
    {
        $fromDatetime = $this->request->getPost('from_datetime');
        $toDatetime   = $this->request->getPost('to_datetime');

        if (empty($fromDatetime) || empty($toDatetime)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Date range is required.']);
        }

        $db = \Config\Database::connect();

        $sql = "SELECT
                    i.full_name        AS visitor_name,
                    i.contact          AS contact_no,
                    i.ic_passport      AS ic_no,
                    i.company          AS visitor_company,
                    i.invited_by       AS person_visited,
                    i.staff_id         AS staff_id,
                    i.reason           AS visit_reason,
                    i.vehicle_registration AS vehicle_no,
                    i.status           AS visit_status,
                    i.visit_date       AS visit_date,
                    MIN(vcl.scanned_at) AS checkin_time,
                    MAX(vcl.scanned_at) AS checkout_time,
                    COUNT(vcl.id)       AS total_scans
                FROM invitations i
                LEFT JOIN visitor_card_logs vcl ON vcl.invitation_id = i.id
                WHERE i.visit_date >= ?
                  AND i.visit_date <= ?
                GROUP BY
                    i.id, i.full_name, i.contact, i.ic_passport,
                    i.company, i.invited_by, i.staff_id, i.reason,
                    i.vehicle_registration, i.status, i.visit_date
                ORDER BY i.visit_date ASC, i.full_name ASC";

        $rows = $db->query($sql, [
            date('Y-m-d', strtotime($fromDatetime)),
            date('Y-m-d', strtotime($toDatetime)),
        ])->getResultArray();

        $visitors = [];
        foreach ($rows as $row) {
            $visitors[] = [
                'visitor_name'    => $row['visitor_name']    ?? 'N/A',
                'contact_no'      => $row['contact_no']      ?? 'N/A',
                'ic_no'           => $row['ic_no']           ?? 'N/A',
                'visitor_company' => $row['visitor_company'] ?? 'N/A',
                'person_visited'  => $row['person_visited']  ?? 'N/A',
                'staff_id'        => $row['staff_id']        ?? 'N/A',
                'visit_reason'    => $row['visit_reason']    ?? 'N/A',
                'vehicle_no'      => $row['vehicle_no']      ?? '-',
                'visit_status'    => ucfirst($row['visit_status'] ?? 'N/A'),
                'visit_date'      => $row['visit_date']      ? date('d/m/Y', strtotime($row['visit_date'])) : '-',
                'checkin_time'    => $row['checkin_time']    ? date('d/m/Y H:i', strtotime($row['checkin_time'])) : '-',
                'checkout_time'   => $row['checkout_time']   ? date('d/m/Y H:i', strtotime($row['checkout_time'])) : '-',
                'total_scans'     => (int) $row['total_scans'],
            ];
        }

        return $this->response->setJSON([
            'success'       => true,
            'total_visitors'=> count($visitors),
            'from_datetime' => date('d M Y', strtotime($fromDatetime)),
            'to_datetime'   => date('d M Y', strtotime($toDatetime)),
            'visitors'      => $visitors,
        ]);
    }
}
