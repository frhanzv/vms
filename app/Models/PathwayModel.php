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

        $lanes = $this->db->table('pathway_lanes')
            ->select('pathway_lanes.lane_id AS raw_id, pathway_lanes.sort_order, lanes.lane AS name')
            ->join('lanes', 'lanes.id = pathway_lanes.lane_id', 'left')
            ->where('pathway_lanes.pathway_id', $id)
            ->get()
            ->getResultArray();

        $subLocations = $this->db->table('pathway_sub_locations psl')
            ->select('psl.sub_location_id AS raw_id, psl.sort_order, sl.name, l.location_access')
            ->join('sub_locations sl', 'sl.id = psl.sub_location_id', 'left')
            ->join('locations l', 'l.id = sl.location_id', 'left')
            ->where('psl.pathway_id', $id)
            ->get()
            ->getResultArray();

        // Tag each item with its type then return a unified sorted doors array
        foreach ($lanes as &$l) {
            $l['type']    = 'lane';
            $l['lane_id'] = $l['raw_id'];
        }
        unset($l);

        foreach ($subLocations as &$s) {
            $s['type'] = 'sub_location';
        }
        unset($s);

        $doors = array_merge($lanes, $subLocations);
        usort($doors, fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        $pathway['lanes'] = $lanes;          // kept for legacy callers
        $pathway['doors'] = $doors;          // unified list for the modal

        return $pathway;
    }

    // Accepts either a plain array of IDs (legacy) or an array of {id, sort_order} objects
    public function syncLanes($pathwayId, array $laneItems)
    {
        $this->db->table('pathway_lanes')
            ->where('pathway_id', $pathwayId)
            ->delete();

        if (!empty($laneItems)) {
            $batch = [];
            foreach ($laneItems as $order => $item) {
                if (is_array($item)) {
                    $batch[] = [
                        'pathway_id' => $pathwayId,
                        'lane_id'    => (int) $item['id'],
                        'sort_order' => isset($item['sort_order']) ? (int) $item['sort_order'] : $order,
                    ];
                } else {
                    $batch[] = [
                        'pathway_id' => $pathwayId,
                        'lane_id'    => (int) $item,
                        'sort_order' => $order,
                    ];
                }
            }
            $this->db->table('pathway_lanes')->insertBatch($batch);
        }
    }

    // Sync sub-location stops for a pathway (sort_order is position in unified door sequence)
    public function syncSubLocations($pathwayId, array $items)
    {
        $this->db->table('pathway_sub_locations')
            ->where('pathway_id', $pathwayId)
            ->delete();

        if (!empty($items)) {
            $batch = [];
            foreach ($items as $item) {
                $batch[] = [
                    'pathway_id'       => $pathwayId,
                    'sub_location_id'  => (int) $item['id'],
                    'sort_order'       => (int) $item['sort_order'],
                ];
            }
            $this->db->table('pathway_sub_locations')->insertBatch($batch);
        }
    }

    public function getAllActive()
    {
        return $this->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getLanesForPathways(array $pathwayIds): array
    {
        if (empty($pathwayIds)) {
            return [];
        }

        return $this->db->table('pathway_lanes')
            ->select('pathway_lanes.pathway_id, pathway_lanes.lane_id AS raw_id, pathway_lanes.sort_order, lanes.lane AS name')
            ->join('lanes', 'lanes.id = pathway_lanes.lane_id', 'left')
            ->whereIn('pathway_lanes.pathway_id', $pathwayIds)
            ->orderBy('pathway_lanes.pathway_id', 'ASC')
            ->orderBy('pathway_lanes.sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getSubLocationsForPathways(array $pathwayIds): array
    {
        if (empty($pathwayIds)) {
            return [];
        }

        return $this->db->table('pathway_sub_locations psl')
            ->select('psl.pathway_id, psl.sub_location_id AS raw_id, psl.sort_order, sl.name, l.location_access')
            ->join('sub_locations sl', 'sl.id = psl.sub_location_id', 'left')
            ->join('locations l', 'l.id = sl.location_id', 'left')
            ->whereIn('psl.pathway_id', $pathwayIds)
            ->orderBy('psl.pathway_id', 'ASC')
            ->orderBy('psl.sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }
}
