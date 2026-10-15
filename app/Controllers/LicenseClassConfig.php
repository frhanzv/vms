<?php

namespace App\Controllers;

/**
 * Config > License Class. Same fields as KPK: License Class Name, Minimum Age, Status.
 * A client admin manages its own classes; the platform admin manages the shared ones (client_id NULL)
 * that every client sees.
 */
class LicenseClassConfig extends BaseController
{
    private const PER_PAGE = 10;

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
        $b = \Config\Database::connect()->table('license_classes')->where('id', $id);
        $c = $this->scope();
        $c === null ? $b->where('client_id IS NULL', null, false) : $b->where('client_id', $c);

        return $b->get()->getRowArray() ?: null;
    }

    public function index()
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('license_classes')) {
            return $this->json(false, 'Run php spark migrate first.', ['data' => [], 'total' => 0, 'pages' => 1, 'page' => 1]);
        }
        $c = $this->scope();
        $q = trim((string) $this->request->getGet('q'));
        $page = max(1, (int) $this->request->getGet('page'));

        $apply = function ($b) use ($c, $q) {
            $c === null ? $b->where('client_id IS NULL', null, false)
                : $b->groupStart()->where('client_id', $c)->orWhere('client_id IS NULL', null, false)->groupEnd();
            if ($q !== '') {
                $b->like('name', $q);
            }

            return $b;
        };

        $total = $apply($db->table('license_classes'))->countAllResults();
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page  = min($page, $pages);
        $rows  = $apply($db->table('license_classes'))->orderBy('name')->orderBy('id')->limit(self::PER_PAGE, ($page - 1) * self::PER_PAGE)->get()->getResultArray();
        foreach ($rows as &$r) {
            $r['editable'] = $c === null ? $r['client_id'] === null : (int) $r['client_id'] === $c;
        }

        return $this->json(true, 'ok', ['data' => $rows, 'total' => $total, 'pages' => $pages, 'page' => $page]);
    }

    public function save()
    {
        $p    = $this->request->getPost();
        $id   = (int) ($p['id'] ?? 0);
        $name = trim((string) ($p['name'] ?? ''));
        $age  = trim((string) ($p['min_age'] ?? ''));
        if ($name === '') {
            return $this->json(false, 'License Class Name is required.', [], 400);
        }
        if ($age === '' || ! ctype_digit($age) || (int) $age > 120) {
            return $this->json(false, 'Minimum Age is required (a number).', [], 400);
        }
        $db = \Config\Database::connect();

        $dup = $db->table('license_classes')->where('LOWER(name)', strtolower($name));
        $this->scope() === null ? $dup->where('client_id IS NULL', null, false) : $dup->where('client_id', $this->scope());
        if ($id) {
            $dup->where('id !=', $id);
        }
        if ($dup->countAllResults() > 0) {
            return $this->json(false, 'This license class already exists.', [], 400);
        }

        $data = ['name' => mb_substr($name, 0, 50), 'min_age' => (int) $age, 'updated_at' => date('Y-m-d H:i:s')];
        if ($id) {
            if (! $this->mine($id)) {
                return $this->json(false, 'License class not found.', [], 404);
            }
            $db->table('license_classes')->where('id', $id)->update($data);
        } else {
            $db->table('license_classes')->insert($data + ['client_id' => $this->scope(), 'status' => 'active', 'created_at' => date('Y-m-d H:i:s')]);
        }

        return $this->json(true, 'License class saved.');
    }

    /** Switch a class between Active and Inactive. */
    public function toggle($id)
    {
        $row = $this->mine((int) $id);
        if (! $row) {
            return $this->json(false, 'License class not found.', [], 404);
        }
        $new = $row['status'] === 'active' ? 'inactive' : 'active';
        \Config\Database::connect()->table('license_classes')->where('id', (int) $id)->update(['status' => $new, 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->json(true, 'License class is now ' . ucfirst($new) . '.');
    }
}
