<?php

namespace App\Filters;

use App\Services\SyncTrigger;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * After mutating HTTP requests, queue a debounced background sync to cloud.
 */
class SyncAutoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (! SyncTrigger::isEnabled()) {
            return;
        }

        if (! in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
            return;
        }

        if (self::isSyncControlRequest($request)) {
            return;
        }

        if ($response->getStatusCode() >= 400) {
            return;
        }

        SyncTrigger::markDirty();
    }

    private static function isSyncControlRequest(RequestInterface $request): bool
    {
        $path = trim($request->getUri()->getPath(), '/');

        foreach (['config/runDataSync', 'api/sync/trigger'] as $ignoredPath) {
            if ($path === $ignoredPath || str_ends_with($path, '/' . $ignoredPath)) {
                return true;
            }
        }

        return false;
    }
}
