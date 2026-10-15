<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class StaffController extends BaseController
{
    public function import()
    {
        helper(['access', 'feature']);

        // The Upload File popup posts here with fetch() (AJAX) and gets JSON back,
        // so the user stays on the Staff List / Closed List. A plain form post
        // (JavaScript off) is redirected to the list it came from — never
        // redirect()->back(): the previous URL in the session can be the
        // "/?debugbar" script request, which is the Host Dashboard.
        $returnTo = (string) $this->request->getPost('return_to');
        $target   = base_url(in_array($returnTo, ['staffs', 'staffs/closed-list'], true) ? $returnTo : 'staffs');
        $isAjax   = $this->request->isAJAX();

        /** One exit for every outcome: JSON for the popup, redirect + flash otherwise. */
        $done = function (bool $ok, string $message, array $errors = [], array $extra = []) use ($target, $isAjax) {
            if ($isAjax) {
                if ($ok) {
                    // The popup reloads the list; the flash shows the result on top of it.
                    session()->setFlashdata('success', $message);
                    if ($errors !== []) {
                        session()->setFlashdata('error', implode("\n", $errors));
                    }
                }

                return $this->response->setJSON(['success' => $ok, 'message' => $message, 'errors' => array_values($errors)] + $extra);
            }

            $redirect = redirect()->to($target);
            if ($ok) {
                $redirect = $redirect->with('success', $message);
            }
            if (! $ok || $errors !== []) {
                $redirect = $redirect->with('error', implode("\n", array_merge($ok ? [] : [$message], $errors)));
            }

            return $redirect;
        };

        if (! has_access('staff_pass_list', 'edit')) {
            return $done(false, 'You are not allowed to import staff records.');
        }

        // A file over PHP's post_max_size arrives with no POST data and no file at all.
        $contentLength = (int) $this->request->getServer('CONTENT_LENGTH');
        if ($contentLength > 0 && $this->request->getPost() === [] && $_FILES === []) {
            return $done(false, 'The file is too large to upload (server limit post_max_size = ' . ini_get('post_max_size') . ').');
        }

        $file = $this->request->getFile('upload_file');

        if (! $file || $file->hasMoved()) {
            return $done(false, 'No valid file uploaded. Please choose the Excel file and try again.');
        }
        if (! $file->isValid()) {
            return $done(false, 'The file could not be uploaded: ' . $file->getErrorString());
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xlsx', 'xls'])) {
            return $done(false, 'Only .xlsx or .xls files are allowed.');
        }

        $tmpPath = $file->getTempName();

        try {
            $spreadsheet = IOFactory::load($tmpPath);
        } catch (\Exception $e) {
            return $done(false, 'Failed to read the Excel file: ' . $e->getMessage());
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows  = $sheet->toArray(null, true, true, false);
        // Unformatted values too: a date typed into Excel is stored as a serial
        // number, and its display format (d/m or m/d) depends on the PC's locale.
        $rawRows = $sheet->toArray(null, true, false, false);

        if (count($rows) < 2) {
            return $done(false, 'The file has no data rows. Fill in the staff from row 2 of the Template.');
        }

        // Build a normalised header map: lowercase trimmed header => column index
        $rawHeaders = array_map(fn($h) => trim(str_replace('*', '', strtolower(trim((string) $h)))), $rows[0]);
        $headerMap  = array_flip($rawHeaders);

        $columnAliases = [
            'app_no'               => ['app no', 'app_no', 'application no', 'application number'],
            'full_name'            => ['full name', 'full_name', 'name'],
            'ic_passport'          => ['ic / passport no.', 'ic / passport no', 'ic/passport', 'ic / passport', 'ic no. / passport', 'ic_passport', 'ic no.', 'ic no', 'ic', 'passport no.', 'passport no', 'passport', 'ic/passport no'],
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
            'access_branch'        => ['approving branch', 'access branch', 'access_branch', 'branch'],
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

        // Location Access is mandatory and must be gates this client may use (own + site group + unassigned).
        helper('client_visibility');
        $allowedGates = array_column(visibility_filter_locations((new \App\Models\LocationModel())->getAllActive()), 'location_access');
        // Typed names are matched ignoring upper/lower case and extra spaces; several
        // locations may be separated by commas, semicolons or line breaks in the cell.
        $norm    = static fn(string $v): string => strtoupper(preg_replace('/\s+/', ' ', trim($v)));
        $byNorm  = [];
        foreach ($allowedGates as $gate) {
            $byNorm[$norm((string) $gate)] = (string) $gate;
        }
        $splitCell = static fn(?string $cell): array => array_values(array_filter(array_map('trim', preg_split('/[,;\r\n]+/', (string) $cell)), static fn($v) => $v !== ''));
        $resolveGates = static function (?string $cell) use ($splitCell, $norm, $byNorm): array {
            $out = [];
            foreach ($splitCell($cell) as $typed) {
                if (isset($byNorm[$norm($typed)])) {
                    $out[] = $byNorm[$norm($typed)];
                }
            }
            return array_values(array_unique($out));
        };
        /** Why a Location Access cell is not accepted, or null when every name matches. */
        $gateProblem = static function (?string $cell) use ($splitCell, $norm, $byNorm): ?string {
            $typed = $splitCell($cell);
            if ($typed === []) {
                return 'Location Access is empty (it is mandatory).';
            }
            $bad = [];
            foreach ($typed as $t) {
                if (isset($byNorm[$norm($t)])) {
                    continue;
                }
                // "CHANGING ROOM 1" when only "CHANGING ROOM 1 IN" / "... OUT" exist.
                $hint = array_values(array_filter($byNorm, static fn($real, $key) => in_array($key, [$norm($t) . ' IN', $norm($t) . ' OUT'], true), ARRAY_FILTER_USE_BOTH));
                $bad[] = "'{$t}'" . ($hint !== [] ? " (did you mean '" . implode("' or '", $hint) . "'?)" : '');
            }

            return $bad === [] ? null
                : 'Location Access ' . implode(', ', $bad) . ' ' . (count($bad) > 1 ? 'are not locations' : 'is not a location') . ' you can use. Use the names exactly as in Config > Location Access Management, separated by commas.';
        };

        // --- Validation pass: check all rows before inserting anything ---
        $validationErrors = [];
        $seenInFile = []; // track IC/Passport values within the file itself

        $dateFields = ['date_of_birth' => 'Date of Birth', 'visa_expiry' => 'Visa Expiry', 'license_expiry' => 'License Expiry'];

        foreach (array_slice($rows, 1) as $i => $row) {
            $get = fn(string $field) => isset($fieldIndex[$field])
                ? (trim((string) ($row[$fieldIndex[$field]] ?? '')) ?: null)
                : null;

            $rowValues = array_filter(array_map(fn($v) => trim((string) $v), $row));
            if (empty($rowValues)) {
                continue;
            }

            $rowNum  = $i + 2; // +1 for header, +1 for 1-based
            foreach ($dateFields as $field => $label) {
                if ($get($field) !== null && $this->cellDate($rawRows[$i + 1] ?? [], $fieldIndex[$field] ?? null) === null) {
                    $validationErrors[] = "Row {$rowNum}: {$label} '" . $get($field) . "' is not a valid date (use dd/mm/yyyy).";
                }
            }
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
            if (($gateMsg = $gateProblem($get('location_access'))) !== null) {
                $validationErrors[] = "Row {$rowNum}: " . $gateMsg;
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
                    if ($icMsg = \App\Libraries\PassIdentity::check($db, (string) $ic)) {
                        $validationErrors[] = "Row {$rowNum}: " . $icMsg;
                    }
                }
            }
        }

        if (!empty($validationErrors)) {
            return $done(false, 'Import denied — nothing was saved. Please fix the following and upload again:', $validationErrors);
        }

        // --- Insert pass ---
        $inserted = 0;
        $counter  = 1;
        $failed   = [];
        $actor    = (string) (session()->get('full_name') ?: session()->get('username'));
        // Dropdown values are stored the way the request form stores them (MALE, LOCAL, ...).
        $upper    = static fn(?string $v): ?string => $v === null ? null : strtoupper($v);
        $fields   = $db->getFieldNames('staff');

        foreach (array_slice($rows, 1) as $i => $row) {
            $raw = $rawRows[$i + 1] ?? [];
            $get = fn(string $field) => isset($fieldIndex[$field])
                ? (trim((string) ($row[$fieldIndex[$field]] ?? '')) ?: null)
                : null;

            $rowValues = array_filter(array_map(fn($v) => trim((string) $v), $row));
            if (empty($rowValues)) {
                continue;
            }

            $importLoc = implode(',', $resolveGates($get('location_access')));
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
                'visa_expiry'                  => $this->cellDate($raw, $fieldIndex['visa_expiry'] ?? null),
                'license_class'                => $get('license_class'),
                'license_expiry'               => $this->cellDate($raw, $fieldIndex['license_expiry'] ?? null),
                'date_of_birth'                => $this->cellDate($raw, $fieldIndex['date_of_birth'] ?? null),
                'sex'                          => $upper($get('sex')),
                'contact_number'               => $get('contact_number'),
                'email'                        => $get('email'),
                'department'                   => $get('department'),
                'designation'                  => $get('designation'),
                'resident'                     => $upper($get('resident')),
                'sub_type'                     => $upper($get('sub_type')),
                'type_of_application'          => $upper($get('type_of_application')) ?? 'NEW',
                'access_branch'                => in_array($upper($get('access_branch')), ['KSB', 'KPK', 'BOTH'], true) ? $upper($get('access_branch')) : null,
                'date_of_application'          => $this->parseDate($get('date_of_application')) ?? $get('date_of_application') ?? $today,
                'location_access'              => $importLoc,
                // KPK upload-staff-record: HR's sheet is the source of truth, so an
                // imported staff pass skips approval and goes straight to CLOSED
                // (status 300005) — here: Approved with an Active card, so it shows
                // on the Staff List and the Closed List. A physical card can be
                // printed later with Reprint Card on its Card Info page.
                // The sheet's Status column is the employee flag (Active /
                // Inactive), not the pass workflow.
                'status'                       => 'Approved',
                'is_active'                    => strtolower((string) $get('status')) === 'inactive' ? 0 : 1,
                'card_status'                  => 'Active',
                'issued_at'                    => $now,
                'issued_by'                    => $actor !== '' ? $actor : null,
                'company_id'                   => current_company_id() ?: null,
                'suspension_period'            => $get('suspension_period'),
                'next_action'                  => null,
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
                'country'                      => $upper($get('country')) ?? 'MALAYSIA',
                'created_at'                   => $now,
            ];

            try {
                $ok = $db->table('staff')->insert(array_intersect_key($record, array_flip($fields)));
            } catch (\Throwable $e) {
                log_message('error', 'Staff import insert failed: ' . $e->getMessage());
                $ok = false;
            }
            $staffId = $ok ? (int) $db->insertID() : 0;
            if ($staffId <= 0) {
                $failed[] = ($record['full_name'] ?? '?') . ' (' . ($record['ic_passport'] ?? '?') . ')';
                $counter++;
                continue;
            }

            // Imported passes are already Approved (HR sheet), so every client owning one of the gates is Approved too.
            if ($db->tableExists('staff_client_approvals')) {
                \App\Libraries\StaffClientApprovals::sync($db, $staffId, (string) $record['location_access']);
                if (\App\Libraries\StaffClientApprovals::hasRows($db, $staffId)) {
                    \App\Libraries\StaffClientApprovals::decide($db, $staffId, null, 'Approved', 'import');
                }
            }

            if (($record['license_class'] || $record['license_expiry']) && $db->tableExists('staff_driving_licenses')) {
                $db->table('staff_driving_licenses')->insert([
                    'staff_id'       => $staffId,
                    'license_class'  => $record['license_class'],
                    'license_expiry' => $record['license_expiry'],
                    'created_at'     => $now,
                ]);
            }
            $inserted++;
            $counter++;
        }

        if ($inserted === 0 && $failed === []) {
            return $done(false, 'No staff rows found in the file. Fill in the staff from row 2 of the Template.');
        }
        if ($failed !== []) {
            $failed = array_map(static fn($f) => 'Could not be saved: ' . $f . ' (see writable/logs for the reason).', $failed);
            return $inserted > 0
                ? $done(true, "{$inserted} staff record(s) imported.", $failed, ['imported' => $inserted])
                : $done(false, 'No staff record could be saved.', $failed);
        }

        return $done(true, "{$inserted} staff record(s) imported successfully — now on the Staff List and the Closed List.", [], ['imported' => $inserted]);
    }

    /**
     * Staff List / Closed List > Template: the latest import template, built on
     * download with this client's Location Access names and departments.
     */
    public function template()
    {
        helper(['client_visibility', 'feature']);
        $locations = array_column(visibility_filter_locations((new \App\Models\LocationModel())->getAllActive()), 'location_access');

        $departments = [];
        try {
            $rows        = \Config\Database::connect()->table('departments')->select('name')->where('status', 'Active')->orderBy('name')->get()->getResultArray();
            $departments = array_column($rows, 'name');
        } catch (\Throwable $e) {
            log_message('error', 'Staff template departments: ' . $e->getMessage());
        }

        $path = \App\Libraries\StaffImportTemplate::toFile(\App\Libraries\StaffImportTemplate::build($locations, $departments));

        return $this->response->download($path, null)->setFileName('StaffTemplateNew.xlsx');
    }

    /**
     * A date cell from the unformatted row: an Excel date (serial number) is
     * converted directly; typed text must be dd/mm/yyyy (or yyyy-mm-dd).
     * Returns Y-m-d, or null when empty / not a real date.
     */
    private function cellDate(array $rawRow, ?int $index): ?string
    {
        if ($index === null) {
            return null;
        }
        $value = $rawRow[$index] ?? null;
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        if (is_int($value) || is_float($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        return $this->parseDate(trim((string) $value));
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

        // Day first (Malaysian format). Strict: 31/02 or 6/24 are rejected
        // instead of silently rolling over into another date.
        $value = preg_replace('/\s+00:00(:00)?$/', '', $value);
        $formats = ['!d/m/Y', '!Y-m-d', '!d-m-Y', '!d.m.Y', '!Y/m/d'];
        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $value);
            $errors = \DateTime::getLastErrors();
            if ($dt !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $dt->format('Y-m-d');
            }
        }

        return null;
    }
}
