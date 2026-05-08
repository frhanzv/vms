<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailTemplateModel extends Model
{
    protected $table = 'email_templates';
    protected $primaryKey = 'id';
    protected $allowedFields = ['code', 'subject', 'body', 'primary_color', 'content_bg_color', 'text_color', 'logo_url'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'code' => 'required|max_length[120]',
        'subject' => 'permit_empty|max_length[255]',
        'body' => 'permit_empty',
        'primary_color' => 'permit_empty|max_length[7]',
        'content_bg_color' => 'permit_empty|max_length[7]',
        'text_color' => 'permit_empty|max_length[7]',
    ];
}

