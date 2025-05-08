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
    public function index(Request $request)
    {
        $user = auth()->user();
        $isUnitLeader = $user->role->name === 'Trưởng đơn vị'; // Kiểm tra vai trò trưởng đơn vị

        $selectedYear = $request->input('year', now()->year - 1); // Mặc định là năm hiện tại

        // Tìm kỳ đánh giá theo năm
        $currentPeriod = Periods::where('year', $selectedYear)->first();

        // Tìm đánh giá của người dùng cho kỳ đánh giá đó
        $evaluation = Evaluation::where('evaluator_id', $user->id)
            ->where('period_id', $currentPeriod->id ?? null)
            ->first();
        if($isUnitLeader) {
            $criteria = EvaluationCriteria::where('category_id', 23)->get();
        }
        else {
            $criteria = EvaluationCriteria::where('category_id', 24)->get();
        }
        $periods = Periods::all(); // Lấy tất cả các kỳ đánh giá
        $classifications = MetaType::where('category', 'evaluation_classification')->get();

        $isCurrentYear = $selectedYear == now()->year - 1; // Kiểm tra có phải năm hiện tại không

        if ($request->ajax()) {
            // Trả về HTML của bảng đánh giá
            $html = view('pages.partials.evaluation-table', compact('evaluation', 'isCurrentYear', 'criteria', 'classifications', 'isUnitLeader'))->render();
            return response()->json(['html' => $html]);
        }

        return view('pages.self-evaluation', compact('evaluation', 'criteria', 'periods', 'classifications', 'selectedYear', 'isCurrentYear', 'isUnitLeader'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        // Lấy period_id từ query string hoặc mặc định là kỳ đánh giá của năm hiện tại
        $periodId = $request->input('period_id');
        if (!$periodId) {
            $currentPeriod = Periods::where('year', now()->year-1)->first();
            if (!$currentPeriod) {
                return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm hiện tại.');
            }
            $periodId = $currentPeriod->id;
        }

        $request->merge(['period_id' => $periodId]); // Gán period_id vào request để sử dụng trong validate và lưu dữ liệu

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
                ->where('name', 'Hoàn thành') 
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

    public function getList()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        $evaluations = Evaluation::where('unit_id', $user->unit_id)
        ->with(['evaluator', 'classification', 'details.criteria'])
        ->get();

        return view('pages.all-evaluation', compact('evaluations'));
    }
}
