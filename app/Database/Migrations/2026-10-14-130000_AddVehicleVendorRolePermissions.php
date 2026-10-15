<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Role Management gets the checkboxes the Vehicle and Vendor pages now check:
 *   vehicle.show_hide_columns / new_issued_pass / sticker_tools / terminate_pass
 *   vendor_pass_list.show_hide_columns
 *   vendor_workflow.view (Vendor Workflow menu and pages)
 *   config.vehicle_type (Config > Vehicle Types)
 *
 * So nobody loses what they could already do, existing roles are given the new boxes
 * where they already had the matching access: Closed List roles get the issued-pass
 * and sticker tools, Vendor Pass List roles get Show / Hide Columns, Admin and Client
 * Superadmin keep Vendor Workflow, and roles with Page Settings or Dynamic Form Fields
 * keep Vehicle Types. A box already saved either way is left alone. Roles with an empty
 * access JSON already have full access and are untouched.
 */
class AddVehicleVendorRolePermissions extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('roles') || ! $this->db->fieldExists('access', 'roles')) {
            return;
        }
        helper('role');

        foreach ($this->db->table('roles')->select('id, name, access')->get()->getResultArray() as $role) {
            $access = json_decode((string) ($role['access'] ?? ''), true);
            if (! is_array($access) || empty($access)) {
                continue;
            }
            $before = $access;
            $slug   = function_exists('normalize_role_slug') ? normalize_role_slug((string) $role['name']) : strtolower((string) $role['name']);
            $give   = static function (array &$a, string $module, string $key): void {
                if (! isset($a[$module]) || ! is_array($a[$module])) {
                    $a[$module] = [];
                }
                if (! array_key_exists($key, $a[$module])) {
                    $a[$module][$key] = true;
                }
            };

            $vehicle = (array) ($access['vehicle'] ?? []);
            if (in_array(true, $vehicle, true)) {
                $give($access, 'vehicle', 'show_hide_columns');
            }
            if (! empty($vehicle['public_closed_list']) || ! empty($vehicle['cargo_closed_list'])) {
                foreach (['new_issued_pass', 'sticker_tools', 'terminate_pass'] as $k) {
                    $give($access, 'vehicle', $k);
                }
            }
            if (in_array(true, (array) ($access['vendor_pass_list'] ?? []), true)) {
                $give($access, 'vendor_pass_list', 'show_hide_columns');
            }
            if (in_array($slug, ['admin', 'clientsuperadmin'], true)) {
                $give($access, 'vendor_workflow', 'view');
            }
            $config = (array) ($access['config'] ?? []);
            if (! empty($config['page_settings']) || ! empty($config['dynamic_form_fields'])) {
                $give($access, 'config', 'vehicle_type');
            }

            if ($access !== $before) {
                $this->db->table('roles')->where('id', $role['id'])->update(['access' => json_encode($access)]);
            }
        }
    }

    public function down()
    {
        // Left in place: removing the keys would take access away from roles.
    }
}
