<?php
namespace App\Http\Controllers;

use App\Models\EvaluationCriteria;

class EvaluationCriteriaController extends Controller
{
    public function index()
    {
        $evaluations_criteria = EvaluationCriteria::where('category_id', 21)->get();
        return view('pages.index', compact('evaluations_criteria'));
    }
}