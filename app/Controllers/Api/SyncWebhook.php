<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\SyncTrigger;

/**
 * Wake endpoint — cloud POSTs here so Jetson pulls immediately
 * when Jetson MySQL is not reachable from the internet.
 */
class SyncWebhook extends BaseController
{
    public function trigger()
    {
        $expected = env('SYNC.webhookToken');
        if (empty($expected)) {
            return $this->response->setStatusCode(503)->setJSON([
                'success' => false,
                'message' => 'SYNC.webhookToken is not configured on this server.',
            ]);
        }

        $token = $this->request->getHeaderLine('X-Sync-Token');
        if ($token === '' || ! hash_equals($expected, $token)) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Invalid sync token.',
            ]);
        }

        SyncTrigger::spawnBackgroundSync();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Sync queued.',
        ]);
    }
}
