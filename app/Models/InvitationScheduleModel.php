<?php

namespace App\Models;

use CodeIgniter\Model;

class InvitationScheduleModel extends Model
{
    protected $table = 'invitation_schedules';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['invitation_id', 'date_from', 'date_to'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'invitation_id' => 'required|integer',
        'date_from' => 'required|valid_date',
        'date_to' => 'required|valid_date'
    ];
}