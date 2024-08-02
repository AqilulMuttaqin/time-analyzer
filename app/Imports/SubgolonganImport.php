<?php

namespace App\Imports;

use App\Models\Golongan;
use App\Models\Subgolongan;
use Maatwebsite\Excel\Facades\Excel;

class SubgolonganImport
{
    public function import($filePath) {
        $data = Excel::toArray([], $filePath)[0];

        $headColumn = $data[0];
        $subgolonganColumn = array_search('Sub Golongan', $headColumn);
        $golonganColumn = array_search('Golongan', $headColumn);

        $importedCount = 0;
        $skippedRows = [];

        foreach ($data as $key => $row) {
            if ($key === 0) {
                continue;
            }

            $subgolongan = $row[$subgolonganColumn];
            $golongan = Golongan::where('nama', $row[$golonganColumn])->value('id');

            $emptyColumns = [];

            if ($subgolongan === null) {
                $emptyColumns[] = 'Sub Golongan';
            } else if ($golongan === null) {
                $emptyColumns[] = 'Golongan';
            }

            if (!empty($emptyColumns)) {
                $skippedRows[] = [
                    'row' => $key + 1,
                    'empty_columns' => $emptyColumns
                ];
                continue;
            }

            $softDeleteCek = Subgolongan::withTrashed()
                ->where('nama', $subgolongan)
                ->where('id_golongan', $golongan)
                ->first();

            if ($softDeleteCek) {
                $softDeleteCek->restore();
            } else {
                Subgolongan::UpdateOrCreate(
                    [
                        'nama' => $subgolongan,
                        'id_golongan' => $golongan
                    ]
                );
            }
            
            $importedCount++;
        }

        return [
            'imported_count' => $importedCount,
            'skipped_rows' => $skippedRows
        ];
    }
}
