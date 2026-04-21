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
use App\Models\LaneModel;
use App\Models\RejectReasonModel;
use App\Models\VisitorCardModel;
use App\Models\VideoModel;
use App\Models\VisitReasonModel;
use App\Models\VisitorTypeModel;
use App\Models\SettingModel;
use App\Models\DeviceAssignmentModel;
use App\Models\EmailTemplateFormFieldModel;
use App\Models\RegTypeModel;
use App\Models\BusinessTypeModel;
use App\Models\BlacklistReasonModel;


use App\Models\EmailTemplateModel;
use App\Models\PathwayModel;
use App\Models\SecurityAlertPriorityModel;
use App\Libraries\EmailTemplateService;

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
    protected $laneModel;
    protected $rejectReasonModel;
    protected $visitorCardModel;
    protected $videoModel;
    protected $visitReasonModel;
    protected $visitorTypeModel;
    protected $settingModel;
    protected $deviceAssignmentModel;
    protected $emailTemplateFormFieldModel;
    protected $regTypeModel;
    protected $bizTypeModel;
    protected $blacklistReasonModel;


    protected $emailTemplateModel;
    protected $pathwayModel;
    protected $alertPriorityModel;

    /**
     * Version-checked update for config entities.
     * Reads `version` from the JSON input and uses optimistic locking.
     * Falls back to a regular update if no version is provided (backward compat).
     *
     * @param  \CodeIgniter\Model $model  The model instance
     * @param  int|string         $id     Primary key
     * @param  array              $data   Fields to update (version is handled automatically)
     * @param  array              $input  Raw JSON input (to read 'version' from)
     * @param  string             $entityType  Human-readable name for error messages
     * @return \CodeIgniter\HTTP\Response|null  Returns error response on conflict, null on success
     */
    protected function versionedUpdate($model, $id, array $data, array $input, string $entityType)
    {
        $clientVersion = isset($input['version']) ? (int) $input['version'] : null;

        if ($clientVersion !== null && method_exists($model, 'updateWithLock')) {
            try {
                $model->updateWithLock($id, $data, $clientVersion, $entityType);
                return null;
            } catch (\App\Exceptions\ConcurrencyException $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ])->setStatusCode(409);
            }
        }

        if ($model->update($id, $data)) {
            return null;
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => "Failed to update {$entityType}",
            'errors' => $model->errors()
        ])->setStatusCode(500);
    }

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
        $this->laneModel = new LaneModel();
        $this->rejectReasonModel = new RejectReasonModel();
        $this->visitorCardModel = new VisitorCardModel();
        $this->videoModel = new VideoModel();
        $this->visitReasonModel = new VisitReasonModel();
        $this->visitorTypeModel = new VisitorTypeModel();
        $this->settingModel = new SettingModel();
        $this->deviceAssignmentModel = new DeviceAssignmentModel();
        $this->emailTemplateFormFieldModel = new EmailTemplateFormFieldModel();
        $this->regTypeModel = new RegTypeModel();
        $this->bizTypeModel = new BusinessTypeModel();
        $this->blacklistReasonModel = new BlacklistReasonModel();



        $this->emailTemplateModel = new EmailTemplateModel();
        $this->pathwayModel = new PathwayModel();
        $this->alertPriorityModel = new SecurityAlertPriorityModel();
    }

    public function index()
    {
        // =========================
        // REG TYPE SECTION
        // =========================
        $regSearch  = $this->request->getGet('regtype_search') ?? '';
        $regPage    = (int)($this->request->getGet('regtype_page') ?? 1);
        $regPerPage = 10;
        $regOffset  = ($regPage - 1) * $regPerPage;

        $regBuilder = $this->regTypeModel->orderBy('name', 'ASC');

        if ($regSearch) {
            $regBuilder->like('name', $regSearch);
        }

        $regTotal    = (clone $regBuilder)->countAllResults();
        $regTypes    = $regBuilder->findAll($regPerPage, $regOffset);


        // =========================
        // BUSINESS TYPE SECTION
        // =========================
        $bizSearch  = $this->request->getGet('biztype_search') ?? '';
        $bizPage    = (int)($this->request->getGet('biztype_page') ?? 1);
        $bizPerPage = 10;
        $bizOffset  = ($bizPage - 1) * $bizPerPage;

        $bizBuilder = $this->bizTypeModel->orderBy('business_type', 'ASC');

        if ($bizSearch) {
            $bizBuilder->like('business_type', $bizSearch);
        }

        $bizTotal = (clone $bizBuilder)->countAllResults();
        $bizTypes = $bizBuilder->findAll($bizPerPage, $bizOffset);

        // =========================
        // BLACKLIST REASON SECTION
        // =========================
        $blacklistSearch  = $this->request->getGet('blacklist_search') ?? '';
        $blacklistBuilder = $this->blacklistReasonModel->orderBy('reason', 'ASC');

        if ($blacklistSearch) {
            $blacklistBuilder->like('reason', $blacklistSearch);
        }

        $blacklistReasons = $blacklistBuilder->findAll();


        // =========================
        // PASS DATA TO VIEW
        // =========================
        return view('config/index', [
            'pageTitle' => 'System Configuration - SafeG',
            'logs' => $this->getSystemLogs(),

            // Reg Type
            'reg_types'        => $regTypes,
            'regtype_total'    => $regTotal,
            'regtype_page'     => $regPage,
            'regtype_per_page' => $regPerPage,
            'regtype_search'   => $regSearch,

            // Business Type
            'biz_types'        => $bizTypes,
            'biztype_total'    => $bizTotal,
            'biztype_page'     => $bizPage,
            'biztype_per_page' => $bizPerPage,
            'biztype_search'   => $bizSearch,

            // Blacklist Reason
            'blacklist_reasons' => $blacklistReasons,
            'blacklist_search'  => $blacklistSearch,
        ]);
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
            if ($count >= $limit)
                break;

            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $lines = array_reverse($lines); // Newest first

            foreach ($lines as $line) {
                if ($count >= $limit)
                    break;

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
                'current_page' => (int) $page,
                'per_page' => (int) $perPage,
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
            $error = $this->versionedUpdate($this->roleModel, $id, $data, $input, 'role');
            if ($error) {
                return $error;
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Role updated successfully'
            ]);
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
            $page = (int) ($this->request->getGet('page') ?? 1);
            $perPage = (int) ($this->request->getGet('per_page') ?? 10);
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
            $error = $this->versionedUpdate($this->userModel, $id, $data, $input, 'user');
            if ($error) {
                return $error;
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'User updated successfully'
            ]);
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
            $page = (int) ($this->request->getGet('page') ?? 1);
            $perPage = (int) ($this->request->getGet('per_page') ?? 10);
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
            $input = $this->request->getJSON(true);

            if (!$this->companyModel->find($id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Company not found'
                ])->setStatusCode(404);
            }

            $data = $input;
            unset($data['version'], $data['id']);

            $error = $this->versionedUpdate($this->companyModel, $id, $data, $input, 'company');
            if ($error) {
                return $error;
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Company updated successfully'
            ]);
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
            $page = (int) ($this->request->getGet('page') ?? 1);
            $perPage = (int) ($this->request->getGet('per_page') ?? 10);
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
            $input = $this->request->getJSON(true);
            if (!is_array($input)) {
                $input = [];
            }

            if (!$this->subCompanyModel->find($id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Sub company not found'
                ])->setStatusCode(404);
            }

            $data = $input;
            unset($data['version'], $data['id']);

            $error = $this->versionedUpdate($this->subCompanyModel, $id, $data, $input, 'sub company');
            if ($error) {
                return $error;
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sub company updated successfully'
            ]);
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
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
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

            $input = $this->request->getJSON(true);
            if (!is_array($input)) {
                $input = [];
            }

            $validation = \Config\Services::validation();
            $validation->setRules([
                'name' => 'required|min_length[2]|max_length[100]',
                'code' => "required|min_length[2]|max_length[10]|is_unique[countries.code,id,{$id}]",
                'status' => 'required|in_list[Active,Inactive]'
            ]);

            if (!$validation->run($input)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ])->setStatusCode(400);
            }

            $data = [
                'name' => $input['name'],
                'code' => strtoupper($input['code']),
                'status' => $input['status']
            ];

            $error = $this->versionedUpdate($this->countryModel, $id, $data, $input, 'country');
            if ($error) {
                return $error;
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Country updated successfully'
            ]);
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
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
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
                'totalPages' => (int) ceil($total / $perPage),
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

            $input = $this->request->getJSON(true);
            if (!is_array($input)) {
                $input = [];
            }

            $validation = \Config\Services::validation();
            $validation->setRules([
                'country_id' => 'required|is_natural_no_zero',
                'name' => 'required|min_length[2]|max_length[100]',
                'code' => 'required|min_length[2]|max_length[10]',
                'status' => 'required|in_list[active,inactive]'
            ]);

            if (!$validation->run($input)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ])->setStatusCode(400);
            }

            $data = [
                'country_id' => $input['country_id'],
                'name' => $input['name'],
                'code' => strtoupper($input['code']),
                'status' => $input['status']
            ];

            $error = $this->versionedUpdate($this->stateModel, $id, $data, $input, 'state');
            if ($error) {
                return $error;
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'State updated successfully'
            ]);
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
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
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
                'totalPages' => (int) ceil($total / $perPage),
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

            $input = $this->request->getJSON(true);
            if (!is_array($input)) {
                $input = [];
            }

            $validation = \Config\Services::validation();
            $validation->setRules([
                'state_id' => 'required|is_natural_no_zero',
                'name' => 'required|min_length[2]|max_length[100]',
                'code' => 'permit_empty|min_length[2]|max_length[10]',
                'status' => 'required|in_list[active,inactive]'
            ]);

            if (!$validation->run($input)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ])->setStatusCode(400);
            }

            $data = [
                'state_id' => $input['state_id'],
                'name' => $input['name'],
                'code' => !empty($input['code']) ? strtoupper($input['code']) : null,
                'status' => $input['status']
            ];

            $error = $this->versionedUpdate($this->cityModel, $id, $data, $input, 'city');
            if ($error) {
                return $error;
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'City updated successfully'
            ]);
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
            'currentPage' => (int) $page,
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

        $input = $this->request->getJSON(true);
        if (!is_array($input)) {
            $input = [];
        }
        $data = $input;
        unset($data['version'], $data['id']);

        // Uppercase the code if provided
        if (!empty($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $error = $this->versionedUpdate($this->departmentModel, $id, $data, $input, 'department');
        if ($error) {
            return $error;
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
            'currentPage' => (int) $page,
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

        $input = $this->request->getJSON(true);
        if (!is_array($input)) {
            $input = [];
        }
        $data = $input;
        unset($data['version'], $data['id']);

        // Uppercase the code if provided
        if (!empty($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $error = $this->versionedUpdate($this->designationModel, $id, $data, $input, 'designation');
        if ($error) {
            return $error;
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
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
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

        $input = $this->request->getJSON(true);
        if (!is_array($input)) {
            $input = [];
        }
        $data = $input;
        unset($data['version'], $data['id']);

        // Hash password if provided and not empty
        if (!empty($data['adam_password'])) {
            $data['adam_password'] = password_hash($data['adam_password'], PASSWORD_DEFAULT);
        } else {
            // Don't update password if empty
            unset($data['adam_password']);
        }

        $error = $this->versionedUpdate($this->locationModel, $id, $data, $input, 'location');
        if ($error) {
            return $error;
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

    // ============================================
    // Lane Management
    // ============================================

    /**
     * Get lanes with pagination, search and sorting
     */
    public function getLanes()
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = (int) ($this->request->getGet('limit') ?? 10);
        $search = $this->request->getGet('search') ?? '';
        $sortBy = $this->request->getGet('sortBy') ?? '';

        $offset = ($page - 1) * $limit;

        $lanes = $this->laneModel->getLanesWithPagination($search, $sortBy, $limit, $offset);
        $total = $this->laneModel->getTotalLanes($search);

        return $this->response->setJSON([
            'success' => true,
            'data' => $lanes,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'totalPages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * Get single lane
     */
    public function getLane($id)
    {
        $lane = $this->laneModel->find($id);

        if (!$lane) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lane not found'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $lane
        ]);
    }

    /**
     * Create new lane
     */
    public function createLane()
    {
        $data = $this->request->getJSON(true);

        if (!$this->laneModel->insert($data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create lane',
                'errors' => $this->laneModel->errors()
            ])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Lane created successfully',
            'lane_id' => $this->laneModel->getInsertID()
        ]);
    }

    /**
     * Update lane
     */
    public function updateLane($id)
    {
        if (!$this->laneModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lane not found'
            ])->setStatusCode(404);
        }

        $input = $this->request->getJSON(true);
        if (!is_array($input)) {
            $input = [];
        }
        $data = $input;
        unset($data['version'], $data['id']);

        $error = $this->versionedUpdate($this->laneModel, $id, $data, $input, 'lane');
        if ($error) {
            return $error;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Lane updated successfully'
        ]);
    }

    /**
     * Delete lane
     */
    public function deleteLane($id)
    {
        if (!$this->laneModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lane not found'
            ])->setStatusCode(404);
        }

        if (!$this->laneModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete lane'
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Lane deleted successfully'
        ]);
    }

    // ============================================
    // Reject Reason Management
    // ============================================

    /**
     * Get reject reasons with pagination, search and sorting
     */
    public function getRejectReasons()
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = (int) ($this->request->getGet('limit') ?? 10);
        $search = $this->request->getGet('search') ?? '';
        $sortBy = $this->request->getGet('sortBy') ?? '';

        $offset = ($page - 1) * $limit;

        $reasons = $this->rejectReasonModel->getRejectReasonsWithPagination($search, $sortBy, $limit, $offset);
        $total = $this->rejectReasonModel->getTotalRejectReasons($search);

        return $this->response->setJSON([
            'success' => true,
            'data' => $reasons,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'totalPages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * Get single reject reason
     */
    public function getRejectReason($id)
    {
        $reason = $this->rejectReasonModel->find($id);

        if (!$reason) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Reject reason not found'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $reason
        ]);
    }

    /**
     * Create new reject reason
     */
    public function createRejectReason()
    {
        $data = $this->request->getJSON(true);

        if (!$this->rejectReasonModel->insert($data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create reject reason',
                'errors' => $this->rejectReasonModel->errors()
            ])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Reject reason created successfully',
            'reason_id' => $this->rejectReasonModel->getInsertID()
        ]);
    }

    /**
     * Update reject reason
     */
    public function updateRejectReason($id)
    {
        if (!$this->rejectReasonModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Reject reason not found'
            ])->setStatusCode(404);
        }

        $input = $this->request->getJSON(true);
        if (!is_array($input)) {
            $input = [];
        }
        $data = $input;
        unset($data['version'], $data['id']);

        $error = $this->versionedUpdate($this->rejectReasonModel, $id, $data, $input, 'reject reason');
        if ($error) {
            return $error;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Reject reason updated successfully'
        ]);
    }

    /**
     * Delete reject reason
     */
    public function deleteRejectReason($id)
    {
        if (!$this->rejectReasonModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Reject reason not found'
            ])->setStatusCode(404);
        }

        if (!$this->rejectReasonModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete reject reason'
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Reject reason deleted successfully'
        ]);
    }

    // ========================
    // Visitor Card Methods
    // ========================

    public function getVisitorCards()
    {
        $page = $this->request->getGet('page') ?? 1;
        $search = $this->request->getGet('search') ?? '';
        $sort = $this->request->getGet('sort') ?? 'card_asc';

        $visitorCards = $this->visitorCardModel->getVisitorCardsWithPagination($page, $search, $sort);
        $total = $this->visitorCardModel->getTotalVisitorCards($search);

        return $this->response->setJSON([
            'success' => true,
            'data' => $visitorCards,
            'pagination' => [
                'current_page' => $page,
                'per_page' => 10,
                'total' => $total,
                'total_pages' => ceil($total / 10)
            ]
        ]);
    }

    public function getVisitorCard($id)
    {
        $visitorCard = $this->visitorCardModel->find($id);

        if (!$visitorCard) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Visitor card not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $visitorCard
        ]);
    }

    public function createVisitorCard()
    {
        $data = [
            'card_id' => $this->request->getPost('card_id'),
            'status' => $this->request->getPost('status')
        ];

        if (!$this->visitorCardModel->insert($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Failed to create visitor card',
                'errors' => $this->visitorCardModel->errors()
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Visitor card created successfully'
        ]);
    }

    public function updateVisitorCard($id)
    {
        $visitorCard = $this->visitorCardModel->find($id);

        if (!$visitorCard) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Visitor card not found'
            ]);
        }

        $input = $this->request->getJSON(true);
        if (!is_array($input)) {
            $input = [];
        }
        $data = [
            'card_id' => $input['card_id'] ?? null,
            'status' => $input['status'] ?? null,
        ];

        $error = $this->versionedUpdate($this->visitorCardModel, $id, $data, $input, 'visitor card');
        if ($error) {
            return $error;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Visitor card updated successfully'
        ]);
    }

    public function deleteVisitorCard($id)
    {
        $visitorCard = $this->visitorCardModel->find($id);

        if (!$visitorCard) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Visitor card not found'
            ]);
        }

        if (!$this->visitorCardModel->delete($id)) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to delete visitor card'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Visitor card deleted successfully'
        ]);
    }

    // ========================
    // Video Management Methods
    // ========================

    public function getVideos()
    {
        $page = $this->request->getGet('page') ?? 1;
        $search = $this->request->getGet('search') ?? '';
        $sort = $this->request->getGet('sort') ?? 'name_asc';

        $videos = $this->videoModel->getVideosWithPagination($page, $search, $sort);
        $total = $this->videoModel->getTotalVideos($search);

        return $this->response->setJSON([
            'success' => true,
            'data' => $videos,
            'pagination' => [
                'current_page' => $page,
                'per_page' => 10,
                'total' => $total,
                'total_pages' => ceil($total / 10)
            ]
        ]);
    }

    public function getVideo($id)
    {
        $video = $this->videoModel->find($id);

        if (!$video) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Video not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $video
        ]);
    }

    public function createVideo()
    {
        $validation = \Config\Services::validation();

        // Validate video file
        $validationRules = [
            'video_file' => [
                'label' => 'Video File',
                'rules' => 'uploaded[video_file]|max_size[video_file,51200]|ext_in[video_file,mp4,avi,mov,wmv,mkv]',
                'errors' => [
                    'uploaded' => 'Please select a video file',
                    'max_size' => 'Video file size must not exceed 50MB',
                    'ext_in' => 'Only mp4, avi, mov, wmv, mkv video formats are allowed'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $videoFile = $this->request->getFile('video_file');

        if ($videoFile->isValid() && !$videoFile->hasMoved()) {
            // Generate unique filename
            $newName = $videoFile->getRandomName();

            // Move file to public/assets/videos
            $videoFile->move(FCPATH . 'assets/videos', $newName);

            $data = [
                'name' => $this->request->getPost('name'),
                'file_path' => 'assets/videos/' . $newName,
                'status' => $this->request->getPost('status')
            ];

            if (!$this->videoModel->insert($data)) {
                // Delete uploaded file if database insert fails
                @unlink(FCPATH . 'assets/videos/' . $newName);

                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Failed to create video',
                    'errors' => $this->videoModel->errors()
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Video uploaded successfully'
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'success' => false,
            'message' => 'Failed to upload video file'
        ]);
    }

    public function updateVideo($id)
    {
        $video = $this->videoModel->find($id);

        if (!$video) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Video not found'
            ]);
        }

        $jsonInput = $this->request->getJSON(true);
        $input = is_array($jsonInput) ? $jsonInput : [];
        $input = array_merge($input, $this->request->getPost() ?? []);

        // Validate video data manually with ID exclusion
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => [
                'label' => 'Video Name',
                'rules' => "required|max_length[255]|is_unique[videos.name,id,{$id}]",
                'errors' => [
                    'required' => 'Video name is required',
                    'max_length' => 'Video name cannot exceed 255 characters',
                    'is_unique' => 'This video name already exists'
                ]
            ],
            'status' => [
                'label' => 'Status',
                'rules' => 'required|in_list[active,inactive]',
                'errors' => [
                    'required' => 'Status is required',
                    'in_list' => 'Status must be active or inactive'
                ]
            ]
        ]);

        if (!$validation->run($input)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'name' => $input['name'],
            'status' => $input['status']
        ];

        // Check if new video file is uploaded
        $videoFile = $this->request->getFile('video_file');

        if ($videoFile && $videoFile->isValid() && !$videoFile->hasMoved()) {
            // Validate new video file
            $fileValidation = \Config\Services::validation();
            $fileValidation->setRules([
                'video_file' => [
                    'label' => 'Video File',
                    'rules' => 'max_size[video_file,51200]|ext_in[video_file,mp4,avi,mov,wmv,mkv]',
                    'errors' => [
                        'max_size' => 'Video file size must not exceed 50MB',
                        'ext_in' => 'Only mp4, avi, mov, wmv, mkv video formats are allowed'
                    ]
                ]
            ]);

            if (!$fileValidation->withRequest($this->request)->run()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $fileValidation->getErrors()
                ]);
            }

            // Delete old video file
            if (!empty($video['file_path'])) {
                $oldFilePath = FCPATH . $video['file_path'];
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            }

            // Upload new video file
            $newName = $videoFile->getRandomName();
            $videoFile->move(FCPATH . 'assets/videos', $newName);
            $data['file_path'] = 'assets/videos/' . $newName;
        }

        // Update without model validation since we already validated
        $this->videoModel->skipValidation(true);
        $error = $this->versionedUpdate($this->videoModel, $id, $data, $input, 'video');
        $this->videoModel->skipValidation(false);
        if ($error) {
            return $error;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Video updated successfully'
        ]);
    }

    public function deleteVideo($id)
    {
        $video = $this->videoModel->find($id);

        if (!$video) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Video not found'
            ]);
        }

        if (!$this->videoModel->deleteWithFile($id)) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to delete video'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Video deleted successfully'
        ]);
    }

    // Visit Reason Management
    public function getVisitReasons()
    {
        $perPage = $this->request->getGet('per_page') ?? 10;
        $page = $this->request->getGet('page') ?? 1;
        $search = $this->request->getGet('search') ?? '';
        $sortBy = $this->request->getGet('sort_by') ?? 'created_at';
        $sortOrder = $this->request->getGet('sort_order') ?? 'DESC';

        $offset = ($page - 1) * $perPage;

        $builder = $this->visitReasonModel->builder();

        if (!empty($search)) {
            $builder->like('reason', $search);
        }

        $totalRecords = $builder->countAllResults(false);

        $visitReasons = $builder
            ->orderBy($sortBy, $sortOrder)
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $from = $totalRecords > 0 ? $offset + 1 : 0;
        $to = min($offset + $perPage, $totalRecords);

        return $this->response->setJSON([
            'success' => true,
            'data' => $visitReasons,
            'pagination' => [
                'total' => $totalRecords,
                'per_page' => (int) $perPage,
                'current_page' => (int) $page,
                'last_page' => ceil($totalRecords / $perPage),
                'from' => $from,
                'to' => $to
            ]
        ]);
    }

    public function createVisitReason()
    {
        $rules = [
            'reason' => 'required|min_length[3]|max_length[255]|is_unique[visit_reasons.reason]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'reason' => $this->request->getPost('reason')
        ];

        if (!$this->visitReasonModel->insert($data)) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to create visit reason'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Visit reason created successfully'
        ]);
    }

    public function updateVisitReason($id)
    {
        $visitReason = $this->visitReasonModel->find($id);

        if (!$visitReason) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Visit reason not found'
            ]);
        }

        $jsonInput = $this->request->getJSON(true);
        $input = is_array($jsonInput) ? $jsonInput : [];
        $input = array_merge($input, $this->request->getPost() ?? []);

        // Manual validation with ID exclusion for unique check
        $rules = [
            'reason' => "required|min_length[3]|max_length[255]|is_unique[visit_reasons.reason,id,{$id}]"
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($rules);

        if (!$validation->run($input)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'reason' => $input['reason']
        ];

        // Skip model validation since we did it manually
        $this->visitReasonModel->skipValidation(true);
        $error = $this->versionedUpdate($this->visitReasonModel, $id, $data, $input, 'visit reason');
        $this->visitReasonModel->skipValidation(false);
        if ($error) {
            return $error;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Visit reason updated successfully'
        ]);
    }

    public function deleteVisitReason($id)
    {
        $visitReason = $this->visitReasonModel->find($id);

        if (!$visitReason) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Visit reason not found'
            ]);
        }

        if (!$this->visitReasonModel->delete($id)) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to delete visit reason'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Visit reason deleted successfully'
        ]);
    }

    // Visitor Type Management
    public function getVisitorType($id)
    {
        $row = $this->visitorTypeModel->find($id);

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Visitor type not found',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $row,
        ]);
    }

    public function getVisitorTypes()
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        $page = (int) ($this->request->getGet('page') ?? 1);
        $search = $this->request->getGet('search') ?? '';
        $sortBy = $this->request->getGet('sort_by') ?? 'created_at';
        $sortOrder = $this->request->getGet('sort_order') ?? 'DESC';

        $offset = ($page - 1) * $perPage;
        $totalRecords = $this->visitorTypeModel->countVisitorTypes($search);
        $rows = $this->visitorTypeModel->getVisitorTypesPage($perPage, $offset, $search, $sortBy, $sortOrder);

        $from = $totalRecords > 0 ? $offset + 1 : 0;
        $to = min($offset + $perPage, $totalRecords);

        return $this->response->setJSON([
            'success' => true,
            'data' => $rows,
            'pagination' => [
                'total' => $totalRecords,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $perPage > 0 ? max(1, (int) ceil($totalRecords / $perPage)) : 1,
                'from' => $from,
                'to' => $to,
            ],
        ]);
    }

    public function createVisitorType()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[255]|is_unique[visitor_types.name]',
            'path' => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'path' => $this->request->getPost('path') ?? '',
        ];

        if (!$this->visitorTypeModel->insert($data)) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to create visitor type',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Visitor type created successfully',
        ]);
    }

    public function updateVisitorType($id)
    {
        $row = $this->visitorTypeModel->find($id);

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Visitor type not found',
            ]);
        }

        $jsonInput = $this->request->getJSON(true);
        $input = is_array($jsonInput) ? $jsonInput : [];
        $input = array_merge($input, $this->request->getPost() ?? []);

        $rules = [
            'name' => "required|min_length[2]|max_length[255]|is_unique[visitor_types.name,id,{$id}]",
            'path' => 'permit_empty|max_length[500]',
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($rules);

        if (!$validation->run($input)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors(),
            ]);
        }

        $data = [
            'name' => $input['name'],
            'path' => $input['path'] ?? '',
        ];

        $this->visitorTypeModel->skipValidation(true);
        $error = $this->versionedUpdate($this->visitorTypeModel, $id, $data, $input, 'visitor type');
        $this->visitorTypeModel->skipValidation(false);
        if ($error) {
            return $error;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Visitor type updated successfully',
        ]);
    }

    public function deleteVisitorType($id)
    {
        $row = $this->visitorTypeModel->find($id);

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Visitor type not found',
            ]);
        }

        if (!$this->visitorTypeModel->delete($id)) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to delete visitor type',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Visitor type deleted successfully',
        ]);
    }

    // ============== DEVICE ASSIGNMENTS METHODS ==============

    public function getDeviceAssignments()
    {
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 10;
        $search = $this->request->getGet('search') ?? '';
        $offset = ($page - 1) * $perPage;

        $devices = $this->deviceAssignmentModel->getDeviceAssignmentsWithPagination($search, $perPage, $offset);
        $total = $this->deviceAssignmentModel->getTotalDeviceAssignments($search);

        return $this->response->setJSON([
            'success' => true,
            'data' => $devices,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => (int) $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total)
            ]
        ]);
    }

    public function getDeviceAssignment($id)
    {
        $device = $this->deviceAssignmentModel->find($id);
        if (!$device) {
            return $this->response->setJSON(['success' => false, 'message' => 'Device not found'])->setStatusCode(404);
        }
        return $this->response->setJSON(['success' => true, 'data' => $device]);
    }

    public function createDeviceAssignment()
    {
        $input = $this->request->getJSON(true);
        $rules = [
            'device_id' => 'required|max_length[50]',
            'ip_address' => 'required|valid_ip',
            'status' => 'required|in_list[Online,Offline]',
            'registration_status' => 'required|in_list[Registered,Unregistered]',
            'location_id' => 'required|numeric',
            'type' => 'required|in_list[Check-In,Check-Out]',
            'last_heartbeat' => 'permit_empty|valid_date[Y-m-d H:i:s]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        try {
            if ($this->deviceAssignmentModel->insert($input)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Device Assignment created successfully']);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to create Device Assignment'])->setStatusCode(500);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }

    public function generateVisitorQr()
    {
        // Must hardcode the IP instead of base_url() so the phone gets the LAN IP 
        // even if the admin generated this QR code while visiting "localhost" in their PC browser.
        $qrCodeData = 'http://192.168.100.243:8080/vms/visitor-registration?token=MTM%3D';

        $options = new \chillerlan\QRCode\QROptions();
        $options->version = \chillerlan\QRCode\Common\Version::AUTO;
        $options->outputInterface = \chillerlan\QRCode\Output\QRGdImagePNG::class;
        $options->eccLevel = \chillerlan\QRCode\Common\EccLevel::L;
        $options->scale = 5;
        $options->outputBase64 = false;

        $qrcode = new \chillerlan\QRCode\QRCode($options);
        $output = $qrcode->render($qrCodeData);

        $this->response->setHeader('Content-Type', 'image/png');
        echo $output;
        exit;
    }

    public function updateDeviceAssignment($id)
    {
        if (!$this->deviceAssignmentModel->find($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Device not found'])->setStatusCode(404);
        }

        $input = $this->request->getJSON(true);
        if (!is_array($input)) {
            $input = [];
        }
        $rules = [
            'device_id' => 'required|max_length[50]',
            'ip_address' => 'required|valid_ip',
            'status' => 'required|in_list[Online,Offline]',
            'registration_status' => 'required|in_list[Registered,Unregistered]',
            'location_id' => 'required|numeric',
            'type' => 'required|in_list[Check-In,Check-Out]',
            'last_heartbeat' => 'permit_empty|valid_date[Y-m-d H:i:s]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        $data = $input;
        unset($data['version'], $data['id']);

        $error = $this->versionedUpdate($this->deviceAssignmentModel, $id, $data, $input, 'device assignment');
        if ($error) {
            return $error;
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Device Assignment updated successfully']);
    }

    public function deleteDeviceAssignment($id)
    {
        if (!$this->deviceAssignmentModel->find($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Device not found'])->setStatusCode(404);
        }
        if ($this->deviceAssignmentModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Device Assignment deleted successfully']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete Device Assignment'])->setStatusCode(500);
    }

    public function checkDeviceStatus($id)
    {
        $device = $this->deviceAssignmentModel->find($id);
        if (!$device) {
            return $this->response->setJSON(['success' => false, 'message' => 'Device not found'])->setStatusCode(404);
        }

        $ip = $device['ip_address'];
        $os = strtoupper(substr(PHP_OS, 0, 3));

        $isOnline = false;

        // Define if we should do a real hardware ping. Defaults to false (simulation) unless explicitly set to true.
        $enableRealPing = env('ENABLE_REAL_PING', false);

        if ($enableRealPing) {
            // Pinging the device with a short timeout. Use 1 ping attempt.
            if ($os === 'WIN') {
                exec("ping -n 1 -w 1000 " . escapeshellarg($ip), $output, $result);
                $isOnline = ($result === 0);
            } else {
                exec("ping -c 1 -W 1 " . escapeshellarg($ip), $output, $result);
                $isOnline = ($result === 0);
            }
        } else {
            // Simulated Ping behavior for development
            usleep(500000); // Simulate network latency (500ms)
            // By default, simulate Offline as requested
            $isOnline = false;
        }

        $status = $isOnline ? 'Online' : 'Offline';
        $updateData = ['status' => $status];
        if ($isOnline) {
            $updateData['last_heartbeat'] = date('Y-m-d H:i:s');
        }

        $this->deviceAssignmentModel->update($id, $updateData);
        $updatedDevice = $this->deviceAssignmentModel->find($id);

        return $this->response->setJSON([
            'success' => true,
            'status' => $updatedDevice['status'],
            'last_heartbeat' => $updatedDevice['last_heartbeat']
        ]);
    }

    public function getAllLocations()
    {
        return $this->response->setJSON(['success' => true, 'data' => $this->locationModel->findAll()]);
    }

    // ============== SETTINGS METHODS ==============

    public function getIpRangeSettings()
    {
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'ip_range_from' => $this->settingModel->getSetting('ip_range_from'),
                'ip_range_to' => $this->settingModel->getSetting('ip_range_to')
            ]
        ]);
    }

    public function saveIpRangeSettings()
    {
        $input = $this->request->getJSON(true);
        $rules = [
            'ip_range_from' => 'required|valid_ip',
            'ip_range_to' => 'required|valid_ip'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        $this->settingModel->setSetting('ip_range_from', $input['ip_range_from']);
        $this->settingModel->setSetting('ip_range_to', $input['ip_range_to']);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'IP Range settings saved successfully'
        ]);
    }

    public function getEmailTemplateFormSettings()
    {
        return $this->response->setJSON([
            'success' => true,
            'data' => $this->emailTemplateFormFieldModel->getOrderedFields()
        ]);
    }

    public function getInvitationEmailTemplateSettings()
    {
        $emailTemplateService = new EmailTemplateService();
        $process = (string) ($this->request->getGet('process') ?? EmailTemplateService::PROCESS_INVITATION);
        if (!$emailTemplateService->isSupportedProcess($process)) {
            $process = EmailTemplateService::PROCESS_INVITATION;
        }

        $raw = $this->settingModel->getSetting($emailTemplateService->getStorageKey($process));
        $decoded = $raw ? json_decode((string) $raw, true) : [];

        return $this->response->setJSON([
            'success' => true,
            'data' => $emailTemplateService->normalizeTemplate($process, $decoded),
            'meta' => [
                'process' => $process,
                'process_options' => $emailTemplateService->getProcessOptions(),
                'placeholders' => [
                    '{{visitor_name}}',
                    '{{company}}',
                    '{{location}}',
                    '{{reason}}',
                    '{{invited_by}}',
                    '{{link_expiry_date}}',
                ],
            ],
        ]);
    }

    public function saveInvitationEmailTemplateSettings()
    {
        $input = $this->request->getJSON(true);
        if (!is_array($input)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid template payload',
            ])->setStatusCode(400);
        }

        $emailTemplateService = new EmailTemplateService();
        $process = (string) ($input['process'] ?? EmailTemplateService::PROCESS_INVITATION);
        if (!$emailTemplateService->isSupportedProcess($process)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unsupported email process',
            ])->setStatusCode(400);
        }
        unset($input['process']);

        $normalized = $emailTemplateService->normalizeTemplate($process, $input);

        $this->settingModel->setSetting(
            $emailTemplateService->getStorageKey($process),
            json_encode($normalized)
        );

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Email template saved successfully',
            'data' => $normalized,
            'meta' => ['process' => $process],
        ]);
    }

    public function getEmailTemplates()
    {
        $rows = $this->emailTemplateModel
            ->orderBy('code', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $rows,
        ]);
    }

    public function getEmailTemplate($id)
    {
        $row = $this->emailTemplateModel->find((int) $id);
        if (! $row) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email template not found',
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $row,
        ]);
    }

    public function createEmailTemplate()
    {
        $input = $this->request->getJSON(true);
        if (! is_array($input)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid payload',
            ])->setStatusCode(400);
        }

        $code = strtoupper(trim((string) ($input['code'] ?? '')));
        $subject = isset($input['subject']) ? trim((string) $input['subject']) : null;
        $body = isset($input['body']) ? (string) $input['body'] : null;
        $primaryColor = isset($input['primary_color']) ? trim((string) $input['primary_color']) : null;
        $contentBgColor = isset($input['content_bg_color']) ? trim((string) $input['content_bg_color']) : null;
        $textColor = isset($input['text_color']) ? trim((string) $input['text_color']) : null;

        if ($code === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Code is required',
            ])->setStatusCode(400);
        }

        // Allow only safe code format (matches screenshot style).
        if (! preg_match('/^[A-Z0-9_]+$/', $code)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Code can contain only A-Z, 0-9 and underscore',
            ])->setStatusCode(400);
        }

        if ($this->emailTemplateModel->where('code', $code)->first()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Code already exists',
            ])->setStatusCode(409);
        }

        $primaryColor = $this->normalizeHexColorOrNull($primaryColor);
        $contentBgColor = $this->normalizeHexColorOrNull($contentBgColor);
        $textColor = $this->normalizeHexColorOrNull($textColor);

        $id = $this->emailTemplateModel->insert([
            'code' => $code,
            'subject' => $subject,
            'body' => $body,
            'primary_color' => $primaryColor,
            'content_bg_color' => $contentBgColor,
            'text_color' => $textColor,
        ], true);

        if (! $id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create email template',
                'errors' => $this->emailTemplateModel->errors(),
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Email template created',
            'data' => $this->emailTemplateModel->find((int) $id),
        ]);
    }

    public function updateEmailTemplate($id)
    {
        $row = $this->emailTemplateModel->find((int) $id);
        if (! $row) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email template not found',
            ])->setStatusCode(404);
        }

        $input = $this->request->getJSON(true);
        if (! is_array($input)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid payload',
            ])->setStatusCode(400);
        }

        // Code is immutable from UI (edit modal locks it).
        $subject = array_key_exists('subject', $input) ? trim((string) $input['subject']) : $row['subject'];
        $body = array_key_exists('body', $input) ? (string) $input['body'] : $row['body'];
        $primaryColor = array_key_exists('primary_color', $input) ? $this->normalizeHexColorOrNull($input['primary_color']) : ($row['primary_color'] ?? null);
        $contentBgColor = array_key_exists('content_bg_color', $input) ? $this->normalizeHexColorOrNull($input['content_bg_color']) : ($row['content_bg_color'] ?? null);
        $textColor = array_key_exists('text_color', $input) ? $this->normalizeHexColorOrNull($input['text_color']) : ($row['text_color'] ?? null);

        if (! $this->emailTemplateModel->update((int) $id, [
            'subject' => $subject,
            'body' => $body,
            'primary_color' => $primaryColor,
            'content_bg_color' => $contentBgColor,
            'text_color' => $textColor,
        ])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update email template',
                'errors' => $this->emailTemplateModel->errors(),
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Email template updated',
            'data' => $this->emailTemplateModel->find((int) $id),
        ]);
    }

    private function normalizeHexColorOrNull($value): ?string
    {
        $color = trim((string) $value);
        if ($color === '') {
            return null;
        }
        if (preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            return strtoupper($color);
        }
        return null;
    }

    public function saveEmailTemplateFormSettings()
    {
        $input = $this->request->getJSON(true);

        if (!is_array($input) || !isset($input['fields']) || !is_array($input['fields'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid settings payload'
            ])->setStatusCode(400);
        }

        foreach ($input['fields'] as $index => $row) {
            if (!isset($row['id'])) {
                continue;
            }

            $this->emailTemplateFormFieldModel->update((int) $row['id'], [
                'is_enabled' => isset($row['is_enabled']) ? (int) filter_var($row['is_enabled'], FILTER_VALIDATE_BOOLEAN) : 1,
                'is_required' => isset($row['is_required']) ? (int) filter_var($row['is_required'], FILTER_VALIDATE_BOOLEAN) : 0,
                'sort_order' => $index + 1,
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Email template form settings saved successfully'
        ]);
    }

    public function createEmailTemplateFormField()
    {
        $input = $this->request->getJSON(true);
        $label = trim((string) ($input['label'] ?? ''));

        if ($label === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Field label is required'
            ])->setStatusCode(400);
        }

        $fieldType = strtolower((string) ($input['field_type'] ?? 'text'));
        $allowedTypes = ['text', 'textarea', 'email', 'tel', 'date', 'select'];
        if (!in_array($fieldType, $allowedTypes, true)) {
            $fieldType = 'text';
        }

        $maxSort = $this->emailTemplateFormFieldModel
            ->selectMax('sort_order')
            ->first();

        $nextSort = ((int) ($maxSort['sort_order'] ?? 0)) + 1;
        $baseKey = 'custom_' . strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $label));
        $baseKey = trim($baseKey, '_');
        if ($baseKey === 'custom') {
            $baseKey = 'custom_field';
        }

        $fieldKey = $baseKey;
        $suffix = 1;
        while ($this->emailTemplateFormFieldModel->where('field_key', $fieldKey)->first()) {
            $fieldKey = $baseKey . '_' . $suffix;
            $suffix++;
        }

        $this->emailTemplateFormFieldModel->insert([
            'field_key' => $fieldKey,
            'label' => $label,
            'field_type' => $fieldType,
            'placeholder' => $input['placeholder'] ?? null,
            'options' => $fieldType === 'select' ? ($input['options'] ?? null) : null,
            'is_required' => isset($input['is_required']) ? (int) filter_var($input['is_required'], FILTER_VALIDATE_BOOLEAN) : 0,
            'is_enabled' => 1,
            'sort_order' => $nextSort,
            'is_system' => 0,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Field added successfully'
        ]);
    }

    public function updateEmailTemplateFormField($id)
    {
        $field = $this->emailTemplateFormFieldModel->find($id);
        if (!$field) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Field not found'
            ])->setStatusCode(404);
        }

        $input = $this->request->getJSON(true);
        $payload = [];

        if (isset($input['label']) && trim((string) $input['label']) !== '') {
            $payload['label'] = trim((string) $input['label']);
        }

        if (isset($input['placeholder'])) {
            $payload['placeholder'] = trim((string) $input['placeholder']) ?: null;
        }

        if (isset($input['is_enabled'])) {
            $payload['is_enabled'] = (int) filter_var($input['is_enabled'], FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($input['is_required'])) {
            $payload['is_required'] = (int) filter_var($input['is_required'], FILTER_VALIDATE_BOOLEAN);
        }

        if (!$field['is_system']) {
            if (isset($input['field_type'])) {
                $fieldType = strtolower((string) $input['field_type']);
                $allowedTypes = ['text', 'textarea', 'email', 'tel', 'date', 'select'];
                if (in_array($fieldType, $allowedTypes, true)) {
                    $payload['field_type'] = $fieldType;
                }
            }

            if (isset($input['options'])) {
                $payload['options'] = trim((string) $input['options']) ?: null;
            }
        }

        if (!empty($payload)) {
            $this->emailTemplateFormFieldModel->update($id, $payload);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Field updated successfully'
        ]);
    }

    public function deleteEmailTemplateFormField($id)
    {
        $field = $this->emailTemplateFormFieldModel->find($id);
        if (!$field) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Field not found'
            ])->setStatusCode(404);
        }

        if ((int) $field['is_system'] === 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'System fields cannot be deleted'
            ])->setStatusCode(400);
        }

        $this->emailTemplateFormFieldModel->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Field deleted successfully'
        ]);
    }

    public function reorderEmailTemplateFormFields()
    {
        $input = $this->request->getJSON(true);

        if (!is_array($input) || !isset($input['ordered_ids']) || !is_array($input['ordered_ids'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid ordering payload'
            ])->setStatusCode(400);
        }

        foreach ($input['ordered_ids'] as $index => $id) {
            $this->emailTemplateFormFieldModel->update((int) $id, [
                'sort_order' => $index + 1,
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Field order updated successfully'
        ]);
    }

    // ============== REGISTRATION TYPE METHODS ==============

    public function getRegTypes()
    {
        $page    = (int)($this->request->getGet('page') ?? 1);
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        $search  = $this->request->getGet('search') ?? '';

        $offset  = ($page - 1) * $perPage;

        $builder = $this->regTypeModel->orderBy('name', 'ASC');

        if ($search) {
            $builder->like('name', $search);
        }

        $total = $builder->countAllResults(false);
        $data  = $builder->findAll($perPage, $offset);

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

    public function getRegType($id)
    {
        $row = $this->regTypeModel->find($id);

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Registration type not found',
            ]);
        }
    }
    // ============================================
    // Pathway Management
    // ============================================

    public function getPathways()
    {
        $page   = (int) ($this->request->getGet('page') ?? 1);
        $limit  = (int) ($this->request->getGet('limit') ?? 10);
        $search = $this->request->getGet('search') ?? '';
        $sortBy = $this->request->getGet('sortBy') ?? '';

        $offset = ($page - 1) * $limit;

        $pathways = $this->pathwayModel->getPathwaysWithPagination($search, $sortBy, $limit, $offset);
        $total    = $this->pathwayModel->getTotalPathways($search);

        $pathwayIds = array_column($pathways, 'id');
        $allLanes   = $this->pathwayModel->getLanesForPathways($pathwayIds);

        $lanesMap = [];
        foreach ($allLanes as $row) {
            $lanesMap[$row['pathway_id']][] = $row;
        }

        foreach ($pathways as &$p) {
            $p['lanes'] = $lanesMap[$p['id']] ?? [];
        }
        unset($p);

        return $this->response->setJSON([
            'success'    => true,
            'data'       => $pathways,
            'pagination' => [
                'page'       => $page,
                'limit'      => $limit,
                'total'      => $total,
                'totalPages' => ceil($total / $limit),
            ],
        ]);
    }

    public function getPathway($id)
    {
        $pathway = $this->pathwayModel->getPathwayWithLanes($id);

        if (!$pathway) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pathway not found',
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $row,
        ]);
    }

    public function createRegType()
    {
        $input = $this->request->getJSON(true);

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'         => 'required|min_length[2]|max_length[100]|is_unique[reg_type.name]',
            'can_print_cp' => 'permit_empty|in_list[0,1]',
            'status'       => 'required|in_list[Active,Inactive]',
        ]);

        if (!$validation->run($input)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validation->getErrors(),
            ]);
        }

        $data = [
            'name'         => strtoupper($input['name']),
            'can_print_cp' => $input['can_print_cp'] ?? 0,
            'status'       => $input['status'],
        ];

        if (!$this->regTypeModel->insert($data)) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to create registration type',
                'errors'  => $this->regTypeModel->errors(),
            ]);
        }  // ← closes the if block

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Registration type created successfully',
            'data'    => ['id' => $this->regTypeModel->getInsertID()],
        ]);
    }  

    public function createPathway()
    {
        $input = $this->request->getJSON(true);

        $data = [
            'name'   => $input['name'] ?? '',
            'status' => $input['status'] ?? 'active',
        ];

        if (!$this->pathwayModel->insert($data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create pathway',
                'errors'  => $this->pathwayModel->errors(),
            ])->setStatusCode(400);
        }

        $pathwayId = $this->pathwayModel->getInsertID();

        if (!empty($input['lane_ids']) && is_array($input['lane_ids'])) {
            $this->pathwayModel->syncLanes($pathwayId, $input['lane_ids']);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Registration type created successfully',
            'data'    => ['id' => $this->regTypeModel->getInsertID()],
        ]);
    }

    public function updateRegType($id)
    {
        $row = $this->regTypeModel->find($id);

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Registration type not found',
            ]);
        }
    }

    public function updatePathway($id)
    {
        if (!$this->pathwayModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pathway not found',
            ])->setStatusCode(404);
        }

        $input = $this->request->getJSON(true);
        if (!is_array($input)) {
            $input = [];
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'         => "required|min_length[2]|max_length[100]|is_unique[reg_type.name,id,{$id}]",
            'can_print_cp' => 'permit_empty|in_list[0,1]',
            'status'       => 'required|in_list[Active,Inactive]',
        ]);

        if (!$validation->run($input)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validation->getErrors(),
            ]);
        }

        $data = [
            'name'         => strtoupper($input['name']),
            'can_print_cp' => $input['can_print_cp'] ?? 0,
            'status'       => $input['status'],
        ];

        $error = $this->versionedUpdate($this->regTypeModel, $id, $data, $input, 'registration type');
        $data = [
            'name'   => $input['name'] ?? '',
            'status' => $input['status'] ?? 'active',
        ];

        $error = $this->versionedUpdate($this->pathwayModel, $id, $data, $input, 'pathway');
        if ($error) {
            return $error;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Registration type updated successfully',
        ]);
    }

    public function deleteRegType($id)
    {
        $row = $this->regTypeModel->find($id);

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Registration type not found',
            ]);
        }

        if (!$this->regTypeModel->delete($id)) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to delete registration type',
            ]);
        if (isset($input['lane_ids']) && is_array($input['lane_ids'])) {
            $this->pathwayModel->syncLanes($id, $input['lane_ids']);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pathway updated successfully',
        ]);
    }

    public function deletePathway($id)
    {
        if (!$this->pathwayModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pathway not found',
            ])->setStatusCode(404);
        }

        if (!$this->pathwayModel->delete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete pathway',
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Registration type deleted successfully',
        ]);
    }

    public function blacklistReason()
    {
       $data['blacklist_reasons'] = $this->blacklistReasonModel->findAll();

        return view('config/index', $data);
    }

    // Update Business Type in Config
    public function updateBusinessType()
    {
        $id = $this->request->getPost('id');

        $data = [
            'business_type' => $this->request->getPost('business_type'),
            'reg_type' => $this->request->getPost('reg_type'),
            'ledger' => $this->request->getPost('ledger'),
            'haulier' => $this->request->getPost('haulier'),
            'lpk_license_no' => $this->request->getPost('lpk_license_no'),
            'lpk_license_no_optional' => $this->request->getPost('lpk_license_no_optional'),
            'lpk_ancillary_contractor' => $this->request->getPost('lpk_ancillary_contractor'),
            'customs_license_no' => $this->request->getPost('customs_license_no'),
            'sst_reg_no' => $this->request->getPost('sst_reg_no'),
            'business_vol' => $this->request->getPost('business_vol'),
            'trade_ref_no' => $this->request->getPost('trade_ref_no'),
            'bank_info' => $this->request->getPost('bank_info'),
            'operator_code' => $this->request->getPost('operator_code'),
            'copy_board_director_ic' => $this->request->getPost('copy_board_director_ic'),
            'apad_certificate_no' => $this->request->getPost('apad_certificate_no'),
            'license_expiry_date' => $this->request->getPost('license_expiry_date'),
            'warehouse_info' => $this->request->getPost('warehouse_info'),
            'nature_of_business' => $this->request->getPost('nature_of_business'),
            'pli' => $this->request->getPost('pli'),
            'status' => strtoupper($this->request->getPost('status')),
        ];

        $this->bizTypeModel->update($id, $data);

        return redirect()->to(base_url('config'))->with('success', 'Business type updated successfully.');
    }


            'message' => 'Pathway deleted successfully',
        ]);
    }

    public function getAllLanes()
    {
        $lanes = $this->laneModel->where('status', 'active')
            ->orderBy('CAST(lane AS UNSIGNED) = 0', 'ASC')
            ->orderBy('CAST(lane AS UNSIGNED)', 'ASC')
            ->orderBy('lane', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data'    => $lanes,
        ]);
    }

    // ============== ALERT PRIORITY MANAGEMENT METHODS ==============

    public function getAlertPriorities()
    {
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        $offset  = ($page - 1) * $perPage;

        $total = $this->alertPriorityModel->countAllResults(false);
        $items = $this->alertPriorityModel
            ->orderBy('id', 'ASC')
            ->findAll($perPage, $offset);

        return $this->response->setJSON([
            'success' => true,
            'data' => $items,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage),
                'from' => $total > 0 ? $offset + 1 : 0,
                'to' => min($offset + $perPage, $total),
            ],
        ]);
    }

    public function updateAlertPriority($id)
    {
        $item = $this->alertPriorityModel->find($id);
        if (!$item) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Alert priority not found',
            ]);
        }

        $input = $this->request->getJSON(true);
        if (!is_array($input)) {
            $input = $this->request->getPost();
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'priority' => 'required|in_list[low,medium,high]',
            'response_time' => 'required|max_length[80]',
            'notification_scope' => 'required|max_length[100]',
        ]);

        if (!$validation->run($input)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors(),
            ]);
        }

        $data = [
            'priority' => $input['priority'],
            'response_time' => $input['response_time'],
            'notification_scope' => $input['notification_scope'],
        ];

        $this->alertPriorityModel->skipValidation(true);
        $error = $this->versionedUpdate($this->alertPriorityModel, $id, $data, $input, 'alert priority');
        $this->alertPriorityModel->skipValidation(false);
        if ($error) {
            return $error;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Alert priority updated successfully',
        ]);
    }
}
