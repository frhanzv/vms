<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class CompanyModel extends Model
{
    use OptimisticLockTrait;

    protected $table            = 'companies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'registration_no', 'address', 'contact_no', 'email', 'status', 'version'];

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
        'name' => 'required|min_length[3]|max_length[255]',
        'registration_no' => 'permit_empty|max_length[100]',
        'address' => 'permit_empty|max_length[500]',
        'contact_no' => 'permit_empty|max_length[20]',
        'email' => 'permit_empty|valid_email|max_length[255]',
        'status' => 'required|in_list[active,inactive]'
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'Company name is required',
            'min_length' => 'Company name must be at least 3 characters',
            'max_length' => 'Company name cannot exceed 255 characters'
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
     * Get companies with pagination and search
     */
    public function getCompaniesWithPagination($search = '', $sortBy = '', $limit = 10, $offset = 0)
    {
        $builder = $this->select('id, name, registration_no, address, contact_no, email, status, created_at');
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('name', $search)
                    ->orLike('registration_no', $search)
                    ->orLike('email', $search)
                    ->orLike('contact_no', $search)
                    ->groupEnd();
        }
        
        // Apply sorting
        switch ($sortBy) {
            case 'name_asc':
                $builder->orderBy('name', 'ASC');
                break;
            case 'name_desc':
                $builder->orderBy('name', 'DESC');
                break;
            case 'status':
                $builder->orderBy('status', 'DESC');
                break;
            default:
                $builder->orderBy('created_at', 'DESC');
        }
        
        $builder->limit($limit, $offset);
        
        return $builder->findAll();
    }

    /**
     * Get total companies count with search
     */
    public function getTotalCompanies($search = '')
    {
        if (!empty($search)) {
            $this->groupStart()
                 ->like('name', $search)
                 ->orLike('registration_no', $search)
                 ->orLike('email', $search)
                 ->orLike('contact_no', $search)
                 ->groupEnd();
        }
        
        return $this->countAllResults();
    }
}
