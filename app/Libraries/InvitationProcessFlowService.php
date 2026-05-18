<?php

namespace App\Libraries;

use App\Models\SettingModel;
use App\Models\WorkflowModel;

class InvitationProcessFlowService
{
    public const SETTING_KEY = 'invitation_process_flow_sequence';
    public const CUSTOM_STEPS_SETTING_KEY = 'invitation_process_flow_custom_steps';

    /**
     * Full invitation flow: six workflow-management steps plus the original four visitor
     * steps, merged to nine unique keys (registration appears once).
     *
     * @var array<string, array{label: string, route: string, query?: array<string, string>}>
     */
    private const STEP_DEFINITIONS = [
        'registration' => [
            'label' => 'Visitor Registration',
            'route' => 'visitor-registration',
        ],
        'scan_mykad' => [
            'label' => 'Scan MYKAD',
            'route' => 'visitor-registration',
            'query' => [
                'step' => 'scan_mykad',
            ],
        ],
        'security_briefing' => [
            'label' => 'Security Briefing',
            'route' => 'security/briefing',
        ],
        'video' => [
            'label' => 'Video',
            'route' => 'security/briefing',
        ],
        'take_photo' => [
            'label' => 'Take Photo / FR',
            'route' => 'security/facial-verification',
        ],
        'facial_verification' => [
            'label' => 'Facial Verification',
            'route' => 'security/facial-verification',
        ],
        'approval' => [
            'label' => 'Approval',
            'route' => 'security/checkin',
        ],
        'receive_qr' => [
            'label' => 'Receive QR',
            'route' => 'security/completed',
        ],
        'completion' => [
            'label' => 'Completion',
            'route' => 'security/completed',
        ],
    ];

