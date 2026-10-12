<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * The Excel file vendors download on Vendor List > Import.
 * Lookup order: the client's own uploaded file -> the platform's uploaded file -> the built-in
 * public/files/VendorPassTemplate.xlsx. Uploaded files live in writable/ and are served by a route.
 */
class VendorImportTemplate
{
    /** Columns the import cannot work without (same names VendorList::import expects, case / * ignored). */
    public const REQUIRED = [
        'type of registration', 'designation', 'worker type', 'resident', 'location access',
        'in / out bound', 'ic number', 'date of birth', 'sex', 'full name',
    ];

    public static function dir(): string
    {
        return WRITEPATH . 'uploads/import_templates/';
    }

    public static function builtIn(): string
    {
        return FCPATH . 'files/VendorPassTemplate.xlsx';
    }

    /** Stored file for a scope: client id, or null for the platform-wide file. */
    public static function customPath(?int $clientId): string
    {
        return static::dir() . ($clientId ? 'client_' . $clientId : 'global') . '.xlsx';
    }

    /** File a given client should download. */
    public static function resolve(?int $clientId): string
    {
        foreach ([$clientId ?: null, null] as $scope) {
            $p = static::customPath($scope);
            if (is_file($p)) {
                return $p;
            }
        }

        return static::builtIn();
    }

    /** @return string|null error text when the workbook cannot be used as the import template */
    public static function validate(string $path): ?string
    {
        try {
            $reader = IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(true);
            $sheet = $reader->load($path)->getSheet(0);
        } catch (\Throwable $e) {
            return 'That file could not be opened. Upload a normal .xlsx Excel file.';
        }

        $headers = [];
        foreach ($sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1', null, true, false)[0] ?? [] as $h) {
            $headers[] = trim(str_replace('*', '', strtolower(trim((string) $h))));
        }
        $missing = array_values(array_diff(static::REQUIRED, $headers));
        if ($missing !== []) {
            return 'The first sheet, row 1 must keep these column headings: ' . implode(', ', $missing) . '. Download the current template and edit that one.';
        }

        return null;
    }
}
