<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;

class AppConfig extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Get all app configs with optional search & pagination
     */
    public function getAll()
    {
        $page    = (int)($this->request->getGet('page') ?? 1);
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        $search  = trim($this->request->getGet('search') ?? '');
        $offset  = ($page - 1) * $perPage;

        $builder = $this->db->table('app_configs')->orderBy('id', 'ASC');

        if ($search !== '') {
            $builder->like('description', $search);
        }

        $total = (clone $builder)->countAllResults(false);
        $data  = $builder->limit($perPage, $offset)->get()->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'data'    => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'total_pages'  => (int) ceil($total / $perPage),
                'from'         => $total > 0 ? $offset + 1 : 0,
                'to'           => min($offset + $perPage, $total),
            ],
        ]);
    }

    /**
     * Get a single app config by ID
     */
    public function get($id)
    {
        $row = $this->db->table('app_configs')->where('id', $id)->get()->getRowArray();

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Record not found.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $row,
        ]);
    }

    /**
     * Update an app config by ID
     */
    public function update($id)
    {
        $row = $this->db->table('app_configs')->where('id', $id)->get()->getRowArray();

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Record not found.',
            ]);
        }

        $input = $this->request->getJSON(true);

        if (empty($input['description'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Description is required.',
            ]);
        }

        $this->db->table('app_configs')->where('id', $id)->update([
            'description'            => trim($input['description']),
            'active'                 => $input['active'] ?? 'YES',
            'day_to_send_first_alert'  => $input['day_to_send_first_alert'] !== '' ? (int)$input['day_to_send_first_alert'] : null,
            'day_to_send_second_alert' => $input['day_to_send_second_alert'] !== '' ? (int)$input['day_to_send_second_alert'] : null,
            'day_to_block'           => $input['day_to_block'] !== '' ? (int)$input['day_to_block'] : null,
            'updated_at'             => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'App config updated successfully.',
        ]);
    }
}