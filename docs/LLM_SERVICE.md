# VMS LLM Service

The VMS LLM service is a small wrapper around an OpenAI-compatible chat completions API. It can point to OpenAI, a local model gateway, or any compatible provider.

## Environment

Add these values to `.env` on the server:

```dotenv
LLM_ENABLED=true
LLM_BASE_URL=https://api.openai.com/v1
LLM_API_KEY=your-api-key
LLM_MODEL=gpt-4o-mini
LLM_TEMPERATURE=0.2
LLM_MAX_TOKENS=800
LLM_TIMEOUT=30
```

For a local OpenAI-compatible service, change `LLM_BASE_URL`, for example:

```dotenv
LLM_BASE_URL=http://localhost:11434/v1
LLM_API_KEY=local
LLM_MODEL=llama3.1
```

## Usage In Code

```php
$llm = \Config\Services::llm();

$result = $llm->generateText('Write a short visitor safety notice.');

if ($result['success']) {
    echo $result['text'];
} else {
    log_message('error', $result['error']);
}
```

For multi-turn chat:

```php
$result = \Config\Services::llm()->chat([
    ['role' => 'user', 'content' => 'Summarize these security alerts...'],
]);
```

## CLI Test

```bash
php spark llm:ask "Write a short visitor safety notice"
```
