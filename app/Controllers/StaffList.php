<?php

namespace App\Controllers;

class StaffList extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $results = $db->table('staff s')
            ->select('s.*, c.status as card_status, c.expiry_date as card_expiry')
            ->join('staff_cards c', 'c.staff_id = s.id', 'left')
            ->orderBy('s.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $totalStaff = count($results);
        $checkedIn = 0;
        $withCard = 0;

        $staffList = [];

        foreach ($results as $index => $row) {

            if (!empty($row['check_in_time'])) {
                $checkedIn++;
            }

            if (!empty($row['card_status'])) {
                $withCard++;
            }

            $staffList[] = [
                'id'                => $row['id'],
                'no'                => $index + 1,
                'date'              => date('d/m/Y', strtotime($row['created_at'])),
                'app_no'            => $row['app_no'] ?? 'N/A',
                'full_name'         => $row['full_name'] ?? 'N/A',
                'ic_passport'       => $row['ic_passport'] ?? 'N/A',
                'staff_no'          => $row['staff_no'] ?? 'N/A',
                'status'            => $row['status'] ?? 'N/A',
                'suspension_period' => $row['suspension_period'] ?? '-',
                'next_action'       => $row['next_action'] ?? '-',
                'card_status'       => $row['card_status'] ?? '-',
                'card_expiry'       => $row['card_expiry'] ?? '-',
                'remark'            => $row['remark'] ?? '-'
            ];
        }

        $companyId       = (int) session()->get('company_id');
        $formFieldModel  = new \App\Models\ClientFormFieldModel();
        $showPrintButton = $formFieldModel->isEnabled($companyId, 'staff_pass_request', 'print_button');

        return view('staffs/list', [
            'pageTitle'       => 'Staff List - SafeG',
            'stats' => [
                'total'     => $totalStaff,
                'checkedIn' => $checkedIn,
                'withCard'  => $withCard,
            ],
            'staffList'       => $staffList,
            'showPrintButton' => $showPrintButton,
        ]);
    }
}
