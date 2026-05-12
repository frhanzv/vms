<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class LaneModel extends Model
{
    use OptimisticLockTrait;

    protected $table = 'lanes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'lane',
        'location_id',
        'barrier_no',
        'weight_id',
        'slip_print',
        'antena_ip',
        'kiosk_ip',
        'cam_id_1',
        'cam_id_2',
        'cam_id_3',
        'cam_photo_ip_1',
        'cam_photo_ip_2',
        'in_bound',
        'out_bound',
        'last_logged_in_by',
        'last_logged_in_datetime',
        'last_changed_on_printer_paper',
        'status',
        'rfid_reader_ip',
        'rfid_reader_port',
        'rfid_enabled',
        'scan_type',
        'version'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'lane' => 'required|max_length[100]',
        'location_id' => 'required|is_natural_no_zero',
        'barrier_no' => 'permit_empty|max_length[50]',
        'weight_id' => 'permit_empty|max_length[50]',
        'slip_print' => 'required|in_list[enabled,disabled]',
        'antena_ip' => 'permit_empty|valid_ip',
        'kiosk_ip' => 'permit_empty|valid_ip',
        'cam_id_1' => 'permit_empty|max_length[50]',
        'cam_id_2' => 'permit_empty|max_length[50]',
        'cam_id_3' => 'permit_empty|max_length[50]',
        'cam_photo_ip_1' => 'permit_empty|valid_ip',
        'cam_photo_ip_2' => 'permit_empty|valid_ip',
        'in_bound' => 'required|in_list[yes,no]',
        'out_bound' => 'required|in_list[yes,no]',
        'last_logged_in_by' => 'permit_empty|max_length[100]',
        'last_logged_in_datetime' => 'permit_empty|valid_date',
        'last_changed_on_printer_paper' => 'permit_empty|valid_date',
        'status'    => 'required|in_list[active,inactive]',
        'scan_type' => 'permit_empty|in_list[rfid,qr_code]'
    ];

    protected $validationMessages = [
        'lane' => [
            'required' => 'Lane name is required'
        ],
        'location_id' => [
            'required' => 'Location is required',
            'is_natural_no_zero' => 'Please select a valid location'
        ],
        'slip_print' => [
            'required' => 'Slip print status is required',
            'in_list' => 'Slip print must be enabled or disabled'
        ],
        'in_bound' => [
            'required' => 'In bound status is required',
            'in_list' => 'In bound must be yes or no'
        ],
        'out_bound' => [
            'required' => 'Out bound status is required',
            'in_list' => 'Out bound must be yes or no'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be active or inactive'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get lanes with pagination, search, and sorting
     */
    public function getLanesWithPagination($search = '', $sortBy = '', $limit = 10, $offset = 0)
    {
        $builder = $this->db->table($this->table);
        $builder->select('lanes.*, locations.location_access');
        $builder->join('locations', 'locations.id = lanes.location_id', 'left');

        // Apply search
        if (!empty($search)) {
            $builder->groupStart()
                ->like('lanes.lane', $search)
                ->orLike('locations.location_access', $search)
                ->orLike('lanes.barrier_no', $search)
                ->orLike('lanes.weight_id', $search)
                ->orLike('lanes.antena_ip', $search)
                ->orLike('lanes.kiosk_ip', $search)
                ->groupEnd();
        }

        // Apply sorting
        switch ($sortBy) {
            case 'lane_asc':
                $builder->orderBy('lanes.lane', 'ASC');
                break;
            case 'lane_desc':
                $builder->orderBy('lanes.lane', 'DESC');
                break;
            case 'location_asc':
                $builder->orderBy('locations.location_access', 'ASC');
                break;
            case 'location_desc':
                $builder->orderBy('locations.location_access', 'DESC');
                break;
            case 'status':
                $builder->orderBy('lanes.status', 'DESC');
                break;
            default:
                $builder->orderBy('lanes.id', 'DESC');
        }

        $builder->limit($limit, $offset);
        return $builder->get()->getResultArray();
    }

    /**
     * Get total lanes count with search
     */
    public function getTotalLanes($search = '')
    {
        $builder = $this->db->table($this->table);
        $builder->select('lanes.*');
        $builder->join('locations', 'locations.id = lanes.location_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('lanes.lane', $search)
                ->orLike('locations.location_access', $search)
                ->orLike('lanes.barrier_no', $search)
                ->orLike('lanes.weight_id', $search)
                ->orLike('lanes.antena_ip', $search)
                ->orLike('lanes.kiosk_ip', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Get all active lanes
     */
    public function getAllActive()
    {
        return $this->where('status', 'active')
            ->orderBy('lane', 'ASC')
            ->findAll();
    }

    /**
     * All lanes with parent location labels for device assignment UI.
     */
    public function getAllWithLocationLabelsForDeviceAssignment(): array
    {
        return $this->builder()
            ->select('lanes.*, locations.branch, locations.location_access')
            ->join('locations', 'locations.id = lanes.location_id', 'left')
            ->orderBy('locations.branch', 'ASC')
            ->orderBy('locations.location_access', 'ASC')
            ->orderBy('lanes.id', 'ASC')
            ->get()
            ->getResultArray();
    }
}
