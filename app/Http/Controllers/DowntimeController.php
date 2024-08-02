<?php

namespace App\Http\Controllers;

use App\Exports\DowntimeExport;
use App\Exports\DowntimeReportExport;
use App\Exports\FormatImportDowntime;
use App\Imports\DowntimeImport;
use App\Models\Downtime;
use App\Models\Downtimecode;
use App\Models\Effective;
use App\Models\Golongan;
use App\Models\Section;
use App\Models\Subgolongan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class DowntimeController extends Controller
{
    public function index() {
        $subgolongan = Subgolongan::all();
        $downtimecode = Downtimecode::all();

        if (request()->ajax()) {
            $downtime = DB::table('downtime')
                ->select('downtime.*', 'subgolongan.nama as subgolongan', 'downtimecode.kode as downtimecode')
                ->leftJoin('subgolongan', 'downtime.id_subgolongan', '=', 'subgolongan.id')
                ->leftJoin('downtimecode', 'downtime.id_downtimecode', '=', 'downtimecode.id');

            if (request()->filled('start_date') && request()->filled('end_date')) {
                $start_date = Carbon::parse(request('start_date'))->startOfDay();
                $end_date = Carbon::parse(request('end_date'))->endOfDay();
                $downtime->whereBetween('downtime.tanggal', [$start_date, $end_date]);
            }

            return DataTables::of($downtime->limit(10))->make(true);
        }

        return view('pages.downtime', [
            'title' => 'Downtime',
            'subgolongan' => $subgolongan,
            'downtimecode' => $downtimecode
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'tanggal' => 'required',
            'week' => 'required|in:1,2,3,4,5',
            'shift' => 'required|string|in:A,B',
            'id_subgolongan'=> 'required',
            'id_downtimecode'=> 'required',
            'detail' => 'nullable|string',
            'minute' => 'required|numeric',
            'man_hours' => 'required|numeric'
        ]);

        Downtime::create([
            'tanggal' => $request->input('tanggal'),
            'week' => $request->input('week'),
            'shift' => $request->input('shift'),
            'id_subgolongan' => $request->input('id_subgolongan'),
            'id_downtimecode' => $request->input('id_downtimecode'),
            'detail' => $request->input('detail'),
            'minute' => $request->input('minute'),
            'man_hours' => $request->input('man_hours')
        ]);
    }

    public function update(Request $request, $downtime) {
        $request->validate([
            'tanggal' => 'required',
            'week' => 'required|in:1,2,3,4,5',
            'shift' => 'required|string|in:A,B',
            'id_subgolongan'=> 'required',
            'id_downtimecode'=> 'required',
            'detail' => 'nullable|string',
            'minute' => 'required|numeric',
            'man_hours' => 'required|numeric'
        ]);

        $downtime = Downtime::where('id', $downtime)->first();

        if (!$downtime) {
            return redirect()->back()->with('error', 'Downtime not found');
        }

        $downtime->update([
            'tanggal' => $request->input('tanggal'),
            'week' => $request->input('week'),
            'shift' => $request->input('shift'),
            'id_subgolongan' => $request->input('id_subgolongan'),
            'id_downtimecode' => $request->input('id_downtimecode'),
            'detail' => $request->input('detail'),
            'minute' => $request->input('minute'),
            'man_hours' => $request->input('man_hours')
        ]);
    }

    public function destroy($downtime) {
        $downtime = Downtime::where('id', $downtime)->first();

        if (!$downtime) {
            return redirect()->back()->with('error', 'Downtime not found');
        }

        $downtime->delete();
    }

    public function deleteAll(Request $request) {
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $downtime = Downtime::query();
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();

            $downtime->whereBetween('tanggal', [$start_date, $end_date]);
        }

        if (!$downtime) {
            return redirect()->back();
        }

        $downtime->delete();
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $importer = new DowntimeImport();
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
        return Excel::download(new FormatImportDowntime, 'Format Downtime Import.xlsx');
    }

    public function exportReportData() {
        $start = '2024-07-01';
        $end = '2024-07-31';

        $downtime = Downtime::whereBetween('tanggal', [$start, $end])
            ->with([
            'subgolongan' => function ($query) {
                $query->with([
                    'golongan' => function ($query) {
                        $query->withTrashed();
                    }
                ])->withTrashed();
            },'downtimecode'])
            ->get();

        $effective = Effective::whereBetween('tanggal', [$start, $end])
            ->with([
            'subgolongan' => function ($query) {
                $query->with([
                    'golongan' => function ($query) {
                        $query->withTrashed();
                    }
                ])->withTrashed();
            }])
            ->get();

        $section = Section::with(['downtimecode' => function ($query) use ($start, $end) {
            $query->with(['downtime' => function ($query) use ($start, $end) {
                $query->whereBetween('tanggal', [$start, $end])
                    ->with(['subgolongan' => function ($query) {
                        $query->with([
                            'golongan' => function ($query) {
                                $query->withTrashed();
                            }
                        ])->withTrashed();
                    }]);
                }]);
            }])->get();

        $golonganT = Golongan::with('subgolongan')
            ->where('lokasi', 'T')
            ->get();

        $golonganB = Golongan::with('subgolongan')
            ->where('lokasi', 'B')
            ->get();

        $labelWeek = '';

        $countG = [];
        $sectionDcTotal = [];
        $sectionGTotal = [];

        foreach ($section as $s) {
            $gTotal = [];
            foreach ($s->downtimecode as $dc) {
                foreach ($golonganB as $g) {
                    foreach ($g->subgolongan as $sg) {
                        $totalG = $dc->downtime->where('id_subgolongan', $sg->id)->sum('minute');
                        $count[$dc->id][$sg->id] = $totalG;
                        $dcTotal[$dc->id] = isset($dcTotal[$dc->id]) ? $dcTotal[$dc->id] + $totalG : $totalG;
                        $gTotal[$sg->id] = isset($gTotal[$sg->id]) ? $gTotal[$sg->id] + $totalG : $totalG;
                    }
                }
                foreach ($golonganT as $g) {
                    foreach ($g->subgolongan as $sg) {
                        $totalG = $dc->downtime->where('id_subgolongan', $sg->id)->sum('minute');
                        $count[$dc->id][$sg->id] = $totalG;
                        $dcTotal[$dc->id] = isset($dcTotal[$dc->id]) ? $dcTotal[$dc->id] + $totalG : $totalG;
                        $gTotal[$sg->id] = isset($gTotal[$sg->id]) ? $gTotal[$sg->id] + $totalG : $totalG;
                    }
                }
            }
            $countG[$s->id] = $count;
            $sectionDcTotal[$s->id] = $dcTotal;
            $sectionGTotal[$s->id] = $gTotal;
        }

        $allGTotal = [];
        $allTotalMinute = 0;
        foreach ($sectionGTotal as $gTId => $gT) {
            $totalSection = 0;
            $sectionAllG = [];
            foreach ($gT as $gId => $t) {
                $totalSection += $t;
                if (isset($allGTotal[$gId])) {
                    $allGTotal[$gId] += $t;
                } else {
                    $allGTotal[$gId] = $t;
                }
                $allTotalMinute += $t;
            }
            $sectionAllG = $totalSection;
            $sectionAllGTotal[$gTId] = $sectionAllG;
        }

        $totalGEh = [];
        $allTotalGEh = 0;
        foreach ($effective as $e) {
            $subgolonganId = $e->subgolongan->id;
            $regulerEh = $e->reguler_eh + $e->overtime;
            $totalRegulerEh = $regulerEh * 60;

            if (isset($totalGEh[$subgolonganId])) {
                $totalGEh[$subgolonganId] += $totalRegulerEh;
            } else {
                $totalGEh[$subgolonganId] = $totalRegulerEh;
            }
            $allTotalGEh += $totalRegulerEh;
        }

        $percentLossTimeAll = $allTotalGEh != 0 ? ($allTotalMinute / $allTotalGEh) * 100 : 0;
        $percentLossTime = [];
        foreach ($allGTotal as $subgolonganId => $gTotal) {
            if (isset($totalGEh[$subgolonganId])) {
                $totalEh = $totalGEh[$subgolonganId];
                $percentLossTime[$subgolonganId] = $totalEh != 0 ? ($gTotal / $totalEh) * 100 : 0;
            } else {
                $percentLossTime[$subgolonganId] = 0;
            }
        }

        $totalGSgB = 0;
        $totalGEhSgB = 0;
        foreach ($golonganB as $l) {
            $totalGSg = 0;
            $totalGEhSg = 0;
            foreach ($l->subgolongan as $g) {
                $totalGSg += $allGTotal[$g->id];
                $totalGSgB += $allGTotal[$g->id];
                if (isset($totalGEh[$g->id])) {
                    $totalGEhSg += $totalGEh[$g->id];
                    $totalGEhSgB += $totalGEh[$g->id];
                } else {
                    $totalGEhSg += 0;
                    $totalGEhSgB += 0;
                }
            }
            $percentLossTimeSubgolongan[$l->id] = $totalGEhSg != 0 ? ($totalGSg / $totalGEhSg) * 100 : 0;
        }
        $percentLossTimeGolonganB = $totalGEhSgB != 0 ? ($totalGSgB / $totalGEhSgB) * 100 : 0;

        $totalGSgT = 0;
        $totalGEhSgT = 0;
        foreach ($golonganT as $l) {
            $totalGSg = 0;
            $totalGEhSg = 0;
            foreach ($l->subgolongan as $g) {
                $totalGSg += $allGTotal[$g->id];
                $totalGSgT += $allGTotal[$g->id];
                if (isset($totalGEh[$g->id])) {
                    $totalGEhSg += $totalGEh[$g->id];
                    $totalGEhSgT += $totalGEh[$g->id];
                } else {
                    $totalGEhSg += 0;
                    $totalGEhSgT += 0;
                }
            }
            $percentLossTimeSubgolongan[$l->id] = $totalGEhSg != 0 ? ($totalGSg / $totalGEhSg) * 100 : 0;
        }
        $percentLossTimeGolonganT = $totalGEhSgT != 0 ? ($totalGSgT / $totalGEhSgT) * 100 : 0;

        return view('pages.export.downtime-report', [
            'title' => 'Table',
            'downtime' => $downtime,
            'effective' => $effective,
            'section' => $section,
            'golonganT' => $golonganT,
            'golonganB' => $golonganB,
            'start' => $start,
            'end' => $end,
            'week' => $labelWeek,
            'countG' => $countG,
            'sectionDcTotal' => $sectionDcTotal,
            'sectionGTotal' => $sectionGTotal,
            'sectionAllGTotal' => $sectionAllGTotal,
            'allGTotal' => $allGTotal,
            'allTotalMinute' => $allTotalMinute,
            'totalGEh' => $totalGEh,
            'allTotalGEh' => $allTotalGEh,
            'percentLossTime' => $percentLossTime,
            'percentLossTimeSubgolongan' => $percentLossTimeSubgolongan,
            'percentLossTimeGolonganB' => $percentLossTimeGolonganB,
            'percentLossTimeGolonganT' => $percentLossTimeGolonganT,
            'percentLossTimeAll' => $percentLossTimeAll,
        ]);
    }

    public function exportReport(Request $request) {
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $filename = 'Summary Downtime ' . Carbon::parse($start_date)->setTimezone('Asia/Jakarta')->format('j F Y') . ' - ' . Carbon::parse($end_date)->setTimezone('Asia/Jakarta')->format('j F Y') . '.xlsx';

            $export = new DowntimeReportExport($start_date, $end_date);
            
            return Excel::download($export, $filename);
        } else {
            alert()->error('Error','Masukan filter terlebih dahulu!');
            return redirect()->route('downtime');
        }
    }

    public function exportData(Request $request) {
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $filename = 'Data Downtime ' . Carbon::parse($start_date)->setTimezone('Asia/Jakarta')->format('j F Y') . ' - ' . Carbon::parse($end_date)->setTimezone('Asia/Jakarta')->format('j F Y') . '.xlsx';

            $export = new DowntimeExport($start_date, $end_date);
            
            return Excel::download($export, $filename);
        } else {
            alert()->error('Error','Masukan filter terlebih dahulu!');
            return redirect()->route('downtime');
        }
    }
}
