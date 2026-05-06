<?php

namespace App\Controllers;

use App\Libraries\InvitationProcessFlowService;

class VisitorWorkflow extends BaseController
{
    protected InvitationProcessFlowService $flowService;

    public function __construct()
    {
        $this->flowService = new InvitationProcessFlowService();
    }

    public function index()
    {
        $steps = $this->flowService->getOrderedSteps();
        $db = \Config\Database::connect();
        $totalVisitors = 0;
        if ($db->tableExists('invitation_visitors')) {
            $totalVisitors = (int) $db->table('invitation_visitors')->countAllResults();
        }

        $workflowMeta = [
            'registration' => [
                'icon' => 'assignment',
                'icon_color' => 'purple',
                'trigger_icon' => 'person_add',
                'trigger' => 'Visitor opens invitation link',
            ],
            'scan_mykad' => [
                'icon' => 'badge',
                'icon_color' => 'primary',
                'trigger_icon' => 'contactless',
                'trigger' => 'After registration details are saved',
            ],
            'security_briefing' => [
                'icon' => 'shield_person',
                'icon_color' => 'orange',
                'trigger_icon' => 'play_circle',
                'trigger' => 'After prior steps (security briefing video)',
            ],
            'video' => [
                'icon' => 'play_circle',
                'icon_color' => 'orange',
                'trigger_icon' => 'smart_display',
                'trigger' => 'Additional or standalone video step',
            ],
            'take_photo' => [
                'icon' => 'photo_camera',
                'icon_color' => 'primary',
                'trigger_icon' => 'photo',
                'trigger' => 'After previous workflow step completes',
            ],
            'facial_verification' => [
                'icon' => 'face',
                'icon_color' => 'primary',
                'trigger_icon' => 'verified_user',
                'trigger' => 'Facial capture / verification step',
            ],
            'approval' => [
                'icon' => 'fact_check',
                'icon_color' => 'primary',
                'trigger_icon' => 'how_to_vote',
                'trigger' => 'Host or reception approval',
            ],
            'receive_qr' => [
                'icon' => 'qr_code_2',
                'icon_color' => 'primary',
                'trigger_icon' => 'qr_code_scanner',
                'trigger' => 'Collect entry QR or pass',
            ],
            'completion' => [
                'icon' => 'check_circle',
                'icon_color' => 'primary',
                'trigger_icon' => 'task_alt',
                'trigger' => 'Final completion / confirmation',
            ],
        ];

        $workflows = [];
        foreach ($steps as $index => $step) {
            $meta = $workflowMeta[$step['key']] ?? [
                'icon' => 'list_alt',
                'icon_color' => 'primary',
                'trigger_icon' => 'bolt',
                'trigger' => 'Invitation flow step',
            ];
            $icon = $meta['icon'];
            $iconColor = $meta['icon_color'];
            $triggerIcon = $meta['trigger_icon'];
            $trigger = $meta['trigger'];

            if ($step['key'] === 'scan_mykad') {
                $routeLabel = $step['route'] . '?step=scan_mykad';
            } elseif (str_starts_with($step['route'], 'security/')) {
                $routeLabel = $step['route'] . '?flow_step=' . $step['key'];
            } else {
                $routeLabel = $step['route'];
            }

            $workflows[] = [
                'name' => $step['label'],
                'route' => $routeLabel,
                'icon' => $icon,
                'icon_color' => $iconColor,
                'trigger_icon' => $triggerIcon,
                'trigger' => $trigger,
                'step_order' => $index + 1,
                'status' => 'Active',
                'status_class' => 'green',
                'modified' => 'Edit Sequence',
            ];
        }

        $data = [
            'pageTitle' => 'Visitor Workflow Management - SafeG',
            'steps' => $steps,
            'workflows' => $workflows,
            'stats' => [
                'total' => count($steps),
                'active_workflows' => 1,
                'active_change' => 'Invitation process enabled',
                'total_visitors' => $totalVisitors,
                'visitors_change' => 'Live visitor records',
                'avg_time' => '-',
                'time_change' => 'Drag to reorder on Edit Sequence',
                'alerts' => 0,
                'alerts_text' => 'No workflow alert',
            ],
        ];

        return view('visitors/workflow', $data);
    }

    public function create()
    {
        $steps = $this->flowService->getOrderedSteps();

        return view('visitors/workflow_create', [
            'pageTitle' => 'Edit Visitor Workflow - SafeG',
            'steps' => $steps,
        ]);
    }

    public function save()
    {
        $stepsJson = (string) $this->request->getPost('steps_json');
        $customStepsJson = (string) $this->request->getPost('custom_steps_json');
        $decoded = json_decode($stepsJson, true);
        $customDecoded = json_decode($customStepsJson, true);
        if (! is_array($decoded)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid workflow sequence.');
        }
        if (! is_array($customDecoded)) {
            $customDecoded = [];
        }

        $steps = [];
        foreach ($decoded as $item) {
            $steps[] = (string) $item;
        }

        if ($steps === []) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please add at least one workflow step.');
        }

        try {
            if (! $this->flowService->saveCustomSteps($customDecoded)) {
                throw new \RuntimeException('Unable to save custom workflows.');
            }
            if (! $this->flowService->saveSequence($steps)) {
                throw new \RuntimeException('Unable to save workflow sequence.');
            }

            return redirect()->to(base_url('workflow'))
                ->with('success', 'Workflow sequence saved successfully.');
        } catch (\Throwable $e) {
            log_message('error', 'Workflow save failed: {message}', ['message' => $e->getMessage()]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save workflow: ' . $e->getMessage());
        }
    }

}
