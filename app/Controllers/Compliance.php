<?php

namespace App\Controllers;

use App\Models\UserModel;

class Compliance extends BaseController
{
    public function index()
    {
        // Get current user data
        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $currentUser = $userModel->find($userId);
        
        $data = [
            'pageTitle' => 'Compliance Dashboard - SafeG',
            'userName' => $currentUser['full_name'] ?? session()->get('full_name') ?? 'Admin User',
            'userRole' => $currentUser['role'] ?? session()->get('role') ?? 'Administrator',
            'userPhoto' => $currentUser['profile_photo'] ?? null
        ];

        return view('compliance/index', $data);
    }
}
