<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

class LlmAsk extends BaseCommand
{
    protected $group       = 'llm';
    protected $name        = 'llm:ask';
    protected $description = 'Test the configured VMS LLM service with a prompt.';
    protected $usage       = 'llm:ask "Summarize today\'s visitor activity"';
    protected $arguments   = [
        'prompt' => 'Prompt to send to the configured LLM provider.',
    ];
    protected $options = [
        '--model'       => 'Override configured LLM model for this request.',
        '--temperature' => 'Override configured temperature for this request.',
        '--max-tokens'  => 'Override configured max output tokens for this request.',
    ];

    public function run(array $params): void
    {
        $prompt = trim(implode(' ', $params));

        if ($prompt === '') {
            CLI::error('Please provide a prompt.');
            CLI::write('Example: php spark llm:ask "Write a short visitor safety notice"', 'yellow');
            return;
        }

        $options = array_filter([
            'model'       => CLI::getOption('model') ?: null,
            'temperature' => CLI::getOption('temperature') !== null ? (float) CLI::getOption('temperature') : null,
            'max_tokens'  => CLI::getOption('max-tokens') !== null ? (int) CLI::getOption('max-tokens') : null,
        ], static fn ($value) => $value !== null);

        $result = Services::llm()->generateText($prompt, $options);

        if (! $result['success']) {
            CLI::error($result['error'] ?? 'LLM request failed.');
            return;
        }

        CLI::write($result['text'], 'green');
    }
}
