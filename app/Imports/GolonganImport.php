<?php

namespace App\Imports;

use App\Models\Golongan;
use Maatwebsite\Excel\Facades\Excel;

class GolonganImport
{
    public function import($filePath) {
        $data = Excel::toArray([], $filePath)[0];

        $headColumn = $data[0];
        $namaColumn = array_search('Nama Golongan', $headColumn);
        $lokasiColumn = array_search('Lokasi (B/T)', $headColumn);

        $importedCount = 0;
        $skippedRows = [];

        foreach ($data as $key => $row) {
            if ($key === 0) {
                continue;
            }

            $nama = $row[$namaColumn];
            $lokasi = $row[$lokasiColumn];

            $emptyColumns = [];

            if ($nama === null) {
                $emptyColumns[] = 'Nama Golongan';
            } else if (!in_array($lokasi, ['B', 'T'])) {
                $emptyColumns[] = 'Lokasi (B/T)';
            }

            if (!empty($emptyColumns)) {
                $skippedRows[] = [
                    'row' => $key + 1,
                    'empty_columns' => $emptyColumns
                ];
                continue;
            }

            $softDeleteCek = Golongan::withTrashed()
                ->where('nama', $nama)
                ->where('lokasi', $lokasi)
                ->first();

            if ($softDeleteCek) {
                $softDeleteCek->restore();
            } else {
                Golongan::UpdateOrCreate(
                    [
                        'nama' => $nama,
                        'lokasi' => $lokasi
                    ],
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
