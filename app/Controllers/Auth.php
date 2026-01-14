<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        // If user is already logged in, redirect to dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
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
                'username' => $user['username'],
                'email' => $user['email'],
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
