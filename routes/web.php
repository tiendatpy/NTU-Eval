<?php
use App\Models\Evaluation;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UnitEvaluationController;
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
    Route::get('/all-quality-ratings', [EvaluationController::class, 'getListQualityRating'])->name('quality-ratings.list');
    Route::get('/all-title-nominations', [EvaluationController::class, 'getListTitleNomination'])->name('title-nominations.list');
    Route::put('/all-title-nominations/{id}', [EvaluationController::class, 'update'])->name('title-nominations.update');
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Route::middleware(['auth'])->group(function () {
//     Route::put('/title-nominations/{id}', [TitleNominationController::class, 'update'])->name('title-nominations.update');
// });

Route::middleware(['auth'])->group(function () {
    Route::post('/approve-quality', [EvaluationController::class, 'approveQuality'])->name('evaluations.approve-quality');
    Route::post('/approve-titles', [EvaluationController::class, 'approveTitles'])->name('evaluations.approve-titles');
});
// export
Route::get('/self-evaluations/{id}/export', [ExportController::class, 'exportEvaluation'])
    ->name('evaluations.export')
    ->middleware('auth');
Route::get('/export-quality-ratings', [ExportController::class, 'exportQualityList'])
    ->name('evaluations.export-quality-ratings');
Route::get('export/title-nominations', [ExportController::class, 'exportTitleNominations'])
    ->name('evaluations.export-title-nominations');

Route::middleware(['auth'])->group(function () {
    Route::get('/unit-members', [UnitController::class, 'members'])->name('unit.members');
});

Route::middleware(['auth'])->group(function () {
    // Tự đánh giá đơn vị (cho trưởng đơn vị)
    Route::get('/unit-evaluation', [UnitEvaluationController::class, 'index'])->name('unit.evaluations.index');
    Route::post('/unit-evaluation', [UnitEvaluationController::class, 'store'])->name('unit.evaluations.store');
    
    // // Xem danh sách đánh giá đơn vị 
    // Route::get('/unit-evaluations/list', [UnitEvaluationController::class, 'getListUnitEvaluations'])
    //     ->name('unit.evaluations.list');
    
});

require __DIR__.'/auth.php';
