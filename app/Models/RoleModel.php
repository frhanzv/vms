<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class RoleModel extends Model
{
    use OptimisticLockTrait;

    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'description', 'status', 'version'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|min_length[3]|max_length[50]|is_unique[roles.name,id,{id}]',
        'description' => 'permit_empty|max_length[255]',
        'status' => 'required|in_list[active,inactive]'
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'Role name is required',
            'min_length' => 'Role name must be at least 3 characters',
            'max_length' => 'Role name cannot exceed 50 characters',
            'is_unique' => 'This role name already exists'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be either active or inactive'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get roles with user count
     */
    public function getRolesWithUserCount($search = '', $limit = 10, $offset = 0)
    {
        $builder = $this->db->table($this->table);
        $builder->select('roles.*, COUNT(users.id) as user_count');
        $builder->join('users', 'users.role = roles.name', 'left');
        $builder->groupBy('roles.id');
        
        if (!empty($search)) {
            $builder->like('roles.name', $search);
            $builder->orLike('roles.description', $search);
        }
        
        $builder->orderBy('roles.created_at', 'DESC');
        $builder->limit($limit, $offset);
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get total roles count with search
     */
    public function getTotalRoles($search = '')
    {
        $builder = $this->db->table($this->table);
        
        if (!empty($search)) {
            $builder->like('name', $search);
            $builder->orLike('description', $search);
        }
        
        return $builder->countAllResults();
    }

    /**
     * Check if role is assigned to any user
     */
    public function isRoleAssigned($roleId)
    {
        $role = $this->find($roleId);
        if (!$role) {
            return false;
        }

        $userModel = new \App\Models\UserModel();
        $count = $userModel->where('role', $role['name'])->countAllResults();
        
        return $count > 0;
    }

    /**
     * Get role by name
     */
    public function getRoleByName($name)
    {
        return $this->where('name', $name)->first();
    }
}
