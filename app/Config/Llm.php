<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Llm extends BaseConfig
{
    /**
     * Master switch so callers can keep LLM features wired in while disabled.
     */
    public bool $enabled;

    /**
     * OpenAI-compatible chat completions endpoint base URL.
     * Examples:
     * - https://api.openai.com/v1
     * - http://localhost:11434/v1
     */
    public string $baseUrl;

    public string $apiKey;
    public string $model;
    public float $temperature;
    public int $maxTokens;
    public int $timeout;

    /**
     * Default application-level guardrail for VMS use cases.
     */
    public string $systemPrompt;

    public function __construct()
    {
        parent::__construct();

        $this->enabled       = filter_var(env('LLM_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
        $this->baseUrl       = rtrim((string) env('LLM_BASE_URL', 'https://api.openai.com/v1'), '/');
        $this->apiKey        = (string) env('LLM_API_KEY', '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');
        $this->model         = (string) env('LLM_MODEL', 'gpt-4o-mini');
        $this->temperature   = (float) env('LLM_TEMPERATURE', '0.2');
        $this->maxTokens     = (int) env('LLM_MAX_TOKENS', '800');
        $this->timeout       = (int) env('LLM_TIMEOUT', '30');
        $this->systemPrompt  = (string) env(
            'LLM_SYSTEM_PROMPT',
            'You are an assistant inside a Visitor Management System. Be concise, factual, and avoid exposing sensitive personal data unless it is necessary for the requested operational task.'
        );
    }
}
