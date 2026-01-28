<?php

namespace App\Models;

use CodeIgniter\Model;

class VisitorLicenseModel extends Model
{
    protected $table = 'visitor_licenses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'invitation_id', 'license_number', 'license_type', 'expiry_date', 'license_class'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
