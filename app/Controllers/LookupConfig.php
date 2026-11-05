<?php

namespace App\Controllers;

use App\Libraries\ConfigLookups;

/**
 * Config > Payment Type / Branch Office / Release Reason / Operating Hours / Bank Name / Sub Business Type
 * (KPK payment-info, branch-office, release-reason, operating-hours, bank-name-info, sub-categories-info).
 * Same behaviour as Config > License Class: a client admin manages its own rows, the platform admin manages
 * the shared ones (client_id NULL) that every client sees - or, on a list with client_pick (Payment Type,
 * like KPK's company filter), a client it picks. Routes: config/lookups/<list>[/schema|/save|/<id>/toggle].
 */
class LookupConfig extends BaseController
{
    private const PAGE_SIZES = [10, 20, 30, 40]; // KPK items per page

    private ?array $businessTypeCache = null;

    /** null = shared rows; otherwise the client whose rows are managed. */
    private function scope(array $d): ?int
    {
        helper(['role', 'feature']);
        if (! is_platform_superadmin()) {
            return (int) current_client_id();
        }
        $pick = (int) ($this->request->getGet('client_id') ?? $this->request->getPost('client_id') ?? 0);
        if (! empty($d['client_pick']) && $pick > 0 && \Config\Database::connect()->table('clients')->where('id', $pick)->countAllResults() > 0) {
            return $pick;
        }

        return null;
    }

    private function json(bool $ok, string $msg, array $extra = [], int $code = 200)
    {
        return $this->response->setStatusCode($code)->setJSON(['success' => $ok, 'message' => $msg] + $extra);
    }

    private function def(string $list): ?array
    {
        $d = ConfigLookups::definitions()[$list] ?? null;
        if (! $d) {
            return null;
        }
        helper(['access']);

        return has_access('config', $d['access']) ? $d : null;
    }

    private function mine(array $d, int $id): ?array
    {
        $b = \Config\Database::connect()->table($d['table'])->where('id', $id);
        $c = $this->scope($d);
        $c === null ? $b->where('client_id IS NULL', null, false) : $b->where('client_id', $c);

        return $b->get()->getRowArray() ?: null;
    }

    private function businessTypes(): array
    {
        if ($this->businessTypeCache !== null) {
            return $this->businessTypeCache;
        }
        $db = \Config\Database::connect();

        return $this->businessTypeCache = $db->tableExists('businesstype')
            ? array_column($db->table('businesstype')->select('id, business_type')->orderBy('business_type')->get()->getResultArray(), 'business_type', 'id')
            : [];
    }

