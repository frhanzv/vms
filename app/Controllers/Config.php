<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\CompanyModel;
use App\Models\SubCompanyModel;
use App\Models\CountryModel;
use App\Models\StateModel;
use App\Models\CityModel;
use App\Models\DepartmentModel;
use App\Models\DesignationModel;
use App\Models\LocationModel;

class Config extends BaseController
{
    protected $roleModel;
    protected $userModel;
    protected $companyModel;
    protected $subCompanyModel;
    protected $countryModel;
    protected $stateModel;
    protected $cityModel;
    protected $departmentModel;
    protected $designationModel;
    protected $locationModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->roleModel = new RoleModel();
        $this->userModel = new UserModel();
        $this->companyModel = new CompanyModel();
        $this->subCompanyModel = new SubCompanyModel();
        $this->countryModel = new CountryModel();
        $this->stateModel = new StateModel();
        $this->cityModel = new CityModel();
        $this->departmentModel = new DepartmentModel();
        $this->designationModel = new DesignationModel();
        $this->locationModel = new LocationModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'System Configuration - SafeG',
            'logs' => $this->getSystemLogs()
        ];

        return view('config/index', $data);
    }

    private function getSystemLogs($limit = 100, $level = null)
    {
        $logPath = WRITEPATH . 'logs/';
        $logs = [];
        
        // Get all log files sorted by date (newest first)
        $logFiles = glob($logPath . 'log-*.log');
        rsort($logFiles);
        
        $count = 0;
        foreach ($logFiles as $file) {
            if ($count >= $limit) break;
            
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $lines = array_reverse($lines); // Newest first
            
            foreach ($lines as $line) {
                if ($count >= $limit) break;
                
                // Parse log line: LEVEL - YYYY-MM-DD HH:MM:SS --> Message
                if (preg_match('/^(\w+)\s+-\s+(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})\s+-->\s+(.+)$/', $line, $matches)) {
                    $logLevel = $matches[1];
                    $timestamp = $matches[2];
                    $message = $matches[3];
                    
                    // Filter by level if specified
                    if ($level && strtoupper($level) !== strtoupper($logLevel)) {
                        continue;
                    }
                    
                    $logs[] = [
                        'level' => $logLevel,
                        'timestamp' => $timestamp,
                        'message' => $message,
                        'color' => $this->getLogLevelColor($logLevel)
                    ];
                    
                    $count++;
                }
            }
        }
        
        return $logs;
    }
    
    private function getLogLevelColor($level)
    {
        $colors = [
            'ERROR' => 'red',
            'CRITICAL' => 'red',
            'ALERT' => 'red',
            'EMERGENCY' => 'red',
            'WARNING' => 'yellow',
            'NOTICE' => 'blue',
            'INFO' => 'green',
            'DEBUG' => 'blue'
        ];
        
        return $colors[strtoupper($level)] ?? 'gray';
    }
    
    public function getLogs()
    {
        $level = $this->request->getGet('level');
        $limit = $this->request->getGet('limit') ?? 100;
        
        $logs = $this->getSystemLogs($limit, $level);
        
        return $this->response->setJSON([
            'success' => true,
            'logs' => $logs
        ]);
    }
    
    public function exportLogs()
    {
        $level = $this->request->getGet('level');
        $logPath = WRITEPATH . 'logs/';
        
        // Get all log files
        $logFiles = glob($logPath . 'log-*.log');
        rsort($logFiles);
        
        $content = "System Logs Export - " . date('Y-m-d H:i:s') . "\n";
        $content .= str_repeat('=', 80) . "\n\n";
        
        foreach ($logFiles as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if ($level) {
                    if (stripos($line, $level) !== false) {
                        $content .= $line . "\n";
                    }
                } else {
                    $content .= $line . "\n";
                }
            }
        }
        
        return $this->response
            ->setHeader('Content-Type', 'text/plain')
            ->setHeader('Content-Disposition', 'attachment; filename="system-logs-' . date('Y-m-d-His') . '.txt"')
            ->setBody($content);
    }

    // ============== ROLE MANAGEMENT METHODS ==============

    /**
     * Get roles with pagination and search
     */
    public function getRoles()
    {
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 10;
        $search = $this->request->getGet('search') ?? '';

        $offset = ($page - 1) * $perPage;
        $roles = $this->roleModel->getRolesWithUserCount($search, $perPage, $offset);
        $total = $this->roleModel->getTotalRoles($search);

        return $this->response->setJSON([
            'success' => true,
            'data' => $roles,
            'pagination' => [
                'current_page' => (int)$page,
                'per_page' => (int)$perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total)
            ]
        ]);
    }

    /**
     * Get single role
     */
    public function getRole($id)
    {
        $role = $this->roleModel->find($id);

        if (!$role) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role not found'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $role
        ]);
    }

    /**
     * Create new role
     */
    public function createRole()
    {
        $input = $this->request->getJSON(true);
        
        $rules = [
            'name' => 'required|min_length[3]|max_length[50]|is_unique[roles.name]',
            'description' => 'permit_empty|max_length[255]',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        $data = [
            'name' => $input['name'],
            'description' => $input['description'] ?? null,
            'status' => $input['status']
        ];

        try {
            $roleId = $this->roleModel->insert($data);

            if ($roleId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Role created successfully',
                    'data' => ['id' => $roleId]
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create role'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error creating role: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error creating role: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update role
     */
    public function updateRole($id)
    {
        $role = $this->roleModel->find($id);

        if (!$role) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role not found'
            ])->setStatusCode(404);
        }

        $input = $this->request->getJSON(true);
        
        $rules = [
            'name' => "required|min_length[3]|max_length[50]|is_unique[roles.name,id,{$id}]",
            'description' => 'permit_empty|max_length[255]',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        $data = [
            'name' => $input['name'],
            'description' => $input['description'] ?? null,
            'status' => $input['status']
        ];

        try {
            if ($this->roleModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Role updated successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update role'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error updating role: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating role: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete role
     */
    public function deleteRole($id)
    {
        $role = $this->roleModel->find($id);

        if (!$role) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role not found'
            ])->setStatusCode(404);
        }

        // Check if role is assigned to any user
        if ($this->roleModel->isRoleAssigned($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot delete role that is assigned to users'
            ])->setStatusCode(400);
        }

        if ($this->roleModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete role'
        ])->setStatusCode(500);
    }

    // ============== USER MANAGEMENT METHODS ==============

    /**
     * Get users with pagination, search and sorting
     */
    public function getUsers()
    {
        try {
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 10);
            $search = $this->request->getGet('search') ?? '';
            $sortBy = $this->request->getGet('sort_by') ?? '';

            $offset = ($page - 1) * $perPage;
            $users = $this->userModel->getUsersWithPagination($search, $sortBy, $perPage, $offset);
            $total = $this->userModel->getTotalUsers($search);

            return $this->response->setJSON([
                'success' => true,
                'data' => $users,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => ceil($total / $perPage),
                    'from' => $offset + 1,
                    'to' => min($offset + $perPage, $total)
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getUsers: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading users: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get single user
     */
    public function getUser($id)
    {
        $user = $this->userModel->select('id, username, email, full_name, staff_id, contact_no, role, is_active')
                                ->find($id);

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Create new user
     */
    public function createUser()
    {
        $input = $this->request->getJSON(true);
        
        $rules = [
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'full_name' => 'required|min_length[3]|max_length[255]',
            'staff_id' => 'permit_empty|max_length[50]',
            'contact_no' => 'permit_empty|max_length[20]',
            'role' => 'required',
            'is_active' => 'required|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        $data = [
            'username' => $input['username'],
            'email' => $input['email'],
            'password' => $input['password'],
            'full_name' => $input['full_name'],
            'staff_id' => $input['staff_id'] ?? null,
            'contact_no' => $input['contact_no'] ?? null,
            'role' => $input['role'],
            'is_active' => $input['is_active']
        ];

        try {
            $userId = $this->userModel->insert($data);

            if ($userId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User created successfully',
                    'data' => ['id' => $userId]
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create user'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error creating user: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error creating user: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update user
     */
    public function updateUser($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ])->setStatusCode(404);
        }

        // Get input from JSON body
        $input = $this->request->getJSON(true);
        
        $rules = [
            'username' => "required|min_length[3]|max_length[100]|is_unique[users.username,id,{$id}]",
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'full_name' => 'required|min_length[3]|max_length[255]',
            'staff_id' => 'permit_empty|max_length[50]',
            'contact_no' => 'permit_empty|max_length[20]',
            'role' => 'required',
            'is_active' => 'required|in_list[0,1]'
        ];

        // If password is provided and not empty, validate it
        if (!empty($input['password'])) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        $data = [
            'username' => $input['username'],
            'email' => $input['email'],
            'full_name' => $input['full_name'],
            'staff_id' => $input['staff_id'] ?? null,
            'contact_no' => $input['contact_no'] ?? null,
            'role' => $input['role'],
            'is_active' => $input['is_active']
        ];

        // Only update password if provided and not empty
        if (!empty($input['password'])) {
            $data['password'] = $input['password'];
        }

        try {
            if ($this->userModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User updated successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update user'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error updating user: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating user: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ])->setStatusCode(404);
        }

        if ($this->userModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete user'
        ])->setStatusCode(500);
    }

    /**
     * Get all roles for dropdown
     */
    public function getAllRoles()
    {
        $roles = $this->roleModel->where('status', 'active')->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $roles
        ]);
    }

    // ============== COMPANY MANAGEMENT METHODS ==============

    /**
     * Get companies with pagination and search
     */
    public function getCompanies()
    {
        try {
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 10);
            $search = $this->request->getGet('search') ?? '';
            $sortBy = $this->request->getGet('sort') ?? '';

            $offset = ($page - 1) * $perPage;
            $companies = $this->companyModel->getCompaniesWithPagination($search, $sortBy, $perPage, $offset);
            $total = $this->companyModel->getTotalCompanies($search);

            return $this->response->setJSON([
                'success' => true,
                'data' => $companies,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'total_pages' => ceil($total / $perPage)
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getCompanies: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to fetch companies: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get single company
     */
    public function getCompany($id)
    {
        try {
            $company = $this->companyModel->find($id);
            
            if (!$company) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Company not found'
                ])->setStatusCode(404);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $company
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getCompany: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to fetch company: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Create new company
     */
    public function createCompany()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if ($this->companyModel->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Company created successfully',
                    'data' => ['id' => $this->companyModel->getInsertID()]
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create company',
                'errors' => $this->companyModel->errors()
            ])->setStatusCode(400);
        } catch (\Exception $e) {
            log_message('error', 'Error in createCompany: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create company: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update company
     */
    public function updateCompany($id)
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$this->companyModel->find($id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Company not found'
                ])->setStatusCode(404);
            }

            if ($this->companyModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Company updated successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update company',
                'errors' => $this->companyModel->errors()
            ])->setStatusCode(400);
        } catch (\Exception $e) {
            log_message('error', 'Error in updateCompany: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update company: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete company
     */
    public function deleteCompany($id)
    {
        try {
            if (!$this->companyModel->find($id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Company not found'
                ])->setStatusCode(404);
            }

            if ($this->companyModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Company deleted successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete company'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error in deleteCompany: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete company: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // ============== SUB COMPANY MANAGEMENT METHODS ==============

    /**
     * Get sub companies with pagination and search
     */
    public function getSubCompanies()
    {
        try {
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 10);
            $search = $this->request->getGet('search') ?? '';
            $companyFilter = $this->request->getGet('company_id') ?? '';
            $sortBy = $this->request->getGet('sort') ?? '';

            $offset = ($page - 1) * $perPage;
            $subCompanies = $this->subCompanyModel->getSubCompaniesWithPagination($search, $companyFilter, $sortBy, $perPage, $offset);
            $total = $this->subCompanyModel->getTotalSubCompanies($search, $companyFilter);

            return $this->response->setJSON([
                'success' => true,
                'data' => $subCompanies,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'total_pages' => ceil($total / $perPage)
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getSubCompanies: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to fetch sub companies: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get single sub company
     */
    public function getSubCompany($id)
    {
        try {
            $subCompany = $this->subCompanyModel->find($id);
            
            if (!$subCompany) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Sub company not found'
                ])->setStatusCode(404);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $subCompany
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getSubCompany: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to fetch sub company: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Create new sub company
     */
    public function createSubCompany()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if ($this->subCompanyModel->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Sub company created successfully',
                    'data' => ['id' => $this->subCompanyModel->getInsertID()]
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create sub company',
                'errors' => $this->subCompanyModel->errors()
            ])->setStatusCode(400);
        } catch (\Exception $e) {
            log_message('error', 'Error in createSubCompany: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create sub company: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update sub company
     */
    public function updateSubCompany($id)
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (!$this->subCompanyModel->find($id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Sub company not found'
                ])->setStatusCode(404);
            }

            if ($this->subCompanyModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Sub company updated successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update sub company',
                'errors' => $this->subCompanyModel->errors()
            ])->setStatusCode(400);
        } catch (\Exception $e) {
            log_message('error', 'Error in updateSubCompany: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update sub company: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete sub company
     */
    public function deleteSubCompany($id)
    {
        try {
            if (!$this->subCompanyModel->find($id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Sub company not found'
                ])->setStatusCode(404);
            }

            if ($this->subCompanyModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Sub company deleted successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete sub company'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error in deleteSubCompany: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete sub company: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get all companies for dropdown
     */
    public function getAllCompanies()
    {
        $companies = $this->companyModel->where('status', 'active')->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $companies
        ]);
    }

    // ==================== COUNTRY MANAGEMENT ====================
    
    /**
     * Get countries with pagination and search
     */
    public function getCountries()
    {
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        $search = $this->request->getGet('search') ?? '';
        $sortBy = $this->request->getGet('sort_by') ?? '';

        $offset = ($page - 1) * $perPage;

        $countries = $this->countryModel->getCountriesWithPagination($search, $sortBy, $perPage, $offset);
        $total = $this->countryModel->getTotalCountries($search);

        return $this->response->setJSON([
            'success' => true,
            'data' => $countries,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ]);
    }

    /**
     * Get single country
     */
    public function getCountry($id)
    {
        $country = $this->countryModel->find($id);

        if (!$country) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Country not found'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $country
        ]);
    }

    /**
     * Create new country
     */
    public function createCountry()
    {
        try {
            $json = $this->request->getJSON(true);

            $validation = \Config\Services::validation();
            $validation->setRules([
                'name' => 'required|min_length[2]|max_length[100]',
                'code' => 'required|min_length[2]|max_length[10]|is_unique[countries.code]',
                'status' => 'required|in_list[Active,Inactive]'
            ]);

            if (!$validation->run($json)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ])->setStatusCode(400);
            }

            $data = [
                'name' => $json['name'],
                'code' => strtoupper($json['code']),
                'status' => $json['status']
            ];

            if ($this->countryModel->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Country created successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create country'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error in createCountry: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create country: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update country
     */
    public function updateCountry($id)
    {
        try {
            $country = $this->countryModel->find($id);
            if (!$country) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Country not found'
                ])->setStatusCode(404);
            }

            $json = $this->request->getJSON(true);

            $validation = \Config\Services::validation();
            $validation->setRules([
                'name' => 'required|min_length[2]|max_length[100]',
                'code' => "required|min_length[2]|max_length[10]|is_unique[countries.code,id,{$id}]",
                'status' => 'required|in_list[Active,Inactive]'
            ]);

            if (!$validation->run($json)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ])->setStatusCode(400);
            }

            $data = [
                'name' => $json['name'],
                'code' => strtoupper($json['code']),
                'status' => $json['status']
            ];

            if ($this->countryModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Country updated successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update country'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error in updateCountry: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update country: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete country
     */
    public function deleteCountry($id)
    {
        try {
            $country = $this->countryModel->find($id);
            if (!$country) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Country not found'
                ])->setStatusCode(404);
            }

            if ($this->countryModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Country deleted successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete country'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error in deleteCountry: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete country: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // ==================== STATE MANAGEMENT ====================
    
    /**
     * Get states with pagination and search
     */
    public function getStates()
    {
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        $search = $this->request->getGet('search') ?? '';
        $countryFilter = $this->request->getGet('country_filter') ?? '';
        $sortBy = $this->request->getGet('sort_by') ?? '';

        $offset = ($page - 1) * $perPage;

        $states = $this->stateModel->getStatesWithPagination($search, $countryFilter, $sortBy, $perPage, $offset);
        $total = $this->stateModel->getTotalStates($search, $countryFilter);

        $from = $total > 0 ? $offset + 1 : 0;
        $to = min($offset + $perPage, $total);

        return $this->response->setJSON([
            'success' => true,
            'states' => $states,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'totalPages' => (int)ceil($total / $perPage),
                'from' => $from,
                'to' => $to
            ]
        ]);
    }

    /**
     * Get single state
     */
    public function getState($id)
    {
        $state = $this->stateModel->find($id);

        if (!$state) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'State not found'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'state' => $state
        ]);
    }

    /**
     * Create new state
     */
    public function createState()
    {
        try {
            $json = $this->request->getJSON(true);

            $validation = \Config\Services::validation();
            $validation->setRules([
                'country_id' => 'required|is_natural_no_zero',
                'name' => 'required|min_length[2]|max_length[100]',
                'code' => 'required|min_length[2]|max_length[10]',
                'status' => 'required|in_list[active,inactive]'
            ]);

            if (!$validation->run($json)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ])->setStatusCode(400);
            }

            $data = [
                'country_id' => $json['country_id'],
                'name' => $json['name'],
                'code' => strtoupper($json['code']),
                'status' => $json['status']
            ];

            if ($this->stateModel->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'State created successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create state'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error in createState: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create state: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update state
     */
    public function updateState($id)
    {
        try {
            $state = $this->stateModel->find($id);
            if (!$state) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'State not found'
                ])->setStatusCode(404);
            }

            $json = $this->request->getJSON(true);

            $validation = \Config\Services::validation();
            $validation->setRules([
                'country_id' => 'required|is_natural_no_zero',
                'name' => 'required|min_length[2]|max_length[100]',
                'code' => 'required|min_length[2]|max_length[10]',
                'status' => 'required|in_list[active,inactive]'
            ]);

            if (!$validation->run($json)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ])->setStatusCode(400);
            }

            $data = [
                'country_id' => $json['country_id'],
                'name' => $json['name'],
                'code' => strtoupper($json['code']),
                'status' => $json['status']
            ];

            if ($this->stateModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'State updated successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update state'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error in updateState: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update state: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete state
     */
    public function deleteState($id)
    {
        try {
            $state = $this->stateModel->find($id);
            if (!$state) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'State not found'
                ])->setStatusCode(404);
            }

            if ($this->stateModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'State deleted successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete state'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error in deleteState: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete state: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get all countries for dropdown
     */
    public function getAllCountries()
    {
        $countries = $this->countryModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'countries' => $countries
        ]);
    }

    // =============== CITY MANAGEMENT METHODS ===============

    /**
     * Get cities with pagination
     */
    public function getCities()
    {
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        $search = $this->request->getGet('search') ?? '';
        $stateFilter = $this->request->getGet('state_filter') ?? '';
        $countryFilter = $this->request->getGet('country_filter') ?? '';
        $sortBy = $this->request->getGet('sort_by') ?? '';

        $offset = ($page - 1) * $perPage;

        $cities = $this->cityModel->getCitiesWithPagination($search, $stateFilter, $countryFilter, $sortBy, $perPage, $offset);
        $total = $this->cityModel->getTotalCities($search, $stateFilter, $countryFilter);

        $from = $total > 0 ? $offset + 1 : 0;
        $to = min($offset + $perPage, $total);

        return $this->response->setJSON([
            'success' => true,
            'cities' => $cities,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'totalPages' => (int)ceil($total / $perPage),
                'from' => $from,
                'to' => $to
            ]
        ]);
    }

    /**
     * Get single city
     */
    public function getCity($id)
    {
        $city = $this->cityModel
            ->select('cities.*, states.country_id')
            ->join('states', 'states.id = cities.state_id')
            ->find($id);

        if (!$city) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'City not found'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'city' => $city
        ]);
    }

    /**
     * Create new city
     */
    public function createCity()
    {
        try {
            $json = $this->request->getJSON(true);

            $validation = \Config\Services::validation();
            $validation->setRules([
                'state_id' => 'required|is_natural_no_zero',
                'name' => 'required|min_length[2]|max_length[100]',
                'code' => 'permit_empty|min_length[2]|max_length[10]',
                'status' => 'required|in_list[active,inactive]'
            ]);

            if (!$validation->run($json)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ])->setStatusCode(400);
            }

            $data = [
                'state_id' => $json['state_id'],
                'name' => $json['name'],
                'code' => !empty($json['code']) ? strtoupper($json['code']) : null,
                'status' => $json['status']
            ];

            if ($this->cityModel->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'City created successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create city'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error in createCity: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create city: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update city
     */
    public function updateCity($id)
    {
        try {
            $city = $this->cityModel->find($id);
            if (!$city) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'City not found'
                ])->setStatusCode(404);
            }

            $json = $this->request->getJSON(true);

            $validation = \Config\Services::validation();
            $validation->setRules([
                'state_id' => 'required|is_natural_no_zero',
                'name' => 'required|min_length[2]|max_length[100]',
                'code' => 'permit_empty|min_length[2]|max_length[10]',
                'status' => 'required|in_list[active,inactive]'
            ]);

            if (!$validation->run($json)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ])->setStatusCode(400);
            }

            $data = [
                'state_id' => $json['state_id'],
                'name' => $json['name'],
                'code' => !empty($json['code']) ? strtoupper($json['code']) : null,
                'status' => $json['status']
            ];

            if ($this->cityModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'City updated successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update city'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error in updateCity: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update city: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete city
     */
    public function deleteCity($id)
    {
        try {
            $city = $this->cityModel->find($id);
            if (!$city) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'City not found'
                ])->setStatusCode(404);
            }

            if ($this->cityModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'City deleted successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete city'
            ])->setStatusCode(500);
        } catch (\Exception $e) {
            log_message('error', 'Error in deleteCity: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete city: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get all states for dropdown (filtered by country if provided)
     */
    public function getAllStates()
    {
        $countryId = $this->request->getGet('country_id');
        
        $builder = $this->stateModel->where('status', 'active');
        
        if (!empty($countryId)) {
            $builder = $builder->where('country_id', $countryId);
        }
        
        $states = $builder->orderBy('name', 'ASC')->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'states' => $states
        ]);
    }

    // =============== Department Management ===============

    /**
     * Get departments list with pagination
     */
    public function getDepartments()
    {
        $page = $this->request->getGet('page') ?? 1;
        $search = $this->request->getGet('search') ?? '';
        $sortBy = $this->request->getGet('sort_by') ?? '';
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $departments = $this->departmentModel->getDepartmentsWithPagination($search, $sortBy, $perPage, $offset);
        $total = $this->departmentModel->getTotalDepartments($search);

        $pagination = [
            'currentPage' => (int)$page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];

        return $this->response->setJSON([
            'success' => true,
            'departments' => $departments,
            'pagination' => $pagination
        ]);
    }

    /**
     * Get single department
     */
    public function getDepartment($id)
    {
        $department = $this->departmentModel->find($id);

        if (!$department) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Department not found'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'department' => $department
        ]);
    }

    /**
     * Create new department
     */
    public function createDepartment()
    {
        $data = $this->request->getJSON(true);

        // Uppercase the code if provided
        if (!empty($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        if (!$this->departmentModel->insert($data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create department',
                'errors' => $this->departmentModel->errors()
            ])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Department created successfully',
            'department_id' => $this->departmentModel->getInsertID()
        ]);
    }

    /**
     * Update department
     */
    public function updateDepartment($id)
    {
        if (!$this->departmentModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Department not found'
            ])->setStatusCode(404);
        }

        $data = $this->request->getJSON(true);

        // Uppercase the code if provided
        if (!empty($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        if (!$this->departmentModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update department',
                'errors' => $this->departmentModel->errors()
            ])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Department updated successfully'
        ]);
    }

    /**
     * Delete department
     */
    public function deleteDepartment($id)
    {
        if (!$this->departmentModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Department not found'
            ])->setStatusCode(404);
        }

        if (!$this->departmentModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete department'
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Department deleted successfully'
        ]);
    }

    // =============== Designation Management ===============

    /**
     * Get designations list with pagination
     */
    public function getDesignations()
    {
        $page = $this->request->getGet('page') ?? 1;
        $search = $this->request->getGet('search') ?? '';
        $sortBy = $this->request->getGet('sort_by') ?? '';
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $designations = $this->designationModel->getDesignationsWithPagination($search, $sortBy, $perPage, $offset);
        $total = $this->designationModel->getTotalDesignations($search);

        $pagination = [
            'currentPage' => (int)$page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];

        return $this->response->setJSON([
            'success' => true,
            'designations' => $designations,
            'pagination' => $pagination
        ]);
    }

    /**
     * Get single designation
     */
    public function getDesignation($id)
    {
        $designation = $this->designationModel->find($id);

        if (!$designation) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Designation not found'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'designation' => $designation
        ]);
    }

    /**
     * Create new designation
     */
    public function createDesignation()
    {
        $data = $this->request->getJSON(true);

        // Uppercase the code if provided
        if (!empty($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        if (!$this->designationModel->insert($data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create designation',
                'errors' => $this->designationModel->errors()
            ])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Designation created successfully',
            'designation_id' => $this->designationModel->getInsertID()
        ]);
    }

    /**
     * Update designation
     */
    public function updateDesignation($id)
    {
        if (!$this->designationModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Designation not found'
            ])->setStatusCode(404);
        }

        $data = $this->request->getJSON(true);

        // Uppercase the code if provided
        if (!empty($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        if (!$this->designationModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update designation',
                'errors' => $this->designationModel->errors()
            ])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Designation updated successfully'
        ]);
    }

    /**
     * Delete designation
     */
    public function deleteDesignation($id)
    {
        if (!$this->designationModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Designation not found'
            ])->setStatusCode(404);
        }

        if (!$this->designationModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete designation'
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Designation deleted successfully'
        ]);
    }

    // ==================== LOCATION ACCESS MANAGEMENT ====================
    
    /**
     * Get locations with pagination and search
     */
    public function getLocations()
    {
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        $search = $this->request->getGet('search') ?? '';
        $sortBy = $this->request->getGet('sort_by') ?? '';

        $offset = ($page - 1) * $perPage;

        $locations = $this->locationModel->getLocationsWithPagination($search, $sortBy, $perPage, $offset);
        $total = $this->locationModel->getTotalLocations($search);
        $totalPages = ceil($total / $perPage);

        return $this->response->setJSON([
            'success' => true,
            'locations' => $locations,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'totalPages' => $totalPages,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total)
            ]
        ]);
    }

    /**
     * Get single location
     */
    public function getLocation($id)
    {
        $location = $this->locationModel->find($id);
        
        if (!$location) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Location not found'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'location' => $location
        ]);
    }

    /**
     * Create location
     */
    public function createLocation()
    {
        $data = $this->request->getJSON(true);

        // Hash password if provided
        if (!empty($data['adam_password'])) {
            $data['adam_password'] = password_hash($data['adam_password'], PASSWORD_DEFAULT);
        }

        if (!$this->locationModel->insert($data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create location',
                'errors' => $this->locationModel->errors()
            ])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Location created successfully',
            'location_id' => $this->locationModel->getInsertID()
        ]);
    }

    /**
     * Update location
     */
    public function updateLocation($id)
    {
        if (!$this->locationModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Location not found'
            ])->setStatusCode(404);
        }

        $data = $this->request->getJSON(true);

        // Hash password if provided and not empty
        if (!empty($data['adam_password'])) {
            $data['adam_password'] = password_hash($data['adam_password'], PASSWORD_DEFAULT);
        } else {
            // Don't update password if empty
            unset($data['adam_password']);
        }

        if (!$this->locationModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update location',
                'errors' => $this->locationModel->errors()
            ])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Location updated successfully'
        ]);
    }

    /**
     * Delete location
     */
    public function deleteLocation($id)
    {
        if (!$this->locationModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Location not found'
            ])->setStatusCode(404);
        }

        if (!$this->locationModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete location'
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Location deleted successfully'
        ]);
    }
}


