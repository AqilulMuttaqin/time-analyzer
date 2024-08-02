<?php

namespace App\Imports;

use App\Models\Section;
use Maatwebsite\Excel\Facades\Excel;

class SectionImport
{
    public function import($filePath) {
        $data = Excel::toArray([], $filePath)[0];

        $headColumn = $data[0];
        $namaColumn = array_search('Nama Section', $headColumn);

        $importedCount = 0;
        $skippedRows = [];

        foreach ($data as $key => $row) {
            if ($key === 0) {
                continue;
            }

            $nama = $row[$namaColumn];

            $emptyColumns = [];

            if ($nama === null) {
                $emptyColumns[] = 'Nama Section';
            }

            if (!empty($emptyColumns)) {
                $skippedRows[] = [
                    'row' => $key + 1,
                    'empty_columns' => $emptyColumns
                ];
                continue;
            }

            Section::updateOrCreate(
                [
                    'nama' => $nama,
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
