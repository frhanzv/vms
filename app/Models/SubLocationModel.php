<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class SubLocationModel extends Model
{
    use OptimisticLockTrait;

    protected $table            = 'sub_locations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'location_id', 'status', 'version'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'        => 'required|max_length[255]',
        'location_id' => 'required|integer',
        'status'      => 'required|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'name'        => ['required' => 'Sub location name is required'],
        'location_id' => ['required' => 'Location access is required'],
        'status'      => [
            'required' => 'Status is required',
            'in_list'  => 'Status must be active or inactive',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // -----------------------------------------------------------------------
    // Paginated list with joined location_access name
    // -----------------------------------------------------------------------
    public function getWithPagination(
        string $search         = '',
        string $locationFilter = '',
        string $sortBy         = '',
        int    $limit          = 10,
        int    $offset         = 0
    ): array {
        $builder = $this->db->table('sub_locations sl')
            ->select('sl.*, l.location_access, l.branch')
            ->join('locations l', 'l.id = sl.location_id', 'left');

        if ($search !== '') {
            $builder->groupStart()
                ->like('sl.name', $search)
                ->orLike('l.location_access', $search)
                ->groupEnd();
        }

        if ($locationFilter !== '') {
            $builder->where('sl.location_id', $locationFilter);
        }

        switch ($sortBy) {
            case 'name_asc':      $builder->orderBy('sl.name', 'ASC'); break;
            case 'name_desc':     $builder->orderBy('sl.name', 'DESC'); break;
            case 'location_asc':  $builder->orderBy('l.location_access', 'ASC'); break;
            case 'location_desc': $builder->orderBy('l.location_access', 'DESC'); break;
            case 'status':        $builder->orderBy('sl.status', 'ASC'); break;
            default:              $builder->orderBy('sl.id', 'DESC');
        }

        return $builder->limit($limit, $offset)->get()->getResultArray();
    }

    public function getTotal(string $search = '', string $locationFilter = ''): int
    {
        $builder = $this->db->table('sub_locations sl')
            ->join('locations l', 'l.id = sl.location_id', 'left');

        if ($search !== '') {
            $builder->groupStart()
                ->like('sl.name', $search)
                ->orLike('l.location_access', $search)
                ->groupEnd();
        }

        if ($locationFilter !== '') {
            $builder->where('sl.location_id', $locationFilter);
        }

        return (int) $builder->countAllResults();
    }

    // Used by Pathway Management to populate the "Available Doors" list
    public function getAllActive(): array
    {
        return $this->db->table('sub_locations sl')
            ->select('sl.id, sl.name, l.location_access, l.branch')
            ->join('locations l', 'l.id = sl.location_id', 'left')
            ->where('sl.status', 'active')
            ->groupBy('sl.id')
            ->orderBy('l.location_access', 'ASC')
            ->orderBy('sl.name', 'ASC')
            ->get()
            ->getResultArray();
    }
}