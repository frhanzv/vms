<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class VisitorTypeModel extends Model
{
    use OptimisticLockTrait;

    protected $table            = 'visitor_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'path', 'version'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[255]|is_unique[visitor_types.name]',
        'path' => 'permit_empty|max_length[500]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Visitor type name is required',
            'is_unique'  => 'This visitor type already exists',
            'max_length' => 'Name cannot exceed 255 characters',
        ],
    ];

    public function getVisitorTypesPage(int $limit, int $offset, string $search = '', string $sortBy = 'created_at', string $sortOrder = 'DESC'): array
    {
        $builder = $this->builder();

        if ($search !== '') {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('path', $search)
                ->groupEnd();
        }

        $allowedSort = ['created_at', 'name', 'path'];
        if (! in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'created_at';
        }
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

        return $builder->orderBy($sortBy, $sortOrder)
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function countVisitorTypes(string $search = ''): int
    {
        $builder = $this->builder();

        if ($search !== '') {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('path', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }
}
