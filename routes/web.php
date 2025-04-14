<?php

use App\Models\Evaluation;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\EvaluationCriteriaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view( 'pages/index');
// });
Route::get('/', [EvaluationCriteriaController::class, 'index'])->name('home');
Route::get('/self-evaluation', [EvaluationController::class, 'index'])->name('evaluations.index');
Route::post('/self-evaluation', [EvaluationController::class, 'store'])->name('evaluations.store');


