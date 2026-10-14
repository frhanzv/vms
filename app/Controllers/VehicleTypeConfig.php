<?php

namespace App\Controllers;

use App\Libraries\VehicleRules;

/**
 * Config > Vehicle Types. A client admin manages its own types; the platform admin manages the
 * shared ones (client_id NULL) that every client can use.
 */
class VehicleTypeConfig extends BaseController
{
    private const FLAGS = ['seater_req', 'working_height_req', 'working_load_req', 'truck_head_req', 'trailer_req', 'truck_weight_req', 'chassis_req', 'trailer_weight_req', 'trailer_type_req', 'req_pass', 'payment_required', 'duplicate_plate_ok'];

    private function scope(): ?int
    {
        helper(['role', 'feature']);

        return is_platform_superadmin() ? null : (int) current_client_id();
    }

    private function json(bool $ok, string $msg, array $extra = [], int $code = 200)
    {
        return $this->response->setStatusCode($code)->setJSON(['success' => $ok, 'message' => $msg] + $extra);
    }

    private function mine(int $id): ?array
    {
        $b = \Config\Database::connect()->table('vehicle_types')->where('id', $id);
        $c = $this->scope();
        $c === null ? $b->where('client_id IS NULL', null, false) : $b->where('client_id', $c);

        return $b->get()->getRowArray() ?: null;
    }

    public function index()
    {
        $b = \Config\Database::connect()->table('vehicle_types')->orderBy('name');
        $c = $this->scope();
        $c === null ? $b->where('client_id IS NULL', null, false)
            : $b->groupStart()->where('client_id', $c)->orWhere('client_id IS NULL', null, false)->groupEnd();

        $rows = array_map(static function ($r) use ($c) {
            $r['editable'] = $c === null ? $r['client_id'] === null : (int) $r['client_id'] === $c;

            return $r;
        }, $b->get()->getResultArray());

        return $this->json(true, 'ok', ['data' => $rows, 'fields' => array_map(static fn($f) => $f[1], VehicleRules::TYPE_FIELDS)]);
    }

    public function save()
    {
        $p    = $this->request->getPost();
        $id   = (int) ($p['id'] ?? 0);
        $name = trim((string) ($p['name'] ?? ''));
        if ($name === '') {
            return $this->json(false, 'Give the vehicle type a name.', [], 400);
        }
        $cat = in_array($p['category'] ?? '', ['car', 'motorbike', 'van', 'truck', 'other'], true) ? $p['category'] : 'car';
        $db  = \Config\Database::connect();

        $dup = $db->table('vehicle_types')->where('LOWER(name)', strtolower($name));
        $this->scope() === null ? $dup->where('client_id IS NULL', null, false) : $dup->where('client_id', $this->scope());
        if ($id) {
            $dup->where('id !=', $id);
        }
        if ($dup->countAllResults() > 0) {
            return $this->json(false, 'A vehicle type with this name already exists.', [], 400);
        }

        $data = ['name' => mb_substr($name, 0, 100), 'category' => $cat, 'status' => ($p['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active', 'updated_at' => date('Y-m-d H:i:s')];
        if ($db->fieldExists('vehicle_group', 'vehicle_types')) {
            $data['vehicle_group'] = in_array($p['vehicle_group'] ?? '', ['public', 'cnc', 'both'], true) ? $p['vehicle_group'] : 'both';
        }
        if ($db->fieldExists('operation', 'vehicle_types')) {
            $data['operation'] = in_array($p['operation'] ?? '', VehicleRules::OPERATIONS, true) ? $p['operation'] : null;
        }
        foreach (self::FLAGS as $f) {
            if ($db->fieldExists($f, 'vehicle_types')) {
                $data[$f] = ! empty($p[$f]) ? 1 : 0;
            }
        }

        if ($id) {
            if (! $this->mine($id)) {
                return $this->json(false, 'Vehicle type not found.', [], 404);
            }
            $db->table('vehicle_types')->where('id', $id)->update($data);
        } else {
            $db->table('vehicle_types')->insert($data + ['client_id' => $this->scope(), 'created_at' => date('Y-m-d H:i:s')]);
        }

        return $this->json(true, 'Vehicle type saved.');
    }

    public function delete($id)
    {
        if (! $this->mine((int) $id)) {
            return $this->json(false, 'Vehicle type not found.', [], 404);
        }
        $db = \Config\Database::connect();
        if ($db->table('vehicles')->where('vehicle_type_id', (int) $id)->countAllResults() > 0) {
            return $this->json(false, 'This type is used by vehicle passes. Set it to Inactive instead of deleting.', [], 400);
        }
        $db->table('vehicle_types')->where('id', (int) $id)->delete();

        return $this->json(true, 'Vehicle type deleted.');
    }
}
