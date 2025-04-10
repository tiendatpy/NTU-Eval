<?php
namespace App\Http\Controllers;

use App\Models\Evaluation;

class EvaluationController extends Controller
{
    public function index()
    {
        // Lấy dữ liệu evaluations cùng với classification và status
        $evaluations = Evaluation::with(['status'])->get();

        return view('pages.index', compact('evaluations'));
    }
}