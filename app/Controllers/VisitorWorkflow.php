<?php

namespace App\Controllers;

class VisitorWorkflow extends BaseController
{
    public function index()
    {
        // Sample workflow data
        $data = [
            'pageTitle' => 'Visitor Workflow Management - SafeG',
            'workflows' => [
                [
                    'name' => 'Standard Visitor Check-in',
                    'id' => 'WF-2023-001',
                    'trigger' => 'iPad Kiosk',
                    'trigger_icon' => 'qr_code_scanner',
                    'steps' => 5,
                    'status' => 'Active',
                    'status_class' => 'green',
                    'modified' => '2h ago',
                    'icon' => 'badge',
                    'icon_color' => 'primary'
                ],
                [
                    'name' => 'Delivery & Logistics',
                    'id' => 'WF-2023-004',
                    'trigger' => 'Loading Dock',
                    'trigger_icon' => 'dock',
                    'steps' => 3,
                    'status' => 'Active',
                    'status_class' => 'green',
                    'modified' => 'Yesterday',
                    'icon' => 'local_shipping',
                    'icon_color' => 'purple'
                ],
                [
                    'name' => 'VIP Client Visit',
                    'id' => 'WF-2023-008',
                    'trigger' => 'Pre-registration',
                    'trigger_icon' => 'event',
                    'steps' => 8,
                    'status' => 'Draft',
                    'status_class' => 'gray',
                    'modified' => 'Oct 24, 2023',
                    'icon' => 'stars',
                    'icon_color' => 'orange'
                ],
                [
                    'name' => 'High Security Area Access',
                    'id' => 'WF-2023-012',
                    'trigger' => 'Biometric',
                    'trigger_icon' => 'fingerprint',
                    'steps' => 12,
                    'status' => 'Active',
                    'status_class' => 'green',
                    'modified' => 'Oct 20, 2023',
                    'icon' => 'verified_user',
                    'icon_color' => 'red'
                ],
                [
                    'name' => 'Contractor Temp Pass',
                    'id' => 'WF-2022-099',
                    'trigger' => 'Reception',
                    'trigger_icon' => 'badge',
                    'steps' => 4,
                    'status' => 'Archived',
                    'status_class' => 'red',
                    'modified' => 'Sep 15, 2023',
                    'icon' => 'construction',
                    'icon_color' => 'gray'
                ]
            ],
            'stats' => [
                'active_workflows' => 12,
                'active_change' => '+2 this week',
                'total_visitors' => 450,
                'visitors_change' => '+15% vs yesterday',
                'avg_time' => '1m 45s',
                'time_change' => '-5% improvement',
                'alerts' => 0,
                'alerts_text' => 'All systems normal'
            ]
        ];

        return view('visitors/workflow', $data);
    }

    public function create()
    {
        $data = [
            'pageTitle' => 'Create Workflow - SafeG',
            'workflowName' => 'Visitor Arrival Workflow',
            'workflowStatus' => 'Active'
        ];

        return view('visitors/workflow_create', $data);
    }
}
