<?php

namespace App\Controllers;

use App\Libraries\CardTemplates;

/**
 * Config > Card Template Management: add / use / delete the artwork cards are printed on.
 * A client admin manages its own client's templates; the platform admin manages the global ones
 * (used by every client that has not uploaded its own).
 */
class CardTemplateConfig extends BaseController
{
    private function scopeClient(): ?int
    {
        helper(['role', 'feature']);

        return is_platform_superadmin() ? null : (int) current_client_id();
    }

    private function json(bool $ok, string $msg, array $extra = [], int $code = 200)
    {
        return $this->response->setStatusCode($code)->setJSON(['success' => $ok, 'message' => $msg] + $extra);
    }

    public function index()
    {
        $db       = \Config\Database::connect();
        $clientId = $this->scopeClient();
        $rows     = CardTemplates::rows($db, $clientId);

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'id' => (int) $r['id'], 'module' => $r['module'], 'slot' => $r['slot'], 'name' => $r['name'],
                'url' => CardTemplates::urlOf($r['file_path']), 'is_active' => (int) $r['is_active'] === 1, 'created_at' => $r['created_at'],
            ];
        }

        $defaults = [];
        foreach (CardTemplates::SLOTS as $module => $slots) {
            foreach ($slots as $slot => $label) {
                $defaults[] = ['module' => $module, 'slot' => $slot, 'label' => $label, 'url' => CardTemplates::defaultUrl($module, $slot)];
            }
        }

        return $this->json(true, 'ok', [
            'data' => $data, 'defaults' => $defaults, 'modules' => CardTemplates::MODULES, 'slots' => CardTemplates::SLOTS,
        ]);
    }

    public function upload()
    {
        $module = (string) $this->request->getPost('module');
        $slot   = (string) $this->request->getPost('slot');
        $name   = trim((string) $this->request->getPost('name'));
        if (! CardTemplates::isValid($module, $slot)) {
            return $this->json(false, 'Choose the pass type and which card side this image is for.', [], 400);
        }
        if ($name === '') {
            return $this->json(false, 'Give the template a name.', [], 400);
        }

        $file = $this->request->getFile('image');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $this->json(false, 'Choose an image file to upload.', [], 400);
        }
        if (! in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return $this->json(false, 'Only JPG, PNG or WEBP images are allowed.', [], 400);
        }
        if ($file->getSize() > 5 * 1024 * 1024) {
            return $this->json(false, 'The image is too big (maximum 5 MB).', [], 400);
        }
        $size = @getimagesize($file->getTempName());
        if (! $size) {
            return $this->json(false, 'That file is not a valid image.', [], 400);
        }
        // CR-80 portrait card (like 459 x 729): width / height about 0.6 - 0.7
        $ratio = $size[0] / max(1, $size[1]);
        if ($ratio < 0.55 || $ratio > 0.75) {
            return $this->json(false, 'The card image must be portrait, like 459 x 729 pixels (yours is ' . $size[0] . ' x ' . $size[1] . ').', [], 400);
        }

        $dir = FCPATH . 'assets/uploads/card_templates';
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $newName = $file->getRandomName();
        $file->move($dir, $newName);

        $db       = \Config\Database::connect();
        $clientId = $this->scopeClient();
        $this->deactivateSlot($db, $module, $slot, $clientId);
        $now = date('Y-m-d H:i:s');
        $db->table('card_templates')->insert([
            'client_id' => $clientId, 'module' => $module, 'slot' => $slot, 'name' => mb_substr($name, 0, 120),
            'file_path' => 'assets/uploads/card_templates/' . $newName, 'is_active' => 1,
            'created_by' => (string) (session()->get('full_name') ?: session()->get('username')), 'created_at' => $now, 'updated_at' => $now,
        ]);

        return $this->json(true, 'Template added and now in use for ' . CardTemplates::SLOTS[$module][$slot] . '.');
    }

    public function activate($id)
    {
        $db  = \Config\Database::connect();
        $row = $this->ownRow($db, (int) $id);
        if (! $row) {
            return $this->json(false, 'Template not found.', [], 404);
        }
        $this->deactivateSlot($db, $row['module'], $row['slot'], $row['client_id'] ? (int) $row['client_id'] : null);
        $db->table('card_templates')->where('id', (int) $id)->update(['is_active' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->json(true, 'Template is now in use.');
    }

    /** Stop using the uploaded image of this slot and go back to the built-in one. */
    public function useDefault()
    {
        $module = (string) $this->request->getPost('module');
        $slot   = (string) $this->request->getPost('slot');
        if (! CardTemplates::isValid($module, $slot)) {
            return $this->json(false, 'Unknown template slot.', [], 400);
        }
        $this->deactivateSlot(\Config\Database::connect(), $module, $slot, $this->scopeClient());

        return $this->json(true, 'Back to the built-in template.');
    }

    public function delete($id)
    {
        $db  = \Config\Database::connect();
        $row = $this->ownRow($db, (int) $id);
        if (! $row) {
            return $this->json(false, 'Template not found.', [], 404);
        }
        $db->table('card_templates')->where('id', (int) $id)->delete();
        // remove the file only when nothing else uses it
        if ($db->table('card_templates')->where('file_path', $row['file_path'])->countAllResults() === 0) {
            $abs = FCPATH . ltrim($row['file_path'], '/');
            if (str_starts_with(str_replace('\\', '/', $row['file_path']), 'assets/uploads/card_templates/') && is_file($abs)) {
                @unlink($abs);
            }
        }

        return $this->json(true, 'Template deleted.' . ((int) $row['is_active'] === 1 ? ' That slot now prints on the built-in template.' : ''));
    }

    private function ownRow($db, int $id): ?array
    {
        $b = $db->table('card_templates')->where('id', $id);
        $clientId = $this->scopeClient();
        $clientId ? $b->where('client_id', $clientId) : $b->where('client_id IS NULL', null, false);

        return $b->get()->getRowArray() ?: null;
    }

    private function deactivateSlot($db, string $module, string $slot, ?int $clientId): void
    {
        $b = $db->table('card_templates')->where('module', $module)->where('slot', $slot);
        $clientId ? $b->where('client_id', $clientId) : $b->where('client_id IS NULL', null, false);
        $b->update(['is_active' => 0]);
    }
}
