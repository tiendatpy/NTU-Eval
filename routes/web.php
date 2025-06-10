<?php

use App\Models\Evaluation;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UnitEvaluationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\Admin\QualityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\TitleController;
use App\Http\Controllers\Admin\EvaluationCriteriaController;
use App\Http\Controllers\Admin\PeriodAdminController;

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
    return view('auth/login');
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
    if (auth()->user()->role->isUnitLeader) {
        return redirect()->route('dashboard.stats');
    }elseif (auth()->user()->role->isSuperAdmin) {
        return redirect()->route('admin.periods.index');
    }
    return redirect()->route('evaluations.index');
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



// crud period
Route::middleware(['auth'])->group(function () {
    // Đợt đánh giá
    Route::prefix('periods')->name('periods.')->group(function () {
        Route::get('/', [PeriodController::class, 'index'])->name('index');
        Route::get('/create', [PeriodController::class, 'create'])->name('create');
        Route::post('/store', [PeriodController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PeriodController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PeriodController::class, 'update'])->name('update');
        Route::post('/toggle-status/{id}', [PeriodController::class, 'toggleStatus'])->name('toggle-status');
    });
});

Route::middleware(['auth', 'unit.leader'])->group(function () {
    // crud period
    Route::prefix('periods')->name('periods.')->group(function () {
        Route::get('/', [PeriodController::class, 'index'])->name('index');
        Route::get('/create', [PeriodController::class, 'create'])->name('create');
        Route::post('/store', [PeriodController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PeriodController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PeriodController::class, 'update'])->name('update');

    });
    // self-evaluation for unit leader
    Route::get('/unit-evaluation', [UnitEvaluationController::class, 'index'])->name('unit.evaluations.index');
    Route::post('/unit-evaluation', [UnitEvaluationController::class, 'store'])->name('unit.evaluations.store');

    Route::get('/unit-evaluation/approve', [UnitEvaluationController::class, 'approvalList'])
        ->name('unit-evaluations.approve');

    // Xử lý phê duyệt
    Route::post('/unit-evaluation/approve/{id}', [UnitEvaluationController::class, 'approve'])
        ->name('unit-evaluations.approve-submit');
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
    // manage members of unit
    Route::get('/unit-members', [MemberController::class, 'members'])->name('unit.members');

});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Đợt đánh giá đã có
    Route::resource('periods', PeriodAdminController::class);
    
    // Tài khoản người dùng
    Route::resource('users', UserController::class);
    
    // Đơn vị
    Route::resource('units', UnitController::class);
    
    Route::resource('criteria', EvaluationCriteriaController::class);
    
    Route::resource('rewards', RewardController::class);

    Route::resource('titles', TitleController::class);
    
    // Xếp loại chất lượng
    Route::resource('quality', QualityController::class);
});
require __DIR__ . '/auth.php';
