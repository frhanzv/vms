<?php

namespace App\Controllers;

class InvitationList extends BaseController
{
    public function index()
    {
        // Sample invitation data
        $data = [
            'pageTitle' => 'Invitation List - SafeG',
            'stats' => [
                'total' => 524,
                'pending' => 42,
                'approved' => 398,
                'rejected' => 84
            ],
            'invitations' => [
                [
                    'no' => 1,
                    'date' => '17/12/2025',
                    'full_name' => 'MUHAMAD FAIZ BIN ZAKAWI',
                    'ic_passport' => '911229065265',
                    'contact' => '01125520740',
                    'vehicle_reg' => 'WC9958E',
                    'location' => 'WORKSHOP PHASE 2',
                    'company' => 'ABC CONSTRUCTION SDN BHD',
                    'invited_by' => 'Ahmad Bin Abdullah',
                    'status' => 'Approved',
                    'reason' => 'OTHER'
                ],
                [
                    'no' => 2,
                    'date' => '17/12/2025',
                    'full_name' => 'N.JUHARI BIN A.RAHMAN',
                    'ic_passport' => '740115115419',
                    'contact' => '0195165419',
                    'vehicle_reg' => '',
                    'location' => 'PHASE 1',
                    'company' => 'CATERING SERVICES',
                    'invited_by' => 'Siti Nurhaliza',
                    'status' => 'Approved',
                    'reason' => 'CATERING'
                ],
                [
                    'no' => 3,
                    'date' => '17/12/2025',
                    'full_name' => 'THURGESWARAN A/L KANAPATHI',
                    'ic_passport' => '990521065789',
                    'contact' => '01116996095',
                    'vehicle_reg' => '',
                    'location' => 'PHASE 1',
                    'company' => 'EXPRESS DELIVERY SDN BHD',
                    'invited_by' => 'Mohd Rizal',
                    'status' => 'Pending',
                    'reason' => 'DELIVERY'
                ],
                [
                    'no' => 4,
                    'date' => '17/12/2025',
                    'full_name' => 'SELANGOR',
                    'ic_passport' => '990608106961',
                    'contact' => '0173599873',
                    'vehicle_reg' => 'ANR6673',
                    'location' => 'KSB PHASE 2 GATE',
                    'company' => 'CONSULTANT ENGINEERING',
                    'invited_by' => 'Tan Ah Kow',
                    'status' => 'Approved',
                    'reason' => 'SITE VISIT'
                ],
                [
                    'no' => 5,
                    'date' => '17/12/2025',
                    'full_name' => 'CHE WAN MUHAMMAD IZZAT BIN CHE WAN BADERI',
                    'ic_passport' => '990913115550',
                    'contact' => '01112982098',
                    'vehicle_reg' => 'DDM 5584',
                    'location' => 'KSB PHASE 2 GATE',
                    'company' => 'PROJECT MANAGEMENT CO',
                    'invited_by' => 'Kumar A/L Raman',
                    'status' => 'Approved',
                    'reason' => 'SITE VISIT'
                ],
                [
                    'no' => 6,
                    'date' => '16/12/2025',
                    'full_name' => 'WONG KAH WAI',
                    'ic_passport' => '880523145678',
                    'contact' => '0123456789',
                    'vehicle_reg' => 'BCD1234',
                    'location' => 'PHASE 1',
                    'company' => 'MAINTENANCE SERVICES',
                    'invited_by' => 'Lee Chong Wei',
                    'status' => 'Rejected',
                    'reason' => 'MAINTENANCE'
                ],
                [
                    'no' => 7,
                    'date' => '16/12/2025',
                    'full_name' => 'JESSICA TAN',
                    'ic_passport' => '920301089876',
                    'contact' => '0198765432',
                    'vehicle_reg' => 'WXY9876',
                    'location' => 'PHASE 2',
                    'company' => 'SECURITY AUDIT FIRM',
                    'invited_by' => 'Abu Bakar',
                    'status' => 'Pending',
                    'reason' => 'AUDIT'
                ],
                [
                    'no' => 8,
                    'date' => '16/12/2025',
                    'full_name' => 'RAJA AZMAN BIN RAJA AHMAD',
                    'ic_passport' => '850715125566',
                    'contact' => '0177654321',
                    'vehicle_reg' => '',
                    'location' => 'WORKSHOP PHASE 2',
                    'company' => 'EQUIPMENT SUPPLIER',
                    'invited_by' => 'Fatimah Zahra',
                    'status' => 'Approved',
                    'reason' => 'DELIVERY'
                ]
            ]
        ];

        return view('invitations/list', $data);
    }

    public function create()
    {
        $data = [
            'pageTitle' => 'New Invitation - SafeG'
        ];

        return view('invitations/create', $data);
    }

    public function store()
    {
        // TODO: Implement invitation storage logic
        // This will handle form submission
        
        // For now, redirect back to invitations list
        return redirect()->to(base_url('invitations'))->with('success', 'Invitation created successfully!');
    }
}
