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
        if (session()->get('role') === 'superadmin') {
            return;
        }

        $featureKey = $arguments[0] ?? null;
        if (!$featureKey) {
            return;
        }

        $companyId = (int) session()->get('company_id');

        if (!(new ClientFeatureModel())->isEnabled($companyId, $featureKey)) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Your company does not have access to this feature.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
