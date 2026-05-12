<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

/**
 * VMS — Receive WorkWise Worker Count
 *
 * PLACE THIS FILE AT:
 *   vms-main/app/Controllers/Api/WorkerCountReceiver.php
 *
 * ROUTE — add in vms-main/app/Config/Routes.php OUTSIDE any auth group:
 *   $routes->post('api/receive-worker-count', 'Api\WorkerCountReceiver::receive');
 *
 * HOW IT WORKS:
 *   WorkWise POSTs JSON → this controller saves it into the api_keys table
 *   → visible in VMS API Management page under "WorkWise Worker Count"
 */
class WorkerCountReceiver extends BaseController
{
    use ResponseTrait;

    // Record identifier in api_keys table
    private string $recordName = 'WorkWise Worker Count';

    public function receive()
    {
        // ── STEP 1: Parse incoming JSON ───────────────────────────────────────
        $rawBody = $this->request->getBody();
        $payload = $this->request->getJSON(true);

        if (empty($payload)) {
            // Try manual decode as fallback
            $payload = json_decode($rawBody, true);
        }

        if (empty($payload)) {
            return $this->failValidationErrors([
                'error'    => 'Empty or invalid JSON payload received.',
                'raw_body' => $rawBody,
                'tip'      => 'Make sure WorkWise sends Content-Type: application/json',
            ]);
        }

        // ── STEP 2: Extract values safely ─────────────────────────────────────
        $totalWorkers  = (int) ($payload['total_workers']  ?? 0);
        $activeWorkers = (int) ($payload['active_workers'] ?? 0);
        $source        = $payload['source']  ?? 'workwise';
        $sentAt        = $payload['sent_at'] ?? date('Y-m-d H:i:s');
        $receivedAt    = date('Y-m-d H:i:s');

        // ── STEP 3: Build the JSON blob to store ──────────────────────────────
        $responseJson = json_encode([
            'total_workers'  => $totalWorkers,
            'active_workers' => $activeWorkers,
            'source'         => $source,
            'sent_at'        => $sentAt,
            'received_at'    => $receivedAt,
        ], JSON_PRETTY_PRINT);

        // ── STEP 4: Detect api_keys table columns and save ────────────────────
        try {
            $db     = \Config\Database::connect();
            $action = 'unknown';

            // Detect which JSON column exists in api_keys
            $jsonColumn = $this->detectJsonColumn($db);

            // Check if record already exists
            $existing = $db->table('api_keys')
                           ->where('name', $this->recordName)
                           ->get()
                           ->getRowArray();

            if ($existing) {
                // ── UPDATE existing record ────────────────────────────────────
                $updateData = [
                    'updated_at' => $receivedAt,
                ];

                // Set the correct JSON column
                $updateData[$jsonColumn] = $responseJson;

                // Set last_used_at only if column exists
                if ($this->columnExists($db, 'api_keys', 'last_used_at')) {
                    $updateData['last_used_at'] = $receivedAt;
                }

                $db->table('api_keys')
                   ->where('id', $existing['id'])
                   ->update($updateData);

                $action = 'updated';

            } else {
                // ── INSERT new record ─────────────────────────────────────────
                $insertData = [
                    'name'        => $this->recordName,
                    'created_at'  => $receivedAt,
                    'updated_at'  => $receivedAt,
                    $jsonColumn   => $responseJson,
                ];

                // Optional columns — add only if they exist in table
                if ($this->columnExists($db, 'api_keys', 'service')) {
                    $insertData['service'] = 'GET';
                }
                if ($this->columnExists($db, 'api_keys', 'api_key')) {
                    $insertData['api_key'] = 'http://workwise.test/push/vms-worker-count';
                }
                if ($this->columnExists($db, 'api_keys', 'description')) {
                    $insertData['description'] = 'Total worker count pushed from WorkWise system';
                }
                if ($this->columnExists($db, 'api_keys', 'status')) {
                    $insertData['status'] = 'active';
                }
                if ($this->columnExists($db, 'api_keys', 'last_used_at')) {
                    $insertData['last_used_at'] = $receivedAt;
                }

                $db->table('api_keys')->insert($insertData);
                $action = 'created';
            }

            // ── STEP 5: Return success response ───────────────────────────────
            return $this->respond([
                'success'        => true,
                'message'        => 'Worker count saved into VMS api_keys table!',
                'action'         => $action,
                'json_column'    => $jsonColumn,
                'total_workers'  => $totalWorkers,
                'active_workers' => $activeWorkers,
                'received_at'    => $receivedAt,
            ]);

        } catch (\Exception $e) {
            return $this->failServerError(json_encode([
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'tip'     => 'Check your api_keys table schema with: DESCRIBE api_keys;',
                'payload_received' => $payload,
            ]));
        }
    }

    // ── HELPERS ───────────────────────────────────────────────────────────────

    /**
     * Detect which JSON storage column exists in api_keys.
     * Tries common names in priority order.
     */
    private function detectJsonColumn(\CodeIgniter\Database\BaseConnection $db): string
    {
        $candidates = [
            'last_response_json',   // original code
            'json',                 // short alias
            'response_json',
            'response',
            'data',
            'meta',
            'payload',
        ];

        foreach ($candidates as $col) {
            if ($this->columnExists($db, 'api_keys', $col)) {
                return $col;
            }
        }

        // Fallback: use the first TEXT/JSON column found in the table
        $fields = $db->getFieldData('api_keys');
        foreach ($fields as $field) {
            if (in_array(strtolower($field->type), ['text', 'json', 'longtext', 'mediumtext'])) {
                return $field->name;
            }
        }

        throw new \RuntimeException(
            'Could not find a suitable JSON/TEXT column in api_keys table. ' .
            'Run "DESCRIBE api_keys;" and add the column name to $candidates in WorkerCountReceiver.php'
        );
    }

    /**
     * Check if a column exists in a table.
     */
    private function columnExists(\CodeIgniter\Database\BaseConnection $db, string $table, string $column): bool
    {
        try {
            return in_array($column, $db->getFieldNames($table));
        } catch (\Exception $e) {
            return false;
        }
    }
}