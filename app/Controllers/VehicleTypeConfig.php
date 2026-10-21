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

    /** KPK yes/no columns of a vehicle type (the flags tell where it is offered and what kind of vehicle it is). */
    private const KPK_FLAGS = ['is_commercial', 'is_container', 'is_cargo', 'is_non_cargo', 'is_public', 'is_car', 'is_motorbike', 'is_van'];

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

        $db = \Config\Database::connect();
        $links = [];
        $opts  = [];
        if ($db->tableExists('license_classes')) {
            $ob = $db->table('license_classes')->select('id, name')->where('status', 'active')->orderBy('name');
            $c === null ? $ob->where('client_id IS NULL', null, false)
                : $ob->groupStart()->where('client_id', $c)->orWhere('client_id IS NULL', null, false)->groupEnd();
            $opts = $ob->get()->getResultArray();
            if ($rows && $db->tableExists('vehicle_type_license_classes')) {
                $lb = $db->table('vehicle_type_license_classes l')->select('l.vehicle_type_id, c.id, c.name')
                    ->join('license_classes c', 'c.id = l.license_class_id')->whereIn('l.vehicle_type_id', array_column($rows, 'id'))->orderBy('c.name');
                foreach ($lb->get()->getResultArray() as $l) {
                    $links[(int) $l['vehicle_type_id']][] = ['id' => (int) $l['id'], 'name' => $l['name']];
                }
            }
        }
        foreach ($rows as &$r) {
            $r['license_classes'] = $links[(int) $r['id']] ?? [];
        }

        return $this->json(true, 'ok', ['flags_ready' => $db->fieldExists('is_public', 'vehicle_types'), 'data' => $rows, 'license_options' => $opts, 'fields' => array_map(static fn($f) => $f[1], VehicleRules::TYPE_FIELDS)]);
    }

    public function save()
    {
        $p    = $this->request->getPost();
        $id   = (int) ($p['id'] ?? 0);
        $name = trim((string) ($p['name'] ?? ''));
        if ($name === '') {
            return $this->json(false, 'Give the vehicle type a name.', [], 400);
        }
        $db  = \Config\Database::connect();
        $on  = static fn(string $k): bool => ! empty($p[$k]);
        $flagsReady = $db->fieldExists('is_public', 'vehicle_types');
        if ($flagsReady) {
            // the older columns (category / used for / operation) are worked out from the KPK flags
            $cat = $on('is_car') ? 'car' : ($on('is_motorbike') ? 'motorbike' : ($on('is_van') ? 'van' : ($on('is_commercial') ? 'truck' : 'other')));
        } else {
            $cat = in_array($p['category'] ?? '', ['car', 'motorbike', 'van', 'truck', 'other'], true) ? $p['category'] : 'car';
        }

        $dup = $db->table('vehicle_types')->where('LOWER(name)', strtolower($name));
        $this->scope() === null ? $dup->where('client_id IS NULL', null, false) : $dup->where('client_id', $this->scope());
        if ($id) {
            $dup->where('id !=', $id);
        }
        if ($dup->countAllResults() > 0) {
            return $this->json(false, 'A vehicle type with this name already exists.', [], 400);
        }

        $data = ['name' => mb_substr($name, 0, 100), 'category' => $cat, 'status' => ($p['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active', 'updated_at' => date('Y-m-d H:i:s')];
        if ($flagsReady) {
            foreach (self::KPK_FLAGS as $f) {
                $data[$f] = $on($f) ? 1 : 0;
            }
            $hasC = $on('is_container') || $on('is_cargo') || $on('is_non_cargo');
            $grp  = $on('is_public') && $hasC ? 'both' : ($on('is_public') ? 'public' : ($hasC ? 'cnc' : 'both'));
            $op   = null;
            if ($on('is_container')) {
                $op = ($on('is_cargo') || $on('is_non_cargo')) ? 'CONTAINER, CARGO AND NON-CARGO' : 'CONTAINER';
            } elseif ($on('is_cargo') || $on('is_non_cargo')) {
                $op = $on('is_cargo') ? 'CARGO AND NON-CARGO' : 'NON-CARGO';
            }
        } else {
            $grp = in_array($p['vehicle_group'] ?? '', ['public', 'cnc', 'both'], true) ? $p['vehicle_group'] : 'both';
            $op  = in_array($p['operation'] ?? '', VehicleRules::OPERATIONS, true) ? $p['operation'] : null;
        }
        if ($db->fieldExists('vehicle_group', 'vehicle_types')) {
            $data['vehicle_group'] = $grp;
        }
        if ($db->fieldExists('operation', 'vehicle_types')) {
            $data['operation'] = $op;
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
            $id = (int) $db->insertID();
        }
        $this->syncLicenseClasses($id, $p['license_class_ids'] ?? []);

        return $this->json(true, 'Vehicle type saved.');
    }

    /** The License Classes allowed to drive this type (only ones visible to this admin are accepted). */
    private function syncLicenseClasses(int $typeId, $ids): void
    {
        $db = \Config\Database::connect();
        if (! $typeId || ! $db->tableExists('vehicle_type_license_classes') || ! $db->tableExists('license_classes')) {
            return;
        }
        $ids = array_values(array_unique(array_filter(array_map('intval', (array) $ids))));
        if ($ids) {
            $c  = $this->scope();
            $vb = $db->table('license_classes')->select('id')->whereIn('id', $ids);
            $c === null ? $vb->where('client_id IS NULL', null, false)
                : $vb->groupStart()->where('client_id', $c)->orWhere('client_id IS NULL', null, false)->groupEnd();
            $ids = array_map('intval', array_column($vb->get()->getResultArray(), 'id'));
        }
        $db->table('vehicle_type_license_classes')->where('vehicle_type_id', $typeId)->delete();
        foreach ($ids as $lid) {
            $db->table('vehicle_type_license_classes')->insert(['vehicle_type_id' => $typeId, 'license_class_id' => $lid]);
        }
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
        if ($db->tableExists('vehicle_type_license_classes')) {
            $db->table('vehicle_type_license_classes')->where('vehicle_type_id', (int) $id)->delete();
        }
        $db->table('vehicle_types')->where('id', (int) $id)->delete();

        return $this->json(true, 'Vehicle type deleted.');
    }
}
