<?php

namespace App\Controllers;

use App\Libraries\DashboardPicture;

/**
 * Config > Dashboard Upload (KPK dashboard-upload): upload the picture shown on the dashboard.
 * Only one picture is in use per scope: saving a new one switches the older ones to Inactive.
 * A client admin manages its own picture; the platform admin manages the shared one.
 * Routes: config/dashboard-upload[/upload|/(:num)/toggle|/(:num)/delete].
 */
class DashboardUploadConfig extends BaseController
{
    private const MAX_BYTES = 5 * 1024 * 1024;
    private const TYPES     = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif'];

    private function scope(): ?int
    {
        helper(['role', 'feature']);

        return is_platform_superadmin() ? null : (int) current_client_id();
    }

    private function json(bool $ok, string $msg, array $extra = [], int $code = 200)
    {
        return $this->response->setStatusCode($code)->setJSON(['success' => $ok, 'message' => $msg] + $extra);
    }

    private function guard(): ?\CodeIgniter\HTTP\ResponseInterface
    {
        helper(['access']);
        if (! has_access('config', 'dashboard_upload')) {
            return $this->json(false, 'Not allowed.', [], 403);
        }
        if (! \Config\Database::connect()->tableExists('dashboard_uploads')) {
            return $this->json(false, 'Run php spark migrate first.', [], 400);
        }

        return null;
    }

    private function scoped($b)
    {
        $c = $this->scope();

        return $c === null ? $b->where('client_id IS NULL', null, false) : $b->where('client_id', $c);
    }

    private function mine(int $id): ?array
    {
        return $this->scoped(\Config\Database::connect()->table('dashboard_uploads')->where('id', $id))->get()->getRowArray() ?: null;
    }

    public function index()
    {
        if ($r = $this->guard()) {
            return $r;
        }
        $db   = \Config\Database::connect();
        $rows = $this->scoped($db->table('dashboard_uploads'))->orderBy('id', 'DESC')->get()->getResultArray();
        $names = array_column($db->table('users')->select('id, username')->whereIn('id', array_filter(array_column($rows, 'uploaded_by')) ?: [0])->get()->getResultArray(), 'username', 'id');
        $data = array_map(static fn($r) => [
            'id' => (int) $r['id'], 'name' => $r['name'], 'url' => base_url($r['file_path']), 'status' => $r['status'],
            'uploaded_by' => $names[$r['uploaded_by']] ?? '-', 'created_at' => $r['created_at'],
        ], $rows);

        return $this->json(true, 'ok', ['data' => $data, 'shown' => DashboardPicture::url($this->scope())]);
    }

    public function upload()
    {
        if ($r = $this->guard()) {
            return $r;
        }
        $file = $this->request->getFile('picture');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $this->json(false, 'Choose a picture to upload.', [], 400);
        }
        $ext = strtolower($file->getClientExtension() ?: $file->getExtension());
        if (! isset(self::TYPES[$ext]) || ! @getimagesize($file->getTempName())) {
            return $this->json(false, 'Only JPG, PNG, WEBP or GIF pictures are allowed.', [], 400);
        }
        if ($file->getSize() > self::MAX_BYTES) {
            return $this->json(false, 'The picture is too big (maximum 5 MB).', [], 400);
        }
        $dir = FCPATH . DashboardPicture::DIR;
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $name = 'dash_' . ($this->scope() ?? 0) . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (! copy($file->getTempName(), $dir . '/' . $name)) {
            return $this->json(false, 'Could not save the picture.', [], 500);
        }

        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        $db->transStart();
        $this->scoped($db->table('dashboard_uploads'))->where('status', 'active')->update(['status' => 'inactive', 'updated_at' => $now]);
        $db->table('dashboard_uploads')->insert([
            'client_id' => $this->scope(), 'name' => mb_substr($file->getClientName(), 0, 200), 'file_path' => DashboardPicture::DIR . '/' . $name,
            'mime' => self::TYPES[$ext], 'status' => 'active', 'uploaded_by' => (int) session()->get('user_id') ?: null, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $db->transComplete();

        return $this->json(true, 'Dashboard picture saved. It now shows on the dashboard.');
    }

    /** Use this picture (the others switch off), or switch it off. */
    public function toggle($id)
    {
        if ($r = $this->guard()) {
            return $r;
        }
        $row = $this->mine((int) $id);
        if (! $row) {
            return $this->json(false, 'Picture not found.', [], 404);
        }
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        if ($row['status'] === 'active') {
            $db->table('dashboard_uploads')->where('id', $row['id'])->update(['status' => 'inactive', 'updated_at' => $now]);

            return $this->json(true, 'Picture switched off. The dashboard shows no picture.');
        }
        $this->scoped($db->table('dashboard_uploads'))->where('status', 'active')->update(['status' => 'inactive', 'updated_at' => $now]);
        $db->table('dashboard_uploads')->where('id', $row['id'])->update(['status' => 'active', 'updated_at' => $now]);

        return $this->json(true, 'Picture now shows on the dashboard.');
    }

    public function delete($id)
    {
        if ($r = $this->guard()) {
            return $r;
        }
        $row = $this->mine((int) $id);
        if (! $row) {
            return $this->json(false, 'Picture not found.', [], 404);
        }
        \Config\Database::connect()->table('dashboard_uploads')->where('id', $row['id'])->delete();
        $path = FCPATH . $row['file_path'];
        if (str_starts_with((string) $row['file_path'], DashboardPicture::DIR . '/') && is_file($path)) {
            @unlink($path);
        }

        return $this->json(true, 'Picture deleted.');
    }
}
