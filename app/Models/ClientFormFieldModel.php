<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientFormFieldModel extends Model
{
    protected $table            = 'client_form_fields';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['company_id', 'form_type', 'field_key', 'is_enabled'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public static function formTypes(): array
    {
        return [
            'visitor_registration' => 'Visitor Registration',
            'invitation'           => 'Invitation Form',
        ];
    }

    // Static field definitions for the Invitation form.
    // Visitor Registration fields are pulled live from email_template_form_fields (is_system=1).
    public static function invitationFields(): array
    {
        return [
            ['field_key' => 'staff_id',          'label' => 'Staff ID (Person Visited)'],
            ['field_key' => 'visitor_type',       'label' => 'Visitor Type'],
            ['field_key' => 'company_visited',    'label' => 'Company Being Visited'],
            ['field_key' => 'contact_person',     'label' => 'Contact Person No.'],
            ['field_key' => 'link_expiry',        'label' => 'Invitation Link Expiry'],
            ['field_key' => 'reason',             'label' => 'Reason for Visit'],
            ['field_key' => 'location',           'label' => 'Location / Venue'],
            ['field_key' => 'allow_sub_invites',  'label' => 'Allow Sub-invitations'],
            ['field_key' => 'visitor_full_name',  'label' => 'Visitor Full Name'],
            ['field_key' => 'visitor_contact',    'label' => 'Visitor Contact Number'],
            ['field_key' => 'visitor_email',      'label' => 'Visitor Email'],
            ['field_key' => 'schedule',           'label' => 'Visit Schedule (Date & Time)'],
        ];
    }

    /**
     * Returns all field definitions for a form type with per-client enabled state applied.
     * Absence of a record means the field defaults to enabled.
     */
    public function getForCompanyForm(int $companyId, string $formType): array
    {
        $definitions = $this->getDefinitions($formType);
        if (empty($definitions)) {
            return [];
        }

        $rows   = $this->where('company_id', $companyId)->where('form_type', $formType)->findAll();
        $stored = array_column($rows, 'is_enabled', 'field_key');

        $result = [];
        foreach ($definitions as $def) {
            $key      = $def['field_key'];
            $result[] = [
                'field_key'  => $key,
                'label'      => $def['label'],
                'is_enabled' => isset($stored[$key]) ? (int) $stored[$key] : 1,
            ];
        }
        return $result;
    }

    /**
     * Upserts field flags for a company+form. Only writes rows that are disabled
     * or already exist — absence means enabled.
     */
    public function saveForCompanyForm(int $companyId, string $formType, array $fields): void
    {
        foreach ($fields as $key => $enabled) {
            $enabled  = $enabled ? 1 : 0;
            $existing = $this->where('company_id', $companyId)
                             ->where('form_type', $formType)
                             ->where('field_key', $key)
                             ->first();

            if ($existing) {
                $this->update($existing['id'], ['is_enabled' => $enabled]);
            } elseif ($enabled === 0) {
                $this->insert([
                    'company_id' => $companyId,
                    'form_type'  => $formType,
                    'field_key'  => $key,
                    'is_enabled' => 0,
                ]);
            }
        }
    }

    /**
     * Check if a field is enabled for a company+form.
     * Returns true when no record exists (default on).
     */
    public function isEnabled(int $companyId, string $formType, string $fieldKey): bool
    {
        $row = $this->where('company_id', $companyId)
                    ->where('form_type', $formType)
                    ->where('field_key', $fieldKey)
                    ->first();

        return $row === null || (bool) $row['is_enabled'];
    }

    private function getDefinitions(string $formType): array
    {
        if ($formType === 'invitation') {
            return self::invitationFields();
        }

        if ($formType === 'visitor_registration') {
            $rows = (new EmailTemplateFormFieldModel())
                ->where('is_system', 1)
                ->orderBy('sort_order', 'ASC')
                ->findAll();

            return array_map(fn($r) => [
                'field_key' => $r['field_key'],
                'label'     => $r['label'],
            ], $rows);
        }

        return [];
    }
}
