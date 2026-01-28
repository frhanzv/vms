<?php

namespace App\Models;

use CodeIgniter\Model;

class VisitorEquipmentModel extends Model
{
    protected $table = 'visitor_equipment';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'invitation_id', 'equipment_type', 'brand_model', 'serial_number', 'quantity',
        'category', 'size', 'transport', 'purpose', 'voltage'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
