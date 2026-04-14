<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class StateModel extends Model
{
    use OptimisticLockTrait;

    protected $table = 'states';
    protected $primaryKey = 'id';
    protected $allowedFields = ['country_id', 'name', 'code', 'status', 'version'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getStatesWithPagination($search = '', $countryFilter = '', $sortBy = '', $limit = 10, $offset = 0)
    {
        $builder = $this->builder();
        $builder->select('states.*, countries.name as country_name');
        $builder->join('countries', 'countries.id = states.country_id');

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('states.name', $search)
                ->orLike('states.code', $search)
                ->orLike('countries.name', $search)
                ->groupEnd();
        }

        // Apply country filter
        if (!empty($countryFilter)) {
            $builder->where('states.country_id', $countryFilter);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'name_asc':
                $builder->orderBy('states.name', 'ASC');
                break;
            case 'name_desc':
                $builder->orderBy('states.name', 'DESC');
                break;
            case 'country_asc':
                $builder->orderBy('countries.name', 'ASC');
                break;
            case 'country_desc':
                $builder->orderBy('countries.name', 'DESC');
                break;
            case 'status':
                $builder->orderBy('states.status', 'DESC');
                break;
            default:
                $builder->orderBy('countries.name', 'ASC')
                       ->orderBy('states.name', 'ASC');
        }

        return $builder->limit($limit, $offset)->get()->getResultArray();
    }

    public function getTotalStates($search = '', $countryFilter = '')
    {
        $builder = $this->builder();
        $builder->select('states.*');
        $builder->join('countries', 'countries.id = states.country_id');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('states.name', $search)
                ->orLike('states.code', $search)
                ->orLike('countries.name', $search)
                ->groupEnd();
        }

        if (!empty($countryFilter)) {
            $builder->where('states.country_id', $countryFilter);
        }

        return $builder->countAllResults();
    }
}
