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
    protected $allowedFields    = ['client_id', 'form_type', 'field_key', 'is_enabled'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public static function formTypes(): array
    {
        return [
            'visitor_registration'  => 'Visitor Registration',
            'invitation'            => 'Invitation Form',
            'staff_pass_request'    => 'Staff Pass Request',
            'visitor_pass_request'  => 'Visitor Pass Request',
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
            ['field_key' => 'csp_number',           'label' => 'CSP Number & Expiry Date',       'default_enabled' => false],
            ['field_key' => 'evetting',             'label' => 'E-Vetting Section',              'default_enabled' => false],
            ['field_key' => 'print_button',         'label' => 'Show Print Button (Staff List)', 'default_enabled' => false],
        ];
    }

    // Static field definitions for the Invitation form.
    // Shared visit-context fields also respect visitor_registration toggles (see getInvitationFormConfig).
    public static function invitationFields(): array
    {
        return [
            ['field_key' => 'staff_id',          'label' => 'Staff ID Of Person Visited'],
            ['field_key' => 'visitor_type',       'label' => 'Visitor Type'],
            ['field_key' => 'company_visited',    'label' => 'Name Of Company Visited'],
            ['field_key' => 'host_contact',      'label' => 'Contact No Of Person Visited'],
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
     * Resolved invitation form toggles for Create Invitation — merges invitation + visitor_registration.
     *
     * @return array<string, bool> field_key => is_enabled
     */
    public function getInvitationFormConfig(int $clientId): array
    {
        $config = [];
        foreach (self::invitationFields() as $def) {
            $key = $def['field_key'];
            $config[$key] = $this->isInvitationFieldEnabled($clientId, $key);
        }

        // Backward compatibility for legacy invitation rows / views using contact_person.
        $config['contact_person'] = $config['host_contact'];

        return $config;
    }

    protected function isInvitationFieldEnabled(int $clientId, string $key): bool
    {
        $invitationKeys = [$key];
        if ($key === 'host_contact') {
            $invitationKeys[] = 'contact_person';
        }

        foreach ($invitationKeys as $invKey) {
            if (! $this->isEnabled($clientId, 'invitation', $invKey)) {
                return false;
            }
        }

        foreach ($this->visitorRegistrationKeysForInvitation($key) as $vrKey) {
            if (! $this->isEnabled($clientId, 'visitor_registration', $vrKey)) {
                return false;
            }
        }

        return true;
    }

    /** @return list<string> */
    protected function visitorRegistrationKeysForInvitation(string $invitationKey): array
    {
        return match ($invitationKey) {
            'host_contact', 'contact_person' => ['host_contact'],
            'company_visited'                => ['company_visited'],
            'staff_id'                       => ['staff_id'],
            'reason'                         => ['visit_reason'],
            default                          => [],
        };
    }

    public static function visitorPassRequestFields(): array
    {
        return [
            ['field_key' => 'company_visiting',  'label' => 'Company Visiting (Visit Information)'],
            ['field_key' => 'date_of_visit',     'label' => 'Date of Visit Section'],
            ['field_key' => 'details_of_visit',  'label' => 'Details of Visit Section'],
            ['field_key' => 'person_details',    'label' => 'Person Details Section'],
            ['field_key' => 'driving_license',   'label' => 'Driving License Section'],
            ['field_key' => 'company_details',   'label' => 'Company Details Section (Name & Registration ID)', 'default_enabled' => false],
            ['field_key' => 'asset_equipment',   'label' => 'Asset / Equipment Details Section'],
            ['field_key' => 'document_upload',   'label' => 'Document Upload Section'],
            ['field_key' => 'profile_photo',     'label' => 'Profile Photo Section'],
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

        $rows   = $this->where('client_id', $companyId)->where('form_type', $formType)->findAll();
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
            $existing = $this->where('client_id', $companyId)
                             ->where('form_type', $formType)
                             ->where('field_key', $key)
                             ->first();

            if ($existing) {
                $this->update($existing['id'], ['is_enabled' => $enabled]);
            } elseif ($enabled === 0) {
                $this->insert([
                    'client_id' => $companyId,
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
        $row = $this->where('client_id', $companyId)
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

        if ($formType === 'visitor_pass_request') {
            return self::visitorPassRequestFields();
        }

        return [];
    }
}
