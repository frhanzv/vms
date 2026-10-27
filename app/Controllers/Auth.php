<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SettingModel;
use App\Models\ClientModel;
use App\Models\CompanyModel;

class Auth extends BaseController
{
    private function getDefaultLoginPageSettings(): array
    {
        return [
            'template' => 'default',
            'page_title' => 'SafeG - Visitor Management System Login',
            'brand_name' => 'SafeG',
            'heading' => 'Welcome back',
            'subheading' => 'Please enter your details to sign in.',
            'username_label' => 'Username or Email',
            'username_placeholder' => 'Enter your username or email',
            'password_label' => 'Password',
            'password_placeholder' => 'Enter your password',
            'remember_text' => 'Remember me',
            'forgot_password_text' => 'Forgot Password?',
            'login_button_text' => 'Login',
            'demo_title' => 'Demo Credentials:',
            'demo_admin_text' => 'Super admin: admin / admin123',
            'demo_host_text' => 'Host: host / host123',
            'demo_officer_text' => 'Officer: officer / officer123',
            'demo_approver_text' => 'Site admin (requests): approver / approver123',
            'demo_gxo_text' => 'GXO Clientsuperadmin: gxoadmin / gxo123',
            'contact_prompt' => "Don't have an account?",
            'contact_link_text' => 'Contact Administrator',
            'footer_text' => 'SafeG Visitor Management System.',
            'hero_title' => 'Secure, Seamless Visitor Management.',
            'hero_subtitle' => "Safety Without the Hassle with SafeG's Intelligent Visitor Management System",
            'hero_badge_text' => 'A Malaysian Product',
            'background_image' => 'assets/images/safeg/login_bg.png', // KPK login background
        ];
    }

