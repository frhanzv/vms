<?php

namespace App\Controllers;

use App\Models\UserModel;

class Settings extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        helper('access');
        if (!has_access('settings', 'view')) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access. You do not have permission to view Settings.');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found');
        }

        $data = [
            'pageTitle' => 'Settings - SafeG',
            'user' => $user,
            'validation' => \Config\Services::validation()
        ];

        return view('settings/index', $data);
    }

    public function updateProfile()
    {
        helper('access');
        if (!has_access('settings', 'view')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $userId = session()->get('user_id');
        $currentUser = $this->userModel->find($userId);

        $rules = [
            'full_name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email',
            'contact_no' => 'permit_empty|min_length[10]|max_length[15]|regex_match[/^[0-9+\-\s()]+$/]',
        ];

        // Only check email uniqueness if email has changed
        $newEmail = $this->request->getPost('email');
        if ($newEmail !== $currentUser['email']) {
            $rules['email'] .= '|is_unique[users.email]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => $newEmail,
            'contact_no' => $this->request->getPost('contact_no'),
            'receive_email_notifications' => $this->request->getPost('receive_email_notifications') ? 1 : 0
        ];

        // Skip model validation since we already validated in controller
        $this->userModel->skipValidation(true);
        
        if ($this->userModel->update($userId, $updateData)) {
            // Reset validation skip
            $this->userModel->skipValidation(false);
            
            // Update session
            session()->set('full_name', $updateData['full_name']);
            session()->set('email', $updateData['email']);
            session()->set('contact_no', $updateData['contact_no']);
            session()->set('receive_email_notifications', $updateData['receive_email_notifications']);
            return redirect()->to('/settings')->with('success', 'Profile updated successfully!');
        }

        // Reset validation skip
        $this->userModel->skipValidation(false);
        
        // Get model errors if any
        $modelErrors = $this->userModel->errors();
        $errorMessage = !empty($modelErrors) ? implode(', ', $modelErrors) : 'Failed to update profile';
        return redirect()->back()->with('error', $errorMessage);
    }

    public function updatePassword()
    {
        $userId = session()->get('user_id');

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('password_errors', $this->validator->getErrors());
        }

        // Verify current password
        $user = $this->userModel->find($userId);
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('password_error', 'Current password is incorrect');
        }

        // Update password
        $updateData = [
            'password' => $this->request->getPost('new_password')
        ];

        if ($this->userModel->update($userId, $updateData)) {
            return redirect()->to('/settings')->with('success', 'Password changed successfully!');
        }

        return redirect()->back()->with('error', 'Failed to change password');
    }

    public function updatePhoto()
    {
        $userId = session()->get('user_id');

        $rules = [
            'profile_photo' => [
                'label' => 'Profile Photo',
                'rules' => 'uploaded[profile_photo]|is_image[profile_photo]|mime_in[profile_photo,image/jpg,image/jpeg,image/png,image/gif]|max_size[profile_photo,2048]',
            ],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $this->validator->getErrors()
            ]);
        }

        $file = $this->request->getFile('profile_photo');

        if ($file->isValid() && !$file->hasMoved()) {
            // Create uploads directory if it doesn't exist
            $uploadPath = FCPATH . 'assets/uploads/profiles/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Delete old photo if exists
            $user = $this->userModel->find($userId);
            if (!empty($user['profile_photo']) && file_exists($uploadPath . $user['profile_photo'])) {
                unlink($uploadPath . $user['profile_photo']);
            }

            // Generate new filename
            $newName = $userId . '_' . time() . '.' . $file->getExtension();
            $file->move($uploadPath, $newName);

            // Update database
            $updateData = ['profile_photo' => $newName];
            if ($this->userModel->update($userId, $updateData)) {
                // Update session
                session()->set('profile_photo', $newName);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Profile photo updated successfully!',
                    'photo_url' => base_url('assets/uploads/profiles/' . $newName)
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to upload photo'
        ]);
    }

    public function removePhoto()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!empty($user['profile_photo'])) {
            // Delete photo file
            $uploadPath = FCPATH . 'assets/uploads/profiles/';
            $photoPath = $uploadPath . $user['profile_photo'];
            
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }

            // Update database to remove photo reference
            $updateData = ['profile_photo' => null];
            if ($this->userModel->update($userId, $updateData)) {
                // Update session
                session()->remove('profile_photo');
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Profile photo removed successfully!'
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'No photo to remove or failed to remove photo'
        ]);
    }
}