<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class VisitReasonModel extends Model
{
    use OptimisticLockTrait;

    protected $table = 'visit_reasons';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['reason', 'version'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'reason' => 'required|min_length[3]|max_length[255]|is_unique[visit_reasons.reason]'
    ];

    protected $validationMessages = [
        'reason' => [
            'required' => 'Visit reason is required',
            'min_length' => 'Visit reason must be at least 3 characters',
            'max_length' => 'Visit reason cannot exceed 255 characters',
            'is_unique' => 'This visit reason already exists'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Get paginated visit reasons with search
    public function getVisitReasons($limit = 10, $offset = 0, $search = '')
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->like('reason', $search);
        }

        return $builder
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    // Count visit reasons with search
    public function countVisitReasons($search = '')
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->like('reason', $search);
        }

        return $builder->countAllResults();
    }
}
