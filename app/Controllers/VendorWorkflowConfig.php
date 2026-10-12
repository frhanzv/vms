<?php

namespace App\Controllers;

use App\Libraries\VendorWorkflow;

/**
 * Vendor Workflow page: the steps of a vendor pass, who does them, and which optional steps are on.
 * A client admin edits its own client's workflow; the platform admin edits the default every other client uses.
 */
class VendorWorkflowConfig extends BaseController
{
    private function scope(): int
    {
        helper(['role', 'feature']);

        return is_platform_superadmin() ? 0 : (int) current_client_id();
    }

    private function save(string $key, array $data)
    {
        $db  = \Config\Database::connect();
        $cid = $this->scope();
        if (! isset(VendorWorkflow::STEPS[$key])) {
            return redirect()->to(base_url('vendor-workflow'))->with('error', 'Unknown step.');
        }
        $now = date('Y-m-d H:i:s');
        $row = $db->table('vendor_workflow_settings')->where('client_id', $cid)->where('step_key', $key)->get()->getRowArray();
        if ($row) {
            $db->table('vendor_workflow_settings')->where('id', $row['id'])->update($data + ['updated_at' => $now]);
        } else {
            $db->table('vendor_workflow_settings')->insert($data + ['client_id' => $cid, 'step_key' => $key, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now]);
        }
        VendorWorkflow::forget();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $cid = $this->scope();

        return view('vendors/workflow', [
            'pageTitle' => 'Vendor Workflow Management - SafeG',
            'steps'     => VendorWorkflow::steps($db, $cid),
            'platform'  => $cid === 0,
            'tableOk'   => $db->tableExists('vendor_workflow_settings'),
        ]);
    }

    public function toggle($key)
    {
        if (empty(VendorWorkflow::STEPS[$key]) || VendorWorkflow::STEPS[$key][3]) {
            return redirect()->to(base_url('vendor-workflow'))->with('error', 'This step is required and cannot be turned off.');
        }
        $on = VendorWorkflow::enabled(\Config\Database::connect(), $this->scope(), $key);
        $this->save($key, ['is_active' => $on ? 0 : 1]);

        return redirect()->to(base_url('vendor-workflow'))->with('success', VendorWorkflow::STEPS[$key][0] . ($on ? ' turned off.' : ' turned on.'));
    }

    public function edit($key)
    {
        $name = trim((string) $this->request->getPost('step_name'));
        $trig = trim((string) $this->request->getPost('trigger_event'));
        if ($name === '') {
            return redirect()->to(base_url('vendor-workflow'))->with('error', 'The step needs a name.');
        }
        $this->save($key, ['step_name' => mb_substr($name, 0, 100), 'trigger_event' => mb_substr($trig, 0, 255)]);

        return redirect()->to(base_url('vendor-workflow'))->with('success', 'Step updated.');
    }

    public function reset()
    {
        \Config\Database::connect()->table('vendor_workflow_settings')->where('client_id', $this->scope())->delete();
        VendorWorkflow::forget();

        return redirect()->to(base_url('vendor-workflow'))->with('success', 'Vendor workflow reset to the default.');
    }
}
