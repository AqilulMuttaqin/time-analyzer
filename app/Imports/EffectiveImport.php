<?php

namespace App\Imports;

use App\Models\Effective;
use App\Models\Subgolongan;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EffectiveImport
{
    public function import($filePath) {
        $data = Excel::toArray([], $filePath)[0];

        $headColumn = $data[0];
        $tanggalColumn = array_search('Tanggal', $headColumn);
        $weekColumn = array_search('Week', $headColumn);
        $subgolonganColumn = array_search('Sub Golongan', $headColumn);
        $shiftColumn = array_search('Shift', $headColumn);
        $standartColumn = array_search('Standart MP Direct', $headColumn);
        $indirectColumn = array_search('Indirect Act By Direct', $headColumn);
        $overtimeColumn = array_search('Overtime', $headColumn);
        $regulerColumn = array_search('Reguler Effective Hours', $headColumn);

        $importedCount = 0;
        $skippedRows = [];

        foreach ($data as $key => $row) {
            if ($key === 0) {
                continue;
            }

            $date = Date::excelToDateTimeObject($row[$tanggalColumn])->format('d-m-Y');
            $week = $row[$weekColumn];
            $tanggal = Carbon::createFromFormat('d-m-Y', $date)->startOfDay();
            $subgolongan = Subgolongan::where('nama', $row[$subgolonganColumn])->value('id');
            $shift = $row[$shiftColumn];
            $standart = $row[$standartColumn];
            $indirect = $row[$indirectColumn];
            $overtime = $row[$overtimeColumn];
            $reguler = $row[$regulerColumn];

            $emptyColumns = [];

            if ($tanggal === null) {
                $emptyColumns[] = 'Tanggal';
            } else if ($week === null || !in_array($week, [1, 2, 3, 4, 5])) {
                $emptyColumns[] = 'Week';
            } else if ($subgolongan === null) {
                $emptyColumns[] = 'Sub Golongan';
            } else if ($shift === null || !in_array($shift, ['A', 'B'])) {
                $emptyColumns[] = 'Shift';
            } else if ($standart === null) {
                $emptyColumns[] = 'Standart MP Direct';
            } else if ($indirect === null) {
                $emptyColumns[] = 'Indirect Act By Direct';
            } else if ($overtime === null) {
                $emptyColumns[] = 'Overtime';
            } else if ($reguler === null) {
                $emptyColumns[] = 'Reguler Effective Hours';
            }

            if (!empty($emptyColumns)) {
                $skippedRows[] = [
                    'row' => $key + 1,
                    'empty_columns' => $emptyColumns
                ];
                continue;
            }

            Effective::create(
                [
                    'tanggal' => $tanggal,
                    'week' => $week,
                    'id_line' => $subgolongan,
                    'shift' => $shift,
                    'standart' => $standart,
                    'indirect' => $indirect,
                    'overtime' => $overtime,
                    'reguler_eh' => $reguler
                ]
            );
            $importedCount++;
        }

        return [
            'imported_count' => $importedCount,
            'skipped_rows' => $skippedRows
        ];
    }
}
