<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Admin-editable replacement for the old hardcoded LOCATION_OPTIONS const
 * that was duplicated across VendorPassRequest and VendorCardInfo (and read
 * from the former by VendorList / VendorProcessDetail). A new location
 * added through VendorLocations::create() shows up immediately on every
 * page that reads getActiveOptions() — the request form, the detail page,
 * Card Info, and Process Detail.
 */
class VendorLocationModel extends Model
{
    protected $table            = 'vendor_locations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['code', 'label', 'sort_order', 'is_active'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'code'  => 'required|max_length[50]|is_unique[vendor_locations.code,id,{id}]',
        'label' => 'required|max_length[150]',
    ];

    /**
     * code => label map of active locations, in display order. This is the
     * drop-in replacement for LOCATION_OPTIONS everywhere it was used.
     */
    public function getActiveOptions(): array
    {
        $rows = $this->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('label', 'ASC')
            ->findAll();

        $options = [];
        foreach ($rows as $row) {
            $options[$row['code']] = $row['label'];
        }

        return $options;
    }

    /** All rows (active + inactive) for the management page — inactive ones stay visible there so they can be re-enabled, just not offered as a choice elsewhere. */
    public function getAllOrdered(): array
    {
        return $this->orderBy('sort_order', 'ASC')->orderBy('label', 'ASC')->findAll();
    }

    /**
     * code => label for EVERY row, active or not. Used wherever an already
     * -saved location_access value needs to be shown back as a label (the
     * read-only detail page, Card Info) — a vendor record saved against a
     * location that's since been deactivated should still show its real
     * name instead of silently dropping it.
     */
    public function getAllLabelMap(): array
    {
        $rows = $this->orderBy('sort_order', 'ASC')->orderBy('label', 'ASC')->findAll();

        $options = [];
        foreach ($rows as $row) {
            $options[$row['code']] = $row['label'];
        }

        return $options;
    }

    /** Turns a free-typed code into a safe slug (lowercase, underscores) so it matches cleanly against stored comma-separated location_access values. */
    public static function slugifyCode(string $code): string
    {
        $slug = strtolower(trim($code));
        $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);
        return trim($slug, '_');
    }
}
