<?php

use App\Http\Controllers\DowntimecodeController;
use App\Http\Controllers\DowntimeController;
use App\Http\Controllers\EffectiveController;
use App\Http\Controllers\GolonganController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubgolonganController;
use App\Http\Controllers\TargetdwController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::match(['get', 'head'], '/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    } else if (auth()->user()) {
        return redirect()->route('dashboard');
    }
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/data-user', [UserController::class, 'index'])->name('user');
    Route::post('/admin/add-user', [UserController::class, 'store'])->name('admin.add-user');
    Route::put('/admin/update-user/{user}', [UserController::class, 'update'])->name('admin.update-user');
    Route::delete('/admin/delete-user/{user}', [UserController::class, 'destroy'])->name('admin.delete-user');

    Route::post('/admin/add-section', [SectionController::class, 'store'])->name('admin.add-section');
    Route::put('/admin/update-section/{section}', [SectionController::class, 'update'])->name('admin.update-section');
    Route::delete('/admin/delete-section/{section}', [SectionController::class, 'destroy'])->name('admin.delete-section');
    Route::post('/admin/import-section', [SectionController::class, 'import'])->name('admin.import-section');
    Route::get('/admin/format-import-section', [SectionController::class, 'formatImport'])->name('admin.format-import-section');
    
    Route::post('/admin/add-downtimecode', [DowntimecodeController::class, 'store'])->name('admin.add-downtimecode');
    Route::put('/admin/update-downtimecode/{downtimecode}', [DowntimecodeController::class, 'update'])->name('admin.update-downtimecode');
    Route::delete('/admin/delete-downtimecode/{downtimecode}', [DowntimecodeController::class, 'destroy'])->name('admin.delete-downtimecode');
    Route::post('/admin/import-downtimecode', [DowntimecodeController::class, 'import'])->name('admin.import-downtimecode');
    Route::get('/admin/format-import-downtimecode', [DowntimecodeController::class, 'formatImport'])->name('admin.format-import-downtimecode');
    
    Route::post('/admin/add-golongan', [GolonganController::class, 'store'])->name('admin.add-golongan');
    Route::put('/admin/update-golongan/{golongan}', [GolonganController::class, 'update'])->name('admin.update-golongan');
    Route::delete('/admin/delete-golongan/{golongan}', [GolonganController::class, 'destroy'])->name('admin.delete-golongan');
    Route::post('/admin/import-golongan', [GolonganController::class, 'import'])->name('admin.import-golongan');
    Route::get('/admin/format-import-golongan', [GolonganController::class, 'formatImport'])->name('admin.format-import-golongan');
    
    Route::post('/admin/add-subgolongan', [SubgolonganController::class, 'store'])->name('admin.add-subgolongan');
    Route::put('/admin/update-subgolongan/{subgolongan}', [SubgolonganController::class, 'update'])->name('admin.update-subgolongan');
    Route::delete('/admin/delete-subgolongan/{subgolongan}', [SubgolonganController::class, 'destroy'])->name('admin.delete-subgolongan');
    Route::post('/admin/import-subgolongan', [SubgolonganController::class, 'import'])->name('admin.import-subgolongan');
    Route::get('/admin/format-import-subgolongan', [SubgolonganController::class, 'formatImport'])->name('admin.format-import-subgolongan');

    Route::post('/admin/add-effective', [EffectiveController::class, 'store'])->name('admin.add-effective');
    Route::put('/admin/update-effective/{effective}', [EffectiveController::class, 'update'])->name('admin.update-effective');
    Route::delete('/admin/delete-effective/{effective}', [EffectiveController::class, 'destroy'])->name('admin.delete-effective');
    Route::post('/admin/deleteAll-effective', [EffectiveController::class, 'deleteAll'])->name('admin.deleteAll-effective');
    Route::post('/admin/import-effective', [EffectiveController::class, 'import'])->name('admin.import-effective');
    Route::get('/admin/format-import-effective', [EffectiveController::class, 'formatImport'])->name('admin.format-import-effective');

    Route::post('/admin/add-downtime', [DowntimeController::class, 'store'])->name('admin.add-downtime');
    Route::put('/admin/update-downtime/{downtime}', [DowntimeController::class, 'update'])->name('admin.update-downtime');
    Route::delete('/admin/delete-downtime/{downtime}', [DowntimeController::class, 'destroy'])->name('admin.delete-downtime');
    Route::post('/admin/deleteAll-downtime', [DowntimeController::class, 'deleteAll'])->name('admin.deleteAll-downtime');
    Route::post('/admin/import-downtime', [DowntimeController::class, 'import'])->name('admin.import-downtime');
    Route::get('/admin/format-import-downtime', [DowntimeController::class, 'formatImport'])->name('admin.format-import-downtime');
    
    Route::post('/admin/add-target-downtime', [TargetdwController::class, 'store'])->name('admin.add-target-downtime');
    Route::put('/admin/update-target-downtime/{targetdw}', [TargetdwController::class, 'update'])->name('admin.update-target-downtime');
    Route::delete('/admin/delete-target-downtime/{targetdw}', [TargetdwController::class, 'destroy'])->name('admin.delete-target-downtime');
});

Route::middleware(['auth', 'role:admin,user'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/chartact', [HomeController::class, 'chartAct'])->name('chartact');
    Route::get('/chartdwdp', [HomeController::class, 'chartDwDp'])->name('chartdwdp');
    Route::get('/show-report', [HomeController::class, 'showReport'])->name('show-report');
    Route::get('/export-report', [HomeController::class, 'exportReport'])->name('export-report');
    Route::get('/validate-report-dashboard', [HomeController::class, 'val'])->name('validate.report-dashboard');
    Route::post('/create-report-dashboard', [HomeController::class, 'createReport'])->name('create.report-dashboard');
    Route::put('/update-report-dashboard', [HomeController::class, 'updateReport'])->name('update.report-dashboard');

    Route::get('/data-section', [SectionController::class, 'index'])->name('section');
    Route::get('/data-dcode', [DowntimecodeController::class, 'index'])->name('downtimecode');
    Route::get('/data-subgolongan', [SubgolonganController::class, 'index'])->name('subgolongan');
    Route::get('/data-golongan', [GolonganController::class, 'index'])->name('golongan');
    Route::get('/data-effecticve', [EffectiveController::class, 'index'])->name('effective');
    Route::get('/data-downtime', [DowntimeController::class, 'index'])->name('downtime');
    Route::get('/data-target-downtime', [TargetdwController::class, 'index'])->name('target-downtime');

    Route::get('/admin/export-downtime', [DowntimeController::class, 'exportReport'])->name('admin.export-downtime');
    Route::get('/admin/export-data-downtime', [DowntimeController::class, 'exportData'])->name('admin.export-data-downtime');
    Route::get('/admin/table', [DowntimeController::class, 'exportReportData'])->name('admin.table');
    
    Route::get('/data-report', [ReportController::class, 'index'])->name('report-monthly');
    Route::get('/show-report/{id}', [ReportController::class, 'show'])->name('report.show');
    Route::post('/add-report', [ReportController::class, 'store'])->name('report.store');
    Route::put('/update-report/{id}', [ReportController::class, 'update'])->name('report.update');
    Route::delete('/delete-report/{id}', [ReportController::class, 'destroy'])->name('report.delete');
    Route::get('/report-table', [ReportController::class, 'showExport'])->name('report.table');
    Route::get('/export-report-table/{id}', [ReportController::class, 'export'])->name('report.export');
});

require __DIR__.'/auth.php';
