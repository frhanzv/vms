<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['name', 'code', 'description', 'status', 'created_at', 'updated_at'];

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
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Department name is required',
            'min_length' => 'Department name must be at least 2 characters',
            'max_length' => 'Department name cannot exceed 100 characters'
        ],
        'code' => [
            'min_length' => 'Department code must be at least 2 characters',
            'max_length' => 'Department code cannot exceed 20 characters'
        ],
        'description' => [
            'max_length' => 'Description cannot exceed 500 characters'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be either active or inactive'
        ]
    ];

    /**
     * Get departments with pagination and search
     */
    public function getDepartmentsWithPagination($search = '', $sortBy = '', $limit = 10, $offset = 0)
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
            case 'status':
                $builder->orderBy('status', 'ASC');
                break;
            default:
                $builder->orderBy('name', 'ASC');
        }

        return $builder->limit($limit, $offset)->get()->getResultArray();
    }

    /**
     * Get total departments count with search
     */
    public function getTotalDepartments($search = '')
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
     * Get all active departments
     */
    public function getAllActive()
    {
        return $this->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
