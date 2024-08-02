<?php

namespace App\Http\Controllers;

use App\Exports\FormatImportSubgolongan;
use App\Imports\SubgolonganImport;
use App\Models\Downtime;
use App\Models\Effective;
use App\Models\Golongan;
use App\Models\Subgolongan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class SubgolonganController extends Controller
{
    public function index() {
        $golongan = Golongan::all();

        if (request()->ajax()) {
            $subgolongan = Subgolongan::with([
                'golongan' => function ($query) {
                    $query->withTrashed();
                }])
                ->orderBy('id_golongan')
                ->get();

            $subgolongan->map(function($item, $key) {
                $item['rowIndex'] = $key + 1;
                return $item;
            });

            return DataTables::of($subgolongan)->make(true);
        }


        return view('pages.subgolongan', [
            'title' => 'Sub Golongan',
            'golongan' => $golongan
        ]);
    }

    public function store(Request $request) {
        $subgolongan = Subgolongan::withTrashed()
            ->where('nama', $request->input('nama'))
            ->where('id_golongan', $request->input('id_golongan'))
            ->first();

        if ($subgolongan) {
            $subgolongan->restore();
        } else {
            $request->validate([
                'nama' => 'required|string|unique:subgolongan,nama,NULL,id,id_golongan,'.$request->input('id_golongan'),
                'id_golongan' => 'required'
            ]);
            
            Subgolongan::create([
                'nama' => $request->input('nama'),
                'id_golongan' => $request->input('id_golongan')
            ]);
        }
    }

    public function update(Request $request, $subgolongan) {
        $request->validate([
            'nama' => 'required|string|unique:subgolongan,nama,'.$subgolongan.',id,id_golongan,'.$request->input('id_golongan'),
            'id_golongan' => 'required'
        ]);

        $subgolongan = Subgolongan::where('id', $subgolongan)->first();

        if (!$subgolongan) {
            return redirect()->back()->with('error', 'subgolongan not found');
        }

        $subgolongan->update([
            'nama' => $request->input('nama'),
            'id_golongan' => $request->input('id_golongan')
        ]);
    }

    public function destroy($subgolongan) {
        $subgolongan = Subgolongan::where('id', $subgolongan)->first();

        if (!$subgolongan) {
            return redirect()->back()->with('error', 'subgolongan not found');
        }

        $downtimeCount = Downtime::where('id_subgolongan', $subgolongan->id)->count();
        $effectiveCount = Effective::where('id_subgolongan', $subgolongan->id)->count();
        if ($downtimeCount > 0 || $effectiveCount > 0) {
            $subgolongan->delete();
        } else {
            $subgolongan->forceDelete();
        }
    }

    public function formatImport() {
        return Excel::download(new FormatImportSubgolongan, 'Format Sub Golongan Import.xlsx');
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $importer = new SubgolonganImport();
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
