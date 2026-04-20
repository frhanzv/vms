<?php

namespace App\Models;

use CodeIgniter\Model;

class BusinessTypeModel extends Model
{
    protected $table         = 'businesstype';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'business_type', 'reg_type', 'ledger', 'haulier',
        'lpk_license_no', 'lpk_license_no_optional', 'lpk_ancillary_contractor',
        'customs_license_no', 'sst_reg_no', 'business_vol', 'trade_ref_no',
        'bank_info', 'operator_code', 'copy_board_director_ic', 'apad_certificate_no',
        'license_expiry_date', 'warehouse_info', 'nature_of_business', 'pli', 'status',
    ];
}