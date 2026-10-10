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
            'background_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCMW9bMBWzlF_H5fr9w7w3EWL0xxvt6SL3WOWhab785VAq-rPN7ObkIsyr14Mt_qViRpBCsWeGiJy_MvZtevN5n7tZw-bEZ5gGzGS2KQKwDBo8Tn69WH_kATZaaiyZsJGR9HjJoetsBEwp1g9XBSxn7zDaU-iPaepoY4EqrJGMvx8MR2FGxM9MzfDj0bLLzMBl0EAhlHtGT5a3UQyiNcsJ6_IRtUWS8HkpAFoMcKYbXFM3murPhLrKZYTSGa2hSBA4v8ggyH-BBtQ',
        ];
    }

    private function resolveLoginBackgroundImageUrl(?string $rawPath): string
    {
        $default = $this->getDefaultLoginPageSettings()['background_image'];
        $path = trim((string) $rawPath);

        if ($path === '') {
            return $default;
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        return base_url(ltrim($path, '/'));
    }

    /** /c/{CODE}/login — the same login page, branded and bound to one client. */
    public function clientLogin($code = '')
    {
        helper('client_link');
        $client = client_by_code((string) $code);
        if (! $client) {
            return redirect()->to(base_url('login'))->with('error', 'That client link is not valid.');
        }

        return $this->login($client);
    }

    public function login(?array $clientLink = null)
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

        return view($view, ['loginPageSettings' => $settings, 'clientLink' => $clientLink]);
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
            helper(['role', 'client_link']);
            $role = normalize_role_slug($user['role']);

            // Signed in through a client's own link: the account must belong to that client.
            // The platform owner and vendor company accounts (which may work with several
            // clients sharing one site) can use any client's link.
            $linkCode = trim((string) $this->request->getPost('client_code'));
            if ($linkCode !== '') {
                $linkClient = client_by_code($linkCode);
                $exempt     = in_array($role, ['superadmin', normalize_role_slug('vendor_admin')], true);
                if ($linkClient && ! $exempt && (int) $user['client_id'] !== (int) $linkClient['id']) {
                    return redirect()->back()->with('error', 'This account does not belong to ' . $linkClient['name'] . '. Please use your own login link.')->withInput();
                }
            }

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

    /** /c/{CODE}/register — registration with the client fixed by the link. */
    public function clientRegister($code = '')
    {
        helper('client_link');
        $client = client_by_code((string) $code);
        if (! $client) {
            return redirect()->to(base_url('register'))->with('error', 'That client link is not valid. Please choose your client below.');
        }

        return $this->register($client);
    }

    public function register(?array $clientLink = null)
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register', [
            'pageTitle'  => 'Register Your Company - SafeG',
            'clientLink' => $clientLink,
            'prefillSsm' => trim((string) $this->request->getGet('ssm')),
        ]);
    }

    /** Active clients a vendor can choose to register with: [{id, name}]. */
    private function activeClientOptions(): array
    {
        $rows = (new ClientModel())->where('status', 'active')->orderBy('name', 'ASC')->findAll();

        return array_map(
            static fn(array $c) => ['id' => (int) $c['id'], 'name' => (string) $c['name']],
            $rows
        );
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
            'clients'   => $this->activeClientOptions(),
        ]);
    }

    public function doRegister($linkCode = '')
    {
        helper('client_link');
        $ssmNo        = trim((string) $this->request->getPost('ssm_no'));
        $clientId     = (int) $this->request->getPost('client_id');
        // Registering through a client's own link: the client is the link's, not a free choice.
        $linkClient   = $linkCode !== '' ? client_by_code((string) $linkCode) : null;
        if ($linkClient) {
            $clientId = (int) $linkClient['id'];
        }
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

        $client = $clientId > 0 ? (new ClientModel())->where('id', $clientId)->where('status', 'active')->first() : null;
        if (! $client) {
            $errors[] = 'Please choose the client you are registering with.';
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

        // Remind the client that this vendor is waiting for approval.
        try {
            \App\Libraries\VendorInviteMailer::registered($company, $client, ['full_name' => $fullName, 'email' => $email]);
        } catch (\Throwable $e) {
            log_message('error', 'Registration reminder failed: ' . $e->getMessage());
        }

        return redirect()->to($linkClient ? client_link_url($linkClient, 'login') : base_url('login'))->with('success', 'Registration received. Your account now needs to be verified by the administrator. Once it is verified you can log in with your company SSM No as the username.');
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
