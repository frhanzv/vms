<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Traits\OptimisticLockTrait;

class VideoModel extends Model
{
    use OptimisticLockTrait;

    protected $table = 'videos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['name', 'file_path', 'status', 'version'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|max_length[255]|is_unique[videos.name]',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Video name is required',
            'max_length' => 'Video name cannot exceed 255 characters',
            'is_unique' => 'This video name already exists'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be active or inactive'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get videos with pagination, search, and sorting
     */
    public function getVideosWithPagination($page = 1, $search = '', $sortBy = '')
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $builder = $this->builder();

        // Apply search
        if (!empty($search)) {
            $builder->like('name', $search);
        }

        // Apply sorting
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
                $builder->orderBy('id', 'DESC');
        }

        $builder->limit($limit, $offset);
        return $builder->get()->getResultArray();
    }

    /**
     * Get total videos count with search
     */
    public function getTotalVideos($search = '')
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->like('name', $search);
        }

        return $builder->countAllResults();
    }

    /**
     * Get all active videos
     */
    public function getAllActive()
    {
        return $this->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get the active video for security briefing
     */
    public function getActiveVideo()
    {
        return $this->where('status', 'active')
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * Delete video and its file
     */
    public function deleteWithFile($id)
    {
        $video = $this->find($id);
        
        if ($video && !empty($video['file_path'])) {
            $filePath = FCPATH . $video['file_path'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        return $this->delete($id);
    }
}
