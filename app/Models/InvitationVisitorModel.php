<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;
use App\Traits\SyncUidTrait;

class InvitationVisitorModel extends Model
{
    use OptimisticLockTrait, SyncUidTrait;

    protected $table = 'invitation_visitors';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'sync_uid',
        'invitation_id', 'full_name', 'ic_passport', 'contact',
        'company', 'vehicle_registration', 'visitor_card_id',
        'check_in_time', 'check_out_time', 'version',
    ];

    protected $beforeInsert = ['assignSyncUid'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'invitation_id' => 'required|integer',
        'full_name' => 'required|max_length[255]',
        'ic_passport' => 'required|max_length[50]',
        'contact' => 'required|max_length[20]'
    ];
}