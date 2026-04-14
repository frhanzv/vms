<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class SubCompanyModel extends Model
{
    use OptimisticLockTrait;

    protected $table            = 'sub_companies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['company_id', 'name', 'description', 'status', 'version'];

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
        'company_id' => 'required|is_natural_no_zero',
        'name' => 'required|min_length[3]|max_length[255]',
        'description' => 'permit_empty|max_length[500]',
        'status' => 'required|in_list[active,inactive]'
    ];
    protected $validationMessages   = [
        'company_id' => [
            'required' => 'Company is required',
            'is_natural_no_zero' => 'Invalid company selected'
        ],
        'name' => [
            'required' => 'Sub company name is required',
            'min_length' => 'Sub company name must be at least 3 characters',
            'max_length' => 'Sub company name cannot exceed 255 characters'
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
     * Get sub companies with pagination and search, including parent company name
     */
    public function getSubCompaniesWithPagination($search = '', $companyFilter = '', $sortBy = '', $limit = 10, $offset = 0)
    {
        $builder = $this->select('sub_companies.id, sub_companies.name, sub_companies.description, sub_companies.status, sub_companies.created_at, sub_companies.company_id, companies.name as company_name')
                        ->join('companies', 'companies.id = sub_companies.company_id', 'left');
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('sub_companies.name', $search)
                    ->orLike('sub_companies.description', $search)
                    ->orLike('companies.name', $search)
                    ->groupEnd();
        }
        
        if (!empty($companyFilter)) {
            $builder->where('sub_companies.company_id', $companyFilter);
        }
        
        // Apply sorting
        switch ($sortBy) {
            case 'name_asc':
                $builder->orderBy('sub_companies.name', 'ASC');
                break;
            case 'name_desc':
                $builder->orderBy('sub_companies.name', 'DESC');
                break;
            case 'company':
                $builder->orderBy('companies.name', 'ASC');
                break;
            case 'status':
                $builder->orderBy('sub_companies.status', 'DESC');
                break;
            default:
                $builder->orderBy('sub_companies.created_at', 'DESC');
        }
        
        $builder->limit($limit, $offset);
        
        return $builder->findAll();
    }

    /**
     * Get total sub companies count with search
     */
    public function getTotalSubCompanies($search = '', $companyFilter = '')
    {
        $builder = $this->select('sub_companies.id')
                        ->join('companies', 'companies.id = sub_companies.company_id', 'left');
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('sub_companies.name', $search)
                    ->orLike('sub_companies.description', $search)
                    ->orLike('companies.name', $search)
                    ->groupEnd();
        }
        
        if (!empty($companyFilter)) {
            $builder->where('sub_companies.company_id', $companyFilter);
        }
        
        return $builder->countAllResults();
    }
}
