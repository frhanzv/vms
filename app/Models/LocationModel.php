<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $table = 'locations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'branch', 'location_access', 'adam_ip', 'adam_password', 
        'mobile_app', 'is_hold_area', 'visitor_pass_print', 'turnstile', 
        'in_out_bound', 'status', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'branch' => 'required|min_length[2]|max_length[100]',
        'location_access' => 'required|min_length[2]|max_length[100]',
        'adam_ip' => 'permit_empty|valid_ip',
        'adam_password' => 'permit_empty|min_length[4]|max_length[100]',
        'mobile_app' => 'required|in_list[enabled,disabled]',
        'is_hold_area' => 'required|in_list[yes,no]',
        'visitor_pass_print' => 'required|in_list[enabled,disabled]',
        'turnstile' => 'required|in_list[active,inactive]',
        'in_out_bound' => 'required|in_list[inbound,outbound,both]',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'branch' => [
            'required' => 'Branch name is required',
            'min_length' => 'Branch name must be at least 2 characters',
            'max_length' => 'Branch name cannot exceed 100 characters'
        ],
        'location_access' => [
            'required' => 'Location access is required',
            'min_length' => 'Location access must be at least 2 characters',
            'max_length' => 'Location access cannot exceed 100 characters'
        ],
        'adam_ip' => [
            'valid_ip' => 'Please enter a valid IP address'
        ],
        'adam_password' => [
            'min_length' => 'Password must be at least 4 characters',
            'max_length' => 'Password cannot exceed 100 characters'
        ],
        'mobile_app' => [
            'required' => 'Mobile app status is required',
            'in_list' => 'Mobile app status must be enabled or disabled'
        ],
        'is_hold_area' => [
            'required' => 'Hold area status is required',
            'in_list' => 'Hold area must be yes or no'
        ],
        'visitor_pass_print' => [
            'required' => 'Visitor pass print status is required',
            'in_list' => 'Visitor pass print must be enabled or disabled'
        ],
        'turnstile' => [
            'required' => 'Turnstile status is required',
            'in_list' => 'Turnstile status must be active or inactive'
        ],
        'in_out_bound' => [
            'required' => 'In/Out bound is required',
            'in_list' => 'In/Out bound must be inbound, outbound, or both'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be either active or inactive'
        ]
    ];

    /**
     * Get locations with pagination and search
     */
    public function getLocationsWithPagination($search = '', $sortBy = '', $limit = 10, $offset = 0)
    {
        $builder = $this->builder();

        // Search
        if (!empty($search)) {
            $builder->groupStart()
                ->like('branch', $search)
                ->orLike('location_access', $search)
                ->orLike('adam_ip', $search)
                ->groupEnd();
        }

        // Sorting
        switch ($sortBy) {
            case 'branch_asc':
                $builder->orderBy('branch', 'ASC');
                break;
            case 'branch_desc':
                $builder->orderBy('branch', 'DESC');
                break;
            case 'location_asc':
                $builder->orderBy('location_access', 'ASC');
                break;
            case 'location_desc':
                $builder->orderBy('location_access', 'DESC');
                break;
            case 'status':
                $builder->orderBy('status', 'ASC');
                break;
            default:
                $builder->orderBy('branch', 'ASC')->orderBy('location_access', 'ASC');
        }

        return $builder->limit($limit, $offset)->get()->getResultArray();
    }

    /**
     * Get total locations count with search
     */
    public function getTotalLocations($search = '')
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->groupStart()
                ->like('branch', $search)
                ->orLike('location_access', $search)
                ->orLike('adam_ip', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Get all active locations
     */
    public function getAllActive()
    {
        return $this->where('status', 'active')
            ->orderBy('branch', 'ASC')
            ->orderBy('location_access', 'ASC')
            ->findAll();
    }
}
