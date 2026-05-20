<?php

namespace App\Models;

use CodeIgniter\Model;

class StaffModel extends Model
{
    protected $table            = 'staff';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'app_no',
        'full_name',
        'ic_passport',
        'staff_no',
        'status',
        'suspension_period',
        'next_action',
        'card_status',
        'card_expiry',
        'remark',
        'location_access',
        'date_of_application',
        'type_of_application',
        'designation',
        'resident',
        'sub_type',
        'date_of_birth',
        'sex',
        'name_on_staff_pass',
        'contact_number',
        'email',
        'department',
        'address_1',
        'address_2',
        'address_3',
        'city',
        'state',
        'postal_code',
        'country',
        'csp_number',
        'csp_expiry_date',
        'evetting_date_of_application',
        'evetting_date_of_result',
        'evetting_result',
        'government_id',
        'other_doc',
        'visa_expiry',
        'license_class',
        'license_expiry',
    ];

    // Timestamps
    protected $useTimestamps = false; // 'created_at' is managed by DB default
    protected $createdField  = 'created_at';

    // Validation
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}