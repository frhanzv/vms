<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class ClientModel extends Model
{
    use OptimisticLockTrait;

    protected $table            = 'clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'code', 'registration_no', 'address', 'contact_no', 'email', 'status', 'version'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'   => 'required|min_length[2]|max_length[255]',
        'code'   => 'permit_empty|max_length[50]',
        'status' => 'required|in_list[active,inactive]',
    ];

    public function getClientsWithPagination(string $search = '', string $sortBy = '', int $limit = 10, int $offset = 0): array
    {
        $builder = $this->select('id, name, code, registration_no, address, contact_no, email, status, created_at');

        if ($search !== '') {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('code', $search)
                ->orLike('registration_no', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        switch ($sortBy) {
            case 'name_asc':
                $builder->orderBy('name', 'ASC');
                break;
            case 'name_desc':
                $builder->orderBy('name', 'DESC');
                break;
            case 'status':
                $builder->orderBy('status', 'DESC');
                break;
            default:
                $builder->orderBy('created_at', 'DESC');
        }

        return $builder->findAll($limit, $offset);
    }

    public function getTotalClients(string $search = ''): int
    {
        if ($search !== '') {
            $this->groupStart()
                ->like('name', $search)
                ->orLike('code', $search)
                ->orLike('registration_no', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        return $this->countAllResults();
    }
}
