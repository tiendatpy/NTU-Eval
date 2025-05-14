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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
        $managerId = Cache::remember('criteria_category_manager_id', 86400, function () {
            return MetaType::where('name', 'Viên chức, NLĐ quản lý')->value('id');
        });
        $staffId = Cache::remember('criteria_category_staff_id', 86400, function () {
            return MetaType::where('name', 'Viên chức, NLD không quản lý')->value('id');
        });
        if ($isUnitLeader) {
            $criteria = EvaluationCriteria::where('category_id', $managerId)->get();
        } else {
            $criteria = EvaluationCriteria::where('category_id', $staffId)->get();
        }

        // Lấy dữ liệu cần thiết cho form
        $periods = Periods::all();
        $quality = Quality::all();
        $titleTypeId = Cache::remember('title_type_id', 86400, function () {
            return MetaType::where('category', 'type_title')
                ->where('name', 'Cá nhân')
                ->value('id');
        });
        $titles = Title::where('type_id', $titleTypeId)->get();
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

            $status = MetaType::where('category', 'evaluation_status')
                ->where('name', 'Đang xét duyệt')
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
                ->with('success', 'Tự đánh giá thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function getListQualityRating(Request $request)
    {
        $user = auth()->user();

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

        $quality = Quality::all();

        if (!$period) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
        }

        // Xây dựng query cơ bản
        $query = Evaluation::where('period_id', $period->id)
                        ->with(['evaluator', 'quality', 'title', 'reward', 'approvedQuality', 'approvedTitle', 'details']);

        // Nếu là trưởng đơn vị, lấy tất cả đánh giá trong đơn vị, ngoại trừ của chính mình
        if ($user->role->name === 'Trưởng đơn vị') {
            $query->where('unit_id', $user->unit_id)
                ->where('evaluator_id', '!=', $user->id);
        } 
        // Nếu là nhân viên thường, chỉ lấy đánh giá trong cùng đơn vị
        else {
            $query->where('unit_id', $user->unit_id);
        }

        $evaluations = $query->orderBy('created_at', 'asc')->get();

        if ($user->role->name === 'Trưởng đơn vị') {
            return view('pages.unit-leader.quality-approval', compact(
                'evaluations',
                'years',
                'quality',
                'selectedYear'
            ));
        } else {
            return view('pages.all-quality-rating', compact(
                'evaluations',
                'years',
                'selectedYear'
            ));
        }
    }

    public function getListTitleNomination(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        $years = Periods::orderBy('year', 'desc')->pluck('year')->unique();

        $defaultYear = now()->year - 1;

        if (!$years->contains($defaultYear)) {
            $defaultYear = $years->first(); // Nếu không có, lấy năm gần nhất
        }

        $selectedYear = $request->input('year', $defaultYear);

        $period = Periods::where('year', $selectedYear)->first();

        if (!$period) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
        }

        $query = Evaluation::with(['evaluator', 'title', 'reward', 'status'])
            ->where('period_id', $period->id)
            ->where('unit_id', $user->unit_id);

        if ($user->role->name === 'Trưởng đơn vị') {
            $query->where('unit_id', $user->unit_id);
        }

        $nominations = $query->orderBy('created_at', 'asc')
            ->paginate(10);

        if ($user->role->name === 'Trưởng đơn vị') {
            return view('pages.unit-leader.title-approvals', compact('nominations', 'years', 'selectedYear'));
        } else {
            return view('pages.all-title-nominations', compact('nominations', 'years', 'selectedYear'));
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'review' => ['required', 'string', 'max:1000'],
        ]);

        $nomination = Evaluation::findOrFail($id);
        $nomination->update([
            'review' => html_entity_decode(strip_tags($request->input('review'))),
        ]);

        return redirect()->route('title-nominations.list')
            ->with('success', 'Đã cập nhật góp ý.');
    }
    // unit-leader-approval

    public function show($id)
    {
        $nomination = Evaluation::with(['evaluator', 'title', 'reward'])->findOrFail($id);
        $titles = Title::where('type_id', 11)->get(); // Lấy danh hiệu có type_id là 11);
        return view('pages.unit-leader.unit-leader-approval-detail', compact('nomination', 'titles'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'approved_title_id' => ['required', 'exists:titles,id'], // Kiểm tra danh hiệu được duyệt hợp lệ
        ]);

        $nomination = Evaluation::findOrFail($id);
        $approvedStatus = MetaType::where('category', 'nomination_status')
            ->where('name', 'Đã phê duyệt')
            ->firstOrFail();

        // Cập nhật danh hiệu được duyệt và bình xét
        $nomination->update([
            'approved_title_id' => $request->input('approved_title_id'), // Cập nhật danh hiệu được duyệt
            'status_id' => $approvedStatus->id,
        ]);

        return redirect()->route('unit-leader-approvals.show', $id)
            ->with('success', 'Đã xét duyệt thành công.');
    }

    public function approveQualities(Request $request)
    {
        $user = auth()->user();
        
        // Kiểm tra xem người dùng có phải là trưởng đơn vị không
        if ($user->role->name !== 'Trưởng đơn vị') {
            return back()->with('error', 'Bạn không có quyền thực hiện hành động này.');
        }
        
        // Validate dữ liệu đầu vào
        $request->validate([
            'evaluations' => 'required|array',
            'evaluations.*.id' => 'required|exists:evaluations,id',
            'evaluations.*.quality_id' => 'required|exists:quality,id',
        ]);
        
        
        try {
            DB::beginTransaction();
            
            // Cập nhật từng đánh giá
            foreach ($request->evaluations as $evaluationData) {
                $evaluation = Evaluation::findOrFail($evaluationData['id']);
                
                // Kiểm tra xem đánh giá có thuộc đơn vị của trưởng đơn vị không
                if ($evaluation->unit_id != $user->unit_id) {
                    continue;
                }
                
                // Cập nhật trạng thái và xếp loại được duyệt
                $evaluation->update([
                    'approved_quality_id' => $evaluationData['quality_id'],
                ]);
            }
            
            DB::commit();
            return redirect()->route('quality-ratings.list')
                ->with('success', 'Đã phê duyệt xếp loại thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
