<?php

namespace App\Controllers;

class VisitorList extends BaseController
{
    public function index()
    {
        // Sample visitor data
        $data = [
            'pageTitle' => 'Visitor List - SafeG',
            'stats' => [
                'total' => 1392,
                'checkedIn' => 845,
                'pending' => 42
            ],
            'visitors' => [
                [
                    'no' => 1,
                    'date' => '17/12/2025',
                    'full_name' => 'MOHD NAZARUDIN BIN AWANG LONG',
                    'ic_passport' => '940408115099',
                    'contact' => '0136576085',
                    'vehicle_reg' => null,
                    'location' => 'PHASE 1',
                    'type' => 'Walk-In',
                    'card_status' => 'Active',
                    'pass_no' => '413632',
                    'reason' => 'OTHER'
                ],
                [
                    'no' => 2,
                    'date' => '17/12/2025',
                    'full_name' => 'MUHAMAD FAIZ BIN ZAKAWI',
                    'ic_passport' => '911229065265',
                    'contact' => '01125520740',
                    'vehicle_reg' => 'WC9958E',
                    'location' => 'WORKSHOP PHASE 2',
                    'type' => 'Invitation',
                    'card_status' => null,
                    'pass_no' => null,
                    'reason' => 'OTHER'
                ],
                [
                    'no' => 3,
                    'date' => '17/12/2025',
                    'full_name' => 'AHMAD ZUHAIRI BIN AZMA',
                    'ic_passport' => '970609115025',
                    'contact' => '0126548445',
                    'vehicle_reg' => 'VJA1139',
                    'location' => 'PHASE 1',
                    'type' => 'Walk-In',
                    'card_status' => null,
                    'pass_no' => null,
                    'reason' => 'OTHER'
                ],
                [
                    'no' => 4,
                    'date' => '17/12/2025',
                    'full_name' => 'MUHAMMAD HIDAYAT BIN NGAH',
                    'ic_passport' => '840201115093',
                    'contact' => '0136576077',
                    'vehicle_reg' => null,
                    'location' => 'PHASE 1',
                    'type' => 'Walk-In',
                    'card_status' => null,
                    'pass_no' => null,
                    'reason' => 'OTHER'
                ],
                [
                    'no' => 5,
                    'date' => '17/12/2025',
                    'full_name' => 'N.JUHARI BIN A.RAHMAN',
                    'ic_passport' => '740115115419',
                    'contact' => '0195165419',
                    'vehicle_reg' => '',
                    'location' => 'PHASE 1',
                    'type' => 'Invitation',
                    'card_status' => 'Active',
                    'pass_no' => '413499',
                    'reason' => 'CATERING'
                ],
                [
                    'no' => 6,
                    'date' => '17/12/2025',
                    'full_name' => 'THURGESWARAN A/L KANAPATHI',
                    'ic_passport' => '990521065789',
                    'contact' => '01116996095',
                    'vehicle_reg' => '',
                    'location' => 'PHASE 1',
                    'type' => 'Invitation',
                    'card_status' => null,
                    'pass_no' => null,
                    'reason' => 'DELIVERY'
                ],
                [
                    'no' => 7,
                    'date' => '17/12/2025',
                    'full_name' => 'SELANGOR',
                    'ic_passport' => '990608106961',
                    'contact' => '0173599873',
                    'vehicle_reg' => 'ANR6673',
                    'location' => 'KSB PHASE 2 GATE',
                    'type' => 'Invitation',
                    'card_status' => 'Active',
                    'pass_no' => '413667',
                    'reason' => 'SITE VISIT'
                ],
                [
                    'no' => 8,
                    'date' => '17/12/2025',
                    'full_name' => 'CHE WAN MUHAMMAD IZZAT BIN CHE WAN BADERI',
                    'ic_passport' => '990913115550',
                    'contact' => '01112982098',
                    'vehicle_reg' => 'DDM 5584',
                    'location' => 'KSB PHASE 2 GATE',
                    'type' => 'Invitation',
                    'card_status' => 'Active',
                    'pass_no' => '413726',
                    'reason' => 'SITE VISIT'
                ]
            ]
        ];

        return view('visitors/list', $data);
    }
}
