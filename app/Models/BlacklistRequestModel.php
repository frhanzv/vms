<?php

namespace App\Models;

use CodeIgniter\Model;

class BlacklistRequestModel extends Model
{
    protected $table            = 'blacklist';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Allowed fields
    protected $allowedFields = [
        'created_date',
        'blacklist_date',
        'ic_passport_no',
        'staff_id',
        'name',
        'status',
        'type',
    ];

    // Validation rules
    protected $validationRules = [
        'name'          => 'required|max_length[255]',
        'ic_passport_no'=> 'required|max_length[100]',
        'blacklist_date' => 'permit_empty|valid_date[Y-m-d]',
        'created_date'  => 'permit_empty|valid_date[Y-m-d]',
        'status'        => 'required|in_list[active,pending,closed]',
        'type'          => 'permit_empty|max_length[100]',
        'staff_id'      => 'permit_empty|max_length[100]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Name is required.',
            'max_length' => 'Name cannot exceed 255 characters.',
        ],
        'ic_passport_no' => [
            'required'   => 'IC / Passport No is required.',
            'max_length' => 'IC / Passport No cannot exceed 100 characters.',
        ],
        'status' => [
            'required' => 'Status is required.',
            'in_list'  => 'Status must be active, pending, or closed.',
        ],
    ];

    protected $skipValidation = false;

    // -------------------------
    // Custom query methods
    // -------------------------

    // Get all with optional filters
    public function getAll(array $filters = [])
    {
        $builder = $this->db->table($this->table);

        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $builder->where('type', $filters['type']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('name', $filters['search'])
                ->orLike('ic_passport_no', $filters['search'])
                ->orLike('staff_id', $filters['search'])
            ->groupEnd();
        }

        return $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
    }

    // Get single record by ID
    public function getById(int $id)
    {
        return $this->find($id);
    }

    // Check for duplicate IC/Passport (useful before insert)
    public function isDuplicate(string $icPassport, ?int $excludeId = null): bool
    {
        $builder = $this->where('ic_passport_no', $icPassport);

        if ($excludeId !== null) {
            $builder = $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}