<?php

namespace App\Models;

use CodeIgniter\Model;

class DesignationModel extends Model
{
    protected $table = 'designations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['name', 'code', 'description', 'wo_flag', 'status', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]',
        'code' => 'permit_empty|min_length[2]|max_length[20]',
        'description' => 'permit_empty|max_length[500]',
        'wo_flag' => 'required|in_list[yes,no]',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Designation name is required',
            'min_length' => 'Designation name must be at least 2 characters',
            'max_length' => 'Designation name cannot exceed 100 characters'
        ],
        'code' => [
            'min_length' => 'Designation code must be at least 2 characters',
            'max_length' => 'Designation code cannot exceed 20 characters'
        ],
        'description' => [
            'max_length' => 'Description cannot exceed 500 characters'
        ],
        'wo_flag' => [
            'required' => 'WO Flag is required',
            'in_list' => 'WO Flag must be either yes or no'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be either active or inactive'
        ]
    ];

    /**
     * Get designations with pagination and search
     */
    public function getDesignationsWithPagination($search = '', $sortBy = '', $limit = 10, $offset = 0)
    {
        $builder = $this->builder();

        // Search
        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('code', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }

        // Sorting
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
            case 'wo_flag':
                $builder->orderBy('wo_flag', 'ASC');
                break;
            case 'status':
                $builder->orderBy('status', 'ASC');
                break;
            default:
                $builder->orderBy('name', 'ASC');
        }

        return $builder->limit($limit, $offset)->get()->getResultArray();
    }

    /**
     * Get total designations count with search
     */
    public function getTotalDesignations($search = '')
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('code', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Get all active designations
     */
    public function getAllActive()
    {
        return $this->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