    /** Config > State names (KPK Branch Office picks the state from the State list). */
    private function states(): array
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('states')) {
            return [];
        }
        $b = $db->table('states')->select('name')->orderBy('name');
        if ($db->fieldExists('status', 'states')) {
            $b->groupStart()->where('status IS NULL', null, false)->orWhere('LOWER(status) !=', 'inactive')->groupEnd();
        }

        return array_values(array_unique(array_filter(array_column($b->get()->getResultArray(), 'name'))));
    }

    /** Config > Location Visited rows marked Meeting Room (KPK getMeetingLocationVisitedList). */
    private function meetingRooms(bool $activeOnly = true): array
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('location_visited') || ! $db->fieldExists('meeting_room', 'location_visited')) {
            return [];
        }
        $b = $db->table('location_visited')->select('id, name')->where('meeting_room', 1)->orderBy('name');
        if ($activeOnly) {
            $b->where('status', 'active');
        }

        return array_column($b->get()->getResultArray(), 'name', 'id');
    }

    private function options(array $f): array
    {
        return match ($f['kind']) {
            'businesstype' => $this->businessTypes(),
            'state'        => $this->states(),
            'meetingroom'  => $this->meetingRooms(),
            default        => $f['options'] ?? [],
        };
    }

    /** Field definitions for the page (business type / state choices filled in). */
    public function schema($list)
    {
        $d = $this->def((string) $list);
        if (! $d) {
            return $this->json(false, 'Not found.', [], 404);
        }
        foreach ($d['fields'] as &$f) {
            if (in_array($f['kind'], ['businesstype', 'state', 'meetingroom'], true)) {
                $f['options'] = $this->options($f);
            }
        }
        unset($f);
        helper(['role']);
        $clients = [];
        if (! empty($d['client_pick']) && is_platform_superadmin()) {
            $clients = array_column(\Config\Database::connect()->table('clients')->select('id, name')->orderBy('name')->get()->getResultArray(), 'name', 'id');
        }

        return $this->json(true, 'ok', [
            'title' => $d['title'], 'fields' => $d['fields'], 'page_sizes' => self::PAGE_SIZES,
            'filter' => $d['filter'] ?? null, 'sort' => $d['sort'] ?? null, 'clock_toggle' => ! empty($d['clock_toggle']),
            'clients' => $clients ?: null,
        ]);
    }

    public function index($list)
    {
        $d = $this->def((string) $list);
        if (! $d) {
            return $this->json(false, 'Not found.', ['data' => [], 'total' => 0, 'pages' => 1, 'page' => 1], 404);
        }
        $db = \Config\Database::connect();
        if (! $db->tableExists($d['table'])) {
            return $this->json(false, 'Run php spark migrate first.', ['data' => [], 'total' => 0, 'pages' => 1, 'page' => 1]);
        }
        $c       = $this->scope($d);
        $q       = trim((string) $this->request->getGet('q'));
        $page    = max(1, (int) $this->request->getGet('page'));
        $perPage = in_array((int) $this->request->getGet('per_page'), self::PAGE_SIZES, true) ? (int) $this->request->getGet('per_page') : self::PAGE_SIZES[0];
        $fv      = trim((string) $this->request->getGet('filter'));
        $fcol    = $d['filter'] ?? null;
        $sortDir = ['asc' => 'ASC', 'desc' => 'DESC'][$this->request->getGet('sort')] ?? null;

        $apply = function ($b) use ($c, $q, $d, $fcol, $fv) {
            $c === null ? $b->where('client_id IS NULL', null, false)
                : $b->groupStart()->where('client_id', $c)->orWhere('client_id IS NULL', null, false)->groupEnd();
            if ($q !== '') {
                $b->groupStart()->where('1 = 0', null, false);
                foreach ($d['search'] as $col) {
                    $b->orLike($col, $q);
                }
                if (! empty($d['search_room'])) {
                    $ids = array_keys(array_filter($this->meetingRooms(false), static fn($n) => stripos((string) $n, $q) !== false));
                    if ($ids) {
                        $b->orWhereIn('location_visited_id', $ids);
                    }
                }
                $b->groupEnd();
            }
            if ($fcol && $fv !== '') {
                $b->where($fcol, $fcol === 'business_type_id' ? (int) $fv : $fv);
            }

            return $b;
        };

        $total = $apply($db->table($d['table']))->countAllResults();
        $pages = max(1, (int) ceil($total / $perPage));
        $page  = min($page, $pages);
        $b     = $apply($db->table($d['table']));
        if ($sortDir && ! empty($d['sort'])) {
            $b->orderBy((string) array_key_first($d['sort']), $sortDir);
        }
        foreach ($d['order'] as $col) {
            $col === 'day'
                ? $b->orderBy("FIELD(day, '" . implode("','", ConfigLookups::DAYS) . "')", '', false)
                : $b->orderBy($col);
        }
        $rows  = $b->orderBy('id')->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
        $types = isset($d['fields']['business_type_id']) ? $this->businessTypes() : [];
        $rooms = isset($d['fields']['location_visited_id']) ? $this->meetingRooms(false) : [];
        foreach ($rows as &$r) {
            if ($rooms || isset($d['fields']['location_visited_id'])) {
                $r['room_name'] = $rooms[$r['location_visited_id']] ?? '-';
            }
            $r['editable'] = $c === null ? $r['client_id'] === null : (int) $r['client_id'] === $c;
            if ($types) {
                $r['business_type'] = $types[$r['business_type_id']] ?? '-';
            }
            foreach (['start_time', 'end_time'] as $t) {
                if (isset($r[$t])) {
                    $r[$t] = substr((string) $r[$t], 0, 5);
                }
            }
        }

        return $this->json(true, 'ok', ['data' => $rows, 'total' => $total, 'pages' => $pages, 'page' => $page, 'per_page' => $perPage]);
    }

    public function save($list)
    {
        $d = $this->def((string) $list);
        if (! $d) {
            return $this->json(false, 'Not found.', [], 404);
        }
        $p    = $this->request->getPost();
        $id   = (int) ($p['id'] ?? 0);
        $data = [];
        foreach ($d['fields'] as $key => $f) {
            $v = trim((string) ($p[$key] ?? ''));
            if (($f['required'] ?? false) && $v === '') {
                return $this->json(false, "{$f['label']} is required.", [], 400);
            }
            switch ($f['kind']) {
                case 'select':
                    $allowed = array_is_list($f['options']) ? $f['options'] : array_keys($f['options']);
                    $v       = strtoupper($v);
                    if (! in_array($v, array_map('strval', $allowed), true)) {
                        return $this->json(false, "Choose a valid {$f['label']}.", [], 400);
                    }
                    break;
                case 'state':
                    if ($v !== '' && ! in_array($v, $this->states(), true)) {
                        return $this->json(false, "Choose a valid {$f['label']}.", [], 400);
                    }
                    $v = $v === '' ? null : $v;
                    break;
                case 'money':
                    if (! is_numeric($v) || (float) $v < 0 || (float) $v > 99999999) {
                        return $this->json(false, "{$f['label']} must be a number (0 or more).", [], 400);
                    }
                    $v = round((float) $v, 2);
                    break;
                case 'time':
                    if (! preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', substr($v, 0, 5))) {
                        return $this->json(false, "{$f['label']} must be a time (HH:MM).", [], 400);
                    }
                    $v = substr($v, 0, 5) . ':00';
                    break;
                case 'meetingroom':
                    if (! isset($this->meetingRooms()[(int) $v])) {
                        return $this->json(false, "Choose a valid {$f['label']}.", [], 400);
                    }
                    $v = (int) $v;
                    break;
                case 'date':
                    $dt = \DateTime::createFromFormat('Y-m-d', $v);
                    if (! $dt || $dt->format('Y-m-d') !== $v) {
                        return $this->json(false, "{$f['label']} must be a date.", [], 400);
                    }
                    if (! empty($f['not_past']) && ! $id && $v < date('Y-m-d')) {
                        return $this->json(false, "{$f['label']} cannot be in the past.", [], 400);
                    }
                    break;
                case 'businesstype':
                    if (! isset($this->businessTypes()[(int) $v])) {
                        return $this->json(false, "Choose a valid {$f['label']}.", [], 400);
                    }
                    $v = (int) $v;
                    break;
                default:
                    $v = $v === '' ? null : mb_substr($v, 0, $f['max'] ?? 255);
            }
            $data[$key] = $v;
        }
        if (isset($data['start_time'], $data['end_time']) && $data['end_time'] <= $data['start_time']) {
            return $this->json(false, 'End Time must be after Start Time.', [], 400);
        }

        $db    = \Config\Database::connect();
        $scope = $this->scope($d);
        $dup   = $db->table($d['table']);
        foreach ($d['unique'] as $col) {
            // case-insensitive for text; dates / times / ids compare as they are
            is_string($data[$col]) && in_array($d['fields'][$col]['kind'] ?? 'text', ['text', 'textarea', 'select', 'state'], true)
                ? $dup->where("LOWER({$col})", mb_strtolower($data[$col])) : $dup->where($col, $data[$col]);
        }
        $scope === null ? $dup->where('client_id IS NULL', null, false) : $dup->where('client_id', $scope);
        if ($id) {
            $dup->where('id !=', $id);
        }
        if ($dup->countAllResults() > 0) {
            return $this->json(false, 'This ' . $d['noun'] . ' already exists.', [], 400);
        }

        $now = date('Y-m-d H:i:s');
        if ($id) {
            if (! $this->mine($d, $id)) {
                return $this->json(false, ucfirst($d['noun']) . ' not found.', [], 404);
            }
            $db->table($d['table'])->where('id', $id)->update($data + ['updated_at' => $now]);
        } else {
            $db->table($d['table'])->insert($data + ['client_id' => $scope, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]);
        }

        return $this->json(true, ucfirst($d['noun']) . ' saved.');
    }

    /** Switch a row between Active and Inactive. */
    public function toggle($list, $id)
    {
        $d   = $this->def((string) $list);
        $row = $d ? $this->mine($d, (int) $id) : null;
        if (! $row) {
            return $this->json(false, 'Not found.', [], 404);
        }
        $new = $row['status'] === 'active' ? 'inactive' : 'active';
        \Config\Database::connect()->table($d['table'])->where('id', (int) $id)->update(['status' => $new, 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->json(true, ucfirst($d['noun']) . ' is now ' . ucfirst($new) . '.');
    }
}
