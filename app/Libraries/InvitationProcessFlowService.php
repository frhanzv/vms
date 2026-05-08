<?php

namespace App\Libraries;

use App\Models\SettingModel;

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

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    /**
     * @return array<int, string>
     */
    public function getSequence(): array
    {
        $raw = $this->settingModel->getSetting(self::SETTING_KEY);
        $decoded = $raw ? json_decode((string) $raw, true) : null;
        $arr = is_array($decoded) ? $decoded : [];
        $hasStoredSequence = is_string($raw) && trim($raw) !== '';

        $normalized = $this->normalizeSequence($arr);

        // First-run bootstrap: persist the default 9-step workflow so all devices read
        // the same sequence from DB even before anyone clicks "Save Sequence".
        if (! $hasStoredSequence) {
            $this->settingModel->setSetting(self::SETTING_KEY, json_encode($normalized));
        }

        return $normalized;
    }

    /**
     * @param array<int, string> $sequence
     */
    public function saveSequence(array $sequence): bool
    {
        $normalized = $this->normalizeSequence($sequence);

        return (bool) $this->settingModel->setSetting(self::SETTING_KEY, json_encode($normalized));
    }

    /**
     * @param array<int, array{key:string,label:string,route:string}> $steps
     */
    public function saveCustomSteps(array $steps): bool
    {
        $sanitized = [];
        foreach ($steps as $row) {
            $key = trim((string) ($row['key'] ?? ''));
            $label = trim((string) ($row['label'] ?? ''));
            $route = trim((string) ($row['route'] ?? ''));
            if ($key === '' || $label === '' || $route === '') {
                continue;
            }

            $sanitized[] = [
                'key' => $key,
                'label' => $label,
                'route' => $route,
            ];
        }

        return (bool) $this->settingModel->setSetting(self::CUSTOM_STEPS_SETTING_KEY, json_encode($sanitized));
    }

    /**
     * @return array<int, array{key: string, label: string, route: string, is_custom: bool}>
     */
    public function getOrderedSteps(): array
    {
        $definitions = $this->getDefinitions();
        $steps = [];
        foreach ($this->getSequence() as $key) {
            if (! isset($definitions[$key])) {
                continue;
            }
            $definition = $definitions[$key];
            $steps[] = [
                'key' => $key,
                'label' => $definition['label'],
                'route' => $definition['route'],
                'is_custom' => (bool) ($definition['is_custom'] ?? false),
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
