<?php

namespace App\Http\Controllers;

use App\Models\EvaluationDetail;

class EvaluationDetailsController extends Controller
{
    public function index($id)
    {
        // Lấy dữ liệu chi tiết đánh giá theo ID
        $evaluationDetails = EvaluationDetail::where('evaluation_id', $id)->get();
        return view('pages.evaluation_details', compact('evaluationDetails'));
    }
}
