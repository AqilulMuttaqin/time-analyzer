<?php

namespace App\Http\Controllers;

use App\Exports\FormatImportSection;
use App\Imports\SectionImport;
use App\Models\Section;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class SectionController extends Controller
{
    public function index() {
        if (request()->ajax()) {
            $section = Section::all();

            $section->map(function ($item, $key) {
                $item['rowIndex'] = $key + 1;
                return $item;
            });

            return DataTables::of($section)->make(true);
        }

        return view('pages.section', [
            'title' => 'Section'
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'nama' => 'required|unique:section,nama'
        ]);

        Section::create([
            'nama' => $request->input('nama')
        ]);
    }

    public function update(Request $request, $section) {
        $request->validate([
            'nama' => 'required|unique:section,nama,'.$section
        ]);

        $section = Section::where('id', $section)->first();

        if (!$section) {
            return redirect()->back()->with('error', 'Section not found');
        }

        $section->update([
            'nama' => $request->input('nama')
        ]);
    }

    public function destroy($section) {
        $section = Section::where('id', $section)->first();


        if (!$section) {
            return redirect()->back()->with('error', 'Section not found');
        }

        $section->delete();
    }

    public function formatImport() {
        return Excel::download(new FormatImportSection, 'Format Section Import.xlsx');
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $importer = new SectionImport();
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
