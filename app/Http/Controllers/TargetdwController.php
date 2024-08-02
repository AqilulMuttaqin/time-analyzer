<?php

namespace App\Http\Controllers;

use App\Models\Targetdw;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TargetdwController extends Controller
{
    public function index() {
        if (request()->ajax()) {
            $targetdw = Targetdw::orderBy('month', 'desc')->get();

            $targetdw->map(function($item, $key) {
                $item['rowIndex'] = $key + 1;
                return $item;
            });

            return DataTables::of($targetdw)->make(true);
        }

        return view('pages.targetdw', [
            'title' => 'Target Downtime'
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'month' => 'required|string|unique:targetdw,month',
            'target' => 'required'
        ]);
        
        Targetdw::create([
            'month' => $request->input('month'),
            'target' => $request->input('target')
        ]);
    }
    
    public function update(Request $request, $targetdw) {
        $request->validate([
            'month' => 'required|string|unique:targetdw,month,'. $targetdw,
            'target' => 'required'
        ]);

        $targetdw = Targetdw::where('id', $targetdw)->first();

        if (!$targetdw) {
            return redirect()->back();
        }

        $targetdw->update([
            'month' => $request->input('month'),
            'target' => $request->input('target')
        ]);
    }

    public function destroy($targetdw) {
        $targetdw = Targetdw::where('id', $targetdw)->first();

        if (!$targetdw) {
            return redirect()->back();
        }

        $targetdw->delete();
    }
}