    private function resolveLoginBackgroundImageUrl(?string $rawPath): string
    {
        $default = $this->getDefaultLoginPageSettings()['background_image'];
        $path = trim((string) $rawPath);

        if ($path === '') {
            $path = $default;
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        return base_url(ltrim($path, '/'));
    }

    public function login()
    {
        // If user is already logged in, redirect to dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        $settingModel = new SettingModel();
        $defaults = $this->getDefaultLoginPageSettings();
        $settings = [];

        foreach ($defaults as $key => $defaultValue) {
            $storedValue = $settingModel->getSetting('login_' . $key);
            $settings[$key] = ($storedValue !== null && trim((string) $storedValue) !== '')
                ? (string) $storedValue
                : $defaultValue;
        }

        $settings['background_image_url'] = $this->resolveLoginBackgroundImageUrl($settings['background_image'] ?? null);

        $template = strtolower((string) ($settings['template'] ?? 'default'));
        $view = $template === 'gxo' ? 'auth/login_gxo' : 'auth/login';

        return view($view, ['loginPageSettings' => $settings]);
    }

    public function attemptLogin()
    {
        $usernameOrEmail = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        // Validate input
        if (empty($usernameOrEmail) || empty($password)) {
            return redirect()->back()->with('error', 'Username/Email and password are required')->withInput();
        }

        // Check credentials against database
        $userModel = new UserModel();
        $user = $userModel->verifyPassword($usernameOrEmail, $password);

        if ($user) {
            helper('role');
            $role = normalize_role_slug($user['role']);

            // Set session data
            $sessionData = [
                'user_id'    => $user['id'],
                'client_id'    => $user['client_id'],
                'company_id'   => $user['company_id'],
                'staff_id'   => $user['staff_id'],
                'username'   => $user['username'],
                'email'      => $user['email'],
                'contact_no' => $user['contact_no'],
                'full_name'  => $user['full_name'],
                'role'       => $role,
                'isLoggedIn' => true,
                'loginTime'  => time()
            ];
            session()->set($sessionData);

            // Handle remember me
            if ($remember) {
                // Set cookie for 30 days
                setcookie('remember_user', $user['username'], time() + (30 * 24 * 60 * 60), '/');
            }

            $redirectMap = [
                'superadmin'       => '/dashboard',
                'clientsuperadmin' => '/dashboard',
                'admin'            => '/visitors',
                'officer'          => '/workflow',
                'host'             => '/invitations',
                // Self-registered vendor company accounts have no real use
                // for the staff dashboard — their whole reach is their own
                // Online Vendor List, so send them straight there.
                'vendoradmin'      => '/dashboard/vendor', // normalize_role_slug() strips underscores
            ];
            $destination = $redirectMap[$role] ?? '/dashboard';
            if ($role === 'vendoradmin') {
                // Land on the dashboard only if the role is allowed it.
                helper('dashboard_nav');
                $destination = dashboard_tab_allowed('vendor') ? '/dashboard/vendor' : '/vendors';
            }
            return redirect()->to($destination)->with('success', 'Login successful!');
        } else {
            // Tell a freshly registered vendor why they can't log in yet.
            $pending = (new UserModel())
                ->groupStart()->where('username', $usernameOrEmail)->orWhere('email', $usernameOrEmail)->groupEnd()
                ->where('role', 'vendor_admin')->where('is_active', 0)->where('verified_at IS NULL', null, false)->first();
            if ($pending && password_verify((string) $password, (string) $pending['password'])) {
                return redirect()->back()->with('error', 'Your registration is still waiting to be verified by the administrator. You will be able to log in once it is verified.')->withInput();
            }

            return redirect()->back()->with('error', 'Invalid username or password')->withInput();
        }
    }

    public function logout()
    {
        // Destroy session
        session()->destroy();

        // Remove remember me cookie
        setcookie('remember_user', '', time() - 3600, '/');

        return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
    }

    // =========================================================================
    // Vendor company self-registration.
    //
    // A vendor is a COMPANY (Config > Company Management, `companies` table)
    // that deals with a CLIENT (the tenant, `clients` table — e.g. GXO). The
    // company must already be registered by an administrator; this flow
    // creates that company's first LOGIN ACCOUNT (role: vendor_admin), linked
    // to the company (users.company_id) and to the client it deals with
    // (users.client_id). That account can then add its own employees' vendor
    // pass requests — only ever for its own company.
    // =========================================================================

    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register', [
            'pageTitle'  => 'Register Your Company - SafeG',
            'prefillSsm' => trim((string) $this->request->getGet('ssm')),
        ]);
    }

    /**
     * The client a vendor company belongs to: the owner of the Location Access it was
     * created under. A company made before that existed falls back to the only active
     * client, if there is exactly one.
     */
    private function clientForCompany(array $company): ?array
    {
        $clients = new ClientModel();
        if (! empty($company['client_id'])) {
            return $clients->where('id', (int) $company['client_id'])->where('status', 'active')->first() ?: null;
        }
        $all = $clients->where('status', 'active')->findAll();

        return count($all) === 1 ? $all[0] : null;
    }

    /** True when this company already has a vendor account (by SSM username or by company link). */
    private function companyHasAccount(array $company): bool
    {
        $userModel = new UserModel();

        if ($userModel->where('username', (string) $company['registration_no'])->first()) {
            return true;
        }

        return (bool) $userModel
            ->where('company_id', (int) $company['id'])
            ->where('role', 'vendor_admin')
            ->first();
    }

    /**
     * AJAX: look up a company by SSM No so the form can auto-fill its name
     * (and offer the clients to register with) before the applicant commits.
     */
    public function searchCompany()
    {
        $ssmNo = trim((string) $this->request->getPost('ssm_no'));
        if ($ssmNo === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Please enter your company SSM No.']);
        }

        $company = (new CompanyModel())->findByRegistrationNo($ssmNo);
        if (! $company) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'We could not find a company with that SSM No. Please contact the administrator to have your company registered first.',
            ]);
        }

        if ($this->companyHasAccount($company)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An account already exists for this company. Please log in, or use "Forgot Password" if you cannot remember the password.',
            ]);
        }

        return $this->response->setJSON([
            'success'   => true,
            'name'      => $company['name'],
            'pass_name' => ($company['pass_name'] ?? '') !== '' ? $company['pass_name'] : $company['name'],
            'email'     => (string) ($company['email'] ?? ''),
            'contact_no' => (string) ($company['contact_no'] ?? ''),
        ]);
    }

    public function doRegister()
    {
        helper('client_link');
        $ssmNo        = trim((string) $this->request->getPost('ssm_no'));
        $password     = (string) $this->request->getPost('password');
        $email        = trim((string) $this->request->getPost('email'));
        $fullName     = trim((string) $this->request->getPost('full_name'));
        $icNumber     = trim((string) $this->request->getPost('ic_number'));
        $contactNo    = trim((string) $this->request->getPost('contact_no'));
        $agreedTerms  = (bool) $this->request->getPost('agree_terms');

        $errors = [];
        if ($ssmNo === '') { $errors[] = 'Company SSM No is required.'; }
        if ($password === '' || strlen($password) < 6) { $errors[] = 'Password must be at least 6 characters.'; }
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'A valid email is required.'; }
        if ($fullName === '') { $errors[] = "Administrator's full name is required."; }
        if ($icNumber === '') { $errors[] = 'IC Number is required.'; }
        if ($contactNo === '') { $errors[] = 'Contact Number is required.'; }
        if (! $agreedTerms) { $errors[] = 'You must accept the Terms & Conditions to register.'; }

        $company = $ssmNo !== '' ? (new CompanyModel())->findByRegistrationNo($ssmNo) : null;
        if (! $company) {
            $errors[] = 'We could not find a company with that SSM No. Please contact the administrator to have your company registered first.';
        }

        // No client choice: the vendor is under the client that owns its company's Location Access.
        $client = $company ? $this->clientForCompany($company) : null;
        if ($company && ! $client) {
            $errors[] = 'Your company has no Location Access assigned yet. Please contact the administrator.';
        }
        // Vendor Workflow: the client has switched Vendor Registration off.
        if ($client && ! \App\Libraries\VendorWorkflow::enabled(\Config\Database::connect(), (int) $client['id'], 'vendor_registration')) {
            $errors[] = 'Vendor registration is not open. Please contact the administrator.';
        }

        $userModel = new UserModel();
        if ($company && $this->companyHasAccount($company)) {
            $errors[] = 'An account already exists for this company.';
        }
        if ($email !== '' && $userModel->where('email', $email)->first()) {
            $errors[] = 'This email is already registered.';
        }

        if (! empty($errors)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $errors));
        }

        // The account is created INACTIVE and waits in Config > User until an
        // administrator verifies it (no email link — the admin is the gate).
        $ok = $userModel->insert([
            'client_id'                    => (int) $client['id'],
            'company_id'                   => (int) $company['id'],
            'username'                     => (string) $company['registration_no'],
            'email'                        => $email,
            'password'                     => $password,
            'full_name'                    => $fullName,
            'ic_number'                    => $icNumber,
            'contact_no'                   => $contactNo,
            'role'                         => 'vendor_admin',
            'is_active'                    => 0,
        ]);

        if (! $ok) {
            return redirect()->back()->withInput()->with('error', 'Could not complete registration: ' . implode(' ', $userModel->errors() ?: ['Please check your details and try again.']));
        }

        // Vendor Workflow: when the client has switched "Account Verification" off, the account is
        // active straight away - no staff verification and no reminder email.
        if (! \App\Libraries\VendorWorkflow::enabled(\Config\Database::connect(), (int) $client['id'], 'account_verification')) {
            $userModel->skipValidation(true)->update((int) $ok, ['is_active' => 1, 'verified_at' => date('Y-m-d H:i:s')]);

            return redirect()->to($linkClient ? client_link_url($linkClient, 'login') : base_url('login'))->with('success', 'Registration complete. You can now log in with your company SSM No as the username.');
        }

        // Remind the client that this vendor is waiting for approval.
        try {
            \App\Libraries\VendorInviteMailer::registered($company, $client, ['full_name' => $fullName, 'email' => $email]);
        } catch (\Throwable $e) {
            log_message('error', 'Registration reminder failed: ' . $e->getMessage());
        }

        return redirect()->to(base_url('login'))->with('success', 'Registration received. Your account now needs to be verified by the administrator. Once it is verified you can log in with your company SSM No as the username.');
    }

    public function forgotPassword()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/forgot_password', [
            'pageTitle' => 'Forgot Password - SafeG',
        ]);
    }

    public function doForgotPassword()
    {
        $identifier = trim((string) $this->request->getPost('identifier'));
        $generic    = 'If that account exists, a new password has been sent to its registered email.';

        if ($identifier === '') {
            return redirect()->back()->withInput()->with('error', 'Please enter your Staff ID, IC Number or Passport Number.');
        }

        $userModel = new UserModel();
        $user      = $userModel->findForPasswordRecovery($identifier);

        if (! $user || empty($user['email'])) {
            // Same message whether or not the account exists, so this page
            // can't be used to probe which staff IDs / IC numbers are valid.
            return redirect()->to(base_url('login'))->with('success', $generic);
        }

        $newPassword = substr(bin2hex(random_bytes(6)), 0, 10);
        $userModel->update($user['id'], ['password' => $newPassword]);

        $emailConfig = config('Email');
        $message = '
            <p>Hello ' . esc($user['full_name'] ?? $user['username']) . ',</p>
            <p>Your password has been reset. Your new password is:</p>
            <p style="font-size:16px;font-weight:bold;">' . esc($newPassword) . '</p>
            <p>Please log in and change it as soon as possible.</p>
        ';

        $email = \Config\Services::email();
        $email->setMailType('html');
        $email->setFrom($emailConfig->fromEmail, $emailConfig->fromName);
        $email->setTo($user['email']);
        $email->setSubject('Your SafeG Password Has Been Reset');
        $email->setMessage($message);
        $email->send();

        return redirect()->to(base_url('login'))->with('success', $generic);
    }
}
