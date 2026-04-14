<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailTemplateFormFieldModel extends Model
{
    protected $table = 'email_template_form_fields';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'field_key',
        'label',
        'field_type',
        'placeholder',
        'options',
        'is_required',
        'is_enabled',
        'sort_order',
        'is_system',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getOrderedFields(): array
    {
        return $this->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function getSystemFieldMap(): array
    {
        $rows = $this->where('is_system', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        $map = [];
        foreach ($rows as $row) {
            $map[$row['field_key']] = $row;
        }

        return $map;
    }
}
