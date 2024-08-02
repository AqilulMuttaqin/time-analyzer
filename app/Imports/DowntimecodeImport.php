<?php

namespace App\Imports;

use App\Models\Downtimecode;
use App\Models\Section;
use Maatwebsite\Excel\Facades\Excel;

class DowntimecodeImport
{
    public function import($filePath) {
        $data = Excel::toArray([], $filePath)[0];

        $headColumn = $data[0];
        $kodeColumn = array_search('Kode Problem', $headColumn);
        $keteranganColumn = array_search('Keterangan', $headColumn);
        $sectionColumn = array_search('Section', $headColumn);

        $importedCount = 0;
        $skippedRows = [];

        foreach ($data as $key => $row) {
            if ($key === 0) {
                continue;
            }

            $kode = $row[$kodeColumn];
            $keterangan = $row[$keteranganColumn];
            $section = Section::where('nama', $row[$sectionColumn])->value('id');

            $emptyColumns = [];

            if ($kode === null) {
                $emptyColumns[] = 'Kode Problem';
            } else if ($keterangan === null) {
                $emptyColumns[] = 'Keterangan';
            } else if ($section === null) {
                $emptyColumns[] = 'Section';
            }

            if (!empty($emptyColumns)) {
                $skippedRows[] = [
                    'row' => $key + 1,
                    'empty_columns' => $emptyColumns
                ];
                continue;
            }

            Downtimecode::UpdateOrCreate(
                [
                    'kode' => $kode,
                    'id_section' => $section
                ],
                [
                    'keterangan' => $keterangan,
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
