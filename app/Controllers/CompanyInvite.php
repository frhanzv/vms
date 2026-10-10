<?php

namespace App\Controllers;

use App\Libraries\VendorInviteMailer;
use App\Models\CompanyModel;
use App\Models\UserModel;

/**
 * "Send invite" button in Config > Company: (re)sends the registration email
 * to the vendor, carrying the CURRENT client's link. The same email goes out
 * automatically when a company is created (CompanyModel afterInsert).
 */
class CompanyInvite extends BaseController
{
    public function send($id)
    {
        helper(['feature', 'role', 'client_link']);

        $company = (new CompanyModel())->find((int) $id);
        if (! $company) {
            return $this->response->setJSON(['success' => false, 'message' => 'Company not found.']);
        }
        if (trim((string) ($company['email'] ?? '')) === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Add an email address to this company first.']);
        }

        $reg = (new CompanyModel())->registrationState((int) $company['id']);
        if ($reg === 'registered' || $reg === 'pending') {
            return $this->response->setJSON(['success' => false, 'message' => 'This company has already registered.']);
        }

        $client = is_platform_superadmin() ? null : (new \App\Models\ClientModel())->find((int) current_company_id());
        [$ok, $msg] = VendorInviteMailer::invite($company, $client);

        return $this->response->setJSON(['success' => $ok, 'message' => $msg]);
    }
}
