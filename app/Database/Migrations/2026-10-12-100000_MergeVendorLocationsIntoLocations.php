<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * One list of gates for visitors, staff and vendors: `locations`
 * (Config > Location Access Management). The old separate `vendor_locations`
 * list is folded in:
 *
 *  1. every vendor location that has no gate of the same name yet becomes a
 *     `locations` row (Client = its owner, or BOTH when nobody owned it);
 *  2. vendor passes stored the OLD code (e.g. kpk_gate); they are rewritten to
 *     the code of the matching gate (slug of its name, e.g. kpk_gate).
 *
 * The `vendor_locations` table itself is left in place, unused (safe rollback).
 */
class MergeVendorLocationsIntoLocations extends Migration
{
    private function slug(string $s): string
    {
        return trim((string) preg_replace('/[^a-z0-9]+/', '_', strtolower(trim($s))), '_');
    }

    public function up()
    {
        if (! $this->db->tableExists('vendor_locations') || ! $this->db->tableExists('locations')) {
            return;
        }
        $hasOwner = $this->db->fieldExists('client_id', 'locations');
        $now      = date('Y-m-d H:i:s');

        $clientNames = [];
        foreach ($this->db->table('clients')->select('id, name')->get()->getResultArray() as $c) {
            $clientNames[(int) $c['id']] = (string) $c['name'];
        }

        // gates by slug
        $bySlug = [];
        foreach ($this->db->table('locations')->select('id, location_access')->get()->getResultArray() as $l) {
            $bySlug[$this->slug((string) $l['location_access'])] ??= (string) $l['location_access'];
        }

        $map = []; // old code => new code
        foreach ($this->db->table('vendor_locations')->orderBy('id')->get()->getResultArray() as $v) {
            $label = trim((string) $v['label']);
            $new   = $this->slug($label);
            if ($new === '') {
                continue;
            }
            if (! isset($bySlug[$new])) {
                $owner = $hasOwner && ! empty($v['client_id']) ? (int) $v['client_id'] : null;
                $row   = [
                    'branch'          => $owner && isset($clientNames[$owner]) ? $clientNames[$owner] : 'BOTH',
                    'location_access' => mb_substr($label, 0, 100),
                    'status'          => (int) ($v['is_active'] ?? 1) === 1 ? 'active' : 'inactive',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
                if ($hasOwner) {
                    $row['client_id'] = $owner;
                }
                $this->db->table('locations')->insert($row);
                $bySlug[$new] = $label;
            }
            $map[(string) $v['code']] = $new;
        }

        // rewrite stored vendor passes
        if ($map && $this->db->tableExists('vendors') && $this->db->fieldExists('location_access', 'vendors')) {
            foreach ($this->db->table('vendors')->select('id, location_access')->where("location_access IS NOT NULL AND location_access <> ''", null, false)->get()->getResultArray() as $v) {
                $codes = array_values(array_filter(array_map('trim', explode(',', (string) $v['location_access'])), static fn($c) => $c !== ''));
                $new   = array_values(array_unique(array_map(static fn($c) => $map[$c] ?? $c, $codes)));
                $csv   = implode(',', $new);
                if ($csv !== (string) $v['location_access']) {
                    $this->db->table('vendors')->where('id', (int) $v['id'])->update(['location_access' => $csv]);
                }
            }
        }
    }

    public function down()
    {
        // Intentionally empty: merged gates and rewritten codes are kept.
    }
}
