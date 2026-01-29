<?php

namespace App\Models;

use CodeIgniter\Model;

class VisitorCardLogModel extends Model
{
    protected $table = 'visitor_card_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'visitor_card_id',
        'invitation_id',
        'action',
        'lane_id',
        'scanned_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = false;

    // Validation
    protected $validationRules = [
        'visitor_card_id' => 'required|integer',
        'action' => 'required|in_list[checkin,checkout]',
        'scanned_at' => 'required'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get card logs with related data
     */
    public function getLogsWithDetails($filters = [])
    {
        $builder = $this->builder();
        
        $builder->select('visitor_card_logs.*, 
                          visitor_cards.card_id as card_epc,
                          invitations.host_name,
                          invitations.visitor_name,
                          lanes.name as lane_name,
                          lanes.lane_type')
                ->join('visitor_cards', 'visitor_cards.id = visitor_card_logs.visitor_card_id', 'left')
                ->join('invitations', 'invitations.id = visitor_card_logs.invitation_id', 'left')
                ->join('lanes', 'lanes.id = visitor_card_logs.lane_id', 'left');

        // Apply filters
        if (!empty($filters['start_date'])) {
            $builder->where('DATE(visitor_card_logs.scanned_at) >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('DATE(visitor_card_logs.scanned_at) <=', $filters['end_date']);
        }

        if (!empty($filters['card_id'])) {
            $builder->where('visitor_card_logs.visitor_card_id', $filters['card_id']);
        }

        if (!empty($filters['action'])) {
            $builder->where('visitor_card_logs.action', $filters['action']);
        }

        $builder->orderBy('visitor_card_logs.scanned_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get today's card activity
     */
    public function getTodayActivity()
    {
        return $this->where('DATE(scanned_at)', date('Y-m-d'))
                    ->orderBy('scanned_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get card usage statistics
     */
    public function getCardStatistics($startDate = null, $endDate = null)
    {
        $builder = $this->builder();

        if ($startDate) {
            $builder->where('DATE(scanned_at) >=', $startDate);
        }

        if ($endDate) {
            $builder->where('DATE(scanned_at) <=', $endDate);
        }

        $builder->select('action, COUNT(*) as count')
                ->groupBy('action');

        return $builder->get()->getResultArray();
    }
}
