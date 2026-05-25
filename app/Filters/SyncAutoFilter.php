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

        if ($response->getStatusCode() >= 400) {
            return;
        }

        SyncTrigger::markDirty();
    }
}
