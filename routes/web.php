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
    Route::get('/self-evaluation/result', [EvaluationController::class, 'result'])->name('evaluations.result');
    Route::get('/all-evaluations', [EvaluationController::class, 'getListEvaluation'])->name('evaluations.list');

    Route::get('all-evaluations/{evaluation}/details', [EvaluationController::class, 'viewDetails'])->name('all-evaluations.view-details');
    Route::post('all-evaluations/{evaluation}/approve-details', [EvaluationController::class, 'approveDetails'])->name('all-evaluations.approve-details');
    Route::post('all-evaluations/{evaluation}/add-review', [EvaluationController::class, 'addReview'])->name('all-evaluations.add-review');
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

// Route::middleware(['auth'])->group(function () {
//     Route::post('/approve-quality', [EvaluationController::class, 'approveQuality'])->name('evaluations.approve-quality');
//     Route::post('/approve-titles', [EvaluationController::class, 'approveTitles'])->name('evaluations.approve-titles');
// });



// export
Route::get('/self-evaluations/{id}/export', [ExportController::class, 'exportEvaluation'])
    ->name('evaluations.export')
    ->middleware('auth');
Route::get('/export-quality-ratings', [ExportController::class, 'exportQualityList'])
    ->name('evaluations.export-quality-ratings');
Route::get('export/title-nominations', [ExportController::class, 'exportTitleNominations'])
    ->name('evaluations.export-title-nominations');

// for member
Route::middleware(['auth'])->group(function () {
    Route::get('/unit-members', [UnitController::class, 'members'])->name('unit.members');
});
Route::middleware(['auth'])->group(function () {
    // Tự đánh giá đơn vị (cho trưởng đơn vị)
    Route::get('/unit-evaluation', [UnitEvaluationController::class, 'index'])->name('unit.evaluations.index');
    Route::post('/unit-evaluation', [UnitEvaluationController::class, 'store'])->name('unit.evaluations.store');
});

require __DIR__ . '/auth.php';
