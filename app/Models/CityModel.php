<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class CityModel extends Model
{
    use OptimisticLockTrait;

    protected $table            = 'cities';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['state_id', 'name', 'code', 'status', 'version'];

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
    protected $validationRules      = [];
    protected $validationMessages   = [];
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
     * Get cities with pagination, search, filters, and sorting
     */
    public function getCitiesWithPagination($search = '', $stateFilter = '', $countryFilter = '', $sortBy = '', $limit = 10, $offset = 0)
    {
        $builder = $this->db->table($this->table);
        $builder->select('cities.*, states.name as state_name, countries.name as country_name');
        $builder->join('states', 'cities.state_id = states.id');
        $builder->join('countries', 'states.country_id = countries.id');

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart();
            $builder->like('cities.name', $search);
            $builder->orLike('cities.code', $search);
            $builder->groupEnd();
        }

        // Apply state filter
        if (!empty($stateFilter)) {
            $builder->where('cities.state_id', $stateFilter);
        }

        // Apply country filter
        if (!empty($countryFilter)) {
            $builder->where('states.country_id', $countryFilter);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'name_asc':
                $builder->orderBy('cities.name', 'ASC');
                break;
            case 'name_desc':
                $builder->orderBy('cities.name', 'DESC');
                break;
            case 'state_asc':
                $builder->orderBy('states.name', 'ASC');
                break;
            case 'state_desc':
                $builder->orderBy('states.name', 'DESC');
                break;
            case 'country_asc':
                $builder->orderBy('countries.name', 'ASC');
                break;
            case 'country_desc':
                $builder->orderBy('countries.name', 'DESC');
                break;
            case 'status':
                $builder->orderBy('cities.status', 'DESC');
                break;
            default:
                $builder->orderBy('cities.name', 'ASC');
        }

        $builder->limit($limit, $offset);
        return $builder->get()->getResultArray();
    }

    /**
     * Get total count of cities with filters
     */
    public function getTotalCities($search = '', $stateFilter = '', $countryFilter = '')
    {
        $builder = $this->db->table($this->table);
        $builder->join('states', 'cities.state_id = states.id');
        $builder->join('countries', 'states.country_id = countries.id');

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart();
            $builder->like('cities.name', $search);
            $builder->orLike('cities.code', $search);
            $builder->groupEnd();
        }

        // Apply state filter
        if (!empty($stateFilter)) {
            $builder->where('cities.state_id', $stateFilter);
        }

        // Apply country filter
        if (!empty($countryFilter)) {
            $builder->where('states.country_id', $countryFilter);
        }

        return $builder->countAllResults();
    }
}
