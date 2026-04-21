<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;

class BlacklistReasonCreate extends BaseController
{
    public function save()
    {
        $db = Database::connect();

        $id     = $this->request->getPost('id');
        $reason = trim($this->request->getPost('reason'));
        $type   = $this->request->getPost('type');
        $status = $this->request->getPost('status');

        if (empty($reason)) {
            return redirect()->to(base_url('config'))->with('error', 'Reason is required.');
        }

        $now = date('Y-m-d H:i:s');

        if (!empty($id)) {
            $db->table('blacklistreason')->where('id', $id)->update([
                'reason'     => $reason,
                'type'       => $type,
                'status'     => $status,
                'updated_at' => $now,
            ]);
        } else {
            $db->table('blacklistreason')->insert([
                'reason'     => $reason,
                'type'       => $type,
                'status'     => $status,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return redirect()->to(base_url('config'))->with('success', 'Blacklist reason saved successfully.');
    }
}