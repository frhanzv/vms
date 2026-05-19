<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class StaffController extends BaseController
{
    public function import()
    {
        $file = $this->request->getFile('upload_file');

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'No valid file uploaded.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xlsx', 'xls'])) {
            return redirect()->back()->with('error', 'Only .xlsx or .xls files are allowed.');
        }

        $tmpPath = $file->getTempName();

        try {
            $spreadsheet = IOFactory::load($tmpPath);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to read the Excel file: ' . $e->getMessage());
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows  = $sheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return redirect()->back()->with('error', 'The file has no data rows.');
        }

        // Build a normalised header map: lowercase trimmed header => column index
        $rawHeaders = array_map(fn($h) => trim(str_replace('*', '', strtolower(trim((string) $h)))), $rows[0]);
        $headerMap  = array_flip($rawHeaders);

        $columnAliases = [
            'app_no'               => ['app no', 'app_no', 'application no', 'application number'],
            'full_name'            => ['full name', 'full_name', 'name'],
            'ic_passport'          => ['ic/passport', 'ic / passport', 'ic no. / passport', 'ic_passport', 'ic no.', 'ic no', 'ic', 'passport no.', 'passport no', 'passport', 'ic/passport no'],
            'staff_no'             => ['staff no.', 'staff no', 'staff_no', 'staff number', 'staff id'],
            'date_of_birth'        => ['date of birth', 'date_of_birth', 'dob'],
            'sex'                  => ['sex', 'gender'],
            'contact_number'       => ['contact number', 'contact_number', 'contact no.', 'contact no', 'phone', 'mobile', 'tel'],
            'email'                => ['email', 'email address'],
            'department'           => ['department', 'dept'],
            'designation'          => ['designation', 'position'],
            'resident'             => ['resident'],
            'sub_type'             => ['sub type', 'sub_type'],
            'type_of_application'  => ['type of application', 'type_of_application', 'application type'],
            'date_of_application'  => ['date of application', 'date_of_application'],
            'location_access'      => ['location access', 'location_access', 'access location'],
            'status'               => ['status'],
            'suspension_period'    => ['suspension period', 'suspension_period'],
            'next_action'          => ['next action', 'next_action'],
            'name_on_staff_pass'   => ['name on staff pass', 'name_on_staff_pass', 'pass name'],
            'visa_expiry'          => ['visa expiry', 'visa_expiry', 'visa expiry date'],
            'license_class'        => ['license class', 'license_class', 'licence class'],
            'license_expiry'       => ['license expiry', 'license_expiry', 'licence expiry', 'license expiry date'],
            'remark'               => ['remark', 'remarks', 'notes'],
            'csp_number'           => ['csp number', 'csp_number', 'csp no'],
            'csp_expiry_date'      => ['csp expiry', 'csp_expiry_date', 'csp expiry date'],
            'evetting_date_of_application' => ['evetting date of application', 'evetting_date_of_application'],
            'evetting_date_of_result'      => ['evetting date of result', 'evetting_date_of_result', 'evetting result date'],
            'evetting_result'      => ['evetting result', 'evetting_result'],
            'address_1'            => ['address 1', 'address_1', 'address1', 'address'],
            'address_2'            => ['address 2', 'address_2', 'address2'],
            'address_3'            => ['address 3', 'address_3', 'address3'],
            'city'                 => ['city'],
            'state'                => ['state'],
            'postal_code'          => ['postal code', 'postal_code', 'postcode', 'zip'],
            'country'              => ['country'],
        ];

        // Resolve which column index maps to each DB field
        $fieldIndex = [];
        foreach ($columnAliases as $field => $aliases) {
            foreach ($aliases as $alias) {
                if (isset($headerMap[$alias])) {
                    $fieldIndex[$field] = $headerMap[$alias];
                    break;
                }
            }
        }

        // When the template has separate "IC No." and "Passport No." columns,
        // ic_passport maps to IC No. (primary). Store Passport No. as fallback
        // so rows with no IC but a passport number are still captured.
        $passportFallbackIndex = null;
        if (isset($fieldIndex['ic_passport'])) {
            $icHeader = $rawHeaders[$fieldIndex['ic_passport']] ?? '';
            if (in_array($icHeader, ['ic no.', 'ic no', 'ic'])) {
                foreach (['passport no.', 'passport no', 'passport'] as $passAlias) {
                    if (isset($headerMap[$passAlias])) {
                        $passportFallbackIndex = $headerMap[$passAlias];
                        break;
                    }
                }
            }
        }

        $db       = \Config\Database::connect();
        $now      = date('Y-m-d H:i:s');
        $today    = date('d-m-Y');
        $batchTag = 'IMP-' . date('Ymd');

        $requiredFields = [
            'full_name'      => 'Full Name',
            'ic_passport'    => 'IC No. / Passport',
            'resident'       => 'Resident',
            'date_of_birth'  => 'Date of Birth',
            'sex'            => 'Gender',
            'staff_no'       => 'Staff No.',
            'contact_number' => 'Contact No.',
            'email'          => 'Email',
        ];

        // --- Validation pass: check all rows before inserting anything ---
        $validationErrors = [];
        $seenInFile = []; // track IC/Passport values within the file itself

        foreach (array_slice($rows, 1) as $i => $row) {
            $get = fn(string $field) => isset($fieldIndex[$field])
                ? (trim((string) ($row[$fieldIndex[$field]] ?? '')) ?: null)
                : null;

            $rowValues = array_filter(array_map(fn($v) => trim((string) $v), $row));
            if (empty($rowValues)) {
                continue;
            }

            $rowNum  = $i + 2; // +1 for header, +1 for 1-based
            $missing = [];
            foreach ($requiredFields as $field => $label) {
                $value = $get($field);
                // ic_passport also accepts passport fallback
                if ($field === 'ic_passport' && $value === null && $passportFallbackIndex !== null) {
                    $value = trim((string) ($row[$passportFallbackIndex] ?? '')) ?: null;
                }
                if ($value === null) {
                    $missing[] = $label;
                }
            }

            if (!empty($missing)) {
                $validationErrors[] = "Row {$rowNum}: missing " . implode(', ', $missing) . '.';
            }

            // Duplicate check — IC/Passport against DB and within the file
            $ic = $get('ic_passport') ?? (
                $passportFallbackIndex !== null
                    ? (trim((string) ($row[$passportFallbackIndex] ?? '')) ?: null)
                    : null
            );
            if ($ic !== null) {
                if (isset($seenInFile[$ic])) {
                    $validationErrors[] = "Row {$rowNum}: IC/Passport '{$ic}' is duplicated in the file (first seen on row {$seenInFile[$ic]}).";
                } else {
                    $seenInFile[$ic] = $rowNum;
                    if ($db->table('staff')->where('ic_passport', $ic)->countAllResults() > 0) {
                        $validationErrors[] = "Row {$rowNum}: IC/Passport '{$ic}' already exists in the system.";
                    }
                }
            }
        }

        if (!empty($validationErrors)) {
            $errorMsg = 'Import denied. Please fix the following errors and re-upload:' . "\n" . implode("\n", $validationErrors);
            return redirect()->back()->with('error', $errorMsg);
        }

        // --- Insert pass ---
        $inserted = 0;
        $counter  = 1;

        foreach (array_slice($rows, 1) as $row) {
            $get = fn(string $field) => isset($fieldIndex[$field])
                ? (trim((string) ($row[$fieldIndex[$field]] ?? '')) ?: null)
                : null;

            $rowValues = array_filter(array_map(fn($v) => trim((string) $v), $row));
            if (empty($rowValues)) {
                continue;
            }

            $record = [
                'app_no'                       => $get('app_no') ?? ($batchTag . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT)),
                'full_name'                    => $get('full_name'),
                'name_on_staff_pass'           => $get('name_on_staff_pass'),
                'ic_passport'                  => $get('ic_passport') ?? (
                    $passportFallbackIndex !== null
                        ? (trim((string) ($row[$passportFallbackIndex] ?? '')) ?: null)
                        : null
                ),
                'staff_no'                     => $get('staff_no'),
                'visa_expiry'                  => $this->parseDate($get('visa_expiry')),
                'license_class'                => $get('license_class'),
                'license_expiry'               => $this->parseDate($get('license_expiry')),
                'date_of_birth'                => $this->parseDate($get('date_of_birth')),
                'sex'                          => $get('sex'),
                'contact_number'               => $get('contact_number'),
                'email'                        => $get('email'),
                'department'                   => $get('department'),
                'designation'                  => $get('designation'),
                'resident'                     => $get('resident'),
                'sub_type'                     => $get('sub_type'),
                'type_of_application'          => $get('type_of_application'),
                'date_of_application'          => $this->parseDate($get('date_of_application')) ?? $get('date_of_application') ?? $today,
                'location_access'              => $get('location_access'),
                'status'                       => $get('status'),
                'suspension_period'            => $get('suspension_period'),
                'next_action'                  => $get('next_action'),
                'remark'                       => $get('remark'),
                'csp_number'                   => $get('csp_number'),
                'csp_expiry_date'              => $this->parseDate($get('csp_expiry_date')),
                'evetting_date_of_application' => $this->parseDate($get('evetting_date_of_application')),
                'evetting_date_of_result'      => $this->parseDate($get('evetting_date_of_result')),
                'evetting_result'              => $get('evetting_result'),
                'address_1'                    => $get('address_1'),
                'address_2'                    => $get('address_2'),
                'address_3'                    => $get('address_3'),
                'city'                         => $get('city'),
                'state'                        => $get('state'),
                'postal_code'                  => $get('postal_code'),
                'country'                      => $get('country'),
                'created_at'                   => $now,
            ];

            $db->table('staff')->insert($record);
            $inserted++;
            $counter++;
        }

        return redirect()->to(base_url('staffs'))->with('success', "{$inserted} staff record(s) imported successfully.");
    }

    private function parseDate(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // PhpSpreadsheet may return Excel serial date as a numeric string
        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Try common date formats
        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y', 'd.m.Y'];
        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $value);
            if ($dt !== false) {
                return $dt->format('Y-m-d');
            }
        }

        return null;
    }
}
