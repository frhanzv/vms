<?php

namespace App\Models;

use CodeIgniter\Model;

class BlacklistReasonModel extends Model
{
    protected $table = 'blacklistreason';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'reason',
        'type',
        'status'
    ];
}
