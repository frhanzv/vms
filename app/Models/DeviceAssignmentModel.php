<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class DeviceAssignmentModel extends Model
{
    use OptimisticLockTrait;

    protected $table            = 'device_assignments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'device_id',
        'ip_address',
        'status',
        'registration_status',
        'location_id',
        'type',
        'last_heartbeat',
        'version'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getDeviceAssignmentsWithPagination($search = '', $perPage = 10, $offset = 0)
    {
        $builder = $this->db->table($this->table)
            ->select('device_assignments.*, lanes.lane as location_name')
            ->join('lanes', 'lanes.id = device_assignments.location_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('device_assignments.device_id', $search)
                ->orLike('device_assignments.ip_address', $search)
                ->orLike('lanes.lane', $search)
                ->groupEnd();
        }

        return $builder->orderBy('device_assignments.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();
    }

    public function getTotalDeviceAssignments($search = '')
    {
        $builder = $this->db->table($this->table)
            ->join('lanes', 'lanes.id = device_assignments.location_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('device_assignments.device_id', $search)
                ->orLike('device_assignments.ip_address', $search)
                ->orLike('lanes.lane', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }
}
