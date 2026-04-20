<?php

namespace App\Models;

use App\Traits\OptimisticLockTrait;
use CodeIgniter\Model;

class SecurityAlertPriorityModel extends Model
{
    use OptimisticLockTrait;

    protected $table            = 'security_alert_priorities';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'alert_name',
        'priority',
        'response_time',
        'notification_scope',
        'version',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'alert_name' => 'required|max_length[150]',
        'priority' => 'required|in_list[low,medium,high]',
        'response_time' => 'required|max_length[80]',
        'notification_scope' => 'required|max_length[100]',
    ];
}
