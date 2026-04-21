<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class PathwayModel extends Model
{
    use OptimisticLockTrait;

    protected $table            = 'pathways';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'status',
        'version',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'   => 'required|max_length[255]',
        'status' => 'required|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Pathway name is required',
        ],
        'status' => [
            'required'  => 'Status is required',
            'in_list'   => 'Status must be active or inactive',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getPathwaysWithPagination($search = '', $sortBy = '', $limit = 10, $offset = 0)
    {
        $builder = $this->db->table($this->table);
        $builder->select('pathways.*');

        if (!empty($search)) {
            $builder->like('pathways.name', $search);
        }

        switch ($sortBy) {
            case 'name_asc':
                $builder->orderBy('pathways.name', 'ASC');
                break;
            case 'name_desc':
                $builder->orderBy('pathways.name', 'DESC');
                break;
            case 'status':
                $builder->orderBy('pathways.status', 'DESC');
                break;
            default:
                $builder->orderBy('pathways.id', 'DESC');
        }

        $builder->limit($limit, $offset);
        return $builder->get()->getResultArray();
    }

    public function getTotalPathways($search = '')
    {
        $builder = $this->db->table($this->table);

        if (!empty($search)) {
            $builder->like('pathways.name', $search);
        }

        return $builder->countAllResults();
    }

    public function getPathwayWithLanes($id)
    {
        $pathway = $this->find($id);
        if (!$pathway) {
            return null;
        }

        $pathway['lanes'] = $this->db->table('pathway_lanes')
            ->select('pathway_lanes.lane_id, pathway_lanes.sort_order, lanes.lane')
            ->join('lanes', 'lanes.id = pathway_lanes.lane_id', 'left')
            ->where('pathway_lanes.pathway_id', $id)
            ->orderBy('pathway_lanes.sort_order', 'ASC')
            ->get()
            ->getResultArray();

        return $pathway;
    }

    public function syncLanes($pathwayId, array $laneIds)
    {
        $this->db->table('pathway_lanes')
            ->where('pathway_id', $pathwayId)
            ->delete();

        if (!empty($laneIds)) {
            $batch = [];
            foreach ($laneIds as $order => $laneId) {
                $batch[] = [
                    'pathway_id' => $pathwayId,
                    'lane_id'    => (int) $laneId,
                    'sort_order' => $order,
                ];
            }
            $this->db->table('pathway_lanes')->insertBatch($batch);
        }
    }

    public function getAllActive()
    {
        return $this->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getLanesForPathways(array $pathwayIds)
    {
        if (empty($pathwayIds)) {
            return [];
        }

        return $this->db->table('pathway_lanes')
            ->select('pathway_lanes.pathway_id, pathway_lanes.lane_id, pathway_lanes.sort_order, lanes.lane')
            ->join('lanes', 'lanes.id = pathway_lanes.lane_id', 'left')
            ->whereIn('pathway_lanes.pathway_id', $pathwayIds)
            ->orderBy('pathway_lanes.pathway_id', 'ASC')
            ->orderBy('pathway_lanes.sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }
}
