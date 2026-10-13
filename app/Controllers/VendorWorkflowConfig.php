<?php

namespace App\Controllers;

use App\Libraries\VendorWorkflow;

/**
 * Vendor Workflow page: the steps of a vendor pass, who does them, their order, and which optional steps are on.
 * A client admin edits its own client's workflow; the platform admin edits the default every other client uses.
 */
class VendorWorkflowConfig extends BaseController
{
    private function scope(): int
    {
        helper(['role', 'feature']);

        return is_platform_superadmin() ? 0 : (int) current_client_id();
    }

    private function back(string $type, string $msg, string $to = 'vendor-workflow')
    {
        return redirect()->to(base_url($to))->with($type, $msg);
    }

    /** Move passes already in flight to where they belong now that a card step may be off. */
    private function settle(): void
    {
        $db  = \Config\Database::connect();
        $cid = $this->scope();
        $ids = $cid > 0 ? [$cid] : array_map('intval', array_column($db->table('clients')->select('id')->get()->getResultArray(), 'id'));
        VendorWorkflow::forget();
        VendorWorkflow::settle($db, $ids);
    }

    /** The step as the current scope sees it, or null. */
    private function find(string $key): ?array
    {
        foreach (VendorWorkflow::steps(\Config\Database::connect(), $this->scope()) as $s) {
            if ($s['key'] === $key) {
                return $s;
            }
        }

        return null;
    }

    private function save(array $step, array $data): void
    {
        $db  = \Config\Database::connect();
        $cid = $this->scope();
        $now = date('Y-m-d H:i:s');
        $row = $db->table('vendor_workflow_settings')->where('client_id', $cid)->where('step_key', $step['key'])->get()->getRowArray();
        if ($row) {
            $db->table('vendor_workflow_settings')->where('id', $row['id'])->update($data + ['updated_at' => $now]);
        } else {
            $ins = $data + ['client_id' => $cid, 'step_key' => $step['key'], 'is_active' => $step['is_active'] ? 1 : 0, 'created_at' => $now, 'updated_at' => $now];
            if ($db->fieldExists('is_custom', 'vendor_workflow_settings')) { // columns from the Edit Sequence migration
                $ins += ['is_custom' => $step['is_custom'] ? 1 : 0, 'owner' => $step['is_custom'] ? $step['owner'] : null];
            }
            $db->table('vendor_workflow_settings')->insert($ins);
        }
        VendorWorkflow::forget();
    }

    public function index()
    {
        $db  = \Config\Database::connect();
        $cid = $this->scope();

        return view('vendors/workflow', [
            'pageTitle' => 'Vendor Workflow Management - SafeG',
            'steps'     => VendorWorkflow::steps($db, $cid),
            'platform'  => $cid === 0,
            'tableOk'   => $db->tableExists('vendor_workflow_settings') && $db->fieldExists('sort_order', 'vendor_workflow_settings'),
        ]);
    }

    /** Edit Sequence page: drag the active steps, add custom steps. */
    public function create()
    {
        $steps = VendorWorkflow::steps(\Config\Database::connect(), $this->scope());

        return view('vendors/workflow_sequence', [
            'pageTitle' => 'Edit Vendor Process Sequence - SafeG',
            'steps'     => array_values(array_filter($steps, fn ($s) => $s['is_active'])),
        ]);
    }

    public function saveSequence()
    {
        $db    = \Config\Database::connect();
        $cid   = $this->scope();
        $order = json_decode((string) $this->request->getPost('steps_json'), true);
        $new   = json_decode((string) $this->request->getPost('custom_steps_json'), true);
        if (! is_array($order) || ! $db->fieldExists('sort_order', 'vendor_workflow_settings')) {
            return $this->back('error', 'The sequence could not be saved. Run php spark migrate first.', 'vendor-workflow/create');
        }
        $all = VendorWorkflow::steps($db, $cid);
        $byKey = array_column($all, null, 'key');

        // new custom steps get a real key now
        $map = [];
        foreach ((array) $new as $c) {
            $name = trim((string) ($c['label'] ?? ''));
            $tmp  = (string) ($c['key'] ?? '');
            if ($name === '' || $tmp === '') {
                continue;
            }
            $owner = in_array($c['owner'] ?? '', VendorWorkflow::OWNERS, true) ? $c['owner'] : 'Staff';
            $key   = 'custom_' . bin2hex(random_bytes(4));
            $map[$tmp] = $key;
            $byKey[$key] = ['key' => $key, 'is_active' => true, 'is_custom' => true, 'owner' => $owner];
            $this->save($byKey[$key], ['step_name' => mb_substr($name, 0, 100), 'trigger_event' => mb_substr(trim((string) ($c['trigger_event'] ?? '')), 0, 255), 'is_active' => 1, 'owner' => $owner]);
        }

        $keys = [];
        foreach ($order as $k) {
            $k = $map[$k] ?? $k;
            if (isset($byKey[$k]) && ! in_array($k, $keys, true)) {
                $keys[] = $k;
            }
        }
        // Steps that are off keep the exact slot they had, so turning one back on puts it where it was.
        foreach ($all as $pos => $s) {
            if (! $s['is_active'] && ! in_array($s['key'], $keys, true)) {
                array_splice($keys, min($pos, count($keys)), 0, [$s['key']]);
            }
        }
        foreach ($keys as $i => $k) {
            $this->save($byKey[$k], ['sort_order' => $i + 1]);
        }

        return $this->back('success', 'Vendor workflow sequence saved.');
    }

    public function toggle($key)
    {
        $s = $this->find((string) $key);
        if (! $s) {
            return $this->back('error', 'Unknown step.');
        }
        $this->save($s, ['is_active' => $s['is_active'] ? 0 : 1]);
        $this->settle();

        return $this->back('success', $s['name'] . ($s['is_active'] ? ' turned off.' : ' turned on.'));
    }

    public function edit($key)
    {
        $s    = $this->find((string) $key);
        $name = trim((string) $this->request->getPost('step_name'));
        $trig = trim((string) $this->request->getPost('trigger_event'));
        if (! $s) {
            return $this->back('error', 'Unknown step.');
        }
        if ($name === '') {
            return $this->back('error', 'The step needs a name.');
        }
        $data = ['step_name' => mb_substr($name, 0, 100), 'trigger_event' => mb_substr($trig, 0, 255)];
        $owner = (string) $this->request->getPost('owner');
        if ($s['is_custom'] && in_array($owner, VendorWorkflow::OWNERS, true)) {
            $data['owner'] = $owner;
        }
        $this->save($s, $data);

        return $this->back('success', 'Step updated.');
    }

    /** Only custom steps can be deleted (the built-in ones are the real flow; turn optional ones off instead). */
    public function delete($key)
    {
        $db  = \Config\Database::connect();
        $row = $db->table('vendor_workflow_settings')->where('client_id', $this->scope())->where('step_key', (string) $key)->where('is_custom', 1)->get()->getRowArray();
        if (! $row) {
            return $this->back('error', 'Only a custom step added for this workflow can be deleted.');
        }
        $db->table('vendor_workflow_settings')->where('id', $row['id'])->delete();
        VendorWorkflow::forget();

        return $this->back('success', 'Step deleted.');
    }

    public function reset()
    {
        \Config\Database::connect()->table('vendor_workflow_settings')->where('client_id', $this->scope())->delete();
        VendorWorkflow::forget();
        $this->settle();

        return $this->back('success', 'Vendor workflow reset to the default.');
    }
}
