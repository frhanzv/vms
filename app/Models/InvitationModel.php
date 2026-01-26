<?php

namespace App\Models;

use CodeIgniter\Model;

class InvitationModel extends Model
{
    protected $table = 'invitations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'full_name', 'ic_passport', 'contact', 'visitor_email', 'company', 'vehicle_registration', 
        'location', 'invited_by', 'reason', 'other_reason', 'link_expiry', 'status',
        'staff_id', 'company_visited', 'host_contact'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'full_name' => 'required|max_length[255]',
        'ic_passport' => 'permit_empty|max_length[50]',
        'contact' => 'required|max_length[20]',
        'visitor_email' => 'permit_empty|valid_email|max_length[255]',
        'reason' => 'required|max_length[255]',
        'status' => 'in_list[Pending,Approved,Rejected]'
    ];

    protected $validationMessages = [
        'full_name' => [
            'required' => 'Full name is required',
            'max_length' => 'Full name cannot exceed 255 characters'
        ],
        'ic_passport' => [
            'required' => 'IC/Passport number is required',
            'max_length' => 'IC/Passport number cannot exceed 50 characters'
        ],
        'contact' => [
            'required' => 'Contact number is required',
            'max_length' => 'Contact number cannot exceed 20 characters'
        ],
        'visitor_email' => [
            'valid_email' => 'Please provide a valid email address',
            'max_length' => 'Email address cannot exceed 255 characters'
        ],
        'reason' => [
            'required' => 'Reason for visit is required',
            'max_length' => 'Reason cannot exceed 255 characters'
        ]
    ];

    public function getInvitationsWithPagination($page = 1, $perPage = 10, $search = '', $sortBy = 'created_at', $sortOrder = 'DESC')
    {
        $builder = $this->builder();
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('full_name', $search)
                    ->orLike('ic_passport', $search)
                    ->orLike('contact', $search)
                    ->orLike('company', $search)
                    ->orLike('reason', $search)
                    ->groupEnd();
        }

        // Get total count for pagination
        $totalRecords = $builder->countAllResults(false);

        // Apply pagination and sorting
        $offset = ($page - 1) * $perPage;
        $data = $builder->orderBy($sortBy, $sortOrder)
                       ->limit($perPage, $offset)
                       ->get()
                       ->getResultArray();

        return [
            'data' => $data,
            'total' => $totalRecords,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($totalRecords / $perPage)
        ];
    }
}