<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\EvaluationCriteria;
use App\Models\MetaType;
use App\Models\Periods;
use App\Models\Quality;
use App\Models\Title;
use App\Models\Reward;
use App\Models\TitleNomination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        $isUnitLeader = $user->role->name === 'Trưởng đơn vị'; // Kiểm tra vai trò trưởng đơn vị

        $selectedYear = $request->input('year', now()->year - 1); // Mặc định là năm hiện tại - 1

        // Tìm kỳ đánh giá theo năm
        $currentPeriod = Periods::where('year', $selectedYear)->first();

        if (!$currentPeriod) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
        }

        // Tìm đánh giá của người dùng cho kỳ đánh giá đó
        $evaluation = Evaluation::where('evaluator_id', $user->id)
            ->where('period_id', $currentPeriod->id)
            ->with(['details.criteria'])
            ->first();

        // Lấy tiêu chí đánh giá phù hợp với vai trò
        if ($isUnitLeader) {
            $criteria = EvaluationCriteria::where('category_id', 17)->get();
        } else {
            $criteria = EvaluationCriteria::where('category_id', 18)->get();
        }

        // Lấy dữ liệu cần thiết cho form
        $periods = Periods::all();
        $quality = Quality::all();
        $titles = Title::where('type_id', 10)->get();
        $rewards = Reward::all();

        $isCurrentYear = $selectedYear == now()->year - 1;

        if ($request->ajax()) {
            // Trả về HTML của bảng đánh giá qua AJAX
            $html = view('pages.partials.evaluation-table', compact(
                'evaluation',
                'isCurrentYear',
                'criteria',
                'quality',
                'isUnitLeader',
                'titles',
                'rewards'
            ))->render();

            return response()->json(['html' => $html]);
        }

        return view('pages.self-evaluation', compact(
            'evaluation',
            'criteria',
            'periods',
            'quality',
            'titles',
            'rewards',
            'selectedYear',
            'isCurrentYear',
            'isUnitLeader'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        // Lấy period_id từ kỳ đánh giá của năm hiện tại - 1
        $currentPeriod = Periods::where('year', now()->year - 1)->first();
        if (!$currentPeriod) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm hiện tại.');
        }
        $periodId = $currentPeriod->id;

        // Validate dữ liệu đầu vào
        $request->validate([
            'classification_id' => 'required|exists:quality,id', 
            'details' => 'required|array',
            'details.*.criteria_id' => 'required|exists:evaluation_criteria,id',
            'details.*.rating' => 'required|numeric|min:1|max:4',
            'details.*.evidence' => 'nullable|string',
            'comment' => 'nullable|string|max:1000',
            'title_id' => 'required|exists:titles,id',
            'reward_id' => 'required|exists:rewards,id',
            'achievement' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // Tính toán điểm đánh giá
            $sum = 0;
            $count = 0;
            foreach ($request->details as $detail) {
                $sum += $detail['rating'];
                $count++;
            }
            $finalRating = $count > 0 ? round($sum / $count, 2) : 0;

            // Lấy trạng thái "Hoàn thành"
            $status = MetaType::where('category', 'evaluation_status')
                ->where('name', 'Hoàn thành')
                ->first();

            if (!$status) {
                throw new \Exception('Không tìm thấy trạng thái đánh giá.');
            }

            // Tạo đánh giá theo cấu trúc mới
            $evaluation = Evaluation::create([
                'evaluator_id' => $user->id,
                'unit_id' => $user->unit_id,
                'period_id' => $periodId,
                'rating' => $finalRating, // Sử dụng trường 'rating' theo migration mới
                'quality_id' => $request->classification_id, // Sử dụng quality_id thay vì classification_id
                'approved_quality_id' => null, // Trường này sẽ được điền bởi trưởng đơn vị sau này
                'title_id' => $request->title_id, // Thêm title_id trực tiếp vào evaluation
                'approved_title_id' => null, // Trường này sẽ được điền bởi trưởng đơn vị sau này
                'reward_id' => $request->reward_id, // Thêm reward_id trực tiếp vào evaluation
                'achievement' => html_entity_decode(strip_tags($request->achievement)), // Thêm thành tích trực tiếp
                'comment' => html_entity_decode(strip_tags($request->comment)),
                'review' => null, // Trường này sẽ được điền bởi trưởng đơn vị sau này
                'feedback' => null, // Trường này sẽ được điền sau này
                'status_id' => $status->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Tạo chi tiết đánh giá
            foreach ($request->details as $detail) {
                EvaluationDetail::create([
                    'evaluation_id' => $evaluation->id,
                    'criteria_id' => $detail['criteria_id'],
                    'score' => $detail['rating'], // Vẫn giữ score tại bảng chi tiết
                    'evidence' => html_entity_decode(strip_tags($detail['evidence'])),
                ]);
            }

            DB::commit();

            return redirect()->route('evaluations.index')
                ->with('success', 'Đánh giá và đề xuất danh hiệu đã được lưu thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Self Evaluation Error: ' . $e->getMessage());
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function getList(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        // Lấy danh sách các năm để lọc
        $years = Periods::orderBy('year', 'desc')->pluck('year')->unique();

        // Lấy năm được chọn, mặc định là năm hiện tại - 1
        $defaultYear = now()->year - 1;

        // Kiểm tra xem năm mặc định có trong danh sách năm không
        if (!$years->contains($defaultYear)) {
            $defaultYear = $years->first(); // Nếu không có, lấy năm gần nhất
        }

        $selectedYear = $request->input('year', $defaultYear);

        // Lấy period_id từ năm được chọn
        $period = Periods::where('year', $selectedYear)->first();

        if (!$period) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
        }

        // Lấy tất cả đánh giá trong đơn vị của người dùng hiện tại
        $evaluations = Evaluation::where('unit_id', $user->unit_id)
            ->where('period_id', $period->id)
            ->with(['evaluator', 'quality', 'title', 'reward', 'approvedQuality', 'approvedTitle', 'details'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.all-quality-rating', compact(
            'evaluations',
            'years',
            'selectedYear'
        ));
    }
}
