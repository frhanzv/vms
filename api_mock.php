<?php
/**
 * ============================================================
 *  Mock Laravel Backend  —  for VMS API Management testing
 *  Run with:  php -S localhost:8000 api_mock.php
 * ============================================================
 */

// Allow requests from your VMS (CORS)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$uri    = strtok($_SERVER['REQUEST_URI'], '?');
$method = $_SERVER['REQUEST_METHOD'];

// ── Router ────────────────────────────────────────────────────
// GET /api/registry  →  list of endpoints (used by Sync button)
if ($method === 'GET' && $uri === '/api/registry') {
    echo json_encode([
        'success' => true,
        'data'    => [
            [
                'name'        => 'getVisitorList',
                'method'      => 'GET',
                'uri'         => '/api/visitors',
                'description' => 'Get all visitors from Laravel',
            ],
            [
                'name'        => 'getVisitorDetail',
                'method'      => 'GET',
                'uri'         => '/api/visitors/{id}',
                'description' => 'Get a single visitor by ID',
            ],
            [
                'name'        => 'createVisitor',
                'method'      => 'POST',
                'uri'         => '/api/visitors',
                'description' => 'Register a new visitor',
            ],
            [
                'name'        => 'updateVisitor',
                'method'      => 'PUT',
                'uri'         => '/api/visitors/{id}',
                'description' => 'Update visitor information',
            ],
            [
                'name'        => 'deleteVisitor',
                'method'      => 'DELETE',
                'uri'         => '/api/visitors/{id}',
                'description' => 'Remove a visitor record',
            ],
            [
                'name'        => 'getStaffList',
                'method'      => 'GET',
                'uri'         => '/api/staff',
                'description' => 'Get all staff members',
            ],
            [
                'name'        => 'getBlacklist',
                'method'      => 'GET',
                'uri'         => '/api/blacklist',
                'description' => 'Get blacklisted visitors',
            ],
            [
                'name'        => 'checkInVisitor',
                'method'      => 'POST',
                'uri'         => '/api/checkin',
                'description' => 'Record visitor check-in',
            ],
            [
                'name'        => 'checkOutVisitor',
                'method'      => 'POST',
                'uri'         => '/api/checkout',
                'description' => 'Record visitor check-out',
            ],
            [
                'name'        => 'getAccessReport',
                'method'      => 'GET',
                'uri'         => '/api/reports/access',
                'description' => 'Get visitor access report',
            ],
        ],
    ]);
    exit;
}

// GET /api/visitors  →  dummy visitor list
if ($method === 'GET' && $uri === '/api/visitors') {
    echo json_encode([
        'success' => true,
        'data'    => [
            [
                'id'         => 1,
                'name'       => 'Ahmad bin Abdullah',
                'ic_number'  => '900101-14-1234',
                'phone'      => '0123456789',
                'email'      => 'ahmad@example.com',
                'company'    => 'Tech Sdn Bhd',
                'purpose'    => 'Meeting',
                'status'     => 'checked_in',
                'created_at' => '2026-04-27 09:00:00',
            ],
            [
                'id'         => 2,
                'name'       => 'Siti Nurhaliza',
                'ic_number'  => '950202-10-5678',
                'phone'      => '0198765432',
                'email'      => 'siti@example.com',
                'company'    => 'Design Studio',
                'purpose'    => 'Delivery',
                'status'     => 'checked_out',
                'created_at' => '2026-04-27 10:30:00',
            ],
            [
                'id'         => 3,
                'name'       => 'Rajesh Kumar',
                'ic_number'  => '880303-08-9012',
                'phone'      => '0112233445',
                'email'      => 'rajesh@example.com',
                'company'    => 'Consulting Group',
                'purpose'    => 'Interview',
                'status'     => 'pending',
                'created_at' => '2026-04-27 11:00:00',
            ],
        ],
        'total' => 3,
    ]);
    exit;
}

// GET /api/staff  →  dummy staff list
if ($method === 'GET' && $uri === '/api/staff') {
    echo json_encode([
        'success' => true,
        'data'    => [
            ['id' => 1, 'name' => 'Ali Hassan',    'department' => 'Security',  'role' => 'Guard'],
            ['id' => 2, 'name' => 'Mary Tan',      'department' => 'HR',        'role' => 'Manager'],
            ['id' => 3, 'name' => 'Kumar Selvam',  'department' => 'IT',        'role' => 'Engineer'],
        ],
    ]);
    exit;
}

// GET /api/blacklist  →  dummy blacklist
if ($method === 'GET' && $uri === '/api/blacklist') {
    echo json_encode([
        'success' => true,
        'data'    => [
            ['id' => 1, 'name' => 'Unknown Person', 'reason' => 'Trespassing', 'added_at' => '2026-01-15'],
        ],
    ]);
    exit;
}

// GET /api/reports/access  →  dummy report
if ($method === 'GET' && $uri === '/api/reports/access') {
    echo json_encode([
        'success' => true,
        'data'    => [
            'total_visitors'   => 120,
            'checked_in'       => 45,
            'checked_out'      => 70,
            'pending'          => 5,
            'generated_at'     => date('Y-m-d H:i:s'),
        ],
    ]);
    exit;
}

// POST /api/visitors  →  create visitor
if ($method === 'POST' && $uri === '/api/visitors') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    echo json_encode([
        'success' => true,
        'message' => 'Visitor created successfully',
        'data'    => array_merge(['id' => rand(100, 999)], $input),
    ]);
    exit;
}

// POST /api/checkin
if ($method === 'POST' && $uri === '/api/checkin') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    echo json_encode([
        'success'    => true,
        'message'    => 'Check-in recorded',
        'visitor_id' => $input['visitor_id'] ?? null,
        'time'       => date('Y-m-d H:i:s'),
    ]);
    exit;
}

// POST /api/checkout
if ($method === 'POST' && $uri === '/api/checkout') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    echo json_encode([
        'success'    => true,
        'message'    => 'Check-out recorded',
        'visitor_id' => $input['visitor_id'] ?? null,
        'time'       => date('Y-m-d H:i:s'),
    ]);
    exit;
}

// 404 fallback
http_response_code(404);
echo json_encode([
    'success' => false,
    'message' => 'Endpoint not found: ' . $method . ' ' . $uri,
    'hint'    => 'Available: GET /api/registry, GET /api/visitors, GET /api/staff, GET /api/blacklist, GET /api/reports/access, POST /api/visitors, POST /api/checkin, POST /api/checkout',
]);
