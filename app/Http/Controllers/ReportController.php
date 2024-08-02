<?php

namespace App\Http\Controllers;

use App\Exports\ReportMonthlyExport;
use App\Models\Action;
use App\Models\Concern;
use App\Models\Report;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class ReportController extends Controller
{
    public function index() {
        if (request()->ajax()) {
            $reportQuery = Report::with('section', 'concern.action')
                ->orderBy('id', 'desc')
                ->orderBy('month', 'desc');
            
            if (auth()->user()->role == 'admin') {
                $report = $reportQuery->get();
            } else if (auth()->user()->role == 'user') {
                $sectionId = auth()->user()->id_section;
                $report = $reportQuery->where('id_section', $sectionId)->get();
            }
    
            $report->each(function ($report) {
                $report->concern->each(function ($concern) {
                    $concern->action->each(function ($action) {
                        if ($action->status === 'On Progress' && Carbon::now()->subDay()->greaterThan(Carbon::parse($action->due_date))) {
                            $action->update(['status' => 'N-OK']);
                        }
                    });
                });
            });
    
            $report->map(function ($item, $key) {
                $item['rowIndex'] = $key + 1;
                return $item;
            });
    
            return DataTables::of($report)->make(true);
        }
    
        $section = Section::all();
        
        return view('pages.report-monthly', [
            'title' => 'Report Monthly',
            'section' => $section
        ]);
    }

    public function show($id) {
        $report = Report::with('section', 'concern.action')->find($id);
    
        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }
    
        return response()->json($report);
    }

    public function store(Request $request) {
        if (auth()->user()->role == 'admin') {
            $request->validate([
                'month' => 'required|string|unique:report,month,NULL,id,id_section,'.$request->id_section,
                'id_section' => 'required',
                'concerns.*' => 'required',
                'action.*.*' => 'required',
                'pic.*.*' => 'required',
                'due_date.*.*' => 'required',
                'status.*.*' => 'required'
            ]);
    
            $report = Report::create([
                'month' => $request->month,
                'id_section' => $request->id_section,
            ]);
        } else if (auth()->user()->role == 'user') {
            $sectionId = auth()->user()->id_section;
            $request->validate([
                'month' => 'required|string|unique:report,month,NULL,id,id_section,'.$sectionId,
                'concerns.*' => 'required',
                'action.*.*' => 'required',
                'pic.*.*' => 'required',
                'due_date.*.*' => 'required',
                'status.*.*' => 'required'
            ]);
    
            $report = Report::create([
                'month' => $request->month,
                'id_section' => $sectionId,
            ]);
        }

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

    public function update(Request $request, $id) {
        if (auth()->user()->role == 'admin') {
            $request->validate([
                'month' => 'required|string|unique:report,month,'.$id.',id,id_section,'.$request->id_section,
                'id_section' => 'required',
                'concerns.*' => 'required',
                'action.*.*' => 'required',
                'pic.*.*' => 'required',
                'due_date.*.*' => 'required',
                'status.*.*' => 'required'
            ]);
    
            $report = Report::findOrFail($id);
            $report->update([
                'month' => $request->month,
                'id_section' => $request->id_section,
            ]);
        } else if (auth()->user()->role == 'user') {
            $sectionId = auth()->user()->id_section;
            $request->validate([
                'month' => 'required|string|unique:report,month,'.$id.',id,id_section,'.$sectionId,
                'concerns.*' => 'required',
                'action.*.*' => 'required',
                'pic.*.*' => 'required',
                'due_date.*.*' => 'required',
                'status.*.*' => 'required',
            ]);
    
            $report = Report::findOrFail($id);
            $report->update([
                'month' => $request->month,
                'id_section' => $sectionId,
            ]);
        }

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

        return response()->json(['message' => 'Report updated successfully']);
    }

    public function destroy($id) {
        $report = Report::findOrFail($id);
        
        if(!$report) {
            return redirect()->back();
        }

        $report->concern()->each(function ($concern) {
            $concern->action()->delete();
        });
        $report->concern()->delete();
        $report->delete();
    }

    public function showExport() {
        $report = Report::with('section', 'concern.action')->find(11);
        
        return view('pages.export.monthly-report', [
            'title' => 'Table',
            'report' => $report
        ]);
    }
    
    public function export($id) {
        $report = Report::with('section', 'concern.action')->find($id);
        $idReport = $id;
        $month = Carbon::parse($report->month)->format('F Y');
        $filename = 'Report Monthly '. $report->section->nama . ' Periode ' . $month . '.xlsx';
        $export = new ReportMonthlyExport($idReport);

        return Excel::download($export, $filename);
    }
}
