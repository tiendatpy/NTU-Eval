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

        $isUnitLeader = $user->role->isUnitLeader == true; // Kiểm tra vai trò trưởng đơn vị

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
        $periods = Periods::orderBy('year', 'desc')->get();
        $quality = Quality::all();

        $titleTypeId = Cache::remember('title_type_id', 86400, function () {
            return MetaType::where('category', 'type_title')
                ->where('name', 'Cá nhân')
                ->value('id');
        });
        $titles = Title::where('type_id', $titleTypeId)->get();

        $rewards = Reward::all();

        // $isCurrentYear = $selectedYear == now()->year - 1;
        $openStatusId = Cache::remember('period_status_open_id', 86400, function () {
            return MetaType::where('category', 'period_status')
                ->where('name', 'Mở')
                ->value('id');
        });

        $isOpenPeriod = $currentPeriod->status_id == $openStatusId;

        if ($request->ajax()) {
            // Trả về HTML của bảng đánh giá qua AJAX
            $html = view('pages.partials.evaluation-table', compact(
                'evaluation',
                'isOpenPeriod',
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
            'isOpenPeriod',
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
            'quality_id' => 'required|exists:quality,id',
            'details' => 'required|array',
            'details.*.criteria_id' => 'required|exists:evaluation_criteria,id',
            'details.*.rating' => 'required|numeric|min:1|max:4',
            'details.*.evidence' => 'nullable|string',
            'comment' => 'nullable|string',
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

            // Kiểm tra xem đã tồn tại đánh giá cho kỳ đánh giá này chưa
            $existingEvaluation = Evaluation::where('evaluator_id', $user->id)
                ->where('period_id', $periodId)
                ->first();

            if ($existingEvaluation) {
                // Cập nhật đánh giá hiện có
                $existingEvaluation->update([
                    'rating' => $finalRating,
                    'quality_id' => $request->quality_id,
                    'title_id' => $request->title_id,
                    'reward_id' => $request->reward_id,
                    'achievement' => $request->achievement,
                    'comment' => $request->comment,
                    'status_id' => $status->id,
                    'updated_at' => now(),
                ]);

                // Cập nhật chi tiết đánh giá
                foreach ($request->details as $detail) {
                    $existingDetail = EvaluationDetail::where('evaluation_id', $existingEvaluation->id)
                        ->where('criteria_id', $detail['criteria_id'])
                        ->first();

                    if ($existingDetail) {
                        // Cập nhật chi tiết hiện có
                        $existingDetail->update([
                            'rating' => $detail['rating'],
                            'evidence' => $detail['evidence'],
                        ]);
                    } else {
                        // Tạo chi tiết mới nếu không tồn tại
                        EvaluationDetail::create([
                            'evaluation_id' => $existingEvaluation->id,
                            'criteria_id' => $detail['criteria_id'],
                            'rating' => $detail['rating'],
                            'evidence' => $detail['evidence'],
                        ]);
                    }
                }

                $evaluation = $existingEvaluation;
                $message = 'Cập nhật đánh giá thành công.';
            } else {
                // Tạo đánh giá mới
                $evaluation = Evaluation::create([
                    'evaluator_id' => $user->id,
                    'unit_id' => $user->unit_id,
                    'period_id' => $periodId,
                    'rating' => $finalRating,
                    'quality_id' => $request->quality_id,
                    'approved_quality_id' => null,
                    'title_id' => $request->title_id,
                    'approved_title_id' => null,
                    'reward_id' => $request->reward_id,
                    'approved_reward_id' => null, // Thêm trường này theo migration
                    'achievement' => $request->achievement,
                    'comment' => $request->comment,
                    'review' => null,
                    'feedback' => null,
                    'status_id' => $status->id,
                    'approved_by' => null, // Thêm trường này theo migration
                    'approved_at' => null, // Thêm trường này theo migration
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Tạo chi tiết đánh giá
                foreach ($request->details as $detail) {
                    EvaluationDetail::create([
                        'evaluation_id' => $evaluation->id,
                        'criteria_id' => $detail['criteria_id'],
                        'rating' => $detail['rating'],
                        'evidence' => $detail['evidence'],
                    ]);
                }

                $message = 'Tự đánh giá thành công.';
            }

            DB::commit();

            return redirect()->route('evaluations.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function result()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        // Lấy kỳ đánh giá của năm hiện tại - 1
        $currentPeriod = Periods::where('year', now()->year - 1)->first();
        if (!$currentPeriod) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm hiện tại.');
        }

        // Tìm đánh giá của người dùng cho kỳ đánh giá đó
        $evaluation = Evaluation::where('evaluator_id', $user->id)
            ->where('period_id', $currentPeriod->id)
            ->with(['details.criteria', 'quality', 'title', 'reward', 'approvedQuality', 'approvedTitle'])
            ->first();

        if (!$evaluation) {
            return redirect()->route('evaluations.index')
                ->with('error', 'Không tìm thấy đánh giá của bạn cho kỳ đánh giá này.');
        }

        // Lấy tiêu chí đánh giá phù hợp với vai trò
        $isUnitLeader = $user->role->isUnitLeader == true;
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

        $isOpenPeriod = true; // Luôn true vì chúng ta đang xem đánh giá năm hiện tại

        // Trả về view với partial evaluation-result
        return view('pages.self-evaluation-result', compact(
            'evaluation',
            'isOpenPeriod',
            'criteria',
            'isUnitLeader'
        ));
    }


    public function getListEvaluation(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        $isUnitLeader = $user->role->isUnitLeader == true;

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

        // Xây dựng query cơ bản
        $query = Evaluation::where('period_id', $period->id)
            ->with([
                'evaluator',
                'quality',
                'approvedQuality',
                'title',
                'approvedTitle',
                'reward',
                'approvedReward',
                'approver',
                'status',
                'unit'
            ])->orderBy('created_at', 'asc');

        if ($isUnitLeader) {
            $query->where('unit_id', $user->unit_id);
        } else {
            $query->where('unit_id', $user->unit_id)
                ->where('evaluator_id', '!=', $user->id);
        }

        // Lọc theo đơn vị nếu có
        if ($request->has('unit_id') && $request->unit_id) {
            $query->where('unit_id', $request->unit_id);
        }

        // Lọc theo xếp loại chất lượng nếu có
        if ($request->has('quality_id') && $request->quality_id) {
            $query->where('quality_id', $request->quality_id);
        }

        // Lọc theo danh hiệu thi đua nếu có
        if ($request->has('title_id') && $request->title_id) {
            $query->where('title_id', $request->title_id);
        }

        // Lọc theo trạng thái nếu có
        if ($request->has('status_id') && $request->status_id) {
            $query->where('status_id', $request->status_id);
        }

        // Lấy danh sách đánh giá đã lọc
        $evaluations = $query->orderBy('created_at', 'desc')->paginate(5);

        // Lấy danh sách xếp loại chất lượng để hiện thị trong dropdown filter
        $qualities = Quality::all();

        // Lấy danh sách danh hiệu thi đua để hiển thị trong dropdown filter
        $titleTypeId = Cache::remember('title_type_id', 86400, function () {
            return MetaType::where('category', 'type_title')
                ->where('name', 'Cá nhân')
                ->value('id');
        });
        $titles = Title::where('type_id', $titleTypeId)->get();

        // Lấy danh sách trạng thái để hiển thị trong dropdown filter
        $statuses = MetaType::where('category', 'evaluation_status')->get();

        // Thêm thống kê nếu là trưởng đơn vị
        $statistics = null;
        if ($isUnitLeader) {
            // Lấy ID trạng thái "Đã phê duyệt"
            $approvedStatusId = MetaType::where('category', 'evaluation_status')
                ->where('name', 'Đã phê duyệt')
                ->value('id');

            // Lấy ID trạng thái "Đang xét duyệt"
            $pendingStatusId = MetaType::where('category', 'evaluation_status')
                ->where('name', 'Đang xét duyệt')
                ->value('id');

            // Đếm tổng số đánh giá trong đơn vị
            $totalEvaluations = Evaluation::where('period_id', $period->id)
                ->where('unit_id', $user->unit_id)
                ->count();

            // Đếm số đánh giá đã được phê duyệt
            $approvedCount = Evaluation::where('period_id', $period->id)
                ->where('unit_id', $user->unit_id)
                ->where('status_id', $approvedStatusId)
                ->count();

            // Đếm số đánh giá đang chờ phê duyệt
            $pendingCount = Evaluation::where('period_id', $period->id)
                ->where('unit_id', $user->unit_id)
                ->where('status_id', $pendingStatusId)
                ->count();

            // Tính phần trăm
            $approvedPercentage = $totalEvaluations > 0 ? round(($approvedCount / $totalEvaluations) * 100) : 0;
            $pendingPercentage = $totalEvaluations > 0 ? round(($pendingCount / $totalEvaluations) * 100) : 0;

            $statistics = [
                'total' => $totalEvaluations,
                'approved' => [
                    'count' => $approvedCount,
                    'percentage' => $approvedPercentage
                ],
                'pending' => [
                    'count' => $pendingCount,
                    'percentage' => $pendingPercentage
                ]
            ];
        }

        $openStatusId = Cache::remember('period_status_open_id', 86400, function () {
            return MetaType::where('category', 'period_status')
                ->where('name', 'Mở')
                ->value('id');
        });

        $isOpenPeriod = $period->status_id == $openStatusId;

        return view('pages.partials.self-evaluation-list', compact(
            'evaluations',
            'years',
            'selectedYear',
            'qualities',
            'titles',
            'statuses',
            'isUnitLeader',
            'statistics',
            'isOpenPeriod',
        ));
    }

    public function addReview(Request $request, Evaluation $evaluation)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        // Kiểm tra quyền: chỉ cho phép người dùng không phải trưởng đơn vị trong cùng đơn vị
        if ($user->role->isUnitLeader == true || $user->unit_id !== $evaluation->unit_id) {
            return back()->with('error', 'Bạn không có quyền thêm nhận xét này.');
        }

        // Validate dữ liệu
        $request->validate([
            'review' => 'string',
        ]);


        try {
            // Cập nhật trường review
            $evaluation->update([
                'review' => $request->review,
            ]);

            return redirect()->route('all-evaluations.view-details', $evaluation->id)
                ->with('success', 'Đã thêm góp thành công.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function viewDetails(Evaluation $evaluation)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        $period = Periods::findOrFail($evaluation->period_id);

        // Lấy ID của trạng thái "Mở"
        $openStatusId = Cache::remember('period_status_open_id', 86400, function () {
            return MetaType::where('category', 'period_status')
                ->where('name', 'Mở')
                ->value('id');
        });

        // Kiểm tra xem kì đánh giá có đang mở không
        $isOpenPeriod = $period->status_id == $openStatusId;

        // Load các quan hệ cần thiết
        $evaluation->load([
            'details.criteria',
            'quality',
            'title',
            'reward',
            'approvedQuality',
            'approvedTitle',
            'status',
            'evaluator',
            'unit'
        ]);

        // Xác định vai trò của người xem
        $isUnitLeader = $user->role->isUnitLeader == true;
        $isCurrentUserEvaluation = $user->id === $evaluation->evaluator_id;

        // Lấy danh sách xếp loại và danh hiệu cho dropdown
        $qualities = Quality::all();

        $titleTypeId = Cache::remember('title_type_id', 86400, function () {
            return MetaType::where('category', 'type_title')
                ->where('name', 'Cá nhân')
                ->value('id');
        });
        $titles = Title::where('type_id', $titleTypeId)->get();
        $rewards = Reward::all();

        return view('pages.partials.self-evaluation-details', compact(
            'evaluation',
            'isUnitLeader',
            'isCurrentUserEvaluation',
            'qualities',
            'titles',
            'rewards',
            'isOpenPeriod',

        ));
    }

    public function approveDetails(Request $request, Evaluation $evaluation)
    {
        $user = auth()->user();

        if (!$user || $user->role->name !== 'Trưởng đơn vị') {
            return redirect()->route('login')
                ->with('error', 'Bạn không có quyền thực hiện hành động này.');
        }

        // Kiểm tra xem người dùng có phải là trưởng đơn vị của người đánh giá không
        if ($user->unit_id != $evaluation->unit_id) {
            return back()->with('error', 'Bạn không có quyền phê duyệt đánh giá này.');
        }

        // Validate dữ liệu đầu vào
        $request->validate([
            'approved_quality_id' => 'required|exists:quality,id',
            'approved_title_id' => 'required|exists:titles,id',
            'approved_reward_id' => 'nullable|exists:rewards,id',
            'feedback' => 'nullable|string',
        ]);



        try {
            DB::beginTransaction();

            // Cập nhật trạng thái thành "Đã phê duyệt"
            $approvedStatus = MetaType::where('category', 'evaluation_status')
                ->where('name', 'Đã phê duyệt')
                ->firstOrFail();

            // Cập nhật đánh giá
            $evaluation->update([
                'approved_quality_id' => $request->approved_quality_id,
                'approved_title_id' => $request->approved_title_id,
                'approved_reward_id' => $request->approved_reward_id,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'feedback' => $request->feedback,
                'status_id' => $approvedStatus->id,
            ]);


            DB::commit();

            return redirect()->route('evaluations.list')
                ->with('success', 'Đã phê duyệt đánh giá thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
