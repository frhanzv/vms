<?php

namespace App\Controllers;

class VisitorLogbook extends BaseController
{
    public function index()
    {
        // Sample logbook data
        $data = [
            'pageTitle' => 'Visitor Logbook - SafeG',
            'records' => [
                [
                    'visitor_name' => 'Nurul Izzah',
                    'company' => 'TechCorp Solutions',
                    'host' => 'David Miller',
                    'checkin' => 'Oct 24, 09:15 AM',
                    'status' => 'Checked In',
                    'status_class' => 'green'
                ],
                [
                    'visitor_name' => 'Tan Wei Ming',
                    'company' => 'Freelance',
                    'host' => 'Sarah Connor',
                    'checkin' => 'Oct 24, 10:00 AM',
                    'status' => 'Checked In',
                    'status_class' => 'green'
                ],
                [
                    'visitor_name' => 'Priya a/p Muthusamy',
                    'company' => 'Logistics Plus',
                    'host' => 'Warehouse Ops',
                    'checkin' => 'Oct 23, 02:45 PM',
                    'status' => 'Checked Out',
                    'status_class' => 'slate'
                ],
                [
                    'visitor_name' => 'Ahmad bin Razak',
                    'company' => 'Maintenance Co',
                    'host' => 'Facility Mgr',
                    'checkin' => 'Oct 23, 08:30 AM',
                    'status' => 'Checked Out',
                    'status_class' => 'slate'
                ],
                [
                    'visitor_name' => 'Lee Mei Ling',
                    'company' => 'Consulting Grp',
                    'host' => 'Executive Team',
                    'checkin' => 'Oct 22, 11:00 AM',
                    'status' => 'Checked Out',
                    'status_class' => 'slate'
                ]
            ]
        ];

        return view('visitors/logbook', $data);
    }
}
