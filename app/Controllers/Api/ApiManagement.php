<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ApiKeyModel;

class ApiManagement extends BaseController
{
    protected ApiKeyModel $apiKeyModel;

    public function __construct()
    {
        $this->apiKeyModel = new ApiKeyModel();
    }

    private function ensureApiKeysTable(): void
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('api_keys')) {
            return;
        }

        $forge = \Config\Database::forge();
        $forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'service' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'api_key' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
                'null'       => false,
            ],
            'last_used_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('api_keys', true);
    }

    public function getApiKeys()
    {
        $this->ensureApiKeysTable();
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        $search  = $this->request->getGet('search') ?? '';
        $offset  = ($page - 1) * $perPage;

        $builder = $this->apiKeyModel;
        if ($search !== '') {
            $builder = $builder->groupStart()
                ->like('name', $search)
                ->orLike('service', $search)
                ->groupEnd();
        }

        $total = $builder->countAllResults(false);
        $items = $builder->orderBy('created_at', 'DESC')->findAll($perPage, $offset);

        return $this->response->setJSON([
            'success' => true,
            'data' => $items,
            'pagination' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => (int) ceil($total / max($perPage, 1)),
                'from'         => $total > 0 ? $offset + 1 : 0,
                'to'           => min($offset + $perPage, $total),
            ],
        ]);
    }

    public function getApiKey($id)
    {
        $this->ensureApiKeysTable();
        $item = $this->apiKeyModel->find($id);
        if (!$item) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'API call not found',
            ]);
        }

        $this->apiKeyModel->update($id, ['last_used_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $item,
        ]);
    }

    public function createApiKey()
    {
        $this->ensureApiKeysTable();
        $input = $this->request->getJSON(true);
        if (!is_array($input)) {
            $input = $this->request->getPost();
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'    => 'required|max_length[100]',
            'service' => 'required|max_length[100]',
            'api_key' => 'required',
            'status'  => 'permit_empty|in_list[active,inactive]',
        ]);

        if (!$validation->run($input)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validation->getErrors(),
            ]);
        }

        $data = [
            'name'        => $input['name'],
            'service'     => strtoupper((string) $input['service']),
            'api_key'     => $input['api_key'],
            'description' => $input['description'] ?? null,
            'status'      => $input['status'] ?? 'active',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        try {
            if (!$this->apiKeyModel->insert($data)) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to create API call',
                    'errors'  => $this->apiKeyModel->errors(),
                ]);
            }
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to create API call',
                'errors'  => $e->getMessage(),
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'API call created successfully',
        ]);
    }

    public function updateApiKey($id)
    {
        $this->ensureApiKeysTable();
        $item = $this->apiKeyModel->find($id);
        if (!$item) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'API call not found',
            ]);
        }

        $input = $this->request->getJSON(true);
        if (!is_array($input)) {
            $input = $this->request->getPost();
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'    => 'required|max_length[100]',
            'service' => 'required|max_length[100]',
            'api_key' => 'required',
            'status'  => 'permit_empty|in_list[active,inactive]',
        ]);

        if (!$validation->run($input)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validation->getErrors(),
            ]);
        }

        $data = [
            'name'        => $input['name'],
            'service'     => strtoupper((string) $input['service']),
            'api_key'     => $input['api_key'],
            'description' => $input['description'] ?? null,
            'status'      => $input['status'] ?? 'active',
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        try {
            if (!$this->apiKeyModel->update($id, $data)) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to update API call',
                    'errors'  => $this->apiKeyModel->errors(),
                ]);
            }
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to update API call',
                'errors'  => $e->getMessage(),
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'API call updated successfully',
        ]);
    }

    public function deleteApiKey($id)
    {
        $this->ensureApiKeysTable();
        $item = $this->apiKeyModel->find($id);
        if (!$item) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'API call not found',
            ]);
        }

        if (!$this->apiKeyModel->delete($id)) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to delete API call',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'API call deleted successfully',
        ]);
    }

    // =========================================================================
    // NEW — Sync API endpoints from the Laravel backend /api/registry
    // =========================================================================
    /**
     * POST  config/syncApiKeys
     * Body  JSON: { "laravel_url": "http://your-laravel-host" }
     *
     * Calls GET {laravel_url}/api/registry on the Laravel side.
     * Inserts any endpoint whose `name` does not yet exist in api_keys.
     * Existing entries are never overwritten.
     *
     * Returns: { success, imported, skipped, errors[], message }
     */
    public function syncApiKeys()
    {
        $this->ensureApiKeysTable();

        $input      = $this->request->getJSON(true) ?? [];
        $laravelUrl = rtrim($input['laravel_url'] ?? '', '/');

        if (empty($laravelUrl)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'laravel_url is required.',
            ]);
        }

        // 1. Fetch registry from Laravel
        $client = \Config\Services::curlrequest();

        try {
            $resp = $client->get($laravelUrl . '/api/registry', [
                'timeout'     => 10,
                'http_errors' => false,
                'headers'     => ['Accept' => 'application/json'],
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(502)->setJSON([
                'success' => false,
                'message' => 'Could not reach Laravel backend: ' . $e->getMessage(),
            ]);
        }

        $body = json_decode($resp->getBody(), true);

        // Accept { success, data:[...] }  OR  a plain array
        $endpoints = [];
        if (isset($body['data']) && is_array($body['data'])) {
            $endpoints = $body['data'];
        } elseif (is_array($body)) {
            $endpoints = $body;
        }

        if (empty($endpoints)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Laravel registry returned no endpoints. Make sure GET /api/registry exists and returns data.',
            ]);
        }

        // 2. Load existing names for fast duplicate check
        $existing    = array_column($this->apiKeyModel->select('name')->findAll(), 'name');
        $existingSet = array_flip($existing);

        // 3. Insert new endpoints only
        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $now      = date('Y-m-d H:i:s');

        foreach ($endpoints as $ep) {
            $name   = trim($ep['name']        ?? '');
            $method = strtoupper(trim($ep['method'] ?? 'GET'));
            $uri    = trim($ep['uri']         ?? '');
            $desc   = trim($ep['description'] ?? '');

            if ($name === '' || $uri === '') {
                $errors[] = 'Skipped — missing name or uri: ' . json_encode($ep);
                continue;
            }

            if (isset($existingSet[$name])) {
                $skipped++;
                continue;
            }

            $row = [
                'name'        => $name,
                'service'     => $method,
                'api_key'     => $uri,
                'description' => $desc ?: null,
                'status'      => 'active',
                'created_at'  => $now,
                'updated_at'  => $now,
            ];

            try {
                if ($this->apiKeyModel->insert($row)) {
                    $imported++;
                    $existingSet[$name] = true; // prevent double-import in same run
                } else {
                    $errors[] = 'DB insert failed for: ' . $name;
                }
            } catch (\Throwable $e) {
                $errors[] = 'Exception for ' . $name . ': ' . $e->getMessage();
            }
        }

        return $this->response->setJSON([
            'success'  => true,
            'imported' => $imported,
            'skipped'  => $skipped,
            'errors'   => $errors,
            'message'  => "Sync complete — {$imported} imported, {$skipped} already existed.",
        ]);
    }

    // =========================================================================
    // NEW — Actually CALL an external API endpoint and return the data
    // =========================================================================
    /**
     * POST  config/callExternalApi
     * Body JSON: {
     *   "api_key_id": 5,           // id of the api_keys row
     *   "base_url": "http://...",  // optional override of the Laravel host
     *   "params": {...}            // optional query/body params to pass along
     * }
     *
     * This answers the mentor's question:
     *   "if we received data, where do we put it?"
     *
     * The response from the external API is returned to the frontend.
     * The caller (JS) decides what to do with the data — e.g. display it,
     * save it to VMS, pre-fill a form, etc.
     */
    public function callExternalApi()
    {
        $this->ensureApiKeysTable();

        $input   = $this->request->getJSON(true) ?? [];
        $id      = $input['api_key_id'] ?? null;
        $baseUrl = rtrim($input['base_url'] ?? '', '/');
        $params  = $input['params'] ?? [];

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'api_key_id is required.',
            ]);
        }

        $entry = $this->apiKeyModel->find($id);
        if (!$entry) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'API entry not found.',
            ]);
        }

        if ($entry['status'] !== 'active') {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'This API entry is inactive.',
            ]);
        }

        // Build the full URL
        $endpoint = $entry['api_key']; // e.g. /api/packageDetail
        if (empty($baseUrl)) {
            // Try to get the saved Laravel base URL from settings
            $db       = \Config\Database::connect();
            $setting  = $db->table('settings')->where('key', 'laravel_base_url')->get()->getRowArray();
            $baseUrl  = $setting ? rtrim($setting['value'], '/') : '';
        }

        if (empty($baseUrl) && !str_starts_with($endpoint, 'http')) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'No base URL configured. Please set it in the Sync modal or pass base_url in the request.',
            ]);
        }

        $fullUrl = str_starts_with($endpoint, 'http') ? $endpoint : ($baseUrl . $endpoint);
        $method  = strtoupper($entry['service'] ?? 'GET'); // GET, POST, etc.

        // Make the HTTP call
        $client = \Config\Services::curlrequest();
        $options = [
            'timeout'     => 15,
            'http_errors' => false,
            'headers'     => ['Accept' => 'application/json'],
        ];

        try {
            if ($method === 'GET') {
                if (!empty($params)) {
                    $fullUrl .= '?' . http_build_query($params);
                }
                $resp = $client->get($fullUrl, $options);
            } else {
                $options['json'] = $params;
                $resp = $client->request($method, $fullUrl, $options);
            }
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(502)->setJSON([
                'success' => false,
                'message' => 'Could not reach external API: ' . $e->getMessage(),
            ]);
        }

        // Update last_used_at
        $this->apiKeyModel->update($id, ['last_used_at' => date('Y-m-d H:i:s')]);

        $statusCode = $resp->getStatusCode();
        $body       = json_decode($resp->getBody(), true);

        return $this->response->setJSON([
            'success'     => $statusCode >= 200 && $statusCode < 300,
            'status_code' => $statusCode,
            'data'        => $body,
            'message'     => $statusCode >= 200 && $statusCode < 300
                ? 'Data retrieved successfully from ' . $entry['name']
                : 'External API returned HTTP ' . $statusCode,
        ]);
    }

    // =========================================================================
    // NEW — Save the Laravel base URL for reuse
    // =========================================================================
    /**
     * POST config/saveLaravelBaseUrl
     * Body JSON: { "base_url": "http://your-laravel-host" }
     */
    public function saveLaravelBaseUrl()
    {
        $input   = $this->request->getJSON(true) ?? [];
        $baseUrl = rtrim($input['base_url'] ?? '', '/');

        if (empty($baseUrl)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'base_url is required.',
            ]);
        }

        $db = \Config\Database::connect();

        // Check if 'settings' table has this key
        $existing = $db->table('settings')->where('key', 'laravel_base_url')->get()->getRowArray();

        if ($existing) {
            $db->table('settings')->where('key', 'laravel_base_url')->update(['value' => $baseUrl]);
        } else {
            // Try to insert — if settings table doesn't exist, just skip
            try {
                $db->table('settings')->insert([
                    'key'   => 'laravel_base_url',
                    'value' => $baseUrl,
                ]);
            } catch (\Throwable $e) {
                // Settings table may not exist — that's OK, base_url can be passed per-request
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Laravel base URL saved.',
        ]);
    }
}