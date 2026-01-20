<?php

namespace App\Models;

use CodeIgniter\Model;

class InvitationVisitorModel extends Model
{
    protected $table = 'invitation_visitors';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'invitation_id', 'full_name', 'ic_passport', 'contact', 
        'company', 'vehicle_registration'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'invitation_id' => 'required|integer',
        'full_name' => 'required|max_length[255]',
        'ic_passport' => 'required|max_length[50]',
        'contact' => 'required|max_length[20]'
    ];
}