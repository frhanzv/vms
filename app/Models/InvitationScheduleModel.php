<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\SyncUidTrait;

class InvitationScheduleModel extends Model
{
    use SyncUidTrait;

    protected $table = 'invitation_schedules';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['sync_uid', 'invitation_id', 'date_from', 'date_to'];

    protected $beforeInsert = ['assignSyncUid'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'invitation_id' => 'required|integer',
        'date_from' => 'required|valid_date',
        'date_to' => 'required|valid_date'
    ];
}