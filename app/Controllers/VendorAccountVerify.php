<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Config > User: the admin's "Verify" button for a vendor company that has
 * self-registered. Flow: admin creates the company (Config > Company) ->
 * vendor registers -> account waits here, inactive -> admin verifies ->
 * vendor can log in.
 */
class VendorAccountVerify extends BaseController
{
    public function verify($id = 0)
    {
        helper(['role', 'feature']);

        $users = new UserModel();
        $user  = $users->find((int) $id);

        if (! $user || ! can_manage_target_user($user)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'You are not allowed to verify this account.']);
        }
        if (normalize_role_slug((string) $user['role']) !== normalize_role_slug('vendor_admin')) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Only vendor company accounts are verified here.']);
        }
        if ((int) $user['is_active'] === 1) {
            return $this->response->setJSON(['success' => true, 'message' => 'This account is already active.']);
        }

        // The company must still be an active record in Company Management.
        $company = (new \App\Models\CompanyModel())->where('id', (int) ($user['company_id'] ?? 0))->first();
        if (! $company || strtolower((string) ($company['status'] ?? '')) !== 'active') {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'The company for this account is missing or not Active in Company Management.']);
        }

        $users->skipValidation(true)->update((int) $user['id'], [
            'is_active'   => 1,
            'verified_at' => date('Y-m-d H:i:s'),
            'verified_by' => (int) session()->get('user_id'),
        ]);

        $this->sendVerifiedEmail((string) $user['email'], (string) $user['full_name'], (string) $company['name'], (string) $user['username']);

        return $this->response->setJSON(['success' => true, 'message' => 'Account verified. The vendor can now log in.']);
    }

    private function sendVerifiedEmail(string $to, string $fullName, string $companyName, string $username): void
    {
        try {
            $cfg   = config('Email');
            $email = \Config\Services::email();
            $email->setMailType('html');
            $email->setFrom($cfg->fromEmail, $cfg->fromName);
            $email->setTo($to);
            $email->setSubject('Your SafeG Vendor Account Has Been Verified');
            $email->setMessage(
                '<p>Hello ' . esc($fullName) . ',</p>'
                . '<p>The account for <strong>' . esc($companyName) . '</strong> has been verified. You can now log in at '
                . '<a href="' . base_url('login') . '">' . base_url('login') . '</a> using your company SSM No (<strong>' . esc($username) . '</strong>) as the username and the password you chose when registering.</p>'
            );
            $email->send();
        } catch (\Throwable $e) {
            log_message('error', 'Verified-account email failed: ' . $e->getMessage());
        }
    }
}
