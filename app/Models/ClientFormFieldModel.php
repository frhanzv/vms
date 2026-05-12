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
            'staff_pass_request'   => 'Staff Pass Request',
        ];
    }

    public static function staffPassFields(): array
    {
        return [
            ['field_key' => 'type_of_application', 'label' => 'Type Of Application'],
            ['field_key' => 'designation',          'label' => 'Designation'],
            ['field_key' => 'resident',             'label' => 'Resident'],
            ['field_key' => 'sub_type',             'label' => 'Sub Type'],
            ['field_key' => 'location_access',      'label' => 'Location Access'],
            ['field_key' => 'ic_number',            'label' => 'IC / Passport Number'],
            ['field_key' => 'date_of_birth',        'label' => 'Date of Birth'],
            ['field_key' => 'sex',                  'label' => 'Sex'],
            ['field_key' => 'full_name',            'label' => 'Full Name'],
            ['field_key' => 'name_on_staff_pass',   'label' => 'Name On Staff Pass'],
            ['field_key' => 'staff_no',             'label' => 'Staff No'],
            ['field_key' => 'contact_number',       'label' => 'Contact Number'],
            ['field_key' => 'email',                'label' => 'Email Address'],
            ['field_key' => 'department',           'label' => 'Department'],
            ['field_key' => 'address_1',            'label' => 'Address 1'],
            ['field_key' => 'address_2',            'label' => 'Address 2'],
            ['field_key' => 'address_3',            'label' => 'Address 3'],
            ['field_key' => 'country',              'label' => 'Country'],
            ['field_key' => 'state',                'label' => 'State'],
            ['field_key' => 'city',                 'label' => 'City'],
            ['field_key' => 'postal_code',          'label' => 'Postal Code'],
            ['field_key' => 'driving_license',      'label' => 'Driving License Section'],
            ['field_key' => 'document_upload',      'label' => 'Document Upload Section'],
            ['field_key' => 'print_button',         'label' => 'Show Print Button (Staff List)', 'default_enabled' => false],
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
                'is_enabled' => isset($stored[$key]) ? (int) $stored[$key] : (isset($def['default_enabled']) ? (int) $def['default_enabled'] : 1),
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

        if ($row !== null) {
            return (bool) $row['is_enabled'];
        }

        foreach ($this->getDefinitions($formType) as $def) {
            if ($def['field_key'] === $fieldKey) {
                return $def['default_enabled'] ?? true;
            }
        }

        return true;
    }

    protected function getDefinitions(string $formType): array
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

        if ($formType === 'staff_pass_request') {
            return self::staffPassFields();
        }

        return [];
    }
}
