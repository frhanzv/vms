<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Get current user data
        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $currentUser = $userModel->find($userId);
        
        // Sample data for the dashboard
        $data = [
            'pageTitle' => 'Host Dashboard - SafeG',
            'currentDate' => date('F jS, Y'),
            'userName' => $currentUser['full_name'] ?? session()->get('full_name') ?? 'Ahmad',
            'userRole' => $currentUser['role'] ?? session()->get('role') ?? 'Host Admin',
            'userPhoto' => $currentUser['profile_photo'] ?? null,
            'stats' => [
                'expectedToday' => 12,
                'currentlyOnSite' => 5,
                'checkedOut' => 8,
                'outOfWindow' => 3
            ],
            'visitors' => [
                [
                    'name' => 'Siti Aminah',
                    'email' => 'sarah@techcorp.com',
                    'company' => 'TechCorp',
                    'host' => 'Ahmad',
                    'time' => '10:00 AM',
                    'status' => 'On-Site',
                    'statusClass' => 'green',
                    'hasImage' => true,
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBKU4O32CkAWBcbyvUxbpg-g824zi9-4NFslhX39LAlIBIPFCWsN9xARVCDXVgurxCi1AIegBS3twFp_h7UVDmR_775iFsru4kxmAxI2UpwFYP96dTRCm5Ohh3dOBpo3BMvoLAlJVwsD7ZTBzO5jDFNcAnY6MbEjQP7Im0YmuiZtXqr48J7G_ZYLV3P0-qs41IVLGsaRXU8-6_6pfYrC9JSX5_UPFNXc2O02YbeWTV-vOPTLqKqmyPxN_YXInJ8-fYp5YR-vdbCFw'
                ],
                [
                    'name' => 'David Kim',
                    'email' => 'david@designstudio.io',
                    'company' => 'Design Studio',
                    'host' => 'Lisa Wong',
                    'time' => '01:30 PM',
                    'status' => 'Pre-Arrival',
                    'statusClass' => 'amber',
                    'hasImage' => false,
                    'initials' => 'DK'
                ],
                [
                    'name' => 'Marcus Johnson',
                    'email' => 'marcus.j@contractor.com',
                    'company' => 'Independent',
                    'host' => 'Ahmad',
                    'time' => '02:00 PM',
                    'status' => 'Pre-Arrival',
                    'statusClass' => 'amber',
                    'hasImage' => true,
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC7Qy_sApFg1QnxPAmAyI8Cy1KKzt_ZEqRYoTR9jOLnElybTrKrTFrb49KgzFpgS2a5WFpuOK4JOUm4xTxKUEuaaeonUIZ9xVGsr7d1Ry5_dUvGMFyB6y2moLTab8PW2-LcUNIB5Keoev8XxpmKNgVI2Ht4VjFovI_Q5E4MQVZvYRn86_3l0BFNXrxgJuKurq6gq-wcnsLowiKge4wIVLYQE21ZIZx3hQ7xL1elCZkRGiC8fPmCC7go_3EJYG1L3SNFwmwbJpX65Q'
                ],
                [
                    'name' => 'Mohd Rizal',
                    'email' => 'michael@vendor.com',
                    'company' => 'Vendor Inc.',
                    'host' => 'John Doe',
                    'time' => '09:00 AM',
                    'status' => 'Checked Out',
                    'statusClass' => 'slate',
                    'hasImage' => false,
                    'initials' => 'MR'
                ]
            ],
            'recentActivity' => [
                [
                    'name' => 'Siti Aminah',
                    'action' => 'checked in',
                    'time' => '2 min ago',
                    'location' => 'Lobby iPad',
                    'status' => 'online',
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAz34EtcHPU0TOXXotMqxNayGWngTC-vYcpMz-hgnjU1rHvB0Mnp_wxtiX_sfgjowdKu7k9KXs-fd49ANOLODpPm18MQFc-gR554bdkqp2C6u9DB326G5pDzehAsCmeMJCgQdONbUrWzE43opaayoqJjITQdpskWjy1jgo8eqOnHCbmr7BYJUTA5JqqWaFAU06VPapaHbl1Hfx46FxDQpTz1C9TiyUWwYUYAgBz5HD_44FwlPYpCPk7l7ZYS1N1ANhhH-tDocdk9g'
                ],
                [
                    'name' => 'Mohd Rizal',
                    'action' => 'checked out',
                    'time' => '15 min ago',
                    'location' => 'Reception',
                    'status' => 'offline',
                    'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCUSWhh5vn_F1u1r-Qaocf45cYPNiS8QOxzRujmdrltSN13VrY7_VIEWej7Ce8fXSznVu0LxucIxqdm4lIN5zQ7CN2XiKYdorjK0yP7ElbavS3FZb8GWmhoks3fE-30TJOBpVKQAwan8wNyun_DPz3eE7VDqBb1SxPZXFqvBhCdRx0zPc6OnsaHkKS1lGRftMpUENqfkQzH0gOe5JAEQCWUQ0wp_FtZl6UShPjibL_MayobdYOnjNQfoyJbEQ1I94qMn-q4VQdTPA'
                ],
                [
                    'name' => 'Jamil Hashim',
                    'action' => 'invitation created',
                    'time' => '1 hour ago',
                    'location' => 'by Ahmad',
                    'status' => 'pending',
                    'initials' => 'JH'
                ]
            ]
        ];

        return view('dashboard', $data);
    }
}
