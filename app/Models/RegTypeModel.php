<?php

namespace App\Models;

use CodeIgniter\Model;

class RegTypeModel extends Model
{
    protected $table         = 'reg_type';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'name',
        'can_print_cp',
        'status',
    ];

    protected $validationRules = [
        'name'   => 'required|min_length[2]|max_length[100]',
        'status' => 'required|in_list[Active,Inactive]',
    ];
}