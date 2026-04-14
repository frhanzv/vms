<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class RejectReasonModel extends Model
{
    use OptimisticLockTrait;

    protected $table = 'reject_reasons';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'reason',
        'mobile_app',
        'commercial',
        'status',
        'version'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'reason' => 'required|max_length[255]',
        'mobile_app' => 'required|in_list[enabled,disabled]',
        'commercial' => 'required|in_list[yes,no]',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'reason' => [
            'required' => 'Reject reason is required',
            'max_length' => 'Reject reason cannot exceed 255 characters'
        ],
        'mobile_app' => [
            'required' => 'Mobile app status is required',
            'in_list' => 'Mobile app must be enabled or disabled'
        ],
        'commercial' => [
            'required' => 'Commercial status is required',
            'in_list' => 'Commercial must be yes or no'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be active or inactive'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get reject reasons with pagination, search, and sorting
     */
    public function getRejectReasonsWithPagination($search = '', $sortBy = '', $limit = 10, $offset = 0)
    {
        $builder = $this->builder();

        // Apply search
        if (!empty($search)) {
            $builder->like('reason', $search);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'reason_asc':
                $builder->orderBy('reason', 'ASC');
                break;
            case 'reason_desc':
                $builder->orderBy('reason', 'DESC');
                break;
            case 'status':
                $builder->orderBy('status', 'DESC');
                break;
            default:
                $builder->orderBy('id', 'DESC');
        }

        $builder->limit($limit, $offset);
        return $builder->get()->getResultArray();
    }

    /**
     * Get total reject reasons count with search
     */
    public function getTotalRejectReasons($search = '')
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->like('reason', $search);
        }

        return $builder->countAllResults();
    }

    /**
     * Get all active reject reasons
     */
    public function getAllActive()
    {
        return $this->where('status', 'active')
            ->orderBy('reason', 'ASC')
            ->findAll();
    }
}
