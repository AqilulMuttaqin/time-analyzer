<?php

namespace App\Http\Controllers;

use App\Exports\ReportMonthlyExport;
use App\Models\Action;
use App\Models\Concern;
use App\Models\Downtime;
use App\Models\Effective;
use App\Models\Report;
use App\Models\Section;
use App\Models\Targetdw;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    public function index() {
        $section = Section::get();
        return view('pages.dashboard', [
            'title' => 'Dashboard',
            'section' => $section
        ]);
    }

    public function chartAct(Request $request) {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        if ($request->has('term')) {
            $term = $request->input('term');
            if ($currentMonth >= 7) {
                $startYear = $currentYear - $term;
                $endYear = $currentYear + 1 - $term;
            } else {
                $startYear = $currentYear - 1 - $term;
                $endYear = $currentYear - $term;
            }
        }

        $label = ['Last Term'];
        for ($month = 7; $month <= 12; $month++) {
            $label[] = Carbon::create($startYear, $month, 1)->format('F Y');
        }
        for ($month = 6; $month <= 12; $month++) {
            if ($month >= 10) {
                $monthYear = "$startYear-$month";
            } else {
                $monthYear = "$startYear-0$month";
            }

            $totalDowntime = Downtime::whereYear('tanggal', '=', $startYear)
                ->whereMonth('tanggal', '=', $month)
                ->sum('minute');
            $totalEffective = Effective::whereYear('tanggal', '=', $startYear)
                ->whereMonth('tanggal', '=', $month)
                ->sum(DB::raw('reguler_eh + overtime'));

            if ($totalEffective == 0) {
                $totalDt1 = 0;
            } else {
                $totalDt1 = ($totalDowntime / ($totalEffective * 60)) * 100;
            }
            
            $totalDt2 = Targetdw::where('month', '=', $monthYear)
                ->sum('target');
            
            $data1[] = $totalDt1;
            $data2[] = $totalDt2;
        }
        for ($month = 1; $month <= 6; $month++) {
            $monthYear = "$endYear-0$month";

            $totalDowntime = Downtime::whereYear('tanggal', '=', $endYear)
                ->whereMonth('tanggal', '=', $month)
                ->sum('minute');
            $totalEffective = Effective::whereYear('tanggal', '=', $endYear)
                ->whereMonth('tanggal', '=', $month)
                ->sum(DB::raw('reguler_eh + overtime'));

            if ($totalEffective == 0) {
                $totalDt1 = 0;
            } else {
                $totalDt1 = ($totalDowntime / ($totalEffective * 60)) * 100;
            }

            $totalDt2 = Targetdw::where('month', '=', $monthYear)
                ->sum('target');
            
            $data1[] = $totalDt1;
            $data2[] = $totalDt2;
            $label[] = Carbon::create($endYear, $month, 1)->format('F Y');
        }

        $data = [
            "chartId" => "chartActTarget",
            "data1" => $data1,
            "data2" => $data2,
            "label" => $label
        ];

        return response()->json($data);
    }

    public function chartDwDp(Request $request) {
        $section = Section::all();
        $monthYear = $request->input('month');
        $checked = $request->input('mhmn');
        list($currentYear, $currentMonth) = explode('-', $monthYear);

        $currentLastMonth = $currentMonth - 1;
        $currentLastYear = $currentYear;
        if ($currentMonth == 1) {
            $currentLastMonth = 12;
            $currentLastYear -= 1;
        }

        $month = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $thisMonth = $currentMonth - 1;
        $labelMonth = "{$month[$thisMonth]} $currentYear";
        $labelYDwDp = $checked == "true" ? "Minute" : "Man Hours";
        $totalDowntime = $this->getTotalDowntime($currentMonth, $currentYear, $checked);
        $totalLastDowntime = $this->getTotalDowntime($currentLastMonth, $currentLastYear, $checked);
        $dataAllWeek = $this->getWeeklyDowntime($currentMonth, $currentYear, $currentLastMonth, $currentLastYear);
        list($chartWeekId, $chartDataWeek) = $this->getSectionWeeklyDowntime($section, $currentMonth, $currentYear, $currentLastMonth, $currentLastYear);
        $breakdownDwSubGolongan = $this->getBreakdownDowntime($currentMonth, $currentYear, $checked);

        $data = [
            "section" => $section,
            "chartId" => "chartDwDp",
            "data1" => $totalDowntime->pluck('total'),
            "data2" => $totalLastDowntime->pluck('total'),
            "label" => $totalDowntime->pluck('section_name'),
            "labelY" => $labelYDwDp,
            "dataBreakdown" => $breakdownDwSubGolongan->pluck('total'),
            "labelBreakdown" => $breakdownDwSubGolongan->pluck('nama'),
            "datasetLabel" => $labelMonth,
            "chartAllWeekId" => "chartWeekAll",
            "dataAllWeek" => $dataAllWeek['dataAllWeek'],
            "targetAllWeek" => $dataAllWeek['targetWeek'],
            "chartWeekId" => $chartWeekId,
            "chartDataWeek" => $chartDataWeek,
            "labelWeek" => ['Last Week', 'Week-1', 'Week-2', 'Week-3', 'Week-4', 'Week-5'],
            "labelXWeek" => $labelMonth,
        ];

        return response()->json($data);
    }

    private function getTotalDowntime($month, $year, $checked) {
        $selectColumn = $checked == "true" ? 'SUM(d.minute)' : 'SUM(d.man_hours)';
        return DB::table('section as s')
            ->leftJoin('downtimecode as c', 's.id', '=', 'c.id_section')
            ->leftJoin('downtime as d', function ($join) use ($month, $year) {
                $join->on('c.id', '=', 'd.id_downtimecode')
                    ->whereMonth('d.tanggal', $month)
                    ->whereYear('d.tanggal', $year);
            })
            ->select('s.nama as section_name', DB::raw("ROUND(COALESCE($selectColumn, 0), 1) as total"))
            ->groupBy('s.id', 's.nama')
            ->get();
    }

    private function getWeeklyDowntime($currentMonth, $currentYear, $currentLastMonth, $currentLastYear) {
        $lastWeek = DB::table(DB::raw("(SELECT week FROM effective WHERE MONTH(tanggal) = $currentLastMonth AND YEAR(tanggal) = $currentLastYear UNION SELECT week FROM downtime WHERE MONTH(tanggal) = $currentLastMonth AND YEAR(tanggal) = $currentLastYear) as combined"))
            ->max('week');

        $thisWeek = DB::table(DB::raw("(SELECT week FROM effective WHERE MONTH(tanggal) = $currentMonth AND YEAR(tanggal) = $currentYear UNION SELECT week FROM downtime WHERE MONTH(tanggal) = $currentMonth AND YEAR(tanggal) = $currentYear) as combined"))
            ->max('week');

        $lastWeekAllDowntime = Downtime::where('week', $lastWeek)
            ->whereYear('tanggal', $currentLastYear)
            ->whereMonth('tanggal', $currentLastMonth)
            ->sum('minute');

        $lastWeekAllEffective = Effective::where('week', $lastWeek)
            ->whereYear('tanggal', $currentLastYear)
            ->whereMonth('tanggal', $currentLastMonth)
            ->sum(DB::raw('reguler_eh + overtime'));

        $dataAllWeek = [];
        if ($lastWeekAllEffective == 0 || $lastWeekAllDowntime == 0) {
            $dataAllWeek[0] = 0;
        } else {
            $dataAllWeek[0] = ($lastWeekAllDowntime / ($lastWeekAllEffective * 60)) * 100;
        }

        $targetWeek = [];
        $targetLastMonthYear = sprintf('%04d-%02d', $currentLastYear, $currentLastMonth);
        $targetLastWeek = Targetdw::where('month', $targetLastMonthYear)->sum('target');
        $targetWeek[0] = $targetLastWeek;

        $targetThisMonthYear = sprintf('%04d-%02d', $currentYear, $currentMonth);
        $targetThisWeek = Targetdw::where('month', $targetThisMonthYear)->sum('target');

        for ($i = 1; $i <= $thisWeek; $i++) { 
            $thisWeekAllDowntime = Downtime::where('week', $i)
                ->whereYear('tanggal', $currentYear)
                ->whereMonth('tanggal', $currentMonth)
                ->sum('minute');

            $thisWeekAllEffective = Effective::where('week', $i)
                ->whereYear('tanggal', $currentYear)
                ->whereMonth('tanggal', $currentMonth)
                ->sum(DB::raw('reguler_eh + overtime'));

            if ($thisWeekAllEffective == 0 || $thisWeekAllDowntime == 0) {
                $dataAllWeek[$i] = 0;
            } else {
                $dataAllWeek[$i] = ($thisWeekAllDowntime / ($thisWeekAllEffective * 60)) * 100;
            }

            $targetWeek[$i] = $targetThisWeek;
        }

        return [
            'dataAllWeek' => $dataAllWeek,
            'targetWeek' => $targetWeek,
        ];
    }

    private function getSectionWeeklyDowntime($section, $currentMonth, $currentYear, $currentLastMonth, $currentLastYear) {
        $chartWeekId = [];
        $chartDataWeek = [];

        $lastWeek = DB::table(DB::raw("(SELECT week FROM effective WHERE MONTH(tanggal) = $currentLastMonth AND YEAR(tanggal) = $currentLastYear UNION SELECT week FROM downtime WHERE MONTH(tanggal) = $currentLastMonth AND YEAR(tanggal) = $currentLastYear) as combined"))
            ->max('week');

        $thisWeek = DB::table(DB::raw("(SELECT week FROM effective WHERE MONTH(tanggal) = $currentMonth AND YEAR(tanggal) = $currentYear UNION SELECT week FROM downtime WHERE MONTH(tanggal) = $currentMonth AND YEAR(tanggal) = $currentYear) as combined"))
            ->max('week');

        foreach ($section as $key => $item) {
            $lastWeekDowntime = Downtime::with('downtimecode')
                ->join('downtimecode', 'downtime.id_downtimecode', '=', 'downtimecode.id')
                ->where('downtimecode.id_section', $item->id)
                ->where('week', $lastWeek)
                ->whereYear('tanggal', $currentLastYear)
                ->whereMonth('tanggal', $currentLastMonth)
                ->sum('minute');

            $lastWeekEffective = Effective::where('week', $lastWeek)
                ->whereYear('tanggal', $currentLastYear)
                ->whereMonth('tanggal', $currentLastMonth)
                ->sum(DB::raw('reguler_eh + overtime'));

            $dataWeek = [];
            if ($lastWeekEffective == 0 || $lastWeekDowntime == 0) {
                $dataWeek[0] = 0;
            } else {
                $dataWeek[0] = ($lastWeekDowntime / ($lastWeekEffective * 60)) * 100;
            }
            
            for ($i = 1; $i <= $thisWeek; $i++) { 
                $thisWeekDowntime = Downtime::with('downtimecode')
                    ->join('downtimecode', 'downtime.id_downtimecode', '=', 'downtimecode.id')
                    ->where('downtimecode.id_section', $item->id)
                    ->where('week', $i)
                    ->whereYear('tanggal', $currentYear)
                    ->whereMonth('tanggal', $currentMonth)
                    ->sum('minute');

                $thisWeekEffective = Effective::where('week', $i)
                    ->whereYear('tanggal', $currentYear)
                    ->whereMonth('tanggal', $currentMonth)
                    ->sum(DB::raw('reguler_eh + overtime'));

                if ($thisWeekEffective == 0) {
                    $dataWeek[$i] = 0;
                } else {
                    $dataWeek[$i] = ($thisWeekDowntime / ($thisWeekEffective * 60)) * 100;
                }
            }

            $chartId = "chartWeek$key";
            $chartWeekId[] = $chartId;
            $chartDataWeek[] = $dataWeek;
        }

        return [$chartWeekId, $chartDataWeek];
    }

    private function getBreakdownDowntime($month, $year, $checked) {
        $selectColumn = $checked == "true" ? 'SUM(d.minute)' : 'SUM(d.man_hours)';
        return DB::table('downtime as d')
            ->whereMonth('d.tanggal', $month)
            ->whereYear('d.tanggal', $year)
            ->leftJoin('subgolongan as l', 'd.id_subgolongan', '=', 'l.id')
            ->select('l.nama', DB::raw("ROUND($selectColumn, 1) as total"))
            ->groupBy('l.nama')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
    }


    public function showReport(Request $request) {
        $month = $request->query('month');
        $id = $request->query('id');

        $report = Report::with('section', 'concern.action')
            ->where('month', $month)
            ->where('id_section', $id)
            ->first();
    
        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }
    
        return response()->json($report);
    }

    public function exportReport(Request $request) {
        $month = $request->query('month');
        $id = $request->query('id');

        $report = Report::with('section', 'concern.action')
            ->where('month', $month)
            ->where('id_section', $id)
            ->first();

        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }
        
        if ($request->ajax()) {
            return response()->json(['success' => 'Report found'], 200);
        }

        $monthFormat = Carbon::parse($month)->format('F Y');
        $idReport = $report->id;
        
        $filename = 'Report Monthly '. $report->section->nama .' Periode '. $monthFormat .'.xlsx';
        $export = new ReportMonthlyExport($idReport);
        
        return Excel::download($export, $filename);
    }

    public function val(Request $request) {
        $request->validate([
            'month' => 'required|string|unique:report,month,NULL,id,id_section,'.$request->id,
            'id' => 'required',
        ]);
    }

    public function createReport(Request $request) {
        $request->validate([
            'concerns.*' => 'required',
            'action.*.*' => 'required',
            'pic.*.*' => 'required',
            'due_date.*.*' => 'required',
            'status.*.*' => 'required'
        ]);

        $report = Report::create([
            'month' => $request->month,
            'id_section' => $request->id_section
        ]);

        foreach ($request->concerns as $index => $concern) {
            $newConcern = Concern::create([
                'concerns' => $concern,
                'id_report' => $report->id,
            ]);

            foreach ($request->action[$index] as $actionIndex => $action) {
                Action::create([
                    'action' => $action,
                    'pic' => $request->pic[$index][$actionIndex],
                    'due_date' => $request->due_date[$index][$actionIndex],
                    'status' => $request->status[$index][$actionIndex],
                    'id_concern' => $newConcern->id,
                ]);
            }
        }
    }

    public function updateReport(Request $request) {
        $request->validate([
            'concerns.*' => 'required',
            'action.*.*' => 'required',
            'pic.*.*' => 'required',
            'due_date.*.*' => 'required',
            'status.*.*' => 'required',
        ]);

        $report = Report::where('month', $request->month)
            ->where('id_section', $request->id_section)
            ->firstOrFail();

        $report->concern()->each(function ($concern) {
            $concern->action()->delete();
        });
        $report->concern()->delete();
        
        foreach ($request->concerns as $index => $concern) {
            $newConcern = $report->concern()->create([
                'concerns' => $concern,
            ]);
    
            foreach ($request->action[$index] as $actionIndex => $action) {
                $newConcern->action()->updateOrCreate([
                    'action' => $action,
                    'pic' => $request->pic[$index][$actionIndex],
                    'due_date' => $request->due_date[$index][$actionIndex],
                    'status' => $request->status[$index][$actionIndex],
                ]);
            }
        }
    }
}
