<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\DeviatesCastableAttributes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index() {
        $section = Section::all();
        if(request()->ajax()) {
            $user = User::with('section')->get();
            $user->map(function ($item, $key) {
                $item['rowIndex'] = $key + 1; 
                return $item;
            });

            return DataTables::of($user)->make(true);
        }
        return view('pages.user', [
            'title' => 'Users',
            'section' => $section
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,user',
            'id_section' => 'required',
        ]);

        User::create([
            'nik' => $request->input('nik'),
            'nama' => $request->input('nama'),
            'password' => Hash::make($request->input('password'),),
            'pw' => $request->input('password'),
            'role' => $request->input('role'),
            'id_section' => $request->input('id_section'),
        ]);
    }

    public function update(Request $request, $user) {
        $request->validate([
            'nama' => 'required|string|max:255',
            'role' => 'required|string|in:admin,user',
            'password' => 'required|string|min:6',
            'id_section' => 'required',
        ]);

        $user = User::where('nik', $user)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User not found');
        }

        $user->update([
            'nama' => $request->input('nama'),
            'role' => $request->input('role'),
            'password' => Hash::make($request->input('password')),
            'pw' => $request->input('password'),
            'id_section' => $request->input('id_section'),
        ]);
    }

    public function destroy($user) {
        $user = User::where('nik', $user)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User not found');
        }

        $user->delete();
    }
}
