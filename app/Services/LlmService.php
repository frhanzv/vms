<?php

namespace App\Services;

use CodeIgniter\HTTP\CURLRequest;
use Config\Llm;
use Config\Services;
use Throwable;

class LlmService
{
    protected Llm $config;
    protected CURLRequest $client;

    public function __construct(?Llm $config = null, ?CURLRequest $client = null)
    {
        $this->config = $config ?? config('Llm');
        $this->client = $client ?? Services::curlrequest([
            'timeout'     => $this->config->timeout,
            'http_errors' => false,
        ]);
    }

    /**
     * Generate a text response from a simple prompt.
     *
     * @param array<string, mixed> $options
     *
     * @return array{success: bool, text: string, error: string|null, model?: string, usage?: array, raw?: array}
     */
    public function generateText(string $prompt, array $options = []): array
    {
        return $this->chat([
            ['role' => 'user', 'content' => $prompt],
        ], $options);
    }

    /**
     * Send a chat-style request to an OpenAI-compatible chat completions API.
     *
     * @param list<array{role: string, content: string}> $messages
     * @param array<string, mixed> $options
     *
     * @return array{success: bool, text: string, error: string|null, model?: string, usage?: array, raw?: array}
     */
    public function chat(array $messages, array $options = []): array
    {
        if (! $this->config->enabled) {
            return $this->fail('LLM service is disabled.');
        }

        if ($this->config->apiKey === '') {
            return $this->fail('LLM API key is not configured.');
        }

        $messages = $this->normalizeMessages($messages, (string) ($options['system_prompt'] ?? $this->config->systemPrompt));

        if ($messages === []) {
            return $this->fail('No LLM messages were provided.');
        }

        $payload = [
            'model'       => (string) ($options['model'] ?? $this->config->model),
            'messages'    => $messages,
            'temperature' => (float) ($options['temperature'] ?? $this->config->temperature),
            'max_tokens'  => (int) ($options['max_tokens'] ?? $this->config->maxTokens),
        ];

        try {
            $response = $this->client->post($this->config->baseUrl . '/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config->apiKey,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'json' => $payload,
            ]);

            $status = $response->getStatusCode();
            $rawBody = (string) $response->getBody();
            $data = json_decode($rawBody, true);

            if ($status < 200 || $status >= 300) {
                $message = is_array($data)
                    ? (string) ($data['error']['message'] ?? $data['message'] ?? 'LLM request failed.')
                    : 'LLM request failed.';

                log_message('error', "LlmService: HTTP {$status} - {$message}");
                return $this->fail($message);
            }

            if (! is_array($data)) {
                return $this->fail('LLM response was not valid JSON.');
            }

            $text = trim((string) ($data['choices'][0]['message']['content'] ?? ''));

            return [
                'success' => true,
                'text'    => $text,
                'error'   => null,
                'model'   => (string) ($data['model'] ?? $payload['model']),
                'usage'   => is_array($data['usage'] ?? null) ? $data['usage'] : [],
                'raw'     => $data,
            ];
        } catch (Throwable $e) {
            log_message('error', 'LlmService: ' . $e->getMessage());
            return $this->fail('LLM request could not be completed.');
        }
    }

    /**
     * @param list<array{role: string, content: string}> $messages
     *
     * @return list<array{role: string, content: string}>
     */
    protected function normalizeMessages(array $messages, string $systemPrompt): array
    {
        $normalized = [];

        if ($systemPrompt !== '') {
            $normalized[] = ['role' => 'system', 'content' => $systemPrompt];
        }

        foreach ($messages as $message) {
            $role = (string) ($message['role'] ?? '');
            $content = trim((string) ($message['content'] ?? ''));

            if (! in_array($role, ['system', 'user', 'assistant'], true) || $content === '') {
                continue;
            }

            $normalized[] = ['role' => $role, 'content' => $content];
        }

        return $normalized;
    }

    /**
     * @return array{success: false, text: string, error: string}
     */
    protected function fail(string $message): array
    {
        return [
            'success' => false,
            'text'    => '',
            'error'   => $message,
        ];
    }
}
