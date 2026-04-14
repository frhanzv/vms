<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class CountryModel extends Model
{
    use OptimisticLockTrait;

    protected $table = 'countries';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'code', 'status', 'version'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getCountriesWithPagination($search = '', $sortBy = '', $limit = 10, $offset = 0)
    {
        $builder = $this->builder();

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('code', $search)
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
            case 'code_asc':
                $builder->orderBy('code', 'ASC');
                break;
            case 'code_desc':
                $builder->orderBy('code', 'DESC');
                break;
            case 'status':
                $builder->orderBy('status', 'DESC');
                break;
            default:
                $builder->orderBy('name', 'ASC');
        }

        return $builder->limit($limit, $offset)->get()->getResultArray();
    }

    public function getTotalCountries($search = '')
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('code', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }
}
