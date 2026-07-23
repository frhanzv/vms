<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ClientFeatureModel;

class ClientFeatureFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper(['feature', 'role']);

        // Platform superadmin is never blocked by per-company feature flags.
        if (is_platform_superadmin()) {
            return;
        }

        $companyId = current_company_id();

        $featureKey = $arguments[0] ?? null;
        if (!$featureKey) {
            return;
        }

        if (!(new ClientFeatureModel())->isEnabled($companyId, $featureKey)) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Your company does not have access to this feature.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
