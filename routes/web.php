<?php

use App\Models\Evaluation;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UnitEvaluationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;

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

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/self-evaluation', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::post('/self-evaluation', [EvaluationController::class, 'store'])->name('evaluations.store');
    Route::get('/self-evaluation/result', [EvaluationController::class, 'result'])->name('evaluations.result');
    Route::get('/all-evaluations', [EvaluationController::class, 'getListEvaluation'])->name('evaluations.list');

    Route::get('/all-evaluations/{evaluation}/details', [EvaluationController::class, 'viewDetails'])->name('all-evaluations.view-details');
    Route::post('/all-evaluations/{evaluation}/approve-details', [EvaluationController::class, 'approveDetails'])->name('all-evaluations.approve-details');
    Route::post('/all-evaluations/{evaluation}/add-review', [EvaluationController::class, 'addReview'])->name('all-evaluations.add-review');
});



Route::get('/dashboard', function () {
    // if (auth()->check() && auth()->user()->role->isUnitLeader == true) {
    //     return redirect()->route('dashboard.stats');
    // }
    // return redirect()->route('evaluations.index');
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// export
Route::get('/self-evaluations/{id}/export', [ExportController::class, 'exportEvaluation'])
    ->name('evaluations.export')
    ->middleware('auth');
Route::get('export/export-unit-report', [ExportController::class, 'exportUnitReport'])
    ->name('evaluations.export-unit-report');
Route::get('export/export-last-report', [ExportController::class, 'exportLastReport'])
    ->name('evaluations.export-last-report');

// for member
Route::middleware(['auth'])->group(function () {
    Route::get('/unit-members', [UnitController::class, 'members'])->name('unit.members');
});

// Tự đánh giá đơn vị (cho trưởng đơn vị)
Route::middleware(['auth'])->group(function () {
    Route::get('/unit-evaluation', [UnitEvaluationController::class, 'index'])->name('unit.evaluations.index');
    Route::post('/unit-evaluation', [UnitEvaluationController::class, 'store'])->name('unit.evaluations.store');
});

//dashboard
Route::get('/dashboard/stats', [DashboardController::class, 'index'])
    ->name('dashboard.stats')
    ->middleware(['auth']);

// report
Route::get('/unit-report', [ReportController::class, 'getListForUnitReport'])
    ->name('unit-report')
    ->middleware(['auth']);
Route::get('/last-report', [ReportController::class, 'getListForLastReport'])
    ->name('last-report')
    ->middleware(['auth']);
    
require __DIR__ . '/auth.php';
