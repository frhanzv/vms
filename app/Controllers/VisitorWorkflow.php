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
        $steps = $this->flowService->getOrderedSteps(false); // Get all steps including disabled
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

        $activeWorkflows = [];
        $disabledWorkflows = [];

        foreach ($steps as $index => $step) {
            $meta = $workflowMeta[$step['key']] ?? [
                'icon' => 'list_alt',
                'icon_color' => 'primary',
                'trigger_icon' => 'bolt',
                'trigger' => 'Invitation flow step',
            ];

            if ($step['key'] === 'scan_mykad') {
                $routeLabel = $step['route'] . '?step=scan_mykad';
            } elseif (str_starts_with($step['route'], 'security/')) {
                $routeLabel = $step['route'] . '?flow_step=' . $step['key'];
            } else {
                $routeLabel = $step['route'];
            }

            $workflowData = [
                'id' => $step['id'],
                'key' => $step['key'],
                'name' => $step['label'],
                'route' => $routeLabel,
                'db_route' => $step['db_route'],
                'icon' => $meta['icon'],
                'icon_color' => $meta['icon_color'],
                'trigger_icon' => $meta['trigger_icon'],
                'trigger' => $step['trigger_event'] ?: $meta['trigger'],
                'step_order' => $step['step_order'],
                'is_active' => $step['is_active'],
            ];

            if ($step['is_active']) {
                $activeWorkflows[] = $workflowData;
            } else {
                $disabledWorkflows[] = $workflowData;
            }
        }

        $data = [
            'pageTitle' => 'Visitor Workflow Management - SafeG',
            'activeWorkflows' => $activeWorkflows,
            'disabledWorkflows' => $disabledWorkflows,
            'stats' => [
                'total' => count($steps),
                'active_workflows' => count($activeWorkflows),
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

    public function toggleActive($id)
    {
        $workflowModel = new \App\Models\WorkflowModel();
        $workflow = $workflowModel->find($id);
        if (!$workflow) {
            return $this->response->setJSON(['success' => false, 'message' => 'Workflow not found']);
        }

        $newState = $workflow['is_active'] ? 0 : 1;
        $workflowModel->update($id, ['is_active' => $newState]);

        return $this->response->setJSON(['success' => true, 'is_active' => $newState]);
    }

    public function editWorkflow($id)
    {
        $workflowModel = new \App\Models\WorkflowModel();
        $name = (string) $this->request->getPost('step_name');
        $route = (string) $this->request->getPost('route');
        $triggerEvent = (string) $this->request->getPost('trigger_event');
        
        if (trim($name) === '') {
            return redirect()->back()->with('error', 'Workflow name cannot be empty.');
        }

        $workflowModel->update($id, [
            'step_name' => trim($name),
            'route' => trim($route) === '' ? null : trim($route),
            'trigger_event' => trim($triggerEvent) === '' ? null : trim($triggerEvent),
        ]);
        return redirect()->back()->with('success', 'Workflow updated successfully.');
    }

    public function deleteWorkflow($id)
    {
        $workflowModel = new \App\Models\WorkflowModel();
        if ($workflowModel->delete($id)) {
            return redirect()->back()->with('success', 'Workflow deleted successfully.');
        }
        return redirect()->back()->with('error', 'Failed to delete workflow.');
    }

    public function create()
    {
        $steps = $this->flowService->getOrderedSteps(true);
        $allSteps = $this->flowService->getOrderedSteps(false);

        return view('visitors/workflow_create', [
            'pageTitle' => 'Edit Visitor Workflow - SafeG',
            'steps' => $steps,
            'allSteps' => $allSteps,
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
