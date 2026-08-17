<?php

namespace App\Controllers;

use App\Models\EmapMapModel;
use CodeIgniter\HTTP\ResponseInterface;

class Emap extends BaseController
{
    private EmapMapModel $maps;

    public function __construct()
    {
        helper(['access', 'role']);
        $this->maps = new EmapMapModel();
    }

    public function index(): string
    {
        return view('emap/index', [
            'pageTitle' => 'E-Map - Visitor Management System',
            'canDesign' => $this->canDesign(),
            'canPublish' => $this->canPublish(),
        ]);
    }

    public function bootstrap(): ResponseInterface
    {
        $map = $this->getOrCreateDefaultMap();

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'map' => $this->presentMap($map),
                'references' => $this->referenceData(),
                'visitors' => $this->liveVisitors(),
                'movementLog' => $this->movementLog(),
                'permissions' => [
                    'design' => $this->canDesign(),
                    'publish' => $this->canPublish(),
                ],
                'serverTime' => date(DATE_ATOM),
            ],
        ]);
    }

    public function live(): ResponseInterface
    {
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'visitors' => $this->liveVisitors(),
                'movementLog' => $this->movementLog(),
                'serverTime' => date(DATE_ATOM),
            ],
        ]);
    }

    public function update(int $id): ResponseInterface
    {
        if (!$this->canDesign()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You do not have permission to edit maps.',
            ]);
        }

        $map = $this->findVisibleMap($id);
        if (!$map) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Map not found.',
            ]);
        }

        $payload = $this->request->getJSON(true);
        $layout = $payload['layout'] ?? null;
        if (!is_array($layout) || !$this->validLayout($layout)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'The map layout is invalid.',
            ]);
        }

        $status = ($payload['publish'] ?? false) && $this->canPublish()
            ? 'published'
            : ($map['status'] ?? 'draft');

        $nextVersion = (int) ($map['version'] ?? 1) + 1;
        $data = [
            'name' => trim((string) ($payload['name'] ?? $map['name'])),
            'premise_name' => trim((string) ($payload['premiseName'] ?? $map['premise_name'])),
            'floor_name' => trim((string) ($payload['floorName'] ?? $map['floor_name'])),
            'canvas_width' => (int) ($layout['width'] ?? $map['canvas_width']),
            'canvas_height' => (int) ($layout['height'] ?? $map['canvas_height']),
            'layout_json' => json_encode($layout, JSON_UNESCAPED_SLASHES),
            'status' => $status,
            'version' => $nextVersion,
            'updated_by' => (int) session()->get('user_id'),
            'published_at' => $status === 'published' ? date('Y-m-d H:i:s') : ($map['published_at'] ?? null),
        ];

        if (!$this->maps->update($id, $data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Unable to save the map.',
                'errors' => $this->maps->errors(),
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $status === 'published' ? 'Map published.' : 'Map draft saved.',
            'data' => ['map' => $this->presentMap($this->maps->find($id))],
        ]);
    }

    private function canDesign(): bool
    {
        $role = normalize_role_slug((string) session()->get('role'));
        return in_array($role, ['superadmin', 'clientsuperadmin', 'admin'], true);
    }

    private function canPublish(): bool
    {
        $role = normalize_role_slug((string) session()->get('role'));
        return in_array($role, ['superadmin', 'clientsuperadmin'], true);
    }

    private function currentClientId(): ?int
    {
        $clientId = function_exists('current_client_id') ? (int) current_client_id() : 0;
        return $clientId > 0 ? $clientId : null;
    }

    private function getOrCreateDefaultMap(): array
    {
        $clientId = $this->currentClientId();
        $builder = $this->maps->builder()->orderBy('id', 'ASC');
        $clientId === null
            ? $builder->where('client_id', null)
            : $builder->where('client_id', $clientId);

        $existing = $builder->get()->getRowArray();
        if ($existing) {
            return $existing;
        }

        $layout = $this->defaultLayout();
        $this->maps->insert([
            'client_id' => $clientId,
            'name' => 'Main Premise - Ground Floor',
            'premise_name' => 'Main Premise',
            'floor_name' => 'Ground Floor',
            'canvas_width' => $layout['width'],
            'canvas_height' => $layout['height'],
            'layout_json' => json_encode($layout, JSON_UNESCAPED_SLASHES),
            'status' => 'draft',
            'version' => 1,
            'created_by' => (int) session()->get('user_id'),
            'updated_by' => (int) session()->get('user_id'),
        ]);

        return $this->maps->find($this->maps->getInsertID());
    }

    private function findVisibleMap(int $id): ?array
    {
        $map = $this->maps->find($id);
        if (!$map) {
            return null;
        }

        $clientId = $this->currentClientId();
        if ($clientId !== null && (int) ($map['client_id'] ?? 0) !== $clientId) {
            return null;
        }

        return $map;
    }

    private function presentMap(array $map): array
    {
        $layout = json_decode((string) ($map['layout_json'] ?? ''), true);
        if (!is_array($layout)) {
            $layout = $this->defaultLayout();
        }

        return [
            'id' => (int) $map['id'],
            'name' => $map['name'],
            'premiseName' => $map['premise_name'],
            'floorName' => $map['floor_name'],
            'status' => $map['status'],
            'version' => (int) $map['version'],
            'publishedAt' => $map['published_at'],
            'layout' => $layout,
        ];
    }

    private function referenceData(): array
    {
        $db = db_connect();

        $locations = $db->table('locations')
            ->select('id, branch, location_access, status')
            ->where('status', 'active')
            ->orderBy('location_access', 'ASC')
            ->get()->getResultArray();

        $subLocations = $db->table('sub_locations sl')
            ->select('sl.id, sl.name, sl.location_id, l.location_access')
            ->join('locations l', 'l.id = sl.location_id', 'left')
            ->where('sl.status', 'active')
            ->orderBy('sl.name', 'ASC')
            ->get()->getResultArray();

        $lanes = $db->table('lanes')
            ->select('id, lane, location_id, status, rfid_reader_ip, rfid_enabled')
            ->where('status', 'active')
            ->orderBy('lane', 'ASC')
            ->get()->getResultArray();

        $devices = $db->table('device_assignments da')
            ->select('da.id, da.device_id, da.ip_address, da.status, da.registration_status, da.location_id, da.type, sl.name AS location_name')
            ->join('sub_locations sl', 'sl.id = da.location_id', 'left')
            ->orderBy('da.device_id', 'ASC')
            ->get()->getResultArray();

        return compact('locations', 'subLocations', 'lanes', 'devices');
    }

    private function liveVisitors(): array
    {
        $db = db_connect();
        if (!$db->tableExists('visitor_card_logs')) {
            return [];
        }

        $builder = $db->table('visitor_card_logs vcl')
            ->select("vcl.id AS movement_id, vcl.invitation_id, vcl.action, vcl.lane_id,
                      COALESCE(vcl.to_sub_location_id, vcl.sub_location_id) AS sub_location_id,
                      vcl.scanned_at, i.full_name, i.company, i.invited_by,
                      iv.check_in_time, iv.check_out_time, l.location_id")
            ->join('(SELECT invitation_id, MAX(id) AS latest_id FROM visitor_card_logs WHERE invitation_id IS NOT NULL GROUP BY invitation_id) latest', 'latest.latest_id = vcl.id', 'inner', false)
            ->join('invitations i', 'i.id = vcl.invitation_id', 'inner')
            ->join('invitation_visitors iv', 'iv.invitation_id = i.id', 'left')
            ->join('lanes l', 'l.id = vcl.lane_id', 'left')
            ->where('i.status', 'Approved')
            ->where('iv.check_in_time IS NOT NULL', null, false)
            ->where('iv.check_out_time IS NULL', null, false)
            ->orderBy('vcl.scanned_at', 'DESC');

        $clientId = $this->currentClientId();
        if ($clientId !== null && $db->fieldExists('client_id', 'invitations')) {
            $builder->where('i.client_id', $clientId);
        }

        $rows = $builder->get()->getResultArray();
        $seen = [];
        $result = [];
        foreach ($rows as $row) {
            $invitationId = (int) $row['invitation_id'];
            if (isset($seen[$invitationId])) {
                continue;
            }
            $seen[$invitationId] = true;
            $name = trim((string) ($row['full_name'] ?? 'Visitor'));
            $result[] = [
                'id' => $invitationId,
                'name' => $name,
                'initials' => $this->initials($name),
                'company' => $row['company'] ?: 'N/A',
                'host' => $row['invited_by'] ?: 'N/A',
                'timeIn' => $row['check_in_time'],
                'lastSeen' => $row['scanned_at'],
                'action' => $row['action'],
                'laneId' => $row['lane_id'] ? (int) $row['lane_id'] : null,
                'locationId' => $row['location_id'] ? (int) $row['location_id'] : null,
                'subLocationId' => $row['sub_location_id'] ? (int) $row['sub_location_id'] : null,
            ];
        }

        return $result;
    }

    private function movementLog(): array
    {
        $db = db_connect();
        if (!$db->tableExists('visitor_card_logs')) {
            return [];
        }

        $builder = $db->table('visitor_card_logs vcl')
            ->select('vcl.id, vcl.action, vcl.scanned_at, i.full_name, i.company, l.lane, sl.name AS sub_location_name')
            ->join('invitations i', 'i.id = vcl.invitation_id', 'left')
            ->join('lanes l', 'l.id = vcl.lane_id', 'left')
            ->join('sub_locations sl', 'sl.id = COALESCE(vcl.to_sub_location_id, vcl.sub_location_id)', 'left', false)
            ->orderBy('vcl.scanned_at', 'DESC')
            ->limit(20);

        $clientId = $this->currentClientId();
        if ($clientId !== null && $db->fieldExists('client_id', 'invitations')) {
            $builder->where('i.client_id', $clientId);
        }

        return array_map(static fn(array $row): array => [
            'id' => (int) $row['id'],
            'name' => $row['full_name'] ?: 'Unknown visitor',
            'company' => $row['company'] ?: 'N/A',
            'action' => $row['action'],
            'zone' => $row['sub_location_name'] ?: ($row['lane'] ?: 'Unknown zone'),
            'time' => $row['scanned_at'],
        ], $builder->get()->getResultArray());
    }

    private function validLayout(array $layout): bool
    {
        if (!isset($layout['width'], $layout['height'])) {
            return false;
        }

        foreach (['zones', 'walls', 'assets', 'labels'] as $key) {
            if (isset($layout[$key]) && !is_array($layout[$key])) {
                return false;
            }
        }

        return (int) $layout['width'] >= 400 && (int) $layout['height'] >= 300;
    }

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        return strtoupper(substr($parts[0] ?? 'V', 0, 1) . substr($parts[1] ?? '', 0, 1));
    }

    private function defaultLayout(): array
    {
        return [
            'width' => 1000,
            'height' => 590,
            'zones' => [
                ['id' => 'lobby', 'name' => 'LOBBY', 'x' => 45, 'y' => 56, 'w' => 185, 'h' => 210, 'color' => '#ddebff', 'locationId' => null, 'subLocationId' => null],
                ['id' => 'office', 'name' => 'OFFICE AREA', 'x' => 230, 'y' => 56, 'w' => 380, 'h' => 210, 'color' => '#fff0c9', 'locationId' => null, 'subLocationId' => null],
                ['id' => 'meeting', 'name' => 'MEETING ROOM', 'x' => 610, 'y' => 56, 'w' => 220, 'h' => 150, 'color' => '#e8dfff', 'locationId' => null, 'subLocationId' => null],
                ['id' => 'production', 'name' => 'PRODUCTION AREA', 'x' => 700, 'y' => 206, 'w' => 265, 'h' => 210, 'color' => '#ddf4e5', 'locationId' => null, 'subLocationId' => null],
                ['id' => 'cafeteria', 'name' => 'CAFETERIA', 'x' => 45, 'y' => 330, 'w' => 220, 'h' => 200, 'color' => '#eee7ff', 'locationId' => null, 'subLocationId' => null],
                ['id' => 'loading', 'name' => 'LOADING BAY', 'x' => 265, 'y' => 330, 'w' => 435, 'h' => 200, 'color' => '#eceff2', 'locationId' => null, 'subLocationId' => null],
                ['id' => 'warehouse', 'name' => 'WAREHOUSE', 'x' => 700, 'y' => 416, 'w' => 265, 'h' => 114, 'color' => '#ffe1e1', 'locationId' => null, 'subLocationId' => null],
            ],
            'walls' => [
                ['id' => 'wall-1', 'x1' => 265, 'y1' => 330, 'x2' => 700, 'y2' => 330],
                ['id' => 'wall-2', 'x1' => 610, 'y1' => 206, 'x2' => 700, 'y2' => 206],
            ],
            'assets' => [],
            'labels' => [],
        ];
    }
}
