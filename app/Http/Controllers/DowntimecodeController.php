<?php

namespace App\Http\Controllers;

use App\Exports\FormatImportDowntimeCode;
use App\Imports\DowntimecodeImport;
use App\Models\Downtimecode;
use App\Models\Section;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class DowntimecodeController extends Controller
{
    public function index() {
        $section = Section::all();
        if(request()->ajax()) {
            $code = Downtimecode::with('section')->orderBy('id_section')->get();
            $code->map(function ($item, $key) {
                $item['rowIndex'] = $key + 1; 
                return $item;
            });
            
            return DataTables::of($code)->make(true);
        }

        return view('pages.downtimecode', [
            'title' => 'Downtime Code',
            'section' => $section
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'kode' => 'required|unique:downtimecode,kode',
            'keterangan' => 'required|string',
            'id_section' => 'required'
        ]);

        Downtimecode::create([
            'kode' => $request->input('kode'),
            'keterangan' => $request->input('keterangan'),
            'id_section' => $request->input('id_section')
        ]);
    }

    public function update(Request $request, $code) {
        $request->validate([
            'kode' => 'required|unique:downtimecode,kode,'.$code,
            'keterangan' => 'required|string',
            'id_section' => 'required'
        ]);

        $code = Downtimecode::where('id', $code)->first();

        if (!$code) {
            return redirect()->back()->with('error', 'Downtime Code not found');
        }

        $code->update([
            'kode' => $request->input('kode'),
            'keterangan' => $request->input('keterangan'),
            'id_section' => $request->input('id_section')
        ]);
    }

    public function destroy($code) {
        $code = Downtimecode::where('id', $code)->first();

        if (!$code) {
            return redirect()->back()->with('error', 'Downtime Code not found');
        }

        $code->delete();
    }

    public function formatImport() {
        return Excel::download(new FormatImportDowntimecode, 'Format Downtime Code Import.xlsx');
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $importer = new DowntimecodeImport();
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
