<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\InboundApiKeyModel;
use Config\Services;

class InboundApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');
        
        if (empty($header) || !preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return Services::response()
                ->setJSON(['success' => false, 'message' => 'Unauthorized: Missing or invalid token format'])
                ->setStatusCode(401);
        }

        $token = $matches[1];
        
        // Ensure table exists first
        $db = \Config\Database::connect();
        if ($db->tableExists('inbound_api_tokens')) {
            $model = new InboundApiKeyModel();
            $client = $model->where('token', $token)->where('status', 'active')->first();
            
            if (!$client) {
                return Services::response()
                    ->setJSON(['success' => false, 'message' => 'Unauthorized: Invalid or inactive token'])
                    ->setStatusCode(401);
            }
            
            // Update last used at
            $model->update($client['id'], ['last_used_at' => date('Y-m-d H:i:s')]);
            
            // Allow request to proceed
        } else {
             return Services::response()
                ->setJSON(['success' => false, 'message' => 'Unauthorized: System not configured yet'])
                ->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
