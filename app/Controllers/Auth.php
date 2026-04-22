<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SettingModel;

class Auth extends BaseController
{
    private function getDefaultLoginPageSettings(): array
    {
        return [
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
            'demo_admin_text' => 'Admin: admin / admin123',
            'demo_host_text' => 'Host: host / host123',
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

        return view('auth/login', ['loginPageSettings' => $settings]);
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
            // Set session data
            $sessionData = [
                'user_id' => $user['id'],
                'staff_id' => $user['staff_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'contact_no' => $user['contact_no'],
                'full_name' => $user['full_name'],
                'role' => $user['role'],
                'isLoggedIn' => true,
                'loginTime' => time()
            ];
            session()->set($sessionData);

            // Handle remember me
            if ($remember) {
                // Set cookie for 30 days
                setcookie('remember_user', $user['username'], time() + (30 * 24 * 60 * 60), '/');
            }

            return redirect()->to('/dashboard')->with('success', 'Login successful!');
        } else {
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
}
