<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkflowModel extends Model
{
    protected $table            = 'workflows';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'client_id',
        'step_name',
        'step_key',
        'step_order',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'step_name'  => 'required|max_length[100]',
        'step_key'   => 'required|max_length[50]',
        'step_order' => 'required|integer',
        'is_active'  => 'required|in_list[0,1]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
