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

        $workflows = [];
        foreach ($steps as $index => $step) {
            $icon = 'list_alt';
            $iconColor = 'primary';
            $triggerIcon = 'bolt';
            $trigger = 'Flow step';

            if ($step['key'] === 'registration') {
                $icon = 'assignment';
                $iconColor = 'purple';
                $triggerIcon = 'person_add';
                $trigger = 'Visitor opens invitation link';
            } elseif ($step['key'] === 'security_briefing') {
                $icon = 'play_circle';
                $iconColor = 'orange';
                $triggerIcon = 'play_arrow';
                $trigger = 'After registration submit';
            } elseif ($step['key'] === 'facial_verification') {
                $icon = 'face';
                $iconColor = 'primary';
                $triggerIcon = 'verified_user';
                $trigger = 'After briefing completion';
            } elseif ($step['key'] === 'completion') {
                $icon = 'check_circle';
                $iconColor = 'primary';
                $triggerIcon = 'task_alt';
                $trigger = 'After facial verification';
            }

            $workflows[] = [
                'id' => 'WF-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'name' => $step['label'],
                'icon' => $icon,
                'icon_color' => $iconColor,
                'trigger_icon' => $triggerIcon,
                'trigger' => $trigger,
                'steps' => 1,
                'status' => 'Active',
                'status_class' => 'green',
                'modified' => 'Sequence #' . ($index + 1),
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
                'time_change' => 'Config-driven sequence',
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
        $decoded = json_decode($stepsJson, true);
        if (! is_array($decoded)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid workflow sequence.');
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
