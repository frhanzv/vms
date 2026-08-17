<?php

namespace App\Models;

use CodeIgniter\Model;

class EmapMapModel extends Model
{
    protected $table            = 'emap_maps';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'client_id',
        'name',
        'premise_name',
        'floor_name',
        'canvas_width',
        'canvas_height',
        'layout_json',
        'status',
        'version',
        'created_by',
        'updated_by',
        'published_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'          => 'required|max_length[150]',
        'premise_name'  => 'required|max_length[150]',
        'floor_name'    => 'required|max_length[150]',
        'canvas_width'  => 'required|integer|greater_than_equal_to[400]',
        'canvas_height' => 'required|integer|greater_than_equal_to[300]',
        'status'        => 'required|in_list[draft,published]',
    ];
}
