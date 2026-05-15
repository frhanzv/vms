<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class UserModel extends Model
{
    use OptimisticLockTrait;

    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['company_id', 'username', 'email', 'password', 'full_name', 'staff_id', 'contact_no', 'role', 'is_active', 'receive_email_notifications', 'profile_photo', 'version'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'full_name' => 'required|min_length[3]|max_length[255]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    public function verifyPassword($usernameOrEmail, $password)
    {
        // Check if input is email or username
        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            $user = $this->where('email', $usernameOrEmail)
                         ->where('is_active', 1)
                         ->first();
        } else {
            $user = $this->where('username', $usernameOrEmail)
                         ->where('is_active', 1)
                         ->first();
        }

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    /**
     * Get users with pagination, search and sorting
     */
    public function getUsersWithPagination($search = '', $sortBy = '', $limit = 10, $offset = 0)
    {
        $builder = $this->select('id, username, email, full_name, staff_id, contact_no, role, is_active, created_at');
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('username', $search)
                    ->orLike('full_name', $search)
                    ->orLike('email', $search)
                    ->orLike('staff_id', $search)
                    ->orLike('contact_no', $search)
                    ->groupEnd();
        }
        
        // Apply sorting
        switch ($sortBy) {
            case 'username_asc':
                $builder->orderBy('username', 'ASC');
                break;
            case 'username_desc':
                $builder->orderBy('username', 'DESC');
                break;
            case 'name_asc':
                $builder->orderBy('full_name', 'ASC');
                break;
            case 'name_desc':
                $builder->orderBy('full_name', 'DESC');
                break;
            case 'email_asc':
                $builder->orderBy('email', 'ASC');
                break;
            case 'email_desc':
                $builder->orderBy('email', 'DESC');
                break;
            case 'status':
                $builder->orderBy('is_active', 'DESC');
                break;
            default:
                $builder->orderBy('created_at', 'DESC');
        }
        
        $builder->limit($limit, $offset);
        
        return $builder->findAll();
    }

    /**
     * Get all active admins (clientsuperadmin + admin) for a given company.
     */
    public function getCompanyAdmins(int $companyId): array
    {
        return $this->select('id, full_name, email, contact_no')
            ->whereIn('role', ['clientsuperadmin', 'admin'])
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->findAll();
    }

    /**
     * Get all active superadmins (platform-level).
     */
    public function getSuperAdmins(): array
    {
        return $this->select('id, full_name, email, contact_no')
            ->where('role', 'superadmin')
            ->where('is_active', 1)
            ->findAll();
    }

    /**
     * Get total users count with search
     */
    public function getTotalUsers($search = '')
    {
        if (!empty($search)) {
            $this->groupStart()
                 ->like('username', $search)
                 ->orLike('full_name', $search)
                 ->orLike('email', $search)
                 ->orLike('staff_id', $search)
                 ->orLike('contact_no', $search)
                 ->groupEnd();
        }
        
        return $this->countAllResults();
    }
}
