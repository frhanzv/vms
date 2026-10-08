<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * The ONLY pages a vendor company account (role vendor_admin, created
     * through the public Register page) may open. Everything else is turned
     * away here, before any controller runs — deny by default, so a page
     * added later (dashboard, config, reports…) can never leak to a
     * self-registered account just because nobody remembered to restrict it.
     */
    private const VENDOR_ADMIN_ALLOWED = [
        '#^vendors$#',
        '#^vendors/export$#',
        '#^vendors/import$#',
        '#^vendors/vendorpassrequest(/store)?$#',
        '#^vendorpassrequest/(view|edit|update)/[^/]+$#',
        '#^uploads/(vendor_photos|government_ids|other_docs|mysejahtera|facial_photos)/[^/]+$#', // their own request attachments
        '#^auth/logout$#',
        '#^files/#',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            // Redirect to login page
            return redirect()->to('/login')->with('error', 'Please login to access this page.');
        }

        helper('role');
        if (normalize_role_slug((string) session()->get('role')) === 'vendor_admin') {
            $path = trim((string) (method_exists($request, 'getPath') ? $request->getPath() : $request->getUri()->getPath()), '/');

            foreach (self::VENDOR_ADMIN_ALLOWED as $pattern) {
                if (preg_match($pattern, $path)) {
                    return;
                }
            }

            return redirect()->to('/vendors')->with('error', 'You do not have access to that page.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
