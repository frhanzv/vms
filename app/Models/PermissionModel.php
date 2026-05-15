<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table            = 'permissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['key', 'category', 'label'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get permissions grouped by category
     */
    public function getGroupedPermissions(): array
    {
        $permissions = $this->orderBy('category', 'ASC')->findAll();
        $grouped = [];
        foreach ($permissions as $p) {
            $grouped[$p['category']][] = $p;
        }
        return $grouped;
    }
}
