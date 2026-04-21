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
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / max($perPage, 1)),
                'from' => $total > 0 ? $offset + 1 : 0,
                'to' => min($offset + $perPage, $total),
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
            'data' => $item,
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
            'name' => 'required|max_length[100]',
            'service' => 'required|max_length[100]',
            'api_key' => 'required',
            'status' => 'permit_empty|in_list[active,inactive]',
        ]);

        if (!$validation->run($input)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors(),
            ]);
        }

        $data = [
            'name' => $input['name'],
            'service' => strtoupper((string) $input['service']),
            'api_key' => $input['api_key'],
            'description' => $input['description'] ?? null,
            'status' => $input['status'] ?? 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            if (!$this->apiKeyModel->insert($data)) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to create API call',
                    'errors' => $this->apiKeyModel->errors(),
                ]);
            }
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to create API call',
                'errors' => $e->getMessage(),
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
            'name' => 'required|max_length[100]',
            'service' => 'required|max_length[100]',
            'api_key' => 'required',
            'status' => 'permit_empty|in_list[active,inactive]',
        ]);

        if (!$validation->run($input)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors(),
            ]);
        }

        $data = [
            'name' => $input['name'],
            'service' => strtoupper((string) $input['service']),
            'api_key' => $input['api_key'],
            'description' => $input['description'] ?? null,
            'status' => $input['status'] ?? 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            if (!$this->apiKeyModel->update($id, $data)) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to update API call',
                    'errors' => $this->apiKeyModel->errors(),
                ]);
            }
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to update API call',
                'errors' => $e->getMessage(),
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
}