    private SettingModel $settingModel;
    private WorkflowModel $workflowModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->workflowModel = new WorkflowModel();
    }

    /**
     * @return array<int, string>
     */
    public function getSequence(): array
    {
        $workflows = $this->workflowModel
            ->where('is_active', 1)
            ->orderBy('step_order', 'ASC')
            ->findAll();

        $sequence = [];
        foreach ($workflows as $w) {
            $sequence[] = $w['step_key'];
        }

        // Bootstrap if empty
        if ($sequence === []) {
            $allowed = $this->getAllowedStepKeys();
            foreach ($allowed as $i => $key) {
                $this->workflowModel->insert([
                    'step_name' => self::STEP_DEFINITIONS[$key]['label'],
                    'step_key' => $key,
                    'step_order' => $i + 1,
                    'is_active' => 1
                ]);
                $sequence[] = $key;
            }
        }

        return $sequence;
    }

    /**
     * Update step_order for the provided sequence of step_keys.
     * Keys not in the array are unaffected (or disabled if we choose to, but we only reorder active ones).
     *
     * @param array<int, string> $sequence
     */
    public function saveSequence(array $sequence): bool
    {
        // $sequence contains active step_keys in the desired order
        $order = 1;
        foreach ($sequence as $key) {
            $workflow = $this->workflowModel->where('step_key', $key)->first();
            if ($workflow) {
                $this->workflowModel->update($workflow['id'], [
                    'step_order' => $order,
                    'is_active' => 1
                ]);
                $order++;
            }
        }
        return true;
    }

    public function saveCustomSteps(array $steps): bool
    {
        // Custom steps logic can be migrated to DB directly.
        foreach ($steps as $row) {
            $key = trim((string) ($row['key'] ?? ''));
            $label = trim((string) ($row['label'] ?? ''));
            $route = trim((string) ($row['route'] ?? ''));
            if ($key === '' || $label === '' || $route === '') {
                continue;
            }
            
            $triggerEvent = trim((string) ($row['trigger_event'] ?? ''));
            
            // For now, if we create a custom step, we just insert it into workflows table
            if (!$this->workflowModel->where('step_key', $key)->first()) {
                $this->workflowModel->insert([
                    'step_name' => $label,
                    'step_key' => $key,
                    'route' => $route,
                    'trigger_event' => $triggerEvent === '' ? null : $triggerEvent,
                    'step_order' => 999, // Will be reordered
                    'is_active' => 1
                ]);
            }
        }
        // Save the route into settings as fallback since `workflows` table has no route column
        $existing = $this->getCustomSteps();
        foreach ($steps as $s) {
            $exists = false;
            foreach ($existing as $e) {
                if ($e['key'] === $s['key']) $exists = true;
            }
            if (!$exists) $existing[] = $s;
        }
        $this->settingModel->setSetting(self::CUSTOM_STEPS_SETTING_KEY, json_encode($existing));
        return true;
    }

    /**
     * @return array<int, array{id: int, key: string, label: string, route: string, is_custom: bool, is_active: int, step_order: int}>
     */
    public function getOrderedSteps(bool $onlyActive = true): array
    {
        $definitions = $this->getDefinitions();
        
        if ($onlyActive) {
            $this->workflowModel->where('is_active', 1);
        }
        
        $workflows = $this->workflowModel->orderBy('step_order', 'ASC')->findAll();
        
        $steps = [];
        foreach ($workflows as $w) {
            $key = $w['step_key'] ?? '';
            $def = $definitions[$key] ?? ['route' => '#', 'is_custom' => true];
            $wRoute = $w['route'] ?? null;
            
            $steps[] = [
                'id' => $w['id'] ?? null,
                'key' => $key,
                'label' => $w['step_name'] ?? '',
                'route' => $wRoute ?: $def['route'],
                'db_route' => $wRoute,
                'is_active' => (int) ($w['is_active'] ?? 0),
                'step_order' => (int) ($w['step_order'] ?? 0),
                'trigger_event' => $w['trigger_event'] ?? null,
                'is_custom' => (bool) ($def['is_custom'] ?? false),
            ];
        }

        return $steps;
    }

    /**
     * @return array<int, string>
     */
    public function getAllowedStepKeys(): array
    {
        return array_keys($this->getDefinitions());
    }

    /**
     * Step keys in the current sequence that use this route path (same order as sequence).
     *
     * @return list<string>
     */
    public function getStepKeysForRoute(string $route): array
    {
        $definitions = $this->getDefinitions();
        $keys = [];
        foreach ($this->getSequence() as $key) {
            if (! isset($definitions[$key])) {
                continue;
            }
            if ($definitions[$key]['route'] === $route) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    /**
     * Pick the active flow step when several keys share one URL (e.g. briefing vs video).
     */
    public function resolveFlowStepForRoute(string $route, ?string $fromRequest): string
    {
        $candidates = $this->getStepKeysForRoute($route);
        if ($fromRequest !== null && $fromRequest !== '' && in_array($fromRequest, $candidates, true)) {
            return $fromRequest;
        }
        if ($candidates !== []) {
            return $candidates[0];
        }

        return match ($route) {
            'security/briefing' => 'security_briefing',
            'security/facial-verification' => 'facial_verification',
            'security/completed' => 'completion',
            'security/checkin' => 'approval',
            default => '',
        };
    }

    public function getNextStepUrl(string $currentStep, ?string $token = null): ?string
    {
        $sequence = $this->getSequence();
        $index = array_search($currentStep, $sequence, true);
        if ($index === false) {
            return null;
        }

        $nextKey = $sequence[$index + 1] ?? null;
        if ($nextKey === null) {
            return null;
        }

        return $this->buildStepUrl($nextKey, $token);
    }

    public function getFirstStepAfterRegistrationUrl(?string $token = null): ?string
    {
        return $this->getNextStepUrl('registration', $token);
    }

    public function buildStepUrl(string $stepKey, ?string $token = null): ?string
    {
        $definitions = $this->getDefinitions();
        if (! isset($definitions[$stepKey])) {
            return null;
        }

        $def = $definitions[$stepKey];
        $query = array_merge([], $def['query'] ?? []);
        if ($token !== null && $token !== '') {
            $query['token'] = $token;
        }

        $route = $def['route'];
        if (str_starts_with($route, 'security/')) {
            $query['flow_step'] = $stepKey;
        }

        $url = base_url($route);
        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    /**
     * @param array<int, mixed> $sequence
     * @return array<int, string>
     */
    private function normalizeSequence(array $sequence): array
    {
        $allowed = $this->getAllowedStepKeys();
        $deduped = [];
        foreach ($sequence as $stepKey) {
            $step = (string) $stepKey;
            if ($step !== '' && in_array($step, $allowed, true) && ! in_array($step, $deduped, true)) {
                $deduped[] = $step;
            }
        }

        // No saved/custom sequence yet: use all defined steps as default order.
        if ($deduped === []) {
            return $allowed;
        }

        return $deduped;
    }

    /**
     * @return array<string, array{label:string,route:string,query?:array<string,string>,is_custom?:bool}>
     */
    private function getDefinitions(): array
    {
        $definitions = self::STEP_DEFINITIONS;
        foreach ($this->getCustomSteps() as $step) {
            $definitions[$step['key']] = [
                'label' => $step['label'],
                'route' => $step['route'],
                'is_custom' => true,
            ];
        }

        return $definitions;
    }

    /**
     * @return array<int, array{key:string,label:string,route:string}>
     */
    private function getCustomSteps(): array
    {
        $raw = $this->settingModel->getSetting(self::CUSTOM_STEPS_SETTING_KEY);
        $decoded = $raw ? json_decode((string) $raw, true) : null;
        if (! is_array($decoded)) {
            return [];
        }

        $rows = [];
        foreach ($decoded as $item) {
            if (! is_array($item)) {
                continue;
            }
            $key = trim((string) ($item['key'] ?? ''));
            $label = trim((string) ($item['label'] ?? ''));
            $route = trim((string) ($item['route'] ?? ''));
            if ($key === '' || $label === '' || $route === '') {
                continue;
            }
            if (isset(self::STEP_DEFINITIONS[$key])) {
                continue;
            }
            $rows[] = [
                'key' => $key,
                'label' => $label,
                'route' => $route,
            ];
        }

        return $rows;
    }
}
