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
    protected $allowedFields    = ['client_id', 'form_type', 'field_key', 'is_enabled', 'is_required'];

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
            'vendor_pass_request'   => 'Vendor Pass Request',
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

            // --- KPK staff pass pipeline (request -> approve -> process -> print -> issue -> closed) ---
            ['field_key' => 'access_branch',        'label' => 'Approving Branch (KSB / KPK / Both)'],
            ['field_key' => 'photo_upload',         'label' => 'Passport Photo Upload (Request Form)'],
            ['field_key' => 'remark',               'label' => 'Remark Field (Request Form)'],
            ['field_key' => 'direct_close',         'label' => 'Direct Close (skip Printing/Issuance, activate card immediately on approval)', 'default_enabled' => false],
            ['field_key' => 'request_button',       'label' => 'Show Request Button (Staff List)'],
            ['field_key' => 'edit_button',          'label' => 'Show Edit Button (Staff List)'],
            ['field_key' => 'delete_button',        'label' => 'Show Delete Button (Staff List)'],
            ['field_key' => 'approve_button',       'label' => 'Show Approve Button (Staff List)'],
            ['field_key' => 'reject_button',        'label' => 'Show Reject Button (Staff List)'],
            ['field_key' => 'import_button',        'label' => 'Show Import Button (Staff List)'],
            ['field_key' => 'export_button',        'label' => 'Show Export Button (Staff List)'],
            ['field_key' => 'process_update_button',       'label' => 'Process / Card Info: Update Details Button'],
            ['field_key' => 'process_reject_button',       'label' => 'Process / Card Info: Reject Button'],
            ['field_key' => 'process_upload_photo_button', 'label' => 'Process / Card Info: Upload / Take Photo'],
            ['field_key' => 'process_rfid_section',        'label' => 'Process / Card Info: RFID Card Binding'],
            ['field_key' => 'printing_generate_button',    'label' => 'Print / Reprint Card Button'],
            ['field_key' => 'issuance_issue_button',       'label' => 'Issue Card Button (Issuance List)'],
            ['field_key' => 'closed_export_button',        'label' => 'Show Export Button (Closed List)'],
            ['field_key' => 'closed_card_details_button',  'label' => 'Show Card Details Button (Closed List)'],
            ['field_key' => 'card_info_add_license_button',   'label' => 'Card Info: Add / Remove License'],
            ['field_key' => 'card_info_edit_location_button', 'label' => 'Card Info: Edit Location Access'],
            ['field_key' => 'card_info_activate_button',      'label' => 'Card Info: Activate Card'],
            ['field_key' => 'card_info_terminate_button',     'label' => 'Card Info: Terminate Card'],
            ['field_key' => 'suspend_button',       'label' => 'Card Info: Suspend / Unsuspend Pass'],
            ['field_key' => 'renew_button',         'label' => 'Renew Pass Button (Closed List / Card Info)'],
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
     * Every field, section and button the Vendor module can show — per the
     * instruction "everything configurable: the button, the field, the
     * mandatory". Each entry can carry:
     *   - 'default_enabled' (bool, default true)      — shown unless turned off
     *   - 'requirable' (bool, default false)           — whether a "Mandatory"
     *                                                     switch applies at all
     *   - 'default_required' (bool, default false)     — its out-of-the-box state,
     *                                                     chosen to match what the
     *                                                     form already enforced in
     *                                                     code before this existed
     * Buttons never set 'requirable' — "Mandatory" has no meaning for a button,
     * so the Config screen only shows an Enabled switch for those.
     */
    public static function vendorPassFields(): array
{
    return [
        // --- Application Info section ---
        ['field_key' => 'type_of_application',  'label' => 'Type Of Application'],
        ['field_key' => 'sub_type',              'label' => 'Sub Type'],
        ['field_key' => 'resident',              'label' => 'Resident'],

        // --- Vendor Company section ---
        ['field_key' => 'vendor_company',        'label' => 'Vendor Company Section (SSM No / Company Name)'],

        // --- Personal Details section ---
        ['field_key' => 'staff_no',              'label' => 'Staff No. (at vendor company)'],
        ['field_key' => 'ic_passport',            'label' => 'IC / Passport Number'],
        ['field_key' => 'date_of_birth',          'label' => 'Date Of Birth'],
        ['field_key' => 'sex',                    'label' => 'Sex'],
        ['field_key' => 'designation',            'label' => 'Designation'],
        ['field_key' => 'contact_number',         'label' => 'Contact Number'],
        ['field_key' => 'email',                  'label' => 'Email Address'],

        // --- Address section ---
        ['field_key' => 'address',                'label' => 'Address Section (Line 1-3 & Postcode)'],

        // --- Visit Details section ---
        ['field_key' => 'visit_details',          'label' => 'Visit Details Section (Person/Contact/Location Visited)'],

        // --- CSP & E-Vetting section (matches Staff's csp_number/evetting, off by default there too) ---
        ['field_key' => 'csp_number',             'label' => 'CSP Number & Expiry Date',        'default_enabled' => false],
        ['field_key' => 'evetting',                'label' => 'E-Vetting Section',                'default_enabled' => false],

        // --- Pass & Documents section ---
        ['field_key' => 'pass_expiry',             'label' => 'Pass Expiry Field'],
        ['field_key' => 'remark',                  'label' => 'Remark Field'],
        ['field_key' => 'photo_upload',            'label' => 'Photo Upload'],
        ['field_key' => 'document_upload',         'label' => 'Government ID / Other Documents Upload'],

        ['field_key' => 'direct_close', 'label' => 'Direct Close (skip Printing/Issuance, activate card immediately on approval)', 'default_enabled' => false],
        // This mirrors KPK's ModuleConfig.vpDirectClose. Off by default — most
    // companies will want the normal Printing -> Issuance -> Closed flow.
        ['field_key' => 'additional_verification', 'label' => 'Additional Verification (MySejahtera/Facial Photo)', 'default_enabled' => false],
        ['field_key' => 'card_issuance', 'label' => 'Card Issuance Section (Receipt/Vehicle/Card Type/Status)', 'default_enabled' => false],
        // --- List page buttons (checked alongside has_access, same as print_button) ---
        ['field_key' => 'edit_button',             'label' => 'Show Edit Button (Vendor List)'],
        ['field_key' => 'delete_button',           'label' => 'Show Delete Button (Vendor List)'],
        ['field_key' => 'approve_button',          'label' => 'Show Approve Button (Vendor List)'],
        ['field_key' => 'reject_button',           'label' => 'Show Reject Button (Vendor List)'],
        ['field_key' => 'qr_button',               'label' => 'Show QR Pass Button (Vendor List)'],
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

        $rows         = $this->where('client_id', $companyId)->where('form_type', $formType)->findAll();
        $storedEnable = array_column($rows, 'is_enabled', 'field_key');
        $storedRequire = array_column($rows, 'is_required', 'field_key');

        $result = [];
        foreach ($definitions as $def) {
            $key           = $def['field_key'];
            $requirable    = (bool) ($def['requirable'] ?? false);
            $defaultEnable = isset($def['default_enabled']) ? (int) $def['default_enabled'] : 1;
            $defaultRequire = isset($def['default_required']) ? (int) $def['default_required'] : 0;

            $row = [
                'field_key'  => $key,
                'label'      => $def['label'],
                'is_enabled' => isset($storedEnable[$key]) ? (int) $storedEnable[$key] : $defaultEnable,
                'requirable' => $requirable,
            ];

            if ($requirable) {
                $storedReq         = $storedRequire[$key] ?? null;
                $row['is_required'] = $storedReq !== null ? (int) $storedReq : $defaultRequire;
            } else {
                $row['is_required'] = 0;
            }

            $result[] = $row;
        }
        return $result;
    }

    /**
     * Upserts field flags for a company+form. Only writes rows that are disabled,
     * marked required, or already exist — absence means enabled/not-required
     * (falling back to each field's own default).
     *
     * $fields accepts two shapes for backward compatibility across the 5 form
     * types that share this method/UI:
     *   - legacy: [field_key => bool $enabled]
     *   - extended (used by vendor_pass_request's "Mandatory" switch):
     *     [field_key => ['enabled' => bool, 'required' => bool]]
     */
    public function saveForCompanyForm(int $companyId, string $formType, array $fields): void
    {
        $definitions = $this->getDefinitions($formType);
        $requirableKeys = [];
        foreach ($definitions as $def) {
            if (! empty($def['requirable'])) {
                $requirableKeys[$def['field_key']] = true;
            }
        }

        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                $enabled  = ! empty($value['enabled']) ? 1 : 0;
                $required = isset($requirableKeys[$key]) && ! empty($value['required']) ? 1 : 0;
            } else {
                $enabled  = $value ? 1 : 0;
                $required = null;
            }

            $existing = $this->where('client_id', $companyId)
                             ->where('form_type', $formType)
                             ->where('field_key', $key)
                             ->first();

            $data = ['is_enabled' => $enabled];
            if (isset($requirableKeys[$key])) {
                $data['is_required'] = $required;
            }

            if ($existing) {
                $this->update($existing['id'], $data);
            } elseif ($enabled === 0 || ! empty($data['is_required'])) {
                $this->insert(array_merge([
                    'client_id' => $companyId,
                    'form_type' => $formType,
                    'field_key' => $key,
                ], $data));
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

    /**
     * Check if a field is mandatory for a company+form. A saved 0/1 in
     * is_required always wins; otherwise falls back to the field definition's
     * 'default_required' (default false). A field not marked 'requirable' in
     * its definition is never mandatory regardless of stored data.
     */
    public function isRequired(int $companyId, string $formType, string $fieldKey): bool
    {
        $def = null;
        foreach ($this->getDefinitions($formType) as $candidate) {
            if ($candidate['field_key'] === $fieldKey) {
                $def = $candidate;
                break;
            }
        }

        if ($def === null || empty($def['requirable'])) {
            return false;
        }

        $row = $this->where('client_id', $companyId)
                    ->where('form_type', $formType)
                    ->where('field_key', $fieldKey)
                    ->first();

        if ($row !== null && $row['is_required'] !== null) {
            return (bool) $row['is_required'];
        }

        return (bool) ($def['default_required'] ?? false);
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

        if ($formType === 'vendor_pass_request') {
            return self::vendorPassFields();
        }

        return [];
    }
}
