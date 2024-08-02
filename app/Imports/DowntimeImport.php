<?php

namespace App\Imports;

use App\Models\Line;
use App\Models\Cccode;
use App\Models\Downtime;
use App\Models\Downtimecode;
use App\Models\Subgolongan;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DowntimeImport
{
    public function import($filePath) {
        $data = Excel::toArray([], $filePath)[0];

        $headColumn = $data[0];
        $tanggalColumn = array_search('Tanggal', $headColumn);
        $weekColumn = array_search('Week', $headColumn);
        $shiftColumn = array_search('Shift', $headColumn);
        $subgolonganColumn = array_search('Sub Golongan', $headColumn);
        $downtimecodeColumn = array_search('Downtime Code', $headColumn);
        $detailColumn = array_search('Detail Problem', $headColumn);
        $minuteColumn = array_search('Minute', $headColumn);
        $mhColumn = array_search('Man Hours', $headColumn);

        $importedCount = 0;
        $skippedRows = [];

        foreach ($data as $key => $row) {
            if ($key === 0) {
                continue;
            }

            $date = Date::excelToDateTimeObject($row[$tanggalColumn])->format('d-m-Y');
            $tanggal = Carbon::createFromFormat('d-m-Y', $date)->startOfDay();
            $week = $row[$weekColumn];
            $shift = $row[$shiftColumn];
            $subgolongan = Subgolongan::where('nama', $row[$subgolonganColumn])->value('id');
            $downtimecode = Downtimecode::where('kode', $row[$downtimecodeColumn])->value('id');
            $detail = $row[$detailColumn];
            $minute = $row[$minuteColumn];
            $mh = $row[$mhColumn];

            $emptyColumns = [];

            if ($tanggal === null) {
                $emptyColumns[] = 'Tanggal';
            } else if ($week === null || !in_array($week, [1, 2, 3, 4, 5])) {
                $emptyColumns[] = 'Week';
            } else if ($shift === null || !in_array($shift, ['A', 'B'])) {
                $emptyColumns[] = 'Shift';
            } else if ($subgolongan === null) {
                $emptyColumns[] = 'Sub Golongan';
            } else if ($downtimecode === null) {
                $emptyColumns[] = 'Downtime Code';
            } else if ($minute === null) {
                $emptyColumns[] = 'Minute';
            } else if ($mh === null) {
                $emptyColumns[] = 'Man Hours';
            }

            if (!empty($emptyColumns)) {
                $skippedRows[] = [
                    'row' => $key + 1,
                    'empty_columns' => $emptyColumns
                ];
                continue;
            }

            Downtime::create(
                [
                    'tanggal' => $tanggal,
                    'week' => $week,
                    'shift' => $shift,
                    'id_subgolongan' => $subgolongan,
                    'id_downtimecode' => $downtimecode,
                    'detail' => $detail,
                    'minute' => $minute,
                    'man_hours' => $mh
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
