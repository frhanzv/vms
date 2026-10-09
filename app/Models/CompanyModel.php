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
    protected $allowedFields    = ['name', 'pass_name', 'registration_no', 'address', 'contact_no', 'email', 'status', 'version'];

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
        'pass_name' => 'permit_empty|max_length[255]',
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
        $builder = $this->select('companies.id, companies.name, companies.pass_name, companies.registration_no, companies.address, companies.contact_no, companies.email, companies.status, companies.created_at, ' . $this->registrationStateSql() . ' AS registration_state', false);
        $this->applyRegistrationFilter($builder);
        
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
        $this->applyRegistrationFilter($this);
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

    /**
     * Has the vendor registered yet? (Config > Company "Registered" column.)
     *   registered      = a vendor account exists and is active (verified)
     *   pending         = a vendor account exists, waiting for admin verification
     *                     (or switched off)
     *   not_registered  = nobody has registered for this company
     */
    private function registrationStateSql(): string
    {
        return "CASE "
            . "WHEN EXISTS (SELECT 1 FROM users vu WHERE vu.company_id = companies.id AND vu.role = 'vendor_admin' AND vu.is_active = 1) THEN 'registered' "
            . "WHEN EXISTS (SELECT 1 FROM users vu WHERE vu.company_id = companies.id AND vu.role = 'vendor_admin') THEN 'pending' "
            . "ELSE 'not_registered' END";
    }

    /** Optional ?registered=registered|pending|not_registered filter from the list page. */
    private function applyRegistrationFilter($builder): void
    {
        $f = (string) service('request')->getGet('registered');
        $conds = [
            'registered'     => "EXISTS (SELECT 1 FROM users vu WHERE vu.company_id = companies.id AND vu.role = 'vendor_admin' AND vu.is_active = 1)",
            'pending'        => "(EXISTS (SELECT 1 FROM users vu WHERE vu.company_id = companies.id AND vu.role = 'vendor_admin') AND NOT EXISTS (SELECT 1 FROM users vu WHERE vu.company_id = companies.id AND vu.role = 'vendor_admin' AND vu.is_active = 1))",
            'not_registered' => "NOT EXISTS (SELECT 1 FROM users vu WHERE vu.company_id = companies.id AND vu.role = 'vendor_admin')",
        ];
        if (isset($conds[$f])) {
            $builder->where($conds[$f], null, false);
        }
    }

    /**
     * Looks up an admin-registered company by its SSM No (registration_no)
     * for the public vendor self-registration page. Only ACTIVE companies
     * are findable, and the match is exact after trimming.
     */
    public function findByRegistrationNo(string $ssmNo): ?array
    {
        $ssmNo = trim($ssmNo);
        if ($ssmNo === '') {
            return null;
        }

        return $this->where('registration_no', $ssmNo)
            ->where('status', 'active')
            ->first();
    }
}
