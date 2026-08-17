<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login to access this page.');
        }

        $module = $arguments[0] ?? null;
        $action = $arguments[1] ?? 'view';

        if (! $module) {
            return redirect()->to('/dashboard')->with('error', 'Access permission is not configured for that page.');
        }

        helper('access');
        if (! has_access($module, $action)) {
            return redirect()->to('/dashboard')->with('error', 'You do not have permission to access that page.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing.
    }
}
