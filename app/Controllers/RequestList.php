<?php

namespace App\Controllers;

class RequestList extends BaseController
{
    public function index()
    {
        $data = [
            'pageTitle' => 'Request List - SafeG',
            'stats' => [
                'pending' => 12,
                'flagged' => 3,
                'expected' => 45,
                'rejected' => 2
            ],
            'currentRequest' => [
                'id' => 'VIS-8821',
                'name' => 'Shamsul',
                'company' => 'TechCorp Sdn Bhd',
                'host' => 'Noraini',
                'arrival' => 'In 15 mins (10:00 AM)',
                'purpose' => 'Quarterly Review',
                'photo' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDk469b-dKrunbvMNHTyL5Obu_L7mF3omTA2fcCSHrIbzujcSpw1mOwDDcYJVLyN-PZQNAktz3SHGt6biuGblutHU5aItGkUFSqNbUFtcNFsVcgwhDhwO-tIepo5aiLNXFxm1dybY6s63e-rJdQXZON287__Ts0F6xCkc5FKET4LMHukMKeKy0m8Qm18ljPJSG9rwRZToV8gu7UF2P4C-p6mxxAJ_jLMBj2EClk8alTsj2kqn_YI7cG3aniGJmmBvopkufgP_CoIQ',
                'access_zones' => ['Lobby', 'Floor 4 (Conference)'],
                'assets' => [
                    ['type' => 'laptop_mac', 'name' => 'MacBook Pro (Serial: XYZ-123)']
                ],
                'notes' => 'Visitor requires WiFi guest access.',
                'watchlist_status' => 'cleared',
                'ai_match' => 98
            ],
            'queueRequests' => [
                [
                    'name' => 'Shamsul',
                    'company' => 'TechCorp Sdn Bhd',
                    'host' => 'Noraini',
                    'time' => '15m',
                    'photo' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBdLM8uzH-P3uDos8--DCzKHjNiHHZbeU0lVLSHi4yP-4G4VA-AqHNrdyCINgKp6F6etWdBR59oOSg91QbgM4hJVfxz52SS4R5ruuBZzhouyxPDNMZfA793MViSvq1d9B0ct_fdYU7XH752nMqM8ApHVlJWMA7UT4NLM1S0_M4KWFxGzjXLnmgwt2SkwMI1uIuydQhbRYGOb_xETlf-zAd7fqaKBqViFq3AxvzzXRATKMKIf7oSKQmKvFfnNM6hAOVpZtDTqUXZEQ',
                    'status' => 'active',
                    'is_flagged' => false
                ],
                [
                    'name' => 'Amir Ali',
                    'company' => 'Global Consulting',
                    'host' => '',
                    'time' => '35m',
                    'photo' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAH-QPeb_iYX3TwjnpK-BSXdsR2z8Oz4CV9N3ipX3kO93QNPoAmoUXbmpNZsaIvK5qOlUR-gQDf6zX6bwD-wyz1dIwdQGtoZMt56slCUSkenucXqFbC6Bei8It5zKzSd8RdG9iIq09nObpdjqaQ1rLKIN61BIsfl38rtvpFcM4i9KT-Z1tfuoXja1foz30ciWnoR-U6MLBADPZ2IabCQKr2SqZtnVxtQ6AIuXNwqXn9wMsXHiE_K_aOTYVL2H53qdLge05uGfV0wg',
                    'status' => 'warning',
                    'is_flagged' => true
                ],
                [
                    'name' => 'Robert Chen',
                    'company' => 'Delivery: DS Experts Sdn Bhd',
                    'host' => 'Dock B',
                    'time' => '1h',
                    'photo' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDVT_-rpiVcyDb0fcYXZZDfnGtZsZWo58qCdmQGizxMtnP8OioQMnRUFQ2DLnX3LMJhrF5V_Yq4ousiNpoX7Pu6XJmk6VefDsc1RkvPSPZsVzp6xrlpzsXmGFsijTQZadt6WfjZZiQJlANJzwjZHArEBYd9lSI8GKg_ASl_nuhElGmVp4ukhFbanlx29gvMXixKWe4-c_w2mDFT3tEOd2ZNqCuKU01SH3REAeZad-bIBuUsnFeA7OND3XCCSCKocQ9pErvcorm4CQ',
                    'status' => 'normal',
                    'is_flagged' => false,
                    'icon' => 'local_shipping'
                ],
                [
                    'name' => 'Navin Kumar',
                    'company' => 'Independent Contractor',
                    'host' => 'Maintenance',
                    'time' => '2h',
                    'photo' => '',
                    'initials' => 'NK',
                    'status' => 'normal',
                    'is_flagged' => false,
                    'icon' => 'engineering'
                ]
            ]
        ];

        return view('requests/list', $data);
    }
}
