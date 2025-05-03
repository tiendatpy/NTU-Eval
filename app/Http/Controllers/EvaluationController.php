<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\EvaluationCriteria;
use App\Models\MetaType;
use App\Models\Periods;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EvaluationController extends Controller
{
    public function index()
    {
        $criteria = EvaluationCriteria::where('category_id', 30)->get();
        $evaluation = Evaluation::latest()->first();
        $classifications = MetaType::where('category', 'evaluation_classification')->get(); // Lấy danh sách xếp loại chất lượng
        $periods = Periods::all();
        return view('pages.self-evaluation', compact('criteria', 'evaluation', 'classifications', 'periods'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }


        $request->validate([
            'period_id' => 'required|exists:periods,id', // Kiểm tra period_id hợp lệ
            'classification_id' => 'required|exists:meta_types,id',
            'details' => 'required|array',
            'details.*.criteria_id' => 'required|exists:evaluation_criteria,id',
            'details.*.score' => 'required|numeric|min:1|max:4',
            'details.*.evidence' => 'nullable|string', 
        ]);

        DB::beginTransaction();

        try {
            $sum = 0;
            $count = 0;

            foreach ($request->details as $detail) {
                $sum += $detail['score'];
                $count++;
            }

            $finalScore = $count > 0 ? round($sum / $count, 2) : 0;

            $status = MetaType::where('category', 'evaluation_status')
                ->where('name', 'Tự đánh giá') 
                ->first();

            $evaluation = Evaluation::create([
                'unit_id' => $user->unit_id,
                'evaluator_id' => $user->id,
                'period_id' => $request->period_id,
                'score' => $finalScore,
                'classification_id' => $request->classification_id,
                'status_id' => $status?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);



            foreach ($request->details as $detail) {
                EvaluationDetail::create([
                    'evaluation_id' => $evaluation->id,
                    'criteria_id' => $detail['criteria_id'],
                    'score' => $detail['score'],
                    'evidence' => html_entity_decode(strip_tags($detail['evidence'])),
                ]);
            }

            DB::commit();

            return redirect()->route('evaluations.index')->with('success', 'Đánh giá đã được lưu.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
