<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class VisitorCardModel extends Model
{
    use OptimisticLockTrait;

    protected $table = 'visitor_cards';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'card_id',
        'serial_no',
        'status',
        'version'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'card_id' => 'required|max_length[50]|is_unique[visitor_cards.card_id,id,{id}]',
        'serial_no' => 'required|max_length[100]',
        'status' => 'required|in_list[active,in_use,lost,inactive]'
    ];

    protected $validationMessages = [
        'card_id' => [
            'required' => 'Card EPC is required',
            'max_length' => 'Card EPC cannot exceed 50 characters',
            'is_unique' => 'This card EPC already exists'
        ],
        'serial_no' => [
            'required' => 'Serial No is required',
            'max_length' => 'Serial No cannot exceed 100 characters',
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be active, in_use, lost, or inactive'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Override update to dynamically set the is_unique validation rule for card_id.
     */
    public function update($id = null, $data = null): bool
    {
        if ($id !== null) {
            $this->validationRules['card_id'] = "required|max_length[50]|is_unique[visitor_cards.card_id,id,{$id}]";
        }
        return parent::update($id, $data);
    }

    /**
     * Get visitor cards with pagination, search, and sorting
     */
    public function getVisitorCardsWithPagination($page = 1, $search = '', $sortBy = '')
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $builder = $this->builder();

        // Apply search
        if (!empty($search)) {
            $builder->groupStart()
                ->like('card_id', $search)
                ->orLike('serial_no', $search)
                ->groupEnd();
        }

        // Apply sorting
        switch ($sortBy) {
            case 'card_asc':
                $builder->orderBy('card_id', 'ASC');
                break;
            case 'card_desc':
                $builder->orderBy('card_id', 'DESC');
                break;
            case 'serial_asc':
                $builder->orderBy('serial_no', 'ASC');
                break;
            case 'serial_desc':
                $builder->orderBy('serial_no', 'DESC');
                break;
            case 'status':
                $builder->orderBy('status', 'DESC');
                break;
            default:
                $builder->orderBy('id', 'DESC');
        }

        $builder->limit($limit, $offset);
        return $builder->get()->getResultArray();
    }

    /**
     * Get total visitor cards count with search
     */
    public function getTotalVisitorCards($search = '')
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->groupStart()
                ->like('card_id', $search)
                ->orLike('serial_no', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Get all active visitor cards
     */
    public function getAllActive()
    {
        return $this->where('status', 'active')
            ->orderBy('card_id', 'ASC')
            ->findAll();
    }
}
