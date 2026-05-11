<?php

/**
 * ============================================================
 *  STEP 2 of 3 — ApiKeyModel
 *  Create this as a NEW file: app/Models/ApiKeyModel.php
 * ============================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class ApiKeyModel extends Model
{
    protected $table      = 'api_keys';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'name',
        'service',
        'api_key',
        'description',
        'status',
        'last_used_at',
        'last_response_json',
        'created_at',
        'updated_at',
    ];

    protected $validationRules = [
        'name'    => 'required|max_length[100]',
        'service' => 'required|max_length[100]',
        'api_key' => 'required',
        'status'  => 'permit_empty|in_list[active,inactive]',
    ];
}