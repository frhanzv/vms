<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Staff List / Closed List > Template: the Excel file HR fills in for Import.
 *
 * Built on download so it always matches the current Staff Pass Request form
 * and StaffController::import(), and so the Location Access / Department
 * pick-lists show exactly what the downloading client can use.
 *
 * Sheet "Staff"       — one header row; HR fills from row 2.
 * Sheet "Locations"   — every Location Access name this client may use.
 * Sheet "Instructions"— how to fill each column.
 * Sheet "Lists"       — hidden source of the dropdowns.
 */
class StaffImportTemplate
{
    public const ROWS = 1000; // rows that get dropdowns / text formatting

    /**
     * Column definitions in sheet order. Headers are what the importer
     * recognises (a trailing * marks a column the import requires).
     *
     * @return list<array{header:string, width:int, list?:string, text?:bool, note:string}>
     */
    public static function columns(): array
    {
        return [
            ['header' => 'Type Of Application', 'width' => 20, 'list' => 'type', 'note' => 'NEW, RENEWAL or REPLACEMENT. Blank = NEW.'],
            ['header' => 'Designation',         'width' => 18, 'list' => 'designation', 'note' => 'Pick from the list.'],
            ['header' => 'Resident *',          'width' => 12, 'list' => 'resident', 'note' => 'LOCAL or FOREIGN.'],
            ['header' => 'Sub Type',            'width' => 14, 'list' => 'subtype', 'note' => 'PERMANENT, TEMPORARY, CONTRACT or FAMILY PASS.'],
            ['header' => 'Location Access *',   'width' => 34, 'list' => 'location', 'note' => 'Pick one from the list, or type several names separated by commas, exactly as on the Locations sheet (including IN / OUT).'],
            ['header' => 'Approving Branch',    'width' => 16, 'list' => 'branch', 'note' => 'KSB, KPK or BOTH. Blank = any approver.'],
            ['header' => 'IC / Passport No. *', 'width' => 20, 'text' => true, 'note' => 'Numbers only for IC (no dashes), e.g. 900101015555. Each IC / passport only once.'],
            ['header' => 'Date Of Birth *',     'width' => 15, 'text' => true, 'note' => 'dd/mm/yyyy, e.g. 24/06/1990.'],
            ['header' => 'Gender *',            'width' => 10, 'list' => 'gender', 'note' => 'MALE or FEMALE.'],
            ['header' => 'Full Name *',         'width' => 28, 'note' => 'Full name as on the IC / passport.'],
            ['header' => 'Name On Staff Pass',  'width' => 24, 'note' => 'Name printed on the card.'],
            ['header' => 'Staff No. *',         'width' => 14, 'text' => true, 'note' => 'Employee number.'],
            ['header' => 'Contact No. *',       'width' => 16, 'text' => true, 'note' => 'Numbers only, e.g. 60123456789.'],
            ['header' => 'Email *',             'width' => 26, 'note' => 'e.g. name@company.com'],
            ['header' => 'Department',          'width' => 18, 'list' => 'department', 'note' => 'Pick from the list.'],
            ['header' => 'Address 1',           'width' => 26, 'note' => ''],
            ['header' => 'Address 2',           'width' => 22, 'note' => ''],
            ['header' => 'Address 3',           'width' => 22, 'note' => ''],
            ['header' => 'City',                'width' => 16, 'note' => ''],
            ['header' => 'State',               'width' => 16, 'note' => ''],
            ['header' => 'Postcode',            'width' => 10, 'text' => true, 'note' => '5 digits, e.g. 41000.'],
            ['header' => 'Country',             'width' => 14, 'note' => 'Blank = MALAYSIA.'],
            ['header' => 'Visa Expiry',         'width' => 14, 'text' => true, 'note' => 'Foreign staff only. dd/mm/yyyy.'],
            ['header' => 'License Class',       'width' => 13, 'list' => 'license', 'note' => 'Driving license class (optional).'],
            ['header' => 'License Expiry',      'width' => 14, 'text' => true, 'note' => 'dd/mm/yyyy (optional).'],
            ['header' => 'Remark',              'width' => 26, 'note' => ''],
            ['header' => 'Status',              'width' => 11, 'list' => 'status', 'note' => 'Active or Inactive employee. Blank = Active.'],
        ];
    }

