<?php

namespace App\Http\Controllers;

use App\Exports\FormatImportEffective;
use App\Imports\EffectiveImport;
use App\Models\Effective;
use App\Models\Subgolongan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class EffectiveController extends Controller
{
    public function index() {
        $subgolongan = Subgolongan::all();

        if (request()->ajax()) {
            $effective = DB::table('effective')
                ->select('effective.*', 'subgolongan.nama as subgolongan')
                ->leftJoin('subgolongan', 'effective.id_subgolongan', '=', 'subgolongan.id');

            if (request()->filled('start_date') && request()->filled('end_date')) {
                $start_date = Carbon::parse(request('start_date'))->startOfDay();
                $end_date = Carbon::parse(request('end_date'))->endOfDay();
                $effective->whereBetween('effective.tanggal', [$start_date, $end_date]);
            }

            return DataTables::of($effective)->make(true);
        }

        return view('pages.effective', [
            'title' => 'Effective',
            'subgolongan' => $subgolongan
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'tanggal' => 'required',
            'week' => 'required',
            'shift' => 'required|string|in:A,B',
            'standart' => 'required|numeric',
            'indirect' => 'required|numeric',
            'overtime' => 'required|numeric',
            'reguler_eh' => 'required|numeric',
            'id_subgolongan' => 'required'
        ]);

        Effective::create([
            'tanggal' => $request->input('tanggal'),
            'week' => $request->input('week'),
            'shift' => $request->input('shift'),
            'standart' => $request->input('standart'),
            'indirect' => $request->input('indirect'),
            'overtime' => $request->input('overtime'),
            'reguler_eh' => $request->input('reguler_eh'),
            'id_subgolongan' => $request->input('id_subgolongan')
        ]);
    }

    public function update(Request $request, $effective) {
        $request->validate([
            'tanggal' => 'required',
            'week' => 'required',
            'shift' => 'required|string|in:A,B',
            'standart' => 'required|numeric',
            'indirect' => 'required|numeric',
            'overtime' => 'required|numeric',
            'reguler_eh' => 'required|numeric',
            'id_subgolongan' => 'required'
        ]);

        $effective = Effective::where('id', $effective)->first();

        if (!$effective) {
            return redirect()->back()->with('error', 'Effective not found');
        }

        $effective->update([
            'tanggal' => $request->input('tanggal'),
            'week' => $request->input('week'),
            'shift' => $request->input('shift'),
            'standart' => $request->input('standart'),
            'indirect' => $request->input('indirect'),
            'overtime' => $request->input('overtime'),
            'reguler_eh' => $request->input('reguler_eh'),
            'id_subgolongan' => $request->input('id_subgolongan')
        ]);
    }

    public function destroy($effective) {
        $effective = Effective::where('id', $effective)->first();


        if (!$effective) {
            return redirect()->back()->with('error', 'Effective not found');
        }

        $effective->delete();
    }

    public function deleteAll(Request $request) {
        if ($request->filled('start_date') && $request->filled('end_date')){
            $effective = Effective::query();
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();

            $effective->whereBetween('tanggal', [$start_date, $end_date]);
        }

        if (!$effective) {
            return redirect()->back();
        }

        $effective->delete();
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $importer = new EffectiveImport();
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

    public function formatImport() {
        return Excel::download(new FormatImportEffective, 'Format Effective Hours Import.xlsx');
    }
}
