<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class InboundApi extends BaseController
{
    use ResponseTrait;

    private function ensureTokensTable(): void
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('inbound_api_tokens')) {
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
            'client_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
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
        $forge->createTable('inbound_api_tokens', true);
    }

    public function __construct()
    {
        $this->ensureTokensTable();
    }

    /**
     * Generic receiver for inbound webhooks
     * POST /api/v1/receive
     */
    public function receive()
    {
        $payload = $this->request->getJSON(true);
        
        if (!is_array($payload)) {
            $payload = $this->request->getPost();
        }

        if (empty($payload)) {
            return $this->failValidationErrors('Invalid or empty payload');
        }

        // Log the received payload to a log file
        log_message('info', 'Inbound API Received Payload: ' . json_encode($payload));

        // Note: For future development, implement payload processing here based on the data type

        return $this->respond([
            'success' => true,
            'message' => 'Data received successfully',
            'received_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Generate a new Inbound API Token (For testing / setup)
     * POST /api/v1/generate-token (Protected by Superadmin route group later, but accessible internally)
     */
    public function generateToken()
    {
        $payload = $this->request->getJSON(true);
        $clientName = $payload['client_name'] ?? 'Generic External System';

        $token = bin2hex(random_bytes(32)); // 64 char secure token

        $model = new \App\Models\InboundApiKeyModel();
        $model->insert([
            'client_name' => $clientName,
            'token'       => $token,
            'status'      => 'active'
        ]);

        return $this->respond([
            'success' => true,
            'message' => 'Token generated successfully',
            'client_name' => $clientName,
            'token' => $token
        ]);
    }
}