    /**
     * @param list<string>|null $locations Location Access names this client may use
     *                                     (null = generic copy, e.g. public/files)
     * @param list<string>      $departments active department names
     */
    public static function build(?array $locations, array $departments): Spreadsheet
    {
        $generic   = $locations === null;
        $locations = $locations ?? [];
        natcasesort($locations);
        $locations   = array_values(array_unique(array_filter(array_map('strval', $locations))));
        $departments = array_values(array_unique(array_filter(array_map('strval', $departments))));

        $lists = [
            'type'        => ['NEW', 'RENEWAL', 'REPLACEMENT'],
            'designation' => ['PEN. LORI', 'OPERATOR', 'SUPERVISOR', 'ENGINEER', 'MANAGER'],
            'resident'    => ['LOCAL', 'FOREIGN'],
            'subtype'     => ['PERMANENT', 'TEMPORARY', 'CONTRACT', 'FAMILY PASS'],
            'location'    => $locations,
            'branch'      => ['KSB', 'KPK', 'BOTH'],
            'gender'      => ['MALE', 'FEMALE'],
            'department'  => $departments,
            'license'     => ['B', 'B2', 'C', 'D', 'DA', 'E', 'E1', 'E2'],
            'status'      => ['Active', 'Inactive'],
        ];
        // Free choice allowed (comma-separated locations, departments not set up yet).
        $softLists = ['location', 'department', 'designation'];

        $book = new Spreadsheet();
        $book->getProperties()->setTitle('Staff Import Template')->setCreator('SafeG');

        // --- Hidden "Lists" sheet: one column per dropdown ---
        $listSheet = $book->createSheet();
        $listSheet->setTitle('Lists');
        $ranges = [];
        $col    = 1;
        foreach ($lists as $key => $values) {
            $letter = Coordinate::stringFromColumnIndex($col++);
            $listSheet->setCellValue($letter . '1', $key);
            foreach ($values as $i => $value) {
                $listSheet->setCellValueExplicit($letter . ($i + 2), $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
            $ranges[$key] = $values === [] ? null : "Lists!\${$letter}\$2:\${$letter}\$" . (count($values) + 1);
        }
        $listSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        // --- "Staff" sheet ---
        $sheet = $book->getSheet(0);
        $sheet->setTitle('Staff');
        $last  = self::ROWS + 1;
        foreach (self::columns() as $i => $c) {
            $letter = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($letter . '1', $c['header']);
            $sheet->getColumnDimension($letter)->setWidth($c['width']);
            if ($c['note'] !== '') {
                $comment = $sheet->getComment($letter . '1');
                $comment->getText()->createTextRun($c['note']);
                $comment->setWidth('220pt')->setHeight('60pt');
            }
            if (! empty($c['text'])) {
                // Text cells keep IC / phone leading zeros and dd/mm/yyyy exactly as typed.
                $sheet->getStyle("{$letter}2:{$letter}{$last}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            }
            if (isset($c['list']) && $ranges[$c['list']] !== null) {
                $soft = in_array($c['list'], $softLists, true);
                $dv   = new DataValidation();
                $dv->setType(DataValidation::TYPE_LIST)
                    ->setFormula1($ranges[$c['list']])
                    ->setAllowBlank(true)
                    ->setShowDropDown(true)
                    ->setShowInputMessage(true)
                    ->setPromptTitle($c['header'])
                    ->setPrompt(mb_substr($c['note'], 0, 250))
                    ->setShowErrorMessage(! $soft)
                    ->setErrorStyle($soft ? DataValidation::STYLE_INFORMATION : DataValidation::STYLE_STOP)
                    ->setErrorTitle('Not in the list')
                    ->setError('Please pick a value from the list.');
                $sheet->setDataValidation("{$letter}2:{$letter}{$last}", $dv);
            }
        }
        $lastCol = Coordinate::stringFromColumnIndex(count(self::columns()));
        $header  = $sheet->getStyle("A1:{$lastCol}1");
        $header->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $header->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF137FEC');
        $header->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->freezePane('A2');
        // Required columns: red header text marker via a lighter fill.
        foreach (self::columns() as $i => $c) {
            if (str_ends_with($c['header'], '*')) {
                $letter = Coordinate::stringFromColumnIndex($i + 1);
                $sheet->getStyle($letter . '1')->getFill()->getStartColor()->setARGB('FF0F5BB5');
            }
        }

        // --- "Locations" sheet: what Location Access may contain ---
        $locSheet = $book->createSheet(1);
        $locSheet->setTitle('Locations');
        $locSheet->setCellValue('A1', 'Location Access names you can use (copy exactly; several = separate with commas)');
        $locSheet->getStyle('A1')->getFont()->setBold(true);
        $locSheet->getColumnDimension('A')->setWidth(60);
        if ($generic) {
            $locSheet->setCellValue('A2', 'Download the template from Staff List > Template to get the list of locations for your company.');
        } elseif ($locations === []) {
            $locSheet->setCellValue('A2', 'No locations are set up for your company yet — ask your admin to add them in Config > Location Access Management.');
        }
        foreach ($locations as $i => $name) {
            $locSheet->setCellValueExplicit('A' . ($i + 2), $name, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        }

        // --- "Instructions" sheet ---
        $help = $book->createSheet(2);
        $help->setTitle('Instructions');
        $help->getColumnDimension('A')->setWidth(22);
        $help->getColumnDimension('B')->setWidth(95);
        $rows = [
            ['How to use', 'Fill one staff per row on the Staff sheet, starting at row 2. Do not change or move the header row.'],
            ['', 'Columns marked * are required. The import checks every row first and saves nothing until all rows are valid; any problem is listed by row number.'],
            ['', 'Imported staff are treated as HR-approved and go straight to the Closed List with an active card. Use Reprint Card on Card Info if a physical card is needed.'],
            ['', 'Hover over a header cell to see a hint for that column. Columns with a dropdown show the allowed values.'],
            ['', ''],
        ];
        foreach (self::columns() as $c) {
            $rows[] = [$c['header'], $c['note'] !== '' ? $c['note'] : '-'];
        }
        foreach ($rows as $i => [$a, $b]) {
            $help->setCellValue('A' . ($i + 1), $a);
            $help->setCellValue('B' . ($i + 1), $b);
            $help->getStyle('B' . ($i + 1))->getAlignment()->setWrapText(true);
        }
        $help->getStyle('A1:A' . count($rows))->getFont()->setBold(true);

        $book->setActiveSheetIndex(0);

        return $book;
    }

    /** Write the workbook to a temporary .xlsx file and return its path. */
    public static function toFile(Spreadsheet $book): string
    {
        $path = tempnam(sys_get_temp_dir(), 'staff_tpl_') . '.xlsx';
        (new Xlsx($book))->save($path);

        return $path;
    }
}
