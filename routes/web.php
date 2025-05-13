<?php
use App\Models\Evaluation;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\TitleNominationController;
use App\Http\Controllers\ExportController;
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
    Route::get('/all-quality-ratings', [EvaluationController::class, 'getList'])->name('evaluations.list');
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/title-nominations', [TitleNominationController::class, 'index'])->name('title-nominations.index');
    Route::post('/title-nominations', [TitleNominationController::class, 'store'])->name('title-nominations.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/list-title-nominations', [TitleNominationController::class, 'getList'])->name('title-nominations.list');
    Route::put('/title-nominations/{id}', [TitleNominationController::class, 'update'])->name('title-nominations.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/unit-leader-approvals/{id}', [TitleNominationController::class, 'show'])->name('unit-leader-approvals.show');
    Route::put('/unit-leader-approvals/{id}', [TitleNominationController::class, 'approve'])->name('unit-leader-approvals.approve');
    Route::put('/list-title-nominations/{id}', [TitleNominationController::class, 'fastApprove'])->name('title-nominations.fastApprove');
});

Route::get('/self-evaluations/{id}/export', [ExportController::class, 'exportEvaluation'])
    ->name('evaluations.export')
    ->middleware('auth');

require __DIR__.'/auth.php';
