<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class InvitationModel extends Model
{
    use OptimisticLockTrait;

    protected $table = 'invitations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'full_name', 'ic_passport', 'contact', 'visitor_email', 'company', 'vehicle_registration', 
        'location', 'invited_by', 'reason', 'visitor_type_id', 'other_reason', 'link_expiry', 'status',
        'staff_id', 'company_visited', 'host_contact', 'registration_no',
        'date_of_birth', 'sex', 'resident', 'address', 'postcode', 'city', 'state', 'country',
        'government_id_path', 'invitation_letter_path', 'profile_photo_path',
        'vehicle_category', 'vehicle_type',
        'video_watched', 'video_watched_at', 'video_completion_percentage',
        'facial_verification_image', 'facial_verified_at', 'checked_in_at',
        'version'
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
        'status' => 'in_list[Pending,Submitted,Approved,Rejected]'
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