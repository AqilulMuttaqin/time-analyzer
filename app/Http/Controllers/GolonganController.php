<?php

namespace App\Http\Controllers;

use App\Exports\FormatImportGolongan;
use App\Imports\GolonganImport;
use App\Models\Golongan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class GolonganController extends Controller
{
    public function index() {
        if (request()->ajax()) {
            $golongan = Golongan::orderBy('lokasi')->get();

            $golongan->map(function ($item, $key) {
                $item['rowIndex'] = $key + 1;
                return $item;
            });

            return DataTables::of($golongan)->make(true);
        }

        return view('pages.golongan', [
            'title' => 'Golongan'
        ]);
    }

    public function store(Request $request) {
        $golongan = Golongan::withTrashed()
            ->where('nama', $request->input('nama'))
            ->where('lokasi', $request->input('lokasi'))
            ->first();

        if ($golongan) {
            $golongan->restore();
        } else {
            $request->validate([
                'nama' => 'required|string|unique:golongan,nama,NULL,id,lokasi,'.$request->input('lokasi'),
                'lokasi' => 'required|string|in:T,B'
            ]);
    
            Golongan::create([
                'nama' => $request->input('nama'),
                'lokasi' => $request->input('lokasi')
            ]);
        }
    }

    public function update(Request $request, $golongan) {
        $request->validate([
            'nama' => 'required|string|unique:golongan,nama,'.$golongan.',id,lokasi,'.$request->input('lokasi'),
            'lokasi' => 'required|string|in:T,B'
        ]);

        $golongan = Golongan::where('id', $golongan)->first();

        if (!$golongan) {
            return redirect()->back()->with('error', 'Golongan not found');
        }

        $golongan->update([
            'nama' => $request->input('nama'),
            'lokasi' => $request->input('lokasi')
        ]);
    }

    public function destroy($golongan) {
        $golongan = Golongan::where('id', $golongan)->first();

        if (!$golongan) {
            return redirect()->back()->with('error', 'Golongan not found');
        }

        $golongan->delete();
    }

    public function formatImport() {
        return Excel::download(new FormatImportGolongan, 'Format Golongan Import.xlsx');
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $importer = new GolonganImport();
        $importResult = $importer->import(request()->file('file'));

        if ($importResult['skipped_rows'] == null) {
            alert()->success('Success', $importResult['imported_count'] . ' Data Berhasil Ditambah');

            return redirect()->back()->with('success', 'Effective data imported successfully.')
                ->with('imported_count', $importResult['imported_count'])
                ->with('skipped_rows', $importResult['skipped_rows']);
        } else {
            $importedCount = $importResult['imported_count'];
            $skippedRowCount = count($importResult['skipped_rows']);

            $emptyColumns = [];
            $emptyRows = [];
            foreach ($importResult['skipped_rows'] as $skippedRow) {
                $emptyRows[] = $skippedRow['row'];
                $emptyColumns = array_merge($emptyColumns, $skippedRow['empty_columns']);
            }

            $errorMessage = $importedCount . ' Data Tersimpan, ' . $skippedRowCount . ' Data Gagal Ditambahkan';

            if (!empty($emptyRows)) {
                $errorMessage .= ', Baris kosong: ' . implode(', ', $emptyRows);
            }

            alert()->warning('Success', $errorMessage)->persistent(true);

            return redirect()->back();
        }
    }
}
