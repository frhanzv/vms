<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login to access this page.');
        }

        if ($arguments) {
            helper('role');
            $userRole = normalize_role_slug(session()->get('role'));
            $allowed  = array_map('normalize_role_slug', $arguments);

            if (!in_array($userRole, $allowed, true)) {
                return redirect()->to('/dashboard')->with('error', 'You do not have permission to access that page.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
