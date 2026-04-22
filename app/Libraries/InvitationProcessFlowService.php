<?php

namespace App\Libraries;

use App\Models\SettingModel;

class InvitationProcessFlowService
{
    public const SETTING_KEY = 'invitation_process_flow_sequence';

    /**
     * @var array<string, array{label: string, route: string}>
     */
    private const STEP_DEFINITIONS = [
        'registration' => [
            'label' => 'Registration Form',
            'route' => 'visitor-registration',
        ],
        'security_briefing' => [
            'label' => 'Security Briefing',
            'route' => 'security/briefing',
        ],
        'facial_verification' => [
            'label' => 'Facial Verification',
            'route' => 'security/facial-verification',
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

        return $this->normalizeSequence(is_array($decoded) ? $decoded : []);
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
     * @return array<int, array{key: string, label: string, route: string}>
     */
    public function getOrderedSteps(): array
    {
        $steps = [];
        foreach ($this->getSequence() as $key) {
            $definition = self::STEP_DEFINITIONS[$key];
            $steps[] = [
                'key' => $key,
                'label' => $definition['label'],
                'route' => $definition['route'],
            ];
        }

        return $steps;
    }

    /**
     * @return array<int, string>
     */
    public function getAllowedStepKeys(): array
    {
        return array_keys(self::STEP_DEFINITIONS);
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
        if (! isset(self::STEP_DEFINITIONS[$stepKey])) {
            return null;
        }

        $url = base_url(self::STEP_DEFINITIONS[$stepKey]['route']);
        if ($token !== null && $token !== '') {
            $url .= '?token=' . urlencode($token);
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

        foreach ($allowed as $defaultStep) {
            if (! in_array($defaultStep, $deduped, true)) {
                $deduped[] = $defaultStep;
            }
        }

        return $deduped;
    }
}
