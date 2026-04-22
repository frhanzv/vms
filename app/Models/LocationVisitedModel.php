<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class LocationVisitedModel extends Model
{
    use OptimisticLockTrait;

    protected $table            = 'location_visited';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'status', 'version'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[255]|is_unique[location_visited.name]',
        'status' => 'required|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Location visited name is required',
            'is_unique' => 'This location visited already exists',
            'max_length' => 'Name cannot exceed 255 characters',
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be active or inactive',
        ],
    ];

    public function getRowsPage(int $limit, int $offset, string $search = '', string $sortBy = 'created_at', string $sortOrder = 'DESC'): array
    {
        $builder = $this->builder();

        if ($search !== '') {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('status', $search)
                ->groupEnd();
        }

        $allowedSort = ['created_at', 'name', 'status'];
        if (!in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'created_at';
        }
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

        return $builder->orderBy($sortBy, $sortOrder)
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function countRows(string $search = ''): int
    {
        $builder = $this->builder();

        if ($search !== '') {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('status', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }
}
